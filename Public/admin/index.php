<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:28 PM
 */

class MainAdmin
{

    var $currentDir = "";

    public function __construct()
    {
        // init the required globals
        global $settings;

        // load the main required init.php file
        include_once FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php";
        $init = new init();
        $this->currentDir = FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "admin" . $settings->getSubLine();

        // login check
        $init->loginCheck();

        // init the required functions
        $this->init();
    }

    public function init()
    {
        // init the required global variables
        global $message, $session, $database;

        // check for admin status
        $session->adminCheck();

        // load the header
        $this->loadView("ad_main_panel_header.html");

        // get the required page
        if (isset($_GET['page'])) {
            $page = $database->secureInput($_GET['page']);
        } else {
            $page = "";
        }

        // split the text and check if starts with "../" or "./" or "/"
        $error = false;
        if (strpos($page, '../') === 0 || strpos($page, './') === 0 || strpos($page, '/') === 0 || strpos($page, '~') === 0) {
            $error = true;
        }

        // check if page is empty
        if (!empty($page) && $page != "index" && file_exists($this->currentDir . $page . ".php") && !$error) {

            // load the required page file
            include_once $this->currentDir . $page . ".php";
        } else {

            // load default view
            $this->loadView("admin_main_panel_default.html");
        }

        // load the footer
        $this->loadView("ad_main_panel_footer.html");
    }

    public function loadView($templateName)
    {
        // init the required global variables
        global $viewController;

        // load the view
        $viewController->loadView($templateName);
    }
}

new MainAdmin();