<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/1/2017
 * Time: 7:22 PM
 */
class Administrator{

    /**
     * init the class
     */
    function init(){

    }

    function getTotalUsers(){

        // define all the global variables
        global $database;

        $sql = "SELECT count(*) FROM ". TBL_USERS;
        $result = mysqli_query($database->connection,$sql);
        $num = mysqli_fetch_row($result);
        return $num[0];
    }

    /**
     * Get all the current admins in an array form
     * @param int $limit
     * @return array|bool
     */
    function getAdmins($limit = 0){

        // define all the global variables
        global $database;

        if($limit == 0){
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_LEVEL . "='100'";
        } else {
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_LEVEL . "='100' LIMIT ". $limit;
        }
        $admins = "";
        $results = mysqli_query($database->connection,$sql);

        if(mysqli_num_rows($results) < 1){
            return false;
        }

        while($row = mysqli_fetch_assoc($results)){
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
    function getUsers($limit = 0){

        // define all the global variables
        global $database;

        if($limit == 0){
            $sql = "SELECT * FROM ". TBL_USERS;
        } else {
            $sql = "SELECT * FROM ". TBL_USERS. " LIMIT ". $limit;
        }

        $results = mysqli_query($database->connection,$sql);
        $users = "";

        if(mysqli_num_rows($results) < 1){
            return false;
        }

        while($row = mysqli_fetch_assoc($results)){
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
    function getBannedUsers($limit = 0){

        // define all the global variables
        global $database;

        if($limit == 0){
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_BANNED . "='1'";
        } else {
            $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_BANNED . "='1' LIMIT ". $limit;
        }

        $results = mysqli_query($database->connection,$sql);
        $users = "";

        if(mysqli_num_rows($results) < 1){
            return false;
        }

        while($row = mysqli_fetch_assoc($results)){
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
    function activateHTTPS($activate){

        // define all the global variables
        global $database, $message, $settings;

        if($activate){
            // check if already activated
            if ($settings->isHTTPS()) {
                return false;
            }

            $sql = "UPDATE ". TBL_SETTINGS . " SET value = '1' WHERE field = '". TBL_SETTINGS_FORCE_HTTPS . "'";
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

            $sql = "UPDATE ". TBL_SETTINGS . " SET value = '0' WHERE field = '". TBL_SETTINGS_FORCE_HTTPS . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
                die;
            }

            //if no error then set the success message
            $message->setSuccess("You have de-activated ssl across your script");
            return true;
        }
    }

}