<?php
/**
 * TdtextNotifier forwards all the events to the right classes implementing the necessary interfaces
 * This is a singleton: as only one notifier will notify all actions
 *
 * @author Pieter Colpaert
 */
namespace tdt\core\tdtext;
use tdt\core\utility\Config;

class TdtextNotifier {
    private static $me;
    private $extensions;

    private $interfaces = array(
        "initiated" => array(
            "class" => "IAfterInitialization",
            "method" => "execute"
        ),
        "object_ready" => array(
            "class" => "ITransformer",
            "method" => "transform"
        ),
        "definitions_loaded" => array(
            "class" => "IDefinitionsEditor",
            "method" => "editDefinitions"
        ),
        "routes_loaded" => array(
            "class" => "IRoutesEditor",
            "method" => "editRoutes"
        ),
        "formatters_loaded" => array(
            "class" => "IFormattersEditor",
            "method" => "editFormatters"
        ),
        "extraction_completed" => array(
            "class" => "ITransformer",
            "method" => "transform"
        )
    );


    private function __construct(){
    }

    /**
     * A function that should be called on configuration time.
     * @param $extensions is an array of strings containing the full class names of the extensions
     */
    public function setExtensions($extensions){
        $this->extensions = $extensions;
    }

    public static function getInstance(){
        if(!isset(self::$me)){
            self::$me = new TdtextNotifier();
        }
        return self::$me;
    }

    /**
     * This is the method that observes everything
     */
    public function update($eventname, &$info){
        
        $classes = Config::get("tdtext","classes");
        
        //get all interfaces from the extensions
        foreach($classes as $class){
            if(class_exists($class)){
                $implements = class_implements($class);
                if(isset($this->interfaces[$eventname]) && in_array("tdt\\core\\tdtext\\" . $this->interfaces[$eventname]["class"], $implements)){
                    $methodname = $this->interfaces[$eventname]["method"];
                    $ext = new $class();
                    //$ext->request =  // add info about the current request
                    if($ext->isEnabled()){
                        $ext->$methodname($info);
                    }
                }
            }
        }
    }
}
