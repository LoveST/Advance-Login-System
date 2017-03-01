<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 1:11 PM
 */
class Settings{

    private $message; // instance of the Message class.
    private $database; // instance of the Database class.
    private $settings; // store all the database settings (array)
    const Login_Enabled = TBL_SETTINGS_LOGIN_ENABLE;
    const SITE_NAME = TBL_SETTINGS_SITE_NAME;
    const SITE_URL = TBL_SETTINGS_SITE_URL;
    const SITE_EMAIL = TBL_SETTINGS_SITE_EMAIL;
    const SITE_ENABLED = TBL_SETTINGS_SITE_ENABLED;
    const SITE_THEME = TBL_SETTINGS_SITE_THEME;
    const SECRET_CODE = TBL_SETTINGS_SECRET_KEY;

    /**
     * Settings constructor for PHP4
     */
    function User(){
        $this->__construct();
    }

    /**
     * Settings constructor for PHP5.
     */
    function __construct(){

    }

    /**
     * initiate the class
     * @param $message
     * @param $database
     */
    function init($message, $database){
        $this->database = $database;
        $this->message = $message;
        $this->callSettings();
    }

    /**
     * call this function using a direct class access to get the needed data from the required array
     * @param $dataType
     * @return mixed
     */
    function get($dataType){
        return $this->settings[$dataType];
    }

    /**
     * call the database and store the settings table with its values in the settings array
     */
    private function callSettings(){
        // connect to the database and pull the settings table
        $sql = "SELECT * FROM ". TBL_SETTINGS;

        // try pulling the required data
        if(!$result = mysqli_query($this->database->connection,$sql)){
            $this->message->setError("Error while trying to pull the required data from the database." , Message::Fatal, __FILE__ , __LINE__ - 1);
        }

        if(mysqli_num_rows($result) <1){
            $this->message->setError("Settings table doesn't contain any values for the script to run." , Message::Fatal, __FILE__ , __LINE__ - 1);
        }

        // must not be empty fields
        $required = [TBL_SETTINGS_SITE_NAME,TBL_SETTINGS_SITE_URL,TBL_SETTINGS_SITE_EMAIL,TBL_SETTINGS_SECRET_KEY];

        // store the data in the settings array to complete the function
        while($row = mysqli_fetch_assoc($result)){
            foreach($row as $key => $value){
                // check if the field is required and if its empty
                if(in_array($key,$required) && empty($value)){
                    $this->message->customKill("Settings", "The sql field " . $key . " most not be empty", "default");
                }

                // check for empty theme field in the sql and set it to 'default'
                if($key == TBL_SETTINGS_SITE_THEME && empty($value)){
                    $this->settings[$key] = "default";
                    continue;
                }
                // check for empty language field in the sql and set it to 'us-eng'
                if($key == TBL_SETTINGS_SITE_LANG && empty($value)){
                    $this->settings[$key] = "us-eng";
                    continue;
                }
                $this->settings[$key] = $value;
            }
        }
    }

    function canLogin(){
        return $this->settings[TBL_SETTINGS_LOGIN_ENABLE];
    }

    function canRegister(){
        return $this->settings[TBL_SETTINGS_REGISTER_ENABLE];
    }

    function siteName(){
        return $this->settings[TBL_SETTINGS_SITE_NAME];
    }

    function siteURL(){
        return $this->settings[TBL_SETTINGS_SITE_URL];
    }

    function siteEmail(){
        return $this->settings[TBL_SETTINGS_SITE_EMAIL];
    }

    function siteDisabled(){
        return !$this->settings[TBL_SETTINGS_SITE_ENABLED];
    }

    function siteTheme(){
        return $this->settings[TBL_SETTINGS_SITE_THEME];
    }

    function siteLanguage(){
       return $this->settings[TBL_SETTINGS_SITE_LANG];
    }

    function secretKey(){
        return $this->settings[TBL_SETTINGS_SECRET_KEY];
    }

    function activationRequired(){
        return $this->settings[TBL_SETTINGS_ACTIVATION_REQUIRED];
    }

    function pinRequired(){
        return $this->settings[TBL_SETTINGS_PIN_REQUIRED];
    }

    function minimumAgeRequired(){
        return $this->settings[TBL_SETTINGS_MINIMUM_AGE_REQUIRED];
    }

    function minimumAge(){
        return $this->settings[TBL_SETTINGS_MINIMUM_AGE];
    }

    function canChangeUsername(){
        return $this->settings[TBL_SETTINGS_USERNAME_CHANGE];
    }

    function isHTTPS(){
        return $this->settings[TBL_SETTINGS_FORCE_HTTPS];
    }

    function maxWarnings(){
        return $this->settings[TBL_SETTINGS_MAX_WARNINGS];
    }

    function checkHTTPS(){
        // check if force HTTPS is enabled
        if($this->isHTTPS()){
            // check if the page is being loaded without encryption
            if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on"){
                header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
                exit();
            }
        } else {
            // check if the page is being loaded with encryption
            if(!empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] == "on"){
                header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
                exit();
            }
        }
    }

}