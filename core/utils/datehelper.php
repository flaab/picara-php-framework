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
* A set of useful datetime functions 
*
* @package    Utils 
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/
 
class Datehelper extends Pi_error_store
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'datehelper';

    /**
    * Instance for singleton pattern
    */
    private static $instance;

    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object. 
    */
    
    private function __construct(){}
    
    //---------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return    Datetime
    */
    
    public static function singleton() 
    {
        if (!isset(self::$instance))
        {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }
    
    //---------------------------------------------------------
    
    /**
    * Returns a UNIX timestamp from a valid date string
    *
    * @param    string    $date_string 
    * @return   string
    */
    
	public static function stamp_from_string($date_string)
	{
		if (is_integer($date_string) || is_numeric($date_string))
		{
			return intval($date_string);
			
		} else {
		
			return strtotime($date_string);
		}
	}
	
	//---------------------------------------------------------
	
	/**
	* Creates a nicely formatted date string given a valid date string
	*
	* @param    string    $date_string
	* @return   string
	*/
	
	public static function nice($date_string = NULL)
	{
		if ($date_string != NULL)
		{
			$date = self::stamp_from_string($date_string);
			
		} else {
		
			$date = time();
		}

		$ret = date("D, M jS Y, H:i", $date);
		return str_replace(", 00:00", '', $ret);
	}
	
	//---------------------------------------------------------
	
	/**
	* Creates a short nice formatted date string
	*
	* @param    string    $date_string
	* @return   string
	*/
	
	public static function short($date_string = NULL)
	{
		$date = $date_string ? self::stamp_from_string($date_string) : time();

		$y = self::is_this_year($date) ? '' : ' Y';

		if (self::is_today($date))
		{
			$ret = "Today, " . date("H:i", $date);
			
		} elseif (self::was_yesterday($date)) {
		
			$ret = "Yesterday, " . date("H:i", $date);
			
		} else {
		
			$ret = date("M jS{$y}, H:i", $date);
		}

		return $ret;
	}
	
	//---------------------------------------------------------
	
	/**
    * Checks if given date string is today
    *
    * @param    string    $date_string
    * @return   bool
    */
     
	public static function is_today($date_string)
	{
		$date = self::stamp_from_string($date_string);
		return date('Y-m-d', $date) == date('Y-m-d', time());
	}
	
    //---------------------------------------------------------	
	
	/**
    * Checks if given datetime is in this week
    *
    * @param    string    $date_string
    * @return   bool
    */
    
	public static function is_this_week($date_string)
	{ 
		$date = self::stamp_from_string($date_string) + 86400;
		return date('W Y', $date) == date('W Y', time());
	}
	
    //---------------------------------------------------------		
	
	/**
    * Checks if given datetime is in this month
    *
    * @param    string    $date_string
    * @return   bool
    */
    
	public static function is_this_month($date_string)
	{
		$date = self::stamp_from_string($date_string);
		return date('m Y',$date) == date('m Y', time());
	}
	
    //---------------------------------------------------------			
	
	/**
    * Checks if given datetime is in this year
    *
    * @param    string    $date_string
    * @return   bool
    */
    
	public static function is_this_year($date_string)
	{
		$date = self::stamp_from_string($date_string);
		return  date('Y', $date) == date('Y', time());
	}
	
	//---------------------------------------------------------			
	
	/**
    * Checks if given datetime was yesterday
    *
    * @param    string    $date_string 
    * @return   bool
    */
    
	public static function was_yesterday($date_string)
	{
		$date = self::stamp_from_string($date_string);
		return date('Y-m-d', $date) == date('Y-m-d', strtotime('yesterday'));
	}
	
	//---------------------------------------------------------	
	
	/**
    * Checks if given datetime is tomorrow
    *
    * @param    string    $date_string
    * @return   bool
    */
    
	public static function is_tomorrow($date_string)
	{
		$date = self::stamp_from_string($date_string);
		return date('Y-m-d', $date) == date('Y-m-d', strtotime('tomorrow'));
	}
	
	//---------------------------------------------------------	
	
    /**
    * Creates a nice string indicating time ago in words
    *
    * @param    string    $date_string 
    * @param    array     $options 
    * @param    string    $backwards    True if date_string is in the future
    * @return   string
    */
    
    private static function time_ago_in_words($datetime_string, $options = array(), $backwards = NULL)
    {
		$in_seconds = $this->stamp_from_string($datetime_string);

		if ($backwards === NULL && $in_seconds > time())
		{
			$backwards = true;
		}

		$format = 'j/n/y';
		$end = '+1 month';

		if (is_array($options))
		{
			if (isset($options['format']))
			{
				$format = $options['format'];
				unset($options['format']);
			}
			
			if (isset($options['end']))
			{
				$end = $options['end'];
				unset($options['end']);
			}
			
		} else {
			$format = $options;
		}

		if ($backwards)
		{
			$start = abs($in_seconds - time());
			
		} else {
		
			$start = abs(time() - $in_seconds);
		}

		$months = floor($start / 2638523.0769231);
		$diff = $start - $months * 2638523.0769231;
		$weeks = floor($diff / 604800);
		$diff -= $weeks * 604800;
		$days = floor($diff / 86400);
		$diff -= $days * 86400;
		$hours = floor($diff / 3600);
		$diff -= $hours * 3600;
		$minutes = floor($diff / 60);
		$diff -= $minutes * 60;
		$seconds = $diff;

		$relative_date = '';

		if ($start > abs(time() - $this->stamp_from_string($end)))
		{
			$relative_date = 'on ' . date($format, $in_seconds);
			
		} else {
		
			if (abs($months) > 0)
			{
				// months, weeks and days
				$relative_date .= ($relative_date ? ', ' : '') . $months . ' month' . ($months > 1 ? 's' : '');
				$relative_date .= $weeks > 0 ? ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '') : '';
				$relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
				
			} elseif (abs($weeks) > 0) {
			
				// weeks and days
				$relative_date .= ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '');
				$relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
				
			} elseif (abs($days) > 0) {
			
				// days and hours
				$relative_date .= ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '');
				$relative_date .= $hours > 0 ? ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '') : '';
				
			} elseif (abs($hours) > 0) {
			
				// hours and minutes
				$relative_date .= ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '');
				$relative_date .= $minutes > 0 ? ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '';
				
			} elseif (abs($minutes) > 0) {
			
				// minutes only
				$relative_date .= ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
				
			} else {
			
				// seconds only
				$relative_date .= ($relative_date ? ', ' : '') . $seconds . ' second' . ($seconds != 1 ? 's' : '');
			}

			if (!$backwards)
			{
				$relative_date .= ' ago';
			}
		}
		
		return $relative_date;
	}

    //--------------------------------------------------------
	
	/**
    * Calls time_ago_in_words but it can also process future stamps
    *
    * @param    string    $date_string 
    * @param    string    $format 
    * @return   string 
    */
    
	public static function relative_time($datetime_string, $format = 'j/n/y')
	{
		$date = strtotime($datetime_string);
		if (strtotime("now") > $date) 
		{
			$ret = self::time_ago_in_words($datetime_string, $format, false);
		} else {
			$ret = self::time_ago_in_words($datetime_string, $format, true);
		}
		return $ret;
	}
    
    //--------------------------------------------------------
    
    /**
    * Given a date, a human age is calculated
    *
    * @param    string    $date
    * @return   int
    */

    public static function age_old($date_of_birth)
    {
        $cur_year=date("Y");
        $cur_month=date("m");
        $cur_day=date("d");

        $dob_year=substr($date_of_birth, 0, 4);
        $dob_month=substr($date_of_birth, 5, 2);
        $dob_day=substr($date_of_birth, 8, 2);

        if($cur_month>$dob_month || ($dob_month==$cur_month && $cur_day>=$dob_day))
            return $cur_year-$dob_year;
        
        return $cur_year-$dob_year-1;
    }
    
}
?>
