<?php

/**
 * FR_Presenter_php
 */

class FR_Presenter_raw extends FR_Presenter_common {

	
    public function __construct($module) {
        parent::__construct($module);
    }
    
    public function display($tplFile = null) {
        echo $tplFile;        
    }

    public function __destruct() {
        parent::__destruct();
    }

}
