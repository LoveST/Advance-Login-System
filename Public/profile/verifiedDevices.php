<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 3/8/2018
 * Time: 3:14 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// get the verified devices
$devices = $user->devices()->getDevices();

// load the view
$viewController->loadView("profile_view_verifiedDevices.html");