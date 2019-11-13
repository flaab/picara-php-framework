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
* Destroys models and controllers for the application
*
* @package    Scripts 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
*/
 
class destroyShellController extends Pi_shell_controller
{
    var $valid_options = array();
    var $valid_assertions = array();
    var $protected_logs = array(DEFAULT_LOG);
    var $load = array('Watcher');

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
    * Destroys given model and files path
    *
    * @param    string    $name
    */

    public function model($name = NULL)
    {
        // Abort if null name
        if($name == NULL) $this->abort("Model name cannot be empty");

        // Full path
        $full_path = MODEL . strtolower($name) . '.php';

        // Config path
        $config_path = MODELCONFIG . strtolower($name) . '.yml';

        // If it does not exist
        if(!file_exists($full_path))
            $this->abort("Model $name does not exist ($full_path)");
        
        // Deleting
        $this->_delete($full_path);
        $this->_delete($config_path);

        // Files path
        $file_path = MODEL_FILES . strtolower($name);
        if(is_dir($file_path))
        {           
            if(is_writable($file_path))
            {
                // Deleting cache path
                if(FileSystem::delete_tree($file_path))
                {
                    $this->putline(" > Delete files directory recursively\t($file_path)");
                }
            
            } else {

                // Not writable, message
                $this->putline(' > Warning: I do not have enough permissions to delete '. $file_path . '. You should delete it manually');

            }
        }
    }

    //--------------------------------------------------------

    /**
    * Destroys given controller, view path, cache path, lang path
    *
    * @param    string    $name
    */

    public function controller($name = NULL)
    {
        // Abort if null name
        if($name == NULL) $this->abort("Controller name cannot be empty");

        // File name
        $file_name = strtolower($name);

        // Full path
        $full_path = CONTROLLER . $file_name . '.php';

        // View path
        $view_path = VIEW . $file_name . '/';

        // Lang path
        $lang_path = LANG . $file_name . '/';

        // Cache path
        $cache_path = CACHE . $file_name . '/';

        // If it does not exist
        if(!file_exists($full_path))
            $this->abort("Controller $name does not exist ($full_path)");

        // Deleting
        if(unlink($full_path))
        {
            $this->putline(" > Delete $full_path");
        } else {
            $this->abort("I cannot delete $full_path, check permissions");
        }

        // Deleting view path
        if(FileSystem::delete_tree($view_path))
        {
            $this->putline(" > Delete view directory recursively\t($view_path)");    
        }

        // Deleting lang path
        if(FileSystem::delete_tree($lang_path))
        {
            $this->putline(" > Delete lang directory recursively\t($lang_path)");    
        }

        // Deleting cache path
        if(FileSystem::delete_tree($cache_path))
        {
            $this->putline(" > Delete cache directory recursively\t($cache_path)");
        }
    }

    //--------------------------------------------------------

    /**
    * Destroys given shell controller
    *
    * @param    string    $name
    */

    public function shell($name = NULL)
    {
        // Abort if null name
        if($name == NULL) $this->abort("Controller name cannot be empty");

        // File name
        $file_name = strtolower($name);

        // Full path
        $full_path = SHELL . $file_name . '.php';

        // If it does not exist
        if(!file_exists($full_path))
            $this->abort("Shell controller $name does not exist ($full_path)");

        // Deleting
        if(unlink($full_path))
        {
            $this->putline(" > Delete $full_path");
        } else {
            $this->abort("I cannot delete $full_path, check permissions");
        }
    }

    //--------------------------------------------------------

    /**
    * Destroys a database connection and all dependant models
    *
    * @param    string    $name
    */

    public function connection($name = NULL)
    {
         // Abort if null name
        if($name == NULL) $this->abort("Connection name cannot be empty");

        // File name
        $file_name = strtolower($name) . '.yml';

        // Full path
        $full_path = CONNECTION . $file_name;

        // If it does not exist
        if(!file_exists($full_path))
            $this->abort("Connection $name does not exist");

        // Deleting
        if(unlink($full_path))
            $this->putline(" > Delete $full_path");
        else
            $this->abort("I cannot delete $full_path, check permissions");

    }

    //--------------------------------------------------------

    /**
    * Destroys a log directory
    *
    * @param    string    $log
    */

    public function log($log)
    {
        // If protected
        if(in_array($log, $this->protected_logs))
            $this->abort("Log '$log' is part of the framework and cannot be deleted");

        // Existing logs
        $logs = $this->watcher->get('logs');
        $existing = array_keys($logs);
        
        // To lowercase
        $log = strtolower($log);

        // If already exists
        if(!in_array($log, $existing))
            $this->abort("Log '$log' does not exist");

        // Just one path to to be destroyed
        $paths = array(LOG . $log);

        // Creating it
        foreach($paths as $path)
        {
            if(FileSystem::delete_tree($path))
                $this->putline(" > Delete $path");
            else
                $this->abort("I cannot delete '$path', check permissions");
        }

    }

    //--------------------------------------------------------
    
    /**
    * Deletes given path or explodes
    *
    * @param    string    $path
    */

    private function _delete($path)
    {
        // Deleting
        if(unlink($path))
            $this->putline(" > Delete $path");
        else
            $this->abort("I cannot delete $path, check permissions");
    }

    //--------------------------------------------------------
    /**
    * Displays help and dies
    */
    public function help()
    {
        $help = file_get_contents(BUILTIN_SHELL_HELP . 'destroy.txt');
        $this->put($help);
    }
}

?>
