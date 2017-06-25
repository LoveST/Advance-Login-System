<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/12/2017
 * Time: 11:28 AM
 */

namespace ALS\ViewController;

use ALS\Translator\Translator;

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
     */
    public function loadView($templateName)
    {

        // init the required global variables
        global $browser, $config, $captcha, $passwordManager, $message, $settings, $user, $functions, $mail, $database, $mailTemplates, $admin, $browser, $profileManager, $session;

        // check if view is accessible
        if (!$this->isViewAccessible($templateName)) {
            $this->killViewer("the required template does not exist, or it's not readable.");
        }

        // generate a unique id for the file
        $this->uniqueID = md5(uniqid(rand(), true));
        $fileExtension = $ext = pathinfo($templateName, PATHINFO_EXTENSION);

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
            $this->killViewer("Error while loading the template");
        }

        // delete the temporary template file
        $this->deleteFile($this->requiredTemplate);

        // print the timestamp if enabled
        if($settings->siteLoadingTimestamp()){
            echo 'Page generated in ' . $settings->initTimeStamp() . ' seconds.';
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
        global $settings, $user, $functions, $message;

        $vars = array(
            '{:username}' => $user->getUsername(),
            '{:user_id}' => $user->getID(),
            '{:user_email}' => $user->getEmail(),
            '{:user_firstName}' => $user->getFirstName(),
            '{:user_lastName}' => $user->getLastName(),
            '{:siteURL}' => $settings->siteURL(),
            '{:siteName}' => $settings->siteName(),
            '{:siteEmail}' => $settings->siteEmail(),
            '{:templateURL}' => $settings->getTemplatesURL(),
        );

        // check if any custom arrays has been supplied, then apply it to the current array
        if ($this->customVariables != null) {
            $vars = array_merge($vars, $this->customVariables);
        }

        // convert variables to actual values
        $newFile = strtr($file, $vars);

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
            $this->killViewer("Incorrect information has been supplied to the viewer");
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

        $message->customKill("View Controller Error", $msg, $settings->siteTheme());

    }

    private function deleteFile($file)
    {

        // init the required global variables
        global $settings;

        if (!unlink($settings->getTemplatesCachePath() . $file)) {
            $this->killViewer("Error handling the cached template");
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