<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 3/1/2018
 * Time: 6:24 PM
 */

namespace ALS;

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class Avatars
{

    public function __construct()
    {

    }

    function printAvatar($user)
    {

    }

    function getUserAvatar()
    {
        // define global variables
        global $settings, $user;

        $type = 'image/jpeg';
        $path = $settings->getAvatarsPath() . "1.jpg";

        $contents = file_get_contents($path);
        $base64 = base64_encode($contents);

        // fix the image rotation

        return ('data:' . $type . ';base64,' . $base64);
    }

}