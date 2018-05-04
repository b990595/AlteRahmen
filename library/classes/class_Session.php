<?php

class Session {

    private static $isStarted  = false;

    private static function init() {
        if (!self::$isStarted) {
            $ses = session_start();
            if (!$ses) {
                Throw new Exception("Could not start session");
            }
            self::$isStarted = true;
        }
    }

    public static function set($key, $value, $override = true) {
        self::init();
        if ($override) {
              $_SESSION[$key] = $value;
        } else {
            if (!isset($_SESSION[$key])){
                $_SESSION[$key] = $value; 
            }else{
                Throw new Exception("Session ".$key." is allready set.");
            }
        }
        return true;
    }
    
    public static function setToArray($key, $key2, $value, $override = true) {
        self::init();
        if ($override) {
              $_SESSION[$key][$key2] = $value;
        } else {
            if (!isset($_SESSION[$key][$key2])){
                $_SESSION[$key][$key2] = $value; 
            }else{
                Throw new Exception("Session ".$key."/".$key2." is allready set.");
            }
        }
        return true;
    }
    
    public static function getFromArray($key1, $key2, $valueIfNotFound = null){
        self::init();
        if (isset($_SESSION[$key1][$key2])){
            return $_SESSION[$key1][$key2]; 
        }else{
            return $valueIfNotFound;
        }
    }
    
   
    public static function deleteFromArray($key1, $key2){
        self::init();
        unset($_SESSION[$key1][$key2]);
    }
    
    public static function delete($key){
        self::init();
        unset($_SESSION[$key]);
    }
    
    public static function GetOrDie($key) {
        self::init();
        $g = isset($_SESSION[$key]) ? $_SESSION[$key] : "__/&%/&%¤((falsevaerdi__";
        if ($g == "__/&%/&%¤((falsevaerdi__") {
            throw new Exception($key . " not found.");
        } else {
            return $g;
        }
    }

    public static function GetOrBlank($key) {
        self::init();
        $g = isset($_SESSION[$key]) ? $_SESSION[$key] : "";
        return $g;
    }

    public static function GetOrZero($key) {
        self::init();
        $g = isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
        return $g;
    }

    public static function GetOrFalse($key) {
        self::init();
        $g = isset($_SESSION[$key]) ? $_SESSION[$key] : false;
        return $g;
    }

    public static function GetOrValue($key, $value) {
        self::init();
        $g = isset($_SESSION[$key]) ? $_SESSION[$key] : $value;
        return $g;
    }

    public static function getUserdataNavn(){
        self::init();
        $value =  isset($_SESSION['userdata']['data']['medarbejder_navn'])?trim($_SESSION['userdata']['data']['medarbejder_navn']):false;
        if ($value){
            return $value;
        }
        throw new Exception("Navn not found in session.");
    }


    public static function getUserdataFilial(){
        self::init();
        $value =  isset($_SESSION['userdata']['data']['filial'])?trim($_SESSION['userdata']['data']['filial']):false;
        if ($value){
            return $value;
        }
        throw new Exception("Filial not found in session.");
    }

    public static function getUserdataAfdelingKey(){
        self::init();
        $value =  isset($_SESSION['userdata']['data']['afdeling_key'])?trim($_SESSION['userdata']['data']['afdeling_key']):false;
        if ($value){
            return $value;
        }
        throw new Exception("AfdelingKey not found in session.");
    }



    public static function getTuser(){
        self::init();
        $g = isset($_SESSION['userid'])?trim($_SESSION['userid']):"__/&%/&%¤((falsevaerdi__";
        if ($g == "__/&%/&%¤((falsevaerdi__") {
            throw new Exception("Tuser not found in session.");
        } else {
            return $g;
        }
    }


    public static function getBasicAuth(){
        self::init();
        $tmpSession = self::GetOrDie("userdata");
        return "Basic " . base64_encode($tmpSession["userid"] . ":" . $tmpSession["password"]);
    }

    public static function getUserTicket(){
        self::init();
        $tmpSession = self::GetOrDie("userdata");
        return base64_encode($tmpSession["userid"] . ":" . $tmpSession["password"]);
    }

}
