<?php

namespace tdt\core\tdtext;

interface IAfterInitialization {
    public function execute(&$config);
}