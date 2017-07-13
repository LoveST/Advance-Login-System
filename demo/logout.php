<?

require "../init.php";

if(!$session->logged_in()){
    $message->setError("You've never logged in before!", \ALS\Message\Message::Error);
    header("Location: login.php");
} else {
    if($session->logOut()){
        header("Location: index.php");
    } else {
        $message->setError("Something went wrong while logging out", \ALS\Message\Message::Error);
        header("Location: index.php");
    }
}
