<?php

/**
 * Abstract class to delete a resource
 *
 * @package The-Datatank/model/resources/delete
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\delete;

abstract class ADeleter {

    protected $package;
    protected $resource;
    protected $RESTparameters;

    public function __construct($package, $resource) {
        $this->package = $package;
        $this->resource = $resource;        
    }

    /**
     * This method deletes a resource definition.
     */
    abstract public function delete();
}