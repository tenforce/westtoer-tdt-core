<?php

namespace tdt\core\tdtext;

abstract class AFormatter implements IFormattersEditor {
    protected $name;
    protected $mediatype;
    /**
     * @param $name e.g. "JSON"
     */
    public function __construct($name, $mediatype = "text/html"){
        $this->name = $name;
    }

    function editFormatters(&$formatters){
        $formatters[$this->name] = get_class($this); //Question: does this work across namespaces?
    }

    /**
     * Returns an array according to the discovery API of parameter objects.
     * They include extra parameters which may be given to a formatter
     * e.g. for a chart visualization, you might want to ask which fields to use, or for CSV, you might want to ask for the delimiter to use.
     */
    abstract function getGETParameters();

    /**
     * when reading the a resource configured with this strategy, this is what's going to happen.
     * @param $parameters both contains the formatter parameters as the resource parameter
     * @param $resourceconfiguration contains the resourceidentifier and the config parameters
     * @param $object the object to print
     */
    abstract function printBody($resourceconfiguration, $parameters, $object);
}