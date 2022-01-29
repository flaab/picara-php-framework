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
* Implements core important framework functions, which are probably called from
* many file of the application code. Handles libraries loading, autoloading, database connection 
* and many other important issues.
*
* @package      System
* @author       Arturo Lopez 
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/

class Pi_core
{
    /**
    * Self instance
    */
    private static $instance;
    
    /**
    * Connection information
    */
    private $connection_info = NULL;

    //----------------------------------------------------------

    /**
    * Private constructor to avoid direct creation of object
    */
    private function __construct(){}
    
    //----------------------------------------------------------    
    
    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return   Pi_core
    */
    public static function singleton() 
    {
        if (!isset(self::$instance))
        {
            $c = __CLASS__;
            self::$instance = new $c;
        }
    return self::$instance;
    }
    
    //---------------------------------------------------------- 
    
    /**
    * Executes any Controller/action request given and renders the view at any part of the layout or view code.
    *
    * @param    string    $handuri 
    */
    
    public static function requestAction($handuri)
    {
        // Controller execution
        require(ACTION . 'CreateDispatcher.php');
        require(ACTION . 'ExecuteController.php');
        require(ACTION . 'RenderView.php');
    }
    
    //----------------------------------------------------------      
    
    /**
    * Performs a soft framework redirection from any controller to another.
    * A soft redirection should be done if the target paged is cached.
    *
    * @param    string    $handuri
    * @example  core/redirect.php 
    */
    
    public function soft_redirect($handuri)
    {
        require('index.php');
        //header('Location: '. $this->get_base_href() . $handuri);
        exit();
    }
    
    //----------------------------------------------------------      
    
    /**
    * Performs an http redirection to an uri provided
    *
    * @param    string    $handuri
    * @example  core/redirect.php 
    */
    
    public function redirect($handuri)
    {
        //require('index.php');
        header('Location: '. $this->get_base_href() . $handuri);
        exit();
    }

    //----------------------------------------------------------    
    
    /**
    * Aborts showing given message or displays 404 error page
    *
    * @param    string    $explanation
    * @param    string    $title
    */
    public static function abort($explanation = 'A critical error has happened', $title = 'Execution aborted')
    {
        // If the dispatcher is verbose, the explanation is showed on screen. If not, error 404
        if(ENVIRONMENT == "testing")
        {
            Pi_core::quit($explanation, $title);
        
        } else {
            Pi_core::http_error(404);
        }
    }
    
    //----------------------------------------------------------    
    
    /**
    * Displays the given error page
    *
    * @param    int    $error
    * @example  core/error_page.php
    */
    public static function http_error($error)
    {
        // Check for a 404 page
        $page = PAGES . $error . '.php';

        // Return actual 404
        http_response_code($error); 

        // Load error page
        if(!file_exists($page))
            trigger_error("The error page '". $error ."php' does not exist, please create it.", E_USER_WARNING);
        else
            include($page);
        exit();
    }
    
    //----------------------------------------------------------
    
    /**
    * Halts the application and displays a message
    *
    * @param    string    $title 
    * @param    string    $explanation
    * @example  core/abort.php
    */
    public static function quit($explanation = 'A critical error has happened', $title = 'Execution aborted')
    {    
        echo("<h1>$title</h1>");
    
        if(is_array($explanation))
        {
            for($i=0; $i < count($explanation); $i++)
            {
                echo("<p>". $explanation[$i] ."</p>");    
    
            }    
    
        } else {
    
        echo("<p>".$explanation ."</p>");
        }

        exit();
    }    
    
    //----------------------------------------------------------     
     
    /**
    * Returns the base href    
    **/
    
    public static function get_base_href()
    {
        // If string and strlen ok
        if(defined('CURL_BASE_URL') && is_string(CURL_BASE_URL) && strlen(CURL_BASE_URL) > 7)
        {
            // Trim string and append trailing slash if missing
            return(preg_replace("/([^\/])$/", "$1/", trim(CURL_BASE_URL)));
        }

        // Protocol
        if(isset($_SERVER['HTTPS']))
        {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }
        
        // Port
        if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
            $port = ":". $_SERVER["SERVER_PORT"];
        else
            $port = "";

        // Base href string
        return($protocol . '://'. $_SERVER['SERVER_NAME'] . $port . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']) .'/');
    }
}
?>
