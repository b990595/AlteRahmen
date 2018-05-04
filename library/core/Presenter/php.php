<?php

/**
 * FR_Presenter_php
 */
class FR_Presenter_php extends FR_Presenter_common {

    public function __construct(FR_WebModule $module) {
        parent::__construct($module);
    }

    public function display($tplFile) {

        // Template
        if (trim($this->module->masterTemplate) != "" && file_exists(FR_TEMPLATES_PATH . "/" . $this->module->masterTemplate . "/prepend.phtml")) {
            include(FR_TEMPLATES_PATH . "/" . $this->module->masterTemplate . "/prepend.phtml");
        }


        // ************************************************************
        // View (/tpl) file
        // ************************************************************
        $__data = $this->module->getData();
        if (is_array($__data)) {
            foreach (array_keys($__data) as $d) {
                $$d = $__data[$d];
            }
        }

        $path = str_replace("*", $this->module->moduleName, FR_VIEWS_PATH);
        include($path . "/" . $tplFile . ".phtml");
        // ************************************************************
        // Template
        if (trim($this->module->masterTemplate) != "" && file_exists(FR_TEMPLATES_PATH . "/" . $this->module->masterTemplate . "/append.phtml")) {
            include(FR_TEMPLATES_PATH . "/" . $this->module->masterTemplate . "/append.phtml");
        }
    }

    public function __destruct() {
        parent::__destruct();
    }

}
