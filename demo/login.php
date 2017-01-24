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
    }

}

require "templates/".TEMPLATE."/login.html";