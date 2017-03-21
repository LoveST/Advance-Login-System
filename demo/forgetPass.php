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
$captcha = $_POST[''];

    switch($_GET['option']){
        case "confirm";
            if(isset($_POST['confirm'])) {
                if ($passwordManager->resetPasswordUsingCodeAndEmail($email, $code)) {
                    header("Location: forgetPass.php?option=createNew&u=$email&c=$code");
                }
            }
            require "templates/". $settings->get(Settings::SITE_THEME) ."/confirmPasswordReset.html";
            break;
        case "createNew";
            $decryptEmail = $database->escapeString($_GET['u']);
            $decryptCode = $database->escapeString($_GET['c']);
            $password = $database->escapeString($_POST['password']);
            $password2 = $database->escapeString($_POST['password2']);

            if(isset($_POST["change"])) {
                if ($passwordManager->confirmNewPassword($decryptEmail, $decryptCode, $password, $password2)) {
                    $success = true;
                }
            }

            require "templates/". $settings->get(Settings::SITE_THEME) ."/newPassword.html";
            break;
        default;
            if(isset($_POST['reset'])){
				//$template = file_get_contents('demo/templates/ubold/');
                if($passwordManager->forgetPasswordWithEmail($username, $email, true, file_get_contents('templates/'. $settings->get(Settings::SITE_THEME) . '/forgetPasswordEmail.html'))){
                    $success = true;
                }
            }
            require "templates/". $settings->get(Settings::SITE_THEME) ."/resetPassword.html";
            break;
    }