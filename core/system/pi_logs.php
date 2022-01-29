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
* Max log filesize in bytes. When the max is reached, the log 
* will be renamed and other start fresh. If NULL, you should 
* manually take care of the log file
*
* @default: 4194304 bytes (4 Mb)
*/
define('MAX_LOG_FILESIZE', 4194304);

/**
* Manages log entries and files.
*
* @package      System
* @author       Arturo Lopez
* @copyright    Copyright    (c) 2008-2019, Arturo Lopez
* @version      0.1
* @example      logs/logs.php
*/

class Pi_logs extends Pi_overloadable
{
    /**
    * Instance for singleton pattern
    */
    private static $instance;

    /**
    * Base log directory for this execution (shell or web)
    */
    private $base_dir;
    
    /**
    * Message types (Do not change order)
    */
    private $types = array('error','warning','message');
    
    /**
    * Log levels according to environment
    */
    private $log_levels = array('testing' => 3, 'development' => 2, 'production' => 1);

    /**
    * Checked files
    */
    private $checked = array();

    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object. 
    */
    
    private function __construct()
    {
        // Write down log base directory
        $this->base_dir = LOG;
    }
    
    //----------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return    Pi_logs    
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
    * Writes a line to the log, checking log level.
    *
    * @param    int       $type
    * @param    string    $msg
    * @param    string    $log
    */
    
    private function write_to_log($type, $msg, $log)
    {
        // If log level is null, do not write
        if(is_null(LOG_LEVEL)) return;
        
        // Log level (-1 to match real levels)
        $auto_log_level = LOG_LEVEL - 1;

        // If it is zero, adjust the value
        if(LOG_LEVEL == 0)
            $auto_log_level = $this->log_levels[ENVIRONMENT];
        
        // Log level check 
        if($type < $auto_log_level)
        {
            $log_file = $this->get_log_path($log);
            $line = $this->_getLogLine($type, $msg);
            $file = @fopen($log_file,'a+');
            
            if(!$file)
                trigger_error("I cannot open log file, located at $log_file. Check permissions.", E_USER_WARNING);        

            if(!@fwrite($file, $line))
                trigger_error("I cannot write to log file, located at $log_file. Check permissions.", E_USER_WARNING);
            
            @fclose($file);
            
            return;
        } 
    }

    //----------------------------------------------------------

    /**
    * Calculates the path to desired log file
    *
    * @param    string    $log
    */

    private function get_log_path($log)
    {
        // Directory where files should be
        $dir = $this->base_dir . $log . '/';
        
        // If the directory does not exist, an error is thrown
        if(!is_dir($dir))
            trigger_error("Requested log directory does not exist. You can create it executing 'php scripts/picara create log $log'", E_USER_ERROR);

        // Path to file
        $file = $dir . ENVIRONMENT . '.log';

        // Check for size
        $this->check_size($file, $log);

        return $file;     
    }
    
    //----------------------------------------------------------    
        
    /**
    * Provides a complete log line to be written into the log file
    *
    * @param    int       $type Error type
    * @param    string    $msg Message
    */
    
    private function _getLogLine($type, $msg)
    {
        $stamp = date("D M d H:i:s Y");
        $session = session_id();
        $type = $this->types[$type];

        if(EXECUTION == 'web')
            $session = "[web $session]";
        else
            $session = '[shell]';
            
        $line = "[$stamp][$type]$session $msg\n";

        return $line;
    }
    
    //----------------------------------------------------------
    
    /**
    * Writes an error message to the log file
    *
    * @param    string    $msg
    * @param    string    $log
    */
    
    public function error($msg, $log = DEFAULT_LOG)
    {
        $this->write_to_log(0, $msg, $log);
    }
    
    //----------------------------------------------------------
    
    /**
    * Writes a warning message to the log file
    *
    * @param    string    $msg
    * @param    string    $log
    */
    
    public function warning($msg, $log = DEFAULT_LOG)
    {
        $this->write_to_log(1, $msg, $log);
    }

    //----------------------------------------------------------    
        
    /**
    * Writes a standard message to the log file
    *
    * @param    string    $msg
    * @param    string    $log
    */
    
    public function message($msg, $log = DEFAULT_LOG)
    {
        $this->write_to_log(2, $msg, $log);
    }

    //----------------------------------------------------------

    /**
    * Checks log file size and expires it if neccesary
    *
    * @param    string    $path
    */

    private function check_size($path, $log)
    {
        // A file is checked only once, and only if is written in current environment and execution
        if(in_array($log, $this->checked)) return;

        // If file exists, check size
        if(file_exists($path))
        {
            if(filesize($path) > MAX_LOG_FILESIZE)
            {    
                if(!$this->expire($path))
                    trigger_error("I do not have permissions to rename $path", E_USER_ERROR);
            }
        }

        // Do not check this log again in this execution
        $this->checked[] = $log;
    }
    
    //----------------------------------------------------------    
    
    /**
    * Expires the current log file by renaming it to log.log-YYYYMMdd-H:M
    *
    * @return bool
    */
    
    private function expire($path)
    {
        $stamp = date("-Ymd-Hi");
        $new_name = $path . $stamp;
        return @rename($path, $new_name);
    }

    //--------------------------------------------------------

    /**
    * Magic function implementation
    *
    * @param    string    $method
    * @param    string    $arguments
    */

    protected function _magic($method, $arguments)
    {
        /**
        * Implements: [type] _to_ [log_name]
        */

        if(preg_match("/^(error|message|warning)_to_(.+)$/", $method, $captured))
        {
            return $this->{$captured[1]}($arguments[0], $captured[2]); 
        }
        $this->method_does_not_exist($method);
    }
}
?>
