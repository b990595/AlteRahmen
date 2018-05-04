<?php

interface OrchResultLEInterface {

    public function getData();

    public function getReturnCode();

    public function getReturnMessage();

    public function getCaller();
    
    public function hasItems();

    public function getItems();

    public function isOk();

    public function isBusinessError();

    public function isPersonalIdError();

    public function isForbidden();

    public function isNotFound();

    public function isGlobalError();

    public function isUnknownError();
    
    public function isNullObject();
    
    public function getDKErrorString();
    
}
