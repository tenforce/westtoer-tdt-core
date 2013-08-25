<?php

namespace tdt\core\tdtext;

interface IRouteEditor {
    abstract function editRoutes(&$routes); //add, remove or edit routes
}