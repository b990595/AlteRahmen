<?php
class NoAuth{

    private static $urls = array(
        "system/login/fallback",
        "system/login/doLogin",
    );

    public static function freeAccess($module, $route, $action){
        $tmp = strtolower($module)."/".$route."/".$action;
        return in_array($tmp, self::$urls);
    }

}
