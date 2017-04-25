<?php

/**
 * Created by PhpStorm.
 * User: masis
 * Date: 4/25/2017
 * Time: 11:14 AM
 */
namespace ALS\AUTH\Twilio;
require "Twilio/autoload.php";

use ALS\Message\Message;
use Twilio\Rest\Client;

class Twilio
{

    function sendSMS($infoArray)
    {

        // init the required global variables
        global $database, $message, $settings;

        // check if array is empty then return an error
        if (empty($infoArray) || empty($infoArray['to']) || empty($infoArray['msg'])) {
            $message->setError("Required parameters are missing from the create statement", Message::Error);
            return false;
        }

        // check if string length is greater than 100
        if (strlen($infoArray['msg']) > 100) {
            $message->setError("Max allowed characters for a message is 100", Message::Error);
            return false;
        }

        // check if twilio account sid has ben setup
        if ($settings->twilioAccountSid() == "") {
            $message->setError("Missing account security identifier", Message::Error);
            return false;
        }

        // check if twilio authentication token has ben setup
        if ($settings->twilioAuthToken() == "") {
            $message->setError("Missing authentication code", Message::Error);
            return false;
        }

        // if-no errors then continue with the sending progress
        $client = new Client($settings->twilioAccountSid(), $settings->twilioAuthToken());

        // create and send the message
        $sms = $client->account->messages->create(
            $infoArray['to'],
            array(
                'from' => "+15017250604",
                // the sms body
                'body' => $infoArray['msg']
            )
        );

        return true;
    }

}