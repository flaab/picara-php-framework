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
* Implements the exportation of models to other formats or data types.
* Even the class is pretty simple right now, it should be getting
* more complicated with future versions.
*
* @package    System
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/

abstract class Pi_exportable extends Pi_callbacks
{
    //===========================================================
    // Xml
    //===========================================================
    
    /**
    * Converts the object fields into xml
    *
    * @return    string    
    */

    public final function toXml()
    {
         $class = $this->my_class;
         
         $xml = "<$class>\n";
         
         foreach($this->fields as $key => $value)
         {
             $xml .= "  <$key>";
             $xml .= $value;
             $xml .= "</$key>\n";
         }
         
         $xml .= "</$class>\n";
         
         return $xml;
    }

    //===========================================================
    // Csv
    //===========================================================
    
    /**
    * Converts object to csv
    *
    * @return    string
    */
    
    public final function toCsv()
    {
        foreach($this->fields as $key => $value)
        {
            $array[$key] = '"'. $value .'"';
        }
        
        $csv = implode(';', $array);

        return $csv . "\n";
    }

    //===========================================================
    // Yaml
    //===========================================================

    /**
    * Exports object fields to Yaml
    *
    * @return   string
    */

    public final function toYaml()
    {
        $yml = yaml_emit($this->toArray());
        return $yml;
    }

    //===========================================================
    // To JSON
    //===========================================================
    
    public final function toJson()
    {
        return(json_encode($this->fields));
    }

    //===========================================================
    // Array
    //===========================================================
    
    /**
    * Returns an array with the object fields and values
    *
    * @return   array
    */
    
    public final function toArray()
    {
        foreach($this->fields as $key => $value)
        {
           $array[$key] = $value;
        }
        return $array;
    }

    //===========================================================
    // String
    //===========================================================
    
    /**
    * Returns a string representing the object
    *
    * @return   string
    */
     
    public final function toString()
    {
        // Show fields
        $string = $this->my_class ." Object";
        $myFields = $this->metadata->read_columns($this->my_class);
        $string .= LINE_BREAK ."---";
         
         
        foreach($myFields as $key)
        {
            $string .= LINE_BREAK .'(' . $key . ') => ' . $this->fields->$key;
        }
          
        // Show relationships
        $relationships = $this->getRelatedModels();
        $string .= LINE_BREAK ."--- ". LINE_BREAK ." Relationships ". LINE_BREAK ."---";
        
        if(count($relationships) == 0)
        {
            $string .= LINE_BREAK ."None";
        } else {
          
            foreach($relationships as $key)
            {
                $string .= LINE_BREAK .'(' . $key . ') => ' . $this->getRelationshipFK($key);
            }
        }
          
        // Show relationships
        $errors = $this->getErrorStore();
        $string .= LINE_BREAK ."--- ". LINE_BREAK ." Error Store ". LINE_BREAK ."---";
          
        if(count($errors) == 0)
        {
            $string .= LINE_BREAK ."None";
        } else {
          
            foreach($errors as $key => $value)
            {
                $string .= LINE_BREAK .'(' . $key . ') => ' . $value;
            }
        }

        $string .= LINE_BREAK;
        return $string;
    }

    //--------------------------------------------------------

    /**
    * Functional alias to toString
    *
    * @return   string
    */

    public final function __toString()
    {
        return $this->toString();     
    }
}

?>
