<?php

class OrchResultLE implements OrchResultLEInterface {

    private $data = array();
    private $returnCode = 1;
    private $returnMessage = "";
    private $isOk = false;
    private $isBusinessError = false;
    private $isForbidden = false;
    private $isNotFound = false;
    private $isPersonalIdError = false;
    private $isGlobalError = false;
    private $isUnknownError = false;
    private $isNullObject = false;
    private $caller = array();

    /**
     * @param OrchResultInterface result
     */
    public function __construct($result) {
        if ($result instanceof OrchResultInterface) {

            $this->data = $result->getData();
            $this->returnCode = $result->getReturnCode();
            $this->returnMessage = $result->getReturnMessage();
            $this->isOk = $result->isOk();
            $this->isBusinessError = $result->isBusinessError();
            $this->isForbidden = $result->isForbidden();
            $this->isNotFound = $result->isNotFound();
            $this->isPersonalIdError = $result->isPersonalIdError();
            $this->isGlobalError = $result->isGlobalError();
            $this->isUnknownError = $result->isUnknownError();
            $this->isNullObject = $result->isNullObject();
            $this->caller = $result->getCaller();
            
        } else {
            throw new Exception("Invalid input-type. Must be instance of OrchResultInterface");
        }
    }

    public function isPersonalIdError() {
        return $this->isPersonalIdError;
    }

    public function isOk() {
        return $this->isOk;
    }

    public function isBusinessError() {
        return $this->isBusinessError;
    }

    public function isForbidden() {
        return $this->isForbidden;
    }

    public function isNotFound() {
        return $this->isNotFound;
    }

    public function isGlobalError() {
        return $this->isGlobalError;
    }

    public function isUnknownError() {
        return $this->isUnknownError;
    }

    public function getCaller() {
        return $this->caller;
    }

    public function getData() {
        return $this->data;
    }

    public function getDataByKey($key, $key2 = null) {
        if ($key2) {
            return $this->data[$key][$key2];
        } else {
            return $this->data[$key];
        }
    }

    public function countItems() {
        if ($this->hasItems()) {
            return (int) $this->data['numItems'];
        } else {
            return 0;
        }
    }

    public function hasItems() {
        if (isset($this->data['numItems']) && $this->data['numItems'] > 0) {
            return true;
        } else {
            return false;
        }
    }

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

    public function getDKErrorString() {
        $retur = "";
        if ($this->isForbidden()) {
            $retur = "Ingen adgang";
        } else if ($this->isPersonalIdError()) {
            $retur = "Kunden mangler legitimation";
        } else if ($this->isBusinessError()) {
            $retur = "Kernefejl: " . $this->returnMessage;
        } else if ($this->isGlobalError()) {
            $retur = "Generel fejl i kald til Kernesystemet: " . $this->returnMessage;
        } else if ($this->isUnknownError()) {
            $retur = "Ukendt fejl";
        }
        return $retur;
    }

    public function isNullObject() {
        return $this->isNullObject;
    }

}
