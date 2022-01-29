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
* Implements shared functionality among all overloadable objects 
*
* @package      System
* @author       Arturo Lopez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/

abstract class Pi_overloadable extends Pi_error_store
{
    /**
    * Unique magic function implementation for all application objects.
    * His duty is to check if function '_magic' exist and delegate the
    * work.
    *
    * @param    string    $method
    * @param    array     $arguments
    */
    
    public final function __call($method, $arguments)
    {
        // If method _magic is implemented
        if(!method_exists($this, '_magic'))
            $this->method_does_not_exist($method);

        return $this->_magic($method, $arguments);
    }

    //--------------------------------------------------------

    /**
    * Mixes received with default parameters
    *
    * @param    array    $default
    * @param    array    $arguments
    * @return   array
    */

    protected final function mix_parameters($default = array(), $arguments = array())
    {
        $result = array();                   
        $cardinality = count($default);     
        
        for($it = 0; $it < $cardinality; $it++)
        {
            if(isset($arguments[$it]))
                $result[$it] = $arguments[$it];
            else
                $result[$it] = $default[$it];
        }
        
        return $result;
    }
    //--------------------------------------------------------

    /**
    * Aborts the exection and displays the proper error message
    *
    * @param    string    $method
    */

    protected final function method_does_not_exist($method)
    {
        trigger_error("The function '$method' does not exist in class ". get_class($this), E_USER_ERROR);
    }

    //----------------------------------------------------------
    
    /**
    * Gets the desired order for the query from the method called
    *
    * @param    string    $method
    * @return   string
    */

    protected final function _getOrder($method)
    {
        // Requested order
        $orderBy = preg_replace("/^.*OrderBy/", '', $method);
            
        // If no matches, no order
        if($orderBy == $method)
            return NULL;
                  
        return $orderBy;
    }
}
?>