<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/12/2017
 * Time: 4:35 PM
 */

/** Check user & site status **/
require "../init.php";
$init = new init("../../Core.php");
$init->loginCheck();
$session->adminCheck();
/** End check user & site status**/

// get the _GET tag request
$ac = $database->secureInput($_GET['ac']);
$username = $database->secureInput($_GET['username']);
$id = $database->secureInput($_GET['id']);

// try to load the user
$requestedUser = $session->loadUser($username);

if (isset($_POST['change_level'])) {

} else if (isset($_POST['ban_account'])) {

    if ($user->getUsername() != $username) {

        // try to ban the user
        if ($requestedUser->ban()) {
            $message->setSuccess($username . "'s account has been banned successfully'");
        }

    } else {
        $message->setError("You can't ban yourself !", \ALS\Message::Error);
    }

    $viewController->loadView("ad_viewUserProfile.html");

} else if (isset($_POST['unban_account'])) {

    if ($user->getUsername() != $username) {

        // try to ban the user
        if ($requestedUser->unBan()) {
            $message->setSuccess($username . "'s account has been un-banned successfully'");
        }

    } else {
        $message->setError("You can't un-ban yourself !", \ALS\Message::Error);
    }

    $viewController->loadView("ad_viewUserProfile.html");

} else {

    $requestedUser = $session->loadUser($username);

    if (!$requestedUser) {
        die("no such user found");
    }

    $viewController->loadView("ad_viewUserProfile.html");

}