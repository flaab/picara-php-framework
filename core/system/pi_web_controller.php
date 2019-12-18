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
* Implements functionality shared among all controllers
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
* @note       You can implement customized shared functionality among your controllers by modifying /core/MyWebController.php
*/
 
abstract class Pi_web_controller extends Pi_paginator
{
    /**
    * Full path to layout to be loaded
    */
    var $load_layout = NULL;
    
    /**
    * Full path to view
    */
    var $load_view = NULL;
    
    /**
    * Variables for the view
    */
    private $legacy = array();

    /**
    * Window title
    */
    private $page_title;

    /**
    * View paths
    */
    private static $view_path = array(VIEW, BUILTIN_WEB_VIEW);
    
    //----------------------------------------------------------
     
    /**
    * Passes a variable to the view
    *
    * @param    string    $index 
    * @param    mixed     $whatever 
    * @example  controller/set.php
    */
     
    protected final function set($index, $whatever)
    {
        $this->legacy[$index] = $whatever;
    }
    
    //----------------------------------------------------------

    /**
    * Replies as JSON if the controller is an api
    *
    * @param    mixed     $whatever 
    */
     
    protected final function response_as_json($whatever)
    {
        // It is json
        header('Content-Type: application/json');

        // Encode if it is not string
        if(!is_string($whatever))
            $whatever = json_encode($whatever);
        
        // Print and die
        print($whatever);
        die;
    }
    
    //----------------------------------------------------------

    /**
    * Replies as CSV if the controller is an api
    *
    * @param    mixed     $whatever 
    */
     
    protected final function response_as_csv($whatever)
    {
        header('Content-Type: text/csv');
        print($whatever);
        die;
    }
    
    //----------------------------------------------------------

    /**
    * Replies as YAML if the controller is an api
    *
    * @param    mixed     $whatever 
    */
     
    protected final function response_as_yaml($whatever)
    {
        header('Content-Type: application/x-yaml');
        print($whatever); die;
    }
    
    //----------------------------------------------------------
    
    /**
    * Replies as YAML if the controller is an api
    *
    * @param    mixed     $whatever 
    */
     
    protected final function response_as_xml($whatever)
    {
        header('Content-Type: text/xml');
        print($whatever); die;
    }
    
    //----------------------------------------------------------
       
    /**
    * Sets the navigator window title
    *
    * @param     string    $title
    */
       
    protected final function setTitle($title)
    {
        $this->meta_title = $title;
    }

    //--------------------------------------------------------

    /**
    * Sets desired lang into the session
    *
    * @param    string    $lang
    */

    protected final function setLang($lang)
    {
        if(!$this->metadata->is_enabled_lang($lang))
            trigger_error("Lang '$lang' is not supported in the application", E_USER_ERROR);

        $_SESSION['picara']['lang'] = $lang;
    }
       
       //----------------------------------------------------------
       
       /**
       * Creates the window title by joining default project title and default connector
       * 
       * @param     string    $text
       * @example   controller/appendTitle.php
       */
       
       protected final function appendTitle($text)
       {
           $this->meta_title = TITLE . CONNECTOR . $text;
       }
       
        //----------------------------------------------------------
       
       /**
       * Creates the window title by joining received string before the last title
       * 
       * @param     string    $text
       */
       
       protected final function preAppendTitle($text)
       {
           $this->meta_title = $text . CONNECTOR . TITLE;
       }
       
       //----------------------------------------------------------
       
       /**
       * Forces a specific view to be rendered. 
       * Be careful, there is no error checking.
       *
       * @param     string       $name 
       * @access    protected
       * @example   controller/setView.php
       */
       
       protected final function setView($name)
       {
           $view = VIEW . $name . '.php';
           
           if(!file_exists($view))
               $this->core->quit('Create the view '. $view,'Requested view does not exist');
           
           $this->load_view = $view;
       }
       
     //----------------------------------------------------------
       
     /**
     * Forces a specific layout to be rendered. 
     * Be careful, there is no error checking.
     *
     * @param     string       $name
     * @access    protected
     * @example   controller/setLayout.php
     */
          
     protected final function setLayout($name)
     {          
           $layout = LAYOUT . $name . '.php';
           
           if(!file_exists($layout))
               $this->core->quit("Create it in $layout", 'Requested layout does not exist');
               
         $this->load_layout = $layout;
     }
     
