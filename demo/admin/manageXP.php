<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/16/2017
 * Time: 11:23 PM
 */

require "../../init.php";

if(!$user->isAdmin()){
    $message->customKill("Invalid Privileges","You do not have the permission to access this page",$settings->siteTheme());
    die();
}

if(isset($_POST['addXP'])){
$functions->addXP($_POST['username'], $_POST['amount']);
} else if(isset($_POST['subtractXP'])){
    $functions->subtractXP($_POST['username'], $_POST['amount']);
}

require "../templates/". $settings->get(Settings::SITE_THEME) ."/ad_manageXP.html";