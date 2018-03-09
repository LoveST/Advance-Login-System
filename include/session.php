<?
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/12/2016
 * Time: 12:42 PM
 */

namespace ALS;

if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

class Session
{

    /**
     * init the class
     */
    function init()
    {

        global $translator;

        if (!$this->loginThrowSession()) {
            $this->loginThrowCookie(); // log the user in if he has the right cookie for his account
        }

        // init the user preferred language if any were to be found
        $translator->initSessionLanguage();

    }

    /**
     * Main function to login a guest as a user or admin if needed using a password
     * @param $username
     * @param $password
     * @param int $rememberMe
     * @return boolean
     */
    public function loginWithPassword($username, $password, $rememberMe = 1)
    {

        // define all the global variables
        global $database, $message, $settings, $functions, $browser, $translator;

        $username = $this->secureInput($username);
        $password = $this->secureInput($password);
        $rememberMe = $this->secureInput($rememberMe);

        if (!$settings->canLogin()) {
            $message->setError($translator->translateText("loginsDisabled"), Message::Error);
            return false;
        }

        if (empty($username) || empty($password)) {
            $message->setError($translator->translateText("allFieldsRequired"), Message::Error);
            return false;
        }

        // Username checks
        if (preg_match('/[^A-Za-z0-9]/', $username)) {
            $message->setError($translator->translateText("usernameContentError"), Message::Error);
            return false;
        }

        if (strlen($username) < 6 || strlen($username) > 25) {
            $message->setError($translator->translateText("usernameLengthError"), Message::Error);
            return false;
        }

        if (!$functions->userExist($username)) {
            $message->setError($translator->translateText("wrongLoginInfo"), Message::Error);
            return false;
        }
        // password checks
        if (strlen($password) < 8 && strlen($password) > 25) {
            $message->setError($translator->translateText("passwordLengthError"), Message::Error);
            return false;
        }

        // check if the user account is enabled
        if (!$functions->is_userActivated($username)) {
            $message->setError($translator->translateText("activateAccountBeforeLogin"), Message::Error);
            return false;
        }

        $sql = ("SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'");

        // get the sql results
        $result = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        if ($database->getQueryNumRows($result, true) < 1) {
            $message->setError($translator->translateText("wrongLoginInfo"), Message::Error);
            return false;
        }

        // grab the database results
        $row = $database->getQueryEffectedRow($result, true);

        // check if password fields match and if not then discard all changes
        if (!password_verify($password, $row[TBL_USERS_PASSWORD])) {
            $message->setError($translator->translateText("wrongLoginInfo"), Message::Error);
            return false;
        }

        // check if banned
        if ($row[TBL_USERS_BANNED] == 1) {
            $message->setError($translator->translateText("accountGotBanned"), Message::Error);
            return false;
        }

        // ** login successful process ** //
        $rememberMe = (86400 * $rememberMe); // one day as default
        $newToken = md5(uniqid(rand(), false));
        $id = $row[TBL_USERS_ID]; // id of the current user logging in

        // check if remember me is chosen
        if ($rememberMe != 0 && $rememberMe != "") {

            $user_dataArray = array(
                'token' => $newToken,
                'time' => time(),
                'ip' => $functions->getUserIP(),
                'browser_name' => $browser->getBrowser(),
                'browser_platform' => $browser->getPlatform()
            );

            // serialize the array
            $sArray = serialize($user_dataArray);

            // get the current time
            $loginTime = date("Y-m-d H:i:s", time());

            // ** Update the user Cookie code ** //
            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_TOKEN . " = '" . $sArray . "', " . TBL_USERS_SIGNIN_AGAIN . " = '0', " . TBL_USERS_LAST_LOGIN . " = '$loginTime' WHERE " . TBL_USERS_USERNAME . " = '" . $username . "'";

            // get the sql results
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                return false;
            }

            // ** set the cookie ** //
            $data = $id . "," . $newToken;
            $data = $functions->encryptIt($data); // encrypt the data
            setcookie("user_data", $data, time() + $rememberMe, "/"); // 86400 = 1 day

        }

        // ** Update the current session data ** //
        foreach ($row As $rowName => $rowValue) {
            $_SESSION["user_data"][$rowName] = $rowValue;
        }

