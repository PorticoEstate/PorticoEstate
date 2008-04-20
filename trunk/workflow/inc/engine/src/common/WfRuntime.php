<?php
require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'common'.'/'.'Base.php');

//!! WfRuntime
//! A class to handle instances at runtime
/*!
* This class can be viewed by the user like an Instance, it is in fact more than an instance
* as it handle concurrency and part of the core execution of the instance while avoiding
* bad manipulation of the instance
*/
class WfRuntime extends Base
{

  // processes config values cached for this object life duration
  // init is done at first use for the only process associated with this runtime object
  var $conf= Array();

  //instance and activity are the two most important object of the runtime
  var $activity = null;
  var $instance = null;
  var $instance_id = 0;
  var $activity_id = 0;
  //process object is used, for example, to retrieve the compiled code
  var $process = null;
  //workitems is a reference to $instance->workitems
  var $workitems = null;
  //activities is a reference to $instance->activities
  var $activities = null;
  //security Object
  var $security = null;
  //boolean, wether or not we are in a transaction
  var $transaction_in_progress = false;
  //boolean, wether or not we are in debug mode
  var $debug=false;
  //boolean, wether or not we are in automotic mode (i.e.: non-interactive), big impact on error handling
  var $auto_mode=false;

  /*!
  * @public
  * Constructor takes a PEAR::Db object
  */
  function WfRuntime(&$db)
  {
    $this->child_name = 'WfRuntime';
    parent::Base($db);
    require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'BaseActivity.php');
    require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'Process.php');
    require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'Instance.php');
    require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'common'.'/'.'WfSecurity.php');

    //first the activity is not set
    $this->activity = null;
    $this->instance =& new Instance($this->db);
    $this->process =& new Process($this->db);
    $this->security =& new WfSecurity($this->db);
  }

  /*!
  * @private
  * Collect errors from all linked objects which could have been used by this object
  * Each child class should instantiate this function with her linked objetcs, calling get_error(true)
  * for example if you had a $this->process_manager created in the constructor you shoudl call
  * $this->error[] = $this->process_manager->get_error(false, $debug);
  * @param $debug is false by default, if true debug messages can be added to 'normal' messages
  * @param $prefix is a string appended to the debug message
  */
  function collect_errors($debug=false, $prefix = '')
  {
    parent::collect_errors($debug, $prefix);
    if (isset($this->instance) && !!($this->instance)) $this->error[] = $this->instance->get_error(false, $debug, $prefix);
    if (isset($this->process) && !!($this->process)) $this->error[] = $this->process->get_error(false, $debug, $prefix);
    if (isset($this->security) && !!($this->security)) $this->error[] = $this->security->get_error(false, $debug, $prefix);
    if (isset($this->activity) && !!($this->activity)) $this->error[] = $this->activity->get_error(false, $debug, $prefix);
  }

  /*!
  * @public
  * Call this function to end-up dying and giving a last message
  * @param $last_message is your last sentence
  * @param $include_errors is a false boolean by default, if true we'll include error messages
  * @param $debug is false by default, if true you will obtain more messages, if false you could obtain theses
  * @param $dying true by default, tells the engine to die or not
  * messages as well if this object has been placed in debug mode with setDebug(true)
  * recorded by this runtme object.
  * @return nothing, it die!
  */
  function fail($last_message, $include_errors = false, $debug=false, $dying=true)
  {
    $the_end = '';
    //see if local objects have been set to enforce it
    if ($this->debug) $debug = true;
    if ($this->auto_mode) $dying = false;

    if ($include_errors)
    {
      $the_end = $this->get_error(false, $debug).'<br />';
    }
    $the_end .= $last_message;
    if ($this->transaction_in_progress)
    {
      //we had a transaction actually, we mark a fail, this will force Rollback
      $this->db->FailTrans();
      $this->db->CompleteTrans();
    }
    if ($dying)
    {
      //this will make the session die
      galaxia_show_error($the_end, $dying);
    }
    else
    {
      //this will NOT BREAK the session!
      return $the_end;
    }
  }

  /*!
  * @private
  * load the config values for the process associated with the runtime
  *
  * config values are cached while this WfRuntime object stays alive
  * @param $arrayconf is a config array with default value i.e.:
  *	* key is the config option name
  *	* value is the config default value
  * @return a config array with values associated with the current process
  * for the asked config options and as well for som WfRuntime internal
  * config options
  */
  function &getConfigValues(&$arrayconf)
  {
    if (!(isset($this->process)))
    {
      $this->loadProcess();
    }
    $arrayconf['auto-release_on_leaving_activity'] = 1;
    $this->conf =  $this->process->getConfigValues($arrayconf);
    return $this->conf;
  }

  /*!
  * @public
  * Load the instance, the activity and the process, all things needed by the runtime engine to 'execute' the activity
  *
  * @param $activityId is the activity id, the activity we will run
  * @param $instanceId is the instance Id, can be empty for a start or standalone activity
  * @return true or false
  */
  function &LoadRuntime($activityId,$instanceId=0)
  {
    // load activity
    if (!($this->loadActivity($activityId, true, true)))
    {
      return false;
    }
    //interactive or non_interactive?
    $this->setAuto(!($this->activity->isInteractive()));
    //load instance
    if (!($this->loadInstance($instanceId)))
    {
      return false;
    }
    // load process
    if (!($this->loadProcess()))
    {
      return false;
    }

    //ensure the activity is not completed
    $this->instance->setActivityCompleted(false);

    //set the workitems and activities links
    $this->workitems =& $this->instance->workitems;
    $this->activities =& $this->instance->activities;
    return true;
  }

  /*!
  * @private
  * retrieve the process object associated with the activity
  *
  * @param $pId is the process id of the process you want, if you do not give it we will try to
  * take it from the activity
  * @return true if everything was ok. False in the other case, consult errors
  */
  function loadProcess($pId=0)
  {
    if ( (!(isset($this->process))) || ($this->process->getProcessId()==0))
    {
      if ( (empty($pId)) || (!($pId)) )
      {
        $pId = $this->activity->getProcessId();
        if ( (empty($pId)) || (!($pId)) )
        {
          //fail can return in auto mode or die
          $errors = $this->fail(tra('No Process indicated'),true, $this->debug, !($this->auto_mode));
          $this->error[] = $errors;
          return false;
        }
      }
      if ($this->debug) $this->error[] = 'loading process '.$pId;
      $this->process->getProcess($this->activity->getProcessId($pId));
    }
    return true;
  }

  /*!
  *
  *
  * @return the actual Process Object
  */
  function &getProcess()
  {
    return $this->process;
  }

  /*!
  * @private
  * retrieve the activity of the right type from a baseActivity Object
  *
  * @param $activity_id is the activity_id you want
  * @param $with_roles will load the roles links on the object
  * @param $with_agents will load the agents links on the object
  * @return true if everything was ok. False in the other case, consult errors
  */
  function loadActivity($activity_id, $with_roles= true,$with_agents=false)
  {
    if ( (empty($activity_id)) || (!($activity_id)) )
    {
      //fail can return in auto mode or die
      $errors = $this->fail(tra('No activity indicated'),true, $this->debug, !($this->auto_mode));
      $this->error[] = $errors;
      return false;
    }
    $base_activity =& new BaseActivity($this->db);
    $this->activity =& $base_activity->getActivity($activity_id, $with_roles, $with_agents);
    if (!$this->activity)
    {
      $errors = $this->fail(tra('failed to load the activity'),true, $this->debug, !($this->auto_mode));
      $this->error[] = $errors;
      return false;
    }
    $this->activity_id = $activity_id;
    $this->error[] =  $base_activity->get_error();
    if ($this->debug) $this->error[] = 'loading activity '.$activity_id;
    return true;
  }

  /*!
  *
  *
  * @return the actual Activity Object
  */
  function &getActivity()
  {
    return $this->activity;
  }

  /*!
  * @public
  * retrieve the instance which could be an empty object
  * @param $instanceId is the instance id
  * @return true if everything was ok. False in the other case, consult errors
  */
  function loadInstance($instanceId)
  {
    $this->instance_id = $instanceId;
    $this->instance->loadInstance($instanceId);
    if ( ($this->instance->getInstanceId()==0)
      && (! (($this->activity->getType()=='standalone') || ($this->activity->getType()=='start') )) )
    {
      //fail can return in auto mode or die
      $errors = $this->fail(tra('no instance avaible'), true, $this->debug, !($this->auto_mode));
      $this->error[] = $errors;
      return false;
    }
    if ($this->debug) $this->error[] = 'loading instance '.$instanceId;
    return true;
  }

  /*!
  * @public
  * Perform necessary security checks at runtime before running an activity
  * This will as well lock the tables via the security object.
  * It should be launched in a transaction.
  * @return true if ok, false if the user has no runtime access
  * instance and activity are unsetted in case of false check
  */
  function checkUserRun()
  {
    if ($this->activity->getType()=='view')
    {
      //on view activities  the run action is a special action
      $action = 'viewrun';
    }
    else
    {
      $action = 'run';
    }
    //this will test the action rights and lock the necessary rows in tables in case of 'run'
    $result = $this->security->checkUserAction($this->activity_id,$this->instance_id,$action);
    $this->error[] =  $this->security->get_error(false, $this->debug);
    if ($result)
    {
      return true;
    }
    else
    {
      return false;
    }
  }


  /*!
  * @public
  * Perform necessary security checks at runtime
  * This will as well lock the tables via the security object.
  * It should be launched in a transaction.
  * @return true if ok, false if the user has no runtime access
  * instance and activity are unsetted in case of false check
  */
  function checkUserRelease()
  {
    //the first thing to scan if wether or not this process is configured for auto-release
    if ( (isset($this->conf['auto-release_on_leaving_activity'])) && ($this->conf['auto-release_on_leaving_activity']))
    {
      //this will test the release rights and lock the necessary rows in tables in case of 'release'
      $result = $this->security->checkUserAction($this->activity_id,$this->instance_id,'release');
      $this->error[] =  $this->security->get_error(false, $this->debug);
      if ($result)
      {
        //we are granted an access to release but there is a special bad case where
        //we are a user authorized at releasing instances owned by others and where
        //this instance is owned by another (for some quite complex reasons).
        //we should not release this new user!!
        //Then this is auto-release, not a conscious act and so we will release only
        //if we can still grab this instance (avoiding the bad case)

        //test grab before release
        if ($this->checkUserRun())
        {
          return true;
        }
      }
    }
    return false;
  }

  /*!
  * This will set/unset the WfRuntime in debug mode
  * @debug_mode is true by default, set it to false to disable debug mode
  */
  function setDebug($debug_mode=true)
  {
    $this->debug = $debug_mode;
  }

  /*!
  * This will set/unset the WfRuntime in automatic mode. i.e : executing
  * non-interactive or interactive activities. Automatic mode have big impacts
  * on error handling and on the way activities are executed
  * @auto_mode is true by default, set it to false to disable automatic mode
  */
  function setAuto($auto_mode=true)
  {
    $this->auto_mode = $auto_mode;
  }

  /*!
  * This function will start a transaction, call it before setActivityUser()
  */
  function StartRun()
  {
    $this->transaction_in_progress =true;
    $this->db->StartTrans();
  }

  /*!
  * This function ends the transactions started in StartRun()
  */
  function EndStartRun()
  {
    if ($this->transaction_in_progress)
    {
      $this->db->CompleteTrans();
      $this->transaction_in_progress =false;
    }
  }

  /*!
  * For interactive activities this function will set the current user on the instance-activities table.
  * This will prevent having several user using the same activity on the same intsance at the same time
  * But if you want this function to behave well you should call it after a checkUserRun or a checkUserRelease
  * and inside a transaction. Theses others function will ensure the table will be locked and the user
  * is really granted the action
  * @param $grab is true by default, if false the user will be set to '*', releasing the instance-activity record
  */
  function setActivityUser($grab=true)
  {
    if(isset($GLOBALS['user']) && !empty($this->instance->instanceId) && !empty($this->activity_id))
    {
      if ($this->activity->isInteractive())
      {// activity is interactive and we want the form, we'll try to grab the ticket on this instance-activity (or release)
        if ($grab)
        {
          $new_user = $GLOBALS['user'];
        }
        else
        {
          $new_user= '*';
        }
        if (!$this->instance->setActivityUser($this->activity_id,$new_user))
        {
           //fail can return in auto mode or die
           $errors = $this->fail(lang("You do not have the right to run this activity anymore, maybe a concurrent access problem, refresh your datas.", true, $this->debug, !($this->auto_mode)));
           $this->error[] = $errors;
           return false;
        }
      }// if activity is not interactive there's no need to grab the token
    }
    else
    {
      //fail can return in auto mode or die
      $errors= $this->fail(lang("We cannot run this activity, maybe this instance or this activity do not exists anymore.", true, $this->debug, !($this->auto_mode)));
      $this->error[] = $errors;
      return false;
    }
  }

  /*!
  * Try to give some usefull info about the current runtime
  * @return an associative arrays with keys/values which could be usefull
  */
  function &getRuntimeInfo()
  {
    $result = Array();
//    _debug_array($this->instance);
    if (isset($this->instance))
    {
      $result['instance_name'] = $this->instance->getName();
      $result['instance_owner'] = $this->instance->getOwner();
    }
    if (isset($this->activity))
    {
      $result['activity_name'] = $this->activity->getName();
      $result['activity_id'] = $this->activity_id;
      $result['activity_type'] = $this->activity->getType();
    }
    return $result;
  }

  /*!
  * This part of the runtime will be runned just after the "require_once ($source);" which is the inclusion
  * of compiled php file. We are in fact after all the "user code" part. We should decide what to do next
  * @param $debug is false by default
  * @return an array which must be analysed byr the application run class. It contains 2 keys
  *	* 'action' : value is a string is the action the run class should do
  *		* 'return' should return the result we just returned (in auto mode, to propagate infos)
  *		* 'loop' should just loop on the form, i.e.: do nothing
  *		* 'leaving' should show a page for the user leaving the activity (Cancel or Close without completing)
  *		* 'completed' should show a page for the user having completed the activity
  *	* 'engine_info' : value is an array is an array containing a lot of infos about what was done by the engine
  *		especially when completing the instance or when executing an automatic activity
  */
  function handle_postUserCode($debug=false)
  {
    $result = Array();

     // re-retrieve instance id which could have been modified by a complete
     $this->instance_id	= $this->instance->getInstanceId();

     //synchronised instance object with the database
     $this->instance->sync();

    // for interactive activities in non-auto mode:
    if (!($this->auto_mode) && $this->activity->isInteractive())
    {
      if ($this->instance->getActivityCompleted())
      {
        // activity is interactive and completed,
        // we have to continue the workflow
        // and send any autorouted activity which could be after this one
        // this is not done in the $instance->complete() to let
        // xxx_pos.php code be executed before sending the instance

        $result['engine_info'] =& $this->instance->sendAutorouted($this->activity_id);

        // application should display completed page
        $result['action']='completed';
        return $result;
      }
      // it hasn't been completed
      else
      {
        if ($GLOBALS['workflow']['__leave_activity'])
        {
          // activity is interactive and the activity source set the
          // $GLOBALS[workflow][__leave_activity] it's a 'cancel' mode.
          // we redirect the user to the leave activity page
          $result['action']='leaving';
          return $result;
        }
        else
        {
          //the activity is not completed and the user doesn't want to leave
          // we loop on the form
          $result['action']='loop';
          return $result;
        }
      }
    }
    else
    {
      // in auto mode or with non interactive activities we return engine info
      // and we collect our errors, we do not let them for other objects
      $this->collect_errors($debug);
      $result['engine_info']['debug'] = implode('<br />',array_filter($this->error));
      $result['engine_info']['info'] =& $this->getRuntimeInfo();
      $result['action'] = 'return';
      return $result;
    }
  }

  /*!
  * @public
  * Gets the the 'Activity Completed' status
  */
  function getActivityCompleted()
  {
    return $this->instance->getActivityCompleted();
  }


  //----------- Instance public function mapping -------------------------------------------

  /*!
  * Sets the next activity to be executed, if the current activity is
  * a switch activity the complete() method will use the activity setted
  * in this method as the next activity for the instance.
  * Note that this method receives an activity name as argument (Not an Id)
  * and that it does not need the activityId like the instance method
  * @param $actname : name of the next activity
  */
  function setNextActivity($actname)
  {
    return $this->instance->setNextActivity($this->activity_id,$actname);
  }

  /*!
  * This method can be used to set the user that must perform the next
  * activity of the process. this effectively "assigns" the instance to
  * some user.
  * @param $user is the next user id
  */
  function setNextUser($user)
  {
    return $this->instance->setNextUser($user);
  }

  /*!
  * This method can be used to get the user that must perform the next
  * activity of the process. This can be empty if no setNextUser() was done before.
  * It wont return the default user but only the user which was assigned by a setNextUser.
  */
  function getNextUser()
  {
    return $this->instance->getNextUser();
  }

  /*!
  * Sets the name of this instance.
  * @param $value is the new name of the instance
  */
  function setName($value)
  {
    return $this->instance->setName($value);
  }

  /*!
  * Get the name of this instance.
  */
  function getName() {
    return $this->instance->getName();
  }

  /*!
  * Sets the category of this instance.
  */
  function setCategory($value)
  {
    return $this->instance->setcategory($value);
  }

  /*!
  * Get the category of this instance.
  */
  function getCategory()
  {
    return $this->instance->getCategory();
  }

  /*!
  * Sets a property in this instance. This method is used in activities to
  * set instance properties.
  * all property names are normalized for security reasons and to avoid localisation
  * problems (A->z, digits and _ for spaces). If you have several set to call look
  * at the setProperties function. Each call to this function has an impact on database
  * @param $name is the property name (it will be normalized)
  * @value is the value you want for this property
  * @return true if it was ok
  */
  function set($name,$value)
  {
    return $this->instance->set($name,$value);
  }

  /*!
  * Sets several properties in this instance. This method is used in activities to
  * set instance properties. Use this method if you have several properties to set
  * as it will avoid
  * all property names are normalized for security reasons and to avoid localisation
  * problems (A->z, digits and _ for spaces). If you have several set to call look
  * at the setProperties function. Each call to this function has an impact on database
  * @param $properties_array is an associative array containing for each record the
  * property name as the key and the property value as the value. You do not need the complete
  * porperty array, you can give only the knew or updated properties.
  * @return true if it was ok
  */
  function setProperties($properties_array)
  {
     return $this->instance->setProperties($properties_array);
  }


  /*!
  * Gets the value of an instance property.
  * @param $name is the name of the instance
  * @return false if the property was not found, but an error message is stored
  * in the instance object
  */
  function get($name)
  {
    return $this->instance->get($name);
  }

  /*!
  * Returns an array of assocs describing the activities where the instance
  * is present, can be more than one activity if the instance was "splitted"
  */
  function getActivities()
  {
    return $this->instance->getActivities();
  }

  /*!
  * Gets the instance status can be
  * 'completed', 'active', 'aborted' or 'exception'
  */
  function getStatus()
  {
    return $this->instance->getStatus();
  }

  /*!
  * Sets the instance status , the value can be:
  * @param $status is the status you want, it can be:
  * 'completed', 'active', 'aborted' or 'exception'
  * @return true or false
  */
  function setStatus($status)
  {
    return $this->instance->setStatus($status);
  }

  /*!
  * Gets the instance priority, it's an integer
  */
  function getPriority()
  {
    return $this->instance->getPriority();
  }

  /*!
  * Sets the instance priority,
  * @param $priority should be an integer
  */
  function setPriority($priority)
  {
    return $this->instance->setPriority($priority);
  }

  /*!
  * Returns the instanceId
  */
  function getInstanceId()
  {
    return $this->instance->getInstanceId();
  }

  /*!
  Returns the processId for this instance
  */
  function getProcessId() {
    return $this->instance->getProcessId();
  }

  /*!
  * Returns the owner of the instance
  */
  function getOwner()
  {
    return $this->instance->getOwner();
  }

  /*!
  * Sets the instance owner
  * @user is the user id of the owner
  */
  function setOwner($user)
  {
    return $this->instance->setOwner($user);
  }

  /*!
  * Returns the user that must execute or is already executing an activity
  * where the instance is present.
  * @param $activityId is the activity id
  * @return false if the activity was not found for the instance, else return the user id
  * or '*' if no user is defined yet.
  */
  function getActivityUser($activityId)
  {
    return $this->instance->getActivityUser($activityId);
  }

  /*!
  * Sets the status of the instance in some activity
  * @param $activityId is the activity id
  * @param $status is the new status, it can be 'running' or 'completed'
  * @return false if no activity was found for the instance
  */
  function setActivityStatus($activityId,$status)
  {
    return $this->instance->setActivityStatus($activityId,$status);
  }


  /*!
  * Gets the status of the instance in some activity, can be
  * 'running' or 'completed'
  * @param $activityId is the activity id
  */
  function getActivityStatus($activityId)
  {
    return $this->instance->getActivityStatus($activityId);
  }

  /*!
  * Resets the start time of the activity indicated to the current time.
  * @param $activityId is the activity id
  */
  function setActivityStarted($activityId)
  {
    return $this->instance->setActivityStarted($activityId);
  }

  /*!
  * Gets the Unix timstamp of the starting time for the given activity.
  * @param $activityId is the activity id
  */
  function getActivityStarted($activityId)
  {
    return $this->instance->getActivityStarted($activityId);
  }

  /*!
  * Gets the time where the instance was started
  * @return a Unix timestamp
  */
  function getStarted()
  {
    return $this->instance->getStarted();
  }

  /*!
  * Gets the end time of the instance (when the process was completed)
  */
  function getEnded()
  {
    return $this->instance->getEnded();
  }


  //! Completes an activity
  /*!
  * YOU MUST NOT CALL complete() for non-interactive activities since
  * the engine does automatically complete automatic activities after
  * executing them.
  * @return true or false, if false it means the complete was not done for some internal reason
  * consult get_error() for more informations
  */
  function complete()
  {
    if (!($this->activity->isInteractive()))
    {
      $this->error[] = tra('interactive activities should not call the complete() method');
      return false;
    }

    return $this->instance->complete($this->activity_id);
  }

  /*!
  * Aborts an activity and terminates the whole instance. We still create a workitem to keep track
  * of where in the process the instance was aborted
  */
  function abort()
  {
    return $this->instance->abort();
  }

  /*!
  * Gets a comment for this instance
  * @param $cId is the comment id
  */
  function get_instance_comment($cId)
  {
    return $this->instance->get_instance_comment($cId);
  }

  /*!
  * Inserts or updates an instance comment
  */
  function replace_instance_comment($cId, $activityId, $activity, $user, $title, $comment)
  {
    return $this->instance->replace_instance_comment($cId, $activityId, $activity, $user, $title, $comment);
  }

  /*!
  * Removes an instance comment
  * @param $cId is the comment id
  */
  function remove_instance_comment($cId)
  {
    return $this->instance->remove_instance_comment($cId);
  }

  /*!
  * Lists instance comments
  */
  function get_instance_comments()
  {
    return $this->instance->get_instance_comments();
  }
}


?>
