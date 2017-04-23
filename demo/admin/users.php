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
$session->adminCheck();
/** End check user & site status**/

    $totalUsers = $admin->getUsers();
    $banedUsers = $admin->getBannedUsers();
    $administrator = $admin->getAdmins();

    require "../". TEMPLATE_PATH ."/users_info.html";