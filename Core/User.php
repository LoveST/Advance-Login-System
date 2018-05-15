<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 8:26 PM
 */

namespace ALS;

use ALS\User\Authenticators;
use ALS\User\Devices;

require "user/User_Default.php";
require "user/Authenticators.php";

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class User extends User_Default
{

    private $authenticators;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * init an instance of this class to be used in the main API methods that came with the script
     * @param string $key
     * @param string $token
     * @return bool|User
     */
    function initAPIInstance($key, $token)
    {
        // define the required globals
        global $database, $message, $groups, $translator;

        // check for empty strings
        if (empty($key) || empty($token)) {
            $message->setError($translator->translateText("initAPI_param_needed"), Message::Error);
            return false;
        }

        // check if a user exists with the current credentials
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_API_KEY . " = '" . $key . "' AND " . TBL_USERS_API_TOKEN . " = '" . $token . "'";

        // query the results
        $results = $database->getQueryResults($sql);

        // check for any errors
        if ($database->anyError()) {
            $message->setError("Database Connection Error =>" . $database->getError(), Message::Error);
            return false;
        }

        // check if any matched data
        if ($database->getQueryNumRows($results, true) < 1) {
            $message->setError("Wrong authentication key or token used", Message::Error);
            return false;
        }

        // get the results
        $data = $database->getQueryEffectedRow($results, true);

        // set the userData
        $this->setUserData($data);

        // set the user group
        $this->setGroup($groups->loadGroup($this->getGroupID()));

        return $this;
    }

    function initUserRestAPI($id)
    {

        global $database, $groups;

        // check if id is empty
        if (empty($id) || $id == "") {
            return false;
        }

        // setup the sql request
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = '" . $id . "' LIMIT 1";

        // get the results
        $data = $database->getQueryResults($sql);

        // check if user exists
        if ($database->getQueryNumRows($data, true) <= 0) {
            return false;
        }

        // get the required data
        $data = $database->getQueryEffectedRow($data, true);

        // set the userData
        $this->setUserData($data);

        // set the user group
        $this->setGroup($groups->loadGroup($this->getGroupID()));

        return $this;
    }

    /**
     * init an instance of this class and supply it with the user data ($data) and the other classes Or basically use a username for the variable ($data)
     * @param $data
     * @return bool
     */
    function initInstance($data)
    {

        // define all the global variables
        global $message, $translator, $groups;

        // check if $data is an array or only a username
        if (is_array($data)) {

            // load the data
            $this->setUserData($data);

            // load the group
            $this->setGroup($groups->loadGroup($this->getGroupID()));

        } else { // if username is supplied, try to load the user
            if (!$this->loadInstance($data)) {
                $message->setError($translator->translateText("no_user_exists"), Message::Error);
                return false;
            }
        }

        $this->setDevices(new Devices()); // create the unique logs class for the current user
        $this->authenticators = new Authenticators($this->getUserData());
        return true;
    }

    /**
     * load the instance using a username
     * @param $username
     * @return bool
     */
    private function loadInstance($username)
    {

        // define all the global variables
        global $database, $groups;

        // pull the requested user $username information from the database
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";

        // get the sql results
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        // check for empty results
        if ($database->getQueryNumRows($results, true) < 1) {
            return false;
        }
        // call the results
        $row = $database->getQueryEffectedRow($results, true);

        // set the userData
        $this->setUserData($row);

        // set the user group
        $this->setGroup($groups->loadGroup($this->getGroupID()));

        return true;
    }

    /**
     * Re initiate the user data if cookies were to be found after the class init
     */
    function initUserData()
    {

        // define global variables
        global $groups;

        // pull put the needed information for the session if available.
        $this->setUserData($_SESSION["user_data"]);

        // check if user has to log in again
        // check if user has to sign in again with his credentials
        if ($this->getUserData()[TBL_USERS_SIGNIN_AGAIN]) {
            $_SESSION['user_data'] = "";
            $_SESSION['user_id'] = "";
            session_destroy();
            return false;
        }

        // set the uer current devices class
        $this->devices()->init($this->getUserData());

        // set the users current group class
        $this->setGroup($groups->loadGroup($this->getGroupID()));

        // update the user's logged in IP address
        $this->updateLastLoginIP();

        // send a heart beat to the database
        $this->sendHeartBeat();

        $this->authenticators = new Authenticators($this->getUserData());

        return true;
    }

    /**
     * @return Authenticators
     */
    function getAuthenticators()
    {
        return $this->authenticators;
    }

    /**
     * update the users latest login ip address for security purposes
     */
    private function updateLastLoginIP()
    {
        // define the required global variables
        global $mailTemplates;

        // check if device has already been active, then skip
        if ($this->devices()->canAccess()) {
            return false;
        }

        // check if session is already in progress the return false
        if (isset($_SESSION['new_device_check'])) {
            if ($_SESSION['new_device_check'] == 1) {
                return false;
            }
        }

        // check if sessions is timed-out
        if (isset($_SESSION['new_device_check_timeout'])) {
            if ($_SESSION['new_device_check_timeout'] + 4 * 60 * 60 >= time()) { // 4 hours for the session to expire
                return false;
            }
        }

        // if they don't match then update the database with the current results
        if (!$this->updateUserRecord(TBL_USERS_LASTLOGIN_IP, md5($this->devices()->getUserIP()))) {
            return false;
        }

        // create a new session to hold the needed variable
        $_SESSION['new_device_check'] = 1;
        $_SESSION['new_device_check_timeout'] = time();

        // send a message to the current user's email address to check status the login session
        $this->setNewLogin(true);
        $mailTemplates->newSignIn();

        // if everything goes right just return true
        return true;
    }

    final

        /**
         * update & set the users current heartbeat
         * fire this function every time the class User initiates ( for statics )
         * @return bool
         */
    function sendHeartBeat()
    {

        // get the current time
        $currentTime = date("Y-m-d H:i:s", time());

        // update the records
        if (!$this->updateUserRecord(TBL_USERS_LAST_LOGIN, $currentTime)) {
            return false;
        }

        // return true if nothing stops the heartbeat
        return true;
    }

    /**
     * Log the user out from the current session
     * @return bool
     */
    public function logOut()
    {

        // check if the user is already not logged in and return true
        if (empty($_SESSION["user_data"]) && empty($_COOKIE["user_data"]) && empty($_COOKIE["user_id"])) {
            return true;
        } else {

            // ** Clear the Cookie auth code ** //
            if (!$this->updateUserRecord(TBL_USERS_TOKEN, '')) {
                return false;
            }

            // ** Unset the session & cookies ** //
            unset($_SESSION["user_data"]);
            unset($_COOKIE["user_data"]);
            unset($_COOKIE["user_id"]);
            unset($_SESSION['authenticated']);
            setcookie("user_data", null, -1, '/');
            setcookie("user_id", null, -1, '/');
            return true;
        }
    }

}

$user = new User();