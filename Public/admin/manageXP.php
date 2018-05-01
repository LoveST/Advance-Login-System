<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/16/2017
 * Time: 11:23 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class Admin_ManageXP
{

    public function __construct()
    {
        // init the required globals
        global $admin, $viewController;

        if (isset($_POST['addXP'])) {
            $admin->addXP($_POST['username'], $_POST['amount']);
        } else if (isset($_POST['subtractXP'])) {
            $admin->subtractXP($_POST['username'], $_POST['amount']);
        }

        // load the view
        $viewController->loadView("ad_manageXP.html");

    }

}

new Admin_ManageXP();