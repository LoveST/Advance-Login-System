<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 4/20/2018
 * Time: 12:10 AM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class User_changePassword
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $profileManager, $functions;

        // check if form has been submitted
        if (isset($_POST['update'])) {
            if ($profileManager->setNewPassword($_POST['oldPass'], $_POST['pinNumber'], $_POST['newPass'], $_POST['confirmNewPass'])) {
                $functions->directBackToSource("login.php");
            }
        }

        // load the view
        $viewController->loadView("profile_change_password.html");
    }
}

new User_changePassword();