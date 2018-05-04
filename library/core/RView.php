<?php

abstract class FR_RView {

    // Settings
    private $db;
    private $table;
    private $connectionString;

    /**
     * Select
     *
     * @var string
     */
    private $select = "*";
    private $select2 = "";

    /**
     * Where
     *
     * @var string
     */
    private $where = "1";

    /**
     * Orderby
     *
     * @var string
     */
    private $orderby = "";
    private $groupby = "";

    /**
     * Antal pr.
     * side
     *
     * @var int
     */
    private $perpage = 25;

    /**
     * Vis antal
     *
     * @var bool (default=true)
     */
    private $showcount = true;
    private $showpaging = true;
    private $disableSorting = false;

    /**
     * Søgebare felter
     *
     * @var single array
     */
    private $searchFields = null;
    private $searchText = "";

    /**
     * Evaluerer tal som tekst, så at når man søger på eks.
     * et postnr., så kan man finde det i en streng, hvor det står midt i.
     *
     * @var bool
     */
    private $searchOnlyAsStrings = false;
    // Interne (huske) parametre
    private $lastorderby = "";
    private $indexstart = 0;
    private $where2 = "";
    private $keycol_selected = null;
    // Kolonner
    private $cols = null;
    private $keycols = null;
    private $sumcols = null;
    // Render
    private $hideHeadersIfNoData = false;
    private $hideHeaders = false;
    private $data = null;
    private $includeWhereInKeyCols = true;
    private $InlineEditRowDivEnabled = false;
    private $InlineEditRowDivKey = "";
    private $InlineEditRowDivPrefix = "";
    private $InlineEditRowDivArray = array();
    private $Modal = false;
    private $ModalId = "";
    private $ModalIframeId = "";
    private $ModalSpinnerId = "";

    /**
     * Konstruktør
     */
    public function __construct() {
        $this->lastorderby = HttpRequest::RequestOrBlank("__orderby");
        $this->indexstart = HttpRequest::RequestOrZero("__indexstart");

        $this->init();
    }

    abstract protected function init();

    protected function setWhere($where) {
        if (trim($where) != "") {
            $this->where = $where;
        } else {
            $this->where = "1";
        }
    }

    protected function setSelect($select) {
        if (trim($select) != "") {
            $this->select = $select;
        } else {
            $this->select = "*";
        }
    }

    protected function setOrderBy($orderby) {
        $this->orderby = $orderby;
    }

    protected function setGroupBy($groupby) {
        $this->groupby = $groupby;
    }

    protected function hideCount() {
        $this->showcount = false;
    }

    protected function hidePaging() {
        $this->showpaging = false;
    }

    protected function disableSorting() {
        $this->disableSorting = true;
    }

    protected function enableSearch($fields, $helpText = "", $searchOnlyAsStrings = false) {
        if (is_array($fields)) {
            $this->searchFields = $fields;
            $this->searchText = $helpText;
            $this->searchOnlyAsStrings = (bool) $searchOnlyAsStrings;
        }
    }

    protected function disableSearch() {
        $this->searchFields = null;
        $this->searchText = "";
        $this->searchOnlyAsStrings = false;
    }

    protected function hideHeadersIfNoData() {
        $this->hideHeadersIfNoData = true;
    }

    protected function hideHeaders() {
        $this->hideHeaders = true;
    }

    protected function setPerpage($perpage) {
        $this->perpage = $perpage;
    }

    protected function disableIncludeWhereInKeyCols() {
        $this->includeWhereInKeyCols = false;
    }

    protected function enableInlineEdit($keyCol) {
        $this->InlineEditRowDivKey = $keyCol;
        $this->InlineEditRowDivPrefix = md5(system::unik());
        $this->InlineEditRowDivEnabled = true;
    }

    public function getInlineEditDivs() {
        return $this->InlineEditRowDivArray;
    }

    private function searchStrip($s) {
        // Foranstillede nuller
        while (mb_substr($s, 0, 1) == "0") {
            $s = mb_substr($s, 1, mb_strlen($s) - 1);
        }

        // bindestreger
        $s = str_ireplace("-", "", $s);

        if (!is_numeric($s)) {
            $s = "%" . $s . "%";
        }

        return $s;
    }

    /**
     *
     * @param string $connectionString        	
     */
    protected function setConnection($connectionString, $table) {
        $this->connectionString = $connectionString;

        $connectionArray = Rjson::JSONToArray($connectionString);
        $this->table = $table;
        $this->db = isset($connectionArray ['db']) ? $connectionArray ['db'] : FR_DEFAULT_DB;
    }

