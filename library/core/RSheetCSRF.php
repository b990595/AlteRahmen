<?php

class FR_RSheetCSRF {

    private static function unik() {
        $u = \Ramsey\Uuid\Uuid::uuid4();
        return $u->toString();
    }

    public static function cleanup() {
        $ses = Session::GetOrFalse("RSheet_CSRF");
        if (is_array($ses)) {
            foreach (array_keys($ses) as $hash) {
                $s = $ses[$hash];
                if ($s['expire'] < time()) {
                    Session::deleteFromArray("RSheet_CSRF", $hash);
                }
            }
        }
    }

    public static function create($key, $keyValue, $connectionString, $table, $tableExt = "", $timeoutSec = 3600) {
        self::cleanup();
        $hash = self::unik();
        $expire = time() + $timeoutSec;

        $data = array(
            "hash" => $hash,
            "key" => $key,
            "keyValue" => $keyValue,
            "connectionString" => $connectionString,
            "table" => $table,
            "tableExt" => $tableExt,
            "expire" => $expire
        );

        Session::setToArray("RSheet_CSRF", $hash, $data, false);
        return $hash;
    }

    public static function renew($hash, $timeoutSec = 3600) {
        self::cleanup();
        $data = Session::getFromArray("RSheet_CSRF", $hash, false);
        if ($data) {
            $data['expire'] = time() + $timeoutSec;
            return Session::setToArray("RSheet_CSRF", $hash, $data, true);
        } else {
            return false;
        }
    }

    public static function get($hash) {
        self::cleanup();
        if (trim($hash) != "") {
            return Session::getFromArray("RSheet_CSRF", $hash, false);
        } else {
            return false;
        }
    }

}
