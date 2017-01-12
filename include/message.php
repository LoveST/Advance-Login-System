<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 7:38 PM
 */

if(count(get_included_files()) ==1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class Message {

    var $msg; // store the error text and type
    const __default = self::Warning; // set the default error type to a warning.
    const Fatal = 1; // the error message is a fatal error.
    const Warning = 2; // the error message is just a warning.

    /**
     * Message constructor for PHP4
     */
    function Message(){
        $this->__construct();
    }

    /**
     * Message constructor for PHP5.
     */
    function __construct(){
        // check if any error has been posted to the session and if true then pull it and clear the session.
        if($_SESSION["error_msg"] != "" && $_SESSION["error_type"] != ""){
            $this->msg[0] = $_SESSION["error_msg"];
            $this->msg[1] = $_SESSION["error_type"];

            unset($_SESSION["error_msg"]);
            unset($_SESSION["error_type"]);
        }
    }

    /**
     * Set an error message to be displayed to the user
     * @param $msg
     * @param $type
     */
    function setError($msg, $type){
        if(!empty($msg)){
            $this->msg[0] = $msg;
            $this->msg[1] = $type;
        }
    }

    /**
     * check to see if any error has occurred
     */
    function anyError(){
        return !empty($this->msg);
    }

    /**
     * return the current error message
     * @return string
     */
    function getError(){
        return $this->msg[0];
    }

    /**
     * return the current error type
     * @return integer
     */
    function getErrorType(){
        return $this->msg[1];
    }

    /**
     * read the actual meaning of the occurred error
     * @param $type
     * @return string
     */
    function readErrorType($type){
        $error = "";

        switch($type){
            case "1";
                $error = "Fatal error";
                break;
            case "2";
                $error = "Warning";
                break;
        }
        return $error;
    }

}

$message = new Message();