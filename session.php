<?
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/12/2016
 * Time: 12:42 PM
 */

error_reporting(0);
if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");
session_start();
require "include/user.php";
require "config.php";
require "database.php";

class session {

    var $connection; // public variable for the database connection
    private $message; // instance of the Message class.
    private $userData; // instance of the user class.

    /**
     * session constructor.
     */
    function __construct(){
        $this->message = new Message(); // init the message class for any errors
        $this->userData = new User(); // init the message class for any errors
        $this->dbConnect(); // init the connect to database function
    }

    /**
     * Initiate the connection to the database
     *
     */
    private function dbConnect(){
        $database = new Database();
        $this->connection = $database->connection;
    }

    /**
     * Main function to login a guest as a user or admin if needed using a password
     * @param $username
     * @param $password
     * @param int $rememberMe
     * @return true|false
     */
    public function loginWithPassword($username, $password, $rememberMe = 0){

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

        $sql = ("SELECT * FROM users WHERE username = '". $username ."' AND password = '". $password . "'");

        if (!$result = mysqli_query($this->connection,$sql)) {
            $this->message->setError("Error while pulling data from the database : " . mysqli_error($this->connection), Message::Fatal, __FILE__,__LINE__);
        }

        if(mysqli_num_rows($result) < 1){
            $this->message->setError("Wrong username/password has been used", Message::Error);
            return false;
        }

        $row = mysqli_fetch_assoc($result);

        // ** login successful process ** //
        $rememberMe = (86400 * $rememberMe);
        $newtoken = md5(uniqid(rand(), true));
        $newuserhash = md5($username);
        $userIP = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_X_FORWARDED_FOR']);
        $value = "$userIP|$newtoken|$newuserhash";

        $_SESSION["user_data"]['username'] = $row['username'];
        $_SESSION["user_data"]['level'] = $row['level'];
        $_SESSION["user_data"]['firstName'] = $row['firstName'];
        $_SESSION["user_data"]['lastName'] = $row['lastName'];

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
     *
     * @param $username
     * @param $email
     */
    function forgetPasswordWithEmail($username, $email){

    }

    function resetPasswordUsingCodeAndEmail($email, $code){

    }

    public function register(){

    }

    /**
     * Log the user out from the current session
     * @return bool
     */
    public function logOut(){
        if(empty($_SESSION["user_data"])){
            return false;
        } else { unset($_SESSION["user_data"]); return true;}
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

$session = new session();   // init the session class