<?php

class arraysql{

	
	public static function add_column_from_otherarray($mainarray, $otherarray, $mainlinkcolumn, $otherlinkcolumn, $column_to_add ,$newcolumnname=false){
	if (!is_array($mainarray) || !is_array($otherarray)){
			return false;
		}else{
			
			if (!$newcolumnname){
				$newcolumnname = $column_to_add;
			}
			
			$keys = array_keys($mainarray);
			foreach ($keys as $k){
				$join = false;
				$join = self::get_first_strict($otherarray, $otherlinkcolumn, $mainarray[$k][$mainlinkcolumn]);
				if (is_array($join)){
											
					$mainarray[$k][$newcolumnname] = $join[$column_to_add];
				
				}
			}
			
			return $mainarray;
			
		}
		
		
		
		
	}
	
	

	public static function add_calc_column($array, $newcolumn, $function, $arrayfield_to_function = false){
            if (!is_array($array)){
			return false;
		}else{
			
			$keys = array_keys($array);
			
			foreach ($keys as $k){
				if ($arrayfield_to_function==false ||$arrayfield_to_function==""){
                                    eval ("\$array[\$k][\$newcolumn] = ".$function."(\$array[\$k]);");
                                }else{
                                    eval ("\$array[\$k][\$newcolumn] = ".$function."(\$array[\$k][\$arrayfield_to_function]);");
                                }
			}
			
			return $array;

		}

	}






