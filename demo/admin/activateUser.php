<?
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/15/2017
 * Time: 11:12 AM
 */

/** Check user & site status **/
require "../../init.php";
$session->statusCheck();
/** End check user & site status**/

if(!$user->isAdmin()){
    $message->customKill("Invalid Privileges","You do not have the permission to access this page",$settings->siteTheme());
    die();
}

if(isset($_POST['activate'])){
	
	$username = $database->escapeString($_POST['username']);
	if($getUser = $session->loadUser($username)){
		if($getUser->activateAccount()){
            $success = true;
		}
	}
	
} else if(isset($_POST['de-activate'])){

    $username = $database->escapeString($_POST['username']);
    if($getUser = $session->loadUser($username)){
        if($getUser->disableAccount()){
            $success = true;
        }
    }

}

require "../templates/". $settings->get(Settings::SITE_THEME) ."/ad_activateUser.html";