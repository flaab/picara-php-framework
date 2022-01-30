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
// GETTEXT LOCALES SUPPORT
//==============================================================

/**
* Activates langfiles support
*
* @default    false
*/
define('LANG_SUPPORT', true);

/**
* Default language for lang support
*
* @default    en    Always must have a value
*/
define('DEFAULT_LANG', 'en');

/**
* Force a specific language to be loaded.
* Used for development purposes.
*
* @default  false   No lang forced 
*/
define('FORCED_LANG', false);

/**
* Require lang string in urls parser.
* If not, redirect will be made.
*
* @default  false 
*/
define('LANG_IN_URLS', false);

/**
* Supported languages. 
* Edit at will.
*/
$_LANGUAGES = array(
    //'ar' => 'عربى',            // Arabic
    'en' => 'English',           // English
    'es' => 'Español',           // Spanish
    //'fr' => 'Français',        // French
    //'hi' => 'हिंदी',             /// Hindi
    //'it' => 'Italiano',        // Italian
    //'de' => 'Deutsche',        // German
    //'pt' => 'Português',       // Portuguese
    //'ru' => 'Pусский',         // Russian
    //'ja' => '日本',            // Japanese
    //'zh' => '中文',            // Chinese
    //'th' => 'ไทย',             // Thai
    //'id' => 'Indonesia',       
    //'af' => _('Afrikaanse'),
    //'be' => _('Belarusian'),
    //'bg' => _('Bulgarian'),
    //'bn' => _('Bengali'),
    //'bs' => _('Bosnian'),
    //'bo' => _('Tibetan'),
    //'ca' => _('Catalan'),
    //'cs' => _('Czech'),
    //'da' => _('Danish'),
    //'el' => _('Greek'),
    //'eu' => _('Euskera'),
    //'et' => _('Estonian'),
    //'fi' => _('Finnish'),
    //'gl' => _('Galician'),
    //'he' => _('Hebrew'),
    //'hu' => _('Hungarian'),
    //'is' => _('Islandic'),
    //'ko' => _('Korean'),
    //'lv' => _('Latvian'),
    //'lt' => _('Lithuanian'),
    //'ml' => _('Malayam'),
    //'mr' => _('Marathi'),
    //'nl' => _('Dutch'),
    //'no' => _('Norwegian'),
    //'pl' => _('Polish'),
    //'pa' => _('Punjabi'),
    //'ro' => _('Romanian'),
    //'sr' => _('Serbian'),
    //'sk' => _('Slovakian'),
    //'sq' => _('Albanian'),
    //'sv' => _('Swedish'),
    //'ta' => _('Tamil'),
    //'te' => _('Telugu'),
    //'uk' => _('Ukranian'),
    //'ur' => _('Urdu'),
    //'sl' => _('Slovenian'),
    //'vi' => 'Tiếng Việt',
    //'yi' => _('Yiddish'),
);

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
