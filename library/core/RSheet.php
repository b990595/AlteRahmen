<?php

/**
 *
 * @author T180469
 * @version 1.0.2
 *
 */
abstract class FR_RSheet extends FR_RSheet_Fields
{

    public function initForm($data = array(), $action = "self", $method = "post", $target = "_self", $enctype = "application/x-www-form-urlencoded")
    {
        $this->noSaveOnlyForm = true;
        $this->noSaveOnlyFormData = $data;
        $this->noSaveOnlyFormSubmitData = array("action" => $action, "method" => $method, "target" => $target, "enctype" => $enctype);
        $this->setCSRFHash("no_save");
        $this->sheetId = md5(system::unik());
        $this->resetAndInit();
    }

    public function initAutoSaveForm($key, $keyValue, $connectionString, $table, $tableExt = "")
    {
        $this->setCSRFHash(FR_RSheetCSRF::create($key, $keyValue, $connectionString, $table, $tableExt));
        $this->sheetId = "ID" . md5($this->getCSRFHash());
        $this->resetAndInit();
    }

    /**
     * Tegning af Sheet
     */
    abstract protected function initSheet();

    public function render()
    {
        echo "<script language='javascript'>";

        // **************************************************************
        // Indeksering af input, så man kan finde id udfra name.
        // **************************************************************
        echo "var nameToIdObject_" . $this->sheetId . " = " . $this->getNameToIdJSObject() . ";";
        // **************************************************************
        // **************************************************************
        // Ved "no need to save", skal der foretages numberformat første gang, men ikke de efterfølgende gange ..
        // **************************************************************
        echo "var firsttimeformat_" . $this->sheetId . " = false;";
        // **************************************************************
        // **************************************************************
        // Skjul og vis diverse ting på savebar
        // **************************************************************
        //echo "function updateIconOn_" . $this->sheetId . "(){";
        //echo "$(\"#savebar_" . $this->sheetId . "\").css(\"background-color\", \"#f9f9f9\");";
        //echo "$(\"#savebar_" . $this->sheetId . "\").html(\"<img src='" . FR_IMG_PATH . "/RIcons/edit-2.gif' width='8px' height='8px' align='absmiddle' />\");";
        //echo "}";
        //echo "function updateIconOff_" . $this->sheetId . "(){";
        //echo "if (stristr($(\"#savebar_" . $this->sheetId . "\").html(), \"edit-2.gif\")){";
        //echo "$(\"#savebar_" . $this->sheetId . "\").css(\"background-color\", \"#f9f9f9\");";
        //echo "$(\"#savebar_" . $this->sheetId . "\").html(\"&nbsp;\");";
        //echo "}";
        //echo "}";
        // **************************************************************
        // **************************************************************
        // Formatering af tal felter (input)
        // **************************************************************
        echo "function formatNumber_" . $this->sheetId . "(){";
        echo "$('input[tag=number][sheetId=" . $this->sheetId . "]').each(function(k,v){";
        echo "var tmpformat = $.parseJSON($(this).attr('meta'));";
        echo "$(this).autoNumeric(tmpformat);";
        echo "$(this).autoNumericSet($(this).val());";
        echo "});";
        echo "}";

        echo "function formatNumberById_" . $this->sheetId . "(id){";
        echo "var tmpformat = $.parseJSON($('#'+id).attr('meta'));";
        echo "$('#'+id).autoNumeric(tmpformat);";
        echo "$('#'+id).autoNumericSet($('#'+id).val());";
        echo "}";


        // **************************************************************
        // **************************************************************
        // Formatering af beregnede felter (span)
        // **************************************************************
        echo "function formatNumberCalc_" . $this->sheetId . "(){";
        // Opdaterer span indhold for alle calc-fields
        if (is_array($this->calcFields)) {
            foreach ($this->calcFields as $cf) {
                if ($cf ['type'] == "number") {
                    echo "var tmpSourceId = nameToIdObject_" . $this->sheetId . "['" . $cf ['name'] . "'];";
                    if ($cf ['showinthousands']) {
                        echo "$(\"#" . $cf ['spanid'] . "\").html(\"" . $cf ['prependText'] . "\" + number_format(
							$(\"#\"+tmpSourceId).val()
									 / 1000, " . $cf ['decimals'] . ", \",\", \".\"));";
                    } else {
                        echo "$(\"#" . $cf ['spanid'] . "\").html(\"" . $cf ['prependText'] . "\" + number_format(
							$(\"#\"+tmpSourceId).val()
									, " . $cf ['decimals'] . ", \",\", \".\"));";
                    }
                } else if ($cf['type'] == "text") {
                    echo "var tmpSourceId = nameToIdObject_" . $this->sheetId . "['" . $cf ['name'] . "'];";
                    echo "$(\"#" . $cf ['spanid'] . "\").html($(\"#\"+tmpSourceId).val());";

                } else if ($cf['type'] == "date") {
                    echo "$(\"#" . $cf ['displayid'] . "\").val(formatDate_" . $this->sheetId . "($(\"#" . $cf ['valueid'] . "\").val()));";

                }
            }
        }
        echo "}";

        echo "function formatDate_" . $this->sheetId, "(d){";
        echo "if(trim(d)==''){";
        echo "return '';";
        echo "}else{";
        echo "return d[8]+d[9]+'-'+d[5]+d[6]+'-'+d[0]+d[1]+d[2]+d[3];";
        echo "}";
        echo "}";


        // **************************************************************
        // **************************************************************
        // DOCUMENT READY ..
        // **************************************************************
        echo "$(document).ready(function(){";

        echo "formatNumber_" . $this->sheetId . "();";
        echo "recalcsheet_" . $this->sheetId . "('', false, true);";
        echo "setTimeout(function(){";
        echo "$('#__loadingbarimg_" . $this->sheetId . "').css('opacity',0.2);";
        echo "}, 500);";
        echo "setTimeout(function(){";
        echo "$('#__loadingbarimg_" . $this->sheetId . "').css('opacity',0.8);";
        echo "}, 2000);";
        echo "});";

        // **************************************************************
        // Genbregn sheet, kaldes ved update af et felt
        // **************************************************************
        echo "function recalcsheet_" . $this->sheetId . "(n, postback, calledOnLoad){";
        //echo "$(\"#savebar_" . $this->sheetId . "\").css(\"background-color\", \"#f8e7b3\");";
        //echo "$(\"#savebar_" . $this->sheetId . "\").html(\"<img src='" . FR_IMG_PATH . "/RIcons/busy.gif' width='8px' height='8px' align='absmiddle' />\");";
        //echo "$(\"#__reload_\"+n).hide();";
        //echo "$(\"#__saving_\"+n).show();";
        // {nameid} Benyttes til de simple felter ..

        $tmpBgColor = "#9ABA7F";
        if ($this->noSaveOnlyForm) {
            $tmpBgColor = "#eeeeee";
        }

        echo "var oldBg = $('#'+n).css('background-color');";
        echo "$('#'+n).css('background-color','" . $tmpBgColor . "');";
// __{nameid} Benyttes til felter, hvor der ligger et hiddenfield bag ..
        echo "var oldBg2 = $('#__'+n).css('background-color');";
        echo "$('#__'+n).css('background-color','" . $tmpBgColor . "');";

        echo "var msA = microtime(true);";

        echo "if (calledOnLoad == true){";
//echo "if (window.loadingRSheetData == null){";
//echo "window.loadingRSheetData = 1;";
//echo "window.loadingRSheetDataCompleted = 0;";
//echo "}else{";
//echo "window.loadingRSheetData = window.loadingRSheetData + 1;";
//echo "}";

        echo "callurl = \"" . FR_SYSTEM_CONTROLLERS . "/RSheet/calc?calledOnLoad=1\";";
        echo "}else{";
        echo "callurl = \"" . FR_SYSTEM_CONTROLLERS . "/RSheet/calc\";";
        echo "}";

        echo "$.post(callurl, $(\"#rsheetform_" . $this->sheetId . "\").serialize(), function(data){";
//echo "$(\"#__saving_\"+n).hide();";

        echo "var msB = microtime(true);";
        echo "var msC = 65 - ((msB - msA)*1000);";
        echo "if (msC < 1){msC = 1;}";

        echo "setTimeout(function(){";
        echo "$('#'+n).css('background-color',oldBg);";
        echo "$('#__'+n).css('background-color',oldBg2);";
        echo "}, msC);";


        echo "if (data == null || data.__returncode == null || data.__returncode != 'ok'){";

        echo "if(data.__returncode == 'noneedtosave'){";

        //echo "alert('no need');";
        //echo "$(\"#savebar_" . $this->sheetId . "\").css(\"background-color\", \"#f9f9f9\");";
        //echo "$(\"#savebar_" . $this->sheetId . "\").html(\"<img src='" . FR_IMG_PATH . "/RIcons/tick_16.png' width='8px' height='8px' align='absmiddle' />\");";

        echo "if (firsttimeformat_" . $this->sheetId . " == false){ formatNumberCalc_" . $this->sheetId . "(); firsttimeformat_" . $this->sheetId . " = true; }";

        echo "}else if(data.__returncode == 'timeout'){";

        echo "alert('Data blev ikke gemt pga. en timeout, prøv igen ..');";
        echo "window.localStorage.scrollTop = $(document).scrollTop();";
        echo "window.location.href = window.location.href;";

        echo "}else if(strstr(data.__returncode,'editlock:')){";

        echo "alert(data.__returncode);";

        echo "}else{";

        //echo "$(\"#__failure_\"+n).show();";
        //echo "$(\"#savebar_" . $this->sheetId . "\").css(\"background-color\", \"#c78f8f\");";
        //echo "$(\"#savebar_" . $this->sheetId . "\").html(\"<img src='" . FR_IMG_PATH . "/RIcons/warning_16.png' align='absmiddle' />&nbsp;Data er <b>ikke</b> gemt. (\" + data.__returncode +\")\");";

        echo "alert('FEJL: Data er ikke gemt.\\n\\n'+data.__returncode);";
        echo "}";

        echo "}else{";


        echo "for (var i in data) {";
// OLD: Bad performance
// echo "$(\"[name=\"+i+\"][sheetId=" . $this->sheetId . "]\").val(data[i]);";
// echo "$(\"[tag=__failure_\"+i+\"]\").hide();";
// NEW: Better performance
        echo "var tmpDataId = nameToIdObject_" . $this->sheetId . "[i];";


        // *************************************************************
        // Opdatering af tal - felter (inkl. formatNumber..)
        // *************************************************************
        echo "if($(\"#__\"+tmpDataId).length > 0){";
        echo "if ($(\"#\"+tmpDataId).val() != data[i]){";
        echo "$(\"#__\"+tmpDataId).val(data[i]);";
        echo "formatNumberById_" . $this->sheetId . "('__'+tmpDataId);";
        echo "}";
        echo "}";
        // *************************************************************

        echo "$(\"#\"+tmpDataId).val(data[i]);";


//echo "$(\"#__failure_\"+tmpDataId).hide();";

        echo "};";

// Gemmer det nye json som __oldjson (hiddenfield)
// OLD: Bad performance
// echo "$(\"input[name=__oldjson][sheetId=" . $this->sheetId . "]\").val(data.newjson);";
// NEW: Better performance

        echo "$(\"#__oldjson_" . $this->sheetId . "\").val(data.__newjson);";
        echo "formatNumberCalc_" . $this->sheetId . "();";


        echo "if(data.__executeJS!=''){eval(base64_decode(data.__executeJS));}";

        echo "if (postback==true){";
        echo "window.localStorage.scrollTop = $(document).scrollTop();";

//echo "$.post(\"" . FR_SYSTEM_CONTROLLERS . "/RSheet/save\", {\"oldjson_keyvalue\":data.oldjson_keyvalue, \"oldjson\":data.oldjson, \"oldjson_sql\":data.oldjson_sql, \"sqlparams\":data.sqlparams, \"sqlparamsDblEncoded\":data.sqlparamsDblEncoded, \"sql\":data.sql, \"sqlExt\":data.sqlExt, \"sqlparamsExt\":data.sqlparamsExt, \"__connectionString\":\"" . base64_encode($this->connectionString) . "\", \"__table\":\"" . $this->table . "\", \"pass\":\"12qwaszx\"}, function(savedata){";
        echo "window.location.href = window.location.href;";
//echo "if (savedata == \"ok\"){ window.location.href = window.location.href; }";
//echo "else{";
//echo "$(\"#savebar_" . $this->sheetId . "\").html(savedata);";
//echo "alert('FEJL: Data er ikke gemt.\\n\\n'+str_ireplace('<br>','\\n',savedata));";
//echo "}";
//echo "});";
        echo "}else{";


//echo "$.post(\"" . FR_SYSTEM_CONTROLLERS . "/RSheet/save\", {\"oldjson_keyvalue\":data.oldjson_keyvalue, \"oldjson\":data.oldjson, \"oldjson_sql\":data.oldjson_sql, \"sqlparams\":data.sqlparams, \"sqlparamsDblEncoded\":data.sqlparamsDblEncoded, \"sql\":data.sql, \"sqlExt\":data.sqlExt, \"sqlparamsExt\":data.sqlparamsExt, \"__connectionString\":\"" . base64_encode($this->connectionString) . "\", \"__table\":\"" . $this->table . "\", \"pass\":\"12qwaszx\"}, function(savedata){";
//echo "if (savedata == \"ok\"){";
//echo "$(\"#savebar_" . $this->sheetId . "\").css(\"background-color\", \"#f9f9f9\");";
//echo "$(\"#savebar_" . $this->sheetId . "\").html(\"<img src='" . FR_IMG_PATH . "/RIcons/tick_16.png' width='8px' height='8px' align='absmiddle' />\");";
//echo "}else{";
//echo "$(\"#savebar_" . $this->sheetId . "\").html(savedata);";
//echo "alert('FEJL: Data er ikke gemt.\\n\\n'+str_ireplace('<br>','\\n',savedata));";
//echo "}";
//echo "});";
        echo "}";

        echo "}";

// Loading bar skjules og Sheet-content vises ved første load ..
        echo "$('#__loadingbar_" . $this->sheetId . "').hide();";
        echo "$('#__sheetcontent_" . $this->sheetId . "').show();";


// END POST TO callurl
        echo "},'json');";


//echo "if (calledOnLoad == true){";
//echo "setTimeout(function(){";
//echo "window.loadingRSheetDataCompleted = window.loadingRSheetDataCompleted + 1;";
//echo "if (window.loadingRSheetData == window.loadingRSheetDataCompleted){";
// Fjerner evt. blockUI (som er en man selv kan sætte på sin main page..
// Eksempel:
// <script language='javascript'>
// $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
// </script>
//echo "try{ $.unblockUI(); $(document).ajaxStart(''); }catch(e){}";
//echo "}";
//echo "}, 1000);";
//echo "}";


        echo "}";
// **************************************************************


        echo "</script>";

        echo "<table style='border: 0px solid #eeeeee;' cellpadding='0' cellspacing='0'>";
//echo "<tr><td id='savebar_" . $this->sheetId . "' style='background-color: #f9f9f9; padding-right: 2px; border-bottom: 1px solid #eeeeee; cursor:pointer; text-align:right; font-size: 8px;' "
//. "onclick='javascript:recalcsheet_" . $this->sheetId . "(\"dummy\", false, false);'>";
//echo "<img src='" . FR_IMG_PATH . "/RIcons/tick_16.png' width='8px' height='8px' align='absmiddle' />";
//echo "</td></tr>";
        echo "<tr><td id='__loadingbar_" . $this->sheetId . "'>";

        echo "<img src='" . FR_IMG_PATH . "/loadingAnimationSmall.gif' id='__loadingbarimg_" . $this->sheetId . "' style='opacity: 0;' />";

        echo "</td></tr><tr><td id='__sheetcontent_" . $this->sheetId . "' style='display: none;'>";

        if ($this->noSaveOnlyForm) {
            RForm::start($this->noSaveOnlyFormSubmitData['action'], "rsheetform_" . $this->sheetId, $this->noSaveOnlyFormSubmitData['target'], $this->noSaveOnlyFormSubmitData['method'], $this->noSaveOnlyFormSubmitData['enctype']);
        } else {
            RForm::start("self", "rsheetform_" . $this->sheetId);
        }

        echo "<input type='hidden' name='__sheetProcessorString' value='" . trim($this->sheetProcessorString) . "' />";

        if (!$this->noSaveOnlyForm) {
            $tmpstore = base64_encode(Rjson::arrayToJSON($this->storeFields));
            echo "<input type='hidden' name='__storefields' value='" . $tmpstore . "' />";

            $tmpstoreExt = base64_encode(Rjson::arrayToJSON($this->storeFieldsExt));
            echo "<input type='hidden' name='__storefieldsExt' value='" . $tmpstoreExt . "' />";

            $tmplogtime = base64_encode(Rjson::arrayToJSON($this->logtimeFields));
            echo "<input type='hidden' name='__logtimefields' value='" . $tmplogtime . "' />";
        }

        echo "<input type='hidden' id='__oldjson_" . $this->sheetId . "' name='__oldjson' sheetId='" . $this->sheetId . "' value='" . base64_encode($this->json) . "' />";

        if (!$this->noSaveOnlyForm) {
            echo "<input type='hidden' id='__updated_" . $this->sheetId . "' name='__updated' sheetId='" . $this->sheetId . "' value='" . $this->dataOrEmpty('__updated') . "' />";
            echo "<input type='hidden' id='__updated_by_" . $this->sheetId . "' name='__updated_by' sheetId='" . $this->sheetId . "' value='" . $this->dataOrEmpty('__updated_by') . "' />";
            echo "<input type='hidden' id='__created_" . $this->sheetId . "' name='__created' sheetId='" . $this->sheetId . "' value='" . $this->dataOrEmpty('__created') . "' />";
            echo "<input type='hidden' id='__created_by_" . $this->sheetId . "' name='__created_by' sheetId='" . $this->sheetId . "' value='" . $this->dataOrEmpty('__created_by') . "' />";
        }
        echo "<input type='hidden' name='__hash' value='" . $this->getCSRFHash() . "' />";

        echo $this->html;
        echo "</td></tr>";
        echo "</table>";


        RForm::end();
    }

    private
    function dataOrEmpty($key)
    {
        return isset($this->data[$key]) ? trim($this->data[$key]) : "";
    }

    private function recalcHttpPost(array $dataArray)
    {
        $tmpSession = Session::GetOrDie("userdata");

        $url = "http://" . $_SERVER['SERVER_NAME'] . FR_SYSTEM_CONTROLLERS . "/RSheet/calc?recalc=1&__userTicket=" . base64_encode($tmpSession["userid"] . ":" . $tmpSession["password"]);
        if ($_SERVER['SERVER_PORT'] == 443) {
            $url = str_replace("http://", "https://", $url);
        }

        $fields_string = "";

        // Danner dataArray om til URL-POST
        foreach (array_keys($dataArray) as $key) {

            if (is_array($dataArray [$key])) {
                foreach ($dataArray [$key] as $d) {
                    $fields_string .= $key . '[]=' . urlencode((string)$d) . '&';
                }
            } else {
                $fields_string .= $key . '=' . urlencode((string)$dataArray [$key]) . '&';
            }
        }

        $fields_string = rtrim($fields_string, '&');

        $csrf = null;

        if (isset($dataArray['__hash'])) {
            $csrf = FR_RSheetCSRF::get($dataArray['__hash']);
        }
        if (!is_array($csrf)) {
            throw new Exception("__hash not found in data (csrf)");
        }


        $fields_string .= "&__hashjson64=" . base64_encode(json_encode($csrf));
        //$fields_string  = "__hashjson64=" . base64_encode(json_encode($csrf))."&".$fields_string;

        // Curl
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    public
    function recalcSheet()
    {
        $tmpstore = base64_encode(Rjson::arrayToJSON($this->storeFields));
        $tmpstoreExt = base64_encode(Rjson::arrayToJSON($this->storeFieldsExt));
        $tmplogtime = base64_encode(Rjson::arrayToJSON($this->logtimeFields));

        $fields = array(
            "__sheetProcessorString" => trim($this->sheetProcessorString),
            "__storefields" => $tmpstore,
            "__storefieldsExt" => $tmpstoreExt,
            "__logtimefields" => $tmplogtime,
            "__oldjson" => base64_encode($this->json),
            "__hash" => $this->getCSRFHash()
        );

        if (is_array($this->data)) {

            foreach (array_keys($this->data) as $ak) {

                if (is_array($this->data [$ak])) {
                    foreach (array_keys($this->data [$ak]) as $akc) {
                        $fields [$ak] [$akc] = $this->data [$ak] [$akc];
                    }
                } else {
                    $fields [$ak] = $this->data [$ak];
                }
            }
        }

        $response = $this->recalcHttpPost($fields);
        // OLD: $response = system::httppost("http://" . $_SERVER ['SERVER_NAME'] . FR_SYSTEM_CONTROLLERS . "/RSheet/calc?recalc=1", $fields);



        $array = Rjson::JSONToArray($response);

        $returnStr = "";
        if (is_array($array) && isset($array ['__returncode'])) {
            if ($array ['__returncode'] == "noneedtosave") {
                $returnStr = "ok - noneedtosave";
            } else {
                $returnStr = $array ['__returncode'];
            }
        } else {
            $returnStr = "fejl";
        }
        if (mb_substr($returnStr, 0, 2) == "ok") {
            return true;
        }
        throw new Exception("Could not recalc sheet: " . $response . $array ['__returncode']);
    }

}
