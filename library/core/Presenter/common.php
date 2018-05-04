<?php

/**
  * FR_Presenter_common
  */

/**
 * FR_Presenter_common
 *
 * Dette er den grundlæggende klasse for alle Presenters (View)
 * Alle andre Presenters udvider herfra.
 */
abstract class FR_Presenter_common {
	protected $module;
	public function __construct($module) {
		$this->module = $module;
	}
	//abstract public function display($tplFile);
	public function __destruct() {
		
	}
}

