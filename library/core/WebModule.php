<?php

/**
  * FR_Module
  *
  * Dette er den grundlæggende base for alle moduler.
  * Alle moduler udvider herfra.
  */
abstract class FR_WebModule extends FR_Object_Web {
	
	/**
	 * $presenter
	 *
	 * Sætter default presenter.
	 */
	public $presenter = 'php';
	
	/**
	 * $masterTemplate
	 *
	 * @var string $masterTemplate Master template (som udgangspunkt altid = default)
	 */
	public $masterTemplate = "default";
	
	/**
	 * $data
	 *
	 * Det data som skal videre til Presenter (VIEW)
	 */
	protected $data = array ();
	
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
	 * set
	 *
	 * Sætter data, der skal videreføres til Presenter (VIEW)
	 */
	protected function set($var, $val) {
		$this->data [$var] = $val;
	}
	
	/**
	 * getData
	 *
	 * Returnerer modulets data.
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * isValid
	 *
	 * Afgør om moduler er validt til at køre.
	 * Hvis det udvider fra FR_Module og FR_Auth så er det ok.
	 */
	public static function isValid($module) {
		return (is_object ( $module ) && $module instanceof FR_WebModule && $module instanceof FR_WebController);
	}
	public function __destruct() {
		parent::__destruct ();
	}
}

?>
