<?php

class OrchResult implements OrchResultInterface, OrchResultLEInterface {

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
    private $isNullObject = false;
    private $caller = array();
    private $results = array();

    /**
     * @param OrchResult, CorebankWSResult or Array $result
     */
    public function __construct($result = null, $addBusinessErrorToFlow = true) {
        if ($result !== null) {
            if (is_array($result)) {
                $this->data = $result;
                $this->isOk = true;
                $this->returnCode = 0;
            } else if ($result instanceof OrchResultInterface || $result instanceof CorebankWSResult) {

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
            }
        } else {
            // Tomt resultat

            $this->isOk = true;
            $this->returnCode = 0;
            $this->returnMessage = "";
            $this->isNullObject = true;
        }

        if ($this->isBusinessError && $addBusinessErrorToFlow) {
            CorebankFlow::addBusinessError();
        }

        $this->generateCaller();
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

    public function setData($data) {
        $this->data = $data;
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

    public function setNotFoundError($message) {
        RestResultSettings::changeExceptionReturnTo404NotFound();
        return $this->setBusinessError($message);
    }

    public function setBadRequestError($message) {
        RestResultSettings::changeExceptionReturnTo400BadRequest();
        return $this->setBusinessError($message);
    }

    public function setBusinessError($message = "", $addBusinessErrorToFlow = true) {
        $this->isOk = false;
        $this->isBusinessError = true;
        $this->returnCode = 9;
        $this->returnMessage = $message;
        if ($addBusinessErrorToFlow) {
            CorebankFlow::addBusinessError();
        }
    }

    public function isNullObject() {
        return $this->isNullObject;
    }

    public function getSubResults() {
        return $this->results;
    }

    public function isCorebankProxyResult() {
        if (isset($this->data['StdRtrnInfo']['RtrnCd'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Omdanner resultdata til et array tilsvarende det, der findes ved en implementeret service.
     * Dog under repeat på lange lister osv.
     */
    public function normalizeCorebankProxyResultData() {
        // Gør ingenting, hvis ikke der er tale om et proxy-result
        if ($this->isCorebankProxyResult()) {
            $type = "";
            $keyName = "";
            foreach (array_keys($this->data) as $key) {
                $letter = mb_substr($key, mb_strlen($key) - 1, 1);
                // Første key med D eller L afgør sagen.
                if ($letter === "L" && $type === "" && is_array($this->data[$key])) {
                    // Liste
                    $type = "list";
                    $keyName = $key;
                } else if ($letter === "D" && $type === "" && is_array($this->data[$key])) {
                    // Data
                    $type = "data";
                    $keyName = $key;
                }
            }

            if ($type == "list") {
                foreach (array_keys($this->data[$keyName]) as $key) {
                    $letter = mb_substr($key, mb_strlen($key) - 1, 1);
                    if ($letter === "D" && is_array($this->data[$keyName][$key]) && count($this->data[$keyName][$key]) == $this->data[$keyName]['NoOfRows']) {
                        $newData = array("numItems" => $this->data[$keyName]['NoOfRows'], "items" => $this->data[$keyName][$key]);
                        $this->data = $newData;
                        return $this;
                    }else if ($letter === "D" && $this->data[$keyName]['NoOfRows'] == 1 && is_array($this->data[$keyName][$key]) && !isset($this->data[$keyName][$key][0])) {
                        $newData = array("numItems" => $this->data[$keyName]['NoOfRows'], "items" => array($this->data[$keyName][$key]));
                        $this->data = $newData;
                        return $this;
                    }
                }
                // Hvis ingen data ..
                $newData = array("numItems" => 0, "items" => array());
                $this->data = $newData;
                return $this;

            } else if ($type == "data") {
                $newData = $this->data[$keyName];
                $this->data = $newData;
                return $this;
            }
            throw new Exception("Could not create new data from proxy-result.");
        }
        return $this;
    }

}
