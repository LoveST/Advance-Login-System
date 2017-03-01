<?
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/12/2016
 * Time: 12:42 PM
 */

if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");
session_start();
date_default_timezone_set('UTC');

class session {

    var $connection; // public variable for the database connection
    public $message; // instance of the Message class.
    private $userData; // instance of the user class.
    private $passwordManager; // instance of the password manager class
    private $mail; // instance of the mail class.
    private $settings; // instance of Settings class.
    private $functions; // instance of Functions class.

    /**
     * session constructor.
     */
    function __construct(){

    }

    /**
     * init the class
     * @param $databaseClass
     * @param $messageClass
     * @param $userDataClass
     * @param $passwordManagerClass
     * @param $mailClass
     */
    function init($databaseClass, $messageClass, $userDataClass, $passwordManagerClass, $mailClass, $settings, $functions){
        $this->message = $messageClass; // init the message class for any errors
        $this->userData = $userDataClass; // init the User class
        $this->mail = $mailClass; // init the Mail class
        $this->passwordManager = $passwordManagerClass;
        $this->settings = $settings;
        $this->functions = $functions;
        $this->dbConnect($databaseClass); // init the connect to database function
        $this->loginThrowCookie(); // log the user in if he has the right cookie for his account
    }

    /**
     * Initiate the connection to the database
     *@param $databaseClass
     */
    private function dbConnect($databaseClass){
        $this->connection = $databaseClass->connection;
    }

    /**
     * Main function to login a guest as a user or admin if needed using a password
     * @param $username
     * @param $password
     * @param int $rememberMe
     * @return true|false
     */
    public function loginWithPassword($username, $password, $rememberMe = 1){

        $username = $this->escapeString($username);
        $password = $this->escapeString($password);
        $rememberMe = $this->escapeString($rememberMe);

        if(!$this->settings->canLogin()){
            $this->message->setError("Logging in has been disabled at the moment.", Message::Error);
            return false;
        }

        if(empty($username) || empty($password)){
            $this->message->setError("Username/Password most not be empty", Message::Error);
            return false;
        }

        // Username checks
        if(preg_match('/[^A-Za-z0-9]/', $username)){
            $this->message->setError("Username most contain only letters and numbers", Message::Error);
            return false;
        }

        if(strlen($username) < 6 || strlen($username) > 25){
            $this->message->setError("Username length most be between 6 -> 25 characters long", Message::Error);
            return false;
        }

        if(!$this->functions->userExist($username)){
            $this->message->setError("Wrong username/password has been used", Message::Error);
            return false;
        }

        // password checks
        if(strlen($password) < 8 && strlen($password) > 25){
            $this->message->setError("Password length most be between 8 -> 25 characters long", Message::Error);
            return false;
        }

        // check if the user account is enabled
        if(!$this->functions->is_userActivated($username)){
            $this->message->setError("You need to activate your account before trying to log in.", Message::Error);
            return false;
        }

        $password = md5($password); // hash the password

        $sql = ("SELECT * FROM ".TBL_USERS." WHERE ".TBL_USERS_USERNAME." = '". $username ."' AND ".TBL_USERS_PASSWORD." = '". $password . "'");

        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        if(mysqli_num_rows($result) < 1){
            $this->message->setError("Wrong username/password has been used", Message::Error);
            return false;
        }

        $row = mysqli_fetch_assoc($result);

        // ** login successful process ** //
        $rememberMe = (86400 * $rememberMe); // one day as default
        $newtoken = md5(uniqid(rand(), true));
        $newuserhash = md5($username);
        $userIP = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_X_FORWARDED_FOR']);
        $value = "$userIP|$newtoken|$newuserhash";
        $value = hash_hmac('md5', $value, $this->settings->get(Settings::SECRET_CODE));
        $id = $row[TBL_USERS_ID]; // id of the current user logging in

        // ** Update the user Cookie code ** //
        $sql = "UPDATE ".TBL_USERS." SET " . TBL_USERS_TOKEN." = '".$value."' WHERE ". TBL_USERS_USERNAME." = '".$username."'";
        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
            return false;
        }

        // ** set the cookie ** //
        setcookie("user_data", $value, time() + $rememberMe, "/"); // 86400 = 1 day
        setcookie("user_id", $id, time() + $rememberMe, "/"); // 86400 = 1 day

        // ** Update the online users counter ** //
        $currentTime = date("Y-m-d H:i:s", time());
        $id = $row[TBL_USERS_ID];
        $username = $row[TBL_USERS_USERNAME];

