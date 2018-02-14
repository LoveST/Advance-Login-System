<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/13/2018
 * Time: 7:10 PM
 */

/** Check user & site status **/
require "../../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

// get the required random variables
$minPasswordLength = $settings->minPasswordLength();
$maxPasswordLength = $settings->maxPasswordLength();
$maxPinLength = $settings->maxRequiredPinLength();
$sameIpLogin = $settings->sameIpLogin();
$maxVerifiedDevices = $settings->maxVerifiedDevices();

$viewController->loadView("ad_randomVariables.html");