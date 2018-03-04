<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/3/2018
 * Time: 8:26 PM
 */

// disable direct access to the file
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

if (isset($_POST['update'])) {
    if ($profileManager->setNewUsername($_POST['username'], $_POST['pin'])) {
        header("Location: login.php");
    }
}

// load the view
$viewController->loadView("profile_change_username.html");