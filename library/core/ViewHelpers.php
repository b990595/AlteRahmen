<?php

class FR_ViewHelpers {

    public static function displayLoader($loaderText, $route, $data = array()) {
        if (isset($_SESSION['__controller_loader_ignore'])) {
            unset($_SESSION['__controller_loader_ignore']);
        } else {
            $_SESSION['__controller_loader_ignore'] = 1;
            echo "<div style='text-align:center; font-size: 12px; font-family: Arial; color: #666666;'>" . $loaderText . "<br><img src='" . FR_IMG_PATH . "/RIcons/busy2.gif' align='absmiddle' /></div>";
            echo "<script language='javascript'>";
            echo "window.location.replace(\"" . self::getUrlByRoute($route, $data) . "\");";
            echo "</script>";
            die();
        }
    }

    public static function modalDialog($id, int $width = null) {
        $html = '';
        $html .= '<div id = "' . $id . '" class="modal fade" role = "dialog" >';

        if ($width){
            $html .= '<div class="modal-dialog" style="width: '.$width.'px;" >';
        }else{
            $html .= '<div class="modal-dialog" >';
        }

        $html .= '<div class="modal-content" >';
        $html .= '<div class="modal-header" >';
        $html .= '<button type="button" class="close" data-dismiss="modal" style="font-size: 24px;">&times;</button>';
        $html .= '<h4 class="modal-title" id="' . self::modalGetTitleId($id) . '">Title</h4>';
        $html .= '</div>';
        $html .= '<div class="modal-body" id="' . self::modalGetBodyId($id) . '">';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';
        $html .= '</div> ';

        return $html;
    }

    private static function modalGetBodyId($modalId) {
        return $modalId . "__body";
    }

    private static function modalGetTitleId($modalId) {
        return $modalId . "__title";
    }

    public static function getUrlByRoute($route, $data = array(), $action = null, $module = null) {
        $params = "";
        if (!empty($data)) {
            $params = FR_Helpers::arrayToParams($data);
        }
        if (!$module) {
            $module = SystemInternals::getModule();
        }
        if ($action) {
            return FR_ROOT . "/" . $module . "/" . $route . "/" . $action . $params;
        } else {
            return FR_ROOT . "/" . $module . "/" . $route . $params;
        }
    }

    public static function getUrlByController($controller, $action, $dataArray = null, $module = null) {
        $params = "";
        if (is_array($dataArray) && !empty($dataArray)) {
            $params = FR_Helpers::arrayToParams($dataArray);
        }
        if (!$module) {
            $module = SystemInternals::getModule();
        }

        return FR_ROOT . "/" . $module . "/" . $controller . "/" . $action . $params;
    }

    public static function buttonLoadUrlIntoModal(string $modalId, string $modalTitle, $url, string $buttonText, string $buttonStyle = "", string $buttonClass = "btn btn-default btn-xs") {
        $html = "";
        $html .= "<a class='" . $buttonClass . "' style='" . $buttonStyle . "' onclick='javascript:";
        $html .= self::JSLoadUrlIntoModal($modalId, $modalTitle, $url);
        $html .= "' style='cursor: pointer;'>" . $buttonText . "</a>";
        return $html;
    }

    public static function JSLoadUrlIntoModal(string $modalId, string $modalTitle, $url) {
        $html = "";
        $html .= "
            $(\"#" . self::modalGetTitleId($modalId) . "\").html(\"" . $modalTitle . "\");
            $(\"#" . self::modalGetBodyId($modalId) . "\").html(\"\");
            $.get(\"" . $url . "\", 
            function(data){
                $(\"#" . self::modalGetBodyId($modalId) . "\").html(data);
            });
            $(\"#" . $modalId . "\").modal();";

        return $html;
    }

    public static function getCurrentUrl() {
        $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
        if (trim($url) != "") {
            return $url;
        }
        throw new Exception("SERVER:REQUEST_URI not set ..");
    }

}
