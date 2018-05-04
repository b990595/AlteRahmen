<?php

/**
 * @author T180469
 * Felter, der HAR en readonly-version, der kan benyttes i edit-mode.
 */
abstract class FR_RSheet_DualFields extends FR_RSheet_Core
{

// ********************************************************************
// TIME
// ********************************************************************
    protected function addTime($name, $start = "08:00", $end = "19:30", $interval = 15, $style = "", $postbackOnChange = false)
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");


        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        $this->addEdit("<select class='form-control input-sm' validateId='$name' sheetId='" . $this->sheetId . "' id='$nameid' name='$name' size='1'
				style='width: 90px; $style'
				onchange='javascript:recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);'>");

// ***********************************************
// Options
// ***********************************************
        $startTs = mktime(mb_substr($start, 0, 2), mb_substr($start, 3, 2), 0, 1, 1, 1970);
        $endTs = mktime(mb_substr($end, 0, 2), mb_substr($end, 3, 2), 0, 1, 1, 1970);
        $intervalSec = $interval * 60;
        $this->addEdit("<option value=''>Vælg</option>");
        if ($endTs >= $startTs) {
            for ($x = $startTs; $x <= $endTs; $x = $x + $intervalSec) {
                $tmpVal = date("H:i", $x);
                $this->addEdit("<option ");
                if ($tmpVal == $value) {
                    $this->addEdit("selected");
                }
                $this->addEdit(" value='" . $tmpVal . "'>" . $tmpVal . "</option>");
            }
        }
// ***********************************************

        $this->addEdit("</select>");

        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");

// Readonly-version Storage
        $this->addReadonly("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $value . "</span>");
    }

    protected function addTime_AsReadOnly($name)
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $this->addEdit("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value=\"" . $value . "\" />");

        $this->add("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $value . "</span>");
    }

// ********************************************************************
// SELECT
// ********************************************************************
    protected function addSelectBox($name, $contentArray, $style = "", $postbackOnChange = false, $enableUserInput = false, $userInputButtonText = "Tilføj en værdi til listen", $userInputPromptText = "Angiv den værdi du vil tilføje.")
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");


        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        $this->addEdit("<select class='form-control input-sm' onMouseWheel='return false;' validateId='$name' sheetId='" . $this->sheetId . "' id='$nameid' name='$name' size='1'
				style='$style'
				onchange='javascript:recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);'>");

// ***********************************************
// Options
// ***********************************************
        if (is_array($contentArray)) {

            if ($value != "") {
                $tmp = arraysql::get_first_strict($contentArray, "value", $value);
                if (!is_array($tmp)) {
                    $contentArray [] = array(
                        "value" => $value
                    );
                }
            }

            foreach ($contentArray as $c) {

                if (is_array($c)) {
                    $subvalue = isset($c ['value']) ? $c ['value'] : "";
                    $text = isset($c ['text']) ? $c ['text'] : $subvalue;
                }

                $this->addEdit("<option value=\"$subvalue\"");
                if ($subvalue == $value) {
                    $this->addEdit(" selected");
                }
                $this->addEdit(">$text</option>");
            }
        }
// ***********************************************

        $this->addEdit("</select>");

// **********************************************
// USER INPUT
// **********************************************
        if ($enableUserInput) {
            $this->addEdit("<script language='javascript'>");
            $this->addEdit("function __select_" . $nameid . "_userinput(){");
            $this->addEdit("var tekst = prompt('" . $userInputPromptText . "','');");
            $this->addEdit("if (tekst!='' && tekst!=null){");
            $this->addEdit("$('select#" . $nameid . "').html($('select#" . $nameid . "').html() + '<option selected value=\"'+tekst+'\">'+tekst+'</option>');");

            $this->addEdit("recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);");

            $this->addEdit("}");
            $this->addEdit("}");
            $this->addEdit("</script>");

            $this->addEdit("<button type='button' class='btn btn-default btn-xs' style='margin-top: 3px;' onclick='javascript:__select_" . $nameid . "_userinput();' style='cursor: pointer;'>");
            $this->addEdit("<img src='" . FR_IMG_PATH . "/RIcons/add.gif' border='0' align='absmiddle' />&nbsp;" . $userInputButtonText);
            $this->addEdit("</button>");
        }
// **********************************************

        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");

        // Readonly-version Storage
        $tmpValue = $value;
        if (is_array($contentArray) && trim($value) != "") {
            $tmpText = arraysql::get_first_strict($contentArray, "value", $value);
            if (is_array($tmpText) && isset($tmpText['text']) && trim($tmpText['text']) != "") {
                $tmpValue = $tmpText['text'];
            }
        }
        $this->addReadonly("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $tmpValue . "</span>");
    }

    protected function addSelectBox_AsReadOnly($name)
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");
        $this->addEdit("<input sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value=\"" . $value . "\" />");

        $this->add("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $value . "</span>");
    }

// ********************************************************************
// RADIOBUTTONS
// ********************************************************************
    protected function addRadioButtons($name, $contentArray, $vertical = true, $postbackOnChange = false)
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");


        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        $delimitHtml = "&nbsp;&nbsp;&nbsp;";
        if ($vertical) {
            $delimitHtml = "<br>";
        }

// Maxlength = $size, da man eller ikke kan se alt i readonly-version
        $this->addEdit("<input validateId='$name' sheetId='" . $this->sheetId . "'
				type='hidden' name='$name' id='$nameid' value='" . $value . "'
			/>");

        if (is_array($contentArray)) {
            foreach ($contentArray as $v) {
                $tmpval = htmlspecialchars($v['value'], ENT_QUOTES | ENT_HTML401, "UTF-8");
                $tmptxt = trim($v['text']) != "" ? trim($v['text']) : $v['value'];

                $this->addEdit("<input class='' type='radio' name='_radioselect_$nameid' value='$tmpval'");
                if ($tmpval == $value) {
                    $this->addEdit(" checked ");
                }
                $this->addEdit(" onclick='javascript:$(\"#" . $nameid . "\").val(\"" . $tmpval . "\"); recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);' ");
                $this->addEdit(">&nbsp;$tmptxt" . $delimitHtml);
            }
        }

        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");

// Readonly-version Storage
        $this->addReadonly("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $value . "</span>");
    }

    protected function addRadioButtons_AsReadOnly($name)
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");
        $this->addEdit("<input sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value=\"" . $value . "\" />");

        $this->add("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $value . "</span>");
    }

// ********************************************************************
// DATE
// ********************************************************************
    protected function addDate($name, $style = "", $postbackOnChange = false)
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $this->calcFields [] = array(
            "valueid" => $nameid,
            "displayid" => "__" . $nameid,
            "type" => "date"
        );


        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");


        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        $this->addEdit("<input class='form-control input-sm' validateId='$name' sheetId='" . $this->sheetId . "'
				type='hidden' name='$name' id='$nameid' value='" . $value . "'
				/>");

        $displayValue = "";
        if (trim($value) != "") {
            $displayValue = format::SQLDateToDK($value);
        }

        $this->addEdit("<input class='form-control input-sm' type='text' id='__" . $nameid . "' value='" . $displayValue . "' size='10'
				onchange='javascript:recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);'
				style='width: 105px; text-align:center; $style'
				readonly />");

        //$this->addEdit("<img src='" . FR_IMG_PATH . "/RIcons/cal.gif' align='absmiddle' style='cursor:pointer; margin-left: 2px;' onclick=\"javascript:$('#" . $nameid . "_display').datepicker('show');\" />");

        $this->addEdit('<script language="javascript">
							$("#__' . $nameid . '").datepicker({
									monthNames:["Januar", "Februar", "Marts", "April", "Maj", "Juni", "Juli", "August", "September", "Oktober", "November", "December"],
					dayNamesMin:["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
					firstDay: 1,
								showWeek: true,
					weekHeader: "",
					dateFormat: "dd-mm-yy",
					altFormat: "yy-mm-dd",
					yearRange: "1870:2038",
					changeYear: true,
					changeMonth: true,
					altField: $("#' . $nameid . '")
				});
				</script>');

        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");

// Readonly-version (storage)
        $this->addReadonly("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $displayValue . "</span>");
    }

    protected function addDate_AsReadOnly($name)
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $displayValue = "";
        if (trim($value) != "") {
            $displayValue = format::SQLDateToDK($value);
        }

        $this->addEdit("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value=\"" . $value . "\" />");

        $this->add("<span name='" . str_replace("[]", "", $name) . "' sheetId='" . $this->sheetId . "'>" . $displayValue . "</span>");
    }

// ********************************************************************
// OnOff
// ********************************************************************
    protected function addOnOff($name, $text, $style = "", $valueWhenSelected = "1", $postbackOnChange = false)
    {
        $selectedValue = htmlspecialchars($valueWhenSelected, ENT_QUOTES | ENT_HTML401, "UTF-8");
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");


        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        $this->addEdit("<input validateId='$name' sheetId='" . $this->sheetId . "' 
				type='hidden' name='$name' id='$nameid' value='" . $value . "'
			/>");


        $this->addEdit("<button type='button' class='btn btn-default btn-xs' style='$style'
			onclick='javascript:
	
			if ($(\"#" . $nameid . "\").val()==\"" . $selectedValue . "\"){
			$(\"#" . $nameid . "\").val(\"\");
		}else{
					$(\"#" . $nameid . "\").val(\"" . $selectedValue . "\");
		}
	
			$(\"[tag=checklistImgSelected" . $nameid . "_" . md5($selectedValue) . "]\").toggle();
					recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);'
	
				onmouseover='javascript:this.style.backgroundColor = \"#eeeeee\";'
				onmouseout='javascript:this.style.backgroundColor = \"#f9f9f9\";'>");
        $this->addReadonly("<button type='button' class='btn btn-default' style='$style'>");

        if ($value == $selectedValue) {
            $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($selectedValue) . "' src='" . FR_IMG_PATH . "/RIcons/tick_16.png' align='absmiddle' />");
            $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($selectedValue) . "' style='display:none;' src='" . FR_IMG_PATH . "/RIcons/delete2.gif' align='absmiddle' />");
        } else {
            $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($selectedValue) . "' style='display:none;' src='" . FR_IMG_PATH . "/RIcons/tick_16.png' align='absmiddle' />");
            $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($selectedValue) . "' src='" . FR_IMG_PATH . "/RIcons/delete2.gif' align='absmiddle' />");
        }

        if ($text != "") {
            $this->add("&nbsp;");
        }
        $this->add($text);

        $this->add("</button>");


        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");
    }

    protected function addOnOff_AsReadOnly($name, $text, $style = "", $valueWhenSelected = "1")
    {
        $selectedValue = htmlspecialchars($valueWhenSelected, ENT_QUOTES | ENT_HTML401, "UTF-8");
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");


        $this->addEdit("<input sheetId='" . $this->sheetId . "' 
				type='hidden' name='$name' id='$nameid' value='" . $value . "'
			/>");


        $this->add("<button type='button' class='btn btn-default btn-xs' style='$style'>");

        if ($value == $selectedValue) {
            $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($selectedValue) . "' src='" . FR_IMG_PATH . "/RIcons/tick_16.png' align='absmiddle' />");
        } else {
            $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($selectedValue) . "' src='" . FR_IMG_PATH . "/RIcons/delete2.gif' align='absmiddle' />");
        }

        if ($text != "") {
            $this->add("&nbsp;");
        }
        $this->add($text);

        $this->add("</button>");
    }

// ********************************************************************
// Checklist
// ********************************************************************
    protected function addChecklist($name, $contentArray, $columns = 1, $style = "", $postbackOnChange = false)
    {
        $value = "";
        $nameid = $this->newid($name);


        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        $this->addEdit("<input validateId='$name' sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value='" . $value . "' />");

        if (is_array($contentArray)) {
            $x = 0;
            $this->add("<div style='width: 100%;'><table class='table-condensed' style='$style'><tr>");
            foreach ($contentArray as $c) {
                $c['value'] = htmlspecialchars($c['value'], ENT_QUOTES | ENT_HTML401, "UTF-8");
                $c['text'] = htmlspecialchars($c['text'] ?? $c['value'], ENT_QUOTES | ENT_HTML401, "UTF-8");

                if ($x == $columns) {
                    $this->add("</tr><tr>");
                    $x = 0;
                }

                $this->addEdit("<td><button type='button' class='btn btn-default btn-xs' style='width: 100%; $style'
											onclick='javascript:
											if (stristr($(\"#" . $nameid . "\").val(), \"[" . $c ['value'] . "]\")){
									tmpNewValue = str_ireplace(\"[" . $c ['value'] . "]\", \"\", $(\"#" . $nameid . "\").val());
							$(\"#" . $nameid . "\").val(tmpNewValue);
									}else{
							$(\"#" . $nameid . "\").val($(\"#" . $nameid . "\").val()+\"[" . $c ['value'] . "]\");
						}
						$(\"[tag=checklistImgSelected" . $nameid . "_" . md5($c ['value']) . "]\").toggle();
						recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);'>");

                $this->addReadonly("<td><button type='button' class='btn btn-default btn-xs' style='width: 100%; $style'>");

                if (stristr($value, "[" . $c ['value'] . "]")) {
                    $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($c ['value']) . "' src='" . FR_IMG_PATH . "/RIcons/tick_16.png' align='absmiddle' />");
                    $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($c ['value']) . "' style='display:none;' src='" . FR_IMG_PATH . "/RIcons/delete2.gif' align='absmiddle' />");
                } else {
                    $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($c ['value']) . "' style='display:none;' src='" . FR_IMG_PATH . "/RIcons/tick_16.png' align='absmiddle' />");
                    $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($c ['value']) . "' src='" . FR_IMG_PATH . "/RIcons/delete2.gif' align='absmiddle' />");
                }

                $this->add("&nbsp;");
                if (isset($c ['text'])) {
                    $this->add($c ['text']);
                } else {
                    $this->add($c ['value']);
                }

                $this->add("</button></td>");
                $x++;
            }
            $colsMissing = $columns - $x;
            if ($colsMissing > 0) {
                for ($t = 0; $t < $colsMissing; $t++) {
                    $this->add("<td>&nbsp;</td>");
                }
            }

            $this->add("</tr></table></div>");
        }

        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");
    }

    protected function addChecklist_AsReadOnly($name, $contentArray, $columns = 1, $style = "")
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $this->addEdit("<input sheetId='" . $this->sheetId . "'
				type='hidden' name='$name' id='$nameid' value='" . $value . "'
										/>");

        if (is_array($contentArray)) {
            $x = 0;
            $this->add("<div style='width: 100%;'><table width='100%' cellspacing='1' cellpadding='3' bgcolor='#cccccc' style='$style'><tr bgcolor='#f9f9f9'>");
            foreach ($contentArray as $c) {
                $c['value'] = htmlspecialchars($c['value'], ENT_QUOTES | ENT_HTML401, "UTF-8");
                $c['text'] = htmlspecialchars($c['text'], ENT_QUOTES | ENT_HTML401, "UTF-8");

                if ($x == $columns) {
                    $this->add("</tr><tr bgcolor='#f9f9f9'>");
                    $x = 0;
                }

                $this->add("<td>");

                if (stristr($value, "[" . $c ['value'] . "]")) {
                    $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($c ['value']) . "' src='" . FR_IMG_PATH . "/RIcons/tick_16.png' align='absmiddle' />");
                } else {
                    $this->add("<img tag='checklistImgSelected" . $nameid . "_" . md5($c ['value']) . "' src='" . FR_IMG_PATH . "/RIcons/delete2.gif' align='absmiddle' />");
                }

                $this->add("&nbsp;");
                if (isset($c ['text'])) {
                    $this->add($c ['text']);
                } else {
                    $this->add($c ['value']);
                }
                $this->add("</td>");
                $x++;
            }
            $colsMissing = $columns - $x;
            if ($colsMissing > 0) {
                for ($t = 0; $t < $colsMissing; $t++) {
                    $this->add("<td>&nbsp;</td>");
                }
            }

            $this->add("</tr></table></div>");
        }
    }


// ********************************************************************
// Number
// ********************************************************************
    /**
     * @param $name
     * @param int $decimals
     * @param string $prependText
     * @param string $style
     * @param bool $postbackOnChange
     * @param int $min
     * @param int $max
     * @param string $aSep (tusindtal-sep)
     * @param string $aDec (komma-symbol)
     *
     */
    protected function addNumber($name, $decimals = 2, $prependText = "", $style = "", $postbackOnChange = false, $min = -99999999999, $max = 99999999999, $aSep = ".", $aDec = ",")
    {
        $value = 0;

        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] / 1 : 0;
        }

// Max og Min
        if ($value > $max) {
            $value = $max;
        } else if ($value < $min) {
            $value = $min;
        }

        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        $this->addEdit("<input class='form-control input-sm' validateId='$name' sheetId='" . $this->sheetId . "' name='$name' id='$nameid' type='hidden' value='$value' />");
        $this->addEdit("<input class='form-control input-sm' sheetId='" . $this->sheetId . "'
				meta='{\"aSep\":\"$aSep\", \"aDec\":\"$aDec\", \"mDec\":$decimals, \"vMin\":$min, \"vMax\":$max, \"aSign\":\"$prependText\"}'
				type='text' tag='number' name='__$nameid' id='__$nameid' value='" . $value . "'
				style='text-align: right; $style'
				onchange='javascript:
				$(\"#$nameid\").val($(this).autoNumericGet());
				recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);'
						onkeyup='javascript:if(event.keyCode == 13){this.blur();}'
						onclick='javascript:this.focus(); this.select();'
	
				/>");
        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");

// Readonly-version (storage)
        $value = $prependText . number_format($value, $decimals, $aDec, $aSep);
        $this->addReadonly("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' name='$name' id='$nameid' type='text' value='$value' style='border: 1px solid #666666; text-align: right; $style' disabled />");

        return $nameid;

    }

    protected function addNumber_AsReadOnly($name, $decimals = 2, $prependText = "", $style = "")
    {
        $value = 0;
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] / 1 : 0;
        }

        $this->addEdit("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' name='$name' id='$nameid' type='hidden' value='$value' />");

        $value = $prependText . number_format($value, $decimals, ",", ".");
        $this->add("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' type='text' value='$value' style='text-align: right; $style' disabled />");
    }

// ********************************************************************
// Text
// ********************************************************************
    protected function addText($name, $size = 20, $style = "", $postbackOnChange = false, $disableInput = false)
    {

        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

        if ($disableInput) {
            $this->addEdit("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' name='$name' id='$nameid' type='text' size='$size' value='$value' style='border: 1px solid #cccccc; $style' disabled />");
        } else {
            $this->addEdit("<input class='form-control input-sm' validateId='$name' sheetId='" . $this->sheetId . "' size='$size' maxlength='$size'
				type='text' name='$name' id='$nameid' value='" . $value . "'
				style='border: 1px solid #cccccc; $style'
				onchange='javascript:recalcsheet_" . $this->sheetId . "(\"$nameid\", $postback, false);'
						onkeyup='javascript:if(event.keyCode == 13){this.blur();}'
						onclick='javascript:this.focus(); this.select();'
								/>");
            $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");
        }
        // Readonly-version (storage)
        $this->addReadonly("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' name='$name' id='$nameid' type='text' size='$size' value='$value' style='border: 1px solid #cccccc; $style' disabled />");
        return $nameid;
    }

    protected function addText_AsReadOnly($name, $size = 20, $style = "")
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $this->addEdit("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value='" . $value . "' />");

        $this->add("<input class='form-control input-sm' sheetId='" . $this->sheetId . "' type='text' size='$size' value='$value' style='border: 1px solid #cccccc; $style' disabled />");
    }

// ********************************************************************
// TextArea
// ********************************************************************
    protected function addTextarea($name, $style = "", $postbackOnChange = false, $noBordersInReadonlyVersion = false)
    {

        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $postback = "false";
        if ($postbackOnChange) {
            $postback = "true";
        }

// Maxlength = $size, da man eller ikke kan se alt i readonly-version
// MD5 value er nødvendig, da autogrow gør, at der fyres 2 onchange events af ..
        // recalc function er nødvendig både på onchange og onfocusout, eller så virker det ikke i alle situationer
        $this->addEdit("<textarea class='form-control input-sm' validateId='$name' sheetId='" . $this->sheetId . "' name='$name' id='$nameid'
				style='$style'
				value_md5='" . md5($value) . "'
				onchange='javascript:
				if (md5(this.value) != $(this).attr(\"value_md5\")){
					$(this).attr(\"value_md5\", md5(this.value));
					recalcsheet_" . $this->sheetId . "(\"$nameid\", " . $postback . ", false);
				}'
				
				
                                                
                                                onfocusout='javascript:if (md5(this.value) != $(this).attr(\"value_md5\")){
					$(this).attr(\"value_md5\", md5(this.value));
					recalcsheet_" . $this->sheetId . "(\"$nameid\", " . $postback . ", false);
				}'
								>$value</textarea>");
        // setTimeout sikrer at autogrow virker, eller returneres forkert width() #!"#"#¤"!¤#!!!
        $this->addEdit("<script language='javascript'>$(document).ready(function(){"
            . "setTimeout(function(){"
            . "\$(\"#" . $nameid . "\").autogrow();"
            . "},300);"
            . "});</script>");
        $this->addEdit("<span id='__validateSpan_$nameid' validateMessageId='$name' sheetId='" . $this->sheetId . "' style='color: red;'></span>");

// Readonly-version (storage)
// Fjerner evt. height-param i css.
        if (stristr($style, "height:")) {
            $tmppos = mb_stripos($style, "height:");
            $tmpsub1 = mb_substr($style, 0, $tmppos);
            $tmpsub2 = mb_substr($style, $tmppos);
            $tmppos2 = mb_strpos($tmpsub2, ";");
            if ($tmppos2 > 7) {
                $style = $tmpsub1 . mb_substr($tmpsub2, $tmppos2 + 1);
            } else {
                $style = $tmpsub1;
            }
        }
        if ($noBordersInReadonlyVersion) {
            $this->addReadonly("<div id='$nameid'>" . str_replace(PHP_EOL, "<br>", $value) . "</div>");
        } else {
            $this->addReadonly("<div id='$nameid' style='border: 1px solid #666666; padding: 3px; $style' disabled >" . str_replace(PHP_EOL, "<br>", $value) . "</div>");

        }
    }

    protected function addTextarea_AsReadOnly($name, $style = "")
    {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");
        $this->addEdit("<input sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value=\"" . $value . "\" />");

        $this->add("<div style='border: 1px solid #666666; padding: 3px; $style' disabled >" . str_replace("\n", "<br>", $value) . "</div>");
    }

}
