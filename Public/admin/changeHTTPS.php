<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 4:34 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

if(isset($_POST['enable'])){
    $admin->activateHTTPS(true);
} else if(isset($_POST{'disable'})){
    $admin->activateHTTPS(false);
}

// set the required variables


// load the view
$viewController->loadView("ad_setHTTPS.html");