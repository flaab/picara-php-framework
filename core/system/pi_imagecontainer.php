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
* Implements the image container functionality for all models.
* If properly specified in the model configuration file, a set
* of images with thumbnails can be associated to each
* object.
*
* @package    System
* @author     Arturo Lopez 
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

abstract class Pi_imagecontainer extends Pi_filecontainer
{
    /**
    * Valid image extensions
    */
    private $valid_ext = array('jpg','jpeg','JPG','JPEG','PNG','png','GIF','gif');

    /**
    * Valid image mime-types
    */
    private $valid_mimes = array('image/jpeg','image/pjpeg','image/png','image/gif');
    
    //--------------------------------------------------------

    /**
    * Returns the complete path to the images directory for this object
    *
    * @return   string
    */
    public final function get_image_path()
    {
        // If no represented row yet, error
        if(!is_numeric($this->fields->id))
            trigger_error('Images for unknown objects cannot be managed', E_USER_ERROR);

        return MODEL_FILES . $this->my_class . '/' . MODEL_IMAGES . $this->fields->id . '/';        
    }

    //--------------------------------------------------------

    /**
    * Validates the image storing messages in the object error store
    *
    * @param    array    $image    Pointer to a $_FILES element to validate
    * @return   bool
    */

    public final function validate_image($image)
    {
        // Must be an array
        if(!is_array($image))
            trigger_error('An array is expected to perform the uploaded image validation', E_USER_ERROR);

        // If user did not previously check the file
        if($image['error'] == 4)
            trigger_error('No image was selected to upload, please make sure a file has been selected to upload before validating', E_USER_ERROR);
            
        // If name is empty or any error has ocurred
        if($image['error'] != 0)
        {
            $this->add_validation_error('An unexpected error has ocurred, maybe maximum file size has been exceeded. Check php and apache configuration.');
            return false;
        }
        
        // Mime-type validation
        if(!in_array($image['type'], $this->valid_mimes))
            $this->add_validation_error($this->config->images->messages->wrong_mime);

        // Extension validation
        preg_match("/\.([a-z]+)$/i", $image['name'], $matches);
        if(!in_array($matches[1], $this->valid_ext))
            $this->add_validation_error($this->config->images->messages->wrong_ext);

        // If anything wrong by now, exit
        if($this->validation_failed())
            return false;

        //===========================================================
        // Size, widht and heigh validation
        //===========================================================

        // Size
        if($this->config->images->validation->max_size[0] > 0)
        {
            if($image['size'] > $this->config->images->validation->max_size[0])
                $this->add_validation_error($this->config->images->validation->max_size[1]); 
        }

        // Grep image width and height to validate if neccesary
        $res = Images::get_image_size($image['tmp_name']);
       
        // Max width
        if($this->config->images->validation->max_width[0] > 0)
        {
            if($res['width'] > $this->config->images->validation->max_width[0])
                $this->add_validation_error($this->config->images->validation->max_width[1]); 
        }

        // Max height
        if($this->config->images->validation->max_height[0] > 0)
        {
            if($res['height'] > $this->config->images->validation->max_height[0])
                $this->add_validation_error($this->config->images->validation->max_height[1]); 
        }

        // Min width
        if($this->config->images->validation->min_width[0] > 0)
        {
            if($res['width'] < $this->config->images->validation->min_width[0])
                $this->add_validation_error($this->config->images->validation->min_width[1]); 
        }

        // Min height
        if($this->config->images->validation->min_height[0] > 0)
        {
            if($res['height'] < $this->config->images->validation->min_height[0])
                $this->add_validation_error($this->config->images->validation->min_height[1]); 
        }

        // Return
        if($this->validation_failed())
            return false;

        return true;
    }

    //--------------------------------------------------------

    /**
    * Stores given main image
    *
    * @param    array    $image    Pointer to $_FILES element to store
    * @return   bool
    */

