<?php

class CorebankFlow {

    private static $data = array("actions" => 0, "ok" => 0, "failed" => 0, "list" => array(), "list_failed" => array());
    private static $indexCount = 0;
    private static $successCount = 0;
    private static $failureCount = 0;
    private static $corebankWrites = 0;
    private static $businessErrors = 0;
    private static $returnFlowFromController = false;
    private static $latestSDCErrorCode = null;
    private static $globalFlowMessage = "";

    public static function setGlobalFlowMessage(string $msg) {
        self::$globalFlowMessage = trim($msg);
    }

    public static function appendGlobalFlowMessage(string $msg) {
        if (self::$globalFlowMessage != ""){
            self::$globalFlowMessage.= " ".trim($msg);
        }else{
            self::setGlobalFlowMessage($msg);
        }
    }

    public static function forceReturnOfCorebankflow() {
        self::$returnFlowFromController = true;
    }

    public static function addSuccess($action, $data = array()) {
        self::$indexCount++;
        self::$successCount++;
        self::$data['actions'] = self::$indexCount;
        self::$data['ok'] = self::$successCount;
        self::$data['list'][] = array(
            "index" => self::$indexCount,
            "status" => "ok",
            "action" => $action,
            "data" => $data
        );
    }

    public static function addFailure($action, $data = array(), $problem = "", $SDCErrorCode = 1) {
        self::$latestSDCErrorCode = $SDCErrorCode;
        self::$indexCount++;
        self::$failureCount++;
        self::$data['actions'] = self::$indexCount;
        self::$data['failed'] = self::$failureCount;
        self::$data['list'][] = array(
            "index" => self::$indexCount,
            "status" => "failure",
            "action" => $action,
            "data" => $data,
            "problem" => $problem,
            "SDCErrorCode" => $SDCErrorCode
        );
        self::$data['list_failed'][] = array(
            "index" => self::$indexCount,
            "status" => "failure",
            "action" => $action,
            "data" => $data,
            "problem" => $problem,
            "SDCErrorCode" => $SDCErrorCode
        );
    }

    public static function addCorebankWrite() {
        self::$corebankWrites++;
    }

    public static function retreatCorebankWrite() {
        if (self::$corebankWrites > 0) {
            self::$corebankWrites--;
        }
    }

    private static function getCorebankWriteBoolean() {
        if (self::$corebankWrites > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function addBusinessError() {
        self::$businessErrors++;
    }

    public static function retreatBusinessError() {
        if (self::$businessErrors > 0) {
            self::$businessErrors--;
        }
    }

    private static function getBusinessErrorBoolean() {
        if (self::$businessErrors > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getFlowArray($always = false) {
        if (self::$returnFlowFromController || $always) {
            if (self::$data['actions'] > 0) {
                $data = self::$data;
                $retur = array();
                $retur['actions'] = $data['actions'];
                $retur['ok'] = $data['ok'];
                $retur['failed'] = $data['failed'];
                $retur['corebankWrite'] = self::getCorebankWriteBoolean();
                $retur['isBusinessError'] = self::getBusinessErrorBoolean();
                $retur['latestSDCErrorCode'] = self::$latestSDCErrorCode;
                $retur['globalFlowMessage'] = self::$globalFlowMessage;
                $retur['list'] = $data['list'];
                $retur['list_failed'] = $data['list_failed'];
                return $retur;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

}
