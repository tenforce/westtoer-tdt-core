<?php

namespace tdt\core\tdtext;

interface IFormattersEditor {
    /**
    * Add or edit formatters in this array
    */
    abstract function editFormatters(&$formatters);
}