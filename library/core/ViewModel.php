<?php
abstract class FR_ViewModel {

	private $html = "";
	
        public function getHtml(){
            return $this->html;
        }

        protected function appendHtml($html){
            $this->html.=$html;
        }


}
