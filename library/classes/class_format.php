<?php

/**
 * 100% static klasse, der (modsat system::) UDELUKKENDE indeholder formateringsfunktioner, med ét input parameter.
 *
 */
class format {

    public static function ensureUTF8($str, $checkForDoubleEncoding = false) {
        if (!mb_detect_encoding($str, 'UTF-8', true)) {
            return utf8_encode($str);
        }else{
            if ($checkForDoubleEncoding){
                $tmpStr = utf8_decode($str);
                if (mb_detect_encoding($tmpStr, 'UTF-8', true)){
                    $str = $tmpStr;
                }
            }
            return $str;
        }
        
        
        
        
    }

    public static function booleanToX($bool) {
        if ($bool) {
            return "X";
        } else {
            return "";
        }
    }

    public static function Only100chars($txt) {
        if (mb_strlen($txt) > 100) {
            return mb_substr($txt, 0, 100) . " ...";
        } else {
            return $txt;
        }
    }

    public static function SQLDateToMaanedAar($d) {
        return ucfirst(system::get_month_navn((mb_substr($d, 5, 2) / 1))) . " " . mb_substr($d, 0, 4);
    }

    public static function WarningIfNegativ($tal) {
        if ($tal < 0) {
            return "<img src='" . FR_IMG_PATH . "/RIcons/warning.gif' border='0' align='absmiddle' />";
        } else {
            return "";
        }
    }

    public static function CommaToHtmlBreak($t) {
        $t = str_ireplace(",", "<br>", $t);

        return $t;
    }

    public static function Procent4decimals($tal) {
        return number_format($tal, 4, ",", ".") . "%";
    }

    public static function ProcentUpTo4Decimals($tal) {
        if (!stristr($tal, ".")) {
            $tal = $tal . ".0";
        }
        $a = explode(".", $tal);

        if ($a [1] == 0) {
            $a [1] = "0000";
        } elseif ($a [1] < 10) {
            $a [1] = $a [1] . "000";
        } elseif ($a [1] < 100) {
            $a [1] = $a [1] . "00";
        } elseif ($a [1] < 1000) {
            $a [1] = $a [1] . "0";
        }

        return $a [0] . "," . $a [1] . "%";
    }

    public static function MailToTuser($tuser) {
        return "<a href='mailto:$tuser'><img src='" . FR_IMG_PATH . "/RIcons/letter.gif' border='0' align='absmiddle' /></a>";
    }

    public static function cprse($cpr) {
        if ((int) $cpr == 0) {
            return "";
        }
        if (trim($cpr) == "") {
            return "";
        }
        $cpr = $cpr + 0;
        if (mb_strlen($cpr) > 8) {

            if ($cpr < 3200000000) {
                $cpr = self::length10($cpr);
                $cpr1 = mb_substr($cpr, 0, 6);
                $cpr2 = mb_substr($cpr, 6, 4);
                return $cpr1 . "-" . $cpr2;
            } else {
                return $cpr;
            }
        } else {
            return $cpr;
        }
    }

    public static function cprseAsNumber($cprse) {
        $cprse = str_ireplace("-", "", $cprse);
        $cprse = $cprse / 1;
        return $cprse;
    }

    public static function ProcentX100With1Decimal($n) {
        $n = $n * 100;
        return round($n, 1) . "%";
    }

    public static function Segment($t) {
        if (stristr($t, "grøn")) {
            return "<span style='color: green;'>Grøn</span>";
        } elseif (stristr($t, "gul")) {
            return "<span style='color: orange;'>Gul</span>";
        } elseif (stristr($t, "rød")) {
            return "<span style='color: red;'>Rød</span>";
        } elseif (stristr($t, "ungdom")) {
            return "<span style='color: #333333;'>Ungdom</span>";
        } else {
            return $t;
        }
    }

    public static function length10($n) {
        return system::length($n, 10);
    }

    public static function ucFirst($s) {
        return ucfirst($s);
    }

    public static function ucWords($s) {
        return ucwords($s);
    }

    public static function dknumberContrast($n) {
        $n = $n / 1;
        if ($n == 0) {
            return "<span style='color: #CCCCCC;'>" . number_format($n, 2, ",", ".") . "</span>";
        } else {
            return system::dkformat($n, 2);
        }
    }

