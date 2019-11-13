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
* Default number of elements to display on each pagination page
* 
* @default 9
*/
define('PAGINATION_ELEMENTS', 9);

/**
* Default number of pagelinks displayed when paginating
*
* @default 9
*/
define('PAGINATION_PAGELINKS', 9);


/**
* Provides the pagination feature for all web controllers.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

abstract class Pi_paginator extends MyController
{
    /**
    * Stores all pagination parameters during the process
    */
    protected $pagination = array();
    
    
    //--------------------------------------------------------

    /**
    * Performs a complete pagination process abstracting the user
    * from the required steps to perform a results limited
    * queries pagination. Receives a huge collection
    * with all objects to be paginated.
    *
    * You should consider performing the pagination manually
    * if you expect a lot of results, this function might became
    * slow. This is dony by calling pagination_setup(), pagination_contents()
    * and pagination_commit().
    *
    * @param    string                  $model         Model to paginate
    * @param    Pi_modelcollection      $collection    Collection returned by query
    * @param    int                     $page          Actual page
    * @param    string                  $baselink      Base link to create pagelinks
    * @param    string                  $display       Optional different display name
    * @param    string                  $snap          Optional full path to another snap file
    */
    
    protected final function paginate($model, $collection, $page, $baselink, $display, $snap)
    {
        // Check valid collection
        if(get_class($collection) != 'Pi_modelcollection')
            trigger_error('Collection parameter is expected to be a Pi_modelcollection', E_USER_ERROR);

        // First, the pagination is setted up
        $this->pagination_setup($model, $page, $collection->length());
        
        // Obtains objects to show for this page; its an array but no problem cause implements iterator
        $elements = $collection->segment($this->pagination['first_result'], $this->pagination['elements']);

        // Add contents to this page
        $this->pagination_contents($elements, $baselink, $display, $snap);
        
        // Commit
        $this->pagination_commit();
    }

    //--------------------------------------------------------
    
    /**
    * Calculates pagination for given model and page, storing limits and
    * results that are used afterwards to perform a limited query.
    *
    * @param    string    $model
    * @param    int       $page
    * @param    int       $total    Total results to paginate
    */

    protected final function pagination_setup($model, $page, $total)
    {  
        // Page should be numeric
        if(!is_numeric($page))
            $this->core->abort('Page should be a numeric parameter');

        // Stores first page, one, of course
        $this->pagination['first_page'] = 1;

        // Sets pagination for this model
        $this->_setPagination($model);
        
        // Stores model
        $this->pagination['model'] = $model;

        // Stores total
        $this->pagination['total_results'] = $total;
        
        // If empty
        if($total <= 0)
            $this->pagination['empty'] = true;
        
        // We are now ready to calculate pagination
        $this->_calculatePagination($page);
        
        // Stores page
        $this->pagination['page'] = $page;
    }

    //--------------------------------------------------------

    /**
    * Performs the pagination according calculated parameters and received results.
    *
    * @param    ModelCollection    $collection    Collection of objects to display at current page
    * @param    string             $baselink      Link to create links to other pagination pages
    * @param    string             $display       Display name to show at pagination view
    * @param    string             $snap          Path to another snap to use
    */

    protected final function pagination_contents($collection, $baselink, $display = NULL, $snap = NULL)
    {
        // No isolated execution
        if(count($this->pagination) <= 0)
            trigger_error('A pagination setup must be performed before appending contents to it', E_USER_ERROR);

        // Reads config for paginated model
        $config = $this->metadata->config->get($this->pagination['model']);

        // Obtains display name if not provided
        if($display == NULL) $this->pagination['display'] = $config->display; else $this->pagination['display'] = $display;

        // Calculates modelsnap if not provided
        if($snap == NULL) $this->pagination['snap'] = MODELSNAP . $this->pagination['model'] . '.php'; else $this->pagination['snap'] = $snap;
       
        // Checks modelsnap existance
        if(!file_exists($this->pagination['snap']))
            $this->core->abort("The ModelSnap " . $this->pagination['snap'] . " does not exist.",'Pagination request failed');

        // Fix baselink if last slash is missing
        if(!preg_match("/\/$/", $baselink)) $baselink .= '/';

        // Baselink is saved
        $this->pagination['base_link'] = $baselink;

        // Collection is stored
	 	$this->pagination['collection'] = $collection;

        // Model pagination view forced, user can change it afterwards
        $this->load_view = BUILTIN_WEB_VIEW . '/Paginate/model.php';  
    }

    //--------------------------------------------------------

    /**
    * Commits by delegating the pagination array to the view.
    * It is separated from pagination_contents to let the user override
    * any values before turning them permanent in the view.
    *
    * Increments page numbers over zero and reverts the left links array.
    */

    protected final function pagination_commit()
    {
        $this->pagination['first_result']++;
        $this->pagination['last_result']++;

        if(is_array($this->pagination['left_links']))
            $this->pagination['left_links'] = array_reverse($this->pagination['left_links']);
    
        $this->set('pagination', $this->pagination); 
    }

    //===========================================================
    // Private pagination functions
    //===========================================================

	/**
	* Calculates all page numbers and links
    *
    * @param    int    $page
	*/

	private final function _calculatePagination($page)
	{
	 	// First element to show in this page
        // FIXED: Move before the return of empty results to avoid sql errors
        $this->pagination['first_result'] = $this->_getFirstIndexForPage($page);
        
        // If collection is empty, fast return
        if($this->pagination['empty'] == true)    return;
        
	 	// First element to show in this page
        $this->pagination['first_result'] = $this->_getFirstIndexForPage($page);

	 	// Error if out of range
	 	if($this->pagination['first_result'] >= $this->pagination['total_results'])
	 	 	$this->core->abort("Page number ". $page . " cannot be created for this pagination");
	 	 
	 	// Last element of the page
	 	$this->pagination['last_result'] = $this->_getLastIndexForPage($page, $this->pagination['total_results']);	 	 

        // Last page to link in the view
	 	$this->pagination['last_page'] = ceil($this->pagination['total_results'] / $this->pagination['elements']);
        
        // Store current page in pagination array
        $this->pagination['page'] = $page;

		// Center and each sides limits
		$center = ceil($this->pagination['pagelinks'] / 2);
		$limit = floor($this->pagination['pagelinks'] / 2);
		
		// Both sides links
		$this->pagination['side_left'] = $this->_getPageNumbers("left", $page, $this->pagination['last_page'], $limit, true);
		$this->pagination['side_right'] = $this->_getPageNumbers("right", $page, $this->pagination['last_page'], $limit, true);
	
		// Both sides links must be equilibrated, this means that if we have less available links in a side, the gap must be filled on the other
		if($this->pagination['side_right'] > $this->pagination['side_left'])
		{
			$this->pagination['side_right'] = $this->pagination['side_right'] + ($limit - $this->pagination['side_left']);
			
		} else if($this->pagination['side_right'] < $this->pagination['side_left']){
		
			$this->pagination['side_left']= $this->pagination['side_left'] + ($limit - $this->pagination['side_right']);
		}
		
		// Obtains the page to links
		$this->pagination['left_links'] = $this->_getPageNumbers("left", $page, $this->pagination['last_page'], $this->pagination['side_left']);
		 
		// Right side links
		$this->pagination['right_links'] = $this->_getPageNumbers("right", $page, $this->pagination['last_page'], $this->pagination['side_right']);
	}

    //--------------------------------------------------------
	 
	/**
	* Sets the pagelinks, elements and order according the paginated model, or defaults
    *
    * @param    string    $modelname
	*/

	private final function _setPagination($modelname)
	{
        if(!Pi_loader::model_exists($modelname))
            $this->core->abort("Pagination request is not valid, model '$modelname' does not exist");
        
        // Configuration
	 	$config = $this->metadata->config->get($modelname);

        // Stores paginated modelname
        $this->pagination['model'] = $modelname;
	
        // Stores current pagination orientation
        $this->pagination['direction'] = $config->pagination->direction;

	 	// Pagination elements
        if(isset($config->pagination->elements) && is_numeric($config->pagination->elements) && $config->pagination->elements > 0)
	 	{
	 		$this->pagination['elements'] = $config->pagination->elements;
	 		
	 	} else {
	 	
	 		trigger_error("Pagination elements must be a positive integer, please, check config file for model '$modelname'", E_USER_ERROR);
	 	}
	 	
	 	// Pagination links
        if(isset($config->pagination->pagelinks) && is_numeric($config->pagination->pagelinks) && $config->pagination->pagelinks > 0)
	 	{
	 		$this->pagination['pagelinks'] = $config->pagination->pagelinks;
	 		
	 	} else {
	 	
	 		trigger_error("Pagination pagelinks must be a positive integer, please, check config file for model '$modelname'", E_USER_ERROR);
	 	}

		// Order
        if(isset($config->pagination->order) && is_string($config->pagination->order) && in_array($config->pagination->order, $this->metadata->read_columns($modelname)))
		{
			$this->pagination['order'] = $config->pagination->order;

		} else {

            trigger_error("Pagination order must be a existing field of the represented table, please, check config file for model '$modelname'", E_USER_ERROR);

        }
    }

    //--------------------------------------------------------
	
    /**
	* Calculates first result shown for given page according pagination
    *
	* @param    int    $page
    * @return   int
	*/
	 
	private final function _getFirstIndexForPage($page)
	{
	 	$firstIndex = ($page * $this->pagination['elements']) - $this->pagination['elements'];
	 	return $firstIndex;
	}

    //--------------------------------------------------------
	 
	/**
	* Calculates last result shown for given page
	*
	* @return    int
	* @param     int    $page
    * @param     int    $total
	*/
	 
	private final function _getLastIndexForPage($page, $total)
	{
	 	$last = ($page * $this->pagination['elements']) - 1;
	 	$first = $this->_getFirstIndexForPage($page);
	 	
	 	// Iteration in $ids array from first to last until isset fails
	 	for($it = $first; $it <= $last; $it++)
	 	{
	 		if($it >= $total)
	 			return($it-1);
	 	}
	 	
	 	// If got here, it's not the last page to paginate
	 	return $last;
	 	
	}

    //--------------------------------------------------------
	 
	/**
	* Completes the pagination calculation according stored pagination settings
    *
    * @param    string    $direction
    * @param    int       $page
    * @param    int       $last
    * @param    int       $limit
    * @param    bool      $justNumbers
	* @return   array|int
	*/

	private final function _getPageNumbers($direction, $page, $last, $limit, $justNumbers = false)
	{
	  	$numbers = array();
	  	$start = 1;
	  	
	  	// Left
	  	if($direction == "left")
	  	{
	  		for($it = $page; $it >= $start; $it--)
	  		{
	  			if($page != $it)
	  				$numbers[] = $it;
	  				
	  			if(count($numbers) >= $limit)
	  				break;
	  		}
	  	}
	  	
	  	// Left
	  	if($direction == "right")
	  	{
	  		for($it = $page; $it <= $last; $it++)
	  		{
	  			if($page != $it)
	  				$numbers[] = $it;
	  				
	  			if(count($numbers) >= $limit)
	  				break;
	  		}
	  	}
	  	
	  	// Return
	  	if($justNumbers == false)
	  	{
	  		return $numbers;
	  	} else {
	  		return count($numbers);
	  	}
	}	 
}
?>
