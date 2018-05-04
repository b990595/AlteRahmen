<?php

use Jblib\EventManager\SharedEventManagerFactory;
use Zend\EventManager\EventManager;

class MySqlDB
{

    const EVENT_COMMIT = 'commit';
    const EVENT_POST_COMMIT = 'commit.post';

    /**
     *
     * @var mysqli
     */
    private $conn = null;
    private $connectionData = null;
    private $mode = null;

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct($connectionString)
    {
        $connectionArray = json_decode($connectionString, true);
        if (is_array($connectionArray)) {
            $server = isset($connectionArray ['server']) ? $connectionArray ['server'] : FR_DB_HOST;
            $database = isset($connectionArray ['db']) ? $connectionArray ['db'] : FR_DEFAULT_DB;
            $user = isset($connectionArray ['user']) ? $connectionArray ['user'] : FR_DB_USER;
            $pass = isset($connectionArray ['pass']) ? $connectionArray ['pass'] : FR_DB_PASS;
            $port = isset($connectionArray ['port']) ? $connectionArray ['port'] : FR_DB_PORT;
            $charset = isset($connectionArray ['charset']) ? $connectionArray ['charset'] : FR_DB_CHARSET;
            $this->mode = isset($connectionArray ['mode']) ? $connectionArray ['mode'] : null;

            $connectionHash = sha1($server . $database . $user . $pass . $port . $charset);

            $this->connectionData = array(
                "connectionHash" => $connectionHash,
                "server" => $server,
                "database" => $database,
                "user" => $user,
                "pass" => $pass,
                "port" => $port,
                "charset" => $charset
            );
        }
        else {
            Throw new Exception("Unvalid connectionString");
        }
    }

    private function initConnection()
    {
        if (!$this->isConnected()) {
            if (is_array($this->connectionData)) {
                if (isset($GLOBALS ['__MySqlConnections'][$this->connectionData['connectionHash']])) {
                    /* @var $db $this */
                    $db = $GLOBALS ['__MySqlConnections'][$this->connectionData['connectionHash']];
                    $this->conn = $db->getMysqliConnection();
                    $this->eventManager = $db->getEventManager(false);
                }
                else {
                    $this->conn = new mysqli($this->connectionData['server'], $this->connectionData['user'],
                        $this->connectionData['pass'], $this->connectionData['database'],
                        $this->connectionData['port']);
                    $this->conn->set_charset($this->connectionData['charset']);
                    if ($this->mode) {
                        $this->conn->query("SET sql_mode='" . $this->conn->real_escape_string($this->mode) . "'");
                    }
                    $this->startTransaction();
                    $this->eventManager = new EventManager(SharedEventManagerFactory::getInstance(), [__CLASS__]);
                    $GLOBALS ['__MySqlConnections'][$this->connectionData['connectionHash']] = $this;
                }
            }
            else {
                throw new Exception("No connection data found");
            }

        }
    }

    public function getMysqliConnection()
    {
        return $this->conn;
    }

    private function startTransaction()
    {
        $this->conn->query("START TRANSACTION");
        $this->conn->query("SET autocommit=0");
    }

    public function commit()
    {
        $this->getEventManager()->trigger(self::EVENT_COMMIT);
        $this->sqlQuery("COMMIT");
        $this->getEventManager()->trigger(self::EVENT_POST_COMMIT);
    }

    public function close($commitTransaction = false)
    {
        if ($this->isConnected()) {
            if ($commitTransaction) {
                $this->commit();
            }
            $this->conn->close();
            $this->conn = null;
            $this->connectionData = null;
        }
    }