    public final function add_main_image($image)
    {
        // Get ext
        $ext = $this->get_suitable_ext($image['tmp_name']);
        if(!$ext) return(false);
        
        // Image name
        $main_image_name = MAIN_IMAGE .'.'. $ext;

        // Calculate path
        if($this->validate_image($image))
        {
            return $this->store_image($image['tmp_name'], $main_image_name);
        }
        return false;
    }

    //--------------------------------------------------------

    /**
    * Returns wether this object has a main image or not
    *
    * @return   bool
    */

    public final function has_main_image()
    {
        // Image path
        $path = $this->get_image_path(); 

        // Image paths
        $image_path1 = $path . MAIN_IMAGE .'.jpg';
        $image_path2 = $path . MAIN_IMAGE .'.png';
        $image_path3 = $path . MAIN_IMAGE .'.gif';

        if(is_file($image_path1) ||is_file($image_path2) ||file_exists($image_path3))
            return true;

        return false;
    }

    //--------------------------------------------------------
    
    /**
    * Deletes main image and thumbnail. Please note an error
    * will be thrown if no permissions, so if false is returned
    * means the object did not have a main image.
    *
    * @return bool
    */

    public final function delete_main_image()
    {
        $main = $this->get_main_image();

        foreach($main as $img)
        {
            if(!@unlink($img))
            {
                $this->log->message('I cannot delete '. $image_path .', check permissions.');
                trigger_error('I cannot delete '. $image_path .', check permissions', E_USER_ERROR);
                return(false);
            }
            
            // Log
            $this->log->message("Image ". $img ." deleted succesfully.");
        }    
        
        // Exec
        return(true);
    }

    //--------------------------------------------------------

    /**
    * Deletes an extra image from its name or number in the row.
    * False is returned if image does not exist.
    *
    * @param    string|int    $id
    * @return   bool
    */

    public final function delete_extra_image($id)
    {
        // If it is an integer
        if(is_int($id))
        {
            // Full list is grabbed
            $extra = $this->get_extra_images();

            // If that position does not exist
            if(!isset($extra[$id]))
                return false;

            // Obtain image name
            $image_name = preg_replace("/^([^\/]+\/)+/", '', $extra[$id]['image']);

            return $this->delete_image($image_name);

        } else if(is_string($id)) {

            // Path to image
            $full_path = $this->get_image_path() . $id;

            // If does not exist, false
            if(!file_exists($full_path))
                return false;

            return $this->delete_image($id);

        } else {

            trigger_error('A string or an integer is expected to delete an extra image', E_USER_ERROR);

        }
    }

    //--------------------------------------------------------

    /**
    * Stores given extra image
    *
    * @param    array    $image    Pointer to $_FILES element to store
    * @return   bool
    */

    public final function add_extra_image($image)
    {
        // If max amount of extra images is reached, there is no need to go any further
        if($this->config->images->extra->max_images > 0)
        {
            $extra = $this->get_extra_images();
            if($this->config->images->extra->max_images < count($extra)+1)
            {
                $this->add_validation_error($this->config->images->messages->max_images);
                return false;
            }
        }
        
        // Image exists and is valid
        if($this->validate_image($image))
        {
            // Get suitable extension according to mime, not submission
            $ext = $this->get_suitable_ext($image['tmp_name']);
            if(!$ext) return(false);

            // If extra images must be renamed
            if($this->config->images->extra->rename == true)
            {
                // New name must be created to avoid repeated names for concurrent use
                $name = substr(md5(microtime() . rand(1,1000)), 0, 8) .'.'. $ext; 
            
            } else {
                
                // File name must preserve original name but proper extension
                $name = preg_replace("/\.[a-zA-Z]{3,4}$/", '.'. $ext, $image['name']);
            }
            
            // New path for image
            $new_path = $this->get_image_path() . $name;

            return $this->store_image($image['tmp_name'], $name);
        }
        return false;
    }
    
    //--------------------------------------------------------

      
    //--------------------------------------------------------

    /**
    * Returns the main image for this model or false
    *
    * @return   array
    */

