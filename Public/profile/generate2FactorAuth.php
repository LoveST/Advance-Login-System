<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/8/2018
 * Time: 8:42 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// insert custom scripts
$viewController->addCustomScript(' <script src="' . $settings->getTemplatesURL() . 'plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>');
$viewController->addCustomScript('<script src="' . $settings->getTemplatesURL() . 'plugins/autoNumeric/autoNumeric.js" type="text/javascript"></script>');

// load the view
$viewController->loadView("profile_generate_2factorCode.html");