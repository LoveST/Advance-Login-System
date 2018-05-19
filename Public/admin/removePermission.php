<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/13/2018
 * Time: 7:02 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class Admin_removePermission
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $settings, $groups;

        // set the required variables
        global $listGroups;
        $listGroups = $groups->listGroups();

        // check if form has been submitted
        if (isset($_POST['remove'])) {

            // grab the needed information
            $groupName = $_POST['groupName'];
            $permissionsString = $_POST['permissions'];

            // split the text every ','
            $permissions = explode(",", $permissionsString);

            // pass the values to the actual functions for validation and further process
            $groups->removePermission($permissions, $groupName);

        }

        // load the required scripts
        global $customScripts;
        $customScripts = '<script src="' . $settings->getTemplatesURL() . 'plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js"></script>' . "\n";

        // load the view
        $viewController->loadView("ad_removePermission.html");
    }
}

new Admin_removePermission();