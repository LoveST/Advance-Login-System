<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/6/2017
 * Time: 12:39 PM
 */

class decryptText
{
    public function __construct()
    {
        // define the required global variables
        global $functions;

        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        $init = new init();

        // login check
        $init->loginCheck();

        // load view
        $this->loadView();
    }

    public function loadView()
    {
        // init the required global variables
        global $viewController;

        // load the view
        $viewController->loadView("decryptText.html");
    }
}

new decryptText();