<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 8:26 PM
 */

namespace ALS;

use ALS\User\Devices;
use ALS\User\Group;

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class User
{

    private $userData; // declare the required variables for the user data.
    private $levelData; // declare the required variables for the level data.
    private $group;
    private $devices; // instance of the devices class of the current user
    private $newLogin = false;
    const First_Name = TBL_USERS_FNAME;
    const Last_Name = TBL_USERS_LNAME;
    const UserName = TBL_USERS_USERNAME;
    const ID = TBL_USERS_ID;
    const Date_Joined = TBL_USERS_DATE_JOINED;
    const Email = TBL_USERS_EMAIL;
    const Level = TBL_USERS_LEVEL;
    const Banned = TBL_USERS_BANNED;
    const PIN = TBL_USERS_PIN;

    function __construct()
    {
        $this->devices = new Devices();
    }

    /**
     * init an instance of this class and supply it with the user data ($data) and the other classes Or basically use a username for the variable ($data)
     * @param $data
     * @return bool
     */
    function initInstance($data)
    {

        // define all the global variables
        global $message, $translator;

        // check if $data is an array or only a username
        if (is_array($data)) {
            $this->userData = $data;
        } else { // if username is supplied, try to load the user
            if (!$this->loadInstance($data)) {
                $message->setError($translator->translateText("no_user_exists"), Message::Error);
                return false;
            }
        }

        $this->levelData = $this->loadLevel($this->getLevel()); // load all the current level information and store it in the database
        $this->devices = new Devices(); // create the unique logs class for the current user

        return true;
    }

    function devices()
    {
        return $this->devices;
    }

    /**
     * load the instance using a username
     * @param $username
     * @return bool
     */
    private function loadInstance($username)
    {

        // define all the global variables
        global $database;

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
        $this->userData = $row;
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
        $this->userData = $_SESSION["user_data"];

        // check if user has to log in again
        // check if user has to sign in again with his credentials
        if ($this->userData[TBL_USERS_SIGNIN_AGAIN]) {
            $_SESSION['user_data'] = "";
            $_SESSION['user_id'] = "";
            session_destroy();
            return false;
        }

        $this->levelData = $this->loadLevel($this->getLevel()); // load all the current level information and store it in the database
        $this->devices->init($this->userData);
        $this->group = $groups->loadGroup($this->getLevel());
        $this->updateLastLoginIP(); // update the user's logged in IP address
        $this->sendHeartBeat();

        return true;
    }

    /**
     * update the users latest login ip address for security purposes
     */
    private function updateLastLoginIP()
    {

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

        // send a message to the current user's email address to verify the login session
        $this->newLogin = true;
        $mailTemplates = new MailTemplates();
        $mailTemplates->newSignIn();

        // if everything goes right just return true
        return true;
    }

    /**
     * Check if the current user is an admin
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->hasPermission("*")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the required user data as needed by using User::'Data Type'
     * @param $dataType
     * @return mixed
     */
    function get($dataType)
    {
        return $this->userData[$dataType];
    }

    /**
     * get the user current group
     * @return Group
     */
    function getGroup()
    {
        if ($this->group !== null) {
            return $this->group;
        }
    }

    /**
     * ban the current user
     * @return bool
     */
    function ban()
    {

        // define all the global variables
        global $database, $message, $translator;

        // check if banned
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "' AND " . TBL_USERS_BANNED . " = '0'";

        // get the sql results
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        // check for empty results (user is already banned before)
        if ($database->getQueryNumRows($results, true) < 1) {
            $message->setError($translator->translateText("already_banned"), Message::Error);
            return false;
        }

        // ban the user
        if (!$this->updateUserRecord(TBL_USERS_BANNED, '1')) {
            return false;
        }

        // make sure to sign the user off
        $this->forceSignInAgain();

        return true;
    }

    /**
     * un-ban the current user
     * @return bool
     */
    function unBan()
    {

        // define all the global variables
        global $database, $message, $translator;

        // check if banned
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "' AND " . TBL_USERS_BANNED . " = '1'";

        // get the sql results
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        // check for empty results (user is already banned before)
        if ($database->getQueryNumRows($results, true) < 1) {
            $message->setError($translator->translateText("never_banned"), Message::Error);
            return false;
        }

        // unBan the user
        if (!$this->updateUserRecord(TBL_USERS_BANNED, '0')) {
            return false;
        }

        return true;
    }

    /**
     * get the current user's username
     * @return string
     */
    function getUsername()
    {
        return $this->userData[TBL_USERS_USERNAME];
    }

    /**
     * get the current user's first name
     * @return string
     */
    function getFirstName()
    {
        return $this->userData[TBL_USERS_FNAME];
    }

    /**
     * get the current user's last name
     * @return string
     */
    function getLastName()
    {
        return $this->userData[TBL_USERS_LNAME];
    }

    /**
     * get the current user's id
     * @return int
     */
    function getID()
    {
        return $this->userData[TBL_USERS_ID];
    }

    /**
     * get the current user's email address
     * @return string
     */
    function getEmail()
    {
        return $this->userData[TBL_USERS_EMAIL];
    }

    /**
     * get the date that the user has joined at
     * @return \DateTime|string
     */
    function getDateJoined()
    {
        return $this->userData[TBL_USERS_DATE_JOINED];
    }

    /**
     * get the users last login time
     * @return \DateTime
     */
    function getLastLoginTime()
    {
        return $this->userData[TBL_USERS_LAST_LOGIN];
    }

    /**
     * get the users last login time in text format
     * @return string
     */
    function getLastLoginText()
    {
        global $functions;
        return $functions->calculateTime($this->getLastLoginTime());
    }

    /**
     * get the user birth date
     * @return string
     */
    function getBirthDate()
    {
        return $this->userData[TBL_USERS_BIRTH_DATE];
    }

    /**
     * get the user's age
     * @return int
     */
    function getAge()
    {
        global $functions;
        return $functions->getAge($this->getBirthDate());
    }

    /**
     * get the user level id
     * @return int
     */
    function getLevel()
    {
        return $this->userData[TBL_USERS_LEVEL];
    }

    /**
     * get the users preferred language
     * @return string
     */
    function getPreferredLanguage()
    {
        return $this->userData[TBL_USERS_PREFERRED_LANGUAGE];
    }

    /**
     * Check if user has to sign in again
     * @return boolean
     */
    function mustSignInAgain()
    {
        return $this->userData[TBL_USERS_SIGNIN_AGAIN];
    }

    /**
     * Check if two factor authentication is enabled for the current user
     * @return boolean
     */
    function twoFactorEnabled()
    {
        return $this->userData[TBL_USERS_TWOFACTOR_ENABLED];
    }

    /**
     * get the verification code for the current user that has been set before
     * @return string
     */
    function getVerificationCode()
    {
        return $this->userData[TBL_USERS_VERIFICATION_CODE];
    }

    /**
     * get the user's last login ip address
     * @return string
     */
    function getLastLoginIP()
    {
        return $this->userData[TBL_USERS_LASTLOGIN_IP];
    }

    /**
     * get the user secret
     * @return string
     */
    function getSecret()
    {
        // TODO
        return $this->userData[TBL_USERS_SECRET];
    }

    /**
     * get the user current level name
     * @return string
     */
    function getLevelName()
    {
        if ($this->getGroup() != null) {
            return $this->getGroup()->getName();
        }
    }

    /**
     * get all the current user permissions
     * @return array
     */
    function getPermissions()
    {
        if ($this->getGroup() !== null) {
            return $this->getGroup()->getPermissions();
        }
    }

    /**
     * Set this function to force the current user to log in again and re-initiate the data
     * @return bool
     */
    function forceSignInAgain()
    {

        // call the database to store the new session data
        if (!$this->updateUserRecord(TBL_USERS_SIGNIN_AGAIN, '1')) {
            return false;
        }

        return true;
    }

    /**
     * check if the user's account is banned
     * @return bool
     */
    function is_banned()
    {
        if ($this->userData[TBL_USERS_BANNED] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if the given pin number (md5) matches the current one stored in the session
     * @param String $pin
     * @return bool
     */
    function is_samePinNumber($pin)
    {
        if ($this->get(User::PIN) == $pin) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if the given password matches the current user's one
     * @param string $password
     * @return bool
     */
    function is_samePassword($password)
    {
        if (password_verify($password, $this->get(TBL_USERS_PASSWORD))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if the current user account is activated
     * @return bool
     */
    function is_accountActivated()
    {

        // check if the table value of TBL_USERS_ACTIVATED is equal to 1 or 0
        if ($this->get(TBL_USERS_ACTIVATED) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if the current user is logging in from a new device
     * @return bool
     */
    public function is_loggedFromNewDevice()
    {
        return $this->newLogin;
    }

    /**
     * Get a certain level information
     * @param $level
     * @return array|bool
     */
    function loadLevel($level)
    {

        // define all the global variables
        global $database;

        $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_LEVEL . " = '" . $level . "'";

        // get the sql results
        $result = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        $row = $database->getQueryEffectedRow($result, true);
        return $row;
    }

    /**
     * get the permissions of a certain level as an array
     * @param $level
     * @return array|bool
     */
    function loadLevelPermissions($level)
    {

        // define all the global variables
        global $database;

        // load the current user permissions
        $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_LEVEL . " = '" . $level . "'";

        // get the sql results
        $result = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        $row = $database->getQueryEffectedRow($result, true);

        // separate every single permission after a | sign and store it in an array and return it
        $permissions = explode("|", $row[TBL_LEVELS_PERMISSIONS]);
        return $permissions;
    }

    /**
     * check if the current user has a certain permission
     * @param $permission
     * @return bool
     */
    function hasPermission($permission)
    {
        // check if class has been initiated
        if ($this->getGroup() == null) {
            return false;
        }

        // load the permissions for the current user
        $permissions = $this->getGroup()->getPermissions();

        // check if empty array
        if (empty($permissions)) {
            return false;
        }

        // check if first offset is *
        if ($permissions[0] == "*") {
            return true;
        }

        // loop throw the permissions and check if any * is to be found that matches the current requested permission
        foreach ($permissions As $perm) {

            // only loop if * is found
            if (strpos($perm, '*')) {

                // split the permission every '_'
                $permArgs = explode("_", $perm);
                $permissionArgs = explode("_", $permission);

                // check if first args matches the permission args
                if ($permArgs[0] == $permissionArgs[0]) {

                    // loop throw the rest of permArgs
                    for ($i = 1; $i < count($permArgs); $i++) {

                        if ($permArgs[$i] == $permissionArgs[$i]) {
                            continue;
                        } else {
                            if ($permArgs[$i] = "*") {
                                return true;
                            }
                        }

                    }

                }
            }
        }

        if (in_array($permission, $permissions)) {
            return true;
        } else {
            return false;
        }
    }

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
     * activate the users account
     * @return bool
     */
    function activateAccount()
    {

        // define all the global variables
        global $message, $translator;

        // check if account is already activated then just return true
        if ($this->is_accountActivated()) {
            $message->setError($translator->translateText("already_activated"), Message::Error);
            return false;
        }

        // if account is not activated then update the sql records
        if (!$this->updateUserRecord(TBL_USERS_ACTIVATED, '1')) {
            return false;
        }

        // if everything goes right then return true
        $message->setSuccess($translator->translateText("successful_activation", array("accountName" => $this->getUsername())));
        return true;
    }

    /**
     * disable the users account
     * @return bool
     */
    function disableAccount()
    {

        // define all the global variables
        global $message, $translator;

        // check if account is not activated then just return true
        if (!$this->is_accountActivated()) {
            $message->setError($translator->translateText("never_activated"), Message::Error);
            return false;
        }

        // if account is activated then update the sql records
        if (!$this->updateUserRecord(TBL_USERS_ACTIVATED, '0')) {
            return false;
        }

        // if everything goes right then return true
        $message->setSuccess($translator->translateText("successful_deactivation", array("accountName" => $this->getUsername())));
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

    /**
     * get the users current xp amount
     * @return int
     */
    public function getXP()
    {

        // check if empty xp field
        if (empty($this->userData[TBL_USERS_XP])) {
            return 0;
        }

        return $this->userData[TBL_USERS_XP];
    }

    /**
     * Add x amount of xp to the user
     * @param $amount
     * @return bool
     */
    public function addXP($amount)
    {

        // check if its a number
        if (!is_numeric($amount)) {
            return false;
        }

        // check if double xp then double the amount
        if ($this->hasDoubleXP()) {
            $amount = $amount * 2;
        }

        // add the old user xp to the new one
        $newXP = $this->getXP() + $amount;

        if (!$this->updateUserRecord(TBL_USERS_XP, $newXP)) {
            return false;
        }

        // update the current xp level that the user has
        $this->userData[TBL_USERS_XP] = $newXP;

        return true;
    }

    /**
     * Subtract x amount of xp from the user
     * @param $amount
     * @return bool
     */
    public function subtractXP($amount)
    {

        // define all the global variables
        global $database;

        if (!is_numeric($amount)) {
            return false;
        }

        // add the old user xp to the new one
        $newXP = $this->getXP() - $amount;

        // check if newXP is less than 0 then set it to 0
        if ($newXP < 0) {
            $newXP = 0;
        }

        // update the current user xp
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_XP . " = '" . $newXP . "' WHERE " . TBL_USERS_USERNAME . " = '" . $this->userData[TBL_USERS_USERNAME] . "'";

        // get the sql results
        $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        // update the current xp level that the user has
        $this->userData[TBL_USERS_XP] = $newXP;

        // get the user's total lost xp
        $lostXP = $this->getLostXP();
        $newLostXP = $lostXP + $amount;

        // update the lost xp amount in database
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_LOST_XP . " = '" . $newLostXP . "' WHERE " . TBL_USERS_USERNAME . " = '" . $this->userData[TBL_USERS_USERNAME] . "'";

        // get the sql results
        $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        //update the current lost xp for the current session
        $this->userData[TBL_USERS_LOST_XP] = $newLostXP;

        return true;
    }

    /**
     * Enable double xp for the current user
     */
    public function setDoubleXP()
    {

    }

    /**
     * Check if double xp is activated for the current user
     * @return bool
     */
    public function hasDoubleXP()
    {
        return $this->userData[TBL_USERS_HAS_DOUBLEXP];
    }

    /**
     * Get the total amount of xp lost since join
     * @return int
     */
    public function getLostXP()
    {
        return $this->userData[TBL_USERS_LOST_XP];
    }

    /**
     * Check if the given pin number matches the user's pin
     * @param int $pin
     * @return boolean
     */
    public function matchPin($pin)
    {
        return (md5($pin) == $this->get(TBL_USERS_PIN));
    }

    /**
     * Update the required user record in the sql database
     * High Risk !! No checks is being performed at this point
     * you have to make sure all the data is being submitted in
     * the right intended way
     * @param string|array $data
     * @param mixed|null $value
     * @return bool
     */
    public function updateUserRecord($data, $value = null)
    {

        // define all the global variables
        global $database;

        // secure the data
        $field = $database->secureInput($data);
        $value = $database->secureInput($value);

        // setup the initial sql query content
        $content = "";

        // check if arrays has been submitted
        if (is_array($field)) {

            // get the length of the array of fields
            $length = count($field);
            $i = 1;

            // loop throw the array and add each record
            foreach ($field as $currentField => $currentValue) {

                // add the elements to the query string content
                $content .= $currentField . " = '" . $currentValue . "'";

                // check if not last then add a comma
                if ($i < $length) {
                    $content .= ", ";
                    $i++;
                }
            }
        } else {

            // check if value is empty
            if ($value == null) {
                return false;
            }

            // setup the default mysql 1 time update
            $content .= $field . " = '" . $value . "'";
        }

        // setup and complete the sql query
        $sql = "UPDATE " . TBL_USERS . " SET " . $content . " WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "' AND " . TBL_USERS_ID . " = '" . $this->getID() . "'";

        // query and check for errors
        $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        } else {
            return true;
        }
    }

}