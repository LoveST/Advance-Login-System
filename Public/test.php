<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/6/2017
 * Time: 12:27 AM
 */


/**
 *
 *  html ->   {:url:loginPage}
 *  php  ->   split {:url:  -  variable  -  }
 *
 *  SQL  ->         variable        address
 *---------------------------------------------------------------
 *Example :         loginPage       {:settings_siteURL}login.php
 *---------------------------------------------------------------
 *
 * Class ->   url
 *
 *
 *
 *
 * profile/index.php?page=...
 * admin/index.php?page=...
 *
 * To-Do => fix an exploit that the user can call a specific file from a different folder and path using the url
 */
echo $settings->maxVerifiedDevices() . "<br>";
var_dump(sizeof($user->devices()->getDevicesArray()));
$core->loadDirectories();