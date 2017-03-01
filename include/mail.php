<?php

/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 4/28/2016
 * Time: 1:40 AM
 */

class mail {

    private $to = "";
    private $from = "";
    private $subject = "";
    private $headers = "";
    private $isTemplate = false;
    private $template = "";
    private $fromName = "";
    private $message = "";
    private $text = "";
    private $mime_boundary = "";

    /**
     * init the class
     */
    public function init(){

    }

    /**
     * set the email address whom the message will be send
     * @param $sendTo
     */
    public function to($sendTo){
        $this->to = $sendTo;
    }

    /**
     * set the email address whom the message is being sent from
     * @param $sentFrom
     */
    public function fromEmail($sentFrom){
        $this->from = $sentFrom;
    }

    /**
     * set the name of the email sender
     * @param $sentFrom
     */
    public function fromName($sentFrom){
        $this->fromName = $sentFrom;
    }

    /**
     * set the subject of the email
     * @param $subject
     */
    public function subject($subject){
        $this->subject = $subject;
    }

    /**
     * choose if you want to send a template or just a text
     * @param $isTemplate
     */
    public function isTemplate($isTemplate){
        $this->isTemplate = $isTemplate;
    }

    /**
     * @param $templateContent
     */
    public function template($templateContent){
        $this->template = $templateContent;
    }

    /**
     * set the message text if not a template
     * @param $msgText
     */
    public function text($msgText){
        $this->text = $msgText;
    }

    /**
     * final step to send the email
     * @return bool
     */
    public function send(){

        // hash the current time with md5 encryption
        $this->mime_boundary = md5(time());

        $this->headers .= 'From: '. $this->fromName .' '. $this->from .$this->breakLine();
        $this->headers .= "Message-ID:<".$this->breakLine()." TheSystem@".$_SERVER['SERVER_NAME'].">".$this->breakLine();
        $this->headers .= "X-Mailer: PHP v".phpversion().$this->breakLine();           // These two to help avoid spam-filters
        // Boundry for marking the split & Multitype Headers
        $this->headers .= 'MIME-Version: 1.0'.$this->breakLine();
        $this->headers .= "Content-Type: multipart/related; boundary=\"".$this->mime_boundary."\"".$this->breakLine();
        // check if the email is a template or a text
        if($this->isTemplate){
            // set the template content headers
            $this->headers .= "--".$this->mime_boundary.$this->breakLine();
            $this->headers .= "Content-Type: text/html; charset=UTF-8".$this->breakLine();
            $this->headers .= "Content-Transfer-Encoding: 8bit".$this->breakLine();
            $this->message .= $this->template.$this->breakLine().$this->breakLine();
        } else {
            // set the text mail headers
            $this->headers .= "--".$this->mime_boundary.$this->breakLine();
            $this->headers .= "Content-Type: text/plain; charset=iso-8859-1".$this->breakLine();
            $this->headers .= "Content-Transfer-Encoding: 8bit".$this->breakLine();
            // split the text mail with every new line
            $msgLines = explode("\n", $this->text);
            // loop throw the array
            foreach($msgLines as $msgLine) {
                $this->message .= $msgLine.$this->breakLine();
            }
        }

        // last line to be added to the headers
        $this->message .= "--".$this->mime_boundary."--".$this->breakLine().$this->breakLine();
        // send the mail
        $sender = mail($this->from, $this->subject, $this->message, $this->headers);
        return $sender;
    }

    /**
     * return a line breaker
     * @return string
     */
    public function breakLine() {
        if (strtoupper(substr(PHP_OS, 0, 3) == 'WIN')) {
            $eol = "\r\n";
        } elseif (strtoupper(substr(PHP_OS, 0, 3) == 'MAC')) {
            $eol = "\r";
        } else {
            $eol = "\n";
        }
        return $eol;
    }
}