        /**

        $sql2 = "INSERT
                 INTO ". TBL_HEARTBEAT . " (".TBL_HEARTBEAT_ID.",".TBL_HEARTBEAT_USERNAME.",".TBL_HEARTBEAT_TIMESTAMP.") VALUES ('$id','$username','$currentTime')";
        if (!$result2 = mysqli_query($this->connection, $sql2)) {
            $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__, __LINE__);
            return false;
        }

         **/

        // ** Update the current session data ** //
        $_SESSION["user_data"][TBL_USERS_ID] = $row[TBL_USERS_ID];
        $_SESSION["user_data"][TBL_USERS_USERNAME] = $row[TBL_USERS_USERNAME];
        $_SESSION["user_data"][TBL_USERS_LEVEL] = $row[TBL_USERS_LEVEL];
        $_SESSION["user_data"][TBL_USERS_FNAME] = $row[TBL_USERS_FNAME];
        $_SESSION["user_data"][TBL_USERS_LNAME] = $row[TBL_USERS_LNAME];
        $_SESSION["user_data"][TBL_USERS_DATE_JOINED] = $row[TBL_USERS_DATE_JOINED];
        $_SESSION["user_data"][TBL_USERS_EMAIL] = $row[TBL_USERS_EMAIL];
        $_SESSION["user_data"][TBL_USERS_BANNED] = $row[TBL_USERS_BANNED];
        $_SESSION["user_data"][TBL_USERS_XP] = $row[TBL_USERS_XP];
        $_SESSION["user_data"][TBL_USERS_LOST_XP] = $row[TBL_USERS_LOST_XP];

