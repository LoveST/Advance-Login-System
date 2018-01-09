<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/3/2018
 * Time: 4:45 PM
 */

namespace ALS\USER_API;


use ALS\API\API_DEFAULT;
use ALS\User;

class USER_API extends API_DEFAULT
{

    private $logged_in = false;
    private $apiUser = null;

    public function __construct($token = "")
    {

        // construct the parent class
        parent::__construct();

        // try to check if session is already in progress
        $this->tryCreateSession($token);

    }

    private function tryCreateSession($token)
    {

        global $database, $functions, $settings, $browser;

        // clear any tags from the token
        $token = $database->secureInput($token);

        // check for empty token
        if (empty($token) || $token == "") {
            return false;
        }

        // check if token exists in database
        $sql = "SELECT * FROM " . TBL_USERS_API_CALLS . " WHERE " . TBL_USERS_API_CALLS_USER_TOKEN . " = '" . $token . "' LIMIT 1";
        $results = $database->getQueryResults($sql);

        // check if DB contains the token
        if ($database->getQueryNumRows($results, true) <= 0) {
            parent::printError("API token does not exist");
            return false;
        }

        // grab the result set
        $user = $database->getQueryEffectedRow($results, true);

        // get the user token, expiration date, user agent, browser name & ip
        $userID = $user[TBL_USERS_API_CALLS_USER_ID];
        $expirationDate = $functions->decryptIt($user[TBL_USERS_API_CALLS_EXPIRATION_DATE]);
        $userAgent = $functions->decryptIt($user[TBL_USERS_API_CALLS_USER_AGENT]);
        $browserName = $functions->decryptIt($user[TBL_USERS_API_CALLS_BROWSER_NAME]);
        $ip = $functions->decryptIt($user[TBL_USERS_API_CALLS_USER_IP]);

        // Todo add the ability to modify the option to enable or disable multiple IP requests
        if ($settings->sameIpLogin()) {
            // check if different ip is present
            if ($functions->getUserIP() != $ip) {
                return false;
            }
        }

        // check if the user agent are the same
        if ($userAgent != $browser->getUserAgent()) {
            return false;
        }

        // check if browser name are the same
        if ($browserName != $browser->getBrowser()) {
            return false;
        }

        // check if token is expired, then delete the token it self
        if (strtotime($expirationDate) < time()) {
            $this->deleteToken($token, $userID);
            parent::printError("Token expired");
            return false;
        }

        // if no errors then set the logged in status to true
        $this->logged_in = true;

        // create an instance from the User class as API
        $this->apiUser = new User();
        $this->apiUser->initUserRestAPI($userID);

        return true;
    }

    public function login($username, $password, $appID, $appKey)
    {

        global $database, $settings, $browser, $functions;

        // get the user id and check if user exist
        if (!$id = $functions->getUserID($username)) {
            parent::printError("Wrong username or password used");
        }

        // check if passwords match
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = '" . $id . "'";
        $results = $database->getQueryResults($sql);
        $row = $database->getQueryEffectedRow($results, true);

        // check the passwords
        if (!password_verify($password, $row[TBL_USERS_PASSWORD])) {
            parent::printError("Wrong username or password used");
        }

        // get the user browser information & encrypt them
        $platform = $functions->encryptIt($browser->getPlatform());
        $expirationDate = $functions->encryptIt("");
        $userAgent = $functions->encryptIt($browser->getUserAgent());
        $browserName = $functions->encryptIt($browser->getBrowser());
        $browserAOL = $functions->encryptIt($browser->getAolVersion());
        $ip = $functions->encryptIt($functions->getUserIP());

        // generate the user UUID
        $uuid = uniqid(md5($username . $id . $browser->getUserAgent() . $browser->getPlatform()), true);

        // insert the data to Database


        parent::setExecutable(array("token" => $uuid));
    }

    /**
     * Delete a certain token from the users api database
     * @param string $token
     * @param int $id
     */
    private function deleteToken($token, $id)
    {

        global $database;

        // prepare the sql request
        $sql = "DELETE FROM " . TBL_USERS_API_CALLS . " WHERE " . TBL_USERS_API_CALLS_USER_ID . " = '" . $id . "' AND " . TBL_USERS_API_CALLS_USER_TOKEN . " = '" . $token . "'";

        // execute the sql request
        $database->getQueryResults($sql);
    }

    /**
     * Check if the current session is an active one
     * @return bool
     */
    public function logged_in()
    {
        return $this->logged_in;
    }

}