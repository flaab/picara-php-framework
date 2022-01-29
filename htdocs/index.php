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
* Change Dir
* Shift php exeution up one dir, but keep
* http execution in public directory.
*/
chdir("../");

/**
* Paths and config
*/
require_once('core/config/paths.php');
require_once(CONFIG     . 'config.php');
require_once(USERCONFIG . 'application.php');
require_once(USERCONFIG . 'environment.php');

/**
* Error Handler
*/
require_once(SYSTEM . 'pi_web_error_handler.php');

/**
* Basic system libraries
*/
require_once(SYSTEM  . 'pi_error_store.php');
require_once(SYSTEM  . 'pi_uri.php');
require_once(SYSTEM  . 'pi_web_dispatcher.php');
require_once(SYSTEM  . 'pi_overloadable.php');
require_once(SYSTEM  . 'pi_route.php');
require_once(SYSTEM  . 'pi_object.php');
require_once(SYSTEM  . 'pi_callbacks.php');
require_once(SYSTEM  . 'pi_db.php');
require_once(SYSTEM  . 'pi_metadata.php');
require_once(SYSTEM  . 'pi_config.php');
require_once(SYSTEM  . 'pi_core.php');
require_once(SYSTEM  . 'pi_loader.php');
require_once(SYSTEM  . 'pi_logs.php'); 
require_once(SYSTEM  . 'pi_validation.php');
require_once(SYSTEM  . 'pi_querybridge.php');
require_once(SYSTEM  . 'pi_query.php');
require_once(SYSTEM  . 'pi_session.php');
require_once(SYSTEM  . 'pi_controller.php'); 
require_once(USERLIB . 'mycontroller.php');
require_once(SYSTEM  . 'pi_paginator.php');
require_once(SYSTEM  . 'pi_web_controller.php'); 
require_once(USERLIB . 'mywebcontroller.php');

/**
* Autoload
*/
require_once(SYSTEM . 'pi_autoload.php');

/**
* Dispatcher
*/
require(ACTION . 'dispatcher.php');

/**
* Execution
*/
require(ACTION . 'controller.php');

/**
* Layout
*/
require(ACTION . 'layout.php');
 
?>
