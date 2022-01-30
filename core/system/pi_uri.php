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
* Parses requests and provides uri information
*
* @package    System 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
*/
 
class Pi_uri extends Pi_error_store
{
    /**
    * Complete uri string
    */
    private $request_string;
    
    /**
    * Array containing request
    */
    private $request_array = array();

    /**
     * Original request array
     */
    private $original_request_array = array();
    
    /**
    * Controller requested
    */
    private $controller      = DEFAULT_CONTROLLER;
    public  $controller_link = DEFAULT_CONTROLLER;

    /**
    * Action requested
    */
    private $action       = DEFAULT_ACTION;
    public  $action_link  = DEFAULT_ACTION;
    
    /**
    * Website base href
    */
    private $base_href;
    
    /**
    * Canonical for this request
    */
    private $canonical;
    
    /**
    * Parameters
    */
    private $parameters = array();

    /**
    * Stores original request if routed
    */
    private $original_request;
    
    /**
    * Total uri tokens
    */
    private $total;
    
    /**
     * Public l18n instance
     */
    public $l18n;

    /**
     * Array with lang change links
     */
    public $l18n_change_links;
    

    //----------------------------------------------------------
    
    /**
    * The uri might be provided manually
    *
    * @param    string    $uri
    * @param    string    $original_request
    */
    
    public function __construct($uri = NULL, $original_request = false)
    {
        // Request assignment
        if($uri != NULL)
        {
            $this->request_string = strtolower($uri);
               
        } else {
             
            // Calculate from query string 
            // Always lower caps to make canonicals valid
            //$request = strtolower($_SERVER['QUERY_STRING']);
            $request = strtolower($this->get_query_string());
            
            // Remove get params, anchors, start and trailing slash
            $request = preg_replace('/\?.*$/', '', $request);
            $request = preg_replace('/^\/|\/+$/', '', $request);
            $this->request_string = preg_replace('/#.*$/', '', $request);
        }    

        // Request received
        if($this->request_string != '')
             $this->request_array =array_values(array_filter(explode('/', $this->request_string), 'strlen'));
        
        // Original request received
        if($original_request != '')
             $this->original_request_array = array_values(array_filter(explode('/', strtolower($original_request)), 'strlen'));
        
        // Save base href once
        $this->base_href = Pi_core::get_base_href();
        
        // Create i18n instance
        $this->l18n = new Pi_l18n();
        
        // If lang enabled, must be in urls and no lang is hardcoded
        if(LANG_SUPPORT && LANG_IN_URLS)
        {
            // Supported lang in URL
            if($this->l18n->is_supported($this->request_array[0]))
            {
                if($this->l18n->change_lang($this->request_array[0]))
                {
                    array_shift($this->request_array);
                    $this->url_lang = $this->l18n->lang;
                }
            
            // No supported lang in URL
            // We need to redirect in a 301 manner
            // Only in live browsing.
            } else if(is_null($uri)) {

                // If post received
                // do not redirect.
                if(count($_POST) == 0)
                {
                    header("Location: ". $this->base_href . $this->l18n->lang .'/'. $this->request_string);
                    die;
                }
            }
            
            // Here, we define the L18N constant!
            if(strlen(CURL_BASE_URL) > 0)
                define('CURL_BASE_URL_L18N', CURL_BASE_URL . $this->l18n->lang .'/');
            else            
                define('CURL_BASE_URL_L18N', $this->base_href . $this->l18n->lang .'/');

            // Define base href as constant for lang changes
            define('PICARA_BASE_HREF', $this->base_href);
            
            // Edit base href to hold lang in templates
            $this->base_href = $this->base_href . $this->l18n->lang .'/';

        } else {
            
            // No lang support, define normal curl base url to avoid from failing
            if(strlen(CURL_BASE_URL) > 0)
            {
                define('CURL_BASE_URL_L18N', CURL_BASE_URL);
            } else {
                define('CURL_BASE_URL_L18N', $this->base_href);
            }
        }

        // Change links for l18n
        $this->l18n_change_links = $this->l18n->get_change_lang_links($this->request_array);
        
        // Count total arguments in query string
        $this->total = count($this->request_array);    
        
        // Set controller
        $this->_setController();
        $this->_setAction();
        $this->_setParameters();

        // Saves canonical url getting rid of trailing index/index string that might or not be there
        $this->canonical = preg_replace("/(\/index){0,2}\/*$/", '', $this->base_href . $this->controller .'/'. $this->action .'/'. implode('/', $this->parameters));
        
        // If routed, store original request
        $this->original_request = $original_request;
    }
    
