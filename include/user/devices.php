<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 3/21/2017
 * Time: 11:31 PM
 */
class Devices{

    var $computers; // store all the computers that the user is being logged from
    private $userData; // store the loaded user data

    /**
     * init the class
     * @param array $userData
     */
    function init($userData){

        // store the current userData in the userData variable
        $this->userData = $userData;
    }

    function addDevice(){

        // init the global variables
        global $database, $message;

        // check if class is instance of a live user
        if(!$this->isActiveUser()){
            return false;
        }

        // get the current user devices
        $devices = $this->getDevicesArray();

        // get the current device as array
        $currentDevice = $this->deviceToArray($this->getCurrentDevice());

        // check if the current device is already been added before
        if($this->canAccess()){
            return false;
        }

        // push the current device info to the array
        array_push($devices, $currentDevice);

        // serialize the current array
        $devices = serialize($devices);

        // update the devices record in the database
        $sql = "UPDATE ". TBL_USERS . " SET ". TBL_USERS_DEVICES . " = '". $devices . "' WHERE ". TBL_USERS_USERNAME . " = '". $this->getUsername() . "'";
        if (!$result = mysqli_query($database->connection,$sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__,__LINE__);
            return false;
        }

        // if no errors found then return true
        return true;
    }

    /**
     * remove a certain device by using the device id parameter
     * @param int $id
     * @return bool
     */
    function removeDevice($id){

        // init the global variables
        global $database, $message;

        // escape strings
        $id = $database->escapeString($id);

        // set the new device id
        $id = $id - 1;

        // get the current user devices
        $devices = $this->getDevices();

        // store the devices array
        $devicesArray = Array();

        // convert the current device classes to array of devices
        foreach ($devices as $key => $device){
            array_push($devicesArray, $this->deviceToArray($device));
        }

        // remove the requested device from array
        unset($devicesArray[$id]);

        // serialize the current array
        $devicesArray = serialize($devicesArray);

        // update the devices record in the database
        $sql = "UPDATE ". TBL_USERS . " SET ". TBL_USERS_DEVICES . " = '". $devicesArray . "' WHERE ". TBL_USERS_USERNAME . " = '". $this->getUsername() . "'";
        if (!$result = mysqli_query($database->connection,$sql)) {
            $message->setError("Error while pulling data from the database : " . mysqli_error($database->connection), __FILE__,__LINE__);
            return false;
        }

        // if no errors found then return true
        return true;
    }

    /**
     * check if the current device is verified or supply it with another Device class
     * and check if that certain device is verified
     * @param Device $device
     * @return bool
     */
    function isVerified($device){
            return in_array($device, $this->getDevices());
    }

    /**
     * check if the class has been initiated by a live user
     * @return bool
     */
    private function isActiveUser(){
        if(empty($this->getUsername())){
            return false;
        } else { return true; }
    }

    /**
     * Check if the current browser and ip is allowed to log in from
     */
    function canAccess(){
        if(in_array($this->deviceToArray($this->getCurrentDevice()), $this->getDevicesArray())) { return true; } else { return false; }
    }

    /**
     * get the current user's username
     * @return string
     */
    function getUsername(){
        return $this->userData[TBL_USERS_USERNAME];
    }

    /**
     * convert from device class to array
     * @param Device $device
     * @return array
     */
    function deviceToArray($device){

        // init the required array
        $deviceArray = Array(
            "ip" => $device->getIP(),
            "browser" => $device->getBrowserName(),
            "version" => $device->getBrowserVersion(),
            "os" => $device->getOS()
        );

        // return the array
        return $deviceArray;
    }

    /**
     * get the current device Class
     * @return Device
     */
    function getCurrentDevice(){

        // init the global variables
        global $browser;

        // create the required array for the current device info
        $arr = Array();
        $arr['ip'] = $this->getUserIP();
        $arr['browser'] = $browser->getBrowser();
        $arr['version'] = $browser->getVersion();
        $arr['os'] = $browser->getPlatform();

        $device = new Device($arr);

        // return the info array
        return $device;
    }

    /**
     * get the current users id
     * @return string
     */
    function getUserIP(){
        return $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    /**
     * get the current user's logged in devices as an array
     */
    function getDevicesArray(){

        // get the devices array
        $devices = $this->userData[TBL_USERS_DEVICES];

        // check for empty string or array
        if(empty($devices)){
            return Array();
        }

        // un-serialize the string
        $devices = unserialize($devices);

        // return the results
        return $devices;
    }

    /**
     * get the current user's logged in devices class
     */
    public function getDevices(){

        // get the devices array
        $devices = $this->userData[TBL_USERS_DEVICES];

        // check if empty array or null
        if($devices == null || empty($devices)){
            return Array();
        }

        // un-serialize the string
        $devices = unserialize($devices);

        // hold all the devices classes
        $deviceClasses = Array();

        // loop throw each one and create a class for every single one
        foreach ($devices AS $key => $device){

            // create instance of the device class
            $device = new Device($device);

            array_push($deviceClasses, $device);
        }

        // return the devices array
        return $deviceClasses;
    }

}