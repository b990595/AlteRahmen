<?php

class system {

    public static function unik() {
        $u = \Ramsey\Uuid\Uuid::uuid4();
        return $u->toString();
    }

    /**
     * 
     * Omdanner en SAS 1/2dim string til et array.
     * StrictMode medfører: At der kræver samme antal kolonner i alle rækker.
     * 
     * @param string $str
     * @param bool $strictMode
     * @param char $valueSep
     * @param char $lineSep
     * @return array
     * @throws Exception
     */
    public static function split2dimString($str, $exspectedNumberOfCols, $valueSep = "#", $lineSep = "¤") {
        if ($str === null || $str == "" || $str === false) {
            return array();
        }

        $retur = array();
        $lines = explode($lineSep, $str);
        if (is_array($lines)) {
            foreach ($lines as $l) {
                $values = explode($valueSep, $l);
                if (is_array($values)) {
                    $tmpCount = count($values);
                    if ($tmpCount != $exspectedNumberOfCols) {
                        RestResultSettings::changeExceptionReturnTo400BadRequest();
                        throw new Exception("Funtion exspects [" . $exspectedNumberOfCols . "] cols in datalist (split2dimString)");
                    }
                    $retur[] = $values;
                } else {
                    RestResultSettings::changeExceptionReturnTo400BadRequest();
                    throw new Exception("Could not explode [" . $l . "]");
                }
            }
        } else {
            RestResultSettings::changeExceptionReturnTo400BadRequest();
            throw new Exception("Could not explode [" . $str . "]");
        }
        return $retur;
    }
    
    public static function split2dimStringTEST($str, $exspectedNumberOfCols, $valueSep = "#", $lineSep = "¤") {
        if ($str === null || $str == "" || $str === false) {
            return array();
        }

        $retur = array();
        $lines = explode($lineSep, $str);
        if (is_array($lines)) {
            foreach ($lines as $l) {
                $values = explode($valueSep, $l);
                if (is_array($values)) {
                    $tmpCount = count($values);
                    if ($tmpCount != $exspectedNumberOfCols) {
                        //RestResultSettings::changeExceptionReturnTo400BadRequest();
                        //throw new Exception("Funtion exspects [" . $exspectedNumberOfCols . "] cols in datalist (split2dimString)");
                    }
                    $retur[] = $values;
                } else {
                    //RestResultSettings::changeExceptionReturnTo400BadRequest();
                    //throw new Exception("Could not explode [" . $l . "]");
                }
            }
        } else {
            //RestResultSettings::changeExceptionReturnTo400BadRequest();
            //throw new Exception("Could not explode [" . $str . "]");
        }
        return $retur;
    }
    

    public static function drawHeader($headerText, $subText = "", $rightHtml = "") {
        echo "<table cellpadding='0' cellspacing='0' width='100%'>
		<tr bgcolor='#17384D'>
		
		<td style='width: 10px; background-color:#ff972C;'></td>
		
		<td style='color: #fefffb; font-size: 18px; padding-left: 3px;'>
		" . $headerText;

        if (trim($subText) != "") {
            echo "<br>";
            echo "<span style='font-size: 12px; color: #fefffb'>" . $subText . "</span>";
        }

        echo "</td>
		<td align='right' style='color: #fefffb; padding-right: 6px;'>
		" . $rightHtml . "
		</td>
		
		</tr>
		</table>";
    }

    public static function beforeUnderscore($input) {
        $tmp = mb_strpos($input, "_");
        if ($tmp > 0) {
            return mb_substr($input, 0, $tmp);
        } else {
            return "";
        }
    }

    public static function afterUnderscore($input) {
        $tmp = mb_strpos($input, "_");
        if ($tmp > 0) {
            return mb_substr($input, $tmp);
        } else {
            return "";
        }
    }

    public static function arrayPrint($array) {
        echo self::printArray($array);
    }

    public static function arrayPrintNoEcho($array) {
        return self::printArray($array);
    }

    public static function firstLetterOfEachWord($string) {
        $words = explode(" ", $string);
        $letters = "";
        if (is_array($words)) {
            foreach ($words as $value) {
                $letters .= mb_substr($value, 0, 1);
            }
        }
        return $letters;
    }

