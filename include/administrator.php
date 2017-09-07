<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/1/2017
 * Time: 7:22 PM
 */

namespace ALS;

class Administrator
{

    function getTotalUsers()
    {

        // define all the global variables
        global $database;

        $sql = "SELECT * FROM " . TBL_USERS;

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        return $database->getQueryNumRows($results, true);
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

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        if ($database->getQueryNumRows($results, true) < 1) {
            return false;
        }

        foreach ($database->getQueryEffectedRows($results, true) as $row) {
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

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        $users = "";

        if ($database->getQueryNumRows($results, true) < 1) {
            return false;
        }

        foreach ($database->getQueryEffectedRows($results, true) as $row) {
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

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        $users = "";

        if ($database->getQueryNumRows($results, true) < 1) {
            return false;
        }

        foreach ($database->getQueryEffectedRows($results, true) as $row) {
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

            // get the sql results
            if (!$result = $database->getQueryResults($sql)) {
                return false;
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

            // get the sql results
            if (!$result = $database->getQueryResults($sql)) {
                return false;
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
        $username = $database->secureInput($username);
        $amount = $database->secureInput($amount);

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
        $username = $database->secureInput($username);
        $amount = $database->secureInput($amount);

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
        $siteSecretKey = $database->secureInput($siteSecretKey);
        $captchaKey = $database->secureInput($captchaKey);
        $captchaSecretKey = $database->secureInput($captchaSecretKey);

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

    /**
     * @param int $since -> the amount of days passed for the users that has signed up
     * @return array|bool
     */
    function getTotalRegisteredUsers($since)
    {

        // define all the global variables
        global $database, $message, $settings, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("admin_analytics_registeredUsers")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // call the database and get the results back
        $query = "SELECT COUNT(*) FROM  " . TBL_USERS . " WHERE  " . TBL_USERS_DATE_JOINED . " > NOW() - INTERVAL $since DAY";

        // get the sql results
        if (!$result = $database->getQueryResults($query)) {
            return false;
        }

        $row = $database->getQueryEffectedRow($result, true);

        // return the array
        return $row[0];
    }

    /**
     * @param $since
     * @return bool|array
     */
    function getTotalLoggedUsers($since)
    {

        // define all the global variables
        global $database, $message, $settings, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("admin_analytics_loggedUsers")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return 0;
        }

        // call the database and get the results back
        $query = "SELECT COUNT(*) FROM  " . TBL_USERS . " WHERE  " . TBL_USERS_LAST_LOGIN . " > NOW() - INTERVAL $since DAY";

        // get the sql results
        if (!$result = $database->getQueryResults($query)) {
            return false;
        }

        $row = $database->getQueryEffectedRow($result, true);

        // return the array
        return $row[0];
    }

    /**
     * get all the users between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function getTotalRegisteredUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $settings, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_registeredUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // Set the required parameters
        $users = "";

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_DATE_JOINED . " >= '$startDate' AND 
        " . TBL_USERS_DATE_JOINED . " <= '$endDate'" . $limitArgs;

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        foreach ($database->getQueryEffectedRows($results, true) as $row) {
            $currentUser = new User();
            $currentUser->initInstance($row);

            $users[] = $currentUser;
        }

        return $users;
    }

    /**
     * get all the logged in users between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function getTotalLoggedUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $settings, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_loggedUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // Set the required parameters
        $users = "";

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_LAST_LOGIN . " >= '$startDate' AND 
        " . TBL_USERS_LAST_LOGIN . " <= '$endDate'" . $limitArgs;

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        foreach ($database->getQueryEffectedRows($results, true) as $row) {
            $currentUser = new User();
            $currentUser->initInstance($row);

            $users[] = $currentUser;
        }

        return $users;
    }

    /**
     * count the number of signed up users in between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function countTotalRegisteredUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $settings, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_countRegisteredUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT COUNT(*) FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_DATE_JOINED . " >= '$startDate' AND 
        " . TBL_USERS_DATE_JOINED . " <= '$endDate'" . $limitArgs;

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        $row = $database->getQueryEffectedRow($result, true);


        return $row[0];
    }

    /**
     * count the number of logged in users in between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function countTotalLoggedUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $settings, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_countLoggedUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT COUNT(*) FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_LAST_LOGIN . " >= '$startDate' AND 
        " . TBL_USERS_LAST_LOGIN . " <= '$endDate'" . $limitArgs;

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        $row = $database->getQueryEffectedRow($result, true);


        return $row[0];
    }

    /**
     * Add a new user level to the main script
     * @param String $name
     * @param int $level
     * @param String[] $permissions
     * @return bool
     */
    function addNewLevel($name, $level, $permissions)
    {

        // define all the global variables
        global $database, $message;

        // escape strings
        $name = $database->secureInput($name);
        $level = $database->secureInput($level);
        //$permissions = $database->secureInput($permissions);

        // check for any empty fields
        if ($name == "" || $level == "" || $permissions == "") {
            $message->setError("All fields are required to be filled", Message::Error);
            return false;
        }

        // check if level name exists
        if ($this->isLevelNameAvailable($name)) {
            $message->setError("Level name already exists", Message::Error);
            return false;
        }

        // check if level exists
        if ($this->isLevelAvailable($level)) {
            $message->setError("Level already exists", Message::Error);
            return false;
        }

        // check if array of permissions has been supplied and its not an empty array
        if (!is_array($permissions)) {
            $message->setError("An array of strings must be supplied for the permissions", Message::Error);
            return false;
        }

        if (empty($permissions)) {
            $message->setError("At least 1 permission is required", Message::Error);
            return false;
        }

        // split the permissions array and store it in a string with a '|' separator
        $permissionsString = "";
        $i = 0;
        foreach ($permissions as $permission) {

            // escape the string for db protection
            $permission = $database->secureInput($permission);

            // check if permissions only has * inside, then refuse it and don't add it
            if ($permission == "*") {
                continue;
            }

            // check if string has no spaces in it then don't add it
            if (preg_match('/\s/', $permission)) {
                continue;
            }

            // add the permission to the string array
            $permissionsString .= $permission;

            // check if not last, then add a separator
            if (($i + 1) < count($permissions)) {
                $permissionsString .= "|";
            }

            $i++;
        }

        // update the database with the new results
        $sql = "INSERT INTO " . TBL_LEVELS . " (" . TBL_LEVELS_LEVEL . "," . TBL_LEVELS_NAME . "," . TBL_LEVELS_PERMISSIONS . ") VALUES
        ('$level','$name','$permissionsString')";

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        // if not errors then return a success message
        $message->setSuccess("Level " . $name . "(" . $level . "), has been successfully created");
        return true;
    }

    /**
     * check if the given level name exists
     * @param String $levelName
     * @return boolean
     */
    function isLevelNameAvailable($levelName)
    {

        // define all the global variables
        global $database, $message;

        // check for empty object given
        if ($levelName == "") {
            return false;
        }

        // check in database if exists
        $sql = "SELECT COUNT(*) FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_NAME . " = '$levelName'";

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        // grab the results
        $row = $database->getQueryEffectedRow($result, true);

        // check if any values has been returned
        if ($row[0] > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if the given level exists
     * @param Int $level
     * @return boolean
     */
    function isLevelAvailable($level)
    {

        // define all the global variables
        global $database, $message;

        // check for empty object given
        if ($level == "") {
            return false;
        }

        // check in database if exists
        $sql = "SELECT COUNT(*) FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_LEVEL . " = '$level'";

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        // grab the results
        $row = $database->getQueryEffectedRow($result, true);

        // check if any values has been returned
        if ($row[0] > 0) {
            return true;
        } else {
            return false;
        }
    }

}