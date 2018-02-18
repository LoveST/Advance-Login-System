<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/28/2017
 * Time: 3:04 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// set the required variables
$siteName = $settings->siteName();
$siteURL = $settings->siteURL();
$siteEmail = $settings->siteEmail();
$siteEnabled = $settings->get(TBL_SETTINGS_SITE_ENABLED);
$siteTheme = $settings->siteTheme();
$siteLanguage = $settings->siteLanguage();

// load the view
$viewController->loadView("ad_siteSettings.html");