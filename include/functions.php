<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 2:51 PM
 */
class Functions{

    private $database; // instance of the Database class.
    private $message; // instance of the Message class.
    private $userData; // instance of the user class.
    private $settings; // instance of the settings class.
    private $mail; // instance of the mail class.

    /**
     * init the class
     * @param $database
     * @param $messageClass
     * @param $userDataClass
     * @param $mail
     * @param $settings
     */
    function init($database, $messageClass, $userDataClass,$mail, $settings){
        $this->database = $database;
        $this->message = $messageClass;
        $this->userData = $userDataClass;
        $this->mail = $mail;
        $this->settings = $settings;
    }

    /**
     * encrypt a string using the site key
     * @param $q
     * @return string
     */
    function encryptIt( $q ) {
        $cryptKey  = $this->settings->SECRET_CODE;
        $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        return( $qEncoded );
    }

    /**
     * decrypt a string using the site key
     * @param $q
     * @return string
     */
    function decryptIt( $q ) {
        $cryptKey  = $this->settings->SECRET_CODE;
        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
    }

}