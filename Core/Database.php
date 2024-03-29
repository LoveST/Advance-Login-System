<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 8/10/2016
 * Time: 5:46 PM
 */

namespace ALS;

use ALS\Databases\_dbConnection;

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class Database
{

    var $connection; // public variable for the database connection
    public $_CONNECTION_TYPE;
    var $_DBConnections;
    private $_dbError = false;
    private $_dbErrorMSG = "";
    var $_errorKILL;

    public function _init($dieIfError = true)
    {
        // set the error handler
        $this->anyErrorKill($dieIfError);

        // get the parent connection class
        include FRAMEWORK_PATH . $this->getSubLine() . "Databases" . $this->getSubLine() . "_dbConnection.php";

        // init the supported Database Drivers
        $this->_DBConnections = array("MySQLi", "PDO");

        // check the connection type supplied if valid
        $this->checkConnectionType();
    }

    final function connectToDB($dbName = null)
    {
        // connect to the Database
        $this->connection = $this->getConnectionType()->connect($dbName);
    }

    /**
     * check for database connection type
     */
    private function checkConnectionType()
    {
        // define all the global variables
        global $message;

        // check if current connection type exists
        if (in_array(CONNECTION_TYPE, $this->_DBConnections)) {

            // get the connection class path
            $classPath = FRAMEWORK_PATH . $this->getSubLine() . "Databases" . $this->getSubLine() . CONNECTION_TYPE . ".php";

            // check if database connection class exists
            if (!is_readable($classPath)) {
                $message->setError("Database Connection Class Not Found", Message::Fatal);
            }

            // include the required database file
            include_once $classPath;

            // create an object from the connection file
            $class = "ALS\\Databases\\" . CONNECTION_TYPE;
            $object = new $class();

            // store the object
            $this->_CONNECTION_TYPE = $object;
        }
    }

    /**
     * Get the required database connection type class
     * @return _dbConnection
     */
    public function getConnectionType()
    {
        return $this->_CONNECTION_TYPE;
    }

    /**
     * Set the option to kill the script if any database error were
     * to be found while connecting to SQL
     * @param bool $condition
     */
    function anyErrorKill($condition = true)
    {
        $this->_errorKILL = $condition;
    }

    /**
     * @return bool
     */
    public function anyError()
    {
        return $this->_dbError;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->_dbErrorMSG;
    }

    /**
     * @param string $dbErrorMSG
     */
    public function setError($dbErrorMSG)
    {
        // init the required globals
        global $message;

        if ($this->_errorKILL) {
            $message->setError($dbErrorMSG, Message::Fatal);
        } else {
            $this->_dbError = true;
            $this->_dbErrorMSG = $dbErrorMSG;
        }
    }

    /**
     * Prepare any given string from injections
     * @param $string
     * @return string
     */
    function escapeString($string)
    {
        if ($this->_CONNECTION_TYPE == "MySQLi") {
            return mysqli_real_escape_string($this->connection, $string);
        } else {
            return $string;
        }
    }

    /**
     * Protect your input from all kinds of injections like HTML,JS,SQL
     * @param $input
     * @return mixed
     */
    function secureInput($input)
    {

        // check if array has been given
        if (is_array($input)) {
            return $input;
        }

        return $this->escapeString(trim(strip_tags(addslashes($input))));
    }

    /**
     * hash a text for ultimate security
     * @param $text
     * @return bool|string
     */
    function hashPassword($text)
    {
        return password_hash($text, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    /**
     * get the results from an sql query
     * @param $sqlRequest
     * @param array $parameters
     * @param string $types
     * @return bool|\mysqli_result
     */
    function getQueryResults($sqlRequest, $parameters = null, $types = null)
    {
        return $this->getConnectionType()->getResults($sqlRequest, $parameters, $types);
    }

    /**
     * execute a direct MySQLi function to get the total
     * number of rows effected in a single query
     * @param string $sqlRequest
     * @param bool $isSqlRespond
     * @param array $parameters
     * @param string $types
     * @return int
     */
    function getQueryNumRows($sqlRequest, $isSqlRespond = false, $parameters = null, $types = null)
    {

        // run the sql query and get the results
        if (!$isSqlRespond) {
            $results = $this->getQueryResults($sqlRequest, $parameters, $types);
        } else {
            $results = $sqlRequest;
        }

        // get the total rows effected
        $numRows = $this->getConnectionType()->getNumRows($results);

        // return the total number of effected rows
        return $numRows;
    }

    /**
     * get a query results after submitting an sql request
     * @param string $sqlRequest
     * @param bool $isSqlRespond
     * @return bool|array
     */
    function getQueryEffectedRow($sqlRequest, $isSqlRespond = false)
    {

        // run the sql query and get the results
        if (!$isSqlRespond) {
            $results = $this->getQueryResults($sqlRequest);
        } else {
            $results = $sqlRequest;
        }

        // check if results is not empty
        if (!$results) {
            return false;
        }

        // get the effected rows
        $row = $this->getConnectionType()->getRow($results);

        // return the effected rows
        return $row;
    }

    /**
     * get a query results after submitting an sql request
     * @param string $sqlRequest
     * @param bool $isSqlRespond
     * @return bool|array
     */
    function getQueryEffectedRows($sqlRequest, $isSqlRespond = false)
    {

        // run the sql query and get the results
        if (!$isSqlRespond) {
            $results = $this->getQueryResults($sqlRequest);
        } else {
            $results = $sqlRequest;
        }

        // check if results is not empty
        if (!$results) {
            return false;
        }

        // init the array to hold the effected rows
        $rows = [];

        // get the effected rows
        while ($row = $this->getConnectionType()->getRows($results)) {
            $rows[] = $row;
        }

        // return the effected rows
        return $rows;
    }

    function getTableNames()
    {

        // prepare the required statement
        $sql = "show tables";

        // execute the query
        $results = $this->getQueryResults($sql);

        // check if any results
        if ($this->getQueryNumRows($results, true) <= 0) {
            $this->setError("Database callback: No Results");
            return false;
        }

        // grab the required array
        $rows = $this->getQueryEffectedRows($results, true);

        // loop throw each element and store the required data
        $names = array();
        foreach ($rows as $table) {
            $names[] = reset($table);
        }

        // return the array
        return $names;
    }

    function getTableColumns($tableName)
    {

        // secure the table name
        $tableName = $this->secureInput($tableName);

        // prepare the required statement
        $sql = "SHOW COLUMNS FROM " . $tableName;

        // execute the query
        $results = $this->getQueryResults($sql);

        // return the results
        return $this->getQueryEffectedRows($results, true);
    }

    function countTableFields($tableName)
    {

        // secure the table name
        $tableName = $this->secureInput($tableName);

        // prepare the required statement
        $sql = "SHOW COLUMNS FROM " . $tableName;

        // execute the query
        $results = $this->getQueryResults($sql);

        // return the results
        return count($this->getQueryEffectedRows($results, true));
    }

    /**
     * get the required sub line for the current server's os
     * @return string
     */
    function getSubLine()
    {
        // check the servers current OS
        if (PHP_OS == "Linux") {
            $sub = "/";
        } else {
            $sub = "\\";
        }

        return $sub;
    }
}

$database = new Database();