<?php

/**
 * FR_Presenter_php
 */
class FR_Presenter_excel extends FR_Presenter_common {

    public function __construct($module) {
        parent::__construct($module);
    }

    public function display($tplFile = null) {
        if (file_exists($tplFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($tplFile));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($tplFile));
            ob_clean();
            flush();
            readfile($tplFile);
        }
    }

    public function __destruct() {
        parent::__destruct();
    }

}
