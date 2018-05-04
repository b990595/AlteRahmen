<?php

class SqlLog {

    /**
     * 
     * @param String $system
     * @param FR_USER $user
     */
    public static function log($system, $user) {
        $db = new MySqlDB(FR_CONN_LOG);
        $store = array();
        $store['system'] = $system;
        $store['tuser'] = $user->getUserid();
        $store['navn'] = $user->getNavn();
        $store['filial'] = $user->getFilial();
        $store['afdeling'] = $user->getAfdeling();
        $store['afdeling_key'] = $user->getAfdelingKey();
        $store['dato'] = date("Y-m-d");
        $tmpReq = $_REQUEST;
        if (is_array($tmpReq)){
            if (isset($tmpReq['__userTicket'])){
                unset($tmpReq['__userTicket']);
            }
            $tmpReq = json_encode($tmpReq);
        }else{
            $tmpReq = "";
        }
        $store['request'] = $tmpReq;
        $db->saveRow($store, "legacy");
    }

}
