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
* Creates models, controllers, shells, databases, logs...etc
*
* @package    Scripts 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/
 
class createShellController extends Pi_shell_controller
{
    var $valid_options = array('table','display','host','db','user','password','adapter','port','connection');
    var $valid_assertions = array('replace','force');
    var $valid_adapters = array('mysql','postgres','oracle','sqlite');
    var $reserved_controllers = array('My');
    var $load = array('Watcher');
    
    /**
    * Index redirects to help
    */
    public function index()
    {
        $this->help();
    }

    //--------------------------------------------------------

    /**
    * Creates desired model
    *
    * @param    string    $name
    */

    public function model($name = NULL)
    {
        // Name check
        if(!$this->validation->alphanumeric($name,"_"))
            $this->abort("Model name needs to be alphanumeric.");
        
        // Model name
        $model_name = ucfirst($name);

        // Existing autoloaded classes
        $existing = $this->watcher->get_autoloaded(array('models'));
        
        // If repeated
        if(in_array($model_name, $existing))
            $this->abort("Class '$model_name' already exists in the application, please choose another one", E_USER_ERROR);

        // Filenames
        $file_name = strtolower($model_name) . '.php';
        $config_file = strtolower($model_name) . '.yml';
      
        // If no connection is set, no models can be created
        if(!$this->db->exists())
        {
            $this->abort("There are no connections in the application. Please, create a connection first");
        }

        // Connection
        if($this->options->connection)
        {
            $connection = $this->options->connection;
            $this->putline(" > Using '$connection' as connection");
        } else {
            $connection = DEFAULT_CONNECTION;
            $this->putline(" > Assuming '$connection' as connection");
        }

        // Check if connection exists
        if(!$this->db->exists($connection))
            $this->abort("Connection '$connection' does not exist in the application");

        // Connection attempt
        if(!$this->db->connect($connection, true))
            $this->abort("Connection could not be entablished, please check '$connection' connection information (". CONNECTION . $connection . ".php)");

        // Table name
        if($this->options->table)
        {
            $table = $this->options->table;
            $this->putline(" > Using '$table' as table name");
        } else {
            $table = strtolower($name);
            $this->putline(" > Assuming '$table' as table name");
        }

        // Existing tables
        $tables = $this->db->link->{$connection}->MetaTables();

        // If table does not exist
        if(!in_array($table, $tables))
            $this->abort("Table '$table' does not exist for connection '$connection'. Please, use option -table to specify a valid one");
        
        // Obtain table metadata
        $metadata = $this->db->link->{$connection}->MetaColumns($table);

        // Suposse it does not pk
        $has_primary_key = false;

        // Check if there is a primary key named as PRIMARY_KEY constant
        foreach($metadata as $object)
        {
            if($object->name == PRIMARY_KEY && $object->primary_key == 1)
                $has_primary_key = true;
        }

        // If no primary key
        if($has_primary_key == false)
            $this->abort("Table '$table' does not have any primary key field named ". PRIMARY_KEY);

        // Display name
        if($this->options->display)
        {
            $display = $this->options->display;
            $this->putline(" > Using '$display' as display name");
        } else {
            $display = ucfirst($name);
            $this->putline(" > Assuming '$display' as display name");
        }

        // Template read
        $model_template = file_get_contents(BUILTIN_SHELL_DATA . 'create/ModelTemplate.php.txt');
        
        // Modelname, table and displayname replacement
        $model_template = str_replace('<modelname>', $model_name, $model_template);

        // Config template
        $config_template = file_get_contents(BUILTIN_SHELL_DATA . 'create/ConfigTemplate.yml');

        // Replacements
        $config_template = str_replace('<table>', $table, $config_template);
        $config_template = str_replace('<displayname>', $display, $config_template);
        $config_template = str_replace('<connection>', $connection, $config_template);
        $config_template = str_replace('<modelname>', $model_name, $config_template);
        
        // Full path to write
        $model_path = MODEL . $file_name;

        // Config path
        $config_path = MODELCONFIG . $config_file;

        // Writes
        $this->_write($model_path, $model_template);
        $this->_write($config_path, $config_template);
    }

    //--------------------------------------------------------

    /**
    * Creates desired controller
    *
    * @param    string    $name
    */

    public function controller($name = NULL, $admin = 0)
    {
        // Name check
        if(!$this->validation->alphanumeric($name,"_"))
            $this->abort("Controller name needs to be be alphanumeric.");
        
        // Admin?
        if($admin < 0 || $admin > 1)
            $this->abort("The admin parameter value is invalid");

        // Controller name
        $controller_name = ucfirst($name);

        // If controller name is 'My', crash!
        if(in_array($controller_name, $this->reserved_controllers))
            $this->abort("Controller '$controller_name' cannot be created, please choose another name");

        // Filename
        $file_name = strtolower($controller_name) . '.php';

        // Template read
        if($admin == 0)
            $template = file_get_contents(BUILTIN_SHELL_DATA . 'create/ControllerTemplate.php.txt');
        else
            $template = file_get_contents(BUILTIN_SHELL_DATA . 'create/AdminControllerTemplate.php.txt');

        // Modelname, table and displayname replacement
        $template = str_replace('<controllername>', $controller_name, $template);

        // Full path to write
        $full_path = CONTROLLER . $file_name;

        // Full path to view dir
        $view_path = VIEW . strtolower($controller_name) . '/';

        // Full path to lang dir
        $lang_path = LANG . strtolower($controller_name) . '/';

        // Full path to cache dir
        $cache_path = CACHE . strtolower($controller_name) . '/';

        // Full path to main view
        $view_index = $wiew_path . 'index.php';

        // Writes
        $this->_write($full_path, $template);
        
        // View directory must also be created
        if(FileSystem::create_tree($view_path))
            $this->putline(" > Create view directory\t($view_path)");
        
        // Lang directory
        if(FileSystem::create_tree($lang_path))
            $this->putline(" > Create lang directory\t($lang_path)");

        // Cache directory
        if(FileSystem::create_tree($cache_path))
            $this->putline(" > Create cache directory\t($cache_path)");
    }
    
