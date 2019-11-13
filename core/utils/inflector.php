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
* A bunch of useful string functions  
*
* @package      Utils
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Inflector extends Pi_error_store
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'inflector';

    /**
    * Private instance
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
    * Avoid direct creation of object  
    */
    private function __construct() {}

    //--------------------------------------------------------

    /**
    * Calculates the singular of a word
    *
    * @param    string    $word
    * @return   string
    */
    
    public static function singular($word)
    {
        $word = strtolower(trim($word));
        $end = substr($word, -3);
        
        if ($end == 'ies')
        {
            $word = substr($word, 0, strlen($word)-3).'y';

        } elseif ($end == 'ses') { 

            $word = substr($word, 0, strlen($word)-2);

        } else {

            $end = substr($word, -1);

            if ($end == 's')
            {
                $word = substr($word, 0, strlen($word)-1);
            }
        }
        return $word;
    }
    
    //--------------------------------------------------------

    /**
    * Calculates the plural of a word
    *
    * @param    string    $word
    * @return   string
    */
    
    public function plural($word, $force = false)
	{
	    $word = strtolower(trim($word));
	    $end = substr($word, -1);

	    if ($end == 'y')
	    {
	        $word = substr($word, 0, strlen($word)-1).'ies';
	        
	    } elseif ($end == 's') {
	        
	        if ($force == true)
	        {
	            $word .= 'es';
	        }
	        
	    } else {
	    
	        $word .= 's';
	    }
	    return $word;
	}
	
	//--------------------------------------------------------

    /**
    * Replaces spaces with underscores
    *
    * @param    string    $word
    * @return   string
    */
    
    public function underscore($word)
	{
		return preg_replace("/[\s]+/", '_', strtolower(trim($word)));
	}
	
	//--------------------------------------------------------
	
	/**
    * Replaces underscores with spaces
    *
    * @param    string    $word
    * @return   string
    */
    
    public function humanize($word)
	{
		return ucwords(preg_replace("/[_]+/", ' ', strtolower(trim($word))));
	}
	
	//--------------------------------------------------------
	
	/**
    * Deletes spaces or underscores and capitalizes every word
    *
    * @param    string    $word
    * @return   string
    */
    
    public function camelize($word)
	{		
		$word = 'x'.strtolower(trim($word));
		$word = ucwords(preg_replace("/[\s_]+/", ' ', $word));
		return substr(str_replace(' ', '', $word), 1);
	}
	
    //--------------------------------------------------------
	
	/**
    * Turns spaces into underscores and deletes any unknow characters
    *
    * @param    string    $word
    * @return   string
    */
    
    public function url_camelize($word)
	{	
        // foreign character
        $f = array(
            '&acirc;'   => 'a',
            '&aelig;'   => 'ae',
            '&agrave;'  => 'a',
            '&aring;'   => 'a',
            '&auml;'    => 'a',
            '&ccedil;'  => 'c',
            '&ecirc;'   => 'e',
            '&egrave;'  => 'e',
            '&euml;'    => 'e',
            '&icirc;'   => 'l',
            '&igrave;'  => 'l',
            '&iuml;'    => 'l',
            '&ocirc;'   => 'o',
            '&ograve;'  => 'o',
            '&oslash;'  => 'o',
            '&ouml;'    => 'o',
            '&szlig;'   => 'B',
            '&ucirc;'   => 'u',
            '&Acirc;'   => 'A',
            '&Aelig;'   => 'AE',
            '&Agrave;'  => 'A',
            '&Aring;'   => 'A',
            '&Auml;'    => 'A',
            '&Ccedil;'  => 'C',
            '&Ecirc;'   => 'E',
            '&Egrave;'  => 'E',
            '&Euml;'    => 'E',
            '&Icirc;'   => 'I',
            '&Igrave;'  => 'I',
            '&Iuml;'    => 'I',
            '&Ocirc;'   => 'O',
            '&Ograve;'  => 'O',
            '&Oslash;'  => 'O',
            '&Ouml;'    => 'O',
            '&Szlig;'   => 'B',
            '&Ucirc;'   => 'U',
            '&ETH;'     => 'D',
            '&eth'      => 'd'
        );

        // Remove html_entities and convert to normal
        $word = html_entity_decode(str_replace(array_keys($f), array_values($f), $word));
        
        // Destroy utf8 and latin1 char codes
        $word = preg_replace("/&#[0-9]+;/", '', $word);

	    // Conversions
        $c = array(
            'á' => 'a',
            'Á' => 'A',
            'é' => 'e',
            'É' => 'e',
            'í' => 'i',
            'Í' => 'i',
            'ó' => 'o',
            'Ó' => 'o',
            'ú' => 'u',
            'Ú' => 'u',
            'ñ' => 'n',
            'Ñ' => 'n',
            ' ' => '_',
            '-' => '_',
        );

        // Replacement
        $word = str_replace(array_keys($c), array_values($c), $word);
        
        // Remove everything but letters, numbers as underscores
        $word = preg_replace("/[^a-z0-9_]/i", '', $word);

        // Remove doubled spaces
        $word = preg_replace("/_+/", '_', $word);

        return $word;
    }
}
?>
