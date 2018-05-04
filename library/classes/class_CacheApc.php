<?php

class CacheApc {

    public static function get($key, $namespace) {
        $nKey = $namespace . "/" . $key;
        $bRes = false;
        $vData = apc_fetch($nKey, $bRes);
        return ($bRes) ? $vData : null;
    }

    public static function set($key, $namespace, $data, $secondsToLive = 3600) {
        $nKey = $namespace . "/" . $key;
        return apc_store($nKey, $data, $secondsToLive);
    }

    public static function delete($key, $namespace) {
        $nKey = $namespace . "/" . $key;
        return (apc_exists($nKey)) ? apc_delete($nKey) : true;
    }

}
