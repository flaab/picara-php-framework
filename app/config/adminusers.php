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
* This file stores admin users for the admin backend.
* This method is has been selected to avoid depending on sqlite.
* If you create a superuser, this file is not used.
*/

$GLOBALS['picara_admin_users'] = array(
    
    // Example admin user
    'admin' => array(
        'name' => 'Administrator',
        'pwd'  => 'mypassword',
    ),
);
