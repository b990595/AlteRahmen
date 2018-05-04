<?php

class MySqlResult {

    private $data = array();
    private $isError = false;
    private $errorText = "";
    private $isSuccess = true;

    public function __construct($result, $data = null) {
        if (!$result) {
            $this->isError = true;
            $this->isSuccess = false;
        } else {
            $this->isError = false;
            $this->isSuccess = true;

            if (!is_array($data) && is_object($result) && $result->num_rows > 0) {
                $this->fetchData($result);
            } else if (is_array($data)) {
                $this->data = $data;
            }
        }
    }

    private function fetchData($result) {
        /** @var mysqli_result $result */
        While ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $this->data[] = $row;
        }
    }

    public function getData() {
        return $this->data;
    }

    public function hasData() {
        if (is_array($this->data) && count($this->data) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isError() {
        return $this->isError;
    }

    public function setErrorText($txt) {
        $this->errorText = $txt;
    }

    public function getErrorText() {
        if ($this->isError) {
            return $this->errorText;
        } else {
            return "No error";
        }
    }

    public function isSuccess() {
        return $this->isSuccess;
    }

    public function isSuccessAndHasData() {
        if ($this->isSuccess && $this->hasData()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @deprecated Use isSuccessAndHasData()
     * @return bool
     */
    public function isSucessAndHasData()
    {
        return $this->isSuccessAndHasData();
    }
}
