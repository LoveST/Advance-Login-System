<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 3/8/2018
 * Time: 3:14 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class User_verifiedDevices
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $devices;

        // get the verified devices
        $devices = $devices->getDevices();

        // load the view
        $viewController->loadView("profile_view_verifiedDevices.html");
    }
}

new User_verifiedDevices();