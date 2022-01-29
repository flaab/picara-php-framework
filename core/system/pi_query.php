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
* Provides a fast and easy way to perform database queries
* 
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
* @example    query/query.php
*/
 
class Pi_query extends Pi_overloadable
{
    /**
    * Instance for singleton pattern
    */
    private static $instance;

    /**
    * Model to search over
    */
    private $model;        
         
    /**
    * Table to use
    */    
    private $table;    
    
    /**
    * Fields to query
    */
    private $fields;
     
    /**
    * Query order
    */
    private $orderBy;
         
    /**
    * Result shape 
    */
    private $shape;

    /**
    * Orientation (DESC or ASC)
    */
    private $orientation;

    /**
    * Configuration instance
    */
    private $configuration;
          
    /**
    * Database holder instance
    */
    private $db;

    /**
    * Metadata object
    */
    private $metadata;

    /**
    * Connection name to use in the query
    */
    private $connection;

    /**
    * Possible shapes
    */
    private $possible_shapes = array(

        'collection'  => COLLECTION,
        'arrays'      => ARRAYS,
        'csv'         => CSV,
        'xml'         => XML,
        'ids'         => IDS,
        'cardinality' => CARDINALITY,
        'objects'     => OBJECTS,
        'yml'         => YML,
        'json'        => JSON

    );

    /**
    * Selected fields for each specific data shapes
    */
    private $selected_fields = array(

        IDS         => PRIMARY_KEY,
        CARDINALITY => 'COUNT(*)'

    );

    //---------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @param     Pi_db       $db
    * @return    Pi_query
    */
    
    public static function singleton(Pi_db $db = NULL) 
    {
        if (!isset(self::$instance)) 
        {
            if($db == NULL)
                trigger_error('I cannot create a query instance without a Pi_db object', E_USER_ERROR);

            $c = __CLASS__;
            self::$instance = new $c($db);
        }
        
        self::$instance->_create_bridges();
        return self::$instance;
    }

    //---------------------------------------------------------

    /**
    * Assigns the connection's holder by reference
    *
    * @param    Db    $db
    */

    private function __construct(Pi_db $db)
    {
        $this->db = $db;
        $this->metadata = Pi_metadata::singleton();
    }
    
    //----------------------------------------------------------
     
    /**
    * Sets up the query
    *
    * @param    string    $model
    * @param    string    $orderBy
    * @param    string    $orientation
    */

    private function setUp($model, $orderBy = NULL, $orientation = NULL)
    {
         // Model to query
         $this->model = $model;
         
         // Checks model existance
         if(!Pi_loader::model_exists($this->model))
             trigger_error("The model '$this->model' does not exist in the application", E_USER_ERROR);

         // Table for this model
         $this->table = $this->metadata->config->read($this->model, 'table');

         // Connection for this model
         $this->connection = $this->metadata->config->read($this->model, 'connection');

         // Results order
         if($orderBy == NULL) { $this->orderBy = PRIMARY_KEY; } else { $this->orderBy = $orderBy; }

         // Orientation (Asc or Desc)
         $this->orientation = $orientation;
    
         // Set selected fields
         $this->_set_selected_fields();
    }

    //==================================================
    // Low-level query functions
    //==================================================             
 
    /**
    * Queries the given last entries of a model
    *
    * @param   int       $number    Desired number of results
    * @param   string    $model     Model to query
    * @param   string    $orderBy   Used field to order results
    * @return  mixed
    */
      
    public function getLast($number, $model, $orderBy = NULL)
    {
        // Set up the search
        $this->setUp($model, $orderBy, 'DESC');
         
        // Query to execute
        $query = "SELECT $this->fields FROM $this->table ORDER BY $this->orderBy $this->orientation";
         
        // Execution and results
        $resource = $this->free($query, $this->connection, $number);
        return $this->getResults($resource);
    }
     
    //----------------------------------------------------------
     
    /**
    * Queries the given first entries of a model
    *
    * @param    int       $number    Desired number of results
    * @param    string    $model     Model to query
    * @param    string    $orderBy   Used field to order results
    * @return   mixed
    */
      
    public function getFirst($number, $model, $orderBy = NULL)
    {
        // Set up the search
        $this->setUp($model, $orderBy, 'ASC');
         
        // Query to execute
        $query = "SELECT $this->fields FROM $this->table ORDER BY $this->orderBy $this->orientation";
         
         // Execution
         $resource = $this->free($query, $this->connection, $number);
         return $this->getResults($resource);
    }

