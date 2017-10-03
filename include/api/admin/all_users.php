<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 10/2/2017
 * Time: 2:21 AM
 */

namespace ALS\API;

class all_users1 extends API_DEFAULT
{

    function __construct($params = null)
    {
        // construct the parent class
        parent::__construct();

        // prepare the results
        $this->getUsers($params);

        // execute the api call
        parent::executeAPI();

    }

    function getUsers($params = null)
    {

        // globals
        global $database, $session, $user;

        // check if the user has permission
        if (!$user->hasPermission("als_api_admin_getUsers")) {
            parent::printError("You don't the permission to do this");
        }

        // prepare the sql result set
        if ($params != null && !empty($params['limit'])) {
            $sql = "SELECT " . TBL_USERS_USERNAME . " FROM " . TBL_USERS;
        } else {
            $sql = "SELECT " . TBL_USERS_USERNAME . " FROM " . TBL_USERS;
        }

        // get the sql results
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            parent::printError("Error while loading the required data from database");
        }

        // check if result set is less than 1
        if ($database->getQueryNumRows($results, true) < 1) {
            parent::printError("No users found");
        }

        // set the executable
        $users = array();

        foreach ($database->getQueryEffectedRows($results, true) as $user) {
            $users[] = $user["username"];
        }

        parent::setExecutable($users);

    }

}