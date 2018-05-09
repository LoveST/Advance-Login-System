<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/3/2018
 * Time: 9:30 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class User_View2Factor
{

    public function __construct()
    {
        // init the required global variables
        global $viewController;

        // load the required file
        $viewController->loadView("profile_view_2authCode.html");
    }
}

new User_View2Factor();