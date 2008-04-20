<?php
require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'ProcessManager'.'/'.'BaseManager.php');
//!! ActivityManager
//! A class to maniplate process activities and transitions
/*!
  This class is used to add,remove,modify and list
  activities used in the Workflow engine.
  Activities are managed in a per-process level, each
  activity belongs to some process.
*/
class ActivityManager extends BaseManager {
  //ProcessManager used sometimes by this object
  var $process_manager;
  /*!
    Constructor takes a PEAR::Db object to be used
    to manipulate activities in the database.
  */
  function ActivityManager(&$db)
  {
    parent::BaseManager($db);
    $this->child_name = 'ActivityManager';
    require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'ProcessManager'.'/'.'ProcessManager.php');
    //$this->process_manager is not set here to avoid object A loading objetc B loading object A, etc...

  }

  /*!
  * Collect errors from all linked objects which could have been used by this object
  * Each child class should instantiate this function with her linked objetcs, calling get_error(true)
  * for example if you had a $this->process_manager created in the constructor you shoudl call
  * $this->error[] = $this->process_manager->get_error(false, $debug);
  * @param $debug is false by default, if true debug messages can be added to 'normal' messages
  * @param $prefix is a string appended to the debug message
  */
  function collect_errors($debug=false, $prefix='')
  {
    parent::collect_errors($debug, $prefix);
    if (isset($this->process_manager)) $this->error[] = $this->process_manager->get_error(false, $debug, $prefix);
  }

  /*!
  * Associates an activity with a role
  * @param $activityId is the activity Id
  * @param $roleId is the roleId
  * @param readonly is false by default, if true the role will be in read-only mode
  */
  function add_activity_role($activityId, $roleId, $readonly = false)
  {
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'activity_roles where wf_activity_id=? and wf_role_id=?';
    $this->query($query,array($activityId, $roleId));
    $query = 'insert into '.GALAXIA_TABLE_PREFIX.'activity_roles (wf_activity_id,wf_role_id,wf_readonly) values(?,?,?)';
    $this->query($query,array($activityId, $roleId, (int)$readonly));
  }

  /*!
   Gets the roles associated to an activity
  */
  function get_activity_roles($activityId) {
    $query = 'select wf_activity_id,roles.wf_role_id,roles.wf_name, wf_readonly
              from '.GALAXIA_TABLE_PREFIX.'activity_roles gar
              INNER JOIN '.GALAXIA_TABLE_PREFIX.'roles roles on gar.wf_role_id = roles.wf_role_id
              where wf_activity_id=?';
    $result = $this->query($query,array($activityId));
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        $ret[] = $res;
      }
    }
    return $ret;
  }

  /*!
   Removes a role from an activity
  */
  function remove_activity_role($activityId, $roleId)
  {
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'activity_roles
              where wf_activity_id=? and wf_role_id=?';
    $this->query($query, array($activityId,$roleId));
  }

  //! Removes an user from all fields where he could be on every activities
  /*!
  * This function delete all references on the given user on all activities.
  * It will concern: wf_default_user
  * @param $user is the user id to remove
  */
  function remove_user($user)
  {
    $query = 'update '.GALAXIA_TABLE_PREFIX.'activities set wf_default_user=? where wf_default_user=?';
    $this->query($query,array('',$user));
  }

  //! Transfer all references to one user to another one in the activities
  /*!
  * This function transfer all references concerning one user to another user
  * It will concern: wf_default_user
  * @param $user_array is an associative arrays, keys are:
  * 	* 'old_user' : the actual user id
  * 	* 'new_user' : the new user id
  */
  function transfer_user($user_array)
  {
    $query = 'update '.GALAXIA_TABLE_PREFIX.'activities set wf_default_user=? where wf_default_user=?';
    $this->query($query,array($user_array['new_user'],$user_array['old_user']));
  }

  /*!
   Checks if a transition exists
  */
  function transition_exists($pid,$actFromId,$actToId)
  {
    return($this->getOne('select count(*) from
      '.GALAXIA_TABLE_PREFIX.'transitions where wf_p_id=? and wf_act_from_id=? and wf_act_to_id=?',
      array($pid,$actFromId,$actToId)
    ));
  }

  /*!
   Adds a transition
  */
  function add_transition($pId, $actFromId, $actToId)
  {
    // No circular transitions allowed
    if($actFromId == $actToId) {
		$this->error[] = tra('No circular transitions allowed.');
		return false;
    }

    // Rule: if act is not spl-x or spl-a it can't have more than
    // 1 outbound transition.
    $a1 = $this->get_activity($actFromId);
    $a2 = $this->get_activity($actToId);
    if(!$a1 || !$a2) {
		$this->error[] = tra('No activites');
		return false;
    }
    if($a1['wf_type'] != 'switch' && $a1['wf_type'] != 'split') {
      if($this->getOne('select count(*) from '.GALAXIA_TABLE_PREFIX.'transitions where wf_act_from_id=?', array($actFromId)) > 1) {
        $this->error[] = tra('Cannot add transition only split or switch activities can have more than one outbound transition');
        return false;
      }
    }

    // Rule: if act is standalone or view no transitions allowed
    if(($a1['wf_type'] == 'standalone' || $a2['wf_type']=='standalone') || ($a1['wf_type'] == 'view' || $a2['wf_type']=='view') )
    {
		$this->error[] = tra('No transitions allowed for standalone or view activities');
		return false;
    }
    // No inbound to start
    if($a2['wf_type'] == 'start') {
		$this->error[] = tra('No inbound for start activity');
		return false;
    }
    // No outbound from end
    if($a1['wf_type'] == 'end') {
		$this->error[] = tra('No outbound for end activity');
		return false;
    }


    $query = 'delete from `'.GALAXIA_TABLE_PREFIX.'transitions` where `wf_act_from_id`=? and `wf_act_to_id`=?';
    $this->query($query,array($actFromId, $actToId));
    $query = 'insert into `'.GALAXIA_TABLE_PREFIX.'transitions`(`wf_p_id`,`wf_act_from_id`,`wf_act_to_id`) values(?,?,?)';
    $this->query($query,array($pId, $actFromId, $actToId));

    return true;
  }

  /*!
   Removes a transition
  */
  function remove_transition($actFromId, $actToId)
  {
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'transitions where wf_act_from_id=? and wf_act_to_id=?';
    $this->query($query, array($actFromId,$actToId));
    return true;
  }

  /*!
   Removes all the activity transitions
  */
  function remove_activity_transitions($pId, $aid)
  {
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'transitions where wf_p_id=? and (wf_act_from_id=? or wf_act_to_id=?)';
    $this->query($query,array($pId,$aid,$aid));
  }


  /*!
   Returns all the transitions for a process
  */
  function get_process_transitions($pId,$actid=0)
  {
    $bindvars = Array();
    $bindvars[] = $pId;
    if(!$actid) {
        $query = 'select a1.wf_name as wf_act_from_name, a2.wf_name as wf_act_to_name, wf_act_from_id, wf_act_to_id
          from '.GALAXIA_TABLE_PREFIX.'transitions gt
          INNER JOIN '.GALAXIA_TABLE_PREFIX.'activities a1 ON gt.wf_act_from_id = a1.wf_activity_id
          INNER JOIN '.GALAXIA_TABLE_PREFIX.'activities a2 ON gt.wf_act_to_id = a2.wf_activity_id
          where gt.wf_p_id = ?';
    } else {
        $query = 'select a1.wf_name as wf_act_from_name, a2.wf_name as wf_act_to_name, wf_act_from_id, wf_act_to_id
        from '.GALAXIA_TABLE_PREFIX.'transitions gt
        INNER JOIN '.GALAXIA_TABLE_PREFIX.'activities a1 ON gt.wf_act_from_id = a1.wf_activity_id
        INNER JOIN '.GALAXIA_TABLE_PREFIX.'activities a2 ON gt.wf_act_to_id = a2.wf_activity_id
        where gt.wf_p_id = ?
        and (wf_act_from_id = ?)';
        $bindvars[] = $actid;
    }
    $result = $this->query($query, $bindvars, -1, -1, true, 'a1.wf_flow_num');
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        $ret[] = $res;
      }
    }
    return $ret;
  }

  /*!
   Indicates if an activity is autoRouted
  */
  function activity_is_auto_routed($actid)
  {
    return($this->getOne('select count(*) from '.GALAXIA_TABLE_PREFIX.'activities where wf_activity_id=? and wf_is_autorouted=?', array($actid,'y')));
  }

  /*!
   Returns all the activities for a process as
   an array
  */
  function get_process_activities($pId)
  {
    $query = 'select * from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=?';
    $result = $this->query($query, array($pId));
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        $ret[] = $res;
      }
    }
    return $ret;
  }
	/**
	* Returns an array of activities that can have transitions,
	* i.e., non-standalone/view activities
	* @param pId
	* @param $type_exclusion type you want to exclude from the result
	* @return array of activities array, count
	*/
  function &get_transition_activities($pId, $type_exclusion = false)
  {
	$where = '';
	$wheres = array();
	$wheres[] = "wf_type <> 'standalone'";
	$wheres[] = "wf_type <> 'view'";

	if( $type_exclusion )
	{
	  $wheres[] = "wf_type <> '".$type_exclusion."'";
	}
	$where = implode(' and ', $wheres);

	return $this->list_activities($pId, 0, -1, 'wf_flow_num__asc', '', $where);
  }

  /**
  * Returns an array of activities that have transitions,
  * @params pId: the process Id
  * @return array of activities array, with activities datas in ['data'](array) and number of activities in ['cant']
  */
  function get_process_activities_with_transitions($pId)
  {
    $query = 'select distinct a1.wf_name as wf_name, a1.wf_activity_id as wf_activity_id, a1.wf_flow_num
      from '.GALAXIA_TABLE_PREFIX.'transitions gt
      INNER JOIN '.GALAXIA_TABLE_PREFIX.'activities a1 ON gt.wf_act_from_id = a1.wf_activity_id
      INNER JOIN '.GALAXIA_TABLE_PREFIX.'activities a2 ON gt.wf_act_to_id = a2.wf_activity_id
      where gt.wf_p_id = ? ';
    $result = $this->query($query, array($pId),  -1, -1, true, 'a1.wf_flow_num');
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        $ret[] = $res;
      }
    }
    $retval = Array();
    $retval['data'] = $ret;
    $retval['cant'] = count($ret);
    return $retval;
  }
  /*!
   Builds the graph
  */
  //\todo build the real graph
  function build_process_graph($pId)
  {
    if (!(isset($this->process_manager))) $this->process_manager =& new ProcessManager($this->db);
    $attributes = Array(

    );
    $graph = new Process_GraphViz(true,$attributes);
    $name = $this->process_manager->_get_normalized_name($pId);
    $graph->set_pid($name);

    //Get the config of the workflow where we'll have some tips for the graph
    $myconfneeds = array(
      'draw_roles' => true,
      'font_size' => 12,
      );
    $configs = $this->process_manager->getConfigValues($pId,true,true,$myconfneeds);

    // Nodes are process activities so get
    // the activities and add nodes as needed
    $nodes = $this->get_process_activities($pId);

    foreach($nodes as $node)
    {
      if($node['wf_is_interactive']=='y')
      {
        $color='black';
        $fillcolor='0.6,0.6,0.9'; //blue TLS values
      }
      else
      {
        $color='black';
        $fillcolor='0.25,1,0.8';//green in TLS values
      }
      // get the fontsize, defined in the process
      $fontsize = $configs['font_size'];
      // if asked add roles on the graph
      if ($configs['draw_roles'])
      {
        $activity_roles = $this->get_activity_roles($node['wf_activity_id']);
      }
      // fill activity roles
      $act_role= '';
      if (isset($activity_roles))
      {
        foreach ($activity_roles as $role)
        {
          // the / is escaped and space seems to be necessary,
          //issues with some special characters if no spaces between this char and the /
          $act_role = $act_role." \\n[".$role['wf_name']."]";
        }
      }
      $auto[$node['wf_name']] = $node['wf_is_autorouted'];
      $graph->addNode($node['wf_name'],array('URL'=>"foourl?wf_activity_id=".$node['wf_activity_id'],
                                      'label'=>$node['wf_name'].$act_role,
                                      'shape' => $this->_get_activity_shape($node['wf_type']),
                                      'color' => $color,
                                      'fillcolor'=> $fillcolor,
                                      'style' => 'filled',
                                      'fontsize' => $fontsize
                                      )
                     );
    }

    // Now add edges, edges are transitions,
    // get the transitions and add the edges
    $edges = $this->get_process_transitions($pId);
    foreach($edges as $edge)
    {
      if($auto[$edge['wf_act_from_name']] == 'y') {
        $color = '0.25,1,0.28'; #dark green in TLS values
        $arrowsize = 1;
      } else {
        $color = '0.6,0.6,0.9'; #blue in TLS values
        $arrowsize= 2;
      }
        $graph->addEdge(array($edge['wf_act_from_name'] => $edge['wf_act_to_name']), array('color'=>$color,arrowsize=>$arrowsize));
    }


    // Save the map image and the image graph
    $graph->image_and_map();
    unset($graph);
    return true;
  }


  /*!
   Validates if a process can be activated checking the
   process activities and transitions the rules are:
   0) No circular activities
   1) Must have only one a start and end activity
   2) End must be reachable from start
   3) Interactive activities must have a role assigned
   4) Roles should be mapped
   5) Standalone and view activities cannot have transitions
   6) Non intractive activities non-auto routed must have some role
      so the user can "send" the activity
   7) start activities must be autorouted and interactive
  */
  function validate_process_activities($pId)
  {
    $errors = Array();
    // Pre rule no cricular activities
    $cant = $this->getOne('select count(*) from '.GALAXIA_TABLE_PREFIX.'transitions where wf_p_id=? and wf_act_from_id=wf_act_to_id'
      , array($pId));
    if($cant) {
      $errors[] = tra('Circular reference found some activity has a transition leading to itself');
    }

    // Rule 1 must have exactly one start and end activity
    $cant = $this->getOne('select count(*) from '
      .GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_type=?', array($pId,'start'));
    if($cant < 1) {
      $errors[] = tra('Process does not have a start activity');
    }
    $cant = $this->getOne('select count(*) from '
      .GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_type=?', array($pId,'end'));
    if($cant != 1) {
      $errors[] = tra('Process does not have exactly one end activity');
    }

    // Rule 2 end must be reachable from start
    // and Rule 7 start activities must be autorouted and interactive
    $nodes = Array();
    $endId = $this->getOne('select wf_activity_id from '
      .GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_type=?', array($pId,'end'));
    if ((!isset($endId)) || ($endId=='') || ($endId == 0))
    {
      //no end
      $errors[] = tra('this process has no end activity');
      $endId = 0;
    }

    $aux['id']=$endId;
    $aux['visited']=false;
    $nodes[] = $aux;

    $query = 'select wf_is_autorouted,wf_is_interactive,wf_activity_id from '
      .GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_type=?';
    $result = $this->query($query, array($pId,'start'));
    while($res = $result->fetchRow()) {
      $start_node['id'] = $res['wf_activity_id'];
      if(!($res['wf_is_interactive'] == 'y')) {
            $errors[] = tra('start activities must be interactive');
      }
      if(!($res['wf_is_autorouted'] == 'y')) {
            $errors[] = tra('start activities must be autorouted');
      }
    }
    $start_node['visited']=true;

    while($this->_list_has_unvisited_nodes($nodes) && !$this->_node_in_list($start_node,$nodes)) {
      for($i=0;$i<count($nodes);$i++) {
        $node=&$nodes[$i];
        if(!$node['visited']) {
          $node['visited']=true;
          $query = 'select wf_act_from_id from '.GALAXIA_TABLE_PREFIX.'transitions where wf_act_to_id=?';
          $result = $this->query($query, array($node['id']));
          $ret = Array();
          while($res = $result->fetchRow()) {
            $aux['id'] = $res['wf_act_from_id'];
            $aux['visited']=false;
            if(!$this->_node_in_list($aux,$nodes)) {
              $nodes[] = $aux;
            }
          }
        }
      }
    }

    if(!$this->_node_in_list($start_node,$nodes)) {
      // Start node is NOT reachable from the end node
      $errors[] = tra('End activity is not reachable from start activity');
    }

    //Rule 3: interactive activities must have a role
    //assigned.
    //Rule 5: standalone and view activities can't have transitions
    $query = 'select * from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id = ?';
    $result = $this->query($query, array($pId));
    while($res = $result->fetchRow()) {
      $aid = $res['wf_activity_id'];
      if($res['wf_is_interactive'] == 'y') {
          $cant = $this->getOne('select count(*) from '
            .GALAXIA_TABLE_PREFIX.'activity_roles where wf_activity_id=?', array($res['wf_activity_id']));
          if(!$cant) {
            $errors[] = tra('Activity %1 is interactive but has no role assigned', $res['wf_name']);
          }
      } else {
        if( $res['wf_type'] != 'end' && $res['wf_is_autorouted'] == 'n') {
          $cant = $this->getOne('select count(*) from '
            .GALAXIA_TABLE_PREFIX.'activity_roles where wf_activity_id=?', array($res['wf_activity_id']));
          if(!$cant)
          {
              $errors[] = tra('Activity %1 is non-interactive and non-autorouted but has no role assigned', $res['wf_name']);
          }
        }
      }
      if(($res['wf_type']=='standalone')||($res['wf_type']=='view')) {
        if($this->getOne('select count(*) from '
          .GALAXIA_TABLE_PREFIX.'transitions where wf_act_from_id=? or wf_act_to_id=?', array($aid,$aid)))
        {
           $errors[] = tra('Activity %1 is standalone or view but has transitions', $res['wf_name']);
        }
      }

    }


    //Rule4: roles should be mapped
    $query = 'select * from '.GALAXIA_TABLE_PREFIX.'roles where wf_p_id = ?';
    $result = $this->query($query, array($pId));
    while($res = $result->fetchRow()) {
        $cant = $this->getOne('select count(*) from '
          .GALAXIA_TABLE_PREFIX.'user_roles where wf_role_id=?', array($res['wf_role_id']));
        if(!$cant) {
          $errors[] = tra('Role %1 is not mapped', $res['wf_name']);
        }
    }


    // End of rules

    // Validate process sources
    $serrors=$this->validate_process_sources($pId);
    $errors = array_merge($errors,$serrors);

    $this->error = array_merge ($this->error, $errors);



    $isValid = (count($errors)==0) ? 'y' : 'n';

    $query = 'update '.GALAXIA_TABLE_PREFIX.'processes set wf_is_valid=? where wf_p_id=?';
    $this->query($query, array($isValid,$pId));

    $this->_label_nodes($pId);

    return ($isValid=='y');

  }

  /*!
  Validate process sources
  Rules:
  1) Interactive activities (non-standalone or view) must use complete()
  2) Standalone activities must not use $instance
  3) Switch activities must use setNextActivity
  4) Non-interactive activities cannot use complete()
  5) View activities cannot use $instance->set
  */
  function validate_process_sources($pid)
  {
    $errors=Array();
    $wf_procname= $this->getOne('select wf_normalized_name from '.GALAXIA_TABLE_PREFIX.'processes where wf_p_id=?', array($pid));

    $query = 'select * from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=?';
    $result = $this->query($query, array($pid));
    while($res = $result->fetchRow()) {
      $actname = $res['wf_normalized_name'];
      $source = GALAXIA_PROCESSES.'/'.$wf_procname.'/'.'code'.'/'.'activities'.'/'.$actname.'.php';
      if (!file_exists($source)) {
          $errors[] = tra('source code file for activity %1 is not avaible', $actname);
          continue;
      }
      $fp = fopen($source,'r');
      if (!$fp)
      {
        $errors[] = tra('source code for activity %1 is not avaible', $actname);
      }
      else
      {
        $data='';
        while(!feof($fp))
        {
          $data.=fread($fp,8192);
        }
        fclose($fp);
      }
      if($res['wf_type']=='standalone') {
          if(strstr($data,'$instance')) {
            $errors[] = tra('Activity %1 is standalone and is using the $instance object', $res['wf_name']);
          }
      }
      else
      {
        if($res['wf_type']=='view')
        {
          if(strstr($data,'$instance->set'))
          {
            $errors[] = tra('Activity %1 is view and is using the $instance object in write mode', $res['wf_name']);
          }
        }
        else
        { // for all others than standalone or view ...
          if($res['wf_is_interactive']=='y')
          {
            if(!strstr($data,'$instance->complete()'))
            {
              $errors[] = tra('Activity %1 is interactive so it must use the $instance->complete() method', $res['wf_name']);
            }
          }
          else
          { // not interactive ...
            if(strstr($data,'$instance->complete()'))
            {
              $errors[] = tra('Activity %1 is non-interactive so it must not use the $instance->complete() method', $res['wf_name']);
            }
          }
          if($res['wf_type']=='switch')
          {
            if(!strstr($data,'$instance->setNextActivity('))
            {
              $errors[] = tra('Activity %1 is switch so it must use $instance->setNextActivity($actname) method', $res['wf_name']);
            }
          }
        }
      }
    }
    return $errors;
  }

  /*!
   Indicates if an activity with the same name exists
   If you gives an activityId this activity won't be checked (if the activity already exists give this activity id)
  */
  function activity_name_exists($pId,$name, $activityId=0)
  {
    $name = addslashes($this->_normalize_name($name));
    $array_args = array($pId, $name, $activityId);
    $number = $this->getOne('select count(*) from '.GALAXIA_TABLE_PREFIX.'activities
            where wf_p_id=? and wf_normalized_name=? and not(wf_activity_id=?)', $array_args);
    return !($number==0);
  }

  /*!
    Gets activity infos. fields are returned as an asociative array
    Warning: get_activity requires no more processId, an activity id is far enough to return
    informations about an activity.
  */
  function get_activity($activityId)
  {
    require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'BaseActivity.php');
    $act = new BaseActivity($this->db);
    //Warning, we now use the BaseActivity object for it, interactivity and autorouting is now true/fales, not y/n
    return ($act->getActivity($activityId,false,true, true));
  /*
    $query = "select * from ".GALAXIA_TABLE_PREFIX."activities where wf_activity_id=?";
    $result = $this->query($query, array($activityId));
    $res = False;
    if (!(empty($result)))
    {
      $res = $result->fetchRow();
    }
    $res['toto']=$toto;
    return $res;
  */
  }

  /*!
  * Lists activities at a per-process level
  * @param $pId is the process id
  * @param $offset is the first row number (see $maxRecords)
  * @param $maxRecords is the maximum number of records returned
  * @param $sort_mode is the sort order
  * @param $find is a string searched name or description of the activity
  * @param $where is a sql string appended
  * @param count_roles is true by default and is adding stat queries results about number of roles
  * concerned by the activity
  * return a reference to an array containing two keys, 'cant' for the number of total activities
  * and 'data' containing an associative array with the activities content
  */
  function &list_activities($pId,$offset,$maxRecords,$sort_mode,$find,$where='', $count_roles=true)
  {
    $sort_mode = str_replace("__"," ",$sort_mode);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid=' where wf_p_id=? and ((wf_name like ?) or (wf_description like ?))';
      $bindvars = array($pId,$findesc,$findesc);
    } else {
      $mid=' where wf_p_id=? ';
      $bindvars = array($pId);
    }
    if($where) {
      $mid.= " and ($where) ";
    }
    $query = 'select * from '.GALAXIA_TABLE_PREFIX."activities $mid";
    $query_cant = 'select count(*) from '.GALAXIA_TABLE_PREFIX."activities $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset,true,$sort_mode);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      if ($count_roles) {
        $res['wf_roles'] = $this->getOne('select count(*) from '
        .GALAXIA_TABLE_PREFIX.'activity_roles where wf_activity_id=?',array($res['wf_activity_id']));
      }
      $ret[] = $res;
    }
    $retval = Array();
    $retval['data'] = $ret;
    $retval['cant'] = $cant;
    return $retval;
  }



  /*!
  * Removes an activity.
  * This will also remove transitions concerning the activity, roles associations, agents associations
  * anassociated agents data
  * @param $pId is the process id
  * @param $activityId is the activity id
  * @param $transaction is optional and true by default, it will permit to encapsulate the different deletes
  * in a transaction, if you already started a transaction encapsulating this one use this paramater to prevent
  * us to open a new one.
  * @return true if it was ok, false if nothing was done
  */
  function remove_activity($pId, $activityId, $transaction = true)
  {
    if (!(isset($this->process_manager))) $this->process_manager =& new ProcessManager($this->db);
    $proc_info = $this->process_manager->get_process($pId);
    $actname = $this->_get_normalized_name($activityId);

    // start a transaction
    $this->db->StartTrans();

    //the activity
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_activity_id=?';
    $this->query($query,array($pId,$activityId));

    //transitions
    $query = 'select wf_act_from_id,wf_act_to_id from '
      .GALAXIA_TABLE_PREFIX.'transitions where wf_act_from_id=? or wf_act_to_id=?';
    $result = $this->query($query, array($activityId,$activityId));
    while($res = $result->fetchRow()) {
      $this->remove_transition($res['wf_act_from_id'], $res['wf_act_to_id']);
    }

    //roles
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'activity_roles where wf_activity_id=?';
    $this->query($query, array($activityId));

    //agents
    $query = 'select wf_agent_id, wf_agent_type from '.GALAXIA_TABLE_PREFIX.'activity_agents where wf_activity_id=?';
    $result = $this->query($query, array($activityId));
    if (!(empty($result)))
    {
      while ($res = $result->fetchRow())
      {
        //delete the associated agent
        $query = 'delete from '.GALAXIA_TABLE_PREFIX.'agent_'.$res['wf_agent_type'].' where wf_agent_id=?';
        $this->query($query, array($res['wf_agent_id']));
      }
      //now we can delete the association table
      $query = 'delete from '.GALAXIA_TABLE_PREFIX.'activity_agents where wf_activity_id=?';
      $this->query($query, array($activityId));
    }

    // And we have to remove the user and compiled files
    // for this activity
    $wf_procname = $proc_info['wf_normalized_name'];
    unlink(GALAXIA_PROCESSES.'/'.$wf_procname.'/'.'code'.'/'.'activities'.'/'.$actname.'.php');
    if (file_exists(GALAXIA_PROCESSES.'/'.$wf_procname.'/'.'code'.'/'.'templates'.'/'.$actname.'.tpl')) {
      @unlink(GALAXIA_PROCESSES.'/'.$wf_procname.'/'.'code'.'/'.'templates'.$actname.'.tpl');
    }
    unlink(GALAXIA_PROCESSES.'/'.$wf_procname.'/'.'compiled'.'/'.$actname.'.php');

    // perform commit (return true) or Rollback (return false)
    return $this->db->CompleteTrans();
  }

  /*!
    Updates or inserts a new activity in the database, $vars is an associative
    array containing the fields to update or to insert as needed.
    $pId is the processId
    $activityId is the activityId
  */
  function replace_activity($pId, $activityId, $vars, $create_files=true)
  {
    if (!(isset($this->process_manager))) $this->process_manager =& new ProcessManager($this->db);
    $TABLE_NAME = GALAXIA_TABLE_PREFIX.'activities';
    $now = date("U");
    $vars['wf_last_modif']=$now;
    $vars['wf_p_id']=$pId;
    $vars['wf_normalized_name'] = $this->_normalize_name($vars['wf_name']);

    $proc_info = $this->process_manager->get_process($pId);


    foreach($vars as $key=>$value)
    {
      $vars[$key]=addslashes($value);
    }

    if($activityId) {
      $oldname = $this->_get_normalized_name($activityId);
      // update mode
      $first = true;
      $bindvars = Array();
      $query ="update $TABLE_NAME set";
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= " $key=$value ";
        $first = false;
      }
      $query .= ' where wf_p_id=? and wf_activity_id=? ';
      $bindvars[] = $pId;
      $bindvars[] = $activityId;
      $this->query($query, $bindvars);

      $newname = $vars['wf_normalized_name'];
      // if the activity is changing name then we
      // should rename the user_file for the activity
      // remove the old compiled file and recompile
      // the activity

      $user_file_old = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'code'.'/'.'activities'.'/'.$oldname.'.php';
      $user_file_new = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'code'.'/'.'activities'.'/'.$newname.'.php';
      rename($user_file_old, $user_file_new);

      $user_file_old = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'code'.'/'.'templates'.'/'.$oldname.'.tpl';
      $user_file_new = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'code'.'/'.'templates'.'/'.$newname.'.tpl';
      if ($user_file_old != $user_file_new) {
        @rename($user_file_old, $user_file_new);
      }


      $compiled_file = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'compiled'.'/'.$oldname.'.php';
      unlink($compiled_file);
      $this->error[] = $this->compile_activity($pId,$activityId);


    } else {

      // When inserting activity names can't be duplicated
      if($this->activity_name_exists($pId, $vars['wf_name'])) {
          return false;
      }
      unset($vars['wf_activity_id']);
      // insert mode
      $first = true;
      $query = "insert into $TABLE_NAME(";
      foreach(array_keys($vars) as $key) {
        if(!$first) $query.= ',';
        $query.= "$key";
        $first = false;
      }
      $query .=") values(";
      $first = true;
      foreach(array_values($vars) as $value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= "$value";
        $first = false;
      }
      $query .=")";
      $this->query($query);
      //commented, last_modif time can be far away on high load! And if your database is not incrementing Ids it is not a database
      //$activityId = $this->getOne('select max(wf_activity_id) from '.$TABLE_NAME.' where wf_p_id=? and wf_last_modif=?', array($pId,$now));
      $activityId = $this->getOne('select max(wf_activity_id) from '.$TABLE_NAME.' where wf_p_id=? ', array($pId));
      $ret = $activityId;
      /* seems to be a debug code
      if(!$activityId) {
         print("select max(wf_activity_id) from $TABLE_NAME where wf_p_id=$pId and wf_last_modif=$now");
         die;
      }
      */
      // Should create the code file
	  if ($create_files) {
		  $wf_procname = $proc_info["wf_normalized_name"];
		  $fw = fopen(GALAXIA_PROCESSES.'/'.$wf_procname.'/'.'code'.'/'.'activities'.'/'.$vars['wf_normalized_name'].'.php','w');
			fwrite($fw,'<'.'?'.'php'."\n".'?'.'>');
			fclose($fw);

			 if($vars['wf_is_interactive']=='y') {
				$fw = fopen(GALAXIA_PROCESSES.'/'.$wf_procname.'/'.'code'.'/'.'templates'.'/'.$vars['wf_normalized_name'].'.tpl','w');
				if (defined('GALAXIA_TEMPLATE_HEADER') && GALAXIA_TEMPLATE_HEADER) {
				  fwrite($fw,GALAXIA_TEMPLATE_HEADER . "\n");
				}
				fclose($fw);
			}
	  }

      $this->error[] = $this->compile_activity($pId,$activityId);

    }
    // Get the id
    return $activityId;
  }

  /*!
  * Associates an activity with an agent type and create or retrieve the associated agent
  * if the agent of this type for this activity already exists we return his id.
  * @param activityId: the activity id
  * @param $agentType: The type of the agent (string)
  * @return agent_id created or retrieved after this association was done or false in case of problems
  */
  function add_activity_agent($activityId, $agentType)
  {
    $agent_id = 0;
    //this will retrieve info directly from the agent table, not on the recorded activity-agent association table
    $agents = $this->get_activity_agents($activityId);
    foreach ($agents as $agent)
    {
      if ($agent['wf_type']==$agentType)
      {
        //we found an agent which were previously associated with the activity
        $agent_id = $agent['wf_agent_id'];
        //but we still need to ensure it is still associated with the activity
        $actualAssoc = $this->getOne('select wf_activity_id from '.GALAXIA_TABLE_PREFIX.'activity_agents where wf_activity_id=?', array($activityId));
        if (!($actualAssoc == $activityId))
        {
          $query = 'insert into '.GALAXIA_TABLE_PREFIX.'activity_agents (wf_activity_id,wf_agent_id,wf_agent_type) values(?,?,?)';
          $this->query($query,array($activityId, $agentId, $agentType));
        }
        return $agent_id;
      }
    }
    //if we are here we did not find this type of agent for this activity
      //TODO: check agent type is in autorized list
    //add a new agent record
    $query = 'insert into '.GALAXIA_TABLE_PREFIX.'agent_'.$agentType.' (wf_agent_id) values(DEFAULT)';
    $this->query($query);
    $query = 'select max(wf_agent_id) from '.GALAXIA_TABLE_PREFIX.'agent_'.$agentType;
    $agentId = $this->getOne($query);
    //record the association
    $query = 'insert into '.GALAXIA_TABLE_PREFIX.'activity_agents (wf_activity_id,wf_agent_id,wf_agent_type) values(?,?,?)';
    $this->query($query,array($activityId, $agentId, $agentType));
    return $agentId;
  }

  /*!
  * Gets the agents (id and type) associated to an activity
  * @param $activityId is the activity id
  * @return an associative array which can be empty
  */
  function get_activity_agents($activityId)
  {
    $query = 'select wf_agent_id, wf_agent_type
              from '.GALAXIA_TABLE_PREFIX.'activity_agents
              where wf_activity_id=?';
    $result = $this->query($query,array($activityId));
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        $ret[] = $res;
      }
    }

    return $ret;
  }

  /*!
  * Gets the agent datas (with id and type as well) associated to an activity
  * from the agent table
  * @param $activityId is the activity id
  * @param $agentType is the agent type, giving the table name for the engine
  * @return an associative array which can be empty if we did not find the agentType
  * for this activity
  */
  function get_activity_agent_data($activityId, $agentType)
  {
    $query = 'select wf_agent_id
              from '.GALAXIA_TABLE_PREFIX.'activity_agents
              where wf_activity_id=? and wf_agent_type=?';
    $agent_id = $this->getOne($query,array($activityId,$agentType));
    $ret = Array();
    if ($agent_id==0)
    {
      return $ret;
    }
    else
    {
      $query = 'select * from '.GALAXIA_TABLE_PREFIX.'agent_'.$agentType.' where wf_agent_id=?';
      $result = $this->query($query,array($agent_id));
      if (!(empty($result)))
      {
        while($res = $result->fetchRow())
        {
          $ret[] = $res;
        }
        //fake agent_type column, we know what it should be and that we should have only one record
        $ret[0]['wf_agent_type'] = $agentType;
      }
    }
    return $ret[0];
  }

  /*!
  * Removes an agent from an activity
  * @param activityId the activity id
  * @param $agentId The id of the agent
  * @param $removeagent is false by default, if true the agent himself is destroyed, that means if you
  *	re-associate the activity with the same agent type all previous configuration will be lost
  */
  function remove_activity_agent($activityId, $agentId, $removeagent=false)
  {

    if ($removeagent)
    {
      $query = 'select wf_agent_type from '.GALAXIA_TABLE_PREFIX.'activity_agents
          where wf_activity_id=? and wf_agent_id=?';
      $agent_type = $this->getOne($query, array($activityId, $agentId));
      $this->remove_agent($agentId, $agent_type);
    }
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'activity_agents
            where wf_activity_id=? and wf_agent_id=?';
    $this->query($query, array($activityId, $agentId));
  }

  /*!
  * remove an agent.
  * @param $agentId is the agent id
  * @param $agent_type is the agent_type
  */
  function remove_agent($agentId, $agent_type)
  {
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'agent_'.$agent_type.'
      where wf_agent_id=?';
    $this->query($query, array($agentId));
  }

  /*!
  * Sets if an activity is interactive or not
  */
  function set_interactivity($pId, $actid, $value)
  {
    $query = 'update '.GALAXIA_TABLE_PREFIX.'activities set wf_is_interactive=? where wf_p_id=? and wf_activity_id=?';
    $this->query($query, array($value, $pId, $actid));
    // If template does not exist then create template
    $this->error[] = $this->compile_activity($pId,$actid);
  }

  /*!
  * Sets if an activity is auto routed or not
  */
  function set_autorouting($pId, $actid, $value)
  {
    $query = 'update '.GALAXIA_TABLE_PREFIX.'activities set wf_is_autorouted=? where wf_p_id=? and wf_activity_id=?';
    $this->query($query, array($value, $pId, $actid));
  }

  /*!
    Sets the default user for an activity
  */
  function set_default_user($activityId, $default_user)
  {
    if ($default_user=='')
    {
      $default_user='*';
    }
    $query  = 'update '.GALAXIA_TABLE_PREFIX.'activities set wf_default_user=? where wf_activity_id=?';
    $this->query($query, array($default_user, $activityId));
  }

  /*!
    Gets the default user for an activity
    if performAccessCheck is true then this function will check if this user as really access granted
    to the given activity.
    If wrong or no default user or the user has no access grant and performAccessCheck was asked, '*' is returned
  */
  function get_default_user($activityId, $performAccessCheck=true)
  {
    $query  = 'Select wf_default_user from '.GALAXIA_TABLE_PREFIX.'activities where wf_activity_id=?';
    $result = $this->getOne($query,array($activityId));
    if (!(isset($result)) || ($result=='') || ($result==false))
    {
      $result='*';
    }
    //if we had a user and if asked we'll try to see if he has really access granted
    elseif ( (!($result=='*')) && $performAccessCheck)
    {
      $wf_security = new WfSecurity($this->db);
      // perform the check
      if (!($wf_security->checkUserAccess($result,$activityId)))
      {
        // bad check, we ignore this default_user
        $result='*';
      }
    }
    return $result;
  }

  /*!
  * Compiles an activity
  * @return an array containing error messages
  */
  function compile_activity($pId, $activityId)
  {
    if (!(isset($this->process_manager))) $this->process_manager =& new ProcessManager($this->db);
    $errors = Array();
    $act_info = $this->get_activity($activityId, true, true);
       $actname = $act_info['wf_normalized_name'];
    $proc_info = $this->process_manager->get_process($pId);
    $compiled_file = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'compiled'.'/'.$act_info['wf_normalized_name'].'.php';
    $template_file = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'code'.'/'.'templates'.'/'.$actname.'.tpl';
    $user_file = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'code'.'/'.'activities'.'/'.$actname.'.php';
    $pre_file = GALAXIA_LIBRARY.'/'.'compiler'.'/'.$act_info['wf_type'].'_pre.php';
    $pos_file = GALAXIA_LIBRARY.'/'.'compiler'.'/'.$act_info['wf_type'].'_pos.php';
    if (!(empty($act_info['wf_agents'])))
    {
      $agents = true;
      $pre_agent_file = GALAXIA_LIBRARY.'/'.'compiler'.'/'.'agents_pre.php';
      $pos_agent_file = GALAXIA_LIBRARY.'/'.'compiler'.'/'.'agents_pos.php';
    }
    else
    {
      $agents = false;
    }

    $fw = fopen($compiled_file,"wb");
    if (!$fw)
    {
      $errors[] = tra('cannot create compiled file %1',$compiled_file);
      //stop here
      return $errors;
    }

    // First of all add an include to the shared code
    $shared_file = GALAXIA_PROCESSES.'/'.$proc_info['wf_normalized_name'].'/'.'code'.'/'.'shared.php';

    fwrite($fw, '<'."?php require_once('$shared_file'); ?".'>'."\n");

    // Before pre shared
    $fp = fopen(GALAXIA_LIBRARY.'/'.'compiler'.'/'.'_shared_pre.php',"rb");
    if (!$fp)
    {
      $errors[] = tra('pre-shared code is not avaible');
    }
    else
    {
      while (!feof($fp))
      {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
      }
      fclose($fp);
    }

    // Now get pre file for the activity
    $fp = fopen($pre_file,"rb");
    if (!$fp)
    {
      $errors[] = tra('pre-activity %1 code is not avaible', $act_info['wf_type']);
    }
    else
    {
      while (!feof($fp))
      {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
      }
      fclose($fp);
    }

    // Now get pre file for the agent
    if ($agents)
    {
      $fp = fopen($pre_agent_file,"rb");
      if (!$fp)
      {
        $errors[] = tra('pre-agent %1 code is not avaible', $act_info['wf_agent']);
      }
      else
      {
        while (!feof($fp))
        {
          $data = fread($fp, 4096);
          fwrite($fw,$data);
        }
        fclose($fp);
      }
    }

    // Get the user data for the activity
    $fp = fopen($user_file,"rb");
    if (!$fp)
    {
      $errors[] = tra('activity %1 code is not avaible', $actname);
    }
    else
    {
      while (!feof($fp))
      {
        $data = fread($fp, 4096);
        fwrite($fw,$data);
      }
      fclose($fp);
    }

    // Get pos
    $fp = fopen($pos_file,"rb");
    if (!$fp)
    {
      $errors[] = tra('post activity %1 code is not avaible', $act_info['wf_type']);
    }
    else
    {
      while (!feof($fp))
      {
          $data = fread($fp, 4096);
          fwrite($fw,$data);
      }
      fclose($fp);
    }

    // Now get pos file for the agent
    if ($agents)
    {
      $fp = fopen($pos_agent_file,"rb");
      if (!$fp)
      {
        $errors[] = tra('post-agent %1 code is not avaible', $act_info['wf_agent']);
      }
      else
      {
        while (!feof($fp))
        {
          $data = fread($fp, 4096);
          fwrite($fw,$data);
        }
        fclose($fp);
      }
    }

    // Shared pos
    $fp = fopen(GALAXIA_LIBRARY.'/'.'compiler'.'/'.'_shared_pos.php',"rb");
    if (!$fp)
    {
      $errors[] = tra('post shared code is not avaible');
    }
    else
    {
      while (!feof($fp)) {
          $data = fread($fp, 4096);
          fwrite($fw,$data);
      }
      fclose($fp);
    }

    fclose($fw);

    //Copy the templates

    if($act_info['wf_is_interactive']=='y' && !file_exists($template_file)) {
      $fw = fopen($template_file,'w');
      if (defined('GALAXIA_TEMPLATE_HEADER') && GALAXIA_TEMPLATE_HEADER) {
        fwrite($fw,GALAXIA_TEMPLATE_HEADER . "\n");
      }
      fclose($fw);
    }
    if($act_info['wf_is_interactive']!='y' && file_exists($template_file)) {
      @unlink($template_file);
      if (GALAXIA_TEMPLATES && file_exists(GALAXIA_TEMPLATES.'/'.$proc_info['wf_normalized_name'].'/'.$actname.'.tpl')) {
        @unlink(GALAXIA_TEMPLATES.'/'.$proc_info['wf_normalized_name'].'/'.$actname.'.tpl');
      }
    }
    if (GALAXIA_TEMPLATES && file_exists($template_file)) {
      @copy($template_file,GALAXIA_TEMPLATES.'/'.$proc_info['wf_normalized_name'].'/'.$actname.'.tpl');
    }
    return $errors;
  }

  /*!
   \private
   Returns activity id by pid,name (activity names are unique)
  */
  function _get_activity_id_by_name($pid,$name)
  {
    $name = addslashes($name);
    $bindarray = array($pid,$name);
    if($this->getOne('select count(*) from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_name=?', $bindarray))
    {
      return($this->getOne('select wf_activity_id from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_name=?', $bindarray));
    }
    else
    {
      return '';
    }
  }

  /*!
   \private Returns the activity shape
  */
  function _get_activity_shape($type)
  {
    switch($type) {
      case 'start':
          return 'circle';
      case 'end':
          return 'doublecircle';
      case 'activity':
          return 'box';
      case 'split':
          return 'triangle';
      case 'switch':
        return 'diamond';
      case 'join':
          return 'invtriangle';
      case 'standalone':
          return 'hexagon';
      case 'view':
          return 'hexagon';
      default:
          return 'egg';

    }

  }


  /*!
   \private Returns true if a list contains unvisited nodes
   list members are assoc arrays containing id and visited
  */
  function _list_has_unvisited_nodes($list)
  {
    foreach($list as $node) {
      if(!$node['visited']) return true;
    }
    return false;
  }

  /*!
   \private Returns true if a node is in a list
   list members are assoc arrays containing id and visited
  */
  function _node_in_list($node,$list)
  {
    foreach($list as $a_node) {
      if($node['id'] == $a_node['id']) return true;
    }
    return false;
  }

  /*!
  \private
  Normalizes an activity name
  */
  function _normalize_name($name)
  {
    $name = str_replace(" ","_",$name);
    $name = preg_replace("/[^A-Za-z_0-9]/",'',$name);
    return $name;
  }

  /*!
  \private
  Returns normalized name of an activity
  */
  function _get_normalized_name($activityId)
  {
    return $this->getOne('select wf_normalized_name from '
      .GALAXIA_TABLE_PREFIX.'activities where wf_activity_id=?', array($activityId));
  }

  /*!
  \private
  Labels nodes. Return false if something was wrong
  */
  function _label_nodes($pId)
  {
    ///an empty list of nodes starts the process
    $nodes = Array();
    // the end activity id
    $endId = $this->getOne('select wf_activity_id from '
      .GALAXIA_TABLE_PREFIX.'activities where wf_p_id=? and wf_type=?',array($pId,'end'));
    if ((!isset($endId)) || ($endId=='') || ($endId == 0))
    {
      //no end
      return false;
    }

    // and the number of total nodes (=activities)
    $cant = $this->getOne('select count(*) from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=?', array($pId));
    $nodes[] = $endId;
    $label = $cant;
    $num = $cant;

    $query = 'update '.GALAXIA_TABLE_PREFIX.'activities set wf_flow_num=? where wf_p_id=?';
    $this->query($query,array($cant+1,$pId));
    $seen = array();
    while(count($nodes)) {
      $newnodes = Array();
      foreach($nodes as $node) {
        // avoid endless loops
        if (isset($seen[$node])) continue;
        $seen[$node] = 1;
        $query = 'update '.GALAXIA_TABLE_PREFIX.'activities set wf_flow_num=? where wf_activity_id=?';
        $this->query($query, array($num,$node));
        $query = 'select wf_act_from_id from '.GALAXIA_TABLE_PREFIX.'transitions where wf_act_to_id=?';
        $result = $this->query($query, array($node));
        $ret = Array();
        while($res = $result->fetchRow()) {
          $newnodes[] = $res['wf_act_from_id'];
        }
      }
      $num--;
      $nodes=Array();
      $nodes=$newnodes;

    }

    $min = $this->getOne('select min(wf_flow_num) from '.GALAXIA_TABLE_PREFIX.'activities where wf_p_id=?', array($pId));
    $query = 'update '.GALAXIA_TABLE_PREFIX."activities set wf_flow_num=wf_flow_num-$min where wf_p_id=?";
    $this->query($query, array($pId));

    //$query = "update ".GALAXIA_TABLE_PREFIX."activities set flowNum=0 where flowNum=$cant+1";
    //$this->query($query);
    return true;
  }
}


?>
