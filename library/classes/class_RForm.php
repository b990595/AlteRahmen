<?php

/**
 * Den version af FORM - klassen, der bruges i RDocument
 *
 */
class RForm{

	private static $currentform = null;
	private static $numbers = false;
	
	
	public static function start($action="self", $name="form1", $target="_self", $method="post", $enctype="application/x-www-form-urlencoded"){

		self::$currentform = $name;
		self::$numbers = false;
		
		if ($action=="self"){
			$newaction=$_SERVER['PHP_SELF'];
		}else{
			$newaction = $action;
		}
	
		echo "<form target=\"".$target."\" id=\"$name\" name=\"$name\" method=\"$method\" enctype=\"$enctype\" action=\"$newaction\">";
			

	}

	public static function startNoMargin($action="self", $name="form1", $target="_self", $method="post", $enctype="application/x-www-form-urlencoded"){
	
		self::$currentform = $name;
		self::$numbers = false;
	
		if ($action=="self"){
			$newaction=$_SERVER['PHP_SELF'];
		}else{
			$newaction = $action;
		}
	
		echo "<form style='margin: 0px;' target=\"".$target."\" id=\"$name\" name=\"$name\" method=\"$method\" enctype=\"$enctype\" action=\"$newaction\">";
			
	
	}
	
	public static function numberV2($name, $decimals = 2, $defaultvalue=0, $prependText = "", $style = "", $min = -99999999999, $max = 99999999999){
		$value = $defaultvalue;
		
		// Max og Min
		if ($value > $max) {
			$value = $max;
		} else if ($value < $min) {
			$value = $min;
		}
		
		echo "<input name='$name' id='$name' type='hidden' value='$value' />" ;
		echo "<input 
				meta='{\"aSep\":\".\", \"aDec\":\",\", \"mDec\":$decimals, \"vMin\":$min, \"vMax\":$max, \"aSign\":\"$prependText\"}'
				type='text' tag='number' name='__$name' id='__$name' value='" . $value . "'
				style='border: 1px solid #666666; text-align: right; $style'
				onchange='javascript:$(\"#$name\").val($(this).autoNumericGet());'
				onclick='javascript:this.focus(); this.select();'
                				/>" ;
		
		echo "<script language='javascript'>";
		echo "function RFormFormatNumber(){";
		echo "$('#__$name').each(function(k,v){";
		echo "var tmpformat = $.parseJSON($(this).attr('meta'));";
		echo "$(this).autoNumeric(tmpformat);";
		echo "$(this).autoNumericSet($(this).val());";
		echo "});";
		echo "}";
		echo "RFormFormatNumber();";
		echo "</script>";
		
	}
	
	
	public static function date($name, $defaultvalue=false, $style = ""){
		if ($defaultvalue!=false && trim($defaultvalue)!=""){
			$value = $defaultvalue;
		}else{
			$value = date("Y-m-d");
		}
		$displayValue = format::SQLDateToDK($value);
		
		echo "<input type='hidden' name='$name' id='$name' value='" . $value . "' />";
		
		
		echo "<input type='text' id='" . $name . "_display' value='" . $displayValue . "' size='10'
				style='border: 1px solid #666666; $style'
				readonly />";
		
				echo "<img src='" . FR_IMG_PATH . "/RIcons/cal.gif' align='absmiddle' style='cursor:pointer; margin-left: 2px;' onclick=\"javascript:$('#" . $name . "_display').datepicker('show');\" />";
		
				echo '<script language="javascript">
				$("#' . $name . '_display").datepicker({
					monthNames:["Januar", "Februar", "Marts", "April", "Maj", "Juni", "Juli", "August", "September", "Oktober", "November", "December"],
					dayNamesMin:["Sø", "Ma", "Ti", "On", "To", "Fr", "Lø"],
					firstDay: 1,
					showWeek: true,
					weekHeader: "",
					dateFormat: "dd-mm-yy",
					altFormat: "yy-mm-dd",
					altField: $("#' . $name . '")
		});
				</script>';
		
	}
	

