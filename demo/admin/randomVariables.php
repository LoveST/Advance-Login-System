<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/13/2018
 * Time: 7:10 PM
 */

// get the required random variables
$minPasswordLength = $settings->minPasswordLength();
$maxPasswordLength = $settings->maxPasswordLength();
$maxPinLength = $settings->maxRequiredPinLength();
$sameIpLogin = $settings->sameIpLogin();
$maxVerifiedDevices = $settings->maxVerifiedDevices();