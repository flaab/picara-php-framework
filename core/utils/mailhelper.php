<?php

################################################################
#                                                              #
#       PROJECT:    PICARA PHP WEB DEVELOPMENT FRAMEWORK       #
#       WEBSITE:    https://git.io/Je8zR                       #
#       COPYRIGHT:  Arturo Lopez Perez                         #
#       AUTHOR:     Arturo Lopez Perez                         #
#       LICENSE:    MIT License                                #
#                                                              #
################################################################

// Recojo phpmailer
require_once(VENDORS . 'phpmailer/class.phpmailer.php');

/**
* A set of useful datetime functions 
*
* @package    Utils 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/
 
class MailHelper extends Pi_error_store
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'mailhelper';

    /**
    * Instance for singleton pattern
    */
    private static $instance;

    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object. 
    */
    
    private function __construct(){}
    
    //---------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return    Datetime
    */
    
    public static function singleton() 
    {
        if (!isset(self::$instance))
        {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }
    
    //--------------------------------------------------------

    /**
    * Sends an email using SMTP or sendmail.
    *     
    * @param    string    $to
    * @param    string    $subject
    * @param    string    $message
    * @param    string    $reply_to
    */

    public static function send($to, $subject, $message, $reply_to = NULL)
    {
        // If reply to is null
        if(is_null($reply_to))
            $reply_to = MC_FROM_MAIL;
        
        // Mails enabled?
        if(MC_SEND_MAIL)
        {
            // Import PHP Mailer
            $mail = new PHPMailer();

            // Check if SMPT has to be used
            if(strlen(MC_SMTP_HOST) > 0 && strlen(MC_SMTP_USERNAME) > 0 && strlen(MC_SMTP_PASSWORD) > 0)
            {
                $mail->isSMTP();                           // Use SMTP
                $mail->Host         = MC_SMTP_HOST;        // SMTP Host
                $mail->SMTPAuth     = true;                // Enable Auth
                $mail->Username     = MC_SMTP_USERNAME;    // SMTP Username
                $mail->Password     = MC_SMTP_PASSWORD;    // SMTP password
                $mail->SMTPSecure   = 'tls';               // Enable encryption
             
            } else { 
                
                // No SMTP
                $mail->Host = 'localhost';
                $mail->Mailer = 'sendmail';
            }

            // Mail Data
            $mail->From = MC_FROM_MAIL;
            $mail->FromName = MC_FROM_NAME;
            $mail->AddAddress($to, $to);
            $mail->AddReplyTo($reply_to, $reply_to);

            // Subject
            $mail->Subject = $subject;
            
            // HTML body
            $mail->Body = $message;

            // HTML body
            $mail->Body = nl2br($mail->Body);
            $mail->AltBody = strip_tags($mail->Body);

            // Done!
            return $mail->Send();
        }
        return true;
    }
    
    //--------------------------------------------------------

    /**
    * Sends an email to the admin of the website
    *
    * @param    string    $message
    * @param    string    $subject
    */

    public static function notification($subject, $message)
    {
        return(self::send(MC_ADMIN_MAIL, "[NOTIFICATION] ". $subject, $message));
    }

}
?>
