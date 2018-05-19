<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:54 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class Admin_searchUser
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $settings, $admin;

        // set the required variables
        global $totalUsers;
        $totalUsers = $admin->getUsers();

        // load the required scripts
        global $customScripts;
        $customScripts = '<script src="' . $settings->getTemplatesURL() . 'assets/js/popper.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/jquery.dataTables.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.bootstrap4.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.responsive.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/responsive.bootstrap4.min.js"></script>' . "\n";

        // load the view
        $viewController->loadView("ad_totalUsers.html");
    }
}

new Admin_searchUser();
