<?php

class RCurlResult{
    
    private $info = array();
    private $response = "";
    private $data = false;
    private $method = null;
    private $url = null;
    
    public function __construct($response, $info, $method, $url) {
        $this->info = $info;
        $this->response = $response;
        $this->method = strtolower($method);
        $this->url = $url;
    }
    
    public function getUrl(){
        return $this->url;
    }
    
    public function getMethod(){
        return $this->method;
    }
    
    public function getResponse(){
        return $this->response;
    }
    
    public function getInfo(){
        return $this->info;
    }
    
    public function is2xx(){
        if ($this->info['http_code']>=200 && $this->info['http_code']<=299){
            return true;
        }else{
            return false;
        }
    }
    
    public function is404NotFound(){
        if ($this->info['http_code']>=404){
            return true;
        }else{
            return false;
        }
    }
    
    public function is403Forbidden(){
        if ($this->info['http_code']>=403){
            return true;
        }else{
            return false;
        }
    }
    
    public function isJson(){
        $this->data = json_decode($this->response, true);
        if (is_array($this->data)){
            return true;
        }else{
            return false;
        }
    }
    
    public function getJsonAsArray(){
        if (is_array($this->data)){
            return $this->data;
        }else{
            $this->data = json_decode($this->response, true);
            return $this->data;
        }
    }
    
}