<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 8/10/2016
 * Time: 5:46 PM
 */

class Database {

    var $connection; // public variable for the database connection

    /**
     * Database constructor for PHP5.
     */
    function __construct(){
        $this->init(); // init the connection to the database
    }

    /**
     * init the class
     */
    private function init(){
        $this->connect();
    }

    /**
     * Establish the database connection
     */
    private function connect(){

        // define all the global variables
        global $message;

        $this->connection = mysqli_connect(DBURL,DBUSER,DBPASS,DBNAME,DBPORT);
        // Check for any connection errors
        if (mysqli_connect_errno()){
            $message->customKill("Database Connection Error", "Connection to the database failed: " . mysqli_connect_error() , "ubold");
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