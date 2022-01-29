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
* Remove indentation when generating static html documents to save traffic
* @default true;
*/
define('REMOVE_IDENTATION', true);

/**
* Remove html comments when generating static html documents to save traffic
* @default true;
*/
define('REMOVE_COMMENTS', true);

/**
* Assists the generation and manipulation of HTML static or semi-static documents from code.
* This means that if the the generated HTML document for an specific request exists, it
* will be served and the application execution skipped.
*
* Suposse you run a newspaper website that receives a really huge traffic; a million
* visits per day. Generating an static document twice a day will mean two queries to the
* database instead of a million, and will save you a lot of cpu resources and time.
*
* Serving an static document can be up to a 90-100 times faster that processing the request.
*
* You can also preserve blocks of Php code from being interpreted when generating.
*
* Backups are created and files are trated atomically so no client requests are lost during the
* generation process.
*
*
* @package    Libs
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2009, Arturo Lopez
* @version    0.1
* @example    cache/cache.php
* @note       Collections of static HTML documents can be generated using the administration scripts
*/
  
class Cache extends Pi_cache
{   
    /**
    * Signature string to validate the generation request
    */
    var $signature;                    
    
    /**
    * New cache file to be created
    */        
    var $cache_file_new;    
    
    /**
    * Backup file for this request
    */        
    var $cache_file_old;        
    
    /**
    * File containing the signature
    */
    private $signature_file;    
    
    /**
    * Dispatcher instance
    */        
    private $dispatcher;    

    /**
    * Unparsed view for the request
    */
    private $unparsed_view;                
    
    /**
    * Temporal parsed view created
    */
    private $parsed_view;    
    
    /**
    * Unparsed layout
    */        
    private $unparsed_layout;
    
    /**
    * Parsed layout
    */
    private $parsed_layout;
    
    /**
    * Dispatcher generated errors, if any
    */    
    private $dispatcher_errors = NULL;    
    
    /**
    * Full Url to perform the query
    */ 
    private $url;                        
    
    /**
    * Post param sent
    */
    private $post;       

    /**
    * Forced lang to generate
    */
    protected $lang = false;

    /**
    * Metadata object
    */
    private $metadata;
    
    //----------------------------------------------------------                

    /**
    * Validates request and sets up the generation process for an optional
    * specific lang.
    * 
    * @param      string           $request
    * @param      string           $lang
    * @example    cache/new.php
    */
     
    public function __construct($request, $lang = NULL)
    {
         // Force request to be lowercase
         $request = strtolower($request);

         // Dispatcher creation to validate request
         $this->dispatcher = new Pi_web_dispatcher($request, true);
         
         // Validation
         $this->dispatcher_errors = $this->dispatcher->getErrorStore();

         // Uri object must be stored
         $this->uri = $this->dispatcher->uri;

         // Metadata object 
         $this->metadata = Pi_metadata::singleton();

         // Go on if no errors
         if($this->dispatcher_errors == NULL)
         {
             // If a lang has been forced, we must check it is enabled
             if($lang != NULL && LANG_SUPPORT == true)
             {
                 if(!$this->metadata->is_enabled_lang($lang))
                     trigger_error("Lang '$lang' is not enabled in the application and I cannot generate cache files for it.", E_USER_ERROR);

                 // Save it
                 $this->lang = $lang;
             }

             // Parent sets up all paths
             parent::__construct($this->dispatcher->uri);

             // Signature and signature file
             $this->signature = preg_replace("/(\.|\s)/",'',microtime());
             $this->signature_file = TMP . "signature" . $this->signature;

             // New cache file
             $this->cache_file_new = $this->cache_file . ".new";
         
             // Old cache file
             $this->cache_file_old = $this->cache_file . ".old";
             
             // Unparsed view
             $this->unparsed_view = $this->dispatcher->viewPath;
                          
             // Parsed view
             $this->parsed_view = preg_replace("/.*\//", TMP_VIEW, $this->unparsed_view) . $this->signature;
             
             // Unparsed layout
             $this->unparsed_layout = $this->dispatcher->getLayoutPath();

             // Parsed layout
             $this->parsed_layout = preg_replace("/.*\//", TMP_LAYOUT, $this->unparsed_layout) . $this->signature;
             
             // Curl stuff
             $this->url = CURL_BASE_URL  . $request;
             
         } else {
         
             $this->storeError($this->dispatcher_errors);
         }
    }
    
    //--------------------------------------------------------
    
    /**
    * Creates a new cache document and a backup of the existing one
    *
    * @example    cache/create.php
    * @return     int 
    */
    
