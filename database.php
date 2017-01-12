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

    /**
     * Database constructor for PHP4
     */
    function Database(){
        $this->__construct();
    }

    /**
     * Database constructor for PHP5.
     */
    function __construct(){
        $this->connect();
    }

    /**
     * Establish the database connection
     */
    function connect(){
        try {
            $this->connection = new PDO('mysql:host=' . DBURL . ';dbname=' . DBNAME . ';charset=utf8mb4', DBUSER, DBPASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch(PDOException $ex){
            die('Unable to connect : ' . $ex->getMessage());
        }
    }

    /**
     * Prepare any given string from injections
     * @param $string
     * @return string
     */
    function escapeString($string){
        return mysql_escape_string($string);
    }

    /**
     * Secure a password by hashing it to random string generated by the strings given from $pass + $nonce + SITE_KEY
     * @param $pass
     * @param $nonce
     * @return string
     */
    function securePassword($pass, $nonce){
        $secure = hash_hmac('sha512', $pass . $nonce, SITE_KEY);

        return $secure;
    }

}
$database = new Database();