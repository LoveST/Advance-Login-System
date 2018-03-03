<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 1:11 PM
 */

namespace ALS;

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class Settings
{

    private $settings; // store all the database settings (array)
    const SITE_NAME = TBL_SETTINGS_SITE_NAME;
    const SITE_URL = TBL_SETTINGS_SITE_URL;
    const SITE_PATH = TBL_SETTINGS_SITE_PATH;
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
    const TIMESTAMP = TBL_SETTINGS_LOADING_TIMESTAMP;
    const TEMPLATES_FOLDER = TBL_SETTINGS_TEMPLATES_FOLDER;
    const TIMEZONE = TBL_SETTINGS_SITE_TIMEZONE;

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
        $this->initTimeStamp(); // check is loading timestamp is enabled
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
        define("TEMPLATE_PATH", "templates/" . $this->siteTheme() . "/");
        define("SITE_NAME", $this->siteName());
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
        $results = $database->getQueryResults($sql);
        if ($database->anyError()) {
            $message->customKill("Fatal Error", "Error while trying to pull the required data from the database.", "default");
        }

        if ($database->getQueryNumRows($results, true) < 1) {
            $message->customKill("Fatal Error", "Settings table doesn't contain any values for the script to run.", "default");
        }

        // store the data in the settings array to complete the function
        foreach ($database->getQueryEffectedRows($results, true) as $row) {

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

        // set the site timezone
        $this->setSiteTimeZone();

        // check for empty theme field in the sql and set it to 'default'
        if ($this->settings[TBL_SETTINGS_SITE_THEME] == "") {
            $this->settings[TBL_SETTINGS_SITE_THEME] = "default";
        }

        // check for empty language field in the sql and set it to 'us-eng'
        if ($this->settings[TBL_SETTINGS_SITE_LANG] == "") {
            $this->settings[TBL_SETTINGS_SITE_LANG] = "us-eng";
        }

    }

    /**
     * Initiate the start of the functions while loading everything else
     * @return bool|float
     */
    function initTimeStamp()
    {
        // check if timestamp is been enabled
        if (!$this->siteLoadingTimestamp()) {
            return false;
        }

        static $start;

        if (is_null($start)) {
            $start = microtime(true);
        } else {
            $diff = round((microtime(true) - $start), 4);
            $start = null;
            return $diff;
        }
    }

    /**
     * Check if login's are enabled on the server
     * @return bool
     */
    function canLogin()
    {
        return $this->settings[TBL_SETTINGS_LOGIN_ENABLE];
    }

    /**
     * Enable/Disable user registration
     * @return bool
     */
    function canRegister()
    {
        return $this->settings[TBL_SETTINGS_REGISTER_ENABLE];
    }

    /**
     * get the script name
     * @return string
     */
    function siteName()
    {
        return $this->settings[TBL_SETTINGS_SITE_NAME];
    }

    /**
     * get the site url, ending with "/"
     * @return string
     */
    function siteURL()
    {
        return $this->settings[TBL_SETTINGS_SITE_URL];
    }

    /**
     * get the site path
     * @return string
     */
    function sitePath()
    {
        return $this->settings[TBL_SETTINGS_SITE_PATH];
    }

    /**
     * Get the script main e-mail address
     * @return string
     */
    function siteEmail()
    {
        return $this->settings[TBL_SETTINGS_SITE_EMAIL];
    }

    /**
     * Check if the script is disabled
     * @return bool
     */
    function siteDisabled()
    {
        return !$this->settings[TBL_SETTINGS_SITE_ENABLED];
    }

    /**
     * Get the script theme name
     * @return string
     */
    function siteTheme()
    {
        return $this->settings[TBL_SETTINGS_SITE_THEME];
    }

    /**
     * Get the script default used language
     * @return string
     */
    function siteLanguage()
    {
        return $this->settings[TBL_SETTINGS_SITE_LANG];
    }

    /**
     * Get the script timezone
     * @return string
     */
    function siteTimeZone()
    {
        return $this->settings[TBL_SETTINGS_SITE_TIMEZONE];
    }

    /**
     * Get the script secret key
     * IMPORTANT: DO NOT !! print the value of the secret anywhere
     * @return string
     */
    function secretKey()
    {
        return SITE_SECRET;
    }

    /**
     * Enable/Disable activation for new users
     * @return bool
     */
    function activationRequired()
    {
        return $this->settings[TBL_SETTINGS_ACTIVATION_REQUIRED];
    }

    /**
     * Check if pin is required along the script files and
     * there functions
     * @return bool
     */
    function pinRequired()
    {
        return $this->settings[TBL_SETTINGS_PIN_REQUIRED];
    }

    /**
     * Enable/Disable age restrictions
     * @return bool
     */
    function minimumAgeRequired()
    {
        return $this->settings[TBL_SETTINGS_MINIMUM_AGE_REQUIRED];
    }

    /**
     * Check the minimum age required for a user to be before
     * signing up on the site
     * @return int
     */
    function minimumAge()
    {
        return $this->settings[TBL_SETTINGS_MINIMUM_AGE];
    }

    /**
     * Check if the user can change his username
     * @return int
     */
    function canChangeUsername()
    {
        return $this->settings[TBL_SETTINGS_USERNAME_CHANGE];
    }

    /**
     * Check if HTTPS is enabled on the script
     * @return bool
     */
    function isHTTPS()
    {
        return $this->settings[TBL_SETTINGS_FORCE_HTTPS];
    }

    /**
     * Get the total number or warnings that a user can get
     * before getting his account locked for X amount of time
     * * TO-DO
     * @return int
     */
    function maxWarnings()
    {
        return $this->settings[TBL_SETTINGS_MAX_WARNINGS];
    }

    /**
     * Allow/Deny the user to login in case there was any
     * different IP address that's been used
     * @return bool
     */
    function sameIpLogin()
    {
        return $this->settings[TBL_SETTINGS_SAME_IP_LOGIN];
    }

    /**
     * Get the total allowed verified devices for each user
     * @return int
     */
    function maxVerifiedDevices()
    {
        return $this->settings[TBL_SETTINGS_MAX_VERIFIED_DEVICES];
    }

    function twilioAccountSid()
    {
        return $this->settings[TBL_SETTINGS_TWILIO_ACCOUNT_SID];
    }

    function twilioAuthToken()
    {
        return $this->settings[TBL_SETTINGS_TWILIO_AUTH_TOKEN];
    }

    function twilioPhoneNumber()
    {
        return $this->settings[TBL_SETTINGS_TWILIO_PHONE_NUMBER];
    }

    /**
     * get the site templates folder name
     * @return string
     */
    function templatesFolder()
    {
        return $this->settings[TBL_SETTINGS_TEMPLATES_FOLDER];
    }

    /**
     * Check if the site timestamp is enabled
     * @return bool
     */
    function siteLoadingTimestamp()
    {
        return $this->settings[TBL_SETTINGS_LOADING_TIMESTAMP];
    }

    /**
     * Get the required max length for a pin number
     * @return int
     */
    function maxRequiredPinLength()
    {
        return $this->settings[TBL_SETTINGS_MAX_REQUIRED_PIN_LENGTH];
    }

    /**
     * Get the maximum required password length
     * @return int
     */
    function maxPasswordLength()
    {
        return $this->settings[TBL_SETTINGS_MAX_PASSWORD_LENGTH];
    }

    /**
     * Get the minimum required password length
     * @return int
     */
    function minPasswordLength()
    {
        return $this->settings[TBL_SETTINGS_MIN_PASSWORD_LENGTH];
    }

    /**
     * Check if a certain group is a default one
     * @return bool
     */
    function isDefaultGroup()
    {
        return $this->settings[TBL_LEVELS_DEFAULT_GROUP];
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

    /**
     * Set the whole site time zone
     */
    private function setSiteTimeZone()
    {
        if ($this->siteTimeZone() == "") {
            date_default_timezone_set('America/Los_Angeles');
        } else {
            date_default_timezone_set($this->siteTimeZone());
        }
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
     * get the site templates cache folder path
     */
    function getTemplatesCachePath()
    {

        $sub = "";

        // check the servers current OS
        if (PHP_OS == "Linux") {
            $sub = "/";
        } else {
            $sub = "\\";
        }

        // start building the path
        $path = $this->sitePath() . $sub . $this->templatesFolder() . $sub . $this->siteTheme() . $sub . "cache" . $sub;

        // return the path
        return $path;
    }

    /**
     * get the site templates path
     * @return string
     */
    function getTemplatesPath()
    {

        $sub = "";

        // check the servers current OS
        if (PHP_OS == "Linux") {
            $sub = "/";
        } else {
            $sub = "\\";
        }

        // start building the path
        $path = $this->sitePath() . $sub . $this->templatesFolder() . $sub . $this->siteTheme() . $sub;

        // return the path
        return $path;
    }

    /**
     * get the site templates url
     * @return string
     */
    function getTemplatesURL()
    {

        // start building the path
        $path = "http://" . $this->siteURL() . $this->templatesFolder() . "/" . $this->siteTheme() . "/";

        // return the path
        return $path;
    }

    /**
     * Get the default avatars folder path
     * @return string
     */
    function getAvatarsPath()
    {
        return $this->settings[TBL_SETTINGS_AVATARS_PATH];
    }

    /**
     * Get the default avatars folder url
     * @return string
     */
    function getAvatarsURL()
    {
        return $this->settings[TBL_SETTINGS_AVATARS_URL];
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
        $database->getQueryResults($sql);
        if ($database->anyError()) {
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
        $database->getQueryResults($sql);
        if ($database->anyError()) {
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
        $database->getQueryResults($sql);
        if ($database->anyError()) {
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
        $database->getQueryResults($sql);
        if ($database->anyError()) {
            $message->setError("Could not update : " . $setting, Message::Error);
            return false;
        }

        // if no error then return true
        return true;
    }

    /**
     * get the required sub line for the current server's os
     * @return string
     */
    function getSubLine()
    {
        $sub = "";

        // check the servers current OS
        if (PHP_OS == "Linux") {
            $sub = "/";
        } else {
            $sub = "\\";
        }

        return $sub;
    }

}