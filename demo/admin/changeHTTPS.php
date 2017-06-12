<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 4:34 PM
 */

/** Check user & site status **/
require "../../init.php";
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

if(isset($_POST['enable'])){
    $admin->activateHTTPS(true);
} else if(isset($_POST{'disable'})){
    $admin->activateHTTPS(false);
}

$viewController->loadView("ad_setHTTPS.html");