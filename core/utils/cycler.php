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
* A string cycler class  
*
* @package      Utils
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Cycler extends Pi_error_store
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'cycler';

    /**
    * Private instance
    */
    private static $instance;
   
    /**
    * Control array over all executing cycles
    */
    private static $control = array();

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
    * The cycler function
    *
    * @param    string|array    $e1    First element to cycle or array with all elements to cycle
    * @param    string          $e2    Second element to cycle
    */

    public static function cycle($e1, $e2 = NULL)
    {
        // If e1 is an array
        if(is_array($e1))
            return self::_real_cycle($e1);

        // Forced check
        if($e2 == NULL)
            trigger_error('At least two elements are required to be cycled', E_USER_ERROR);
        
        return self::_real_cycle(array($e1, $e2)); 
    }

    //--------------------------------------------------------

    /**
    * The real cycler function
    *
    * @param    array    $elements
    */

    private static function _real_cycle($elements)
    {
        // Needed check
        if(count($elements) < 2) 
            trigger_error('Array must have more than one element to be cycled', E_USER_ERROR);

        // Index
        $index = implode('_', $elements);
        
        // If this cycle is not created, we should create it
        if(!isset(self::$control[$index]))
        {
            // Array creation
            self::$control[$index] = array(
                
                'values'    => $elements,
                'total'     => count($elements),
                'seq'       => count($elements)
            );
        }

        // Obtain index of element to return
        $element = self::$control[$index]['seq'] % self::$control[$index]['total'];

        // Increase counter
        self::$control[$index]['seq']++;

        // Return appropiate element
        return self::$control[$index]['values'][$element];
    }
}
?>
