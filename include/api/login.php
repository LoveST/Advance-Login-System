<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/20/2017
 * Time: 8:45 PM
 */

namespace ALS\API;

use ALS\User;

class login extends API_DEFAULT
{

    private $user;

    /**
     * user_exist constructor.
     * @param User $user
     * @param null $params
     */
    function __construct($user, $params = null)
    {
        // set the user class
        $this->user = $user;

        // construct the parent class
        parent::__construct();

        // prepare the results
        $this->tryLogin($params);

        // execute the api call
        parent::executeAPI();

    }

    function tryLogin($params)
    {
        global $functions;

        // check if username or password is empty
        if(empty($params['username']) || empty($params['password'])){
            parent::printError("Missing username or password");
        }

        // create the required variables
        $username = $params['username'];
        $password = $params['password'];

        // check if user exist
        if(!$functions->userExist($username)){
            parent::printError("Wrong username or password used.");
        }

        // print the success message
        $token = md5("testtesttest");
        parent::setExecutable(array("token" => $token));
        //parent::printMSG("logged in successfully");

    }
}