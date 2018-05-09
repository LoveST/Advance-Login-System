<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/8/2018
 * Time: 9:05 PM
 */


// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class User_changePinNumber
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $profileManager, $functions, $settings;

        // insert custom scripts
        $viewController->addCustomScript(' <script src="' . $settings->getTemplatesURL() . 'plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>');
        $viewController->addCustomScript('<script src="' . $settings->getTemplatesURL() . 'plugins/autoNumeric/autoNumeric.js" type="text/javascript"></script>');

        // check if form has been submitted
        if (isset($_POST['update'])) {

            // call the update function
            if ($profileManager->setNewPin($_POST['password'], $_POST['pin'], $_POST['newPin'], $_POST['confirmNewPin'])) {
                $functions->directBackToSource("index.php");
            }
        }

        // load the view
        $viewController->loadView("profile_change_pinNumber.html");
    }
}

new User_changePinNumber();