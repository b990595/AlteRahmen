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

        $redirect = $r;
        if (strstr($r, "?")){
            $redirect.="&__userTicket=".base64_encode($user.":".sha1($pass));
        }else{
            $redirect.="?__userTicket=".base64_encode($user.":".sha1($pass));
        }


        return $this->renderRedirect($redirect);
    }
    
}