    //----------------------------------------------------------
 
    /**
    * Performs the query to retrieve any model using the given where clause
    *
    * @param    string    $model
    * @param    string    $conditions
    * @param    int       $offset
    * @param    int       $rowcount
    * @return   mixed
    */
     
    public function getWhere($model, $conditions, $offset = NULL, $rowcount = NULL)
    {
        // Set up the search
        $this->setUp($model);
         
        // Query to execute
        $query = "SELECT $this->fields FROM $this->table WHERE $conditions";
        
        // Execution
        $resource = $this->free($query, $this->connection, $offset, $rowcount);
        return $this->getResults($resource);
    }

    //--------------------------------------------------------

    /**
    * Performs a query over a model searching an specific value on every field.
    * If a non-strict search rule is declared over a field, the LIKE operator will be used.
    *
    * @param    string    $model
    * @param    array     $pairs
    * @param    int       $offset
    * @param    int       $rowcount
    * @return   mixed
    */

    public function seek($model, $pairs, $orderBy = NULL, $offset = NULL, $rowcount = NULL, $orientation = NULL)
    {
        // Pairs should not be empty
        if(count($pairs) < 1)
            trigger_error('A non-empty array was expected at seek() function', E_USER_ERROR);
        
        // Set up the search
        $this->setUp($model, $orderBy, $orientation);
   
        // Conditions array
        $cond_array = array();

        // Iteration
        foreach($pairs as $key => $value)
           $cond_array[] = $key ." = '". $value ."'"; 

        // Conditions string
        $cond_string = implode(' AND ', $cond_array);

        // Query
        $query = "SELECT $this->fields FROM $this->table WHERE $cond_string ORDER BY $this->orderBy $this->orientation";
        
        // Execution
        $resource = $this->free($query, $this->connection, $offset, $rowcount);
        return $this->getResults($resource);
    }

    //--------------------------------------------------------

    /**
    * Performs a search engine query for given model. This is a private function
    * similar to getWhere, but including an 'order by` clause we cannot add to
    * getWhere()
    *
    * @param    string    $model
    * @param    string    $conditions
    * @param    string    $orderBy
    * @param    int       $offset
    * @param    int       $rowcount
    * @param    string    $orientation
    */

    public function performSearchQuery($model, $conditions, $orderBy = NULL, $offset = NULL, $rowcount = NULL, $orientation = NULL)
    {
        // Set up
        $this->setUp($model, $orderBy, $orientation);
    
        // Query
        $query = "SELECT $this->fields FROM $this->table WHERE $conditions ORDER BY $this->orderBy $this->orientation";

        // Execution
        $resource = $this->free($query, $this->connection, $offset, $rowcount);
        return $this->getResults($resource);
    }

    //--------------------------------------------------------

    /**
    * Performs the configured search for given model
    *
    * @param    string    $model
    * @param    int       $offset
    * @param    int       $rowcount
    */

    public function performSearch($model, $search, $orderBy = NULL, $offset = NULL, $rowcount = NULL, $orientation = NULL, $count = false)
    {   
        // If just numrows required
        if($count == true)
            $this->setShape(CARDINALITY);

        return $this->performSearchQuery($model, $this->_get_where_clause($model, $search), $orderBy, $offset, $rowcount, $orientation); 
    }
     
     //----------------------------------------------------------

     /**
     * Grabs all entries of the desired model
     * 
     * @param    string    $model          Model name to query
     * @param    bool      $justids        Only matching primary keys will be returned
     * @param    bool      $numrows        Only affected rows will be returned
     * @param    int       $offset         Offset for the database query
     * @param    int       $rowcount       Rowcount for the database query
     * @param    string    $orderBy        Used field to order results
     * @param    string    $orientation    Asc or Desc
     * @return   mixed
     */
      
     public function getAll($model, $orderBy = NULL, $offset = NULL, $rowcount = NULL, $justids = false, $numrows = false, $orientation = 'ASC')
     {
         // Set up the search
         $this->setUp($model, $orderBy, $orientation);
         
         // If just num rows asked
         if($numrows == true)
             $this->setShape(CARDINALITY);
             
         // If just ids asked
         if($justids == true)
             $this->setShape(IDS);

         // Query to execute
         $query = "SELECT $this->fields FROM $this->table ORDER BY $this->orderBy $this->orientation";

         // Execution
         $resource = $this->free($query, $this->connection, $offset, $rowcount);
         return $this->getResults($resource);
     }
     
