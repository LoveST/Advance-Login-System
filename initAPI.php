<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 4/24/2017
 * Time: 12:53 PM
 */

// include in the beginning of every single php file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");
// Start the session
session_start();
// Turn off all error reporting
error_reporting(0);

require "include/config.php";
require "include/database.php";
require "include/settings.php";
require "include/user/device.php";
require "include/browser.php";
require "include/captcha.php";
require "include/user.php";
require "include/user/devices.php";
require "include/mail.php";
require "include/functions.php";
require "include/passwordManager.php";
require "include/session.php";
require "include/administrator.php";
require "include/profileManager.php";
require "include/api.php";

/**
 * init Database class
 */

use ALS\Database\Database;

$database = new Database();

/**
 * init Settings class
 */

use ALS\Settings\Settings;

$settings = new Settings();

/**
 * init the Browser class
 */

use ALS\Browser\Browser;

$browser = new Browser();

/**
 * init Captcha class
 */

use ALS\Captcha\Captcha;

$captcha = new Captcha();

/**
 * init User class
 */

use ALS\User\User;

$user = new User();
$user->init();

/**
 * init Mail class
 */

use ALS\Mail\Mail;

$mail = new Mail();

/**
 * init passwordManager class
 */

use ALS\passwordManager;

$passwordManager = new passwordManager();

/**
 * init Functions class
 */

use ALS\Functions\Functions;

$functions = new Functions();
$functions->init();


/**
 * init Session class
 */

use ALS\Session\Session;

$session = new Session();
$session->init();

/**
 * init Administrator class
 */

use ALS\Administrator\Administrator;

$admin = new Administrator();

/**
 * init profileManager class
 */

use ALS\profileManager;

$profileManager = new profileManager();

/**
 * init API class
 */

use ALS\API\API;

$api = new API;