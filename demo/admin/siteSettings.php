<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/28/2017
 * Time: 3:04 PM
 */

/** Check user & site status **/
require "../../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

$viewController->loadView("ad_siteSettings.html");

