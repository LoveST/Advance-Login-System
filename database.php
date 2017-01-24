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
            $this->message->setError("Connection to the database failed: " . mysqli_connect_error() , Message::Fatal, __FILE__ , __LINE__);
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

    /**
     * encrypt a string using the site key
     * @param $q
     * @return string
     */
    function encryptIt( $q ) {
        $cryptKey  = SITE_KEY;
        $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        return( $qEncoded );
    }

    /**
     * decrypt a string using the site key
     * @param $q
     * @return string
     */
    function decryptIt( $q ) {
        $cryptKey  = SITE_KEY;
        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
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