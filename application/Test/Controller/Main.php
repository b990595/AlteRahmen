<?php
namespace App\Test\Controller;

class Main extends \FR_WebController{

    public function hello(){
        return $this->render("Hello");
    }

}


