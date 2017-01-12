<?
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/12/2016
 * Time: 12:42 PM
 */
if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");
session_start();
require "include/message.php";
require "config.php";
require "database.php";

class session {

    var $connection; // public variable for the database connection
    private $user = Array(); // store all the needed user information
    private $message; // instance of the Message class.

    /**
     * session constructor.
     */
    function __construct(){
        $this->message = new Message(); // init the message class for any errors
        $this->dbConnect(); // init the connect to database function
        $this->setupLogin(); // store all the current user information if logged in

    }

    /**
     * Initiate the connection to the database
     */
    private function dbConnect(){
        $database = new Database();
        $this->connection = $database->connection;
    }

    /**
     * Setup up the session if a user is logged in
     */
    private function setupLogin(){
        if(isset($_SESSION['userInfo']) || $_SESSION['userInfo']){
            $this->user = $_SESSION['userInfo'];
        }
    }

    /**
     * Main function to login a guest as a user or admin if needed using a password
     * @param $username
     * @param $password
     * @param int $rememberMe
     * @return true|false
     */
    public function loginWithPassword($username, $password, $rememberMe = 0){
        if(empty($username) || empty($password)){
            $this->setError("Username/Password most not be empty");
            return false;
        }

        // Username checks
        if(preg_match('/[^A-Za-z0-9]/', $username)){
            $this->setError("Username most contain only letters and numbers");
            return false;
        }

        if(strlen($username) < 6 || strlen($username) > 25){
            $this->setError("Username length most be between 6 -> 25 characters long");
            return false;
        }

        // password checks
        if(strlen($username) >= 8 && strlen($username) <= 25){
            $this->setError("Password length most be between 8 -> 25 characters long");
            return false;
        }

        $password = md5($password);
        $sql = $this->connection->prepare("SELECT * FROM users WHERE username = '". $username ."' AND password = '". $password . "'");
        $sql->execute();

        if($sql->rowCount() < 1){
            $this->setError("Wrong username/password has been used");
            return false;
        }

        $row = $sql->fetch(PDO::FETCH_ASSOC);


        // ** login successful process ** //
        $rememberMe = (86400 * $rememberMe);
        $newtoken = md5(uniqid(rand(), true));
        $newuserhash = md5($username);
        $userIP = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_X_FORWARDED_FOR']);
        $value = "$userIP|$newtoken|$newuserhash";

        $_SESSION['username'] = $row['username'];
        $_SESSION['level'] = $row['level'];
        $_SESSION['f_name'] = $row['firstName'];
        $_SESSION['l_name'] = $row['lastName'];

        setcookie('remember', $value, $rememberMe, '/', SITEURL, isset($_SERVER["HTTP"]), true);

        $sql = $this->connection->prepare("UPDATE users SET token='" . $newtoken ."', expire='". $rememberMe ."' WHERE " . TBL_USERS_USERNAME ."='". $username ."'");
        $sql->bindParam(':username', $username);
        if($sql->execute() && $sql->rowCount() > 0){

            $info = Array("username" => $username, "email" => $row['username']);
            $_SESSION["userInfo"] = $info;
            return true;
        } else {
            $this->setError("Something went wrong while trying to log in :(");
            return false;
        }
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
     * Set the error message to be shown to the guest or to the user
     * @param $msg
     */
    private function setError($msg = "Unknown error"){
        $_SESSION['error'] = $msg;
    }

    /**
     * Get the error message if any occurred
     * @return mixed
     */
    public function getError(){
        return $_SESSION['error'];
    }

    public function register(){

    }

    public function logOut(){

    }

    public function logged_in(){
        if(!empty(['userInfo'])){
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
     * Get the current session username
     * @return mixed
     */
    public function Username(){
        return $this->user['username'];
    }

    /**
     * Get the current session user email
     * @return mixed
     */
    public function Email(){
        return $this->user['email'];
    }

    /**
     * Get the current session user first name
     * @return mixed
     */
    public function firstName(){
        return $this->user['firstName'];
    }

    /**
     * Get the current session user last name
     * @return mixed
     */
    public function lastName(){
        return $this->user['lastName'];
    }
}

$session = new session();   // init the session class