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

    $banedUsers = $admin->getBannedUsers();
    require "../templates/". $settings->get(Settings::SITE_THEME) ."/ad_bannedUsers.html";