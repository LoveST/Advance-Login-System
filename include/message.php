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
    const Error = 3; // the error message is for the client instead of the administration

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

    }

    /**
     * init the class
     */
    function init(){
        // check if any error has been posted to the session and if true then pull it and clear the session.
        if($_SESSION["error"] != ""){
            $this->msg = $_SESSION["error"];
            unset($_SESSION["error"]);
        }
    }

    /**
     * Set an error message to be displayed to the user
     * @param $msg
     * @param $type
     * @param string $fileName / include the file name that the error has occurred in
     * @param int $lineNumber / include the line number that the error has occurred in
     */
    function setError($msg, $type, $fileName="", $lineNumber=0){
        if(!empty($msg)){ // check if the message is not empty
            if($type == 3){ // check if it's a user error
                $array = array(
                    "msg" => $msg,
                    "type" => $type,
                );
                $this->msg = $array;
                $_SESSION["error"][] = $array;
                return;
            } else {
                $array = array(
                    "msg" => $msg,
                    "type" => $type,
                    "fileName" => $fileName,
                    "lineNumber" => $lineNumber,
                );
                $this->msg = $array;
                $_SESSION["error"][] = $array;
            }
        }
    }

    /**
     * check to see if any error has occurred
     */
    function anyError(){
        return !empty($this->msg) && $this->msg["msg"] != "";
    }

    /**
     * @param int $type // 0 = (default) all errors , 1 = only fatal errors , 2 = only warnings
     * @return mixed
     */
    function getError($type=0){
        $data = $_SESSION["error"];
        if(empty($data) || $data[0]['msg'] == "") return "";

        if($type == 0){ // get all the errors
            foreach($data as $key => $value){
                if($key != 0) echo "<br>";
                echo '<b>' . $this->readErrorType($value['type']) . ': </b>' . $value['msg'] . ' (<b> '.$value['fileName'].'</b> on Line <b>'.$value['lineNumber'].'</b> )';
                if($value["type"] == 1) die;
            }
        } else if($type == 2){ // get all the errors with a type value of 2
            foreach($data as $key => $value){
                if($value['type'] == 2) {
                    if ($key != 0) echo "<br>";
                    echo '<b>' . $this->readErrorType($value['type']) . ': </b>' . $value['msg'] . ' (<b> ' . $value['fileName'] . '</b> on Line <b>' . $value['lineNumber'] . '</b> )';
                }
            }
        } else if($type == 3){ // get all the errors with a type value of 3
            foreach($data as $key => $value){
                if($value['type'] == 3) {
                    if ($key != 0) echo "<br>";
                    echo '<b>' . $this->readErrorType($value['type']) . ': </b>' . $value['msg'];
                }
            }
        }
    }

    /**
     * return the current error type
     * @return integer
     */
    static function getErrorType(){
        return $_SESSION["error"]["type"];
    }

    /**
     * read the actual meaning of the occurred error
     * @param $type
     * @return string
     */
    static function readErrorType($type){
        $error = "";

        switch($type){
            case "1";
                $error = "Fatal error";
                break;
            case "2";
                $error = "Warning";
                break;
            case "3";
                $error = "Error";
                break;
        }
        return $error;
    }

    /**
     * kill the script with a custom message
     * @param $message
     * @param $file
     * @param int $line
     */
    function kill($message,$file, $line = 0){
        if($line == 0){
            die ('<b>Fatal Error : </b>' . $message . ' (<b> '.$file.'</b> )');
        } else {
            die ('<b>Fatal Error : </b>' . $message . ' (<b> '.$file.'</b> on Line <b>'.$line.'</b> )');
        }
    }

    function customKill($title,$message,$theme_url){
            $_SESSION['theme_url'] = $theme_url;
            $_SESSION['err_msg'] = $message;
            $_SESSION['err_title'] = $title;
            header('location: error.php');
        die();
    }

}

$message = new Message();