    private static function printArray($arr) {
        $retStr = '<ul>';
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                if (is_array($val)) {
                    $retStr .= '<li><b>' . $key . '</b>: ' . self::printArray($val) . '</li>';
                } else {
                    if ($val === true) {
                        $val = "true";
                    } else if ($val === false) {
                        $val = "false";
                    } else if ($val === null) {
                        $val = "null";
                    }
                    $retStr .= '<li><b>' . $key . '</b>: ' . $val . '</li>';
                }
            }
        }
        $retStr .= '</ul>';
        return $retStr;
    }

    public static function httppost($url, $dataArray = array()) {

        // Tilføjer selv server
        if (mb_substr($url, 0, 1) == "/") {
            $url = "http://" . $_SERVER ['SERVER_NAME'] . $url;
        }

        $fields_string = "";

        // Danner dataArray om til URL-POST
        foreach (array_keys($dataArray) as $key) {

            if (is_array($dataArray [$key])) {
                foreach ($dataArray [$key] as $d) {
                    $fields_string .= $key . '[]=' . urlencode((string) $d) . '&';
                }
            } else {
                $fields_string .= $key . '=' . urlencode((string) $dataArray [$key]) . '&';
            }
        }
        rtrim($fields_string, '&');

        // Curl
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        if ($fields_string != "") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    // Create Microsoftcompertible GUID
    public static function GUID($namespace = '') {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        $data .= $_SERVER ['REQUEST_TIME'];
        $data .= $_SERVER ['HTTP_USER_AGENT'];
        $data .= $_SERVER ['LOCAL_ADDR'];
        $data .= $_SERVER ['LOCAL_PORT'];
        $data .= $_SERVER ['REMOTE_ADDR'];
        $data .= $_SERVER ['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = '{' . mb_substr($hash, 0, 8) . '-' . mb_substr($hash, 8, 4) . '-' . mb_substr($hash, 12, 4) . '-' . mb_substr($hash, 16, 4) . '-' . mb_substr($hash, 20, 12) . '}';

        // Sikring af uniqid ikke gentages, hvis funktionen kaldes hurtigt mange gange i træk.
        $testuid = uniqid("", true);
        if ($testuid == $uid) {
            sleep(1);
        }

        return $guid;
    }

    public static function sendMail($to, $subject, $body = "", $fromname = "Jutlander Bank A/S", $fromaddress = "noreply@jutlander.dk") {
        $body = str_ireplace("\n", "<br>", $body);
        $body = str_ireplace("\r\n", "<br>", $body);
        $body = str_ireplace("\n\r", "<br>", $body);

        $eol = "\r\n";

        $headers = "From: " . $fromname . "<" . $fromaddress . ">" . $eol;
        $headers .= "Reply-To: " . $fromname . "<" . $fromaddress . ">" . $eol;
        $headers .= "Return-Path: " . $fromname . "<" . $fromaddress . ">" . $eol;
        $headers .= "Message-ID: <" . time() . "-" . $fromaddress . ">" . $eol;
        $headers .= "X-Mailer: PHP v" . phpversion() . $eol;

        $headers .= "MIME-Version: 1.0" . $eol;
        $headers .= "Content-Type: text/html; charset=iso-8859-1" . $eol;
        $headers .= "Content-Transfer-Encoding: 8bit" . $eol;

        ini_set('sendmail_from', $fromaddress);
        $mail_sent = mail($to, $subject, $body, $headers);
        ini_restore('sendmail_from');

        return $mail_sent;
    }

    /**
     * Lægger / trækker en eller flere maaneder til/fra en YYYYMM.
     *
     * @var Int $maaned
     * @var Int +/- måneder
     * @return Int ny YYYYMM
     */
    public static function adjustMaaned($maaned, $adjust) {
        if ($adjust > 0) {
            for ($x = 0; $x < $adjust; $x ++) {
                $y = mb_substr($maaned, 0, 4) + 0;
                $m = mb_substr($maaned, 4, 2) + 0;
                $m = $m + 1;
                if ($m == 13) {
                    $m = 1;
                    $y = $y + 1;
                }

                $maaned = system::length($y, 4) . system::length($m, 2);
            }
        } elseif ($adjust < 0) {
            for ($x = 0; $x > $adjust; $x --) {
                $y = mb_substr($maaned, 0, 4) + 0;
                $m = mb_substr($maaned, 4, 2) + 0;
                $m = $m - 1;
                if ($m == 0) {
                    $m = 12;
                    $y = $y - 1;
                }

                $maaned = system::length($y, 4) . system::length($m, 2);
            }
        }

        return $maaned;
    }

    /**
     * Konverterer eksempelvis "Kaj;Bent;Bjarne; Lise" til ('Kaj','Bent','Bjarne','Lise')
     * Udfører og trim på de enkelte elementer
     *
     * @param string $source        	
     * @param char $seperator        	
     * @param char $SQLtextqualifier        	
     *
     * @return bool
     */
    public static function implodeStringToSQL($source, $seperator = ";", $SQLtextqualifier = "'") {
        $split = explode($seperator, $source);
        if (!is_array($split)) {
            return false;
        }

        $retur = self::implodeSingleArrayToSQL($split, $SQLtextqualifier);

        return $retur;
    }

    public static function implodeSingleArrayToJSArray($source, $textqualifier = "'") {
        if (!is_array($source)) {
            return false;
        }

        $elementer = null;
        foreach ($source as $s) {
            $elementer [] = trim($s);
        }

        $retur = "[";
        foreach ($elementer as $e) {
            if ($textqualifier != "") {
                $e = str_ireplace($textqualifier, "", $e);
            }
            if (is_numeric($e)) {
                $retur .= $e . ",";
            } else {
                $retur .= $textqualifier . $e . $textqualifier . ",";
            }
        }

        // Fjerner sidste komma
        $retur = mb_substr($retur, 0, mb_strlen($retur) - 1);

        $retur .= "]";

        return $retur;
    }

    /**
     * Konverterer 1-dim-array til ('Kaj','Bent','Bjarne','Lise')
     * Udfører og trim på de enkelte elementer
     *
     * @param array $source        	
     * @param char $SQLtextqualifier        	
     *
     * @return bool
     */
    public static function implodeSingleArrayToSQL($source, $SQLtextqualifier = "'") {
        if (!is_array($source)) {
            return false;
        }

        $elementer = null;
        foreach ($source as $s) {
            $elementer [] = trim($s);
        }

        $retur = "(";
        foreach ($elementer as $e) {
            if ($SQLtextqualifier != "") {
                $e = str_ireplace($SQLtextqualifier, "", $e);
            }
            $retur .= $SQLtextqualifier . $e . $SQLtextqualifier . ",";
        }

        // Fjerner sidste komma
        $retur = mb_substr($retur, 0, mb_strlen($retur) - 1);

        $retur .= ")";

        return $retur;
    }

    /**
     * Konverterer kolonne multi-dim-array til ('Kaj','Bent','Bjarne','Lise')
     * Udfører og trim på de enkelte elementer
     *
     * @param array $source        	
     * @param string $col        	
     * @param char $SQLtextqualifier        	
     *
     * @return bool
     */
    public static function implodeMultiArrayToSQL($source, $col, $SQLtextqualifier = "'") {
        $source = arraysql::column_to_1dim_array($source, $col);

        $retur = self::implodeSingleArrayToSQL($source, $SQLtextqualifier);

        return $retur;
    }

    public static function get_month_navn($mnd) {
        $array ['1'] = "januar";
        $array ['2'] = "februar";
        $array ['3'] = "marts";
        $array ['4'] = "april";
        $array ['5'] = "maj";
        $array ['6'] = "juni";
        $array ['7'] = "juli";
        $array ['8'] = "august";
        $array ['9'] = "september";
        $array ['10'] = "oktober";
        $array ['11'] = "november";
        $array ['12'] = "december";

        $mnd = (int) $mnd;
        return $array [$mnd];
    }

    public static function length($data, $length = 10, $cutfrom = "left") {
        $len = mb_strlen($data);
        if ($len < $length) {
            while ($len < $length) {
                $data = "0" . $data;
                $len = mb_strlen($data);
            }
            return $data;
        } elseif ($len > $length) {
            while ($len > $length) {
                if ($cutfrom == "left") {
                    $data = mb_substr($data, $len - $length, $length);
                    return $data;
                } else {
                    $data = mb_substr($data, 0, $length);
                    return $data;
                }
            }
        } else {
            return $data;
        }
    }

    public static function comma_to_dot($val) {
        $val = str_replace(",", ".", $val);
        return $val + 0;
    }

    public static function dot_to_comma($val) {
        $val = str_replace(".", ",", $val);
        return $val;
    }

    /**
     * Konverterer et tal (som string) til number.
     *
     * Eks. 12.563,50 bliver til 12563.5
     *
     * @param string $tal        	
     * @return number
     */
    public static function string_to_number($tal) {
        $tal = str_ireplace(".", "", $tal);
        $tal = str_ireplace(",", ".", $tal);
        return $tal;
    }

    /**
     * Returnerer et tal med formen xxx.xxx,xx
     * Negative tal skrives med rød.
     *
     * @param number $tal        	
     * @return string
     */
    public static function dkformat($tal, $decimals = 2) {
        $tal = $tal + 0;
        $color = "black";
        if ($tal < 0) {
            $color = "#AA0000";
        }

        $a = number_format($tal, $decimals, ",", ".");

        if ($a == "-0,00") {
            return "<span style='color:$color;'>0,00</span>";
        } else {
            return "<span style='color:$color;'>$a</span>";
        }
    }

    /**
     * Retunerer kort elle langt dansk navn på en måned(1-12)
     *
     * @param int $timestamp
     *        	(Unix timestamp)
     * @param bool $long
     *        	(Retuner lang)
     * @return string
     */
    public static function get_month($timestamp, $long = true) {
        $timestamp = intval($timestamp);
        $month = date('n', $timestamp);
        $long_months = array(
            1 => 'januar',
            2 => 'februar',
            3 => 'marts',
            4 => 'april',
            5 => 'maj',
            6 => 'juni',
            7 => 'juli',
            8 => 'august',
            9 => 'september',
            10 => 'oktober',
            11 => 'november',
            12 => 'december'
        );

        $short_months = array(
            1 => 'jan',
            2 => 'feb',
            3 => 'mar',
            4 => 'apr',
            5 => 'maj',
            6 => 'jun',
            7 => 'jul',
            8 => 'aug',
            9 => 'sep',
            10 => 'okt',
            11 => 'nov',
            12 => 'dec'
        );

        if ($long && isset($long_months [$month])) {
            return $long_months [$month];
        } elseif (!$long && isset($short_months [$month])) {
            return $short_months [$month];
        } else {
            return false;
        }
    }

    /**
     * Laver et PHP-timestamp til et dansk ugedagsnavn.
     *
     * @param int $timestamp        	
     * @param bool $long
     *        	(Lang el. kort)
     * @return Streng eller false
     */
    public static function get_day($timestamp, $long = true) {
        $timestamp = intval($timestamp);
        $day = date('w', $timestamp);

        $long_days = array(
            0 => 'søndag',
            1 => 'mandag',
            2 => 'tirsdag',
            3 => 'onsdag',
            4 => 'torsdag',
            5 => 'fredag',
            6 => 'lørdag'
        );

        $short_days = array(
            0 => 'sø',
            1 => 'ma',
            2 => 'ti',
            3 => 'on',
            4 => 'to',
            5 => 'fr',
            6 => 'lø'
        );

        if ($long && isset($long_days [$day])) {
            return $long_days [$day];
        } elseif (!$long && isset($short_days [$day])) {
            return $short_days [$day];
        } else {
            return false;
        }
    }

    /**
     * Formaterer en dato
     *
     * @param int $timestamp        	
     * @param bool $no_seconds        	
     * @param bool $long        	
     * @return Datostreng eller false
     */
    public static function format_date($timestamp, $options = array(), $long_day = true, $long_month = true) {
        $timestamp = intval($timestamp);
        if (array_key_exists('no_seconds', $options)) {
            $last_part = ' Y - H:i';
        } else {
            $last_part = ' Y - H:i:s';
        }
        $day = ((array_key_exists('show_day_name', $options))) ? self::get_day($timestamp, $long_day) . ' d. ' : '';
        return $day . date('j. ', $timestamp) . self::get_month(intval(date('n', $timestamp)), $long_month) . date($last_part, $timestamp);
    }

    // End format_date

    /**
     * Formaterer en tid
     *
     * @param int $timestamp        	
     * @param bool $no_seconds        	
     * @return Tidsstreng eller false
     */
    public static function format_time($timestamp, $options = array()) {
        $timestamp = intval($timestamp);
        if (array_key_exists('no_seconds', $options)) {
            return date('H:i', $timestamp);
        } else {
            return date('H:i:s', $timestamp);
        }
    }

    // End format_date
    public static function notesdate_to_timestamp($notesdate) {
        $split = str_split($notesdate);

        $aar = $split [0] . $split [1] . $split [2] . $split [3];
        $mnd = $split [5] . $split [6];
        $dag = $split [8] . $split [9];
        $time = $split [11] . $split [12];
        $minut = $split [14] . $split [15];
        $sekund = $split [17] . $split [19];

        // echo $dag."/".$mnd." ".$aar." kl.".$time.".".$minut.":".$sekund;

        return mktime($time + 0, $minut + 0, $sekund + 0, $mnd + 0, $dag + 0, $aar + 0);
    }

    public static function sqldate_to_timestamp($sqldate) {
        $split = str_split($sqldate);

        (int) $aar = $split [0] . $split [1] . $split [2] . $split [3];
        (int) $mnd = $split [5] . $split [6];
        (int) $dag = $split [8] . $split [9];

        if ($aar == 0) {
            return 0;
        } else {
            return mktime(0, 0, 0, $mnd + 0, $dag + 0, $aar + 0);
        }
    }

    public static function gruppekalenderdate_to_timestamp($notesdate) {
        $split = str_split($notesdate);
        $aar = $split [0] . $split [1] . $split [2] . $split [3];
        $mnd = $split [4] . $split [5];
        $dag = $split [6] . $split [7];

        return mktime(0, 0, 0, $mnd, $dag, $aar);
    }

    public static function notesxmldate_to_timestamp($notesdate) {
        $split = str_split($notesdate);
        $aar = $split [0] . $split [1] . $split [2] . $split [3];
        $mnd = $split [4] . $split [5];
        $dag = $split [6] . $split [7];
        $time = $split [9] . $split [10];
        $minut = $split [11] . $split [12];
        $sekund = $split [13] . $split [14];

        // echo $dag."/".$mnd." ".$aar." kl.".$time.".".$minut.":".$sekund;

        return mktime($time, $minut, $sekund, $mnd, $dag, $aar);
    }

    public static function notesviewdatetime_to_timestamp($notesdate) {
        if ($notesdate != "") {

            $streg1 = mb_strpos($notesdate, "-");
            $streg2 = strrpos($notesdate, "-");
            $semi1 = mb_strpos($notesdate, ":");
            $semi2 = strrpos($notesdate, ":");

            $mellem = mb_strpos($notesdate, " ");
            $len = mb_strlen($notesdate);

            $dag = mb_substr($notesdate, 0, $streg1);
            $mnd = mb_substr($notesdate, $streg1 + 1, $streg2 - $streg1 - 1);
            $aar = mb_substr($notesdate, $streg2 + 1, $mellem - $streg2 - 1);
            if (mb_strlen($aar) == 2) {
                if ($aar > 69) {
                    $aar = "19$aar";
                } else {
                    $aar = "20$aar";
                }
            }

            $time = mb_substr($notesdate, $mellem + 1, $semi1 - $mellem - 1);
            $minut = mb_substr($notesdate, $semi1 + 1, $semi2 - $semi1 - 1);
            $sekund = mb_substr($notesdate, $semi2 + 1, $len - $semi2 - 1);

            $timest = mktime($time, $minut, $sekund, $mnd, $dag, $aar);

            return $timest;
        }
    }

    public static function switchYYYYMMDD($date) {
        $split = str_split($date);
        $format = $split [6] . $split [7] . "-" . $split [4] . $split [5] . "-" . $split [0] . $split [1] . $split [2] . $split [3];
        return $format;
    }

    public static function SQLDateTime_to_timestamp($datetime) {
        $split = str_split($datetime);
        for ($x = 0; $x < 20; $x++) {
            if (!isset($split[$x])) {
                $split[$x] = "";
            }
        }
        // ÅÅÅÅ-MM-DD og måske TT:MM
        $aar = intval($split [0] . $split [1] . $split [2] . $split [3]);
        $mnd = intval($split [5] . $split [6]);
        $dag = intval($split [8] . $split [9]);

        if (stristr($datetime, ":")) {
            $time = intval($split [11] . $split [12]);
            $minut = intval($split [14] . $split [15]);
            $sec = intval($split [17] . $split [18]);
        } else {
            $time = 0;
            $minut = 0;
            $sec = 0;
        }
        if ($aar == 0) {
            return 0;
        } else {
            return mktime($time, $minut, $sec, $mnd, $dag, $aar);
        }
    }

    public static function DKdato_to_timestamp($dato) {
        $split = str_split($dato);
        // DD-MM-ÅÅÅÅ og måske TT:MM
        $aar = $split [6] . $split [7] . $split [8] . $split [9];
        $mnd = $split [3] . $split [4];
        $dag = $split [0] . $split [1];

        if (stristr($dato, ":")) {
            $time = $split [11] . $split [12];
            $minut = $split [14] . $split [15];
        } else {
            $time = 0;
            $minut = 0;
        }

        return mktime($time, $minut, 0, $mnd, $dag, $aar);
    }

    public static function DKdatoTid_to_timestamp($dato) {
        $split = str_split($dato);
        // DD-MM-ÅÅÅÅ TT:MM:SS
        $aar = $split [6] . $split [7] . $split [8] . $split [9];
        $mnd = $split [3] . $split [4];
        $dag = $split [0] . $split [1];

        $time = $split [11] . $split [12];
        $minut = $split [14] . $split [15];
        $sekund = $split [17] . $split [18];

        return mktime($time, $minut, $sekund, $mnd, $dag, $aar);
    }

    public static function calendardate_to_timestamp($calendardate) {
        $split = str_split($calendardate);

        if ($split [2] == "-" && $split [5] == "-") {
            // DD-MM-ÅÅÅÅ
            $aar = $split [6] . $split [7] . $split [8] . $split [9];
            $mnd = $split [3] . $split [4];
            $dag = $split [0] . $split [1];
        }

        if ($split [1] == "-" && $split [4] == "-") {
            // D-MM-ÅÅÅÅ
            $aar = $split [5] . $split [6] . $split [7] . $split [8];
            $mnd = $split [2] . $split [3];
            $dag = $split [0];
        }

        if ($split [2] == "-" && $split [4] == "-") {
            // DD-M-ÅÅÅÅ
            $aar = $split [5] . $split [6] . $split [7] . $split [8];
            $mnd = $split [3];
            $dag = $split [0] . $split [1];
        }

        if ($split [1] == "-" && $split [3] == "-") {
            // D-M-ÅÅÅÅ
            $aar = $split [4] . $split [5] . $split [6] . $split [7];
            $mnd = $split [2];
            $dag = $split [0];
        }

        if ($split [4] == "-" && $split [7] == "-") {
            // ÅÅÅÅ-MM-DD
            $aar = $split [0] . $split [1] . $split [2] . $split [3];
            $mnd = $split [5] . $split [6];
            $dag = $split [8] . $split [9];
        }

        return mktime(0, 0, 0, $mnd, $dag, $aar);
    }

    public static function first_instance($input, $delimiter = ";") {
        $pos = mb_strpos($input, $delimiter);
        if ($pos > 0) {
            return mb_substr($input, 0, $pos);
        } else {
            return $input;
        }
    }

}