    public function create()
    {
        // Curl base url must have been defined
        if(strlen(CURL_BASE_URL) <= 7)
            trigger_error("Please define the CURL_BASE_URL constant to your public address at ". USERCONFIG ."application.php", E_USER_ERROR);
        
        // Only if no dispatcher errors have been generated
        if($this->dispatcher_errors == NULL)
        {
            // Try to create signature file
            $this->createSignatureFile();
            
            // Try to create parsed view file
            $this->createParsedView();
            
            // Try to create parsed layout
            $this->createParsedLayout();
            
            // If everything ok
            if(file_exists($this->signature_file) && file_exists($this->parsed_view)  && file_exists($this->parsed_layout))
            {
                // If lang support is enabled
                if(LANG_SUPPORT == true)
                {
                    // If a lang has been manually forced
                    if($this->lang != false)
                    {
                        if($this->_real_create($this->lang))
                            return 1;
                        
                        return 0;

                    } else {

                        // All langs must be generated
                        $langs = array_keys($this->metadata->get_enabled_langs());

                        // Sum of created files
                        $files_created = 0;

                        // Cache file is created for all langs
                        foreach($langs as $lang)
                        {
                            if($this->_real_create($lang))
                                $files_created++;
                        }

                        return $files_created;
                    }

                } else {

                    // No lang support, generation on the fly
                    if($this->_real_create())
                        return 1;

                    return 0;
                }

            } else {
                
                $this->storeError('I cannot create signature and parsed view file. Check permissions.');
                $this->log->error('Failed to create '. $this->cache_file .'. Check permissions.');
            }
            
            // Cleanup if generation is never executed
            $this->cleanUp();
        }

        return false;
    }

   
    //----------------------------------------------------------    
    
    /**
    * Creates the signature file for the generation
    *
    * @return    bool
    */
    
    private function createSignatureFile()
    {
        $creation = @file_put_contents($this->signature_file, '');
        chmod($this->signature_file, 0777);
        return $creation;
    }
    
    //----------------------------------------------------------
    
    /**
    * Destroys signature file if dispatcher hasn't done it before
    *
    * @return    bool
    */
    
    private function destroySignatureFile()
    {
        if(file_exists($this->signature_file))
            return @unlink($this->signature_file);
    }
    
    //----------------------------------------------------------
    
    /**
    * Creates and stores parsed view
    *
    * @return    bool
    */
    
    private function createParsedView()
    {
        $unparsed_contents = @file_get_contents($this->unparsed_view);
        $parsed_contents = $this->parseCode($unparsed_contents);
        return FileSystem::put_file($this->parsed_view, $parsed_contents);
    }
    
    //----------------------------------------------------------
    
    /**
    * Creates and stores de parsed layout
    *
    * @return    bool
    */
    
    private function createParsedLayout()
    {
        $unparsed_contents = @file_get_contents($this->unparsed_layout);
        $parsed_contents = $this->parseCode($unparsed_contents);
        return FileSystem::put_file($this->parsed_layout, $parsed_contents);
    }
    
    //----------------------------------------------------------
    
    /**
    * Destroys parsed view file
    *
    * @return    int
    */
    
    private function destroyParsedView()
    {
        return @unlink($this->parsed_view);
    }
    
    //----------------------------------------------------------
    
    /**
    * Destroys the parsed layout file
    *
    * @return    int    result
    */
    
    private function destroyParsedLayout()
    {
        return @unlink($this->parsed_layout);
    }
    
    //----------------------------------------------------------
    
    /**
    * Parses any php code given
    *
    * @param    string    $code
    * @return   string
    */
    
