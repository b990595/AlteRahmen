<?php

class RestResultSettings {

    private static $exceptionHttpCode = 500;
    private static $exceptionHttpText = "Internal Server Error";
    private static $forceCustomNonExceptionReturn = false;
    private static $forceCustomNonExceptionReturnHttpCode = null;
    private static $forceCustomNonExceptionReturnHttpText = null;
    

    public static function getExceptionHttpCode() {
        return self::$exceptionHttpCode;
    }

    public static function getExceptionHttpText() {
        return self::$exceptionHttpText;
    }
    
    public static function changeExceptionReturnTo400BadRequest(){
        self::$exceptionHttpCode = 400;
        self::$exceptionHttpText = "Bad Request";
    }
    
    public static function changeExceptionReturnTo404NotFound(){
        self::$exceptionHttpCode = 404;
        self::$exceptionHttpText = "Not Found";
    }
    
    public static function changeExceptionReturnTo500InternalServerError(){
        self::$exceptionHttpCode = 500;
        self::$exceptionHttpText = "Internal Server Error";
    }
    
    public static function forceCustomNonExceptionReturn204NoContent(){
        self::$forceCustomNonExceptionReturn = true;
        self::$forceCustomNonExceptionReturnHttpCode = 204;
        self::$forceCustomNonExceptionReturnHttpText = "No Content";
    }
    
    public static function forceCustomNonExceptionReturn404NotFound(){
        self::$forceCustomNonExceptionReturn = true;
        self::$forceCustomNonExceptionReturnHttpCode = 404;
        self::$forceCustomNonExceptionReturnHttpText = "Not Found";
    }
    
    public static function disableCustomNonExceptionReturn(){
        self::$forceCustomNonExceptionReturn = false;
        self::$forceCustomNonExceptionReturnHttpCode = null;
        self::$forceCustomNonExceptionReturnHttpText = null;
    }
    
    public static function getForceCustomNonExceptionReturn(){
        return self::$forceCustomNonExceptionReturn;
    }
    
    public static function getForceCustomNonExceptionReturnHttpCode(){
        return self::$forceCustomNonExceptionReturnHttpCode;
    }
    
    public static function getForceCustomNonExceptionReturnHttpText(){
        return self::$forceCustomNonExceptionReturnHttpText;
    }
}
