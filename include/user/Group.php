<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/17/2017
 * Time: 4:48 PM
 */

namespace ALS\User\Group;


class Group
{

    private $data; // to-store all the group data

    function __construct($groupData)
    {
        $this->data = $groupData;
    }

    /**
     * get the group level
     * @return int
     */
    function getLevel()
    {
        return $this->data[TBL_LEVELS_LEVEL];
    }

    /**
     * get the group main name
     * @return string
     */
    function getName()
    {
        return $this->data[TBL_LEVELS_NAME];
    }

    /**
     * get the group permissions
     * @return array
     */
    function getPermissions()
    {
        // separate every single permission after a | sign and store it in an array and return it
        $permissions = explode("|", $this->data[TBL_LEVELS_PERMISSIONS]);
        return $permissions;
    }

}