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
* DISPATCHER EXECUTION
* The controller function is executed and results retrieved.
* Results are saved into standard variables for the template to use.
* If no template is used, the framework assumes that this is an API. 
*/

// Execute the method and store results
$legacy = $dispatcher->process();

// Set local variables for template
foreach($legacy as $key => $val)
{
	$k = $key;
   	$$k = $val; 
}

// Clean vars
unset($legacy);

?>
