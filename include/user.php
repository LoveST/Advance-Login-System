<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 8:26 PM
 */
if(count(get_included_files()) ==1) exit("You don't have the permission to access this file."); // disable direct access to the file.
require "user/xp.php";

class User {

    private $userData; // declare the required variables for the user data.
    private $message; // instance of the Message class.
    private $database; // instance of the database class
    private $xp; // instance of the XP class
    const First_Name = TBL_USERS_FNAME;
    const Last_Name = TBL_USERS_LNAME;
    const UserName = TBL_USERS_USERNAME;
    const ID = TBL_USERS_ID;
    const Date_Joined = TBL_USERS_DATE_JOINED;
    const Email = TBL_USERS_EMAIL;
    const Level = TBL_USERS_LEVEL;
    const Banned = TBL_USERS_BANNED;

    /**
     * User constructor for PHP4
     */
    function User(){
        $this->__construct();
    }

    /**
     * User constructor for PHP5.
     */
    function __construct(){
        //$this->userData = $_SESSION["user_data"]; // pull put the needed information for the session if available.
        $this->xp = new xp();
    }

    /**
     * init the class
     * @param $message
     * @param $database
     */
    function init($database, $message){
        $this->message = $message;
        $this->database = $database;
    }

    /**
     * init an instance of this class and supply it with the user data and the other classes
     * @param $data
     * @param $database
     * @param $message
     */
    function initInstance($data, $database, $message){
        $this->database = $database;
        $this->message = $message;
        $this->userData = $data;
        $this->xp = new xp(); // new instance of the xp class
        $this->xp->init($this->database, $this->userData); // init the XP class (after all the user data is been loaded)
    }

    /**
     * Re initiate the user data if cookies were to be found after the class init
     */
    function initUserData(){
        $this->userData = $_SESSION["user_data"]; // pull put the needed information for the session if available.
        $this->xp = new xp(); // new instance of the xp class
        $this->xp->init($this->database, $this->userData); // init the XP class (after all the user data is been loaded)
    }

    /**
     * return the instance of the xp class
     * @return xp
     */
    function xp(){
        return $this->xp;
    }

    /**
     * Check if the current user is an admin
     * @return bool
     */
    public function isAdmin(){
        if($this->userData[TBL_USERS_LEVEL] == 100){ return true; } else { return false; }
    }

    /**
     * get the required user data as needed by using User::'Data Type'
     * @param $dataType
     * @return mixed
     */
    function get($dataType){
        return $this->userData[$dataType];
    }

    function username(){
        return $this->userData[TBL_USERS_USERNAME];
    }

    function id(){
        return $this->userData[TBL_USERS_ID];
    }

    function email(){
        return $this->userData[TBL_USERS_EMAIL];
    }

    function dateJoined(){
        return $this->userData[TBL_USERS_DATE_JOINED];
    }

    function level(){
        return $this->userData[TBL_USERS_LEVEL];
    }

}

abstract class userLevel{
    const Guest = 0;
    const User = 1;
    const Administrator = 100;
}

$user = new User();