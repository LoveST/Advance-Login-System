<?php

/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 9/13/2017
 * Time: 1:08 AM
 */

namespace ALS;

const _PATH = "include/";

class Core
{

    /**
     * Core constructor.
     */
    function __construct()
    {
        // Start the session
        session_start();

        // import all the required classes
        require _PATH . "config.php";
        require _PATH . "auth/Google.php";
        require _PATH . "message.php";
        require _PATH . "database.php";
        require _PATH . "settings.php";
        require _PATH . "Groups.php";
        require _PATH . "user/device.php";
        require _PATH . "browser.php";
        require _PATH . "captcha.php";
        require _PATH . "user.php";
        require _PATH . "user/devices.php";
        require _PATH . "mail.php";
        require _PATH . "functions.php";
        require _PATH . "passwordManager.php";
        require _PATH . "session.php";
        require _PATH . "administrator.php";
        require _PATH . "profileManager.php";
        require _PATH . "MailTemplates.php";
        require _PATH . "ViewController.php";

    }

    /**
     * Initiate all the functions for the classes
     * that are required to run the script
     */
    final function initClasses()
    {
        // call the function for each class in the script
        $this->_Message();
        $this->_Database();
        $this->_Settings();
        $this->_Groups();
        $this->_ViewController();
        $this->_Translator();
        $this->_Browser();
        $this->_Captcha();
        $this->_Mail();
        $this->_User();
        $this->_PasswordManager();
        $this->_Functions();
        $this->_Session();
        $this->_Administrator();
        $this->_ProfileManager();
        $this->_MailTemplates();

    }

    /**
     * init the Message Class
     */
    function _Message()
    {
        // build the variable to store it
        $message = new Message();
        $GLOBALS['message'] = $message;
        $message->init();
    }

    /**
     * init the Database Class
     */
    function _Database()
    {
        // build the variable to store it
        $GLOBALS['database'] = new Database();
    }

    /**
     * init the Settings Class
     */
    function _Settings()
    {
        // build the variable to store it
        $GLOBALS['settings'] = new Settings();
    }

    /**
     * init the Groups Class
     */
    function _Groups()
    {
        // build the variable to store it
        $GLOBALS['groups'] = new Groups();
    }

    /**
     * init the ViewController Class
     */
    function _ViewController()
    {
        // build the variable to store it
        $GLOBALS['viewController'] = new ViewController();
    }

    /**
     * init the Translator Class
     */
    function _Translator()
    {
        // build the variable to store it
        global $viewController;
        $GLOBALS['translator'] = $viewController->getTranslator();
    }

    /**
     * init the Browser Class
     */
    function _Browser()
    {
        // build the variable to store it
        $GLOBALS['browser'] = new Browser();
    }

    /**
     * init the Captcha Class
     */
    function _Captcha()
    {
        // build the variable to store it
        $GLOBALS['captcha'] = new Captcha();
    }

    /**
     * init the Mail Class
     */
    function _Mail()
    {
        // build the variable to store it
        $GLOBALS['mail'] = new Mail();
    }

    /**
     * init the User Class
     */
    function _User()
    {
        // build the variable to store it
        $GLOBALS['user'] = new User();
    }

    /**
     * init the passwordManager Class
     */
    function _PasswordManager()
    {
        // build the variable to store it
        $GLOBALS['passwordManager'] = new passwordManager();
    }

    /**
     * init the Functions Class
     */
    function _Functions()
    {
        // build the variable to store it
        $GLOBALS['functions'] = new Functions();
    }

    /**
     * init the Session Class
     */
    function _Session()
    {
        // build the variable to store it
        $session = new Session();
        $GLOBALS['session'] = $session;
        $session->init();
    }

    /**
     * init the Administrator Class
     */
    function _Administrator()
    {
        // build the variable to store it
        $GLOBALS['admin'] = new Administrator();
    }

    /**
     * init the profileManager Class
     */
    function _ProfileManager()
    {
        // build the variable to store it
        $GLOBALS['profileManager'] = new profileManager();
    }

    /**
     * init the MailTemplates Class
     */
    function _MailTemplates()
    {
        // build the variable to store it
        $GLOBALS['mailTemplates'] = new MailTemplates();
    }

}