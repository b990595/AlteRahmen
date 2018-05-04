<?php

class SkatWS {

    private $curl;

    public function __construct() {
        $this->curl = new RCurl();
        // Altid linp, da det er read data .. og linp har de nyeste ..
        $this->curl->setPreUrl("https://ws.linp.web.jbank.dk/api/skat/");
        $this->curl->setTimeout(30);
        $tmpSession = Session::GetOrDie("userdata");
        $this->curl->setHeaders(array(
            "Accept: application/hal+json",
            "Authorization: Basic " . base64_encode($tmpSession["userid"] . ":" . $tmpSession["password"]),
            "Content-Type: application/json",
            "Cache-Control: max-age=0"
        ));
    }

    /**
     * 
     * @param string $operation
     * @param array $data
     * @return RCurlResult
     */
    private function GET($operation, $data = array()) {
        return $this->curl->GET($operation, $data);
    }
    
    public function aarsopgoerelser($cpr){
        return $this->GET("kunder/".$cpr."/aarsopgoerelser");
    }
   
    public function loenperioder($cpr){
        return $this->GET("kunder/".$cpr."/loenperioder");
    }

}
