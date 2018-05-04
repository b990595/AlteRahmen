<?php

class HttpRequest {

    public static function RequestOrDie($name) {
        $g = isset($_REQUEST[$name]) ? $_REQUEST[$name] : "__/&%/&%¤((falsevaerdi__";
        if ($g == "__/&%/&%¤((falsevaerdi__") {
            throw new Exception($name . " not found.");
        } else {
            return $g;
        }
    }

    public static function RequestOrBlank($name) {
        $g = isset($_REQUEST[$name]) ? $_REQUEST[$name] : "";
        return $g;
    }

    public static function RequestOrZero($name) {
        $g = isset($_REQUEST[$name]) ? $_REQUEST[$name] : 0;
        return $g;
    }

    public static function RequestOrFalse($name) {
        $g = isset($_REQUEST[$name]) ? $_REQUEST[$name] : false;
        return $g;
    }

    public static function RequestOrValue($name, $value) {
        $g = isset($_REQUEST[$name]) ? $_REQUEST[$name] : $value;
        return $g;
    }

    public static function PostOrDie($name) {
        $g = isset($_POST[$name]) ? $_POST[$name] : "__/&%/&%¤((falsevaerdi__";
        if ($g == "__/&%/&%¤((falsevaerdi__") {
            throw new Exception($name . " not found.");
        } else {
            return $g;
        }
    }

    public static function PostOrBlank($name) {
        $g = isset($_POST[$name]) ? $_POST[$name] : "";
        return $g;
    }

    public static function PostOrZero($name) {
        $g = isset($_POST[$name]) ? $_POST[$name] : 0;
        return $g;
    }

    public static function PostOrFalse($name) {
        $g = isset($_POST[$name]) ? $_POST[$name] : false;
        return $g;
    }

    public static function PostOrValue($name, $value) {
        $g = isset($_POST[$name]) ? $_POST[$name] : $value;
        return $g;
    }

    public static function GetOrDie($name) {
        $g = isset($_GET[$name]) ? $_GET[$name] : "__/&%/&%¤((falsevaerdi__";
        if ($g == "__/&%/&%¤((falsevaerdi__") {
            throw new Exception($name . " not found.");
        } else {
            return $g;
        }
    }

    public static function GetOrBlank($name) {
        $g = isset($_GET[$name]) ? $_GET[$name] : "";
        return $g;
    }

    public static function GetOrZero($name) {
        $g = isset($_GET[$name]) ? $_GET[$name] : 0;
        return $g;
    }

    public static function GetOrFalse($name) {
        $g = isset($_GET[$name]) ? $_GET[$name] : false;
        return $g;
    }

    public static function GetOrValue($name, $value) {
        $g = isset($_GET[$name]) ? $_GET[$name] : $value;
        return $g;
    }

}
