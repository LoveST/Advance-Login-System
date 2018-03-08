<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 3/8/2018
 * Time: 3:25 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// load the view
$viewController->loadView("profile_change_accountInfo.html");