<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/13/2018
 * Time: 5:23 PM
 */

/** Check user & site status **/
require "../../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

// get every single group
$listGroups = $groups->listGroups();

// check if form has been submitted
if (isset($_POST['add'])) {

    // grab the needed information
    $groupName = $_POST['groupName'];
    $permissionsString = $_POST['permissions'];

    // split the text every ','
    $permissions = explode(",", $permissionsString);

    // pass the values to the actual functions for validation and further process
    $groups->addPermission($permissions, $groupName);

}

$viewController->loadView("ad_addPermission.html");