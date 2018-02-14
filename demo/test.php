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

foreach ($groups->listGroups() as $group) {
    echo "Group name : " . $group->getName() . "<br>";
}