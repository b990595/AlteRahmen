<?php

class CorebankWSResult {

    private $data = array();
    private $httpCode;
    private $returnCode = 1;
    // ok, forbidden, legitimation, business, global, other
    private $returnType = "other";
    private $returnMessage = "";
    private $returnNotFound = false;

    /**
     * @param RCurlResult $restResult
     */
    public function __construct($restResult) {
        if ($restResult instanceof RCurlResult) {
            // HTTP CODE
            $info = $restResult->getInfo();

            if ($info['http_code'] >= 100 && $info['http_code'] <= 599) {
                $this->httpCode = $info['http_code'];
            } else {
                throw new Exception("Invalid http_code (CorebankWSResult, httpcode: ".$info['http_code'].")");
            }

            if ($this->httpCode == 401) {
                // Forbidden
                $this->returnCode = 7;
                $this->returnType = "forbidden";
                $this->returnMessage = "Forbidden";
                if (trim($restResult->getResponse()) != "") {
                    $this->returnMessage = $this->returnMessage . ": " . trim($restResult->getResponse());
                }
            } else if ($this->httpCode >= 300) {
                // Globalfejl
                $this->returnCode = 1;
                $this->returnType = "global";
                if ($this->httpCode >= 500) {
                    $this->returnMessage = "Server error";
                } else if ($this->httpCode >= 400) {
                    $this->returnMessage = "Request error";
                } else {
                    $this->returnMessage = "Other error";
                }
                if (trim($restResult->getResponse()) != "") {
                    $this->returnMessage = $this->returnMessage . ": " . trim($restResult->getResponse());
                    $responseArray = json_decode($restResult->getResponse(), true);
                    if (is_array($responseArray) && isset($responseArray['message']) && trim($responseArray['message'])!=""){
                        $tmpRM = "";
                        if (isset($responseArray['code']) && trim($responseArray['code'])!=""){
                            $tmpRM.= trim($responseArray['code']).": ";
                        }
                        $tmpRM.=trim($responseArray['message']);
                        $this->returnMessage = $tmpRM;
                    }
                }
            } else if ($this->httpCode == 204 && $restResult->getMethod() == "delete") {
                // No Content (ved DELETE)
                $this->returnCode = 0;
                $this->returnType = "ok";
                $this->returnMessage = "";
            } else {
                // ok eller business

                if ($restResult->isJson()) {
                    $tmpData = $restResult->getJsonAsArray();
                    if (isset($tmpData['error'])) {
                        if ($tmpData['error'] === 0) {
                            $this->returnCode = 0;
                            $this->returnType = "ok";
                            $this->returnMessage = "";

                            $this->data = $tmpData['data'];
                        } else if ($tmpData['error'] === 9) {
                            if (stristr($tmpData['message'], "legitimationsreglerne")) {
                                // Legitimation)
                                $this->returnCode = $tmpData['error'];
                                $this->returnType = "legitimation";
                                $this->returnMessage = trim($tmpData['message']);
                            } else {
                                // Business (other)
                                $this->returnCode = $tmpData['error'];
                                $this->returnType = "business";
                                $this->returnMessage = trim($tmpData['message']);
                            }
                        } else if ($tmpData['error'] === 6) {
                            // Data not found
                            $this->returnCode = $tmpData['error'];
                            $this->returnType = "business";
                            $this->returnMessage = trim("DB Changed: " . $tmpData['message']);
                        } else if ($tmpData['error'] === 8) {
                            // Data not found
                            $this->returnCode = $tmpData['error'];
                            $this->returnType = "business";
                            $this->returnMessage = trim("Not found: " . $tmpData['message']);
                            $this->returnNotFound = true;
                        } else if ($tmpData['error'] === 7) {
                            // Forbidden
                            $this->returnCode = $tmpData['error'];
                            $this->returnType = "forbidden";
                            $this->returnMessage = "Forbidden";
                        } else if ($tmpData['error'] === 14) {
                            // IDFR_ERROR
                            $this->returnCode = $tmpData['error'];
                            $this->returnType = "business";
                            $this->returnMessage = trim("IDFR Error: " . $tmpData['message']);
                        } else if ($tmpData['error'] === 3 || $tmpData['error'] === 21) {
                            // Force
                            $this->returnCode = $tmpData['error'];
                            $this->returnType = "business";
                            $this->returnMessage = trim("Force: " . $tmpData['message']);
                        } else {
                            // Other
                            $this->returnCode = $tmpData['error'];
                            $this->returnType = "other";
                            $this->returnMessage = "";
                        }

                        // Add to CorebankFlow
                        if ($this->isOk()) {
                            //CorebankFlow::addSuccess($restResult->getMethod(), $restResult->getUrl() );
                        } else {
                            //CorebankFlow::addFailure($restResult->getMethod(), $restResult->getUrl(), $this->returnType, $this->returnMessage);
                        }
                    } else {
                        throw new Exception("Invalid restResult (no error field)");
                    }
                } else {
                    throw new Exception("Invalid restResult");
                }
            }
        } else {
            throw new Exception("Invalid RCurlResult (not instanceof)");
        }
    }

    public function isPersonalIdError() {
        if (!$this->isOk() && $this->returnType == "legitimation") {
            return true;
        } else {
            return false;
        }
    }

    public function isOk() {
        if ($this->httpCode >= 200 && $this->httpCode < 300 && $this->returnType == "ok") {
            return true;
        } else {
            return false;
        }
    }

    public function isBusinessError() {
        if ($this->returnType == "business") {
            return true;
        } else {
            return false;
        }
    }

    public function isForbidden() {
        if ($this->returnType == "forbidden") {
            return true;
        } else {
            return false;
        }
    }

    public function isNotFound() {
        return $this->returnNotFound;
    }

    // Skal udgå
    public function getData() {
        return $this->data;
    }

    // Skal udgå
    public function getDataByKey($key, $key2 = null) {
        if ($key2) {
            return $this->data[$key][$key2];
        } else {
            return $this->data[$key];
        }
    }

    // Skal udgå
    public function hasItems() {
        if (isset($this->data['numItems']) && $this->data['numItems'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Skal udgå
    public function getItems() {
        if ($this->hasItems() && isset($this->data['items'])) {
            return $this->data['items'];
        } else {
            return array();
        }
    }

    public function getReturnMessage() {
        return $this->returnMessage;
    }

    public function getReturnCode() {
        return $this->returnCode;
    }

    // Skal udgå
    public function getDKErrorString() {
        $retur = "";
        if ($this->returnType == "forbidden") {
            $retur = "Ingen adgang";
        } else if ($this->returnType == "legitimation") {
            $retur = "Kunden mangler legitimation";
        } else if ($this->returnType == "business") {
            $retur = "Kernefejl: " . $this->returnMessage;
        } else if ($this->returnType == "global") {
            $retur = "Generel fejl i kald til Kernesystemet: " . $this->returnMessage;
        } else if ($this->returnType == "other") {
            $retur = "Ukendt fejl";
        }
        return $retur;
    }

    public function isGlobalError() {
        if ($this->returnType == "global") {
            return true;
        } else {
            return false;
        }
    }

    public function isUnknownError() {
        if ($this->returnType == "other") {
            return true;
        } else {
            return false;
        }
    }

   
    
}
