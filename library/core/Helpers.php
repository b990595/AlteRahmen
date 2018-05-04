<?php

/**
 * @author T180469
 * Helpers er en samling af små praktiske værktøjer i frameworket.
 *
 */
class FR_Helpers {

    public static function loadHtmlIntoDiv($divId, $url) {
        echo '<script type="text/javascript">
            $(document).ready(
            function () {
                $.get("' . $url . '", function(data){
                    $("#' . $divId . '").html(data);
                });
            });</script>';
    }

    public static function loadHtmlIntoDivButton($divId, $url, $buttonText = "Klik", $hideOnClick = false, $style = "", $class= "btn btn-xs btn-default") {
        $html = "";
        $html.= "<button class='".$class."' style='".$style."' "
        . "onclick='javascript:";
        if ($hideOnClick){
            $html.= "$(this).hide();";
        }
        $html.= "$.get(\"" . $url . "\", function(data){\$(\"#" . $divId ."\").html(data);});'>";
        $html.= $buttonText;
        $html.= "</button>";
        return $html;
    }

    public static function requireNotBlank($var) {
        if (trim($var) == "") {
            throw new Exception("Variablen kan ikke være blank.");
        }
    }

    public static function requireLargerThanZero($var) {
        $var = $var / 1;
        if ($var > 0) {
            // Alles gut ..
        } else {
            throw new Exception("Variablen skal være større end 0.");
        }
    }

    public static function requireCprse($var) {
        if ($var < 10000000 || $var > 9999999999) {
            throw new Exception("Variablen er ikke et validt cprse/kundenr.");
        }
    }

    public static function requireMinLength($var, $length, $trimString = true) {
        if ($trimString) {
            $strLen = mb_strlen(trim($var));
        } else {
            $strLen = mb_strlen($var);
        }

        if ($strLen < $length) {
            throw new Exception("Variablen er for kort.");
        }
    }

    public static function requireExactLength($var, $length, $trimString = true) {
        if ($trimString) {
            $strLen = mb_strlen(trim($var));
        } else {
            $strLen = mb_strlen($var);
        }

        if ($strLen != $length) {
            throw new Exception("Variablen er forkert længde.");
        }
    }

    public static function userHasAccess($accessString, $delimiter = ";") {
        $tmp = explode($delimiter, $accessString);
        if (is_array($tmp)) {
            foreach ($tmp as $t) {
                $t = str_replace("(", "", $t);
                $t = str_replace(")", "", $t);
                $t = "(" . $t . ")";
                if (stristr(Session::GetOrBlank("access"), $t)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function displayLoaderAndRedoRequest($loaderText = "Indlæser siden, vent venligst.") {
        $backtrace = debug_backtrace();
        if (!is_object($backtrace [1] ['object'])) {
            die("Fejl i loader / backtrace.");
        }

        $module = $backtrace [1] ['object']->moduleName;
        $class = $backtrace [1] ['object']->routeName;
        $action = $backtrace [1] ['function'];

        $loaderUrl = FR_ROOT . "/" . $module . "/" . $class . "/" . $action;
        $loaderMD5 = md5($loaderUrl);

        $skiploader = false;
        if (isset($_SESSION ['__FR_LOADER'] [$loaderMD5]) && $_SESSION ['__FR_LOADER'] [$loaderMD5] == "1") {
            $skiploader = true;
            unset($_SESSION ['__FR_LOADER'] [$loaderMD5]);
        }

        if (!$skiploader) {
            $_SESSION ['__FR_LOADER'] [$loaderMD5] = "1";



            // *******************************************************
            // GET DATA
            // *******************************************************
            $dataArray = null;
            if (is_array($_GET)) {
                foreach (array_keys($_GET) as $getkey) {
                    if (!stristr($getkey, "__FR")) {
                        $dataArray [$getkey] = $_GET [$getkey];
                    }
                }
            }

            $params = self::arrayToParams($dataArray);

            // *******************************************************
            // POST DATA
            // *******************************************************
            $doPost = false;
            if (is_array($_POST) && count($_POST) > 0) {
                $doPost = true;
                RForm::start($loaderUrl . $params, "__FR_LoaderForm");
                foreach (array_keys($_POST) as $key) {
                    RForm::hidden($key, $_POST [$key]);
                }
                RForm::end();
            }

            echo "<div style='text-align:center; font-size: 12px; font-family: Arial; color: #666666;'>" . $loaderText . "<br><img src='" . FR_IMG_PATH . "/RIcons/busy2.gif' align='absmiddle' /></div>";
            echo "<script language='javascript'>";
            if ($doPost) {
                echo "document.getElementById('__FR_LoaderForm').submit();";
            } else {
                // Replace fordi, det erstatter browser history.
                echo "window.location.replace(\"" . $loaderUrl . $params . "\");";
            }
            echo "</script>";
            die();
        }
    }

    public static function getUrlByController($dataArray, $module, $controller, $action) {
        $params = self::arrayToParams($dataArray);
        return FR_ROOT . "/" . $module . "/" . $controller . "/" . $action . $params;
    }

    public static function arrayToParams($dataArray) {
        $params = "";
        if (is_array($dataArray)) {
            $params .= "?";
            foreach (array_keys($dataArray) as $key) {
                if ($params != "?") {
                    $params .= "&";
                }
                $params .= $key . "=";
                $params .= urlencode($dataArray [$key]);
            }
        }
        return $params;
    }

}
