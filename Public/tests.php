<?php
include "../Core.php";

use ALS\searchBy;

$core = new \ALS\Core();
$core->initClasses();

// hold the required custom variables
$vars = array("loginLink" => "http://www.lovemst.com");
$viewController->setCustomReservedCharacters($vars);

// translate the page
$file = $viewController->preLoadView("mail_user_login_link.html");

echo $file;