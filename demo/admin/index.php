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
$viewController->loadView("ad_main_panel_header.html");

// get the required page
$page = $_GET['page'];

// check if page is empty
if (!empty($page) && $page != "index" && file_exists($page . ".php")) {

    // load the required page file
    include $page . ".php";
} else {

    // load default view
    $viewController->loadView("admin_main_panel_default.html");

}

// load the footer
$viewController->loadView("ad_main_panel_footer.html");
