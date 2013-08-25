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
        $routes["GET | " . $this->resourceidentifier] = get_class($this);
    }

    public function GET(){
        $parameters = $_GET;
        
        echo $this->read($parameters);
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