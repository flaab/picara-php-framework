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
* This is a test script, initially used only to test connections 
*
* @package    Scripts 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
*/

class testShellController extends Pi_shell_controller
{
    var $valid_options = array();
    var $valid_assertions = array();

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
    * Test given application connection
    *
    * @param    string    $conn
    */

    public function connection($conn)
    {
        if($this->db->connect($conn))
            $this->putline(" > Connection '$conn' \t OK");
        else
            $this->putline(" > Connection '$conn' \t FAILED");
    }

    //--------------------------------------------------------
    
    /**
    * Displays help and dies
    */

    public function help()
    {
        $help = file_get_contents(BUILTIN_SHELL_HELP . 'test.txt');
        $this->put($help);
    }
} 

?>
