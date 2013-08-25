<?php

namespace tdt\core\Router;

use tdt\core\utility\Glue;
use tdt\core\utility\Config;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\pages\Generator;
use tdt\core\tdtext\TdtextNotifier;


class Router {

    public $routes = array(
        "/" => "tdt\\core\\controllers\\DocumentationController"
    );
    
    public function __construct($config){
        if (count($config) > 0) {
            $config_object = json_decode(json_encode($config)); // need the config to be an object so we can validate for required properties
            $schema = file_get_contents("configuration-schema.json",true);

            $validator = new Validator();
            $validator->check($config_object, json_decode($schema));

            if (!$validator->isValid()) {

                $error_string = "";
                
                foreach ($validator->getErrors() as $error) {
                    $error_string .= "Validation for the json schema didn't validate: [".$error['property'] . "] => " . $error['message'] . "\n";
                }    
                $this->throwException(500, array("The given configuration file for the resource model does not validate. Violations are \n $error_string"));;        
            }

            Config::setConfig($config);
        }
        R::setup(Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"), Config::get("db", "user"), Config::get("db", "password"));

        $tdtext = TdtextNotifier::getInstance();
        $tdtext->setExtensions(Config::get("tdtext","classes"));
    }

    public function run(){
        $tdtext = TdtextNotifier::getInstance();
        $tdtext->update("initiated", Config::getConfigArray());

        $allroutes = $this->routes;

        $tdtext->update("routes_ready", $allroutes);

        // Only keep the routes that use the requested HTTP method
        $unsetkeys = preg_grep("/^" . strtoupper($_SERVER['REQUEST_METHOD']) . "/", array_keys($allroutes), PREG_GREP_INVERT);
        foreach ($unsetkeys as $key) {
            unset($allroutes[$key]);
        }

        $routes = array();
        // Drop the HTTP method from the route
        foreach ($allroutes as $route => $controller) {
            $route = preg_replace('/^' . strtoupper($_SERVER['REQUEST_METHOD']) . '(\s|\t)*\|(\s|\t)*/', "", trim($route));
            $routes[trim($route)] = trim($controller);
        }

        //$log->logInfo("The routes we are working with", $routes);

        try {
            // This function will do the magic.
            Glue::stick($routes);
        } catch (Exception $e) {

            // Instantiate a Logger
            $log = new Logger('router');
            $log->pushHandler(new StreamHandler(app\core\Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ERROR));

            // Generator to generate an error page
            $generator = new Generator();
            $generator->setTitle("The DataTank");

            if($e instanceof tdt\exceptions\TDTException){
                // DataTank error
                $log->addError($e->getMessage());
                set_error_header($e->getCode(), $e->getShort());
                if($e->getCode() < 500){
                    $generator->error($e->getCode(), "Sorry, but there seems to be something wrong with the call you've made", $e->getMessage());
                }else{
                    $generator->error($e->getCode(), "Sorry, there seems to be something wrong with our servers", "If you're the system administrator, please check the logs. Otherwise, check back in a short while.");
                }
            }else{
                // General error
                $log->addCritical($e->getMessage());
                set_error_header(500, "Internal Server Error");
                $generator->error($e->getCode(), "Sorry, there seems to be something wrong with our servers", "If you're the system administrator, please check the logs. Otherwise, check back in a short while.");
            }

            exit(0);
        }
        
    }

}
