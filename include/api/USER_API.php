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
            parent::printError(9991, "API token does not exist");
            return false;
        }

        // grab the result set
        $user = $database->getQueryEffectedRow($results, true);

        // get the user token, expiration date, user agent, browser name & ip
        $userID = $user[TBL_USERS_API_CALLS_USER_ID];
        $pinVerified = trim($user[TBL_USERS_API_CALLS_PIN_VERIFIED]);
        $expirationDate = trim($user[TBL_USERS_API_CALLS_EXPIRATION_DATE]);
        $userAgent = trim($user[TBL_USERS_API_CALLS_USER_AGENT]);
        $browserName = trim($user[TBL_USERS_API_CALLS_BROWSER_NAME]);
        $platform = trim($user[TBL_USERS_API_CALLS_PLATFORM]);
        $ip = trim($functions->decryptIt($user[TBL_USERS_API_CALLS_USER_IP]));

        // Todo add the ability to modify the option to enable or disable multiple IP requests
        if ($settings->sameIpLogin() && !$functions->is_localhost($ip)) {
            // check if different ip is present
            if (strcmp($functions->getUserIP(), $ip) != 0) {
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

        // check if different platform presents
        if ($browser->getPlatform() != $platform) {
            return false;
        }

        // check if token is expired, then delete the token it self
        if ($expirationDate < time()) {
            $this->deleteToken($token, $userID);
            parent::printError(9992, "Token expired");
            return false;
        }

        // check if pin has been verified
        if ($pinVerified == 0) {
            parent::printError(9993, "Pin Verification Is Required");
            return false;
        }

        // if no errors then set the logged in status to true
        $this->logged_in = true;

        // create an instance from the User class as API
        $this->apiUser = new User();
        $this->apiUser->initUserRestAPI($userID);

        return true;
    }

    /**
     * Log the user in
     * @param $username
     * @param $password
     * @param $appID
     * @param $appKey
     * @return bool
     */
    public function login($username, $password, $appID, $appKey)
    {

        global $database, $settings, $browser, $functions;

        // get the user id and check if user exist
        if (!$id = $functions->getUserID($username)) {
            parent::printError(0003, "Wrong username or password used");
        }

        // check if passwords match
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = '" . $id . "'";
        $results = $database->getQueryResults($sql);
        $row = $database->getQueryEffectedRow($results, true);

        // check the passwords
        if (!password_verify($password, $row[TBL_USERS_PASSWORD])) {
            parent::printError(0003, "Wrong username or password used");
        }

        // get the user browser information & encrypt them
        $platform = $browser->getPlatform();
        $expirationDate = strtotime('+7 day', time());
        $userAgent = $browser->getUserAgent();
        $browserName = $browser->getBrowser();
        $browserAOL = $browser->getAolVersion();
        $ip = $functions->encryptIt($functions->getUserIP());

        // generate the user UUID
        $uuid = uniqid(md5($username . $id . $browser->getUserAgent() . $browser->getPlatform()), true);

        // check if user has already logged in throw the same Application
        $sql = "SELECT * FROM " . TBL_USERS_API_CALLS . " WHERE " . TBL_USERS_API_CALLS_APP_ID . " = '" . $appID . "' AND " . TBL_USERS_API_CALLS_APP_KEY . " = '" . $appKey . "'";
        $results = $database->getQueryResults($sql);
        $row = $database->getQueryEffectedRow($results, true);

        //  if any results were found
        if ($database->getQueryNumRows($results, true) > 0) {
            // print the user new token
            parent::setExecutable(array("token" => $row[TBL_USERS_API_CALLS_USER_TOKEN]));
            return true;
        }

        // insert the data to Database
        $sql = "INSERT INTO " . TBL_USERS_API_CALLS . " (" . TBL_USERS_API_CALLS_USER_ID . ", " . TBL_USERS_API_CALLS_USER_TOKEN . ", " . TBL_USERS_API_CALLS_EXPIRATION_DATE . ", " . TBL_USERS_API_CALLS_APP_ID . ", " . TBL_USERS_API_CALLS_APP_KEY . ", " . TBL_USERS_API_CALLS_PIN_VERIFIED . ", " . TBL_USERS_API_CALLS_BROWSER_NAME . ", " . TBL_USERS_API_CALLS_BROWSER_AOL . ", " . TBL_USERS_API_CALLS_USER_AGENT . ", " . TBL_USERS_API_CALLS_USER_IP . ", " . TBL_USERS_API_CALLS_PLATFORM . ") 
                VALUES ('$id', '$uuid', '$expirationDate', '$appID', '$appKey', '0', '$browserName', '$browserAOL', '$userAgent', '$ip', '$platform')";

        // execute the sql request
        $database->getQueryResults($sql);

        // print the user new token
        parent::setExecutable(array("token" => $uuid));
    }

    public function checkStatus($token, $appID, $appKEY)
    {

        global $database, $functions;

        // check if application ID or Key are missing
        if (empty($appID) || empty($appKEY) || $appID == null || $appKEY == null) {
            parent::printError(9960, "Missing Application ID or Key");
            return false;
        }

        // check if token is missing
        if (empty($token) || $token == null) {
            parent::printError(9961, "Missing Login Token");
            return false;
        }

        // if no errors then return a success message
        parent::setExecutable(array("status" => "1"));
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