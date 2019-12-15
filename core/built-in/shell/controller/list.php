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
    
    //--------------------------------------------------------

    /**
     * Lists all users
     *
     * @param   string  $type   Null, superuser, staff or regular
     */
    public function users($type = NULL)
    {
        // User must exist
        if(!Pi_loader::model_exists("User")) $this->abort("The model 'user' does not exist in the application.");
        
        // Valid type
        $valid_type = array(NULL, 'superuser','staff','regular');
        if(!in_array($type, $valid_type))
            $this->abort("Type '". $type ."' is not a valid user type.");

        // Get all user types
        if($type == NULL || $type == 'superuser')
            $super   = $this->db->query->arrays->getUserWhere("type = 'superuser' ORDER BY id ASC");
        if($type == NULL || $type == 'staff')
            $staff   = $this->db->query->arrays->getUserWhere("type = 'staff' ORDER BY id ASC");
        if($type == NULL || $type == 'regular')
            $regular = $this->db->query->arrays->getUserWhere("type = 'regular' ORDER BY id ASC");

        // Super Users
        if(!is_null($super) && count($super) >= 1)
        {
            $this->putunderlined("Super Users");
            foreach($super as $u)
            {
                $this->putline(" ". $u['id'] .") ". ucwords($u['name']) . " (". $u['mail'] .")");
            }
            $this->putline("");
        }
        
        // Staff
        if(!is_null($staff) && count($staff) >= 1)
        {
            $this->putunderlined("Staff");
            foreach($staff as $u)
            {
                $this->putline(" ". $u['id'] .") ". ucwords($u['name']) . " (". $u['mail'] .")");
            }
            $this->putline("");
        }
        
        // Regular
        if(!is_null($regular) && count($regular) >= 1)
        {
            $this->putunderlined("Regular");
            foreach($regular as $u)
            {
                $this->putline(" ". $u['id'] .") ". ucwords($u['name']) . " (". $u['mail'] .")");
            }
            $this->putline("");
        }
    }
}

?>
