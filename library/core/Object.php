<?php

  /**
  * FR_Object
  *
  * Grundlag for de fleste klasser.
  * Basis funktionalitet.
  */
  
  
  abstract class FR_Object
  {
      /**
      * $me
      *
      * @var mixed $me Reflektionsklassen. (muligheden for at klasse kan kigge på sig selv)
      */
      protected $me;

            
      
      /**
      * __construct
      */
      
      public function __construct()
      {
          $this->me = new ReflectionClass($this);
      }
     
     
       /**
      * __destruct
      */
      public function __destruct()
      {
          
      }
  }

?>
