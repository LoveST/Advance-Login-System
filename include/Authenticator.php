<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 10/11/2017
 * Time: 10:55 PM
 */

namespace ALS;


class Authenticator
{

    public function __construct()
    {

    }

    /**
     * Check if the current user is logged in
     * @return bool
     */
    public function isLoggedIn()
    {
        return true;
    }

}