<?php

require "../init.php";

/*********/
$message->getError();
/*********/

if($session->logged_in()){

    require "templates/".TEMPLATE."/home.html";

} else {
    header("Location: login.php");
}