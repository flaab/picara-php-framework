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
* Picara error types configuration file.
*
* This config file determines how does the framework catch
* and throw php and application errors.
* 
* Possible show values are...
*
* 0 -> Always will be shown
* 1 -> Just if debug_code >= 1
* 2 -> Just if debug_code = 2
*/

$types = array(
    
    // Fail silently
    'production' => array( 

        E_ERROR              => array('message' => 'PHP Error',        
                                    'abort' => true, 
                                    'show'  => false),
                                        
        E_WARNING            => array('message' => 'PHP Warning',
                                    'abort' => false, 
                                    'show'  => false),
                                        
        E_PARSE              => array('message' => 'PHP syntax error', 
                                    'abort' => true, 
                                    'show'  => false),
                                    
        E_NOTICE             => array('message' => 'PHP notice',
                                    'abort' => false, 
                                    'show'  => false),
                                        
        E_CORE_ERROR         => array('message' => 'PHP Core Error',
                                    'abort' => true,
                                    'show'  => false),
                                        
        E_CORE_WARNING       => array('message' => 'PHP Core Warning',
                                    'abort' => true,
                                    'show'  => false),
                                        
        E_COMPILE_ERROR      => array('message' => 'PHP Compile Error',
                                    'abort' => true,
                                    'show'  => false),
                                        
        E_COMPILE_WARNING    => array('message' => 'PHP Compile Warning',
                                    'abort' => true,
                                    'show'  => false),
                                        
        E_USER_ERROR         => array('message' => 'Framework Error',
                                    'abort' => true,
                                    'show'  => false),
                                        
        E_USER_WARNING       => array('message' => 'Framework Warning',
                                    'abort' => false, 
                                    'show'  => false),
                                        
        E_USER_NOTICE        => array('message' => 'Framework Notice',
                                    'abort' => false,
                                    'show'  => false),
                                        
        E_STRICT             => array('message' => 'PHP Note',
                                    'abort' => false,
                                    'show'  => false),
                                        
        E_RECOVERABLE_ERROR  => array('message' => 'PHP Catchable Fatal Error',
                                    'abort' => true,
                                    'show'  => false),

        E_DEPRECATED         => array('message' => 'PHP Deprecated Notice',
                                    'abort' => false,
                                    'show'  => false),
        
        E_PARSE              => array('message' => 'PHP Parse Error',
                                    'abort' => false,
                                    'show'  => false),
    ),

    // Same failures as production, but verbose
    'development' => array(
        
        E_ERROR              => array('message' => 'PHP Error',        
                                    'abort' => true, 
                                    'show'  => true),
                                        
        E_WARNING            => array('message' => 'PHP Warning',
                                    'abort' => false, 
                                    'show'  => true),
                                        
        E_PARSE              => array('message' => 'PHP syntax error', 
                                    'abort' => true, 
                                    'show'  => true),
                                    
        E_NOTICE             => array('message' => 'PHP notice',
                                    'abort' => false, 
                                    'show'  => false),
                                        
        E_CORE_ERROR         => array('message' => 'PHP Core Error',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_CORE_WARNING       => array('message' => 'PHP Core Warning',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_COMPILE_ERROR      => array('message' => 'PHP Compile Error',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_COMPILE_WARNING    => array('message' => 'PHP Compile Warning',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_USER_ERROR         => array('message' => 'Framework Error',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_USER_WARNING       => array('message' => 'Framework Warning',
                                    'abort' => false, 
                                    'show'  => true),
                                        
        E_USER_NOTICE        => array('message' => 'Framework Notice',
                                    'abort' => false,
                                    'show'  => false),
                                        
        E_STRICT             => array('message' => 'PHP Note',
                                    'abort' => false,
                                    'show'  => false),
                                        
        E_RECOVERABLE_ERROR  => array('message' => 'PHP Catchable Fatal Error',
                                    'abort' => true,
                                    'show'  => true),
        
        E_DEPRECATED         => array('message' => 'PHP Deprecated Notice',
                                    'abort' => false,
                                    'show'  => true),
        
        E_PARSE              => array('message' => 'PHP Parse Error',
                                    'abort' => false,
                                    'show'  => true),

    ),

    // As much info as possible
    'testing' => array(
        
        E_ERROR              => array('message' => 'PHP Error',        
                                    'abort' => true, 
                                    'show'  => true),
                                        
        E_WARNING            => array('message' => 'PHP Warning',
                                    'abort' => false, 
                                    'show'  => true),
                                        
        E_PARSE              => array('message' => 'PHP syntax error', 
                                    'abort' => true, 
                                    'show'  => true),
                                    
        E_NOTICE             => array('message' => 'PHP notice',
                                    'abort' => false, 
                                    'show'  => false),
                                        
        E_CORE_ERROR         => array('message' => 'PHP Core Error',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_CORE_WARNING       => array('message' => 'PHP Core Warning',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_COMPILE_ERROR      => array('message' => 'PHP Compile Error',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_COMPILE_WARNING    => array('message' => 'PHP Compile Warning',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_USER_ERROR         => array('message' => 'Framework Error',
                                    'abort' => true,
                                    'show'  => true),
                                        
        E_USER_WARNING       => array('message' => 'Framework Warning',
                                    'abort' => false, 
                                    'show'  => true),
                                        
        E_USER_NOTICE        => array('message' => 'Framework Notice',
                                    'abort' => false,
                                    'show'  => true),
                                        
        E_STRICT             => array('message' => 'PHP Note',
                                    'abort' => false,
                                    'show'  => true),
                                        
        E_RECOVERABLE_ERROR  => array('message' => 'PHP Catchable Fatal Error',
                                    'abort' => true,
                                    'show'  => true),
        
        E_DEPRECATED         => array('message' => 'PHP Deprecated Notice',
                                    'abort' => false,
                                    'show'  => true),
        
        E_PARSE              => array('message' => 'PHP Parse Error',
                                    'abort' => false,
                                    'show'  => true),

    ), 
);

?>
