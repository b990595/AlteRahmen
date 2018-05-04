<?php

trait OrchResultTrait {

    private $flatArray = array();
    
    public function addResult(OrchResultInterface $result) {
        $this->results[] = $result;
    }

    private function generateCaller() {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        if (mb_substr($bt[2]['class'], 0, 10) == "CorebankWS") {
            $this->caller['created_by']['class'] = isset($bt[3]['class'])?$bt[3]['class']:"";
            $this->caller['created_by']['function'] = isset($bt[3]['function'])?$bt[3]['function']:"";
            $this->caller['called_from']['class'] = isset($bt[5]['class'])?$bt[5]['class']:"";
            $this->caller['called_from']['function'] = isset($bt[5]['function'])?$bt[5]['function']:"";
        } else {
            $this->caller['created_by']['class'] = isset($bt[2]['class'])?$bt[2]['class']:"";
            $this->caller['created_by']['function'] = isset($bt[2]['function'])?$bt[2]['function']:"";
            $this->caller['called_from']['class'] = isset($bt[4]['class'])?$bt[4]['class']:"";
            $this->caller['called_from']['function'] = isset($bt[4]['function'])?$bt[4]['function']:"";
        }
    }

    /**
     * 
     * @return OrchResultLE[]
     */
    public function createFlatResultsArray(){
        $this->flatArray = array(); // Nulstil
        $this->flatArray[] = new OrchResultLE($this); // Tilføj sig selv som første
        $this->createFlatArrayRecur($this->getSubResults()); // Loop over træ-strukturen
        return $this->flatArray;
    }
        
    private function createFlatArrayRecur($input){
        if (is_array($input)){
            foreach ($input as $i){
                $this->createFlatArrayRecur($i);
            }
        }else if ($input instanceof OrchResultInterface){
            $this->flatArray[] = new OrchResultLE($input);
            $this->createFlatArrayRecur($input->getSubResults());
        }else{
            throw new Exception("Input [createFlatArrayRecur] must be array or instance of OrchResultInterface");
        }
    }
 
    public function getDKErrorString() {
        $retur = "";
        if ($this->isForbidden()) {
            $retur = "Ingen adgang";
        } else if ($this->isPersonalIdError()) {
            $retur = "Kunden mangler legitimation";
        } else if ($this->isBusinessError()) {
            $retur = "Kernefejl: " . $this->returnMessage;
        } else if ($this->isGlobalError()) {
            $retur = "Generel fejl i kald til Kernesystemet: " . $this->returnMessage;
        } else if ($this->isUnknownError()) {
            $retur = "Ukendt fejl";
        }
        return $retur;
    }
    
}
