<?php
require "../init.php";

$secret = "65be31bdc03aa73140d74020cd011d04";
$secret = $user->generateUniqueSecret();
//print $googleAuth->getCode($secret);

echo "<img src='".$googleAuth->getUrl($user->getUsername(), $settings->siteName(), $secret)."'>";