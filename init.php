<?

// include this file in every single php file you'd life to have access to the script facilities

if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");
session_start();
require "config.php";
require "include/message.php";
require "database.php";
require "include/user.php";
require "include/mail.php";
require "include/passwordManager.php";
require "session.php";

/**
 * init Message class
 */

    $message = new Message();
    $message->init();

/**
 * init Database class
 */

    $user = new User();
    $user->init($message);

/**
 * init Database class
 */

    $database->Database();
    $database->init($message);

/**
 * init passwordManager class
 */

    $mail = new Mail();
    $passwordManager->init($database,$message,$user,$mail);

/**
 * init Session class
 */

    $session = new session();
    $session->init($database,$message,$user,$passwordManager,$mail);