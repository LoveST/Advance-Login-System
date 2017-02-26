<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/25/2017
 * Time: 4:12 PM
 */
class xp{

    private $database; // instance of the database class
    private $connection; // declare the connection variable for easy connection to sql
    private $user; // instance of the current user class


    /**
     * Init the class
     * @param $database
     * @param $user
     */
    public function init($database, $user){
        $this->database = $database;
        $this->connection = $database->connection;
        $this->user = $user;
    }

    public function getXP(){
        if($this->user[TBL_USERS_XP] == ""){
            return 0;
        } else {
            return $this->user[TBL_USERS_XP];
        }
    }

    /**
     * Add x amount of xp to the user
     * @param $amount
     * @return bool
     */
    public function addXP($amount){
        if(!is_int($amount)){
            return false;
        }

        // check if double xp then double the amount
        if($this->is_DoubleXP()){
            $amount = $amount * 2;
        }

        // add the old user xp to the new one
        $newXP = $this->getXP() + $amount;

        $sql = "UPDATE ". TBL_USERS. " SET ". TBL_USERS_XP . " = '". $newXP . "' WHERE ". TBL_USERS_USERNAME . " = '". $this->user[TBL_USERS_USERNAME] . "'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            return false;
        }

        // update the current xp level that the user has
        $this->user[TBL_USERS_XP] = $newXP;
        $_SESSION['user_data'][TBL_USERS_XP] = $newXP;

        return true;
    }

    /**
     * Subtract x amount of xp from the user
     * @param $amount
     * @return bool
     */
    public function subtractXP($amount){
        if(!is_int($amount)){
            return false;
        }

        // add the old user xp to the new one
        $newXP = $this->getXP() - $amount;

        // check if newXP is less than 0 then set it to 0
        if($newXP < 0){
            $newXP = 0;
        }

        $sql = "UPDATE ". TBL_USERS. " SET ". TBL_USERS_XP . " = '". $newXP . "' WHERE ". TBL_USERS_USERNAME . " = '". $this->user[TBL_USERS_USERNAME] . "'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            return false;
        }

        // update the current xp level that the user has
        $this->user[TBL_USERS_XP] = $newXP;
        $_SESSION['user_data'][TBL_USERS_XP] = $newXP;

        return true;
    }

    public function setDoubleXP(){

    }

    /**
     * Check if double xp is activated for the current user
     * @return bool
     */
    public function is_DoubleXP(){
        return false;
    }

    /**
     * Get the total amount of xp lost since join
     * @return int
     */
    public function getLostXP(){
        return $this->user[TBL_USERS_LOST_XP];
    }

}