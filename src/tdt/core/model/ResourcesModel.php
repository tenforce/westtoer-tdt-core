<?php

/**
 * This is the model for our application. You can access everything from here
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model;

use tdt\core\model\CoreResourceFactory;
use tdt\core\model\DBQueries;
use tdt\core\model\Doc;
use tdt\core\model\GenericResourceFactory;
use tdt\core\model\InstalledResourceFactory;
use tdt\core\model\RemoteResourceFactory;
use tdt\core\model\resources\GenericResource;
use tdt\core\model\ResourcesModel;
use tdt\core\universalfilter\universalfilters\UniversalFilterNode;
use tdt\cache\Cache;
use tdt\core\utility\Config;
use tdt\exceptions\TDTException;
use RedBean_Facade as R;
use JsonSchema\Validator;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ResourcesModel {

    private $host;
    private $subdir;
    private static $instance;
    private $factories;     
    public $config;

    private function __construct() {

        $this->host = Config::get("general", "hostname");
        $this->subdir = Config::get("general", "subdir");

        $this->factories = array(); //(ordening does matter here! Put the least expensive on top)
        $this->factories["generic"] = new GenericResourceFactory();
        $this->factories["core"] = new CoreResourceFactory();
        $this->factories["remote"] = new RemoteResourceFactory();
        $this->factories["installed"] = new InstalledResourceFactory();
         
        //Register the fatal error handler
        register_shutdown_function(array($this,"fatal_error_handler"));
    }

    public static function getInstance(array $config = array()) {

        if (count($config) > 0) {

            $config_object = json_decode(json_encode($config)); // need the config to be an object so we can validate for required properties
            $schema = file_get_contents("configuration-schema.json",true);

            $validator = new Validator();
            $validator->check($config_object, json_decode($schema));

            if (!$validator->isValid()) {

                $error_string = "";
                
                foreach ($validator->getErrors() as $error) {
                    $error_string .= "Validation for the json schema didn't validate: [".$error['property'] . "] => " . $error['message'] . "\n";
                }    
                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(500, array("The given configuration file for the resource model does not validate. Violations are \n $error_string"), $exception_config);                
            }

            Config::setConfig($config);
        }
        R::setup(Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"), Config::get("db", "user"), Config::get("db", "password"));
        if (!isset(self::$instance)) {
            self::$instance = new ResourcesModel();
        }
        return self::$instance;
    }

    /**
     * Checks if a package exists
     */
    public function hasPackage($package) {
        $package = strtolower($package);
        $doc = $this->getAllPackagesDoc();
        foreach ($doc as $packagename => $resourcenames) {
            if ($package == $packagename) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the doc whether a certain resource exists in our system.
     * We will look for a definition in the documentation. Of course,
     * the result of the documentation visitor class will be cached
     *
     * @return a boolean
     */
    public function hasResource($package, $resource) {
        $package = strtolower($package);
        $resource = strtolower($resource);

        $doc = $this->getAllDoc();
        foreach ($doc as $packagename => $resourcenames) {
            if ($package == $packagename) {
                foreach ($resourcenames as $resourcename => $var) {
                    if ($resourcename == $resource) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function throwException($code, $message = array()){
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException($code, $message, $exception_config);   
    }

    /**
     * Creates the given resource
     * @param string $package The package name under which the resource will exist.
     * @param string $resource The resource name under which the resource will be called.
     * @param array $parameters An array with create parameters     
     */
    public function createResource($packageresourcestring, $parameters) {

        $packageresourcestring = strtolower($packageresourcestring);
        $pieces = explode("/", $packageresourcestring);

        // Throws exception when it's not valid, returns packagestring when done
        $package = $this->isResourceValid($pieces);
        $resource = array_pop($pieces);

        $media_type = $parameters["media_type"];
        $content_type = explode('+', $media_type);
        $media_type = array_shift($content_type);
        unset($parameters["media_type"]);        
                 
        $doc = $this->getDiscoveryDoc();

        if(empty($doc->resources->definitions->methods->put->mediaType->$media_type)){
            $this->throwException(452, array("The given media type $media_type doesn't exist. Take a look at the discovery document to identify the possible source types."));
        }

        $create_params = $doc->resources->definitions->methods->put->mediaType->$media_type->parameters;
        
        // Exctract the source_type of the media-type       

        $source_type = explode('/', $media_type);
        $source_type = end($source_type);
        $source_type = explode('.', $source_type);
        $source_type = strtolower($source_type[1]);            

        $required_parameters = array();

        foreach($create_params as $param_name => $param_info){
            if($param_info["required"]){
                array_push($required_parameters, $param_name);
            }
        }
        
        // Check if all the required parameters are being passed.
        foreach ($required_parameters as $key) {
            if (empty($parameters[$key])) {                
                $this->throwException(452, array("The required parameter " . $key . " has not been passed, check out the discovery document for a list for the required parameters for the $media_type media type."));
            }
        }

        // Check if there are nonexistent parameters being passed.
        foreach(array_keys($parameters) as $key){
            if(!in_array(strtolower($key), array_keys(array_change_key_case($create_params)))){                
                $this->logInfo("While creating a resource definition, the given parameter $key is non existent for the given type of resource and will be ignored.");
                unset($parameters[$key]);
            }
        }

        $factory_name = $source_type;

        // Small tweak for the factory selection, remote and installed is not problem
        // However strategies that share common logic (aka generic resources such as csv, shp, db, xls, ...) are being handled by the generic factory
        if($factory_name != "remote" || $factory_name != "installed"){
            $factory_name = "generic";
            $parameters["generic_type"] = $source_type;
        }
        
        // All the necessities have been validates, let's create a resource definition!
        $creator = $this->factories[$factory_name]->createCreator($package, $resource, $parameters);
        try {
            // First check if the identifier for the resource definitino already exists.
            if ($this->hasResource($package, $resource)) {
                // If the identifier exists, delete it first and continue adding it.
                // It could be that because errors occured after the addition, that
                // the documentation reset in the CUDController isn't up to date anymore
                // This will result in a hasResource() returning true and deleteResource returning false (error)
                // This is our queue to reset the internal documentation.
                try {
                    $this->deleteResource($package, $resource);
                } catch (Exception $ex) {
                    //Clear the documentation in our cache for it has changed
                    $this->clearCachedDocumentation();                    
                    $this->throwException(500, array("Error: " . $ex->getMessage() . "We've done a hard reset on the internal documentation, try adding it again. If this doesn't work please log on issue or e-mail one of the developers."));
                }
            }
        } catch (Exception $ex) {
            //Clear the documentation in our cache for it has changed
            $this->deleteResource($package, $resource);
        }

        try{
            //R::freeze(true); -> see issue #21 on github.com/tdt/core
            R::begin();
            $creator->create();
            R::commit();
        }catch(Exception $ex){

            R::rollback();
            $this->clearCachedDocumentation();           
            $this->throwException(500, array("Error whilst adding the resource: " . $ex->getMessage()));
        }

    }

    private function logInfo($info){
        $log = new Logger('error_handler');        
        $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_". date('Y-m-d') . ".txt", Logger::INFO));
        $log->addInfo($info);
    }

    public function fatal_error_handler(){
        
        // If a fatal error occurs, during a PUT method, we have to delete the put resource, it could be
        // that there are some leftovers!
        
        if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "PUT"){
           $error = error_get_last();
           if(!is_null($error)){
                R::rollback();

                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";

                /**
                 * The sever error data
                 */

                $errfile = "unknown file";
                $errstr  = "shutdown";
                $errno   = E_CORE_ERROR;
                $errline = 0;

                $errno   = $error["type"];
                $errfile = $error["file"];
                $errline = $error["line"];
                $errstr  = $error["message"];
                header('HTTP/1.0 500 Internal Server Error', true, 500);
                throw new TDTException(500,array("Fatal error caught (file - error string - error number - error line): " . $errfile . " - $errstr - $errno - $errline"),$exception_config);
            }

        }
    }

    public function clearCachedDocumentation() {
        $cache_config = array();

        $cache_config["system"] = Config::get("general", "cache", "system");
        $cache_config["host"] = Config::get("general", "cache", "host");
        $cache_config["port"] = Config::get("general", "cache", "port");

        $c = Cache::getInstance($cache_config);
        $c->delete($this->host . $this->subdir . "documentation");
        $c->delete($this->host . $this->subdir . "admindocumentation");
        $c->delete($this->host . $this->subdir . "packagedocumentation");
    }

    /**
     * This function doesn't return anything, but throws exceptions when the validation fails
     */
    private function isResourceValid($pieces) {
        
        // Create the identifier for the new resource definition, and its data to be publicized,
        // if the condition isn't false that is, the condition to add the resource is:
        //  ((1)) an identifier exists out of a collection path, and a last piece the servers as a resource name. 
        // This can be compared to how folders and files work. A resource name cannot replace a collection (just like a resource name cannot
        // replace a folder when creating one.) example:
        // we have a collection identified by X/Y/Z and our new resource identifier is identified by X/Y/Z, Z being the last part, ergo a resource name
        // this cannot be tolerated as we would then delete an entire collection (and all of its resources) to add a new resource definition
        // you can thus only replace/renew resource with resources.
        //  ((2)) the collection so far built (first X, then X/Y, then X/Y/Z in our example) cannot be a resource
        // so we have to built the collection first, and check if it's not a resource

        
        // If we have only 1 package entry (resource consists of 1 hierarchy of packages i.e. package/resource)
        // then we can return true, because a package/resource may overwrite an existing package/resource
        
        $resource = array_pop($pieces);
        if (count($pieces) == 1) {
            return $pieces[0];
        }
        
        // Check if the packagestring isn't a resource ((2))
        $packagestring = array_shift($pieces);

        foreach ($pieces as $package) {
            if ($this->isResource($packagestring, $package)) {                
                $this->throwException(452, array($packagestring . "/" . $package . " is already a resource, you cannot overwrite resources with packages!"));
            }
            $packagestring .= "/" . $package;
        }

        // Check if the last part of the identifier is not a collection already ((1))
        $resourcestring = $packagestring . "/" . $resource;
        if ($this->isPackage($resourcestring)) {           
            $this->throwException(452, array($resourcestring . " is already a packagename, you cannot overwrite a package with a resource."));
        }
        return $packagestring;
    }

    /*
     * Analyses a URI, and returns package, resource and RESTparameters,
     * in contrast with "isResourceValid" this will not return exceptions
     * because it doesn't assume that the package/resource is the only thing
     * in the URI. This function copes with RESTparameters as well.
     *
     * It will look for the first valid string that matches a resource and return it,
     * as well with the RESTparameters and packagestring. If no resource is identified, it returns an exception
     */

    public function fetchPackageAndResource($pieces) {
        $result = array(); // contains package, resource, RESTparameters
        $RESTparameters = array();

        $package = array_shift($pieces);

        foreach ($pieces as $piece) {
            if ($this->isResource($package, $piece)) {
                $result["package"] = $package;
                $result["resource"] = $piece;
                array_shift($pieces);
                $result["RESTparameters"] = $pieces;
                return $result;
            } else {
                $package.= $package . "/" . $piece;
                array_shift($pieces);
            }
        }
    }

    private function isPackage($needle) {
        $result = DBQueries::getPackageId($needle);
        return $result != NULL;
    }

    private function isResource($package, $subpackage) {
        $result = DBQueries::getResourceType($package, $subpackage);
        return $result != NULL;
    }

    /**
     * @Deprecated
     * Searches for a generic entry in the generic- create part of the documentation, independent of
     * how it is passed (i.e. csv == CSV )
     * @return The correct entry in the generic table ( csv would be changed with CSV )
     */
    private function formatGenericType($genType, $genericTable) {
        foreach ($genericTable as $type => $value) {
            if (strtoupper($genType) == strtoupper($type)) {
                return $type;
            }
        }
        $this->throwException(452, array($genType . " was not found as a generic_type."));
    }

    /**
     * Reads the resource with the given parameters
     * @param string $package The package name under which the resource exists.
     * @param string $resource The resource name.
     * @param array $parameters An array with read parameters
     * @param array $RESTparameters An array with additional RESTparameters
     */
    public function readResource($package, $resource, $parameters, $RESTparameters) {

        $resource = strtolower($resource);
        $package = strtolower($package);

        //first check if the resource exists
        if (!$this->hasResource($package, $resource)) {         
            $this->throwException(452, array("package/resource pair: $package, $resource was not found."));
        }

        foreach ($this->factories as $factory) {
            if ($factory->hasResource($package, $resource)) {
                $reader = $factory->createReader($package, $resource, $parameters, $RESTparameters);               
                return $reader->execute();
            }
        }
    }

    /**
     * Updates the resource definition with the given parameters.
     * @param string $package The package name
     * @param string $resource The resource name
     * @param array $parameters An array with update parameters
     * @param array $RESTparameters An array with additional RESTparameters
     */
    public function updateResource($package, $resource, $parameters, $RESTparameters) {

        //first check if the resource exists
        if (!$this->hasResource($package, $resource)) {           
            $this->throwException(452, array("package/resource pair: $package, $resource was not found."));
        }

        /**
         * Get the resource properties from the documentation
         * Replace that passed properties and re-add the resource
         */
        $doc = $this->getAllDescriptionDoc();
        $currentParameters = $doc->$package->$resource;

        /**
         * Strip non create parameters from the definition
         */
        unset($currentParameters->parameters);
        unset($currentParameters->requiredparameters);

        if (isset($currentParameters->remote_package)) {
            unset($currentParameters->documentation);
        }

        unset($currentParameters->remote_package);
        unset($currentParameters->resource);

        foreach ($parameters as $parameter => $value) {
            if ($value != "" && $parameter != "columns") {
                $currentParameters->$parameter = $value;
            }
        }

        /**
         * Columns aren't key => value datamembers and will be handled separatly
         */
        if (isset($currentParameters->columns) && isset($parameters["columns"])) {
            foreach ($parameters["columns"] as $index => $value) {
                $currentParameters->columns[$index] = $value;
            }
        }

        // delete the empty parameters from the currentParameters object
        foreach ((array) $currentParameters as $key => $value) {
            if ($value == "") {
                unset($currentParameters->$key);
            }
        }
        $currentParameters = (array) $currentParameters;
        $this->createResource($package . '/' . $resource, $currentParameters);
    }

    /**
     * Deletes a Resource
     * @param string $package The package name
     * @param string $resource The resource name
     * @param array $parameters An array with delete parameters     
     */
    public function deleteResource($package, $resource) {

        // Check if the resource exists
        if (!$this->hasResource($package, $resource)) {           
            $this->throwException(452, array("Trying to delete $package / $resource, but it's no longer present in the back-end."));
        }

        $factory = "";
        if ($this->factories["generic"]->hasResource($package, $resource)) {
            $factory = $this->factories["generic"];
        } else if ($this->factories["remote"]->hasResource($package, $resource)) {
            $factory = $this->factories["remote"];
        } else if ($this->factories["installed"]->hasResource($package, $resource)) {
            $factory = $this->factories["installed"];
        } else {           
            $this->throwException(452, array("Trying to delete $package / $resource, but it's no longer present in the back-end."));
        }
        $deleter = $factory->createDeleter($package, $resource);
        $deleter->delete();

        //Clear the documentation in our cache for it has changed
        $this->clearCachedDocumentation();
    }

    /**
     * Deletes all Resources in a package
     * @param string $package The packagename that needs to be deleted.
     */
    public function deletePackage($package) {
        $resourceDoc = $this->getAllDoc();
        $packageDoc = $this->getAllPackagesDoc();
        if (isset($packageDoc->$package)) {
            $packageId = DBQueries::getPackageId($package);
            $subpackages = DBQueries::getAllSubpackages($packageId["id"]);

            foreach ($subpackages as $subpackage) {
                $subpackage = $subpackage["full_package_name"];
                if (isset($resourceDoc->$subpackage)) {
                    $resources = $resourceDoc->$subpackage;
                    foreach ($resourceDoc->$subpackage as $resource => $documentation) {
                        if ($resource != "creation_date") {
                            $this->deleteResource($subpackage, $resource, array());
                        }
                    }
                }
                $this->deletePackage($subpackage);
            }
            DBQueries::deletePackage($package);
        } else {           
            $this->throwException(404, array($package));
        }
    }

    /**
     * Uses a visitor to get all docs and return them
     * To have an idea what's in here, just check yourinstallationfolder/TDTInfo/Resources
     * @return a doc object containing all the packages, resources and further documentation
     */
    public function getAllDoc() {
        $doc = new Doc();
        return $doc->visitAll($this->factories);
    }

    public function getDCATDocumentation(){
        $doc = new Doc();
        return $doc->getDCATDocumentation($this->factories);
    }

    public function getAllDescriptionDoc() {
        $doc = new Doc();
        return $doc->visitAllDescriptions($this->factories);
    }

    public function getDiscoveryDoc() {        
        $doc = new Doc();        
        return $doc->visitAllAdmin($this->factories);
    }

    public function getAllPackagesDoc() {
        $doc = new Doc();
        return $doc->visitAllPackages();
    }

    /**
     * This function processes a resourcepackage-string
     * It will analyze it trying to do the following:
     * Find the first package-name hit, it will continue to eat pieces
     * of the resourcepackage string, untill it finds that the eaten string matches a package name
     * the piece after it found the package will be the resourcename ( if any pieces left ofcourse )
     * the pieces after the resourcename are the RESTparameters
     * @return array First entry is the [packagename], second entry is the [resourcename], third is the array with [RESTparameters]
     * If the package hasn't been found FALSE is returned!
     */
    public function processPackageResourceString($packageresourcestring) {

        $packageresourcestring = strtolower($packageresourcestring);
        $result = array();

        $pieces = explode("/", $packageresourcestring);
        if (count($pieces) == 0) {
            array_push($pieces, $packageresourcestring);
        }

        $package = array_shift($pieces);

        //Get an instance of our resourcesmodel
        $model = ResourcesModel::getInstance(Config::getConfigArray());
        $doc = $model->getAllDoc();
        $foundPackage = FALSE;
        
        
        // Since we do not know where the package/resource/requiredparameters end, we're going to build the package string
        // and check if it exists, if so we have our packagestring. Why is this always correct ? Take a look at the
        // ResourcesModel class -> funcion isResourceValid()         
        $resourcename = "";
        $reqparamsstring = "";

        if (!isset($doc->$package)) {
            while (!empty($pieces)) {
                $package .= "/" . array_shift($pieces);

                if (isset($doc->$package)) {

                    $foundPackage = TRUE;
                    $resourcename = array_shift($pieces);

                    /**
                     * Check if the resource exists
                     */
                    if($resourcename != null || $resourcename != ""){
                        $package_object = $doc->$package;
                        if(!isset($package_object->$resourcename)){
                            $this->throwException(404, array($packageresourcestring));
                        }
                    }

                    $reqparamsstring = implode("/", $pieces);
                    break;
                }
            }
        } else {
            $foundPackage = TRUE;
            $resourceNotFound = TRUE;
            while (!empty($pieces) && $resourceNotFound) {
                $resourcename = array_shift($pieces);
                if (!isset($doc->$package->$resourcename) && $resourcename != NULL) {
                    $package .= "/" . $resourcename;
                    $resourcename = "";
                } else {
                    $resourceNotFound = FALSE;
                }
            }
            $reqparamsstring = implode("/", $pieces);
        }


        $RESTparameters = array();
        $RESTparameters = explode("/", $reqparamsstring);
        if ($RESTparameters[0] == "") {
            $RESTparameters = array();
        }

        if ($resourcename == "") {
            $packageDoc = $model->getAllPackagesDoc();
            $allPackages = array_keys(get_object_vars($packageDoc));

            $foundPackage = in_array($package, $allPackages);

            if (!$foundPackage) {               
                $this->throwException(404, array($packageresourcestring));
            }
        }

        $result["packagename"] = $package;
        $result["resourcename"] = $resourcename;
        $result["RESTparameters"] = $RESTparameters;
        return $result;
    }

    /**
     * Check if the resource implements IFilter or not
     * return FALSE if not the resource doesn't implement iFitler
     * return the resource if it does
     */
    public function isResourceIFilter($package, $resource) {
        $package = strtolower($package);
        $resource = strtolower($resource);

        foreach ($this->factories as $factory) {

            if ($factory->hasResource($package, $resource)) {

                // remote resource just proxies the url so we don't need to take that into account
                if (get_class($factory) == "tdt\core\model\GenericResourceFactory") {

                    $genericResource = new GenericResource($package, $resource);
                    $strategy = $genericResource->getStrategy();

                    $interfaces = class_implements($strategy);

                    if (in_array("tdt\\core\\model\\resources\\read\IFilter", $interfaces)) {
                        return $genericResource;
                    } else {
                        return FALSE;
                    }
                } elseif (get_class($factory) == "tdt\core\model\InstalledResourceFactory") {

                    $reader = $factory->createReader($package, $resource, array(), array());
                    $interfaces = class_implements($reader);

                    if (in_array("tdt\\core\\model\\resources\\read\IFilter", $interfaces)) {
                        return $reader;
                    } else {
                        return FALSE;
                    }
                }
            }
        }
    }

    /**
     * Read the resource but by calling the readAndProcessQuery function
     */
    public function readResourceWithFilter(UniversalFilterNode $query, $resource) {
        $result = $resource->readAndProcessQuery($query);
        return $result;
    }

    /**
     * Get the columns from a tabular datasource.
     */
    public function getColumnsFromResource($package, $resource) {
        $package = strtolower($package);
        $resource = strtolower($resource);

        $gen_resource_id = DBQueries::getGenericResourceId($package, $resource);

        if (isset($gen_resource_id["gen_resource_id"]) && $gen_resource_id["gen_resource_id"] != "") {
            return DBQueries::getPublishedColumns($gen_resource_id["gen_resource_id"]);
        }
        return NULL;
    }

}