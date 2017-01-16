<?

require "../init.php";

if($session->logged_in()){
    header("Location: index.php");
} else {
    if(isset($_POST["login"])){
        if($session->loginWithPassword($_POST["username"],$_POST["password"], $_POST['rememberMe'])){
            header("Location: index.php");
        }
    }

}

require "template/login.html";