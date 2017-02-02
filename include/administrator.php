<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/1/2017
 * Time: 7:22 PM
 */
class Administrator{

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

    function getTotalUsers(){
        $sql = "SELECT count(*) FROM ". TBL_USERS;
        $result = mysqli_query($this->database->connection,$sql);
        $num = mysqli_fetch_row($result);
        return $num[0];
    }

    /**
     * Get all the current admins in an array form
     * @param int $limit
     * @return array|bool
     */
    function getAdmins($limit = 0){
        if($limit == 0){
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_LEVEL . "='100'";
        } else {
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_LEVEL . "='100' LIMIT ". $limit;
        }
        $admins = "";
        $results = mysqli_query($this->database->connection,$sql);

        if(mysqli_num_rows($results) < 1){
            return false;
        }

        while($row = mysqli_fetch_assoc($results)){
            $admins[] = $row;
        }
        return $admins;
    }

    /**
     * Get the total users in the database
     * @param int $limit
     * @return array|bool
     */
    function getUsers($limit = 0){
        if($limit == 0){
            $sql = "SELECT * FROM ". TBL_USERS;
        } else {
            $sql = "SELECT * FROM ". TBL_USERS. " LIMIT ". $limit;
        }

        $results = mysqli_query($this->database->connection,$sql);
        $users = "";

        if(mysqli_num_rows($results) < 1){
            return false;
        }

        while($row = mysqli_fetch_assoc($results)){
            $users[] = $row;
        }
        return $users;
    }

    /**
     * get the total users that are banned from the database
     * @param int $limit
     * @return array|bool|string
     */
    function getBannedUsers($limit = 0){
        if($limit == 0){
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_BANNED . "='1'";
        } else {
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_BANNED . "='1' LIMIT ". $limit;
        }

        $results = mysqli_query($this->database->connection,$sql);
        $users = "";

        if(mysqli_num_rows($results) < 1){
            return false;
        }

        while($row = mysqli_fetch_assoc($results)){
            $users[] = $row;
        }
        return $users;
    }

}