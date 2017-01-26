<?

require "../init.php";

if($session->logged_in()){
    header("Location: index.php");
} else {
    if(isset($_POST["login"])){
        $daysToRemember = 0;
        if($_POST['rememberMe'] == "true" || isset($_POST["rememberMe"])){
            $daysToRemember = 180;
        }

        if($session->loginWithPassword($_POST["username"],$_POST["password"], $daysToRemember)){
            header("Location: index.php");
        }
    } else {
        if(!$this->settings->canLogin()){
            $this->message->setError("Logging in has been disabled at the moment.", Message::Error);
            return false;
        }
    }
}

require "templates/". $settings->get(Settings::SITE_THEME) ."/login.html";