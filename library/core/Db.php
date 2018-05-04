<?php
abstract class FR_Db extends FR_Db_ActiveRecords {

	protected $connectionString;
	

	function __construct(){
		$this->initDb();
		$conn = $this->connect($this->connectionString);
		if(!$conn){
			// Fejlhåndtering
			echo "Ingen adgang til databasen.";
			die();
		}
	}

	abstract protected function initDb();



}
