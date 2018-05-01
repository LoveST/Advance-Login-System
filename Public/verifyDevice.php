<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/12/2017
 * Time: 9:01 PM
 */

use ALS\LoginStatus;

class VerifyDevice
{

    public function __construct()
    {
        // init the required global variables
        global $session, $functions, $viewController;

        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        // check login status
        if ($session->statusCheck() == LoginStatus::VerifyDevice) {

            if (isset($_POST["pin"])) {

                // grab the post
                $pin = $_POST['pin'];

                // submit for check
                if ($session->verifyDevice($pin)) {
                    header("Location: index.php");
                }

            }

            // load the needed template
            $viewController->loadView("verify_device.html");

        } else {
            header("Location: index.php");
        }
    }
}

new VerifyDevice();