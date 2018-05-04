<?php

  /**
  * FR_Object_Web
  */
 
 
  /**
  * FR_Object_Web
  *
  */
  
  abstract class FR_Object_Web extends FR_Object
  {
      /**
      * $user
      *
      * Dette er den nuværende bruger
      */
      public function __construct()
      {

      	parent::__construct();
                    
      }

      public function __destruct()
      {
          parent::__destruct();
      }
  }
