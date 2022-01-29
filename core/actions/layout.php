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

/*
* Layout inclusion should not fail, checked in controller
*/
if(isset($dispatcher->signature))
{
	include($dispatcher->getCacheLayout());
	
} else {

	include($dispatcher->myController->load_layout);
}


?>
