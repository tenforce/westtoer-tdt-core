<?php

/**
 * This class will handle all calls to core resources e.g. self-documentation about the API, the entire set of resource definitions,...
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Michiel Vancoillie
 */

namespace tdt\core\model;

use tdt\core\utility\Config;

class CoreResourceFactory extends AResourceFactory {

    private $directory;
    private $namespace;

    public function __construct() {
        $this->directory = __DIR__ . "/packages/core/";
        $this->namespace = "tdt\\core\\model\\packages\\core\\";
    }
    
    protected function getAllResourceNames() {
        return array(
                    "tdtinfo" => array("resources", "packages","admin", "formatters", "dcat"),
                    "tdtadmin" => array("resources","docreset", "discovery")
                );
    }
    
    public function createReader($package, $resource, $parameters, $RESTparameters) {
        
        $classname = $this->namespace . $package . "\\"  . ucfirst($resource);
        $reader = new $classname($package, $resource, $RESTparameters);
        $reader->processParameters($parameters);
        return $reader;
    }

    public function makeDoc($doc) {

        //ask every resource we have for documentation
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            $package = strtolower($package);            

            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }

            foreach ($resourcenames as $resourcename) {                
                $resourcename = strtolower($resourcename);
                $resource_adjusted = ucfirst($resourcename);
                $classname = $this->namespace . $package . "\\" . $resource_adjusted;
                $doc->$package->$resourcename = new \stdClass();
                $doc->$package->$resourcename->documentation = $classname::getDoc();
                $doc->$package->$resourcename->requiredparameters = $classname::getRequiredParameters();
                $doc->$package->$resourcename->parameters = $classname::getParameters();
            }
        }
    }

    public function makeDescriptionDoc($doc) {
        $this->makeDoc($doc);
    }

    private function getCreationTime($package, $resource) {

        $resource = ucfirst($resource);
        //if the object read is a directory and the configuration methods file exists,
        //then add it to the installed packages
        if (is_dir($this->directory . $package) && file_exists($this->directory . $package . "/" . $resource . ".class.php")) {
            return filemtime($this->directory . $package . "/" . $resource . ".class.php");
        }
        return 0;
    }

    private function getModificationTime($package, $resource) {
        // for an existing folder you can only get the last modification date in php, so
        return $this->getCreationTime($package, $resource);
    }

    public function makeDeleteDoc($doc) {
        //We cannot delete Core Resources
        $d = new \stdClass();
        $d->documentation = "You cannot delete core resources.";
        if (!isset($doc->delete)) {
            $doc->delete = new \stdClass();
        }
        $doc->delete->core = new \stdClass();
        $doc->delete->core = $d;
    }

    public function getAllPackagesDoc() {
        // Ask every resource we have for documentation
        $packages = array();
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            array_push($packages, $package);
        }
        return $packages;
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

    public function createCreator($package, $resource, $parameters) {        
    }

    public function createDeleter($package, $resource) {        
    }
      
    public function createPUTDocumentation($doc){  
    }
}