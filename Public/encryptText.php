<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/6/2017
 * Time: 12:01 PM
 */

require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();

// get the encrypt template
require TEMPLATE_PATH ."/encryptText.html";