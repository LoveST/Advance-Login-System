<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 8/10/2016
 * Time: 5:46 PM
 */

class Database
{

    var $connection; // public variable for the database connection
    private $message;

    /**
     * Database constructor for PHP4
     */
    function Database(){
        $this->__construct();
    }

    /**
     * init the class
     * @param $message
     */
    function init($message){
        $this->message = $message;
        $this->connect();
    }

    /**
     * Database constructor for PHP5.
     */
    function __construct(){

    }

    /**
     * Establish the database connection
     */
    function connect(){
        $this->connection = mysqli_connect(DBURL,DBUSER,DBPASS,DBNAME,DBPORT);
        // Check for any connection errors
        if (mysqli_connect_errno()){
            $this->message->customKill("Database Connection Error", "Connection to the database failed: " . mysqli_connect_error() , "ubold");
        }

    }

    /**
     * Prepare any given string from injections
     * @param $string
     * @return string
     */
    function escapeString($string){
        return mysqli_real_escape_string($this->connection, $string);
    }

}

$database = new Database();