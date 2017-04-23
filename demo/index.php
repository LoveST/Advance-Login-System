<?php

/** Check user & site status **/
require "../init.php";
$session->statusCheck();
/** End check user & site status**/

require TEMPLATE_PATH ."/home.html";
