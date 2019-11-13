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
* Watches the framework directory structure and delivers information
* about existing elements like connections, libraries, etc.
*
* @package      Libs
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Watcher extends Pi_overloadable
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'watcher';

    /**
    * Private instance
    */
    private static $instance;

    /**
    * Directory map
    */
    private $map = array(

        'libs'          => LIBS,
        'utils'         => UTILS,
        'controllers'   => CONTROLLER,
        'shells'        => SHELL,
        'logs'          => array(LOG, 'directories'),
        'connections'   => CONNECTION,
        'models'        => MODEL,
        'userlibs'      => USERLIB,
        'system'        => SYSTEM
    );

    /**
    * Autoloaded elements
    */
    private $autoloaded = array('system','libs','utils','models','userlibs');

    //--------------------------------------------------------

    /**
    * Singleton implementation
    */

    public static function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    //--------------------------------------------------------

    /**
    * Private constructor to avoid direct creation of object  
    */
    private function __construct(){}

    //--------------------------------------------------------

    /**
    * Returs a hash containing all elements from desired map which
    * name matches a regular expression
    *
    * @param    string    $map
    * @param    string    $regex
    */

    public function get($map, $regex = "/^.*$/")
    {
        if(!isset($this->map[$map]))
            trigger_error("The watch element '$map' does not exist in the application", E_USER_ERROR);

        // Obtain instructions
        $instructions = $this->map[$map];
        
        // Return array
        $res = array();

        // We must watch directory files
        if(is_string($instructions))
        {
            if($map == 'connections') $ext = 'yml'; else $ext = 'php';

            // Php or Yml files are listed
            $files = FileSystem::find_files($instructions, "/\.". $ext  ."$/");

            // Building nice array
            foreach($files as $file)
            {
                // File name is splitted
                $file_name = preg_replace("/^.*\//", '', $file);
                
                // If it matches the regex
                if(preg_match($regex, $file_name))
                    $res[preg_replace("/(^.*\/|\.". $ext ."$)/",'', $file)] = $file;
            }

            return $res;
        
        // For logs
        } else {

            // Special behavior for logs
            //if($map == 'logs')
            //    $instructions[0] .= EXECUTION;

            // Instructions is an array, we should fectch directories instead
            $dirs = FileSystem::find_dirs($instructions[0]);

            // Building nice array
            foreach($dirs as $dir)
                $res[preg_replace("/^.*\//",'', $dir)] = preg_replace("/(shell|web)/", '...', $dir);
            
            return $res;
        }
    }

    //--------------------------------------------------------

    /**
    * Retrieves all scaffolded models
    *
    * @param    string    $exclude
    * @return   array
    */

    public function get_scaffolded_models($exclude = NULL)
    {
        // Find out how many more models are scaffolded
        $other_scaffold_controllers = array_keys($this->get('controllers', "/^scaffold_.+\.php$/i"));
        $other_scaffolds = array();

        foreach($other_scaffold_controllers as $controller)
        {
            $sca_model = preg_replace("/^scaffold_/i", '', $controller);
            
            if($sca_model != $exclude)
                $other_scaffolds[$sca_model] = $controller;
        }

        return $other_scaffolds;
    }

    //--------------------------------------------------------

    /**
    * Returns all existing classes included in the autoload path,
    * this is important to avoid duplicated class names to be created
    * among the application using the scripts.
    *
    * An optional array might be received with a bunch of ignored paths
    *
    * @return   array
    */

    public function get_autoloaded($ignore = array())
    {
        $res = array();

        foreach($this->autoloaded as $element)
        {
            if(!in_array($element, $ignore))
            {
                $paths = $this->get($element);
                $classes = array_keys($paths);
                $res = array_merge($res, $classes);
            }
        }

        return $res;
    }

    //--------------------------------------------------------

    /**
    * Magic function implementation
    *
    * @param    string    $method
    * @param    array     $arguments
    */

    protected function _magic($method, $arguments)
    {
        if(preg_match("/^get_(.+)$/", $method, $captured))
        {
            return $this->get($captured[1]); 
        }
        $this->method_does_not_exist($method);
    }
}
?>
