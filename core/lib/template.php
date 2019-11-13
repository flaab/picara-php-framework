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
 * A dead simple template engine using php syntax.
 *
 * @package    Libs
 * @author     Arturo Lopez Perez
 * @copyright  Copyright (c) 2008-2019, Arturo Lopez
 * @version    0.1
 */
class Template
{
    /**
     * Path to template file
     * @var string
     */
    private $path_to_template;
    
    /**
     * Arguments for the template
     * @var array
     */
    private $arguments;


    /**
     * Constructs the template, checking the template exists.
     *
     * @param    string    $path
     * @param    string    $arguments
     */
    function __construct(string $path, $arguments = array())
    {
        if(!file_exists($path)) 
        {
            trigger_error("The template ". $path ." does not exist.", E_USER_ERROR);
            die;
        }

        if(!is_array($arguments))
        {
            trigger_error("The second parameter of the constructor must be an array.", E_USER_ERROR);
            die;
        }
        
        $this->path_to_template = $path;
        $this->arguments = $arguments; 
    }

    /**
     * Adds arguments to the template.
     *
     * @param   name      $name
     * @param   mixed     $var
     */
    public function set(string $name, $var)
    {
        $this->arguments[$name] = $var;
    }

    /**
     * Evals the template and returns the result.
     *
     * @return  string
     */
    public function parse()
    {
        // Assign variables as local
        foreach($this->arguments as $arg => $val)
            ${$arg} = htmlspecialchars(strip_tags($val));

        // Output buffering
        ob_start();
        require $this->path_to_template;
        $content = ob_get_contents();
        ob_end_clean();

        // Return result
        return $content;
    }
    
    /**
     * Saves the template result into a given path.
     *
     * @param   string  $path
     */
    public function save(string $path)
    {
        return(FileSystem::put_file($path, $this->parse()));
    }

    /**
     * Renders the result of the template.
     */
    public function render()
    {
        print($this->parse());
    }

}

?>
