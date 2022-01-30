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
* Provides a unique metadata object for the application, storing and 
* providing information about databases structure without wasting 
* resources.
*
* A unique config class is created as well, in order to 
* store and provide unique model configurations.
*
* @package      System
* @author       Arturo Lopez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1.1
*/

class Pi_metadata extends Pi_error_store
{
    /**
    * Model configuration
    */
    public $config;

    /**
    * Stored schemas
    */
    private $schemas;

    /**
    * Stored tables
    */
    private $columns;

    /**
    * Enabled langs
    */
    private $langs;

    /**
    * Db holder
    */
    private $db;

    /**
    * Instance for singleton pattern
    */
    private static $instance;

    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object. 
    */
    
    private function __construct()
    {
        $this->config = Pi_config::singleton();
    }

    
    //---------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return    Pi_metadata
    */
    
    public static function singleton() 
    {
        if (!isset(self::$instance))
        {
            $c = __CLASS__;
            self::$instance = new $c;

            // This can only be done once the object exist to avoid segmentation fault
            self::$instance->_create_db();
        }
        return self::$instance;
    }

    //--------------------------------------------------------

    /**
    * Returns enabled langs
    *
    * @return    array
    */

    public function get_enabled_langs()
    {
        // If already included
        if(is_array($this->langs) && count($this->langs) > 0)
            return $this->langs;

        // Includes it and saves it
        //$enabled_file = USERCONFIG . 'langs.yml';

        // Include it
        //if(!file_exists($enabled_file))
        //    trigger_error("Config file '$enabled_file' does not exist, please create it first", E_USER_ERROR);

        // Loads yml file with connection information
        //require_once(VENDORS . 'spyc/spyc.php5');

        // Returns array
        //$enabled = Spyc::YAMLLoad($enabled_file);

        // Assign
        global $_LANGUAGES;
        $this->langs = $_LANGUAGES;

        return $this->langs;
    }

    //--------------------------------------------------------

    /**
    * Checks if given lang is supported
    *
    * @param    string    $lang_code
    * @return   bool
    */

    public function is_enabled_lang($lang)
    {
        $enabled = $this->get_enabled_langs();
        if(in_array($lang, array_keys($enabled)))
            return true;

        return false;
    }

    //--------------------------------------------------------

    /**
    * Checks if two models have the same connection.
    * This is really important because relationships among
    * models having different connections cannot be enforced.
    *
    * @param    string    $m1
    * @param    string    $m2
    */

    public function have_same_connection($m1, $m2)
    {
        $m1 = $this->config->get($m1);
        $m2 = $this->config->get($m2);
        if($m1->connection == $m2->connection)
            return true;
        return false;
    }

    //--------------------------------------------------------

    /**
    * Creates and returns table schema from a model
    *
    * @param    string    $model
    */

    public function read_schema($model)
    {
        // If already delivered
        if(isset($this->schemas[$model]))
            return $this->schemas[$model];
        
        // Not stored yet
        $config = $this->config->get($model);
        $this->db->connect($config->connection);

        // Constructs the schema
        $fields = $this->db->link->{$config->connection}->MetaColumns($config->table);
        
        // Tweaks the schema a bit for the framework
        foreach($fields as $object)
        {
            // If no has_default
            if(!isset($object->has_default)) $object->has_default = false;

            $schema[$object->name] = array(
            
                'type'          => $object->type,
                'metatype'      => $this->db->link->{$config->connection}->MetaType($object->type),
                'null'          => !$object->not_null,
                'has_default'   => $object->has_default,
                'max_length'    => $object->max_length
            );
            
            // Any enums are mysql
            if(isset($object->enums))
            {
                $schema[$object->name]['enums'] = $object->enums;
                $schema[$object->name]['metatype'] = ENUM;
            }
            
            //==========================================================
            // Tweak to treat SET types as ENUM types (MySql)
            //==========================================================
            
            if(preg_match("/^set\(.+\)/", $schema[$object->name]['type']))
            {
                $options = preg_replace("/(set|'|\(|\))/", '', $schema[$object->name]['type']);
                $schema[$object->name]['enums'] = explode(',', $options);
                $schema[$object->name]['metatype'] = ENUM;
            }

            // Some MySql versions return quoted enum values and break scaffolding forms, so lets fix it
            if(isset($schema[$object->name]['enums']))
            {
                $tot = count($schema[$object->name]['enums']);
                for($it = 0; $it < $tot; $it++)
                {
                    if(preg_match("/^'.*'$/", $schema[$object->name]['enums'][$it]))
                        $schema[$object->name]['enums'][$it] = preg_replace("/(^'|'$)/", '', $schema[$object->name]['enums'][$it]);
                }
            }
        } 

        // Gets stored
        $this->schemas[$model] = $schema;
        return $schema;
    }

