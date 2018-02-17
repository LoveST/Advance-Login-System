<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:28 PM
 */

/** Check user & site status **/
require "../../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$status = $session->statusCheck();

use ALS\LoginStatus;

if ($status == LoginStatus::NeedToLogin) {
    header("Location: ../login.php");
} else if ($status == LoginStatus::VerifyDevice) {
    header("Location: ../verifyDevice.php");
} else if ($status == LoginStatus::AuthenticationNeeded) {
    header("Location: ../authentication.php");
}
/** End check user & site status**/

// load the header
$viewController->loadView("ad_main_panel.html");

// get the required page
$page = $_GET['page'];
switch ($page) {
    case "siteSettings":
        $viewController->loadView("ad_siteSettings.html");
        break;
    case "randomVariables":
        require_once "randomVariables.php";
        $viewController->loadView("ad_randomVariables.html");
        break;
    default:

        break;
}

// load the footer
$viewController->loadView("ad_main_panel_footer.html");
