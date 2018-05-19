<?php

class Tests1
{

    public function __construct()
    {
        global $functions, $user;
        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        // init the globals
        global $viewController, $message;

        $text = strstr("msg viewController[0]", "[", true);
        echo $text;
        echo "<br><br><br>";

        // print error
        $message->printError();
        $message->printSuccess();
    }

}

new Tests1();
