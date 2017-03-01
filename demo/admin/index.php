<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:28 PM
 */

require "../../init.php";
if($user->isAdmin()) {
    require "../templates/" . $settings->get(Settings::SITE_THEME) . "/ad_main_panel.html";
} else {
    $message->customKill("Invalid Privileges","You do not have the permission to access this page",$settings->siteTheme());
}