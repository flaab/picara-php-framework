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
* Implements the cache files loading for the dispatcher and
* a bunch of inheritable functions for the cache creator class.
*
* @package      System
* @author       Arturo Lopez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/

class Pi_cache extends Pi_overloadable
{
    /**
    * Original request
    */
    protected $uri;

    /**
    * Stores cache directory for current uri
    */
    public $cache_dir;

    /**
    * Stores cache directory for current uri and lang (if enabled)
    */
    public $cache_dir_with_lang;

    /**
    * Stores full patch to cache file for current uri
    */
    public $cache_file;

    /**
    * Stores full path to cache file to be loaded (standard or lang)
    */
    private $load_file;

    /**
    * Log object
    */
    protected $log;

    //--------------------------------------------------------

    /**
    * Receives a framework Pi_uri object from the dispatcher
    *
    * @param    Pi_uri    $uri
    */

    public function __construct($uri)
    {
        $this->uri = $uri;
        $this->cache_dir = $this->get_dir();
        $this->cache_dir_with_lang = $this->get_dir_with_lang();
        $this->cache_file = $this->get_file();
        $this->log = Pi_logs::singleton();
    }

    //--------------------------------------------------------

    /**
    * Checks if a cache file exists for current uri
    *
    * @return   bool
    */

    public final function exists()
    {
        if(file_exists($this->cache_file)) return true;
        return false;
    }

    //--------------------------------------------------------

    /**
    * Calculates the path to this request cache files for current lang (if enabled)
    *
    * @return   string
    */

    public final function get_dir()
    {
        // Basic path to cache files
        return CACHE . strtolower($this->uri->controller()) . '/'. strtolower($this->uri->action()) . '/';
    }

    //--------------------------------------------------------

    /**
    * Calculates path to current lang cache files. If not enabled, false is returned.
    *
    * @return   string
    */

    public final function get_dir_with_lang()
    {
        // Lang support must be enabled
        if(LANG_SUPPORT == true)
        {
            // If a lang has been forced from generating process
            if(isset($this->lang) && $this->lang != false)
            {
                $lang = $this->lang;

            } else {
            
                // Lang not forced. Environment lang must be used
                if(isset($_SESSION['picara']['lang']))
                    $lang = $_SESSION['picara']['lang'];
                else
                    $lang = DEFAULT_LANG;
            }

            // Append lang dir
            return $this->cache_dir . $lang . '/';

        } else {

            return false;

        }
    }

    //--------------------------------------------------------

    /**
    * Calculates full path to cache file for current request
    *
    * @return   string
    */

    public final function get_file()
    {
        // File name
        $cache_file = $this->calculate_file_name();
        
        // If lang cache must be loaded
        if($this->cache_dir_with_lang != false)
            $cache_final_file = $this->cache_dir_with_lang . $cache_file;
        else
            $cache_final_file = $this->cache_dir . $cache_file;
        
        return $cache_final_file;
    }

    //--------------------------------------------------------

    /**
    * Calculates the appropiate file name for this cache file according request parameters
    *
    * @return   string
    */

    protected final function calculate_file_name()
    {
        // Parameter imploding
        $file_name = implode('-', $this->uri->parameters()) . STATIC_EXT;
       
        // If no parameters, file will be named default
        if(preg_match("/^\..*$/", $file_name))
            $file_name = 'default' . STATIC_EXT;

        return $file_name;
    }

    //--------------------------------------------------------

    /**
    * Loads appropiate cache 
    *
    * @return   bool
    */

    public final function load()
    {
        // Post variables implies dinamic content, not loaded
        if(count($_POST) > 0) return false; 

        // If file does not exist
        if(!$this->exists()) return false;

        // If failed to load
        if(!@include($this->cache_file))
        {
            $this->log->error('Unexpected error loading cache file '. $this->cache_file);
            return false;
        }
        // No need to inform when a cache is loaded (slow down response (slow down response)) 
        //$this->write_cache_log('message','Cache file '. $this->cache_file .' loaded successfully');
        return true;
    }

    //--------------------------------------------------------

    /**
    * Checks existance of signature file to authenticate the cache request
    *
    * @param    string    $signature
    */

    public final function check_signature($signature)
    {
        $file = TMP . "signature$signature";
        
        if(file_exists($file))
        {
            if(!@unlink($file))
            {
                $this->log->error('I do not have enough permissions to delete signature file '. $file);
                trigger_error($msg, E_USER_ERROR);
            }
            return true;   
        }
        return false;
    }
}
?>
