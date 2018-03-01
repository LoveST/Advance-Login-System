<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 2/28/2017
 * Time: 4:55 PM
 */

/** Check user & site status **/
require "../init.php";
$init = new init("../../Core.php");
$init->loginCheck();
$session->adminCheck();
/** End check user & site status**/