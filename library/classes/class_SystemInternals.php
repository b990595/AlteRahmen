<?php

class SystemInternals {

    private static $request_data = array();

    private static $requestId;
    private static $module;
    private static $route;
    private static $action;
    
    public static function getRequestId() {
        return self::$requestId;
    }

    public static function setRequestId($requestId) {
        self::$requestId = $requestId;
    }

            /**
     * @return mixed
     */
    public static function getModule() {
        return self::$module;
    }

    /**
     * @param mixed $module
     */
    public static function setModule(string $module) {
        self::$module = $module;
    }

    /**
     * @return mixed
     */
    public static function getRoute() {
        return self::$route;
    }

    /**
     * @param mixed $route
     */
    public static function setRoute(string $route) {
        self::$route = $route;
    }

    /**
     * @return mixed
     */
    public static function getAction() {
        return self::$action;
    }

    /**
     * @param mixed $action
     */
    public static function setAction(string $action) {
        self::$action = $action;
    }

    /**
     * ONLY USED BY index.php
     * @param array $array
     */
    public static function SetReguestData($array) {
        if (is_array($array)) {
            self::$request_data = $array;
        }
    }

    /**
     * ONLY USED BY REST/WEB-CONTROLLER
     * @return array
     */
    public static function GetReguestData() {
        return self::$request_data;
    }

    /**
     * ONLY USED BY REST-CONTROLLERS IN MODULE FLOWS
     * @param string $name
     */
    public static function SetRRunner3FlowName(string $name) {
        self::$rrunner3_flow_name = trim($name);
    }

    /**
     * ONLY USED BY model_flows_StartLog
     * @return string
     */
    public static function GetRRunner3FlowName() {
        return trim(self::$rrunner3_flow_name);
    }

}
