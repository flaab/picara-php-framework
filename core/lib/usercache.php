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
* Assists the user in the creation of cache html documents that can be embedded
* wherever neccesary. All you need is a template and a generation directory.
*
* @package    Libs
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/
  
class UserCache extends Pi_overloadable
{   
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'UserCache';

    /**
    * Private instance
    */
    private static $instance;
    
    /**
    * MyCache instance
    */
    private $my_cache;

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
    * Private constructor that instances MyCache library from user
    * To access all functions there declared.
    */
    private function __construct()
    {
         $this->my_cache = new MyCache();
    }
    
    //--------------------------------------------------------
    
    /**
    * Receives a name and parameters to generate. Template must be in its place.
    * And directories created with proper permissions.
    *
    * @param    string    $name          Name of the element to generate
    * @param    array     $params        Hash of information to deliver
    * @return   bool
    */

    public final static function generate($name, $params = array())
    {
        // Plantilla
        $template =  APP_TEMPLATES . $name .'.tpl.php';

        // Ruta de generacion
        $path = APP_CACHE . $name .'.php';
        
        // I call myself static mode
        return UserCache::generate_free($template, $path, $params);
    }
    
    //--------------------------------------------------------

    /**
    * The real generation process, available for all those who want to skip the generation rule.
    *
    * @param    string    $template      Template to read
    * @param    string    $path          Path to write generation
    * @param    array     $params        Hash of information to deliver
    * @return   bool
    */

    public final static function generate_free($template, $path, $params = array())
    {
        // Instance of tpl and assign model
        $tpl = new Template($template);
        
        // Assign variables
        foreach($params as $key => $value)
            $tpl->set($key, $value);
        
        // Save the result into path
        if(!$tpl->save($path))
            trigger_error('I could not write '. $path, E_USER_ERROR);
        
        // OK
        return(true);
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
        // Execute function against my cache
        return $this->my_cache->{$method}();
    }
}
?>
