<?php
include "../init.php";


$sql = "SELECT * FROM users WHERE username = 'lovemst'";
$results = $database->getQueryResults($sql, array("s" => $user->getUsername()));
echo $database->getQueryNumRows($results , true);