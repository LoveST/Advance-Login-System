<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/9/2018
 * Time: 11:15 AM
 */

namespace ALS;


class Statistics
{

    public function __construct()
    {

    }

    /**
     * @param int $since -> the amount of days passed for the users that has signed up
     * @return array|bool
     */
    function getTotalRegisteredUsers($since)
    {

        // define all the global variables
        global $database, $message, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("admin_analytics_registeredUsers")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // call the database and get the results back
        $query = "SELECT COUNT(*) FROM  " . TBL_USERS . " WHERE  " . TBL_USERS_DATE_JOINED . " > NOW() - INTERVAL $since DAY";

        // get the sql results
        if (!$result = $database->getQueryResults($query)) {
            return false;
        }

        $row = $database->getQueryEffectedRow($result, true);

        // return the array
        return $row[0];
    }

    /**
     * @param int $since timestamp in seconds
     * @return bool|array
     */
    function getTotalLoggedUsers($since)
    {

        // define all the global variables
        global $database, $message, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("admin_analytics_loggedUsers")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return 0;
        }

        // call the database and get the results back
        $query = "SELECT COUNT(*) FROM  " . TBL_USERS . " WHERE  " . TBL_USERS_LAST_LOGIN . " > NOW() - INTERVAL $since SECOND";

        // get the sql results
        if (!$result = $database->getQueryResults($query)) {
            return false;
        }

        // check if no records where found
        if ($database->getQueryNumRows($result, true) <= 0) {
            return false;
        }

        // get the results
        $row = $database->getQueryEffectedRow($result, true);

        // return the array
        return $row[0];
    }

    /**
     * get all the users between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function getTotalRegisteredUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_registeredUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // Set the required parameters
        $users = "";

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_DATE_JOINED . " >= '$startDate' AND 
        " . TBL_USERS_DATE_JOINED . " <= '$endDate'" . $limitArgs;

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        foreach ($database->getQueryEffectedRows($results, true) as $row) {
            $currentUser = new User();
            $currentUser->initInstance($row);

            $users[] = $currentUser;
        }

        return $users;
    }

    /**
     * get all the logged in users between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function getTotalLoggedUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_loggedUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // Set the required parameters
        $users = "";

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_LAST_LOGIN . " >= '$startDate' AND 
        " . TBL_USERS_LAST_LOGIN . " <= '$endDate'" . $limitArgs;

        // get the sql results
        if (!$results = $database->getQueryResults($sql)) {
            return false;
        }

        foreach ($database->getQueryEffectedRows($results, true) as $row) {
            $currentUser = new User();
            $currentUser->initInstance($row);

            $users[] = $currentUser;
        }

        return $users;
    }

    /**
     * count the number of signed up users in between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function countTotalRegisteredUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_countRegisteredUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_DATE_JOINED . " >= '$startDate' AND 
        " . TBL_USERS_DATE_JOINED . " <= '$endDate'" . $limitArgs;

        // get the sql results
        $result = $database->getQueryResults($sql);
        if ($database->anyError()) {
            return false;
        }

        $row = $database->getQueryNumRows($result, true);


        return $row;
    }

    /**
     * count the number of logged in users in between the specified dates and choose if the data must
     * be limited to save resources or just pass on a value for both the offsets
     * @param $startDate
     * @param $endDate
     * @param $limit
     * @param $offsetStart
     * @param $offsetEnd
     * @return User[]|bool
     */
    function countTotalLoggedUsersInBetween($startDate, $endDate, $limit = 0, $offsetStart = 0, $offsetEnd = 0)
    {

        // define all the global variables
        global $database, $message, $user;

        // check if current user has the required permission
        if (!$user->hasPermission("analytics_countLoggedUsersInBetween")) {
            $message->setError("You don't have the permission to perform this action", Message::Error);
            return false;
        }

        // check if any limits are set
        if ($limit != 0) {
            $limitArgs = " LIMIT " . $limit;
        } else {
            $limitArgs = "";
        }

        // check if any offsets has been used
        if ($offsetEnd != 0 && $offsetEnd >= $offsetStart) {
            $limitArgs = " LIMIT " . $offsetStart . "," . $offsetEnd;
        }

        // call the database and get the results back
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE
        " . TBL_USERS_LAST_LOGIN . " >= '$startDate' AND 
        " . TBL_USERS_LAST_LOGIN . " <= '$endDate'" . $limitArgs;

        // get the sql results
        if (!$result = $database->getQueryResults($sql)) {
            return false;
        }

        return $database->getQueryNumRows($result, true);
    }

}

$statistics = new Statistics();