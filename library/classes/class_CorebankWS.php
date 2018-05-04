<?php

class CorebankWS {

    private $curl;

    public function __construct() {
        $this->curl = new RCurl();
        if (FR_APPLICATION_ENV == "prod") {
            $this->curl->setPreUrl("https://ws.linp.web.jbank.dk/sdckerne/");
        } else {
            //$this->curl->setPreUrl("https://ws.linp.web.jbank.dk/sdckerne/");
            $this->curl->setPreUrl("https://ws.lint.web.jbank.dk/sdckerne/");
        }
        $this->curl->setTimeout(30);
        $this->setHeaders(0);
    }

    private function setHeaders(int $maxage = 0) {
        $tmpSession = Session::GetOrDie("userdata");
        $password = "";
        if (FR_APPLICATION_ENV == "prod" || FR_APPLICATION_ENV == "test"){
            $password = (new SharedSecret())->generateAuthenticationPassword("hmac", "sha256", time(), $tmpSession["userid"]);
        }else{
            // DEVELOPMENT (KUN USER/PASS)
            $password = $tmpSession["password"];
        }

        $this->curl->setHeaders(array(
            "Accept: application/vnd.sdc-kerne-v81-broker.v1+json",
            "Authorization: Basic " . base64_encode($tmpSession["userid"] . ":" . $password),
            "Content-Type: application/json",
            "Cache-Control: max-age=" . $maxage
        ));


    }

    private function setMaxAge(int $seconds) {
        $this->setHeaders($seconds);
    }

    /**
     * 
     * @param string $operation
     * @param  array $data
     * @param int maxAge (allowed cache in seconds)
     * @return \OrchResult
     */
    public function GET($operation, $data = array(), int $maxAge = 0) {
        $this->setMaxAge($maxAge);
        $r = $this->curl->GET($operation, $data);
        if ($maxAge > 0) {
            $this->setMaxAge(0);
        }
        $tmp_cbws = new CorebankWSResult($r);
        return new OrchResult($tmp_cbws);
    }

    /**
     * 
     * @param string $operation
     * @param array $data
     * @param bool $force
     * @return \OrchResult
     */
    public function POST($operation, $data = array(), $force = false) {
        $this->setMaxAge(0);
        if ($force) {
            $r = $this->curl->POST($operation . "?force=1", $data, true);
        } else {
            $r = $this->curl->POST($operation, $data, true);
        }
        $tmp_cbws = new CorebankWSResult($r);
        return new OrchResult($tmp_cbws);
    }

    /**
     * 
     * @param string $operation
     * @param array $data
     * @param bool $force
     * @return \OrchResult
     */
    public function PUT($operation, $data = array(), $force = false) {
        $this->setMaxAge(0);
        if ($force) {
            $r = $this->curl->PUT($operation . "?force=1", $data, true);
        } else {
            $r = $this->curl->PUT($operation, $data, true);
        }
        $tmp_cbws = new CorebankWSResult($r);
        return new OrchResult($tmp_cbws);
    }

    /**
     * 
     * @param string $operation
     * @param array $data
     * @param bool $force
     * @return \OrchResult
     */
    public function DELETE($operation, $data = array(), $force = false) {
        $this->setMaxAge(0);
        if ($force) {
            $r = $this->curl->DELETE($operation . "?force=1", $data, true);
        } else {
            $r = $this->curl->DELETE($operation, $data, true);
        }
        $tmp_cbws = new CorebankWSResult($r);
        return new OrchResult($tmp_cbws);
    }

}
