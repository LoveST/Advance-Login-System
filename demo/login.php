<?

require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();

use ALS\LoginStatus;

if ($session->statusCheck() == LoginStatus::GoodToGo) {
    header("Location: index.php");
} else {

    $page = $database->secureInput($_GET['ac']);

    switch ($page) {
        case "activate";

            if (isset($_POST['activate'])) {
                if ($session->activateAccount($_POST['code'], $_POST['email'])) {
                    $success = true;
                }
                //break;
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
            $viewController->addCustomScript(' <script src="'. $settings->getTemplatesURL() .'plugins/bootstrap-inputmask/bootstrap-inputmask.min.js" type="text/javascript"></script>');
            $viewController->addCustomScript('<script src="'. $settings->getTemplatesURL() .'plugins/autoNumeric/autoNumeric.js" type="text/javascript"></script>');
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

