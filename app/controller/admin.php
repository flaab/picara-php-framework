<?php

/**
 * Controller: 	Admin
 */

class AdminWebController extends MyAdminController
{
    //--
    //-- Controller Options
    //-- 
    
    //var $ignore_errors = true;			    // Ignore errors and display the view
    //var $ignore_messages = true;			    // Ignore validation and flash messages 
    //var $sessionAllowed = 'Session_name';	    // Redirect 403 Forbidden unless this session is created
    var $load = array('Watcher');               // Array of libraries to autoload for this controller
    var $before_action = array('_find_title');  // Callback methods before controller execution
    var $after_action = array('_set_custom_menu', '_other_scaffolds');   // Callback methods after controller execution
    var $layout = 'admin';	                    // Set another layout for this controller
    var $config_file = 'siteadmin.yml';         // Config file to load for this controller

    // Valid user types
    var $valid_user_types = array('superuser','staff');

    //--
    //-- Project title -overriden with app name later-
    //--
    var $project_title = "PicaraPHP Administration";
    
    //--------------------------------------------------------
    // Internal Methods
    //--------------------------------------------------------

    /**
    * Finds project name and stores it
    */
    protected function _find_title()
    {
        if(DEFAULT_TITLE != TITLE) $this->project_title = TITLE;
    }
    
    //--------------------------------------------------------
    // Public Methods
    //--------------------------------------------------------
    
    /**
    * Login page
    */
    
    public function login()
    {
        // Kill session
        $this->session->kill();

        // Title and noindex
        $this->setTitle($this->project_title . CONNECTOR . 'Login'); 
        $this->set('noindex', true);
        
        // If post
        if(isset($_POST['submit']) && !empty($_POST['username']) && !empty($_POST['password']))
        {
            // Sanitized data
            $l_user = trim(strip_tags($_POST['username']));
            $l_pwd  = trim(strip_tags($_POST['password']));
            
            //--
            //-- Log-in using the adminusers.php admin file
            //--
                
            // Check credentials
            if(is_array($this->config['users'][$l_user]) && $l_pwd == $this->config['users'][$l_user]['password'])
            {
                // Type must exist
                if(!in_array($this->config['users'][$l_user]['type'], $this->valid_user_types))
                {
                    $this->flash->validation_error("User type is not valid, please check config file.");
                    return;
                }

                // Session is started
                $this->session->name(ADMIN_SESSION);
                $this->session->store("user",         $l_user);
                $this->session->store("name",         $this->config['users'][$l_user]['name']);
                $this->session->store("type",         $this->config['users'][$l_user]['type']);
                $this->session->store("menus",        $this->config['users'][$l_user]['menus']);
                $this->session->store("permissions",  $this->config['users'][$l_user]['permissions']);
                $this->session->store("tasks",        $this->config['users'][$l_user]['tasks']);

                // Log
                $this->log->message("User ". $l_user ." (". $this->config['users'][$l_user]['type'] .") logged to the Admin Site.");

                // Set to welcome
                $this->core->redirect($this->link['controller'] .'/welcome');

            } else {
                $this->flash->validation_error('Invalid username or password.');
            }
        } 
    }
    
    //--------------------------------------------------------
    
    /**
    * Logout page
    */
    
    public function logout()
    {
        // Admin session must be created
        if(!Pi_session::check(ADMIN_SESSION)) 
        {
            $this->session->kill();
            $this->core->redirect($this->link['controller'] .'/login');
        }
        
        // Log
        $this->log->message("User '". $this->session->read("user") ."' logged out of the Admin Site.");

        // Kill it 
        $this->session->kill();
        $this->setTitle($this->project_title . CONNECTOR . 'Log out');  
        $this->flash->success("Goodbye, you are now logged out.");
        $this->core->redirect($this->link['controller'] .'/login');
    }
    
    //--------------------------------------------------------
    
    /**
    * Welcome page of Admin Site
    */
    
    public function welcome()
    {
        // Admin session must be created
        if(!Pi_session::check(ADMIN_SESSION)) 
        {
            $this->session->kill();
            $this->core->redirect($this->link['controller'] .'/login');
        }
        
        // Set title and noindex
        $this->setTitle($this->project_title . CONNECTOR . "Welcome");  
        $this->set('noindex', true);
        
        // Get scaffolds and send to the view
        $other = $this->watcher->get_scaffolded_models($this->model);
        
    }
    
