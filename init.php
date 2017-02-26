<?

// include in the beginning of every single php file
if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");
// Start the session
session_start();

// Turn off all error reporting
//error_reporting(1);

require "include/config.php";
require "include/message.php";
require "include/database.php";
require "include/settings.php";
require "include/user.php";
require "include/mail.php";
require "include/functions.php";
require "include/passwordManager.php";
require "include/session.php";
require "include/administrator.php";

/**
 * init Message class
 */

    $message = new Message();
    $message->init();

/**
 * init Database class
 */

    $database = new Database();
    $database->init($message);

/**
 * init Settings class
 */

    $settings = new Settings();
    $settings->init($message,$database);

/**
 * init User class
 */

    $user = new User();
    $user->init($database,$message);

/**
 * init Mail class
 */

    $mail = new Mail();

/**
 * init Functions class
 */

    $functions = new Functions();
    $functions->init($database,$message,$user,$mail,$settings);

/**
 * init passwordManager class
 */

    $passwordManager = new passwordManager();
    $passwordManager->init($database,$message,$user,$mail,$settings);

/**
 * init Session class
 */

    $session = new session();
    $session->init($database,$message,$user,$passwordManager,$mail,$settings,$functions);
    $user->initUserData(); // if cookies were found then re-init the main session

/**
 * init Administrator class
 */

    $admin = new Administrator();
    $admin->init($database,$message,$user,$mail,$settings);

 /**
  * Print out all the Fatal errors
  */

    echo $message->getError(1);