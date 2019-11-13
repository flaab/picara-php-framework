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
* This class should implement all model validation functions. 
* Do not hesitate to write your own.
*
* @package    System
* @author     Arturo Lopez Perez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/
 
class Pi_validation extends Pi_overloadable
{
    /**
    * Singleton instance
    */
    private static $instance;
    
    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object
    */
    private function __construct() {}
    
    //----------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return   Pi_validation
    */
    public static function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    } 
    
    //----------------------------------------------------------
    
    /**
    * Checks if string is not empty
    *
    * @param    string    $value
    * @return   boole
    */
  
    public function not_empty($value)
    {
        if(empty($value))
            return false;
            
        return true;
    }
    
    //----------------------------------------------------------
    
    /**
    * Checks if a date is valid
    *
    * @param    string    $date
    * @return   bool
    */
    
    public function valid_date($date)
    {
        $date = explode('-', $date);
        if(checkdate($date[1], $date[2], $date[0]))
        {
            return true;
        } 
        
        return false;
    }
    
    //----------------------------------------------------------
    
    /**
    * Checks if given date is past
    *
    * @param    string    $date
    * @return   bool
    */
    
    public function past_date($date)
    {
        // If date not valid
        if(!$this->valid_date($date))
            return false;
            
        // Conversion to unix timestamp
        $today = date("Y-m-d");
        $today = strtotime($today);
        $date = strtotime($date);
        
        if($date < $today)
            return true;
             
        return false;
    }
    
    //----------------------------------------------------------
    
    /**
    * Checks if a string is alphanumeric 
    *
    * @param    string    $string
    * @param    string    $allowed
    * @return    bool
    */
    
    public function alphanumeric($string, $allowed = '')
    {
        $regex = "/^[a-zA-Z0-9". $allowed  ."]+$/u";

        if(preg_match($regex, $string))
            return true;

        return false;
    }

    //--------------------------------------------------------
    
    /**
    * Validates an IPv4
    *
    * @param    string    $string
    * @return   bool
    */

    public function ipv4($string)
    {
        if(preg_match("/^[0-9]{1,3}(?>\.[0-9]{1-3}){3}$/", $string))
            return true;

        return false;
    }

    //----------------------------------------------------------

    /**
    * Validates an Url
    *
    * @param    string    $string
    * @return   bool
    */

    public function valid_url($string)
    {
        // Protocol, subdomain or www, domain name, simple or double extension, optional port, optional parameters, optional anchors
        $regex = "/^(https?|ftp):\/\/([a-z0-9\-]+)\.([a-z0-9\-]+)(\.[a-z0-9\-]+){1,2}(:[0-9]+)?(\?.*)?(#[a-z0-9_\-])?$/iu";
        
        if(preg_match($regex, $string))
            return true;

        return false;
    }

    //----------------------------------------------------------
    
    /**
    * Checks lenght of a string between a range given
    *
    * @param    string    $string
    * @param    int       $min
    * @param    int       $max
    * @return   bool
    */
    
    public function valid_length($string, $min, $max)
    {
        if(strlen($string) < $min || strlen($string) > $max)
            return false;

        return true;
    }
    
    //----------------------------------------------------------

    /**
     * Checks given number is in the value range given
     *
     * @param    int    $number
     * @param    int    $min
     * @param    int    $max
     */
    public function numeric_value($number, $min, $max)
    {
        if(!is_numeric($number)) return false;

        if($number < $min || $number > $max)
            return false;

        return true;
    }
    
    //----------------------------------------------------------
    
    /**
    * Checks if given mail is valid
    *
    * @param      string    $mail
    * @return     bool
    */
    
    public function valid_mail($mail)
    {
        if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $mail))
        {
            return false;
        } else {
            return true;
        }
    }
    
    //----------------------------------------------------------

    /**
    * Implements dinamic functions for Validator class
    *
    * @param    string     $method
    * @param    string     $arguments
    */

    protected function _magic($method, $arguments)
    {
            /*
             * length_from_ [n] _to_ [y]
             */
            if(preg_match("/^length_[0-9]+_to_[0-9]+$/", $method))
            {
                $numbers = preg_split("/[^0-9]+_/", $method);
                $a = $numbers[1];
                $b = $numbers[2];
                
                return $this->valid_length($arguments[0], $a, $b);
            }

            /*
             * numeric_value_from_ [n] _to_ [y]
             */
            if(preg_match("/^numeric_value_from_[0-9]+_to_[0-9]+$/", $method))
            {
                $numbers = preg_split("/[^0-9]+_/", $method);
                $a = $numbers[1];
                $b = $numbers[2];

                return $this->numeric_value($arguments[0], $a, $b);
            }

            /*
             * numeric_value_greater_than_ [n]
             */
            if(preg_match("/^numeric_value_greater_than_[0-9]+$/", $method))
            {
                $numbers = preg_split("/[^0-9]+_/", $method);
                $a = $numbers[1];

                if(!is_numeric($arguments[0])) return false;

                if($arguments[0] > $a)
                        return true;

                return false;
            } 

            /*
             * numeric_value_less_than_ [n]
             */
            if(preg_match("/^numeric_value_less_than_[0-9]+$/", $method))
            {
                $numbers = preg_split("/[^0-9]+_/", $method);
                $a = $numbers[1];

                if(!is_numeric($arguments[0])) return false;

                if($arguments[0] < $a)
                        return true;

                return false;
            } 

            
            /*
             * alphanumeric_(with_spaces)?(lenght_n_to_y)
             */
            if(preg_match("/^alphanumeric(_with_spaces)?(_length_[0-9]+_to_[0-9]+)?$/", $method))
            {
                // Default values
                $space = '';
                $min = NULL;
                $max = NULL;
                $valid_length = true;

                // Check spaces
                if(preg_match("/_with_spaces/", $method)) { $space = "\ ?"; } 

                // Check for lenght
                if(preg_match("/_length_[0-9]+_to_[0-9]+$/", $method))
                {
                    $numbers = preg_split("/[^0-9]+_/", $method);
                    $valid_length = $this->valid_length($arguments[0], $numbers[1], $numbers[2]);
                } 
  
                return($this->alphanumeric($arguments[0], $space, $min, $max) && $valid_length);
            }

            $this->method_does_not_exists($method);
    }
}
?>
