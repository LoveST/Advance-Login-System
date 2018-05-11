<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/10/2018
 * Time: 5:49 PM
 */

class getInfo extends \ALS\Core
{

    public function __construct()
    {
        // construct the parent
        parent::__construct();

        // load the database class
        parent::initClasses();
        //parent::loadClasses($classes);

        // hold the return array
        global $user;
        $results = array("fName" => $user->getFirstName(), "lName" => $user->getLastName(), "age" => $user->getAge(), "email" => $user->getEmail());

        // return the results
        echo json_encode($results);
        exit();
    }

}

new getInfo();