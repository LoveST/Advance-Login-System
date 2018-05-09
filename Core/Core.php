<?php

/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 9/13/2017
 * Time: 1:08 AM
 */

namespace ALS;

use ALS\AUTH\Google\Google;

error_reporting(ERROR_REPORTING);

class Core
{
    private $dieIfError = true;

    /**
     * Core constructor.
     * @param bool $dieIfError
     */
    function __construct($dieIfError = true)
    {
        // Start the session
        session_start();

        // import all the required classes
        $this->_GoogleAuthenticator();
        $this->_Message();
        $this->_Database();
        $this->_Settings();
        $this->_Links();
        $this->_Groups();
        $this->_Translator();
        $this->_ViewController();
        $this->_Browser();
        $this->_Captcha();
        $this->_Mail();
        $this->_MailTemplates();
        $this->_User();
        $this->_PasswordManager();
        $this->_Functions();
        $this->_Session();
        $this->_Administrator();
        $this->_ProfileManager();
        $this->_Authenticator();
        $this->_Applications();
        $this->_Avatars();

        // set the error handler
        $this->_ErrorKiller($dieIfError);

    }

    function _ErrorKiller($dieIfError)
    {
        $this->dieIfError = $dieIfError;
    }

    /**
     * Initiate all the functions for the classes
     * that are required to run the script
     */
    final function initClasses()
    {


    }

    function _GoogleAuthenticator()
    {
        // load the required class file
        require "auth/Google.php";

        // check if defined
        if (!isset($googleAuth)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['googleAuth'] = new Google();
    }

    /**
     * init the Message Class
     */
    function _Message()
    {
        // load the required class file
        require "message.php";

        // check if defined
        if (!isset($message)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS["message"] = $message;
        $message->init();
    }

    /**
     * init the Database Class
     * @var Database $database
     */
    function _Database()
    {
        // load the required class file
        require "database.php";

        // check if defined
        if (!isset($database)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $database->_init($this->dieIfError);
        $GLOBALS['database'] = $database;
        $database->connect();
    }

    /**
     * init the Settings Class
     */
    function _Settings()
    {
        // load the required class file
        require "settings.php";

        // check if defined
        if (!isset($settings)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['settings'] = $settings;
        $settings->_init();
    }

    /**
     * init the Links Class
     */
    function _Links()
    {
        // load the required class file
        require "Links.php";

        // check if defined
        if (!isset($links)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['links'] = $links;
    }

    /**
     * init the Groups Class
     */
    function _Groups()
    {
        // load the required class file
        require "Groups.php";

        // check if defined
        if (!isset($groups)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['groups'] = $groups;
    }

    /**
     * init the ViewController Class
     */
    function _ViewController()
    {
        // load the required class file
        require "viewController.php";

        // check if defined
        if (!isset($viewController)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['viewController'] = $viewController;
        $viewController->_init();
    }

    /**
     * init the Translator Class
     */
    function _Translator()
    {
        // load the required class file
        require "Translator.php";

        // check if defined
        if (!isset($translator)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['translator'] = $translator;
    }

    /**
     * init the Browser Class
     */
    function _Browser()
    {
        // load the required class file
        require "Browser.php";

        // check if defined
        if (!isset($browser)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['browser'] = $browser;
    }

    /**
     * init the Captcha Class
     */
    function _Captcha()
    {
        // load the required class file
        require "Captcha.php";

        // check if defined
        if (!isset($captcha)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['captcha'] = $captcha;
    }

    /**
     * init the Mail Class
     */
    function _Mail()
    {
        // load the required class file
        require "Mail.php";

        // check if defined
        if (!isset($mail)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['mail'] = $mail;
    }

    /**
     * init the User Class
     */
    function _User()
    {
        // load the required class file
        require "user/device.php";
        require "user/devices.php";

        // check if defined
        if (!isset($devices)) die("Undefined variables at line " . __LINE__);
        $GLOBALS['devices'] = $devices;

        // load the User class
        require "User.php";

        // check if defined
        if (!isset($user)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['user'] = $user;
    }

    /**
     * init the passwordManager Class
     */
    function _PasswordManager()
    {
        // load the required class file
        require "passwordManager.php";

        // check if defined
        if (!isset($passwordManager)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['passwordManager'] = $passwordManager;
    }

    /**
     * init the Functions Class
     */
    function _Functions()
    {
        // load the required class file
        require "Functions.php";

        // check if defined
        if (!isset($functions)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['functions'] = $functions;
    }

    /**
     * init the Session Class
     */
    function _Session()
    {
        // load the required class file
        require "Session.php";

        // check if defined
        if (!isset($session)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['session'] = $session;
        $session->init();
    }

    /**
     * init the Administrator Class
     */
    function _Administrator()
    {
        // load the required class file
        require "administrator.php";

        // check if defined
        if (!isset($admin)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['admin'] = $admin;
    }

    /**
     * init the profileManager Class
     */
    function _ProfileManager()
    {
        // load the required class file
        require "profileManager.php";

        // check if defined
        if (!isset($profileManager)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['profileManager'] = $profileManager;
    }

    function _Authenticator()
    {
        // load the required class file
        require "Authenticator.php";

        // check if defined
        if (!isset($authenticator)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['authenticator'] = $authenticator;
    }

    function _Applications()
    {
        // load the required class file
        require "Applications.php";

        // check if defined
        if (!isset($applications)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['applications'] = $applications;
    }

    /**
     * init the MailTemplates Class
     */
    function _MailTemplates()
    {
        // load the required class file
        require "MailTemplates.php";

        // check if defined
        if (!isset($mailTemplates)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['mailTemplates'] = $mailTemplates;
    }

    /**
     * init the Avatars Class
     */
    function _Avatars()
    {
        // load the required class file
        require "Avatars.php";

        // check if defined
        if (!isset($avatars)) die("Undefined variables at line " . __LINE__);

        // store the variable and initiate the class
        $GLOBALS['avatars'] = $avatars;
    }

}