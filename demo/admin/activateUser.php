<?
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/15/2017
 * Time: 11:12 AM
 */

/** Check user & site status **/
require "../../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$session->statusCheck();
$session->adminCheck();
/** End check user & site status**/

if (isset($_POST['activate'])) {

    $username = $database->secureInput($_POST['username']);
    if ($getUser = $session->loadUser($username)) {
        if ($getUser->activateAccount()) {
            $success = true;
        }
    }

} else if (isset($_POST['de-activate'])) {

    $username = $database->secureInput($_POST['username']);
    if ($user->getUsername() != $username) {
        if ($getUser = $session->loadUser($username)) {
            if ($getUser->disableAccount()) {
                $success = true;
            }
        }
    } else {
        $message->setError("you can't de-activate your self", \ALS\Message::Error);
    }

}

$viewController->loadView("ad_activateUser.html");