	public static function column_to_1dim_array($array, $col){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				$retur[] = $a[$col];
			}
			if (is_array($retur)){
				return $retur;
			}else{
				return false;
			}
		}
	}



	public static function get_strict($array, $col, $value){
                $newarray = array();
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]==$value){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray) && !empty($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}

	
	public static function get_first_strict($array, $col, $value){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]==$value){
					return $a;
				}
			}
			return false;
		}
	}
	

	public static function get_like($array, $col, $value){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if (stristr($a[$col],$value)){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}


	public static function get_first_like($array, $col, $value){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if (stristr($a[$col],$value)){
					return $a;
				}
			}
				return false;
		}
	}
	
	
	

	public static function get_or($array, $col1, $value1, $like1=false, $col2, $value2, $like2=false, $col3="", $value3="", $like3=false, $col4="", $value4="", $like4=false, $col5="", $value5="", $like5=false){
		if (!is_array($array)){
			return false;
		}else{
			// Array 1
			if ($like1){$array1 = self::get_like($array, $col1, $value1);}else{$array1 = self::get_strict($array, $col1, $value1);}
			// Array 2
			if ($like2){$array2 = self::get_like($array, $col2, $value2);}else{$array2 = self::get_strict($array, $col2, $value2);}
			// Array 3
			if ($col3!=""){if ($like3){$array3 = self::get_like($array, $col3, $value3);}else{$array3 = self::get_strict($array, $col3, $value3);}}
			// Array 4
			if ($col4!=""){if ($like4){$array4 = self::get_like($array, $col4, $value4);}else{$array4 = self::get_strict($array, $col4, $value4);}}
			// Array 5
			if ($col5!=""){if ($like5){$array5 = self::get_like($array, $col5, $value5);}else{$array5 = self::get_strict($array, $col5, $value5);}}

			//Combine
			if (is_array($array1)){
				foreach ($array1 as $a){$combinedarray[] = $a;}
			}
			if (is_array($array2)){
				foreach ($array2 as $a){$combinedarray[] = $a;}
			}
			if (is_array($array3) && $col3!=""){
				foreach ($array3 as $a){$combinedarray[] = $a;}
			}
			if (is_array($array4) && $col4!=""){
				foreach ($array4 as $a){$combinedarray[] = $a;}
			}
			if (is_array($array5) && $col5!=""){
				foreach ($array5 as $a){$combinedarray[] = $a;}
			}

			if (!is_array($combinedarray)){
				return false;
			}else{
				return self::unik($combinedarray);
			}

		}

	}



	public static function get_and($array, $col1, $value1, $like1=false, $col2, $value2, $like2=false, $col3="", $value3="", $like3=false, $col4="", $value4="", $like4=false, $col5="", $value5="", $like5=false){
		// Array 1
		if (!is_array($array)){
			return false;
		}else{if ($like1){$newarray = self::get_like($array, $col1, $value1);}else{$newarray = self::get_strict($array, $col1, $value1);}}

		// Array 2
		if (!is_array($newarray)){
			return false;
		}else{if ($like2){$newarray = self::get_like($newarray, $col2, $value2);}else{$newarray = self::get_strict($newarray, $col2, $value2);}}

		// Array 3
		if ($col3!=""){
			if (!is_array($newarray)){
				return false;
			}else{if ($like3){$newarray = self::get_like($newarray, $col3, $value3);}else{$newarray = self::get_strict($newarray, $col3, $value3);}}}

			// Array 4
			if ($col4!=""){
				if (!is_array($newarray)){
					return false;
				}else{if ($like4){$newarray = self::get_like($newarray, $col4, $value4);}else{$newarray = self::get_strict($newarray, $col4, $value4);}}}

				// Array 5
				if ($col5!=""){
					if (!is_array($newarray)){
						return false;
					}else{if ($like5){$newarray = self::get_like($newarray, $col5, $value5);}else{$newarray = self::get_strict($newarray, $col5, $value5);}}}


					// Return new array
					if (is_array($newarray)){
						return $newarray;
					}else{
						return false;
					}

	}






	public static function get_greater($array, $col, $value){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]>$value){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}



	public static function get_greater_or_equal($array, $col, $value){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]>=$value){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}



	public static function get_less_or_equal($array, $col, $value){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]<=$value){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}



	public static function get_less($array, $col, $value){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]<$value){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}



	public static function get_between($array, $col, $value1, $value2){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]>$value1 && $a[$col]<$value2){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}



	public static function get_between_or_equal($array, $col, $value1, $value2){
		if (!is_array($array)){
			return false;
		}else{
			foreach ($array as $a){
				if ($a[$col]>=$value1 && $a[$col]<=$value2){
					$newarray[]=$a;
				}
			}
			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}
		}
	}



	public static function sort_by_column_old($array, $col, $reverse=false){
		if (!is_array($array)){
			return false;
		}else{

			// Danner sorterings array
			$x = 0;
			foreach ($array as $a){


				$sortarray[] = $a[$col]."|$x";
				$x++;
			}



			//Sorterer sortering array
			if ($reverse){
				rsort($sortarray);
			}else{
				sort($sortarray);
			}

			// Danner det nye sorterede array

			foreach ($sortarray as $sa){
				$pos = strrpos($sa, "|");
				$number = mb_substr($sa, $pos+1);
				$newarray[] = $array[$number];
			}

			return $newarray;

		}
	}


	public static function sort_by_column($array, $col, $reverse=false){
		if (!is_array($array)){
			return false;
		}else{

			// Danner sorterings array
			$x = 0;
			foreach ($array as $a){


				$sortarray[] = $a[$col];

				$x++;
			}


			//Sorterer sortering array
			if ($reverse){
				arsort($sortarray);
			}else{
				asort($sortarray);
			}

			// Danner det nye sorterede array
			$c = count($sortarray);

			foreach (array_keys($sortarray) as $sak){
				$newarray[] = $array[$sak];
			}

			return $newarray;

		}
	}





	public static function sort_by_multicolumn($array, $col1, $reverse1=false, $col2, $reverse2=false, $col3="", $reverse3=false){
		if (!is_array($array)){
			return false;
		}else{
			$newarray = $array;

			if ($col3!=""){$newarray = self::sort_by_column($newarray, $col3, $reverse3);}
			$newarray = self::sort_by_column($newarray, $col2, $reverse2);
			$newarray = self::sort_by_column($newarray, $col1, $reverse1);

			if (is_array($newarray)){
				return $newarray;
			}else{
				return false;
			}

		}

	}



	public static function unik($array){
		if (!is_array($array)){
			return false;
		}else{
			// Danner single array
			$pointer = 0;
			foreach ($array as $ar){
				foreach ($ar as $a){
					$tmparray[$pointer].=(String)$a;
				}
				$pointer++;
			}

			$unikarray = array_unique($tmparray);
			$unikarray_keys = array_keys($unikarray);
			// Danner unik array
			foreach ($unikarray_keys as $k){
				$newarray[] = $array[$k];
			}

			return $newarray;

		}
	}



	public static function unik_col($array, $col){
		if (!is_array($array)){
			return false;
		}else{
			// Danner single array
			foreach ($array as $ar){
				$tmparray[]=$ar[$col];
			}

			$unikarray = array_unique($tmparray);
			$unikarray_keys = array_keys($unikarray);
			// Danner unik array
			foreach ($unikarray_keys as $k){
				$newarray[] = $array[$k];
			}

			return $newarray;

		}
	}



	public static function unik_multicol_combined($array, $col1, $col2, $col3="", $col4="", $col5=""){
		if (!is_array($array)){
			return false;
		}else{
			// Danner single array
			$pointer = 0;
			foreach ($array as $ar){

				$tmparray[$pointer].=(String)$ar[$col1];
				$tmparray[$pointer].=(String)$ar[$col2];
				if ($col3!=""){$tmparray[$pointer].=(String)$ar[$col3];}
				if ($col4!=""){$tmparray[$pointer].=(String)$ar[$col4];}
				if ($col5!=""){$tmparray[$pointer].=(String)$ar[$col5];}

				$pointer++;
			}


			$unikarray = array_unique($tmparray);
			$unikarray_keys = array_keys($unikarray);
			// Danner unik array
			foreach ($unikarray_keys as $k){
				$newarray[] = $array[$k];
			}

			return $newarray;

		}
	}


	public static function column_walk($array, $column, $function){
		if (!is_array($array)){
			return false;
		}else{
			$count = count($array);
			for ($x=0; $x<$count; $x++){
				$array[$x][$column] = call_user_func($function, $array[$x][$column]);
			}
			return $array;

		}


	}






}