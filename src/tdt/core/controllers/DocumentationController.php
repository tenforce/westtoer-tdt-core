<?php
namespace tdt\core\controllers;
use tdt\core\utility\Config;

class DocumentationController{
    public function GET($matches){
        $location = Config::get("general", "hostname") . Config::get("general", "subdir");

        include(APPPATH . "template/header.php");
        include(APPPATH . "template/documentation.php");
        include(APPPATH . "template/footer.php");
    }
}