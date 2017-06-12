<?

require "../init.php";

if ($session->logged_in() && $user->devices()->canAccess()) {
    header("Location: index.php");
} else if (!$user->devices()->canAccess() && $session->logged_in()) {

    if (isset($_POST['verify'])) {
        if ($session->verifyDevice($_POST['pin'])) {
            header("Location: index.php");
        }
    }

    $viewController->loadView("verify_device.html");
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
                    header("Location: index.php");
                }
            }

            $viewController->loadView("login.html");
            break;
    }

}

