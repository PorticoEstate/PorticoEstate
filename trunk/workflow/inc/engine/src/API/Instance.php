<?php
require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'common' . '/' . 'Base.php');
require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'common' . '/' . 'WfSecurity.php');
require_once(GALAXIA_LIBRARY . '/' . 'src' . '/' . 'ProcessManager' . '/' . 'ActivityManager.php');

//!! Instance
//! A class representing a process instance.
/*!
This class represents a process instance, it is used when any activity is
executed. The $instance object is created representing the instance of a
process being executed in the activity or even a to-be-created instance
if the activity is a start activity.
*/
class Instance extends Base {
  //theses are the member of the instance object changed_ vars are internal members
  //use to detect conflicts on sync with the database
  var $changed = Array('properties' => Array(), 'nextActivity' => Array());
  var $properties = Array();
  var $owner = '';
  var $status = '';
  var $started;
  var $nextActivity=Array();
  var $nextUser;
  var $ended;
  var $name='';
  var $category;
  var $priority = 1;
  /// Array of assocs(activityId, status, started, ended, user, name, interactivity, autorouting)
  var $activities = Array();
  var $pId;
  var $instanceId = 0;
  /// An array of workitem ids, date, duration, activity name, user, activity type and interactivity
  var $workitems = Array();
  //a security object to perform some tests and locks
  var $security;
  // this is an internal reminder
  var $__activity_completed=false;
  //indicator, if true we are not synchronised in the memory object with the database, see sync()
  var $unsynch=false;

  //! Constructor
  function Instance($db)
  {
    $this->child_name = 'Instance';
    parent::Base($db);
  }

  /*!
  * Method used to load an instance data from the database.
  *
  * This function will load/initialize members of the instance object from the database
  * it will populate all members and will by default populate the related activities array
  * and the workitems (history) array.
  *
  * @param $instanceId
  * @param $load_activities true by default, do we need to reload activities from the database?
  * @param $load_workitems true by default, do we need to reload workitems from the database?
  * @deprecated deprecated since 1.3 see LoadInstanceFromDb instead
  */
  function getInstance($instanceId, $load_activities=true, $load_workitems=true)
  {
	trigger_error('Instance->getInstance is deprecated use loadInstance instead',E_USER_WARNING);
	return $this->loadInstance($instanceId, $load_activities, $load_workitems);
  }

