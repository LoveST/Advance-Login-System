<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/13/2018
 * Time: 7:10 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// set the required variables
$minPasswordLength = $settings->minPasswordLength();
$maxPasswordLength = $settings->maxPasswordLength();
$maxPinLength = $settings->maxRequiredPinLength();
$sameIpLogin = $settings->sameIpLogin();
$maxVerifiedDevices = $settings->maxVerifiedDevices();

// load the view
$viewController->loadView("ad_randomVariables.html");