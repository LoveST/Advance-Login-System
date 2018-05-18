<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/14/2018
 * Time: 6:10 PM
 */

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.
use ALS\ALS_Classes;

class fb_getUsernames extends \ALS\Core
{

    public function __construct()
    {

        // construct the parent
        parent::__construct();

        // load the database class
        $classes = array(
            ALS_Classes::FireBase
        );
        parent::loadClasses($classes);

        global $firebase;
        echo $firebase->get()->get("username");
    }

}

new fb_getUsernames();