    //--------------------------------------------------------

    /**
    * Creates desired admin controller
    *
    * @param    string    $name
    */

    public function admincontroller($name = NULL)
    {
        $this->controller($name, 1);
    }

    //--------------------------------------------------------

    /**
    * Creates desired shell controller
    *
    * @param    string    $name
    */

    public function shell($name = NULL)
    {
        // Name check
        if(!$this->validation->alphanumeric($name,"_"))
            $this->abort("Shell controller name needs to be alphanumeric.");
        
        // Controller name
        $controller_name = ucfirst($name);

        // If controller name is 'My', crash!
        if(in_array($controller_name, $this->reserved_controllers))
            $this->abort("Controller '$controller_name' cannot be created, please choose another name");
        
        // Filename
        $file_name = strtolower($controller_name) . '.php';

        // Template read
        $template = file_get_contents(BUILTIN_SHELL_DATA . 'create/ShellTemplate.php.txt');
        
        // Modelname, table and displayname replacement
        $template = str_replace('<controllername>', $controller_name, $template);

        // Full path to write
        $full_path = SHELL . $file_name;

        // Writes
        $this->_write($full_path, $template);
    }

    //--------------------------------------------------------

    /**
    * Creates desired connection for the application
    *
    * @param    string    $name
    */

    public function connection($name = NULL)
    {
        // Check for a name
        if($name == NULL)
            $this->abort("Connection name cannot be empty");

        // Name must start with a letter
        if(!preg_match("/^[A-Za-z]{1}[A-Za-z0-9]+$/", $name))
            $this->abort("Connection name needs to be alphanumeric.");

        // If adapter is set, it must be valid
        if($this->options->adapter)
        {
            if(!in_array($this->options->adapter, $this->valid_adapters))
                $this->abort("Provided database adapter is not valid or not supported yet");
        }

        // If there are no connections, this connection must be named main
        if(!$this->db->exists() && $name != DEFAULT_CONNECTION && !$this->assertion('force'))
        {
            $this->abort("The first connection created should be called '". DEFAULT_CONNECTION ."'. Rename it or append the -force assertion.");
        }

        // Template
        $template = file_get_contents(BUILTIN_SHELL_DATA . 'create/DbTemplate.yml');

        // Target file
        $target_file = CONNECTION . strtolower($name) . '.yml';

        // Some value defaults
        //$this->options = new StdClass();
        if(!$this->options->port) $this->options->port = '';
        if(!$this->options->host) $this->options->host = 'localhost';
        
        // Replacements
        $replacements = array('host','user','db','password','adapter','port');
        
        // Connection name
        $template = str_replace('<name>', $name, $template);

        // Replace
        foreach($replacements as $item)
        {
            $needle = '<'. $item .'>';
            $template = str_replace($needle, $this->options->{$item}, $template);
        }

        // Write to disk
        $this->_write($target_file, $template);
    }

    //--------------------------------------------------------

    /**
    * Creates a new log directory
    *
    * @param    string    $log
    */

    public function log($log)
    {
        // Name check
        if(!$this->validation->alphanumeric($log))
            $this->abort("Log name needs to be alphanumeric.");
        
        // Existing logs
        $logs = $this->watcher->get('logs');
        $existing = array_keys($logs);

        // If already exists
        if(in_array($log, $existing))
            $this->abort("Log '$log' already exists at ". $logs[$log]);

        // Two paths must be created
        $paths = array(LOG . strtolower($log));

        // Creating it
        foreach($paths as $path)
        {
            if(FileSystem::create_tree($path))
            {
                $this->putline(" > Create $path");
                chmod($path, 0777);
            } else {
                $this->abort("I cannot write '$path', check permissions");
            }
        }
    }

    //--------------------------------------------------------
    
    /**
    * Displays help and dies
    */

    public function help()
    {
        $help = file_get_contents(BUILTIN_SHELL_HELP . 'create.txt');
        $this->put($help);
    }

    //--------------------------------------------------------
    
    /**
    * Writes file to a path and displays according message
    *
    * @param    string    $path
    * @param    string    $content
    */

    private function _write($path, $content)
    {
        // If replace has been ordedered
        if($this->assertion('replace'))
            $replace = true;
        else
            $replace = false;
        
        // Writes and displays
        $res = FileSystem::put_file($path, $content, $replace);

        // Result message
        if(!$res)
        {
            $this->putline(" > Exists $path");
            return;
        }

        $this->putline(" > Create $path");
    }
}

?>
