<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/8/2017
 * Time: 7:59 PM
 */

/** Check user & site status **/
require "../../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

// check if form has been submitted
if(isset($_POST['add'])){

    // grab the needed information
    $name = $_POST['name'];
    $level = $_POST['level'];
    $permissionsString = $_POST['permissions'];

    // split the text every ','
    $permissions = explode(",", $permissionsString);

    // pass the values to the actual functions for validation and further process
    $admin->addNewLevel($name, $level, $permissions);

}

$viewController->loadView("ad_addLevel.html");