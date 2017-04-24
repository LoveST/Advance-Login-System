<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/1/2017
 * Time: 7:22 PM
 */
namespace ALS\Administrator;
use ALS\User\User;
use ALS\Message\Message;
use ALS\Settings\Settings;
class Administrator
{

    function getTotalUsers()
    {

        // define all the global variables
        global $database;

        $sql = "SELECT count(*) FROM " . TBL_USERS;
        $result = mysqli_query($database->connection, $sql);
        $num = mysqli_fetch_row($result);
        return $num[0];
    }

    /**
     * Get all the current admins in an array form
     * @param int $limit
     * @return array|bool
     */
    function getAdmins($limit = 0)
    {

        // define all the global variables
        global $database;

        if ($limit == 0) {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_LEVEL . "='100'";
        } else {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_LEVEL . "='100' LIMIT " . $limit;
        }
        $admins = "";
        $results = mysqli_query($database->connection, $sql);

        if (mysqli_num_rows($results) < 1) {
            return false;
        }

        while ($row = mysqli_fetch_assoc($results)) {
            $currentUser = new User();
            $currentUser->initInstance($row);

            $admins[] = $currentUser;
        }
        return $admins;
    }

    /**
     * Get the total users in the database
     * @param int $limit
     * @return array|bool
     */
    function getUsers($limit = 0)
    {

        // define all the global variables
        global $database;

        if ($limit == 0) {
            $sql = "SELECT * FROM " . TBL_USERS;
        } else {
            $sql = "SELECT * FROM " . TBL_USERS . " LIMIT " . $limit;
        }

        $results = mysqli_query($database->connection, $sql);
        $users = "";

        if (mysqli_num_rows($results) < 1) {
            return false;
        }

        while ($row = mysqli_fetch_assoc($results)) {
            $currentUser = new User();
            $currentUser->initInstance($row);

            $users[] = $currentUser;
        }
        return $users;
    }

    /**
     * get the total users that are banned from the database
     * @param int $limit
     * @return integer
     */
    function getBannedUsers($limit = 0)
    {

        // define all the global variables
        global $database;

        if ($limit == 0) {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_BANNED . "='1'";
        } else {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_BANNED . "='1' LIMIT " . $limit;
        }

        $results = mysqli_query($database->connection, $sql);
        $users = "";

        if (mysqli_num_rows($results) < 1) {
            return false;
        }

        while ($row = mysqli_fetch_assoc($results)) {
            $currentUser = new User();
            $currentUser->initInstance($row);

            $users[] = $currentUser;
        }
        return $users;
    }

    /**
     * Enable or Disable for HTTPS on all the script pages
     * @param $activate
     * @return bool
     */
    function activateHTTPS($activate)
    {

        // define all the global variables
        global $database, $message, $settings;

        if ($activate) {
            // check if already activated
            if ($settings->isHTTPS()) {
                return false;
            }

            $sql = "UPDATE " . TBL_SETTINGS . " SET value = '1' WHERE field = '" . TBL_SETTINGS_FORCE_HTTPS . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
                die;
            }

            //if no error then set the success message
            $message->setSuccess("You have activated ssl across your script");
            return true;
        } else {
            // check if already de-activated
            if (!$settings->isHTTPS()) {
                return false;
            }

            $sql = "UPDATE " . TBL_SETTINGS . " SET value = '0' WHERE field = '" . TBL_SETTINGS_FORCE_HTTPS . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
                die;
            }

