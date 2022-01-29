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
* Creates and manages simultaneus database connections
*
* @package      System
* @author       Arturo Lopez
* @copyright    Copyright (c) 2008-2019, Arturo Lopez
* @version      0.1
*/

class Pi_db extends Pi_error_store
{
    /**
    * Instance for singleton pattern
    */
    private static $instance;

    /**
    * Connection handlers
    */
    public $link;

    /**
    * Query helper
    */
    public $query;

    /**
    * Adapters and according drivers
    */
    private $adapters = array(
        
        'postgres' => 'postgres',
        'mysql'    => 'mysqli', 
        'oracle'   => 'oci8',
        'sqlite'   => 'sqlite3',
    );

    /**
    * Functions that should be checked
    */
    private $functions = array(

        'postgres'  => 'pg_connect',
        'mysql'     => 'mysqli_connect',
        'oracle'    => 'oci_connect',
    );
    
    /**
    * Classes that should be checked
    */
    private $classes = array(
    
        'sqlite'    => 'SQLite3',
    );

    //----------------------------------------------------------
    
    /**
    * Private constructor to avoid direct creation of object. 
    */
    
    private function __construct()
    {
        // Standard class to store connection links
        $this->link = new StdClass();

        // The query helper is created with a reference to this object
        $this->query = Pi_query::singleton($this);

    }
    
    //---------------------------------------------------------

    /**
    * Will return a new object or a pointer to the already existing one
    *
    * @return    Db
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
    * Creates and stores desired database connection
    *
    * @param    string    $conn
    * @param    bool      $test
    */
    
    public function connect($conn, $test = false)
    {
        // If name is empty, default connection will be tried to entablish
        if(empty($conn)) $conn = DEFAULT_CONNECTION;

        // Check connection is not already entablished
        if(!isset($this->link->{$conn}) || !is_object(@$this->link->{$conn}) || !$this->link->{$conn}->isConnected())
        {
            // Libs are required just once and only if needed
            require_once(VENDORS . 'adodb5/adodb-exceptions.inc.php');
            require_once(VENDORS . 'adodb5/adodb.inc.php');

            // Dsn
            $dsn = $this->dsn($conn);
            
            // Try
            try {
                
                $this->link->{$conn} = ADONewConnection($dsn);

            } catch(exception $e) { 
                
                if($test == false)
		{
                    trigger_error("Database connection failed. Check '$conn' connection information at file ". CONNECTION . $conn . ".php for ". ENVIRONMENT ." environment", E_USER_ERROR);
                }

                return false;
            }
            return true;
        }
    }

    //--------------------------------------------------------

    /**
    * Closes and unsets desired database connection
    *
    * @param    string    $conn
    */

    public function disconnect($conn)
    {
        if(is_object($this->link->{$conn}) && $this->link->{$conn}->isConnected())
        {
            $this->link->{$conn}->Disconnect();
            unset($this->link->{$conn});
            return true;
        }

        return false;
    }

    //--------------------------------------------------------

    /**
    * Creates the connection dsn according the database environment.
    * Checking that connection file exists it's a waste of time, it just tries
    * to load the file and crash if it fails
    *
    * @param    string    $conn
    * @return   string    $dsn
    */

    private function dsn($conn)
    {
        // Connection file
        $file = CONNECTION . $conn . '.yml';
        
        // Attempts to load the connection file
        if(!file_exists($file))
            trigger_error("Connection '$conn' does not exist", E_USER_ERROR);

        // Parse yml natively
        $connection = yaml_parse(file_get_contents($file)); 

        // Connection information loaded
        $connection = $connection[ENVIRONMENT];
       
        // Check adapter is supported
        if(!isset($this->adapters[$connection['adapter']]))
            trigger_error("Driver '". $connection['adapter'] ."' does not exist or is not supported", E_USER_ERROR);

        // Some characters must be scaped before creating the dsn
        foreach($connection  as $key => $value)
        {
            if(preg_match("/[\/\:\?_]/", $value))
                $connection[$key] = urlencode($value);
        }

        // Explode if libraries for this connection are missing
        $this->check_libraries($connection['adapter']);
        
        // Sqlite
        if($connection['adapter'] == 'sqlite' || $connection['adapter'] == 'sqlite3')
        {
            // Path to sqlite file
            $sqlite_path = getcwd() . '/'. DB . $connection['db'];
             
            // DB file must exist
            if(!file_exists($sqlite_path))
                trigger_error("The SQLite file". $sqlite_path ." does not exist", E_USER_ERROR);

            // Create dsn    
            $dsn  = $this->adapters[$connection['adapter']] . '://'. getcwd() . '/'. DB . $connection['db'];
        
        // Mysql
        // Postgres
        // Oci8
        } else {
            
            $dsn  = $this->adapters[$connection['adapter']] . '://';
            $dsn .= $connection['user'] . ':';
            $dsn .= $connection['password'] . '@';
            $dsn .= $connection['host'];
            $dsn .= '/' . $connection['db'];
            if(isset($connection['port']) && $connection['port'] != "" && $connection['port'] != 0)
                $dsn .= "?port=". $connection['port'];
        } 
        
        return $dsn; 
    }

    //--------------------------------------------------------

    /**
    * Checks if required database libraries are installed
    *
    * @param    string    $adapter
    */

    private function check_libraries($adapter)
    {
        if(isset($this->functions[$adapter]) && !function_exists($this->functions[$adapter]))
            trigger_error("PHP functions to establish '$adapter' connections are not found", E_USER_ERROR);
        
        if(isset($this->classes[$adapter]) && !class_exists($this->classes[$adapter]))
            trigger_error("PHP classes to establish '$adapter' connections are not found", E_USER_ERROR);
    }
    

    //--------------------------------------------------------

    /**
    * Checks if given connection file exists
    *
    * @param
    */
    
    public function exists($conn = NULL)
    {
        // If name is provided, we must check for that connection name
        if($conn != NULL)
        {
            if(file_exists(CONNECTION . $conn . '.yml'))
                return true;

            return false;
        } 

        // Check if any connection exists
        $files = FileSystem::find_files(CONNECTION, '/\.yml$/');

        // If no files retrieved, no connection exists
        if(count($files) == 0) return false;

        return true;
    }

    //----------------------------------------------------------     
    
    /**
    * Retrieves the exception string from the exception given
    * Will throw the message and log it if neccesary
    *
    * @param    exception    $e
    * @return   string
    */    
    
    public static function getExceptionString(exception $e)
    {
        // Get it
        $ex = $e->gettrace();
        $msg = $ex[0]['args'][3];
        $query = $ex[0]['args'][4]; 
        
        // Log       
        $log = Pi_logs::singleton();
        $log->error("SQL Query failed: '". $query ."'. Database said: ". $msg, 'main');
        return $msg;
    }
}
?>
