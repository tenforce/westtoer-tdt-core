<?php

/**
 * This class creates a remote resource
 *
 * @package The-Datatank/model/resources/create
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 * @author Pieter Colpaert
 */

namespace tdt\core\model\resources\create;

use tdt\core\model\DBQueries;
use tdt\exceptions\TDTException;
use tdt\core\utility\Config;
use tdt\core\utility\Request;

class RemoteResourceCreator extends ACreator {

    public function __construct($package, $resource) {
        parent::__construct($package, $resource);
    }

    /**
     * Overrides previously defined method for getting the right parameters.
     * It first calls upon the parent. Then it extends the parent required parameters with base_url and package_name
     */
    public function documentParameters() {

        $parameters = parent::documentParameters();
        $parameters["base_url"] = array(
            "description" => "The base url from the remote resource.",
            "required" => true,
        );

        $parameters["package_name"] = array(
            "description" => "The remote package name of the remote resource.",
            "required" => true,
        );

        $parameters["resource_name"] = array(
            "description" => "The remote resource name of the remote resource. Default value is the local resource_name.",
            "required" => true,
        );

        return $parameters;
    }

    /**
     * This function quickly sets the parameters as part of the class
     */
    public function setParameter($key, $value) {
        $this->$key = $value;
    }

    /**
     * Create the resource definition.
     * The parameters that have been defined can be access via $this.
     */
    public function create() {
        if (!isset($this->resource_name) && $this->resource != "") {
            $this->resource_name = $this->resource;
        }

        if (!isset($this->package_name) && $this->package != "") {
            $this->package_name = $this->package;
        }

        // check for loop
        if(strrpos(strtolower($this->base_url), strtolower(Config::get("general", "hostname") . Config::get("general", "subdir"))) !== false){
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Can't add yourself as remote resource (loop)."), $exception_config);
        }

        // format the base url
        $base_url = $this->base_url;
        if (substr(strrev($base_url), 0, 1) != "/") {
            $base_url .= "/";
        }

        // 1. First check if it really exists on the remote server
        $url = $base_url . "TDTInfo/Resources/" . $this->package_name . "/" . $this->resource_name . ".php";
        $options = array("cache-time" => 1); //cache for 1 second
        $request = Request::http($url, $options);
        if (isset($request->error)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($url), $exception_config);
        }
        $object = unserialize($request->data);
        if (!isset($object[$this->resource_name])) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Resource does not exist on the remote server"), $exception_config);
        }

        // 2. Check if the resource on the server contains an "orginal" resource URI and take that URI instead if exists and reload everything
        if (isset($object["base_url"])) {
            $base_url = $object["base_url"];
            $packagename = $object["remote_package"];
        }

        // 3. store it
        $package_id = parent::makePackage($this->package);
        $resource_id = parent::makeResource($package_id, $this->resource, "remote");
        DBQueries::storeRemoteResource($resource_id, $this->package_name, $this->resource_name, $base_url);
    }

}