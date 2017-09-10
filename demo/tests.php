<?php
include "../init.php";
echo $database->_CONNECTION_TYPE;

if (!array_key_exists(CONNECTION_TYPE, $database->_DBConnections)) {
    echo "it does";
} else { echo "NOPE";}