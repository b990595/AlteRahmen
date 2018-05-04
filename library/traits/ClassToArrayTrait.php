<?php

trait ClassToArrayTrait {

  public function getDataArray($returnEmpty = false) {
      $retur = get_object_vars($this);
      foreach ($retur as $key => $value) {
          if($value === null){
              unset($retur[$key]);
          }
      }
      return $retur;
    }
    
}
