<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/3/2018
 * Time: 8:21 PM
 */

use ALS\ALS;

class Profile
{

    var $currentDir = "";

    public function __construct()
    {
        // init the required globals
        global $settings;

        // load the main required init.php file
        include_once FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php";
        $init = new init();
        $this->currentDir = FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . ALS::$_currentDirectory . $settings->getSubLine();

        // login check
        $init->loginCheck();

        // init the required functions
        $this->init();
    }

    public function init()
    {
        // init the required global variables
        global $functions, $database;


        // load the header
        $this->loadView("profile_main_panel_header.html");

        // get the required page
        $page = array_key_exists('page', $_GET) ? $_GET['page'] : null;
        $page = $database->secureInput($page);

        // split the text and check if starts with "../" or "./" or "/"
        $error = false;
        if (strpos($page, '../') === 0 || strpos($page, './') === 0 || strpos($page, '/') === 0 || strpos($page, '~') === 0) {
            $error = true;
        }

        // check if page is empty
        if (!empty($page) && $page != null && $page != "index" && file_exists($this->currentDir . $page . ".php") && !$error) {

            // load the required page file
            $functions->loadFile($this->currentDir . $page . ".php");
        } else {

            // load default view
            $this->loadView("admin_main_panel_default.html");
        }

        // load the footer
        $this->loadView("profile_main_panel_footer.html");
    }

    public function loadView($templateName)
    {
        // init the required global variables
        global $viewController;

        // load the view
        $viewController->loadView($templateName);
    }
}

new Profile();

