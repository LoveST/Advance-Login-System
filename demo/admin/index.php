<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:28 PM
 */

/** Check user & site status **/
require "../init.php";
$init = new init("../../Core.php");
$init->loginCheck();
$session->adminCheck();
/** End check user & site status**/

// load the header
$viewController->loadView("ad_main_panel_header.html");

// get the required page
$page = $database->secureInput($_GET['page']);

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
