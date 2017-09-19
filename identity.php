<?

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 9/14/2017
 * Time: 11:42 AM
 *
 * The only purpose of this file, is to define the variables so that
 * the IDE can identify the variables them selves and access the class variables
 * without the need of figuring out which goes where.
 */

// include in the beginning of every single php file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");
// Turn off all error reporting
error_reporting(0);
require "include/config.php";
require "include/auth/Google.php";
require "include/message.php";
require "include/database.php";
require "include/settings.php";
require "include/Groups.php";
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
require "include/MailTemplates.php";
require "include/ViewController.php";
/**
 * init Message class
 */
use ALS\Message;
$message = new Message();
$message->init();
/**
 * init Database class
 */
use ALS\Database;
$database = new Database();
/**
 * init Settings class
 */
use ALS\Settings;
$settings = new Settings();
/**
 * init the Groups class
 */
use ALS\Groups;
$groups = new Groups();
/**
 * init the View Controller Class
 */
use ALS\ViewController;
$viewController = new ViewController();
$translator = $viewController->getTranslator();
/**
 * init the Browser class
 */
use ALS\Browser;
$browser = new Browser();
/**
 * init Captcha class
 */
use ALS\Captcha;
$captcha = new Captcha();
/**
 * init Mail class
 */
use ALS\Mail;
$mail = new Mail();
/**
 * init User class
 */
use ALS\User;
$user = new User();
/**
 * init passwordManager class
 */
use ALS\passwordManager;
$passwordManager = new passwordManager();
/**
 * init Functions class
 */
use ALS\Functions;
$functions = new Functions();
/**
 * init Session class
 */
use ALS\Session;
$session = new Session();
$session->init();
/**
 * init Administrator class
 */
use ALS\Administrator;
$admin = new Administrator();
/**
 * init profileManager class
 */
use ALS\profileManager;
$profileManager = new profileManager();
/**
 * init the MailTemplates class
 */
use ALS\MailTemplates;
$mailTemplates = new MailTemplates();