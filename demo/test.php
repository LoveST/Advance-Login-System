<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/6/2017
 * Time: 12:27 AM
 */

require "../include/auth/Google.php";

$secret = 'MASISMEMOMASISMEMO';
$time = floor(time() / 30);
$code = "665714";

$g = new \ALS\AUTH\Google\Google();
print "Current Code is: ";
print $g->getCode($secret);
print "<br>";
print "Check if $code is valid: ";

if ($g->checkCode($secret,$code)) {
    print "YES \n<br>";   
} else {
    print "NO \n<br>";
}

$qr = $g->getQRLink("lovemst", "lovemst.com", $secret, "300", "300");
print_r($secret);
print "$qr\n<br>";
echo "<img src=\"$qr\">";