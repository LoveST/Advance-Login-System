<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/12/2017
 * Time: 9:01 PM
 */

/** Check user & site status **/
require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();

use ALS\LoginStatus;

if ($session->statusCheck() == LoginStatus::VerifyDevice) {

    if (isset($_POST["pin"])) {

        // grab the post
        $pin = $_POST['pin'];

        // submit for check
        if ($session->verifyDevice($pin)) {
            header("Location: index.php");
        }

    }

    // load the needed template
    $viewController->loadView("verify_device.html");

} else {
    header("Location: index.php");
}