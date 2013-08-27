<?php

/**
 * This is a class which will return all the possible admin calls to this datatank
 *
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\core;

use tdt\core\model\ResourcesModel;
use tdt\core\utility\Config;
use tdt\exceptions\TDTException;

class Discovery{

    public function create() {
        $resmod = ResourcesModel::getInstance(Config::getConfigArray());
        $result_object = $resmod->getDiscoveryDoc();

        return $result_object;
    }

    /**
     * check if a property is set in the object, the property to compare with is in lower case.
     */
    private function isPropertySet($object,$lower_property){
        $properties = get_object_vars($object);

        foreach($properties as $property => $value){
            if(strtolower($property) == $lower_property){
                return $property;
            }
        }
        return false;
    }
}

