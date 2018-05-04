<?php
abstract class FR_SoapClient {

	protected static function call($connectionString, $paramArray, $login = "", $password = "") {
		
		$connectionArray = Rjson::JSONToArray($connectionString);
		$ws = new SoapClient($connectionArray['wsdl'], array('login' => $login, 'password' => $password, "cache_wsdl" => WSDL_CACHE_NONE));
		try {
			$data = $ws->__call($connectionArray['function'], $paramArray);
		} catch (Exception $e) {
			$data = $e;
		}
		unset($ws);
		return $data;
	}

}