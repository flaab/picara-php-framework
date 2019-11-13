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
 * 
 * If critical errors have been generated
 *
 */
 if(isset($_SESSION['picara']['controller_errors']) && is_array($_SESSION['picara']['controller_errors']))
 { 
 	// We include error view
 	include(MESSAGE . 'error.php');
 	
 	// Unset existing errors
 	unset($_SESSION['picara']['controller_errors']);
 	
 	// And redirect if needed
 	if(ERROR_REDIRECT != NULL)
 	{
 		Pi_core::requestAction(ERROR_REDIRECT);
 	}
 
 /*
 * 
 * Normal rendering, checking for dataErrors and flash messaged
 *
 */
 	
 } else {
 
 	/*
 	*
 	* dataErrors and flash messages check and display
 	*
 	*/
 	if(!isset($dispatcher->myController->ignore_messages))
 	{
	 	// If flash message exist, we load the displayer
	 	if(isset($_SESSION['picara']['controller_flash']) && is_array($_SESSION['picara']['controller_flash']))
	 	{
	 		include(MESSAGE . 'flash.php');
 			unset($_SESSION['picara']['controller_flash']);
	 	}
	 	
	 	// If validation errors exists, we load the displayer
	 	if(isset($_SESSION['picara']['controller_dataerrors']) && is_array($_SESSION['picara']['controller_dataerrors']))
	 	{
	 		include(MESSAGE . 'dataError.php');
 			unset($_SESSION['picara']['controller_dataerrors']);
	 	}
	 	
        // If validation errors exists, we load the displayer
	 	if(isset($_SESSION['picara']['controller_warning']) && is_array($_SESSION['picara']['controller_warning']))
        {
	 		include(MESSAGE . 'warning.php');
 			unset($_SESSION['picara']['controller_warning']);
	 	}
	 }
	 	
 	/*
 	* If the user forced another view to be loaded
 	* It setView() was used, it should not fail
 	*/
 	
 	if(isset($dispatcher->myController->load_view))
 	{
 			include($dispatcher->myController->load_view);

 	} else {
 	
 		/*
 		* We should load a view, unless the controller ordered not to
 		*/
 		if(!isset($dispatcher->myController->noView))
 		{	
 			/*
 			* Check if signature exists. If it does, we are generating a static document
 			* and should load the parsed view instead.
 			*/
 			if(isset($dispatcher->signature))
 			{
 				include($dispatcher->getCacheView());
 				
 			/*
 			* Not generating; default one loaded
 			*/
 			} else {
 			
 				include($dispatcher->viewPath);
 			
 			}
 		}
 	}
 }
?>
