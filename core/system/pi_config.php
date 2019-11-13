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
* Provides model configuration to the whole application.
* This way, configuration will be common for all instances of 
* the same model, and available from controllers.
*
* This means all instances of the same model will have a
* internal object called config with their configuration
* parameters, and changing it from one will affect all
* instaced objects as well.
*
* It wouldn't be a bad idea to let changes became permanent,
* by rewriting the file with changes to disk. Or making them
* permanent just for current session.
*
* There are no duplicated models, so each model will create a
* standard object.
*
* @package      System
* @author       Arturo Lopez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/

class Pi_config extends Pi_error_store
{
    /**
    * Instance for singleton pattern
    */
    private static $instance;

    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object. 
    */
    
    private function __construct()
    {
    }
    
    //---------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return    Pi_config
    */
    
    public static function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    //----------------------------------------------------------

    /**
    * Provides configuration object for requested model
    *
    * @param    string    $model
    * @return   object
    */

    public function get($model)
    {
        // If previously requested 
        if(isset($this->{$model}) && is_object($this->{$model}))
        {
            return($this->{$model});    
        }

        // Must be created first
        $this->_create_config($model);
        
        // There it goes
        return $this->{$model};
    }
    
    //--------------------------------------------------------

    /**
    * Reads a level 1 configuration atribute from a model
    *
    * @param    string    $model
    * @param    string    $attr
    * @return   mixed
    */

    public function read($model, $attr)
    {
        $c = $this->get($model);
        return $c->$attr;
    }
    
    //--------------------------------------------------------

    /**
    * Reads and creates configuration object for requested model,
    * if the config array is stored in the session, it will be loaded
    * from there.
    *
    * @param    string    $model
    */

    private function _create_config($model)
    {
        // If configuration has not been stored in session
        if(!isset($_SESSION['picara']['model_config'][$model]))
        {
            // Path to config file
            $config_file = MODELCONFIG . $model . '.yml';
    
            // Try to load it
            if(!file_exists($config_file))
                trigger_error("Configuration file for model '$model' does not exist, please check model and config file ($config_file)", E_USER_ERROR);

            // Read
            $configuration = yaml_parse(file_get_contents($config_file)); 
        
        } else {

            // Configuration stored in session
            $configuration = $_SESSION['picara']['model_config'][$model];
        }
      
        // An array called configuration must exist
        if(!isset($configuration) || !is_array($configuration))
                trigger_error("Configuration file for model '$model' is not correct ($config_file)", E_USER_ERROR);
        
        // If empty
        if(count($configuration) == 0) trigger_error("Configuration array for model '$model' is empty", E_USER_ERROR);

        // Assign
        $this->{$model} = $this->_array_to_object($configuration);
    }

    //--------------------------------------------------------

    /**
    * Makes permanent a model configuration for current session,
    * by saving the array in a session variable; this allows the programmer
    * to interact and change any model behaviour without being attached to
    * the default model configuration.
    *
    * @param    string    $model
    * @return   bool
    */
    
    public function permanent_for_session($model)
    {
        // If config has been previously requested, we can store it in session
        if(isset($this->{$model}) && is_object($this->{$model}))
        {
            $_SESSION['picara']['model_config'][$model] = $this->_object_to_array($this->{$model});
            return true;
        }

        trigger_error("Configuration for model '$model' has not been loaded", E_USER_WARNING);
        return false;
    }

    //--------------------------------------------------------

    /**
    * Resets a model configuration and returns it
    *
    * @param    string    $model
    * @return   object
    */

    public function reset($model)
    {
        if(isset($this->{$model})) unset($this->{$model});
        if(isset($_SESSION['picara']['model_config'][$model])) unset($_SESSION['picara']['model_config'][$model]);
        return $this->get($model);
    }

    //--------------------------------------------------------

    /**
    * Returns an object representing an N-dimensional array.
    * Hashes are converted into objects, strings, numbers and
    * arrays are respected.
    *
    * @param    array    $a
    * @return   object
    */

    private function _array_to_object($a)
    {
        $result = new StdClass();

        foreach($a as $key => $value)
        {
            if($this->_is_hash($value))
                $result->{$key} = $this->_array_to_object($value);
            else
                $result->{$key} = $value;
        }

        return $result;
    }

    //--------------------------------------------------------

    /**
    * Converts an object into a session storable array
    *
    * @param    object    $obj
    * @return   array
    */

    private function _object_to_array($obj)
    {
        $result = array();

        foreach($obj as $key => $value)
        {
            if(is_object($value))
                $result[$key] = $this->_object_to_array($value);
            else
                $result[$key] = $value;
        }

        return $result;
    }

    //--------------------------------------------------------

    /**
    * Checks if given array is a hash
    *
    * @param    array    $a
    * @return   bool
    */

    private function _is_hash($a)
    {
        if(!is_array($a)) return false;
        $keys = array_keys($a);
        $tot = count($keys);
        for($it=0; $it < $tot; $it++)
            if(is_string($keys[$it])) return true;
        return false;
    }
}
?>
