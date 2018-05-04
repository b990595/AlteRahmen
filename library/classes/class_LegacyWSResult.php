<?php

class LegacyWSResult {

    private $data = array();
    private $httpCode;
    private $returnMessage = "";

    /**
     *
     * @var RCurlResult 
     */
    private $RestResult;

    /**
     * @param RCurlResult $restResult
     */
    public function __construct($restResult) {
        if ($restResult instanceof RCurlResult) {
            $this->RestResult = $restResult;

            $info = $this->RestResult->getInfo();

            if ($info['http_code'] >= 100 && $info['http_code'] <= 599) {
                $this->httpCode = $info['http_code'];
                if ($this->RestResult->isJson()) {
                    $tmpData = $this->RestResult->getJsonAsArray();
                    $this->data = isset($tmpData['data']) ? $tmpData['data'] : array();
                    $this->returnMessage = isset($tmpData['message']) ? $tmpData['message'] : "";
                }
                
              
            } else {
                throw new Exception("Invalid http_code (LegacyWSResult, httpcode: " . $info['http_code'] . ")");
            }
        }
    }

    public function isOk() {
        if ($this->httpCode >= 200 && $this->httpCode < 300) {
            return true;
        } else {
            return false;
        }
    }

    public function isForbidden() {
        return $this->RestResult->is403Forbidden();
    }

    public function isNotFound() {
        return $this->RestResult->is404NotFound();
    }

    public function getData() {
        return $this->data;
    }

    public function getReturnMessage() {
        return $this->returnMessage;
    }

}