     //----------------------------------------------------------
     
     /**
     * Performs an strict or non strict search over any model, using the provided field and value
     *
     * @param    string     $field          Field to compare
     * @param    string     $value          Value to search
     * @param    stroing    $model          Model name to query
     * @param    bool       $useLike        Will cause the use of the LIKE operator for a non-scrict search
     * @param    bool       $justids        Only matching primary keys will be returned
     * @param    int        $offset         Offset for the database query
     * @param    int        $rowcount       Rowcount for the database query
     * @param    string     $orderBy        Used field to order results
     * @param    string     $orientation    Asc or Desc
     * @return   mixed
     */
     
     public function searchBy($field, $value, $model, $useLike = false, $justids = false, $offset = NULL, $rowcount = NULL, $orderBy = NULL, $orientation = 'ASC')
     {         
         // Set up the search
         $this->setUp($model, $orderBy, $this->orientation);
         
         // Depends on useLike or not
         if($useLike == false) { $param = '='; } else { $param = 'LIKE'; $value = "%$value%";}
                  
         // If just ids asked
         if($justids == true)
             $this->_setShape(IDS);;
         
         // Query to execute
         $query = "SELECT $this->fields FROM $this->table WHERE $field $param '$value' ORDER BY $this->orderBy $this->orientation";
   
         // Execution
         $resource= $this->free($query, $this->connection, $offset, $rowcount);
         return $this->getResults($resource);
     }
     
    //----------------------------------------------------------
     
    /**
    * Returns affected rows by a search. Used only by paginator
    *
    * @param    string     $field Field to compare
    * @param    string     $value Value to search
    * @param    stroing    $model Model name to query
    * @param    bool       $useLike Will cause the use of the LIKE operator for a non-scrict search
    * @param    int        $offset Offset for the database query
    * @param    int        $rowcount Rowcount for the database query
    * @param    string     $orderBy Used field to order results
    * @return   mixed
    */
     
    public function getNumRowsOfSearch($field, $value, $model, $useLike = false, $offset = NULL, $rowcount = NULL, $orderBy = NULL)
    {
        // Cardinality shape forced
        $this->setShape(CARDINALITY);

        // Set up the search
        $this->setUp($model, $orderBy);
         
        // Depends on useLike or not
        if($useLike == false) { $param = '='; } else { $param = 'LIKE'; $value = "%$value%";}
     
        // Query to execute
        $query = "SELECT $this->fields FROM $this->table WHERE $field $param '$value' ORDER BY $this->orderBy ASC";
         
        // Execution
        $resource = $this->free($query, $this->connection, $offset, $rowcount);
        return $this->getResults($resource);
    }  
      
    //----------------------------------------------------------
      
    /**
    * Executes the given query and returns a rough recordset
    *
    * @param     string    $query
    * @param     string    $connection
    * @param     string    $rowcount
    * @param     string    $offset
    * @return    mixed
    */

    public function free($query, $connection = NULL, $rowcount = NULL, $offset = NULL)
    {
        // Connection is entablished
        $this->db->connect($this->connection);

        // Performing a rowcount in postgres using an order|orientation clause fails
        if($this->db->link->{$this->connection}->dataProvider == 'postgres' && $this->shape == CARDINALITY)
            $query = preg_replace("/(ORDER\sBY\s[^\s]+|\sASC|\sDESC)/i",'', $query);    
            
        // Sets fetch mode according data shape to avoid assoc names problems when querying rowcounts
        if($this->shape == CARDINALITY)
            $this->db->link->{$this->connection}->fetchMode = ADODB_FETCH_NUM;
        else
            $this->db->link->{$this->connection}->fetchMode = ADODB_FETCH_ASSOC;

        //==============================================
        // Rowcount and offset provided
        //==============================================
         
        if(is_numeric($rowcount) && is_numeric($offset))
        {
            try { $result = $this->db->link->{$connection}->SelectLimit($query, $offset, $rowcount); } 
             
            catch(exception $e)
            { 
                trigger_error(Pi_db::getExceptionString($e), E_USER_ERROR); 
            }
             
        } else
         
        //==============================================
        // Just rowcount provided
        //==============================================
        
        if(is_numeric($rowcount))
        {
            try { $result = $this->db->link->{$connection}->SelectLimit($query, $rowcount); } 
             
            catch(exception $e)
            { 
                trigger_error(Pi_db::getExceptionString($e), E_USER_ERROR); 
            }

        //==============================================
        // No limited results query
        //==============================================
   
        } else {
         
            // Normal query
            try { $result = $this->db->link->{$connection}->Execute($query); } 
             
            catch(exception $e)
            { 
                trigger_error(Pi_db::getExceptionString($e), E_USER_ERROR); 
            }
        }
         
        // If result failed
        if($result != true)
            trigger_error($this->db->link->{$connection}->ErrorMsg(), E_USER_ERROR);
        
        return $result;    
    }
     
