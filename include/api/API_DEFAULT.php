<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 10/1/2017
 * Time: 2:09 AM
 */

namespace ALS\API;


class API_DEFAULT
{

    private $errorMSG = null;
    private $object = null; // hold the returning results of the api

    public function __construct()
    {
    }

    /**
     * Execute the results of the object variable
     * and display it
     */
    public function executeAPI()
    {
        // check for any errors before executing
        if ($this->anyError()) {
            $this->printMSG($this->getError());
        }

        if ($this->object == null) {
            $this->printError("Nothing to do here !");
        }

        // encode and print out the object variable
        $this->printMSG(json_encode($this->object));

    }

    /**
     * Set the returning object variable that will be printed out
     * by calling the default method executeAPI()
     * @param $results
     */
    final public function setExecutable($results)
    {
        $this->object = $results;
    }

    /**
     * kill the script with a custom message encoded as a JSON text
     * @param string $msg
     */
    function printMSG($msg)
    {
        die(json_encode(array("results" => $msg)));
    }

    /**
     * print an error message to the user
     * @param null|string $msg
     */
    final function printError($msg = null)
    {
        if ($msg == null) {
            die(json_encode(array("error" => $this->getError())));
        } else {
            die(json_encode(array("error" => $msg)));
        }
    }

    /**
     * Check if any error message has been set and or its null
     * @return bool
     */
    final function anyError()
    {
        if (empty($this->errorMSG)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * get the api call error message
     * @return null|string
     */
    final function getError()
    {
        return $this->errorMSG;
    }

}