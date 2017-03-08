<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 4:34 PM
 */

require "../../init.php";

if(!$user->isAdmin()){
    $message->customKill("Invalid Privileges","You do not have the permission to access this page",$settings->siteTheme());
    die();
}

if(isset($_POST['enable'])){
    $admin->activateHTTPS(true);
} else if(isset($_POST{'disable'})){
    $admin->activateHTTPS(false);
}

require "../templates/". $settings->get(Settings::SITE_THEME) ."/ad_setHTTPS.html";