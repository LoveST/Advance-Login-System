<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/8/2017
 * Time: 6:27 PM
 */
/** Check user & site status **/
require "../../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

// grab the needed method to be used and switch between them
$m = $_GET['m'];

switch ($m) {
    case 30;

        $time = strtotime(date("Y-m-d") . ' -30 days');
        $sinceDate = date("Y-m-d", $time);
        $todayDate = date("Y-m-d");
        $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate,$todayDate,20);
        break;
    case 60;
        $time = strtotime(date("Y-m-d") . ' -60 days');
        $sinceDate = date("Y-m-d", $time);
        $todayDate = date("Y-m-d");
        $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate,$todayDate,20);
        break;
    case 90;
        $time = strtotime(date("Y-m-d") . ' -90 days');
        $sinceDate = date("Y-m-d", $time);
        $todayDate = date("Y-m-d");
        $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate,$todayDate,20);
        break;
    case 365;
        $time = strtotime(date("Y-m-d") . ' -365 days');
        $sinceDate = date("Y-m-d", $time);
        $todayDate = date("Y-m-d");
        $registeredUsers = $admin->getTotalRegisteredUsersInBetween($sinceDate,$todayDate,20);
        break;
    default;

        // show the first 10 registered users in the site
        $date = date("Y-m-d");
        $registeredUsers = $admin->getTotalRegisteredUsersInBetween("2000-01-01",$date,10);

        break;
}

$viewController->loadView("ad_registeredUsers.html");