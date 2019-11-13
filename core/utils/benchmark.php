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
* This utility is used to measure time elapsed between two points of code
*
* @package    Utils 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class Benchmark extends Pi_overloadable
{
    /**
    * Load mode for load class
    */
    public static $load_mode = STANDARD;

    /**
    * Load name for load class
    */
    public static $load_name = 'benchmark';

    /**
    * Array containing all stamps
    */
    private $stamps = array();
    
    //----------------------------------------------------------

    /**
    * Saves current time stamp with the given name
    *
    * @param string $name
    * @return bool
    */

    public function mark($name)
    {
        if(isset($this->stamps[$name]))
        {
        	$msg = "Timestamp $name has been already stored";
        	$this->storeError($msg);
            trigger_error($msg, E_USER_WARNING);
            return false;
        }
        
        $this->stamps[$name] = microtime(true);
    }
    
    //----------------------------------------------------------

    /**
    * Gets current timestamp
    *
    * @return    int
    */

    private function now()
    {
        return microtime(true);
    } 
    
    //----------------------------------------------------------

    /**
    * Retrieves given timestamp
    *
    * @param string $name
    * @return float|false
    */

    private function getMark($name)
    {
        if(!isset($this->stamps[$name]))
        {
        		$msg = "Timestamp $name is not stored";
        		$this->storeError($msg);
            	trigger_error($msg, E_USER_WARNING);
                return false;
        }

        return $this->stamps[$name];
    } 
    
    //----------------------------------------------------------

    /**
    * Returns time elapsed between two stamps
    *
    * @param    string    $name1
    * @param    string    $name2
    * @return   float
    */

    private function measure($name1, $name2 = NULL, $measurement = 1)
    {
            // Obtain first stamp value
            if(!isset($this->stamps[$name1]))
            {
                $msg = "Timestamp $name1 is not stored";
        		$this->storeError($msg);
            	trigger_error($msg, E_USER_WARNING);
                return false;
            }

            $stamp1 = $this->stamps[$name1];

            // Obtain second timestamp. If null, now will be used
            if($name2 != NULL)
            {
                    $stamp2 = $this->getMark($name2);

                    if(!$stamp2)
                        return false;

            } else {

                $stamp2 = microtime(true);
            
            }
            return abs(($stamp1 - $stamp2) * $measurement);
    }  
    
    //---------------------------------------------------------- 
    
    /**
    * Magic method to create time elapsed functions like
    *
    * elapsed_microseconds() 
    * elapsed_miliseconds() 
    * elapsed_seconds() 
    * elapsed_minutes() 
    * elapsed_hours() 
    *
    * @param    string    $name1
    * @param    string    $name2
    */

    protected function __magic($method, $arguments)
	{    
		/*
		* elapsed_[measure]
		*/
		if(preg_match("/^elapsed_(microseconds|miliseconds|seconds|minutes|hours|time)$/", $method))
		{
			switch(preg_replace("/^elapsed_/",'',$method))
			{
				case 'microseconds': $measurement = 1000000; break;
				case 'seconds': $measurement = 1; break;
				case 'minutes': $measurement = 1/60; break;
				case 'hours': $measurement = 1/3600; break;
				default: $measurement = 1000;
			}
			
			// Second argument
			if(!isset($arguments[1]))
				$name2 = NULL;
			else
				$name2 = $arguments[1]; 
				
			return $this->measure($arguments[0], $name2, $measurement);
		}
		
        $this->method_does_not_exist($method);	
	}   
}
?>
