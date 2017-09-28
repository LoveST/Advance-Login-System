<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/17/2017
 * Time: 4:42 PM
 */

namespace ALS;

use ALS\User\Group;

require_once "user/Group.php";

class Groups
{

    /**
     * load a certain group and return as a Group class
     * @param int|string $groupLevel
     * @return Group|bool
     */
    function loadGroup($groupLevel)
    {

        global $database, $message;

        // check if empty group level given
        if (empty($groupLevel)) {
            $message->setError("Group id/name cannot be empty", Message::Error);
            return false;
        }

        // check if groupLevel is an INT or String
        if (is_string($groupLevel) && !is_numeric($groupLevel)) {
            // try to load the group from database
            $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_NAME . " = '" . $groupLevel . "'";
        } else {
            // try to load the group from database
            $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_LEVEL . " = '" . $groupLevel . "'";
        }

        // get the sql results
        $result = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        // translate the results to data
        $row = $database->getQueryEffectedRow($result, true);

        // of the Group class
        return new Group($row);
    }

    /**
     * @param int $id
     * @param string $name
     * @param null|string|array $permissions
     * @return bool
     */
    function addGroup($id, $name, $permissions = null)
    {
        // init the global variables
        global $database, $message;

        // check for empty variables
        if (empty($id) || empty($name)) {
            $message->setError("Missing required parameters", Message::Error);
            return false;
        }

        // check if group id or name already exist
        if ($this->groupExists($id) || $this->groupExists($name)) {
            $message->setError("The group ID or Name already exist", Message::Error);
            return false;
        }

        // check if permissions is empty then set it to an empty string
        // if not then serialize the string or array to be ready to store in db
        if (is_null($permissions) || empty($permissions)) {
            $permissions = "";
        } else {
            $permissions = serialize($permissions);
        }

        // setup the sql query
        $sql =
            "INSERT INTO " . TBL_LEVELS . " (" . TBL_LEVELS_LEVEL . "," . TBL_LEVELS_NAME . "," . TBL_LEVELS_PERMISSIONS .
            ") VALUES (" . "$id, '$name', '$permissions'" . ")";
        //die($sql);
        // query the results
        $database->getQueryResults($sql);

        // check for any errors
        if ($database->anyError()) {
            $message->setError("SQL Update Error", Message::Error);
            return false;
        }

        // if no error then set the success message
        $message->setSuccess("The group '" . $name . "' with id '" . $id . "' has been created");
        return true;
    }

    function removeGroup()
    {

    }

    /**
     * Add a new permission/permissions to a certain group
     * @param string|array $perm
     * @param group|string $group
     * @return bool
     */
    function addPermission($perm, $group)
    {
        // init the global variables
        global $database, $message;

        // check if the supplied args are empty
        if (empty($perm) || empty($group) || $perm == null || $group == null) {
            return false;
        }

        // check to see if a string is supplied instead of a Class Object
        if (is_string($group)) {
            $group = $this->loadGroup($group);
        }

        // get the current permissions
        $newPermissions = $group->getPermissions();
        $totalAdded = 0;

        // check if $perm is a string or array
        if (is_string($perm) && !is_array($perm)) {

            // check if permission already exists
            if ($group->permissionExist($perm)) {
                return false;
            }

            // get the current permissions and pass it the new variable
            array_push($newPermissions, $perm);

        } else if (is_array($perm)) {

            // loop throw each permission
            foreach ($perm as $permission) {

                // check if permission already exists
                if ($group->permissionExist($permission)) {
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
        $newPermissions = serialize($newPermissions);

        // setup the sql query
        $sql = "UPDATE " . TBL_LEVELS . " SET " . TBL_LEVELS_PERMISSIONS . " = '" . $newPermissions . "' WHERE " . TBL_LEVELS_NAME . " = '" . $group->getName() . "'";

        // query the results
        $database->getQueryResults($sql);

        // check for any errors
        if ($database->anyError()) {
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
            $message->setSuccess("New Permission has been added to the group '" . $group->getName() . "'");
        } else {
            $message->setSuccess("Total of " . $totalAdded . " permission\s has been added to the group '" . $group->getName() . "'");
        }

        return true;
    }

    /**
     * remove permission/permissions from a certain group
     * @param string|array $perm
     * @param group|string $group
     * @return bool
     */
    function removePermission($perm, $group)
    {
        // init the global variables
        global $database, $message;

        // check if the supplied args are empty
        if (empty($perm) || empty($group) || $perm == null || $group == null) {
            return false;
        }

        // check to see if a string is supplied instead of a Class Object
        if (is_string($group)) {
            $group = $this->loadGroup($group);
        }

        // get the current permissions
        $newPermissions = $group->getPermissions();
        $totalRemoved = 0;

        // check if $perm is a string or array
        if (is_string($perm) && !is_array($perm)) {

            // check if permission already exists
            if (!$group->permissionExist($perm)) {
                return false;
            }

            // get the current permissions and remove the required variable
            $offset = array_search($perm, $newPermissions);
            unset($newPermissions[$offset]);

        } else if (is_array($perm)) {

            // loop throw each permission
            foreach ($perm as $permission) {

                // check if permission already exists
                if (!$group->permissionExist($permission)) {
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
        $newPermissions = serialize($newPermissions);

        // setup the sql query
        $sql = "UPDATE " . TBL_LEVELS . " SET " . TBL_LEVELS_PERMISSIONS . " = '" . $newPermissions . "' WHERE " . TBL_LEVELS_NAME . " = '" . $group->getName() . "'";

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
            $message->setSuccess("The permission " . $perm . " has been removed from the group '" . $group->getName() . "'");
        } else {
            $message->setSuccess("Total of " . $totalRemoved . " permission\s has been removed from the group '" . $group->getName() . "'");
        }

        return true;
    }

    /**
     * check if a certain group exists
     * @param string|int $group
     * @return bool
     */
    function groupExists($group)
    {
        // define all the global variables
        global $database;

        // check for empty object given
        if (empty($group)) {
            return false;
        }

        // check in database if exists
        if (is_string($group)) {
            $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_NAME . " = '$group'";
        } else {
            $sql = "SELECT * FROM " . TBL_LEVELS . " WHERE " . TBL_LEVELS_LEVEL . " = '$group'";
        }

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        // check if any values has been returned
        if ($database->getQueryNumRows($result, true) > 0) {
            return true;
        } else {
            return false;
        }
    }

}