            return true;
    }

    /**
     * Main function to login a guest as a user or admin if needed using a pin number
     * @param $email
     * @param $pin
     * @param int $rememberMe
     */
    public function loginWithPin($email, $pin, $rememberMe = 0){

    }

    /**
     * Log the user in if a matching cookie available
     * @return bool
     */
    function loginThrowCookie(){
        if(empty($_SESSION["user_data"])) { // check if the current session is empty
            if (!empty($_COOKIE["user_data"]) && !empty($_COOKIE["user_id"])) { // check if the current cookie is not empty or null
                $userID = mysqli_real_escape_string($this->connection, $_COOKIE["user_id"]);
                $cookieValue = mysqli_real_escape_string($this->connection, $_COOKIE["user_data"]);

                // ** Get the needed information from the database ** //
                $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = '" . $userID . "' AND " . TBL_USERS_TOKEN . " = '" . $cookieValue . "'";
                if (!$result = mysqli_query($this->connection, $sql)) {
                    $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__, __LINE__);
                    return false;
                }

                // ** Check if such a user exists ** //
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    // ** Update the online users counter ** //
                    $currentTime = date("Y-m-d H:i:s", time());
                    $id = $row[TBL_USERS_ID];
                    $username = $row[TBL_USERS_USERNAME];

                    // ** Update the current session data ** //
                    $_SESSION["user_data"][TBL_USERS_ID] = $row[TBL_USERS_ID];
                    $_SESSION["user_data"][TBL_USERS_USERNAME] = $row[TBL_USERS_USERNAME];
                    $_SESSION["user_data"][TBL_USERS_LEVEL] = $row[TBL_USERS_LEVEL];
                    $_SESSION["user_data"][TBL_USERS_FNAME] = $row[TBL_USERS_FNAME];
                    $_SESSION["user_data"][TBL_USERS_LNAME] = $row[TBL_USERS_LNAME];
                    $_SESSION["user_data"][TBL_USERS_DATE_JOINED] = $row[TBL_USERS_DATE_JOINED];
                    $_SESSION["user_data"][TBL_USERS_EMAIL] = $row[TBL_USERS_EMAIL];
                    $_SESSION["user_data"][TBL_USERS_BANNED] = $row[TBL_USERS_BANNED];
                    $_SESSION["user_data"][TBL_USERS_XP] = $row[TBL_USERS_XP];
                    $_SESSION["user_data"][TBL_USERS_LOST_XP] = $row[TBL_USERS_LOST_XP];
                }
                return true;
            } else { return false; }
        } else { return false; }
    }

    public function register($username,$email,$email2,$password,$password2,$pin,$pin2,$firstName,$lastName,$dataOfBirth){

        // check if registration is enabled
        if(!$this->settings->canRegister()){
            $this->message->setError("Registration is disabled at the moment.", Message::Error);
            return false;
        }

        // escape all the given strings and integers
        $username = $this->escapeString($username);
        $email = $this->escapeString($email);
        $email2 = $this->escapeString($email2);
        $password = $this->escapeString($password);
        $password2 = $this->escapeString($password2);
        $pin = $this->escapeString($pin);
        $pin2 = $this->escapeString($pin2);
        $firstName = $this->escapeString($firstName);
        $lastName = $this->escapeString($lastName);
        $dataOfBirth = $this->escapeString($dataOfBirth);

        // check for empty given strings
        if(empty($username) || empty($email) || empty($email2) || empty($password) || empty($password2) || empty($firstName) || empty($lastName) || empty($dataOfBirth)){
            $this->message->setError("All fields are required", Message::Error);
            return false;
        }

        // check if pin is required and if required check if 0 is given for empty
        if($this->settings->pinRequired()){
            if(empty($pin) || empty($pin2)){
                $this->message->setError("All fields are required", Message::Error);
                return false;
            }
        }

        // Username checks
        if(preg_match('/[^A-Za-z0-9]/', $username)){
            $this->message->setError("Username most contain only letters and numbers", Message::Error);
            return false;
        }

        if(strlen($username) < 6 || strlen($username) > 25){
            $this->message->setError("Username length most be between 6 -> 25 characters long", Message::Error);
            return false;
        }

        // email checks
        if($email != $email2){
            $this->message->setError("Email fields should be the identical", Message::Error);
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->message->setError("invalid email syntax has been used", Message::Error);
            return false;
        }

        // password checks
        if(md5($password) != md5($password2)){
            $this->message->setError("Password fields should be the identical", Message::Error);
            return false;
        }

        if(strlen($password) < 8 && strlen($password) > 25){
            $this->message->setError("Password length most be between 8 -> 25 characters long", Message::Error);
            return false;
        }

        // check if pin is required then check for length and the value being an integer and if they are both the same
        if($this->settings->pinRequired()) { // check if pin is needed
            if ($pin != $pin2) {
                $this->message->setError("Pin number fields should be the identical", Message::Error);
                return false;
            }

            if (!is_numeric($pin)) {
                $this->message->setError("Pin number should only contain numbers", Message::Error);
                return false;
            }

            if ($pin[0] == 0) {
                $this->message->setError("Pin number cannot start with a 0", Message::Error);
                return false;
            }

            if (strlen($pin) < 6 || strlen($pin) > 6) {
                $this->message->setError("Pin number has to be exactly 6 characters long", Message::Error);
                return false;
            }


        }

        // date of birth checks
        if(!is_string($dataOfBirth)){
            $this->message->setError("Date of birth should be a string", Message::Error);
            return false;
        }

        if(!$this->functions->isValidDate($dataOfBirth)){ // $this->functions->isValidDate($dataOfBirth)
            $this->message->setError("Date of birth should be in the form of (mm/dd/yyyy)", Message::Error);
            return false;
        }

        if($this->settings->minimumAgeRequired()){ // check if there is a minimum age restriction for signing up
            if($this->functions->getAge($dataOfBirth) < $this->settings->minimumAge()){
                $this->message->setError("You must be at least " . $this->settings->minimumAge() . " years old to sign up", Message::Error);
                return false;
            }
        }

        // check if username exists
        if($this->functions->userExist($username)){
            $this->message->setError("Username has been used before", Message::Error);
            return false;
        }

        // check if email exists
        if($this->functions->emailExist($email)){
            $this->message->setError("Email has been used before", Message::Error);
            return false;
        }

        // secure the password and the pin if submitted
        $password = md5($password);
        if($this->settings->pinRequired()) { // check if pin is needed
            $pin = md5($pin);
        }

        $date = time(); // current time and date to be set as a date of signing up
        $loginTime = date("Y-m-d H:i:s", time());
        // get a new id for the user
        $id = $this->functions->getNewID();

        // check if activation is required or not and proceed
        if($this->settings->activationRequired()){
            // send the activation code and update the database
            $activationCode = $this->functions->generateRandomString(20);

            $sql = "INSERT
                    INTO ".TBL_USERS." (".TBL_USERS_ID.",".TBL_USERS_USERNAME.",".TBL_USERS_PASSWORD.",".TBL_USERS_FNAME.",".TBL_USERS_LNAME.",".TBL_USERS_EMAIL.",".TBL_USERS_LEVEL.",".TBL_USERS_DATE_JOINED.",".TBL_USERS_LAST_LOGIN.",".TBL_USERS_TOKEN.",".TBL_USERS_EXPIRE.",".TBL_USERS_RESET_CODE.",".TBL_USERS_PIN.",".TBL_USERS_BANNED.",".TBL_USERS_ACTIVATED.",".TBL_USERS_ACTIVATION_CODE.")
                    VALUES ('$id','$username','$password','$firstName','$lastName','$email','1','$loginTime','$loginTime','0','0','0','$pin','0','0','$activationCode')";

            if (!$result = mysqli_query($this->connection, $sql)) {
                $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__, __LINE__ - 2);
                return false;
            }

            // if successful registration then send an email including the activation code
            if(!$this->mail->sendText($this->settings->siteEmail(), $email, "activation code", "your account activation code is : " . $activationCode)){
                $this->message->setError("Registration completed. But field to send the activation code.", Message::Error);
                return false;
            }

            return true;

        } else {
            // automatically activate the user and update the database
            $sql = "INSERT
                    INTO ".TBL_USERS." (".TBL_USERS_ID.",".TBL_USERS_USERNAME.",".TBL_USERS_FNAME.",".TBL_USERS_LNAME.",".TBL_USERS_EMAIL.",".TBL_USERS_LEVEL.",".TBL_USERS_PASSWORD.",".TBL_USERS_DATE_JOINED.",".TBL_USERS_LAST_LOGIN.",".TBL_USERS_EXPIRE.",".TBL_USERS_TOKEN.",".TBL_USERS_RESET_CODE.",".TBL_USERS_PIN.",".TBL_USERS_BANNED.",".TBL_USERS_ACTIVATED.",".TBL_USERS_ACTIVATION_CODE.")
                    VALUES ('$id','$username','$firstName','$lastName','$email','1','$password','$loginTime','$loginTime','0','0','0','$pin','0','1','0')";

            if (!$result = mysqli_query($this->connection, $sql)) {
                $this->message->kill("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__, __LINE__ - 2);
                return false;
            }

            return true;
        }
    }

    /**
     * Log the user out from the current session
     * @return bool
     */
    public function logOut(){
        if(empty($_SESSION["user_data"]) && empty($_COOKIE["user_data"]) && empty($_COOKIE["user_id"])){ // check to see if the user is logged in the session of in the cookies
            return false;
        } else {

            // ** Clear the Cookie auth code ** //
            $sql = "UPDATE ".TBL_USERS." SET " . TBL_USERS_TOKEN." = '' WHERE ". TBL_USERS_USERNAME." = '".$this->userData->get(User::UserName)."'";
            if (!$result = mysqli_query($this->connection,$sql)) {
                $this->message->setError("All fields are required", Message::Error);
                return false;
            }

            // ** Unset the session & cookies ** //
            unset($_SESSION["user_data"]);
            unset($_COOKIE["user_data"]);
            unset($_COOKIE["user_id"]);
            setcookie("user_data",null,-1,'/');
            setcookie("user_id",null,-1,'/');
            return true;
        }
    }

    //activate account
    public function activateAccount($code, $email){

        // escape the given strings
        $code = $this->escapeString($code);
        $email = $this->escapeString($email);

        // start the checks
        if(empty($code) || empty($email)){
            $this->message->setError("Code/Email fields most not be empty", Message::Error);
            return false;
        }

        // check if email exists
        if(!$this->functions->emailExist($email)){
            $this->message->setError("The provided email is not in our database.", Message::Error);
            return false;
        }

        // check if the given code matches the required characters
        if (strlen($code) < 20 || strlen($code) > 20) {
            $this->message->setError("The given code has to be exactly 20 characters long", Message::Error);
            return false;
        }

        // check if account already has been activated
        if($this->functions->is_userActivated($email, true)){
            $this->message->setError("The account is already activated", Message::Error);
            return false;
        }

        $sql = "SELECT * FROM ". TBL_USERS . " WHERE ". TBL_USERS_EMAIL . " = '". $email . "' AND ". TBL_USERS_ACTIVATION_CODE . " = '" . $code . "'";
        if (!$result = mysqli_query($this->connection, $sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__, __LINE__ - 2);
            return false;
        }

        // check if wrong code has been used
        if(mysqli_num_rows($result) < 1){
            $this->message->setError("Wrong activation code has been used.", Message::Error);
            return false;
        }

        //update the user account with the needed information
        $sql = "UPDATE ". TBL_USERS . " SET ". TBL_USERS_ACTIVATED . " ='1',". TBL_USERS_ACTIVATION_CODE . "='' WHERE ". TBL_USERS_EMAIL . " = '". $email . "' AND ". TBL_USERS_ACTIVATION_CODE . " = '" . $code . "'";
        if (!$result = mysqli_query($this->connection, $sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__, __LINE__ - 2);
            return false;
        }

        return true;
    }

    /**
     * check if the client is logged in as a user or as a guest !
     * @return bool
     */
    public function logged_in(){
        if(isset($_SESSION['user_data']) || $_SESSION['user_data']){
            return true;
        }
    }

    /**
     * Prepare any given string from injections
     * @param $string
     * @return string
     */
    function escapeString($string){
        return mysqli_real_escape_string($this->connection, $string);
    }
}

$session = new session();