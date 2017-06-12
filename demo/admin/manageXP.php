<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/16/2017
 * Time: 11:23 PM
 */

/** Check user & site status **/
require "../../init.php";
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

if (isset($_POST['addXP'])) {
    $admin->addXP($_POST['username'], $_POST['amount']);
} else if (isset($_POST['subtractXP'])) {
    $admin->subtractXP($_POST['username'], $_POST['amount']);
}

$viewController->loadView("ad_manageXP.html");