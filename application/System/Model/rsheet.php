<?php

class model_system_rsheet
{

    // FIXME: created bliver fornyet, hvis der er kommet et nyt felt på sheetet
    public static function calc()
    {
        // Original Data
        $a = $_POST;

        // ConnectionData
        $doSaveForm = true;
        $csrf_hash = isset($a['__hash']) ? trim($a['__hash']) : "";

        if ($csrf_hash == "no_save") {
            // NoSaveForm (do not save, only calc)
            $doSaveForm = false;
        } else {
            $CSRFSession = FR_RSheetCSRF::get($csrf_hash);

            if (empty($CSRFSession) && $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] && isset($a['__hashjson64'])) {
                $CSRFSession = json_decode(base64_decode($a['__hashjson64']), true);
            }

            if (empty($CSRFSession)) {
                return Rjson::arrayToJSON(array("__returncode" => "timeout (empty CSRF)"));
            }

        }

        $tmpSheetProcessor = null;
        if ($a['__sheetProcessorString'] != "") {
            $tmpSheetProcessorString = $a['__sheetProcessorString'];
            $tmpSheetProcessor = new $tmpSheetProcessorString();
        }

        if ($doSaveForm) {
            $saveDB = new MySqlDB($CSRFSession['connectionString']);
        }
        // Afgør om det er en genberegning af dokumentet
        $recalc = HttpRequest::GetOrZero("recalc");

        // Er calc kaldt ved visning af RSheet (så skal updater / updated ikke ajourføres)
        // Det burde kun ske, når selve formularen er blevet ændret og data ikke er genberegnet med den nye formular.
        $calledOnLoad = HttpRequest::GetOrZero("calledOnLoad");

        // Afgør tuser (til logning)
        $tuser = Session::GetOrDie("userid");


        // Clean-up (exclude internal fields)
        $data = null;
        foreach (array_keys($a) as $key) {
            if (mb_substr($key, 0, 2) != "__") {
                $data [$key] = $a [$key];
            }
        }


        // Old JSON (til senere brug)
        $oldjson = base64_decode($a ['__oldjson']);
        $olddata = Rjson::JSONToArray($oldjson);


        // EDIT - LOCK
        if ($calledOnLoad == 0 && $recalc == 0 && $doSaveForm) { // Da dette ellers vil blokere for oprettes af ny formular ..
            $elQ = $saveDB->getFirstRow("`json`", $CSRFSession['table'], "`" . $CSRFSession['key'] . "`='" . $CSRFSession['keyValue'] . "'");
            if ($elQ->isSucessAndHasData()) {
                $tmpQData = $elQ->getData();
                $tmpQData2 = Rjson::JSONToArray(base64_decode($tmpQData['json']));

                $dbSay = $tmpQData2['__updated_by'] . " " . $tmpQData2['__updated'];
                $sheetSay = $olddata['__updated_by'] . " " . $olddata['__updated'];


                if ($dbSay != $sheetSay) {
                    return Rjson::arrayToJSON(array("__returncode" => "editlock: DbSay: " . $dbSay . " | FormSay: " . $sheetSay));
                }
            } else {
                return Rjson::arrayToJSON(array("__returncode" => "Database fejl (read json)"));
            }
        }


        // fjerne felter, for at kunne sammenligne senere
        if ($doSaveForm) {
            unset($olddata['__updated']);
            unset($olddata['__updated_by']);
            unset($olddata['__created']);
            unset($olddata['__created_by']);
        }
        // Sorterer efter array keys (for at kunne sammenligne old og new json)
        $olddata = self::sortArray($olddata);
        $oldjson = RJson::arrayToJSON($olddata);


        $tmpdata = $data;
        // Sorterer efter array keys (for at kunne sammenligne old og new json)
        $tmpdata = self::sortArray($tmpdata);
        $newjson = Rjson::arrayToJSON($tmpdata);
        $newjson_before_process = $newjson;


        // ****************************************************
        // PROCESSING (part 1 - processData)
        // ****************************************************
        if (is_object($tmpSheetProcessor) && $tmpSheetProcessor instanceof FR_RSheetProcessor) {

            // Disse to variable genbruges i processing part-2, så de må ikke ændres (kun her lige nedenfor)
            $tmpNewData = json_decode($newjson, true);
            $tmpOldData = json_decode($oldjson, true);

            $tmpNewData2 = $tmpSheetProcessor->processData($tmpNewData, $tmpOldData, (bool)$recalc);
            if (is_array($tmpNewData2)) {
                $newjson2 = json_encode($tmpNewData2);
                if ($newjson2 != $newjson) {
                    // Sortering igen ...
                    $tmpdata = self::sortArray($tmpNewData2);
                    $newjson = Rjson::arrayToJSON($tmpdata);
                    $data = $tmpdata;
                    $tmpNewData = $tmpdata;
                }
            }
        }

