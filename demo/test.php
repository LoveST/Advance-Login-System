<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/6/2017
 * Time: 12:27 AM
 */

require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();

// generate a uuid for the user token for API requests
$u = new \ALS\User();
$u->initUserRestAPI(1);

echo $u->getUsername();