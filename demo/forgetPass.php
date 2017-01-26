<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/15/2017
 * Time: 11:07 PM
 */

require "../init.php";
$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$username = $_POST['username'];
$email = $_POST['email'];
$code = $_POST['code'];

    switch($_GET['option']){
        case "confirm";
            if(isset($_POST['confirm'])) {
                if ($session->resetPasswordUsingCodeAndEmail($email, $code)) {
                    $newEmail = $functions->encryptIt($email);
                    $newCode = $functions->encryptIt($code);
                    header("Location: forgetPass.php?option=createNew&u='$newEmail'&c='$newCode'");
                }
            }
            require "templates/". $settings->get(Settings::SITE_THEME) ."/confirmPasswordReset.html";
            break;
        case "createNew";
            $decryptEmail = $functions->decryptIt($database->escapeString($_GET['u']));
            $decryptCode = $functions->decryptIt($database->escapeString($_GET['c']));
            $password = $database->escapeString($_POST['password']);
            $password2 = $database->escapeString($_POST['password2']);

            if(isset($_POST["change"])) {
                if ($session->pickNewPassword($decryptEmail, $decryptCode, $password, $password2)) {
                    $success = true;
                }
            }

            require "templates/". $settings->get(Settings::SITE_THEME) ."/newPassword.html";
            break;
        default;
            if(isset($_POST['reset'])){
                if($session->forgetPasswordWithEmail($username,$email)){
                    $success = true;
                }
            }
            require "templates/". $settings->get(Settings::SITE_THEME) ."/resetPassword.html";
            break;
    }