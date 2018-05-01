<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/3/2018
 * Time: 8:21 PM
 */

class Profile
{
    public function __construct()
    {
        // define the required global variables
        global $viewController, $database, $functions;

        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        $init = new init();

        // login check
        $init->loginCheck();

        // load the header
        $viewController->loadView("profile_main_panel_header.html");

        // get the required page
        $page = array_key_exists('page', $_GET) ? $_GET['page'] : null;
        $page = $database->secureInput($page);
        // split the text and check if starts with "../" or "./" or "/"
        $error = false;
        if (strpos($page, '../') === 0 || strpos($page, './') === 0 || strpos($page, '/') === 0 || strpos($page, '~') === 0) {
            $error = true;
        }

        // check if page is empty
        if (!empty($page) && $page != "index" && file_exists($page . ".php") && !$error) {

            // load the required page file
            $functions->loadFile($page . ".php");
        } else {
            $functions->loadFile("hello.php");
            // load default view
            $viewController->loadView("admin_main_panel_default.html");
        }

        // load the footer
        $viewController->loadView("profile_main_panel_footer.html");
    }
}

new Profile();

