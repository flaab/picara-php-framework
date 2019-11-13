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
* Implements a few debugging functions for the application
*
* @package      Libs
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Debug extends Pi_error_store
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'debug';

    //--------------------------------------------------------

    /**
    * Singleton implementation
    */

    public static function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    //--------------------------------------------------------

    /**
    * Dumps whatever variable received
    *
    * @param    mixed   $var
    */

    public static function dump($var)
    {

        if(EXECUTION == 'web') echo('<pre>');
        if(is_array($var)) print_r($var); else var_dump($var);
        if(EXECUTION == 'web') echo('</pre>');
    }

    //--------------------------------------------------------

    /**
    * Yells to outloud the data flow
    *
    * @param    string    $message
    */

    public static function yell($message = '')
    {
        if(EXECUTION == 'web') echo('<pre>');
        print('Yell! '. $message);
        if(EXECUTION == 'web') echo('</pre>');
    }

}
?>
