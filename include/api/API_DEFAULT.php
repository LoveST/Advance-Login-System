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
    private $successMSG = null;
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

        // check if error message is null
        if ($this->errorMSG == null) {
            $error = "";
        } else {
            $error = $this->errorMSG;
        }

        // check if success message is null

        // setup the returning results
        $errorArray = array('error' => $error);
        $this->object = array_merge($this->object, $errorArray);

        // return the results array
        die(json_encode($this->object));
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
        if (empty($this->errorMSG) || $this->errorMSG == null) {
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
        if ($this->errorMSG == null) {
            return "";
        }
        
        return $this->errorMSG;
    }

    /**
     *
     * @return null|string
     */
    final function getSuccessMSG()
    {
        if ($this->successMSG == null) {
            return "";
        }

        return $this->successMSG;
    }

    /**
     * @param $msg
     */
    final function setSuccessMSG($msg)
    {
        $this->successMSG = $msg;
    }

}