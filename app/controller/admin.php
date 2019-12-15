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
    var $before_action = array('_find_title', '_other_scaffolds','_set_custom_menu');  // Callback methods before controller execution
    var $after_action = array();                // Callback methods after controller execution
    var $layout = 'admin';	                    // Set another layout for this controller
    
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
            
            // Type
            $l_su   = array();

            // Should we user user model or adminusers file?
            $superuser_exists = $this->_superuser_exists();
            
            //--
            //-- Log-in using the adminusers.php admin file
            //-- Used if this application has no users model
            //--
            if(!$superuser_exists)
            {
                if(!require_once(USERCONFIG . 'adminusers.php'))
                {
                    trigger_error("I can't find the adminusers file at". USERCONFIG . 'adminusers.php', E_USER_ERROR);
                    die;
                }
                
                // Check login
                if(is_array($GLOBALS['picara_admin_users'][$l_user]) && $l_pwd == $GLOBALS['picara_admin_users'][$l_user]['pwd'])
                {
                    // Session is started
                    $this->session->name(ADMIN_SESSION);
                    $this->session->store("user", $l_user);
                    $this->session->store("name", $GLOBALS['picara_admin_users'][$l_user]['name']);
                    $this->session->store("type", 'superuser');
                    
                    // Log
                    $this->log->message("User ". $l_user ." logged to the Admin Site.");

                    // Set to welcome
                    $this->core->redirect($this->request['controller'] .'/welcome');

                } else {
                    $this->flash->validation_error('Invalid username or password.');
                }

            //--
            //-- Log-in using the user model
            //-- Used if this application has user model
            //--
            } else {
                
                // Create user object
                $users = $this->db->query->objects->getUserWhere("mail = '". $l_user ."' AND (type = 'superuser' OR type = 'staff') ORDER BY id ASC LIMIT 0,1");
                if(is_null($users))
                {
                    $this->flash->validation_error("Entered email does not exist.");
                    return;
                }
                
                // Check login
                if(Auth::encrypt($l_pwd) == $users[0]->fields->password && $l_user == $users[0]->fields->mail)
                {
                    // Session is started
                    $this->session->name(ADMIN_SESSION);
                    $this->session->store("user", $l_user);
                    $this->session->store("name", $users[0]->fields->name);
                    $this->session->store("type", $users[0]->fields->type);
                    
                    // Log
                    $this->log->message("User ". $l_user ." logged to the Admin Site.");

                    // Set to welcome
                    $this->core->redirect($this->request['controller'] .'/welcome');

                } else {
                    $this->flash->validation_error('Invalid mail or password.');
                }
                

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
            $this->core->redirect($this->request['controller'] .'/login');
        }
        
        // Log
        $this->log->message("User '". $this->session->read("user") ."' logged out of the Admin Site.");

        // Kill it 
        $this->session->kill();
        $this->setTitle($this->project_title . CONNECTOR . 'Log out');  
        $this->flash->success("Goodbye, you are now logged out.");
        $this->core->redirect($this->request['controller'] .'/login');
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
            $this->core->redirect($this->request['controller'] .'/login');
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
            $this->core->redirect($this->request['controller'] .'/login');
        }

        // Title
        $this->setTitle($this->project_title . CONNECTOR . "Tasks");
        $this->set('noindex', true);
        
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
        // Admin session must be created
        if(!Pi_session::check(ADMIN_SESSION)) 
        {
            $this->session->kill();
            $this->core->redirect($this->request['controller'] .'/login');
        }
        
        // Admin tasks array must be declared and contain this function name
        if(!isset($this->admin_tasks) || !is_array($this->admin_tasks) || !isset($this->admin_tasks[$taskname]))
        {
            $this->flash->validation_error("The task <strong>". $taskname ."</strong> is not declared as a task at ". USERLIB ."mywebcontroller.php");
            $this->core->redirect($this->request['controller'] .'/tasks');
        }
        
        // Method must have name and description
        if(!is_string($this->admin_tasks[$taskname]['name']) || !is_string($this->admin_tasks[$taskname]['description']))
        {
            $this->flash->validation_error("The task <strong>". $taskname ."</strong> does not have name or description. Add it at ". USERLIB ."mywebcontroller.php.");
            $this->core->redirect($this->request['controller'] .'/tasks');
        }

        // Method must exist
        if(!method_exists($this, $taskname))
        {
            $this->flash->validation_error("The method <strong>". $taskname ."</strong> does not exist. Create it at ". USERLIB ."mywebcontroller.php");
            $this->core->redirect($this->request['controller'] .'/tasks');
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
        $this->set('taskname',          $this->admin_tasks[$taskname]['name']);
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
            $this->core->redirect($this->request['controller'] .'/login');
        }
        
        // Read all logs
        $all_logs = $this->watcher->get("logs");

        // If not found
        if(!isset($all_logs[$log]))
        {
            $this->flash->validation_error("The log <strong>". $log ."</strong> was not found in the application.");
            $this->core->redirect($this->request['controller'] .'/'. $this->request['action']);
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
        $this->set("custom_menu", $this->admin_navigation);
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
                
                // Store
                $res[$connection][$model] = array(
                    'controller' => str_replace("_",'-',$controller),
                    'display'    => $config->display,
                );
            }
        }

        // To view
        $this->set('other_scaffolds', $res);
    }
    
    //--------------------------------------------------------
    
    /**
     * Finds out if the user model exits and wether or not a superuser exists.
     * @return  bool
     */
    private function _superuser_exists()
    {
        if(Pi_loader::model_exists("User"))
        {
            $suc = $this->db->query->cardinality->getUserWhere('type = "superuser"');
            if($suc > 0) return(true);
        }
        return(false);
    }
}

?>