        // ****************************************************
        // Return
        if ($newjson != $oldjson || $newjson_before_process != $oldjson) {
            // Overskriver ikke updater og updated ved recalc
            if ($doSaveForm) {
                if ($calledOnLoad != 1 && $recalc == 0) {
                    $data ['__updated'] = date("Y-m-d H:i:s");
                    $data ['__updated_by'] = $tuser;
                } else {
                    $data ['__updated'] = $a ['__updated'] ?? date("Y-m-d H:i:s");
                    $data ['__updated_by'] = $a ['__updated_by'] ?? $tuser;
                }

                if (!isset($a ['__created']) || trim($a ['__created']) == "") {
                    $data ['__created'] = date("Y-m-d H:i:s");
                } else {
                    $data ['__created'] = $a ['__created'];
                }

                if (!isset($a ['__created_by']) || trim($a ['__created_by']) == "") {
                    $data ['__created_by'] = $tuser;
                } else {
                    $data ['__created_by'] = $a ['__created_by'];
                }
            }


            // *********************************************************************
            // StoreFields (er til sidst, sådan at alt kan gemmes som storefield
            // *********************************************************************
            if ($doSaveForm) {
                $sqlparamsCount = 0;
                $sqlparams = null;
                $sqladd = "";
                $storefields = Rjson::JSONToArray(base64_decode($a ['__storefields']));
                if (is_array($storefields)) {
                    foreach ($storefields as $sf) {
                        if (isset($data [$sf])){
                            $sqladd .= ",";
                            //$sqladd .= "`$sf`=?sqlparam$sqlparamsCount";
                            $sqladd .= "`" . $saveDB->escapeKey($sf) . "`='" . $saveDB->escapeString($data [$sf]) . "'";

                            $sqlparams [] = str_ireplace("|", "", base64_encode($data [$sf]));
                            $sqlparamsDblEncoded = "yes";
                            $sqlparamsCount++;
                        }
                    }
                }
                $sqlparamString = "";
                if (is_array($sqlparams)) {
                    $sqlparamString = base64_encode(implode("|", $sqlparams));
                }
                if (trim($sqlparamString == "")) {
                    $sqlparamString = "0";
                }
            }

            // **********************************************************************
            // *********************************************************************
            // StoreFieldsExt (seperat tabel)
            // *********************************************************************
            if ($doSaveForm) {
                $sqlparamsCountExt = 0;
                $sqlparamsExt = null;
                $sqladdExt = "";
                $storefieldsExt = Rjson::JSONToArray(base64_decode($a ['__storefieldsExt']));
                if (is_array($storefieldsExt)) {
                    foreach (array_keys($storefieldsExt) as $sfkey) {
                        if (isset($data [$storefieldsExt[$sfkey]])){
                            $sqladdExt .= ",";
                            $sqladdExt .= "`" . $saveDB->escapeKey($sfkey) . "`='" . $saveDB->escapeString($data [$storefieldsExt[$sfkey]]) . "'";
                            $sqlparamsExt [] = str_ireplace("|", "", base64_encode($data [$storefieldsExt[$sfkey]]));
                            $sqlparamsCountExt++;
                        }
                    }
                }
                $sqlparamStringExt = "";
                if (is_array($sqlparamsExt)) {
                    $sqlparamStringExt = base64_encode(implode("|", $sqlparamsExt));
                }
                if (trim($sqlparamStringExt == "")) {
                    $sqlparamStringExt = "0";
                }
            }
            // **********************************************************************


            $storeJson = RJson::arrayToJSON($data);
            $data['__newjson'] = base64_encode($storeJson);


            if ($doSaveForm) {
                // Database
                $sql = "REPLACE INTO `" . $CSRFSession['table'] . "` SET `" . $CSRFSession['key'] . "`='" . $CSRFSession['keyValue'] . "', `json`='" . base64_encode($storeJson) . "'" . $sqladd;


                $sqlExt = "";
                if (trim($CSRFSession['tableExt']) != "") {
                    $sqlExt = "REPLACE INTO `" . $CSRFSession['tableExt'] . "` SET `" . $CSRFSession['key'] . "`='" . $CSRFSession['keyValue'] . "'" . $sqladdExt;
                }

                if ($sqlExt != "") {
                    if ($saveDB->sqlQuery($sql)->isSuccess()) {
                        if ($saveDB->sqlQuery($sqlExt)->isSuccess()) {
                            $data ['__returncode'] = "ok";
                        } else {
                            $data ['__returncode'] = "database fejl (Table: " . $CSRFSession['tableExt'] . ")";
                        }
                    } else {
                        $data ['__returncode'] = "database fejl (Table: " . $CSRFSession['table'] . ")";
                    }
                } else {
                    if ($saveDB->sqlQuery($sql)->isSuccess()) {
                        $data ['__returncode'] = "ok";
                    } else {
                        $data ['__returncode'] = "database fejl (Table: " . $CSRFSession['table'] . ")";
                    }
                }
            } else {
                // IF !$doSaveForm
                $data ['__returncode'] = "ok";
            }
        } else {
            $data ['__returncode'] = "noneedtosave";
        }

        // ****************************************************
        // PROCESSING (part 2 - afterSave + executeJS)
        // ****************************************************
        if (is_object($tmpSheetProcessor) && $tmpSheetProcessor instanceof FR_RSheetProcessor) {

            if ($doSaveForm) {
                try {
                    $tmpSheetProcessor->afterSave($tmpNewData, $tmpOldData, (bool)$recalc);
                } catch (Exception $e) {
                    $data ['__returncode'] = $e->getMessage();
                    return Rjson::arrayToJSON($data);
                }
            }
            if ($recalc == 0) {
                $data['__executeJS'] = "";
                $tmpJS = trim($tmpSheetProcessor->executeJSAfterUpdate($tmpNewData, $tmpOldData));
                if ($tmpJS != "") {
                    $data['__executeJS'] = base64_encode($tmpJS);
                }
            }
        }
        return Rjson::arrayToJSON($data);
    }

    private static function sortArray($array)
    {
        $retur = null;
        $keys = null;
        if (is_array($array)) {
            foreach (array_keys($array) as $key) {
                $keys [] = $key;
            }
            if (is_array($keys)) {
                sort($keys);

                foreach ($keys as $key) {
                    $retur [$key] = $array [$key];
                }
            }
        }

        return $retur;
    }

}
