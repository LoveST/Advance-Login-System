<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 2/14/2018
 * Time: 4:32 PM
 */

/** Check user & site status **/
require "../Core.php";
$core = new \ALS\Core();
$core->initClasses();
$status = $session->statusCheck();
/** End check user & site status**/

// check if site name is empty
if ($settings->siteName() == "" || $settings->siteName() == null) {
    $message->setError("Site name is empty.", \ALS\Message::Warning, "", __LINE__ - 1);
}

// check if site language is empty
if ($settings->siteLanguage() == "" || $settings->siteLanguage() == null) {
    $message->setError("Site default language not setup", \ALS\Message::Warning, "", __LINE__ - 1);
}

// check if a default group has been setup
if ($groups->getDefaultGroup()->getName() == "" || $groups->getDefaultGroup()->getName() == null) {
    $message->setError("No default group found.", \ALS\Message::Warning, "", __LINE__ - 1);
}

// check if any errors
if ($message->anyError()) {

    $message->printError(2);

} else {
    echo "No errors found";
}