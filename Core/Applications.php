<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 12/25/2017
 * Time: 3:52 PM
 */

namespace ALS;


class Applications
{

    public function __construct()
    {

    }

    public function appExist($id, $key)
    {
        return true;
    }

    public function appExistByID($id)
    {
        return true;
    }

    public function appExistByKey($key)
    {
        return true;
    }

    public function appIsActive($id)
    {
        return true;
    }

}

$applications = new Applications();