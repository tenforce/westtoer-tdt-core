<?php

/**
 * This class will handle a remote resource and connect to another DataTank instance for their data
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 * @author Michiel Vancoillie
 * @author Pieter Colpaert
 */

namespace tdt\core\model;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\create\RemoteResourceCreator;
use tdt\core\model\resources\delete\RemoteResourceDeleter;
use tdt\core\model\resources\read\RemoteResourceReader;
use tdt\core\utility\Config;
use tdt\core\utility\Request;
use tdt\exceptions\TDTException;

class RemoteResourceFactory extends AResourceFactory {

    public function __construct() {

    }

    public function hasResource($package, $resource) {
        $rn = $this->getAllResourceNames();
        return isset($rn[$package]) && in_array($resource, $rn[$package]);
    }

    protected function getAllResourceNames() {
        $resultset = DBQueries::getAllRemoteResourceNames();
        $resources = array();
        foreach ($resultset as $result) {
            if (!isset($resources[$result["package_name"]])) {
                $resources[$result["package_name"]] = array();
            }
            $resources[$result["package_name"]][] = $result["res_name"];
        }
        return $resources;
    }

    public function createCreator($package, $resource, $parameters, $RESTparameters) {
        $creator = new RemoteResourceCreator($package, $resource, $RESTparameters);
        foreach ($parameters as $key => $value) {
            $creator->setParameter($key, $value);
        }
        return $creator;
    }

    public function createReader($package, $resource, $parameters, $RESTparameters) {
        $reader = new RemoteResourceReader($package, $resource, $RESTparameters, $this->fetchResourceDocumentation($package, $resource));
        $reader->processParameters($parameters);
        return $reader;
    }

    public function createDeleter($package, $resource, $RESTparameters) {
        return new RemoteResourceDeleter($package, $resource, $RESTparameters);
    }

    public function makeDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }
            foreach ($resourcenames as $resource) {
                $doc->$package->$resource = new \stdClass();
                $doc->$package->$resource = $this->fetchResourceDocumentation($package, $resource);
            }
        }
    }

    public function makeDescriptionDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }
            foreach ($resourcenames as $resource) {
                $doc->$package->$resource = new \stdClass();
                /**
                 * Get the metadata properties
                 */
                $metadata = DBQueries::getMetaData($package, $resource);
                if (!empty($metadata)) {
                    foreach ($metadata as $name => $value) {
                        if ($name != "id" && $name != "resource_id") {
                            $doc->$package->$resource->$name = $value;
                        }
                    }
                }
                $doc->$package->$resource = $this->fetchResourceDescription($package, $resource);
            }
        }
    }

    public function createDELETEDocumentation() {
        $d = new \stdClass();
        $d->description = "Delete a remote source type definition.";
        $d->httpMethod = "DELETE";       
        return $d;
    }

    public function createPUTDocumentation($doc){

        $media_type = "application/remote";
        $doc->$media_type = new \stdClass();

        $remote_resource = new RemoteResourceCreator("", "", array()); 
        $doc->$media_type->description = "Creates a new remote resource by executing a HTTP PUT on an URL formatted like " . Config::get("general", "hostname") . Config::get("general", "subdir") . "packagename/newresource. The base_uri needs to point to another The DataTank instance.";        
        $doc->$media_type->parameters = $remote_resource->documentParameters();           
    }

    /*
     * This object contains all the information
     * FROM the last used
     * requested object. This way we wont have to call the remote resource
     * every single call to this factory. If we receive a call
     * for another resource, we replace it by the newly asked factory.
     */
    private function fetchResourceDocumentation($package, $resource) {
        $result = DBQueries::getRemoteResource($package, $resource);
        if (sizeof($result) == 0) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($package . "/" . $resource), $exception_config);
        }
        $url = $result["url"] . "TDTInfo/Resources/" . $result["package"] . "/" . $result["resource"] . ".php";
        $options = array("cache-time" => 5); //cache for 5 seconds
        $request = Request::http($url, $options);

        if (isset($request->error)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($url), $exception_config);
        }
        $data = unserialize($request->data);
        $remoteResource = new \stdClass();

        if (!isset($remoteResource->documentation) && isset($data[$resource]) && isset($data[$resource]->documentation)) {
            $remoteResource->documentation = $data[$resource]->documentation;
        } else {
            $remoteResource->documentation = new \stdClass();
        }

        if (isset($data[$resource]->parameters)) {
            $remoteResource->parameters = $data[$resource]->parameters;
        } else {
            $remoteResource->parameters = array();
        }

        if (isset($data[$resource]->requiredparameters)) {
            $remoteResource->requiredparameters = $data[$resource]->requiredparameters;
        } else {
            $remoteResource->requiredparameters = array();
        }
        return $remoteResource;
    }

    /*
     * This object contains all the information
     * FROM the last used
     * requested object. This way we wont have to call the remote resource
     * every single call to this factory. If we receive a call
     * for another resource, we replace it by the newly asked factory.
     */

    private function fetchResourceDescription($package, $resource) {
        $result = DBQueries::getRemoteResource($package, $resource);
        if (sizeof($result) == 0) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($package . "/" . $resource), $exception_config);
        }
        $url = $result["url"] . "TDTInfo/Resources/" . $result["package"] . "/" . $result["resource"] . ".php";
        $options = array("cache-time" => 5); //cache for 5 seconds
        $request = Request::http($url, $options);

        if (isset($request->error)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($url), $exception_config);
        }
        $data = unserialize($request->data);
        $remoteResource = new \stdClass();
        $remoteResource->package_name = $package;
        $remoteResource->remote_package = $result["package"];
        if (!isset($remoteResource->documentation) && isset($data[$resource]) && isset($data[$resource]->documentation)) {
            $remoteResource->documentation = $data[$resource]->documentation;
        } else {
            $remoteResource->documentation = new \stdClass();
        }

        $remoteResource->resource = $resource;
        $remoteResource->base_url = $result["url"];
        $remoteResource->resource_type = "remote";
        if (isset($data[$resource]->parameters)) {
            $remoteResource->parameters = $data[$resource]->parameters;
        } else {
            $remoteResource->parameters = array();
        }

        if (isset($data[$resource]->requiredparameters)) {
            $remoteResource->requiredparameters = $data[$resource]->requiredparameters;
        } else {
            $remoteResource->requiredparameters = array();
        }
        return $remoteResource;
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
        
        $resources->remote = new \stdClass();

        $resources->remote->put = $this->createPUTDocumentation($doc);
        $resources->remote->delete = $this->createDELETEDocumentation($doc);

        $doc->resources = $resources;
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