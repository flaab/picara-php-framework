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
* Provides the active record functionality for all models
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

abstract class Pi_record extends Pi_imagecontainer
{
    //===========================================================
    // SQL FUNCTIONS
    //===========================================================
    
    /**
    * Inserts a new row in the database for this model, acording the 
    * autoincrement config property and the stored values. If the inserting
    * sequence does not exist, it will try to create it.
    *
    * @return   bool
    * @example  record/insert.php
    */
    
    public final function insert()
    {
        // Not executed if something failed
        if($this->failed())
        {
            trigger_error('Previously model existing errors have prevented the insertion to be performed. Check log file for details', E_USER_WARNING);
            return false;
        }
        
        // Populate fields
        $this->populateDateCreated();

        // Fix null values
        $this->fixNullValues();
         
        // Test callbacks
        $this->test_callbacks($this->config->callbacks->before->insert);
        $this->test_callbacks($this->config->callbacks->after->insert);
       
        // Executes callbacks
        $this->execute_callbacks($this->config->callbacks->before->insert);

        // If any of before query callbacks stored an error, transaction is not performed
        if($this->failed())    return false;
        
        // Normal insertion is performed
        try {
            
            $result = $this->my_connection->AutoExecute($this->config->table, $this->getSqlArray(), 'INSERT');
            
        } catch (exception $e) {

            // Insertion failed, but we should only use the sequence if the model in configured to be auto-incrementable
            if($this->config->autoincrement == true)
            {
                // Tries to grab the next sequence id
                $nextId = $this->my_connection->GenID($this->default_sequence);
                
                // If nextId is zero, sequence does not exist and we should try to create it
                if($nextId == 0)
                {
                    // If creating a sequence fails, the insertion failed for other reason and we should abort.
                    if(!$this->createDefaultSequence())
                    {
                        $this->storeAndLog(Pi_db::getExceptionString($e));
    
                        // We might however, not have enough permissions to create sequences so a warning is logged
                        $this->log->warning('SQL insert failed; it seems connection user does not have permission to create sequences');
    
                        return false;                
                    }
                    
                    $nextId = $this->my_connection->GenID($this->default_sequence);
                }
                
                // Got here means we have a valid next primary key to use
                try {
        
                    $result = $this->my_connection->AutoExecute($this->config->table, $this->getSqlArray('INSERT', $nextId), 'INSERT');    
                    
                } catch (exception $e) {
                    
                    // Insertion fails again, a warning is raised
                    $this->storeAndLog(Pi_db::getExceptionString($e));
                    trigger_error('SQL insert failed, check log file for details', E_USER_WARNING);
                    return false;                    
                }

            } else {

               // The model is not configured as auto-incrementable 
               $this->storeAndLog(Pi_db::getExceptionString($e));
               trigger_error('SQL insert failed, check log file for details', E_USER_WARNING);
               return false;                    

            }
        }
        
        // If insertion failed
        if($result != true)
        {
            $msg = "SQL insert failed, database said: ". $this->my_connection->ErrorMsg();
            trigger_error($msg, E_USER_WARNING);
            $this->storeAndLog($msg);
            return false;
        }
        
        // Grab insert id if auto-increment
        if($this->config->autoincrement == true)
            $this->fields->id = $this->my_connection->Insert_ID();

        // Callbacks
        $this->execute_callbacks($this->config->callbacks->after->insert);

        // Log
        $this->log->message("New ". $this->config->display ." inserted succesfully (". $this->primary_key ." = ". $this->fields->{$this->primary_key} .")");

        // OK!
        return true;
    }
    
    //----------------------------------------------------------
        
    /**
    * Updates the represented record
    *
    * @return     bool
    * @example    record/update.php
    */
     
    public final function update()
    {
        // Not executed if something failed
        if($this->failed())
        {
            trigger_error('Previously model existing errors have prevented the update to be performed. Check log file for details', E_USER_WARNING);
            return false;
        }
        
        // Fix null values
        $this->fixNullValues();

        // Populate date_modified if exists
        $this->populateDateModify();
        
        // Checks callbacks
        $this->test_callbacks($this->config->callbacks->before->update);
        $this->test_callbacks($this->config->callbacks->after->update);
       
        // Executes callbacks
        $this->execute_callbacks($this->config->callbacks->before->update);
        
        // If any of before query callbacks stored an error, transaction is not performed
        if($this->failed())    return false;
        
        // Query
        $result = $this->my_connection->AutoExecute($this->config->table, $this->getSqlArray('UPDATE'), 'UPDATE', $this->primary_key .'='. $this->fields->{$this->primary_key});
        
        if($result != true)
        {
            $msg = "SQL update failed, database said: ". $this->my_connection->ErrorMsg();
            trigger_error($msg, E_USER_WARNING);
            $this->storeAndLog($msg);
            return false;
        }

        // Callbacks
        $this->execute_callbacks($this->config->callbacks->after->update);
        
        // Log
        $this->log->message($this->config->display ." updated succesfully (". $this->primary_key ." = ". $this->fields->{$this->primary_key} .")");

        // ok
        return true;
     }
     