    private function parseCode($code)
    {
        $regex = "/\<\?(php|=)?(\s)*\/\*(\s)*#+(\s)*NOPARSE(\s)*#+(\s)*\*\//";
        $result = preg_replace($regex,"{php_start}?php\n/*\n 
                                                * PICARA WEB DEVELOPMENT FRAMEWORK\n 
                                                * This Php code has not been parsed\n 
                                                */\n",$code);
        return $result;
    }
    
    //----------------------------------------------------------
    
    /**
    * Unparses any php code given
    *
    * @param     string    $code
    * @return    string
    */
    
    private function unparseCode($code)
    {
        return preg_replace("/\{php_start\}\?php/",'<?php', $code);
    }
    
    //----------------------------------------------------------
    
    /**
    * Removes Html comments and identation if needed
    *
    * @param    string    $code
    * @return   string
    */
    
    private function cleanUpCode($code) 
    {
        // Comments
        if(REMOVE_COMMENTS == true)
            $code = preg_replace("/\<!--.*--\>/", '', $code);
        
        // Identation
        if(REMOVE_IDENTATION == true)
            $code = preg_replace("/\n\s*/","\n", $code);
        
        return $code;
    }
    
    //----------------------------------------------------------
    
    /**
    * Cleans up temporal files: signature and parsed view
    */
    
    private function cleanUp()
    {
        $this->destroySignatureFile();
        $this->destroyParsedView();
        $this->destroyParsedLayout();
    }
    
    //----------------------------------------------------------

    /**
    * Creates the POST string to send via Curl
    *
    * @param    string    $lang
    * @return   string
    */

    private function _get_post_string($lang = NULL)
    {
         $post = 'picara_signature='. urlencode($this->signature);

         // If a lang has been forced, should be also sent as a post var
         if($lang != NULL)
            $post .= '&picara_lang='. $lang;

         return $post;
    }

    //----------------------------------------------------------

    /**
    * Performs the real generation of cache files for any optional lang
    *
    * @param    string    $lang
    * @return   bool
    */

    private function _real_create($lang = NULL)
    {
        // Base generation path for this file
        $path = $this->cache_dir;

        // If lang has been forced, code must be appended to the path
        if($lang != NULL)   $path .= $lang .'/';

        // Create cache directory tree if it does not exist
        $dir = FileSystem::create_tree($path);

        // Abort if failed and clean up stuff
        if($dir == false)
        {
            $this->storeError('Cache directory does not exist and I cannot create it. Check permissions.');
            $this->log->error('Failed to create '. $dir .'. Check permissions.');
            $this->cleanUp();
            return;
        }

        // File name is appended
        $path .= $this->calculate_file_name();
                
        // Obtains the post string
        $post = $this->_get_post_string($lang);

        // Curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                
        // Execution and save result
        $data = curl_exec($ch);             
        $data = $this->unparseCode($data); 
        $data = $this->cleanUpCode($data);

        // Write to disk
        if(!FileSystem::put_file($path, $data))
        {
            $this->storeError('I cannot create cache file. Check permissions');
            $this->log->error('Failed to create '. $path .'. Check permissions.');
            $this->cleanUp();
            return false;
                    
         } else {    
               
            $this->cleanUp();
            $this->log->message('Cache '. $path .' created.');
            return true;
         }
    }
    
    //----------------------------------------------------------
    
    /**
    * Bridge to delete, purge or restore files for all langs
    *
    * @param    string    $action
    * @return   int
    */

    private function _real_operation($action)
    {
        // Cache path
        $path = $this->cache_dir;
        
        // File name
        $file = $this->calculate_file_name();

        // Real function
        $function = '_real_' . $action;

        // Affected files
        $affected = 0;

        // If lang support is enabled
        if(LANG_SUPPORT == true)
        {
            // If a lang has been manually forced
            if($this->lang != false)
            {
                // Action just for this lang
                $path .= $this->lang . '/' . $file;

                // Execution
                return $this->$function($path);

            } else {

                // All langs must be generated
                $langs = array_keys($this->metadata->get_enabled_langs());

                // Sum of created files
                $affected = 0;

                // Action just all langs
                foreach($langs as $lang)
                {
                    // Action for this lang
                    $path_file = $path . $lang . '/'. $file;
                    
                    // Execution
                    $affected += $this->$function($path_file);
                }

                return $affected;
            }
        
        } else {

            // Action without any lang
            $path .= $file;

            // Execution
            return $this->$function($path);
        }
    }

    //----------------------------------------------------------
    
    /**
    * Moves actual cache document to .old
    *
    * @param     string    $file
    * @return    bool
    */
    
    private function _real_delete($file)
    {
        $result = true;
        if(file_exists($file))
            return @rename($file, $file . '.old');    
        
        return true;
    }
    
    //----------------------------------------------------------
    
    /**
    * Cleans up all cache files related to the request
    *
    * @param     string    $file
    * @return    int
    */
    
    private function _real_purge($file)
    {
        $file_old = $file . '.old';
        $file_new = $file . '.new';
        $res = false;

        if(file_exists($file))
            $res = $res || unlink($file);
            
        if(file_exists($file_old))
            $res = $res || unlink($file_old);
        
        if(file_exists($file_new))
            $res = $res || unlink($file_new);

        // Must assume everything okay, some files might not exist    
        return $res;
    }
    
    //----------------------------------------------------------
    
    /**
    * Restores the last version of the cache file
    *
    * @param     string    $file
    * @return    bool
    */
    
    private function _real_restore($file)
    {
        // Old file
        $file_old = $file . '.old';

        // Only if no dispatcher errors have been generated
        if($this->dispatcher_errors == NULL)
        {
            if(file_exists($file_old))
            {
                if(@rename($file_old, $file));
                    return true;
            }

            $this->storeError('The file '. $file_old .' does not exist. Nothing to restore');
            return false;
        }
        $this->storeError($this->dispatcher_errors);
        return false;
    }

    //--------------------------------------------------------

    /**
    * Magic for real operations
    *
    * @param    string    $method
    * @param    array     $arguments
    */

    protected function _magic($method, $arguments)
    {
        $available = array('delete','purge','restore');
        if(in_array($method, $available))
        {
            return $this->_real_operation($method);
        }
        $this->method_does_not_exist();
    }
}
?>
