
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
* Assists in the authentication of users and encryption of passwords.
* Manages groups and permissions checks.
*
* @package    Libs
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2009, Arturo Lopez
* @version    0.1
*/

class Auth
{   
    /**
     * Encrypts a password and returns the result.
     * It uses the function and salt specified in the application config file.
     * 
     * @param   string  $string 
     * @return  string  
     */
    public static function password_encrypt($string)
    {
        $encf = ENCFUNCTION;
        $res = $encf(SALT . $string);
        return(res);
    }
    
    //----------------------------------------------------------  

    /** 
     * Checks if a given password matches.
     * 
     * @param   string  $password
     * @param   string  $try
     * @return  bool
     */
    public static function password_check($enc_password, $try)
    {
        $try_password = self::password_encrypt($try);
        if($enc_password == $try_password)
        {
            return(true);
        } else {
            return(false);
        }
    }
}