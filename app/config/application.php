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

/**
* Application config file. Add as you wish.
*
* @package      Config
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

//==============================================================
// APPLICATION 
//==============================================================
 
 /**
 * Enter your app name here
 * @default    Your App Name
 */
 define('TITLE', 'Your App Name'); 
 
 /**
 * Connector to construct default browser titles
 * @default ' - '
 */
 define('CONNECTOR', ' - '); 
 
/**
* Browser title when a critical error is thrown
* @default An error has ocurred
*/
define('ERROR_TITLE', 'An error has ocurred');
 
/**
* Redirection to be performed just after a critical error has been thrown
* @default NULL
*/
define('ERROR_REDIRECT', NULL);

//==============================================================
// ENCRYPTION AND SALT
//==============================================================

/**
*  Function to encode passwords
*  @default    sha1
*/
define('ENCFUNCTION', 'sha1');

/**
*  Salt to encode passwords
*  @default    sha1
*/
define('SALT', 'pmig^jl58bd^#jaxqvzqke3udr=a^5_qa_b1(t3#lgil&%*!0y');

//==============================================================
// SESSION NAMES
//==============================================================

/**
* Session variable name 
*/  
define('SESSION_NAME', str_replace(" ", '-', TITLE) .'-Session');

/**
* Session name for the admin panel
*/
define('ADMIN_SESSION', str_replace(" ", '-', TITLE) .'-Admin');

//==============================================================
// MAIL CONFIGURATION
// IF SMTP details not entered, local sendmail is used.
//==============================================================

/**
 * Enable or disable mail sending
 * @default false
 */
define('MC_SEND_MAIL', false);

/**
 * Email of site administrator
 */
define('MC_ADMIN_MAIL', 'yourname@example.com');

/**
 *  Address of mail sender
 */
define('MC_FROM_MAIL', 'noreply@yourdomain.com');

/**
 * Name of automatic email sender
 */
define('MC_FROM_NAME', 'Your App Name');

/**
 * SMTP Hostname
 */
define('MC_SMTP_HOST', 'smtp.domain.com');

/**
 * SMTP Username
 */
define('MC_SMTP_USERNAME', 'postmaster@domain.com');

/**
 *  SMTP Password
 */
define('MC_SMTP_PASSWORD', '');

//==============================================================
// CACHE STATIC HTML DOCUMENTS OPTIONS
//==============================================================

/**
* Set the base href of your website to use the cache system.
*/
define('CURL_BASE_URL', '');

/**
* Extension for cache files
*
* @default    .phtml
*/
define('STATIC_EXT', '.phtml');
 
//==============================================================
// LANG SUPPORT
//==============================================================

/**
* Activates langfiles support
*
* @default    false
*/
define('LANG_SUPPORT', false);

/**
* Default language for lang support
*
* @default    en
*/
define('DEFAULT_LANG', 'en');

/**
* Message consistency causes unexistant messages to be added from default lang files
*
* @default    true
*/
define('MESSAGES_CONSISTENCY', true);

//==============================================================
// LOGS
//==============================================================

/**
* Log level. Affects user logs.
*
* NULL  -> Nothing is stored. Ever.
* 0     -> Automatic log level based on environment
* 1     -> Only errors will be stored 
* 2     -> Errors and warning messages will be stored
* 3     -> Errors, warnings and messages will be stored 
*
* @default 0
*/
define('LOG_LEVEL', 0);


?>
