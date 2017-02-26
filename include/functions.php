<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 2:51 PM
 */
class Functions{

    private $database; // instance of the Database class.
    private $message; // instance of the Message class.
    private $userData; // instance of the user class.
    private $settings; // instance of the settings class.
    private $mail; // instance of the mail class.

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
        $this->message = $messageClass;
        $this->userData = $userDataClass;
        $this->mail = $mail;
        $this->settings = $settings;
    }

    /**
     * encrypt a string using the site key
     * @param $q
     * @return string
     */
    function encryptIt( $q ) {
        $cryptKey  = $this->settings->SECRET_CODE;
        $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        return( $qEncoded );
    }

    /**
     * decrypt a string using the site key
     * @param $q
     * @return string
     */
    function decryptIt( $q ) {
        $cryptKey  = $this->settings->SECRET_CODE;
        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
    }

    /**
     * check if the given date is a valid one matching the format m/d/y
     * @param $date
     * @return bool
    */
    function isValidDate($date){
        if( DateTime::createFromFormat('m/d/Y',$date)->format('m/d/Y') == $date ) {
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
    function getAge($birthday){
        $birthday = new DateTime($birthday);
        $interval = $birthday->diff(new DateTime);
        return $interval->y;
    }

    /**
     * check if the given birthday is above or equal to x years
     * @param $birthday
     * @param int $age
     * @return bool
     */
    function validateAge($birthday, $age = 18){
        // $birthday can be UNIX_TIMESTAMP or just a string-date.
        if(is_string($birthday)) {
            $birthday = strtotime($birthday);
        }

        // check
        // 31536000 is the number of seconds in a 365 days year.
        if(time() - $birthday < $age * 31536000)  {
            return false;
        }

        return true;
    }

    /**
     * check if x user exists
     * @param $username
     * @return bool
     */
    function userExist($username){
        $sql = "SELECT * FROM ". TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";
        if (!$result = mysqli_query($this->database->connection, $sql)) {
            $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), Message::Fatal, __FILE__, __LINE__ - 2);
            die;
        }

        if(mysqli_num_rows($result) > 0){
            return true;
        } else { return false;}
    }

    /**
     * check if x email exists
     * @param $email
     * @return bool
     */
    function emailExist($email){
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . " = '" . $email . "'";
        if (!$result = mysqli_query($this->database->connection, $sql)) {
            $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), Message::Fatal, __FILE__, __LINE__ - 2);
            die;
        }

        if(mysqli_num_rows($result) > 0){
            return true;
        } else { return false;}
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

    /**
     * Convert a string that its in a time format to an actual time based string ("Y-m-d H:i:s")
     * @param $time2
     * @return string
     */
    function calculateTime($time2){
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
        return implode(", ", $times);
    }

    /**
     * get a new id for a new user
     * @return int
     */
    function getNewID(){
        $sql = "SELECT ". TBL_USERS_ID ." FROM " . TBL_USERS . " ORDER BY " . TBL_USERS_ID . " DESC LIMIT 1";
        if (!$result = mysqli_query($this->database->connection, $sql)) {
            $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), Message::Fatal, __FILE__, __LINE__ - 2);
            die;
        }

        if(mysqli_num_rows($result) < 1){
            return 1;
        }

        $row = mysqli_fetch_assoc($result);
        return $row[TBL_USERS_ID] + 1;
    }

    // count the total number of online users using the script in a 1 minute radius
    function onlineCounter(){
        $totalUsers = 0;

        $sql = "SELECT * FROM ". TBL_HEARTBEAT;
        if (!$result = mysqli_query($this->database->connection, $sql)) {
            return 0;
        }

        if(mysqli_num_rows($result) < 1){
            return 0;
        }

        // count the timestamp for each person online atm
        while($row = mysqli_fetch_assoc($result)){
            $last_update = new DateTime($row[TBL_HEARTBEAT_TIMESTAMP]); // last time updated
            $currentTime = new DateTime(date("Y-m-d H:i:s", time())); // current time
            $timeDifference = $currentTime->diff($last_update); // count the difference
            if($timeDifference->i < 1){
                $totalUsers += 1;
            }
        }

        return $totalUsers;
    }

    /**
     * Get the user current level name
     * @param $level
     * @return string
     */
    function getUserLevelName($level = ""){
        if(empty($username) && $level == "") {
            $level = $this->userData->get(User::Level);
        }

        if($level == 0){
            return "Guest";
        } else if ($level == 1){
            return "User";
        } else if($level == 100){
            return "Administrator";
        }
    }

    /**
     * Check if given a user's account with the given (username or email) is activated
     * @param $data
     * @param $isEmail
     * @return bool
     */
    function is_userActivated($data, $isEmail = false){
        if($isEmail){
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . " = '" . $data . "'";
        } else {
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $data . "'";
        }

        if (!$result = mysqli_query($this->database->connection, $sql)) {
            $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->database->connection), Message::Fatal, __FILE__, __LINE__ - 2);
            die;
        }

        $row = mysqli_fetch_assoc($result);
        if($row[TBL_USERS_ACTIVATED] == 1){
            return true;
        } else { return false; }
    }

}