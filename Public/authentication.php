<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/12/2017
 * Time: 8:03 PM
 */

use ALS\LoginStatus;

class Authentication
{

    public function __construct()
    {
        // init the required global variables
        global $session, $functions, $viewController;

        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        // check login status
        if ($session->statusCheck() == LoginStatus::AuthenticationNeeded) {

            if (isset($_POST["authCode"])) {

                // grab the post
                $authCode = $_POST['authCode'];

                // submit for check
                if ($session->authenticateUser($authCode)) {
                    header("Location: index.php");
                }
            }

            // load the needed template
            $viewController->loadView("authentication.html");

        } else {
            header("Location: index.php");
        }
    }

}

new Authentication();
