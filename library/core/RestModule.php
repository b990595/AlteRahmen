<?php

/**
  * FR_Module
  *
  * Dette er den grundlæggende base for alle moduler.
  * Alle moduler udvider herfra.
  */
abstract class FR_RestModule extends FR_Object_Rest {
	
	/**
	 * $presenter
	 *
	 * Sætter default presenter.
	 */
	public $presenter = 'json';
	
	
	/**
	 * $name
	 *
	 * @var string $name Navnet på modulklassen
	 */
	public $name;
	
	/**
	 * $moduleName
	 *
	 * @var string $moduleName Navnet på det efterspurgte modul
	 */
	public $moduleName = null;
	
	/**
	 * $className
	 *
	 * @var string $className Navnet på den efterspurgte klasse
	 */
	public $routeName = null;
	
	/**
	 * $actionName
	 *
	 * @var string $className Navnet på den efterspurgte action
	 */
	public $actionName = null;
	
	/**
	 * __construct
	 *
	 * @author Joe Stump <joe@joestump.net>
	 */
	public function __construct() {
		parent::__construct ();
		
		$this->name = $this->me->getName ();
	}
	
	
	
	/**
	 * isValid
	 *
	 * Afgør om moduler er validt til at køre.
	 * Hvis det udvider fra FR_Module og FR_Auth så er det ok.
	 */
	public static function isValid($module) {
		return (is_object ( $module ) && $module instanceof FR_RestModule && $module instanceof FR_RestController);
	}
	public function __destruct() {
		parent::__destruct ();
	}
}

?>
