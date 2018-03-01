<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/12/2017
 * Time: 8:03 PM
 */

/** Check user & site status **/
require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();

use ALS\LoginStatus;

if ($session->statusCheck() == LoginStatus::AuthenticationNeeded) {

    if (isset($_POST["authCode"])) {

        // grab the post
        $authCode = $_POST['authCode'];

        // submit for check
        if ($session->authenticateUser($authCode)) {
            header("Location: index.php");
        }
    }

    // load the needed template
    $viewController->loadView("authentication.html");

} else {
    header("Location: index.php");
}
