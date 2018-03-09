<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/3/2018
 * Time: 8:21 PM
 */

/** Check user & site status **/
require "../init.php";
$init = new init("../../Core.php");
$init->loginCheck();
/** End check user & site status**/

// load the header
$viewController->loadView("profile_main_panel_header.html");

// get the required page
$page = $database->secureInput($_GET['page']);
// split the text and check if starts with "../" or "./" or "/"
if (strpos($page, '../') === 0 || strpos($page, './') === 0 || strpos($page, '/') === 0 || strpos($page, '~') === 0) {
    $error = true;
}


// check if page is empty
if (!empty($page) && $page != "index" && file_exists($page . ".php") && !$error) {

    // load the required page file
    include $page . ".php";
} else {

    // load default view
    $viewController->loadView("admin_main_panel_default.html");

}

// load the footer
$viewController->loadView("profile_main_panel_footer.html");