    public final function get_main_image()
    {   
        // Possible paths to main image
        $path1 = $this->get_image_path() . MAIN_IMAGE . '.jpg';
        $path2 = $this->get_image_path() . MAIN_IMAGE . '.png';
        $path3 = $this->get_image_path() . MAIN_IMAGE . '.gif';

        // Possible paths to thumb
        $mini_path1 = $this->get_image_path() . MODEL_THUMBS . MAIN_IMAGE . '.jpg';  
        $mini_path2 = $this->get_image_path() . MODEL_THUMBS . MAIN_IMAGE . '.png';  
        $mini_path3 = $this->get_image_path() . MODEL_THUMBS . MAIN_IMAGE . '.gif';  
        
        // Final ones
        $final = array('thumb' => false, 'image' => false);

        // Check which one is it
        if(file_exists($path1)) $final['image'] = $path1;
        if(file_exists($path2)) $final['image'] = $path2;
        if(file_exists($path3)) $final['image'] = $path3;
        if(file_exists($mini_path1)) $final['thumb'] = $mini_path1;
        if(file_exists($mini_path2)) $final['thumb'] = $mini_path2;
        if(file_exists($mini_path3)) $final['thumb'] = $mini_path3;

        // If isset
        if($final['image'] || $final['thumb']) return($final);

        // If a default image has been set up in config
        if(is_string($this->config->images->main->default))
        {
            return array('thumb' => $this->config->images->main->default, 'image' => $this->config->images->main->default);

        } else {

            // Should be false
            return false;
        }
    }
    
    //--------------------------------------------------------
    
    /**
     * Returns suitable extension for this image
     * @param   string  $path
     */

    private final function get_suitable_ext($path)
    {
        // No file to read type?
        if(!file_exists($path)) return(false);

        // Mime type
        $mime = mime_content_type($path);

         // File extension
        $ext = "";
        
        // If png
        if(preg_match("/\png/i", $mime))
        {
            $ext = "png";
        }elseif(preg_match("/jpe?g/i", $mime)){
            $ext = "jpg";
        } elseif(preg_match("/gif/i", $mime)){
            $ext = "gif";
        } else {
            trigger_error("Parameter must be gif, jpg or png.", E_USER_ERROR);
            return(false);
        }
        return($ext);
    }

    //--------------------------------------------------------

    /**
    * Returns extra images for this model
    *
    * @return   array
    */

    public final function get_extra_images()
    {
        // Get main image
        $main  = $this->get_main_image();
        
        // Paths
        $images = $this->get_image_path();
        $thumbs = $images . MODEL_THUMBS;
        
        // Res array
        $res = array();

        // If thumbs dir does not exist
        if(!is_dir($thumbs)) return $res;

        // Files list
        $images_files = FileSystem::find_files($images, "/\.(jpg|png|gif)$/");
        $thumbs_files = FileSystem::find_files($thumbs, "/\.(jpg|png|gif)$/");

        // Total
        $tot_images = count($images_files);

        // If no images, empty array is returned
        if($tot_images == 0) return $res;

        // Must match
        if($tot_images != count($thumbs_files))
        {
            $msg = 'Number of images and thumbnails mismatch for '. $this->my_class .'('. $this->fields->id .')';
            trigger_error($msg, E_USER_WARNING);
            $this->log->warning($msg);
        }
        
        // Result creation
        for($it = 0; $it < $tot_images; $it++)
        {
            // Default image should not be included
            if($main['image'] != $images_files[$it])
            {
                $res[] = array(
                    'image' => $images_files[$it],
                    'thumb' => $thumbs_files[$it]
                );
            }
        }

        return $res;
    }

    //--------------------------------------------------------
    
    /**
    * Creates the directory tree to store images for this model
    */

    protected final function create_image_tree()
    {
        // Full directory structure for images and thumbs
        $path = $this->get_image_path() . MODEL_THUMBS; 
    
        // Creation attemp or die
        if(!is_dir($path))
            if(!FileSystem::create_tree($path)) 
                trigger_error('I cannot create '. $path .', please check permissions', E_USER_ERROR);
    }

