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
* Implements shared functionality for all models.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1.1
*/

abstract class Pi_model extends Pi_record
{  
    /**
    * Fow fields and values
    */
    var $fields;

    /**
    * Old fields and values
    */
    var $old_fields;
    
    /**
    * Writes down self class name
    */
    protected $my_class;
    
    /**
    * Configuration object for this model
    */
    protected $config;
    
    /**
    * Default sequence to use when inserting
    */
    protected $default_sequence;
    
    /**
    * Pointer to this model connection (has been proved to be faster)
    */
    protected $my_connection;
    
    /**
    * Stores primary key field name
    */
    protected $primary_key = PRIMARY_KEY;

    /**
    * Stores images object if extra or main images are enabled
    */
    public $images = NULL;

    /**
     * Possible relationships
     */
    static $possible_relationships = array('has_one','has_many','belongs_to');
    
    //----------------------------------------------------------                                                
    
    /**
    * Constructs the instance of the model from a row primary key or an array
    *  
    * @param    int|array               $arg
    * @param    bool                    $force   
    * @example  model/construct.php
    */
    
    public final function __construct($arg = NULL, $force = false)
    {
        parent::__construct();
        
        // Our fields will be stored here
        $this->fields = new stdClass();
        
        // We must store our class name, get_class() function is not that fast
        $this->my_class = get_class($this);
        
        // Our configuration
        $this->config = $this->metadata->config->get($this->my_class);
        
        // Db connection needed
        $this->db->connect($this->config->connection);
        
        // Pointer to our database connection
        $this->my_connection = $this->db->link->{$this->config->connection};
        
        // Check that all after construct filters exists 
        if(!$this->test_callbacks($this->config->callbacks->after->construct))
        trigger_error('Construction for model '. $this->my_class .' failed, at least one filter function does not exist', E_USER_ERROR);
        
        // Zero creates an empty object
        if(is_numeric($arg) && $arg == 0) return;
        
        // Sequence name follows postgresql seq naming convention
        $this->default_sequence = $this->config->table . '_'. $this->primary_key .'_seq';
        
        // Constructs from database
        if(is_numeric($arg))
        {
            $this->fields->{$this->primary_key} = $arg;
            $this->refresh();
            $this->execute_callbacks($this->config->callbacks->after->construct);
            if(is_object($this->fields))
                foreach($this->fields as $key => $value)
                    if(is_string($value) && strlen($value) > 0)
                        $this->fields->$key = stripslashes($value);
            return;
        }
        
        // constructs from a hash
        if(is_array($arg))
        {
            // Forced assignment used by Query and ModelCollection to gain speed, do not use manually 
            if($force == true)
            {
                foreach($arg as $key => $value)
                {
                    // Assign
                    $this->fields->$key = $value;
                }

                $this->duplicate_values();
                $this->execute_callbacks($this->config->callbacks->after->construct);
                return;
            }
        
            // Our fields
            $required = $this->metadata->get_required_fields($this->my_class);
            $optional = $this->metadata->get_optional_fields($this->my_class);
            $forbidden = $this->metadata->get_forbidden_fields($this->my_class);
            // Checks number of parameters
            if(!(count($arg) >= count($required) && count($arg) <= (count($required) + count($optional))))
            {
                $msg = 'Received hash indexes do not fit model requirements';
                 trigger_error($msg, E_USER_WARNING);                 
                $this->storeAndLog($msg);
            }
        
            // Checks all required fields
            foreach($required as $requiredField)
            {
                $satisfied = false;
        
                // Is this required parameter in our array?
                foreach($arg as $field => $value)
                {
                    if($requiredField == $field && !is_null($value))
                    {
                        $satisfied = true;
                    }
                }
        
                // Outa here, if satisfied is still false, this parameter is not satisfied
                if($satisfied == false)
                {
                    $msg = "Missing required field $requiredField for model " . $this->my_class;
                    trigger_error($msg, E_USER_WARNING);
                    $this->storeAndLog($msg);
                }
            }         
        
            // Checks if missing parameters are optional 
            foreach($arg as $field => $value)
            {
                // A parameter can only be a required or optional
                if(!(in_array($field, $required) || in_array($field, $optional)))
                {
                    $msg = "Unknown field $field for model " . $this->my_class;
                    trigger_error($msg, E_USER_WARNING);
                    $this->storeAndLog($msg);
                }
            }
        
            // Makes sure no forbidden field has been received
            foreach($arg as $field => $value)
            {
                if(in_array($field, $forbidden))
                {
                    $msg = "Forbidden field $field cannot be provided for model " . $this->my_class;
                    trigger_error($msg, E_USER_WARNING);
                    $this->storeAndLog($msg);
                }
            }
        
            // Exists on failure
            if($this->failed()) return;    
        
            // Everything has gone fine, assigns
            foreach($arg as $key => $value)
                $this->fields->$key = $value;
        
            // Filters            
            $this->execute_callbacks($this->config->callbacks->after->construct);
        
            // Validation
            if($this->config->validation->auto == true) $this->validate();        
        
            return;
        }
        
        // Not valid constructor parameter
        $msg = 'Wrong parameter for constructing a model, an integer or array is expected';
        $this->storeError($msg);                
        trigger_error($msg, E_USER_WARNING);
    } 
    
