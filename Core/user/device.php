<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/24/2017
 * Time: 12:45 PM
 */
namespace ALS\User;
class Device {

    private $data; // to hold all the data for a certain device

    /**
     * init the class
     * @param array $data
     */
    function __construct($data){
        $this->data = $data;
    }

    /**
     * get the browser name
     * @return string
     */
    function getBrowserName(){
        return $this->data['browser'];
    }

    /**
     * get the browser version
     * @return string
     */
    function getBrowserVersion(){
        if(empty($this->data['version'])) return "unknown";
        return $this->data['version'];
    }

    /**
     * get the connected device ip
     */
    function getIP(){
        return $this->data['ip'];
    }

    /**
     * @return string
     */
    function getOS(){
        return $this->data['os'];
    }

}