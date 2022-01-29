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
* Provides a set of file system related functions
*
* @package    Libs
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

class FileSystem
{
    /**
    * Writes a file into given path
    *
    * @param    string    $path
    * @param    string    $file
    * @param    bool      $override
    * @return   bool      
    */
    
    public static function put_file($path, $file, $override = true)
    {
        // Obtain path
        $dir_path = self::get_dir_from_file_path($path);
        
        // Is writable
        if(!file_exists($dir_path) || !is_writable($dir_path))
            trigger_error("Supplied directory does not exists or is not writable, check permissions ($path)", E_USER_ERROR);
            
        // If file already exists and override is disabled
        if(file_exists($path) && $override != true)
            return false;
        
        // We should write the file as .new
        $new_file = $path . '.new';
        
        if(empty($file))
        {
            if(!touch($new_file))
                trigger_error("An unexpected error has ocurred writing file $path", E_USER_ERROR);

            return true;

        } else {
        
            // Write or override file
            if(!file_put_contents($new_file, $file))
            {
                trigger_error("Unexpected error writing the new file ". $new_file .", check permissions", E_USER_ERROR);
                
            }
        }

        // We should now check target file exists, and if so, back it up
        if(file_exists($path))
            self::_backup($path);

        // New file is moved to actual file
        if(self::_accomplish($path))
            return true;
            
        trigger_error("An unexpected error has ocurred writing file $path", E_USER_ERROR);
    }
    
    //----------------------------------------------------------  
    
    /**
    * Copies a file to another path
    *
    * @param    string    $source    Path to source file
    * @param    string    $target    Path to target file or directory
    * @param    bool      $override
    * @return   bool      
    */
    
    public static function copy_file($source, $target, $override = true)
    {
        // Source exists
        if(!file_exists($source))
            trigger_error("Source file $source does not exist", E_USER_ERROR);
            
        // If target is only a path, full path should be constructed
        if(preg_match("/\/$/", $target))
            $target .= self::get_file_name_from_path($source);
        
        // If target already exists and override is disabled
        if(file_exists($target) && $override != true)
            return false;

        // Copied as .new
        $new_file = $target . '.new';

        // Copy or override file
        if(!@copy($source, $new_file))
            trigger_error("Unexpected error copying file $source to $new_file", E_USER_ERROR);

        // If target already exists, back it up
        if(file_exists($target))
            self::_backup($target);

        // New file moved to actual file
        if(self::_accomplish($target))
            return true;
            
        trigger_error("Unexpected error copying file $source to $target", E_USER_ERROR);               
    }
    
    //---------------------------------------------------------- 

    /**
    * Returns all files matching a regular expression from a directory
    *
    * @param    string    $path
    * @param    string    $regex
    * @return   array
    */      
    
    public static function find_files($path = '.', $regex = "/^.*$/")
    {
        $all = self::browse_dir($path, $regex);
        return $all['files'];
    }

    //---------------------------------------------------------- 

    /**
    * Returns all directories matching a regular expression from a directory
    *
    * @param    string    $path
    * @param    string    $regex
    * @return   array
    */      
    
    public static function find_dirs($path = '.', $regex = "/^.*$/")
    {
        $all = self::browse_dir($path, $regex);
        return $all['directories'];
    }

    //----------------------------------------------------------

    /**
    * Returns all files and directories matching a regular expression from another directory
    *
    * @param    string    $path
    */

    public static function browse_dir($path = '.', $regex = "/^.*$/")
    {
        // If path does not exist
        if(!is_dir($path))
            trigger_error("Given path ($path) does not exist or is not a directory", E_USER_ERROR);
        
        // Final slash is added if needed
        if(!preg_match("/\/$/", $path))    $path .= '/';

        // Results
        $results['directories'] = array();
        $results['files'] = array();

        // Grab entries
        $all = scandir($path);

        // Iteration
        foreach($all as $element)
        {
            $full_path = $path . $element;
            
            // If pattern is matched and the file is not hidden
            if(!preg_match("/^\./", $element) && preg_match($regex, $element))
            {
                if(is_dir($full_path))
                    $results['directories'][] = $full_path;
                else
                    $results['files'][] = $full_path;
            }
        }

        // Obtaining only dirs
        return $results;
    }
      
    //----------------------------------------------------------     
    
    /**
    * Creates a directory
    *
    * @param    string    $path
    * @param    bool      Will return false if file already exists
    */
    