    //----------------------------------------------------------
         
    /**
    * Deletes represented record from database
    *
    * @return bool
    * @example record/delete.php
    */
     
    public final function delete()
    {
        // We check that beforeDelete() and afterDelete() methods exist
        $this->test_callbacks($this->config->callbacks->before->delete);
        $this->test_callbacks($this->config->callbacks->after->delete);
        
        // If we got here we will execute the before methods
        $this->execute_callbacks($this->config->callbacks->before->delete);
        
        // If any of before query callbacks stored an error, transaction is not performed
        if($this->failed())    return false;
        
        // Query
        $result = $this->my_connection->Execute("DELETE FROM ". $this->config->table ." WHERE ".  $this->primary_key ."=". $this->fields->{$this->primary_key});
        
        if($result != true)
        {
            $msg = "SQL delete failed, database said: ". $this->my_connection->ErrorMsg();
            trigger_error($msg, E_USER_WARNING);
            $this->storeAndLog($msg);
            return false;
        }
        
        // delete dependant models if any
        $this->cascadingDelete();

        // Delete image tree
        $this->delete_image_tree();

        // Delete all file blocks
        $this->delete_all_blocks();

        $this->execute_callbacks($this->config->callbacks->after->delete);
        
        // Log
        $this->log->message($this->config->display ." deleted succesfully (". $this->primary_key ." = ". $this->fields->{$this->primary_key} .")");

        // OK! 
        return true;        
     }
     
    //----------------------------------------------------------
         
    /**
    * Returns the array to be provided to the sql execution
    *
    * @param string $action
    * @param bool $useSeq
    * @return array
    */    
    
    private final function getSqlArray($action = 'INSERT', $nextId = NULL)
    {
        // If the model is autoincremented, primary key is ignored
        if($this->config->autoincrement == true)
            $ignored = array($this->primary_key);
        
        // If date populated is enabled, date_created will be ignored for update and date_modified for insert
        if($this->configuration->population == true)
        {
            if($action == 'INSERT')
                $ignored[] = UPDATE_POPULATED;
            else
                $ignored[] = DATE_POPULATED;
        }  

        // An array is created
        foreach($this->fields as $key => $value)
        {
            if(!in_array($key, $ignored))
            {
                $sqlarray[$key] = $value;
            }
        }
        
        // NextId should be the row id returned by the sequence (if pertinent)
        if($nextId != NULL)
        {
            $sqlarray[$this->primary_key] = $nextId;
        }
        
        return $sqlarray;
    }    
    
    //----------------------------------------------------------    
    
    /**
    * Attempts to create the table sequence
    *
    * @return int result
    */
    
    private final function createDefaultSequence()
    {
        // Start number for sequence
        $ids = $this->db->query->getLast(1, get_class($this));
        
        if($ids != NULL)
            $firstSeqId = $ids[0]->fields->{$this->primary_key};
        else
            $firstSeqId = 0;
        
        // Creating sequence
        try {

            if($this->my_connection->CreateSequence($this->default_sequence, $firstSeqId + 1))
                return true;
            
        // If no permissions, execution should not be stopped
        } catch (exception $e) {
        
            return true;
            
        }
        
        // Sequence already exists
        return true;
    }
    
    //===========================================================
    // AUTO POPULATION
    //===========================================================
    
    /**
    * Fixes string null values to php nulls.
    */

    protected final function fixNullValues()
    {
        foreach($this->fields as $key => $value)
        {
            if($value == 'NULL' || is_null($value) || strlen($value) == 0 || $value == "")
            {
                if(isset($this->fields->{$this->primary_key}) && is_numeric($this->fields->{$this->primary_key}))
                {
                    $this->fields->$key = NULL;
                } else {
                    unset($this->fields->$key);
                }
            } else if(is_string($value) && strlen($value) > 0)
            {
                //$value = str_replace("'", "\'", $value);
                $this->fields->$key = addslashes($value);
            }
        }
    }

    //--------------------------------------------------------
     
