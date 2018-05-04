<?php

class validate {

    public static function different($a, $b) {
        if ($a != $b) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($a . " is not different from " . $b);
        }
    }

    public static function klassifikation($k){
        return self::inArray($k, array("1", "2a", "2b", "3a", "3b", "4", "5"));
    }

    public static function kontonr($k) {
        if ($k > 1000 & $k <= 9999999999) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($k . " is not a valid \"kontonr\"");
        }
    }

    public static function cprse($i) {
        if ($i > 10000000 & $i < 9999999999) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not a valid \"cprse\"");
        }
    }

    public static function cpr($i) {
        if ($i > 101000000 & $i < 3112999999) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not a valid \"cpr\"");
        }
    }

    public static function email($i) {
        if (filter_var($i, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not a valid email-adresse");
        }
    }

    public static function not_empty($i) {
        if (trim($i) != "") {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception("cannot be empty");
        }
    }

    public static function inArray($needle, $haystack) {
        if (in_array($needle, $haystack)) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($needle . " not correct value (inArray)");
        }
    }

    public static function betweenOrSame($number, $min, $max) {
        return self::inRange($number, $min, $max);
    }

    public static function inRange($number, $min, $max) {
        $number = $number / 1;
        $min = $min / 1;
        $max = $max / 1;
        if ($number >= $min && $number <= $max) {
            return true;
        }
        RestResultSettings::changeExceptionReturnTo400BadRequest();
        throw new Exception($number . " not in range [$min - $max]");
    }

    public static function greaterThanZero($i) {
        $i = $i / 1;
        if ($i > 0) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not > 0");
        }
    }

    public static function tuser($i) {
        // Godt princip, at T skal være stort bokstav (sikrer konsistens mht. db-queries)
        $a = substr($i, 0, 1);
        $b = (int) substr($i, 1, 6);

        // Godt princip, at T skal være stort bokstav (sikrer konsistens mht. db-queries)
        if ($a == "T" && $b >= 100000 && $b <= 999999 && strlen($i) == 7) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not a valid \"tuser\"");
        }
    }

    public static function datetime($i) {
        $ts = system::SQLDateTime_to_timestamp($i);
        $i2 = date("Y-m-d H:i:s", $ts);
        if ($i == $i2) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not a valid datetime.");
        }
    }

    public static function date($i) {
        $i1 = $i . " 00:00:00";
        $ts = system::SQLDateTime_to_timestamp($i1);
        $i2 = date("Y-m-d H:i:s", $ts);
        if ($i1 == $i2) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not a valid date.");
        }
    }

    public static function json($i) {
        $array = json_decode($i, true);
        if (is_array($array)) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " is not a valid JSON-string.");
        }
    }

    public static function id($i) {
        if (is_numeric($i) && $i > 0) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($i . " er ikke validt id");
        }
    }

    public static function dateIsToday($date) {
        if ($date == date("Y-m-d")) {
            return true;
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($date . " er ikke i dag.");
        }
    }

    public static function feddepotnrlength($depotnr) {
        $length = array();
        $length[] = 6;
        $length[] = 7;
        $length[] = 8;
        $length[] = 9;
        $length[] = 14;
        try {
            self::stringLengthInArray($depotnr, $length);
            return true;
        } catch (Exception $ex) {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception("Depotnr. " . $depotnr . " har ikke en valid længde.");
        }
    }

    public static function stringLengthInArray($test, array $length) {
        foreach ($length as $value) {
            if (mb_strlen($test) == $value) {
                return true;
            }
        }
        RestResultSettings::changeExceptionReturnTo400BadRequest();
        throw new Exception('Længden på ' . $test . ' er ikke valid');
    }
    
    public static function stringLengthMinMax($test, $min, $max) {
        if (mb_strlen($test) >= $min && mb_strlen($test) <= $max) {
            return true;
        }else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception($test . " skal være mellem " . $min . " og " . $max . " cifre langt.");
         }
    }

    public static function contains($haystack, $needle, $caseSensitive = true) {
        if (strstr($haystack, $needle)) {
            return true;
        } else if ($caseSensitive == false && stristr($haystack, $needle)) {
            return true;
        }
        RestResultSettings::changeExceptionReturnTo400BadRequest();
        throw new Exception("[" . $haystack . "] must contain [" . $needle . "]");
    }

    public static function issetAndNumeric($array, $key) {
        if (isset($array[$key])) {
            if (is_numeric($array[$key])) {
                return true;
            }
            throw new Exception("Key [" . $key . "] is not numeric [issetAndNumeric]");
        }
        throw new Exception("Key [" . $key . "] is not set [issetAndNumeric]");
    }

    public static function isset($array, $key) {
        if (isset($array[$key])) {
           return true;
        }
        throw new Exception("Key [" . $key . "] is not set [isset]");
    }

}
