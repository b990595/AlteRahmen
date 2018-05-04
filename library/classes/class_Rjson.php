<?php

class Rjson {

    public static function arrayToJSON($array) {
        return json_encode($array);
    }

    public static function JSONToArray($json) {
        return json_decode($json, true);
    }

}
