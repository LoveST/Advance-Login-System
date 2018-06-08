<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 4/20/2018
 * Time: 1:02 AM
 */

namespace ALS;
error_reporting(-1);

class ALS
{
    var $_Root = "Public";
    private $_methodsPath = "Methods";
    private $_methodsCall = "Query";
    private $_currentDir = "";
    private $directories = array();
    static $_currentDirectory;

    public function __construct()
    {
        // init the main framework path

        // Start the session
        session_start();

        // load the Core class without initializing it
        include_once "Core/Core.php";

        // start the session timer timestamp
        $_executeStartTime = microtime(true);

        // load the directories
        $this->loadDirectories();

        // load the config
        $this->loadConfig();

        // check if current requested folder is in the reserved section
        if ($this->isReservedFolder($this->getRequestedFolder()) || $this->getRequestedFolder() == $this->_methodsCall) {

            // load the required content and
            $this->loadRequiredContent();
            return;
        } else {

            // load the core
            $this->loadCore();

            // load the view controller
            $this->loadController();

            // process the required templates and sent them to the browser
            global $viewController;
            $viewController->processViews();
        }

        // print the time stamp if enabled
        global $settings;
        if ($settings->siteLoadingTimestamp()) {
            echo "\n<br>Page generated in " . round((microtime(true) - $_executeStartTime), 4) . " seconds.";
        }
    }

    /**
     * load the config file from settings
     */
    public function loadConfig()
    {
        // load the required file
        require_once "Settings/config.php";
    }

    /**
     * Load the main required directories
     */
    public function loadDirectories()
    {
        // parse the ini file
        $this->directories = parse_ini_file("Settings/Directories.ini", true);
    }

    /**
     * Load a required file without further processing it throw the framework
     */
    public function loadRequiredContent()
    {
        // get the current requested path
        $currentRequestedDir = $this->secureInput($_GET["__dir"]);

        // set the required path and check if Methods path required
        if ($this->getRequestedFolder() == $this->_methodsCall) {
            $currentPath = FRAMEWORK_PATH . $this->queryToMethods($currentRequestedDir);
        } else {
            $currentPath = FRAMEWORK_PATH . $this->_Root . $this->getSubLine() . $currentRequestedDir;
        }

        // check if current path is a directory instead of a file
        if (is_dir($currentPath)) {
            echo "Access Denied";
            return;
        }

        // check if file exists
        if (!file_exists($currentPath)) {
            echo "Required file does not exist";
            return;
        }

        // check if required file is under Query directory
        if ($this->getRequestedFolder() == $this->_methodsCall) {

            // safely load the file
            include_once $currentPath;
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($currentPath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($currentPath));
            readfile($currentPath);
            exit();
        }
    }

    private function queryToMethods($string)
    {
        // split the string every '/'
        $stringList = explode("/", $string);

        // return the results
        return $this->_methodsPath . $this->getSubLine() . $stringList[1];
    }

    /**
     * @param $file
     * @return resource
     */
    function getMimeType($file)
    {
        return finfo_open($file);
    }

    /**
     * Call a specific path from the directories list
     * @param string $dirName
     * @param boolean $isReserved
     * @return string
     */
    public function getDirectory($dirName, $isReserved = false)
    {
        // set the required path
        if ($isReserved) {
            $subDir = "reserved_dir";
        } else {
            $subDir = "public_dir";
        }

        if (isset($this->directories[$subDir][$dirName]) && !empty($this->directories[$subDir][$dirName])) {
            return $this->directories[$subDir][$dirName];
        } else {
            return "";
        }
    }

    /**
     * Secure a given input
     * @param $input
     * @return string
     */
    public function secureInput($input)
    {
        return strip_tags(htmlspecialchars($input));
    }

    /**
     * Load the framework main classes and functions
     */
    public function loadCore()
    {
        // create a new instance of the Core class
        $core = new Core();
        $core->_ErrorKiller(ERROR_REPORTING);

        // init the core classes
        $core->initClasses();
    }

    // TODO fix wrong URL redirection
    // ex: http://localhost/als/index.php/admin/profile/logout.php
    // returning: Public/index.php
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
            $page = array_key_exists('__dir', $_GET) ? $_GET['__dir'] : null;
            $currentDir = $this->secureInput($page);

            // check if special path found
            if (strpos($currentDir, "/") !== false && isset($currentDir)) {
                $currentDirList = explode("/", $currentDir);

                // check if currentDie list has more than 1 sub path
                $listCount = count($currentDirList);

                if ($listCount >= 1) {
                    if ($listCount > 1) {

                        // set current folder static variable
                        ALS::$_currentDirectory = $currentDirList[0];

                        // check if first directory is defined in the directories list
                        if (!isset($this->directories['public_dir'][$currentDirList[0]]) || empty($this->directories['public_dir'][$currentDirList[0]])) {
                            $message->kill("Required file does not exist", "Core");
                        }

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
        $newPath = FRAMEWORK_PATH . $this->getSubLine() . $this->_Root . $this->getSubLine() . $requiredDir;

        // check if current path is a directory and if it exists
        // check if folder exists
        if (is_dir($newPath) && !file_exists($newPath)) {
            $message->kill("File Not Found", "Core");
            return;
        }

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
            $message->kill("Required file does not exist: " . $requiredDir, "Core");
        }
    }

    /**
     * Check if a given folder name is reserved
     * @param string $folderName
     * @return bool
     */
    function isReservedFolder($folderName)
    {
        if (in_array($folderName, $this->directories["reserved_dir"])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the current folder's name from the requested directory string
     * @return string
     */
    function getRequestedFolder()
    {
        // get the current requested directory
        $page = array_key_exists('__dir', $_GET) ? $_GET['__dir'] : null;
        $currentDir = $this->secureInput($page);

        // check if special character found
        if (isset($currentDir) && strpos($currentDir, "/") !== false) {

            // separate the string with each / char found
            $currentDirList = explode("/", $currentDir);

            return $currentDirList[0];
        } else {
            return "";
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
$GLOBALS['als'] = $als = new ALS();