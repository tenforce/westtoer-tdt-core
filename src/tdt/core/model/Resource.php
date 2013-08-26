<?php
namespace tdt\core\model;
use tdt\negotiators\ContentNegotiator;
use tdt\negotiators\LanguageNegotiator;

/**
 * This class is a singleton which stores all the information about the currently requested resource
 * @author Pieter Colpaert
 */
class Resource {

    private static $me;

    // All meta-data about the resource that can be public
    private $public = array();

    // All data needed in order to make the call
    private $private = array();
    
    private function __construct(){}

    public function getInstance(){
        if(!isset(self::$me)){
            self::$me = new Resource();
        }
        return self::$me;
    }

    public function setResourceIdentifier($resourceidentifier){
        $this->public["resourceidentifier"] = $resourceidentifier;
    }

    public function getResourceIdentifier(){
        if(isset($this->public["resourceidentifier"]))
            return $this->public["resourceidentifier"];
        else
            return null;
    }

    
    public function setParameters($uritemplateparameters){
        // Merge URI template parameters and GET parameters 
        $this->public["parameters"] = array_merge($_GET,$uritemplateparameters);
    }

    /**
     * @return the parameters set by the request
     */
    public function getParameters(){
        return $this->public["parameters"];
    }
    
    
    public function setSourceConfiguration($config){
        $this->private = $config;
    }

    /**
     * Use this function if you want to know where the data comes from and how it has been or should be processed.
     * @return the configuration of the source as the administrator PUTed it
     */
    public function getSourceConfiguration(){
        return $this->private;
    }

    public function getResponseMediatype(){
        //read accept header
        if ($formats == null) {
            $formats = array("turtle", "ntriples", "rdfxml", "xml", "csv", "json");
        }

        // Always give format set throught the URL the upperhand
        if ($this->format_through_url !== "") {
            return $this->format_through_url;
        } else {
            $cn = new ContentNegotiator(Config::get("general", "defaultformat"));

            $log = new Logger('Controller');
            $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::INFO));
            $log->addInfo("Doing content negotiation.");

            $format = $cn->pop();
            while (!in_array($format, $formats) && $cn->hasNext()) {
                $format = $cn->pop();
            }
            return $format;
        }
    }

    /**
     * Reads content-type header if set. Otherwise, make a guess or return null if no body given.
     * @return 
     */
    public function getRequestMediatype(){
        
        
        
    }

    public function getStatistics(){
        //get an overview of the statistics
    }
}

