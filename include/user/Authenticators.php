<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 10/11/2017
 * Time: 10:58 PM
 */

namespace ALS\User;


use ALS\Message;

class Authenticators
{
    private $data;
    private $auths = array();

    public function __construct($data)
    {
        $this->data = $data;
        $this->init();
    }

    /**
     * Get the current user authenticators
     * @return array
     */
    public function getAuthenticators()
    {
        return $this->auths;
    }

    // authenticators = { array{ site, array(permissions), date_allowed, daysToExpire }}

    /**
     * Initialize the authenticated websites
     */
    private function init()
    {
        // globals
        global $message;

        // check if the supplied data is not empty
        if (!empty($this->data[TBL_USERS_AUTHENTICATORS])) {

            // un-serialize the data
            if (!$auths = unserialize($this->data)) {
                $message->setError("Something went wrong while un-serializing the data", Message::Error);
                return;
            }

            // set the current auth's
            $this->auths = $auths;
        }
    }

}