            //if no error then set the success message
            $message->setSuccess("You have de-activated ssl across your script");
            return true;
        }
    }

    /**
     * add an x amount of xp to a x user
     * @param $username
     * @param int $amount
     * @return bool
     */
    function addXP($username, $amount)
    {

        // define all the global variables
        global $database, $message, $user, $functions;

        // check if current user has the required permission
        if (!$user->hasPermission("manage_xp_add")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // escape the given strings
        $username = $database->escapeString($username);
        $amount = $database->escapeString($amount);

        // check for empty username or amount
        if (empty($username) || empty($amount)) {
            $message->setError("Both fields are required", Message::Error);
            return false;
        }

        // check if amount is a number
        if (!is_numeric($amount)) {
            $message->setError("Only numbers are allowed for the amount", Message::Error);
            return false;
        }

        // check if user exists
        if (!$functions->userExist($username)) {
            $message->setError("Username not found", Message::Error);
            return false;
        }

        // initiate the user class
        $getUser = new User();
        // load the user data
        $getUser->initInstance($username);
        // add the new amount to the user xp
        if ($getUser->addXP($amount)) {
            $message->setSuccess("You have successfully added " . $amount . " XP to " . $getUser->getUsername() . "'s account");
            $message->setSuccess("Now, he has " . $getUser->getXP() . " XP");
            return true;
        } else {
            $message->setError("An error has occurred", Message::Error);
            return false;
        }
    }

    /**
     * subtract an x amount of xp from a x user
     * @param $username
     * @param int $amount
     * @return bool
     */
    function subtractXP($username, $amount)
    {

        // define all the global variables
        global $database, $message, $user, $functions;

        // check if current user has the required permission
        if (!$user->hasPermission("manage_xp_subtract")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // escape the given strings
        $username = $database->escapeString($username);
        $amount = $database->escapeString($amount);

        // check for empty username or amount
        if (empty($username) || empty($amount)) {
            $message->setError("Both fields are required", Message::Error);
            return false;
        }

        // check if amount is a number
        if (!is_numeric($amount)) {
            $message->setError("Only numbers are allowed for the amount", Message::Error);
            return false;
        }

        // check if user exists
        if (!$functions->userExist($username)) {
            $message->setError("Username not found", Message::Error);
            return false;
        }

        // initiate the user class
        $getUser = new User();
        // load the user data
        $getUser->initInstance($username);

        // subtract the new amount from the user xp
        if ($getUser->subtractXP($amount)) {
            $message->setSuccess("You have successfully subtracted " . $amount . " XP from " . $getUser->getUsername() . "'s account");
            $message->setSuccess("Now, he has " . $getUser->getXP() . " XP");
            return true;
        } else {
            $message->setError("An error has occurred", Message::Error);
            return false;
        }
    }

    /**
     * update the site security settings
     * @param $siteSecretKey
     * @param $captchaKey
     * @param $captchaSecretKey
     * @return bool
     */
    function updateSecuritySettings($siteSecretKey, $captchaKey, $captchaSecretKey)
    {

        // define all the global variables
        global $database, $message, $settings, $user, $captcha;

        // escape strings
        $siteSecretKey = $database->escapeString($siteSecretKey);
        $captchaKey = $database->escapeString($captchaKey);
        $captchaSecretKey = $database->escapeString($captchaSecretKey);

        // check if current user has the required permission
        if (!$user->hasPermission("update_site_settings")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // check if any fields are empty then return an error
        if (empty($siteSecretKey) || empty($captchaKey) || empty($captchaSecretKey)) {
            $message->setError("All fields are required to be filled", Message::Error);
            return false;
        }

        // check if site secret consists of 32 characters
        if (strlen($siteSecretKey) != 32) {
            $message->setError("Secret code most be 32 characters long only", Message::Error);
            return false;
        }

        // check if site secret key consists of a hex32 string
        if (!ctype_xdigit($siteSecretKey)) {
            $message->setError("Only a Hex32 string is allowed for the site secret key", Message::Error);
            return false;
        }

        // check if the site secret is the same
        // update the site secret
        if ($settings->secretKey() != $siteSecretKey) {
            $settings->setSiteSecret($siteSecretKey);
            $settings->setSetting(Settings::SECRET_KEY, $siteSecretKey);
        }

        // check if the current captcha key is the same
        // update the captcha key
        if ($captcha->getSiteKey() != $captchaKey) {
            $settings->setCaptchaKey($captchaKey);
            $settings->setSetting(Settings::CAPTCHA_KEY, $captchaKey);
        }

        // check if the current captcha secret key is the same
        // update the captcha secret key
        if ($captcha->getSecretKey() != $captchaSecretKey) {
            $settings->setSetting(Settings::CAPTCHA_SECRET_KEY, $captchaSecretKey);
        }

        // check if the site secret is the same
        // check for any errors then return false
        if ($message->anyError()) {
            return false;
        }

        //set the success message
        $message->setSuccess("Things might have happened");
        return true;
    }

}