<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 8:26 PM
 */
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class User
{

    private $userData; // declare the required variables for the user data.
    private $levelData; // declare the required variables for the level data.
    private $devices; // instance of the devices class of the current user
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
     * init the class
     */
    function init()
    {

    }

    /**
     * init an instance of this class and supply it with the user data ($data) and the other classes Or basically use a username for the variable ($data)
     * @param $data
     * @return bool
     */
    function initInstance($data)
    {

        // define all the global variables
        global $message;

        // check if $data is an array or only a username
        if (is_array($data)) {
            $this->userData = $data;
        } else { // if username is supplied, try to load the user
            if (!$this->loadInstance($data)) {
                $message->setError("The requested user does not exist", Message::Error);
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
        $results = mysqli_query($database->connection, $sql);

        // check for empty results
        if (mysqli_num_rows($results) < 1) {
            return false;
        }
        // call the results
        $row = mysqli_fetch_array($results);

        // set the userData
        $this->userData = $row;
        return true;
    }

    /**
     * Re initiate the user data if cookies were to be found after the class init
     */
    function initUserData()
    {

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

        return true;
    }

    /**
     * Check if the current user is an admin
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->userData[TBL_USERS_LEVEL] == 100) {
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
     * ban the current user
     * @return bool
     */
    function ban()
    {

        // define all the global variables
        global $database, $message;

        // check if banned
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $this->username() . "' AND " . TBL_USERS_BANNED . " = '0'";
        $results = mysqli_query($database->connection, $sql);

        // check for empty results (user is already banned before)
        if (mysqli_num_rows($results) < 1) {
            $message->setError("The user has been banned before", Message::Error);
            return false;
        }

        // ban the user
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_BANNED . " = '1' WHERE " . TBL_USERS_USERNAME . " = '" . $this->username() . "'";
        $results = mysqli_query($database->connection, $sql);

        // if any errors
        if (mysqli_num_rows($results) < 1) {
            $message->setError("Something went wrong while trying to update the records", Message::Error);
            return false;
        }

        return true;
    }

    /**
     * un-ban the current user
     * @return bool
     */
    function unBan()
    {

        // define all the global variables
        global $database, $message;

        // check if banned
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $this->username() . "' AND " . TBL_USERS_BANNED . " = '1'";
        $results = mysqli_query($database->connection, $sql);

        // check for empty results (user is already banned before)
        if (mysqli_num_rows($results) < 1) {
            $message->setError("The user has been banned before", Message::Error);
            return false;
        }

        // unBan the user
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_BANNED . " = '0' WHERE " . TBL_USERS_USERNAME . " = '" . $this->username() . "'";
        $results = mysqli_query($database->connection, $sql);

        // if any errors
        if (mysqli_num_rows($results) < 1) {
            $message->setError("Something went wrong while trying to update the records", Message::Error);
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
     * @return DateTime
     */
    function getDateJoined()
    {
        return $this->userData[TBL_USERS_DATE_JOINED];
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
     * get the user current level name
     * @return string
     */
    function getLevelName()
    {
        if ($this->getLevel() == 100) {
            return "Admin";
        } else if ($this->getLevel() == 1) {
            return "User";
        }

        return $this->levelData[TBL_LEVELS_NAME];
    }

    /**
     * get all the current user permissions
     * @return array
     */
    function getPermissions()
    {
        $permissions = explode("|", $this->levelData[TBL_LEVELS_PERMISSIONS]);
        array_map('trim', $permissions);
        return $permissions;
    }

    /**
     * Set this function to force the current user to log in again and re-initiate the data
     */
    function setMustSignInAgain()
    {

        // define all the global variables
        global $database, $message;

        // call the database to store the new session data
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_SIGNIN_AGAIN . " = '1' WHERE " . TBL_USERS_ID . " = '" . $this->getID() . "' AND " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
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
     * Get a certain level information
     * @param $level
     * @return array
     */
    function loadLevel($level)
    {

        // define all the global variables
        global $database, $message;

        $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_LEVEL . " = '" . $level . "'";
        $result = mysqli_query($database->connection, $sql);
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
            die;
        }
        $row = mysqli_fetch_array($result);
        return $row;
    }

    /**
     * @param $level
     * @return array
     */
    function loadLevelPermissions($level)
    {

        // define all the global variables
        global $database, $message;

        // load the current user permissions
        $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_LEVEL . " = '" . $level . "'";
        $result = mysqli_query($database->connection, $sql);
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->kill("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__, __LINE__ - 2);
            die;
        }
        $row = mysqli_fetch_array($result);

        // seperate every single permission after a | sign and store it in an array and return it
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
        // check if the user is an admin
        if ($this->getLevel() == 100) {
            return true;
        }
        // check if the user is a new user with level 1
        if ($this->getLevel() == 1) {
            return false;
        }

        // load the permissions for the current user
        $permissions = $this->getPermissions();

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

        // define all the global variables
        global $database, $message;

        // get the current time
        $time = date("Y-m-d H:i:s", time());

        // update the timestamp in the database
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_HEARTBEAT . " = '" . $time . "' WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while updating data in the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
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
        global $database, $message;

        // check if account is already activated then just return true
        if ($this->is_accountActivated()) {
            $message->setError("The account has already been activated before.", Message::Error);
            return false;
        }

        // if account is not activated then update the sql records
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_ACTIVATED . " = '1' WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while updating data in the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        // if everything goes right then return true
        $message->setSuccess("The account " . $this->getUsername() . " has been activated");
        return true;
    }

    /**
     * disable the users account
     * @return bool
     */
    function disableAccount()
    {

        // define all the global variables
        global $database, $message;

        // check if account is not activated then just return true
        if (!$this->is_accountActivated()) {
            $message->setError("The account has not been activated before.", Message::Error);
            return false;
        }

        // if account is activated then update the sql records
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_ACTIVATED . " = '0' WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while updating data in the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

        // if everything goes right then return true
        $message->setSuccess("The account " . $this->getUsername() . " has been de-activated");
        return true;
    }

    /**
     * Log the user out from the current session
     * @return bool
     */
    public function logOut()
    {

        // define all the global variables
        global $database, $message;

        // check if the user is already not logged in and return true
        if (empty($_SESSION["user_data"]) && empty($_COOKIE["user_data"]) && empty($_COOKIE["user_id"])) {
            return true;
        } else {

            // ** Clear the Cookie auth code ** //
            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_TOKEN . " = '' WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "'";
            if (!$result = mysqli_query($database->connection, $sql)) {
                return false;
            }

            // ** Unset the session & cookies ** //
            unset($_SESSION["user_data"]);
            unset($_COOKIE["user_data"]);
            unset($_COOKIE["user_id"]);
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

        // define all the global variables
        global $database, $message;

        if (!is_numeric($amount)) {
            return false;
        }

        // check if double xp then double the amount
        if ($this->hasDoubleXP()) {
            $amount = $amount * 2;
        }

        // add the old user xp to the new one
        $newXP = $this->getXP() + $amount;

        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_XP . " = '" . $newXP . "' WHERE " . TBL_USERS_USERNAME . " = '" . $this->getUsername() . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while updating data in the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__ - 2);
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
        global $database, $message;

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
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Error while updating data in the database : " . mysqli_error($database->connection), Message::Fatal, __FILE__, __LINE__ - 2);
            return false;
        }

        // update the current xp level that the user has
        $this->userData[TBL_USERS_XP] = $newXP;

        // get the user's total lost xp
        $lostXP = $this->getLostXP();
        $newLostXP = $lostXP + $amount;

        // update the lost xp amount in database
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_LOST_XP . " = '" . $newLostXP . "' WHERE " . TBL_USERS_USERNAME . " = '" . $this->userData[TBL_USERS_USERNAME] . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
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

}