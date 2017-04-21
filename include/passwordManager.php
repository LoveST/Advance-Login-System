<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/12/2017
 * Time: 2:21 PM
 */
class passwordManager
{

    /**
     * passwordManager constructor for PHP5.
     */
    function __construct()
    {
        $this->init(); // init the required functions to run the class
    }

    /**
     * init the class
     */
    function init()
    {

    }

    /**
     * @param $username
     * @param $email
     * @param bool $includeTemplate
     * @param string $content
     * @param string $userCaptcha
     * @return bool
     */
    function forgetPasswordWithEmail($username, $email, $userCaptcha, $includeTemplate = false, $content = "")
    {

        // define all the global variables
        global $database, $message, $mail, $settings, $captcha;

        // escape strings
        $username = $database->escapeString($username);
        $email = $database->escapeString($email);
        $userCaptcha = $database->escapeString($userCaptcha);

        if (empty($username)) {
            $message->setError("Username cannot be empty !", Message::Error);
            return false;
        }

        if (empty($email)) {
            $message->setError("Email field cannot be empty !", Message::Error);
            return false;
        }

        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $message->setError("The email used is not a valid one !", Message::Error);
            return false;
        }

        if (!($this->checkUserExists($username))) {
            $message->setError("The requested user does not exist !", Message::Error);
            return false;
        }

        if (!$this->checkUserEmail($username, $email)) {
            $message->setError("Email not found in database", Message::Error);
            return false;
        }

        // send the captcha request for validation
        $captcha->sendRequest($userCaptcha);

        // check if captcha was a success
        if (!$captcha->success()) {
            $message->setError("Wrong captcha has been used", Message::Error);
            return false;
        }

        $reset_code = $this->generateRandomString(); // the reset code that will be sent to the user

        // ** Update the database with the new reset code ** //
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_RESET_CODE . " = '" . $reset_code . "' WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        $vars = array(
            '{:username}' => $username,
            '{:siteURL}' => $settings->get(Settings::SITE_URL),
            '{:siteName}' => $settings->get(Settings::SITE_NAME),
            '{:resetCode}' => $reset_code,
        );

        $to = $email;
        $subject = "Account activation || " . $settings->get(Settings::SITE_NAME);
        // convert variables to actual values
        $content = strtr($content, $vars);
        // initiate the mail class to prepare to send the email
        $mail = new mail();
        // set the sender email
        $mail->fromEmail($settings->get(Settings::SITE_EMAIL));
        // set the sender name
        $mail->fromName("Support");
        // set the receiver email
        $mail->to($to);
        // set the subject
        $mail->subject($subject);
        // check if include a template is checked
        if ($includeTemplate) {
            // Set mail to template
            $mail->isTemplate(true);
            // set the mail template content
            $mail->template($content);
        } else {
            // Set mail to text
            $mail->isTemplate(false);
            // set the mail text content
            $mail->text($content);
        }


        if ($mail->send()) {
            $message->setSuccess("A new reset code has been sent to " . $email);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Confirm the password reset process with code and email
     * @param $email
     * @param $code
     * @return bool
     */
    function resetPasswordUsingCodeAndEmail($email, $code)
    {

        // define all the global variables
        global $database, $message;

        // escape the given strings
        $email = $database->escapeString($email);
        $code = $database->escapeString($code);

        if (empty($email)) {
            $message->setError("Email field cannot be empty !", Message::Error);
            return false;
        }

        if (empty($code)) {
            $message->setError("Code field cannot be empty !", Message::Error);
            return false;
        }

        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $message->setError("The email used is not a valid one !", Message::Error);
            return false;
        }

        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . " = '" . $email . "' AND " . TBL_USERS_RESET_CODE . " = '" . $code . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        if (mysqli_num_rows($result) < 1) {
            $message->setError("Failed to locate the reset code. Expired or not found.", Message::Error);
            return false;
        } else {
            return true;
        }
    }


    function confirmNewPassword($email, $code, $password, $password2)
    {

        // define all the global variables
        global $database, $message;

        if (empty($email) || empty($code)) {
            $message->setError("Email and code values cannot be null !", Message::Error);
            return false;
        }

        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $message->setError("The email used is not a valid one !", Message::Error);
            return false;
        }

        if (empty($password) || empty($password2)) {
            $message->setError("All fields are required.", Message::Error);
            return false;
        }

        // password checks
        if (strlen($password) < 8 || strlen($password) > 25) {
            $message->setError("Password length most be between 8 -> 25 characters long", Message::Error);
            return false;
        }

        $password = md5($password);
        $password2 = md5($password2);

        if ($password != $password2) {
            $message->setError("Passwords do not match !", Message::Error);
            return false;
        }

        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . "= '" . $email . "' AND " . TBL_USERS_RESET_CODE . " = '" . $code . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        if (mysqli_num_rows($result) < 1) {
            $message->setError("Failed to locate the reset code. Expired or not found.", Message::Error);
            return false;
        } else {

            $row = mysqli_fetch_assoc($result);
            if ($password == $row[TBL_USERS_PASSWORD]) {
                $message->setError("New password cannot be the same as the old one.", Message::Error);
                return false;
            }

            // update database with the new password and return true and make sure the session and the cookies are destroyed
            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_PASSWORD . "= '$password2'," . TBL_USERS_RESET_CODE . "= '' WHERE " . TBL_USERS_EMAIL . "='$email'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
                return false;
            }

            $message->setSuccess("A new password has been set for your account");
            return true;
        }

    }

    /**
     * Check if user exists
     * @param $username
     * @return bool
     */
    function checkUserExists($username)
    {

        // define all the global variables
        global $database, $message;

        if (empty($username)) {
            die($this->printError("Username and Authentication key most not be empty"));
        }

        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        if (mysqli_num_rows($result) < 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if given email match the one in database
     * @param $username
     * @param $email
     * @return bool
     */
    function checkUserEmail($username, $email)
    {

        // define all the global variables
        global $database, $message;

        if (empty($email) || empty($username)) {
            $message->setError("Username and Email most not be empty", Message::Warning);
            return false;
        }

        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        $row = mysqli_fetch_assoc($result);

        if (($row[TBL_USERS_EMAIL] != $email)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Generate a random string
     * @param int $length
     * @return string
     */
    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}