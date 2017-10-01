<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/20/2017
 * Time: 8:45 PM
 */

namespace ALS\API;

class user_exist extends API_DEFAULT
{

    function __construct($params = null)
    {
        // construct the parent class
        parent::__construct();

        // prepare the results
        $this->checkUser($params);

        // execute the api call
        parent::executeAPI();

    }

    /**
     * check if the user exist and push the results to the executable in the
     * parent class to call
     * @param null $params
     */
    function checkUser($params = null)
    {
        // globals
        global $functions;

        // check if params is null
        if ($params == null) {
            parent::printError("No Parameters Supplied");
        }

        $username = "";

        // check if the supplied parameters is an array or string
        if (is_string($params)) {
            $username = $params;
        } else {
            $username = $params['username'];
        }

        // check if the user exists
        if ($functions->userExist($username)) {
            //parent::printMSG(true);
            parent::setExecutable(1);
        } else {
            //parent::printMSG(false);
            parent::setExecutable(-1);
        }
    }
}