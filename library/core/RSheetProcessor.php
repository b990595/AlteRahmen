<?php

abstract class FR_RSheetProcessor{
    /**
     * OVERRIDE for custom processing of data
     * @param array $newData
     * @param array $oldData
     * @param bool $isRecalc
     * @return array
     */
    public function processData($newData, $oldData, $isRecalc){
        return;
    }
    
    /**
     * OVERRIDE for custom JS execution
     * @param array $newData
     * @param array $oldData
     * @return string
     */
    public function executeJSAfterUpdate($newData, $oldData){
        return "";
    }
    
    /**
     * OVERRIDE for custom event after save
     * @param array $newData
     * @param array $oldData
     * @param bool $isRecalc
     * @return void
     */
    public function afterSave($newData, $oldData, $isRecalc){
        return;
    }
}