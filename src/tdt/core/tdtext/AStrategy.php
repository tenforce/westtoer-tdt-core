<?php

namespace tdt\core\tdtext;

abstract class AStrategy implements IAvailableStrategiesEditor {

    /**
     * Returns an array according to the discovery API of parameter objects.
     * They include the parameters needed to read a resource which's source uses this strategy.
     */
    abstract function getGETParameters();

    /**
     * Returns an array according to the discovery API of parameter objects.
     * They include documentation about whether the parameter is required or not when configuring a source of this strategy type through a PUT request.
     */
    abstract function getConfigParameters();

    /**
     * when reading the a resource configured with this strategy, this is what's going to happen.
     * The resourceconfiguration contains the resourceidentifier and the config parameters (as defined by the getConfigParameters() function)
     */
    abstract function read($resourceconfiguration, $parameters);


    public function isEnabled(){
        return true;
    }
}