<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 3/1/2018
 * Time: 11:14 AM
 */

use ALS\LoginStatus;

class init
{

    /**
     * Check the user's login status
     */
    public function loginCheck()
    {
        // define the required global variables
        global $functions, $settings, $session;

        // grab the user status
        $status = $session->statusCheck();

        if ($status == LoginStatus::NeedToLogin) {
            $functions->redirect($settings->siteURL() . "login.php", true);
        } else if ($status == LoginStatus::VerifyDevice) {
            $functions->redirect($settings->siteURL() . "login.php?ac=verifyPin", true);
        } else if ($status == LoginStatus::AuthenticationNeeded) {
            $functions->redirect($settings->siteURL() . "login.php?ac=googleAuthenticate", true);
        }
    }

}