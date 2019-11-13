<?php

/**
 * Controller: 	Index
 * (!)          Controller generated by the create script
 *
 * Uncomment attributes as you need
 */

class IndexWebController extends MyWebController
{
    //var $ignore_errors = true;			// Ignore critical errors and display the view
    //var $ignore_messages = true;			// Ignore dataError and Flash messages 
    //var $layout = 'another_layout_name';	// Set another layout for this controller (No extension)
    //var $sessionAllowed = 'Session_name';	// Redirect to 403 Forbidden access page unless this session is created
    //var $load = array();                  // Array of libraries to be autoloaded for this controller
    
    // Required apache modules
    var $needed_apache_modules = array('mod_rewrite');
    var $needed_php_modules    = array('pcre','mbstring','curl','gd','Reflection','json','yaml','libxml','sqlite3','pdo_sqlite','session','PDO');
    
    // Writable folders 
    var $needed_writable = array(
        APP_CACHE,
        CACHE,
        LOG,
        DB,
        TMP,
        MODEL_FILES,
    );

    //--------------------------------------------------------
    
    /**
    * Default controller function. Start here!
    */
   
    public function index()
    {
        // Title
        $this->setTitle("It works!");

        // Things to fix
        $fix = array();
        
        // Get all apache and php modules
        $apache_modules = apache_get_modules();
        $php_modules    = get_loaded_extensions();

        // Check apache
        foreach($this->needed_apache_modules as $module)
        {
            if(!in_array(strtolower($module), $apache_modules))
            {
                $fix[] = "The <strong>". $module ."</strong> module is not enabled in your web server.";
            } 
        }
        
        // Check PHP
        foreach($this->needed_php_modules as $module)
        {
            if(!in_array($module, $php_modules))
            {
                $fix[] = "The <strong>". $module ."</strong> extension is not enabled in your PHP installation.";
            } 
        }
        
        // Check dir is writable
        foreach($this->needed_writable as $dir)
        {
            if(!is_writable($dir))
            {
                $fix[] = "The directory <strong>". $dir ."</strong> is not writable.";
            } 
        }

        // Set fix
        $this->set('fix', $fix);
    }
}

?>
