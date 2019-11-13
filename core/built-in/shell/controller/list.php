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
* Displays all models and controllers of the application 
*
* @package    Scripts 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
*/
 
class listShellController extends Pi_shell_controller
{
    var $valid_options = array();
    var $valid_assertions = array();
    var $load = array('Watcher');
    var $listed_elements = array('models','controllers','shells','connections','logs');

    //--------------------------------------------------------

    /**
    * Index redirects to help
    */

    public function index()
    {
        $this->help();
    }

    //--------------------------------------------------------
    
    /**
    * Displays all existing models
    */

	public function models()
    {
        $this->_display('models');
    }

    //--------------------------------------------------------

    /**
    * Displays all existing controllers
    */

    public function controllers()
    {
        $this->_display('controllers');
    }

    //--------------------------------------------------------

    /**
    * Displays all existing controllers
    */

    public function shells()
    {
        $this->_display('shells');
    }

    //--------------------------------------------------------

    /**
    * Displays all existing connections
    */

    public function connections()
    {
        $this->_display('connections');
    }

    //--------------------------------------------------------

    /**
    * Displays all existing logs
    */

    public function logs()
    {
        $this->_display('logs');
    }

    //--------------------------------------------------------

    /**
    * Displays everything
    */

    public function all()
    {
        foreach($this->listed_elements as $element)
        {
            $this->putunderlined(ucfirst($element));
            $this->_display($element);
            $this->putline();
        }
    }

    //--------------------------------------------------------

    /**
    * Displays help and dies
    */

    public function help()
    {
        $help = file_get_contents(BUILTIN_SHELL_HELP . 'list.txt');
        $this->put($help);
    }

    //--------------------------------------------------------
    
    /**
    * Performs the nitty gritty job
    *
    * @param    string    $path
    * @param    string    $object
    */

    private function _display($object)
    {
        $files = $this->watcher->get($object);

        if(count($files) == 0)
        {
            $this->putline(" There are no $object created yet.");
            return;
        }

        // Foreach file path
        foreach($files as $name => $full_path)
        {
            // List
            $this->putline(" > ". ucfirst($name) ."\t ($full_path)");
        }

        $this->putline("");
        $this->putline(" TOTAL: \t ". count($files) ." $object");
    }
}

?>