    //----------------------------------------------------------
     
    /**
    * Calculates the results and returns them according desired data shape
    *
    * @param     AdoDb_result_resource    $resource
    * @return    mixed
    */
    
    private function getResults($resource)
    {
        switch($this->shape)
        {
            case CARDINALITY:
             
                //==================================================
                // Just num rows are returned
                //==================================================
                
                $row = $resource->FetchRow();
                $results = (int) $row[0];
             
            break;
                 
            case IDS: 

                //==================================================
                // Just primary keys are returned
                //==================================================
    
                foreach($resource as $key => $row)
                {
                    $results[] = (int) $row['id'];
                }
                
            break;
             
            case ARRAYS:

                //==================================================
                // A bidimensional array is returned
                //==================================================         

                foreach($resource as $key => $row)
                {
                     $results[] = $row;
                }
             
            break;
             
            case OBJECTS: 
             
                //==================================================
                // An array of objects is returned
                //==================================================    

                foreach($resource as $key => $row)
                {
                    $results[] = new $this->model($row, true);
                }                     
                 
            break;

            case XML:

                //==================================================
                // Results are returned as xml
                //==================================================            

                $collection =  new Pi_modelcollection($resource, $this->model);
                $results = $collection->toXml();

            break;

            case CSV:

                //==================================================
                // Results are returned as csv
                //==================================================            
                $collection =  new Pi_modelcollection($resource, $this->model);
                $results = $collection->toCsv();

            break;
        
            case YML:

                //==================================================
                // Results are returned as yaml
                //==================================================            
                $collection =  new Pi_modelcollection($resource, $this->model);
                $results = $collection->toYaml();

            break;
            
            case JSON:

                //==================================================
                // Results are returned as yaml
                //==================================================            
                $collection =  new Pi_modelcollection($resource, $this->model);
                $results = $collection->toJson();

            break;
             
            default:
             
                //==================================================
                // A model collection is returned
                //==================================================             
                 
                 $results = new Pi_modelcollection($resource, $this->model);

            break;
        }
         
        // Shape reset
        $this->shape = NULL;
         
        return $results;
    }

    //==================================================
    // Result shapes related functions
    //==================================================  
     
    /**
    * Sets the result shape from outside. used by models to retrieve results
    *
    * @param    int    shape
    */
    
    public function setShape($shape)
    {
         $this->shape = $shape;
         $this->_set_selected_fields();
    }

    //--------------------------------------------------------

    /**
    * Sets field token to retrieve in the query according data shape
    */

    private function _set_selected_fields()
    {
         // Specific or general fields for this result shape
         if(in_array($this->shape, array_keys($this->selected_fields)))
         {
             $this->fields = $this->selected_fields[$this->shape];
         } else {
             $this->fields = '*';
         }
    }

    //==================================================
    // Built-in search engine related functions
    //==================================================  

    /**
    * Returns the where clause to search over given model,
    * according his fields configuration and search type.
    *
    * @param    string    $model
    * @param    string    $search
    * @return   string
    */

    private function _get_where_clause($model, $search)
    {
        // If model is not searchable
        if(!$this->metadata->is_searchable($model)) 
            trigger_error("Model '$model' is not searchable. Search fields must be declared first", E_USER_ERROR);

        // Config for model
        $config = $this->metadata->config->get($model);
        
        // Clauses
        $where = $this->_get_complete_where_clause($config->search->fields, $search);

        // Search extension to related models if required
        if(is_array($config->search->related))
        {
            // Foreach related model
            foreach($config->search->related as $related_model)
            {
                // Obtain subquery
                $subquery = $this->_get_related_model_query($model, $related_model, $search);

                // Obtain local foreign key to this relationships
                $fk = $this->metadata->get_relationship_fk($model, $related_model);

                // Finish up the query
                $where .= " OR $fk IN ($subquery)";
            }
        }

        return $where;
    }

