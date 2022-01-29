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
* Each model can contain a set of file blocks. A file block has a 
* specific name and can store one or more files. For instance,
* a model called 'Product' can have a file block named 'Drivers',
* which stores .zip files.
*
* The file container allows all models to associate a set of files
* to each object.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

abstract class Pi_filecontainer extends Pi_validatable
{
    /**
    * Returns the complete path to store file blocks
    *
    * @return   string
    */
    
    public final function get_basic_files_path()
    {
        // File blocks must be enabled
        if($this->config->files->enabled != true)
            trigger_error('File blocks are not enabled for this model', E_USER_ERROR);

        return MODEL_FILES . $this->my_class . '/' . MODEL_BLOCKS . $this->fields->id . '/';        
    }

    //--------------------------------------------------------

    /**
    * Returns the path to received block files or dies
    *
    * @param    string    $block
    * @param    bool      $check
    * @return   string
    */
    
    public final function get_files_path($block, $check = true)
    {
        // If empty block
        if(empty($block))
            trigger_error('Block name cannot be empty', E_USER_ERROR);

        // Block must be enabled
        if($check == true && !$this->is_enabled_block($block))
            trigger_error('File block '. $block .' is not enabled', E_USER_ERROR);

        // If no represented row yet, error
        if(!is_numeric($this->fields->id))
            trigger_error('File blocks for unknown objects cannot be managed', E_USER_ERROR);

        return $this->get_basic_files_path() . $block . '/';        
    }

    //--------------------------------------------------------
    
    /**
    * Stores a file to given block
    *
    * @param    string    $block
    * @param    array     $file     Pointer to the $_FILES element array
    * @return   bool
    */

    public final function add_file($block, $file)
    {
        if($this->validate_file($block, $file))
        {
            // Grab file extension (validation crashes if no extension)
            preg_match("/\.([a-z0-9]{3,4})$/i", $file['name'], $matches);

            // If extra images must be renamed
            if($this->config->files->blocks->{$block}->rename == true)
            {
                // New name must be created to avoid repeated names for concurrent use
                $name = substr(md5(microtime() . rand(1,1000)), 0, 8) . '.'. $matches[1]; 
            
            } else {
                
                // File name must preserve original name, but whitespaces are converted into underscores
                $name = str_replace(' ', '_', $file['name']);
            }
            
            // Path
            $path = $this->get_files_path($block, false);
            
            // File path
            $file_path = $path . $name;
            
            // Attemp to create dir
            if(!is_dir($path))
            {
                if(!FileSystem::create_tree($path))
                {
                    $this->log->error("I do not have enough permissions to create ". $path .", check permissions.");
                    trigger_error("I do not have enough permissions to create $path, check permissions", E_USER_ERROR);
                }
            }

            // Write file or die
            if(!move_uploaded_file($file['tmp_name'], $file_path))
            {
                $this->log->error('I cannot write '. $file_path .', check permissions.');
                trigger_error('I cannot write '. $file_path .', check permissions', E_USER_ERROR);
            }

            // Set 777 for this file
            @chmod($file_path, 0777);
            
            // Log
            $this->log->message('File '. $file_path .' has been created.');

            // OK 
            return true;
        }

        return false;
    }

    //--------------------------------------------------------

    /**
    * Removes a file from given block
    *
    * @param    string        $block
    * @param    string|int    $id       Relative position or file name
    * @return   bool
    */

    public final function delete_file($block, $file)
    {
        // If relative position
        if(is_int($file))
        {
            // Full list is grabbed
            $files = $this->get_files($block);

            // If that position does not exist
            if(!isset($files[$id]))
                return false;

            // Try to del
            if(!@unlink($files[$file]))
            {
                $this->log->error("I cannot delete ". $files[$file] .", check permissions.");
                trigger_error("I cannot delete ". $files[$file] .", check permissions", E_USER_ERROR);
            }
            $this->log->message("File ". $files[$file] ." has been deleted.");
            return true;

        } else if(is_string($file)) {

            // Path
            $path = $this->get_files_path($block);

            // File name
            $file_path = $path . $file;

            // If does not exists
            if(!file_exists($file_path))
                return false;

            // Try to del
            if(!@unlink($file_path))
            {
                $this->log->error("I cannot delete ". $file_path .", check permissions.");
                trigger_error("I cannot delete ". $file_path .", check permissions", E_USER_ERROR);
            }
            $this->log->message("File ". $file_path . " has been deleted.");
            return true; 

        } else {

            trigger_error('A string or integer is expected to delete a file', E_USER_ERROR);
        }
    }
    
