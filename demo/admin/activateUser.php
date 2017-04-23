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
$session->adminCheck();
/** End check user & site status**/

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

require "../". TEMPLATE_PATH ."/ad_activateUser.html";