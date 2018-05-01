<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 1/11/2017
 * Time: 7:38 PM
 */

namespace ALS;
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file."); // disable direct access to the file.

class Message
{

    var $msg; // store the error text and type
    private $success; // store the success message
    const __default = self::Warning; // set the default error type to a warning.
    const Fatal = 1; // the error message is a fatal error.
    const Warning = 2; // the error message is just a warning.
    const Error = 3; // the error message is for the client instead of the administration

    /**
     * init the class
     */
    function init()
    {
        // check if any error has been posted to the session and if true then pull it and clear the session.
        if (!empty($_SESSION["error"])) {
            $this->msg = $_SESSION["error"];
            unset($_SESSION["error"]);
        }

        if (!empty($_SESSION['success'])) {
            $this->success = $_SESSION["success"];
            unset($_SESSION["success"]);
        }
    }

    /**
     * @param string $msg
     * @param array $tracer
     */
    private function fatalKill($msg, $tracer)
    {
        // kill the script with a custom message
        echo '<b>' . $this->readErrorType(1) . ': </b>' . $msg . '<br/><br/>';

        // print the backtrace
        echo "<b>Back-Trace</b><br /><hr />";
        $first = true;
        foreach ($tracer as $value => $trace) {

            // check if first then skip
            if ($first) {
                $first = false;
                continue;
            }

            // check if error has been called from this class then avoid it
            $file = basename($trace['caller']);
            if ($file == "message.php" && $trace['function'] == "backtrace") {
                continue;
            }

            // print the error
            echo '<b>File:</b> ' . $trace['file'] . ' - line ' . $trace['line'] . '<br />';
            echo '<b>Class:</b> ' . $trace['caller'] . "<br />";
            echo '<b>Type:</b> ' . $trace['type'] . "<br />";
            echo '<b>Type:</b> ' . $trace['function'] . "<br />";
            echo "<hr>";
        }

        die();
    }

    private function backtrace()
    {
        $backtrace = debug_backtrace();

        $output = '';
        $outputArray = [];
        $i = 0;
        foreach ($backtrace as $bt) {
            $args = '';
            foreach ($bt['args'] as $a) {
                if (!empty($args)) {
                    $args .= ', ';
                }
                switch (gettype($a)) {
                    case 'integer':
                    case 'double':
                        $args .= $a;
                        break;
                    case 'string':
                        $args .= "\"$a\"";
                        break;
                    case 'array':
                        $args .= 'Array(' . count($a) . ')';
                        break;
                    case 'object':
                        $args .= 'Object(' . get_class($a) . ')';
                        break;
                    case 'resource':
                        $args .= 'Resource(' . strstr($a, '#') . ')';
                        break;
                    case 'boolean':
                        $args .= $a ? 'TRUE' : 'FALSE';
                        break;
                    case 'NULL':
                        $args .= 'Null';
                        break;
                    default:
                        $args .= 'Unknown';
                }
            }

            // add the results to the array
            $outputArray[$i] = Array(
                "file" => @$bt['file'],
                "line" => @$bt['line'],
                "caller" => @$bt['class'],
                "type" => @$bt['type'],
                "function" => @$bt['function'],
                "args" => $args
            );
            $output .= '<br />';
            $output .= '<b>file:</b> ' . @$bt['file'] . ' - line ' . @$bt['line'] . '<br />';
            $output .= '<b>call:</b> ' . @$bt['class'] . @$bt['type'] . @$bt['function'] . '(' . $args . ')<br />';
            $i++;
        }
        return $outputArray;
    }

    /**
     * Set an error message to be displayed to the user
     * @param $msg
     * @param $type
     * @param string $fileName / include the file name that the error has occurred in
     * @param int $lineNumber / include the line number that the error has occurred in
     */
    function setError($msg, $type, $fileName = "", $lineNumber = 0)
    {
        if (!empty($msg)) { // check if the message is not empty
            if ($type == 3 || $type == 4) { // check if it's a user error
                $array = array(
                    "msg" => $msg,
                    "type" => $type,
                );
                $this->msg = $array;
                $_SESSION["error"][] = $array;
                return;
            } else if ($type == 1) { // if FATAL error, then kill the script instantly
                $tracer = $this->backtrace();
                $this->fatalKill($msg, $tracer);
            } else {
                $array = array(
                    "msg" => $msg,
                    "type" => $type,
                    "fileName" => $fileName,
                    "lineNumber" => $lineNumber,
                );
                $this->msg = $array;
                $_SESSION["error"][] = $array;
            }
        }
    }

    /**
     * set the success message
     * @param $msg
     */
    function setSuccess($msg)
    {

        // check for empty string
        if (empty($msg)) {
            return;
        }

        // create the array object
        $array = array(
            "msg" => $msg,
        );

        // store the current message string to session
        $this->success = $array;
        $_SESSION['success'][] = $array;
    }

    /**
     * @return bool|array|string
     */
    function getSuccess()
    {

        // set the current success array to object
        $data = $_SESSION["success"];
        $size = sizeof($data);

        if ($data != null && is_array($data) && $size > 1) {
            return $data;
        } else if (is_array($data) && $size == 1) {
            return $data[0]['msg'];
        } else {
            return false;
        }
    }