    public function isConnected()
    {
        if (is_object($this->conn)) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getInsertId()
    {
        if ($this->conn->insert_id > 0) {
            return $this->conn->insert_id;
        }
        else {
            return null;
        }
    }

    public function hasInsertId()
    {
        if ($this->conn->insert_id > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function __destruct()
    {
        $this->conn = null;
        $this->connectionData = null;
        $this->eventManager = null;
    }

    /**
     *
     * @param string $select
     * @param string $table
     * @param string $where
     * @param string $orderby
     * @param int    $limit
     * @param int    $offset
     * @return MySqlResult
     */
    public function getRows($select, $table, $where = "", $orderby = "", $limit = 0, $offset = 0)
    {
        $this->initConnection();

        $sql = "SELECT $select ";
        if (strstr($table, "`")) {
            $sql .= "FROM $table ";
        }
        else {
            $sql .= "FROM `$table` ";
        }
        if (trim($where) != "") {
            $sql .= "WHERE ($where) ";
        }
        if (trim($orderby) != "") {
            $sql .= "ORDER BY $orderby ";
        }

        if ($limit > 0) {
            $sql .= "LIMIT " . intval($limit) . " ";
        }
        if ($offset > 0) {
            $sql .= "OFFSET " . intval($offset) . " ";
        }

        $result = $this->conn->query($sql);

        return new MySqlResult($result);
    }

    /**
     *
     * @param string $select
     * @param string $table
     * @param string $where
     * @param string $orderby
     * @return MySqlResult
     */
    public function getFirstRow($select, $table, $where = "", $orderby = "")
    {
        $sql = "SELECT $select ";
        if (strstr($table, "`")) {
            $sql .= "FROM $table ";
        }
        else {
            $sql .= "FROM `$table` ";
        }
        if (trim($where) != "") {
            $sql .= "WHERE ($where) ";
        }
        if (trim($orderby) != "") {
            $sql .= "ORDER BY $orderby ";
        }
        $sql .= "LIMIT 1";

        $this->initConnection();
        $result = $this->conn->query($sql);

        if ($result) {
            return new MySqlResult($result, $result->fetch_array(MYSQLI_ASSOC));
        }
        else {
            return new MySqlResult($result);
        }
    }

    /**
     *
     * @param string $table
     * @return MySqlResult
     */
    public function getColumnsData($table)
    {

        $sql = "SHOW COLUMNS ";
        if (strstr($table, "`")) {
            $sql = $sql . "FROM $table ";
        }
        else {
            $sql = $sql . "FROM `$table` ";
        }

        $this->initConnection();
        $result = $this->conn->query($sql);

        return new MySqlResult($result);
    }

    /**
     *
     * @param string $table
     * @return MySqlResult
     */
    public function getColumnNames($table)
    {
        $cols = $this->getColumnsData($table);
        $array = array();
        if ($cols->hasData()) {
            $colsData = $cols->getData();
            foreach ($colsData as $c) {
                $array[] = $c['Field'];
            }
        }

        return new MySqlResult($cols, $array);
    }

    /**
     *
     * @param string $where
     * @param string $table
     * @return int
     */
    public function countRows($where, $table)
    {

        $sql = "SELECT COUNT(*) AS num ";
        $sql .= "FROM `$table` ";
        if (trim($where) != "") {
            $sql .= "WHERE ($where)";
        }

        $this->initConnection();
        $result = $this->conn->query($sql);
        if ($result == false) {
            return 0;
        }
        else {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            return (int)$row['num'];
        }
    }

    /**
     *
     * @param string $sql
     * @return MySqlResult
     */
    public function sqlQuery($sql)
    {
        $this->initConnection();

        return new MySqlResult($this->conn->query($sql));
    }

    /**
     *
     * @param string $where
     * @param string $table
     * @return MySqlResult
     */
    public function deleteRow($where, $table)
    {
        $sql = "DELETE FROM `" . str_replace("`", "", $table) . "` WHERE $where";
        $this->initConnection();

        return new MySqlResult($this->conn->query($sql));
    }

    /**
     *
     * @param string $string
     * @return string
     */
    public function escapeString($string)
    {
        $this->initConnection();

        return $this->conn->real_escape_string($string);
    }

    public function escapeKey($key)
    {
        $matches = array();
        preg_match('/^([a-zA-Z0-9\-_]+)$/', $key, $matches);
        if (count($matches) > 0 && $matches[0] == $key) {
            return $matches[0];
        }
        else {
            throw new Exception("Invalid SQL Key");
        }
    }

    private function insertRow($array, $table)
    {
        $keys = array_keys($array);
        if (is_array($keys)) {
            $sql = "INSERT";
            $sql.= " INTO `" . $table . "`(";

            foreach ($keys as $k) {
                $sql = $sql . "`$k`,";
            }
            $len = mb_strlen($sql);
            $sql = mb_substr($sql, 0, $len - 1);
            $sql = $sql . ") VALUES (";

            $this->initConnection();
            foreach ($keys as $k) {

                if ($array[$k] === null){
                    $sql = $sql . "null,";
                }else{
                    $sql = $sql . "'" . $this->escapeString($array[$k]) . "',";
                }
            }

            $len = mb_strlen($sql);
            $sql = mb_substr($sql, 0, $len - 1);
            $sql = $sql . ")";

            $result = $this->conn->query($sql);

            if ($result) {
                return new MySqlResult($result, array("id" => $this->conn->insert_id));
            }
            else {
                return new MySqlResult($result);
            }
        }
        else {
            throw new Exception("Array expected");
        }
    }

    /**
     *
     * @param string $where
     * @param string $table
     * @param array  $array
     * @return MySqlResult
     * @throws Exception
     */
    public function updateRow($where, $table, $array)
    {
        $keys = array_keys($array);
        if (is_array($keys)) {
            $sql = "UPDATE `" . $table . "` SET ";

            $this->initConnection();
            foreach ($keys as $k) {
                if ($array[$k] === null){
                    $sql = $sql . "`" . $k . "`=null,";
                }else{
                    $sql = $sql . "`" . $k . "`='" . $this->conn->real_escape_string($array[$k]) . "',";
                }
            }

            $len = mb_strlen($sql);
            $sql = mb_substr($sql, 0, $len - 1);
            $sql = $sql . " WHERE $where";

            return new MySqlResult($this->conn->query($sql));
        }
        else {
            throw new Exception("Array expected");
        }
    }

    /**
     *
     * @param array  $array
     * @param string $table
     * @return MySqlResult
     * @throws Exception
     */
    public function saveRow($array, $table)
    {
        $proved_row = array();
        $fields = array();

        // Row keys
        if (is_array($array)) {
            $keys = array_keys($array);
        }
        else {
            throw new Exception("Array expected");
        }

        $columns = $this->getColumnsData($table);
        if ($columns->hasData()) {
            $colData = $columns->getData();
            foreach ($colData as $c) {
                $fields[] = $c['Field'];
            }
        }
        else {
            $tmp = new MySqlResult(false);
            $tmp->setErrorText("No columns in table [" . $table . "]");

            return $tmp;
        }

        // Danner array med de nøgler, der findes i databasen
        foreach ($keys as $k) {
            if (in_array($k, $fields)) {
                $proved_row[$k] = $array[$k];
            }
        }

        // Indsætter værdier i databasen
        return $this->insertRow($proved_row, $table);
    }

    /**
     * @param bool $init
     * @return EventManager
     */
    public function getEventManager($init = true): EventManager
    {
        if ($init) {
            $this->initConnection();
        }
        return $this->eventManager;
    }
}
