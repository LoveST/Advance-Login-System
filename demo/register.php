<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/26/2017
 * Time: 8:50 PM
 */

require "../init.php";

if ($session->logged_in()) {
    header("Location: index.php");
    return;
}

if (isset($_POST['register'])) {

    // define all the needed variables
    $username = $_POST['username'];
    $email = $_POST['email'];
    $email2 = $_POST['email2'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $pin = $_POST['pin'];
    $pin2 = $_POST['pin2'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $dataOfBirth = $_POST['dateOfBirth'];
    $userCaptcha = $_POST['g-recaptcha-response'];

    if ($session->register($username, $email, $email2, $password, $password2, $pin, $pin2, $firstName, $lastName, $dataOfBirth, $userCaptcha)) {
        $success = true;
    }

}

$viewController->loadView("register.html");