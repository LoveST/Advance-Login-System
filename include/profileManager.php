<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/7/2017
 * Time: 3:22 PM
 */

namespace ALS;

class profileManager
{

    /**
     * profileManager constructor for PHP5.
     */
    function __construct()
    {
    }

    public function setNewUsername($username, $pin)
    {

        // define all the global variables
        global $database, $message, $user, $settings, $functions;

        // secure the strings
        $username = $database->secureInput($username);
        $pin = $database->secureInput($pin);

        // check if can change username
        if (!$settings->canChangeUsername()) {
            $message->setError("Username change is been disabled", Message::Error);
            return false;
        }

        // check if empty $username
        if (empty($username)) {
            $message->setError("Username field cannot be empty", Message::Error);
            return false;
        }

        // Username checks
        if (preg_match('/[^A-Za-z0-9]/', $username)) {
            $message->setError("Username most contain only letters and numbers", Message::Error);
            return false;
        }

        if (strlen($username) < 6 || strlen($username) > 25) {
            $message->setError("Username length most be between 6 -> 25 characters long", Message::Error);
            return false;
        }

        // check if username exists
        if ($functions->userExist($username)) {
            $message->setError("Username already exists", Message::Error);
            return false;
        }

        // check if using a pin is a must
        if ($settings->pinRequired()) {

            // check if empty pin
            if (empty($pin)) {
                $message->setError("Pin number field cannot be empty", Message::Error);
                return false;
            }

            // check if pin number matches
            $pin = md5($pin);
            if (!$user->is_samePinNumber($pin)) {
                $message->setError("Wrong pin number has been used", Message::Error);
                return false;
            }

            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_USERNAME . " = '" . $username . "' WHERE " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "' AND " . TBL_USERS_PIN . " = '" . $pin . "'";
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        } else {
            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_USERNAME . " = '" . $username . "' WHERE " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "'";
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        }
    }

