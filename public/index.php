<?php

while (file_exists(dirname(__DIR__) . '/data/deploy.lock')) {
    sleep(1);
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

// Framework logik

$tmpUri = $_SERVER['REQUEST_URI'];
if (strstr($tmpUri, "?")) {
    $tmpUri = mb_substr($tmpUri, 0, mb_strpos($tmpUri, "?"));
}
if (mb_substr($tmpUri, 0, 1) == "/") {
    $tmpUri = mb_substr($tmpUri, 1);
}
$tmpRequest = explode('/', $tmpUri);
$module = isset($tmpRequest[0]) ? ucfirst(trim($tmpRequest[0])) : '';
$route = isset($tmpRequest[1]) ? trim($tmpRequest[1]) : '';
$action = isset($tmpRequest[2]) ? trim($tmpRequest[2]) : '__default';

$ModuleRouteFound = false;

if (file_exists(FR_ROOT_RAW . "/application/" . $module . "/Config/Route.php")) {

    $ModuleRoute = include FR_ROOT_RAW . "/application/" . $module . "/Config/Route.php";

    if (isset($ModuleRoute[$route])) {
        if ($ModuleRoute[$route]['type'] == "controller") {
            $ModuleRouteFound = true;
            $controller = "App\\" . ucfirst($module) . "\\" . str_replace("/", "\\", $ModuleRoute[$route]['controller']);
            $classFile = FR_ROOT_RAW . "/application/" . $module . "/" . $ModuleRoute[$route]['controller'] . ".php";
            $action = $ModuleRoute[$route]['action'] ?? $action;
        }
    }


}else{
    die("No route-config-file found.");
}

if (!$ModuleRouteFound) {
    die("Route not found.");
}

// Request-ID (sammenkædning af logs)
SystemInternals::setRequestId(system::unik());

//*****************************************************************************
// Log (Kibana)
//*****************************************************************************
use Jblib\Monolog\Formatter\LogstashFormatter;
use Jblib\Std\Io\FileUtils;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Registry;

// Make sure the log dir exists
FileUtils::createDir(dirname(__DIR__) . '/data/log');

// Monolog
// create a log channel
$log = new Logger('application');
$loggerServerData = $_SERVER;
// RequestID puttes i extra.referer (for at kunne søge en samlet transaktion frem)
$loggerServerData['HTTP_REFERER'] = "ReqID:" . SystemInternals::getRequestId();

$logStreamHandler = new StreamHandler(FR_LOGFILES_PATH . "/req-logstash.log", Logger::INFO);
$logStreamHandler->setFormatter(new LogstashFormatter($module . "/" . $route . "/" . $action));
$logStreamHandler->pushProcessor(new Monolog\Processor\WebProcessor($loggerServerData));
$log->pushHandler($logStreamHandler);
Registry::addLogger($log);
//*****************************************************************************


// LOG
$log->info("Request recieved", array("message" => "Request recieved", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $controller, "action" => $action, "http_request" => $_REQUEST, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));


if (file_exists($classFile)) {
    require_once($classFile);
    if (class_exists($controller)) {
        try {

            $paramArray = array();
            $tmpParamLogArray = array();


            $instance = new $controller ();

            if (!FR_WebModule::isValid($instance) && !FR_RestModule::isValid($instance)) {
                header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
                $log->warning("Module not valid", array("message" => "Module not valid", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $class, "action" => $action, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));

                die("FEJL! Det ønskede modul er ikke validt!");
            }

            $instance->moduleName = $module;
            SystemInternals::setModule($module);
            $instance->routeName = $route;
            SystemInternals::setRoute($route);

            if ($instance instanceof FR_RestController) {
                $action = strtolower($_SERVER['REQUEST_METHOD']);
            }
            $instance->actionName = $action;
            SystemInternals::setAction($action);


            $tmpAccessAllowed = false;

            if (NoAuth::freeAccess($module, $route, $action)) {
                $tmpAccessAllowed = true;
            } else if ($instance->authenticate()) {
                $tmpAccessAllowed = true;
            }
                
            
            if ($tmpAccessAllowed) {


                // *********************************************************************************
                // Params (store for controller)
                // *********************************************************************************

                if (isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == "application/json" || isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == "application/json") {
                    $tmpJson = file_get_contents("php://input");
                    $tmpJsonData = json_decode($tmpJson, true);
                    // Medtager GET
                    if (is_array($_GET)) {
                        foreach (array_keys($_GET) as $k) {
                            if (!isset($tmpJsonData[$k])) {
                                $tmpJsonData[$k] = $_GET[$k];
                            }
                        }
                    }
                    SystemInternals::SetReguestData($tmpJsonData);


                } else {
                    if ($_SERVER['REQUEST_METHOD'] == "GET") {
                        SystemInternals::SetReguestData($_GET);
                    } else if ($_SERVER['REQUEST_METHOD'] == "POST") {
                        $tmpPOST = $_POST;

                        // Medtager php://input, hvis $_POST er tom ..
                        if (empty($tmpPOST)) {
                            $post_vars = array();
                            $kaj = file_get_contents("php://input");
                            if (trim($kaj) != "") {
                                parse_str($kaj, $post_vars);
                            }
                            if (is_array($post_vars)) {
                                foreach (array_keys($post_vars) as $k) {
                                    if (!isset($tmpPOST[$k])) {
                                        $tmpPOST[$k] = $post_vars[$k];
                                    }
                                }
                            }
                        }
                        // Medtager GET
                        if (is_array($_GET)) {
                            foreach (array_keys($_GET) as $k) {
                                if (!isset($tmpPOST[$k])) {
                                    $tmpPOST[$k] = $_GET[$k];
                                }
                            }
                        }

                        SystemInternals::SetReguestData($tmpPOST);
                    } else {
                        if (!isset($_SERVER['HTTP_CONTENT_TYPE']) || $_SERVER['HTTP_CONTENT_TYPE'] != "application/x-www-form-urlencoded") {
                            if (isset($_SERVER['HTTP_CONTENT_LENGTH']) && $_SERVER['HTTP_CONTENT_LENGTH'] > 0) {
                                header($_SERVER["SERVER_PROTOCOL"] . " 406 Not Acceptable");
                                header("Content-Type: text/plain");
                                echo "Content-Type [" . $_SERVER['HTTP_CONTENT_TYPE'] . "] must be application/x-www-form-urlencoded or application/json";
                                die();
                            }
                        }
                        $post_vars = array();
                        $kaj = file_get_contents("php://input");
                        if (trim($kaj) != "") {
                            parse_str($kaj, $post_vars);
                        }
                        SystemInternals::SetReguestData($post_vars);
                    }
                }

                $ref = new ReflectionClass($instance);

                if (!$ref->hasMethod($action)) {
                    $log->warning("Action not implemented in controller", array("message" => "Action not implemented in controller", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $class, "action" => $action, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                    if ($instance instanceof FR_RestController) {
                        die($_SERVER['REQUEST_METHOD'] . "-metoden er ikke implementeret.");
                    } else {
                        die("Den angivne sti findes ikke.");
                    }
                }
                $method = $ref->getMethod($action);
                $param = $method->getParameters();
                $paramNum = $method->getNumberOfParameters();


                for ($x = 0; $x < $paramNum; $x++) {
                    $tmpRequestParams = SystemInternals::GetReguestData();
                    if (isset($tmpRequestParams [$param [$x]->name])) {
                        $tmpParam = $tmpRequestParams [$param [$x]->name];
                    } else {
                        try {
                            $tmpParam = $param [$x]->getDefaultValue();
                        } catch (Exception $ex) {
                            throw new Exception($ex->getMessage() . " [" . $param [$x]->name . "]", $ex->getCode());
                        }
                    }
                    $paramArray [] = $tmpParam;
                    $tmpParamLogArray[$param [$x]->name] = $tmpParam;
                }


                try {


                    if (count($paramArray > 0)) {
                        $result = call_user_func_array(array(
                            $instance,
                            $action
                        ), $paramArray);
                    } else {
                        $result = $instance->$action();
                    }


                    DBConnectionsCommit();

                    // Udskriver Rest-result, hvis der bare retuneres null, bool, string, number eller array ..
                    if (!is_object($result) && $instance instanceof FR_RestController) {
                        switch ($action) {
                            case "get":

                                if ($result === null) {
                                    $tmpStatus = 404;
                                    $tmpStatusText = "Not Found";
                                } else {
                                    $tmpStatus = 200;
                                    $tmpStatusText = "OK";
                                }
                                break;
                            case "post":
                                if ($result === null) {
                                    $tmpStatus = 404;
                                    $tmpStatusText = "Not Found";
                                } else {
                                    $tmpStatus = 201;
                                    $tmpStatusText = "Created";
                                }
                                break;
                            case "put":
                                if ($result === null) {
                                    $tmpStatus = 404;
                                    $tmpStatusText = "Not Found";
                                } else {
                                    $tmpStatus = 200;
                                    $tmpStatusText = "OK";
                                }
                                break;
                            case "delete":
                                if ($result === null) {
                                    $tmpStatus = 404;
                                    $tmpStatusText = "Not Found";
                                } else if (is_array($result) && count($result) > 0) {
                                    $tmpStatus = 200;
                                    $tmpStatusText = "OK";
                                } else if (is_string($result) && trim($result) != "") {
                                    $tmpStatus = 200;
                                    $tmpStatusText = "OK";
                                } else {
                                    $tmpStatus = 204;
                                    $tmpStatusText = "No Content";
                                }
                                break;
                        }

                        // Overruling
                        if (RestResultSettings::getForceCustomNonExceptionReturn()) {
                            $tmpStatus = RestResultSettings::getForceCustomNonExceptionReturnHttpCode();
                            $tmpStatusText = RestResultSettings::getForceCustomNonExceptionReturnHttpText();
                        }

                        header($_SERVER["SERVER_PROTOCOL"] . " " . $tmpStatus . " " . $tmpStatusText);

                        // Sikre pæn tal formatering ..
                        if (is_numeric($result)) {
                            $result = $result / 1;
                        }
                        if ($tmpStatus != 204) {

                            $out = array(
                                "code" => $tmpStatus,
                                "status" => $tmpStatusText,
                                "message" => "",
                                "data" => $result,
                                "flow" => CorebankFlow::getFlowArray(false)
                            );
                            if ($out['flow'] === null) {
                                unset($out['flow']);
                            }
                            header("Content-type: application/json");
                            echo json_encode($out);
                            $log->info("Request responded (Rest)", array("message" => "Request responded (Rest)", "reqid" => SystemInternals::getRequestId(), "action" => $action, "params" => $tmpParamLogArray, "response" => $out, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                        } else if ($tmpStatus == 204) {
                            $log->info("Request responded (Rest)", array("message" => "Request responded (Rest)", "reqid" => SystemInternals::getRequestId(), "action" => $action, "params" => $tmpParamLogArray, "response" => "", "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                        }
                        die();
                    } else if ($instance instanceof FR_WebController) {
                        $log->info("Request responded (Web)", array("message" => "Request responded (Web)", "reqid" => SystemInternals::getRequestId(), "action" => $action, "params" => $tmpParamLogArray, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                    }
                } catch (Exception $error) {

                    $err = array(
                        "code" => RestResultSettings::getExceptionHttpCode(),
                        "status" => RestResultSettings::getExceptionHttpText(),
                        "message" => $error->getMessage(),
                        "data" => null,
                        "flow" => CorebankFlow::getFlowArray(true)
                    );
                    if ($err['flow'] === null) {
                        unset($err['flow']);
                    }
                    if ($instance instanceof FR_RestController) {
                        header($_SERVER["SERVER_PROTOCOL"] . " " . RestResultSettings::getExceptionHttpCode() . " " . RestResultSettings::getExceptionHttpText());
                        header("Content-Type: application/json");
                        $log->warning("Error (Rest)", array("message" => "Error (Rest)", "reqid" => SystemInternals::getRequestId(), "action" => $action, "params" => $tmpParamLogArray, "response" => $err, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                        die(json_encode($err));
                    } else {
                        $log->warning("Error (Web)", array("message" => "Error (Web)", "reqid" => SystemInternals::getRequestId(), "action" => $action, "params" => $tmpParamLogArray, "response" => $err, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                        //header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
                        die("<b style='color:red;'>FEJL:</b> " . $error->getMessage());
                    }
                }catch (TypeError $error){
                    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                    header("Content-Type: application/json");
                    $err = array(
                        "code" => 400,
                        "status" => "Bad Request",
                        "message" => $error->getMessage().PHP_EOL.$error->getTraceAsString(),
                        "data" => null,
                        "flow" => CorebankFlow::getFlowArray(true)
                    );

                    $log->warning("Error (Rest)", array("message" => "Error (Rest)", "reqid" => SystemInternals::getRequestId(), "action" => $action, "params" => $tmpParamLogArray, "response" => $err, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));



                    die(json_encode($err));

                }
            } else {
                $err = array(
                    "code" => 403,
                    "status" => "Forbidden",
                    "message" => "",
                    "data" => array(),
                    "flow" => CorebankFlow::getFlowArray(false)
                );
                if ($err['flow'] === null) {
                    unset($err['flow']);
                }
                if ($instance instanceof FR_RestController) {
                    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    header("Content-Type: application/json");

                    $log->warning("Forbidden (Rest)", array("message" => "Forbidden (Rest)", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $class, "action" => $action, "params" => $tmpParamLogArray, "response" => $err, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));

                    die(json_encode($err));
                } else {
                    $log->warning("Forbidden (Web)", array("message" => "Forbidden (Web)", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $class, "action" => $action, "params" => $tmpParamLogArray, "response" => $err, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                    header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
                    die("Ugyldig bruger");
                }
            }
        } catch
        (Exception $error) {
            $err = array(
                "code" => 400,
                "status" => "Bad Request",
                "message" => $error->getMessage(),
                "data" => array(),
                "flow" => CorebankFlow::getFlowArray(false)
            );
            if ($err['flow'] === null) {
                unset($err['flow']);
            }
            if ($instance instanceof FR_RestController) {
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                header("Content-Type: application/json");
                $log->warning("Bad Request (Rest)", array("message" => "Bad Request (Rest)", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $class, "action" => $action, "request" => $_REQUEST, "response" => $err, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));

                die(json_encode($err));
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                $log->warning("Bad Request (Web)", array("message" => "Bad Request (Web)", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $class, "action" => $action, "request" => $_REQUEST, "response" => $err, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
                die("FEJL: " . $error->getMessage());
            }
        }
    }
} else {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    $log->warning("Controller not found", array("message" => "Controller not found", "reqid" => SystemInternals::getRequestId(), "module" => $module, "controller" => $class, "action" => $action, "request" => $_REQUEST, "tuser" => Session::GetOrValue("userid", isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] : "no_session")));
    die("Controller not found.");
}

die();
