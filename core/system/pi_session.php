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
* Implements the session management
*
* @package      System 
* @author       Arturo Lopez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/
class Pi_session
{
    /**
    * Session id
    */
    private $id;
    
    /**
    * Singleton pattern
    */
    private static $instance;

    /**
    * Reserved names
    */
    private static $reserved = array('picara');
    
    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object
    */
    private function __construct()
    {
        $this->id = @session_id();
    }
    
    //----------------------------------------------------------

    /**
    * Returns a new object or the pointer to existing one
    *
    * @return     Session
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
    * Keeps alive session when navigating
    */
    public static function keepAlive()
    { 
        session_name(SESSION_NAME);
        session_start();
    } 
    //----------------------------------------------------------
    
    /**
    * Sets or returns the session name (as created by the session class).
    * The php session name is unique!
    *
    * @param     string    $name
    * @return    string
    */
    public static function name($name = NULL)
    {
        // Existing session name
        //$existing_name = session_name();

        if($name != NULL)
        {
            //session_name(SESSION_NAME);
            $_SESSION['picara']['session_name'] = $name;
            $_SESSION['picara']['REMOTE_ADDR'] =  $_SERVER['REMOTE_ADDR'];
            
        } else if(isset($_SESSION['picara']['session_name'])) {
        
            return($_SESSION['picara']['session_name']);
        }
    }
    
    //----------------------------------------------------------
    
    /**
    * Stores a variable or object into the session
    * 
    * @param    string    $name 
    * @param    mixed     $whatever
    */
    public static function store($name, $whatever)
    {
        // Objects cannot be stored
        if(is_object($whatever))
            trigger_error('Objects cannot be stored in session variables', E_USER_ERROR);
            
        // If reserved name
        if(in_array($name, self::$reserved))
            trigger_error("Session variable $name is reserved for the application and cannot be stored", E_USER_ERROR);
            
        $_SESSION[$name] = $whatever;
    }
    
    //----------------------------------------------------------

    /**
     * Deletes given session variable
     *
     * @param     string    $name
     */
    public static function dispose($name)
    {   
        // If reserved name
        if(in_array($name, self::$reserved))
            trigger_error("Session variable $name is reserved for the application and cannot be disposed", E_USER_ERROR);
            
        if(isset($_SESSION[$name]))
        {
            unset($_SESSION[$name]);
        }
    }
    
    //----------------------------------------------------------
    
    /**
     * Retrieves requested variable name
     *
     * @_SERVER['REMOTE_ADDR'] param     string $name
     * @return     mixed
     */
    public static function read($name)
    {
        if(!isset($_SESSION[$name]))
            return false;

        return $_SESSION[$name];
    }
    
    //----------------------------------------------------------

    /**
    * Retrieves all declared session variables and values as array
    *
    * @return     array
    */
    public function readAll()
    {
        return $this->_getUserVars();
    }
    
    //----------------------------------------------------------

    /**
     * Retrieves how many session variables are declared
     *
     * @return     int
     */
     public function count()
     {
        return count($this->_getUserVars());
     }

    /**
     * Retrieves names of all session variables declared
     *
     * @return     array
     */
     public function getNames()
     {
        foreach($this->_getUserVars() as $key => $value)
            $names[] = $key;

        return $names;
     }
     
    //----------------------------------------------------------

    /**
     * Checks if given variable name is declared as session variable
     *
     * @param     string
     */
    public function exists($name)
    {
        if(isset($_SESSION[$name]))
                return true;

        return false;
    } 
    
    //----------------------------------------------------------
    
    /**
    * Checks if the given session name is opened
    * @param     mixed $sdata   Array or string
    * @return     bool
    */
    public static function check($sdata)
    {
        // Local array if string
        if(is_string($sdata)) 
            $larr = array($sdata);
        else  
            $larr = $sdata;
        
        // Iterate  array of sessions
        for($i = 0; $i < count($larr); $i++)
        {
            if(session_name() == SESSION_NAME && 
               $_SESSION['picara']['REMOTE_ADDR'] == $_SERVER['REMOTE_ADDR'] &&
               $_SESSION['picara']['session_name'] == $larr[$i])
            {
                return true;
            }
        }
        return false;
    }
    
    //----------------------------------------------------------
    
    /**
    * Destroys session and stored session variables, leaving controller messages intact.
    */
    public function kill()
    {
        // Unset session vars
        foreach($_SESSION as $key => $var)
        {    
            if(!in_array($key, self::$reserved))
                unset($_SESSION[$key]);
        }

        // Unset picara session vars
        unset($_SESSION['picara']['session_name']);
        unset($_SESSION['picara']['REMOTE_ADDR']);
        //session_unset(); // No need! Session is controlled using session variables!
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns an array containing just user session variables
    * @return     array
    */
    private function _getUserVars()
    {
        foreach($_SESSION as $key => $value)
        {
            // If reserved name
            if(!in_array($key, self::$reserved))
                $result[$key] = $value;
        }
        return $result;
    }
       
}
?>
