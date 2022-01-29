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
* Loads framework libraries and utilities
*
* @package      System
* @author       Arturo Lopez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/

class Pi_loader extends Pi_error_store
{
    /**
    * Instance for singleton pattern
    */
    private static $instance;

    /**
    * Collection of target objects to be affected by loads
    */
    private $targets = array();

    /**
    * Collection of singleton objects
    */
    private $singletons = array();

    /**
    * Collection of instances and class names to be created in new objects
    */
    private $standard = array();

    /**
    * Manual loading path; built-in libs and utils
    */
    private $hand_load_path = array(LIBS, UTILS);

    /**
    * Autoload path; system path is included.
    */
    private static $autoload_path = array(MODEL, LIBS, UTILS, SYSTEM, USERLIB);

    /**
    * Controllers path
    */
    private static $controller_path = array(CONTROLLER, BUILTIN_WEB_CONTROLLER);

    /**
    * Shell controllers path
    */
    private static $shell_path = array(SHELL, BUILTIN_SHELL_CONTROLLER);
    
    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object. 
    *
    * @param    object    $obj
    */
    
    private function __construct() {}
    
    //---------------------------------------------------------

    /**
    * A pointer to the unique Load application object is returned, and
    * caller object is internally stored to be modified by future
    * loads.
    *
    * @param     object    $obj
    * @return    Pi_loader
    */
    
    public static function singleton($obj = NULL) 
    {
        if (!isset(self::$instance))
        {
            $c = __CLASS__;
            self::$instance = new $c();
        }

        // If an object has been received, it should be appended to targets
        if(is_object($obj))
            self::$instance->_add_target($obj);    
        
        return self::$instance;
    }

    //--------------------------------------------------------

    /**
    * Loads desired item into the application, used for libraries and utilities
    *
    * @param    string    $class
    */

    public function load($class)
    {
        // Loads or dies
        $this->_bring($class);
        
        // Check for load mode
        $eval_order = 'if(isset('. $class .'::$load_mode)) $load_mode = '. $class .'::$load_mode;';
        eval($eval_order);
        
        // If load name is not declared, our job has been accomplished
        if(!isset($load_mode))
            return;

        // If load_mode is declared, this class is autoloaded and load_name is needed
        $eval_order = 'if(isset('. $class .'::$load_name)) $load_name = '. $class .'::$load_name;';
        eval($eval_order);
        
        // If load_name does not exist, error
        if(!isset($load_name))
            trigger_error("Library or utility '$class' does not implement the static variable 'load_name'", E_USER_ERROR);

        // Switch between load mode
        switch($load_mode)
        {
            case SINGLETON: $this->_load_singleton($class, $load_name); break;
            case STANDARD:  $this->_load_standard($class, $load_name); break;
            default: trigger_error("The following load mode is not valid: '$load_mode', check '$class' class", E_USER_ERROR);
        }
    }

    //--------------------------------------------------------

    /**
    * Loads a singleton class and stores it
    *
    * @param    string    $class
    * @param    string    $name
    */

    private function _load_singleton($class, $name)
    {
        // If it has been created before
        if(isset($this->singletons[$name]))
        {
            $stored_class = get_class($this->singletons[$name]);

            // Checks for duplicated load names for different classes
            if($stored_class != $class)
                trigger_error("Load name for classes $class and  $stored_class  are repeated", E_USER_ERROR);

            return;
        }

        // Instance should be created
        eval('$this->singletons[$name] = '. $class .'::singleton();');

        // Population
        $this->_populate($name, $this->singletons[$name]);
    }

    //--------------------------------------------------------

    /**
    * Loads a standard object
    *
    * @param    string    $class
    * @param    string    $name
    */

    private function _load_standard($class, $name)
    {
        // If it has not been loaded yet
        if(isset($this->standard[$name]))
        {
            // Checks for duplicated load names for different classes
            if($this->standard[$name] != $class)
                trigger_error("Load name for classes $class and ". $this->standard[$name] ." are repeated", E_USER_WARNING);
            
            return;
        } 

        // Class and name should be stored
        $this->standard[$name] = $class;

        // Population
        $this->_populate($name, $class);
    }

