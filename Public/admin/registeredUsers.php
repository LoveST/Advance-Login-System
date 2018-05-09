<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/8/2017
 * Time: 6:27 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class Admin_registeredUsers
{

    public function __construct()
    {
        // init the required globals
        global $viewController;

        // init the required functions
        $this->init();

        // load the view
        $viewController->loadView("ad_registeredUsers.html");
    }

    public function init()
    {
        // init the required globals
        global $database, $admin;

        // grab the needed method to be used and switch between them
        $m = array_key_exists('m', $_GET) ? $_GET['m'] : null;
        $m = $database->secureInput($m);

        // set the required variables
        global $time, $sinceDate, $todayDate, $registeredUsers;

        switch ($m) {
            case 30;

                $time = strtotime(date("Y-m-d") . ' -30 days');
                $sinceDate = date("Y-m-d", $time);
                $todayDate = date("Y-m-d");
                $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate, $todayDate, 20);
                break;
            case 60;
                $time = strtotime(date("Y-m-d") . ' -60 days');
                $sinceDate = date("Y-m-d", $time);
                $todayDate = date("Y-m-d");
                $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate, $todayDate, 20);
                break;
            case 90;
                $time = strtotime(date("Y-m-d") . ' -90 days');
                $sinceDate = date("Y-m-d", $time);
                $todayDate = date("Y-m-d");
                $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate, $todayDate, 20);
                break;
            case 365;
                $time = strtotime(date("Y-m-d") . ' -365 days');
                $sinceDate = date("Y-m-d", $time);
                $todayDate = date("Y-m-d");
                $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate, $todayDate, 20);
                break;
            default;

                // show the first 10 registered users in the site
                $date = date("Y-m-d");
                $registeredUsers = $admin->getTotalRegisteredUsersInBetween("2000-01-01", $date, 10);
                break;
        }
    }
}

new Admin_registeredUsers();