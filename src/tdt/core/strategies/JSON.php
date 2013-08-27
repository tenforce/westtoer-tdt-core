<?php

/**
 * An abstract class for JSON data
 *
 * @package The-Datatank/model/resources/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\strategies;

use tdt\core\model\resources\AResourceStrategy;
use tdt\exceptions\TDTException;
use tdt\core\utility\Request;
use app\core\Config;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class JSON extends AResourceStrategy {

    public function read(&$configObject, $package, $resource) {
        $data = Request::http($configObject->uri);

        if(empty($configObject->pk)){
            return json_decode($data->data);
        }else{
            $pk = $configObject->pk;
            $object = json_decode($data->data);
            $result_object = array();
            if(is_array($object)){
                
                foreach($object as $entry){
                    if(is_array($entry)){
                        $pk_value = $entry[$pk];                        
                    }else{
                        $pk_value = $entry->$pk;
                    }
                    
                    // Log double primary key occurences.
                    $log = new Logger('JSON');
                    $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::INFO));
                    $log->addInfo("The primary key $pk_value, already exists, overwriting it with the new value.");

                    $result_object[$pk_value] = $entry;
                }
                return $result_object;
            }else{

                $log = new Logger('JSON');
                $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::INFO));
                $log->addInfo("A primary key was provided, but the json object was not an array. Returning JSON object as is.");
                return $object;
            }
        }
        
    }

    public function isValid($package_id, $generic_resource_id) {
        $data = Request::http($this->uri);
        $result = json_decode($data->data);
        if (!$result) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Could not transform the json data from " . $this->uri . " to a php object model, please check if the json is valid."), $exception_config);
        }
        return true;
    }

    public function documentReadRequiredParameters() {
        return array();
    }

    public function documentUpdateRequiredParameters() {
        return array();
    }

    public function documentCreateParameters() {
        return array(
            "uri" => array(
                "description" => "The uri to the json document.",
                "required" => true,
            ),
            "pk" => array(
                "description" => "The primary key to which objects are mapped to, this can only be done with an json array of objects.",
                "required" => true,
            ),
        );
    }

    public function documentReadParameters() {
        return array();
    }

    public function documentUpdateParameters() {
        return array();
    }

    public function getFields($package, $resource) {
        return array();
    }

}