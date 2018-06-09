<?php

class Tests1
{

    public function __construct()
    {
        global $functions, $user, $statistics;
        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        $init = new init();

        // check for logged in user
        $init->loginCheck();

        echo $user->get("email") . "<br>";
        $time = date("Y-m-d H:i:s", // this line is for demonstration
            mktime(0, 0, 0));
        echo strtotime($time);
        echo "<br>" . $statistics->getTotalLoggedUsers(60 * 5); // 5 minutes
    }

}

new Tests1();
