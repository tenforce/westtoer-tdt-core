<?php
/**
 * Against all odds, the TdtextNotifier is an Observer.
 * This is a singleton: as only one notifier will notify all actions
 *
 * It will observe all the events happening, will investigate them and it will notify all instances of certain interfaces in the tdtext namespace.
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
    
    public function getAllExtensions($interface_name){
//        var_dump(get_declared_classes());
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
                    $ext->$methodname($info);
                }
            }
        }
    }
    

}