    public function setNewEmail($email, $email2, $pin)
    {

        // define all the global variables
        global $database, $message, $user, $settings, $functions;

        // secure the strings
        $email = $database->secureInput($email);
        $email2 = $database->secureInput($email2);
        $pin = $database->secureInput($pin);

        // check if empty $email | $email2
        if (empty($email) || empty($email2)) {
            $message->setError("Both email fields are required", Message::Error);
            return false;
        }

        // email checks
        if ($email != $email2) {
            $message->setError("Email fields should be identical", Message::Error);
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message->setError("Invalid email syntax has been used", Message::Error);
            return false;
        }

        // check if email exists
        if ($functions->emailExist($email)) {
            $message->setError("Email address already exists.", Message::Error);
            return false;
        }


        if ($settings->pinRequired()) {

            // check if empty pin
            if (empty($pin)) {
                $message->setError("Pin number field cannot be empty", Message::Error);
                return false;
            }

            // check if pin number matches
            $pin = md5($pin);
            if (!$user->is_samePinNumber($pin)) {
                $message->setError("Wrong pin number has been used", Message::Error);
                return false;
            }

            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_EMAIL . " = '" . $email . "' WHERE " . TBL_USERS_EMAIL . " = '" . $user->getEmail() . "'";
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        } else {

            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_EMAIL . " = '" . $email . "' WHERE " . TBL_USERS_EMAIL . " = '" . $user->getEmail() . "'";
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                die;
            }
            // logout the user and set the error msg
            $user->logOut();
            $message->setError("You've been logged out for security reasons", Message::Error);
            return true;

        }

    }

    /**
     * set a new password for the current user session
     * @param $oldPass
     * @param $pinNumber
     * @param $newPass
     * @param $confirmNewPass
     * @return bool
     */
    function setNewPassword($oldPass, $pinNumber, $newPass, $confirmNewPass)
    {

        // define all the global variables
        global $database, $message, $user;

        // secure the strings
        $oldPass = $database->secureInput($oldPass);
        $pinNumber = $database->secureInput($pinNumber);
        $newPass = $database->secureInput($newPass);
        $confirmNewPass = $database->secureInput($confirmNewPass);

        // check if any of the field are empty
        if ($oldPass == "" || $pinNumber == "" || $newPass == "" || $confirmNewPass == "") {
            $message->setError("All the required fields must be filled", Message::Error);
            return false;
        }

        // new password validations
        if (strlen($newPass) < 8 && strlen($newPass) > 25) {
            $message->setError("Password length most be between 8 -> 25 characters long", Message::Error);
            return false;
        }

        // check if both password fields match each other
        if ($newPass != $confirmNewPass) {
            $message->setError("Both password field has to match", Message::Error);
            return false;
        }

        // check if old password matches the current one
        if (!$user->is_samePassword($oldPass)) {
            $message->setError("Wrong account password has been used", Message::Error);
            return false;
        }

        // check if pin number matches
        if (!$user->is_samePinNumber(md5($pinNumber))) {
            $message->setError("Wrong pin number has been used", Message::Error);
            return false;
        }

        // hash the new password
        $newPass = password_hash($newPass, PASSWORD_DEFAULT, ['cost' => 12]);

        // after validating, update the sql with the needed information
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_PASSWORD . " = '" . $newPass . "' WHERE " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "'";
        $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        // after no errors then return a success message and log the user out
        $message->setSuccess("You have successfully updated your password !");
        $user->forceSignInAgain();
        return true;
    }

    /**
     * Set a new pin number for the current user
     * @param $password
     * @param $currentPin
     * @param $newPin
     * @param $confirmPin
     * @return bool
     */
    function setNewPin($password, $currentPin, $newPin, $confirmPin)
    {
        // define all the global variables
        global $message, $user, $database, $settings, $functions;

        // secure the variables
        $password = $database->secureInput($password);
        $currentPin = $database->secureInput($currentPin);
        $newPin = $database->secureInput($newPin);
        $confirmPin = $database->secureInput($confirmPin);

        // check if any of the field are empty
        if ($password == "" || $currentPin == "" || $newPin == "" || $confirmPin == "") {
            $message->setError("All the required fields must be filled", Message::Error);
            return false;
        }

        // check if pin numbers are numeric
        if (!is_numeric($currentPin) || !is_numeric($newPin)) {
            $message->setError("Pin number has to been a numeric value", Message::Error);
            return false;
        }

        // check if any pin number length is less or greater than the required
        if (!$functions->isValidPinLength($newPin)) {
            $message->setError("Pin number length has to be exactly " . $settings->maxRequiredPinLength() . " integers long", Message::Error);
            return false;
        }

        // check if both pin's match
        if ($newPin != $confirmPin) {
            $message->setError("Both Pin's most match", Message::Error);
            return false;
        }

        // check if the password is correct
        if (!$user->is_samePassword($password)) {
            $message->setError("Wrong password has been used", Message::Error);
            return false;
        }

        // check if the pin is correct
        if (!$user->is_samePinNumber(md5($currentPin))) {
            $message->setError("Wrong pin number used", Message::Error);
            return false;
        }

        // update the current pin
        $user->updateUserRecord(TBL_USERS_PIN, md5($newPin));

        // force the user to sign off
        $user->forceSignInAgain();

        // if everything is done then return true
        $message->setSuccess("Pin number updated successfully");
        return true;
    }

    /**
     * Update the current users first & last name
     * @param $firstName
     * @param $lastName
     * @return bool
     */
    function updateFirstLastName($firstName, $lastName)
    {
        // define all the global variables
        global $message, $user;

        // check for empty fields
        if (empty($firstName) || empty($lastName)) {
            $message->setError("First & Last names cannot be empty", Message::Error);
            return false;
        }

        // check if no changes
        if ($firstName == $user->getFirstName() && $lastName == $user->getLastName()) {
            $message->setError("No changes were made", Message::Error);
            return false;
        }

        // update the first name
        if (!$this->setFirstName($firstName) || !$this->setLastName($lastName)) {
            $message->setError("Something went wrong", Message::Error);
            return false;
        }

        // if no errors then return true
        $message->setSuccess("First & Last names updated successfully");
        return true;
    }

    function updateDateOfBirth($birthDate)
    {
        // define all the global variables
        global $message, $user;

        // check if empty fields
        if (empty($birthDate)) {
            $message->setError("Date of birth cannot be empty", Message::Error);
            return false;
        }

        // check if both fields are the same
        if ($birthDate == $user->getBirthDate()) {
            $message->setError("No changes were made", Message::Error);
            return false;
        }

        // if no errors then return true
        $message->setSuccess("Date of birth updated successfully");
        return true;
    }

    /**
     * Set the user first name
     * @param string $firstName
     * @param User|null $user
     * @return bool
     */
    function setFirstName($firstName, $user = null)
    {
        // define all the global variables
        global $message, $database;

        // secure the input
        $firstName = $database->secureInput($firstName);

        // check if user is null
        if ($user == null) {
            $user = $GLOBALS['user'];
        }

        // check if firstName is empty
        if (empty($firstName)) {
            $message->setError("First name cannot be empty", Message::Error);
            return false;
        }

        // update the user table and return true
        $user->updateUserRecord(TBL_USERS_FNAME, $firstName);
        return true;
    }

    /**
     * Set the user last name
     * @param string $lastName
     * @param User|null $user
     * @return bool
     */
    function setLastName($lastName, $user = null)
    {
        // define all the global variables
        global $message, $database;

        // secure the input
        $lastName = $database->secureInput($lastName);

        // check if user is null
        if ($user == null) {
            $user = $GLOBALS['user'];
        }

        // check if last name is empty
        if (empty($lastName)) {
            $message->setError("Last name cannot be empty", Message::Error);
            return false;
        }

        // update the user table and return true
        $user->updateUserRecord(TBL_USERS_LNAME, $lastName);
        return true;
    }

    /**
     * Set the user's date of birth
     * @param string $birthday
     * @param User|null $user
     * @return bool
     */
    function setBirthday($birthday, $user = null)
    {
        // define all the global variables
        global $message, $database, $functions;

        // secure the input
        $birthday = $database->secureInput($birthday);

        // check if user is null
        if ($user == null) {
            $user = $GLOBALS['user'];
        }

        // check if birth date is empty
        if (empty($birthday)) {
            $message->setError("Birth date cannot be empty", Message::Error);
            return false;
        }

        // check if date prefix matches the required
        if (!$functions->isValidDate($birthday)) {
            $message->setError("Date of birth should be in the form of (mm/dd/yyyy)", Message::Error);
            return false;
        }

        // update the user table and return true
        $user->updateUserRecord(TBL_USERS_BIRTH_DATE, $birthday);
        return true;
    }

    function sendLoginLink($email, $userCaptcha)
    {
        // define all the global variables
        global $message, $database, $functions, $captcha, $mail, $settings, $viewController;

        // secure the inputs
        $email = $database->secureInput($email);
        $userCaptcha = $database->secureInput($userCaptcha);

        // check if any empty
        if (empty($email) || $email == "") {
            $message->setError("All fields are required", Message::Error);
            return false;
        }

        // check if email exists
        if (!$functions->emailExist($email)) {
            $message->setError("Email address does not exist", Message::Error);
            return false;
        }

        // check if captcha matches
        $captcha->sendRequest($userCaptcha);
        if (!$captcha->success()) {
            $message->setError("Wrong captcha has been used", Message::Error);
            return false;
        }

        // load the required user
        $newUser = $functions->searchUser($email, searchBy::email);

        // generate a UUID
        $uuid = md5(uniqid($email));

        // update the user database
        $newUser->updateUserRecord(TBL_USERS_LOGIN_ID, $uuid);

        // hold the required custom variables
        $vars = array(
            "loginLink" => $settings->siteURL() . "login.php?ac=emailLogin&id=" . $newUser->getID() . "&loginID=" . $uuid,
            "username" => $newUser->getUsername(),
            "title" => "Login Link"
        );

        // pre-load the required template
        $viewController->setCustomReservedCharacters($vars);

        // translate the page
        $file = $viewController->preLoadView("mail_user_login_link.html");

        // send the required email containing the link
        $mail->fromEmail($settings->siteEmail())->fromName($settings->siteName())->subject("Login Link")
            ->isTemplate(true)->to($newUser->getEmail())->template($file);

        // check if mail got sent
        if (!$mail->send()) {
            $message->setError("There was an error while sending the email", Message::Error);
            return false;
        }

        // if no errors, return true.
        $message->setSuccess("An email has been sent containing the login link.");
        return true;
    }

}