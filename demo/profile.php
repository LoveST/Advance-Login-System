<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 1:47 PM
 */

require "../init.php";

/** Check user & site status **/
$session->statusCheck();
/** End check user & site status**/


$action = $_GET['ac'];

switch($action){
    case "change_username";

        if(isset($_POST['update'])){
            if($profileManager->setNewUsername($_POST['username'], $_POST['pin'])){
                header("Location: login.php");
            }
        }

        require "templates/" . $settings->get(Settings::SITE_THEME) .  "/profile_change_username.html";
        break;
    case "verified_devices";
        $devices = $user->devices()->getDevices();

        require "templates/" . $settings->get(Settings::SITE_THEME) .  "/profile_verified_devices.html";
        break;
    case "change_information";
        echo "hello";
        break;
    case "change_email";

        if(isset($_POST['update'])){
            if($profileManager->setNewEmail($_POST['email'], $_POST['email2'], $_POST['pin'])){
                header("Location: login.php");
            }
        }

        require "templates/" . $settings->get(Settings::SITE_THEME) .  "/profile_change_email.html";
        break;
    case "change_password";
        echo "hello";
        break;
    case "change_pin";
        echo "hello";
        break;
    default;
        require "templates/" . $settings->get(Settings::SITE_THEME) .  "/profile_main.html";
        break;
}