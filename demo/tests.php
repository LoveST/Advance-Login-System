<?php
include "../Core.php";

$core = new \ALS\Core();
$core->initClasses();
$string = urlencode(htmlentities("localhost/als/demo/apiTest.php?method=user_exist&param=lovemst"));
$link = urlencode(htmlentities(INPUT_GET, "redirect", FILTER_SANITIZE_STRING));

echo urldecode($link);