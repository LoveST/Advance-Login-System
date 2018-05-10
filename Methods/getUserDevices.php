<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/10/2018
 * Time: 3:35 PM
 */

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class Query_getUserInfo extends \ALS\Core
{

    public function __construct()
    {

        // construct the parent
        parent::__construct();
        parent::initClasses();

        // init the required global variables
        global $user;

        // print the required data
        echo json_encode($user->devices()->getDevicesArray());
    }

}

new Query_getUserInfo();