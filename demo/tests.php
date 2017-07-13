<?php
require "../init.php";

if (!$user->twoFactorEnabled()) {
    echo "not e nabled;";
}