    public static function dknumber_to_us($n) {
        $n = str_replace(array('.', ','), array('', '.'), $n);
        return $n;
    }

    public static function dknumber($n) {
        return system::dkformat($n, 2);
    }

    public static function dkheltal($n) {
        return system::dkformat($n, 0);
    }

    public static function dktusind($n) {
        $n = $n / 1000;
        return system::dkformat($n, 0);
    }

    public static function sasbreak_to_br($t) {
        return str_ireplace("^-2n", "<br>", $t);
    }

    public static function sasbreak_to_blank($t) {
        return str_ireplace("^-2n", " ", $t);
    }

    public static function now($a = null) {
        return date("Y-m-d H:i:s");
    }

    public static function dkDateToSql($date) {
        $d = explode("-", $date);
        if (count($d) == 3) {
            return $d [2] . "-" . $d [1] . "-" . $d [0];
        } else {
            return false;
        }
    }

    public static function nbreakToBr($string) {
        return str_replace(PHP_EOL, "<br>", $string);
    }


    public static function SQLToDKDate($d) {
        if (mb_substr($d, 0, 2) == "00" || trim($d) == "") {
            return "&nbsp;";
        } else {

            $d = mb_substr($d, 0, 10);
            $ts = system::sqldate_to_timestamp($d);
            return date("j.", $ts) . " " . system::get_month($ts, false) . " " . date("Y", $ts);
        }
    }

    public static function SQLDateTimeToDK($dt) {
        if ($dt == "0000-00-00 00:00:00" || $dt == "") {
            return "00-00-0000 00:00:00";
        } else {
            $ts = system::SQLDateTime_to_timestamp($dt);
            return date("d-m-Y H:i:s", $ts);
        }
    }

    public static function SQLDateToDK($d) {
        if ($d == "0000-00-00" || $d == "") {
            return "00-00-0000";
        } else {

            $explode = explode("-", $d);
            return $explode [2] . "-" . $explode [1] . "-" . $explode [0];
        }
    }

    public static function SQLDateToNiceDate($d) {
        if ($d == "0000-00-00" || $d == "") {
            return "";
        } else {

            $ts = system::sqldate_to_timestamp($d);
            return date("j.", $ts) . " " . system::get_month($ts) . " " . date("Y", $ts);
        }
    }

    public static function SQLDateTimeToNiceDate($d) {
        if ($d == "0000-00-00 00:00:00" || $d == "") {
            return "";
        } else {

            $ts = system::SQLDateTime_to_timestamp($d);
            return date("j.", $ts) . " " . system::get_month($ts) . " " . date("Y", $ts) . " kl." . date("H:i:s", $ts);
        }
    }

    public static function weekdayByDateTime($d) {
        $ts = system::SQLDateTime_to_timestamp($d);
        return system::get_day($ts, true);
    }

    public static function weekdayByDate($d) {
        $ts = system::sqldate_to_timestamp($d);
        return system::get_day($ts, true);
    }

    public static function yesno($input) {
        if ($input == 1 || $input == "Ja" || $input == "J" || $input == "JA") {
            return "<img src='" . FR_IMG_PATH . "/RIcons/done.png' border='0' align='absmiddle' />";
        } else {
            return "<img src='" . FR_IMG_PATH . "/RIcons/delete2.gif' border='0' align='absmiddle' />";
        }
    }

    public static function numberValue($n) {
        return $n / 1;
    }

    public static function cleanSdcIdfr($idfr) {
        return trim(str_replace("+", "", $idfr));
    }

    /**
     * Omdanner 26.25 til 26 år, 3 mdr.
     * @param float $years_as_float
     * @return string
     */
    public static function loebetid($years) {
        $n = $years;
        $aar = floor($n);
        $n = $n - $aar;
        $mnd = $n * 12;
        $retur = "";
        if ($aar > 0) {
            $retur.= $aar . " år";
        }
        if ($mnd > 0) {
            if ($aar > 0){
                $retur.= ", ";
            }
            $retur.= round($mnd, 0) . " mdr.";
        }
        return $retur;
    }

}
