<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/12/2017
 * Time: 2:21 PM
 */
class passwordManager {

    var $connection; // public variable for the database connection
    private $database; // instance of the Database class.
    private $message; // instance of the Message class.
    private $userData; // instance of the user class.
    private $settings; // instance of the settings class.
    private $mail; // instance of the mail class.

    /**
     * passwordManager constructor for PHP4
     */
    function passwordManager(){
        $this->__construct();
    }

    /**
     * passwordManager constructor for PHP5.
     */
    function __construct(){

    }

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
        $this->connection = $database->connection;
        $this->message = $messageClass;
        $this->userData = $userDataClass;
        $this->mail = $mail;
        $this->settings = $settings;
    }

    /**
     * @param $username
     * @param $email
     * @param bool $includeTemplate
     * @param string $template
     * @return bool
     */
    function forgetPasswordWithEmail($username, $email, $includeTemplate = false, $template=""){

        if(empty($username)){
            $this->message->setError("Username cannot be empty !",Message::Error);
            return false;
        }

        if(empty($email)){
            $this->message->setError("Email field cannot be empty !",Message::Error);
            return false;
        }

        if(!(filter_var($email, FILTER_VALIDATE_EMAIL))){
            $this->message->setError("The email used is not a valid one !",Message::Error);
            return false;
        }

        if(!($this->checkUserExists($username))){
            $this->message->setError("The requested user does not exist !",Message::Error);
            return false;
        }

        if(!$this->checkUserEmail($username,$email)){
            $this->message->setError("Email not found in database",Message::Error);
            return false;
        }

        $reset_code = $this->generateRandomString(); // the reset code that will be sent to the user

        // ** Update the database with the new reset code ** //
        $sql = "UPDATE ".TBL_USERS." SET " . TBL_USERS_RESET_CODE." = '".$reset_code."' WHERE ". TBL_USERS_USERNAME." = '".$username."'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        $vars = array(
            '{$username}'       => $username,
            '{$siteURL}' => $this->settings->get(Settings::SITE_URL),
            '{$siteName}' => $this->settings->get(Settings::SITE_NAME),
            '{$resetCode}' => $reset_code,
        );

        $to = $email;
        $subject = "Password reset || " . $this->settings->get(Settings::SITE_NAME);

        // check if include a template is checked
        if($includeTemplate){
            $message = strtr($template, $vars);
            if($this->mail->sendTemplate($this->settings->get(Settings::SITE_EMAIL), $to, $subject, $message)) {return true; } else { return false;}
        } else {
            $message = strtr($template, $vars);
            if($this->mail->sendText($this->settings->get(Settings::SITE_EMAIL), $to, $subject, $message)) {return true; } else { return false;}
        }
    }

    /**
     * Confirm the password reset process with code and email
     * @param $email
     * @param $code
     * @return bool
     */
    function resetPasswordUsingCodeAndEmail($email, $code){

        // escape the given strings
        $email = $this->database->escapeString($email);
        $code = $this->database->escapeString($code);

        if(empty($email)){
            $this->message->setError("Email field cannot be empty !",Message::Error);
            return false;
        }

        if(empty($code)){
            $this->message->setError("Code field cannot be empty !",Message::Error);
            return false;
        }

        if(!(filter_var($email, FILTER_VALIDATE_EMAIL))){
            $this->message->setError("The email used is not a valid one !",Message::Error);
            return false;
        }

        $sql = "SELECT * FROM ".TBL_USERS." WHERE ".TBL_USERS_EMAIL." = '". $email . "' AND ".TBL_USERS_RESET_CODE." = '".$code."'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        if(mysqli_num_rows($result) < 1){
            $this->message->setError("Failed to locate the reset code. Expired or not found.",Message::Error);
            return false;
        } else { return true; }
    }


    function confirmNewPassword($email, $code, $password, $password2){

        if(empty($email) || empty($code)){
            $this->message->setError("Email and code values cannot be null !",Message::Error);
            return false;
        }

        if(!(filter_var($email, FILTER_VALIDATE_EMAIL))){
            $this->message->setError("The email used is not a valid one !",Message::Error);
            return false;
        }

        if(empty($password) || empty($password2)){
            $this->message->setError("All fields are required.",Message::Error);
            return false;
        }

        // password checks
        if(strlen($password) < 8 || strlen($password) > 25){
            $this->message->setError("Password length most be between 8 -> 25 characters long", Message::Error);
            return false;
        }

        $password = md5($password);
        $password2 = md5($password2);

        if($password != $password2){
            $this->message->setError("Passwords do not match !",Message::Error);
            return false;
        }

        $sql = "SELECT * FROM ".TBL_USERS." WHERE ".TBL_USERS_EMAIL."= '". $email . "' AND ".TBL_USERS_RESET_CODE." = '".$code."'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        if(mysqli_num_rows($result) < 1){
            $this->message->setError("Failed to locate the reset code. Expired or not found.",Message::Error);
            return false;
        } else {

            $row = mysqli_fetch_assoc($result);
            if($password == $row[TBL_USERS_PASSWORD]){
                $this->message->setError("New password cannot be the same as the old one.",Message::Error);
                return false;
            }

            // update database with the new password and return true and make sure the session and the cookies are destroyed
            $sql = "UPDATE ".TBL_USERS." SET ".TBL_USERS_PASSWORD."= '$password2',".TBL_USERS_RESET_CODE."= '' WHERE ".TBL_USERS_EMAIL."='$email'";
            if (!$result = mysqli_query($this->connection,$sql)) {
                $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
                return false;
            }

            return true;
        }

    }

    /**
     * Check if user exists
     * @param $username
     * @return bool
     */
    function checkUserExists($username){
        if(empty($username)){
            die($this->printError("Username and Authentication key most not be empty"));
        }

        $sql = "SELECT * FROM ".TBL_USERS." WHERE ".TBL_USERS_USERNAME." = '". $username . "'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        if(mysqli_num_rows($result) < 1){
            return false;
        } else { return true; }
    }

    /**
     * Check if given email match the one in database
     * @param $username
     * @param $email
     * @return bool
     */
    function checkUserEmail($username, $email){
        if(empty($email) || empty($username)){
            $this->message->setError("Username and Email most not be empty",Message::Warning);
            return false;
        }

        $sql = "SELECT * FROM ".TBL_USERS." WHERE ".TBL_USERS_USERNAME." = '". $username . "'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        $row = mysqli_fetch_assoc($result);

        if(($row[TBL_USERS_EMAIL] != $email)){
            return false;
        } else { return true; }
    }

    /**
     * Generate a random string
     * @param int $length
     * @return string
     */
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}

$passwordManager = new passwordManager();