<?php

class Tests1
{

    public function __construct()
    {
        global $functions;
        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        // init the globals
        global $groups, $message;

        // update records
        var_dump($groups->loadGroup("user")->removePermission(array("test1", "test3")));

        echo "<br><br><br>";

        // print error
        $message->printError();
        $message->printSuccess();
    }

}

new Tests1();
