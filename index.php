<?php

require "session.php";

/*********/
$message->getError();
/*********/

if(isset($_POST["login"])){
    if($session->loginWithPassword($_POST["username"],$_POST["password"])){
        echo "done";
    }
}

if(isset($_POST['logout'])){
    if($session->logOut()){
        echo "logged out. Refresh the page to update.";
    } else {
        echo "You've never logged in before.";
    }
}

require "login.html";
echo $user->get(User::First_Name) . " " . $user->get(User::Last_Name);