    /**
     * Tilføjer key-column
     *
     * @param string $dbfield        	
     * @param string $header        	
     */
    protected function addKeyColumn($dbfield, $header, $showCount = false, $helptext = "", $defaultvalue = "", $reverseSort = false) {
        $tmp = null;
        $tmp ['dbfield'] = $dbfield;
        $tmp ['header'] = $header;
        $tmp ['showCount'] = $showCount;
        $tmp ['helptext'] = $helptext;
        $tmp ['defaultvalue'] = $defaultvalue;
        $tmp ['oneLine'] = true;
        $tmp ['reverseSort'] = $reverseSort;

        $this->keycols [] = $tmp;
    }

    /**
     * Tilføjer data-column
     *
     * @param string $dbfield        	
     * @param string $header        	
     * @param string $format        	
     * @param string $align        	
     * @param string $width        	
     */
    protected function addColumn($dbfield, $header, $format = "", $align = "left", $width = "", $tag = "") {
        $tmp = null;
        $tmp ['dbfield'] = $dbfield;
        $tmp ['header'] = $header;
        $tmp ['format'] = $format;
        $tmp ['align'] = $align;
        $tmp ['width'] = $width;
        $tmp ['link'] = "";
        $tmp ['image'] = "";
        $tmp ['linktarget'] = "";
        $tmp ['tag'] = $tag;

        $this->cols [] = $tmp;
    }

    protected function addCustomColumn($dbfield, $header, $html = "", $align = "left", $width = "", $tag = "") {
        $tmp = null;
        $tmp ['editType'] = "custom";
        $tmp ['dbfield'] = $dbfield;
        $tmp ['header'] = $header;
        $tmp ['html'] = $html;
        $tmp ['align'] = $align;
        $tmp ['width'] = $width;
        $tmp ['tag'] = $tag;

        $this->cols [] = $tmp;
    }

    protected function addLinkColumn($dbfield, $header, $link, $linkText = "", $align = "center", $width = "60px", $linktarget = '_self', $mustConfirmMsg = "") {
        $tmp = null;
        $tmp ['dbfield'] = $dbfield;
        $tmp ['header'] = $header;
        $tmp ['align'] = $align;
        $tmp ['width'] = $width;
        $tmp ['link'] = $link;
        $tmp ['linkText'] = $linkText;
        $tmp ['linktarget'] = $linktarget;
        $tmp ['mustConfirmMsg'] = $mustConfirmMsg;


        $this->cols [] = $tmp;
    }

    protected function addPopupLinkColumn($dbfield, $header, $link, $linkText = "", $align = "center", $width = "60px") {
        $tmp = null;
        $tmp ['dbfield'] = $dbfield;
        $tmp ['header'] = $header;
        $tmp ['align'] = $align;
        $tmp ['width'] = $width;
        $tmp ['link'] = $link;
        $tmp ['linkText'] = $linkText;
        $tmp ['linktarget'] = "__RViewModalPopup__";

        $this->Modal = true;
        $tmpUnik = sha1(system::unik());
        $this->ModalId = "__RViewModal_" . $tmpUnik;
        $this->ModalIframeId = "__RViewModalIframe_" . $tmpUnik;
        $this->ModalSpinnerId = "__RViewModalSpinner_" . $tmpUnik;
        $this->cols [] = $tmp;
    }

    /**
     * Tilføjer et summeringsfelt (vises ovenfor tabellen)
     *
     * @param string $dbfield        	
     * @param string $header        	
     * @param string $format        	
     */
    protected function addTotalSum($dbfield, $header, $format = "") {
        $tmp = null;
        $tmp ['dbfield'] = $dbfield;
        $tmp ['header'] = $header;
        $tmp ['format'] = $format;

        $this->sumcols [] = $tmp;
    }

