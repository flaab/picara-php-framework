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
 * Implements the scaffold to the application
 *
 * @package System 
 * @author Arturo Lopez
 * @copyright Copyright (c) 2008-2019, Arturo Lopez
 */
 
class Pi_scaffold
{
    /**
    * Mysql datetime fields
    */
    private static $mysql = array('datetime' => array('datetime','timestamp'));
   
    //--------------------------------------------------------
   
   /**
    * Performs the complete scaffolding process for the given model
    *
    * @param    string    $model 
    * @param    string    $action 
    * @param    int       $id 
    * @param    array     $data
    */

    public static function createform($model, $action = "insert", $id = NULL, $data = NULL)
    {
        // Metadata instance
        $metadata = Pi_metadata::singleton();

        // Model config must be readed to know if a main image is enabled
        $config = $metadata->config->get($model);

        // Scaffolding schema
        $scaffold = $metadata->read_scaffold_schema($model, $id);
        
        // Change local schema according to scaffold config for this model
        if(isset($config->scaffold))
        {
            // Text Input
            if(is_array($config->scaffold->text))
            {
                for($i = 0; $i < count($config->scaffold->text); $i++)
                {
                    if(isset($scaffold['Data'][$config->scaffold->text[$i]]))
                    {
                        $scaffold['Data'][$config->scaffold->text[$i]]['metatype'] = STRING; 
                    }
                }
            }

            // Text Area
            if(is_array($config->scaffold->textarea))
            {
                for($i = 0; $i < count($config->scaffold->textarea); $i++)
                {
                    if(isset($scaffold['Data'][$config->scaffold->textarea[$i]]))
                    {
                        $scaffold['Data'][$config->scaffold->textarea[$i]]['metatype'] = TEXT; 
                    }
                }
            }

            // Fulltext
            if(is_array($config->scaffold->fulltext))
            {
                for($i = 0; $i < count($config->scaffold->fulltext); $i++)
                {
                    if(isset($scaffold['Data'][$config->scaffold->fulltext[$i]]))
                    {
                        $scaffold['Data'][$config->scaffold->fulltext[$i]]['metatype'] = FULLTEXT; 
                    }
                }
            }
        }

        // Define POST action
        $post_url = 'scaffold-'. strtolower($model) .'/'. $action;
       
        // Append received id to the POST action
        if($id != NULL)    $post_url = $post_url .'/'. $id;
        
        //--------------------------------------------------------
        // Capture default values from post or controller
        //--------------------------------------------------------
        
        if(!isset($data) && isset($_POST['model']))
            $data = $_POST['model'];        
        
        //--------------------------------------------------------
        // Print form
        //--------------------------------------------------------
        
        // Multipart form header, cause main image can also be sent
        print('<form method="POST" action="'. $post_url .'" enctype="multipart/form-data">');
        
        // Foreach fieldset
        foreach($scaffold as $fieldset => $fields)
        {
            if(count($fields))
            {
                // Fieldset start
                print("<fieldset class=\"border p-4\">\n");
                
                // Print legend
                print('<legend class="w-auto">'. $fieldset ."</legend>\n");

                // Foreach field
                foreach($fields as $field => $value)
                {  
                    // Contained data must not be empty
                    if(isset($value) > 0 && !is_null($value))
                    {
                        print('<div class="form-group row">'); 

                        // Fieldset determines what to do
                        switch($fieldset)
                        {
                            // Foreign key
                            case 'Belongings':
                                
                                // Label
                                print('<label for="input'. $field .'" class="col-sm-2 col-form-label">'. ucfirst($field) .'</label>');
                                
                                // Display form select
                                print('<div class="col-sm-10">');
                                self::displayFkMenu($value, $data[$value]); 
                                print('</div>');
                                
                                // Line break
                                print("\n");

                            break;

                            // Standard field
                            case 'Data':
                                
                                // Label
                                print('<label for="input'. $field .'" class="col-sm-2 col-form-label">'. ucfirst($field) ."</label>\n");
                                print('<div class="col-sm-10">');
                                
                                // Field meta-type determines what to do
                                switch($value['metatype'])
                                {
                                    // Enum    
                                    case ENUM:  Form::createEnumMenu($value['enums'], "model[$field]", $data[$field]); print("\n"); break;                  
                                    // Text
                                    case TEXT:  Form::createTextArea("model[$field]", $data[$field]); print("\n"); break;    

                                    case FULLTEXT: Form::createFullTextArea("model[$field]", $data[$field]); print("\n"); break;

                                    // Date
                                    case DATE:  Form::createDateForm("model[$field]", $data[$field], false); print("\n"); break;
                                    
                                    // Datetime
                                    case TIMESTAMP: 
                                    
                                        // MySql date and timestamp
                                        if(in_array($value['type'], self::$mysql['datetime']))
                                        {
                                            Form::createDatetimeForm("model[$field]", $data[$field]);
                                            break;
                                        }

                                        // If only time
                                        if($value['type'] == 'time')
                                        {
                                            Form::createTimeForm("model[$field]", $data[$field]);
                                            break;
                                        }
                                    
                                    break;

                                    // String and all other type
                                    default: Form::createTextInput("model[$field]",$data[$field], $value['max_length']); print("\n");                
                                }
                                print('</div>');
                                

                            break;

                            // Associations
                            case 'Associations':
                                
                                // Foreach related model
                                foreach($scaffold['Associations'][$field] as $assoc_model => $display)
                                {
                                    // Grab display name
                                    $display_name = $metadata->config->read($assoc_model,'display');
                                     
                                    // If elements are not empty
                                    if(!empty($display))
                                    {
                                        print('<label for="input'. $field .'" class="col-sm-2 col-form-label">'. $display_name .'</label>');
                                        print('<div class="col-sm-10">');
                                        Form::displayMultiselect('related['. $field .']['. $assoc_model .']', $display);
                                        print('</div>');
                                        print("\n");
                                    }
                                }

                            break;
                        }
                    }
                    print("</div>");
                }

                // Fieldset end
                print("</fieldset>\n");
                
            }
        }

        //--------------------------------------------------------
        // Associated images processing
        //--------------------------------------------------------

        // If config says so, print out the input
        if($config->images->main->enabled == true)
        {
            // Fieldset
            print("<fieldset class=\"border p-4\">\n");
            print("<legend  class=\"w-auto\">Main Image</legend>\n");


            // If object exists, thumb image should be displayed
            if(is_numeric($id))
            {
                // Display existing one
                $obj = new $model($id);

                // Obtain main image
                $img = $obj->get_main_image();
                
                // Display image and delete checkbox
                if($img != false)
                {
                    print('<img src="'. $img['image'] .'" class="img-thumbnail" width="250"></a><br />');
                    Form::createCheckbox(DELETE_MAIN_IMAGE);
                    print(' Delete');
                    print('<br />');
                } else {
                    // Input output
                    Form::createFileInput(MAIN_IMAGE_INPUT_NAME);
                    print("<br />");
                }
            
            // New insertion
            } else {

                // Input output
                Form::createFileInput(MAIN_IMAGE_INPUT_NAME);
                print("<br />");
            }


            print("</fieldset>\n");
        }       


        //------------------------------------------------------
        
        print("<br />");
        Form::createSubmitButton('submit','Submit');
        print("</form>\n");
    }    
    
