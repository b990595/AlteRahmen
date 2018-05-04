<?php

while (file_exists(dirname(__DIR__) . '/data/deploy.lock')) {
    sleep(1);
}

// *******************************************************
// KLIENT TJEK
// *******************************************************
if (PHP_SAPI !== "cli"){
    die("Error: Only client access is allowed".PHP_EOL);
}

$model = $_SERVER['argv'][1] ?? null;
if (mb_substr($model, 0, 6) !== "Jbank/"){
    die("Error: Invalid model \"".$model."\", must be using forward-slash starting with \"Jbank/\"".PHP_EOL);
}

require_once('../config.php');

$GLOBALS ['__MySqlConnections'] = array();

function DBConnectionsCommit()
{
    if (is_array($GLOBALS['__MySqlConnections'])) {
        foreach ($GLOBALS ['__MySqlConnections'] as $c) {
            $c->close(true);
        }
    }
}

$instanceClass = str_replace("/", "\\", $model);

$instance = new $instanceClass();
if ($instance instanceof CommandlineInterface){

    $tmpArgs = $_SERVER['argv'];
    $args = [];
    unset($tmpArgs[0]);
    unset($tmpArgs[1]);
    foreach ($tmpArgs as $a){
        $args[] = $a;
    }

    $instance->main($args);

    DBConnectionsCommit();

}else{
    echo "Error: Model must be an instace of CommandLineInterface";
}

die(PHP_EOL);


