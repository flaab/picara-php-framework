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
* An efficient implementation of the iterator pattern to model query results.
* Usage is encouraged in order to grant a efficient memory usage when performing queries.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class Pi_modelcollection extends Pi_error_store implements Iterator
{
    /**
    * AdoDb recordset
    */
    private $resource = array();
    
    /**
    * Model to be created
    */
    private $model;
    
    /**
    * Lenght of recordset
    */
    private $length;
    
    /**
    * Fields treated
    */
    private $fieldCount;
    
    //----------------------------------------------------------
    
    /**
    * Creates the collection object from a resultset and a modename
    *
    * @param    AdoRecordSet_*    $resource
    */
    
    public function __construct($resource, $model)
    {
        // Check model exists
        if(!Pi_loader::model_exists($model))
            trigger_error("ModelCollection cannot be constructed; given model does not exist", E_USER_ERROR);
            
        $class = get_class($resource);
        
        if(preg_match("/^ADORecordSet_.+/i", $class))
        {
            $this->model = $model;
            $this->resource = $resource;
            $this->length = $resource->RecordCount();
            $this->fieldCount = $resource->FieldCount();
            return;
            
        } else {
        
            trigger_error("ModelCollection cannot be constructed; first parameter is not a valid result resource", E_USER_ERROR);
        }
    }

    //=============================================
    // Iterator implementation
    //=============================================

    /**
    * Rewinds to the first position
    */
    
    public function rewind()
    {
        $this->resource->moveFirst();
    }
    
    //----------------------------------------------------------

    /**
    * Returns current object
    *
    * @return    object|boolean
    */
    
    public function current()
    {    
        if($this->resource->fields != false)
            return new $this->model($this->resource->fields, true);

        return false;
    }
    
    //----------------------------------------------------------

    /**
    * Returns current key
    *
    * @return    int
    */
    
    public function key()
    {
        return $this->resource->_currentRow;
    }
    
    //----------------------------------------------------------

    /**
    * Moves to next result
    *
    * @return    bool
    */
    
    public function next()
    {
        return $this->resource->moveNext();
    }
    
    //----------------------------------------------------------

    /**
    * Validates current element
    *
    * @return    bool
    */
    
    public function valid()
    {
        return !$this->resource->EOF;
    }
    
    //=============================================
    // Custom collection logic implementation
    //=============================================
    
    /**
    * Returns the length of collection
    *
    * @return    int
    */
    
    public function length()
    {
        return $this->length;
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns the field count of the collection
    *
    * @return    int
    */
    
    public function fieldCount()
    {
        return $this->fieldCount;
    }
    
    //--------------------------------------------------------
    
    /**
    * Returns a collection segment as an array of objects
    *
    * @param    int    $offset
    * @param    int    $rowcount
    * @return   array
    */

    public function segment($start, $end)
    {
        // Empty array if null
        if(is_null($start)) return $array;
        
        // Results
        $res = array();
        
        // If no results
        if($this->length() == 0) return $res;

        // Move the cursor to start position     
        if(!$this->resource->move($start))
            trigger_error("Unexpected error moving cursor of recordset, probably an invalid value has been provided.", E_USER_ERROR);

        // Iteration fetching objects
        for($it=0; $it < $end; $it++)
        {
            $res[] = $this->current();
            if(!$this->next()) break;
        }
        
        return $res;
    }
    
    //=============================================
    // Meta update
    // Used to update a field or a set of fields
    // on a collection of records.
    //=============================================

    /**
    * Updates a set of fields to a certain value on all the collection
    *
    * @param    array    $pairs    Hash with the key value pairs
    * @return   int                Affected rows
    */

    public function meta_update($pairs)
    {
        // Must be array
        if(!is_array($pairs) || count($pairs) == 0)
            trigger_error('A non-empty array is expected to perform a meta-update', E_USER_ERROR);

        // Nothing if no elements
        if($this->length() < 1) return 0;

        // Ids
        $ids = $this->toIds();

        // We must grab the table for this model
        $metadata = Pi_metadata::singleton();
        $table = $metadata->get_table_from_model($this->model);

        $conditions = array();

        // Create the pairs stuff
        foreach($pairs as $key => $value)
            $conditions[] = $key ."='". $value ."'";    
        
        // Create sql
        $sql = "UPDATE ". $table ." SET ". implode(',', $conditions) ." WHERE id IN(". implode(',', $ids) .")";
  
        // Db singleton
        $db = Pi_db::singleton();

        // Read model connection
        $conn = $metadata->config->read($this->model, 'connection');
        
        // Normal query
        try { $db->link->{$conn}->Execute($sql); } 
        
        catch(exception $e)
        { 
            trigger_error(Pi_db::getExceptionString($e), E_USER_ERROR); 
        }

        return $db->link->{$conn}->Affected_Rows();
    }
   
    //=============================================
    // Export to other formats
    //=============================================
    
    /**
    * Returns the whole collection as Xml
    *
    * @return    string
    */
    
    public function toXml()
    {
        $this->rewind();
        $xml = "<Collection model=\"$this->model\">\n";
        
        do {
            
            $obj = $this->current();
            $xml .= $obj->toXml();
        
        } while ($this->next());
        
        $xml .= "</Collection>";
        
        return $xml;
    }
    
    //----------------------------------------------------------    
    
    /**
    * Returns collection as Csv
    *
    * @return    string
    */
    
    public function toCsv()
    {
        $this->rewind();
        $csv = '';
        
        do {
            
            $obj = $this->current();
            $csv .= $obj->toCsv();
        
        } while ($this->next());
        
        return $csv;
    }

    //----------------------------------------------------------

    /**
    * Returns the collection as Yaml
    *
    * @return   string
    */

    public function toYaml()
    {
        $this->rewind();
        $elements = array();
        $yml = "---\n";
        
        do {
            
            $obj = $this->current();
            $yml .= str_replace("---\n", "-\n", $obj->toYaml());

        
        } while ($this->next());
        
        return $yml;
    }
    
    //----------------------------------------------------------
    
    /**
    * Returns the collection as Json
    *
    * @return   string
    */

    public function toJson()
    {
        $this->rewind();
        $elements = array();
        $json = "[";
        $start = true; 

        do {
            
            $obj   = $this->current();
            if(!$start) $json .= ","; 
            $json .= $obj->toJson();
            $start = false;
        
        } while ($this->next());

        $json .= "]";
        return $json;
    }

    //----------------------------------------------------------

    /**
    * Returns the collection as a bidimensional array
    *
    * @return   array
    */

    public function toArray()
    {
        $this->rewind();
        $res = array();

        if($this->length() == 0) return $res; 

        do {
            
            $obj = $this->current();
            $res[] = $obj->toArray();
        
        } while ($this->next());
        
        return $res;
    }
    
    //----------------------------------------------------------

    /**
    * Returns just affected ids
    */

    public function toIds()
    {
        $res = array();

        do {

            $obj = $this->current();
            $res[] = $obj->fields->id;

        } while($this->next());

        return $res;
    }

    //----------------------------------------------------------    
    
    /**
    * Deletes all rows returned by collection
    */
    
    public function delete_all()
    {
        $this->rewind();
        
        do {
            
            $obj = $this->current();

            if($obj != false)
                $obj->delete();
        
        } while ($this->next());
    }
}
?>
