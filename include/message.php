<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 7:38 PM
 */
class Message {

    var $msg; // store the error text and type
    var $error;

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

}