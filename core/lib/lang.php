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
* Implements the multi-lang messages system 
*
* @package      Libs
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Lang extends Pi_error_store
{  
    /**
    * Lang files extension
    */
    private static $extension = '.yml';

    /**
    * Loaded lang
    */
    private $lang;

    /**
    * Loaded controller
    */
    private $controller;

    /**
    * Loaded action
    */
    private $action;

    /**
    * Metadata instance
    */
    private $metadata;

    /**
    * Messages are stored here
    */
    public $messages = array();

    //--------------------------------------------------------

    /**
    * Loads language messages for received lang, controller and action
    *
    * @param    string    $lang
    * @param    string    $controller
    * @param    string    $action
    */

    public function __construct($lang, $controller, $action)
    {
        // Save info
        $this->lang = $lang;
        $this->controller = $controller;
        $this->action = $action;

        // Loads enabled langs
        $this->metadata = Pi_metadata::singleton();

        // Load everything
        $this->_load_lang_files();
    }

    //--------------------------------------------------------

    /**
    * Reloads lang files using another language if desired
    *
    * @param    string    $lang
    */

    public function reload($lang)
    {
        // Save new lang
        $this->lang = $lang;

        // Reset messages
        $this->messages = array();

        // Load new lang messages
        $this->_load_lang_files();
    }

    //--------------------------------------------------------

    /**
    * Loads both lang files (default and desired) into the object
    */

    private function _load_lang_files()
    {
        // Checks if desired lang is enabled in the application
        if(!$this->metadata->is_enabled_lang($this->lang))
            trigger_error("Lang '". $this->lang ."' is not enabled in the application", E_USER_ERROR);

        // Loads default lang messages if necessary
        //if($lang != DEFAULT_LANG && MESSAGES_CONSISTENCY == true)
        //    $this->_load(DEFAULT_LANG);

        // Loads desired lang files
        $this->_load($this->lang);
    }

    //--------------------------------------------------------

    /**
    * Loads into the object desired lang messages. Separater from constructor
    * cause this way we can load both, default and desired lang, in order to
    * fill non-existant messages with default lang messages.
    *
    * @param    string    $lang
    */

    private function _load($lang)
    {
        // Lang file name
        $lang_file = $lang . self::$extension;

        // Common project lang file
        $files[] = LANG . $lang_file;

        // Common controller lang file
        $files[] = LANG . $this->controller . '/' . $lang_file;

        // Specific lang file
        $files[] = LANG . $this->controller . '/' . $this->action . '/' . $lang_file;

        // All messages get read and stored
        for($it = 0; $it < 3; $it++)
            $this->_read_and_store($files[$it]);
    }

    //--------------------------------------------------------
    
    
    /**
    * Loads given langfile and stores each element into the messages object.
    * Global messages can be overriden by common, and common can be overriden
    * by specific.
    *
    * @param    string    $path
    */

    private function _read_and_store($path)
    {
        $messages = $this->_read_yml($path);
        $this->messages = array_merge($this->messages, $messages);
    }
    
    //--------------------------------------------------------

    /**
    * Loads given langfile from a path and returns all messages stored
    *
    * @param    string    $path
    * @return   array
    */
    
    private function _read_yml($path)
    {
        // If file exists
        if(file_exists($path))
        {
            // Parse yml natively
            $messages = yaml_parse(file_get_contents($path)); 
            if(is_null($messages)) $messages = array();
            
            // return
            return $messages;
        } 
        
        // Empty array is returned
        return array();
    }
}
?>
