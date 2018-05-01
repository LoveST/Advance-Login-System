<?

use ALS\LoginStatus;

class Login
{

    public function __construct()
    {
        // init the required global variables
        global $session, $functions;

        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        // check if already logged in
        if ($session->statusCheck() == LoginStatus::GoodToGo) {
            header("Location: index.php");
        }

        // start the process of logging in
        $this->init();
    }

    public function init()
    {
        // init the required global variables
        global $session, $viewController, $functions, $database, $settings, $profileManager;

        // get the current _GET variable
        $page = array_key_exists('ac', $_GET) ? $_GET['ac'] : null;
        $page = $database->secureInput($page);

        // check for each case
        switch ($page) {
            case "activate";

                if (isset($_POST['activate'])) {
                    if ($session->activateAccount($_POST['code'], $_POST['email'])) {
                        $success = true;
                    }
                }

                $viewController->loadView("activate-account.html");
                break;
            case "verifyPin";

                if ($session->statusCheck() == LoginStatus::VerifyDevice) {
                    if (isset($_POST["pin"])) {

                        // grab the post
                        $pin = $_POST['pin'];

                        // submit for check
                        if ($session->verifyDevice($pin)) {
                            $functions->directBackToSource("index.php");
                        }
                    }

                    // load the needed template
                    $viewController->loadView("verify_device.html");
                } else {
                    $functions->directBackToSource("index.php");
                }

                break;
            case  "googleAuthenticate";

                // insert custom scripts
                $viewController->addCustomScript(' <script src="' . $settings->getTemplatesURL() . 'plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>');
                $viewController->addCustomScript('<script src="' . $settings->getTemplatesURL() . 'plugins/autoNumeric/autoNumeric.js" type="text/javascript"></script>');
                $viewController->addCustomScript('<script type="text/javascript">jQuery(function($) {$(\'.autonumber\').autoNumeric(\'init\');});</script>');

                if ($session->statusCheck() == LoginStatus::AuthenticationNeeded) {
                    if (isset($_POST["authCode"])) {

                        // grab the post
                        $authCode = $_POST['authCode'];

                        // submit for check
                        if ($session->authenticateUser($authCode)) {
                            $functions->directBackToSource("index.php");
                        }
                    }

                    // load the needed template
                    $viewController->loadView("authentication.html");
                } else {
                    $functions->directBackToSource("index.php");
                }

                break;
            case "loginWithEmail";

                // check if form posted
                if (isset($_POST['login'])) {

                    // get the required fields
                    $email = $_POST['email'];
                    $userCaptcha = $_POST['g-recaptcha-response'];

                    // call the required function
                    $profileManager->sendLoginLink($email, $userCaptcha);
                }

                // load the view
                $viewController->loadView("loginWithEmail.html");

                break;
            case "emailLogin";

                // check if form posted
                if (isset($_POST['login'])) {

                    // get the required fields
                    $id = $_GET['id'];
                    $loginID = $_GET['loginID'];
                    $email = $_POST['email'];

                    // call the required function
                    if ($session->loginThrowEmail($email, $id, $loginID)) {
                        $functions->directBackToSource("index.php");
                    }
                }

                // load the view
                $viewController->loadView("emailLogin.html");

                break;
            default;

                if (isset($_POST["login"])) {
                    $daysToRemember = 0;
                    if ($_POST['rememberMe'] == "true" || isset($_POST["rememberMe"])) {
                        $daysToRemember = 180;
                    }

                    if ($session->loginWithPassword($_POST["username"], $_POST["password"], $daysToRemember)) {
                        $functions->directBackToSource("index.php");
                    }
                }

                $viewController->loadView("login.html");
                break;
        }
    }

}

new Login();

