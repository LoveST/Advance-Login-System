<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 5/10/2018
 * Time: 5:49 PM
 */

if (count(get_included_files()) == 1) exit("Direct access not permitted.");

class getInfo extends \ALS\Core
{

    public function __construct()
    {
        // construct the parent
        parent::__construct();

        // load the database class
        parent::initClasses();


        // hold the return array
        global $user, $database, $session, $functions;

        // check if user is logged in
        if (!$session->logged_in()) {
            $this->printError("Most be logged in to perform this action");
        }

        // check if authenticated
        if ($session->authenticationNeeded()) {
            $this->printError("You most finish your account authentication process");
        }

        // check if user is an admin
        if (!$user->isAdmin()) {
            $this->printError("You don't have the permission to perform this action");
        }

        // check if keyword is present
        $keyword = $database->secureInput($_GET['keyword']);
        if (empty($keyword)) {
            $this->printError("Username cannot be empty");
        }

        // load the required user data
        $requiredUser = new \ALS\User();
        if (!$requiredUser->initInstance($keyword)) {
            $this->printError("No such user exists");
        }

        $email = $functions->encryptIt($requiredUser->getEmail());
        $results = array("fName" => $requiredUser->getFirstName(), "lName" => $requiredUser->getLastName(), "age" => $requiredUser->getAge(), "email" => $email, "group" => $requiredUser->getGroup()->getName());

        // return the results
        echo json_encode($results);
        exit();
    }

    public function printError($msg)
    {
        die(json_encode(array("error" => $msg)));
    }

}

new getInfo();