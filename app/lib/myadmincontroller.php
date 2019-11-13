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
* Direct parent class admin web controllers.
*
* @package    Core
* @author     Arturo Lopez
* @copyright  Copyright (c) 2007-2019, Arturo Lopez
* @version    0.1
*/
 
abstract class MyAdminController extends MyWebController
{
    /**
     * Custom navigation menus displayed in the admin site.
     * @var array
     */
    var $admin_navigation = array(
            
        'Tools' => array(
            'Tasks' => 'admin/tasks',
            'Logs'  => 'admin/logs',
        ),

        'Other' => array(
            'Visit site' => 'index/index',
            'PhpLiteAdmin' => 'webroot/phpliteadmin/phpliteadmin.php',
        ),
    );

    //--
    //-- Admin Tasks
    //-- 

    var $admin_tasks = array(
            
            // Example task
            'hello_world' => array(
                'name'          => 'Hello World',
                'description'   => 'This task returns a hello world.',
                'expedite'      => true,
                ),
            
            // Example task
            'custom_greet' => array(
                'name'          => 'Custom Greet',
                'description'   => 'This task greets you with a custom message as many times as desired.',
                ),
            
            // Example task
            'example_sum' => array(
                'name'          => 'Simple Sum',
                'description'   => 'This task sums three numbers received as parameters and returns the result.',
                ),
            
            // Example task
            'cache_homepage' => array(
                'name'          => 'Generate homepage cache',
                'description'   => 'This task generates the cache of the home page in the hard-drive to serve it statically.',
                'expedite'      => true,
            ),
            
            // Example task
            'purge_cache_homepage' => array(
                'name'          => 'Delete homepage cache',
                'description'   => 'This task deletes the cache of the home page to serve it dynamically.',
                'expedite'      => false,
            ),
        );

    //--
    //-- Admin Tasks (implementation)
    //--
    
    /**
    * This task returns a hello world
    * @return  mixed   string
    */
    protected final function hello_world()
    {
        return("Hello world!");
    }
    
    /**
    * This task greets many times
    * @param   string  message
    * @param   int     times
    * @return  mixed   string
    */
    protected final function custom_greet($message, $times)
    {
        if(!is_numeric($times) || $times < 1 || $times > 50)
        {
            $this->flash->addDataError("The second parameter must be numeric, above 1 and below 50.");
            return(false);
        }

        for($i = 0; $i < $times; $i++)
        {
            $res .= $message ."\n";
        }
        return($res);
    }
     
    /**
    * Example task that returns the sum of three numbers
    * @param   int first_number
    * @param   int second_number
    * @param   int third_number
    * @return  mixed   String or false
    */  
    protected final function example_sum($first_number, $second_number, $third_number)
    {
        // Check parameters of errors to view
        if(!is_numeric($first_number) || !is_numeric($second_number) || !is_numeric($third_number))
        {
            $this->flash->addDataError("Parameters must be numeric, try again.");
            return(false);  // False indicates to stop
        }
        return("The result is ". ($first_number+$second_number+$third_number) .".");
    }
    
    /**
    * Generates the homepage cache 
    * @return   string
    */
    protected function cache_homepage()
    {
        $cache = new Cache("index/index");
        if($cache->create())
        {
            return("The homepage has been cached succesfully");
        } else {
            $this->flash->addDataError("Creating the cache of the homepage failed");
            return(false);
        }
    }
    
    /**
    * Deletes the the homepage cache 
    * @return   string
    */
    protected function purge_cache_homepage()
    {
        $cache = new Cache("index/index");
        if($cache->purge())
        {
            return("The homepage has been purged succesfully.");
        } else {
            $this->flash->addDataError("Purging the cache of the homepage failed.");
            return(false);
        }
    }
    
    //--
    //-- Custom methods below
    //--
}
?>
