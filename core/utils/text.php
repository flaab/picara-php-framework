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
* Another bunch of string related functions 
*
* @package      Utils
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Text extends Pi_error_store
{   
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'text';
   
    /**
    * Instance of object
    */
    private static $instance;

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
    * Loads the user agent list and sets up the object to retrieve data  
    */
    private function __construct(){}

    //--------------------------------------------------------

    /**
    * Turns Urls and Mail adresses into clickable links
    *
    * @param    string    $text
    * @return   string
    */

    public function auto_link($text)
    {
        $text = preg_replace('/([_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3}))/', '<a href="mailto $1">$1</a>', $text);
        $text = preg_replace('/((?>http|https|ftp):\/\/[^\s]+)/', '<a href="$1">$1</a>', $text); 
        return $text;
    }
    
    //--------------------------------------------------------

    /**
    * Extracts an excerpt from a text with given radius
    *
    * @param    string    $text
    * @param    string    $needle
    * @param    int       $radius
    * @return   string
    */
    
    public function excerpt($text, $needle, $radius = 50)
    {
        // Word count
        $length = strlen($text) - 1;

        // Check needle
        $pos = stripos($text, $needle);
        if(!$pos) return $text;

        // Check if start and final dots must be attached
        $at_start = $at_end = true;
        if($pos - $radius < 1) $at_start = false;
        if(($pos+strlen($needle)) + $radius >= $length) $at_end = false;

        // Extract
        preg_match('/.{0,'. $radius .'}'. $needle .'.{0,'. $radius  .'}/i', $text, $matches);
        if($at_start == true) $matches[0] = '...' . $matches[0];
        if($at_end == true)   $matches[0] .= '...';
        return $matches[0];
    }

    //--------------------------------------------------------

    /**
    * Highlights one or more phrases from a string
    *
    * @param    string          $text
    * @param    string|array    $needle
    * @param    string          $block
    */
    
    public function highlight($text, $needle, $left = '<b>', $right = '</b>')
    {
        if(is_array($needle))
        {
            foreach($needle as $phrase)
                $text = preg_replace('/('. $phrase .')/i', $left . "$1" . $right , $text);

            return $text;
        }
        return preg_replace('/('. $needle .')/i', $left . "$1" . $right , $text);
    }

    //--------------------------------------------------------

    /**
    * Truncates a text to given length and appends a final string
    *
    * @param    string    $text
    * @param    int       $length
    * @param    string    $enclose
    * @return   string
    */
    
    public function truncate($text, $length, $enclose = '...')
    {
        $text = substr($text, 0, $length);
        return $text . $enclose;
    }
}
?>
