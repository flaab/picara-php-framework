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
* Receives and processes all framework requests, creating and executing
* requested controller and serving information to the underneath application. It is
* also responsable of applying dispatcher routing rules and serving static documents from
* the application cache.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class Pi_web_dispatcher extends Pi_error_store
{      
    /**
    * Uri instance
    */
    var $uri;

    /**
    * Original unstripped request
    */
    var $request;
   
    /**
    * Default view path
    */
    var $viewPath;

    /**
    * Is request meant to be an RESTful API call?
    */
    var $is_api_call = false;

    /**
    * Controller instance
    */
    var $myController;
  
    /**
    * Signature file for generating cache files
    */
    var $signature = NULL;

    /**
    * Cache loader object
    */
    private $cache;

    /**
    * Indicates if we are generating an static HTML document
    */
    private $generating = false;
    
    /**
    * Indicates if we shall load cache file if available
    */
    private $load_cache = true;

    /**
    * Controller path
    */
    private $controllerPath;
   
    /**
    * Controller class name
    */
    private $controllerName;

    /**
    * Valid generation request
    */
    private $valid_generation;
   
    /**
    * Forbidden actions to be called; public functions of parent classes to prevent to be called from url
    */
    private static $forbidden_functions = array('storeError','getErrorStore','displayErrorStore','failed','isOk','cleanErrorStore');
   
    //----------------------------------------------------------   
   
    /**
    * Sets the dispatcher up, performs redirection if needed and serves static cache files
    *
    * @param     string    $handUri        Desired request to be executed
    * @param     bool      $generating     Indicates if a cache file is being created. Use with caution.
    */
    
    function __construct($handUri = NULL, $generating = false)
    {
        // If generating errors will not be thrown and the generation process won't fail
        $this->generating = $generating;
           
        // Do not load cache if redirecting or requesting an action
        if($handUri != NULL)    $this->load_cache = false;                          
    
        // New uri object
        $this->uri = new Pi_uri($handUri);
        
        // Routing perform
        $route = new Pi_route($this->uri);
   
        // If redirection has to be made, new uri is stored
        if($route->redirection())
            $this->uri = $route->get_redirection_uri();

        // New cache object
        $this->cache = new Pi_cache($this->uri);

        // Assume it is not an aPI call
        $this->is_api_call = false;

        // Check generation request
        $this->valid_generation = $this->valid_generation_request();
        
        //if($this->valid_generation != true && $generating == false)
        //    $this->display_base_href();
        
        //echo "<pre>";
        //print_r($_SESSION);
        //echo "</pre>";

        // If we are not generating, and we are not user_session or admin_session
        if($this->load_cache == true)
        {
            // Cache tries to be loaded, if not, execution goes on
            if($this->cache->load())  exit();
        }
        
        // Load the request and check if it is valid
        $this->validRequest();
    }
       
    //----------------------------------------------------------

    /**
    * Indicates if cache generation request is received and valid
    *
    * @return   bool
    */

    private function valid_generation_request()
    {
        // Post signature must have been received
        if(isset($_POST['picara_signature']))
        {
            // Check for signature file
            if($this->cache->check_signature($_POST['picara_signature']))
            {
                // Store valid signature
                $this->signature = $_POST['picara_signature'];

                // Post forced lang is also checked
                if(isset($_POST['picara_lang']))
                    $_SESSION['picara']['lang'] = $_POST['picara_lang'];

                return true;
            }
        } 

        // Not received or not valid
        return false;        
    }

    //----------------------------------------------------------
  
    /**
    * Checks if the requested action exist inside the controller
    *
    * @return    bool
    */
    private function validRequest()
    {
        $this->loadController();
        $this->validAction();    
        $this->validView();
        $this->validArgs();    
        return true;        
    }
       
    //==========================================================
    // CACHE CREATION FUNCTIONS
    //==========================================================
       
    /**
    * Checks the authenticity of a cache creation request by checking 
    * signature post vars. If it is, signature is stored and forced lang
    * is checked.
    *
    * @param    string    $signature 
    */
    
    private function setUpCache($signature)
    {
        // Checks generation request authenticity
        if($this->cache->check_signature($signature))
        {
            // Stores signature for further view loading
            $this->signature = $signature; 

            // Checks if any lang should be forced
            if(isset($_POST['picara_lang']))
            {
                // Assigs lang. This way the controller will load proper langfiles.
                $_SESSION['picara']['lang'] = $_POST['picara_lang'];
            }
        }
    }
      
    //----------------------------------------------------------
    
    /**
    * Checks if signature file exist to validate the authenticity of a cache generation request
    * @param string $signature
    * @return bool
    */
    
    private function checkSignature($signature)
    {
        $file = CACHE . "signature$signature";
        
        if(file_exists($file))
        {
            if(!@unlink($file))
            {
                trigger_error("I don't have enough permissions to delete $file.", E_USER_ERROR);
            }
            return true;   
        }
        return false;
    }
     
    //=========================================================================
    // REQUEST VALIDATION
    //=========================================================================
    
    /**
    * Checks if the requested controller exists by trying to load it    
    */
    
    private function loadController()
    {
        if(!Pi_loader::load_controller($this->uri->controller()))
        {
            $this->abort("Generate it using the shell script <i>php scripts/picara create controller ". $this->uri->controller() ."</i>.",
                         'Requested controller does not exist');
        } else {
                
           // Save controller name 
           $this->controllerName = $this->uri->controller() . 'WebController';
           
           // Store controller object
           $this->myController = new $this->controllerName();

           // Path to controller file
           $this->controllerPath = CONTROLLER . $this->uri->controller() . '.php';  

           // Store if this is an API call
           $this->is_api_call = isset($this->myController->api_settings[$this->uri->action()]);
        }
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns declared public methods in the user web controller.
    * It ignores protected, final and underscored methods. 
    * @param    string  $controller Name of controller
    * @return   array
    */
    protected final function getControllerMethods(string $controller)
    {
        // Results
        $res = array('native' => array(), 'inherited' => array());

        // Make the call
        $reflection = new ReflectionClass($controller);
        $methods    = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        
        // For each result
        for($i = 0; $i < count($methods); $i++)
        {
            if($methods[$i]->class == $controller)
                $res['native'][] = $methods[$i]->name;
            else
                $res['inherited'][] = $methods[$i]->name;
        }
        
        // ok
        return($res);
    }
    //----------------------------------------------------------
       
    /**
    * Checks if the requested action is valid and raise a error if not.
    * @return  bool
    */
    
    private function validAction()
    {   
        // Change - to _
        $l_action = $this->uri->action();

        //--
        //-- First things first: check if method is underscored.
        //--  
        
        // Underscored methods are not allowed.
        if(preg_match("/^\_.*$/", $l_action))
            $this->abort("Underscored methods can't be used as public actions.", 'Invalid action');
        
        //--
        //-- Get controller public methods and public inherited methods.  
        //-- Only accept calls to controller public methods. This avoids
        //-- accepting requests for admin tasks and other methods declared
        //-- in the users' MyController and MyShellController classes.
        //-- All other inherited methods in the framework are final.
        //-- 
        
        // Get public controller methods, native and inherited.
        $controller_methods = $this->getControllerMethods(get_class($this->myController));
        
        // Ignore inherited public functions
        if(in_array($l_action, $controller_methods['inherited'])) 
                $this->abort("Method ". $l_action ." cannot be called or re-declared, it belongs to a parent class.");

        // If belongs explicitly to the user controller -without inheritance!-
        if(!in_array($l_action, $controller_methods['native']))
        { 
            // Requested method would be valid but does not exist 
            $this->abort("Create the ". $l_action ." method in <i>". $this->controllerPath ."</i>.",
                'Requested action doex not exist');
        }
        
        // Method exists, but it can be an API not accepting this request method
        if($this->is_api_call && !in_array(strtoupper($_SERVER['REQUEST_METHOD']), $this->myController->api_settings[$l_action]))
            $this->abort("Method ". $l_action ." is not configured to accept ". $_SERVER['REQUEST_METHOD'] ." requests.");
        

        // Function exists
        return(true);
    }
              
    //----------------------------------------------------------
              
    /**
    * Checks if the associated view exists
    * @return bool
    */
        
    private function validView()
    {
        // If controller has not been created return is forced
        if(!is_object($this->myController)) return false;
        
        // Change - to _
        $l_action = $this->uri->action();
        
        // View path
        $this->viewPath = $this->myController->get_view_path($l_action);
        
        // If no view and this call is not an api, exit 
        if(!$this->is_api_call && (!file_exists($this->viewPath) || !$this->viewPath))
        {
            $this->abort("Create it at <i>". VIEW . $this->uri->controller() ."/". $l_action .".php</i>", 
                         'View does not exist');
        }
        
        return(true);
    }
       
    //----------------------------------------------------------
    
    /**
    * Obtains the cache parsed view to be included when generating
    * @return string
    */
    
    public function getCacheView()
    {
        $parsed_view = preg_replace("/.*\//", TMP_VIEW, $this->viewPath) . $this->signature;
         
        if(file_exists($parsed_view))
        {
            return $parsed_view;
         
        } else {
     
            return $this->viewPath;                
        }
    }
       
    //----------------------------------------------------------
       
    /**
    * Obtains the cache layout to be included when generating
    * @return string
    */
    
    public function getCacheLayout()
    {
        $parsed_layout = preg_replace("/.*\//", TMP_LAYOUT, $this->myController->load_layout) . $this->signature;
           
        if(file_exists($parsed_layout))
        {
            return $parsed_layout;
         
        } else {
     
            return $this->myController->load_layout;                
        }
    }
       
    //----------------------------------------------------------
      
    /**
    * Retrieves the layout that the controller will be using, is
    * used by cache lib to parse the layout php code
    *
    * @return string
    */
    
    public function getLayoutPath()
    {
        if(isset($this->myController->layout))
            $layout = $this->myController->layout;
        else
            $layout = DEFAULT_LAYOUT;
               
        return LAYOUT . $layout . '.php';
    }
       
    //----------------------------------------------------------
       
    /**
    * Checks lenght of arguments against method definition.
    */
    
    private function validArgs()
    {
        // Change - to _
        $l_action = $this->uri->action();
        
        //--
        //-- Check parameter count
        //--
        
        // Total parameters on this request
        $uri_parameters = count($this->uri->parameters());

        // We already checked it existed
        $rm = new ReflectionMethod(get_class($this->myController), $l_action);
        $required_params = $rm->getNumberOfRequiredParameters();
        $total_params    = $rm->getNumberOfParameters();

        // Params muast match requirement
        if($uri_parameters < $required_params)
            $this->abort('Too few parameters were provided for this request.','Missing arguments');
        
        // Params muast match definition
        if($uri_parameters > $total_params)
            $this->abort('Too many parameters were provided for this request.','Too many arguments');
        
        // If does not match call
        if(count($this->uri->parameters()) > 10)
            $this->abort('More than 10 parameters have been provided.','Too many arguments');
    }
    
    //----------------------------------------------------------
               
    /**
    * Executes and returns the results of the controller
    * @return array
    */
    
    public function process()
    {  
        return $this->myController->_execute($this->uri);
    }
       
    //----------------------------------------------------------
        
    /**
    * Aborts the dispatcher showing a message, including the 404 page or storing the error message
    * @param string $title
    * @param string $explanation
    */
    
    private function abort($explanation = 'A critical error has happened', $title = 'Execution aborted')
    {
        /*
        * No errors will be thrown when generating, it would stop the generation process
        */
        if($this->generating == false)
        {
            Pi_core::abort($explanation, $title);
               
        } else {
            
            // Store the error instead so cache lib detects the error and avoids generating
            $this->storeError($title);
        }
    }
}
?>
