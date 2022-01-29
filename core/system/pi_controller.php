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
* Shared functionality for WebController and ShellController. Implements
* callbacks execution and automatic library loading.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

abstract class Pi_controller extends Pi_callbacks
{
    /**
    * Before render callbacks
    */
    var $before_action = array();

    /**
    * After render callbacks
    */
    var $after_action = array();

    /**
    * Config file for this controller (optional)
    */
    var $config_file = '';

    /**
    * Config object for this controller (optional)
    */
    var $config;
    
    /**
    * Request array to hold controller and action name
    */
    var $request = array('controller' => '', 'action' => '');
    var $link    = array('controller' => '', 'action' => '');

    //--------------------------------------------------------

    /**
    * Constructor loads declared variables and executes
    * controller callbacks. To perform any logic at construction
    * time callbacks should be used.
    */

    public final function __construct()
    {
        parent::__construct();

        if(isset($this->load) && is_array($this->load))
        {
            foreach($this->load as $lib)
                $this->loader->load($lib);
        }
    }

    //--------------------------------------------------------

    /**
    * Test and executes desired callbacks array
    *
    * @param    array    $functions
    */

    protected final function controller_callbacks($functions)
    {
        // If not declared
        if($functions == NULL)
            return;

        // Check functions exists
        $this->test_callbacks($functions);

        // Execution
        $this->execute_callbacks($functions);
    }
    
    //--------------------------------------------------------

    /**
    * Loads the config file
    */
    
    protected final function load_config()
    {
        if(isset($this->config_file) && is_string($this->config_file) && strlen($this->config_file) > 3)
        {
            $cf = USERCONFIG . $this->config_file;
            if(file_exists($cf))
            {
                $this->config = yaml_parse(file_get_contents($cf)); 
            } else {
                $this->core->abort("I cannot load config file ". $cf ." Check the path and filename.");
            }
        }
    }
}

?>
