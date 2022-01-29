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
* Implements shared functionality among shell controllers
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/
 
abstract class Pi_shell_controller extends MyController
{
    /**
    * Stores user options, for example --name='John doe'
    */
    protected $options = false;

    /**
    * Stores user assertions, for example --help
    */
    protected $assertions = array();

    /**
    * Stores valid assertions to be received
    */
    protected $valid_assertions = array();

    /**
    * Stores valid options to be received
    */
    protected $valid_options = array();

    /**
    * Line break char
    */
    private $line_break = LINE_BREAK;

    /**
    * Error display title
    */
    private $error_title = 'Errors have ocurred';
    
    //----------------------------------------------------------
    
    /**
    * Puts received string into output
    *
    * @param    string    $output
    */
    
    protected final function put($output)
    {
        echo $output;
    }
    
    //----------------------------------------------------------
    
    /**
    * Reads a line from the user
    *
    * @param    string    $output
    */
    
    protected final function input($message = 'Please type: ')
    {
        $var = readline($message ." ");
        return(trim($var));
    }
    

    //----------------------------------------------------------
    
    /**
    * Puts received string and a line break
    *
    * @param    string    $output
    */
    
    protected final function putline($output = '')
    {
        echo $output . $this->line_break;
    }

    //----------------------------------------------------------

    /**
    * Puts a received string underlined
    *
    * @param    string    $output
    */

    protected final function putunderlined($output = '')
    {
        echo(' '. $output . $this->line_break);
        $this->put(' ');
        for($it = 0; $it < strlen($output); $it++)
            $this->put('-');
        $this->put("\n");
    }

    
    //----------------------------------------------------------
    
    /**
    * Puts received string and a double line break
    *
    * @param    string    $output
    */
    
    protected final function putdoubleline($output = '')
    {
        echo $output . $this->line_break . $this->line_break;
    }

    //----------------------------------------------------------

    /**
    * Displays errors and dies
    *
    * @param    array|string    $data
    */

    protected final function abort($data = false)
    {
        // If extra message received, store it
        if($data != false)
            $this->storeError($data);
       
        $this->putline();
        // If there are errors to display, display them
        if($this->failed())
        {
            //$this->putunderlined($this->error_title);
            foreach($this->getErrorStore() as $error)
            {
                $this->putline(" (!) $error"); 
            }

            $this->putline();
        }

        // Execution aborted message
        $this->putdoubleline(" Execution aborted");

        exit(1);
    }

    //--------------------------------------------------------

    /**
    * Returns if options have been received
    *
    * @return   bool
    */

    protected final function options()
    {
        if($this->options != false)
            return true;

        return false;
    }

    //--------------------------------------------------------

    /**
    * Returns if any assertions have been received
    *
    * @return   bool
    */

    protected final function assertions()
    {
        if(count($this->assertions) > 0)
            return true;

        return false;
    }

    //--------------------------------------------------------

    /**
    * Returns if given assertion has been received
    *
    * @return   bool
    */

    protected final function assertion($name)
    {
        if(in_array($name, $this->assertions))
            return true;

        return false;
    }

    //--------------------------------------------------------

    /**
    * Checks if given assertion is valid
    *
    * @return   bool
    */

    private final function validate_assertion($assertion)
    {
        // If restrictions applied
        if(is_array($this->valid_assertions) && count($this->valid_assertions) > 0)
        {
            if(in_array($assertion, $this->valid_assertions))
                return true;

            return false;
        }

        // No restrictions
        return true;
    }

    //--------------------------------------------------------

    /**
    * Checks if given option is valid
    *
    * @return   bool
    */

    private final function validate_option($option)
    {
        // If restrictions applied
        if(is_array($this->valid_options) && count($this->valid_options) > 0)
        {
            if(in_array($option, $this->valid_options))
                return true;

            return false;
        }

        // No restrictions
        return true;
    }
    
    //--------------------------------------------------------
    
   
    /**
    * Stores given options and executes desired action
    * 
    * @param    string    $action
    * @param    array     $args
    * @param    array     $options
    * @param    array     $assertions
    */
    
    public final function _execute($action, $args, $options, $assertions)
    {
        // Nice blank line at start :-)
        $this->putline();

        /*
        * Options are checked and stored
        */
        if(count($options) > 0)
        {
            $this->options = new StdClass();
            
            foreach($options as $key => $value)
            {
                // If option is valid, gets stored
                if(!$this->validate_option($key))
                {
                    $this->storeError("Unknown option $key");
                } else {
                    $this->options->$key = $value;
                }
            }
        }

        /*
        * Assertions are checked and stored
        */
        foreach($assertions as $assertion)
        {
            // If assertion is valid, gets stored
            if(!$this->validate_assertion($assertion))
            {
                $this->storeError("Unknown assertion $assertion");
            } else {
                $this->assertions[] = $assertion;
            }
        }

        // If any errors stored
        if($this->failed()) $this->abort();
        
        // Load config file if needed before action
        $this->load_config();

        // Before action callbacks
        $this->controller_callbacks($this->before_action);
        
        // Argument count
        if(!isset($args)) $args = array();
        $amount = count($args);

        // Err string
        $err_string = "Framework Error\n". 
                       "---------------\n".
                       "Missing arguments for requested controller action.\n\n";
    
        // Execute or error
        try {
          call_user_func_array(array($this, $action), $args);
        } catch(ArgumentCountError $e) {
          print($err_string);
          die;
        }
        
        // After action callbacks
        $this->controller_callbacks($this->after_action);

        // Nice blank line at the end
        $this->putline();
    }

}
?>
