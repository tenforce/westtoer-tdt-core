<?php

/**
 * This is a class which will return all the available resources along with the documentation of that resource in this DataTank
 *
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\core\info;

use tdt\core\model\resources\read\AReader;
use tdt\core\model\ResourcesModel;
use tdt\core\utility\Config;

class Dcat extends AReader {

    public static function getParameters() {
        return array();
    }

    public static function getRequiredParameters() {
        return array();
    }

    public function setParameter($key, $val) {
    }

    public function read() {
        $resmod = ResourcesModel::getInstance(Config::getConfigArray());
        return $resmod->getDCATDocumentation();
    }

    public static function getDoc() {
        return "This resource contains the documentation of all the resources.";
    }
}