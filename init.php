<?

// include in the beginning of every single php file
if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");
// Start the session
session_start();

// Turn off all error reporting
error_reporting(0);

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
require "include/profileManager.php";

/**
 * init Message class
 */

    $message = new Message();
    $message->init();

/**
 * init Database class
 */

    $database = new Database();
    $database->init();

/**
 * init Settings class
 */

    $settings = new Settings();
    $settings->init();
    $settings->checkHTTPS(); // check for SSL encryption

/**
 * init User class
 */

    $user = new User();
    $user->init();

/**
 * init Mail class
 */

    $mail = new mail();

/**
 * init passwordManager class
 */

    $passwordManager = new passwordManager();
    $passwordManager->init();

/**
 * init Functions class
 */

    $functions = new Functions();
    $functions->init();


/**
 * init Session class
 */

    $session = new session();
    $session->init();

/**
 * init Administrator class
 */

    $admin = new Administrator();
    $admin->init();

/**
 * init profileManager class
 */

    $profileManager = new profileManager();
    $profileManager->init();

 /**
  * Print out all the Fatal errors
  */

    echo $message->getError(1);