<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/11/2018
 * Time: 8:19 PM
 */

namespace ALS\API;

use ALS\User;
use ALS\USER_API\USER_API;

class check_status
{

    private $user;
    private $userObj = null;

    /**
     * check_status constructor.
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

        // execute the api call
        $this->userObj->checkStatus($token, $params['appID'], $params['appKey']);

        // execute
        $this->userObj->executeAPI();

    }

}