    //--------------------------------------------------------

    /**
    * Creates the scaffolding schema by classifying field into
    * the following categories:
    *
    * - fields
    * - foreign keys
    *
    * And adding all those n-m relationships that should be scaffolded as well
    *
    * @param    string    $model
    * @param    int       $id
    */

    public function read_scaffold_schema($model, $id = NULL)
    {
        // Complete table schema
        $schema = $this->read_schema($model);

        // Complete field list
        $fields = $this->read_columns($model);
        
        // Forbidden fields (those that should not be used to construct a model)
        $forbidden = $this->get_forbidden_fields($model);
        $ignored   = $this->get_ignored_fields($model);

        // Recognized foreign keys
        $fks = $this->get_foreign_fields($model);

        // Config object for this model
        $config = $this->config->get($model);

        // Response
        $res = array(

            'Belongings'    => array(),                              // Foreign keys
            'Data'          => array(),                              // Fields
            'Associations'  => $this->get_n_m_list($model, $id),     // N-M relationships
    
        );

        // Model must have fields
        if(count($fields) == 0) trigger_error('The model '. $model .' does not have any fields', E_USER_ERROR);

        // Iteration over all fields
        foreach($fields as $field)
        {
            // Add field if not forbidden
            if(!in_array($field, $forbidden) && !in_array($field, $ignored))            
            {
                // If this field is a foreign key
                if(in_array($field, $fks))
                {   
                    // Obtain referenced model
                    $fk_model = $this->get_relationship_name_from_fk($model, $field);

                    $res['Belongings'][$fk_model] = $field;

                // Its a normal field
                } else {  
                    
                    $res['Data'][$field] = $schema[$field];

                }
            }
        }
        
        // There it goes
        return $res;
    }

    //--------------------------------------------------------
    
    /**
    * Reads all columns from a model
    *
    * @param    string    $model
    */

    public function read_columns($model)
    {
        // If already deliveded
        if(isset($this->columns[$model]))
            return $this->columns[$model];

        // Not stored yet
        $schema = $this->read_schema($model);
        if(is_null($schema)) return(array());

        $this->columns[$model] = array_keys($schema);
        return $this->columns[$model];
    }

    //--------------------------------------------------------

    /**
    * Returns the first string field of a model
    *
    * @param    string    $model
    * @return   string
    */

    public function get_first_string_field($model)
    {
         $schema = $this->read_schema($model);
         $fields = array_keys($schema);

         foreach($fields as $field)
         {
             // SQL metadata or TEST for SQLITE
             if($schema[$field]['metatype'] == STRING || $schema[$field]['type'] == 'TEXT')
             {
                 return $field;
             }
         }

         return NULL;     
    }
    
    //--------------------------------------------------------

    /**
    * Finds a modelname from a given table
    *
    * @param    string    $table
    */

    public function get_model_from_table($table)
    {
        // If there is a model with same name as table...cool and fast
        $model = ucfirst($table);

        // If model exists.....
        if(Pi_loader::model_exists($model))
        {
            // Making sure the table is actually that
            $config = $this->config->get($model);
            
            if($config->table == $table)
                return $model;
        }
        
        // We have to check one by one 
        $list = FileSystem::find_files(MODEL, '/\.php$/');

        // For each one of them, the configuration will be loaded
        foreach($list as $path)
        {
            $model = preg_replace('/(.*\/|\.php$)/', '', $path);
            $config = $this->config->get($model);
            if($config->table == $table)
                return $model;

        }

        // No model has this table
        trigger_error("There is no model in the application representing table '$table'", E_USER_ERROR);
    }

    //--------------------------------------------------------

    /**
    * Finds table name for given model
    *
    * @param    string    $model
    */

    public function get_table_from_model($model)
    {
        $config = $this->config->get($model);
        return $config->table;
    }

    //---------------------------------------------------------

    /**
    * Returns all existings foreign keys of a given model; the field
    * must follow the naming convention and related model must exist.
    *
    * @param    string    $model
    * @return   array
    */

