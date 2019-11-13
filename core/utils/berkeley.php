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

define('DBA_STRING', 1);
define('DBA_ARRAY', 2);

/**
 * This utility helps the user to easily interact with berkeley data files.
 *
 * @package    Utils 
 * @author     Arturo Lopez
 * @copyright  Copyright (c) 2008-2019, Arturo Lopez
 * @version    0.1
 */
 
class Berkeley extends Pi_error_store implements Iterator
{
    /**
    * Path to data file
    */
    private $path;
    
    /**
    * File handler
    */
    private $handle = false;
   
    /**
    * Access mode
    */
    private $mode = 'c';
    
    /**
    * Fetch mode
	*/
	private $fetch_mode = DBA_ARRAY;
    
    /**
    * Last inserted id
    */
    private $last_id = 0;
    
    /**
    * Iterator id
    */
    private $iterator_id;
    
    /**
    * Data file cardinality
    */
    private $cardinality = 0;

    /**
    * Stores field names for this data file
    */
    private $fields = array();

    /**
    * Separator string
    */
    private $separator;
    
    //--------------------------------------------------------
        
    /**
    * If data file exists, cardinality and last inserted id is stored
    *
    * @param    string    $path    
    */
    
    public function __construct($path, $separator = '|')
    {
        $this->path = $path;
        $this->separator = $separator;
        
        // Checks file existance
        if(file_exists($this->path))
        {       
            // Checks permissions
            if(!is_writable($this->path))
                trigger_error("I do not have write access to $this->path", E_USER_ERROR);
            
            if(!is_readable($this->path))
                trigger_error("I do not have read access to $this->path", E_USER_ERROR);

            // Connects
            $this->connect();
            
            // Grabs last inserted id
            $this->last_id = $this->_dba_lastkey();
        } 
    }
    
    //--------------------------------------------------------
    
    /**
    * Opens the data file and stores file handler
    *
    * @return    bool
    */
    
    public function connect()
    {
        if(!$this->connected())
        {
            $this->handle = dba_open($this->path, 'c');
        
            if(!$this->handle)
                trigger_error("I cannot create file $this->path, check permissions", E_USER_ERROR);
            
            return true;
            
         // If already connected, true is returned
         } else {
         
             return true;
             
         }
    }
    
    //--------------------------------------------------------    
    
    /**
    * Checks if file handler is opened
    *
    * @return    bool
    */
    
    public function connected()
    {
        if($this->handle != false)
            return true;
    }
    
    //--------------------------------------------------------
    
    /**
    * Closes file handler 
    *
    * @return    bool
    */
    
    public function disconnect()
    {
        if($this->connected())
        {
            $res = dba_close($this->handle);
            $this->handle = false;
            return $res;
         }
    }
  
	//--------------------------------------------------------    
    
    /**
    * Inserts a new row to the data file
    * 
    * @param    array|string    $data
    * @return   bool      
    */
    
    public function insert($data)
    {
        // Connection neeeded
        $this->connect();
        
        // String to be inserted is constructed
        $row = $this->_treat_data($data);        
           
        // New id
        $new_id = $this->_next_id();
        
        // Insert
        $insert = dba_insert($new_id, $row, $this->handle);
        
        // Increment last id
        if($insert)
        {
            $this->cardinality++;
            $this->last_id++;
        }
            
        return $insert;               
    }
    
    //--------------------------------------------------------    
    
    /**
    * Updates given row of data file
    *
    * @param    int             $id
    * @param    array|string    $data
    * @return   bool
    */
    
    public function update($id, $data)
    {  
        // Connection
        $this->connect();
        
        // Row to insert
        $row = $this->_treat_data($data);        
        
        // Checks row exists
        if(dba_exists($id, $this->handle))
        {
           return dba_replace($id, $row, $this->handle); 
            
        } else {
        
            trigger_error("I cannot update an unexistant row ($id)", E_USER_WARNING);
            return false;
        }    
    }
    
    //--------------------------------------------------------    
    
    /**
    * Deletes given row from data file
    *
    * @param    int    $id
    * @return   bool
    */
    
    public function delete($id)
    {
        $this->connect();
        
        if(dba_exists($id, $this->handle))
        {
            return dba_delete($id, $this->handle);
        
        } else {
        
            trigger_error("I cannot delete an unexistant row $id)", E_USER_WARNING);
            return false;
        }
    }
    
