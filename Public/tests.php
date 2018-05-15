<?php

class Tests
{

    public function __construct()
    {
        global $functions;
        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

    }

}

new Tests();
