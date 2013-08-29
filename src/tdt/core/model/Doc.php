<?php

/**
 * Doc is a visitor that will visit every ResourceFactory and ask for their documentation. It is cached because this process is quite heavy.
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
use tdt\cache\Cache;
use tdt\core\utility\Config;
use tdt\formatters\Formatter;

class Doc {
    
    private $hostname;
    private $subdir;

    public static $MEDIA_TYPE_PREFIX = "application/tdt.";
    public static $MEDIA_TYPE_SUFFIX = "";

    private $dcat_namespaces = array(
        "dcat" => "http://www.w3.org/ns/dcat#",
        "dct"  => "http://purl.org/dc/terms/",
        "rdf"  => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
        "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
        "owl"  => "http://www.w3.org/2002/07/owl#",
    );

    public function __construct() {
        $this->hostname = Config::get("general", "hostname");
        $this->subdir = Config::get("general", "subdir");
    }

    /*
     * prepare the caching configuration
     */
    private function prepareCacheConfig() {
        $cache_config = array();
        $cache_config["system"] = Config::get("general", "cache", "system");
        $cache_config["host"] = Config::get("general", "cache", "host");
        $cache_config["port"] = Config::get("general", "cache", "port");

        return $cache_config;
    }

    /**
     * This function will visit any given factory and ask for the documentation of the resources they're responsible for.
     * @return Will return the entire documentation array which can be used by TDTInfo/Resources. It can also serve as an internal checker for availability of packages/resources
     */
    public function visitAll($factories) {
        $c = Cache::getInstance($this->prepareCacheConfig());
        $doc = $c->get($this->hostname . $this->subdir . "documentation");
        if (is_null($doc)) {
            $doc = new \stdClass();
            foreach ($factories as $factory) {
                $factory->makeDoc($doc);
            }
            $c->set($this->hostname . $this->subdir . "documentation", $doc, 60 * 60 * 60); // cache it for 1 hour by default
        }
        return $doc;
    }

    /**
     * This function returns all packages present in the datatank
     */
    public function visitAllPackages() {
        $c = Cache::getInstance($this->prepareCacheConfig());
        $doc = $c->get($this->hostname . $this->subdir . "packagedocumentation");
        if (is_null($doc)) {
            $doc = new \stdClass();
            $packages = DBQueries::getAllPackages();
            foreach ($packages as $package) {
                $packagename = $package->package_name;
                $doc->$packagename = new \stdClass();
            }

            $coreResourceFactory = new CoreResourceFactory();
            $packages = $coreResourceFactory->getAllPackagesDoc();

            foreach ($packages as $package) {
                $doc->$package = new \stdClass();
            }

            $c->set($this->hostname . $this->subdir . "packagedocumentation", $doc, 60 * 60 * 60); // cache it for 1 hour by default
        }
        return $doc;
    }

    /**
     * This function will visit any given factory and ask for the description of the resources they're responsible for.
     * @return Will return the entire description array which can be used by TDTAdmin/Resources.
     */
    public function visitAllDescriptions($factories) {
        $c = Cache::getInstance($this->prepareCacheConfig());
        $doc = $c->get($this->hostname . $this->subdir . "descriptiondocumentation");
        if (is_null($doc)) {
            $doc = new \stdClass();
            foreach ($factories as $factory) {
                $factory->makeDescriptionDoc($doc);
            }
            $c->set($this->hostname . $this->subdir . "descriptiondocumentation", $doc, 60 * 60 * 60); // cache it for 1 hour by default
        }
        return $doc;
    }

    /**
     * Visits all the factories in order to get the admin documentation, which elaborates on the admin functionality
     * @return $mixed An object which holds the documentation on how to perform admin functions such as creation, deletion and updates.
     */
    public function visitAllAdmin($factories) {

        $c = Cache::getInstance($this->prepareCacheConfig());
        $doc = $c->get($this->hostname . $this->subdir . "admindocumentation");

        if (is_null($doc)) {

            $doc = new \stdClass();
            $doc->protocol = "rest";            
            $doc->rootUrl = $this->hostname . $this->subdir . "";
            $doc->resources = new \stdClass();

            // The definition section of the discovery document.
            $doc->resources->definitions = new \stdClass();
            $doc->resources->definitions->methods = new \stdClass();

            // Fill in the put method of the definition resource.
            $doc->resources->definitions->methods->put = new \stdClass();
            $doc->resources->definitions->methods->put->httpMethod = "PUT";
            $doc->resources->definitions->methods->put->contentType = "{mediaType}+" . "[x-www-form-urlencoded|json]"; 
            $doc->resources->definitions->methods->put->path = "/definitions/{identifier}";
            $doc->resources->definitions->methods->put->description = "Add a resource definition identified by {identifier} that allows the publication of data. The identifier has to exist out of 1 or more collection identifier and 1 resource identifier (e.g. city/statistics/demography).";

            $doc->resources->definitions->methods->put->mediaType = new \stdClass();
            foreach ($factories as $factory) {
                $factory->createPUTDocumentation($doc->resources->definitions->methods->put->mediaType);
            }

            // Fill in the delete method of the definition resource.
            $doc->resources->definitions->methods->delete = new \stdClass();
            $doc->resources->definitions->methods->delete->httpMethod = "DELETE";
            $doc->resources->definitions->methods->delete->path = "/definitions/{identifier}";
            $doc->resources->definitions->methods->delete->description = "Remove a resource definition identified by the {identifier}, the corresponding uri that publishes the data will also be deleted.";

            // Fill in the get method of the definition resource.
            $doc->resources->definitions->methods->get = new \stdClass();
            $doc->resources->definitions->methods->get->httpMethod = "GET";
            $doc->resources->definitions->methods->get->path = "/definitions";
            $doc->resources->definitions->methods->get->description = "Retrieve a list of the resource definitions.";            

            // The info section of the discovery document.
            $doc->resources->info = new \stdClass();
            $doc->resources->info->resources = new \stdClass();

            // Add the datasets.
            $doc->resources->info->resources->datasets = new \stdClass();
            $doc->resources->info->resources->datasets->methods = new \stdClass();
            $doc->resources->info->resources->datasets->methods->get = new \stdClass();
            $doc->resources->info->resources->datasets->methods->get->httpMethod = "GET";
            $doc->resources->info->resources->datasets->methods->get->path = "/info/datasets";
            $doc->resources->info->resources->datasets->methods->get->description = "Retrieve information about the available published datasets.";

            // Add the formatters.
            $doc->resources->info->resources->formatters = new \stdClass();
            $doc->resources->info->resources->formatters->methods = new \stdClass();
            $doc->resources->info->resources->formatters->methods->get = new \stdClass();
            $doc->resources->info->resources->formatters->methods->get->httpMethod = "GET";
            $doc->resources->info->resources->formatters->methods->get->path = "/info/formatters";
            $doc->resources->info->resources->formatters->methods->get->description = "Retrieve the available formatters that can be used to retrieve/visualize data in.";

            // Add the DCAT documentation.
            $doc->resources->info->resources->dcat = new \stdClass();
            $doc->resources->info->resources->dcat->methods = new \stdClass();
            $doc->resources->info->resources->dcat->methods->get = new \stdClass();
            $doc->resources->info->resources->dcat->methods->get->httpMethod = "GET";
            $doc->resources->info->resources->dcat->methods->get->path = "/info/dcat";
            $doc->resources->info->resources->dcat->methods->get->description = "Retrieve information about the available published datasets in a DCAT vocabulary.";

            $c->set($this->hostname . $this->subdir . "admindocumentation", $doc, 60 * 60 * 60); // Cache it for 1 hour by default
        }           
        return $doc;
    }

    /**
     * Visits all of the factories to request the documentation in a ARC2 graph object
     * using DCAT and DC vocabulary.
     */ 
    public function getDCATDocumentation($factories){

        $c = Cache::getInstance($this->prepareCacheConfig());
        $parser = $c->get($this->hostname . $this->subdir . "dcatdocumentation");
        if (is_null($parser)) {            

            $rdf_string = "";
            foreach($this->dcat_namespaces as $prefix => $ns){
                $rdf_string .= "@prefix $prefix: <$ns>";
            }

            // Get all of the resource identifiers, these have to be linked for they
            // will be listed as part of the catalog.
            $identifier_list = "";
            $doc = get_object_vars($this->visitAll($factories));
            foreach($doc as $package => $resource_object){
                $resource = get_object_vars($resource_object);
                reset($resource);
                $resource_name = key($resource);
                $identifier_list .= "<" . $this->hostname . $this->subdir . $package . "/" . $resource_name . ">, ";

            }

            $identifier_list = rtrim($identifier_list, ", ");            

            $rdf_string .= '<dcat:catalog>                        
                                a dcat:Catalog;
                                dct:title "Datatank DCAT catalog";
                                dct:description "This is a datatank catalog providing DCAT vocabulary based information about its published datasources." ;
                                dcat:dataset ' . $identifier_list . ' .';                                
           
            // The factories will create triples in turtle syntax
            foreach ($factories as $factory) {
                $rdf_string .= $factory->createDCATDocumentation();              
            }            

            $parser = \ARC2::getTurtleParser();            
            $parser->parse('', $rdf_string);            
            $c->set($this->hostname . $this->subdir . "dcatdocumentation", $parser, 60 * 60 * 60); // cache it for 1 hour by default
        }

        return $parser;
    }

    /**
     * Gets the documentation on the formatters
     * @return $mixed An object which holds the documentation about all the formatters.
     */
    public function visitAllFormatters() {
        $c = Cache::getInstance($this->prepareCacheConfig());
        $doc = $c->get($this->hostname . $this->subdir . "formatterdocs");
        $ff = new Formatter();
        if (is_null($doc)) {
            $doc = $ff->getFormatterDocumentation();
            $c->set($this->hostname . $this->subdir . "formatterdocs", $doc, 60 * 60 * 60);
        }
        return $doc;
    }
}