    public function get_foreign_fields($model)
    {
        // Fields
        $fields = array();             
        
        // Check if model exists
        if(!Pi_loader::model_exists($model)) return($fields); 
        
        // We get the field list
        $schema = $this->read_columns($model);
        if(is_null($schema)) return($fields);

        // Check if any follows the foreign key pattern and check if model exists
        foreach($schema as $field)
        {
            // Follows the foreign key pattern?
            if(preg_match(FK_REGEX, $field))
            {
                $modelname = ucfirst(preg_replace(FK_REGEX, '', $field));
                
                // Existant models are appended
                if(Pi_loader::model_exists($modelname))
                    $fields[] = $field;
             }
         }
         
         return $fields;
    }

    //---------------------------------------------------------

    /**
    * Provides the list of forbidden manual fields for requested model
    *
    * @param    string    $model
    * @return   array
    */

    public function get_forbidden_fields($model)
    {
        $forbidden = array();
        $config = $this->config->get($model);

        // If autoincrement is enabled, primary key will be forbidden
        if($config->autoincrement == true)
        {
            $forbidden[] = PRIMARY_KEY;
        }

        // If population is enabled, date_created and date_modified will be ignored
        if($config->population == true)
        {
            $forbidden[] = DATE_CREATED_POPULATED;
            $forbidden[] = DATE_MODIFIED_POPULATED;
            $forbidden[] = DATETIME_CREATED_POPULATED;
            $forbidden[] = DATETIME_MODIFIED_POPULATED;
        }
                    
        // Add manually forbidden fields
        if(isset($config->integrity->forbidden) && is_array($config->integrity->forbidden))
        {
            $forbidden = array_keys(array_flip(array_merge($forbidden, $config->integrity->forbidden)));
        }
        
        return $forbidden;
    }
        
    //--------------------------------------------------------
    
    /**
    * Provides the list of ignored manual fields for requested model
    *
    * @param    string    $model
    * @return   array
    */

    public function get_ignored_fields($model)
    {
        $ignored = array();
        $config = $this->config->get($model);

        // Add manually ignored fielts
        if(isset($config->integrity->ignored) && is_array($config->integrity->ignored))
        {
            $ignored = array_keys(array_flip(array_merge($ignored, $config->integrity->ignored)));
        }
        
        return $ignored;
    }
    

    //--------------------------------------------------------

    /**
    * Provides the list of optional construction fields for requested model
    *
    * @param    string    $model
    * @return   array
    */

    public function get_optional_fields($model)
    {
        $optional = array();
        $config = $this->config->get($model);
        $forbidden = $this->get_forbidden_fields($model);
        $schema = $this->read_schema($model);
        
        // We will add to the optional array all those that are NULL, have default value and are not forbidden
        foreach($schema as $field => $info)
            if(($info['null'] == true || $info['has_default'] == true) && !in_array($field, $forbidden))
                $optional[] = $field;
         
        // If ignoredFields is set then we will add them here
        if(isset($config->integrity->ignored) && is_array($config->integrity->ignored))
            $optional = array_keys(array_flip(array_merge($optional, $config->integrity->ignored)));
        
        return $optional;
    }

    //--------------------------------------------------------

    /**
    * Provides the list of required fields for the construction of a given model
    *
    * @param    string    $model
    * @return   array
    */

    public function get_required_fields($model)
    {
        $all = $this->read_columns($model);
        $optional = $this->get_optional_fields($model);
        $forbidden = $this->get_forbidden_fields($model);
        
        $required = array_diff($all, $optional);
        $required = array_diff($required, $forbidden);
        $required = array_keys(array_flip($required));
        
        // If ignoredFields is set then we remove them from the required array
        if(isset($config->integrity->ignored) && is_array($config->integrity->ignored))
            $required = array_keys(array_flip(array_diff($required, $config->integrity->ignored)));
            
        return $required;
    }

    //---------------------------------------------------------

    /**
    * Provides the list of possible values for a MySql Enum|Set field
    *
    * @param    string    $model
    * @param    string    $field
    * @return   array
    */

    public function get_enum_values($model, $field)
    {
        // If empty
        if(empty($field)) return NULL;

        // If not in our schema
        if(!in_array($field, $this->read_columns($model))) return NULL;
        
        // Get db schema
        $schema = $this->read_schema($model);
        
        // Check if metatype is E(enum)
        if($schema[$field]['metatype'] != ENUM)
            return NULL;
        
        return $schema[$field]['enums'];
    }

    //--------------------------------------------------------

    /**
    * Returns foreign key for given model and related model by checking
    * over manually declared relationships first, and over convention relationships
    * after. Note each model can declare manual relationships, so the function needs
    * two parameters.
    *
    * @param    string    $model
    * @param    string    $relatedModel
    * @return   string
    */

