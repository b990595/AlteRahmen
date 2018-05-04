<?php

class OrchResultException extends Exception implements OrchResultInterface, OrchResultLEInterface {

    use OrchResultTrait;
    use OrchResultIteratorTrait;

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
    private $caller = array();
    private $results = array();

    /**
     * @param OrchResult, CorebankWSResult or Array $result
     */
    public function __construct($result) {
        if ($result !== null) {
            if ($result instanceof OrchResultInterface || $result instanceof CorebankWSResult) {
                if ($result->isOk()) {
                    throw new Exception("OrchResultException cannot be contructed with an OK result.");
                }
                $this->data = array();
                $this->returnCode = $result->getReturnCode();
                $this->returnMessage = $result->getReturnMessage();
                $this->isBusinessError = $result->isBusinessError();
                $this->isForbidden = $result->isForbidden();
                $this->isNotFound = $result->isNotFound();
                $this->isPersonalIdError = $result->isPersonalIdError();
                $this->isUnknownError = $result->isUnknownError();
                $this->isGlobalError = $result->isGlobalError();
            }
        }

        $this->generateCaller();

        parent::__construct($this->returnMessage, $this->returnCode);
    }

    public function getData() {
        return $this->data;
    }

    public function getReturnCode() {
        return $this->returnCode;
    }

    public function getReturnMessage() {
        return $this->returnMessage;
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

    public function isOk() {
        return $this->isOk;
    }

    public function isGlobalError() {
        return $this->isGlobalError;
    }

    public function getCaller() {
        return $this->caller;
    }

    public function isPersonalIdError() {
        return $this->isPersonalIdError;
    }

    public function isUnknownError() {
        return $this->isUnknownError;
    }

    public function hasItems() {
        return false;
    }

    public function getItems() {
        return null;
    }

    public function isNullObject() {
        return false; // Cannot be NULL-object
    }
    
    public function getSubResults(){
        return $this->results;
    }
        
    public function setData($data) {
        $this->data = $data;
    }

}
