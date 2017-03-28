<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/1/2017
 * Time: 7:46 PM
 */

/** Check user & site status **/
require "../../init.php";
$session->statusCheck();
/** End check user & site status**/

if($user->isAdmin()){
    
    $totalUsers = $admin->getUsers();
    $banedUsers = $admin->getBannedUsers();
    $administrator = $admin->getAdmins();

    require "../templates/". $settings->get(Settings::SITE_THEME) ."/users_info.html";



} else {
    $message->customKill("Invalid Privileges","You do not have the permission to access this page",$settings->siteTheme());
}