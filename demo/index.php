<?php

require "../init.php";

/** Check user & site status **/
$session->statusCheck();
/** End check user & site status**/

require "templates/". $settings->get(Settings::SITE_THEME) ."/home.html";
