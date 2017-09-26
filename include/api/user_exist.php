<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/20/2017
 * Time: 8:45 PM
 */

namespace ALS_API;

class user_exist
{

    function __construct($params = null)
    {

        // globals
        global $api, $functions;

        if ($params == null) {
            $api->printError("No Parameters supplied");
        }

        $username = "";

        // check if the supplied parameters is an array or string
        if(is_string($params)){
            $username = $params;
        } else {
            $username = $params['username'];
        }

        // check if the user exists
        if ($functions->userExist($username)){
            $this->printMSG("1");
        } else {
            $this->printMSG("0");
        }
    }

    function printMSG($msg)
    {
        die(json_encode(array("results" => $msg)));
    }

}