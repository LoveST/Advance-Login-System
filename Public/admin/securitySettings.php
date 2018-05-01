<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:54 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

if (isset($_POST['update'])) {

    $siteSecretKey = $_POST['site_secret'];
    $captchaKey = $_POST['captcha_key'];
    $captchaSecretKey = $_POST['captcha_secret'];

    // call the update security settings method
    $admin->updateSecuritySettings($siteSecretKey, $captchaKey, $captchaSecretKey);

}

// set the required variables
$siteSecretKey = $settings->secretKey();
$captchaKey = $captcha->getSiteKey();
$captchaSecretKey = $captcha->getSecretKey();

// load the view
$viewController->loadView("ad_securitySettings.html");