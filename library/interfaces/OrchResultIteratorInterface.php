<?php

interface OrchResultIteratorInterface {

    /**
     * @return boolean
     */
    public function resultsIteratorHasErrors();
    /**
     * @return OrchResultLEInterface[]
     */
    public function resultsIteratorGetErrors();
    /**
     * @return boolean
     */
    public function resultsIteratorHasNoErrors();
    
   
}
