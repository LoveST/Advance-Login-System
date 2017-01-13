<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/12/2017
 * Time: 2:21 PM
 */
class passwordManager {

    var $connection; // public variable for the database connection
    private $message; // instance of the Message class.
    private $userData; // instance of the user class.
    private $mail; // instance of the mail class.

    /**
     * passwordManager constructor for PHP4
     */
    function passwordManager($connection, $messageClass, $userDataClass,$mail){
        $this->__construct($connection, $messageClass, $userDataClass,$mail);
    }

    /**
     * passwordManager constructor for PHP5.
     */
    function __construct($connection, $messageClass, $userDataClass,$mail){
        $this->connection = $connection;
        $this->message = $messageClass;
        $this->userData = $userDataClass;
        $this->mail = $mail;
    }

    /**
     * @param $username
     * @param $email
     * @param $captcha
     * @return bool
     */
    function forgetPasswordWithEmail($username, $email, $captcha, $includeTemplate = false, $template=""){

        if(empty($username)){
            $this->message->setError("Username cannot be empty !",Message::Error);
            return false;
        }

        if(empty($email)){
            $this->message->setError("Email field cannot be empty !",Message::Error);
            return false;
        }

        if(empty($captcha) || !(is_numeric($captcha))){
            $this->message->setError("Question field most be answered with a number !",Message::Error);
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

        if($captcha != $_SESSION['captcha'] || empty($answer)){
            $this->message->setError("Wrong answer to the question",Message::Error);
            return false;
        }

        $reset_code = $this->generateRandomString(); // the reset code that will be sent to the user

        // ** Update the database with the new reset code ** //
        $sql = "UPDATE ".TBL_USERS." SET " . TBL_USERS_RESET_CODE." = '".$reset_code."' WHERE ". TBL_USERS_USERNAME." = '".$this->userData->get(User::UserName)."'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        $vars = array(
            '{$username}'       => $username,
            '{$siteURL}' => SITEURL,
            '{$siteName}' => SITENAME,
            '{$resetCode}' => $reset_code,
        );

        $to = $email;
        $subject = "Password reset || " . SITENAME;
        $_SESSION['captcha'] = "";

        // check if include a template is checked
        if($includeTemplate){
            $message = strtr($template, $vars);
            if($this->mail->sendTemplate(SITE_EMAIL, $to, $subject, $message)) {return true; } else { return false;}
        } else {
            $message = strtr($template, $vars);
        }
    }

    function resetPasswordUsingCodeAndEmail($email, $code, $captcha){
        return true;
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
    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}