	public static function end(){
		echo "</form>";
	}


	public static function text($name, $defaultvalue="", $size=60, $max=255, $style="", $alt=""){
		$value = $defaultvalue;
		echo "<input type=\"text\" name=\"$name\" id=\"$name\" size=\"$size\" maxlength=\"$max\" alt=\"$alt\" value=\"".htmlentities($value)."\" style=\"$style\" />";
		
	}


	public static function hidden($name, $defaultvalue=""){
		$value = $defaultvalue;
		echo "<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\" />";
		
	}


	public static function radiobutton($name, $contentarray, $defaultvalue="", $horizontal=false, $multicol=0, $style="", $onchange=""){

		if (is_array($contentarray)){
			$value = "";

			if ($multicol>0){echo "<table border='0' cellpadding='0' cellspacing='0'>";}
			$col = 1;

			foreach ($contentarray as $c){

				if (is_array($c)){
					$subvalue = isset($c['value'])?$c['value']:"";
					$text = isset($c['text'])?$c['text']:$subvalue;
				}else{
					$subvalue = $c;
					$text = $c;
				}


				if ($multicol>0){
					if ($col==1){echo "<tr>";}
					echo "<td align='left' valign='top'>";
				}

				echo "<input style=\"$style\" type=\"radio\" id=\"$name\" name=\"$name\" value=\"$subvalue\"";
				if ($value==""){
					if ($subvalue==$defaultvalue){echo " checked";}
				}else{
					if ($subvalue==$value){echo " checked";}
				}

				if ($onchange!=""){
					echo " onclick='javascript:$onchange' ";
				}

				echo "> $text";
				if (!$horizontal && $multicol==0){echo "<br>";}else{echo " ";}
				
				if ($multicol>0){
					echo "</td>";
					if ($col==$multicol){
						echo "</tr>";
						$col = 1;
					}else{
						echo "<td width='10'></td>";
						$col++;
					}

				}



			}
			if ($multicol>0){echo "</table>";}
			

		}else{
			return false;
		}


	}


	public static function checkbox($name, $contentarray, $defaultvalue="", $horizontal=false, $multicol=0, $min=0, $max=999, $showalert=false, $style=""){

		if (is_array($contentarray)){
			$value = $defaultvalue;


			// Hvis defaultvalue er array, så oversættes den ..
			if (is_array($defaultvalue)){
				$newdefaultvalue = "";
				foreach ($defaultvalue as $d){
					$newdefaultvalue.="[".$d."]";
				}
				$defaultvalue = $newdefaultvalue;
			}


			$count = count($contentarray);
			//Script
			echo "<script language=\"javascript\">";
			echo "function set_".$name."_value(sender){";
			echo "var val='';";
			echo "var subval='';";
			echo "var stopscript=false;";

			// Check for min og max
			echo "var antal=0;";
			for ($i=0; $i<$count; $i++){
				echo "if(document.".self::$currentform.".$name$i.checked){";
				echo "antal++;";
				echo "}";
			}
			//For mange
			echo "if(antal>$max){";
			echo "stopscript=true;";
			echo "document.".self::$currentform."[sender].checked = false;";
			if ($showalert){
				echo "alert('Der må ikke sættes mere end $max flueben.');";
			}
			echo "}";
			//For lidt
			echo "if(antal<$min){";
			echo "stopscript=true;";
			echo "document.".self::$currentform."[sender].checked = true;";
			if ($showalert){
				echo "alert('Der skal minimum sættes $min flueben.');";
			}
			echo "}";


			echo "if(!stopscript){";
			// Sæt ny samlet value
			for ($i=0; $i<$count; $i++){
				echo "if(document.".self::$currentform.".$name$i.checked){";
				echo "subval = document.".self::$currentform.".$name$i.value;";
				echo "val=val+'['+subval+']';";
				echo "}";
			}
			echo "document.".self::$currentform.".$name.value = val;";
			echo "}";
			echo "}";
			echo "</script>";
			echo "<input type=\"hidden\" id=\"$name\" name=\"$name\" value=\"$defaultvalue\" />";


			if ($multicol>0){echo "<table border='0' cellpadding='0' cellspacing='0'>";}

			$x = 0;
			$col = 1;

			foreach ($contentarray as $c){

				if (is_array($c)){
					$subvalue = isset($c['value'])?$c['value']:"";
					$text = isset($c['text'])?$c['text']:$subvalue;
				}else{
					$subvalue = $c;
					$text = $c;
				}


				if ($multicol>0){
					if ($col==1){echo "<tr>";}
					echo "<td align='left' valign='top'>";
				}



				echo "<input style=\"$style\" type=\"checkbox\" id=\"$name$x\" name=\"$name$x\" hiddenname=\"$name\" value=\"$subvalue\" onClick=\"javascript:set_".$name."_value('$name$x');\"";
				if ($value==""){
					if (strstr($defaultvalue, "[".$subvalue."]")){echo " checked";}
				}else{
					if (strstr($value, "[".$subvalue."]")){echo " checked";}
				}
				echo "> $text";
				if (!$horizontal && $multicol==0){echo "<br>";}else{echo " ";}
				


				if ($multicol>0){
					echo "</td>";
					if ($col==$multicol){
						echo "</tr>";
						$col = 1;
					}else{
						echo "<td width='10'></td>";
						$col++;
					}

				}



				$x++;
			}
			if ($multicol>0){echo "</table>";}

			


		}else{
			return false;
		}


	}


