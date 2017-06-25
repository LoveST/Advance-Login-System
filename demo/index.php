<?php

/** Check user & site status **/
require "../init.php";
$session->statusCheck();
/** End check user & site status**/

$viewController->loadView("home.html");
