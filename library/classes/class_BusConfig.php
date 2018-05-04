<?php

use Jbank\Busconfig\Model;

class BusConfig{

    public static function get(string $sheet){
        \validate::not_empty($sheet);
        $db = new Model\SettingsDbStore();
        $sheetData = $db->getNewestData($sheet);
        if (isset($sheetData['data'])){
            return self::convertToArray($sheetData['data']);
        }
        throw new \Exception("No configdata found on sheet: ".$sheet);
    }

    private static function convertToArray(array $data){
        $resultArr = [];

        foreach ($data as $key => $value) {
            $keyParts = explode('__', $key);
            if (count($keyParts) > 1) {
                if (!isset($resultArr[end($keyParts)])) {
                    $resultArr[end($keyParts)] = [];
                }
                $resultArr[end($keyParts)][implode('__', array_slice($keyParts, 0, -1))] = $value;
            }
            else {
                $resultArr[$key] = $value;
            }
        }

        return $resultArr;
    }

}