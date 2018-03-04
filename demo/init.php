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
     * init constructor
     * @param string $coreClass
     */
    public function __construct($coreClass)
    {
        // require the Core class
        require_once $coreClass;
        $core = new \ALS\Core();

        // init the classes
        $core->initClasses();
    }

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