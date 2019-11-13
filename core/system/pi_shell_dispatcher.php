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
* Receives and processes all shell framework requests, creating and executing
* requested controller and serving information to the underneath application.
* This allows developers to create shell scripts using all framework tools.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class Pi_shell_dispatcher extends Pi_error_store
{
    /**
    * Stores execution parameters
    */
    private $arguments;    
    
    /**
    * Stores execution options
    */
    private $options = array();

    /**
    * Stores assertions
    */
    private $assertions = array();
    
    /**
    * Controller
    */
    private $controller;

    /**
    * Controller instance
    */
    private $controller_instance;
    
    /**
    * Action
    */
    private $action = DEFAULT_ACTION;

    /**
    * Forbidden actions to be called
    */
    private static $forbidden_functions = array('storeError','getErrorStore','displayErrorStore','failed','isOk','cleanErrorStore');
    
    //----------------------------------------------------------   
    
    /**
    * Receives the array containing all script parameters
    *
    * @param    string    $argv
    */
    
    public function __construct($argv)
    {
        // Stores parameters and options
        $this->store_parameters_and_options($argv);
        
        // Creates controller instance
        $this->load_controller();

        // Checks if action is valid
        $this->validAction();

        // Everything fine
        $this->controller_instance->_execute($this->action, $this->arguments, $this->options, $this->assertions);

    }
    
    //----------------------------------------------------------     
    
    /**
    * Stores controller, action, parameters and options
    *
    * @param    array    $argv
    */
    
    private function store_parameters_and_options($argv)
    {
        // If only one parameter is received an error is thrown
        if(count($argv) < 2)
        {
            trigger_error("Parameters are missing, at least controller name is expected", E_USER_ERROR);
        }        
        
        // Normal parameters count
        $pcount = 0;        
        
        /*
        * Parameter iteration to store controller, action, parameters and options
        */
        for($it = 1; $it < count($argv); $it++)
        {
            /*
            * Checks if given parameter is an option (example: --option=value)
            */
            if(preg_match("/^\-+([a-zA-Z_]+)=(.+)$/", $argv[$it], $res))
            {
                $this->options[$res[1]] = $res[2];
            
            /*
            * Checks if given parameter is an assertion (example: --help)
            */
            } else if(preg_match("/^\-+([a-zA-Z0-9_]+)$/", $argv[$it], $res)) {

                $this->assertions[] = $res[1]; 

            /*
            * Normal parameter has been received
            */
            } else {
            
                // Parameter count is increased
                $pcount++;
                
                // Controller, action or parameter assignment
                switch($pcount)
                {
                    case 1: $this->controller = strtolower($argv[$it]); break;
                    case 2: $this->action = strtolower($argv[$it]); break;
                    default: $this->arguments[] = $argv[$it];
                }   
            }
        }
    }

    //--------------------------------------------------------

    /**
    * Loads the controller or aborts the application
    */
    
    private function load_controller()
    {
        if(!Pi_loader::load_shell($this->controller))
        {
            trigger_error("Requested shell controller does not exist",
                                E_USER_ERROR);
        }
        
        // Declaration of the controller class name
        $classname = $this->controller . 'ShellController';
         
        // Instance creation
        $this->controller_instance = new $classname();
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
        //--
        //-- First things first: check if method is underscored.
        //--  
        
        // Underscored methods are not allowed.
        if(preg_match("/^\_.*$/", $this->action))
            $this->abort("Underscored methods can't be used as public actions.", 'Invalid action');
        
        //--
        //-- Get controller public methods and public inherited methods.  
        //-- Only accept calls to controller public methods. This avoids
        //-- accepting requests for admin tasks and other methods declared
        //-- in the users' MyController and MyShellController classes.
        //-- All other inherited methods in the framework are final.
        //-- 
        
        // Get public controller methods, native and inherited.
        $controller_methods = $this->getControllerMethods(get_class($this->controller_instance));
        
        // Ignore inherited public functions
        if(in_array($this->action, $controller_methods['inherited'])) 
                trigger_error("Method ". $this->action ." cannot be called or re-declared, it belongs to a parent class.", E_USER_ERROR);

        // If belongs explicitly to the user controller -without inheritance!-
        if(!in_array($this->action, $controller_methods['native']))
        { 
            trigger_error("Requested action does not exist in controller ". $this->controller, E_USER_ERROR );
        }
        
        // Method exists, but it can be an API not accepting this request method
        if($this->is_api_call && !in_array(strtoupper($_SERVER['REQUEST_METHOD']), $this->myController->api_settings[$this->action]))
            trigger_error("Method ". $this->action ." is not configured to accept ". $_SERVER['REQUEST_METHOD'] ." requests.", E_USER_ERROR);
        

        // Function exists
        return(true);
    }
}
?>
