<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/10/2018
 * Time: 3:35 PM
 */

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.
use ALS\ALS_Classes;

class Query_getUserInfo extends \ALS\Core
{

    public function __construct()
    {

        // construct the parent
        parent::__construct();

        // load the database class
        $classes = array(
            ALS_Classes::Database,
            ALS_Classes::Settings,
            ALS_Classes::Message
        );
        parent::loadClasses($classes);

        // init the required global variables
        global $settings;
        echo json_encode($settings->getTemplatesCachePath());
        exit();
    }

}

new Query_getUserInfo();