    //--------------------------------------------------------
    
    /**
    * Returns requested row from data file
    *
    * @param    int    $id
    * @return   string|bool
    */
    
    public function fetch($id)
    {
        $this->connect();
        
        if(!dba_exists($id, $this->handle))
            return false;
            
        $row = dba_fetch($id, $this->handle);
        
        return $this->_get_results($row);
    }
    
    //--------------------------------------------------------
    
    /**
    * Searches over data file and returns affected rows as strings
    *
    * @param    string    $needle
    * @return   string|bool   
    */
    
    public function search($needle)
    {
        // Results array
        $results = array();
        
        // Regex to search
        $regex = $this->_get_search_regex($needle);
        
        // Results ids
        $ids = $this->_perform_search($regex);
        
        // Total
        $total = count($ids);
        
        // Return array
        for($it = 0; $it < $total; $it++)
        {
            $results[] = $this->fetch($ids[$it]);
        }
        
        return $results;
    }
    
	//--------------------------------------------------------
    
    /**
    * Returns data file cardinality
    *
    * @return    int
    */
    
    public function cardinality()
    {
        return $this->cardinality;
    }
    
    //--------------------------------------------------------    
    
    /**
    * Returns last inserted id
    *
    * @return    int
    */
    
    public function last_insert_id()
    {
        return $this->last_id;
    }
    
    //--------------------------------------------------------    
    
    /**
    * Deletes all rows from database file
    *
    * @return    int
    */
    
    public function truncate()
    {
        foreach($this as $key => $value)
        {
        	$this->delete($key);
        }
        
        $this->last_id = 0;
        $this->cardinality = 0;
    }
    
    //--------------------------------------------------------
    
    /**
    * Checks if given row id exists
    *
    * @param    int    $id
    * @return   bool
    */
    
    function exists($id)
    {
        $this->connect();
        
        return @dba_exists($id, $this->handle);
    }
    
    //--------------------------------------------------------
    
    /**
    * Saves data file emulated fields
    *
    * @param    array    $fields
    * @return   void
    */
    
    public function set_fields($fields)
    {
        if(!is_array($fields))
            trigger_error('An array of strings is spected to declare data file fields', E_USER_ERROR);
            
        $this->fields = $fields;
    }
    
    //--------------------------------------------------------
    
    /**
    * Saves separator used by data file to emulate fields
    *
    * @param    string    $separator
    */
    
    public function set_separator($separator)
    {
        $this->separator = $separator;
    }
    
    //--------------------------------------------------------
    
    /**
    * Optimizes data file
    *
    * @return    bool
    */
    
    public function optimize()
    {
        $this->connect();
        
        return dba_optimize($this->handle);
    }
    
    //--------------------------------------------------------
    
    /**
    * Deletes data file from hard disk
    *
    * @return    bool
    */
    
    public function drop()
    {
        $this->disconnect();
        $deleted = unlink($this->path);
        
        if($deleted)
        {
            $this->last_id = 0;
            $this->cardinality = 0;
        }
        
        return $deleted;
    }
    
    //--------------------------------------------------------
    
    /**
    * Sets fetch mode
    *
    * @param    int    $model
    */
    
    public function set_fetch_mode($mode)
    {
    	if($mode != DBA_ARRAY && $mode != DBA_STRING)
    		trigger_error("Possible fetch modes are DBA_ARRAY and DBA_STRING", E_USER_ERROR);
    		
    	$this->fetch_mode = $mode;
    }
    
    //--------------------------------------------------------
    
    /**
    * Returns result depending of the fetch mode
    *
    * @param	string    $row
    */
    
