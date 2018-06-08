<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 4:34 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class ad_changeHTTPS
{

    public function __construct()
    {

        // init the required globals
        global $admin, $viewController;

        if (isset($_POST['enable'])) {
            $admin->activateHTTPS(true);
        } else if (isset($_POST{'disable'})) {
            $admin->activateHTTPS(false);
        }

        // load the view
        $viewController->loadView("ad_setHTTPS.html");
    }

}

new ad_changeHTTPS();