<?

require "../init.php";

if($session->logged_in() && $user->devices()->canAccess()) {
    header("Location: index.php");
}else if(!$user->devices()->canAccess() && $session->logged_in()){
    die("your computer has to be verified first by entering your pin number");
} else {

    $page = $_GET['ac'];

    switch($page){
        case "activate";

            if(isset($_POST['activate'])){
                if($session->activateAccount($_POST['code'],$_POST['email'])){
                    $success = true;
                }
                //break;
            }

            require "templates/". $settings->get(Settings::SITE_THEME) ."/activate-account.html";
            break;
        default;

            if(isset($_POST["login"])){
                $daysToRemember = 0;
                if($_POST['rememberMe'] == "true" || isset($_POST["rememberMe"])){
                    $daysToRemember = 180;
                }

                if($session->loginWithPassword($_POST["username"],$_POST["password"], $daysToRemember)){
                    header("Location: index.php");
                }
            }

            require "templates/". $settings->get(Settings::SITE_THEME) ."/login.html";
            break;
    }

}

