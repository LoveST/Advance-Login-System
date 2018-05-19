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

    }

    public function _init()
    {
        // define all the global variables
        global $settings;

        // set the default site language
        $this->setLanguage($settings->siteLanguage());

        // define the new language
        define("LANGUAGE", $this->lang);

        // set the languages folder path
        $this->folderPath = "Languages" . $settings->getSubLine();

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
        // translate the file and return it
        $file = $this->replaceTags("{t}", "{/t}", $file, $this->langFile);
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
     * translate an entire file links with the needed tags
     * @author https://stackoverflow.com/users/4265352/axiac (Axiac)
     * @param $file
     * @return string
     */
    public function translateLinks($file)
    {
        return preg_replace_callback(
            '#' . preg_quote("{l}") . '(.*)' . preg_quote("{/l}") . '#U',
            function (array $matches) {

                // get the character
                $key = $matches[1];

                // add the link to the buffer
                global $links;
                $links->requestLink($key . "");

                // return the translated results
                return "<? echo \$links->getLink('" . $key . "', false); ?>";
            },
            $file
        );
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
                    if (substr($key, 0, 2) == "$$") {                 // '$$'
                        return $this->tr_globVariable($key);
                    } else if (substr($key, 0, 4) == "msgc") {        // 'msgc'
                        return $this->tr_msgC($key);
                    } else if (strpos($key, 'msg') !== false) {         // 'msg'
                        return $this->tr_msg($key);
                    } else if (strpos($key, 'glob') !== false) {        // 'glob'
                        return $this->tr_glob($key);
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

    private function tr_globVariable($string)
    {
        // grab the variable
        $string = substr($string, 2);

        // set the replacement
        $string = '$GLOBALS["' . $string . '"]';

        // return the new string
        return $string;
    }

    private function tr_glob($string)
    {
        // grab the variable
        $string = substr($string, 5);

        // check if variable contains ',' then separate them
        if (strpos($string, ',') !== false) {
            $newStrings = explode(',', $string);
            $variable = "";

            // loop throw each one
            $i = 0;
            foreach ($newStrings as $var) {
                if ($i != 0)
                    $variable .= ",";
                $variable .= "$" . $var;
                $i++;
            }
        } else {
            $variable = "$" . $string;
        }

        // set the replacement
        $string = "<? global " . $variable . "; ?>";

        // return the new string
        return $string;
    }

    private function tr_msgC($string)
    {
        // grab the variable
        $string = substr($string, 5);

        // check if it contains ...
        if (strpos($string, '...') !== false) {
            $newStrings = explode('...', $string);
            $string = "GLOBALS['" . $newStrings[0] . "']->" . $newStrings[1];
        }

        // set the required return statement
        $string = "<? echo $" . $string . "; ?>";

        // return the new string
        return $string;
    }

    private function tr_msg($string)
    {
        // grab the variable
        $string = substr($string, 4);

        // check if the variable contains '[' array start char
        if (strstr($string, "[", true) && strstr($string, "]")) {
            // set the required return statement
            $beforeArray = strstr($string, "[", true);
            $arrayVars = strstr($string, "[");
            $string = '<? echo $GLOBALS["' . $beforeArray . '"]' . $arrayVars . ';?>';
        } else {
            // set the required return statement
            $string = '<? echo $GLOBALS["' . $string . '"];?>';
        }

        // return the new string
        return $string;
    }
}

$translator = new Translator();