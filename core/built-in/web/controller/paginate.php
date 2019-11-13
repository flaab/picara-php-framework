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
* Implements common and flexible pagination functions to use
* by scaffolding and the user itself without extra work.
*
* @package    BuiltControllers
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class PaginateWebController extends MyWebController
{
    /**
    * Loads dependencies
    */
    var $load = array('Watcher');

    //--------------------------------------------------------

    /**
	* Paginates given model using default modelsnap
    *
	* @param   string    $model
	* @param   int       $page
	*/
	 
	public function model($model, $page = 1)
	{
        // Total results to paginate
	 	$total = $this->db->query->getAll($model, NULL, NULL, NULL, false, true);
	    
        // Sets up pagination and creates ranges for current page
        $this->pagination_setup($model, $page, $total);
        
	 	// Results
        $collection = $this->db->query->getAll($model, $this->pagination['order'], $this->pagination['first_result'], $this->pagination['elements']);

        // Performs the pagination request
        $this->pagination_contents($collection, 'Paginate/model/'. $model . '/');

        // Set our specific view
        $this->load_view = BUILTIN_WEB_VIEW . '/Paginate/model.php';  
    
        // Commits
        $this->pagination_commit();
	 	
        // Window title
        $this->appendTitle($this->pagination['display']);
    }

    //--------------------------------------------------------

    /**
	* Paginates a given model from a related model foreign key
	* Example:	All articles with category_id = 4 
	*
	* @param    string    $model
	* @param    string    $relatedModel
	* @param    mixed     $value
	* @param    int       $page
	*/
	 
	public function modelBy($model,$relatedModel, $value, $page=1)
	{
	 	// Obtain the foreign key
	 	$foreignKey = $this->metadata->get_relationship_fk($model, $relatedModel); 
	 	
        // Get the requested model name and foreign key name
	 	$this->pagination['display'] = $this->metadata->config->read($model, 'display');;
	 	$related_display = $this->metadata->config->read($relatedModel, 'display');
	 	
	 	// Create the related model
	 	$relatedObject = new $relatedModel($value);
	 	
	 	//Crash on any failure
	 	if($relatedObject->failed())
	 		$this->core->abort($relatedObject->getErrorStore());
	 
	 	// We get the value name of the related object or error
	 	$valueName = $relatedObject->getValueString();
	    
	 	// Window title
	 	$this->appendTitle($related_display . CONNECTOR . $valueName);
	 	
	 	// If FK is null, then error
	 	if($foreignKey == NULL)
	 		$this->core->abort("Requested model is not related to  '$relatedModel'");
		
        //===========================================================
        // Results are obtained and pagination set
        //===========================================================

	 	// Total fields returned
	 	$total = $this->db->query->getNumRowsOfSearch($foreignKey, $value, $model);

        // Pagination set up
        $this->pagination_setup($model, $page, $total);
	 
	 	// Strict search to get related objects
	 	$collection = $this->db->query->searchBy($foreignKey, $value, $model, false, false, $this->pagination['first_result'], $this->pagination['elements'], $this->pagination['order']);

        // Pagination is performed
        $this->pagination_contents($collection, 'Paginate/modelBy/' . $model . '/' . $relatedModel . '/' . $value . '/');

        // Set our specific view
        $this->load_view = BUILTIN_WEB_VIEW . '/Paginate/modelBy.php';  
        
        // Commits
        $this->pagination_commit();
       
 		//===========================================================
        // Information delegated to the view
        //===========================================================

 		// Related value name must be deliverdad
 		$this->set("valueName", $valueName);

	 	// Baselink must be overriden
	 	$this->set("baselink", "Paginate/modelBy/" . $model . "/" . $relatedModel . "/" . $value . "/");
	}
	 
    //--------------------------------------------------------
	 
	/**
	* Performs a search and displays paginated results for given model
    *
	* @param    string    $model
	* @param    string    $search
	* @param    int       $page
	*/
	 
	public function search($model, $search = NULL, $page = 1)
	{
        // Special snap for search
		$modelsnap = SEARCHSNAP . $model . ".php";
		
        // Spaced search query
        $spaced_search = str_replace(SEARCH_CONNECTOR,' ', $search);

	 	// Humanized and url search string
	 	$this->set('searchQuery', $spaced_search);
	 	$this->set('roughSearch', $search);
	 	
	 	//===========================================================
        // Search is performed
        //===========================================================

	 	// Total results
	 	$total = $this->db->query->performSearch($model, $spaced_search, NULL, NULL, true);

        // Pagination config
        $this->pagination_setup($model, $page, $total);
	
	 	// Once pagination is set, the search can be performed
        $collection = $this->db->query->performSearch($model, $spaced_search, $this->pagination['first_result'], $this->pagination['elements']);

        // Pagination performed
        $this->pagination_contents($collection, 'Paginate/search/'. $model . '/'. $search . '/', NULL, $modelsnap);

        // Set our specific view
        $this->load_view = BUILTIN_WEB_VIEW . '/Paginate/search.php';  
        
        // Commits
        $this->pagination_commit();

 	    //===========================================================
        // Extra information
        //===========================================================
        
        // Window title
	 	$this->appendTitle("Searching for '". str_replace(SEARCH_CONNECTOR,' ', $search) ."' in ". $this->pagination['display']);

 		// Value name must be delegated
 		$this->set("valueName", $valueName);
	}
}
?>
