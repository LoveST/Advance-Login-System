<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 10/24/2017
 * Time: 11:04 AM
 */

require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();
use ALS\AuthStatus;

// check if user has already authenticated
$authType = $_GET['authType'];
$website = "http://www.lovemst.com";

$auth = $authenticator->checkAuthStatus($authType);
if($auth == AuthStatus::Most_Authenticate){

    $viewController->setCustomReservedCharacters(array("auth_website" => $website, "auth_permission" => $authenticator->getAuthTypeDescription($authType)));
    $viewController->loadView("authenticator.html");

} else if($auth == AuthStatus::Authenticated){
    echo "authenticated";
} else if($auth == AuthStatus::Authenticated_Successfully){
    echo "done";
} else {
    echo "Error";
}