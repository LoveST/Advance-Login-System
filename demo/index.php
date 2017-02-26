<?php

require "../init.php";

if($settings->siteDisabled()){
    $message->customKill("Oops!!!","The Site is disabled at the moment", $settings->get(Settings::SITE_THEME));
}

if($session->logged_in()){

    require "templates/". $settings->get(Settings::SITE_THEME) ."/home.html";
    
} else {
    header("Location: login.php");
}