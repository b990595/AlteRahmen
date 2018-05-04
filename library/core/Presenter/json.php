<?php

/**
 * FR_Presenter_php
 */
class FR_Presenter_json extends FR_Presenter_common {

    public function __construct(FR_RestModule $module) {
        parent::__construct($module);
    }

    public function display($data = null, $code, $status, $message = "") {
        // Sikre pæn tal formatering ..
        if (is_numeric($data)) {
            $data = $data / 1;
        }

        $out = json_encode(array(
            "code" => $code,
            "status" => $status,
            "message" => $message,
            "data" => $data
        ));

        header("Content-type: application/json; charset=utf-8");
        echo $out;
    }

    public function __destruct() {
        parent::__destruct();
    }

}
