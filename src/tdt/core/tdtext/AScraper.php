<?php

namespace tdt\core\tdtext;

abstract class AScraper implements IDefinitionsEditor, IRouteEditor {

    /**
     * @param $resourceidentifier the path of the 
     */
    public function __construct($resourceidentifier){
        $this->resourceidentifier = $resourceidentifier;  
    }

    /**
     * @override
     */
    public function editRoutes(&$routes){
        $routes[$resourceidentifier] = get_class($this);
    }

    public function editDefinitions(&$definitions){
        //TODO: edit definitions so that our definition is included
//        $package = 
        $definitions[$package][$path] = "test/scraper";
        
    }

    /**
     * Returns an array according to the discovery API of parameter objects.
     * They include the parameters needed to read a resource which's source uses this strategy.
     */
    abstract function getGETParameters();

    abstract function read($parameters);
    
    abstract function getDocumentation();
}