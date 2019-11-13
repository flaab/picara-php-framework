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
* Provides a collection of unique instances to important framework elements that
* should not be instanced twice in the application. The access to these elements 
* is shared among models and controllers by using inheritance, and grants us
* efficient memory usage.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
* @abstract
*/

abstract class Pi_object extends Pi_overloadable
{
    /**
    * Loader object
    */
    var $loader;

    /**
    * Database object
    */
    var $db;
    
    /**
    * Core object
    */
    var $core;
    
    /**
    * Log object
    */
    var $log;
    
    /**
    * Flash object
    */
    var $flash;
    
    /*
    * Validator object
    */
    var $validation;
    
    /**
    * Session object
    */ 
    var $session;

    /**
    * Metadata object
    */
    var $metadata;
    
    //----------------------------------------------------------
    
    /**
    * Instances all objects using the singleton function to make sure
    * no object is created twice in the application, but stays available from
    * everywhere
    */

    public function __construct()
    {
        $this->loader       = Pi_loader::singleton($this);
        $this->db           = Pi_db::singleton();
        $this->core         = Pi_core::singleton();
        $this->log          = Pi_logs::singleton();
        $this->flash        = Pi_flash::singleton();
        $this->metadata     = Pi_metadata::singleton();
        $this->validation   = Pi_validation::singleton();
        $this->session      = Pi_session::singleton();
    }
}
?>