    //===========================================================
    // RELATIONSHIPS
    //===========================================================
    
    /**
    * Returns the foreign key field name for a given related model.
    *
    * @param     string    $relatedModel
    * @return    string|null    
    */
    
    public final function getRelationshipFK($relatedModel)
    {
        return $this->metadata->get_relationship_fk($this->my_class, $relatedModel);
    }
    
    //----------------------------------------------------------
    
    /**
    * Will return the related model name from a given foreign key field name
    *
    * @param     string         $foreignKey
    * @return    string|null    
    */
    
    private final function getRelationshipName($fk)
    {
        return $this->metadata->get_relationship_name_from_fk($this->my_class, $fk);
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves the convention foreign key field name for a given model
    *
    * @param    string
    * @return   string
    */
    private final function getConventionFkField($model)
    {
        return $this->metadata->get_convention_fk_field($model);
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves the convention relationship name for a given foreign key
    *
    * @param    string
    * @return   string
    */
    private final function getConventionFkRelation($fk)
    {
        return $this->metadata->get_convention_model_from_fk($fk);
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves a list of existing related models
    *
    * @return   array|null
    */
    
    public final function getRelatedModels()
    {
        return $this->metadata->get_related_models($this->my_class);
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves a list of NM related models
    *
    * @return   array|null
    */
    
    public final function getNMRelatedModels()
    {
        return $this->metadata->get_n_m_related_models($this->my_class);
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns default display name for this object, by grabbing
    * the content for the first string field in the table schema.
    * This function is used by related built-in pagination. Please
    * note this function is overridable by the user
    *
    * @return   string
    */
    
    public function getValueString()
    {
        $field = $this->metadata->get_first_string_field($this->my_class);
        
        if(is_null($field))
            return $this->fields->{PRIMARY_KEY};

        return $this->fields->{$field};
    }
    
    //----------------------------------------------------------
    
    /**
    * Retrieves a collection of associated objects (for instance posts - nm rel - tags))
    *     
    * @param    string    $requestedModel 
    * @param    int       $offset 
    * @param    int       $rowcount 
    * @param    int       $shape
    * @return   array
    */
    
    public final function getAssociated($requestedModel, $offset = NULL, $rowcount = NULL, $orderBy = NULL, $shape = COLLECTION)
    {
        // If requested model does not belong to our same connection...
        if(!$this->metadata->have_same_connection($this->my_class, $requestedModel))
        {
            // Error and die, user must know
            trigger_error("Different connections. ". $this->my_class ." cannot be related to ". $requestedModel, E_USER_ERROR);
        }
        
        // Find relationship is user declared relationships first
        if(is_array($this->config->relationships->has_and_belongs_to_many))
        {
            foreach($this->config->relationships->has_and_belongs_to_many as $local)
            {
                if(strtolower($local['class_name']) == strtolower($requestedModel))
                {
                    // Check model exists
                    if(!Pi_loader::model_exists($local['class_name']))
                        trigger_error("The model ". $local['class_name'] . " does not exist", E_USER_ERROR);
                    
                    // Check intermediate model exists
                    if(!Pi_loader::model_exists($local['through']))
                        trigger_error("The model ". $local['through'] . " does not exist", E_USER_ERROR);

                    // If there is no foreign key
                    if(empty($local['foreign_key']))
                        trigger_error("Foreign key for relationship with ". $local['class_name'] ." is empty in config file for ". $this->my_class, E_USER_ERROR);
                    
                    // If there is no foreign key to my model
                    if(empty($local['my_key']))
                        trigger_error("My foreign key for relationship with ". $local['class_name'] ." is empty in config file for ". $this->my_class, E_USER_ERROR);
                    
                    // If intermediate model does not belong to our same connection...
                    if(!$this->metadata->have_same_connection($this->my_class, $local['through']))
                    {
                        // Error and die, user must know
                        trigger_error("Different connections. ". $this->my_class ." cannot be related to ". $local['through'], E_USER_ERROR);
                    }

                    // All checked. Query and return.
                    $sql_condition = "id IN 
                                        (SELECT ". $local['foreign_key'] ." 
                                         FROM ". $this->metadata->get_table_from_model($local['through']) ." 
                                         WHERE ". $local['my_key'] ." = ". $this->fields->id .")";

                    // Desired result shape for results
                    $this->db->query->setShape($shape);
                    
                    // Get all rel_posts
                    return($this->db->query->performSearchQuery($requestedModel, $sql_condition, $orderBy, $offset, $rowcount));    
                }
            }
        }
                    
        // Not found. Try automatically. Slower! 
        $nmModels = $this->getNMRelatedModels();
        
        // Iterate all NMs
        if(count($nmModels) > 0)
        {
            // Desired result shape for results
            $this->db->query->setShape($shape);
        
            // Foreach entry
            foreach($nmModels as $intermediate => $data)
            {
                // Is this what we want?
                if(strtolower($data[0]) == strtolower($requestedModel))
                {
                    // Find foreign keys
                    $target_fk = $this->metadata->get_relationship_fk($intermediate, $requestedModel);
                    $origin_fk = $this->metadata->get_relationship_fk($intermediate, $this->my_class);
                    
                    // Query
                    $sql_condition = "id IN 
                                        (SELECT ". $target_fk ." 
                                         FROM ". $this->metadata->get_table_from_model($intermediate) ." 
                                         WHERE ". $origin_fk ." = ". $this->fields->id .")";

                    // Get all rel_posts
                    return($this->db->query->performSearchQuery($requestedModel, $sql_condition, $orderBy, $offset, $rowcount));    
                }
            } 
        }

        // Nothing.
        return(false);
    }

    //----------------------------------------------------------
    
    /**
    * Returns related models following the model config file or the database schema and conventions.
    *  
    *
    * @param    string    $requestedModel 
    * @param    int       $offset 
    * @param    int       $rowcount 
    * @param    int       $shape
    * @return   array
    */
    
    public final function getRelated($requestedModel, $offset = NULL, $rowcount = NULL, $orderBy = NULL, $shape = COLLECTION)
    {
        // If requested model does not belong to our same connection...
        if(!$this->metadata->have_same_connection($this->my_class, $requestedModel))
        {
            // Error and die, user must know
            trigger_error("Models having different connections cannot be related. ". $this->my_class ." cannot be related to ". $requestedModel, E_USER_ERROR);
        }
        
        // Related models
        $related = NULL;   
     
        // Desired result shape for results
        $this->db->query->setShape($shape);
        
        // User declared relationships
        foreach(self::$possible_relationships as $relationship)
        {
            if(is_array($this->config->relationships->$relationship))
            {
                foreach($this->config->relationships->$relationship as $local)
                {
                    if(strtolower($local['class_name']) == strtolower($requestedModel))
                    {
                        // Check model exists
                        if(!Pi_loader::model_exists($local['class_name']))
                            trigger_error("The model ". $local['class_name'] . " does not exist", E_USER_ERROR);
    
                        // If there is no foreign key
                        if(empty($local['foreign_key']))
                            trigger_error("ForeignKey for declared relationship ". $local['class_name'] ." it's empty in model ". $this->my_class, E_USER_ERROR);
    
                        // If FK is native
                        if($relationship == 'belongs_to')
                        {
                            if(!$this->field_exists($local['foreign_key']))
                                trigger_error("ForeignKey for relationship ". $local['class_name'] ." does not exist in model ". $this->my_class, E_USER_ERROR);
    
                            return $this->db->query->searchBy($this->primary_key, $this->fields->{$local['foreign_key']}, $requestedModel, false, false, $offset, $rowcount, $orderBy);

                        // Foreign key is remote
                        } else {
    
                            // Has one or has many 
                            return $this->db->query->searchBy($local['foreign_key'], $this->fields->{$this->primary_key}, $requestedModel, false, false, $offset, $rowcount,$orderBy);                        
                        }
                    }        
                }
            }
        }   // End of user declared relationships
        
        //--
        //-- Automatic detection of belongs_to relationship
        //--
        
        // Got here, check over convention relationships
        $conventionModels = $this->getRelatedModels();
        
        // If following a native FK
        if(in_array($requestedModel, $conventionModels))
        {
            // Belongs to
            $foreignKey = $this->getRelationshipFK($requestedModel);            
       
            // Results
            if(in_array($foreignKey, $this->metadata->read_columns($this->my_class)))
                return $this->db->query->searchBy($this->primary_key, $this->fields->$foreignKey, $requestedModel, false, false, $offset, $rowcount, $orderBy);                            
        }

        //--
        //-- Automatic detection of has_one or has_many
        //--
        
        // Has many or has one
        $foreignKey = $this->getRelationshipFK($this->my_class);    

        // If that model does not have our fk crush the thing up
        $columns = $this->metadata->read_columns($requestedModel);
        
        // If in the list of columns 
        if(in_array($foreignKey, $columns)) 
        {
            // Return the result
            return $this->db->query->searchBy($foreignKey, $this->fields->{$this->primary_key}, $requestedModel, false, false, $offset, $rowcount, $orderBy);    
        }
        
        // Nothing found. 
        return(false);
    }
    
    //----------------------------------------------------------
    
    /**
    * Performs cascading delete for this object before it gets deleted. But, please note that 
    * implementing constraints on the database engine increases by far the application performance. 
    * This function needs relationships to be declared on the model.
    */
    
    protected final function cascadingDelete()
    {
        // For each has many relationship declared
        if(isset($this->config->relationships->has_many) && count($this->config->relationships->has_many) > 0)
        {
            foreach($this->config->relationships->has_many as $local)
            { 
                // If depends index is declared 
                if(isset($local['cascade']))
                {
                    // If it's value it's true
                    if($local['cascade'] == true)
                    {
                        // We request all depending models
                        $collection = $this->getRelated($local['class_name']);
        
                        // If is not null we delete all of their entries
                        if($collection != NULL)  $collection->delete_all();
                    }
                }
            }
        }
    }
    
    //===========================================================
    // SEARCH ENGINE
    //===========================================================
    
    /**
    * Checks if search fields have been declared for this model
    * @return bool
    */
    
    public function isSearchable()
    {
        if(isset($this->config->search->fields))  return true;
        return false;
    }    
    
    //===========================================================
    // FETCHING AND MODIFYING OBJECT VALUES
    //===========================================================
   
    /**
    * Duplicates object fields in order to allow callbacks to compare new and old values
    */

    protected final function duplicate_values()
    {
        $this->old_fields = clone($this->fields);
    }

    /**
    * Obtains all fields and values from the represented row. Fetches the information twice,
    * in order to allow the programmer to compare old values with new values inside callback
    * functions.
    */
    
    private final function refresh()
    {
        // Our Id
        $id = $this->fields->{$this->primary_key};
    
        try
        {
            $result = $this->my_connection->Execute("SELECT * FROM ". $this->config->table ." WHERE ". $this->primary_key ."= $id");
    
        } catch(exception $e) {
    
            trigger_error(Pi_db::getExceptionString($e), E_USER_ERROR); 
        }
    
        // Values are stored
        $this->fields = $result->FetchNextObject(false);

        // If does not exist
        if(!$this->fields)
        {
            $msg = "Requested row does not exist in table ". $this->config->table ." (Primary key $id)";
            $this->storeError($msg);
            trigger_error($msg, E_USER_NOTICE);
            return;
        }
        
        // Strip slashes
        foreach($this->fields as $key => $value)
            $this->fields->$key = stripslashes($value);

        // Duplicated values are stored
        $this->duplicate_values();
    }
    
    /**
    * Use this function when you need to update more than one field from an array 
    * @param array
    */
    
    public final function changeVars($fields = NULL)
    {
        $myFields = $this->metadata->read_columns($this->my_class);
        
       // Iterate 
        if($fields != NULL && is_array($fields))
        {
            foreach($fields as $key => $value)
            {
                if(in_array($key, $myFields))
                {
                    $this->fields->$key = $value;
                }
            }
            
            // Validation if needed
            if($this->config->validation->auto == true)  $this->validate();
        }
    }
    
    //===========================================================
    // Magic methods
    //===========================================================
    
    /**
    * Implements magic functions for models
    *
    * @param    string    $method
    * @param    array     $arguments
    */
    
    protected final function _magic($method, $arguments)
    {
        /*
        * Magic function: getRelatedModel(int offset, int rowcount)
        */
    
        if(preg_match("/^getRelated(.+)(As(Collection|Ids|Arrays|Objects|Cardinality|Xml|Csv|Yml|Json))?(OrderBy(.+))?$/U", $method, $matches))
        {
            // Default arguments array
            $defaultArgs = array(NULL, NULL); 
    
            // Cross it with the received array
            $args = $this->mix_parameters($defaultArgs, $arguments);       
    
            // Result shape
            $shape = $this->_getResultShape($matches[3]);
    
            // Results
            return $this->getRelated($matches[1], $args[0], $args[1], $matches[5], $shape);
        }
        
        /*
        * Magic function: getAssociatedModel(int offset, int rowcount)
        */
    
        if(preg_match("/^getAssociated(.+)(As(Collection|Ids|Arrays|Objects|Cardinality|Xml|Csv|Yml|Json))?(OrderBy(.+))?$/U", $method, $matches))
        {
            // Default arguments array
            $defaultArgs = array(NULL, NULL); 
    
            // Cross it with the received array
            $args = $this->mix_parameters($defaultArgs, $arguments);       
    
            // Result shape
            $shape = $this->_getResultShape($matches[3]);
    
            // Results
            return $this->getAssociated($matches[1], $args[0], $args[1], $matches[5], $shape);
        }
    
        $this->method_does_not_exist($method);
    }
    
    //--------------------------------------------------------
    
    /**
    * Returns the result shape for this query. This function is similar to
    * Query::_setResultShape() but they cannot be the same as the scope and location
    * of the database object is different, and in this case, the shape must be
    * delegated to Model::getRelated()
    *
    * @param    string    $shape
    * @return   int
    */
    
    private final function _getResultShape($shape)
    {
        switch($shape)
        {
            case 'Ids': return IDS;
            case 'Arrays': return ARRAYS;
            case 'Objects':  return OBJECTS;
            case 'Cardinality': return CARDINALITY;
            case 'Xml': return XML;
            case 'Csv': return CSV;
            case 'Yml': return YML;
            case 'Json': return JSON;
            default: return COLLECTION;
        }
    }
}
?>
