<?php

require "../init.php";

if($settings->siteDisabled()){
    $message->customKill("Oops!!!","The Site is disabled at the moment", $settings->get(Settings::SITE_THEME));
}

if(!$settings->get(Settings::Login_Enabled) || !$settings->canLogin()){
    echo "Login has been disabled for the moment!";
}

if($session->logged_in()){

    require "templates/". $settings->get(Settings::SITE_THEME) ."/home.html";

} else {
    header("Location: login.php");
}