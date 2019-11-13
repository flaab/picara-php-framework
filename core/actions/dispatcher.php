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
* Session
* Keeps alive current session.
* Auth is done using session vars.
*/
Pi_session::name(SESSION_NAME);
Pi_session::keepAlive();

/*
* Dispatcher
**/
if(isset($handuri))
{   
	$dispatcher = new Pi_web_dispatcher($handuri);
	
} else {
	
	$dispatcher = new Pi_web_dispatcher();
}
 
?>