    //--------------------------------------------------------
    
    /**
    * Lists all admin task that can be run. 
    */
    public function tasks()
    {
        // Admin session must be created
        if(!Pi_session::check(ADMIN_SESSION)) 
        {
            $this->session->kill();
            $this->core->redirect($this->link['controller'] .'/login');
        }

        // Title
        $this->setTitle($this->project_title . CONNECTOR . "Tasks");
        $this->set('noindex', true);

        // Iterates all tasks and removes those which I have no permission for
        foreach($this->admin_tasks as $function => $data)
        {
            if(!Pi_session::check_task($function))
                unset($this->admin_tasks[$function]);
        }

        // Set all tasks
        $this->set('tasks', $this->admin_tasks);
    }

    //--------------------------------------------------------
    
    /**
    * Confirms and runs a parametrized admin task.
    * @param   string taskname
    */
    public function runtask(string $taskname)
    {
        // If no permission to run task
        if(!Pi_session::check_task($taskname))
        {
            $this->flash->validation_error("You don't have permission to run the task <strong>". $taskname ."</strong>.");
            $this->core->redirect($this->link['controller'] .'/welcome');
        }

        // Admin session must be created
        if(!Pi_session::check(ADMIN_SESSION)) 
        {
            $this->session->kill();
            $this->core->redirect($this->link['controller'] .'/login');
        }
        
        // Admin tasks array must be declared and contain this function name
        if(!isset($this->admin_tasks) || !is_array($this->admin_tasks) || !isset($this->admin_tasks[$taskname]))
        {
            $this->flash->validation_error("The task <strong>". $taskname ."</strong> is not declared as a task at ". USERLIB ."mywebcontroller.php");
            $this->core->redirect($this->link['controller'] .'/tasks');
        }
        
        // Method must have name and description
        if(!is_string($this->admin_tasks[$taskname]['name']) || !is_string($this->admin_tasks[$taskname]['description']))
        {
            $this->flash->validation_error("The task <strong>". $taskname ."</strong> does not have name or description. Add it at ". USERLIB ."mywebcontroller.php.");
            $this->core->redirect($this->link['controller'] .'/tasks');
        }

        // Method must exist
        if(!method_exists($this, $taskname))
        {
            $this->flash->validation_error("The method <strong>". $taskname ."</strong> does not exist. Create it at ". USERLIB ."mywebcontroller.php");
            $this->core->redirect($this->link['controller'] .'/tasks');
        }

        // Get parameters
        $method = new ReflectionMethod(get_class($this), $taskname);
        $params_original = $method->getParameters();

        // Parameters to view
        $params = array();

        // Class type of parameters
        $classes = array();

        // Clean params and set nice name
        foreach($params_original as $i => $name)
        {
            // Parameteter name
            $params[$i] = ucwords(str_replace('_', ' ', preg_replace('/(^.*\[.+\$|\].*$)/', '', $name)));

            // Class of this parameter using type hinting
            $class_hint = $name->getClass();
            
            // If class exists, get string field to populate
            if(class_exists($class_hint->name))
            {
                // Text field
                $text_field = $this->metadata->get_first_string_field($class_hint->name);

                // Classes
                $classes[$i] = array('name' => $class_hint->name, 'field' => $text_field);
            }

        }
        
        // Set Params
        $this->set('params',        $params);
        $this->set('classes',       $classes);
        
        // All good. Set data to view.
        $this->set('function',      $taskname);
        $this->set('taskname',      $this->admin_tasks[$taskname]['name']);
        $this->set('description',   $this->admin_tasks[$taskname]['description']);
        
        // Window title and noindex
        $this->setTitle($this->project_title . CONNECTOR . "Tasks". CONNECTOR . $this->admin_tasks[$taskname]['name']);
        $this->set('noindex', true);
        

        //--
        //-- Execute if no params nor confirmation is needed 
        //--
        
        $run_task_now = count($params) == 0 && isset($this->admin_tasks[$taskname]['expedite']) && $this->admin_tasks[$taskname]['expedite'];
        
        //--
        //-- Execution of task
        //--
        
        // Execution
        if(isset($_POST['submit']) || $run_task_now)
        {
            if(!isset($_POST['params'])) $_POST['params'] = array();
            if(count($_POST['params']) == count($params))
            {
                // Prepare array
                $user_params = array();

                // If parameters are received
                if(is_array($_POST['params']))
                {
                    // Iteate them
                    for($i = 0; $i < count($_POST['params']); $i++)   
                    {
                        // If it must be an object
                        if(isset($classes[$i]))
                        {
                            $classname = $classes[$i]['name'];
                            $user_params[$i] = new $classname($_POST['params'][$i]);

                        // Plain
                        } else {
                            $user_params[$i] = $_POST['params'][$i];
                        }
                    }
                }
                
                // Execute
                $res = call_user_func_array(array($this, $taskname), $user_params);
                
                // If false return
                if(!$res) return;

                // OK
                $this->flash->success("The following task has been successfully executed.");
                $this->log->message("Task ". $taskname ." successfully executed.");

                // Executed it is
                $this->set('executed', true);
                
                // Set result if ok
                $this->set('res', $res);
            } else {
                $this->flash->validation_error("Parameters and inputs don't match");
            }
        } else {
            $this->set('executed', false);
        }
    }
    
