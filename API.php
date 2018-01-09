<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/20/2017
 * Time: 6:58 PM
 */

namespace ALS;
require "Core.php";
require "include/api/API_DEFAULT.php"; // call the default api constructor
require "include/api/USER_API.php"; // call the default User api constructor

class API extends Core
{

    private $methods;

    function __construct()
    {

        // construct the parent class and disabled the errors
        parent::__construct(true);
        parent::initClasses();

        // grab the main script functions
        $this->setupMethods();

    }

    private function setupMethods()
    {

        // init the required globals
        global $database;

        // check if the main methods file exists
        $filePath = __DIR__ . $database->getSubLine() . "include" . $database->getSubLine() . "api" . $database->getSubLine() . "methods.ini";
        if (!is_readable($filePath)) {
            $this->printError("Main methods file does not exist");
        }

        // try to parse the file
        if (!$file = parse_ini_file($filePath, true)) {
            $this->printError("Unable to parse the main methods file");
        }

        // store the file content
        $this->methods = $file;
    }

    /**
     * Add a new custom .ini file with more methods inside of it
     * @param string $filePath
     */
    function addNewMethods($filePath)
    {

        // init the required globals
        global $message;

        // check if the main methods file exists
        if (!is_readable($filePath)) {
            $this->printError("Custom methods file does not exist");
        }

        // try to parse the file
        if (!$file = parse_ini_file($filePath, true)) {
            $this->printError("Unable to parse the custom methods file");
        }

        // merge the current methods with the new ones
        $this->methods = array_merge($this->methods, $file);
    }

    /**
     * @param $method
     * @param array $parameters
     * @return string
     */
    function callMethod($method, $parameters = null)
    {
        // init the required globals
        global $message, $database, $translator, $user, $applications;

        // check if method exists
        if ($this->methods[$method] == "") {
            $this->printError("Called method does not exist");
        }

        // create a connection with the database
        $apiUser = new User();
        if ($parameters['key'] != "SELF") {
            if (!$apiUser->initAPIInstance($parameters['key'], $parameters['token'])) {
                $this->printError($message->getError(3));
            }
        } else {

            // check if application id & key are supplied
            $appID = $database->secureInput($parameters['appID']);
            $appKey = $database->secureInput($parameters['appKey']);
            if ($appID == "" || $appKey == "") {
                $this->printError("Missing Application ID or Key");
            }

            // check if application exist by id & key
            if (!$applications->appExist($appID, $appKey)) {
                $this->printError("Wrong Application ID/Key Used");
            }

            // check if application is active
            if (!$applications->appIsActive($appID)) {
                $this->printError("The current application API is offline");
            }
        }

        // get the current method parameters
        $currentMethod = $this->methods[$method];

        // check if the method required a specific file to load
        $sub = $database->getSubLine();

        // check if the main path variable is empty
        if ($currentMethod['__path']['path'] != "") {
            $mainPath = $currentMethod['__path']['path'] . $sub;
        } else {
            $mainPath = __DIR__ . $sub . "include" . $sub . "api" . $sub;
        }

        // check if a sub path exist
        if (!empty($currentMethod['file_path'])) {
            $filePath = $mainPath . $currentMethod['file_path'] . $sub . $currentMethod['file_name'];
        } else {
            $filePath = $mainPath . $currentMethod['file_name'];
        }

        // translate any special characters in the path
        $char = array("_PATH_" => $this->methods['__path']['path'], "_DIR_" => __DIR__, "_SUB_" => $sub, "_SLASH_" => "\\");
        $newFilePath = $translator->replaceTags("%", "%", $filePath, $char);

        // check if the needed method file exist
        //die($newFilePath);
        if (!is_readable($newFilePath)) {
            $this->printError("Method file does not exist");
        }

        // load the class file
        include_once $newFilePath;

        // check if a namespace has been specified
        if ($currentMethod['namespace'] != "") {
            $namespace = $translator->replaceTags("%", "%", $currentMethod['namespace'], $char) . "\\";
        } else {
            $namespace = "ALS\\API\\";
        }

        // check if a class has been specified
        if ($currentMethod['class_name'] != "") {
            $className = $currentMethod['class_name'];
        } else {
            $className = $method;
        }

        // create an object from the connection file
        $class = $namespace . $className;
        new $class($apiUser, $parameters);

        return true;
    }

    function printError($msg)
    {

        // set the array
        $array = array("error" => $msg);

        // die and print the message in JSON format
        die(json_encode($array));
    }

}

$api = new API();