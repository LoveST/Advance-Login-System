<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/14/2018
 * Time: 9:11 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class ad_rawDatabaseSettings
{

    public function __construct()
    {
        // init the required globals
        global $viewController, $database, $dbSettings;

        // set the required variables
        $sql = "SELECT * FROM " . TBL_SETTINGS;
        $dbSettings = $database->getQueryEffectedRows($sql);

        // load the view
        $viewController->loadView("ad_rawDatabaseSettings.html");
    }

}

new ad_rawDatabaseSettings();