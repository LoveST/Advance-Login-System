<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/17/2017
 * Time: 4:48 PM
 */

namespace ALS\User;

use ALS\Message;

class Group
{

    private $data; // to-store all the group data

    function __construct($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * get the group level
     * @return int
     */
    function getLevel()
    {
        if (empty($this->data[TBL_LEVELS_LEVEL])) {
            return "";
        } else {
            return $this->data[TBL_LEVELS_LEVEL];
        }
    }

    /**
     * check if the group is a default one
     * @return bool
     */
    function isDefault()
    {
        if (empty($this->data[TBL_LEVELS_DEFAULT_GROUP])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * get the group main name
     * @return string
     */
    function getName()
    {
        if (empty($this->data[TBL_LEVELS_NAME])) {
            return "";
        } else {
            return $this->data[TBL_LEVELS_NAME];
        }
    }

    /**
     * get the required group data as needed by using the column name
     * @param $dataType
     * @return mixed
     */
    function get($dataType)
    {
        // check if in array
        if (array_key_exists($dataType, $this->data)) {
            return $this->data[$dataType];
        } else {
            return false;
        }
    }

    /**
     * get the group permissions
     * @return array
     */
    function getPermissions()
    {
        // separate every single permission after a | sign and store it in an array and return it
        $permissions = explode(",", $this->data[TBL_LEVELS_PERMISSIONS]);

        // check if no array is available
        if (!is_array($permissions)) {
            return array();
        } else {
            return $permissions;
        }
    }

    /**
     * check if a certain permission exists
     * @param string $permission
     * @return bool
     */
    function permissionExist($permission)
    {
        // check if $permission is empty or not string
        if (!is_string($permission) || empty($permission)) {
            return false;
        }

        // check if permissions array contains the current permission
        if (in_array($permission, $this->getPermissions())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add a new permission/permissions to the group
     * @param string|array $perm
     * @return bool
     */
    function addPermission($perm)
    {
        // init the global variables
        global $database, $message, $user;

        // check if the user has permission
        if (!$user->hasPermission("als_groups_addPermission")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // check if the supplied args are empty
        if (empty($perm) || $perm == null) {
            return false;
        }

        // get the current permissions
        $newPermissions = $this->getPermissions();
        $totalAdded = 0;

        // check if $perm is a string or array
        if (is_string($perm) && !is_array($perm)) {

            // check if permission already exists
            if ($this->permissionExist($perm)) {
                return false;
            }

            // get the current permissions and pass it the new variable
            array_push($newPermissions, $perm);

        } else if (is_array($perm)) {

            // loop throw each permission
            foreach ($perm as $permission) {

                // check if permission is empty
                if (empty($permission)) {
                    continue;
                }

                // check if string contains a strict variable ','
                if (strpos($permission, ",") !== false) {
                    continue;
                }

                // check if permission already exists
                if ($this->permissionExist($permission)) {
                    continue;
                }

                // pass the current permission to the array
                array_push($newPermissions, $permission);
                $totalAdded += 1;
            }

        } else {
            return false;
        }

        // serialize the array
        $newPermissions = implode(",", $newPermissions);

        // setup the sql query
        $sql = "UPDATE " . TBL_LEVELS . " SET " . TBL_LEVELS_PERMISSIONS . " = '" . $newPermissions . "' WHERE " . TBL_LEVELS_NAME . " = '" . $this->getName() . "'";

        // query the results
        $database->getQueryResults($sql);

        // Update the records
        if (!$this->updateGroup(TBL_LEVELS_PERMISSIONS, $newPermissions)) {
            $message->setError("SQL Update Error", Message::Error);
            return false;
        }

        // check if $perm is an array and if any changes were to be found
        if (is_array($perm) && $totalAdded == 0) {
            $message->setError("No changes were made", Message::Error);
            return false;
        }

        // if no errors then return true with a success message
        if ($totalAdded == 0) {
            $message->setSuccess("New Permission has been added to the group '" . $this->getName() . "'");
        } else {
            $message->setSuccess("Total of " . $totalAdded . " permission\s has been added to the group '" . $this->getName() . "'");
        }

        return true;
    }

    /**
     * remove permission/permissions from this group
     * @param string|array $perm
     * @return bool
     */
    function removePermission($perm)
    {
        // init the global variables
        global $database, $message, $user;

        // check if the user has permission
        if (!$user->hasPermission("als_groups_removePermission")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // check if the supplied args are empty
        if (empty($perm) || $perm == null) {
            return false;
        }

        // get the current permissions
        $newPermissions = $this->getPermissions();
        $totalRemoved = 0;

        // check if $perm is a string or array
        if (is_string($perm) && !is_array($perm)) {

            // check if permission already exists
            if (!$this->permissionExist($perm)) {
                return false;
            }

            // get the current permissions and remove the required variable
            $offset = array_search($perm, $newPermissions);
            unset($newPermissions[$offset]);

        } else if (is_array($perm)) {

            // loop throw each permission
            foreach ($perm as $permission) {

                // check if permission is empty
                if (empty($permission)) {
                    continue;
                }

                // check if string contains a strict variable ','
                if (strpos($permission, ",") !== false) {
                    continue;
                }

                // check if permission already exists
                if (!$this->permissionExist($permission)) {
                    continue;
                }

                // remove the current permission from the array
                $offset = array_search($permission, $newPermissions);
                unset($newPermissions[$offset]);
                $totalRemoved += 1;
            }

        } else {
            return false;
        }

        // serialize the array
        $newPermissions = implode(",", $newPermissions);

        // setup the sql query
        $sql = "UPDATE " . TBL_LEVELS . " SET " . TBL_LEVELS_PERMISSIONS . " = '" . $newPermissions . "' WHERE " . TBL_LEVELS_NAME . " = '" . $this->getName() . "'";

        // query the results
        $database->getQueryResults($sql);

        // check for any errors
        if ($database->anyError()) {
            $message->setError("SQL Update Error", Message::Error);
            return false;
        }

        // check if $perm is an array and if any changes were to be found
        if (is_array($perm) && $totalRemoved == 0) {
            $message->setError("No changes were made", Message::Error);
            return false;
        }

        // if no errors then return true with a success message
        if ($totalRemoved == 0) {
            $message->setSuccess("The permission " . $perm . " has been removed from the group '" . $this->getName() . "'");
        } else {
            $message->setSuccess("Total of " . $totalRemoved . " permission\s has been removed from the group '" . $this->getName() . "'");
        }

        return true;
    }

    /**
     * Update the current group records in the database
     * @param $field
     * @param $value
     * @return bool
     */
    function updateGroup($field, $value)
    {
        // init the required globals
        global $database;

        // secure the var's
        $field = $database->secureInput($field);
        $value = $database->secureInput($value);

        // setup the initial sql query content
        $content = "";

        // check if arrays has been submitted
        if (is_array($field)) {

            // get the length of the array of fields
            $length = count($field);
            $i = 1;

            // loop throw the array and add each record
            foreach ($field as $currentField => $currentValue) {

                // add the elements to the query string content
                $content .= $currentField . " = '" . $currentValue . "'";

                // check if not last then add a comma
                if ($i < $length) {
                    $content .= ", ";
                    $i++;
                }
            }
        } else {

            // check if value is empty
            if ($value == null) {
                return false;
            }

            // setup the default mysql 1 time update
            $content .= $field . " = '" . $value . "'";
        }

        // setup and complete the sql query
        $sql = "UPDATE " . TBL_LEVELS . " SET " . $content . " WHERE " . TBL_LEVELS_NAME . " = '" . $this->getName() . "'";

        // query the results
        $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        } else {
            return true;
        }
    }

}