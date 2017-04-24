<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 1:11 PM
 */
namespace ALS\Settings;
use ALS\Message\Message;
class Settings
{

    private $settings; // store all the database settings (array)
    const SITE_NAME = TBL_SETTINGS_SITE_NAME;
    const SITE_URL = TBL_SETTINGS_SITE_URL;
    const SITE_EMAIL = TBL_SETTINGS_SITE_EMAIL;
    const SITE_ENABLED = TBL_SETTINGS_SITE_ENABLED;
    const SITE_THEME = TBL_SETTINGS_SITE_THEME;
    const SITE_LANGUAGE = TBL_SETTINGS_SITE_LANG;
    const SECRET_KEY = TBL_SETTINGS_SECRET_KEY;
    const LOGIN_ENABLED = TBL_SETTINGS_LOGIN_ENABLE;
    const REGISTER_ENABLED = TBL_SETTINGS_REGISTER_ENABLE;
    const PIN_REQUIRED = TBL_SETTINGS_PIN_REQUIRED;
    const ACTIVATION_REQUIRED = TBL_SETTINGS_ACTIVATION_REQUIRED;
    const MINIMUM_AGE_REQUIRED = TBL_SETTINGS_MINIMUM_AGE_REQUIRED;
    const MINIMUM_AGE = TBL_SETTINGS_MINIMUM_AGE;
    const USERNAME_CHANGE = TBL_SETTINGS_USERNAME_CHANGE;
    const FORCE_HTTPS = TBL_SETTINGS_FORCE_HTTPS;
    const CAPTCHA_KEY = TBL_SETTINGS_CAPTCHA_KEY;
    const CAPTCHA_SECRET_KEY = TBL_SETTINGS_CAPTCHA_SECRET;
    const SAME_IP_LOGIN = TBL_SETTINGS_SAME_IP_LOGIN;

    /**
     * Settings constructor for PHP4
     */
    function User()
    {
        $this->__construct();
    }

    /**
     * Settings constructor for PHP5.
     */
    function __construct()
    {
        $this->init();
    }

    /**
     * initiate the class
     */
    function init()
    {
        $this->callSettings(); // store all the site settings
        $this->checkHTTPS(); // check if HTTPS is enabled
        $this->initRequiredFields(); // define the required fields for the script
    }

    /**
     * call this function using a direct class access to get the needed data from the required array
     * @param $dataType
     * @return mixed
     */
    function get($dataType)
    {
        return $this->settings[$dataType];
    }

    /**
     * Define the required fields for the script to run smoothly
     */
    function initRequiredFields()
    {
        define("TEMPLATE_PATH", "templates/" . $this->get(Settings::SITE_THEME) . "/");
        define("SITE_NAME", $this->get(Settings::SITE_NAME));
    }

    /**
     * call the database and store the settings table with its values in the settings array
     */
    private function callSettings()
    {

        // define all the global variables
        global $database, $message;

        // connect to the database and pull the settings table
        $sql = "SELECT * FROM " . TBL_SETTINGS;

        // try pulling the required data
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->customKill("Fatal Error", "Error while trying to pull the required data from the database.", "default");
        }

        if (mysqli_num_rows($result) < 1) {
            $message->customKill("Fatal Error", "Settings table doesn't contain any values for the script to run.", "default");
        }

        // store the data in the settings array to complete the function
        while ($row = mysqli_fetch_assoc($result)) {

            // store the needed variables
            $fieldName = $row['field'];
            $fieldValue = $row['value'];

            // add the rows to the array
            $this->settings[$fieldName] = $fieldValue;

        }

        // check for the required values that has to be set for the script to run
        // must not be empty fields
        $required = [TBL_SETTINGS_SITE_NAME, TBL_SETTINGS_SITE_URL, TBL_SETTINGS_SITE_EMAIL, TBL_SETTINGS_SECRET_KEY];
        foreach ($required as $requiredField) {
            if ($this->settings[$requiredField] == "" || is_null($this->settings[$requiredField])) {
                $message->customKill("Settings", "The sql field " . $requiredField . " most not be empty", "default");
            }
        }


        // check for empty theme field in the sql and set it to 'default'
        if ($this->settings[TBL_SETTINGS_SITE_THEME] == "") {
            $this->settings[TBL_SETTINGS_SITE_THEME] = "default";
        }

