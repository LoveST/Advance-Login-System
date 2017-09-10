<?php
require "../init.php";

//var_dump($_SESSION["user_data"]);

$sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_TOKEN . "='1' WHERE " . TBL_USERS_USERNAME . " = 'masis96'";
var_dump( $database->anyError());