  /*!
  * Method used to load an instance data from the database.
  *
  * This function will load/initialize members of the instance object from the database
  * it will populate all members and will by default populate the related activities array
  * and the workitems (history) array.
  * @param $instanceId
  * @param $load_activities true by default, do we need to reload activities from the database?
  * @param $load_workitems true by default, do we need to reload workitems from the database?
  * @return true if everything was ok, false in the other case
  */
  function loadInstance($instanceId, $load_activities=true, $load_workitems=true)
  {
    if (!($instanceId)) return true; //start activities for example - pseudo instances
    // Get the instance data
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instances` where `wf_instance_id`=?";
    $result = $this->query($query,array((int)$instanceId));
    if( empty($result) || (!$result->numRows())) return false;
    $res = $result->fetchRow();

    //Populate
    $this->properties = unserialize(base64_decode($res['wf_properties']));
    $this->status = $res['wf_status'];
    $this->pId = $res['wf_p_id'];
    $this->instanceId = $res['wf_instance_id'];
    $this->priority = $res['wf_priority'];
    $this->owner = $res['wf_owner'];
    $this->started = $res['wf_started'];
    $this->ended = $res['wf_ended'];
    $this->nextActivity = unserialize(base64_decode($res['wf_next_activity']));
    $this->nextUser = $res['wf_next_user'];
    $this->name = $res['wf_name'];
    $this->category = $res['wf_category'];

    // Get the activities where the instance is (nothing for start activities)
    if ($load_activities)
    {
      $this->_populate_activities($instanceId);

    }

    // Get the workitems where the instance is
    if ($load_workitems)
    {
      $query = "select wf_item_id, wf_order_id, gw.wf_instance_id, gw.wf_activity_id, wf_started, wf_ended, gw.wf_user,
              ga.wf_name, ga.wf_type, ga.wf_is_interactive
              from ".GALAXIA_TABLE_PREFIX."workitems gw
              INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON ga.wf_activity_id = gw.wf_activity_id
              where wf_instance_id=? order by wf_order_id ASC";
      $result = $this->query($query,array((int)$instanceId));
      if (!(empty($result)))
      {
        while($res = $result->fetchRow())
        {
          $this->workitems[]=$res;
        }
      }
      return true;
    }

  }

  /*!
  * @private
  * Function used to load all activities related to the insance given in parameter in the activities array
  * @param $instanceId is the instanceId
  */
  function _populate_activities($instanceId)
  {
    $this->activities=Array();
    $query = "select gia.wf_activity_id, gia.wf_instance_id, wf_started, wf_ended, wf_started, wf_user, wf_status,
            ga.wf_is_autorouted, ga.wf_is_interactive, ga.wf_name, ga.wf_type
            from ".GALAXIA_TABLE_PREFIX."instance_activities gia
            INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON ga.wf_activity_id = gia.wf_activity_id
            where wf_instance_id=?";
    $result = $this->query($query,array((int)$instanceId));
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        $this->activities[] = $res;
      }
    }
  }

  /*!
  * @private
  */
  function _synchronize_member(&$changed,&$init,&$actual,$name,$fieldname,&$namearray,&$vararray)
  {
    //if we work with arrays then it's more complex
    //echo "<br>$name is_array?".(is_array($changed)); _debug_array($changed);
    if (!(is_array($changed)))
    {
      if (isset($changed))
      {
        //detect unsynchro
        if (!($actual==$init))
        {
          $this->error[] = tra('Instance: unable to modify %1, someone has changed it before us', $name);
        }
        else
        {
          $namearray[] = $fieldname;
          $vararray[] = $changed;
          $actual = $changed;
        }
	//seems to be not working
        unset ($changed);
      }
    }
    else //we are working with arrays (properties for example)
    {
      $modif_done = false;
      foreach ($changed as $key => $value)
      {
        //detect unsynchro
        if (!($actual[$key]==$init[$key]))
        {
          $this->error[] = tra('Instance: unable to modify %1 [%2], someone has changed it before us', $name, $key);
        }
        else
        {
          $actual[$key] = $value;
          $modif_done = true;
        }
      }
      if ($modif_done) //at least one modif
      {
        $namearray[] = $fieldname;
        //no more serialize, done by the core security_cleanup
        $vararray[] = $actual; //serialize($actual);
      }
      $changed=Array();
    }
  }

  /*!
  * @public
  * Synchronize the instance object with the database. All change smade will be recorded except
  * conflicting ones (changes made on members or properties that has been changed by another source
  * --could be another 'instance' of this instance or an admin form-- since the last call of sync() )
  * the unsynch private member is used to test if more heavy tests should be done or not
  * pseudo instances (start, standalone) are not synchronised since there is no record on database
  */
  function sync()
  {
    if ( (!($this->instanceId)) || (!($this->unsynch)) )
    {
      //echo "<br>nothing to do ".$this->unsynch;
      return true;
    }
    //echo "<br> synch!";_debug_array($this->changed);
    //do it in a transaction, can have several activities running
    $this->db->StartTrans();
    //we need to make a row lock now,
    $where = 'wf_instance_id='.(int)$this->instanceId;
    if (!($this->db->RowLock(GALAXIA_TABLE_PREFIX.'instances', $where)))
    {
      $this->error[] = 'sync: '.tra('failed to obtain lock on %1 table', 'instances');
      $this->db->FailTrans();
    }
    else
    {
      //wf_p_id and wf_instance_id are set in creation only.
      //we remember initial values
      $init_properties = $this->properties;
      $init_status = $this->status;
      $init_priority = $this->priority;
      $init_owner = $this->owner;
      $init_started = $this->started;
      $init_ended = $this->ended;
      $init_nextUser = $this->nextUser;
      $init_nextActivity = $this->nextActivity;
      $init_name = $this->name;
      $init_category = $this->category;
      // we re-read instance members to detect conflicts, changes made while we were unsynchronised
		// TODO: there is instanceID and instance_id, all around that's bad!!!
      $this->loadInstance($this->instanceId, false, false);

      // Now for each modified field we'll change the database value if nobody has changed
      // the database value before us
      $bindvars = Array();
      $querysets = Array();
      $queryset = '';
      $this->_synchronize_member($this->changed['status'],$init_status,$this->status,tra('status'),'wf_status',$querysets,$bindvars);
	  unset ($this->changed['status']);
      $this->_synchronize_member($this->changed['priority'],$init_priority,$this->priority,tra('priority'),'wf_priority',$querysets,$bindvars);
	  unset ($this->changed['priority']);
      $this->_synchronize_member($this->changed['owner'],$init_owner,$this->owner,tra('owner'),'wf_owner',$querysets,$bindvars);
	  unset ($this->changed['owner']);
      $this->_synchronize_member($this->changed['started'],$init_started,$this->started,tra('started'),'wf_started',$querysets,$bindvars);
	  unset ($this->changed['started']);
      $this->_synchronize_member($this->changed['ended'],$init_ended,$this->ended,tra('ended'),'wf_ended',$querysets,$bindvars);
	  unset ($this->changed['ended']);
      $this->_synchronize_member($this->changed['name'],$init_name,$this->name,tra('name'),'wf_name',$querysets,$bindvars);
	  unset ($this->changed['name']);
      $this->_synchronize_member($this->changed['category'],$init_category,$this->category,tra('category'),'wf_category',$querysets,$bindvars);
      $this->_synchronize_member($this->changed['properties'],$init_properties,$this->properties,tra('property'),'wf_properties',$querysets,$bindvars);
      $this->_synchronize_member($this->changed['nextActivity'],$init_nextActivity,$this->nextActivity,tra('next activity'),'wf_next_activity',$querysets,$bindvars);
      $this->_synchronize_member($this->changed['nextUser'],$init_nextUser,$this->nextUser,tra('next user'),'wf_next_user',$querysets,$bindvars);
	  unset ($this->changed['nextUser']);

      if (!(empty($querysets)))
      {
        $queryset = implode(' = ?,', $querysets). ' = ?';
        $query = 'update '.GALAXIA_TABLE_PREFIX.'instances set '.$queryset
              .' where wf_instance_id=?';
        $bindvars[] = $this->instanceId;
        //echo "<br> query $query"; _debug_array($bindvars);
        $this->query($query,$bindvars);
      }
    }
    if (!($this->db->CompleteTrans()))
    {
      $this->error[] = tra('failed to synchronize instance data with the database');
      return false;
    }
    //we are not unsynchronized anymore.
    $this->unsynch = false;
    return true;
  }

  /*!
  * Sets the next activity to be executed, if the current activity is
  * a switch activity the complete() method will use the activity setted
  * in this method as the next activity for the instance.
  * The object records an array of transitions, as the instance can be splitted in several
  * running activities, transition from the current activity to the given activity will
  * be recorded and all previous recorded transitions starting from the current activity
  * will be deleted.
  * @param $activityId is the running activity Id
  * @param $actname Warning this method receives an activity name as argument. (Not an Id)
  * @return true or false
  */
  function setNextActivity($activityId, $actname)
  {
    $pId = $this->pId;
    $actname=trim($actname);
    $aid = $this->getOne('select wf_activity_id from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_name=?',array($pId,$actname));
    if (!($aid))
    {
      $this->error[] = tra('setting next activity to an unexisting activity');
      return false;
    }
    $this->changed['nextActivity'][$activityId]=$aid;
    $this->unsynch = true;
    return true;
  }

  /*!
  This method can be used to set the user that must perform the next
  activity of the process. this effectively "assigns" the instance to
  some user.
  */
  function setNextUser($user) {
	// TODO: check if $user<>changed['nextUser'] before unsynching
    $this->changed['nextUser'] = $user;
    $this->unsynch = true;
    return true;
  }

  /*!
  This method can be used to get the user that must perform the next
  activity of the process. This can be empty if no setNextUser was done before.
  It wont return the default user but only the user which was assigned by a setNextUser.
  */
  function getNextUser()
  {
    if (!(isset($this->changed['nextUser'])))
    {
      return $this->nextUser;
    }
    else
    {
      return $this->changed['nextUser'];
    }
  }

  /*!
  * @private
  * Creates a new instance.
  * This method is called in start activities when the activity is completed
  * to create a new instance representing the started process.
  * @param $activityId is the start activity id
  * @param $user is the current user id
  * @return true if all things goes well
  */
  function _createNewInstance($activityId,$user) {
    // Creates a new instance setting up started, ended, user, status and owner
    $pid = $this->getOne('select wf_p_id from '.GALAXIA_TABLE_PREFIX.'activities where wf_activity_id=?',array((int)$activityId));
    $this->pId = $pid;
    $this->setStatus('active');
    $this->setNextUser('');
	//we pass extra args to started and owner to ignore synchro as we'll insert them just here
    $now = date("U");
	$this->setStarted($now, true);
	$this->setOwner($user, true);

    //we insert started and owner here and we used them to detect instanceId, this could disturb synchro
	$query = 'insert into '.GALAXIA_TABLE_PREFIX.'instances
	  (wf_started,wf_ended,wf_p_id,wf_owner,wf_properties)
      values(?,?,?,?,?)';
	$this->query($query,array($now,0,$pid,$user,$this->security_cleanup(Array(),false)));
    $this->instanceId = $this->getOne('select max(wf_instance_id) from '.GALAXIA_TABLE_PREFIX.'instances
                      where wf_started=? and wf_owner=?',array((int)$now,$user));
    $iid=$this->instanceId;

    // Then add in instance_activities an entry for the
    // activity the user and status running and started now
    $query = 'insert into '.GALAXIA_TABLE_PREFIX.'instance_activities (wf_instance_id,wf_activity_id,wf_user,
            wf_started,wf_status) values(?,?,?,?,?)';
    $this->query($query,array((int)$iid,(int)$activityId,$user,(int)$now,'running'));

    //update database with other datas stored in the object
	//echo "<br/> syncing  in _createNewInstance";
	return $this->sync();
  }

  /*!
  Sets the name of this instance.
  */
  function setName($value)
  {
    $this->changed['name'] = substr($value,0,120);
    $this->unsynch = true;
    return true;
  }

  /*!
  Get the name of this instance.
  */
  function getName()
  {
    if (!(isset($this->changed['name'])))
    {
      return $this->name;
    }
    else
    {
      return $this->changed['name'];
    }
  }

  /*!
  * Sets the category of this instance.
  */
  function setCategory($value)
  {
    $this->changed['category'] = $value;
    $this->unsynch = true;
    return true;
  }

  /*!
  * Get the category of this instance.
  */
  function getCategory()
  {
    if (!(isset($this->changed['category'])))
    {
      return $this->category;
    }
    else
    {
      return $this->changed['category'];
    }
  }

  /*!
  * @private
  * Normalizes a property name
  * @param $name is the name you want to normalize
  * @return the property name
  */
  function _normalize_name($name)
  {
    $name = trim($name);
    $name = str_replace(" ","_",$name);
    $name = preg_replace("/[^0-9A-Za-z\_]/",'',$name);
    return $name;
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
    $name = $this->_normalize_name($name);
    $this->changed['properties'][$name] = $this->security_cleanup($value);
    $this->unsynch = true;
    return true;
  }

  /*!
  * Sets several properties in this instance. This method is used in activities to
  * set instance properties. Use this method if you have several properties to set
  * as it will avoid to re-call the SQL engine for each property.
  * all property names are normalized for security reasons and to avoid localisation
  * problems (A->z, digits and _ for spaces).
  * @param $properties_array is an associative array containing for each record the
  * property name as the key and the property value as the value.
  * @return true if it was ok
  */
  function setProperties($properties_array)
  {
    $backup_values = $this->properties;
    foreach ($properties_array as $key => $value)
    {
      $name = $this->_normalize_name($key);
      $this->changed['properties'][$name] = $this->security_cleanup($value);
    }
    $this->unsynch = true;
    return true;
  }


  /*!
  * Gets the value of an instance property.
  * @param $name is the name of the property
  * @return false if the property was not found, in this case an error message is stored in
  * the instance object
  */
  function get($name)
  {
    $name = $this->_normalize_name($name);
    if(isset($this->changed['properties'][$name]))
    {
      return $this->changed['properties'][$name];
    }
    elseif(isset($this->properties[$name]))
    {
      return $this->properties[$name];
    }
    else
    {
      $this->error[] = tra('property %1 not found', $name);
      return false;
    }
  }

  /*!
  Returns an array of assocs describing the activities where the instance
  is present, can be more than one activity if the instance was "splitted"
  */
  function getActivities() {
    return $this->activities;
  }

  /*!
  Gets the instance status can be
  'completed', 'active', 'aborted' or 'exception'
  */
  function getStatus() {
    if (!(isset($this->changed['status'])))
    {
      return $this->status;
    }
    else
    {
      return $this->changed['status'];
    }
  }

  /*!
  * Sets the instance status
  * @param $status is the status you want, it can be:
  * 'completed', 'active', 'aborted' or 'exception'
  * @return true or false
  */
  function setStatus($status)
  {
    if (!(($status=='completed') || ($status=='active') || ($status=='aborted') || ($status=='exception')))
    {
      $this->error[] = tra('unknown status');
      return false;
    }
    $this->changed['status'] = $status;
    $this->unsynch = true;
    return true;
  }

  /*!
  Gets the instance priority, it's an integer
  */
  function getPriority()
  {
    if (!(isset($this->changed['priority'])))
    {
      return $this->priority;
    }
    else
    {
      return $this->changed['priority'];
    }
  }

  /*!
  Sets the instance priority , the value should be an integer
  */
  function setPriority($priority)
  {
    $mypriority = (int)$priority;
    $this->changed['priority'] = $mypriority;
    $this->unsynch = true;
    return true;
  }

  /*!
  Returns the instanceId
  */
  function getInstanceId()
  {
    return $this->instanceId;
  }

  /*!
  Returns the processId for this instance
  */
  function getProcessId()
  {
    return $this->pId;
  }

  /*!
  Returns the user that created the instance
  */
  function getOwner()
  {
    if (!(isset($this->changed['owner'])))
    {
      return $this->owner;
    }
    else
    {
      return $this->changed['owner'];
    }
  }

  /*!
  * Sets the instance creator user.
  * @param $user is the new owner id, musn't be false, 0 or empty
  * @param $ignore_unsynch is false by default, used to set owner with a user already set in database, do not use it unless
  * you know very well what you're doing
  * @return true if the change was done
  */
  function setOwner($user,$ignore_unsynch=false)
  {
    if (empty($user))
    {
		return false;
    }
	if ($ignore_unsynch)
	{
		$this->owner=$user;
	}
	else
	{
		$this->changed['owner'] = $user;
		$this->unsynch = true;
	}
    return true;
  }

  /*!
  * Sets the user that must execute the activity indicated by the activityId.
  * Note that the instance MUST be present in the activity to set the user,
  * you can't program who will execute an activity.
  * If the user is empty then the activity user is setted to *, allowing any
  * authorised user to take the token later
  *
  * concurrent access to this function is normally handled by WfRuntime and WfSecurity
  * theses objects are the only ones which should call this function. WfRuntime is handling the
  * current transaction and WfSecurity is Locking the instance and instance_activities table on
  * a 'run' action which is the action leading to this setActivityUser call (could be a release
  * as well on auto-release)
  * @param $activityId is the activity Id
  * @param $theuser is the user id or '*' (or 0, '' or null which will be set to '*')
  * @return false if something was wrong
  */
  function setActivityUser($activityId,$theuser) {
    if(empty($theuser)) $theuser='*';
    $found = false;
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['wf_activity_id']==$activityId) {
        // here we are in the good activity
        $found = true;

        // prepare queries
        $where = ' where wf_activity_id=? and wf_instance_id=?';
        $bindvars = array((int)$activityId,(int)$this->instanceId);
        if(!($theuser=='*'))
        {
          $where .= ' and (wf_user=? or wf_user=?)';
          $bindvars[]= $theuser;
          $bindvars[]= '*';
        }

        // update the user
        $query = 'update '.GALAXIA_TABLE_PREFIX.'instance_activities set wf_user=?';
        $query .= $where;
        $bindvars_update = array_merge(array($theuser),$bindvars);
        $this->query($query,$bindvars_update);
        $this->activities[$i]['wf_user']=$theuser;
        return true;
      }
    }
    // if we didn't find the activity it will be false
    return $found;
  }

  /*!
  Returns the user that must execute or is already executing an activity
  wherethis instance is present.
  */
  function getActivityUser($activityId) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['wf_activity_id']==$activityId) {
        return $this->activities[$i]['wf_user'];
      }
    }
    return false;
  }

  /*!
  * Sets the status of the instance in some activity
  * @param $activityId is the activity id
  * @param $status is the new status, it can be 'running' or 'completed'
  * @return false if no activity was found for the instance
  */
  function setActivityStatus($activityId,$status)
  {
    if (!(($status=='running') || ($status=='completed')))
    {
      $this->error[] = tra('unknown status');
      return false;
    }
    for($i=0;$i<count($this->activities);$i++)
    {
      if($this->activities[$i]['wf_activity_id']==$activityId)
      {
        $query = 'update '.GALAXIA_TABLE_PREFIX.'instance_activities set wf_status=? where wf_activity_id=? and wf_instance_id=?';
        $this->query($query,array($status,(int)$activityId,(int)$this->instanceId));
        return true;
      }
    }
    $this->error[] = tra('new status not set, no corresponding activity was found.');
    return false;
  }


  /*!
  Gets the status of the instance in some activity, can be
  'running' or 'completed'
  */
  function getActivityStatus($activityId) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['wf_activity_id']==$activityId) {
        return $this->activities[$i]['wf_status'];
      }
    }
    $this->error[] = tra('activity status not avaible, no corresponding activity was found.');
    return false;
  }

  /*!
  Resets the start time of the activity indicated to the current time.
  */
  function setActivityStarted($activityId) {
    $now = date("U");
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['wf_activity_id']==$activityId) {
        $this->activities[$i]['wf_started']=$now;
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `wf_started`=? where `wf_activity_id`=? and `wf_instance_id`=?";
        $this->query($query,array($now,(int)$activityId,(int)$this->instanceId));
        return true;
      }
    }
    $this->error[] = tra('activity start not set, no corresponding activity was found.');
    return false;
  }

  /*!
  Gets the Unix timstamp of the starting time for the given activity.
  */
  function getActivityStarted($activityId) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['wf_activity_id']==$activityId) {
        return $this->activities[$i]['wf_started'];
      }
    }
    $this->error[] = tra('activity start not avaible, no corresponding activity was found.');
    return false;
  }

  /*!
  * @private
  * Gets an activity from the list of activities of the instance
  * the result is an array describing the instance
  * @param $activityId is the activity id
  * @returns the activity id or false if the activity was not found
  */
  function _get_instance_activity($activityId)
  {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['wf_activity_id']==$activityId) {
        return $this->activities[$i];
      }
    }
    $this->error[] = tra('no corresponding activity was found.');
    return false;
  }

  /*!
  * Sets the time where the instance was started.
  * @param $time is the started time
  * @param $ignore_unsynch is false by default, used to set started with a time already set in database, do not use it unless
  * you know very well what you're doing
  * @returns true if everything is well done
  */
  function setStarted($time,$ignore_unsynch=false)
  {
		 if ($ignore_unsynch)
		 {
				$this->started=$time;
		 }
		 else
		 {
				 $this->changed['started'] = $time;
				 $this->unsynch = true;
		 }
		 return true;
  }

  /*!
  Gets the time where the instance was started (Unix timestamp)
  */
  function getStarted()
  {
    if (!(isset($this->changed['started'])))
    {
      return $this->started;
    }
    else
    {
      return $this->changed['started'];
    }
  }

  /*!
  Sets the end time of the instance (when the process was completed)
  */
  function setEnded($time)
  {
    $this->changed['ended']=$time;
    $this->unsynch = true;
    return true;
  }

  /*!
  Gets the end time of the instance (when the process was completed)
  */
  function getEnded()
  {
    if (!(isset($this->changed['ended'])))
    {
      return $this->ended;
    }
    else
    {
      return $this->changed['ended'];
    }
  }

  /*!
  * @private
  * This set to true or false the 'Activity Completed' status which will
  * be important to know if the user code has completed the current activity
  * @param $bool is true by default, it will be the next status of the 'Activity Completed' indicator
  */
  function setActivityCompleted($bool)
  {
    $this->__activity_completed = $bool;
  }

  /*!
  * @public
  * Gets the 'Activity Completed' status
  */
  function getActivityCompleted()
  {
    return $this->__activity_completed;
  }

  /*
  * @public
  * This function can be called by the instance object himself (for automatic activities)
  * or by the WfRuntime object. In interactive activities code users use complete() --without args--
  * which refer to the WfRuntime->complete() function which call this one.
  * In non-interactive activities a call to a complete() will generate errors because the engine
  * does it his own way as I said first.
  * Particularity of this Complete is that it is Transactional, i.e. it it done completely
  * or not and row locks are ensured.
  * @param $activityId is the activity that is being completed
  * @param $addworkitem indicates if a workitem should be added for the completed
  * activity (true by default).
  * @return true or false, if false it means the complete was not done for some internal reason
  * consult $instance->get_error() for more informations
  */
  function complete($activityId,$addworkitem=true)
  {
    //ensure it's false at first
    $this->setActivityCompleted(false);

    //The complete() is in a transaction, it will be completly done or not at all
    $this->db->StartTrans();

    //lock rows and ensure access is granted
    if (!(isset($this->security))) $this->security =& new WfSecurity($this->db);
    if (!($this->security->checkUserAction($activityId,$this->instanceId,'complete')))
    {
      $this->error[] = tra('you were not allowed to complete the activity');
      $this->db->FailTrans();
    }
    else
    {
      if (!($this->_internalComplete($activityId,$addworkitem)))
      {
        $this->error[] = tra('The activity was not completed');
        $this->db->FailTrans();
      }
    }
    //we mark completion with result of the transaction wich will be false if any error occurs
    //this is the end of the transaction
    $this->setActivityCompleted($this->db->CompleteTrans());

    //we return the completion state.
    return $this->getActivityCompleted();
  }

  /*!
  * @private
  * YOU MUST NOT CALL _internalComplete() directly, use Complete() instead
  * @param $activityId is the activity that is being completed
  * @param $addworkitem indicates if a workitem should be added for the completed
  * activity (true by default).
  * @return true or false, if false it means the complete was not done for some internal reason
  * consult $instance->get_error() for more informations
  */
  function _internalComplete($activityId,$addworkitem=true) {
    global $user;

    if(empty($user))
    {
      $theuser='*';
    }
    else
    {
      $theuser=$user;
    }

    if(!($activityId))
    {
      $this->error[] = tra('it was impossible to complete, no activity was given.');
      return false;
    }

    $now = date("U");

    // If we are completing a start activity then the instance must
    // be created first!
    $type = $this->getOne('select wf_type from '.GALAXIA_TABLE_PREFIX.'activities where wf_activity_id=?',array((int)$activityId));
    if($type=='start')
    {
      if (!($this->_createNewInstance((int)$activityId,$theuser)))
      {
        return false;
      }
    }
    else
    {
      // Now set ended
      $query = 'update '.GALAXIA_TABLE_PREFIX.'instance_activities set wf_ended=? where wf_activity_id=? and wf_instance_id=?';
      $this->query($query,array((int)$now,(int)$activityId,(int)$this->instanceId));
    }

    //Set the status for the instance-activity to completed
    //except for start activities
    if (!($type=='start'))
    {
      if (!($this->setActivityStatus($activityId,'completed')))
      {
        return false;
      }
    }

    //If this and end actt then terminate the instance
    if($type=='end')
    {
      if (!($this->terminate($now)))
      {
        return false;
      }
    }

    //now we synchronise instance with the database
		//echo "<br/> syncing  in _internalComplete";
	if (!($this->sync())) return false;

    //Add a workitem to the instance
    if ($addworkitem)
    {
      return $this->addworkitem($type,$now, $activityId);
    }
    else
    {
      return true;
    }
  }

  /*!
  * @private
  * This function will add a workitem in the workitems table. The instance MUST be synchronised before
  * calling this function.
  * @param $activity_type is the activity type, needed because internals are different for start activities
  * @param $ended is the ending time
  * @param $activityId is the finishing activity id
  */
  function addworkitem($activity_type, $ended, $activityId)
  {
    $iid = $this->instanceId;
    $max = $this->getOne('select max(wf_order_id) from '.GALAXIA_TABLE_PREFIX.'workitems where wf_instance_id=?',array((int)$iid));
    if(!$max)
    {
        $max=1;
    }
    else
    {
        $max++;
    }
    if($activity_type=='start')
    {
      //Then this is a start activity ending
      $started = $this->getStarted();
      //at this time owner is the creator
      $putuser = $this->getOwner();
    }
    else
    {
      $act = $this->_get_instance_activity($activityId);
      if(!$act)
      {
        //this will abort the function
        $this->error[] = tra('failed to create workitem');
        return false;
      }
      else
      {
        $started = $act['wf_started'];
        $putuser = $act['wf_user'];
      }
    }
    //no more serialize, done by the core security_cleanup
    $properties = $this->security_cleanup($this->properties, false); //serialize($this->properties);
    $query='insert into '.GALAXIA_TABLE_PREFIX.'workitems
        (wf_instance_id,wf_order_id,wf_activity_id,wf_started,wf_ended,wf_properties,wf_user) values(?,?,?,?,?,?,?)';
    $this->query($query,array((int)$iid,(int)$max,(int)$activityId,(int)$started,(int)$ended,$properties,$putuser));
    return true;
  }

  //! Send autorouted activities to the next one(s). Private engine function
  /*
  * The arguments are explained just in case.
  * @param $activityId is the activity that is being completed, when this is not
  * passed the engine takes it from the $_REQUEST array,all activities
  * are executed passing the activityId in the URI.
  * @param $force indicates that the instance must be routed no matter if the
  * activity is auto-routing or not. This is used when "sending" an
  * instance from a non-auto-routed activity to the next activity.
  * @private
  * YOU MUST NOT CALL sendAutorouted() for non-interactive activities since
  * the engine does automatically complete and send automatic activities after
  * executing them.
  * This function is in fact a Private function runned by the engine. You should
  * never use it without knowing very very well what you're doing.
  * @return false or an array with ['transition']['failure'] set in case of any problem,
  * true if nothing was done and an array if something done, like walk on transition
  * and execution of an activity (see sendTo comments) or if this activity was a split
  * activity (in this case the array contains a row for each following activity)
  */
  function sendAutorouted($activityId,$force=false)
  {
    $returned_value = Array();
    $type = $this->getOne("select `wf_type` from `".GALAXIA_TABLE_PREFIX."activities` where `wf_activity_id`=?",array((int)$activityId));
    //on a end activity we have nothing to do
    if ($type == 'end')
    {
      return true;
    }
    //If the activity ending is not autorouted then we have nothing to do
    if (!(($force) || ($this->getOne("select `wf_is_autorouted` from `".GALAXIA_TABLE_PREFIX."activities` where `wf_activity_id`=?",array($activityId)) == 'y')))
    {
      $returned_value['transition']['status'] = 'not autorouted';
      return $returned_value;
    }
    //If the activity ending is autorouted then send to the activity
    // Now determine where to send the instance
    $query = "select `wf_act_to_id` from `".GALAXIA_TABLE_PREFIX."transitions` where `wf_act_from_id`=?";
    $result = $this->query($query,array((int)$activityId));
    $candidates = Array();
    while ($res = $result->fetchRow())
    {
      //candidates store activities we can reach from our running activity
      $candidates[] = $res['wf_act_to_id'];
    }
    if($type == 'split')
    {
      $erase_from = false;
      $num_candidates = count($candidates);
      $returned_data = Array();
      $i = 1;
      foreach ($candidates as $cand)
      {
        // only erase split activity in instance when all the activities comming from the split have been set up
        if ($i == $num_candidates)
        {
          $erase_from = true;
        }
        $returned_data[$i] = $this->sendTo($activityId,$cand,$erase_from);
        $i++;
      }
      return $returned_data;
    }
    elseif($type == 'switch')
    {
      if (in_array($this->nextActivity[$activityId],$candidates))
      {
        return $this->sendTo((int)$activityId,(int)$this->nextActivity[$activityId]);
      }
      else
      {
        $returned_value['transition']['failure'] = tra('Error: nextActivity does not match any candidate in autorouting switch activity');
        return $returned_value;
      }
    }
    else
    {
      if (count($candidates)>1)
      {
        $returned_value['transition']['failure'] = tra('Error: non-deterministic decision for autorouting activity');
        return $returned_value;
      }
      else
      {
        return $this->sendTo((int)$activityId,(int)$candidates[0]);
      }
    }
  }

  /*!
  * This is a semi-private function, use GUI's abort function
  * Aborts an activity and terminates the whole instance. We still create a workitem to keep track
  * of where in the process the instance was aborted
  * TODO: review, reuse of completed code
  */
  function abort($activityId=0,$theuser = '',$addworkitem=true)
  {
    if(empty($theuser)) {
      global $user;
      if (empty($user)) {$theuser='*';} else {$theuser=$user;}
    }

    if($activityId==0) {
      $activityId=$_REQUEST['wf_activity_id'];
    }

    // If we are aborting a start activity then the instance must
    // be created first!
    // ==> No, there's no reason to have an uncompleted start activity to abort
    $type = $this->getOne('select wf_type from '.GALAXIA_TABLE_PREFIX.'activities where wf_activity_id=?',array((int)$activityId));

    // Now set ended on instance_activities
    $now = date("U");
    $query = 'update '.GALAXIA_TABLE_PREFIX.'instance_activities set wf_ended=? where wf_activity_id=? and wf_instance_id=?';
    $this->query($query,array((int)$now,(int)$activityId,(int)$this->instanceId));

    //Set the status for the instance-activity to aborted

    // terminate the instance with status 'aborted'
	if (!($this->terminate($now,'aborted'))) return false;

    //now we synchronise instance with the database
		//echo "<br/> syncing  in abort";
    if (!($this->sync())) return false;

    //Add a workitem to the instance
    if ($addworkitem)
    {
      return $this->addworkitem($type,$now, $activityId);
    }
    else
    {
      return true;
    }
  }

  /*!
  * @private
  * Terminates the instance marking the instance and the process
  * as completed. This is the end of a process.
  * Normally you should not call this method since it is automatically
  * called when an end activity is completed.
  * object is synched at the end of this function.
  * @param $time is the terminating time
  * @param $status is the final status, 'completed' by default
  * @return true if everything was ok, false else
  */
  function terminate($time, $status = 'completed') {
    //Set the status of the instance to completed
    if (!($this->setEnded((int)$time))) return false;
    if (!($this->setStatus($status))) return false;
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `wf_instance_id`=?";
    $this->query($query,array((int)$this->instanceId));
		//echo "<br/> syncing  in terminate";
	return $this->sync();
  }


  /*!
  * Sends the instance from some activity to another activity. (walk on a transition)
  * You should not call this method unless you know very very well what
  * you are doing.
  * @param $from is the activity id at the start of the transition
  * @param $activityId is the activity id at the end of the transition
  * @param $erase_from is true by default, if true the coming activity row will be erased from
  * instance_activities table. You should set it to false for example with split activities while
  * you still want to re-call this function
  * @return false if anything goes wrong, true if we are at the end of the execution tree and an array
  * if a part of the process was automatically runned at the end of the transition. this array contains
  * 2 keys 'transition' is the transition we walked on, 'activity' is the result of the run part if it was an automatic activity.
  * 'activity' value is an associated array containing several usefull keys:
  *	* 'completed' is a boolean indicating that the activity was completed or not
  *	* 'debug contains debug messages
  *	* 'info' contains some usefull infos about the activity-instance running (like names)
  *	* 'next' is the result of a SendAutorouted part which could in fact be the result of a call to this function, etc.
  */
  function sendTo($from,$activityId,$erase_from=true)
  {
    //we will use an array for return value
    $returned_data = Array();
    //1: if we are in a join check
    //if this instance is also in
    //other activity if so do
    //nothing
    $query = 'select wf_type, wf_name from '.GALAXIA_TABLE_PREFIX.'activities where wf_activity_id=?';
    $result = $this->query($query,array($activityId));
    if (empty($result))
    {
      $returned_data['transition']['failure'] = tra('Error: trying to send an instance to an activity but it was impossible to get this activity');
      return $returned_data;
    }
    while ($res = $result->fetchRow())
    {
      $type = $res['wf_type'];
      $targetname = $res['wf_name'];
    }
    $returned_data['transition']['target_id'] = $activityId;
    $returned_data['transition']['target_name'] = $targetname;

    // Verify the existence of a transition
    if(!$this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."transitions` where `wf_act_from_id`=? and `wf_act_to_id`=?",array($from,(int)$activityId))) {
      $returned_data['transition']['failure'] = tra('Error: trying to send an instance to an activity but no transition found');
      return $returned_data;
    }

