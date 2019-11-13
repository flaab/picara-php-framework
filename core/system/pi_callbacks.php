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
* Implements callbacks feature for models and controllers.
*
* @package    System
* @author     Arturo Lopez 
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

abstract class Pi_callbacks extends Pi_object
{
    /**
    * Receives an array with a bunch of function names and checks if are executable
    *
    * @param    array    $functions
    * @return   bool
    */

    protected final function test_callbacks($functions)
    {
        return $this->callback($functions, true);
    }

    //--------------------------------------------------------

    /**
    * Receives an array with a bunch of function names and executes them
    *
    * @param    array    $functions
    * @return   bool
    */

    protected final function execute_callbacks($functions)
    {
        return $this->callback($functions);
    }

    //--------------------------------------------------------

    /**
    * Receives an array with a bunch of functions and tests or executes them
    *
    * @param    array    $functions
    * @param    bool     $test
    * @return   bool
    */

    private final function callback($functions, $test = false)
    {
        // If we have something to execute
        if(is_array($functions))
        {
            // Then we check they all exist before executing any of them
            foreach($functions as $method)
            {
                // Execution halted if function does not exist, dangerous to go on
                if(!method_exists($this, $method))
                {
                    $msg = "Callback function $method() does not exist for class ". get_class($this);
                    $this->storeAndLog($msg);
                    trigger_error($msg, E_USER_ERROR);
                }
            }
            
            // If any errors ocurred, we are out
            if($this->failed())
            { 
               return false; 
                 
            } else {

               // If no error has been thrown and only simulation was asked, return true
               if($test == true) { return true; }                     
                 
            }
             
            // Nothing failed
            foreach($functions as $method)
            {
                $this->$method();
            }
             
            // Everything executed
            return true;    
             
        } else {
        
            // Nothing to execute
            return true;
        }
    }
}

?>