    //----------------------------------------------------------

    /**
     * Returns server query string if available.
     * In Apache, query_string is present.
     * In PHP Dev server, request_uri is present.
     *
     * @return  string
     */
    private function get_query_string() 
    {
        $res = "";
        if(isset($_SERVER['QUERY_STRING'])) // Apache
        {
            $res = $_SERVER['QUERY_STRING'];
        } elseif(isset($_SERVER['REQUEST_URI'])) {  // PHP Dev Server
            $res = $_SERVER['REQUEST_URI'];
        }
        return($res);
    }
    
     
    //----------------------------------------------------------
     
    /**
    * Returns desired token
    *
    * @param    int       $index
    * @param    string    $default
    * @return   string
    */
     
     public function token($index, $default = false)
     {
         if(isset($this->request_array[$index]))
         {
             return $this->request_array[$index];
         
         } else {
         
             return $default;
         
         }
     }
     
    //----------------------------------------------------------
    
    /**
    * Returns requested controller
    * @param   bool    $routing    If routing, original value is returned
    * @return    string
    */
    
    public function controller($routing = false)
    {
        if($routing)
            return($this->request_array[0]);
        else
            return $this->controller;
    }
    
    //----------------------------------------------------------
    
    /**
     * Returns requested action
     * @param   bool    $routing    If routing, original value is returned
    * @return    string
    */
    
    public function action($routing = false)
    {
        if($routing)
            return($this->request_array[1]);
        else
            return $this->action;
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns request parameters
    * @return    array
    */
    
    public function parameters()
    {
        return $this->parameters;
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns base href
    * @return    string
    */
    
    public function base_href()
    {
        return $this->base_href;
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns canonical for this request
    * @return    string
    */
    
    public function canonical()
    {
        return $this->canonical;
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns complete request string
    * @return    string
    */
    
    public function uri()
    {
        return $this->request_string;
    }

    //----------------------------------------------------------    
    
    /**
    * Returns complete request as array
    * @return    array
    */
    
    public function uri_array()
    {
        return $this->request_array;
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns the full link to the present page
    *
    * @return    string
    */
    
    public function full_link()
    {
        return Pi_core::get_base_href() . $this->request_string;
    }
    
    //----------------------------------------------------------

    /**
    * Checks if we've been routed
    *
    * @return   bool
    */
    
    public function routed()
    {
        if($this->original_request != false)
            return true;
        return false;
    }

    //----------------------------------------------------------

    /**
    * Returns original request if routing has been performed
    *
    * @return   string|bool
    */

    public function get_original_request()
    {
        return $this->original_request;
    }

    //----------------------------------------------------------
    
    /**
    * Sets requested controller
    */
    
    private function _setController()
    {
        if($this->total > 0)
        {
            // To lowercase again just in case
            // Objects and functions in php are case insensitive
            $this->controller       = strtolower(str_replace("-","_",$this->request_array[0]));
            $this->controller_link  = strtolower(str_replace("-","_",$this->request_array[0]));

            if(isset($this->original_request_array[0]) > 0 && strlen($this->original_request_array[0]) >= 1)
                $this->controller_link  = strtolower(str_replace("-","_",$this->original_request_array[0]));


        }
    }
     
    //----------------------------------------------------------
    
    /**
    * Sets requested action
    */
     
    private function _setAction()
    {
        if($this->total > 1)
        {
            // Set to lowercase
            $this->action     = strtolower(str_replace("-","_",$this->request_array[1]));
            $this->action_link= strtolower(str_replace("-","_",$this->request_array[1]));
            
            if(isset($this->original_request_array[1]) && strlen($this->original_request_array[1]) >= 1)
                $this->action_link  = strtolower(str_replace("-","_",$this->original_request_array[1]));
        }
    }
     
    //----------------------------------------------------------
     
    /**
    * Sets requested parameters
    */
    
    private function _setParameters()
    {
        if($this->total > 2)
        {
            for($it = 2; $it < $this->total; $it++)
            {
                array_push($this->parameters, $this->request_array[$it]);
            }
        }
    }

    //----------------------------------------------------------
     
    /**
    * Fixes a requested url string cleaning up final slashes, request vars and redirection tokens
    * @param    string    $uri
    */
       
    private function fix($uri)
    {
        return preg_replace("/(^\/|\/?&.*|\/|\/:[A-Za-z0-9]*)$/", '', $uri);
    }
}
?>
