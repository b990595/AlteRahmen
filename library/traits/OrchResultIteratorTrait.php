<?php

trait OrchResultIteratorTrait {

    /**
    * @return boolean
     */
    public function resultsIteratorHasErrors() {
        $fejl = false;
        $array = $this->createFlatResultsArray();
        foreach ($array as $a){
            if (!$a->isOk()){
                $fejl = true;
            }
        }
        return $fejl;
    }
    
    /**
     * @return boolean
     */
    public function resultsIteratorHasNoErrors() {
        if ($this->resultsIteratorHasErrors()){
            return false;
        }else{
            return true;
        }
    }

     /**
     * @return OrchResultLEInterface[]
     */
    public function resultsIteratorGetErrors() {
        $fejl = array();
        $array = $this->createFlatResultsArray();
        foreach ($array as $a){
            if (!$a->isOk()){
                $fejl[] = $a;
            }
        }
        return $fejl;
    }

}
