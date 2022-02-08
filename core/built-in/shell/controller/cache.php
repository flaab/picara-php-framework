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
* Manages cache files for the application 
*
* @package    Scripts 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.2
*/
 
class cacheShellController extends Pi_shell_controller
{
    var $valid_options = array('lang');
    var $valid_assertions = array();

    //--------------------------------------------------------
    
    /**
    * Index redirects to help
    */

    public function index()
    {
        $this->help();
    }

    //--------------------------------------------------------

    /**
    * Creates cache files for given requests
    *
    * @param    string    $requests
    */

    public function create($requests)
    {
        $this->_perform_for_all_langs($requests, 'create');
    }

    //--------------------------------------------------------

    /**
    * Receives the request parameter and performs desired function for all enabled langs
    *
    * @param    string    $requests
    * @param    string    $action
    */

    private function _perform_for_all_langs($requests, $action)
    {
        // If lang support is enabled
        if(LANG_SUPPORT == true)
        {
            // If lang option has been received
            if($this->options->lang)
            {   
                // Cache generation without for this lang
                $this->_perform($requests, $action, $this->options->lang);   
            
            } else {
            
                // Cache process must be done verbosely for all enabled langs
                $enabled_langs = $this->metadata->get_enabled_langs();
                
                // Abort if no langs
                if(count($enabled_langs) == 0)
                    $this->abort("No lang is enabled yet, please edit '". USERCONFIG . "langs_enabled.php'");

                // Execute operation
                foreach($enabled_langs as $lang => $name)
                {
                    // Title
                    $this->putunderlined(ucfirst($name) .' ('. $lang .')');

                    // Generation
                    $this->_perform($requests, $action, $lang);
                    $this->putline();
                }
            }

        } else {
            
            // If lang has been received and lang support is not enabled, let user know
            if($this->options->lang)
                $this->abort("Lang support is not enabled, please edit '". USERCONFIG ."'application.php");
    
            // Cache operation without lang support
            $this->_perform($requests, $action);   
        }
    }

    //--------------------------------------------------------

    /**
    * Deletes cache files for given requests
    *
    * @param    string    $requests
    */

    public function delete($requests)
    {
        $this->_perform_for_all_langs($requests, 'delete');
    }

    //--------------------------------------------------------

    /**
    * Restores cache files for given requests
    *
    * @param    string    $requests
    */

    public function restore($requests)
    {
        $this->_perform_for_all_langs($requests, 'restore');
    }

    //--------------------------------------------------------

    /**
    * Purges cache files for given requests
    *
    * @param    string    $requests
    */

    public function purge($requests)
    {
        $this->_perform_for_all_langs($requests, 'purge');
    }

    //--------------------------------------------------------

    /**
    * Performs any operation received by any set of requests
    *
    * @param    string    $requests
    */

    private function _perform($requests, $action, $lang)
    {
        // Requests collection
        $collection = $this->_get_requests($requests);
        
        // Elements ok
        $ok = 0;

        // Elements failed
        $failed = 0;
        
        // Iteration over requests
        foreach($collection as $request)
        {
            // New cache element for desired lang (NULL if not received)
            $cache = new Cache($request, $lang);
            
            // Execution
            $result = $cache->{$action}();
            
            // Result
            if($result)
            { 
                $this->putline(" > ". ucfirst($action) ." ". $cache->cache_file);
                $ok++;
            } else {

                $failed++;

            }
        }
        
        $this->putline();
        $this->put(' Result: ');
        
        // If successfull
        if($ok > 0)
            $this->put(" $ok requests OK. ");

        // If failed
        if($failed > 0)
            $this->put(" $failed requests ignored.");

        $this->putline();
    }

    //--------------------------------------------------------

    /**
    * Parses request pattern and converts it into a set of real requests
    *
    * @param    string    $request
    * @return   array
    */

    private function _get_requests($request)
    {
        $pieces = explode('/', $request);
    
        for($i=0; $i<count($pieces); $i++)
        {
             /*
             * Check for numeric parameters 
             */
             if(preg_match("/\[[0-9]+-[0-9]+\]/", $pieces[$i]))
             {
                /*
                 * Numeric value range
                 */
                 $values = preg_replace("/(\[|\])/",'',$pieces[$i]);
                 $values = explode('-', $values);
    
                 // If range not correct
                 if($values[0] >= $values[1])
                    $this->abort('The numeric value range is not correct. Try again.');
    
                 // Create the range
                 for($n = $values[0]; $n <= $values[1]; $n++)
                    $numbers[] = $n;
                 
                 // Append it to pieces
                 $pieces[$i] = $numbers;
    
             } else   
             
             /*
             * Check for enumerated strings
             */
             if(preg_match("/\[([a-zA-Z0-9_]+,)+[a-zA-Z0-9_]+\]/", $pieces[$i]))
            {
                /*
                 * Enumerated strings
                 */
                 $values = preg_replace("/(\[|\])/", '', $pieces[$i]);
                 $values = explode(',',$values);
                 $pieces[$i] = $values;
            }
        }

        return $this->_get_query_strings($pieces);
    }

    //--------------------------------------------------------

    /**
    * Receives a bidimensional array with pattern pieces and returns the real set of requests
    *
    * @param    array    $pieces
    * @return   array  
    */

    private function _get_query_strings($pieces)
    {
        $combinations = array('');
    
        for($it=0; $it < count($pieces); $it++)
        {
            $combinations = $this->_add_combination($pieces[$it], $combinations);
        }

        return $combinations;
    }

    //--------------------------------------------------------

    /**
    * Creates the set of real requests to be executed from received combinations
    * 
    * @param    array|string    $param
    * @param    array           $combinations
    * @return   array
    */

    private function _add_combination($param, $combinations)
    {
        $tot = count($combinations);
    
        if(is_array($param))
        {
            for($it=0; $it < $tot; $it++)
            {
                foreach($param as $value)
                {
    	            if(empty($combinations[$it])) { $slash = ''; } else { $slash = '/';}
                    $combinations2[] = $combinations[$it] . $slash . $value;  
                }  
            }

            $combinations = $combinations2;
    
        } else {
    
            for($it=0; $it < count($tot); $it++)
            {
            	if(empty($combinations[$it])) { $slash = ''; } else { $slash = '/';}
                $combinations[$it] .= $slash . $param;
            }
    
        }

        return $combinations;
    }
    
    //--------------------------------------------------------
    
    /**
    * Displays help and dies
    */

    public function help()
    {
        $help = file_get_contents(BUILTIN_SHELL_HELP . 'cache.txt');
        $this->put($help);
    }
}

?>
