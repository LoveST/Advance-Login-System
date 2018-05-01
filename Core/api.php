<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 4/24/2017
 * Time: 12:50 PM
 */

namespace ALS\API;


use ALS\Settings\Settings;

class API
{
    private $requested;
    private $running = true;
    private $errMessage = Array(); // to hold the error message that has been thrown (Array)

    /**
     * get the requested data to be used by the client
     * @return mixed
     */
    private function printRequest()
    {

        // check for any errors
        if ($this->anyError()) {
            return $this->getError();
        }

        // return the requested data
        return json_encode(Array('error' => 0, 'request' => $this->requested));
    }

    /**
     * print out the error message if any were to be found
     */
    private function getError()
    {
        return json_encode(Array('error' => 1, 'info' => $this->errMessage));
    }

    /**
     * check if any errors were to be found
     * @return bool
     */
    private function anyError()
    {
        return !empty($this->errMessage);
    }

    /**
     * @param $error
     */
    private function setError($error)
    {
        $this->errMessage = $error;
    }

    function getRespond()
    {

    }

    function getSiteInfo($username, $authenticationKey, $apiKey)
    {

        //define the needed global variables
        global $settings, $database;

        // escape all strings
        $username = $database->escapeString($username);
        $authenticationKey = $database->escapeString($authenticationKey);
        $apiKey = $database->escapeString($apiKey);

        // check for empty username
        if (empty($username)) {
            $this->setError("Api request most be called with a username parameter");
        }

        // check for empty authentication key
        if (empty($authenticationKey)) {
            $this->setError("Api request most be called with a authentication key parameter");
        }

        // check for empty api key
        if (empty($apiKey)) {
            $this->setError("Api request most be called with a apiKey parameter");
        }

        // check for any errors if any found then return the error message
        // without continuing the script
        while ($this->running) {

            // store the requested data
            $this->requested = Array(
                'siteName' => $settings->siteName(),
                'siteURL' => $settings->siteURL(),
                'siteEmail' => $settings->siteEmail(),
                'siteDisabled' => $settings->siteDisabled(),
                'siteTheme' => $settings->siteTheme(),
                'siteLanguage' => $settings->siteLanguage(),
                'secretKey' => $settings->get(Settings::SECRET_KEY),
                'loginEnabled' => $settings->canLogin(),
                'registerEnabled' => $settings->canRegister()
            );

            // stop the loop
            $this->running = false;
        }

        // return the results of the requested data
        return $this->printRequest();

    }

    /**
     * get the site name from the database
     * @return mixed
     */
    function getSiteName()
    {

        //define the needed global variables
        global $settings;

        // check

        if (empty($settings->siteName())) {
            $this->setError("Site name is empty or it has not been initiated.");
        }

        // store the requested data
        $this->requested = Array('siteName' => $settings->siteName());

        // return the results of the requested data
        return $this->printRequest();
    }

}