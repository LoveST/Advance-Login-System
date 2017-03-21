<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/20/2017
 * Time: 12:30 AM
 */
class Captcha {

    private $database; // instance of the Database class.
    private $settings; // instance of the settings class.
    private $siteKey; // site captcha key
    private $secretKey; // the secret code for the captcha
    private $respondArray; // store the incoming data from google re-captcha

    /**
     * Captcha constructor.
     */
    function __construct(){
        $this->init();
    }

    /**
     * init the class
     */
    private function init(){

        // define all the global variables
        global $database, $settings;

        $this->database = $database;
        $this->settings = $settings;

        // define the used codes for the captcha
        $this->siteKey = $this->settings->get(TBL_SETTINGS_CAPTCHA_KEY);
        $this->secretKey = $this->settings->get(TBL_SETTINGS_CAPTCHA_SECRET);
    }

    /**
     * Send the requested captcha to google servers and check for responds
     * @param $userRespond
     */
    function sendRequest($userRespond){

        // get the user ip
        $ip = $_SERVER['REMOTE_ADDR'];
        // send the request to google and store the incoming data
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $this->secretKey . "&response=" . $userRespond . "&remoteip=" . $ip);
        $responseKeys = json_decode($response, true);
        // store all the incoming data in respondArray
        $this->respondArray = $responseKeys;
    }

    /**
     * Check if the user has passed the captcha check
     * @return boolean
     */
    function success(){
        return $this->respondArray["success"];
    }

    /**
     * Get the site key to use in templates
     * @return string
     */
    function getSiteKey(){
        return $this->siteKey;
    }

    /**
     * Get the site secret key to authenticate with google servers
     * @return string
     */
    function getSecretKey(){
        return $this->secretKey;
    }

}