    //--------------------------------------------------------
    
    /**
    * Prints a foreign key drop-down menu using the first string field as display name
    *
    * @param string $field
    * @param string $value Default value
    */
    
    public static function displayFkMenu($field, $value = NULL)
    {
        if($field == NULL)
        {
            trigger_error("There is no string field in the given model to create a select menu", E_USER_ERROR);        
        
        } else {
            
            $metadata = Pi_metadata::singleton();
            $model = $metadata->get_model_from_table(preg_replace(FK_REGEX, '', $field));
            $first_string = $metadata->get_first_string_field($model);
            Form::createSelectMenu($model, $first_string,"model[$field]", $value, true);
        }
    }    
    
    //----------------------------------------------------------
    
    /**
    * Process an array representing an iso date
    *
    * @param    array   $data
    * @return   array 
    */
    
    public static function process($data, $separator = '-')
    {
        foreach($data as $key => $value)
        {
            if(is_array($value))
            {
                if(count($value) == 3)
                {
                    $keys = array_keys($value);
                    if(in_array('hour', $keys)) $separator = ':'; else $separator = '-';

                    $data[$key] = self::implode_integers($value, $separator);

                } else if(count($value) == 6) {

                    $data[$key] = self::process_stamp($value);

                }
            }
        }
        return $data;
    }
    
    //--------------------------------------------------------

    /**
    * Compacts an integer array converting it into a string
    * and adding initial zeros.
    *
    * @param    array    $value
    * @param    string   $separator
    */

    public static function implode_integers($value, $separator = '-')
    {
        foreach($value as $index => $number)
            if(strlen($number) < 2)
                $value[$index] = '0'.$number;
        
        return implode($separator, $value);
    }

    //--------------------------------------------------------

    /**
    * Process an array representing an iso timestamp
    *
    * @param    array    $data
    */

    public static function process_stamp($data)
    {
        $date = array($data['year'], $data['month'], $data['day']);
        $time = array($data['hour'], $data['minute'], $data['second']);
        $cadena = self::implode_integers($date);
        $cadena .= ' ';
        $cadena .= self::implode_integers($time, ':');
        return $cadena;
    }
}
?>
