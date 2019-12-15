<?php

/**
* Model:    User
*
* (!)       Model generated by the create Script.
*/
 
class User extends MyModel
{
    //--
    //-- Model Actions
    //-- 

    var $model_actions = array(
            
            // Change password
            'action_change_password' => array(
                'name'          => 'Change Password',
                'description'   => 'This method changes the password for this user.',
                ),
    );
    
    //---------------------------------------------------------- 
    
    /**
     * Action: Change password for this user.
     *
     * @param   string  $old_password
     * @param   string  $new_password
     * @param   string  $confirmation 
     * @return  mixed   string
     */
    public final function action_change_password(string $new_password, string $confirmation)
    {
        if($new_password == $confirmation && strlen($new_password) > 3 && strlen($confirmation) > 3)
        {
            $this->fields->password = $new_password;
            if($this->update())
            {
                return("Password for user ". $this->fields->id ." successfully changed.");
            } else {
                $this->add_validation_error("Update failed. Try again or see logs.");
                return(false);
            }
        } else {
            $this->add_validation_error("Password and confirmation do not match.");
            return(false); 
        }
    }

    //---------------------------------------------------------- 
    
    /**
     * Encrypts the password if it has changed.
     * Called as a callback before insert and update.
     */
    protected final function encrypt_password()
    {
        if($this->fields->password != $this->old_fields->password)
        {
            $this->fields->password = Auth::encrypt($this->fields->password);
            $this->log->message('Changed password for user '. $this->fields->id);
        }
    }

    //---------------------------------------------------------- 
    
    /**
     * Assigns a token to this user.
     * Called as a callback before insert.
     *
     * @return   bool
     */
    protected final function assign_token()
    {
        if(!isset($this->fields->token) || strlen($this->fields->token) == 0)
            $this->fields->token = Auth::encrypt($this->fields->name . $this->fields->mail);
    }
    
    //---------------------------------------------------------- 
    
    /**
     * Checks the uniqueness of the email entered. Stores error if not unique.
     *
     * @return bool
     */
    protected final function check_unique_email()
    {
        if(isset($this->fields->id)) $l_id = $this->fields->id; else $l_id = 0;
        $res = $this->db->query->cardinality->getUserWhere("mail = '". $this->fields->mail ."' AND id != ". $l_id);
        if($res >= 1)
        {
            $this->storeError("The email ". $this->fields->email ." is already registered.");
            return(false);  // mail not unique
        } else {
            return(true);   // mail unique
        }
    }
}
?>