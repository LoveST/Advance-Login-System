<?php

/** Check user & site status **/
require "init.php";
$init = new init("../Core.php");
$init->loginCheck();
/** End check user & site status**/

$viewController->loadView("home.html");