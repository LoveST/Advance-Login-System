<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 10/11/2017
 * Time: 10:58 PM
 */

namespace ALS\User;


use ALS\Message;

class Authenticators
{
    private $data;
    private $auths = array();

    public function __construct($data)
    {
        $this->data = $data;
        $this->init();
    }

    /**
     * get the current user id
     * @return int
     */
    private function getID()
    {
        return $this->data[TBL_USERS_ID];
    }

    /**
     * Initialize the authenticated websites
     */
    private function init()
    {
        // globals
        global $message, $database;

        // set the sql request
        $sql = "SELECT * FROM " . TBL_USERS_AUTHS . " WHERE " . TBL_USERS_AUTHS_ID . "= '" . $this->getID() . "'";

        // check for any errors
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            $message->setError("Something went wrong while getting the required data from the database", Message::Error);
            return false;
        }

        // check if results contain any data
        if ($database->getQueryNumRows($results, true) > 1) {
            $this->auths = $database->getQueryEffectedRows($results, true);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the current user authenticators
     * @return array
     */
    public function getAuthenticators()
    {
        return $this->auths;
    }

    /**
     * Check if the required permission has been authenticated
     * @param string $authType
     * @return bool
     */
    final function isAuthenticated($authType)
    {
        foreach ($this->getAuthenticators() as $auth) {
            if ($auth[TBL_USERS_AUTHS_AUTH_TYPE] == $authType) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // authenticators = { array{ site, array(permissions), date_allowed, daysToExpire }}

    /**
     * @param string $site
     * @param string $auth_type
     * @return bool
     */
    final public function addAuthType($site, $auth_type)
    {
        // init the required globals
        global $message, $database;

        // check if any of the important fields are empty
        if (empty($site) || empty($auth_type)) {
            $message->setError("Missing required authentication fields", Message::Error);
            return false;
        }

        // check if already authenticated
        if ($this->isAuthenticated($auth_type)) {
            $message->setError("The website has already been authenticated before", Message::Error);
            return false;
        }

        // if no errors then add the required type
        $id = $this->getID();
        $sql = "INSERT
                INTO " . TBL_USERS_AUTHS . " (" . TBL_USERS_AUTHS_ID . "," . TBL_USERS_AUTHS_SITE . "," . TBL_USERS_AUTHS_AUTH_TYPE . ")" . "
                VALUES ('$id', '$site', '$auth_type')";

        // add to database
        $database->getQueryResults($sql);

        // check for any errors
        if ($database->anyError()) {
            $message->setError("Something went wrong while updating the database", Message::Error);
            return false;
        }

        // return a success message
        $message->setSuccess("Site has been authenticated successfully");
        return true;
    }

}