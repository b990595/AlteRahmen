<?php
class CorebankHelpers{
    
    /**
     * Konverterer et beløb til DKK
     * @param number $beloeb
     * @param string $iso (DKK,EUR,USD,CHF osv....)
     * @return number
     * @throws Exception
     */
    public static function toDKKByISO($beloeb, $iso){
        $iso = trim(strtoupper($iso));
        if ($iso == "DKK"){
            return $beloeb / 1;
        }else{
            $m = new model_corebank_crncy_svc_Exchange(null);
            $call = $m->exchangeByISO($beloeb, $iso, "DKK");
            if ($call->isOk()){
                $data = $call->getData();
                return $data['AmountWf'] / 1;
            }
        }
        // Burde ikke kunne ske, da exchangeByISO selv kaster exception ..
        throw new Exception("Fejl under konvertering af valuta.");
    }
    
}
