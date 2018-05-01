<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:55 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// set the required templates
$canLogin = $settings->get(TBL_SETTINGS_LOGIN_ENABLE);
$canRegister = $settings->get(TBL_SETTINGS_REGISTER_ENABLE);
$pinRequired = $settings->get(TBL_SETTINGS_PIN_REQUIRED);
$activationRequired = $settings->get(TBL_SETTINGS_ACTIVATION_REQUIRED);
$minimumAgeRequired = $settings->get(TBL_SETTINGS_MINIMUM_AGE_REQUIRED);
$minimumAge = $settings->minimumAge();

// load the view
$viewController->loadView("ad_authenticationSettings.html");