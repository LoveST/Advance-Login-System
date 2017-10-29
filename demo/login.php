<?

require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();

use ALS\LoginStatus;

if ($session->statusCheck() == LoginStatus::GoodToGo) {
    header("Location: index.php");
} else {

    $page = $_GET['ac'];

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
        default;

            if (isset($_POST["login"])) {
                $daysToRemember = 0;
                if ($_POST['rememberMe'] == "true" || isset($_POST["rememberMe"])) {
                    $daysToRemember = 180;
                }

                if ($session->loginWithPassword($_POST["username"], $_POST["password"], $daysToRemember)) {
                    if ($session->anyRedirect()) {
                        header("Location: " . $session->getRedirect());
                    } else {
                        header("Location: index.php");
                    }
                }
            }

            $viewController->loadView("login.html");
            break;
    }

}

