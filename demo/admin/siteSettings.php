<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/28/2017
 * Time: 3:04 PM
 */

/** Check user & site status **/
require "../../init.php";
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

require "../templates/". $settings->get(Settings::SITE_THEME) ."/ad_siteSettings.html";

