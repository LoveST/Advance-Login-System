<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:28 PM
 */

/** Check user & site status **/
require "../../init.php";
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

$viewController->loadView("ad_main_panel.html");
