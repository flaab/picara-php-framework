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
* Creates Scaffold controller and views for given model 
*
* @package    Scripts 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
*/
 
class scaffoldShellController extends Pi_shell_controller
{
    var $valid_options = array();
    var $valid_assertions = array('replace');
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
    * Creates scaffold controller for given model
    *
    * @param    string    $name
    */

    public function model($name)
    {
        // Model name
        $model_name = ucfirst($name);

        // If model does not exist
        if(!Pi_loader::model_exists($model_name))
            $this->abort("Given model $model_name does not exist in the application");
        
        // Config for this model
        $this->config = Pi_config::singleton();
        $config = $this->config->get($name);

        // If it is forbidden, exit
        if(!$config->scaffold->enabled)
        {
            $this->putline(" > Scaffolding for model '". $name ."' has been disabled in config file.");
            return;
        }     
        
        // File name
        $file_name = strtolower('Scaffold_' . $model_name . '.php');

        // View dir
        $view_path = VIEW . strtolower('Scaffold_' . $model_name . '/');
        
        // Open scaffold template
        $template = file_get_contents(BUILTIN_SHELL_DATA . 'scaffold/ScaffoldTemplate.php.txt');

        // Replace modelname
        $template = str_replace('<modelname>', $model_name, $template);
    
        // Writes controller to disk
        $this->_write(CONTROLLER . $file_name, $template);

        // Creates view dir
        if(FileSystem::create_tree($view_path))
            $this->putline(" > Create $view_path");

        // Copying all scaffold views
        $views = FileSystem::find_files(BUILTIN_SHELL_DATA . 'scaffold/view/', "/\.php$/");

        // Copying each one of them
        foreach($views as $view_file_path)
        {
            $file_name = FileSystem::get_file_name_from_path($view_file_path);
            $target_file = $view_path . $file_name;
            $content = file_get_contents($view_file_path);

            if(empty($content))
                touch($target_file);
            else
                $this->_write($target_file, $content);
        }
    }

    //--------------------------------------------------------

    /**
    * Creates scaffold controller for all models
    */

    public function all()
    {
        $all = $this->watcher->get('models');
        
        if(count($all) == 0)
            $this->abort("There are no models yet, nothing to do.");
    
        $all = array_keys($all);
        foreach($all as $model)
        {
            // Do not scaffold rel_x_x models
            if(!preg_match("/^rel_[a-zA-Z]+_[a-zA-Z]+$/i", $model))
            {
                // OK
                $this->putunderlined(ucfirst($model));
                $this->model($model);
                $this->putline();
            }
        }
    }

    //--------------------------------------------------------
    
    /**
    * Displays help and dies
    */
    public function help()
    {
        $help = file_get_contents(BUILTIN_SHELL_HELP . 'scaffold.txt');
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
