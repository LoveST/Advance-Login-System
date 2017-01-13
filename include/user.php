<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 8:26 PM
 */

if(count(get_included_files()) ==1) exit("You don't have the permission to access this file."); // disable direct access to the file.
require "message.php";

class User {

    private $userData; // declare the required variables for the user data.
    private $message; // instance of the Message class.
    const First_Name = "firstName";
    const Last_Name = "lastName";
    const UserName = "username";
    const ID = "id";
    const Date_Joined = "date_joined";
    const Email = "email";
    const Level = "level";

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
        $this->message = new Message();
        $this->userData = $_SESSION["user_data"]; // pull put the needed information for the session if available.
    }

    /**
     * get the required user data as needed by using User::'Data Type'
     * @param $dataType
     * @return mixed
     */
    function get($dataType){
        return $this->userData[$dataType];
    }

}

$user = new User();