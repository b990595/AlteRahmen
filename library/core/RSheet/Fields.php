<?php

/**
 * @author T180469
 * Felter, der IKKE har en readonly-version, der kan benyttes i edit-mode.
 */
abstract class FR_RSheet_Fields extends FR_RSheet_DualFields {

    /**
     * BEREGNET TEKST-FELT
     * Ja, hidden field er inkluderet ..
     * @param string $name        	
     * @param string $style
     */
    protected function addReadonlyTextfield($name, $style = "") {
        $value = "";
        $nameid = $this->newid($name);

        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : "";
        }

        $this->calcFields [] = array(
            "name" => "$name",
            "spanid" => "_" . $name . "_" . $this->sheetId,
            "type" => "text"
        );

        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $this->addEdit("<input sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value=\"" . $value . "\" />");

        $this->addEdit("<span style='$style' id='_" . $name . "_" . $this->sheetId . "'>" . $value . "</span>");
        $this->addReadonly("<span style='$style'>" . $value . "</span>");

    }

    /**'
     * BEREGNET TAL-FELT
     * Ja, hidden field er inkluderet ..
     * @param string $name
     * @param int $decimals
     * @param string $prependText
     * @param bool $showinthousands
     */
    protected function addReadonlyNumberfield($name, $decimals = 2, $style = "", $prependText = "", $showinthousands = false) {
        $nameid = $this->newid($name);
        $value = 0;
        if (is_array($this->data)) {
            $value = isset($this->data [$name]) ? $this->data [$name] : 0;
        }

        //$formel = str_replace ( ";", "", trim ( $formel ) );
        //$this->calculations [] = "{" . $name . "}=" . $formel . ";";
        $this->calcFields [] = array(
            "name" => "$name",
            "spanid" => "_" . $name . "_" . $this->sheetId,
            "type" => "number",
            "decimals" => $decimals,
            "prependText" => $prependText,
            "showinthousands" => $showinthousands
        );

        $this->addEdit("<input type='hidden' name='$name' id='$nameid' sheetId='" . $this->sheetId . "' value='$value' style='border: 1px solid #666666; text-align: right;' />");
        if ($showinthousands) {
            $this->addEdit("<span style='$style' id='_" . $name . "_" . $this->sheetId . "'>$prependText" . number_format(($value / 1000), $decimals, ",", ".") . "</span>");
        } else {
            $this->addEdit("<span style='$style' id='_" . $name . "_" . $this->sheetId . "'>$prependText" . number_format($value / 1, $decimals, ",", ".") . "</span>");
        }
        // Readonly-version (storage)
        if ($showinthousands) {
            $this->addReadonly("<span style='$style'>$prependText" . number_format(($value / 1000), $decimals, ",", ".") . "</span>");
        } else {
            $this->addReadonly("<span style='$style'>$prependText" . number_format($value / 1, $decimals, ",", ".") . "</span>");
        }
    }

    /**
     * 
     * @param string $name
     * @param var $value
     */
    protected function addHiddenField($name, $value = false) {
        $nameid = $this->newid($name);

        if ($value === false) {
            if (is_array($this->data)) {
                $value = isset($this->data [$name])?$this->data [$name]:"";
            }
        }
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML401, "UTF-8");

        $this->addEdit("<input sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value='$value' />");

        // Readonly-version (storage)
        $this->addReadonly("<input sheetId='" . $this->sheetId . "' type='hidden' name='$name' id='$nameid' value='$value' />");
    }

    protected function addSubmitButton($text = "Gem", $style = "", $class = false, $id = false){
        if ($this->noSaveOnlyForm){
            if ($class === false){
                $class = "btn btn-success";
            }
            if ($id){
                $id_tag = "id='".$id."'";
            }else{
                $id_tag = "";
            }
            $this->addEdit("<input ".$id_tag." sheetId='" . $this->sheetId . "' type='submit' value='$text' class='".$class."' style='".$style."' />");

        }else{
            throw new Exception("Submit-button is only for form with no autosave.");
        }
    }
    
}
