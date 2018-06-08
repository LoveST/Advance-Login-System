<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/5/2018
 * Time: 3:56 PM
 */

namespace ALS\Databases;

use ALS\Message;

class _dbConnection
{

    /**
     * MySQLi constructor.
     */
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
     * @param String $dbName
     * @return \mysqli
     */
    function connect($dbName = null){}

    /**
     * get the results from an sql query
     * @param $sqlRequest
     * @param array $parameters
     * @param string $types
     * @return bool|\mysqli_result
     */
    function getResults($sqlRequest, $parameters = null, $types = null){}

    /**
     * get the total number of effected rows
     * Using MySQLi
     * @param $results
     * @return int
     */
    public function getNumRows($results){}

    /**
     * @param \mysqli_result $results
     * @return mixed
     */
    public function getRow($results){}

    /**
     * @param \mysqli_result $results
     * @return array
     */
    public function getRows($results){}

    /**
     * get the connection from the database
     * @return \mysqli
     */
    private function getConnection(){}

}