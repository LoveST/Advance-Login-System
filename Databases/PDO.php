<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 9/11/2017
 * Time: 6:28 AM
 */

namespace ALS\Databases;

use Als\Message;

class PDO
{

    /**
     * PDO constructor.
     */
    function __construct()
    {
        // init the required globals
        global $message, $database;

        // init the complete list of functions
        $functions = array("getResults", "getNumRows", "getRow", "getRows");

        // loop throw each function in the current connection type and check for availability
        for ($i = 0; $i < count($functions); $i++) {

            // get the function
            $function = $functions[$i];

            // check if doesn't exist
            if (!method_exists($this, $function)) {
                $database->setError("Missing Function In Database Connection : function(" . $function . ")");
            }
        }
    }

    /**
     * connect to the database
     */
    function connect()
    {
        // init the required globals
        global $message, $database;

        try {
            $connection = new \PDO("mysql:host=" . DBURL . ";port=" . DBPORT . ";dbname=" . DBNAME, DBUSER, DBPASS);
            // set the PDO error mode to exception
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            var_dump($database->setError($this->getErrorMSG($e)));
            $database->setError("Database Connection Failed: " . $this->getErrorMSG($e));
            return false;
        }

        return $connection;
    }

    /**
     * get the results from an sql query
     * @param $sqlRequest
     * @param array $parameters
     * @param string $types
     * @return bool|\PDOStatement
     */
    function getResults($sqlRequest, $parameters = null, $types = null)
    {

        // define all the global variables
        global $database, $message;

        // try and catch for any errors
        try {

            // check for any errors
            if (!$result = $this->getConnection()->prepare($sqlRequest)) {
                $database->setError(mysqli_error($database->connection));
            }

            // bind the parameters
            if ($types && $parameters) {

                // get the parameters
                for ($i = 0; $i < count($parameters); $i++) {
                    $result->bindValue($types[$i], $parameters[$i]);
                }
            }

            // execute the query
            $result->execute();
        } catch (\PDOException $e) {
            $database->setError("SQL Error (" . $this->getErrorMSG($e) . ")");
        }

        // return the results
        return $result;
    }

    /**
     * get the total number of effected rows
     * Using PDO
     * @param \PDOStatement $results
     * @return int
     */
    public function getNumRows($results)
    {
        return $results->rowCount();
    }

    /**
     * @param \PDOStatement $results
     * @return mixed
     */
    public function getRow($results)
    {
        return $results->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * get the total rows effected
     * Using PDO
     * @param \PDOStatement $results
     * @return array
     */
    public function getRows($results)
    {
        return $results->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get the database error message
     * @param \PDOException $err
     * @return string
     */
    private function getErrorMSG($err)
    {
        $msg = "";
        switch ($err->getCode()) {
            case 1045;
                $msg = "Database Access Denied";
                break;
            case 1049;
                $msg = "Database Not Found";
                break;
            case 2002;
                $msg = "Connection Timeout";
                break;
            case 42000;
                $msg = "Invalid Syntax";
                break;
            default;
                $msg = "Unhandled Error (" . $err->getMessage() . ")";
                break;
        }
        return $msg;
    }

    /**
     * get the connection from the database
     * @return \PDO
     */
    private function getConnection()
    {
        global $database;
        return $database->connection;
    }
}