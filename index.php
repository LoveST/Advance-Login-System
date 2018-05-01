<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 4/20/2018
 * Time: 1:02 AM
 */

namespace ALS;

class ALS
{
    var $_Root = "Public\\";
    private $_currentDir = "";
    private $directories = array();

    public function __construct()
    {
        // load the config
        $this->loadConfig();

        // load the directories
        $this->loadDirectories();

        // load the core
        $this->loadCore();

        // load the view controller
        $this->loadController();
    }

    /**
     * load the config file from settings
     */
    public function loadConfig()
    {
        // load the required file
        include_once "Settings/config.php";
    }

    public function loadDirectories()
    {
        // parse the ini file
        $this->directories = parse_ini_file("Settings/Directories.ini");
    }

    /**
     * Call a specific path from the directories list
     * @param string $dirName
     * @return string
     */
    public function getDirectory($dirName)
    {
        if (isset($this->directories[$dirName]) && !empty($this->directories[$dirName])) {
            return $this->directories[$dirName];
        } else {
            return "";
        }
    }

    /**
     * Load the framework main classes and functions
     */
    public function loadCore()
    {
        // load the required file
        include_once "Core/Core.php";

        // create a new instance of the Core class
        $core = new Core();

        // init the core classes
        $core->initClasses();
    }

    /**
     * Load the required content from the Public folder
     */
    public function loadController()
    {
        // init the required global variables
        global $message, $functions;

        // hold the current directory
        $requiredDir = "";

        if (isset($_GET['__dir'])) {
            // get the current
            $currentDir = strip_tags(htmlspecialchars($_GET['__dir']));

            // check if special path found
            if (strpos($currentDir, "/") !== false && isset($currentDir)) {
                $currentDirList = explode("/", $currentDir);

                // check if currentDie list has more than 1 sub path
                $listCount = count($currentDirList);
                if ($listCount >= 1) {
                    if ($listCount > 1) {
                        // loop throw each sub path
                        for ($i = 0; $i < $listCount; $i++) {

                            // check if first sub, then translate it to special directory
                            if ($i == 0) {
                                $requiredDir .= $this->getDirectory($currentDirList[$i]) . $this->getSubLine();
                                continue;
                            }

                            // check if last index reached
                            if (($i + 1) == $listCount) {
                                $requiredDir .= $currentDirList[$i];
                                continue;
                            }

                            $requiredDir .= $currentDirList[$i] . $this->getSubLine();
                        }
                    } else {
                        $requiredDir = $currentDir;
                    }
                } else {
                    $requiredDir = $currentDir;
                }
            } else {
                $requiredDir = $currentDir;
            }
        } else {
            $requiredDir = "index.php";
        }

        // check if file exists & include it
        $newPath = FRAMEWORK_PATH . $this->_Root . $requiredDir;

        // check if path ends with a sub line
        if ($functions->stringEndsWith($newPath, $this->getSubLine())) {
            $this->_currentDir = $newPath;
            $newPath .= "index.php";
        } else if (is_dir($newPath)) {
            $newPath .= $this->getSubLine();
            $this->_currentDir = $newPath;
            $newPath .= "index.php";
        }

        // check if file exists and its not a directory
        if (file_exists($newPath) && !is_dir($newPath)) {
            $functions->loadFile($newPath);
        } else {

            $message->kill("Required file does not exist " . $requiredDir, "Core");
        }
    }

    /**
     * get the required sub line for the current server's os
     * @return string
     */
    function getSubLine()
    {
        // check the servers current OS
        if (PHP_OS == "Linux") {
            $sub = "/";
        } else {
            $sub = "\\";
        }

        return $sub;
    }

}

/**
 * Initiate the framework
 */
$GLOBALS['core'] = $core = new ALS();