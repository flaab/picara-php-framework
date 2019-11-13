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
* This is a simple framework config file, however, you should not
* touch this file. For application specific config, edit /app/application.php
*
* @package Config
* @author Arturo Lopez 
* @copyright Copyright (c) 2007-2019, Arturo Lopez
*/

//==============================================================
// SEARCH ENGINE
//==============================================================

/**
 * Max length for a search string
 *
 * @var integer
 * @default 100
 */
define('MAX_SEARCH_STRING_LENGTH', 100); 
 
/**
 * Min length for a search string
 *
 * @var integer
 * @default 2
 */
define('MIN_SEARCH_STRING_LENGTH', 2); 
 
/**
 * Search string connector. This will replace spaces
 * 
 * @var char
 * @default '+'
 */
define('SEARCH_CONNECTOR', '+');  

/**
 * Strict constant. Do not change
 * @var boolean
 */
define('STRICT', true);


//==============================================================
// DATA DICTIONARY
//==============================================================

/**
 * String constant for metadata dictionary
 */ 
define('STRING', 'C');

/**
 * Text constant for metadata dictionary
 */ 
define('TEXT', 'X');

/**
 * Blob constant for metadata dictionary
 */ 
define('BLOB', 'B');

/**
 * Date constant for metadata dictionary
 */ 
define('DATE', 'D');

/**
 * Timestamp constant for metadata dictionary
 */ 
define('TIMESTAMP','T');

/**
 * Boolean constant for metadata dictionary
 */ 
define('BOOLEAN', 'L');

/**
 * Integer constant for metadata dictionary
 */ 
define('INTEGER', 'I');

/**
 * Numeric constant for metadata dictionary
 */ 
define('NUMERIC', 'N');

/**
 * Serial constant for metadata dicionary
 */ 
define('SERIAL', 'R');

/**
 * Enum constant for metadata dictionary
 */ 
define('ENUM', 'E');

//==============================================================
// QUERY RESULT SHAPES
//==============================================================

/**
 * Objects result shape constant for query class
 */ 
define('OBJECTS', 		1);

/**
 * Ids result shape constant for query class
 */ 
define('IDS', 			2);

/**
 * Arrays result shape constant for query class
 */ 
define('ARRAYS', 		3);

/**
 * Cardinality result shape constant for query class
 */ 
define('CARDINALITY',	4);

/**
 * Collection result shape constant for query class
 */ 
define('COLLECTION', 	5);

/**
* Xml result shape for queries
*/
define('XML',           6);

/**
* Csv result shape for queries
*/
define('CSV',           7);

/**
* Yaml result shape
*/
define('YML',           8);

/**
* Json result shape
*/
define('JSON',          9);

/**
* Default result shape
*/
define('DEFAULT_RESULT_SHAPE', COLLECTION);

//==============================================================
// DATABASE CONVENTIONS
//==============================================================

/**
 * Primary key field for all database tables
 *
 * @var string 
 * @default 'id'
 */
define('PRIMARY_KEY', 'id');

/**
* Regular expression to check if a field is a foreign key
* Executing preg_replace(FK_REGEX, field) should return the related table name
*
* @var string
* @default "/_id$/"
*/
define('FK_REGEX', "/_id[0-9]*$/");
 
//==============================================================
// DEFAULT SETTINGS FOR CONTROLLER, ACTION AND CONNECTION
//==============================================================

/**
* Default controller to be executed if any has been requested
* @default Index
*/
define('DEFAULT_CONTROLLER', 'index');

/**
* Default action to be execute if any has been requested
* @default index
*/
define('DEFAULT_ACTION', 'index');	

/**
* Default database name
* @default main
*/
define('DEFAULT_CONNECTION', 'main');

/**
* Default log
* @default  main
*/
define('DEFAULT_LOG', 'main');

//==============================================================
// CONSTANTS FOR AUTOLOADED LIBRARIES 
//==============================================================

/**
* Library should be called using a singleton
*/
define('SINGLETON', 1);

/**
* Library should be normally instanced
*/
define('STANDARD', 2);

//==============================================================
// LINE BREAK AND EXECUTION ENVIRONMENT
//==============================================================

// If executed in browser
if(!isset($_SERVER['SHELL']) && !isset($_SERVER['TERM']))
{
    define('EXECUTION', 'web');
    define('LINE_BREAK', "<br />"); 
    
// If shell execution
} else {

    define('EXECUTION', 'shell');
    define('LINE_BREAK', "\n"); 

}

//==============================================================
// ASSOCIATED FILE BLOCKS
//==============================================================

/**
* Directory name to store all model files
*/
define('MODEL_BLOCKS', 'files/');

/**
* Default post var name for uploaded files
*
* @default    model_uploaded_file
*/
define('UPLOAD_FILE_INPUT_NAME', 'model_uploaded_file');

//==============================================================
// ASSOCIATED IMAGES
//==============================================================

/**
* Directory name to store all model images
*/
define('MODEL_IMAGES', 'images/');

/**
* Default post var name for model images
*
* @default    model_image
*/
define('MAIN_IMAGE_INPUT_NAME', 'model_main_image');

/**
* Delete main image checkbox input name
*
* @default    model_image
*/
define('DELETE_MAIN_IMAGE', 'model_delete_main_image');

/**
* Default name for main image without extension
* @default    main
*/
define('MAIN_IMAGE', 'main');

/**
* Directory name to store thumbnails
*/
define('MODEL_THUMBS', 'thumbs/');

//==============================================================
// OTHER STUFF
//==============================================================

/**
 * Default layout to be loaded, with no extension
 *
 * @default default
 */
define('DEFAULT_LAYOUT', 'default');

/**
 * Auto-populated date created field
 *
 * @default date_created
 */
define('DATE_CREATED_POPULATED', 'date_created');

/**
 * Auto-populated date modified field
 *
 * @default: date_modify
 */
define('DATE_MODIFIED_POPULATED', 'date_modified');

/**
 * Auto-populated datetime create field
 *
 * @default datetime_created
 */
define('DATETIME_CREATED_POPULATED', 'datetime_created');

/**
 * Auto-populated date modified field
 *
 * @default: date_modify
 */
define('DATETIME_MODIFIED_POPULATED', 'datetime_modified');

/**
* Default scaffold snapfile, with extension 
*
* @default: element
*/
define('SCAFFOLDSNAPFILE','element.php');

/**
*
*/
define('DEFAULT_TITLE', 'Your App Name');

?>
