<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/12/2017
 * Time: 11:28 AM
 */

namespace ALS;

require "Translator.php";

class ViewController
{

    private $requiredTemplate = "";
    private $customVariables = null;
    private $uniqueID;
    private $translator;

    public function __construct()
    {

        // init the Translator class
        $this->translator = new Translator();

    }

    /**
     * load a template to the view controller
     * @param string $templateName
     * @return bool
     */
    public function loadView($templateName)
    {

        // init the required global variables
        global $settings, $functions, $message, $user, $session;

        // check if the cache directory is not empty
        if (!$functions->isDirEmpty($settings->getTemplatesCachePath())) {
            $this->emptyCacheFolder();
        }

        // check if empty string is supplied
        if ($templateName == "") {
            $this->killViewer($this->getTranslator()->translateText("templatePathNeeded"));
        }

        // check if the cache directory exists
        if (!file_exists($settings->getTemplatesCachePath())) {
            mkdir($settings->getTemplatesCachePath(), 0777);
        }

        // check if view is accessible
        if (!$this->isViewAccessible($templateName)) {
            $this->killViewer($this->getTranslator()->translateText("templateDoNotExists"));
        }

        // generate a unique id for the file
        $this->uniqueID = md5(uniqid(rand(), true));

        // update the required template
        $this->requiredTemplate = $this->uniqueID . "." . "php";

        // grab the file content
        $file = file_get_contents($settings->getTemplatesPath() . $templateName);

        // replace any special reserved characters
        $file = $this->replaceReservedCharacters($file);

        // translate the TEMPLATE file
        $file = $this->getTranslator()->translateFile($file);

        // save the file to the temporary cache folder
        $fp = fopen($settings->getTemplatesCachePath() . $this->requiredTemplate, "wb");
        fwrite($fp, $file);
        fclose($fp);

        // load the html file
        try {
            include_once $settings->getTemplatesCachePath() . $this->requiredTemplate . "";
        } catch (\Exception $ex) {
            $this->deleteFile($this->requiredTemplate);
            $this->killViewer($this->getTranslator()->translateText("errorLoadingTemplate"));
        }

        // delete the temporary template file
        $this->deleteFile($this->requiredTemplate);

        // print the timestamp if enabled
        if ($settings->siteLoadingTimestamp()) {
            echo 'Page generated in ' . $settings->initTimeStamp() . ' seconds.';
        }

        return true;
    }

    /**
     * pre load a template
     * @param $templateName
     * @return bool|string
     */
    public function preLoadView($templateName)
    {

        // init the required global variables
        global $settings, $functions, $message, $user, $session;

        // check if the cache directory is not empty
        if (!$functions->isDirEmpty($settings->getTemplatesCachePath())) {
            $this->emptyCacheFolder();
        }

        // check if empty string is supplied
        if ($templateName == "") {
            $message->setError($this->getTranslator()->translateText("templatePathNeeded"), Message::Error);
            return false;
        }

        // check if the cache directory exists
        if (!file_exists($settings->getTemplatesCachePath())) {
            mkdir($settings->getTemplatesCachePath(), 0777);
        }

        // check if view is accessible
        if (!$this->isViewAccessible($templateName)) {
            $message->setError($this->getTranslator()->translateText("templateDoNotExists"), Message::Error);
            return false;
        }

        // generate a unique id for the file
        $uniqueID = md5(uniqid(rand(), true));

        // update the required template
        $this->requiredTemplate = $uniqueID . "." . "php";

        // grab the file content
        $file = file_get_contents($settings->getTemplatesPath() . $templateName);

        // replace any special reserved characters
        $file = $this->replaceReservedCharacters($file);

        // translate the TEMPLATE file
        $file = $this->getTranslator()->translateFile($file);

        // return the needed translated file
        return $file;
    }

    /**
     * empty the entire cache folder in case of an Unhandled Error
     */
    private function emptyCacheFolder()
    {

        global $settings;

        // get the total files in the cache folder
        $files = array_diff(scandir($settings->getTemplatesCachePath()), array('.', '..'));

        // unlink each file and delete it
        foreach ($files as $fileName) {

            // unlink the file
            unlink($settings->getTemplatesCachePath() . $settings->getSubLine() . $fileName);

        }
    }

