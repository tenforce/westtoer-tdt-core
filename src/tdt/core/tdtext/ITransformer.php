<?php

namespace tdt\core\tdtext;

interface ITransformer {
   /**
    * Add or edit an object from the moment is read into memory
    * @param $resourceconfiguration contains the identifier of a resource and the configuration
    * @param $object is the data object
    */
    function transform($resourceconfiguration, &$object);
}