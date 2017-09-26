<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 9/25/2017
 * Time: 1:25 AM
 */

require "../API.php";

class initAPI{

    var $api;

    function __construct()
    {
        global $api;
        $this->api = $api;
    }

    function callParentMethod(){
        // get the current method called
        if(!empty($_GET['method'])){
            $param = $_GET['param'];
            $method = $_GET['method'];

            // call the api
            $this->api->callMethod($method, $param);
        } else {
            $this->api->printError("No method supplied");
        }
    }
}
$initAPI = new initAPI();
$initAPI->callParentMethod();