<?php

interface OrchResultInterface extends OrchResultIteratorInterface {

    public function isPersonalIdError();

    public function getData();

    public function getReturnCode();

    public function getReturnMessage();

    public function isOk();

    public function isBusinessError();

    public function isForbidden();

    public function isNotFound();

    public function isGlobalError();
    
    public function isUnknownError();

    public function addResult(OrchResultInterface $result);

    public function hasItems();

    public function getItems();
    
    public function getCaller();
    
    public function isNullObject();
    
    public function getSubResults();
    
    /**
     * @return OrchResultLE[]
     */
    public function createFlatResultsArray();
    
    public function getDKErrorString();
    
}