    //--------------------------------------------------------
    
    /**
    * Shows the application log, and links to other logs.
    * @param   string logname
    */
    public function logs($log = DEFAULT_LOG, $lines = 100)
    {
        // Window title and noindex
        $this->setTitle($this->project_title . CONNECTOR . "Tasks". CONNECTOR . "Logs");
        $this->set('noindex', true);
        
        // Admin session must be created
        if(!Pi_session::check(ADMIN_SESSION)) 
        {
            $this->session->kill();
            $this->core->redirect($this->link['controller'] .'/login');
        }
        
        // Read all logs
        $all_logs = $this->watcher->get("logs");

        // If not found
        if(!isset($all_logs[$log]))
        {
            $this->flash->validation_error("The log <strong>". $log ."</strong> was not found in the application.");
            $this->core->redirect($this->link['controller'] .'/'. $this->request['action']);
            die;
        }

        // Read
        $to_read = $all_logs[$log];
        
        // Web file
        $log_file = $to_read .'/'. ENVIRONMENT .'.log';

        // Read contents
        $log_file_contents = Filesystem::tail($log_file, $lines);
        
        // Set the logs
        $this->set('logs', array_keys($all_logs));
        $this->set('current_log', $log);

        // Set the files
        $this->set('log_content', $log_file_contents);
    }
    
    //--------------------------------------------------------
    
    /**
    * Sets custom navigation menus from my admin controller.
    */
    protected function _set_custom_menu()
    {
        // Get logged users menu
        if(Pi_session::check(ADMIN_SESSION))
        {
            $menu_allowed = $this->session->read('menus');
            $user_type = $this->session->read('type');
            if($user_type != 'superuser') // superuser access all menus
            {
                foreach($this->config['navigation'] as $menu => $content)
                    if(!in_array($menu, $menu_allowed))
                        unset($this->config['navigation'][$menu]);
            }
            $this->set("custom_menu", $this->config['navigation']);
        }
    }

    //--------------------------------------------------------

    /**
    * Delivers other scaffolds to the view grouped by connection
    */
    
    protected function _other_scaffolds()
    {
        // Get other scaffolds
        $other = $this->watcher->get_scaffolded_models($this->model);
         
        // Response array
        $res = array();

        // A nice array containing class name, display name and controller must be provided
        if(is_array($other))
        {
            foreach($other as $class => $controller)
            {
                // Read config
                $config = $this->metadata->config->get($class);

                // Variables
                $model = $class;
                $connection = $config->connection;
                
                if(Pi_session::check(ADMIN_SESSION))
                {
                    if(Pi_session::check_permission($model, 'list'))
                    {
                        // Store
                        $res[$connection][$model] = array(
                            'controller' => str_replace("_",'-',$controller),
                            'display'    => $config->display,
                        );
                    }
                }
            }
        }

        // To view
        $this->set('other_scaffolds', $res);
    }
}

?>
