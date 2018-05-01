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

    private $links = array();
    private $linksList = null;
    private $linksSize = 0;

    /**
     * Get all the links in the database as an array
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Add a link to the buffer list or access one link strictly
     * @param string $linkName
     * @param bool $toBuffer
     * @return string
     */
    public function requestLink($linkName, $toBuffer = true)
    {
        // get the required global variables
        global $database;

        // check if toStore is enabled
        if ($toBuffer) {

            // check if link already been defined
            if (array_key_exists($linkName, $this->links)) {
                return $this->links[$linkName];
            }

            // add to the current link list and add 1 to the size
            $this->linksList[] = $linkName;
            $this->linksSize++;
        } else {

            // prepare the sql query
            $sql = "SELECT * FROM " . TBL_LINKS . " WHERE " . TBL_LINKS_NAME . " = '" . $linkName . "'";
            $results = $database->getQueryResults($sql);

            // check if no results are found
            if ($database->getQueryNumRows($results, true) <= 0) {
                return "";
            }

            // get the current row
            $row = $database->getQueryEffectedRow($results, true);
            $value = $row[TBL_LINKS_VALUE];

            // do a while loop if any tags found
            while ($this->anyTags($value)) {

                // replace the tags
                $value = $this->replaceTags($value);
            }

            // return the required link
            $this->links[$linkName] = $value;
            return $value;
        }
    }

    public function getLink($linkName)
    {
        // check if link exists in the array
        if (array_key_exists($linkName, $this->links)) {
            return $this->links[$linkName];
        } else {
            return "{LinkError}";
        }
    }

    /**
     * Translate the current data links and convert the custom characters
     * Example : {:siteURL}index.php ===> http://localhost/index.php
     */
    public function translateCharacters()
    {
        // get the required global variables
        global $database;

        // store the sql select list
        $list = "";

        // check if the current links array is empty
        if (empty($this->linksList)) {
            return;
        }

        // prepare the sql select list
        for ($i = 0; $i < $this->linksSize; $i++) {
            if ($i == 0) {
                $list .= " \"" . $this->linksList[$i] . "\" ";
            } else {
                $list .= ", \"" . $this->linksList[$i] . "\" ";
            }
        }

        // query the results
        $sql = "SELECT * FROM " . TBL_LINKS . " WHERE " . TBL_LINKS_NAME . " In (" . $list . ")";
        $results = $database->getQueryResults($sql);

        // check if no results are found
        if ($database->getQueryNumRows($results, true) <= 0) {
            return;
        }

        // reset the links list and set it to array
        $this->linksList = null;
        $this->linksSize = 0;
        $this->links = array();

        // loop throw each link
        foreach ($database->getQueryEffectedRows($results, true) as $link) {

            // get the required variables
            $name = $link[TBL_LINKS_NAME];
            $value = $link[TBL_LINKS_VALUE];

            // do a while loop if any tags found
            while ($this->anyTags($value)) {

                // replace the tags
                $value = $this->replaceTags($value);
            }

            // insert into the links array
            $this->links[$name] = $value;
        }

        //test
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

$links = new Links();