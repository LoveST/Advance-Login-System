<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/13/2018
 * Time: 5:23 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class Admin_addPermission
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $settings, $groups, $listGroups;

        // get every single group
        $listGroups = $groups->listGroups();

        // check if form has been submitted
        if (isset($_POST['add'])) {

            // grab the needed information
            $groupName = $_POST['groupName'];
            $permissionsString = $_POST['permissions'];

            // split the text every ','
            $permissions = explode(",", $permissionsString);

            // pass the values to the actual functions for validation and further process
            $groups->addPermission($permissions, $groupName);

        }

        // load the required scripts
        global $customScripts;
        $customScripts = '<script src="' . $settings->getTemplatesURL() . 'plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js"></script>' . "\n";

        // load the view
        $viewController->loadView("ad_addPermission.html");
    }
}

new Admin_addPermission();