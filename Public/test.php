<?php

class Tests1
{

    public function __construct()
    {
        global $functions;
        // load the main required init.php file
        $functions->loadFile(FRAMEWORK_PATH . FRAMEWORK_PUBLIC_PATH . "init.php");
        new init();

        die($functions->decryptIt("zykkZPDXc0Ik5mjIGq1JCb9jOc4T3puYrOubGb0sVk16qYZ7DAniPk3WI6K8UqwIGgECe4NLu45Xr0EGZbIHju5wb1wkwLIS2UtNx2BQ5zk="));

        // load the view controller
        include_once "test.html";
    }

}

new Tests1();
