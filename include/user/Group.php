<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/17/2017
 * Time: 4:48 PM
 */

namespace ALS\User;


class Group
{

    private $data; // to-store all the group data

    function __construct($data)
    {
        $this->data = $data;
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
     * get the group permissions
     * @return array
     */
    function getPermissions()
    {
        // separate every single permission after a | sign and store it in an array and return it
        $permissions = unserialize($this->data[TBL_LEVELS_PERMISSIONS]);

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

}