        return true;
    }

    /**
     * Check if any redirect link has been posted
     * @return bool
     */
    public function anyRedirect()
    {
        return !empty($_GET['redirect']);
    }

    /**
     * Get the redirect link if available
     * @return string
     */
    public function getRedirect()
    {
        return $this->secureInput($_GET['redirect']);
    }

    /**
     * Check if session exists and if then log the user back in
     * @return bool
     */
    function loginThrowSession()
    {

        // define all the global variables
        global $database, $message, $user;

        if (isset($_SESSION['user_data']) || !empty($_SESSION['user_data'])) {

            // update the user_data that was stored in the session
            $user_data = $_SESSION['user_data'];

            // call the database to store the new session data
            $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = " . $user_data[TBL_USERS_ID] . " AND " . TBL_USERS_USERNAME . " = '" . $user_data[TBL_USERS_USERNAME] . "'";

            // get the sql results
            if (!$result = $database->getQueryResults($sql)) {
                $message->setError("Error while puling the user's required fields.", Message::Error);
                return false;
            }

            // check if the user exists
            if ($database->getQueryNumRows($result, true) < 1) {
                return false;
            }

            $row = $database->getQueryEffectedRow($result, true);

            // ** Update the current session data ** //
            foreach ($row As $rowName => $rowValue) {
                $_SESSION["user_data"][$rowName] = $rowValue;
            }

            // check if user has to log in again
            if ($user->mustSignInAgain()) {

                // ** Unset the session & cookies ** //
                unset($_SESSION["user_data"]);
                unset($_COOKIE["user_data"]);
                unset($_COOKIE["user_id"]);
                setcookie("user_data", null, -1, '/');
                setcookie("user_id", null, -1, '/');

                // update the database so that the user can log in again
                $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_SIGNIN_AGAIN . " = '0' WHERE " . TBL_USERS_ID . " = '" . $user->getID() . "' AND " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "'";

                // get the sql results
                $database->getQueryResults($sql);
                if ($database->anyError()) {
                    return false;
                }

                $message->setError("You've been logged out for security reasons", Message::Error);
                return false;
            }

            // initiate the user data
            $user->initUserData();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Log the user in if a matching cookie available
     * @return bool
     */
    function loginThrowCookie()
    {

        // define all the global variables
        global $database, $message, $user, $settings, $functions, $browser;

        if (empty($_SESSION["user_data"])) { // check if the current session is empty
            if (!empty($_COOKIE["user_data"])) { // check if the current cookie is not empty or null

                // decrypt the user data
                $data = $functions->decryptIt($_COOKIE['user_data']);
                $data = explode(",", $data);

                // check if empty cookie
                if ($data[0] == "" || $data[1] == "") {
                    return false;
                }

                $userID = $database->secureInput($data[0]);
                $cookieValue = $database->secureInput($data[1]);

                // ** Get the needed information from the database ** //
                $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_ID . " = '" . $userID . "'";

                // get the sql results
                $result = $database->getQueryResults($sql);
                if ($database->anyError()) {
                    return false;
                }

                // ** Check if such a user exists ** //
                if ($database->getQueryNumRows($result, true) > 0) {
                    $row = $database->getQueryEffectedRow($result, true);

                    // un-serialize the token array
                    $tokenArray = unserialize($row[TBL_USERS_TOKEN]);

                    // check if token and user data cookie match to continue
                    if ($tokenArray['token'] != $cookieValue) {
                        return false;
                    }

                    // check if the cookie was first created on the same device
                    if ($tokenArray['browser_platform'] != $browser->getPlatform()) {
                        return false;
                    }

                    // check if same ip login is disabled
                    // then check if the stored ip matches the current device ip address
                    if (!$settings->sameIpLogin()) {
                        if ($tokenArray['ip'] != $functions->getUserIP()) {
                            return false;
                        }
                    }

                    // check if user has to sign in again with his credentials
                    if ($row[TBL_USERS_SIGNIN_AGAIN]) {
                        unset($_COOKIE["user_data"]);
                        unset($_COOKIE["user_id"]);
                        setcookie("user_data", null, -1, '/');
                        setcookie("user_id", null, -1, '/');

                        // update the database so that the user can log in again
                        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_SIGNIN_AGAIN . " = '0' WHERE " . TBL_USERS_ID . " = '" . $user->getID() . "' AND " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "'";

                        // get the sql results
                        $database->getQueryResults($sql);
                        if ($database->anyError()) {
                            return false;
                        }

                        $message->setError("You've been logged out for security reasons", Message::Error);
                        return false;
                    }

                    // ** Update the current session data ** //
                    foreach ($row As $rowName => $rowValue) {
                        $_SESSION["user_data"][$rowName] = $rowValue;
                    }

                    // initiate the user data
                    $user->initUserData();

                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Main function to register a guest as a new user
     * @param $username
     * @param $email
     * @param $email2
     * @param $password
     * @param $password2
     * @param $pin
     * @param $pin2
     * @param $firstName
     * @param $lastName
     * @param $dataOfBirth
     * @param $userCaptcha
     * @return bool
     */
    public function register($username, $email, $email2, $password, $password2, $pin, $pin2, $firstName, $lastName, $dataOfBirth, $userCaptcha)
    {

        // define all the global variables
        global $database, $message, $captcha, $mail, $settings, $functions;

        // check if registration is enabled
        if (!$settings->canRegister()) {
            $message->setError("Registration is disabled at the moment.", Message::Error);
            return false;
        }

        // escape all the given strings and integers
        $username = $database->secureInput($username);
        $email = $database->secureInput($email);
        $email2 = $database->secureInput($email2);
        $password = $database->secureInput($password);
        $password2 = $database->secureInput($password2);
        $pin = $database->secureInput($pin);
        $pin2 = $database->secureInput($pin2);
        $firstName = $database->secureInput($firstName);
        $lastName = $database->secureInput($lastName);
        $dataOfBirth = $database->secureInput($dataOfBirth);
        $userCaptcha = $database->secureInput($userCaptcha);

        // check for empty given strings
        if (empty($username) || empty($email) || empty($email2) || empty($password) || empty($password2) || empty($firstName) || empty($lastName) || empty($dataOfBirth)) {
            $message->setError("All fields are required", Message::Error);
            return false;
        }

        // check if pin is required and if required check if 0 is given for empty
        if ($settings->pinRequired()) {
            if (empty($pin) || empty($pin2)) {
                $message->setError("All fields are required", Message::Error);
                return false;
            }
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

        // email checks
        if ($email != $email2) {
            $message->setError("Email fields should be identical", Message::Error);
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message->setError("Invalid email syntax has been used", Message::Error);
            return false;
        }

        // hash and secure the password
        $hashPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        $hashPassword2 = password_hash($password2, PASSWORD_DEFAULT, ['cost' => 12]);

        // password checks
        if ($hashPassword != $hashPassword2) {
            $message->setError("Password fields should be the identical", Message::Error);
            return false;
        }

        if (strlen($password) < 8 && strlen($password) > 25) {
            $message->setError("Password length most be between 8 -> 25 characters long", Message::Error);
            return false;
        }

        // check if pin is required then check for length and the value being an integer and if they are both the same
        if ($pin != $pin2) {
            $message->setError("Pin number fields should be the identical", Message::Error);
            return false;
        }

        if (!is_numeric($pin)) {
            $message->setError("Pin number should only contain numbers", Message::Error);
            return false;
        }

        if ($pin[0] == 0) {
            $message->setError("Pin number cannot start with a 0", Message::Error);
            return false;
        }

        if (strlen($pin) < 6 || strlen($pin) > 6) {
            $message->setError("Pin number has to be exactly 6 characters long", Message::Error);
            return false;
        }

        // date of birth checks
        if (!is_string($dataOfBirth)) {
            $message->setError("Date of birth should be a string", Message::Error);
            return false;
        }

        if (!$functions->isValidDate($dataOfBirth)) { // $functions->isValidDate($dataOfBirth)
            $message->setError("Date of birth should be in the form of (mm/dd/yyyy)", Message::Error);
            return false;
        }

        if ($settings->minimumAgeRequired()) { // check if there is a minimum age restriction for signing up
            if ($functions->getAge($dataOfBirth) < $settings->minimumAge()) {
                $message->setError("You must be at least " . $settings->minimumAge() . " years old to sign up", Message::Error);
                return false;
            }
        }

        // check if username exists
        if ($functions->userExist($username)) {
            $message->setError("Username has been used before", Message::Error);
            return false;
        }

        // check if email exists
        if ($functions->emailExist($email)) {
            $message->setError("Email has been used before", Message::Error);
            return false;
        }

        // send the captcha request for validation
        $captcha->sendRequest($userCaptcha);

        // check if captcha was a success
        if (!$captcha->success()) {
            $message->setError("Wrong captcha has been used", Message::Error);
            return false;
        }

        // After passing all checks then start the registration process

        // secure the password and the pin if submitted
        if ($settings->pinRequired()) { // check if pin is needed
            $pin = md5($pin);
        }

        $loginTime = date("Y-m-d H:i:s", time()); // current time and date to be set as a date of signing up
        // get a new id for the user
        $id = $functions->getNewID();

        // check if activation is required or not and proceed
        if ($settings->activationRequired()) {
            // send the activation code and update the database
            $activationCode = $functions->generateRandomString(20);

            $sql = "INSERT
                    INTO " . TBL_USERS . " (" . TBL_USERS_ID . "," . TBL_USERS_USERNAME . "," . TBL_USERS_PASSWORD . "," . TBL_USERS_FNAME . "," . TBL_USERS_LNAME . "," . TBL_USERS_EMAIL . "," . TBL_USERS_LEVEL . "," . TBL_USERS_DATE_JOINED . "," . TBL_USERS_LAST_LOGIN . "," . TBL_USERS_TOKEN . "," . TBL_USERS_EXPIRE . "," . TBL_USERS_RESET_CODE . "," . TBL_USERS_PIN . "," . TBL_USERS_BANNED . "," . TBL_USERS_ACTIVATED . "," . TBL_USERS_ACTIVATION_CODE . "," . TBL_USERS_BIRTH_DATE . ")
                    VALUES ('$id','$username','$hashPassword','$firstName','$lastName','$email','1','$loginTime','$loginTime','0','0','0','$pin','0','0','$activationCode','$dataOfBirth')";

            // get the sql results
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                return false;
            }

            // if successful registration then send an email including the activation code
            $vars = array(
                '{:username}' => $username,
                '{:siteURL}' => $settings->get(Settings::SITE_URL),
                '{:siteName}' => $settings->get(Settings::SITE_NAME),
                '{:activationCode}' => $activationCode,
            );

            $to = $email;
            $content = file_get_contents('templates/' . $settings->get(Settings::SITE_THEME) . "/activateAccountEmailTemplate.html");
            $subject = "Activate Account || " . $settings->get(Settings::SITE_NAME);
            // convert variables to actual values
            $content = strtr($content, $vars);
            // initiate the mail class to prepare to send the email
            $mail = new Mail();
            // set the sender email
            $mail->fromEmail($settings->get(Settings::SITE_EMAIL));
            // set the sender name
            $mail->fromName("Support");
            // set the receiver email
            $mail->to($to);
            // set the subject
            $mail->subject($subject);
            // check if include a template is checked
            // Set mail to template
            $mail->isTemplate(true);
            // set the mail template content
            $mail->template($content);

            if ($mail->send()) {
                return true;
            } else {
                $message->setError("Registration completed. But failed to send the activation code.", Message::Error);
                return false;
            }
        } else {
            // automatically activate the user and update the database
            $sql = "INSERT
                    INTO " . TBL_USERS . " (" . TBL_USERS_ID . "," . TBL_USERS_USERNAME . "," . TBL_USERS_FNAME . "," . TBL_USERS_LNAME . "," . TBL_USERS_EMAIL . "," . TBL_USERS_LEVEL . "," . TBL_USERS_PASSWORD . "," . TBL_USERS_DATE_JOINED . "," . TBL_USERS_LAST_LOGIN . "," . TBL_USERS_EXPIRE . "," . TBL_USERS_TOKEN . "," . TBL_USERS_RESET_CODE . "," . TBL_USERS_PIN . "," . TBL_USERS_BANNED . "," . TBL_USERS_ACTIVATED . "," . TBL_USERS_ACTIVATION_CODE . ")
                    VALUES ('$id','$username','$firstName','$lastName','$email','1','$hashPassword','$loginTime','$loginTime','0','0','0','$pin','0','1','0')";

            // get the sql results
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                return false;
            }

            return true;
        }
    }

    /**
     * Log the user out from the current session
     * @return bool
     */
    public function logOut()
    {

        // define all the global variables
        global $database, $message, $user;

        if (empty($_SESSION["user_data"]) && empty($_COOKIE["user_data"]) && empty($_COOKIE["user_id"])) { // check to see if the user is logged in the session of in the cookies
            return false;
        } else {

            // ** Clear the Cookie auth code ** //
            $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_TOKEN . " = '' WHERE " . TBL_USERS_USERNAME . " = '" . $user->getUsername() . "'";

            // get the sql results
            $database->getQueryResults($sql);
            if ($database->anyError()) {
                $message->setError("Database Error : " . $database->getError(), Message::Fatal);
                return false;
            }

            // ** Unset the session & cookies ** //
            unset($_SESSION["user_data"]);
            unset($_COOKIE["user_data"]);
            unset($_COOKIE["user_id"]);
            setcookie("user_data", null, -1, '/');
            setcookie("user_id", null, -1, '/');
            return true;
        }
    }

    /**
     * Activate an account using code and an email
     * @param $code
     * @param $email
     * @return bool
     */
    public function activateAccount($code, $email)
    {

        // define all the global variables
        global $database, $message, $functions;

        // escape the given strings
        $code = $this->secureInput($code);
        $email = $this->secureInput($email);

        // start the checks
        if (empty($code) || empty($email)) {
            $message->setError("Code/Email fields most not be empty", Message::Error);
            return false;
        }

        // check if email exists
        if (!$functions->emailExist($email)) {
            $message->setError("The provided email is not in our database.", Message::Error);
            return false;
        }

        // check if the given code matches the required characters
        if (strlen($code) < 20 || strlen($code) > 20) {
            $message->setError("The given code has to be exactly 20 characters long", Message::Error);
            return false;
        }

        // check if account already has been activated
        if ($functions->is_userActivated($email, true)) {
            $message->setError("The account is already activated", Message::Error);
            return false;
        }

        $sql = "SELECT * FROM " . TBL_USERS . " WHERE " . TBL_USERS_EMAIL . " = '" . $email . "' AND " . TBL_USERS_ACTIVATION_CODE . " = '" . $code . "'";

        // get the sql results
        $result = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        // check if wrong code has been used
        if ($database->getQueryNumRows($result, true) < 1) {
            $message->setError("Wrong activation code has been used.", Message::Error);
            return false;
        }

        //update the user account with the needed information
        $sql = "UPDATE " . TBL_USERS . " SET " . TBL_USERS_ACTIVATED . " ='1'," . TBL_USERS_ACTIVATION_CODE . "='' WHERE " . TBL_USERS_EMAIL . " = '" . $email . "' AND " . TBL_USERS_ACTIVATION_CODE . " = '" . $code . "'";

        // get the sql results
        $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        $message->setSuccess("Your account has been activated successfully !");
        return true;
    }

    /**
     * check if the client is logged in as a user or as a guest !
     * @return bool
     */
    public function logged_in()
    {
        if (isset($_SESSION['user_data'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Prepare any given string from injections
     * @param $string
     * @return string
     */
    function secureInput($string)
    {

        // define all the global variables
        global $database;

        return $database->secureInput($string);
    }

    /**
     * load a user account and all the needed data from the class User
     * @param $username
     * @return User|bool
     */
    function loadUser($username)
    {

        // define all the global variables
        global $database;

        // escape the username string
        $username = $database->secureInput($username);
        // create a new instance of the User class
        $loadUser = new User();
        // initiate the instance with the requested username
        if (!($found = $loadUser->initInstance($username))) {
            return false;
        }
        // return the loaded user
        return $loadUser;
    }

    /**
     * checkstatus the users current device
     * @param $pin
     * @return boolean
     */
    function verifyDevice($pin)
    {

        // define all the global variables
        global $database, $user, $message;

        // escape string
        $pin = $database->secureInput($pin);

        // pin number checks
        if (!is_numeric($pin)) {
            $message->setError("Pin number should only contain numbers", Message::Error);
            return false;
        }

        if ($pin[0] == 0) {
            $message->setError("Pin number cannot start with a 0", Message::Error);
            return false;
        }

        if (strlen($pin) < 6 || strlen($pin) > 6) {
            $message->setError("Pin number has to be exactly 6 characters long", Message::Error);
            return false;
        }

        // check if pin number matches the users pin
        if (!$user->matchPin($pin)) {
            $message->setError("Wrong pin number has been used", Message::Error);
            return false;
        }

        // add the new device and check for any errors
        if (!$user->devices()->addDevice()) {
            $message->setError("Oops, something went wrong while verifying your new device", Message::Error);
            return false;
        }

        // if no errors then return a success message
        $message->setSuccess("This device has been verified successfully");
        return true;
    }

    /**
     * check the users current status from :
     * if the user is logged in
     * if the user's current device is verified
     * if the site is enabled to view
     */
    function statusCheck()
    {

        // define all the global variables
        global $user, $settings, $message, $translator;

        // check if the site is disabled
        if ($settings->siteDisabled()) {
            // check if the user is an admin then do and exception
            if ($user->isAdmin()) {
                return LoginStatus::GoodToGo;
            }
            $message->customKill($translator->translateText("siteDisabled"), $translator->translateText("siteDisabledMSG"), $settings->get(Settings::SITE_THEME));
            return false;
        }

        // check if the user has already logged in
        if (!$this->logged_in()) {
            return LoginStatus::NeedToLogin;
        }

        // check if the current user's device is verified
        if (!$user->devices()->canAccess()) {
            return LoginStatus::VerifyDevice;
        }

        // check if session is authenticated
        if ($this->authenticationNeeded() && $user->twoFactorEnabled()) {
            return LoginStatus::AuthenticationNeeded;
        }

        return LoginStatus::GoodToGo;
    }

    /**
     * check if the current user has an admin level
     * and if he does not then kill the script and print
     * a custom error message
     * @return bool
     */
    function adminCheck()
    {

        // define all the global variables
        global $user, $message, $settings, $translator;

        // check if the user is admin
        if ($user->isAdmin()) {
            return true;
        }

        // if not then print a custom error message
        $_SESSION['siteTemplateURL'] = $settings->getTemplatesURL();
        $message->customKill($translator->translateText("invalidPrivileges"), $translator->translateText("invalidPrivilegesMSG"), $settings->getTemplatesPath());
        return false;
    }

    /**
     * Check if the current user has a certain permission to access a certain page
     * @param string $permission
     * @return bool
     */
    function requirePermission($permission)
    {
        // define all the global variables
        global $user, $message, $translator, $settings;

        // check if user is logged in and has permission
        if ($this->logged_in() && $user->hasPermission($permission)) {
            return true;
        } else {
            $_SESSION['siteTemplateURL'] = $settings->getTemplatesURL();
            $message->customKill($translator->translateText("invalidPrivileges"), $translator->translateText("invalidPrivilegesMSG"), $settings->getTemplatesPath());
            return false;
        }
    }

    /**
     * get the current session's preferred language to be used
     * Cookie : language
     * @return bool|string
     */
    function getSessionLanguage()
    {

        // define all the global variables
        global $user;

        // check if its a user or guest session
        if ($this->logged_in()) { // user session

            // grab the users preferred language
            $language = $user->getPreferredLanguage();

        } else { // guest session

            // check if any cookies were to be found for a custom language
            $language = $_COOKIE['language'];

        }

        // check if $language has been initialized or not
        if (is_string($language) && $language != "") {
            return $language; // if no errors then return the language string
        } else {
            return false; // if no language, then just return false
        }
    }

    /**
     * check if the current session user needs authentication
     * @return bool
     */
    function authenticationNeeded()
    {

        // define all the global variables
        global $user;

        // check if user has 2-factor authentication enabled
        if (!$user->twoFactorEnabled()) {
            return false;
        }

        // check if secret key has been setup
        if ($user->get2FactorCode() == "") {
            return false;
        }

        // check if current session has been authenticated
        if ($_SESSION["authenticated"] == true) {
            return false;
        }

        // if no errors then ask for authentication
        return true;
    }

    /**
     * Verify the user identity using Google Authenticator
     * @param int $authCode
     * @return bool
     */
    function authenticateUser($authCode)
    {

        // define all global variables
        global $user, $database, $message, $translator, $googleAuth;

        // secure the string
        $authCode = $database->secureInput($authCode);

        // check if empty string
        if ($authCode == "") {
            $message->setError($translator->translateText("allFieldsRequired"), Message::Error);
            return false;
        }

        // check if string length is 6
        if (strlen($authCode) != 6) {
            $message->setError($translator->translateText("authCodeLength"), Message::Error);
            return false;
        }

        // check if codes match
        if (!$googleAuth->checkCode($user->get2FactorCode(), $authCode)) {
            $message->setError($translator->translateText("wrongAuthCode"), Message::Error);
            return false;
        }

        // if no errors then authenticate the session and return true
        $_SESSION["authenticated"] = 1;
        return true;
    }

}

abstract class LoginStatus
{

    const RedirectToLogin = 2;
    const VerifyDevice = 3;
    const MustSignInAgain = 4;
    const AuthenticationNeeded = 5;
    const NeedToLogin = 6;
    const GoodToGo = 1;

}