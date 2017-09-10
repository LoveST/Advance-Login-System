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
    private $connectionTypes = array();
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
        $this->_DBConnections = array("MySQL", "MySQLi", "PDO");

        // check the connection type supplied if valid
        $this->checkConnectionType();

        // connect to the Database
        switch ($this->_CONNECTION_TYPE) {
            case "MySQLi";

                $this->connection = new \mysqli(DBURL, DBUSER, DBPASS, DBNAME, DBPORT);

                // Check for any connection errors
                if ($this->connection->connect_error) {
                    $message->customKill("Database Connection Error", "Connection to the database failed: " . $this->connection->connect_error, "default");
                }

                break;
            default;

                $this->connection = new \mysqli(DBURL, DBUSER, DBPASS, DBNAME, DBPORT);

                // Check for any connection errors
                if ($this->connection->connect_error) {
                    $message->customKill("Database Connection Error", "Connection to the database failed: " . $this->connection->connect_error, "default");
                }

                break;
        }

        // setup the connection types
        $this->setupConnectionTypes();
    }

    /**
     * check for database connection type
     */
    private function checkConnectionType()
    {

        // check if current connection type exists
        if (in_array(CONNECTION_TYPE, $this->_DBConnections)) {
            $this->_CONNECTION_TYPE = CONNECTION_TYPE;
        } else {
            $this->_CONNECTION_TYPE = "MySQLi";
        }

    }

    /**
     * Setup the supported database connections
     */
    private function setupConnectionTypes()
    {

        // put every single connection type in an array

        /**
         * MySQL Driver Instances
         */
        $this->connectionTypes["MySQL"] = array("getMySQLNumRows", "getMySQLRow", "getMySQLRows");

        /**
         * MySQLi Driver Instances
         * Requirements : "mysqlnd" or "nd_mysqli" driver mst be installed on the server
         */
        $this->connectionTypes["MySQLi"] = array("getMySQLiNumRows", "getMySQLiRow", "getMySQLiRows");

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
    private function setError($dbErrorMSG)
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
        return mysqli_real_escape_string($this->connection, $string);
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
     * @return bool|\mysqli_result
     */
    function getQueryResults($sqlRequest)
    {

        // define all the global variables
        global $message;

        // check for any errors
        if (!$result = $this->connection->prepare($sqlRequest)) {
            $this->setError(mysqli_error($this->connection));
        }

        // bind the parameters
        // TO_DO

        // execute the query
        $result->execute();

        // get the results
        $results = $result->get_result();

        // return the results
        return $results;
    }

    /**
     * execute a direct MySQLi function to get the total
     * number of rows effected in a single query
     * @param string $sqlRequest
     * @param bool $isSqlRespond
     * @return int
     */
    function getQueryNumRows($sqlRequest, $isSqlRespond = false)
    {

        // run the sql query and get the results
        if (!$isSqlRespond) {
            $results = $this->getQueryResults($sqlRequest);
        } else {
            $results = $sqlRequest;
        }

        // get the total rows effected
        $numRows = $this->getNumRows($results);

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
        $row = $this->getRow($results);

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
        while ($row = $this->getAllRows($results)) {
            $rows[] = $row;
        }

        // return the effected rows
        return $rows;
    }

    /**
     * get the number of rows effected in a certain database connection
     * @param $results
     * @return int
     */
    private function getNumRows($results)
    {
        // init the globals
        global $message;

        // check if function exists
        $function = $this->connectionTypes[$this->_CONNECTION_TYPE][0];

        if (method_exists($this, $function)) {

            // call in the method and return the value
            return call_user_func(array($this, $function), $results);
        } else {
            $message->setError("Database Connection Type Error", Message::Fatal);
        }
    }

    /**
     * get the effected row in a certain database connection
     * @param $results
     * @return mixed
     */
    private function getRow($results)
    {
        // init the globals
        global $message;

        // check if function exists
        $function = $this->connectionTypes[$this->_CONNECTION_TYPE][1];

        if (method_exists($this, $function)) {

            // call in the method and return the value
            return call_user_func(array($this, $function), $results);
        } else {
            $message->setError("Database Connection Type Error", Message::Fatal);
        }
    }

    /**
     * get all the effected rows in a certain database connection
     * @param $results
     * @return array
     */
    private function getAllRows($results)
    {
        // init the globals
        global $message;

        // check if function exists
        $function = $this->connectionTypes[$this->_CONNECTION_TYPE][2];

        if (method_exists($this, $function)) {

            // call in the method and return the value
            return call_user_func(array($this, $function), $results);
        } else {
            $message->setError("Database Connection Type Error", Message::Fatal);
        }
    }

    /**
     * get the total number of effected rows
     * Using MySQLi
     * @param $results
     * @return int
     */
    private function getMySQLiNumRows($results)
    {
        return $results->num_rows;
    }

    /**
     * @param \mysqli_result $results
     * @return mixed
     */
    private function getMySQLiRow($results)
    {
        return $results->fetch_assoc();
    }

    /**
     * @param \mysqli_result $results
     * @return array
     */
    private function getMYSQLiRows($results)
    {
        return $results->fetch_array();
    }

}