<?php

namespace tdt\core\tdtext;

interface IDefinitionsEditor {
    public function isEnabled();
    public function editDefinitions(&$definitions);
}