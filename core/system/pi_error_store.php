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
* Provides an object built-in error store for every created object
*
* @package System
* @author Arturo Lopez
* @copyright Copyright (c) 2008-2019, Arturo Lopez
* @version 0.1
* @example objecterrors/objecterrors.php
*/

abstract class Pi_error_store
{
    /**
    * Container for all stored errors
    */
    private $collection = NULL;
    
    //----------------------------------------------------------
    
    /**
    * Adds the given error to the store
    * @param string $msg
    */
    
    public final function storeError($msg)
    {
        if(is_array($msg))
           {
               foreach($msg as $item)
               {
                   $this->collection[] = $item;
               }
               
           } else {
           
               $this->collection[] = $msg;
           
           }
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves all stored errors
    *
    * @return array|null
    */
    
    public final function getErrorStore()
    {
        return $this->collection;
    }
    
    //----------------------------------------------------------
    
    /**
    * Prints all errors. Should be used for development purposes only.
    */
    
    public final function displayErrorStore()
    {
        $string =  'Error store '. LINE_BREAK .' ---';
        
        if(count($this->collection) == 0)
          {
              $string .= LINE_BREAK ."None";
              
          } else {
          
              foreach($this->collection as $key => $value)
              {
                   $string .= LINE_BREAK .'(' . $key . ') => ' . $value;
              }
          }
          $string .= LINE_BREAK;
          
          echo $string;    
    }
    
    //----------------------------------------------------------
    
    /**
    * Checks if there are errors stored in the object
    *
    * @return bool
    */
    
    public final function failed()
    {
        if($this->collection != NULL)
            return TRUE;
        else
            return FALSE;
    }
    
    //----------------------------------------------------------
    
    /**
    * Checks if the objects has no errors stored
    *
    * @return bool
    */
    
    public final function isOk()
    {
        if($this->collection == NULL)
            return TRUE;
        else
            return FALSE;
    }
    
    //----------------------------------------------------------
    
    /**
    * Cleans up the error store
    */
    public final function cleanErrorStore()
    {
        $this->collection = NULL;
    }

    //----------------------------------------------------------

    /**
    * Stores the given error into the object and logs it into the log
    * 
    * @param    string    $error
    */
    
    public final function storeAndLog($error)
    {
        $this->storeError($error);
        if(is_object($this->log))
            $this->log->error($error);
    }
    
}
?>
