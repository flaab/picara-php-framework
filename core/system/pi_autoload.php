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
* The autoload function is able to include from scratch every class
* existing in the load path.
*
* @package    System
* @author     Arturo Lopez
* @copyright  2008-2019, Arturo Lopez
*/
spl_autoload_register(function($class)
{
    Pi_loader::autoload($class);
});

// DEPRECATED
//function __autoload($class)
//{
//    Pi_loader::autoload($class);
//}

?>