    //init
    $putuser=0;

    //try to determine the user or *
    //Use the nextUser
    $the_next_user = $this->getNextUser();
    if($the_next_user)
    {
      //we check rights for this user on the next activity
      if (!(isset($this->security))) $this->security =& new WfSecurity($this->db);
      if ($this->security->checkUserAccess($the_next_user,$activityId))
      {
        $putuser = $the_next_user;
      }
    }
    if ($putuser==0)
    {
      // If no nextUser is set, then see if only
      // one user is in the role for this activity
      // and assign ownership to him if this is the case
      $query = "select `wf_role_id` from `".GALAXIA_TABLE_PREFIX."activity_roles` where `wf_activity_id`=?";
      $result = $this->query($query,array((int)$activityId));
      while ($res = $result->fetchRow())
      {
        $roleId = $res['wf_role_id'];
        //regis: group role mapping as an impact here, we need to count real user corresponding to this role
        // and we obtain users 'u' and groups 'g' in user_roles
        // we consider number of members on each group is subject to too much changes and so we do not even try
        // to look in members of the group to find if there is a unique real user candidate for this role
        // you could try it if you want but it's quite complex for something not really usefull
        // if there's at least one group in the roles we then won't even try to get this unique user
        $query_group = "select count(*) from ".GALAXIA_TABLE_PREFIX."user_roles
            where wf_role_id=? and wf_account_type='g'";
        if ($this->getOne($query_group,array((int)$roleId)) > 0 )
        { //we have groups
          //we can break the while, we wont search the candidate
          $putuser=0;
          break;
        }
        else
        {// we have no groups
          $query2 = "select distinct wf_user, wf_account_type from ".GALAXIA_TABLE_PREFIX."user_roles
              where wf_role_id=?";
          $result2 = $this->query($query2,array((int)$roleId));
          while ($res2 = $result2->fetchRow())
          {
            if (!($putuser==0))
            { // we already have one candidate
              // we have another one in $res2['wf_user'] but it means we don't have only one
              // we can unset our job and break the wile
              $putuser=0;
              break;
            }
            else
            {
              // set the first candidate
              $putuser = $res2['wf_user'];
            }
          }
        }
      }

      if ($putuser==0) // no decisions yet
      {
        // then check to see if there is a default user
        $activity_manager =& new ActivityManager($this->db);
        //get_default_user will give us '*' if there is no default_user or if the default user has no role
        //mapped anymore
        $default_user = $activity_manager->get_default_user($activityId,true);
        unset($activity_manager);
        // if they were no nextUser, no unique user avaible, no default_user then we'll have '*'
        // which will let user having the good role mapping grab this activity later
        $putuser = $default_user;
      }
    }

