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
                    header("Location: forgetPass.php?option=createNew&u='$username'&c='$code'");
                }
            }
            require "template/confirmPasswordReset.html";
            break;
        case "createNew";
            echo "pick your new password";
            break;
        default;

            if(isset($_POST['reset'])){
                if($session->forgetPasswordWithEmail($username,$email)){
                    echo "A reset code has been sent to " . $email;
                    die;
                }
            }

            require "template/resetPassword.html";
            break;
    }