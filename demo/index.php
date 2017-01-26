<?php

require "../init.php";

/*********/
$message->getError();
/*********/

if(!$settings->siteEnabled()){
    $message->kill("The Site is disabled at the moment",__FILE__,__LINE__);
}

if(!$settings->get(Settings::Login_Enabled) || !$settings->canLogin()){
    echo "Login has been disabled for the moment!";
}

if($session->logged_in()){

    require "templates/". $settings->get(Settings::SITE_THEME) ."/home.html";

} else {
    header("Location: login.php");
}