    public static function create_dir($path)
    {
        // If dir exists, false is returned
        if(file_exists($path)) 
            return false;
            
        if(@mkdir($path, 0777))
            return true;
            
        trigger_error("Unexpected error creating directory $path", E_USER_ERROR);
    }
    
    //----------------------------------------------------------    
    
    /**
    * Creates a directory tree recursively
    *
    * @param    string    $desired_path
    * @param    bool
    */
    
    public static function create_tree($desired_path)
    {
        $path = '';
        $dir = explode("/", $desired_path);
        
        foreach($dir as $val)
        {
            $path .= $val . "/";
            
            if(!is_dir($path))
            {
                 if(!@mkdir($path, 0777))
                     trigger_error("Unexpected error creating directory $path, check permissions", E_USER_ERROR);
            }
        }
        
        if(is_dir($desired_path))
            return true;
            
        return false;
    }
    
    //----------------------------------------------------------
    
    /**
    * Deletes given path and files recursively
    *
    * @param    string    $path
    * @param    bool
    */
    
    public static function delete_tree($path)
    {
        if(is_dir($path))
        {
            $entries = scandir($path);

            foreach ($entries as $entry)
            {
                if($entry != '.' && $entry != '..')
                {
                    self::delete_tree($path . DIRECTORY_SEPARATOR . $entry);
      			}
    		}
    		
    		if(!@rmdir($path))
                trigger_error("Unexpected error deleting directory $path, check permissions", E_USER_ERROR);

  	    } else {
            
            if(file_exists($path))
    		    if(!@unlink($path))
                    trigger_error("Unexpected error deleting file $path, check permissions", E_USER_ERROR);

  	    }
  	    
  	    return true;
    }
    
    //----------------------------------------------------------                
    
    /**
    * Returns dir path for given file path
    *
    * @param    string    $path
    * @return   string
    */
    
    public static function get_dir_from_file_path($path)
    {
        // If empty file name
        if(strlen($path) == 0)
            trigger_error('Supplied filename is empty', E_USER_ERROR);
            
        // If only path has been supplied
        if(preg_match("/\/$/", $path))
            trigger_error('Supplied string is a directory path. A file path is expected', E_USER_ERROR);
            
        // If just filename is supplied
        if(preg_match("/^[^\/]+$/", $path))
        {
            return '.';
        }
        
        // A path has been supplied
        return preg_replace("/[^\/]+$/", '', $path);
    }
    
    //----------------------------------------------------------                
    
    /**
    * Returns file name from a full path
    *
    * @param    string    $path
    * @return   string
    */
    
    public static function get_file_name_from_path($path)
    {
        // If empty path or no file name appended
        if(strlen($path) == 0 || preg_match("/\/$/", $path))
            return false;
        
        // A path has been supplied
        return preg_replace("/^.*\//", '', $path);
    }
    
    //--------------------------------------------------------

    /**
    * Tails a file any amount of lines.
    * 
    * @param    string  $file
    * @param    int     $lines
    * @return   string
    */
    public static function tail($file, $lines)
    {
       if(!file_exists($file)) return(false);
       $handle = fopen($file, "r");
       $linecounter = $lines;
       $pos = -2;
       $beginning = false;
       $text = array();
       while ($linecounter > 0) {
         $t = " ";
         while ($t != "\n") {
           if(fseek($handle, $pos, SEEK_END) == -1) {
$beginning = true; break; }
           $t = fgetc($handle);
           $pos --;
         }
         $linecounter --;
         if($beginning) rewind($handle);
         $text[$lines-$linecounter-1] = fgets($handle);
         if($beginning) break;
       }
       fclose ($handle);
       //return(implode("", $text));
       return implode('',array_reverse($text)); // array_reverse is optional: you can also just return the $text array which consists of the file's lines.
    }
    
    //----------------------------------------------------------

    /**
    * Backups received file by renaming it to .old
    *
    * @param    string    $path
    */

    private static function _backup($path)
    {
        $old_file = $path . '.old';
        if(rename($path, $old_file))
            return true;

        trigger_error("Unexpected error backing up replaced file, check permissions", E_USER_ERROR);
    }

    //--------------------------------------------------------

    /**
    * Moves new file to actual file
    *
    * @param    string    $target
    */
    
    private static function _accomplish($target)
    {
        $new_file = $target . '.new';

        if(rename($new_file, $target))
        {
            chmod($target, 0777);
            return true;
        }
        return true;
    }
}
?>
