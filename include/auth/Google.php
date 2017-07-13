<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 7/10/2017
 * Time: 7:52 PM
 */

namespace ALS\AUTH\Google;

require "google/GoogleAuthenticator.php";

class Google extends \GoogleAuthenticator
{

    public function getQRLink($username, $hostname, $secretKey, $width, $height)
    {
        $url = 'https://chart.googleapis.com/chart?cht=qr&chs='.$width.'x'.$height.'&chl=';

        $qrCode = 'otpauth://totp/'.$username.'@'.$hostname.'?secret='.$secretKey;

        $url = $url.$qrCode;

        return $url;
    }

}

$googleAuth = new Google();