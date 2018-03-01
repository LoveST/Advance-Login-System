<?

require "init.php";
$init = new init("../Core.php");

use ALS\Message;

if (!$session->logged_in()) {
    $message->setError("You've never logged in before!", Message::Error);
    header("Location: login.php");
} else {
    if ($session->logOut()) {
        header("Location: index.php");
    } else {
        $message->setError("Something went wrong while logging out", Message::Fatal);
        header("Location: index.php");
    }
}