    //--------------------------------------------------------

    /**
    * Returns all existing files from a block
    *
    * @param    string    $block
    * @return   array
    */

    public final function get_files($block)
    {
        // Paths
        $files = $this->get_files_path($block);
        
        // Res array
        $res = array();

        // Must be a dir
        if(!is_dir($files)) return $res;

        // Files list
        $file_list = FileSystem::find_files($files);
        
        // Total
        $tot = count($file_list);

        // If no images, empty array is returned
        if($tot == 0) return $res;

        // There u go
        return $file_list;
    }

    //--------------------------------------------------------

    /**
    * Validates a file according a file block
    *
    * @param    string    $block
    * @param    array     $file     Pointer to $_FILES element array
    * @return   bool
    */

    public function validate_file($block, $file)
    {
        // Block must exist
        if(!$this->is_enabled_block($block))
            trigger_error('Reived file block ('. $block .') does not exist');
        
        // Max files amount must be checked here to avoid losing any time
        if($this->config->files->blocks->{$block}->validation->max_files[0] > 0)
        {
            // Get actual files
            $files = $this->get_files($block);

            // If amount reached
            if($this->config->files->blocks->{$block}->validation->max_files[0] < count($files)+1)
            {
                $this->add_validation_error($this->config->files->blocks->{$block}->validation->max_files[1]);
                return;
            }
        }

        // Must be an array
        if(!is_array($file))
            trigger_error('An array is expected to perform the uploaded file validation', E_USER_ERROR);

        // If user did not previously check the file
        if($file['error'] == 4)
            trigger_error('No file has been uploaded, please make sure a file has been selected to upload before validating', E_USER_ERROR);
            
        // If name is empty or any error has ocurred
        if($file['error'] != 0)
        {
            $this->add_validation_error('An unexpected error has ocurred, maybe maximum server file size has been exceeded. Check php and apache configuration.');
            return false;
        }
        
        // Mime-type validation
        if(is_array($this->config->files->blocks->{$block}->validation->valid_mimes[0]))
            if(!in_array($file['type'], $this->config->files->blocks->{$block}->validation->valid_mimes[0]))
                $this->add_validation_error($this->config->files->blocks->{$block}->validation->valid_mimes[1]);

        // Extension validation
        if(is_array($this->config->files->blocks->{$block}->validation->valid_ext[0]))
        {
            preg_match("/\.([a-z]+)$/i", $file['name'], $matches);
            if(!in_array($matches[1], $this->config->files->blocks->{$block}->validation->valid_ext[0]))
                $this->add_validation_error($this->config->files->blocks->{$block}->validation->valid_ext[1]);
        }

        // Size
        if($this->config->files->blocks->{$block}->validation->max_size[0] > 0)
        {
            if($image['size'] > $this->config->files->blocks->{$block}->validation->max_size[0])
                $this->add_validation_error($this->config->files->blocks->{$block}->validation->max_size[1]); 
        }

        // If anything wrong by now, exit
        if($this->validation_failed())
            return false;

        return true;
    }

    //--------------------------------------------------------

    /**
    * Returns a list of enabled file blocks or false if not enabled
    *
    * @return   array
    */

    public final function get_blocks()
    {
        if($this->config->files->enabled == true)
        {
            $blocks = array();
            
            if(is_array($this->config->files->blocks) || is_object($this->config->files->blocks))
            {
                foreach($this->config->files->blocks as $block => $content)
                {
                    $blocks[] = $block;
                }
            }

            return $blocks;
        }

        return false;
    }

    //--------------------------------------------------------

    /**
    * Returns if received block is enabled
    *
    * @param    string    $block
    * @return   bool
    */

    public final function is_enabled_block($block)
    {
        $enabled = $this->get_blocks();

        if(!$enabled) return false;

        if(in_array($block, $enabled)) return true;

        return false;
    }

    //--------------------------------------------------------

    /**
    * Deletes desired file block for current object
    *
    * @param    string    $block
    * @return   bool
    */

    public final function delete_block($block)
    {
        $path = $this->get_files_path($block);
        return FileSystem::delete_tree($path);
    }

    //--------------------------------------------------------

    /**
    * Deletes all file blocks for current object
    *
    * @return    bool
    */

    public final function delete_all_blocks()
    {
        $blocks = $this->get_blocks();

        if(is_array($blocks))
        {
            $path = $this->get_basic_files_path();
            return FileSystem::delete_tree($path);
        }

        return true;
    }
}

?>
