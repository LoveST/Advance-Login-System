<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/15/2017
 * Time: 11:07 PM
 */

class ForgetPass
{

    public function __construct()
    {
        // init the required global variables
        global $functions, $viewController, $passwordManager, $database, $settings;

        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        // set the required variables
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $username = $_POST['username'];
        $email = $_POST['email'];
        $code = $_POST['code'];
        $captchaInput = $_POST['g-recaptcha-response'];

        // get the current _GET variable
        $page = array_key_exists('option', $_GET) ? $_GET['option'] : null;
        $page = $database->secureInput($page);

        // continue with the process
        switch ($page) {
            case "confirm";
                if (isset($_POST['confirm'])) {
                    if ($passwordManager->resetPasswordUsingCodeAndEmail($email, $code)) {
                        header("Location: forgetPass.php?option=createNew&u=$email&c=$code");
                    }
                }

                $viewController->loadView("confirmPasswordReset.html");
                break;
            case "createNew";
                $decryptEmail = $database->escapeString($_GET['u']);
                $decryptCode = $database->escapeString($_GET['c']);
                $password = $database->escapeString($_POST['password']);
                $password2 = $database->escapeString($_POST['password2']);

                if (isset($_POST["change"])) {
                    if ($passwordManager->confirmNewPassword($decryptEmail, $decryptCode, $password, $password2)) {
                        $success = true;
                    }
                }

                // load the view
                $viewController->loadView("newPassword.html");
                break;
            default;
                if (isset($_POST['reset'])) {
                    //$template = file_get_contents('demo/templates/ubold/');
                    if ($passwordManager->forgetPasswordWithEmail($username, $email, $captchaInput, true, file_get_contents('templates/' . $settings->get(ALS\Settings::SITE_THEME) . '/forgetPasswordEmail.html'))) {
                        $success = true;
                    }
                }

                // load the view
                $viewController->loadView("resetPassword.html");
                break;
        }
    }
}

new ForgetPass();