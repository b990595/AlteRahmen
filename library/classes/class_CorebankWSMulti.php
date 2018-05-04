<?php

class CorebankWSMulti {

    private $curl;

    public function __construct() {
        $this->curl = new RCurlX(true);
        if (FR_APPLICATION_ENV == "prod") {
            $this->curl->setPreUrl("https://ws.linp.web.jbank.dk/sdckerne/");
        } else {
            //$this->curl->setPreUrl("https://ws.linp.web.jbank.dk/sdckerne/");
            $this->curl->setPreUrl("https://ws.lint.web.jbank.dk/sdckerne/");
        }

        $this->curl->setTimeout(60);
        $this->curl->setMaxThreads(64);

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

    public function GET($operation, $data = array(), int $cacheMaxAge = 0) {
        $this->setHeaders($cacheMaxAge);
        return $this->curl->GET($operation, $data);
    }

    public function POST($operation, $data = array(), $force = false) {
        $this->setHeaders(0);
        if ($force) {
            return $this->curl->POST($operation . "?force=1", $data);
        } else {
            return $this->curl->POST($operation, $data);
        }
    }

    public function PUT($operation, $data = array(), $force = false) {
        $this->setHeaders(0);
        if ($force) {
            return $this->curl->PUT($operation . "?force=1", $data);
        } else {
            return $this->curl->PUT($operation, $data);
        }
    }

    public function DELETE($operation, $data = array(), $force = false) {
        $this->setHeaders(0);
        if ($force) {
            return $this->curl->DELETE($operation . "?force=1", $data);
        } else {
            return $this->curl->DELETE($operation, $data);
        }
    }

    /**
     * 
     * @return \OrchResult[]
     * @throws Exception
     */
    public function execute() {
        $array = $this->curl->execute();
        if (is_array($array)) {
            $retur = array();
            foreach ($array as $a) {
                $tmp_cbws = new CorebankWSResult($a);
                $retur[] = new OrchResult($tmp_cbws);
            }
            return $retur;
        } else {
            throw new Exception("Invalid return result RCurlX (not an array)");
        }
    }

}
