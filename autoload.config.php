<?php

// Composer
require_once FR_ROOT_RAW.'/vendor/autoload.php';


/**
 * __autoload
 * Autoload funktion
 *
 * @param string $class
 * @return void
 */
spl_autoload_register('FR_autoloader');

function FR_autoloader($class) {
    if (mb_substr($class, 0, 3) == "FR_") {

        // Henter fra Framework
        $file = str_replace('_', '/', mb_substr($class, 2)) . '.php';
        if (file_exists(FR_LIBRARY_PATH . '/core' . $file)) {
            require_once (FR_LIBRARY_PATH . '/core' . $file);
        }
    } else if (mb_substr($class, 0, 6) == "model_") {
        // Models
        $tmpModel = explode("_", $class);
        $tmpModelC = str_replace("model_" . $tmpModel[1], "", $class);
        $tmpFile = str_replace("*", $tmpModel[1], FR_MODELS_PATH) . str_replace("_", "/", $tmpModelC) . ".php";
        if (file_exists($tmpFile)) {
            require_once ($tmpFile);
        }
    } else if (mb_substr($class, 0, 10) == "viewmodel_") {
        // Viewmodels
        $tmpModel = explode("_", $class);
        $tmpModelC = str_replace("viewmodel_" . $tmpModel[1], "", $class);
        $tmpFile = str_replace("*", $tmpModel[1], FR_VIEWMODELS_PATH) . str_replace("_", "/", $tmpModelC) . ".php";
        if (file_exists($tmpFile)) {
            require_once ($tmpFile);
        }
    } else {
        // Local classes
        if (file_exists(FR_LIBRARY_PATH . "/classes/class_" . $class . ".php")) {
            require_once FR_LIBRARY_PATH . "/classes/class_" . $class . ".php";
        } else if (strstr($class, "Interface") && file_exists(FR_LIBRARY_PATH . "/interfaces/" . $class . ".php")) {
            require_once FR_LIBRARY_PATH . "/interfaces/" . $class . ".php";
        } else if (strstr($class, "Trait") && file_exists(FR_LIBRARY_PATH . "/traits/" . $class . ".php")) {
            require_once FR_LIBRARY_PATH . "/traits/" . $class . ".php";
        } else {
            // NAMESPACE - AUTOLOADER
            $className = ltrim($class, '\\');
            $fileName = '';
            $namespace = '';
            if ($lastNsPos = strrpos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

            // Peger på application-mappen og sætter module til evt. lowercase
            if (mb_substr(strtolower($fileName), 0, 4) == "app".DIRECTORY_SEPARATOR){
                $ex = explode(DIRECTORY_SEPARATOR, $fileName);
                $moduleDirLen = mb_strlen($ex[0].DIRECTORY_SEPARATOR.$ex[1]);
                $PathNoChange = FR_ROOT_RAW."/application/".$ex[1].DIRECTORY_SEPARATOR.mb_substr($fileName, $moduleDirLen);
                $PathLowerCase = FR_ROOT_RAW."/application/".strtolower($ex[1]).DIRECTORY_SEPARATOR.mb_substr($fileName, $moduleDirLen);
                if(file_exists($PathLowerCase)){
                    $fileNameInc = $PathLowerCase;
                }else if(file_exists($PathNoChange)){
                    $fileNameInc = $PathNoChange;
                }else{
                    $fileNameInc = null;
                }
            }else{
                $fileNameInc = stream_resolve_include_path($fileName);
            }


            if ($fileNameInc) {
                require_once $fileNameInc;
            } else {
                return false;
            }
        }
    }
}