    private function _get_results($row)
    {
        $data = explode($this->separator, $row);
        
    	// As string
    	if($this->fetch_mode == DBA_STRING)
    	{
    		return $row;
    		
    	// As array
    	} else {
    	
    		 // Declared fields
        	$declared = count($this->fields);
        
        	// If amount of declared fields matches received results
        	if($declared == count($data))
       		{
            	// Assigned
            	for($it = 0; $it < $declared; $it++)
            	{
                	$results[$this->fields[$it]] = $data[$it];
            	}
            	return $results;
        
	        /*
	        * Declared fields do not match retrieved fields
	        */
	        } else {
	            
	            // Warning
	            if(count($this->fields) > 0)
	            {
	                trigger_error("Declared fields do not match returned fetch values. 
	                                Results are returned as non-associative array", E_USER_WARNING);
	            }
	            return $data;
	    	}
	    }
    }
    
    //--------------------------------------------------------
    
    /**
    * Iterates the data file and saves last primary key
    *
    * @return    int
    */
    
    private function _dba_lastkey()
    {
        // Frist entry
        $id = dba_firstkey($this->handle);
        
        // Iteration until last one is reached
        while($id != false)
        {
            if($id > $last)
                $last = $id;
                
            $this->cardinality++;
            $id = dba_nextkey($this->handle);
        }
        
        if($last != NULL)
            return $last;
        else
            return 0;
    }
    
    //--------------------------------------------------------
    
    /**
    * Returns next identifier to insert
    *
    * @return    int
    */
    
    private function _next_id()
    {
        return $this->last_id + 1;
    }
    
    //--------------------------------------------------------
    
    /**
    * Returns a string to be inserted from an array
    * 
    * @param    array    $data
    */
    
    private function _get_insert_string($data)
    {
        if(!is_array($data))
            trigger_error('An array is expected to create the insert string', E_USER_ERROR);
            
        return implode($this->separator, $data);
    }
    
    //--------------------------------------------------------
    
    /**
    * Evaluates received data to insert and returns it as a string
    *
    * @param    array|string    $data
    * @return   string          $row
    */
    
    private function _treat_data($data)
    {
        // If array, string is created
        if(is_array($data))
        {
            $row = $this->_get_insert_string($data);
            
        // Nothing is done if not
        } else if(is_string($data)) {
 
            $row = $data;
            
        } else {
        
            trigger_error("A string or array is expected to insert a new row into data file", E_USER_ERROR);
        
        }
        
        return $row;
    }
    
    //--------------------------------------------------------
    
    /**
    * Calculates a suitable regex to use to perform a search
    *
    * @param    string    $needle
    * @return   string    $regex
    */
    
    private function _get_search_regex($needle)
    {
        // If regex
        if(preg_match("/^\/.+\/$/", $needle))
        {
            $regex = $needle;
            
        // String if not
        } else if(strlen($needle) > 2){

            $regex = "/". $needle ."/";        
        
        } else {
        
            trigger_error("A regular expression or a suitable string is expected to perform a search", E_USER_ERROR);
        }
        
        return $regex;
    }
    
    //--------------------------------------------------------
    
    /**
    * Performs the search itself and returns all affected ids
    *
    * @param    string    $regex
    * @return   array
    */
    
    private function _perform_search($regex)
    {
        $this->connect();
        
        // Results array
        $results = array();
        
        // First entry
        $id = dba_firstkey($this->handle);
        
        // Iteration till the end
        while($id != false)
        {               
            // Fetch current row
            $cadena = $this->fetch($id);
            
            // Search
            if(preg_match($regex, $cadena))
                $results[] = $id;
            
            $id = dba_nextkey($this->handle);
        }
        
        return $results;
    }
    
    //=============================================
	// Iterator implementation
	//=============================================

	/**
	* Rewinds to the first position
	*/
    public function rewind()
    {
    	$this->connect();
        $this->iterator_id = dba_firstkey($this->handle);
    }
    
    //----------------------------------------------------------

	/**
	* Returns current row
	*/
    public function current()
    {   
    	$row = $this->fetch($this->iterator_id);
        return $row;
    }
    
	//----------------------------------------------------------

	/**
	* Returns current key
	*/
    public function key()
    {
        return $this->iterator_id;
    }
    
    //----------------------------------------------------------

	/**
	* Moves to next result
	*/
    public function next()
    {
    	$this->iterator_id = dba_nextkey($this->handle);
    	return $this->iterator_id;
    }
    
    //----------------------------------------------------------

	/**
	* Validates current element
	*/
    public function valid()
    {
    	if(!$this->iterator_id)
    		return false;
    		
        return true;
    }
    
    //--------------------------------------------------------
    
    /**
    * Closes db handler
    */
    
    function __destruct()
    {
       $this->disconnect();
    }
}
?>
