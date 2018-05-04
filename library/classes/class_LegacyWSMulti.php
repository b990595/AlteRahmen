<?php

class LegacyWSMulti {

    private $curl;

    public function __construct() {
        $this->curl = new RCurlX(true);

        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 443){
            $this->curl->setPreUrl("http://" . $_SERVER['HTTP_HOST'] . "/");
        }else{
            $this->curl->setPreUrl("https://" . $_SERVER['HTTP_HOST'] . "/");
        }
       
        $this->curl->setTimeout(60);
        $this->curl->setMaxThreads(64);

        $tmpSession = Session::GetOrDie("userdata");
        $this->curl->setHeaders(array(
            "Authorization: Basic " . base64_encode($tmpSession["userid"] . ":" . $tmpSession["password"]),
            "Content-Type: application/json",
            "Cache-Control: max-age=0"
        ));
    }

    public function GET($module, $controller, $data = array()) {
        return $this->curl->GET($module."/".$controller, $data);
    }

    public function POST($module, $controller, $data = array()) {
        return $this->curl->POST($module."/".$controller, $data);
    }

    public function PUT($module, $controller, $data = array()) {
        return $this->curl->PUT($module."/".$controller, $data);
    }

    public function DELETE($module, $controller, $data = array()) {
        return $this->curl->DELETE($module."/".$controller, $data);
    }

    /**
     * 
     * @return \LegacyWSResult[]
     * @throws Exception
     */
    public function execute() {
        $array = $this->curl->execute();
        if (is_array($array)) {
            $retur = array();
            foreach ($array as $a) {
                $retur[] = new LegacyWSResult($a);
            }
            return $retur;
        } else {
            throw new Exception("Invalid return result RCurlX (not an array)");
        }
    }

}
