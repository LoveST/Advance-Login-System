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
/** End check user & site status**/

if($user->isAdmin()){

    $banedUsers = $admin->getBannedUsers();
    require "../templates/". $settings->get(Settings::SITE_THEME) ."/ad_bannedUsers.html";

} else {
    $message->customKill("Invalid Privileges","You do not have the permission to access this page",$settings->siteTheme());
}