     //----------------------------------------------------------
     
     /**
      * Executes given controller and action from an uri object
      *
      * @param    uri    $uri
      */
      
      public final function _execute(Pi_uri $uri)
      {            
          // Save uri object into controller
          $this->uri = $uri;
          
          // Arguments 
          $args = $this->uri->parameters();

          // Action
          $action = str_replace('-','_', $this->uri->action());
          
          // Arguments count
          $amount = count($args);
          
          // Assuming the action should be executed
          $execute = true;
          
          // Abort if session is not allowed (303 error)
          if(isset($this->session_allowed) && !empty($this->session_allowed) && !$this->session->check($this->session_allowed))
          {
              $this->core->http_error(403);         
              exit();
          }

          // About if ip is not allowed (405 error)
          if(isset($this->ips_allowed) && !empty($this->ips_allowed) && !in_array($_SERVER['REMOTE_ADDR'], $this->ips_allowed))
          {
              $this->core->http_error(405);         
              exit();
          }

          //--
          //-- Execution
          //-- Load langfile if needed, run callbacks and launch controller action
          //-- 
          if($execute == true)
          {          
              // Lang file is appended to object if needed
              $this->_load_lang($uri->controller(), $action);
            
              // Controller and action stored for later use
              // The url parameters are stored, not actual class and function called
              // Underscores are converted to hyphens to keep links clean
              // The controller has both values at its disposal

              // Controller and action (for urls) delivered
              $this->link['controller']        = strtolower(str_replace("_",'-',$this->uri->controller_link));
              $this->link['action']            = strtolower(str_replace("_",'-',$this->uri->action_link));
              
              // Controller and action being called
              $this->request['controller']     = strtolower($this->uri->controller());
              $this->request['action']         = strtolower($this->uri->action());
                
              // Load config file if needed before action
              $this->load_config();

              // Before action callbacks
              $this->controller_callbacks($this->before_action);

              // Execute or exceptions => 404
              call_user_func_array(array($this, $action), $args);

              // Deliver context information if this is not an api call
              if(!isset($this->api_settings[$this->uri->action()]))
              {
                  $this->set('request', $this->request);
                  $this->set('link',    $this->link);
              }

              // After action callbacks
              $this->controller_callbacks($this->after_action);
          
          }          
          return $this->_giveBack();
      }
      
    //--------------------------------------------------------

    /**
    * Creates the lang object for this controller if lang support is enabled.
    * Loads default languaje unless lang session variable is set.
    *
    * @param    string    $controller
    * @param    string    $action
    */

    private final function _load_lang($controller, $action)
    {
        if(LANG_SUPPORT == true)
        {
            if(isset($_SESSION['picara']['lang']))
                $lang = $_SESSION['picara']['lang'];
            else
                $lang = DEFAULT_LANG;

            $this->lang = new Lang($lang, $controller, $action);
        }
    }

    //----------------------------------------------------------    
       
    /**
     * Delivers the process result to the main application
     *
     * @return    array
     */
        
     private final function _giveBack()
     {   
         // If layout has not been manually set
         if($this->load_layout == NULL)
         {
            if(isset($this->layout))
                   $this->setLayout($this->layout);
            else
                $this->setLayout(DEFAULT_LAYOUT);
        }
        
        // Title
        if(strlen($this->meta_title) > 0) 
            $this->set('meta_title', $this->meta_title);

        // Check for lang
        if(LANG_SUPPORT && isset($this->lang))
            $this->set('lang', $this->lang->messages);
        
        // Return base href and canonical
        $this->set('base_href', $this->uri->base_href());
        $this->set('canonical', $this->uri->canonical());

        // Return my full link
        $this->set('full_link', $this->uri->full_link());

        // There u go
        return $this->legacy;
    }

    //----------------------------------------------------------    
    
    /**
    * Retrieves full path to desired action view
    *
    * @param    string    $action
    */

    public final function get_view_path($action)
    {
        $tot = count(self::$view_path);
        $controller = preg_replace('/WebController$/', '', get_class($this));
        for($i=0; $i < $tot; $i++)
        {
            $path = self::$view_path[$i] . $controller . '/' . $action . '.php';
            
            if(file_exists($path))
                return $path;
        }
        return false;
    }
}
?>
