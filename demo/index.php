<?php

/** Check user & site status **/
require "../init.php";
$status = $session->statusCheck();
use ALS\Session\LoginStatus;

if ($status == LoginStatus::NeedToLogin) {
    header("Location: login.php");
} else if ($status == LoginStatus::VerifyDevice) {
    header("Location: verifyDevice.php");
} else if ($status == LoginStatus::AuthenticationNeeded) {
    header("Location: authentication.php");
}
/** End check user & site status**/

$viewController->loadView("home.html");