        // check for empty language field in the sql and set it to 'us-eng'
        if ($this->settings[TBL_SETTINGS_SITE_LANG] == "") {
            $this->settings[TBL_SETTINGS_SITE_LANG] = "us-eng";
        }

    }

    function canLogin()
    {
        return $this->settings[TBL_SETTINGS_LOGIN_ENABLE];
    }

    function canRegister()
    {
        return $this->settings[TBL_SETTINGS_REGISTER_ENABLE];
    }

    function siteName()
    {
        return $this->settings[TBL_SETTINGS_SITE_NAME];
    }

    function siteURL()
    {
        return $this->settings[TBL_SETTINGS_SITE_URL];
    }

    function siteEmail()
    {
        return $this->settings[TBL_SETTINGS_SITE_EMAIL];
    }

    function siteDisabled()
    {
        return !$this->settings[TBL_SETTINGS_SITE_ENABLED];
    }

    function siteTheme()
    {
        return $this->settings[TBL_SETTINGS_SITE_THEME];
    }

    function siteLanguage()
    {
        return $this->settings[TBL_SETTINGS_SITE_LANG];
    }

    function secretKey()
    {
        return $this->settings[TBL_SETTINGS_SECRET_KEY];
    }

    function activationRequired()
    {
        return $this->settings[TBL_SETTINGS_ACTIVATION_REQUIRED];
    }

    function pinRequired()
    {
        return $this->settings[TBL_SETTINGS_PIN_REQUIRED];
    }

    function minimumAgeRequired()
    {
        return $this->settings[TBL_SETTINGS_MINIMUM_AGE_REQUIRED];
    }

    function minimumAge()
    {
        return $this->settings[TBL_SETTINGS_MINIMUM_AGE];
    }

    function canChangeUsername()
    {
        return $this->settings[TBL_SETTINGS_USERNAME_CHANGE];
    }

    function isHTTPS()
    {
        return $this->settings[TBL_SETTINGS_FORCE_HTTPS];
    }

    function maxWarnings()
    {
        return $this->settings[TBL_SETTINGS_MAX_WARNINGS];
    }

    function sameIpLogin()
    {
        return $this->settings[TBL_SETTINGS_SAME_IP_LOGIN];
    }

    function maxVerifiedDevices()
    {
        return $this->settings[TBL_SETTINGS_MAX_VERIFIED_DEVICES];
    }

    /**
     * check if https is enabled in the settings
     * @return bool
     */
    function isSecure()
    {
        return
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443;
    }

    function getCurrentPageURL()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * check if https is enabled in the settings and if it is,
     * then redirect to https instead of http and vise versa
     */
    function checkHTTPS()
    {
        // check if force HTTPS is enabled
        if ($this->isHTTPS()) {
            // check if the page is being loaded without encryption
            if (!$this->isSecure()) {
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
                exit;
            }

        }
    }

    /**
     * set the site secret key directly
     * Important : do not ! use this method with the input that the user provides for security purposes
     * @param $hex32
     * @return bool
     */
    function setSiteSecret($hex32)
    {

        // define all the global variables
        global $database, $message;

        // set the query
        $sql = "UPDATE " . TBL_SETTINGS . " SET value = '" . $hex32 . "' WHERE field = '" . TBL_SETTINGS_SECRET_KEY . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Could not update the site secret", Message::Error);
            return false;
        }

        // if no error then return true
        return true;
    }

    /**
     * set the captcha key directly
     * Important : do not ! use this method with the input that the user provides for security purposes
     * @param $key
     * @return boolean
     */
    function setCaptchaKey($key)
    {

        // define all the global variables
        global $database, $message;

        // set the query
        $sql = "UPDATE " . TBL_SETTINGS . " SET value = '" . $key . "' WHERE field = '" . TBL_SETTINGS_CAPTCHA_KEY . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Could not update the captcha key", Message::Error);
            return false;
        }

        // if no error then return true
        return true;
    }

    /**
     * set the captcha secret key directly
     * Important : do not ! use this method with the input that the user provides for security purposes
     * @param $key
     * @return boolean
     */
    function setCaptchaSecretKey($key)
    {

        // define all the global variables
        global $database, $message;

        // set the query
        $sql = "UPDATE " . TBL_SETTINGS . " SET value = '" . $key . "' WHERE field = '" . TBL_SETTINGS_CAPTCHA_SECRET . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Could not update the captcha secret key", Message::Error);
            return false;
        }

        // if no error then return true
        return true;
    }

    /**
     * Update a certain setting in the settings table of the script
     * Important : do not ! use this method with the input that the user provides for security purposes
     * @usage setSetting(Settings::SITE_NAME, "script");
     * @param $setting
     * @param $value
     * @return bool
     */
    function setSetting($setting, $value)
    {

        // define all the global variables
        global $database, $message;

        // set the query
        $sql = "UPDATE " . TBL_SETTINGS . " SET value = '" . $value . "' WHERE field = '" . $setting . "'";
        if (!$result = mysqli_query($database->connection, $sql)) {
            $message->setError("Could not update : " . $setting, Message::Error);
            return false;
        }

        // if no error then return true
        return true;
    }

}