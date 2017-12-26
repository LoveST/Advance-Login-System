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
echo $message->printError(3);

echo $functions->encryptIt($user->get2FactorCode());