<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/23/2017
 * Time: 3:15 PM
 */

namespace ALS;


class Translator
{

    private $lang;
    private $folderPath;
    private $cachePath;
    private $langFile;

    function __construct()
    {

        // define all the global variables
        global $settings;

        // set the default site language
        $this->setLanguage($settings->siteLanguage());

        // define the new language
        define("LANGUAGE", $this->lang);

        // set the languages folder path
        $this->folderPath = "languages" . $settings->getSubLine();

        // set the cache folder path
        $this->cachePath = "cache" . $settings->getSubLine();

        // read and parse the INI language file
        $this->langFile = $this->readLanguageFile();

    }

    public function initSessionLanguage()
    {

        // define all the global variables
        global $session;

        // check if session has any preferred language
        if ($sessionLanguage = $session->getSessionLanguage()) {

            // set the session's preferred language
            $this->setLanguage($sessionLanguage);

        }
    }

    /**
     * translate an entire file with the needed tags
     * @param $file
     * @return string
     */
    public function translateFile($file)
    {

        // set the needed REGEX
        $regex = "#{t}(.*?){/t}#";
        $langFile = $this->langFile;

        $file = $this->replaceTags("{t}", "{/t}", $file, $langFile);

        return $file;

    }

    /**
     * get or read the INI language file
     * @return array|bool
     */
    private function readLanguageFile()
    {

        // get the needed file
        $file = $this->folderPath . LANGUAGE . ".ini";

        // parse the ini file and store it in the class
        $file = parse_ini_file($file);

        // return the parsed file
        return $file;
    }

    /**
     * translate a text
     * @param string $text
     * @param null|array $parameters
     * @return string
     */
    public function translateText($text, $parameters = null)
    {
        // check if parameters are not empty
        if ($parameters) {

            // replace the special tags with the corresponding values
            return $this->replaceTags("%", "%", $this->langFile[$text], $parameters);

        } else {
            return $this->langFile[$text];
        }
    }

    /**
     * set the script language
     * @param string $lang
     * @return $this
     */
    public function setLanguage($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * add a secondary language file besides the the main script one
     * @param string $filePath
     * @return bool
     */
    function addSecondLanguageFile($filePath)
    {

        // check if file is null
        if ($filePath == null) {
            return false;
        }

        // check if file is readable
        if (!is_readable($filePath)) {
            return false;
        }

        // check if the file has an INI extension
        if (pathinfo($filePath, PATHINFO_EXTENSION) != "ini") {
            return false;
        }

        // try to parse the ini file
        if (!$file = parse_ini_file($filePath)) {
            return false;
        }

        // merge both arrays (the main one and this one)
        $this->langFile = array_merge($this->langFile, $file);

        return true;
    }

    /**
     * Replaces the tags in a string using the provided replacement values.
     * @author https://stackoverflow.com/users/4265352/axiac (Axiac)
     * @param string $ldelim the string used to mark the start of a tag (e.g. '{{')
     * @param string $rdelim the string used to mark the end of a tag (e.g. '}}')
     * @param string $template the string that contains the tags
     * @param array $replacements the values to replace the tags
     * @return string
     */
    function replaceTags($ldelim, $rdelim, $template, array $replacements)
    {
        return preg_replace_callback(
        // The 'U' flag prevents the .* expression to be greedy
        // and match everything from the first to the last tag
            '#' . preg_quote($ldelim) . '(.*)' . preg_quote($rdelim) . '#U',
            function (array $matches) use ($replacements) {
                // $matches contains the text pieces that matches the capture groups
                // in the regexp
                // $matches[0] is the text that matches the entire regexp
                // $matches[1] is the first capture group: (.*)
                $key = $matches[1];
                if (array_key_exists($key, $replacements)) {
                    // Replace the tag if a replacement value exists in the list
                    // check if a special Variable is present
                    return $replacements[$key];
                } else {
                    // Don't replace the tag if a value is not assigned for it
                    // check if a special Variable is present
                    if ($key[0] == "$" && $key[1] == "$") {
                        $variable = substr($key, 2);
                        $newReplacement = '$GLOBALS["' . $variable . '"]';
                        return $newReplacement;

                    } else if ($key[0] == "m" && $key[1] == "s" && $key[2] == "g") { // check if a print character exists

                        // check if command is 'msg' or 'msgc'
                        if ($key[3] == "c") { // class

                            $variable = substr($key, 5);
                            $newReplacement = "<? echo $" . $variable . "; ?>";

                        } else { // global variable

                            $variable = substr($key, 4);
                            $newReplacement = '<? echo $GLOBALS["' . $variable . '"];?>';

                        }

                        return $newReplacement;
                    } else {
                        return $matches[0];
                    }

                    // Alternatively, you can return a default placeholder string
                    // or return '' to remove the tag completely
                }
            },
            $template
        );
    }

}