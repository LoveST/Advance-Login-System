<?php
/**
 * Created by PhpStorm.
 * User: LoveMST-Tablet
 * Date: 7/17/2017
 * Time: 4:42 PM
 */

namespace ALS\Groups;

use ALS\User\Group\Group;

require_once "user/Group.php";

class Groups
{

    /**
     * load a certain group and return as a Group class
     * @param int $groupLevel
     * @return Group|bool
     */
    function loadGroup($groupLevel){

        global $database;

        // check if empty group level given
        if($groupLevel == "" || empty($groupLevel)){
            return false;
        }

        // try to load the group from database
        $sql = "SELECT * FROM ". TBL_LEVELS . " WHERE ". TBL_LEVELS_LEVEL . " = '" . $groupLevel . "'";

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        // translate the results to data
        $row = mysqli_fetch_array($result);

        // if no errors then return a new instance
        // of the Group class
        return new Group($row);
    }

}