    //--------------------------------------------------------

    /**
    * Populates received lib or util into all target objects
    *
    * @param    string           $name
    * @param    object|string    $class
    */

    private function _populate($name, $class)
    {
        if(is_string($class))
            $class = new $class();

        foreach($this->targets as $target)
            $this->_assign($target, $name, $class);
    }

    //--------------------------------------------------------

    /**
    * Assigns received instance into desired object
    *
    * @param    object           $target
    * @param    string           $name
    * @param    object|string    $class
    */

    private function _assign($target, $name, $class)
    {
        if(isset($target->$name))
            trigger_error("Class variable '$name' has been overriden for class ". get_class($target) ." with a loaded object", E_USER_WARNING);

        if(is_string($class))
            $class = new $class();

        // Important check; an object cannot contain himself
        if(get_class($class) == get_class($target))
            return;

        $target->$name = $class;
    }

    //--------------------------------------------------------

    /**
    * Adds an object to target objects and populates all loaded classes
    *
    * @param    object    $object
    */

    private function _add_target($object)
    {
        // Duplicated objects are not allowed
        if(in_array($object, $this->targets))    return;

        // Adding target
        $this->targets[] = $object;

        // Adding singletons
        foreach($this->singletons as $name => $class)   $this->_assign($object, $name, $class);

        // Adding standard
        foreach($this->standard as $name => $class)    $this->_assign($object, $name, $class);
    }

    //--------------------------------------------------------

    /**
    * Includes desired php file into the application or dies
    *
    * @param    string    $class
    */

    private function _bring($class)
    {
        // Filename must be lowercase
        $file = strtolower($class) . '.php';
        foreach($this->hand_load_path as $path)
        {
            $full_path = $path . $file;
            if(file_exists($full_path))
            {
                require_once($full_path);
                return;
            }
        }
        trigger_error("Library or utility '$class' does not exists or cannot be loaded manually", E_USER_ERROR);
    }

    //===========================================================
    // Static functions
    //===========================================================

    /**
    * Checks if given model exists in the application
    *
    * @param    string    $model
    */
    
    public static function model_exists($model)
    {
        if(!file_exists(MODEL . strtolower($model) . '.php'))
            return false;
              
        return true;
    }

    //--------------------------------------------------------

    /**
    * Autoload for our application
    *
    * @param    string    $class
    */
     
    public static function autoload($class)
    {
        // Filename must be lowercase, always (except in vendors folder)
        $file = strtolower($class) . '.php';
        $tot = count(self::$autoload_path);
        for($i=0; $i < $tot; $i++)
        {    
            $path = self::$autoload_path[$i] . $file;
            
            if(file_exists($path))
            {
                require_once($path);
                return;
            }
        }
        trigger_error("The model or library '$class' does not exist in the application", E_USER_ERROR);
    }

    //--------------------------------------------------------

    /**
    * Loads desired controller into the application
    *
    * @param    string  $controller
    * @return   bool
    */
    
    public static function load_controller($controller)
    {
        // Name must be lowercase
        $file = strtolower($controller) . '.php';
        $tot = count(self::$controller_path);
        for($i=0; $i < $tot; $i++)
        {    
            $path = self::$controller_path[$i] . $file;
            if(file_exists($path))
            {
                require_once($path);
                return true;
            }
        }
        return false;
    }

    //--------------------------------------------------------

    /**
    * Loads desired shell controller into the application
    *
    * @param    string    $controller
    * @return   bool
    */
    
    public static function load_shell($controller)
    {
        // Name must be lowercase
        $file = strtolower($controller) . '.php';
        $tot = count(self::$shell_path);
        for($i=0; $i < $tot; $i++)
        {    
            $path = self::$shell_path[$i] . $file;
            if(file_exists($path))
            {
                require_once($path);
                return true;
            }
        }
        return false;
    }
}
?>
