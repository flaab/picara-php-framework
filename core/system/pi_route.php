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
* Implements the Framework routing, responsable of creating 
* dispatcher uri objects and performs routing if neccesary.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class Pi_route extends Pi_overloadable
{
    /**
    * Original request
    */
    private $request;

    /**
    * Routed request
    */
    private $redirection;

    /**
    * Original uri object
    */
    private $uri;

    /**
    * Routed uri
    */
    private $redirection_uri = false;
    
    //--------------------------------------------------------

    /**
    * Receives a framework request
    *
    * @param    Pi_uri    $uri
    */

    public function __construct($uri)
    {
        // Original uri
        $this->uri = $uri;

        // Get redirection
        $redirection = $this->get_redirection($this->uri->uri());
        
        // If any redirection has to be made
        if($redirection != false)
        {
            $this->redirection = $this->get_routed_request($redirection);
            $this->redirection_uri = new Pi_uri($this->redirection, $this->uri->uri());
        }
    }

    //--------------------------------------------------------

    /**
    * Returns the redirected uri instance to be loaded by the framework
    *
    * @return   Pi_uri
    */

    public function get_redirection_uri()
    {
        return $this->redirection_uri;
    }

    //--------------------------------------------------------

    /**
    * Checks if any redirection has been performed
    */ 

    public function redirection()
    {
        if($this->redirection_uri != false)
            return true;
        
        return false;
    }


    //--------------------------------------------------------

    /**
    * Performs the routing check and stores the new request if neccessary
    *
    * @param    string         $request
    * @return   string|bool
    */

    private function get_redirection($request)
    {  
        // Routes config file
        $routes_file = USERCONFIG . 'routes.yml'; 

        // Routes array
        $routes = array();

        // If file doest no exist, false is returned
        if(!file_exists($routes_file))
            return false;

        // Parse yml natively
        $routes = yaml_parse(file_get_contents($routes_file)); 
        
        // Iteration over routes array
        if(!is_null($routes))
        {
            foreach($routes as $regex => $redirection)                   
            {
                if(preg_match($regex, $request))                              
                    return $redirection;      
            }
        }

        // False if no redirection must be applied
        return false;
    }

    //----------------------------------------------------------
       
    /**
    * Constructs the real routed request
    *
    * @param    string    $redirection
    * @return   string
    */
    
    private function get_routed_request($redirection)
    {
        $redirection = str_replace(':controller', $this->uri->controller(true), $redirection);
        $redirection = str_replace(':action', $this->uri->action(true), $redirection);
        $arguments = $this->uri->parameters();
        $total_arguments = count($arguments);           
           
        for($it = 0; $it < $total_arguments; $it++)
        {
            $number = $it + 1;
            $redirection = str_replace(":p$number", $arguments[$it], $redirection);
        }   

        return $redirection;
    }
}

?>
