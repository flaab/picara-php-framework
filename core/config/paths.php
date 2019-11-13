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
* To implement your own paths, use app/config/application.php
*/
 
//==============================================================
// APP
//==============================================================
define('APP',           'app/');
define('APP_CACHE',     APP . 'cache/');
define('APP_TEMPLATES', APP . 'templates/');
define('CONTROLLER',    APP . 'controller/');
define('SHELL',         APP . 'shell/');
define('MODEL',         APP . 'model/');
define('VIEW',          APP . 'view/');
define('MOD',           APP . 'mod/');
define('LAYOUT',        APP . 'layout/');
define('MODELSNAP',     APP . 'modelsnap/');
define('SEARCHSNAP',    APP . 'searchsnap/');
define('MESSAGE',       APP . 'message/');
define('LOG',           APP . 'log/');
define('USERLIB',       APP . 'lib/');
define('USERCONFIG',    APP . 'config/');
define('USERPAGES',     APP . 'pages/');
define('LANG'      ,    APP . 'lang/');

//==============================================================
// SQLITE Databases folder
//==============================================================
define('DB', 'db/');

//==============================================================
// USER CONFIG
//==============================================================
define('CONNECTION',   USERCONFIG . 'connection/');
define('MODELCONFIG',  USERCONFIG . 'model/');

//==============================================================
// CORE
//==============================================================
define('CORE',              'core/');
define('LIBS',       CORE . 'lib/');
define('VENDORS' ,   CORE . 'vendors/');
define('ACTION',     CORE . 'actions/');
define('CONFIG',     CORE . 'config/');
define('UTILS',      CORE . 'utils/');
define('BUILTIN',    CORE . 'built-in/');
define('SYSTEM',     CORE . 'system/');

//==============================================================
// HTDOCS AND WEBROOT
//==============================================================
define('HTDOCS',       'htdocs/');
define('WEBROOT',     HTDOCS  . 'webroot/');
define('CSS',          WEBROOT . 'css/');
define('IMAGES',       WEBROOT . 'images/');
define('FLASH',        WEBROOT . 'flash/');
define('JS',           WEBROOT . 'js/');
define('PAGES',    	   WEBROOT . 'pages/');
define('MODEL_FILES',  WEBROOT . 'model_files/');


//==============================================================
// BUILT-IN APPLICATION CONTROLLERS AND VIEWS
//==============================================================
define('BUILTIN_WEB', 				BUILTIN       . 'web/');
define('BUILTIN_WEB_CONTROLLER', 	BUILTIN_WEB   . 'controller/');
define('BUILTIN_WEB_VIEW',       	BUILTIN_WEB   . 'view/');
define('BUILTIN_SHELL', 			BUILTIN       . 'shell/');
define('BUILTIN_SHELL_CONTROLLER', 	BUILTIN_SHELL . 'controller/');
define('BUILTIN_SHELL_DATA', 		BUILTIN_SHELL . 'data/');
define('BUILTIN_SHELL_HELP', 		BUILTIN_SHELL . 'help/');


//==============================================================
// CACHE
//==============================================================
define('CACHE', 'cache/');

//==============================================================
// TEMP DIR
//==============================================================
define('TMP'         ,'tmp/');
define('TMP_LAYOUT'  ,'tmp/layout/');
define('TMP_VIEW'    ,'tmp/view/');

?>
