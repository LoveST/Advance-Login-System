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

$url = urlencode($settings->siteURL() . "login.php?token=287563856893568353204&key=29837495f");
$ur = urldecode($_GET['url']);
die($functions->getCurrentPageURL());

//sleep(1);
$functions->redirect("www.facebook.com");
header("Location: " . $url, true, false);

exit();
/**
 * $data[]
 *
 * $authenticateUser();
 *
 *
 */