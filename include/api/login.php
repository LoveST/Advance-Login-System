<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/20/2017
 * Time: 8:45 PM
 */

namespace ALS\API;

use ALS\User;
use ALS\USER_API\USER_API;

class login
{

    private $user;
    private $userObj = null;

    /**
     * user_exist constructor.
     * @param User $user
     * @param null $params
     */
    function __construct($user, $params = null)
    {
        // set the user class
        $this->user = $user;

        // create the user session class
        $token = $params['token'];
        $userObj = new USER_API($token);
        $this->userObj = $userObj;

        // prepare the results
        $this->tryLogin($params);

        // execute the api call
        $userObj->executeAPI();

    }

    function tryLogin($params)
    {
        global $functions, $browser, $database, $settings;

        // check if username or password is empty
        if (empty($params['username']) || empty($params['password'])) {
            $this->userObj->printError("Missing username or password");
        }

        // create the required variables
        $username = $database->secureInput($params['username']);
        $password = $database->secureInput($params['password']);

        // check if already logged in
        if ($this->userObj->logged_in()) {
            $this->userObj->printError("already logged in");
            return false;
        }

        // try to log in
        $this->userObj->login($username, $password, $params['appID'], $params['appKey']);
    }
}