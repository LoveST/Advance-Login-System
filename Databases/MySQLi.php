<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/10/2017
 * Time: 6:43 PM
 */

namespace ALS\Databases;

use ALS\Message;

class MySQLi extends _dbConnection
{

    /**
     * MySQLi constructor.
     */
    function __construct()
    {
        // init the parent class
        parent::__construct();
    }

    /**
     * connect to the database
     * @param String $dbName
     * @return \mysqli
     */
    function connect($dbName = null)
    {

        // init the required globals
        global $message;

        // check if dbName is null
        if (is_null($dbName)) {
            $dbName = DBNAME;
        }

        $connection = new \mysqli(DBURL, DBUSER, DBPASS, $dbName, DBPORT);

        // Check for any connection errors
        if ($connection->connect_error) {
            $message->customKill("Database Connection Error", "Connection to the database failed: " . $connection->connect_error, "default");
        }

        // return the connection
        return $connection;
    }

    /**
     * get the results from an sql query
     * @param $sqlRequest
     * @param array $parameters
     * @param string $types
     * @return bool|\mysqli_result
     */
    function getResults($sqlRequest, $parameters = null, $types = null)
    {

        // define all the global variables
        global $database;

        // check for any errors
        if (!$result = $this->getConnection()->prepare($sqlRequest)) {
            $database->setError(mysqli_error($this->getConnection()));
        }

        // bind the parameters
        if ($types && $parameters) {

            // main parameters holder
            $params[] = $types;
            for ($i = 0; $i < count($parameters); $i++) {
                $param = 'bind' . ($i);
                $$param = $parameters[$i];
                $params[] = &$$param;
            }

            call_user_func_array(array($result, "bind_param"), $params);
        }

        // execute the query
        $result->execute();

        // get the results
        $results = $result->get_result();

        // return the results
        return $results;
    }

    /**
     * get the total number of effected rows
     * Using MySQLi
     * @param $results
     * @return int
     */
    public function getNumRows($results)
    {
        return $results->num_rows;
    }

    /**
     * @param \mysqli_result $results
     * @return mixed
     */
    public function getRow($results)
    {
        return $results->fetch_assoc();
    }

    /**
     * @param \mysqli_result $results
     * @return array
     */
    public function getRows($results)
    {
        return $results->fetch_array();
    }

    /**
     * get the connection from the database
     * @return \mysqli
     */
    private function getConnection()
    {
        global $database;
        return $database->connection;
    }

}