    //update the instance_activities table
    //if not splitting delete first
    //please update started,status,user
    if (($erase_from) && (!empty($this->instanceId)))
    {
      $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `wf_instance_id`=? and `wf_activity_id`=?";
      $this->query($query,array((int)$this->instanceId,$from));
    }

    if ($type == 'join') {
      if (count($this->activities)>1) {
        // This instance will have to wait!
        $returned_data['transition']['status'] = 'waiting';
        return $returned_data;
      }
    }

    //create the new instance-activity
    $returned_data['transition']['target_id'] = $activityId;
    $returned_data['transition']['target_name'] = $targetname;
    $now = date("U");
    $iid = $this->instanceId;
    $query="delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `wf_instance_id`=? and `wf_activity_id`=?";
    $this->query($query,array((int)$iid,(int)$activityId));
    $query="insert into `".GALAXIA_TABLE_PREFIX."instance_activities`(`wf_instance_id`,`wf_activity_id`,`wf_user`,`wf_status`,`wf_started`) values(?,?,?,?,?)";
    $this->query($query,array((int)$iid,(int)$activityId,$putuser,'running',(int)$now));

    //record the transition walk
    $returned_data['transition']['status'] = 'done';


    //we are now in a new activity
    $this->_populate_activities($iid);
    //if the activity is not interactive then
    //execute the code for the activity and
    //complete the activity
    $isInteractive = $this->getOne("select `wf_is_interactive` from `".GALAXIA_TABLE_PREFIX."activities` where `wf_activity_id`=?",array((int)$activityId));
    if ($isInteractive=='n')
    {
      //first we sync actual instance because the next activity could need it
      	//echo "<br/> syncing  in sendTo 1";
	  if (!($this->sync()))
      {
        $returned_data['activity']['failure'] = true;
        return $returned_data;
      }
      // Now execute the code for the activity
      $returned_data['activity'] = $this->executeAutomaticActivity($activityId, $iid);
    }
    else
    {
      // we sync actual instance
	  	//echo "<br/> syncing  in sendTo 2";
      if (!($this->sync()))
      {
        $returned_data['failure'] = true;
        return $returned_data;
      }
    }
    return $returned_data;
  }

  /*!
  * @public
  * This is a public method only because the GUI can ask this action for the admin
  * on restart failed automated activities, but in fact it's quite an internal function,
  * This function handle the execution of automatic activities (and the launch of transitions
  * which can be related to this activity).
  * @param $activityId is the activity id at the end of the transition
  * @param $iid is the instance id
  */
  function executeAutomaticActivity($activityId, $iid)
  {
    $returned_data = Array();
    // Now execute the code for the activity (function defined in galaxia's config.php)
		// echo "<br />execute automatic activity";
    $returned_data =& galaxia_execute_activity($activityId, $iid , 1);

    //we should have some info in $returned_data now. if it is false there's a problem
    if ((!(is_array($returned_data))) && (!($returned_data)) )
    {
      $this->error[] = tra('failed to execute automatic activity');
      //record the failure
      $returned_data['failure'] = true;
      return $returned_data;
    }
    else
    {
      //ok, we have an array, but it can still be a bad result
      //this one is just for debug info
      if (isset($returned_data['debug']))
      {
        //we retrieve this info here, in this object
        $this->error[] = $returned_data['debug'];
      }
      //and this really test if it worked, if not we have a nice failure message (better than just failure=true)
      if (isset($returned_data['failure']))
      {
        $this->error[] = tra('failed to execute automatic activity');
        $this->error[] = $returned_data['failure'];
        //record the failure
        return $returned_data;
      }

    }
    // Reload in case the activity did some change, last sync was done just before calling this function
    // regis: need a sync here, not just a reload from database
		//echo "<br/> syncing  in ExecuteAutomaticActivity";
	$this->unsynch=true; //we force unsynch state as this will force sync() to reload data
	$this->sync();

    //complete the automatic activity----------------------------
    if ($this->Complete($activityId))
    {
      $returned_data['completed'] = true;

      //and send the next autorouted activity if any
      $returned_data['next'] = $this->sendAutorouted($activityId);
    }
    else
    {
      $returned_data['failure'] = $this->get_error();
    }
    return $returned_data;
  }

  /*!
  Gets a comment for this instance
  */
  function get_instance_comment($cId) {
    $iid = $this->instanceId;
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_comments` where `wf_instance_id`=? and `wf_c_id`=?";
    $result = $this->query($query,array((int)$iid,(int)$cId));
    $res = $result->fetchRow();
    return $res;
  }

  /*!
  Inserts or updates an instance comment
  */
  function replace_instance_comment($cId, $activityId, $activity, $user, $title, $comment) {
    if (!$user) {
      $user = 'Anonymous';
    }
    $iid = $this->instanceId;
    //no need on pseudo-instance
    if (!!($this->instanceId))
    {
      if ($cId)
      {
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_comments` set `wf_title`=?,`wf_comment`=? where `wf_instance_id`=? and `wf_c_id`=?";
        $this->query($query,array($title,$comment,(int)$iid,(int)$cId));
      }
      else
      {
        $hash = md5($title.$comment);
        if ($this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."instance_comments` where `wf_instance_id`=? and `wf_hash`=?",array($iid,$hash)))
        {
          return false;
        }
        $now = date("U");
        $query ="insert into `".GALAXIA_TABLE_PREFIX."instance_comments`(`wf_instance_id`,`wf_user`,`wf_activity_id`,`wf_activity`,`wf_title`,`wf_comment`,`wf_timestamp`,`wf_hash`) values(?,?,?,?,?,?,?,?)";
        $this->query($query,array((int)$iid,$user,(int)$activityId,$activity,$title,$comment,(int)$now,$hash));
      }
    }
    return true;
  }

  /*!
  Removes an instance comment
  */
  function remove_instance_comment($cId) {
    $iid = $this->instanceId;
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_comments` where `wf_c_id`=? and `wf_instance_id`=?";
    $this->query($query,array((int)$cId,(int)$iid));
  }

  /*!
  Lists instance comments
  */
  function get_instance_comments() {
    $iid = $this->instanceId;
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_comments` where `wf_instance_id`=? order by ".$this->convert_sortmode("timestamp_desc");
    $result = $this->query($query,array((int)$iid));
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    return $ret;
  }
}
?>