	public static function single_checkbox($name, $text, $value, $checked=false, $style="", $onchange=false){

		$dbvalue = false;
		if ($dbvalue==$value){
			$checked=true;
		}

		echo "<input style=\"$style\" type=\"checkbox\" id=\"$name\" name=\"$name\" value=\"$value\" ";
		if ($onchange){echo "onClick=\"javascript:$onchange\" ";}
		if ($checked){echo "CHECKED";}
		echo " /> $text";

		




	}


	public static function selectbox($name, $contentarray, $defaultvalue="", $style="", $option_style="", $selected_style="", $onchange=false, $size=1){

		if (is_array($contentarray)){
			$value = "";

			echo "<select class='form-control input-sm' style=\"$style\" id=\"$name\" name=\"$name\" size=\"$size\" ";
			if ($onchange){echo "onchange=\"javascript:$onchange\"";}
			echo " >";
			


			foreach ($contentarray as $c){

				if (is_array($c)){
					$subvalue = isset($c['value'])?$c['value']:"";
					$text = isset($c['text'])?$c['text']:$subvalue;
				}else{
					$subvalue = $c;
					$text = $c;
				}

				echo "<option value=\"$subvalue\"";


				if ($value==""){
					if ($subvalue==$defaultvalue){echo " style=\"$selected_style\" selected";}else{echo " style=\"$option_style\"";}
				}else{
					if ($subvalue==$value){echo " style=\"$selected_style\" selected";}else{echo " style=\"$option_style\"";}
				}
				echo ">$text</option>";
				

			}

			echo "</select>";
			

		}else{
			return false;
		}

	}


