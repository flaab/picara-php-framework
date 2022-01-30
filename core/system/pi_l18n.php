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
* Calculates and sets the locale for the execution environment
*
* @package    System 
* @author     Arturo Lopez <arturo@picara-framework.org>
* @copyright  Copyright (c) 2008-2009, Arturo Lopez
*/

class Pi_l18n extends Pi_error_store
{
    /**
     * Accepted languages as per application configuration
     */
    public $languages;

    /**
     * Language for request
     */
    public $language;

    /**
     * Lang code for request
     */
    public $lang;
    
    //----------------------------------------------------------
    
    /**
    * Constructor performs the checks and assigns the lang
    *
    * @param    string    $url_lang     Lang received in URL, if any
    */
    
    public function __construct()
    {
        // Globals
        global $_LANGUAGES;

        // Store the array of supported languages
        $this->languages = $_LANGUAGES;

        // Assume default lang is the one
        $l= DEFAULT_LANG;

        // If lang support
        if(LANG_SUPPORT)
        {
            // If forced lang is set, then this is it.
            // We don't even check if it is supported.
            if(FORCED_LANG != false && strlen(FORCED_LANG) >= 2)
            {
                $l = FORCED_LANG;
            
            // No forced lang. Auto detect.
            } else {
                $suggested_lang = $this->guess_lang();
                if($this->is_supported($suggested_lang))
                {
                    $l = $suggested_lang;
                } else {
                    // Fall to default
                    $l = DEFAULT_LANG;
                }
            }
        }

        // Set lang
        $this->set_lang($l);
    }

    //----------------------------------------------------------

    /**
     * Returns all the links to change language on this very page.
     * Ignoring the current language of course.
     * 
     * @return  array 
     */
    public function get_change_lang_links($request_array)
    {

        $arr = array();
        if(LANG_SUPPORT && LANG_IN_URLS)
        {
            foreach($this->languages as $key => $value)
            {
                $arr[$key] = array(
                    'name'  => $value,
                    'link'  => PICARA_BASE_HREF . $key . '/'. implode('/', $request_array),
                );
            }
        }
        return($arr);
    }
     
    //----------------------------------------------------------

    /**
     * Checks if language is supported
     * 
     * @param   string  $l
     * @return  bool
     */
    
    public function is_supported($l)
    {
        return(in_array($l, array_keys($this->languages)));
    }

    //----------------------------------------------------------
    /**
     * Attempts to change the language, returns result.
     * 
     * @param   string  $l
     * @return  bool
     */

     public function change_lang($l)
     {
        if($this->is_supported($l))
        {
            $this->set_lang($l);
            return(true);
        } else {
            return(false);
        }
     }

    //----------------------------------------------------------
    
    /**
     * Sets the lang received as parameter.
     * 
     * @param   string  $lang 
     */

    public function set_lang($l)
    {
        // Save
        $this->lang = $l; 
        $this->language = $this->languages[$l];
    
        // Set
        putenv("LANG=". $this->lang); 
        putenv("LANGUAGE=". $this->lang); 
        setlocale(LC_ALL, $this->lang);
        $domain = "messages";
        bindtextdomain($domain, "locales"); 
        bind_textdomain_codeset($domain, 'UTF-8');
        textdomain($domain);
    }

    //----------------------------------------------------------
    
    /**
    * Guesses the lang for this request using many conditions.
    * It might not coincide with a supported lang in the list.
    * The condition list might grow.
    *
    * @return   string  
    */
    public function guess_lang()
    {
        $suggested_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return($suggested_lang);
    }
}
