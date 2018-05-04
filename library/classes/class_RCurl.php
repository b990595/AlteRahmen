<?php

class RCurl {

    private $_headers = array();
    private $_options = array();
    private $_timeout = 30;
    private $_preUrl = "";

    public function __construct() {
        return $this;
    }

    public function setPreUrl($preUrl) {
        $this->_preUrl = $preUrl;
    }

    private function makeUrl($url) {
        if (trim($this->_preUrl) == "") {
            return $url;
        } else {
            if (strtolower(mb_substr($url, 0, 7)) != "http://" && strtolower(mb_substr($url, 0, 8)) != "https://") {
                $tmpEnd = mb_substr($this->_preUrl, mb_strlen($this->_preUrl) - 1, 1);
                $tmpStart = mb_substr($url, 0, 1);
                if ($tmpEnd == "/" && $tmpStart == "/") {
                    $url = mb_substr($url, 1);
                }
                return $this->_preUrl . $url;
            } else {
                return $url;
            }
        }
    }

    public function setHeaders($headers = array()) {
        $this->_headers = $headers;
        return $this;
    }

    public function setOptions($options = array()) {
        $this->_options = $options;
        return $this;
    }

    public function setTimeout($sec) {
        $this->_timeout = $sec;
        return $this;
    }

    public function GET($service, $data = array()) {
        return $this->call("GET", $service, $data);
    }

    public function POST($service, $data = array(), $jsonEncodePostFields = false) {
        return $this->call("POST", $service, $data, $jsonEncodePostFields);
    }

    public function PUT($service, $data = array(), $jsonEncodePostFields = false) {
        return $this->call("PUT", $service, $data, $jsonEncodePostFields);
    }

    public function DELETE($service, $data = array(), $jsonEncodePostFields = false) {
        return $this->call("DELETE", $service, $data, $jsonEncodePostFields);
    }

    private function call($method, $url, $data = array(), $jsonEncodePostFields = false) {
        $url = $this->makeUrl($url);
        $method = strtoupper($method);
        $ch = curl_init();

        $options = array();

        if (is_array($this->_options)) {
            $options = $this->_options;
        }

        if ($method == 'GET') {
            $url.="?" . http_build_query($data, '', '&');
        } else if ($method == 'DELETE') {
            $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            if ($jsonEncodePostFields) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            } else {
                $options[CURLOPT_POSTFIELDS] = http_build_query($data, '', '&');
            }
        } else if ($method == 'POST') {
            $options[CURLOPT_POST] = true;
            if ($jsonEncodePostFields) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            } else {
                $options[CURLOPT_POSTFIELDS] = http_build_query($data, '', '&');
            }
        } else if ($method == 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            if ($jsonEncodePostFields) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            } else {
                $options[CURLOPT_POSTFIELDS] = http_build_query($data, '', '&');
            }
        }

        $options[CURLOPT_CONNECTTIMEOUT] = max(1, $this->_timeout); //minimum of 1 second
        $options[CURLOPT_TIMEOUT] = max(1, $this->_timeout); //minimum of 1 second

        $options[CURLOPT_URL] = $url;

        if (is_array($this->_headers)) {
            $options[CURLOPT_HTTPHEADER] = $this->_headers;
        }
        //$options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_RETURNTRANSFER] = true;


        $opts_set = curl_setopt_array($ch, $options);
        if (!$opts_set) {
            Throw new Exception("Could not set options");
        }

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return new RCurlResult($result, $info, $method, $url);
    }

}
