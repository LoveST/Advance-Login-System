<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/17/2018
 * Time: 5:18 PM
 */

class ad_manageGroups
{

    public function __construct()
    {

        // init the required globals
        global $groups, $viewController, $settings, $listGroups;

        // load the groups
        $listGroups = $groups->listGroups();
        $listGroups[0];
        // load the required scripts
        global $customScripts;
        $customScripts = '<script src="' . $settings->getTemplatesURL() . 'assets/js/popper.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/jquery.dataTables.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.bootstrap4.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.responsive.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/responsive.bootstrap4.min.js"></script>' . "\n";

        // load the view
        $viewController->loadView("ad_manageGroups.html");
    }

}

new ad_manageGroups();