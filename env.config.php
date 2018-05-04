<?php
$tmpApplicationEnv = $_SERVER['APPLICATION_ENV'] ?? getenv('APPLICATION_ENV');

// test/production
if ($tmpApplicationEnv == "production"){
    define ( 'FR_APPLICATION_ENV', 'prod');
}else if($tmpApplicationEnv == "test"){
    define ( 'FR_APPLICATION_ENV', 'test');
}else if(trim($tmpApplicationEnv) != ""){
    define ( 'FR_APPLICATION_ENV', $tmpApplicationEnv);
    ini_set("display_errors", 1);
}else{
    die("APPLICATION_ENV not found or valid.");
}


/**
 * FR_LIBRARY_PATH
 *
 * @global string FR_LIBRARY_PATH, Den absolutte sti til frameworket
 */
define ( 'FR_LIBRARY_PATH', dirname ( __FILE__ ) . "/library" );

/**
 * FR_APPLICATION_PATH
 *
 * @global string FR_APPLICATION_PATH, Den absolutte sti til frameworkets application-mappe
 */
define ( 'FR_APPLICATION_PATH', dirname ( __FILE__ ) . "/application" );

/**
 * FR_ROOT_RAW
 *
 * @global string FR_ROOT_RAW, Den absolutte sti til frameworkets rod-bibliotek
 */
define ( 'FR_ROOT_RAW', dirname ( __FILE__ ) );

/**
 * FR_ROOT
 *
 * @global string FR_ROOT, Frameworkets rod
 */
define ( 'FR_ROOT', '');


/**
 * FR_APPLICATION_ID
 *
 * @global string FR_APPLICATION_ID, ID for applikationen
 */
define ( 'FR_APPLICATION_ID', md5($_SERVER['SERVER_NAME'] ?? "dev-server"."legacy".FR_ROOT) );



/**
 * FR_SYSTEM_CONTROLLERS
 *
 * @global string FR_SYSTEM_CONTROLLERS, Frameworkets lib Web
 */
define ( 'FR_SYSTEM_CONTROLLERS', FR_ROOT . "/system" );



/**
 * FR_MODELS_PATH
 *
 * @global string FR_MODELS_PATH, Den absolutte sti til frameworkets Model
 */
define ( 'FR_MODELS_PATH', FR_ROOT_RAW . "/application/*/Model" );


/**
 * FR_VIEWMODELS_PATH
 *
 * @global string FR_VIEWMODELS_PATH, Den absolutte sti til frameworkets view-Model
 */
define ( 'FR_VIEWMODELS_PATH', FR_ROOT_RAW . "/application/*/Viewmodel" );


/**
 * FR_VIEWS_PATH
 *
 * @global string FR_VIEWS_PATH, Den absolutte sti til frameworkets View
 */
define ( 'FR_VIEWS_PATH', FR_ROOT_RAW . "/application/*/View" );

/**
 * FR_CONTROLLERS_PATH
 *
 * @global string FR_CONTROLLERS_PATH, Den absolutte sti til frameworkets Web
 */
define ( 'FR_CONTROLLERS_PATH', FR_ROOT_RAW . "/application/*/Web" );
define ( 'FR_REST_CONTROLLERS_PATH', FR_ROOT_RAW . "/application/*/Rest" );



/**
 * FR_TEMPLATES_PATH
 *
 * @global string FR_TEMPLATES_PATH, Den absolutte sti til frameworkets templates
 */
define ( 'FR_TEMPLATES_PATH', FR_ROOT_RAW . "/layouts" );

/**
 * FR_PUBLIC_PATH
 *
 * @global string FR_PUBLIC_PATH, Den absolutte sti til frameworkets Applicationsmappe
 */
define ( 'FR_PUBLIC_PATH', FR_ROOT . "" );


/**
 * FR_LOGFILES_PATH
 *
 * @global string FR_LOGFILES_PATH, Den absolutte sti til frameworkets logfiler
 */
define ( 'FR_LOGFILES_PATH', FR_ROOT_RAW . "/data/log" );


/**
 * FR_IMG_PATH
 *
 * @global string FR_IMG_PATH
 */
define ( 'FR_IMG_PATH', FR_PUBLIC_PATH . "/img" );


/**
 * FR_CSS_PATH
 *
 * @global string FR_CSS_PATH
 */
define ( 'FR_CSS_PATH', FR_PUBLIC_PATH . "/css" );

/**
 * FR_SCRIPTS_PATH
 *
 * @global string FR_SCRIPTS_PATH
 */
define ( 'FR_SCRIPTS_PATH', FR_PUBLIC_PATH . "/js" );

//**************************
// Namespace-classes (Monolog m.m.)
define('FR_NAMESPACE_ROOT', FR_LIBRARY_PATH ."/classes/namespace");
set_include_path(get_include_path() . PATH_SEPARATOR . FR_NAMESPACE_ROOT);
//**************************
