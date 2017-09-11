<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 8/10/2016
 * Time: 5:46 PM
 */

namespace ALS;

class Database
{

    var $connection; // public variable for the database connection
    public $_CONNECTION_TYPE = "";
    var $_DBConnections = array();
    private $_dbError = false;
    private $_dbErrorMSG = "";

    /**
     * Database constructor for PHP5.
     */
    function __construct()
    {
        // define all the global variables
        global $message;

        // init the supported Database Drivers
        $this->_DBConnections = array("MySQLi", "PDO");

        // check the connection type supplied if valid
        $this->checkConnectionType();

        // connect to the Database
        $this->connection = $this->_CONNECTION_TYPE->connect();
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
            $classPath = __DIR__ . $this->getSubLine() . "databases" . $this->getSubLine() . CONNECTION_TYPE . ".php";

            // check if database connection class exists
            if (!is_readable($classPath)) {
                $message->setError("Database Connection Class Not Found", Message::Fatal);
            }

            include_once $classPath;

            $class = "ALS\\Databases\\" . CONNECTION_TYPE;
            $object = new $class();

            // store the object
            $this->_CONNECTION_TYPE = $object;
        }
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
        $this->_dbError = true;
        $this->_dbErrorMSG = $dbErrorMSG;
    }

    /**
     * Prepare any given string from injections
     * @param $string
     * @return string
     */
    function escapeString($string)
    {
        if($this->_CONNECTION_TYPE == "MySQLi"){
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
    function getQueryResults($sqlRequest, $types = null, $parameters = array())
    {
        return $this->_CONNECTION_TYPE->getResults($sqlRequest, $types, $parameters);
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
    function getQueryNumRows($sqlRequest, $isSqlRespond = false, $types = null, $parameters = null)
    {

        // run the sql query and get the results
        if (!$isSqlRespond) {
            $results = $this->getQueryResults($sqlRequest, $types, $parameters);
        } else {
            $results = $sqlRequest;
        }

        // get the total rows effected
        $numRows = $this->_CONNECTION_TYPE->getNumRows($results);

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
        $row = $this->_CONNECTION_TYPE->getRow($results);

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
        while ($row = $this->_CONNECTION_TYPE->getRows($results)) {
            $rows[] = $row;
        }

        // return the effected rows
        return $rows;
    }

    /**
     * get the required sub line for the current server's os
     * @return string
     */
    function getSubLine()
    {
        $sub = "";

        // check the servers current OS
        if (PHP_OS == "Linux") {
            $sub = "/";
        } else {
            $sub = "\\";
        }

        return $sub;
    }
}