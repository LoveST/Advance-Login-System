<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 2:51 PM
 */

namespace ALS;

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
     * @param $username
     * @return bool
     */
    function userExist($username)
    {

        // define all the global variables
        global $database, $message;

        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";
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
     * @return string
     */
    function calculateTime($time2)
    {
        $precision = 1;
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

    // count the total number of online users using the script in a 1 minute radius
    function onlineCounter()
    {

        // define all the global variables
        global $database;

        $totalUsers = 0;

        $sql = "SELECT * FROM " . TBL_HEARTBEAT;
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return 0;
        }

        if ($database->getQueryNumRows($results, true) < 1) {
            return 0;
        }

        // count the timestamp for each person online atm
        foreach ($database->getQueryEffectedRows($results, true) as $row) {
            $last_update = new \DateTime($row[TBL_HEARTBEAT_TIMESTAMP]); // last time updated
            $currentTime = new \DateTime(date("Y-m-d H:i:s", time())); // current time
            $timeDifference = $currentTime->diff($last_update); // count the difference
            if ($timeDifference->i < 1) {
                $totalUsers += 1;
            }
        }

        return $totalUsers;
    }

    /**
     * Check if given a user's account with the given (username or email) is activated
     * @param $data
     * @param $isEmail
     * @return bool
     */
    function is_userActivated($data, $isEmail = false)
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
    function is_localhost($ip = "")
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

}