<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/3/2018
 * Time: 9:15 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class User_changeEmail
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $profileManager;

        // check if form has been submitted
        if (isset($_POST['update'])) {
            if ($profileManager->setNewEmail($_POST['email'], $_POST['email2'], $_POST['pin'])) {
                header("Location: login.php");
            }
        }

        // load the view
        $viewController->loadView("profile_change_email.html");
    }
}

new User_changeEmail();