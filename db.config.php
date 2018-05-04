<?php
$__localDbConf = parse_ini_file ($_SERVER['HOME']."/.my.cnf", true);



//**********************************************************************
// Shared Secret
//**********************************************************************
define('FR_SHAREDSECRET', $__localDbConf['shared-secret']['password'] ?? "1234-virker-ikke");



//**********************************************************************
// Default database - MySQL
//**********************************************************************

define('FR_DEFAULT_DB', "dw");
define('FR_DB_CHARSET', "utf8");

// Gammel database (bo.db.jbank.dk)
define('FR_DB_USER', $__localDbConf['client-bo']['user']);
define('FR_DB_PASS', $__localDbConf['client-bo']['password']);
define('FR_DB_HOST', $__localDbConf['client-bo']['host']);
define('FR_DB_PORT', $__localDbConf['client-bo']['port']);

// Ny database (db1.db.jbank.dk / db2.db.jbank.dk)
if (isset($__localDbConf['client-legacy']) && is_array($__localDbConf['client-legacy'])){
    define('FR_DB2_USER', $__localDbConf['client-legacy']['user']);
    define('FR_DB2_PASS', $__localDbConf['client-legacy']['password']);
    define('FR_DB2_HOST', $__localDbConf['client-legacy']['host']);
    define('FR_DB2_PORT', $__localDbConf['client-legacy']['port']);

    define('FR_DBPHINX_USER', $__localDbConf['client']['user']);
    define('FR_DBPHINX_PASS', $__localDbConf['client']['password']);
    define('FR_DBPHINX_HOST', $__localDbConf['client']['host'] ?? 'localhost');
    define('FR_DBPHINX_PORT', $__localDbConf['client']['port'] ?? '3306');

}else{
    define('FR_DB2_USER', $__localDbConf['client']['user']);
    define('FR_DB2_PASS', $__localDbConf['client']['password']);
    define('FR_DB2_HOST', $__localDbConf['client']['host'] ?? 'localhost');
    define('FR_DB2_PORT', $__localDbConf['client']['port'] ?? '3306');

    define('FR_DBPHINX_USER', $__localDbConf['client']['user']);
    define('FR_DBPHINX_PASS', $__localDbConf['client']['password']);
    define('FR_DBPHINX_HOST', $__localDbConf['client']['host'] ?? 'localhost');
    define('FR_DBPHINX_PORT', $__localDbConf['client']['port'] ?? '3306');
}

//**********************************************************************

unset($__localDbConf);

//**********************************************************************
// Connections (de parametre, der IKKE sættes, sættes automatisk til defaultværdierne ovenfor)
// BEMÆRK! Alle servernavne skal angives med HOST_NAME og ikke IP-adresse, da kombinationen server_db, benyttes til singletonpattern.
//**********************************************************************

// Logdatabase til brugslogs
define('FR_CONN_LOG', '{"db":"log", "table":"legacy", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

// System
define('FR_CONN_ENGAKU', '{"db":"dw", "table":"engaku_nyeste", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_ENGAKT', '{"db":"dw", "table":"engakt_nyeste", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

define('FR_CONN_CMS', '{"db":"cms", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');


define('FR_CONN_DW', '{"db":"dw", "table":"", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_DWKUNDEOVERBLIK', '{"db":"dwkundeoverblik", "table":"", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_DWKILDER', '{"db":"dwkilder", "table":"", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_DWOPFOLGNINGBONITET', '{"db":"dw_opfolgning_bonitet", "table":"", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_FOLLOWUP', '{"db":"followup", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_DWBEVILGBEFOJ', '{"db":"dw_bevilgbefoj", "table":"", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

define('FR_CONN_SIKKERHEDER_STAGE', '{"db":"sikkerheder_stage", "table":"", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');


define('FR_CONN_MALISTER', '{"db":"ma_lister", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

define('FR_CONN_MA', '{"db":"ma", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

define('FR_CONN_KREDITBESTILLING', '{"db":"kreditbestilling", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_KREDITBESTILLING_EJENDOM', '{"db":"kreditbestilling_ejendom", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

define('FR_CONN_RRUNNER3', '{"db":"rrunner3", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_MIFID', '{"db":"mifid", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');
define('FR_CONN_COREBANK', '{"db":"corebank", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

define('FR_CONN_DOCUMENTATION', '{"db":"documentation", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');

define('FR_CONN_TEST', '{"db":"test", "server":"'.FR_DB_HOST.'", "port":"'.FR_DB_PORT.'", "user":"'.FR_DB_USER.'","pass":"'.FR_DB_PASS.'"}');


// *****************************************************
// LEGACY - Databaser
// *****************************************************
define('FR_CONN_LEGACY_BUSCONFIG', '{"db":"legacy__busconfig", "server":"'.FR_DB2_HOST.'", "port":"'.FR_DB2_PORT.'", "user":"'.FR_DB2_USER.'","pass":"'.FR_DB2_PASS.'"}');
define('FR_CONN_LEGACY_BUSCUSTOMER', '{"db":"legacy__buscustomer", "server":"'.FR_DB2_HOST.'", "port":"'.FR_DB2_PORT.'", "user":"'.FR_DB2_USER.'","pass":"'.FR_DB2_PASS.'"}');
define('FR_CONN_LEGACY', '{"db":"legacy__db", "server":"'.FR_DBPHINX_HOST.'", "port":"'.FR_DBPHINX_PORT.'", "user":"'.FR_DBPHINX_USER.'","pass":"'.FR_DBPHINX_PASS.'","mode":"TRADITIONAL"}');
define('FR_CONN_FABRIKSKOERSLER', '{"db":"fabrikskoersler", "server":"'.FR_DB2_HOST.'", "port":"'.FR_DB2_PORT.'", "user":"'.FR_DB2_USER.'","pass":"'.FR_DB2_PASS.'"}');
