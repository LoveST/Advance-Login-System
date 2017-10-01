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
        global $translator, $database;

        // check if method exists
        if ($this->methods[$method] == "") {
            $this->printError("Called method does not exist");
        }

        // get the current method parameters
        $currentMethod = $this->methods[$method];

        // check if the method required a specific file to load
        $newFilePath = "";
        if ($currentMethod['file_path'] != "" && $currentMethod['file_path'] != null) {
            // check for a custom file path
            // replace the path index & the sub line index
            if ($this->methods['__path']['path'] != "") {
                $dir = $this->methods['__path']['path'];
            }
            $path = $dir . $currentMethod['file_path'];
            $newFilePath = $translator->replaceTags("%", "%", $path, array("_PATH_ => $this->methods['__path']['path']", "_DIR_" => __DIR__, "_SUB_" => $database->getSubLine()));
        } else {
            $newFilePath = __DIR__ . $database->getSubLine() . "include" . $database->getSubLine() . "api" . $database->getSubLine();
        }

        // check if the needed method file exist
        if (!is_readable($newFilePath . $currentMethod['file_name'])) {
            $this->printError("Method file does not exist");
        }

        // load the class file
        include_once $newFilePath . $currentMethod['file_name'];

        // create an object from the connection file
        $class = "ALS\\API\\" . $method;
        $object = new $class($parameters);

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