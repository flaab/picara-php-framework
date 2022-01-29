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
* Custom error handler function.
* 
* @param    int     $error          Error code
* @param    string  $explanation    Error explanation
* @param    string  $file           File where error was found
* @param    int     $line           Line of error 
*/
function custom_error_handler($error, $explanation, $file, $line)
{
    // Errors config file is included
    require(CONFIG . 'errors.php');
    
    // Catch missing arguments error and die
    if($error == E_WARNING && preg_match("/^Missing\sargument.*Controller/", $explanation))
    {
        echo "\nFramework Error\n";
        echo "-----------------\n";
        echo "Missing arguments for requested controller action (File $file, line $line)\n\n";
        exit(1);
    }
    
    /*
    * Show message
    */
    if(isset($types[ENVIRONMENT][$error]))
    {
        if($types[ENVIRONMENT][$error]['show'])
        {
            // Message
            echo "\n" . $types[ENVIRONMENT][$error]['message'] . "\n";
            
            // Splitter line
            for($it=0; $it < strlen($types[ENVIRONMENT][$error]['message']); $it++)
                echo "-";
            
            // Line break    
            echo "\n";

            // Explanation
            echo "$explanation (File $file, line $line)\n\n";
	    }

        // Die if neccesary
        if(!isset($types[ENVIRONMENT][$error]['abort']) || $types[ENVIRONMENT][$error]['abort'] == true)
	        die;
	}
	return;
}
set_error_handler('custom_error_handler');

/** 
* Custom exception handler function.
* 
* @param    exception   $e 
*/
function custom_exception_handler($e)
{
    // Errors config file is included
    require(CONFIG . 'errors.php');
    
    // If we can read severity
    if($e instanceof ErrorException)
    {
        $error = $e->getSeverity();
    } else {
        $error = E_RECOVERABLE_ERROR;
    }

    // Explanation
    $explanation = $e->getMessage();

    // File
    $file = $e->getFile();
    $line = $e->getLine();
    
    // If we are handling this message
    if(isset($types[ENVIRONMENT][$error]))
    {
        // Display if configured to do so
        if($types[ENVIRONMENT][$error]['show'])
        {
            // Message
            echo "\n" . $types[ENVIRONMENT][$error]['message'] . "\n";
            
            // Splitter line
            for($it=0; $it < strlen($types[ENVIRONMENT][$error]['message']); $it++)
                echo "-";
            
            // Line break    
            echo "\n";

            // Explanation
            echo "$explanation (File $file, line $line)\n\n";
        }

        // Die if neccesary
        if(!isset($types[ENVIRONMENT][$error]['abort']) || $types[ENVIRONMENT][$error]['abort'] == true)
        die;
    }
    return;
}
set_exception_handler('custom_exception_handler');
?>
