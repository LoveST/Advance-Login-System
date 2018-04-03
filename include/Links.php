<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 4/1/2018
 * Time: 12:13 AM
 */

namespace ALS;


class Links
{

    private $links = null;

    /**
     * Initiate the current class
     */
    public function init()
    {

        // read the links from the database
        $this->readDatabaseLinks();

        // translate the characters
        $this->translateCharacters();
    }

    /**
     * Load the required data links from the database
     */
    private function readDatabaseLinks()
    {
        // get the required global variables
        global $database;

        // call the database
        $sql = "SELECT * FROM " . TBL_LINKS;

        // request the data
        $results = $database->getQueryResults($sql);

        // get the data
        foreach ($database->getQueryEffectedRows($results, true) as $row) {

            // store the needed variables
            $fieldName = $row[TBL_LINKS_NAME];
            $fieldValue = $row[TBL_LINKS_VALUE];

            // append to the links array
            $this->links[$fieldName] = $fieldValue;
        }
    }

    /**
     * Get all the links in the database as an array
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Get a specific link name value
     * @param string $linkName
     * @return string
     */
    public function getLink($linkName)
    {
        if (array_key_exists($linkName, $this->links)) {
            return $this->links[$linkName];
        } else {
            return "NULL";
        }
    }

    /**
     * Translate the current data links and convert the custom characters
     * Example : {:siteURL}index.php ===> http://localhost/index.php
     */
    private function translateCharacters()
    {
        // loop throw the current links array
        $index = 0;
        foreach ($this->links as $name => $value) {

            // do a while loop if any tags found
            while ($this->anyTags($value)) {

                // replace the tags
                $value = $this->replaceTags($value);
            }

            // update the current list
            $this->links[$name] = $value;
            $index++;
        }
    }

    /**
     * Get the value of a specific character
     * @param $char
     * @return string
     */
    private function getCharacterValue($char)
    {
        // get the required global variables
        global $settings;

        // store the required character text
        $value = "";

        // switch on $char
        switch ($char) {
            case "siteURL";
                $value = $settings->siteURL();
                break;
            default:
                $value = "{null}";
                break;
        }

        // return the stored value
        return $value;
    }

    /**
     * Replaces the tags in a string using the provided replacement values.
     * @author https://stackoverflow.com/users/4265352/axiac (Axiac)
     * @param string $template the string that contains the tags
     * @return string
     */
    private function replaceTags($template)
    {
        return preg_replace_callback(
            '#' . preg_quote("{:") . '(.*)' . preg_quote("}") . '#U',
            function (array $matches) {

                // get the character
                $key = $matches[1];

                // translate the character
                $charValue = $this->getCharacterValue($key);

                // return the translated results
                return $charValue;
            },
            $template
        );
    }

    /**
     * Check if a specific text includes a reserved character
     * @param string $template
     * @return boolean
     */
    private function anyTags($template)
    {
        // hold the required boolean
        $found = false;

        // get the first required character in the text
        $firstChar = substr($template, 0, 2);

        // check if matches
        if ($firstChar == "{:") {
            if (strpos(substr($template, 2), "}") !== false) {
                $found = true;
            }
        }

        // return the results
        return $found;
    }

}