<?php

  /**
  * FR_Object_Web
  */
 
 
  /**
  * FR_Object_Web
  *
  * Dette er den grundlæggende klasse, som sætter bruger og session.
  */
  
  abstract class FR_Object_Rest extends FR_Object
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
