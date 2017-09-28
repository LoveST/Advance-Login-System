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
var_dump($user->hasPermission("analytics_countRegisteredUsersInBetween"));
//var_dump($user->getGroup()->getPermissions());

echo "<br>" . $message->getError(3);
echo "<br>" . $message->getSuccess();
