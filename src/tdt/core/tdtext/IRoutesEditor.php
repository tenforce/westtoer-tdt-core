<?php

namespace tdt\core\tdtext;

interface IRoutesEditor {

    public function isEnabled();
    public function editRoutes(&$routes); //add, remove or edit routes
}