    public function get_relationship_fk($model, $relatedModel)
    {
        // Obtain config for host model
        $config = $this->config->get($model);

        // Search over manually declared relationships
        foreach(Pi_model::$possible_relationships as $relationship)
        {
            if(is_array($config->relationships->$relationship))
            {
                foreach($config->relationships->$relationship as $local)
                {
                    if($local['class_name'] == $relatedModel)
                        return $local['foreign_key'];
                }
            }
        }
    
        // Relationship not manually declared, convention is returned
        return $this->get_convention_fk_field($relatedModel);
    }

    //--------------------------------------------------------

    /**
    * Retrieves the convention foreign key field name for given model. Please,
    * note that convention Fk are built with the table name, not the model name, and models
    * can have a different table name.
    *
    * @param    string    $model
    * @return   string
    */

    private final function get_convention_fk_field($model)
    {
        $table = $this->get_table_from_model($model);
        return $table . '_id';
    }

    //--------------------------------------------------------

    /**
    * Returns the according model to a given foreignkey by checking over
    * manual relationships and convention relationships.
    *
    * @param    string    $model
    * @param    string    $relatedModel
    * @return   string
    */

    public function get_relationship_name_from_fk($model, $fk, $scaffold = false)
    {
        // Config for host model
        $config = $this->config->get($model);
        
        // Search over manually declared relationships
        foreach(Pi_model::$possible_relationships as $relationship)
        {
            if($scaffold && $relationship != 'belongs_to') continue;
            if(is_array($config->relationships->$relationship))
            {
                foreach($config->relationships->$relationship as $local)
                {
                    if($local['foreign_key'] == $fk)
                        return $local['class_name'];
                }
            }
        }
        
        // Convention is returned
        return $this->get_convention_model_from_fk($fk);
    }

    //--------------------------------------------------------

    /**
    * Retrives the model related by a given foreign key; please note
    * convention fk are built from table name, not model name.
    *
    * @param    string    $fk
    * @return   string
    */

    public function get_convention_model_from_fk($fk)
    {
        $table = preg_replace(FK_REGEX, '', $fk);
        return $this->get_model_from_table($table);
    }
    
    //--------------------------------------------------------

    /**
    * Returns a list of direct related models for given model.
    *
    * @param    string    $model
    * @return   array
    */

    public function get_related_models($model)
    {
        // Config for model
        $config = $this->config->get($model);

        // Results stored here
        $models = array();
        
        // Everything can be returnd except has and belongs to many
        $possible_relationships = array("has_one", "has_many", "belongs_to");

        // Search over declared relationships
        foreach($possible_relationships as $relationship)
        {
            if(is_array($config->relationships->$relationship))
            {
                foreach($config->relationships->$relationship as $local)
                {
                    if(Pi_loader::model_exists($local['class_name']))
                    {
                        // Check if this model and related model share connection
                        if($this->have_same_connection($model, $local['class_name']))
                            $models[] = $local['class_name'];
                        else
                            trigger_error("Different connections. ". $model ." cannot be related to ". $local['class_name'], E_USER_ERROR);
                    }
                }
            }
        }
        
        // Over convention models
        $fks = $this->get_foreign_fields($model);
        
        foreach($fks as $field)
        {
            $related = $this->get_convention_model_from_fk($field);
            
            if(!in_array($related, $models) && $this->have_same_connection($model, $related))
            {
                $models[] = $related;
            }
        }
        
        return $models;
    }

    //--------------------------------------------------------

    /**
    * Retrieves a list of has-many related models. It's not
    * performed in get_related_models() to avoid overprocessing,
    * because this function has to iterate all existing models.
    *
    * @param    string    $model
    * @return   array
    */

    public function get_hasmany_related_models($model)
    {
        // Model must exist
        if(!Pi_loader::model_exists($model))
            trigger_error('Model '. $model .' does not exist', E_USER_ERROR);
            
        // To deliver
        $related = array();

        // Ok read all models
        $list = FileSystem::find_files(MODEL, "/\.php$/");
        
        // Iterate them 
        foreach($list as $path)
        {
            // Model name
            $model_name = ucfirst(preg_replace('/(.*\/|\.php$)/', '', $path));
            
            // Read belongs to for this model
            $fks = $this->get_foreign_fields($model_name);
            $belongs_to = array();
            foreach($fks as $field)
                $belongs_to[] = $this->get_convention_model_from_fk($field);
            
            // Find out
            if(in_array($model, $belongs_to))
            {
                $related[] = strtolower($model_name);
            }
        }
        return $related;
    }
    
   
    //--------------------------------------------------------
    
