<?php

/**
 * This will get a resource description from the databank and add the right strategy to process the call to the GenericResource class
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\create\GenericResourceCreator;
use tdt\core\model\resources\delete\GenericResourceDeleter;
use tdt\core\model\resources\GenericResource;
use tdt\core\model\resources\read\GenericResourceReader;
use tdt\core\model\resources\update\GenericResourceUpdater;
use tdt\exceptions\TDTException;
use tdt\core\utility\Config;
use tdt\core\model\Doc;

class GenericResourceFactory extends AResourceFactory {    

    public function __construct() {
    }

    public function hasResource($package, $resource) {
        $resource = DBQueries::hasGenericResource($package, $resource);
        return isset($resource["present"]) && $resource["present"] >= 1;
    }

    public function createCreator($package, $resource, $parameters) {
        if (empty($parameters["generic_type"])) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("The generic type has not been set"), $exception_config);
        }
        $creator = new GenericResourceCreator($package, $resource, $parameters["generic_type"]);
        foreach ($parameters as $key => $value) {
            $creator->setParameter($key, $value);
        }
        return $creator;
    }

    public function createReader($package, $resource, $parameters, $RESTparameters) {
        $reader = new GenericResourceReader($package, $resource, $RESTparameters);
        $reader->processParameters($parameters);
        return $reader;
    }

    public function createDeleter($package, $resource) {
        $deleter = new GenericResourceDeleter($package, $resource);
        return $deleter;
    }

    public function makeDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }

            foreach ($resourcenames as $resourcename) {
                $documentation = DBQueries::getGenericResourceDoc($package, $resourcename);

                $doc->$package->$resourcename = new \stdClass();
                $doc->$package->$resourcename->documentation = $documentation["doc"];

                // Get the meta-data for the resource.
                $metadata = DBQueries::getMetaData($package, $resourcename);
                if (!empty($metadata)) {
                    foreach ($metadata as $name => $value) {
                        if ($name != "id" && $name != "resource_id" && !empty($value)) {
                            $doc->$package->$resourcename->$name = $value;
                        }
                    }
                }

                $genres = new GenericResource($package, $resourcename);
                $strategy = $genres->getStrategy();
                $doc->$package->$resourcename->uri = Config::get("general", "hostname") . Config::get("general", "subdir") . $package . "/" . $resourcename . ".about";
                $doc->$package->$resourcename->parameters = $strategy->documentReadParameters();
                $doc->$package->$resourcename->requiredparameters = array();
            }
        }
    }

    public function makeDescriptionDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }

            foreach ($resourcenames as $resourcename) {
                $documentation = DBQueries::getGenericResourceDoc($package, $resourcename);
                $doc->$package->$resourcename = new \stdClass();
                $doc->$package->$resourcename->documentation = $documentation["doc"];
                $doc->$package->$resourcename->source_type = $documentation["type"];                

                // Get the strategy properties.
                $genericId = $documentation["id"];
                $strategyTable = "generic_resource_" . strtolower($documentation["type"]);

                $result = DBQueries::getStrategyProperties($genericId, $strategyTable);

                if (isset($result[0])) {
                    foreach ($result[0] as $column => $value) {
                        if ($column != "id" && $column != "gen_resource_id") {
                            $doc->$package->$resourcename->$column = $value;
                        }
                    }
                }

               // Get the meta-data for the resource.
                $metadata = DBQueries::getMetaData($package, $resourcename);
                if (!empty($metadata)) {
                    foreach ($metadata as $name => $value) {
                        if ($name != "id" && $name != "resource_id" && !empty($value)) {
                            $doc->$package->$resourcename->$name = $value;
                        }
                    }
                }

                // Get the publised columns.
                $columns = DBQueries::getPublishedColumns($genericId);
                // pretty formatted columns
                $prettyColumns = array();
                if (!empty($columns)) {
                    foreach ($columns as $columnentry) {
                        $prettyColumns[$columnentry["index"]] = $columnentry["column_name"];
                    }
                    $doc->$package->$resourcename->columns = $prettyColumns;
                }

                // Get and process the column aliases.
                $columnAliases = array();
                if (!empty($columns)) {
                    foreach ($columns as $columnentry) {
                        $columnAliases[$columnentry["column_name"]] = $columnentry["column_name_alias"];
                    }
                    $doc->$package->$resourcename->column_aliases = $columnAliases;
                }
            }
        }
    }

    protected function getAllResourceNames() {
        $results = DBQueries::getAllGenericResourceNames();
        $resources = array();
        foreach ($results as $result) {
            if (!array_key_exists($result["package_name"], $resources)) {
                $resources[$result["package_name"]] = array();
            }
            $resources[$result["package_name"]][] = $result["res_name"];
        }
        return $resources;
    }

    /**
     * Create the PUT method documentation.
     */ 
    public function createPUTDocumentation($doc) {        

        foreach($this->getAllStrategies() as $strategy){

            $strategy_name = strtolower($strategy);            
            $media_type = Doc::$MEDIA_TYPE_PREFIX . $strategy_name;
            $doc->$media_type = new \stdClass();
            
            $doc->$media_type->description = "Create a definition that allows the publication of data from a $strategy_name based data structure.";
            $doc->$media_type->parameters = new \stdClass();

            $creator = new GenericResourceCreator("", "", $strategy);            
            $doc->$media_type->parameters = $creator->documentParameters();
        }
    }

    /**
     * Create the DELETE method documentation.
     */ 
    public function createDELETEDocumentation($strategy) {
        
        $delete = new \stdClass();

        $strategy_name = strtolower($strategy);
        $delete->id = "generic.$strategy_name.delete";
        $delete->httpMethod = "DELETE";
        $delete->description = "Delete a resource definition removing it permanently.";
        $delete->parameters = new \stdClass();        
        
        return $delete;
    }

    /**
     * Create the PATCH method documentation.
     */ 
    public function createPATCHDocumentation($strategy) {
        
        $patch = new \stdClass();

        $strategy_name = strtolower($strategy);
        $patch->id = "generic.$strategy_name.patch";
        $patch->httpMethod = "PATCH";
        $patch->description = "PATCH a resource definition .";
        $patch->parameters = new \stdClass();   

        $creator = new GenericResourceCreator("", "", $strategy);            
        $parameters = $creator->documentParameters();
        $req_parameters = $creator->documentRequiredParameters();
        
        foreach($parameters as $parameter => $documentation){           
            $param_class = new \stdClass();
            $param_class->description = $documentation;            

            $patch->parameters->$parameter = $param_class;
            
        }     
        
        return $patch;
    }

    /**
     * Create the API documentation of generic resources, structure is based on the
     * google discovery API reference.
     */ 
    public function createAPIDoc($doc){        
        
        $resources = new \stdClass();

        if(!empty($doc->resources)){
            $resources = $doc->resources;
        }else{
            $doc->resources = $resources;
        }        

        foreach($this->getAllStrategies() as $strategy){
            $name = strtolower($strategy);
            $resources->$name = new \stdClass();
        
            $resources->$name-> $this->createcumentation($strategy);
            $resources->$name->delete = $this->createDELETEDocumentation($strategy);
            //$resources->$name->patch = $this->createPATCHDocumentation($strategy);
        }        
    }


    private function getAllStrategies() {
        $strategies = array();
        if ($handle = opendir(__DIR__ . '/../strategies')) {
            while (false !== ($strat = readdir($handle))) {
                //if the object read is a directory and the configuration methods file exists, then add it to the installed strategie
                if ($strat != "." && $strat != ".." && $strat != "README.md" && !is_dir(__DIR__ . "/../strategies/" . $strat) && file_exists(__DIR__ . "/../strategies/" . $strat)) {
                    $fileexplode = explode(".", $strat);
                    $classname = "tdt\\core\\strategies\\" . $fileexplode[0];
                    if(class_exists($classname)){
                        $class = new \ReflectionClass($classname);
                        if (!$class->isAbstract()) {
                            $strategies[] = $fileexplode[0];
                        }
                    }                    
                }
            }
            closedir($handle);
        }
        return $strategies;
    }

    public function createDCATDocumentation(){

        $rdf_string = "";
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            foreach ($resourcenames as $resourcename) {         

                $documentation = DBQueries::getGenericResourceDoc($package, $resourcename);
                $identifier = $package . "/" . $resourcename;
                $access_uri = Config::get("general", "hostname") . Config::get("general", "subdir") . $identifier;
                $rdf_string .= "<$access_uri> a dcat:Dataset;";
                $rdf_string .= " dct:title \"" . $documentation["doc"] . "\" ;";
                $rdf_string .= " dcat:distribution \"" . $access_uri . "\" . ";                
            }
        }

        return $rdf_string;
    }
}