	public static function number($name, $defaultvalue="", $style="", $decimals=false, $fixeddecimals=false, $min=false, $max=false, $comma=",", $size=12, $alt=""){


		if ($min===false){$min = -999999999999999999999;}
		if ($max===false){$max = 999999999999999999999;}


		$min = str_replace($comma, ".", $min);
		$max = str_replace($comma, ".", $max);


		$value = $defaultvalue;
		$regvalue = $value;



		// value, der skal registreres
		if ($comma!="."){
			$regvalue = str_replace($comma, ".", $regvalue);
		}

		// value, der skal vises
		if ($comma!="."){
			$value = str_replace(".", $comma, $value);
		}



		echo "<script language=\"javascript\">";
		echo "function modifynumber_$name(){";
		echo "var num='';";
		echo "var modyfied_num='';";
		echo "num = document.".self::$currentform.".".$name."_visiblenumber_.value;";
		echo "modyfied_num = num.replace('$comma','.');";


		//Decimaler
		if ($decimals){


			$faktor = 0;

			if ($decimals>0){
				for ($i=0; $i<$decimals;$i++){
					if ($faktor==0){
						$faktor = 10;
					}else{
						$faktor = $faktor*10;
					}
				}
			}
			if ($faktor==0){$faktor=1;}
			echo "modyfied_num = Math.round(modyfied_num*$faktor)/$faktor;";
			if ($fixeddecimals){
				echo "modyfied_num = modyfied_num.toFixed($decimals);";
			}
		}

		
		echo "var fejl = false;";

		// Afgør maximum
		echo "if((modyfied_num/1)>$max){";
		echo "fejl = \"max\";";
		echo "}";
		
		// Afgør minimum
		echo "if((modyfied_num/1)<$min){";
		echo "fejl = \"min\";";
		echo "}";



		echo "if(!fejl){";
		echo "document.".self::$currentform.".$name.value = modyfied_num;";
		if ($decimals){
			echo "var modyfied_formatted_num = modyfied_num.toString();";
			echo "modyfied_formatted_num = modyfied_formatted_num.replace('.', '$comma');";
			echo "document.".self::$currentform.".".$name."_visiblenumber_.value = modyfied_formatted_num;";
		}
		
		echo "}else{";
		

		echo "if(fejl==\"max\"){";

		if ($decimals){
			echo "var newval = $max;";
			echo "newval = newval.toFixed($decimals);";
			echo "document.".self::$currentform.".$name.value = newval;";
			echo "newval = newval.replace('.','$comma');";
			echo "document.".self::$currentform.".".$name."_visiblenumber_.value = newval;";
		}else{
			echo "document.".self::$currentform.".$name.value = $max;";
			echo "document.".self::$currentform.".".$name."_visiblenumber_.value = $max;";
		}
		
		echo "}else if(fejl==\"min\"){";

		if ($decimals){
			echo "var newval = $min;";
			echo "newval = newval.toFixed($decimals);";
			echo "document.".self::$currentform.".$name.value = newval;";
			echo "newval = newval.replace('.','$comma');";
			echo "document.".self::$currentform.".".$name."_visiblenumber_.value = newval;";
		}else{
			echo "document.".self::$currentform.".$name.value = $min;";
			echo "document.".self::$currentform.".".$name."_visiblenumber_.value = $min;";
		}
		
		echo "}else{";

		if ($decimals){
			echo "var newval = 0;";
			echo "newval = newval.toFixed($decimals);";
			echo "document.".self::$currentform.".$name.value = newval;";
			echo "newval = newval.replace('.','$comma');";
			echo "document.".self::$currentform.".".$name."_visiblenumber_.value = newval;";
		}else{
			echo "document.".self::$currentform.".$name.value = 0;";
			echo "document.".self::$currentform.".".$name."_visiblenumber_.value = 0;";
		}
		
		echo "}";



		echo "}";


		
		echo "}";
		
		echo "</script>";
		


		
		echo "<input type=\"hidden\" name=\"$name\" id=\"$name\" size=\"$size\" value=\"$regvalue\" />";
		echo "<input style=\"$style\" type=\"text\" style=\"text-align:right\" name=\"".$name."_visiblenumber_\" id=\"".$name."_visiblenumber_\" size=\"$size\" alt=\"$alt\" value=\"$value\" onchange=\"javascript:modifynumber_$name();\" />";
		
		echo "<script language=\"javascript\">modifynumber_$name();</script>";
		

		self::$numbers[]=$name;
	}








}