<?php

namespace App\System\Controller;

class Login extends \FR_WebController{
    
    public function fallback($r){
        $this->set("r", $r);
        return $this->render("Login");
    }
    
    public function doLogin(){
        $user = \HttpRequest::PostOrDie("user");
        $pass = \HttpRequest::PostOrDie("pass");
        $r = \HttpRequest::PostOrDie("r");

        new \FR_User(base64_encode($user.":".$pass));

        return $this->renderRedirect($r);
    }
    
}
