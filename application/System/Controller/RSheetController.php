<?php
class RSheetController extends FR_WebController {
	public function calc() {
	    $this->renderRaw ( model_system_rsheet::calc () );
	}
}