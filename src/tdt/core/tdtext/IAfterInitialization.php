<?php

namespace tdt\core\tdtext;

interface IAfterInitialization {
    public function isEnabled();
    public function execute(&$config);
}