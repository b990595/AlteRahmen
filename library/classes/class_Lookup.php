<?php

class Lookup {
    
    public static function getNameByCvr($cvr){
        $f = \Jblib\Cvr\ApiFactory::factory();
        $data = $f->get($cvr);
        return isset($data->name)?trim($data->name):"";
    }
  
}
