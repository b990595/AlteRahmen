<?php

class RFactory {

    public static function RApi() {
        $c = new RCurl();
        $c->setPreUrl("https://annie.web.jbank.dk/api/");
        $c->setTimeout(30);
        $c->setHeaders(array(
            "Cache-Control: max-age=0",
            "X-RjApi-SdcUser: ".Session::GetOrDie("userid")
        ));
        return $c;
    }
    
    
}
