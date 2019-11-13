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
* Provides information about the visitor user agent
*
* @package      Utils
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Visitor extends Pi_overloadable
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'visitor';

    /**
    * Private instance
    */
    private static $instance;
    
    /**
    * Recognized platforms
    */  
    private $platforms;

     /**
    * Recognized browsers
    */
    private $browsers;

    /**
    * Recognized mobiles
    */
    private $mobiles;

    /**
    * Recognized robots
    */  
    private $robots;

    /**
    * Assigned platform
    */ 
    private $platform = false;

    /**
    * Assigned browser
    */
    private $browser = false;
    
    /**
    * Assigned mobile
    */
    private $mobile = false;
    
    /**
    * Assigned robot
    */
    private $robot = false;
   
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
    * Loads the user agent list and sets up the object to retrieve data  
    */
    private function __construct()
    {
        $user_agents_file = CONFIG . 'user_agents.php';

        // Load user agents list
        if(file_exists($user_agents_file))
        {
            include($user_agents_file);
            
        } else {
            trigger_error('User agents config file does not exist', E_USER_ERROR);
        }

        // Once loaded, data is stored in our object and check performed
        $data = array('platforms','browsers','mobiles','robots');

		// For each data element
        foreach($data as $name)
        {
            // Browser user agent string 
            $user_agent_string = $this->agent_string();

            // If array exists in included config file
            if(isset(${$name}))
            {
                if(is_array(${$name}))
                {
                    $this->$name = ${$name};

                    // Search over user agents attributes
                    $target = preg_replace("/s$/",'',$name);

                    foreach($this->$name as $key => $value)
                    {
                        // If found, then name is assigned
                        $search = stripos($user_agent_string, $key);

                        // If found, name is assigned 
                        if(is_numeric($search))
                        {
                            $this->$target = $value;
                        }
                    }
                }
            }     
        }
    }
    
    //----------------------------------------------------------

    /**
    * Retrieves full user agent string
    *
    * @return  string 
    */

    public function agent_string()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    
    //----------------------------------------------------------

    /**
    * Returns explorer version
    *
    * @return  string
    */

    public function version()
    {
       $res = preg_match("/\([^;]*;/", $this->agent_string() , $matches);

        if(!$res)
            return "Unknown browser version";

         return preg_replace("/(\(|;)/",'', $matches[0]); 
    }
    
    //----------------------------------------------------------

    /**
    * Returns if the visit is referal
    *
    * @return bool
    */

    public function is_referal()
    {
        if(isset($_SERVER['HTTP_REFERER']))
            return true;

        return false;
    } 
    
    //----------------------------------------------------------

    /**
    * Returns referer
    *
    * @return  string
    */

    public function getReferer()
    {
        return $_SERVER['HTTP_REFERER'];
    } 
    
    //----------------------------------------------------------

    /**
    * Magic functions to implemets the following functions 
    * is ( Browser | Mobile | Robot )
    * get ( Browser | Mobile | Robot )
    */

    protected function _magic($method, $arguments)
    {
        /*
        * Function: is ( Browser | Mobile |  Robot )
        */

        if(preg_match("/^is(Browser|Mobile|Robot)$/", $method))
        {
            $target = strtolower(preg_replace("/^is/",'', $method));

            if($this->$target != false)
                return true;

            return false;         
        }

        /*
        * Function: is ( Browser | Mobile |  Robot )
        */

        if(preg_match("/^get(Browser|Mobile|Robot|Platform)$/", $method))
        {
            $target = strtolower(preg_replace("/^get/",'', $method));
            return $this->$target;
        }

        $this->method_does_not_exist($method);
    } 
}
?>
