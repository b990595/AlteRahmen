<?php

  /**
  * FR_Presenter
  */
  
  
  class FR_Presenter
  {
      public static function factory($type,$module)
      {
      	
      	$file = FR_LIBRARY_PATH.'/core/Presenter/'.$type.'.php';
          if (include($file)) {
              $class = 'FR_Presenter_'.$type;
              if (class_exists($class)) {
                  $presenter = new $class($module);
                  if ($presenter instanceof FR_Presenter_common) {
                      return $presenter;
                  }

                  throw new Exception('Invalid presentation class: '.$type);
              
              }

              throw new Exception('Presentation class not found: '.$type);
          }

          throw new Exception('Presenter file not found: '.$type);
      }
      
      
     

      
      
      
  }