    //--------------------------------------------------------
    
    /**
    * Deletes the directory tree for this model
    */

    protected final function delete_image_tree()
    {
        // Full directory structure for images and thumbs
        $path = $this->get_image_path(); 
        
        // Delete if exists
        if(is_dir($path))
            if(!FileSystem::delete_tree($path)) 
                trigger_error('I cannot delete '. $path .', please check permissions', E_USER_ERROR);
    }

    //--------------------------------------------------------

    /**
    * Deletes given image and thumbnail
    *
    * @param    string    $name
    * @return   bool
    */

    private final function delete_image($name)
    {
        // Image path
        $path = $this->get_image_path(); 

        // Image path
        $image_path = $path . $name;

        // Thumb path
        $thumb_path = $path . MODEL_THUMBS . $name;

        // Unlink image
        if(!@unlink($image_path))
        {
            $this->log->error('I cannot delete '. $image_path .', check permissions.');
            trigger_error('I cannot delete '. $image_path .', check permissions', E_USER_ERROR);
        }
        
        // Unlink thumb
        if(!@unlink($thumb_path))
        {
            $this->log->error('I cannot delete '. $thumb_path .', check permissions.');
            trigger_error('I cannot delete '. $thumb_path .', check permissions', E_USER_ERROR);
        }
        return true;
    }

    //--------------------------------------------------------

    /**
    * Stores given image with desired name
    *
    * @param    string   $image    Path to uploaded image to move
    * @param    string   $name     Example: main.jpg or 12345678.jpg 
    * @return   bool
    */

    private final function store_image($image, $name)
    {
        // Path must exist
        $this->create_image_tree();

        // Base dir
        $base_path = $this->get_image_path();

        // Mime type
        $mime = mime_content_type($image);

        // File extension
        $ext = $this->get_suitable_ext($image);
        if(!$ext) return(false);

        // Adjust name using extension 
        $name = preg_replace("/\.[a-zA-Z]{3,4}$/", '.'. $ext, $name);
        
        // New path
        $new_path = $base_path . $name;
        
        // Thumb full path
        $thumb_path = $base_path . MODEL_THUMBS . $name;

        // Create it or delete original file
        if(!Images::create_thumb($image, $thumb_path,
                            $this->config->images->thumbnails->type,
                            $this->config->images->thumbnails->width, 
                            $this->config->images->thumbnails->height,
                            $this->config->images->thumbnails->min_width,
                            $this->config->images->thumbnails->min_height,
                            $this->config->images->thumbnails->quality))
        {
            unlink($new_path);
            $this->log->error('Unexpected error creating thumbnail file, '. $new_path .' has been deleted.');
            trigger_error('Unexpected error creating thumbnail file, '. $new_path .' has been deleted', E_USER_ERROR);
        }
        
        // If original resize is activated, then we resize the original image
        if($this->config->images->resize->enabled)
        {
            if(!Images::create_thumb($image, $new_path,
                                    $this->config->images->resize->type,
                                    $this->config->images->resize->width, 
                                    $this->config->images->resize->height, 
                                    $this->config->images->resize->min_width,
                                    $this->config->images->resize->min_height,
                                    $this->config->images->resize->quality,
                                    $this->config->images->resize->watermark))
            {
                unlink($new_path);
                $this->log->error('Unexpected error creating target image file, '. $new_path .' has been deleted.');
                trigger_error('Unexpected error creating target image file, '. $new_path .' has been deleted', E_USER_ERROR);
            }
        
            // Ok, we should delete original image
            @unlink($image);
        
        // No resize
        } else {
        
            // Moves the image
            if(!move_uploaded_file($image, $new_path))
            {
                $this->log->error('I cannot write '. $new_path .', check permissions.');
                trigger_error('I cannot write '. $new_path .', check permissions', E_USER_ERROR);
            }
        }
        
        // Log
        $this->log->message('Image '. $new_path .' created.');

        // OOK
        return true;
    }
}
?>
