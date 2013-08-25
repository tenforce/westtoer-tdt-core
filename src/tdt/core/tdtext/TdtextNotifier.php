<?php
/**
 * Against all odds, the TdtextNotifier is an Observer.
 * This is a singleton: as only one notifier will notify all actions
 *
 * It will observe all the events happening, will investigate them and it will notify all instances of certain interfaces in the tdtext namespace.
 */
namespace tdt\core\tdtext;

class TdtextNotifier {
    private static $me;
    private $extensions;

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
        switch($eventname){
            case "routes_ready" :
                echo "routes are loaded";
                break;
            case "":
                break;
            default:
                echo "unknown eventname given";
        }
        var_dump($info);
    }
    

}