    private function renderModal() {

        $html = "";
        $html.= "<div class = \"modal fade\" id = \"" . $this->ModalId . "\" tabindex = \"-1\" role = \"dialog\" aria-labelledby = \"myModalLabel\">";
        $html.= "<div class = \"modal-dialog\" role = \"document\">";
        $html.= "<div class = \"modal-content\">";
        $html.= "<div class = \"modal-header\">";
        $html.= "<button type = \"button\" class = \"close\" data-dismiss = \"modal\" aria-label = \"Close\"><span aria-hidden = \"true\">&times;";
        $html.= "</span></button><br>";
        //$html.= "<h4 class = \"modal-title\" id = \"myModalLabel\">Modal title</h4>";
        $html.= "</div>";
        $html.= "<div class = \"modal-body\">";
        $html.= "<span class = \"fa fa-spinner fa-spin\" id = \"" . $this->ModalSpinnerId . "\">Loading</span>";
        $html.= "<iframe id = \"" . $this->ModalIframeId . "\" name = \"" . $this->ModalIframeId . "\" onload = \"javascript:$('#" . $this->ModalSpinnerId . "').hide(); $(this).show();\" style = \"width: 100%; height: 650px; border: 0px;\" src = \"about:blank\"></iframe>";
        $html.= "</div>";
        //$html.= "<div class = \"modal-footer\">";
        //$html.= "<button type = \"button\" class = \"btn btn-default\" data-dismiss = \"modal\">Close</button>";
        //$html.= "<button type = \"button\" class = \"btn btn-primary\">Save changes</button>";
        //$html.= "</div>";
        $html.= "</div>";
        $html.= "</div>";
        $html.= "</div>";

        return $html;
    }

