<?php
/**
 * Created by PhpStorm.
 * User: masis
 * Date: 6/10/2017
 * Time: 9:44 AM
 */

namespace ALS\MailTemplates;

use ALS\Mail\Mail;
use ALS\Settings\Settings;

class MailTemplates
{

    public function newSignIn()
    {

        // define all the global variables
        global $mail, $user, $settings;

        // grab the current user device info
        $device = $user->devices()->getCurrentDevice();

        $vars = array(
            '{:username}' => $user->getUsername(),
            '{:siteURL}' => $settings->get(Settings::SITE_URL),
            '{:siteName}' => $settings->get(Settings::SITE_NAME),
            '{:browserName}' => $device->getBrowserName(),
            '{:browserVersion}' => $device->getBrowserVersion(),
            '{:os}' => $device->getOS(),
            '{:ipAddress}' => $device->getIP(),
        );

        // grab the needed template
        $template = file_get_contents($settings->getTemplatesPath() . "newLoginDetected.html");

        // convert variables to actual values
        $content = strtr($template, $vars);

        // prepare the mail handler class
        $mail = new Mail();

        // set the required mail parameters
        $mail->fromEmail($settings->siteEmail())->fromName("Support")->to($user->getEmail())->subject("New Device Login")->isTemplate(true)->template($content);

        // send the mail
        return $mail->send();
    }

}