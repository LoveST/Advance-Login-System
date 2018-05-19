<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/8/2017
 * Time: 7:59 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class Admin_addGroup
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $settings, $admin;

        // check if form has been submitted
        if (isset($_POST['add'])) {

            // grab the needed information
            $name = $_POST['name'];
            $level = $_POST['level'];
            $permissionsString = $_POST['permissions'];

            // split the text every ','
            $permissions = explode(",", $permissionsString);

            // pass the values to the actual functions for validation and further process
            $admin->addNewLevel($name, $level, $permissions);

        }

        // load the required scripts
        global $customScripts;
        $customScripts = '<script src="' . $settings->getTemplatesURL() . 'plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js"></script>' . "\n";

        // load the view
        $viewController->loadView("ad_addGroup.html");
    }
}

new Admin_addGroup();