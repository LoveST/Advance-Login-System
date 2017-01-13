<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 4/28/2016
 * Time: 1:40 AM
 */
class Mail
{

    private function sendEmail($from, $to, $subject, $message, $headers = false){
        $header = "";
        if($headers){
            // Always set content-type when sending HTML email
            $header = "MIME-Version: 1.0" . "\r\n";
            $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $header .= 'From: <' . $from .'>' . "\r\n";
        } else {
            // Always set the headers when sending a message
            $header = 'From: <' . $from .'>' . "\r\n";
        }
        if(mail($to,$subject,$message,$header)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * send an email using a template
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @return bool
     */
    function sendTemplate($from, $to, $subject, $message){
        if($this->sendEmail($from, $to,$subject,$message, true)){
            return true;
        } else { return false; }
    }

    /**
     * Send a text only email
     * @param $from
     * @param $to
     * @param $subject
     * @param $message
     * @return bool
     */
    function sendText($from, $to, $subject, $message){
        if($this->sendEmail($from, $to,$subject,$message)){
            return true;
        } else { return false; }
    }

}

$mail = new mail();