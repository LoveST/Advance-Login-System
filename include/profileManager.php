<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 3:22 PM
 */
class profileManager {

    private $database; // instance of the database class
    private $user; // instance of the current user class
    private $settings; // instance of the settings class
    private $message; // instance of the Message class.
    private $functions; // instance of the functions class

    /**
     * profileManager constructor for PHP5.
     */
    function __construct(){

    }

    /**
     * Init the class
     * @param $database
     * @param $user
     * @param $settings
     * @param $functions
     */
    public function init($database, $user, $settings, $functions, $message){
        $this->database = $database;
        $this->user = $user;
        $this->settings = $settings;
        $this->functions = $functions;
        $this->message = $message;
    }

    public function setNewUsername($username, $pin){

        // secure the strings
        $username = $this->database->escapeString($username);
        $pin = $this->database->escapeString($pin);

        // check if can change username
        if(!$this->settings->canChangeUsername()){
            $this->message->setError("Username change is been disabled", Message::Error);
            return false;
        }

        // check if empty $username
        if(empty($username)){
            $this->message->setError("Username field cannot be empty", Message::Error);
            return false;
        }

        // Username checks
        if(preg_match('/[^A-Za-z0-9]/', $username)){
            $this->message->setError("Username most contain only letters and numbers", Message::Error);
            return false;
        }

        if(strlen($username) < 6 || strlen($username) > 25){
            $this->message->setError("Username length most be between 6 -> 25 characters long", Message::Error);
            return false;
        }

        // check if username exists
        if($this->functions->userExist($username)){
            $this->message->setError("Username already exists", Message::Error);
            return false;
        }

        // check if using a pin is a must
        if($this->settings->pinRequired()){

            // check if empty pin
            if(empty($pin)){
                $this->message->setError("Pin number field cannot be empty", Message::Error);
                return false;
            }

            // check if pin number matches
            $pin = md5($pin);
            if(!$this->user->is_samePinNumber($pin)){
                $this->message->setError("Wrong pin number has been used", Message::Error);
                return false;
            }

            $sql = "UPDATE ". TBL_USERS . " SET ". TBL_USERS_USERNAME . " = '" . $username . "' WHERE ". TBL_USERS_USERNAME . " = '" . $this->user->username() . "' AND ". TBL_USERS_PIN . " = '". $pin . "'";
            if (!$result = mysqli_query($this->database->connection, $sql)) {
                $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $this->user->logOut();
            $this->message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        } else {
            $sql = "UPDATE ". TBL_USERS . " SET ". TBL_USERS_USERNAME . " = '" . $username . "' WHERE ". TBL_USERS_USERNAME . " = '" . $this->user->username() . "'";
            if (!$result = mysqli_query($this->database->connection, $sql)) {
                $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $this->user->logOut();
            $this->message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        }
    }

    public function setNewEmail($email, $email2, $pin){

        // secure the strings
        $email = $this->database->escapeString($email);
        $email2 = $this->database->escapeString($email2);
        $pin = $this->database->escapeString($pin);

        // check if empty $email | $email2
        if(empty($email) || empty($email2)){
            $this->message->setError("Both email fields are required", Message::Error);
            return false;
        }

        // email checks
        if($email != $email2){
            $this->message->setError("Email fields should be identical", Message::Error);
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message->setError("Invalid email syntax has been used", Message::Error);
            return false;
        }

        // check if email exists
        if($this->functions->emailExist($email)){
            $this->message->setError("Email address already exists.", Message::Error);
            return false;
        }


        if($this->settings->pinRequired()){

            // check if empty pin
            if(empty($pin)){
                $this->message->setError("Pin number field cannot be empty", Message::Error);
                return false;
            }

            // check if pin number matches
            $pin = md5($pin);
            if(!$this->user->is_samePinNumber($pin)){
                $this->message->setError("Wrong pin number has been used", Message::Error);
                return false;
            }

            $sql = "UPDATE ". TBL_USERS . " SET ". TBL_USERS_EMAIL . " = '" . $email . "' WHERE ". TBL_USERS_EMAIL . " = '" . $this->user->email() . "'";
            if (!$result = mysqli_query($this->database->connection, $sql)) {
                $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $this->user->logOut();
            $this->message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        } else {

            $sql = "UPDATE ". TBL_USERS . " SET ". TBL_USERS_EMAIL . " = '" . $email . "' WHERE ". TBL_USERS_EMAIL . " = '" . $this->user->email() . "'";
            if (!$result = mysqli_query($this->database->connection, $sql)) {
                $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $this->user->logOut();
            $this->message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        }

    }

}