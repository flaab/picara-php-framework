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
* Implements a bunch of image functions to operate with images,
* like thumbnail creation.
*
* @package      Libs
* @author       Arturo Lopez
* @copyright    Copyright (c) 2007-2019, Arturo Lopez
* @version      0.1
*/

class Images extends Pi_error_store
{
    /**
    * Load mode for load class
    */
    public static $load_mode = SINGLETON;

    /**
    * Load name for load class
    */
    public static $load_name = 'images';

    //--------------------------------------------------------

    /**
    * Singleton implementation
    */

    public static function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    //--------------------------------------------------------

    /**
    * Returns the size of any image
    *
    * @param    string    $path
    * @return   array
    */

    public static function get_image_size($path)
    {
        if(!file_exists($path))
            return false;

        $size = getimagesize($path);

        if(!$size)
            trigger_error("$path doesn't seem to be an image", E_USER_ERROR);

        return array('width' => $size[0], 'height' => $size[1], 'html' => $size[3]);
    }

    //--------------------------------------------------------

    /**
    * Creates a thumbnail from a original image and two new dimension parameters.
    * The thumbnail will lose his aspect ratio if proportions do not fit.
    *
    * @param    string    $name        Path to original image
    * @param    string    $filename    Path to image destination    Extension will be added or replaced if needed
    * @param    string    $type        squared or normal
    * @param    int       $new_w       New width                    If not present, it will be calculated to maintain aspect ratio
    * @param    int       $new_h       New height                   If not present, it will be calculated to maintain aspect ratio
    * @param    int       $min_w       Min width                    If not present, it will be calculated to maintain aspect ratio
    * @param    int       $min_h       Min height                   If not present, it will be calculated to maintain aspect ratio
    * @param    int       $quality     Quality of the image         If not present, 90 is assumed
    * @param    string    $watermark   Watermark text to add to image
    * @return   bool
    */

    public static function create_thumb($name, $filename, $type, $new_w = 0, $new_h = 0, $min_w = 0, $min_h = 0, $quality = 90, $watermark = NULL)
    {
        // I need at least one parameter
        if($new_w == 0 && $new_h == 0)
            trigger_error('I need a width or height of the image to resize', E_USER_ERROR);
        
        // To capture extension
        $system=explode(".",$name);
        $mime = mime_content_type($name);

        // File extension
        $ext = "";
        
        // If png
        if(preg_match("/\png/i", $mime))
        {
            $src_img = imagecreatefrompng($name);
            $ext = "png";
        
        // Jpg
        }elseif(preg_match("/jpe?g/i", $mime)){
            $src_img = imagecreatefromjpeg($name);
            $ext = "jpg";
            
            // Gif
        } elseif(preg_match("/gif/i", $mime)){
            $src_img = imagecreatefromgif($name);
            $ext = "gif";

        // Unrecognized    
        } else {
            trigger_error("Submitted image must be gif, jpg or png.", E_USER_ERROR);
        } 
        
        //
        // Dimensions for thumbs or resize
        // Two ways: normal and squared.
        //

        // Nitty gritty stuff
        $old_x=imageSX($src_img);
        $old_y=imageSY($src_img);
        
        // If mode is squared, we must check the real size and make the proper calculations
        // To make de resize how it should. Squared means a virtual square of X*Y must not be exceeded.
        if($type == 'squared')
        {
            // If horizontal
            if($old_x > $old_y)
            {
                $new_h = 0;
            } elseif($old_y > $old_x) {
                $new_w = 0;
            }
        }

        // Calculate new aspect ratio if any of them are zero.
        if($new_h == 0)
            $new_h = ($old_y * $new_w) / $old_x;

        // From the other
        if($new_w == 0)
            $new_w = ($old_x * $new_h) / $old_y;
        
        // If normal, this block of code must be executed
        // Check if width and height requirements are allright
        // If not, make sure the are by changing the thumbnails values
        if($type == 'normal')
        {
            if($new_w < $min_w)
            {
                // New width and height
                $new_h = ($min_w * $new_h) / $new_w;
                $new_w = $min_w;

            } else if($new_h < $min_h) {
                
                // New width and height
                $new_w = ($min_h * $new_w) / $new_h;
                $new_h = $min_h;
            }
        }

        // Create image
        $dst_img=ImageCreateTrueColor($new_w,$new_h);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_w, $new_h, $old_x, $old_y);
        
        // Watermark if needed 
        if(!is_null($watermark))
        {
            $dst_img = self::watermark($dst_img, $watermark);
        }

        // Change extension of file if needed
        $final_filename = preg_replace("/\.[a-zA-Z]{3,4}$/", '.'. $ext, $filename);

        // Create same format as submitted
        if(preg_match("/png/i",$mime)) {
            $res = @imagepng($dst_img, $final_filename);
        } elseif(preg_match("/jpe?g/i",$mime)){
            $res = @imagejpeg($dst_img, $final_filename, $quality);
        } elseif(preg_match("/gif/i",$mime)) {
            $res = @imagegif($dst_img, $final_filename);
        } else {
            trigger_error("Submitted image must be gif, jpg or png.", E_USER_ERROR);
            return(false);    
        }

        // Destruir
        imagedestroy($dst_img);
        imagedestroy($src_img);

        // Respuesta
        return $res;
    }

    /**
    * Crea el watermark para esta imagen a partir del fichero de configuracion
    *
    * @param    image    $dts_image
    * @param    object   $watermark
    * @return   image
    */

    protected function watermark($dst_img, $watermark)
    {
        // color de encima y sombra
        $color = imagecolorallocatealpha($dst_img, 255, 255, 255, 20);
        $black = imagecolorallocatealpha($dst_img, 0, 0, 0, 20);
        
        // cojo dimensiones de mi caja para sacar el centro
        $rect = ImageTTFBbox($watermark->fontsize, $watermark->angle, $watermark->ttf, $watermark->text);
        
        // movidas para sacar el ancho, alto, etc
        $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
        $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));
        
        // dimensiones 
        $dimm = array(
                "left"   => abs($minX),
                "top"    => abs($minY),
                "width"  => $maxX - $minX,
                "height" => $maxY - $minY,
                "box"    => $rect
        );
        

        // creamos fondo
        ImageTTFText($dst_img, 
                     $watermark->fontsize, 
                     $watermark->angle, 
                     (imagesx($dst_img) - $dimm['width']) / 2, 
                     imagesy($dst_img) - $watermark->offset, 
                     $black, 
                     $watermark->ttf, 
                     $watermark->text);
         
        // texto superior
        ImageTTFText ($dst_img, 
                      $watermark->fontsize, 
                      $awatermark->ngle, 
                      (imagesx($dst_img) - $dimm['width']) / 2 - $watermark->shadow, 
                      imagesy($dst_img) - $watermark->offset - $watermark->shadow,
                      $color,
                      $watermark->ttf, 
                      $watermark->text);
        
        return $dst_img;
    }
}
?>
