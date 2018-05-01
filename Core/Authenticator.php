<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 10/11/2017
 * Time: 10:55 PM
 */

namespace ALS;


class Authenticator
{

    private $authTypes = array();

    public function __construct()
    {
        $this->setDefaultAuthTypes();
    }

    public function authenticateUser($authType)
    {

    }

    public function checkAuthStatus($authType)
    {
        // init the required globals
        global $user;

        // check if already authenticated
        if ($user->getAuthenticators()->isAuthenticated($authType)) {
            return AuthStatus::Authenticated;
        } else {
            return AuthStatus::Most_Authenticate;
        }
    }

    public function setDefaultAuthTypes()
    {
        // init the required globals
        global $user;

        // set the type 'basic_info'
        $name = "basic_info";
        $description = "Access your basic account information like username,first name, last name, email address, date of birth";
        $values = array('username' => $user->getUsername(), 'first_name' => $user->getFirstName(), 'last_name' => $user->getLastName(), 'email' => $user->getEmail(), 'birthday' => $user->getBirthDate());
        $this->addAuthType($name, $description, $values);

    }

    /**
     * Check if the current user is logged in
     * @return bool
     */
    public function isLoggedIn()
    {
        return true;
    }

    /**
     * Check if an Authentication Type exists
     * @param string $type
     * @return bool
     */
    final public function isAuthType($type)
    {
        return in_array($type, $this->authTypes);
    }

    final public function getAuthTypeDescription($authType)
    {
        foreach ($this->authTypes as $auth) {
            if ($auth['type'] == $authType) {
                return $auth['description'];
            }
        }
    }

    /**
     * @param string $name
     * @param string $description
     * @param array $values
     * @return bool
     */
    final public function addAuthType($name, $description, $values)
    {
        // init the required globals
        global $message;

        // check if any of the important fields are empty
        if (empty($name) || empty($description) || empty($values)) {
            $message->setError("Missing required authentication fields", Message::Error);
            return false;
        }

        // check if values is an array
        if (!is_array($values)) {
            $message->setError("Values variable most be an array", Message::Error);
            return false;
        }

        // check if name already exists
        if (in_array($name, $this->authTypes)) {
            $message->setError("Authentication type already exist", Message::Error);
            return false;
        }

        // if no errors then add the required type
        $array = array('type' => $name, 'description' => $description, 'values' => $values);

        // add to array
        array_push($this->authTypes, $array);

        return true;
    }

}

$authenticator = new Authenticator();

abstract class AuthStatus
{

    const Authenticated = 1;
    const Most_Authenticate = 2;
    const Authenticated_Successfully = 3;

}