     /**
     * Populates date_modified value
     *
     * @access private
     */
     
     protected final function populateDateModify()
     {
         if($this->field_exists(DATE_MODIFIED_POPULATED) && $this->config->population == true)
         {
             $field = DATE_MODIFIED_POPULATED;
             $this->fields->$field = date("Y-m-d");             
         }
         
         if($this->field_exists(DATETIME_MODIFIED_POPULATED) && $this->config->population == true)
         {
             $field = DATETIME_MODIFIED_POPULATED;
             $this->fields->$field = date("Y-m-d H:i:s");             
         }
     }
     
    //----------------------------------------------------------
     
    /**
     * Populates date_created just before an insert action
     *
     * @access private
     */
     
     public final function populateDateCreated()
     {
         // If field exists and population is enabled
         if($this->field_exists(DATE_CREATED_POPULATED) && $this->config->population == true)
         {
             $field = DATE_CREATED_POPULATED;
             $this->fields->$field = date("Y-m-d");             
         }
         
         // If field exists and population is enabled
         if($this->field_exists(DATETIME_CREATED_POPULATED) && $this->config->population == true)
         {
             $field = DATETIME_CREATED_POPULATED;
             $this->fields->$field = date("Y-m-d H:i:s");             
         }
     }
     
    //===========================================================
    // DATABASE SCHEMA
    //===========================================================
     
     /**
     * Retrieves an array with the table data dictionary
     *
     * @return array
     */
     
     public final function getSchema()
     {
        return $this->metadata->read_schema(get_class($this));
     }
     
     //----------------------------------------------------------
         
     /**
     * Retrieves the data type of any passed field
     *
     * @param string $field
     * @return string 
     */
     
     public final function getFieldType($field)
     {
         $schema = $this->metadata->read_schema($this->my_class);
         if(!isset($schema[$field])) return NULL;
         return $schema[$field]['type'];
     }
     
     //----------------------------------------------------------
         
     /**
     * Retrieves the first string field in the table schema
     *
     * @return string Field
     */
     public final function getFirstString()
     {
         $schema = $this->metadata->read_schema(get_class($this));
         $fields = array_keys($schema);
         
         foreach($fields as $field)
         {
             if($schema[$field]['metatype'] == STRING)
             {
                 return $field;
             }
         }
         return NULL;
     }
     
     //----------------------------------------------------------
         
     /**
     * Retrieves the first text field in the table schema
     * @return string Field
     */
     public final function getFirstText()
     {
         $schema = $this->metadata->read_schema(get_class($this));
         $fields = array_keys($schema);
         $counter = 0;

         foreach($fields as $field)
         {
             if($schema[$field]['metatype'] == TEXT)
             {
                 return $field;
             }
         }
         return NULL;
     }

    //---------------------------------------------------------

    /**
    * This is a shade function for scaffolding that does not
    * have access to the metadata object.
    *
    * @return   array
    */

    public function getFields()
    {
        return $this->metadata->read_columns($this->my_class);
    }
     
    //---------------------------------------------------------

    /**
    * Retrieves foreign fields
    *
    * @return   array
    */

    public function getForeignFields()
    {
        return $this->metadata->get_foreign_fields(get_class($this));
    }

    //----------------------------------------------------------
      
    /**
     * Checks if the given field name exists in our schema
     *
     * @param string $field
     * @return boolean
     */
     
     public final function field_exists($field)
     {
         if(in_array($field, $this->metadata->read_columns(get_class($this))))
         {
             return true;
         }
         
         return false;
     }
     
    //----------------------------------------------------------
         
    /**
    * Alias to get_enum_values() at Metadata class
    *
    * @param string $field
    * @return array
    */
    
    public final function getEnumValues($field = '') 
    {
        return $this->metadata->get_enum_values($this->my_class, $field); 
    }

    //----------------------------------------------------------

    /**
    * Alias to get_forbidden_fields() at Metadata class 
    *
    * @return array
    */
     
    public final function getForbiddenFields()
    {
        return $this->metadata->get_forbidden_fields($this->my_class);
    }
    
    //----------------------------------------------------------
     
    /**
    * Alias to get_optional_fields() at Metadata Class
    *
    * @return array
    */
     
    public final function getOptionalFields()
    {
        return $this->metadata->get_optional_fields($this->my_class);
    }

     
    //----------------------------------------------------------
     
    /**
    * Alias to get_required_fields() at Metadata Class
    *
    * @return array
    */
     
    public final function getRequiredFields()
    {
        return $this->metadata->get_required_fields($this->my_class); 
    }
}
?>
