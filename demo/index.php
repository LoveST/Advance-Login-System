<?php

require "../init.php";

/*********/
$message->getError();
/*********/

if($session->logged_in()){

    echo "<a href=\"logout.php\" name=\"logout\">Log Out</a><br>";
    echo $user->get(User::First_Name) . " " . $user->get(User::Last_Name);

} else {
    header("Location: login.php");
}