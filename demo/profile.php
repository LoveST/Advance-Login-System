<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 1:47 PM
 */

/** Check user & site status **/
require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$status = $session->statusCheck();

use ALS\LoginStatus;

if ($status == LoginStatus::NeedToLogin) {
    header("Location: login.php");
} else if ($status == LoginStatus::VerifyDevice) {
    header("Location: verifyDevice.php");
} else if ($status == LoginStatus::AuthenticationNeeded) {
    header("Location: authentication.php");
}
/** End check user & site status**/


$action = array_key_exists('ac', $_GET) ? $_GET['ac'] : null;

switch ($action) {
    case "change_username";

        if (isset($_POST['update'])) {
            if ($profileManager->setNewUsername($_POST['username'], $_POST['pin'])) {
                header("Location: login.php");
            }
        }

        $viewController->loadView("profile_change_username.html");
        break;
    case "verified_devices";
        $devices = $user->devices()->getDevices();

        $viewController->loadView("profile_verified_devices.html");
        break;
    case "change_information";
        echo "hello";
        break;
    case "change_email";

        if (isset($_POST['update'])) {
            if ($profileManager->setNewEmail($_POST['email'], $_POST['email2'], $_POST['pin'])) {
                header("Location: login.php");
            }
        }

        $viewController->loadView("profile_change_email.html");
        break;
    case "change_password";

        if (isset($_POST['update'])) {
            if ($profileManager->setNewPassword($_POST['oldPass'], $_POST['pinNumber'], $_POST['newPass'], $_POST['confirmNewPass'])) {
                header("Location: login.php");
            }
        }

        $viewController->loadView("profile_change_password.html");
        break;
    case "change_pin";

        if (isset($_POST['update'])) {
            if ($profileManager->setNewPin($_POST['currentPass'], $_POST['currentPin'], $_POST['newPin'], $_POST['confirmPin'])) {
                header("Location: login.php");
            }
        }

        $viewController->loadView("profile_change_pin.html");
        break;
    case "view_2authCode";

        $viewController->loadView("profile_view_2authCode.html");

        break;
    case "generate_2factorCode";

        // check if post already been submitted
        if (!isset($_POST['generate'])) {

            $viewController->loadView("profile_generate_2factorCode.html");

        }


        break;
    default;
        $viewController->loadView("profile_main.html");
        break;
}