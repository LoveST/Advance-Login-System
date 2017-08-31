<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 8/10/2016
 * Time: 5:46 PM
 */

namespace ALS\Database;

use ALS\Message\Message;

class Database
{

    var $connection; // public variable for the database connection

    /**
     * Database constructor for PHP5.
     */
    function __construct()
    {
        $this->connect(); // init the connection to the database
    }

    /**
     * Establish the database connection
     */
    private function connect()
    {

        // define all the global variables
        global $message;

        $this->connection = mysqli_connect(DBURL, DBUSER, DBPASS, DBNAME, DBPORT);
        // Check for any connection errors
        if (mysqli_connect_errno()) {
            $message->customKill("Database Connection Error", "Connection to the database failed: " . mysqli_connect_error(), "default");
        }

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
        if (!$result = mysqli_query($this->connection, $sqlRequest)) {
            $message->setError("SQL query error : " . mysqli_error($this->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        // if no error then return the results
        return $result;
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
        if(!$isSqlRespond) {
            $results = $this->getQueryResults($sqlRequest);
        } else {
            $results = $sqlRequest;
        }

        // get the total rows effected
        $numRows = mysqli_num_rows($results);

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
        if(!$isSqlRespond) {
            $results = $this->getQueryResults($sqlRequest);
        } else {
            $results = $sqlRequest;
        }

        // check if results is not empty
        if (!$results) {
            return false;
        }

        // get the effected rows
        $row = mysqli_fetch_assoc($results);

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
        if(!$isSqlRespond) {
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
        while ($row = mysqli_fetch_array($results)) {
            $rows[] = $row;
        }

        // return the effected rows
        return $rows;
    }

}