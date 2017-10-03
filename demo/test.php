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
//$groups->addPermission("als_test", "user");
var_dump($user->hasPermission("als_SELF(USER)_checkUser"));
echo $message->printError(3);
echo $message->getSuccess();