    /**
    * Creates a bidimensional array with all entries for each n-m related model for provided model
    *
    * @param    string    $model
    * @param    int       $value
    */

    public function get_n_m_list($model, $value = NULL, $ignore_not_matching = false)
    {
        // List
        $n_m = $this->get_n_m_related_models($model);
        
        // Result
        $res = array();

        // Grab a list for all of them ordered by the first string field
        foreach($n_m as $relationship => $content)
        {
            // If same model there is no relationship
            if(strtolower($relationship) == strtolower($model)) continue;

            // Foreach related model
            foreach($content as $related_model)
            {
                // Obtains all entities that can be associated to
                $field = $this->get_first_string_field($related_model);
                $instruction = 'getAll'. $related_model .'OrderBy'. $field;
        
                // All elements to associate
                $elements = $this->db->query->arrays->{$instruction}();
                
                // Obtains all associations in order to mark those that already exists
                $my_fk = $this->get_relationship_fk($relationship, $model);
                $other_fk = $this->get_relationship_fk($relationship, $related_model);
                
                // Query if value is not null
                if($value != NULL)
                    $assocs = $this->db->query->getWhere($relationship, $my_fk .'='. $value);
                
                // Foreach element construct a nice array
                if($elements != NULL)
                {
                    foreach($elements as $item)
                    {
                        // Suposse not selected
                        $selected = false;

                        // Check for selected
                        if(isset($assocs))
                        {
                            foreach($assocs as $a)
                            {
                                if($a->fields->{$other_fk} == $item['id'])
                                {
                                    $selected = true;
                                    break;
                                }
                            }
                        }

                        // Build array item
                        if(!$ignore_not_matching || $selected)
                        {
                            $res[$relationship][$related_model][] = array(

                                'id'        => $item['id'],
                                'value'     => $item[$field],
                                'selected'  => $selected
                            );
                        }
                    }
                }
            }
        }
        
        return $res;
    }

    //--------------------------------------------------------

    /**
    * Retrieves a list of n-m related models.
    *
    * @param    string    $model
    */

    public function get_n_m_related_models($model)
    {
        if(!Pi_loader::model_exists($model))
            trigger_error('Model '. $model .' does not exist', E_USER_ERROR);
        
        $related = array();
        
        $all_models = FileSystem::find_files(MODEL, "/\.php$/");
        // Models name
        foreach($all_models as $element)
            $models[] = preg_replace('/(.*\/|\.php)/', '', $element);
        
        // N-M matches array
        $matching = array();

        // Check which model names matches a rel_word_word pattern and have our model name on it
        foreach($models as $element)
        {
            if(preg_match("/^Rel_[^_]+_[^_]+$/i", $element) && is_numeric(stripos($element, $model)))
                $matching[] = $element;
        }
        
        // Got all matching models, now we must check the other model exists as well
        foreach($matching as $element)
        {
            // Received element must only have 3 fields
            $schema = $this->read_schema($element);
        
            // Must have 3 fields to be a valid n_m related model. One PK and two FKS
            if(count($schema) == 3)
            {
                // Two fields must accomplish the FK_REGEX
                $cont = 0;
                foreach($schema as $field => $content)
                {
                    if(preg_match(FK_REGEX, $field))
                        $cont++;
                }

                // If two fks
                if($cont == 2)
                {
                    // Incanse sensitive regex
                    $regex = '/(^rel_'. $model . '_|_' . $model . '$)/i';
                    
                    // Capture related model name
                    $captured_model = strtolower(preg_replace($regex, '', $element));
                    
                    // Model must be in the all models list
                    if(in_array($captured_model, $models))
                        $related[$element][] = $captured_model;
                }
            }
        }
        return $related;
    }

    //---------------------------------------------------------    
    
    /**
    * Checks if given model is configured to be searchable
    *
    * @param    string    $model
    */

    public function is_searchable($model)
    {
        // Read config
        $config = $this->config->get($model);

        // Make sure arrays exists
        if(is_array($config->search->fields->strict) && count($config->search->fields->strict) > 0)
            return(true);

        // At least one search array must be declared
        if(is_array($config->search->fields->non_strict) && count($config->search->fields->non_strict) > 0)
            return true;
        
        return false;
    }

    //---------------------------------------------------------

    /**
    * Creates the database singleton object.
    * Forced here because Db constructor calls query, and query calls us,
    * so instancing Db in constructor will cause segmentation fault
    */

    private function _create_db()
    {
        if(!is_object($this->db))
            $this->db = Pi_db::singleton();
    }
}
?>