    /**
     * Print the total success messages to the user UI
     */
    function printSuccess()
    {

        // set the current success array to object
        $data = $_SESSION["success"];
        $size = sizeof($data);

        // check if data is empty
        if (is_null($data) || $size == 0) {
            return;
        }

        foreach ($data as $key => $value) {
            if ($key != 0) echo "<br>";
            echo $value['msg'];
        }
    }

    /**
     * check to see if any error has occurred
     */
    function anyError()
    {
        return !empty($this->msg) && !empty($this->msg["msg"]);
    }

    /**
     * check if any success message were to be found
     * @return bool
     */
    function is_success()
    {
        return !empty($this->success) && !empty($this->success["msg"]);
    }

    /**
     * get the total errors that's been added and return an array or string
     * @param int $type
     * @return array|bool|string
     */
    function getError($type = 0)
    {

        // check for empty session message
        $data = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        if (empty($data) || empty($data[0]['msg'])) {
            return "";
        }

        // hold the messages array
        $messages = array();

        // loop throw each case
        if ($type == 0) {

            foreach ($data as $key => $value) {
                $messages[] = $value['msg'];
            }

        } else if ($type == 1) {

            foreach ($data as $key => $value) {
                if ($value['type'] == 1) {
                    $messages[] = $value['msg'];
                }
            }

        } else if ($type == 2) {

            foreach ($data as $key => $value) {
                if ($value['type'] == 2) {
                    $messages[] = $value['msg'];
                }
            }

        } else if ($type == 3) {

            foreach ($data as $key => $value) {
                if ($value['type'] == 3) {
                    $messages[] = $value['msg'];
                }
            }

        } else {
            return false;
        }

        // check if array size is equal too 1
        if (sizeof($messages) == 1) {
            $messages = $messages[0];
        }

        return $messages;
    }

    /**
     * @param int $type // 0 = (default) all errors , 1 = only fatal errors , 2 = only warnings
     * @return mixed
     */
    function printError($type = 0)
    {
        $data = isset($_SESSION['error']) ? $_SESSION['error'] : null;
        if (empty($data) || empty($data[0]['msg'])) {
            echo "";
            return;
        }

        if ($type == 0) { // get all the errors
            foreach ($data as $key => $value) {
                if ($key != 0) echo "<br>";

                // check if line is null or empty
                if (empty($value['lineNumber'])) {
                    $line = 0;
                } else {
                    $line = $value['lineNumber'];
                }

                echo '<b>' . $this->readErrorType($value['type']) . ': </b>' . $value['msg'] . ' (<b> ' . $value['fileName'] . '</b> on Line <b>' . $line . '</b> )';
                if ($value["type"] == 1) die;
            }
        } else if ($type == 2) { // get all the errors with a type value of 2
            foreach ($data as $key => $value) {
                if ($value['type'] == 2) {
                    if ($key != 0) echo "<br>";
                    echo '<b>' . $this->readErrorType($value['type']) . ': </b>' . $value['msg'] . ' (<b> ' . $value['fileName'] . '</b> on Line <b>' . $value['lineNumber'] . '</b> )';
                }
            }
        } else if ($type == 1) { // get all the errors with a type value of 1
            foreach ($data as $key => $value) {
                if ($value['type'] == 1) {
                    if ($key != 0) echo "<br>";
                    echo '<b>' . $this->readErrorType($value['type']) . ': </b>' . $value['msg'] . ' (<b> ' . $value['fileName'] . '</b> on Line <b>' . $value['lineNumber'] . '</b> )';
                }
            }
        } else if ($type == 3) { // get all the errors with a type value of 3
            foreach ($data as $key => $value) {
                if ($value['type'] == 3) {
                    if ($key != 0) echo "<br>";
                    echo '<b>' . $this->readErrorType($value['type']) . ': </b>' . $value['msg'];
                }
            }
        }
    }

    /**
     * return the current error type
     * @return integer
     */
    static function getErrorType()
    {
        return $_SESSION["error"]["type"];
    }

    /**
     * read the actual meaning of the occurred error
     * @param $type
     * @return string
     */
    static function readErrorType($type)
    {
        $error = "";

        switch ($type) {
            case "1";
                $error = "Fatal error";
                break;
            case "2";
                $error = "Warning";
                break;
            case "3";
                $error = "Error";
                break;
        }
        return $error;
    }

    /**
     * kill the script with a custom message
     * @param $message
     * @param $file
     * @param int $line
     */
    function kill($message, $file, $line = 0)
    {
        if ($line == 0) {
            die ('<b>Fatal Error : </b>' . $message . ' (<b> ' . $file . '</b> )');
        } else {
            die ('<b>Fatal Error : </b>' . $message . ' (<b> ' . $file . '</b> on Line <b>' . $line . '</b> )');
        }
    }

    function customKill($title, $message, $theme_url)
    {
        $_SESSION['theme_url'] = $theme_url;
        $_SESSION['err_msg'] = $message;
        $_SESSION['err_title'] = $title;
        header('location: error.php');
        die();
    }

}

$message = new Message();