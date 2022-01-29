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
* Provides a nice object bridge for a certain data shape query
* 
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class Pi_querybridge extends Pi_overloadable
{
    /**
    * Datashape it represents
    */
    private $datashape;

    /**
    * Local pointer to query object
    */
    private $query;

    //--------------------------------------------------------

    /**
    * Assigns the connection's holder by reference
    *
    * @param    Pi_query   $query
    * @param    int        $datashape
    */

    public function __construct($query, $datashape)
    {
        $this->query = $query;
        $this->datashape = $datashape;
    }

    //--------------------------------------------------------

    /**
    * Sets datashape for every query thrown through this object
    */

    private function set_shape()
    {
        $this->query->setShape($this->datashape);
    }

    //--------------------------------------------------------

    /**
    * Delegates all called functions to the query class
    *
    * @param    string   $method
    * @param    string   $arguments
    */

    protected function _magic($method, $arguments)
    {
        $this->set_shape(); 
        return $this->query->_magic($method, $arguments, true);
    }
}
?>
