<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:54 PM
 */

/** Check user & site status **/
require "../../init.php";
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

if(isset($_POST['update'])){

    $siteSecretKey = $_POST['site_secret'];
    $captchaKey = $_POST['captcha_key'];
    $captchaSecretKey = $_POST['captcha_secret'];

    // call the update security settings method
    $admin->updateSecuritySettings($siteSecretKey, $captchaKey, $captchaSecretKey);

}

require "../templates/". $settings->get(Settings::SITE_THEME) ."/ad_securitySettings.html";