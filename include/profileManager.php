<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 3:22 PM
 */
class profileManager
{

    /**
     * profileManager constructor for PHP5.
     */
    function __construct()
    {

    }

    /**
     * Init the class
     */
    public function init()
    {

    }

    public function setNewUsername($username, $pin)
    {

        // define all the global variables
        global $database, $message, $user, $settings, $functions;

        // secure the strings
        $username = $database->escapeString($username);
        $pin = $database->escapeString($pin);

        // check if can change username
        if (!$settings->canChangeUsername()) {
            $message->setError("Username change is been disabled", Message::Error);
            return false;
        }

        // check if empty $username
        if (empty($username)) {
            $message->setError("Username field cannot be empty", Message::Error);
            return false;
        }

        // Username checks
        if (preg_match('/[^A-Za-z0-9]/', $username)) {
            $message->setError("Username most contain only letters and numbers", Message::Error);
            return false;
        }

        if (strlen($username) < 6 || strlen($username) > 25) {
            $message->setError("Username length most be between 6 -> 25 characters long", Message::Error);
            return false;
        }

        // check if username exists
        if ($functions->userExist($username)) {
            $message->setError("Username already exists", Message::Error);
            return false;
        }

        // check if using a pin is a must
        if ($settings->pinRequired()) {

            // check if empty pin
            if (empty($pin)) {
                $message->setError("Pin number field cannot be empty", Message::Error);
                return false;
            }

            // check if pin number matches
            $pin = md5($pin);
            if (!$user->is_samePinNumber($pin)) {
                $message->setError("Wrong pin number has been used", Message::Error);
                return false;
            }

            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_USERNAME . " = '" . $username . "' WHERE " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "' AND " . TBL_USERS_PIN . " = '" . $pin . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        } else {
            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_USERNAME . " = '" . $username . "' WHERE " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        }
    }

    public function setNewEmail($email, $email2, $pin)
    {

        // define all the global variables
        global $database, $message, $user, $settings, $functions;

        // secure the strings
        $email = $database->escapeString($email);
        $email2 = $database->escapeString($email2);
        $pin = $database->escapeString($pin);

        // check if empty $email | $email2
        if (empty($email) || empty($email2)) {
            $message->setError("Both email fields are required", Message::Error);
            return false;
        }

        // email checks
        if ($email != $email2) {
            $message->setError("Email fields should be identical", Message::Error);
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message->setError("Invalid email syntax has been used", Message::Error);
            return false;
        }

        // check if email exists
        if ($functions->emailExist($email)) {
            $message->setError("Email address already exists.", Message::Error);
            return false;
        }


        if ($settings->pinRequired()) {

            // check if empty pin
            if (empty($pin)) {
                $message->setError("Pin number field cannot be empty", Message::Error);
                return false;
            }

            // check if pin number matches
            $pin = md5($pin);
            if (!$user->is_samePinNumber($pin)) {
                $message->setError("Wrong pin number has been used", Message::Error);
                return false;
            }

            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_EMAIL . " = '" . $email . "' WHERE " . TBL_USERS_EMAIL . " = '" . $user->getEmail() . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        } else {

            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_EMAIL . " = '" . $email . "' WHERE " . TBL_USERS_EMAIL . " = '" . $user->getEmail() . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        }

    }

}