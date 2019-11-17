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
* Creates dinamic forms for certain data types. Users might use some of these functions,
* but they are mainly used by the scaffolding process.
*
* @package    Libs
* @author     Arturo Lopez
* @copyright  Copyright (c) 2008-2019, Arturo Lopez
* @version    0.1
*/
 class Form
 {
     /**
      * Displays 3 drop down comboboxes in order to create a date input form
      *
      * @param    string    $varname    Name of the post variable to be sent
      * @param    mixed     $value      
      * @param    bool      $past       Indicates if the date must be past or not
      */
      public static function createDateForm($param, $value = NULL, $past = false)
      {
          /*
          * Set up
          */
          $days = 31;
          $months = 12;
          $firstYear = date("Y") - 100;
          
          /*
          * Default values
          */
          if(is_array($value))
          {
              foreach($value as $number)
                  $date[] = $number;
          } else {
          
              $date = explode('-', $value);
          }
          
          if($past == true)
          {
              $increment = 100;
          } else {
              $increment = 150;
          }
          $years = $firstYear + $increment;
          ?>
          
          <select name="<?= $param ?>[year]">
              <?php
                  for($it = $years; $it >= $firstYear; $it--)
                  {
                      if($it == $date[0]) $selected = 'selected'; else $selected = '';
                      echo "<option value='$it' $selected>$it&nbsp;</option>";
                  }
              ?>
          </select>
          <select name="<?= $param ?>[month]">
              <?php
                  for($it = 1; $it <= $months; $it++)
                  {
                      if($it < 10) $it = '0' . $it;
                      if($it == $date[1]) $selected = 'selected'; else $selected = '';
                      echo "<option value='$it' $selected>$it&nbsp;</option>";
                  }
              ?>
          </select>
          <select name="<?= $param ?>[day]">
              <?php
                  for($it = 1; $it <= $days; $it++)
                  {
                      if($it < 10) $it = '0' . $it;
                      if($it == $date[2]) $selected = 'selected'; else $selected = '';
                      echo "<option value='$it' $selected>$it&nbsp;</option>";
                  }
              ?>
          </select>
          
          <?php
      }
      
     //--------------------------------------------------------
     
     /**
      * Displays 3 comboboxes to create an hour selector
      *
      * @param    string    $varname    Name of the post variable to be sent
      * @param    mixed     $value      
      */
      
      public static function createTimeForm($param, $value = NULL)
      {
          /*
          * Default values
          */
          if(is_array($value))
          {
              foreach($value as $number)
                  $date[] = $number;

          } else {
          
              $date = explode(':', $value);
          }
          
          ?>
          
          <select name="<?= $param ?>[hour]">
              <?php
                  for($it = 0; $it <= 23; $it++)
                  {
                      if($it == $date[0]) $selected = 'selected'; else $selected = '';
                      echo "<option value='$it' $selected>$it&nbsp;</option>";
                  }
              ?>
          </select>
          <select name="<?= $param ?>[minute]">
              <?php
                  for($it = 0; $it <= 59; $it++)
                  {
                      if($it == $date[1]) $selected = 'selected'; else $selected = '';
                      echo "<option value='$it' $selected>$it&nbsp;</option>";
                  }
              ?>
          </select>
          <select name="<?= $param ?>[second]">
              <?php
                  for($it = 0; $it <= 59; $it++)
                  {
                      if($it == $date[2]) $selected = 'selected'; else $selected = '';
                      echo "<option value='$it' $selected>$it&nbsp;</option>";
                  }
              ?>
          </select>
          
          <?php
     
      }

      //--------------------------------------------------------

      /**
      * Creates a datetime form for a datetime field
      *
      * @param    string    $param
      * @param    string    $value
      */

      public static function createDatetimeForm($param, $value = NULL)
      {
          if($value != NULL)
          {
              $res = explode(' ', $value); 
          }

          self::createDateForm($param, $res[0]);
          self::createTimeForm($param, $res[1]);
      }

      //----------------------------------------------------------
      
      /**
      * Creates a human understandable dropdown menu to select a foreign-key
      *
      * @param    string     $model    Model to display
      * @param    string     $field    Field content to display in the dropdown options
      * @param    string     $select   Name Name of the post var to be sent
      * @param    mixed      $value    Default value to be shown
      */

      public static function createSelectMenu($model, $field, $selectName, $value = NULL, $shownull = false, $class="form-control")
      {
          $query = Pi_query::singleton();
          $function = 'getAll'. $model .'OrderBy'. $field;
          $elements = $query->$function($model);
        
          if($elements != NULL)
          {
              echo '<select name="'. $selectName .'" class="'. $class .'">';
              if($shownull == true) echo "<option value=\"NULL\">- - - - </option>";

                  foreach($elements as $element)
                  {
                      if($value == $element->fields->id) { $selected = "selected"; } else { $selected = ""; }
                      echo "<option value='". $element->fields->id ."' $selected>". stripslashes($element->fields->$field) ."</option>";
                  }
          
              echo "</select>";
              
          } else {
          
              trigger_error("No entries for related model $model in the database, cannot create input", E_USER_WARNING);
          }
          
          unset($query);
      }
      
      //----------------------------------------------------------
      
      /**
      * Creates a text input field
      *
      * @param    string    $param        Name of the post var to be sent
      * @param    mixed     $value        Default value to be shown
      * @param    int       $maxlength
      * @param    int       $size
      */

      public static function createTextInput($param, $value = "", $maxlength = NULL, $size = 40, $class="form-control")
      {
          echo('<input type="text" class ="'. $class .'" name="'. $param .'" value="'. stripslashes($value) .'" maxlength="'. $maxlength .'" size="'. $size .'">');
      }    
      
      //----------------------------------------------------------
    
      /**
      * Prints a multiselect element from an array
      * 
      * @param    string   $field
      * @param    array    $elements
      * @param    int      $size
      */

      public static function displayMultiselect($field, $elements, $size = 8, $class = "form-control")
      {
          echo('<select class="'. $class .'" multiple="true" name="'. $field .'[]" size="'. $size .'">');
  
          foreach($elements as $element)
          {
              if($element['selected'] == true) $selected = 'selected'; else $selected = '';
  
              echo('<option value="'. $element['id'] .'"'. $selected .'>'. stripslashes($element['value']) .'</option>');     
          }

          echo("</select>");
      }
      
      //----------------------------------------------------------
      
      /**
      * Creates a fulltext area using Quill (https://quilljs.com)
      * 
      * @param    string    $param    Name of the post var to be sent
      * @param    mixed     $value    Default value to be shown
      * @param    int       $cols
      * @param    int       $rows
      */
      public static function createFullTextArea($param, $value = '', $cols = 30, $rows = 10, $class="form-control")
      {
          echo('<textarea class="'. $class .'" name="'. $param .'" cols="'. $cols .'" rows="'. $rows .'" 
                 id="editor_'. str_replace(array('[',']'), '', $param) .'">'. stripslashes($value) .'</textarea>');
          echo('
            <script src="https://cdn.ckeditor.com/ckeditor5/15.0.0/classic/ckeditor.js"></script>
            <script>
            ClassicEditor
            .create(document.querySelector("#editor_'. str_replace(array('[',']'), '', $param)  .'"), {
                toolbar:["heading", "|", 
                         "bold", "italic", "underline", "|", 
                         "link", "mediaEmbed", "code", "bulletedList", "numberedList", "blockQuote", "|", 
                         "insertTable", "tableColumn", "tableRow", "mergeTableCells", "|",
                         "undo","redo"],
            })
            .catch( error => {
            console.error( error );
            });
            </script>
          ');
      }    

      //----------------------------------------------------------
      
      /**
      * Creates a textarea input field
      *
      * @param    string    $param    Name of the post var to be sent
      * @param    mixed     $value    Default value to be shown
      * @param    int       $cols
      * @param    int       $rows
      */
      public static function createTextArea($param, $value = '', $cols = 30, $rows = 10, $class="form-control")
      {
          echo('<textarea class="'. $class .'" name="'. $param .'" cols="'. $cols .'" rows="'. $rows .'">'. stripslashes($value) .'</textarea>');
      }    
      
     //----------------------------------------------------------      
      
      /**
      * Creates a select menu for a enum data type field
      *
      * @param    array    $option    Available values for the enum field
      * @param    string   $param     Name of the post var to be sent
      * @param    mixed    $value     Default value to be shown
      */
      public static function createEnumMenu($options, $param, $value = NULL, $class = "form-control")
      {
          echo('<select class="'. $class .'" name="'. $param .'">');
          
          foreach($options as $option)
          {
              if($value == $option) { $selected = "selected"; } else { $selected = ""; }
              
              echo("<option value='". stripslashes($option) ."' $selected>". stripslashes($option) ."</option>");
          }
          
          echo('</select>');
      }
      
      //----------------------------------------------------------
      
      /**
      * Prints a file submission input
      *
      * @param    string    $name    Name of the post var to be sent
      */
      public static function createFileInput($name)
      {
          echo("<input type='file' name='$name'>");
      }

      //----------------------------------------------------------
      
      /**
      * Prints a unchecked checkbox
      *
      * @param    string     $name     Name of the post var to be sent
      * @param    string     $class
      */
      public static function createCheckbox($name)
      {
          echo("<input type='checkbox' name='$name'>");
      }

      //----------------------------------------------------------
      
      /**
      * Prints the submit button
      *
      * @param string $name Name of the post var to be sent
      * @param mixed $value Text to be shown in the button
      * @param string $class
      */
      public static function createSubmitButton($name = "submit", $value = "submit", $class = "btn btn-primary")
      {
          echo('<input type="submit" class="'. $class .'" name="'. $name .'" value="'. $value. '">');
      }
}
?>
