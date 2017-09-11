<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/10/2017
 * Time: 6:43 PM
 */

namespace ALS\Databases;
use ALS\Message;

class MySQLi
{

    function __construct()
    {
        // init the required globals
        global $message;

        // init the complete list of functions
        $functions = array("getResults", "getNumRows", "getRow", "getRows");

        // loop throw each function in the current connection type and check for availability
        for ($i = 0; $i < count($functions); $i++) {

            // get the function
            $function = $functions[$i];

            // check if doesn't exist
            if (!method_exists($this, $function)) {
                $message->setError("Missing Function In Database Connection : function(" . $function . ")", Message::Fatal);
            }
        }

    }

    /**
     * connect to the database
     * @return \mysqli
     */
    function connect(){

        // init the required globals
        global $message;

        $connection = new \mysqli(DBURL, DBUSER, DBPASS, DBNAME, DBPORT);

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
     * @return bool|\mysqli_result
     */
    function getResults($sqlRequest, $parameters = array())
    {

        // define all the global variables
        global $database;

        // check for any errors
        if (!$result = $database->connection->prepare($sqlRequest)) {
            $database->setError(mysqli_error($database->connection));
        }

        // bind the parameters
        if (!empty($parameters)) {

            // main parameters holder
            $params = array();
            $types = "";
            foreach ($parameters as $type){
                $types .= $types;
            }

            // add the types to the first byte of the array
            $params[0] = $types;

            // add the parameters
            foreach($parameters as $type => $value){
                $params[] = $value;
            }

            call_user_func_array(array($result, "bind_param"), &$params);
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

}