    /**
     * check if a certain template exists and it can be read.
     * @param $template
     * @return bool
     */
    private function isViewAccessible($template)
    {

        // init the required global variables
        global $settings;

        if (is_readable($settings->getTemplatesPath() . $template)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * fill in all the required reserved keywords and values in an html file and return the new file
     * @param $file
     * @return string
     */
    private function replaceReservedCharacters($file)
    {

        // init the required global variables
        global $settings, $user;

        $vars = array(
            'db_connectionType' => CONNECTION_TYPE,
            'settings_siteName' => $settings->siteName(),
            'settings_siteURL' => $settings->siteURL(),
            'settings_sitePath' => $settings->sitePath(),
            'settings_siteEmail' => $settings->siteEmail(),
            'settings_theme' => $settings->siteTheme(),
            'settings_siteLanguage' => $settings->siteLanguage(),
            'settings_minimumAge' => $settings->minimumAge(),
            'settings_templateURL' => $settings->getTemplatesURL(),
            'settings_timezone' => $settings->siteTimeZone(),
            'settings_templatesFolder' => $settings->templatesFolder(),
        );

        // check if user class has been initiated
        if ($user->getGroup() != null) {
            $userArray = array(
                'user_username' => $user->getUsername(),
                'user_id' => $user->getID(),
                'user_email' => $user->getEmail(),
                'user_firstName' => $user->getFirstName(),
                'user_lastName' => $user->getLastName(),
                'user_dateJoined' => $user->getDateJoined(),
                'user_daysSinceJoined' => $user->getDateJoinedText(),
                'user_groupName' => $user->getGroup()->getName(),
                'user_XP' => $user->getXP(),
                'user_lostXP' => $user->getLostXP(),
                'user_lastLogin' => $user->getLastLoginTime(),
                'user_lastLoginText' => $user->getLastLoginText(),
                'user_age' => $user->getAge(),
                'user_birthday' => $user->getBirthDate(),
                'user_browserName' => $user->devices()->getCurrentDevice()->getBrowserName(),
                'user_browserVersion' => $user->devices()->getCurrentDevice()->getBrowserVersion(),
                'user_ip' => $user->devices()->getCurrentDevice()->getIP(),
                'user_os' => $user->devices()->getCurrentDevice()->getOS(),
            );

            $vars = array_merge($userArray, $vars);
        }

        // check if any custom arrays has been supplied, then apply it to the current array
        if ($this->customVariables != null) {
            $vars = array_merge($vars, $this->customVariables);
        }

        // convert variables to actual values
        $newFile = $this->getTranslator()->replaceTags("{:", "}", $file, $vars);

        // return the new file
        return $newFile;
    }

    /**
     * in addition to the main reserved characters, through this function you can
     * add new characters by supplying it with an array of strings
     * Ex: array('[my_name]' , 'developer');
     * @param array $varArray
     * @return $this
     */
    public function setCustomReservedCharacters($varArray)
    {

        // check if array has been supplied
        if (!is_array($varArray)) {
            $this->killViewer($this->getTranslator()->translateText("incorrectViewerInformation"));
        }

        // set the current custom variables to this array
        $this->customVariables = $varArray;

        return $this;
    }

    /**
     * kill the view controller and the script with a custom message
     * @param string $msg
     */
    private function killViewer($msg)
    {

        // init the required global variables
        global $message, $settings;

        $_SESSION['siteTemplateURL'] = $settings->getTemplatesURL();
        $message->customKill("View Controller Error", $msg, $settings->getTemplatesPath());
    }

    private function deleteFile($file)
    {

        // init the required global variables
        global $settings;

        if (!unlink($settings->getTemplatesCachePath() . $file)) {
            $this->killViewer($this->getTranslator()->translateText("cachedFileError"));
        }
    }

    /**
     * get the translator class
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * translate a text from the actual INI file
     * @param $text
     * @return string
     */
    public function translateText($text)
    {
        return $this->getTranslator()->translateText($text);
    }

}