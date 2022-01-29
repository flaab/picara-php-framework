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
* Implements validation for all models 
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1.1
*/

abstract class Pi_validatable extends Pi_exportable
{
    /**
    * Stores all generated validation errors
    */
    protected $validation_errors = NULL;    

    //--------------------------------------------------------

    /**
    * Performs the object validation according the given validation rules,
    * and stores the result into the model. 
    *
    * A validation rule can be a regular expression or a validation function.
    * 
    * Optional fields are validated if they are not empty
    */
    
    public final function validate()
    {
        // Clear before making new ones
        $this->clear_validation_errors();

        // Grab optional fields
        $optional = $this->metadata->get_optional_fields($this->my_class);
        $required = $this->metadata->get_required_fields($this->my_class);
        
        // Callbacks are tested
        $this->test_callbacks($this->config->callbacks->before->validate);
        $this->test_callbacks($this->config->callbacks->after->validate);
        
        // Before callbacks execution
        $this->execute_callbacks($this->config->callbacks->before->validate);
        
        // Wrong fields arr
        $wrong_fields = array();
        
        // Check all required fields are there
        foreach($required as $field)
        {
            if($this->fields->{$field} == "" || is_null($this->fields->{$field}) || $this->fields->{$field} == "NULL")
            {
                $this->add_validation_error(ucfirst($field) ." cannot be empty");
                $wrong_fields[] = $field;
            }
        }

        // Validation rules must be an array
        if(is_object($this->config->validation->rules))
        {
            // Foreach validation field
            foreach($this->config->validation->rules as $field => $content)
            {
                // If already validated as empty
                if(in_array($field, $wrong_fields)) continue;

                // Optional fields are only validated if they are not empty
                if(!(in_array($field, $optional) && empty($this->fields->$field)))
                {
                    // If hashed
                    $validation = $content->rule;
                    $message = $content->message;

                    // Not hashed
                    if($validation == "") $validation = $content[0];
                    if($message    == "") $message = $content[1];

                    // If condition is a regular expression
                    if(preg_match("/^\/.*\//", $validation))
                    {
                        // Check match
                        if(!preg_match($validation, $this->fields->$field))
                        {
                            $this->add_validation_error($message);
                        }
                    } else {
                         
                        // Condition is a pi_validation function
                        if($this->validation->{$validation}($this->fields->$field) == false)
                        {
                             $this->add_validation_error($message);
                        }
                    }
                }
            }
        }
        
        // Validation performed. Callbacks executed
        $this->execute_callbacks($this->config->callbacks->after->validate);
    }
    
    //----------------------------------------------------------
    
    /**
    * Adds an error to the stored validation errors
    *
    * @param    string    $error 
    */

    public final function add_validation_error($error)
    {
        $this->validation_errors[] = $error;
    }
    
    //----------------------------------------------------------
    
    /**
    * Cleans up stored validation errors
    */

    private final function clear_validation_errors()
    {
        $this->validation_errors = NULL;
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves if the validation process throws a successful result
    *
    * @return bool
    */
    
    public final function validation_ok()
    {
        if($this->validation_errors != NULL)
        {
            return false;
        } else {
            return true;
        }
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves if the validation process has failed
    *
    * @return bool
    */
    
    public final function validation_failed()
    {
        if($this->validation_errors == NULL)
        {
            return false;
        } else {
            return true;
        }
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves all stored validation errors.
    *
    * @return array|null
    */
    
    public final function get_validation_errors()
    {
        return $this->validation_errors;
    }
    
    //----------------------------------------------------------
    
    /**
    * Prints on the screen all validation errors. Should only be used for development purposes
    */
    
    public final function display_validation_errors()
    {
        $string =  'Validation errors '. LINE_BREAK  .'---';
        
        if(count($this->validation_errors) == 0)
          {
              $string .= LINE_BREAK . "None";
              
          } else {
          
              foreach($this->validation_errors as $key => $value)
             {
                   $string .= LINE_BREAK .'(' . $key . ') => ' . $value;
              }
          }
          $string .= LINE_BREAK;
          
          echo $string;
    }
}
?>
