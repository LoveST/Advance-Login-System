<?php

/** Check user & site status **/
require "../init.php";
$session->statusCheck();
/** End check user & site status**/

require "templates/". $settings->get(Settings::SITE_THEME) ."/home.html";
