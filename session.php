<?
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/12/2016
 * Time: 12:42 PM
 */

if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");
session_start();

class session {

    var $connection; // public variable for the database connection
    public $message; // instance of the Message class.
    private $userData; // instance of the user class.
    private $passwordManager; // instance of the password manager class
    private $mail; // instance of the mail class.

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
    function init($databaseClass, $messageClass, $userDataClass, $passwordManagerClass, $mailClass){
        $this->message = $messageClass; // init the message class for any errors
        $this->userData = $userDataClass; // init the User class
        $this->mail = $mailClass; // init the Mail class
        $this->passwordManager = $passwordManagerClass;
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

        // password checks
        if(strlen($password) < 8 && strlen($password) > 25){
            $this->message->setError("Password length most be between 8 -> 25 characters long", Message::Error);
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
        $value = hash_hmac('md5', $value, COOKIE_AUTH_SECRET);
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

        $_SESSION["user_data"]['username'] = $row[TBL_USERS_USERNAME];
        $_SESSION["user_data"]['level'] = $this->userData->levelName($row[TBL_USERS_LEVEL]);
        $_SESSION["user_data"]['firstName'] = $row[TBL_USERS_FNAME];
        $_SESSION["user_data"]['lastName'] = $row[TBL_USERS_LNAME];
        $_SESSION["user_data"]['date_joined'] = $row[TBL_USERS_SINCE];
        $_SESSION["user_data"]['email'] = $row[TBL_USERS_EMAIL];

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

                    // ** Update the current session data ** //
                    $_SESSION["user_data"]['username'] = $row[TBL_USERS_USERNAME];
                    $_SESSION["user_data"]['level'] = $this->userData->levelName($row[TBL_USERS_LEVEL]);
                    $_SESSION["user_data"]['firstName'] = $row[TBL_USERS_FNAME];
                    $_SESSION["user_data"]['lastName'] = $row[TBL_USERS_LNAME];
                    $_SESSION["user_data"]['date_joined'] = $row[TBL_USERS_SINCE];
                    $_SESSION["user_data"]['email'] = $row[TBL_USERS_EMAIL];
                }

            }
        }
    }

    /**
     * @param $username
     * @param $email
     * @return bool
     */
    function forgetPasswordWithEmail($username, $email){
        if(!$this->passwordManager->forgetPasswordWithEmail($username,$email)){
            return false;
        } else { return true;}
    }

    /**
     * @param $email
     * @param $code
     * @return mixed
     */
    function resetPasswordUsingCodeAndEmail($email, $code){
        return $this->passwordManager->resetPasswordUsingCodeAndEmail($email,$code);
    }

    /**
     * @param $email
     * @param $code
     * @param $password
     * @param $password2
     * @return mixed
     */
    function pickNewPassword($email,$code,$password,$password2){
        return $this->passwordManager->confirmNewPassword($email,$code,$password,$password2);
    }

    public function register(){

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
                $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
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
     * Check if the current user is an admin
     * @return bool
     */
    public function isAdmin(){
        if($this->user['level'] == -1){ return true; } else { return false; }
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