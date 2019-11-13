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
* Changes or displays current database environment
*
* @package    Scripts 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
*/
 
class environmentShellController extends Pi_shell_controller
{
    var $valid_options = array();
    var $valid_assertions = array();
    var $valid_environments = array('production','development','testing');

    //--------------------------------------------------------
    
    /**
    * Changes current database environment
    *
    * @param    string    $environment
    */
	
    public function change($environment)
    {
        // Lowercased
        $environment = strtolower($environment);

        // If valid environment
        if(!in_array($environment, $this->valid_environments))
            $this->abort("Provided environment is not valid; only production, development and testing are allowed");

        // Environment valid, reading template
        $template = file_get_contents(BUILTIN_SHELL_DATA . 'environment/template.php');

        // Replacing
        $template = str_replace('<environment_name>', $environment, $template);

        // Initial message
        $this->put(" > Switching to $environment environment ... ");

        // Writing to disk
        if(FileSystem::put_file(USERCONFIG . 'environment.php', $template))
            $this->putline("OK");
        else
            $this->putline("FAILED");
    }

    //--------------------------------------------------------
    
    /**
    * Displays current database environment
    */
    
    public function index()
    {
        $this->putline(" > Current database enviroment: ". ENVIRONMENT);
    }


    //--------------------------------------------------------
    
    /**
    * Displays help and dies
    */
    public function help()
    {
        $help = file_get_contents(BUILTIN_SHELL_HELP . 'environment.txt');
        $this->put($help);
    }
}

?>
