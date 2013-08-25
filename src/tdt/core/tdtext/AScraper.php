<?php

namespace tdt\core\tdtext;

abstract class AScraper implements IDefinitionsEditor, IRoutesEditor {

    /**
     * @param $resourceidentifier the path of the 
     */
    public function __construct(){
        $this->resourceidentifier = $this->getID();
    }

    public abstract function getID();
    

    /**
     * @override
     */
    public function editRoutes(&$routes){
        $routes["GET | " . $this->resourceidentifier . '\.?(?P<format>[^?]+)?.*\??(.*)'] = get_class($this);
    }

    public function GET($matches){
        if(!isset($matches["format"])){
            $matches["format"] = "";
        }
        
        $parameters = $_GET;
        
        $o = $this->read($parameters);
        if(!is_array($o) && !is_object($o)){
            $new = new \stdClass();
            $new->string = $o;
            $o = $new;
        }   

        $formatter = new \tdt\formatters\Formatter(strtoupper($matches["format"]));
        $formatter->execute($this->getID(),$o);

    }

    public function editDefinitions(&$definitions){
        //TODO: edit definitions so that our definition is included
        
        //$definitions[$package][$path] = "test/scraper";
        
    }

    /**
     * Returns an array according to the discovery API of parameter objects.
     * They include the parameters needed to read a resource which's source uses this strategy.
     */
    abstract function getGETParameters();

    abstract function read($parameters);
    
    abstract function getDocumentation();
}