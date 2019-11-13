<?php

/**
 * Controller: 	<controllername>
 * (!)          Controller generated by the create script
 *
 * Uncomment attributes as you need
 */

class <controllername>WebController extends MyAdminController
{
    //--
    //-- Options and Callbacks 
    //-- 
    
    var $layout           = 'admin';                                      // Set another layout for this controller
    var $session_allowed  = array(ADMIN_SESSION);	                      // Allowed sessions to run this controller (else 403 Error)
    var $after_action     = array('_other_scaffolds','_set_custom_menu'); // Callbacks after action
    //var $load           = array();                                      // Libs or utils to load for this controller
    //var $ips_allowed    = array();	                                  // Allowed ips to run this controller (else 403 Error)
    //var $before_action  = array();                                      // Callbacks executed before the action is called
    
    //--------------------------------------------------------
    
    /**
    * Default controller function. Start here!
    */
   
    public function index()
    {

    }
}

?>
