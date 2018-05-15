<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/14/2018
 * Time: 3:01 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class db_ViewTables
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $database, $settings;

        // get the database table names
        global $tableNames;
        $tableNames = $database->getTableNames();

        // load the required scripts
        $customScripts = '<script src="' . $settings->getTemplatesURL() . 'assets/js/popper.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/jquery.dataTables.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.bootstrap4.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.responsive.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/responsive.bootstrap4.min.js"></script>' . "\n";

        // load the view
        $viewController->loadView("ad_db_viewTables.html");
        var_dump($database->countTableFields("users"));
    }
}

new db_ViewTables();