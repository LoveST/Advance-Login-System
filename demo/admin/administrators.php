<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:54 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// set the required variables
$administrator = $admin->getAdmins();

// load the required scripts
$customScripts = '<script src="' . $settings->getTemplatesURL() . 'assets/js/popper.min.js"></script>' . "\n";
$customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/jquery.dataTables.min.js"></script>' . "\n";
$customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.bootstrap4.min.js"></script>' . "\n";
$customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.responsive.min.js"></script>' . "\n";
$customScripts .= '<script src="' . $settings->getTemplatesURL() .'plugins/datatables/responsive.bootstrap4.min.js"></script>' . "\n";

// load the view
$viewController->loadView("ad_administrators.html");

