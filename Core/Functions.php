<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 2:51 PM
 */

namespace ALS;

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class Functions
{

    /**
     * encrypt a string using the site key
     * @param $text
     * @return string
     */
    function encryptIt($text)
    {

        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($text, $cipher, SITE_SECRET, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, SITE_SECRET, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

        return $ciphertext;
    }

    /**
     * decrypt a string using the site key
     * @param $text
     * @return string
     */
    function decryptIt($text)
    {

        $c = base64_decode($text);
        $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, SITE_SECRET, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, SITE_SECRET, $as_binary = true);
        if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
        {
            return $original_plaintext . "\n";
        } else {
            return "";
        }
    }

    /**
     * check if the given date is a valid one matching the format m/d/y
     * @param $date
     * @return bool
     */
    function isValidDate($date)
    {
        if (\DateTime::createFromFormat('m/d/Y', $date)->format('m/d/Y') == $date) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get an age from a given date of birth
     * @param $birthday
     * @return int
     */
    function getAge($birthday)
    {
        $birthday = new \DateTime($birthday);
        $interval = $birthday->diff(new \DateTime);
        return $interval->y;
    }

    /**
     * check if the given birthday is above or equal to x years
     * @param $birthday
     * @param int $age
     * @return bool
     */
    function validateAge($birthday, $age = 18)
    {
        // $birthday can be UNIX_TIMESTAMP or just a string-date.
        if (is_string($birthday)) {
            $birthday = strtotime($birthday);
        }

        // check
        // 31536000 is the number of seconds in a 365 days year.
        if (time() - $birthday < $age * 31536000) {
            return false;
        }

        return true;
    }

    /**
     * check if x user exists
     * @param string $username
     * @return bool
     */
    function userExist($username)
    {
        // define all the global variables
        global $database;

        // call the database
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";
        $results = $database->getQueryResults($sql);

        // check if any results found
        if ($database->getQueryNumRows($results, true) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if x ID exists
     * @param $id
     * @return bool
     */
    function idExist($id)
    {
        // define all the global variables
        global $database;

        // secure the input
        $id = $database->secureInput($id);

        // call the database
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = '" . $id . "'";
        $results = $database->getQueryResults($sql);

        // check if any results found
        if ($database->getQueryNumRows($results, true) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if x email exists
     * @param $email
     * @return bool
     */
    function emailExist($email)
    {

        // define all the global variables
        global $database, $message;

        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . " = '" . $email . "'";
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            $message->setError("SQL error : " . $database->getError(), Message::Fatal);
            die;
        }

        if ($database->getQueryNumRows($results, true) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get a specific user ID by giving its username
     * @param string $username
     * @return bool|int
     */
    function getUserID($username)
    {

        global $database;

        // prepare the sql query
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";

        // execute the query
        $results = $database->getQueryResults($sql);

        // check if results are found
        if ($database->getQueryNumRows($results, true) <= 0) {
            return false;
        }

        // get the user id
        $id = $database->getQueryEffectedRow($results, true)[TBL_USERS_ID];

        // return the the user id
        return $id;
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

    /**
     * Convert a string that its in a time format to an actual time based string ("Y-m-d H:i:s")
     * @param $time2
     * @param int $precision
     * @return string
     */
    function calculateTime($time2, $precision = 1)
    {
        $time1 = time();
        // If not numeric then convert timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }
        // If time1 > time2 then swap the 2 values
        if ($time1 > $time2) {
            list($time1, $time2) = array($time2, $time1);
        }
        // Set up intervals and diffs arrays
        $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
        $diffs = array();
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }
            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }
        // Return string with times
        if (($time = implode(", ", $times)) == "") {
            return "1 second";
        } else {
            return $time;
        }
    }

    /**
     * get a new id for a new user
     * @return int
     */
    function getNewID()
    {

        // define all the global variables
        global $database, $message;

        $sql = "SELECT " . TBL_USERS_ID . " FROM " . TBL_USERS . " ORDER BY " . TBL_USERS_ID . " DESC LIMIT 1";
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            $message->setError("SQL error : " . $database->getError(), Message::Fatal);
            die;
        }

        if ($database->getQueryNumRows($results, true) < 1) {
            return 1;
        }

        $row = $database->getQueryEffectedRow($results, true);
        return $row[TBL_USERS_ID] + 1;
    }

    /**
     * Check if given a user's account with the given (username or email) is activated
     * @param $data
     * @param $isEmail
     * @return bool
     */
    function isUserActivated($data, $isEmail = false)
    {

        // define all the global variables
        global $database, $message;

        if ($isEmail) {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . " = '" . $data . "'";
        } else {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $data . "'";
        }

        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            $message->setError("SQL error : " . $database->getError(), Message::Fatal);
            die;
        }

        $row = $database->getQueryEffectedRow($results, true);
        if ($row[TBL_USERS_ACTIVATED] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the current page full url
     * @return string
     */
    function getCurrentPageURL()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * get the current users ip address
     * @return string
     */
    function getUserIP()
    {
        return trim($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_X_FORWARDED_FOR']);
    }

    /**
     * Check if the user is on a localhost server
     * @param string $ip
     * @return bool
     */
    function isLocalhost($ip = "")
    {
        if (empty($ip) || $ip == "") {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $whitelist = array('::1', '127.0.0.1');
        if (in_array($ip, $whitelist)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the total amount of users signed up this month
     */
    function getCurrentMonthsSignups()
    {

        // define all the global variables
        global $admin;

        $firstDatOfMonth = date("Y-m") . "-01";
        $time = strtotime(date("Y-m-d") . ' +1 days');
        $todaysDate = date("Y-m-d", $time);

        // return the results
        return $admin->countTotalRegisteredUsersInBetween($firstDatOfMonth, $todaysDate);
    }

    /**
     * check if a certain time zone is valid
     * @param $timeZone
     * @return bool
     */
    function isValidTimeZone($timeZone)
    {
        if (in_array($timeZone, timezone_identifiers_list())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if a certain directory is empty
     * @param $path
     * @return bool|null
     */
    function isDirEmpty($path)
    {
        if (!is_readable($path)) return NULL;
        $handle = opendir($path);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Check if a pin number has a valid length
     * @param int $pin
     * @return bool
     */
    function isValidPinLength($pin)
    {
        // define the required global variables
        global $settings;

        // check if $pin is empty or has 0 length
        if (empty($pin) || strlen($pin) == 0 || $pin == null) {
            return false;
        }

        // check if pin number length match the required
        if (strlen($pin) != $settings->maxRequiredPinLength()) {
            return false;
        }

        // if no errors then return true
        return true;
    }

    /**
     * Check if a password length is valid
     * @param string $password
     * @return bool
     */
    function isValidPasswordLength($password)
    {
        // define the required global variables
        global $settings;

        // check if $password is empty or has 0 length
        if (empty($password) || strlen($password) == 0 || $password == null) {
            return false;
        }

        // check if the password length match the required script settings
        if (strlen($password) < $settings->minPasswordLength() || strlen($password) > $settings->maxPasswordLength()) {
            return false;
        }

        // if no errors then return true
        return true;
    }

    /**
     * redirect a client to a specific url
     * @param string $url
     * @param bool $redirectBack
     * @return bool
     */
    function redirect($url, $redirectBack = false)
    {
        // define the required global variables
        global $database;

        // secure and decode the string
        $url = $database->secureInput(urldecode($url));

        // check if string is empty
        if (is_null($url) || empty($url)) {
            return false;
        }

        // check if URL starts with HTTP
        $prefix = "";
        if (substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://") {
            $prefix = "http://";
        }

        // check if redirectBack is enabled
        if ($redirectBack) {
            // insert the current page url to the session for the purpose of coming back to it
            $_SESSION['redirect_source'] = urlencode($this->getCurrentPageURL());
        }

        // redirect the client
        header("Location: " . $prefix . $url);

        // exit the script
        exit();
    }

    /**
     * Check if a redirect to source is available
     * @return bool
     */
    function isDirectBackToSourceAvailable()
    {
        // check if a redirect is available
        if (isset($_SESSION['redirect_source'])) {
            return true;
        }

        // if no redirect found then return false
        return false;
    }

    /**
     * Check if a redirect is available, then set the current page header to the required redirect source
     * @param string $customURL
     */
    function directBackToSource($customURL = "")
    {
        // check if source is available in the session
        if (isset($_SESSION{'redirect_source'})) {
            // set the headers
            header("Location: " . urldecode($_SESSION['redirect_source']));

            // unset the session
            unset($_SESSION['redirect_source']);

            // exit the script
            exit();
        } else if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {

            // secure the url
            $url = urlencode($_GET['redirect']);

            // set the header
            header("Location: " . $url);

            // exit the script
            exit();
        } else if (!empty($customURL)) {

            //redirect to the custom URL
            header("Location: " . $customURL);

            // exit the script
            exit();
        }
    }

    /**
     * Search for a specific user by (username, ID, email)
     * @param mixed $value
     * @param int|searchBy $searchBy
     * @return bool|User
     */
    function searchUser($value, $searchBy)
    {
        // define the required global variables
        global $database;

        // secure the inputs
        $value = $database->secureInput($value);

        // hold the required data to return
        $data = null;

        // check which method should be used
        switch ($searchBy) {
            case searchBy::username:
                $data = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $value . "'";
                break;
            case searchBy::id:
                $data = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = '" . $value . "'";
                break;
            case searchBy::email:
                $data = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . " = '" . $value . "'";
                break;
            default:
                $data = false;
                break;
        }

        // call the database and get the required data
        $results = $database->getQueryResults($data);

        // check if any results
        if ($database->getQueryNumRows($results, true) > 0) {

            // get the data
            $data = $database->getQueryEffectedRow($results, true);

            // load the new user to prepare for the return
            $newUser = new User();
            $newUser->initInstance($data);

            // set the new data
            $data = $newUser;

        } else {
            $data = false;
        }

        // return the results
        return $data;
    }

    /**
     * get the first char in a string
     * @author Patrick Lewis
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function stringStartsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Get the last char in a string
     * @author Patrick Lewis
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function stringEndsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return $length === 0 ||
            (substr($haystack, -$length) === $needle);
    }

    /**
     * Load a specific file and handle the errors
     * @param string $filePath
     */
    function loadFile($filePath)
    {
        // define the required global variables
        global $message;

        // check if file name is empty
        if (!isset($filePath) || empty($filePath)) {
            $message->kill("File path cannot be empty", "File Loader");
            return;
        }

        // check if a directory was specified instead of file path
        if (is_dir($filePath)) {
            $message->kill("File path cannot direct to a directory", "File Loader");
            return;
        }

        // check if file exists
        if (!file_exists($filePath)) {
            $message->kill("Required file does not exist (" . $filePath . ")", "File Loader");
            return;
        }

        // check if file is readable
        if (!is_readable($filePath)) {
            $message->kill("Required file is not accessible or missing permissions", "File Loader");
            return;
        }

        // load the required file
        require_once $filePath;
    }

    function functionExist($function)
    {

    }

}

abstract class searchBy
{
    const username = 1;
    const id = 2;
    const email = 3;
}

$functions = new Functions();