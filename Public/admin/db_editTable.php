<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/17/2018
 * Time: 7:27 AM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class db_editTable
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $database, $settings, $message, $tableName, $tableColumns;

        // get the database table names
        $name = array_key_exists('dbName', $_GET) ? $_GET['dbName'] : null;
        $tableName = $database->secureInput($name);

        // check if database name is empty
        if ($tableName == null || empty($tableName)) {
            $message->setError("Missing table name to edit", \ALS\Message::Error);
        } else {

            // load the required table columns
            $tableColumns = $database->getTableColumns($tableName);
        }

        // load the required scripts
        global $customScripts;
        $customScripts = '<script src="' . $settings->getTemplatesURL() . 'assets/js/popper.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/jquery.dataTables.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.bootstrap4.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/dataTables.responsive.min.js"></script>' . "\n";
        $customScripts .= '<script src="' . $settings->getTemplatesURL() . 'plugins/datatables/responsive.bootstrap4.min.js"></script>' . "\n";

        // load the view
        $viewController->loadView("ad_db_editTables.html");
    }
}

new db_editTable();