<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/27/2017
 * Time: 1:19 PM
 */

error_reporting(1);
session_start();

$title = $_SESSION['err_title'];
$msg = $_SESSION['err_msg'];
$theme = $_SESSION['theme_url'];

if($theme == "" || $msg == "" || $title == ""){
    echo "empty string";
    die;
}

require "templates/". $theme ."/error.html";
unset($_SESSION['err_msg']);
unset($_SESSION['err_title']);
unset($_SESSION['theme_url']);