    //--------------------------------------------------------

    /**
    * Constructs a subquery for the built-in related model search
    *
    * @param    string    $model
    * @param    string    $related_model
    * @param    string    $search
    */

    private function _get_related_model_query($model, $related_model, $search)
    {
        // First, check if related_model is searchable
        if(!$this->metadata->is_searchable($related_model))
            trigger_error("Search over '$model' cannot be extended to '$related_model', because this is not searchable", E_USER_ERROR);

        // Obtains where clause
        $config = $this->metadata->config->get($related_model);

        // Where clause
        $where = $this->_get_complete_where_clause($config->search->fields, $search);

        // Full query
        return "SELECT ". PRIMARY_KEY ." FROM ". $config->table ." WHERE $where";
    }
    
    //--------------------------------------------------------

    /**
    * Constructs the complete where clause for given strict and non-strict fields
    *
    * @param    array    $fields
    * @param    string   $search
    */

    private function _get_complete_where_clause($fields, $search)
    {
        if(is_array($fields->strict))
            $strict = $this->_get_conditions_for_where_clause($fields->strict, $search, '=');

        if(is_array($fields->non_strict))
            $non_strict = $this->_get_conditions_for_where_clause($fields->non_strict, preg_replace("/(^|$|\s+)/", '%', $search), 'LIKE');

        if(strlen($strict) > 0)
        {
            $where = $strict;
            $use_or = true;
        }

        if(strlen($non_strict))
        {
            if($use_or == true)
                $where .= ' OR ';

            $where .= $non_strict;
        }

        return $where;
    }

    //--------------------------------------------------------

    /**
    * Constructs a where clause from received fields and search string
    *
    * @param    array     $fields
    * @param    string    $search
    * @param    string    $operator
    */

    private function _get_conditions_for_where_clause($fields, $search, $operator)
    {
        $items = array();
        foreach($fields as $field)
            $items[] = "$field $operator '$search'";

        return implode(' OR ', $items);
    }

    //--------------------------------------------------------

    /**
    * Creates all bridge query objects
    */

    private function _create_bridges()
    {
        foreach($this->possible_shapes as $shape => $const)
            if($const != DEFAULT_RESULT_SHAPE)
                $this->{$shape} = new Pi_querybridge(self::$instance, $const);
    }


    //==================================================
    // Magic function implementation
    //==================================================             

    /**
    * Implements magic function for queries; it has to be public
    * because class QueryBridge must be able to call it directly.
    *
    * @param    string    $method
    * @param    array     $arguments
    * @param    bool      $manually_shape
    */

