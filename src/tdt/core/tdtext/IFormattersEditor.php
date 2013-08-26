<?php

namespace tdt\core\tdtext;

interface IFormattersEditor {
    /**
    * Add or edit formatters in this array
    */
    function editFormatters(&$formatters);

    public function isEnabled();
}