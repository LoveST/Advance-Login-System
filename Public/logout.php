<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/1/2018
 * Time: 8:40 AM
 */

use ALS\Message;

class Logout
{
    public function __construct()
    {
        // init the required global variables
        global $message, $session, $functions;

        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        if (!$session->logged_in()) {
            $message->setError("You've never logged in before!", Message::Error);
            header("Location: login.php");
        } else {
            if ($session->logOut()) {
                header("Location: index.php");
            } else {
                $message->setError("Something went wrong while logging out", Message::Fatal);
                header("Location: index.php");
            }
        }
    }
}

new Logout();
