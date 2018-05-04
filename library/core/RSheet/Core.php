<?php

abstract class FR_RSheet_Core {

    protected $json = "";
    protected $data = null;
    protected $html = "";
    protected $htmlReadonly = "";
    private $csrf_hash = "";
    // Unikt ID for sheet
    protected $sheetId = "";
    //protected $bindedSelects = null;
    protected $modifiers = null;
    protected $storeFields = null;
    protected $calcFields = null;
    // Felter, der skal gemmes i en anden tabel.
    // Skal angives på formen array("<db-field>"=>"<sheet-field>", "<db-field>"=>"<sheet-field>");
    protected $storeFieldsExt = null;
    // Array. Gemmer datetime + tuser, hver gang feltet får en ny værdi som {feltnavn}_updated,{feltnavn}_updater,
    protected $logtimeFields = null;
    protected $newidcount = 0;
    protected $arrayFieldIndex = null;
    protected $nameToIdArray = null;
    protected $noSaveOnlyForm = false;
    protected $noSaveOnlyFormData = array();
    protected $noSaveOnlyFormSubmitData = array();
    

    /**
     * Class, der extends FR_RSheetProcessor som string eks. model_kaj_calc
     * @var string 
     */
    protected $sheetProcessorString = "";

    protected function setSheetProcessor($sheetProcessorString) {
        $this->sheetProcessorString = $sheetProcessorString;
    }
    
    public function setCSRFHash($hash) {
        $this->csrf_hash = $hash;
    }

    public function getCSRFHash() {
        return $this->csrf_hash;
    }

    protected function getNameToIdJSObject() {
        $retur = "{";
        if (is_array($this->nameToIdArray)) {
            foreach (array_keys($this->nameToIdArray) as $key) {
                if ($retur != "{") {
                    $retur.=",";
                }
                $retur.= $key . ":'" . $this->nameToIdArray[$key] . "'";
            }
        }
        $retur.= "}";
        return $retur;
    }

    protected function resetAndInit() {
        $this->json = "";
        $this->data = null;
        $this->html = "";
        $this->htmlReadonly = "";
        $this->modifiers = null;
        $this->storeFields = null;
        $this->logtimeFields = null;
        $this->newidcount = 0;
        $this->arrayFieldIndex = null;

        $this->loadData();
        $this->initSheet();
    }

    protected function loadData() {
        if (!$this->noSaveOnlyForm) {
            $ses = FR_RSheetCSRF::get($this->csrf_hash);
            if (is_array($ses) && isset($ses['table']) && isset($ses['key']) && isset($ses['keyValue']) && isset($ses['connectionString'])) {

                $db = new MySqlDB($ses['connectionString']);
                $data = $db->getFirstRow("`json`", $ses['table'], "`" . $ses['key'] . "`='" . $db->escapeString($ses['keyValue']) . "'");
                
                //$db->close(); // Fjernet, da den gav problemer med COMMIT i slutningen af scriptet ..
                
                if ($data->hasData()) {
                    $d = $data->getData();
                    $this->json = base64_decode($d['json']);
                    $this->data = Rjson::JSONToArray($this->json);
                }
            } else {
                throw new Exception("CSRF Error (loadData)");
            }
        } else {
            $this->json = json_encode($this->noSaveOnlyFormData);
            $this->data = $this->noSaveOnlyFormData;
        }
    }

    protected function newid($name) {
        $storeArray = true;
        if (strstr($name, "[]")) {
            $storeArray = false;
        }
        $this->newidcount ++;
        $name = str_replace("[", "", $name);
        $name = str_replace("]", "", $name);
        $retur = $name . "___" . $this->newidcount . "_" . $this->sheetId;

        if ($storeArray) {
            $this->nameToIdArray[$name] = $retur;
        }

        return $retur;
    }

    // SKAL SLETTES !!!! Efter hurtigt tjek
    protected function getNextArrayFieldIndex($field) {
        $field = str_replace("[]", "", $field);
        if (!isset($this->arrayFieldIndex [$field])) {
            $this->arrayFieldIndex [$field] = 0;
        }

        $retur = $this->arrayFieldIndex [$field];
        $this->arrayFieldIndex [$field] ++;
        return $retur;
    }

    protected function getDesign() {
        return $this->htmlReadonly;
    }

    protected function getDataArray($internalFields = false) {
        if ($internalFields) {
            $tmp = $this->data;
        } else {
            $tmp = null;
            if (is_array($this->data)) {
                foreach (array_keys($this->data) as $key) {
                    if (mb_substr($key, 0, 2) != "__") {
                        $tmp [$key] = $this->data [$key];
                    }
                }
            }
        }
        return $tmp;
    }

    protected function setDataArray($dataArray) {
        if (is_array($dataArray)) {
            foreach (array_keys($dataArray) as $key) {
                $this->data [$key] = $dataArray [$key];
            }
        }
        if (!isset($this->data['__updated'])) {
            $this->data['__updated'] = "";
        }
        if (!isset($this->data['__updated_by'])) {
            $this->data['__updated_by'] = "";
        }
        if (!isset($this->data['__created'])) {
            $this->data['__created'] = "";
        }
        if (!isset($this->data['__created_by'])) {
            $this->data['__created_by'] = "";
        }

        return true;
    }

    protected function getDataFromArrayFields($fieldsarray) {
        $retur = null;
        if (is_array($fieldsarray)) {

            if (is_array($this->data [$fieldsarray [0]])) {
                $rows = count($this->data [$fieldsarray [0]]);
                for ($x = 0; $x < $rows; $x ++) {
                    $tmp = null;
                    foreach ($fieldsarray as $f) {
                        $tmp [$f] = $this->data [$f] [$x];
                    }
                    $retur [] = $tmp;
                }
            }
        }
        return $retur;
    }

    protected function getCustomData($normalFieldsArray, $arrayFields1Array = null, $arrayFields1Keyname = "", $arrayFields2Array = null, $arrayFields2Keyname = "", $arrayFields3Array = null, $arrayFields3Keyname = "", $arrayFields4Array = null, $arrayFields4Keyname = "", $arrayFields5Array = null, $arrayFields5Keyname = "") {
        $retur = null;
        if (is_array($normalFieldsArray)) {
            foreach ($normalFieldsArray as $f) {
                $retur [$f] = $this->data [$f];
            }
        }

        for ($x = 1; $x <= 5; $x ++) {
            $tmpArrayVar = "arrayFields" . $x . "Array";
            $tmpKeynameVar = "arrayFields" . $x . "Keyname";
            if (is_array($$tmpArrayVar) && trim($$tmpKeynameVar) != "") {
                $retur [trim($$tmpKeynameVar)] = $this->getDataFromArrayFields($$tmpArrayVar);
            }
        }

        return $retur;
    }

    protected function getDataJSON() {
        $tmp = $this->getDataArray();
        return Rjson::arrayToJSON($tmp);
    }

    protected function add($html) {
        $this->html .= $html;
        $this->htmlReadonly .= $html;
    }

    protected function addEdit($html) {
        $this->html .= $html;
    }

    protected function addReadonly($html) {
        $this->htmlReadonly .= $html;
    }

}