    public final function _magic($method, $arguments, $manually_shape = false)
    {
        // If shape is not manually set, shape is sets to default, if cardinality of ids are requested, it will be changed afterwards
        if($manually_shape == false) $this->setShape(DEFAULT_RESULT_SHAPE);
        
        /**
        * GetAll [Model] OrderBy [Field] (Desc|Asc)
        *
        * @param    int     offset
        * @param    int     rowcount
        *
        * Examples:
        * 
        * > getAllCategory();
        * > getAllCategory(5,10);
        * > getAllCategoryOrderByName();
        * > getAllCategoryOrderByDate(10,5);
        * > getAllCategoryOrderByDateAsc();
        * > getAllCategoryDesc();
        */
        if(preg_match("/^getAll(.+)(OrderBy(.+))?(Asc|Desc)?$/U", $method, $matches))
        { 
            return $this->getAll($matches[1], $matches[3], $arguments[0], $arguments[1], false, false, $matches[4]);
        }
        
        //----------------------------------------------------------
        
        /*
        * Get [Model] Where
        *
        * @param    string    $where
        * @param    int       $offset
        * @param    int       $rowcount
        *
        * Examples:
        *
        * > getArticleWhere('author_id = 5');
        * > getArticleWhere('author_id = 5 AND visits < 1200', 5);
        * > getArticleWhere('autor_id = 5', 10, 7);
        */

        if(preg_match("/^get(.+)Where$/", $method, $matches))
        {
            return $this->getWhere($matches[1], $arguments[0], $arguments[1], $arguments[2]);
        }
        
        //----------------------------------------------------------
        
        /**
        * Get [N] (Last|First) [Model] OrderBy [Field]
        *
        * Examples:
        *
        * > getLastArticle();
        * > getFirstCategory();
        * > get5LastArticle();
        * > get5FirstCategory;
        * > get5LastArticleOrderByWriter();
        * > get5FirstArticleOrderByWriter();
        */

        if(preg_match("/^get[0-9]*(Last|First)(.+)(OrderBy(.+))?$/U", $method, $matches))
        {
            // Requested number
            $number = preg_replace("/(^get|(Last|First).+$)/", '', $method);
            
            // If empty, only first has been asked
            if(empty($number)) $number = 1;
            
            // Function to call
            $function = 'get' . $matches[1];
            
            // Results
            return $this->{$function}($number, $matches[2], $matches[4]);
        }
        
        //----------------------------------------------------------
        
        /**
        * Search [Model] By [Field] OrderBy [Field] (Desc|Asc)
        *
        * @param    string    $search
        * @param    int       $offset
        * @param    int       $rowcount
        *
        * Examples:
        *
        * > searchUserByMail('hotmail');
        * > searchUserByMailOrderByDate('hotmail');
        * > searchUserByMailOrderByDate('hotmail', 10);
        * > searchUserByMailOrderByDate('hotmail', 5, 10);
        * > searchUserByMailOrderByDateAsc('hotmail', 5, 10);
        * > searchUserByMailAsc('hotmail', 5, 10);
        */

        if(preg_match("/^search(.+)By(.+)(OrderBy(.+))?(Asc|Desc)?$/U", $method, $matches))
        {
            return $this->searchBy($matches[2], $arguments[0], $matches[1], true, false, $arguments[1], $arguments[2], $matches[4], $matches[5]);
        }

        //----------------------------------------------------------
        
        /**
        * seek [Model] OrderBy [Field] (Desc|Asc)
        *
        * @param    array    $pairs
        * @param    int      $offset
        * @param    int      $rowcount
        *
        * Examples:
        *
        * > seekArticle(...)
        * > seekArticleOrderByDate(...)
        * > seekArticleOrderByDateAsc(...)
        */

        if(preg_match("/^seek(.+)(OrderBy(.+))?(Asc|Desc)?$/U", $method, $matches))
        {
            return $this->seek($matches[1], $arguments[0], $matches[3], $arguments[1], $arguments[2], $matches[4]);
        }

        //--------------------------------------------------------
        
        /**
        * perform [Model] Search OrderBy [Field] (Desc|Asc)
        *
        * @param    string    $search
        * @param    int       $offset
        * @param    int       $rowcount
        *
        * Examples:
        *
        * > performArticleSearch('Lorem ipsum');
        * > performArticleSearch('Lorem ipsum', 5);
        * > performArticleSearch('Lorem ipsum', 10, 20);
        * > performArticleSearchOrderByTitleAsc('Lorem ipsum', 10, 20);
        */

        if(preg_match("/^perform(.+)Search(OrderBy(.+))?(Asc|Desc)?$/U", $method, $matches))
        {
            return $this->performSearch($matches[1], $arguments[0], $matches[3], $arguments[1], $arguments[2], $matches[4]);
        }
        
        //----------------------------------------------------------
        
        /**
        * StrictSearch [Model] By [Field] OrderBy [Field]
        *
        * @param    string    $search    String to search
        * @param    int       $offset
        * @param    int       $rowcount
        *
        * Examples:
        *
        * > strictSearchUserByCountry('Spain');
        * > strictSearchArticleByWriter_id(10);
        * > strictSearchCategoryByDate('2008-10-01', 10, 4);
        */

        if(preg_match("/^strictSearch(.+)By(.+)(OrderBy(.+))?(Asc|Desc)?$/U", $method, $matches))
        {
            return $this->searchBy($matches[2], $arguments[0], $matches[1], false, false, $args[1], $args[2], $matches[4], $matches[5]);
        }
        
        //----------------------------------------------------------

        /*
        * Get [model] Cardinality
        *
        * Examples:
        *
        * > getCategoryCardinality();
        */

        if(preg_match("/^get(.+)Cardinality$/", $method, $matches))
        {
            return $this->getAll($matches[1], NULL, NULL, NULL, false, true);
        }    
         
        $this->method_does_not_exist($method);
    }
}
?>
