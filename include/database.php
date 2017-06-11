<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 8/10/2016
 * Time: 5:46 PM
 */
namespace ALS\Database;
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
     * @return string
     */
    function secureInput($input)
    {
        return $this->escapeString(trim(strip_tags(addslashes($input))));
    }

    function hashPassword($text){
        return password_hash($text, PASSWORD_DEFAULT, ['cost' => 12]);
    }

}