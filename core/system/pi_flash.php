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
* Implements the controller notifications class.
* 
* @package      System
* @author       Arturo Lopez Perez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/

class Pi_flash
{
    /**
    * Self instance
    */
    private static $instance;
    
    //----------------------------------------------------------

    /**
    * Private constructor to avoid direct creation of object
    */
    private function __construct(){}
    
    //----------------------------------------------------------    
    
    /**
    * Will return a new object or a pointer to the already existing one.
    *
    * @return   Pi_flash
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
    
    //----------------------------------------------------------
     
    /**
    * Adds a warning to the controller
    *
    * @param    string|array    $msg 
    */
      
    public final function warning($msg)
    {
       if(is_array($msg))
       {
           foreach($msg as $item)
           {
               $_SESSION['picara']['controller_warning'][] = $item;
           }
           
       } else {
       
           $_SESSION['picara']['controller_warning'][] = $msg;
       
       }
    }
     
    //----------------------------------------------------------
     
    /**
    * Adds a flash message to be displayed before rendering the view.
    *
    * @param    string|array    $msg 
    */
      
    public final function success($msg)
    {
       if(is_array($msg))
       {
           foreach($msg as $item)
           {
               $_SESSION['picara']['controller_flash'][] = $item;
           }
           
       } else {
       
           $_SESSION['picara']['controller_flash'][] = $msg;
       }
    }
       
    //----------------------------------------------------------
       
    /**
    * Adds a critical error message that is showed and prevents the view to be loaded
    *
    * @param    string|array    $msg
    */

    public final function error($msg)
    {
       if(is_array($msg))
       {
           foreach($msg as $item)
           {
               $_SESSION['picara']['controller_errors'][] = $item;
           }
           
       } else {
       
           $_SESSION['picara']['controller_errors'][] = $msg;
       
       }
    }
       
    //----------------------------------------------------------
   
    /**
    * Adds a non critical error message to be shown before rendering the view
    *
    * @param    string|array    $msg
    */
   
    public final function validation_error($msg)
    {
       if(is_array($msg))
       {
           foreach($msg as $item)
           {
               $_SESSION['picara']['controller_dataerrors'][] = $item;
           }
           
       } else {
       
           $_SESSION['picara']['controller_dataerrors'][] = $msg;
       
       }
    }
}
?>
