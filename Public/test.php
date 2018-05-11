<?php

class Tests1
{

    public function __construct()
    {
        global $functions;
        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        // load the view controller
        include_once "test.html";
    }

}

new Tests1();