    /**
     * Tegner tabellen
     */
    public function render() {
        $tmpdata_single = null;



        // Tilføjer calcnumbers til select
        if (trim($this->select2) != "") {
            $this->select .= $this->select2;
        }

        if (!is_array($this->cols)) {
            throw new Exception("Cannot render view without columns");
        }

        // *****************************************************************************
        // DB
        // *****************************************************************************
        //$db = new dbtable ( $this->connectionString );
        $db = new MySqlDB($this->connectionString);

        // Order by (skal beholde default, hvis intet andet er valgt)
        if (trim($this->lastorderby) == "") {
            $this->lastorderby = $this->orderby;
        }

        // Sikring af $this->where ikke er tom
        if (trim($this->where) == "") {
            $this->where = "1";
        }
        // *****************************************************************************
        // *****************************************************************************
        // KEYCOLS - data ( + __Search)
        // *****************************************************************************

        if (is_array($this->keycols)) {
            foreach ($this->keycols as $kc) {

                $tmpreq = HttpRequest::RequestOrValue($kc ['dbfield'], $kc ['defaultvalue']);
                $tmpregwhere = "";
                if (is_array($tmpreq)) {
                    foreach ($tmpreq as $tr) {
                        if ($tr != "") {
                            $tmpregwhere .= "`" . $kc ['dbfield'] . "`='$tr' OR ";
                        }
                    }
                    if ($tmpregwhere != "") {
                        $tmpregwhere = mb_substr($tmpregwhere, 0, mb_strlen($tmpregwhere) - 4);
                        $tmpregwhere = "(" . $tmpregwhere . ")";
                    }
                }

                if ($tmpreq != "" || is_array($tmpreq)) {
                    if (is_array($tmpreq)) {
                        if ($tmpregwhere != "") {
                            $this->where2 .= " AND " . $tmpregwhere;
                        }
                    } else {
                        $this->where2 .= " AND `" . $kc ['dbfield'] . "`='$tmpreq'";
                    }

                    $this->keycol_selected [$kc ['dbfield']] = $tmpreq;
                } else {
                    $this->keycol_selected [$kc ['dbfield']] = "";
                }
            }
        }
        if (isset($_REQUEST ['__search']) && trim($_REQUEST ['__search']) != "" && is_array($this->searchFields)) {
            $this->where2 .= " AND (";
            foreach ($this->searchFields as $sf) {
                $tmpdata = $this->searchStrip(trim($_REQUEST ['__search']));

                if (mb_substr($tmpdata, 0, 1) == "%") {
                    // String
                    $this->where2 .= "`$sf` LIKE '$tmpdata' OR ";
                } else {
                    if ($this->searchOnlyAsStrings) {
                        // String
                        $this->where2 .= "`$sf` LIKE '%$tmpdata%' OR ";
                    } else {
                        // Number
                        $this->where2 .= "`$sf`=$tmpdata OR ";
                    }
                }
            }
            $this->where2 .= "1=2)";
        }


        if ($this->Modal) {
            echo $this->renderModal();
        }

        if (is_array($this->searchFields) || is_array($this->keycols)) {
            echo "<div class='panel panel-default'>";
            echo "<div class='panel-heading'></div>";
            echo "<div class='panel-body'>";
        }

        // *****************************************************************************
        // *****************************************************************************
        // KEYCOLS - draw
        // *****************************************************************************
        echo "<script language='javascript'>";
        echo "function __keycolChange(setScroll){";
        echo "if (setScroll == true){";
        echo "document.getElementById('__rviewkeycolform').__scrolltop.value=$(window).scrollTop();";
        echo "}else{";
        echo "document.getElementById('__rviewkeycolform').__scrolltop.value='0';";
        echo "}";
        echo "document.getElementById('__rviewkeycolform').__indexstart.value='0';";
        echo "document.getElementById('__rviewkeycolform').submit();";
        echo "}";
        $tmpScroll = HttpRequest::PostOrZero("__scrolltop");
        if ($tmpScroll) {
            echo "$(document).ready(function(){"
            . "$(window).scrollTop(" . $tmpScroll . ");"
            . "});";
        }
        echo "</script>";


        RForm::start("", "__rviewkeycolform");

        RForm::hidden("__scrolltop", 0);
        RForm::hidden("__indexstart", $this->indexstart);
        RForm::hidden("__orderby", $this->lastorderby);

        if (is_array($this->keycols)) {
            echo "<table>";

            if ($this->includeWhereInKeyCols) {
                if ($this->where != "" && $this->where != "1") {
                    $tmpwhere = " AND " . $this->where;
                } else {
                    $tmpwhere = "";
                }
            } else {
                $tmpwhere = "";
            }

            // Headers

            echo "<tr>";

            foreach ($this->keycols as $kc) {

                echo "<th>";
                echo $kc ['header'];

                echo "</th>";
            }
            echo "</tr>";

            // Selects
            echo "<tr>";

            // ***************** FOR EACH START

            foreach ($this->keycols as $kc) {

                $tmp = null;
                $tmp [0] ['text'] = "Vis alle";
                $tmp [0] ['value'] = "";

                //$tmpdata = $db->get ( "DISTINCT `" . $kc ['dbfield'] . "`", "", "`" . $kc ['dbfield'] . "`!='' AND `" . $kc ['dbfield'] . "` IS NOT NULL" . $tmpwhere, "`" . $kc ['dbfield'] . "`" );
                $tmpdataQ = $db->getRows("DISTINCT `" . $kc ['dbfield'] . "`", $this->table, "`" . $kc ['dbfield'] . "`!='' AND `" . $kc ['dbfield'] . "` IS NOT NULL" . $tmpwhere, "`" . $kc ['dbfield'] . "`");

                $x = 1;
                if ($tmpdataQ->hasData()) {
                    $tmpdata = $tmpdataQ->getData();
                    $tmpdata_single = arraysql::column_to_1dim_array($tmpdata, $kc ['dbfield']);
                    foreach ($tmpdata as $td) {
                        if ($kc ['showCount']) {
                            //$tmpcount = $db->count("`" . $kc ['dbfield'] . "`='" . $td [$kc ['dbfield']] . "' AND `" . $kc ['dbfield'] . "`!='' AND `" . $kc ['dbfield'] . "` IS NOT NULL" . $tmpwhere);
                            $tmpcount = $db->countRows("`" . $kc ['dbfield'] . "`='" . $td [$kc ['dbfield']] . "' AND `" . $kc ['dbfield'] . "`!='' AND `" . $kc ['dbfield'] . "` IS NOT NULL" . $tmpwhere, $this->table);
                            $tmp [$x] ['text'] = $td [$kc ['dbfield']] . " ($tmpcount)";
                        } else {
                            $tmp [$x] ['text'] = $td [$kc ['dbfield']];
                        }

                        $tmp [$x] ['value'] = $td [$kc ['dbfield']];
                        $x ++;
                    }
                }
                echo "<td style='padding-right: 5px;'>";
                $tmp_selected = "";

                if (is_array($this->keycol_selected [$kc ['dbfield']])) {
                    $tmp_selected = null;
                    foreach ($this->keycol_selected [$kc ['dbfield']] as $kcs) {
                        if (in_array($kcs, $tmpdata_single)) {
                            $tmp_selected [] = $kcs;
                        } else {
                            if ($kcs != "") {
                                $tmp_selected [] = $kcs;
                                if ($kc ['showCount']) {
                                    $tmp [$x] ['text'] = $kcs . " (0)";
                                } else {
                                    $tmp [$x] ['text'] = $kcs;
                                }
                                $tmp [$x] ['value'] = $kcs;
                                $x ++;
                            }
                        }
                    }
                } else {
                    if (is_array($tmpdata_single) && in_array($this->keycol_selected [$kc ['dbfield']], $tmpdata_single)) {
                        $tmp_selected = $this->keycol_selected [$kc ['dbfield']];
                    } else {

                        if ($this->keycol_selected [$kc ['dbfield']] != "") {

                            $tmp_selected = $this->keycol_selected [$kc ['dbfield']];
                            if ($kc ['showCount']) {
                                $tmp [$x] ['text'] = $tmp_selected . " (0)";
                            } else {
                                $tmp [$x] ['text'] = $tmp_selected;
                            }
                            $tmp [$x] ['value'] = $tmp_selected;
                            $x ++;
                        }
                    }
                }

                // Sort $tmp array
                if (is_array($tmp)) {

                    if ($kc ['reverseSort']) {
                        $tmp = arraysql::sort_by_column($tmp, "value", true);
                        if (is_array($tmp)) {
                            $tmp2 = null;
                            $tmp2 [0] ['text'] = "Vis alle";
                            $tmp2 [0] ['value'] = "";

                            foreach ($tmp as $t) {
                                if ($t ['value'] != "") {
                                    $tmp2 [] = $t;
                                }
                            }
                            $tmp = $tmp2;
                        }
                    } else {
                        $tmp = arraysql::sort_by_column($tmp, "value");
                    }
                }

                if ($kc ['oneLine'] == true) {
                    RForm::selectbox($kc ['dbfield'], $tmp, $tmp_selected, "", "", "", "__keycolChange();", 1);
                } else {
                    RForm::selectboxMulti($kc ['dbfield'], $tmp, $tmp_selected, "", "", "", "__keycolChange();", 6);
                }
                echo "</td>";
                // Betinget afhængighed mellem keycols

                if (is_array($tmp_selected)) {
                    $tmpregwhere = "";
                    foreach ($tmp_selected as $tr) {
                        if ($tr != "") {
                            $tmpregwhere .= "`" . $kc ['dbfield'] . "`='$tr' OR ";
                        }
                    }
                    if ($tmpregwhere != "") {
                        $tmpregwhere = mb_substr($tmpregwhere, 0, mb_strlen($tmpregwhere) - 4);
                        $tmpregwhere = "(" . $tmpregwhere . ")";
                        $tmpwhere .= " AND $tmpregwhere ";
                    }
                } else {

                    if ($tmp_selected != "") {
                        $tmpwhere .= " AND `" . $kc ['dbfield'] . "`='" . $tmp_selected . "'";
                    }
                }
            }

            // ***************** FOR EACH END

            echo "</tr>";

            echo "</table>";
        }
        // *****************************************************************************
        // *****************************************************************************
        // SEARCH
        // *****************************************************************************
        if (is_array($this->searchFields)) {

            //echo "<table class='table-condensed' cellspacing='0' cellpadding='0'><tr><td style='padding-top: 8px;'>";
            if (is_array($this->keycols)) {
                $addStyle = "margin-top: 10px;";
            } else {
                $addStyle = "";
            }
            echo "<table style='width: 300px; " . $addStyle . " '>";
            if ($this->searchText != "") {
                echo "<tr><td colspan='3'><div style='color: #666666;'><i>";
                echo $this->searchText;
                echo "</i></div></td></tr>";
            }
            echo "<tr><td>";

            //echo "<div class='row form-group'>";
            //echo "<div class='col-xs-3'>";

            echo "<input class='form-control input-sm' style=\"min-width: 250px;\" type=\"text\" name=\"__search\" id=\"__search\" size=\"40\" maxlength=\"255\" value=\"" . htmlentities(HttpRequest::RequestOrBlank("__search")) . "\"  />";
            //echo "</div>";
            //echo "<div class='col-xs-1'>";
            echo "</td><td>";
            echo "<input class='btn btn-default btn-sm' type='submit' value='Søg' style='margin-left: 5px;' />";
            //echo "</div>";
            //echo "</div>";

            echo "</td><td>";
            if (HttpRequest::RequestOrBlank("__search") != "") {
                echo "<input class='btn btn-default btn-sm' type='submit' value='Vis alle' style='width: 60px; margin-left: 5px;' onclick='javascript:document.getElementById(\"__search\").value = \"\"; document.getElementById(\"__rviewkeycolform\").submit();' />";
            }

            echo "</td></tr></table>";
        }

        if (is_array($this->searchFields) || is_array($this->keycols)) {
            echo "</div></div>"; // PANEL: keycols + search
        }
        echo "</form>";


        // *****************************************************************************
        // *****************************************************************************
        // SQL - DATA
        // *****************************************************************************
        $sql = "SELECT " . $this->select . " FROM `" . $this->table . "` WHERE ((" . $this->where . ")" . $this->where2 . ")";
        if (trim($this->groupby) != "") {
            $sql.= " GROUP BY " . $this->groupby;
        }
        if ($this->lastorderby != "") {
            $sql .= " ORDER BY " . $this->lastorderby;
        }
        $sql .= " LIMIT " . $this->indexstart . "," . $this->perpage;

        //$this->data = $db->sqlexec($sql);
        $tmpDataQ = $db->sqlQuery($sql);
        if ($tmpDataQ->hasData()) {
            $this->data = $tmpDataQ->getData();
        } else {
            $this->data = false;
        }


        // *****************************************************************************
        // *****************************************************************************
        // SUMMERERINGER
        // *****************************************************************************
        if (is_array($this->sumcols)) {
            echo "<table class='table-condensed table-bordered'>";
            foreach ($this->sumcols as $sc) {
                echo "<tr>";
                $tmpsum = null;
                //$tmpsum = $db->get("SUM(`" . $sc ['dbfield'] . "`) AS `summ`", "", "((" . $this->where . ")" . $this->where2 . ")", "", 1);
                $tmpsumQ = $db->getFirstRow("SUM(`" . $sc ['dbfield'] . "`) AS `summ`", $this->table, "((" . $this->where . ")" . $this->where2 . ")");
                if ($tmpsumQ->hasData()) {
                    $tmpsum = $tmpsumQ->getData();
                }
                echo "<td style='min-width: 60px; background-color: #eeeeee;'>";
                echo $sc ['header'];
                echo "</td>";
                echo "<td style='min-width: 60px; text-align: right;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                if ($sc ['format'] != "") {
                    $tmpFormat = str_replace("::", ":", $sc ['format']);
                    if (strstr($tmpFormat, ":")) {
                        $tmpEx = explode(":", $tmpFormat);
                        $tmpClass = $tmpEx[0];
                        $tmpFunction = $tmpEx[1];
                        echo $tmpClass::$tmpFunction($tmpsum ['summ'] + 0);
                    } else {
                        echo format::$tmpFormat($tmpsum ['summ'] + 0);
                    }
                } else {
                    echo ($tmpsum ['summ'] + 0);
                }
                echo "</td>";
                echo "</tr>";
            }
            echo "</table><br>";
        }
        // *****************************************************************************
        // *****************************************************************************
        // Counter
        // *****************************************************************************
        $count = $db->countRows("((" . $this->where . ")" . $this->where2 . ")", $this->table);
        if ($count > 0 && $this->showcount == true) {
            echo "Viser " . count($this->data) . " af " . $count;
        }
        // *****************************************************************************
        // *****************************************************************************
        // Pager
        // *****************************************************************************
        $phtml = "";
        if ($this->showpaging == true && $count > $this->perpage) {


            $pnhtml = "";
            $pfhtml = "";
            if ($count > 0 && count($this->data) < $count) {
                $tmppage = 1;
                $tmpindex = 0;
                echo "<br>";
                while ($tmpindex <= $count) {

                    if ($tmpindex == ($this->indexstart + $this->perpage)) {
                        $pnhtml .= "<a onclick='javascript:document.getElementById(\"__rviewkeycolform\").__indexstart.value=\"$tmpindex\";
											document.getElementById(\"__rviewkeycolform\").submit();' 
						style='cursor: pointer;'>Næste&nbsp;<span class='fa fa-chevron-right'></span></a>&nbsp;&nbsp;&nbsp;";
                    }
                    if ($tmpindex == ($this->indexstart - $this->perpage)) {
                        $pfhtml .= "<a onclick='javascript:document.getElementById(\"__rviewkeycolform\").__indexstart.value=\"$tmpindex\";
											document.getElementById(\"__rviewkeycolform\").submit();' 
						style='cursor: pointer;'><span class='fa fa-chevron-left'></span>&nbsp;Forrige</a>&nbsp;&nbsp;&nbsp;";
                    }

                    if ($tmpindex == $this->indexstart) {
                        $phtml .= "<span>";
                        $tmpStyle = " color: #666666;";
                    } else {
                        $tmpStyle = "";
                    }

                    $phtml .= "<a onclick='javascript:document.getElementById(\"__rviewkeycolform\").__indexstart.value=\"$tmpindex\";
											document.getElementById(\"__rviewkeycolform\").submit();' 
						style='cursor: pointer; $tmpStyle'>$tmppage</a>&nbsp;";
                    if ($tmpindex == $this->indexstart) {
                        $phtml .= "</span>";
                    }
                    $tmppage ++;
                    $tmpindex = $tmpindex + $this->perpage;
                }
            }

            // Næste og forrige (hvis findes)
            $pnfhtml = $pfhtml . $pnhtml;
            if ($pnfhtml != "") {
                $phtml = $pnfhtml . "&nbsp;&nbsp;" . $phtml;
            }

            echo $phtml;
        }

        // *****************************************************************************
        // TABEL
        // *****************************************************************************
        if ($count == 0 && $this->hideHeadersIfNoData == true) {
            // Display nothing
        } else {

            echo "<table class='table table-bordered' jtag='__datatable'>";

            if ($this->hideHeaders == false) {
                echo "<thead>";
                echo "<tr>";

                foreach ($this->cols as $col) {
                    echo "<th headerFor='" . $col ['dbfield'] . "' style='background-color: #DDDDDD;'>";
                    echo "<a style='color: #666666; cursor: pointer;'";

                    if ($this->disableSorting == false) {
                        echo "style='cursor: pointer;'";
                        echo " onclick='";
                        if (strstr($this->lastorderby, "`" . $col ['dbfield'] . "`")) {
                            if (stristr($this->lastorderby, "DESC")) {
                                $tmp_orderby = $this->orderby;
                            } else {
                                $tmp_orderby = "`" . $col ['dbfield'] . "` DESC";
                            }
                        } else {
                            $tmp_orderby = "`" . $col ['dbfield'] . "`";
                        }
                        echo "javascript:document.getElementById(\"__rviewkeycolform\").__orderby.value=\"" . $tmp_orderby . "\";
											document.getElementById(\"__rviewkeycolform\").submit();'";
                        echo "'";
                    }

                    echo ">";
                    echo $col ['header'];
                    echo "</a>";

                    // Tegner pil
                    if (strstr($this->lastorderby, "`" . $col ['dbfield'] . "`") && isset($col['image']) && $col ['image'] == "") {
                        if (stristr($this->lastorderby, "DESC")) {
                            echo "&nbsp;<span class='fa fa-angle-down'><span>";
                        } else {
                            echo "&nbsp;<span class='fa fa-angle-up'><span>";
                        }
                    }

                    echo "</th>";
                }
                echo "</tr>";
                echo "</thead>";
            }


            if (is_array($this->data)) {


                echo "<tbody>";

                foreach ($this->data as $dat) {




                    echo "<tr style='background-color: #f9f9f9;' onMouseover='javascript:$(this).css(\"background-color\",\"#eeeeee\");' onMouseout='javascript:$(this).css(\"background-color\",\"#f9f9f9\");'>";


                    foreach ($this->cols as $col) {

                        $coltag = isset($col ['tag']) ? str_ireplace("'", "", $col ['tag']) : "";

                        echo "<td ";
                        echo "align='" . $col ['align'] . "' tag='" . $coltag . "' dbfield='" . str_ireplace("'", "", $col ['dbfield']) . "' fieldvalue='" . str_ireplace("'", "", $dat [$col ['dbfield']]) . "' ";
                        if ($col ['width'] != "") {
                            echo " width='" . $col ['width'] . "'";
                        }
                        echo " valign='top'>";

                        // DATA
                        // Hvis link
                        if (isset($col ['link']) && $col ['link'] != "") {

                            if ($col ['linktarget'] == "__RViewModalPopup__") {

                                echo "<button type = \"button\" class = \"btn btn-primary btn-xs\" onclick = \"
        javascript:
                $('#" . $this->ModalIframeId . "').hide();
        $('#" . $this->ModalSpinnerId . "').show();
        $('#" . $this->ModalId . "').modal();
        $('#" . $this->ModalIframeId . "').attr('src', '" . $col ['link'] . $dat [$col ['dbfield']] . "');
        \">";
                            } else {

                                if (isset($col['mustConfirmMsg']) && trim($col['mustConfirmMsg']) != ""){
                                    echo "<a onclick=\"javascript:return confirm('".$col['mustConfirmMsg']."');\" href='" . $col ['link'] . $dat [$col ['dbfield']] . "' target='" . $col ['linktarget'] . "'>";

                                }else{
                                    echo "<a href='" . $col ['link'] . $dat [$col ['dbfield']] . "' target='" . $col ['linktarget'] . "'>";

                                }

                            }
                        }

                        // Hvis intet billede (skrives data)
                        if (!isset($col['link']) || $col ['link'] == "") {
                            if (isset($col ['format']) && $col ['format'] != "") {

                                $tmpFormat = str_replace("::", ":", $col ['format']);
                                if (strstr($tmpFormat, ":")) {
                                    $tmpEx = explode(":", $tmpFormat);
                                    $tmpClass = $tmpEx[0];
                                    $tmpFunction = $tmpEx[1];
                                    echo $tmpClass::$tmpFunction($dat [$col ['dbfield']]);
                                } else {
                                    echo format::$tmpFormat($dat [$col ['dbfield']]);
                                }
                            } else {

                                // *****************************************************************
                                // Kolonne indhold
                                // *****************************************************************

                                if (isset($col ['editType']) && $col ['editType'] == "custom") {
                                    // Custom kolonne
                                    echo $col ['html'];
                                } else {
                                    // Almindelig kolonne
                                    echo $dat [$col ['dbfield']];
                                }
                            }
                        } else {
                            echo trim($col['linkText']) != "" ? $col['linkText'] : $dat [$col ['dbfield']];
                        }

                        // Hvis link
                        if (isset($col ['link']) && $col ['link'] != "") {
                            if ($col ['linktarget'] == "__RViewModalPopup__") {
                                echo "</button>";
                            } else {
                                echo "</a>";
                            }
                        }

                        echo "</td>";
                    }




                    echo "</tr>";


                    // *********************************************
                    // Inline-edit DIVs
                    // *********************************************
                    if ($this->InlineEditRowDivEnabled && isset($dat[$this->InlineEditRowDivKey])) {
                        $tmpInlineDivID = $this->InlineEditRowDivPrefix . $dat[$this->InlineEditRowDivKey];

                        echo "<tr style='height: 5px;' id='" . $tmpInlineDivID . "_prerow'>";
                        echo "<td style='text-align: center; cursor:pointer; padding: 0px;' colspan='" . count($this->cols) . "' "
                        . ">";
                        echo "<div style='color:#AAAAAA;' tag='" . $this->InlineEditRowDivPrefix . "_fadown' id='" . $tmpInlineDivID . "_fadown'"
                        . "onclick='"
                        . "$(\"tr[tag=" . $this->InlineEditRowDivPrefix . "]\").hide();"
                        . "$(\"div[tag=" . $this->InlineEditRowDivPrefix . "_faup]\").hide();"
                        . "$(\"div[tag=" . $this->InlineEditRowDivPrefix . "_fadown]\").show();"
                        . "$(\"#" . $tmpInlineDivID . "_fadown\").hide();"
                        . "$(\"#" . $tmpInlineDivID . "_faup\").show();"
                        . "$(\"#" . $tmpInlineDivID . "_row\").show();' >";
                        echo "<span class='fa fa-angle-double-down'></span>&nbsp;";
                        echo "<span class='fa fa-angle-double-down'></span>&nbsp;";
                        echo "<span class='fa fa-angle-double-down'></span>";
                        echo "</div>";
                        echo "<div tag='" . $this->InlineEditRowDivPrefix . "_faup' id='" . $tmpInlineDivID . "_faup' style='color: #AAAAAA; display: none;'"
                        . "onclick='"
                        . "$(\"tr[tag=" . $this->InlineEditRowDivPrefix . "]\").hide();"
                        . "$(\"div[tag=" . $this->InlineEditRowDivPrefix . "_faup]\").hide();"
                        . "$(\"div[tag=" . $this->InlineEditRowDivPrefix . "_fadown]\").show();"
                        . ""
                        . ""
                        . ""
                        . "'>";
                        echo "<span class='fa fa-angle-double-up'></span>&nbsp;";
                        echo "<span class='fa fa-angle-double-up'></span>&nbsp;";
                        echo "<span class='fa fa-angle-double-up'></span>";
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";

                        echo "<tr tag='" . $this->InlineEditRowDivPrefix . "' id='" . $tmpInlineDivID . "_row' style='display: none;' >";
                        echo "<td colspan='" . count($this->cols) . "'>";
                        $this->InlineEditRowDivArray[] = array("key" => $this->InlineEditRowDivKey, "value" => $dat[$this->InlineEditRowDivKey], "id" => $tmpInlineDivID);
                        echo "<div style='width: 90%;' id='" . $tmpInlineDivID . "'></div>";
                        echo "</td></tr>";
                    }
                    // *********************************************
                }



                echo "</tbody>";
            }

            echo "</table>";
            echo $phtml;
        }


        // *****************************************************************************
    }

}
