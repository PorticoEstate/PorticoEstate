<?php require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'common'.'/'.'Base.php');
//!! GUI
//! A GUI class for use in typical user interface scripts
/*!
This class provides methods for use in typical user interface scripts
*/
class GUI extends Base {

  //security object used to obtain access for the user on certain actions from the engine
  var $wf_security;
  //process manager object used to retrieve infos from processes
  var $pm;
  //cache array to avoid queries
  var $process_cache=Array();

  /*!
  List user processes, user processes should follow one of these conditions:
  1) The process has an instance assigned to the user
  2) The process has a begin activity with a role compatible to the
     user roles
  3) The process has an instance assigned to '*' and the
     roles for the activity match the roles assigned to
     the user
  The method returns the list of processes that match this
  and it also returns the number of instances that are in the
  process matching the conditions.
  */
  /*
  TODO:
   *) more options in list_user_instances, they should not be added by the external modules
   */

  /*!
  * Constructor takes a PEAR::Db object
  */
  function GUI(&$db)
  {
    $this->child_name = 'GUI';
    parent::Base($db);
    require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'common'.'/'.'WfSecurity.php');
    $this->wf_security =& new WfSecurity($this->db);
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
    $this->error[] = $this->wf_security->get_error(false, $debug, $prefix);
  }


  function gui_list_user_processes($user,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    // FIXME: this doesn't support multiple sort criteria
    //$sort_mode = $this->convert_sortmode($sort_mode);
    $sort_mode = str_replace("__"," ",$sort_mode);

    $mid = "where gp.wf_is_active=?";
    // add group mapping, warning groups and user can have the same id
    $groups = galaxia_retrieve_user_groups($user);
    $mid .= " and ((gur.wf_user=? and gur.wf_account_type='u')";
    if (is_array($groups))
    {
      $mid .= '	or (gur.wf_user in ('.implode(',',$groups).") and gur.wf_account_type='g')";
    }
    $mid .= ')';
    $bindvars = array('y',$user);
    if($find) {
      $findesc = '%'.$find.'%';
      $mid .= " and ((gp.wf_name like ?) or (gp.wf_description like ?))";
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }
    if($where) {
      $mid.= " and ($where) ";
    }

    $query = "select distinct(gp.wf_p_id),
                     gp.wf_is_active,
                     gp.wf_name as wf_procname,
                     gp.wf_normalized_name as normalized_name,
                     gp.wf_version as wf_version,
                     gp.wf_version as version
              from ".GALAXIA_TABLE_PREFIX."processes gp
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
              $mid";
    $query_cant = "select count(distinct(gp.wf_p_id))
              from ".GALAXIA_TABLE_PREFIX."processes gp
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
              $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset, true, $sort_mode);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow()) {
        // Get instances and activities per process,
        $pId=$res['wf_p_id'];
        $query_act = 'select count(distinct(ga.wf_activity_id))
              from '.GALAXIA_TABLE_PREFIX.'processes gp
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'activities ga ON gp.wf_p_id=ga.wf_p_id
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'roles gr ON gr.wf_role_id=gar.wf_role_id
                INNER JOIN '.GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
              where gp.wf_p_id=?
              and (  ((gur.wf_user=? and gur.wf_account_type='u') ";
        if (is_array($groups))
        {
          $query_act .= ' or (gur.wf_user in ('.implode(',',$groups).") and gur.wf_account_type='g')";
        }
        $query_act .= '))';

        $res['wf_activities']=$this->getOne($query_act,array($pId,$user));
        //we are counting here instances which are completed/exception or actives
        // TODO: maybe we should add a second counter with only running instances
        $query_inst = 'select count(distinct(gi.wf_instance_id))
              from '.GALAXIA_TABLE_PREFIX.'instances gi
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'instance_activities gia ON gi.wf_instance_id=gia.wf_instance_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activity_roles gar ON gia.wf_activity_id=gar.wf_activity_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX."user_roles gur ON gar.wf_role_id=gur.wf_role_id
              where gi.wf_p_id=?
              and (";
        if (is_array($groups))
        {
          $query_inst .= "(gur.wf_user in (".implode(",",$groups).") and gur.wf_account_type='g') or ";
        }
        $query_inst .= "(gi.wf_owner=?)
                         or ((gur.wf_user=?) and gur.wf_account_type='u'))";
        $res['wf_instances']=$this->getOne($query_inst,array($pId,$user,$user));
        $ret[] = $res;
      }
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  /*! list user activities
  * @param $user is the current user id
  * @param $offset is the current starting point for the query results
  * @param $maxRecords is the max number of results to return
  * @param $sort_mode is for sorting
  * @param $find is a string to search in activity name or description
  * @param $where is deprecated it's a string to add to the query, use with care for SQL injection
  * @param $remove_activities_without_instances is false by default will remove all activities having no instances related at this time
  * @param $remove_instances_activities is false by default, if true then all activities related to instances will be avoided
  * (i.e. activities which are not standalone, start or view). If $remove_activities_without_instances is true you'll obtain nothing :-)
  * @param $add_start is false by default, if true start activities are added to the listing, no effect if $remove_activities_without_instances is true
  * @param $add_standalone is false by default, if true standalone activities are added to the listing, no effect if $remove_activities_without_instances is true
  * @param $add_view is false by default, if true view activities are added to the listing, no effect if $remove_activities_without_instances is true
  * @return an associative array, key cant gives the number of results, key data is an associative array conteining the results
  */
  function gui_list_user_activities($user,$offset,$maxRecords,$sort_mode,$find,$where='', $remove_activities_without_instances=false, $remove_instances_activities =false, $add_start = false, $add_standalone = false, $add_view = false)
  {
    // FIXME: this doesn't support multiple sort criteria
    //$sort_mode = $this->convert_sortmode($sort_mode);
    $sort_mode = str_replace("__"," ",$sort_mode);
    $mid = "where gp.wf_is_active=?";
    $bindvars = array('y');

    if ($remove_instances_activities)
    {
      $mid .= " and ga.wf_type <> ? and ga.wf_type <> ? and ga.wf_type <> ?  and  ga.wf_type <> ?  and  ga.wf_type <> ? ";
      $bindvars[] = 'end';
      $bindvars[] = 'switch';
      $bindvars[] = 'join';
      $bindvars[] = 'activity';
      $bindvars[] = 'split';
    }
    if (!($add_start))
    {
      $mid .= " and ga.wf_type <> ?";
      $bindvars[] = 'start';
    }
    if (!($add_standalone))
    {
      $mid .= " and ga.wf_type <> ?";
      $bindvars[] = 'standalone';
    }
    if (!($add_view))
    {
      $mid .= " and ga.wf_type <> ?";
      $bindvars[] = 'view';
    }

    // add group mapping, warning groups and user can have the same id
    $groups = galaxia_retrieve_user_groups($user);
    $mid .= " and ((gur.wf_user=? and gur.wf_account_type='u')";
    if (is_array($groups))
    {
      $mid .= '	or (gur.wf_user in ('.implode(',',$groups).") and gur.wf_account_type='g')";
    }
    $mid .= ')';
    $bindvars[] = $user;
    if($find) {
      $findesc = '%'.$find.'%';
      $mid .= " and ((ga.wf_name like ?) or (ga.wf_description like ?))";
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }
    if($where) {
      $mid.= " and ($where) ";
    }
    if ($remove_activities_without_instances)
    {
      $more_tables = "INNER JOIN ".GALAXIA_TABLE_PREFIX."instance_activities gia ON gia.wf_activity_id=gar.wf_activity_id
                      INNER JOIN ".GALAXIA_TABLE_PREFIX."instances gi ON gia.wf_instance_id=gi.wf_instance_id";
    }
    else
    {
	$more_tables = "";
    }
    $query = "select distinct(ga.wf_activity_id),
                     ga.wf_name,
                     ga.wf_type,
                     gp.wf_name as wf_procname,
                     ga.wf_is_interactive,
                     ga.wf_is_autorouted,
                     ga.wf_activity_id,
                     gp.wf_version as wf_version,
                     gp.wf_p_id,
                     gp.wf_is_active,
		     gp.wf_normalized_name
                from ".GALAXIA_TABLE_PREFIX."processes gp
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
                $more_tables
                $mid";

    $query_cant = "select count(distinct(ga.wf_activity_id))
              from ".GALAXIA_TABLE_PREFIX."processes gp
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
                INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
                $more_tables
                $mid ";
    $result = $this->query($query,$bindvars,$maxRecords,$offset, true, $sort_mode);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = Array();
    $removed_instances = 0;
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        // Get instances per activity
        $query_act = 'select count(distinct(gi.wf_instance_id))
              from '.GALAXIA_TABLE_PREFIX.'instances gi
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'instance_activities gia ON gi.wf_instance_id=gia.wf_instance_id
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'activity_roles gar ON gia.wf_activity_id=gar.wf_activity_id
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'user_roles gur ON gar.wf_role_id=gur.wf_role_id
              where gia.wf_activity_id=?
              and (';
        if (is_array($groups))
        {
          $query_act .= "(gur.wf_user in (".implode(",",$groups).") and gur.wf_account_type='g') or ";
        }
        $query_act .= "(gi.wf_owner=?)
                   or ((gur.wf_user=?) and gur.wf_account_type='u'))";
        $res['wf_instances']=$this->getOne($query_act, array($res['wf_activity_id'],$user,$user));
        $ret[] = $res;
      }
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

	/*! list user activities but each activity name (and not id) appears only one time
	* @param $user is the current user id
	* @param $offset is the current starting point for the query results
	* @param $maxRecords is the max number of results to return
	* @param $sort_mode is for sorting
	* @param $find is a string to search in activity name or description
	* @param $where is deprecated it's a string to add to the query, use with care for SQL injection
	* @param $remove_instances_activities is false by default, if true then all activities related to instances will be avoided
	* (i.e. activities which are not standalone, start or view).
	* @param $add_start is false by default, if true start activities are added to the listing
	* @param $add_standalone is false by default, if true standalone activities are added to the listing
	* @param $add_view is false by default, if true view activities are added to the listing
	* @return an associative array, key cant gives the number of results, key data is an associative array conteining the results
	*/
	function gui_list_user_activities_by_unique_name($user,$offset,$maxRecords,$sort_mode,$find,$where='', $remove_instances_activities =false, $add_start = false, $add_standalone = false, $add_view = false)
	{
		// FIXME: this doesn't support multiple sort criteria
		//$sort_mode = $this->convert_sortmode($sort_mode);
		$sort_mode = str_replace("__"," ",$sort_mode);
		$mid = "where gp.wf_is_active=?";
		$bindvars = array('y');

                if ($remove_instances_activities)
                {
                   $mid .= " and ga.wf_type <> ? and ga.wf_type <> ? and ga.wf_type <> ?  and  ga.wf_type <> ?  and  ga.wf_type <> ? ";
                   $bindvars[] = 'end';
                   $bindvars[] = 'switch';
                   $bindvars[] = 'join';
                   $bindvars[] = 'activity';
                   $bindvars[] = 'split';
                }
		if (!($add_start))
		{
		  $mid .= " and ga.wf_type <> ?";
		  $bindvars[] = 'start';
                }
                if (!($add_standalone))
		{
		  $mid .= " and ga.wf_type <> ?";
		  $bindvars[] = 'standalone';
                }
                if (!($add_view))
		{
		  $mid .= " and ga.wf_type <> ?";
		  $bindvars[] = 'view';
                }

		// add group mapping, warning groups and user can have the same id
		$groups = galaxia_retrieve_user_groups($user);
		$mid .= " and ((gur.wf_user=? and gur.wf_account_type='u')";
		if (is_array($groups))
		{
		  $mid .= ' or (gur.wf_user in ('.implode(',',$groups).") and gur.wf_account_type='g')";
                }
                $mid .= ')';

		$bindvars[] = $user;
		if($find)
		{
			$findesc = '%'.$find.'%';
			$mid .= " and ((ga.wf_name like ?) or (ga.wf_description like ?))";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
		if($where)
		{
			$mid.= " and ($where) ";
		}

		$query = "select distinct(ga.wf_name)
			from ".GALAXIA_TABLE_PREFIX."processes gp
			INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
			INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
			INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
			INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
			$mid";

		$query_cant = "select count(distinct(ga.wf_name))
			from ".GALAXIA_TABLE_PREFIX."processes gp
			INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
			INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
			INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
			INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
			$mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset, true, $sort_mode);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = Array();
		if (!(empty($result)))
		{
		  while($res = $result->fetchRow())
		  {
			$ret[] = $res;
                  }
                }

		$retval = Array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

  //! List start activities avaible for a given user
  function gui_list_user_start_activities($user,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    // FIXME: this doesn't support multiple sort criteria
    $sort_mode = str_replace("__"," ",$sort_mode);

    $mid = "where gp.wf_is_active=? and ga.wf_type=?";
    // add group mapping, warning groups and user can have the same id
    $groups = galaxia_retrieve_user_groups($user);
    $mid .= " and ((gur.wf_user=? and gur.wf_account_type='u')";
    if (is_array($groups))
    {
      $mid .= '	or (gur.wf_user in ('.implode(',',$groups).") and gur.wf_account_type='g')";
    }
    $mid .= ')';
    $bindvars = array('y','start',$user);
    if($find)
    {
      //search on activities and processes
      $findesc = '%'.$find.'%';
      $mid .= " and ((ga.wf_name like ?) or (ga.wf_description like ?) or (gp.wf_name like ?) or (gp.wf_description like ?))";
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }
    if($where)
    {
      $mid.= " and ($where) ";
    }

    $query = "select distinct(ga.wf_activity_id),
                              ga.wf_name,
                              ga.wf_is_interactive,
                              ga.wf_is_autorouted,
                              gp.wf_p_id,
                              gp.wf_name as wf_procname,
                              gp.wf_version,
			      gp.wf_normalized_name
        from ".GALAXIA_TABLE_PREFIX."processes gp
	INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
	INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
	INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
	INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
	$mid";
    $query_cant = "select count(distinct(ga.wf_activity_id))
	from ".GALAXIA_TABLE_PREFIX."processes gp
	INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.wf_p_id=ga.wf_p_id
	INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_activity_id=ga.wf_activity_id
	INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.wf_role_id=gar.wf_role_id
	INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.wf_role_id=gr.wf_role_id
	$mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset, true, $sort_mode);
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        $ret[] = $res;
      }
    }
    $retval = Array();
    $retval["data"]= $ret;
    $retval["cant"]= $this->getOne($query_cant,$bindvars);

    return $retval;
  }

  /*!
  * List instances avaible for a given user, theses instances are all the instances where the user is able
  * to launch a gui action (could be a run --even a run view activity-- or an advanced action like grab, release, admin, etc)
  * type of action really avaible are not given by this function. see getUserAction.
  * @param $user is the given user id
  * @param $offset is the starting number for the returned records
  * @param $maxRecords is the limit of records to return in data (but the 'cant' key count the total number without limits)
  * @param $sort_mode is the sort mode for the query
  * @param $find is a string to look at in activity name, activity description or instance name
  * @param $where is an empty string by default, the string let you add a string to the SQL statement -please be carefull with it.
  * @param $add_properties, false by default, will add properties in the returned instances
  * @param $pId is the process id, 0 by default, in such case it is ignored
  * @param $add_completed_instances false by default, if true we add completed instances in the result
  * @param $add_exception_instances false by default, if true we add instances in exception in the result
  * @param $add_aborted_instances false by default, if true we add aborted instances in the result
  * @param $restrict_to_owner false by default, if true we restrict to instance for which the user is the owner even if it gives no special rights (that can give more or less results -- you'll have ownership but no rights but you wont get rights without ownership)
  * @return an array with number of records in the 'cant key and instances in the 'data' key. Each instance
  * is an array containing theses keys: wf_instance_id, wf_started (instance), wf_ended (instance), wf_owner, wf_user, wf_status (instance status),
  * wf_category, wf_act_status, wf_act_started, wf_name (activity name), wf_type, wf_procname, wf_is_interactive, wf_is_autorouted, wf_activity_id,
  * wf_version (process version), wf_p_id, insname (instance name), wf_priority and wf_readonly (which is true if the user only have
  * read-only roles associated with this activity).
  */
  function gui_list_user_instances($user, $offset, $maxRecords, $sort_mode, $find, $where='', $add_properties=false, $pId=0, $add_active_instances=true, $add_completed_instances=false, $add_exception_instances=false, $add_aborted_instances=false, $restrict_to_owner=false)
  {
    // FIXME: this doesn't support multiple sort criteria
    //$sort_mode = $this->convert_sortmode($sort_mode);
    $sort_mode = str_replace("__"," ",$sort_mode);

    //process restriction
    $mid = 'where gp.wf_is_active=?';
    $bindvars = array('y');
    if (!($pId==0))
    {
        $mid.= " and gp.wf_p_id=?";
        $bindvars[] = $pId;
    }

    //look for a owner restriction
    if ($restrict_to_owner)
    {
        $mid .= "  and gi.wf_owner=?";
        $bindvars[] = $user;
    }
    else //no restriction on ownership, look for user and/or owner
    {
      // add group mapping, warning groups and user can have the same id
      $groups = galaxia_retrieve_user_groups($user);
      $mid .= " and (  ((gur.wf_user=? and gur.wf_account_type='u')";
      if (is_array($groups))
      {
        $mid .= '	or (gur.wf_user in ('.implode(',',$groups).") and gur.wf_account_type='g')";
      }
      $mid .= ')';

      // this collect non interactive instances we are owner of
      $mid .= " 	or ((gi.wf_owner=?) and ga.wf_is_interactive = 'n')";
      // and this collect completed/aborted instances when asked which haven't got any user anymore
      if ($add_completed_instances || add_aborted_instances)
      {
          $mid .= ' or (gur.wf_user is NULL)';
      }
      $mid .= ')';

      $bindvars[] = $user;
      $bindvars[] = $user;
    }

    if($find) {
      $findesc = '%'.$find.'%';
      $mid .= " and ( (upper(ga.wf_name) like upper(?))";
      $mid .= "       or (upper(ga.wf_description) like upper(?))";
      $mid .= "       or (upper(gi.wf_name) like upper(?)))";
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }
    if($where) {
      $mid.= " and ($where) ";
    }

    //instance selection :: instances can be active|exception|aborted|completed
    $or_status = Array();
    if ($add_active_instances) $or_status[] = "(gi.wf_status='active')";
    if ($add_exception_instances) $or_status[] = "(gi.wf_status='exception')";
    if ($add_aborted_instances) $or_status[] = "(gi.wf_status='aborted')";
    if ($add_completed_instances) $or_status[] = "(gi.wf_status='completed')";
    if (!(empty($or_status)))
    {
        $mid .= ' and ('.implode(' or ', $or_status).')';
    }
    else
    { //special case, we want no active instance, and we do not want exception/aborted and completed, so what?
      // maybe a special new status or some bad record in database...
      $mid .= " and (gi.wf_status NOT IN ('active','exception','aborted','completed'))";
    }


    // (regis) we need LEFT JOIN because aborted and completed instances are not showned
    // in instance_activities, they're only in instances
    $query = 'select distinct(gi.wf_instance_id),
                     gi.wf_started,
                     gi.wf_ended,
                     gi.wf_owner,
                     gia.wf_user,
                     gi.wf_status,
                     gi.wf_category,
                     gia.wf_status as wf_act_status,
                     gia.wf_started as wf_act_started,
                     ga.wf_name,
                     ga.wf_type,
                     gp.wf_name as wf_procname,
                     ga.wf_is_interactive,
                     ga.wf_is_autorouted,
                     ga.wf_activity_id,
                     gp.wf_version as wf_version,
                     gp.wf_p_id,
		     gp.wf_normalized_name,
                     gi.wf_name as insname,
                     gi.wf_priority,
                     MIN(gar.wf_readonly) as wf_readonly';
    $query .= ($add_properties)? ', gi.wf_properties' : '';
    $query .= ' from '.GALAXIA_TABLE_PREFIX.'instances gi
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'instance_activities gia ON gi.wf_instance_id=gia.wf_instance_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activities ga ON gia.wf_activity_id = ga.wf_activity_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activity_roles gar ON gia.wf_activity_id=gar.wf_activity_id';
    $query .= ($restrict_to_owner)? '': ' LEFT JOIN '.GALAXIA_TABLE_PREFIX.'user_roles gur ON gur.wf_role_id=gar.wf_role_id';
    $query .= ' INNER JOIN '.GALAXIA_TABLE_PREFIX.'processes gp ON gp.wf_p_id=gi.wf_p_id
              '.$mid.'
              GROUP BY gi.wf_instance_id, gi.wf_started, gi.wf_ended, gi.wf_owner, gia.wf_user, gi.wf_status, gi.wf_category,
              gia.wf_status, gia.wf_started, ga.wf_name, ga.wf_type, gp.wf_name, ga.wf_is_interactive, ga.wf_is_autorouted,
              ga.wf_activity_id, gp.wf_version, gp.wf_p_id, gp.wf_normalized_name, gi.wf_name, gi.wf_priority';
    $query .= ($add_properties)? ', gi.wf_properties' : '';
    // (regis) this count query as to count global -unlimited- (instances/activities) not just instances
    // as we can have multiple activities for one instance and we will show all of them and the problem is
    // that a user having memberships in several groups having the rights is counted several times.
    // If we count instance_id without distinct we'll have several time the same line.
    // the solution is to count distinct instance_id for each activity and to sum theses results
    $query_cant = 'select count(distinct(gi.wf_instance_id)) as cant, gia.wf_activity_id
              from '.GALAXIA_TABLE_PREFIX.'instances gi
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'instance_activities gia ON gi.wf_instance_id=gia.wf_instance_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activities ga ON gia.wf_activity_id = ga.wf_activity_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activity_roles gar ON gia.wf_activity_id=gar.wf_activity_id';
    $query_cant .= ($restrict_to_owner)? '': ' LEFT JOIN '.GALAXIA_TABLE_PREFIX.'user_roles gur ON gur.wf_role_id=gar.wf_role_id';
    $query_cant .=' INNER JOIN '.GALAXIA_TABLE_PREFIX.'processes gp ON gp.wf_p_id=gi.wf_p_id
              '.$mid.'
                GROUP BY gia.wf_activity_id';
    //echo "<br> query => ".$query; _debug_array($bindvars);

    $result = $this->query($query,$bindvars,$maxRecords,$offset, true, $sort_mode);
    $resultcant = $this->query($query_cant,$bindvars);
    $ret = Array();
    if (!(empty($result)))
    {
      $record = Array();
      $i = 0;
      while($res = $result->fetchRow())
      {
        // Get instances per activity
        $ret[]=$res;
      }
    }
    $cant=0;
    if (!(empty($resultcant)))
    {
      while($rescant = $resultcant->fetchRow())
      {
        // Get number of distinct instances per activity
        $cant += $rescant['cant'];
      }
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

/*!
* List all instances where the user is the owner (active, completed, aborted, exception)
  * @param $user is the given user id
  * @param $offset is the starting number for the returned records
  * @param $maxRecords is the limit of records to return in data (but the 'cant' key count the total number without limits)
  * @param $sort_mode is the sort mode for the query
  * @param $find is a string to look at in activity name, activity description or instance name
  * @param $where is an empty string by default, the string let you add a string to the SQL statement -please be carefull with it.
  * @param $add_properties, false by default, will add properties in the returned instances
  * @param $pId is the process id, 0 by default, in such case it is ignored
  * @param $add_completed_instances false by default, if true we add completed instances in the result
  * @param $add_exception_instances false by default, if true we add instances in exception in the result
  * @param $add_aborted_instances false by default, if true we add aborted instances in the result
  * @return an associative array, key cant gives the number of results, key data is an array of instances and each instance
  * an array containing theses keys: wf_instance_id, wf_started (instance), wf_ended (instance), wf_owner, wf_user,
  * wf_status (instance status), wf_category, wf_act_status (activity), wf_act_started (activity), wf_name (activity name),
  * wf_type, wf_procname, wf_is_interactive, wf_is_autorouted, wf_activity_id, wf_version (process version), wf_p_id,
  * insname (instance name), wf_priority and wf_readonly (which is true if the user only have read-only roles associated
  * with this activity).
  */
  function gui_list_instances_by_owner($user, $offset, $maxRecords, $sort_mode, $find, $where='', $add_properties=false, $pId=0, $add_active_instances=true, $add_completed_instances=false, $add_exception_instances=false, $add_aborted_instances=false)
  {
	return $this->gui_list_user_instances($user,$offset,$maxRecords,$sort_mode,$find,$where,$add_properties, $pId,$add_active_instances,$add_completed_instances,$add_exception_instances, $add_aborted_instances,true);
  }

  /*! Get the view activity id avaible for a given process
  * No test is done on real access to this activity for users, this access will be check at runtime (when clicking)
  * @param $pId is the process Id
  * @return the view activity id or false if no view activity is present dor this process
  */
  function gui_get_process_view_activity($pId)
  {
    if (!(isset($this->process_cache[$pId]['view'])))
    {
      if (!(isset($this->pm)))
      {
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'ProcessManager'.'/'.'ProcessManager.php');
        $this->pm =& new ProcessManager($this->db);
      }
      $this->process_cache[$pId]['view'] = $this->pm->get_process_view_activity($pId);
    }
    return $this->process_cache[$pId]['view'];
  }

  //! gets all informations about a given instance and a given user, list activities and status
  /*!
  * We list activities for which the user is the owner or the actual user or in a role giving him access to the activity
  * notice that completed and aborted instances aren't associated with activities and that start and standalone activities
  * aren't associated with an instance ==> if instanceId is 0 you'll get all standalone and start activities in the result.
  * this is the reason why you can give --if you have it-- the process id, to restrict results to start and standalone
  * activities to this process.
  * @param $user is the user id
  * @param $instance_id is the instance id
  * @param $pId is the process id, 0 by default, in such case it is ignored
  * @param $add_completed_instances false by default, if true we add completed instances in the result
  * @param $add_exception_instances false by default, if true we add instances in exception in the result
  * @param $add_aborted_instances false by default, if true we add aborted instances in the result
  * @return an associative array containing :
  * ['instance'] =>
  *     ['instance_id'], ['instance_status'], ['owner'], ['started'], ['ended'], ['priority'], ['instance_name'],
  *    ['process_name'], ['process_version'], ['process_id']
  * ['activities'] =>
  *     ['activity'] =>
  *         ['user']		: actual user
  *         ['id']		: activity Id
  *         ['name']
  *         ['type']
  *         ['is_interactive']	: 'y' or 'n'
  *         ['is_autorouted']	: 'y' or 'n'
  *         ['status']
  */
  function gui_get_user_instance_status($user,$instance_id, $pId=0, $add_completed_instances=false,$add_exception_instances=false, $add_aborted_instances=false)
  {
    $bindvars =Array();
    $mid = "\n where gp.wf_is_active=?";
    $bindvars[] = 'y';
    if (!($pId==0))
    {
      // process restriction
      $mid.= " and gp.wf_p_id=?";
      $bindvars[] = $pId;
    }
    if (!($instance_id==0))
    {
      // instance selection
      $mid .= " and (gi.wf_instance_id=?)";
      $bindvars[] = $instance_id;
      $statuslist[]='active';
      if ($add_exception_instances) $statuslist[]='exception';
      if ($add_aborted_instances) $statuslist[]='aborted';
      if ($add_completed_instances) $statuslist[]='completed';
      $status_list = implode ($statuslist,',');
      $mid .= " and (gi.wf_status in ('".implode ("','",$statuslist)."'))\n";
    }
    else
    {
      // collect NULL instances for start and standalone activities
      $mid .= " and (gi.wf_instance_id is NULL)";
    }
    // add group mapping, warning groups and user can have the same id
    $groups = galaxia_retrieve_user_groups($user);
    $mid .= "\n and ( ((gur.wf_user=? and gur.wf_account_type='u')";
    if (is_array($groups))
    {
      $mid .= '	or (gur.wf_user in ('.implode(',',$groups).") and gur.wf_account_type='g')";
    }
    $mid .= ')';
    $bindvars[] = $user;
    // this collect non interactive instances we are owner of
    $mid .= "\n or (gi.wf_owner=?)";
    $bindvars[] = $user;
    // and this collect completed/aborted instances when asked which haven't got any user anymore
    if (($add_completed_instances) || ($add_aborted_instances))
    {
      $mid .= "\n or (gur.wf_user is NULL)";
    }
    $mid .= ")";

    // we need LEFT JOIN because aborted and completed instances are not showned
    // in instance_activities, they're only in instances
    $query = 'select distinct(gi.wf_instance_id) as instance_id,
                     gi.wf_status as instance_status,
                     gi.wf_owner as owner,
                     gi.wf_started as started,
                     gi.wf_ended as ended,
                     gi.wf_priority as priority,
                     gi.wf_name as instance_name,
                     gp.wf_name as process_name,
                     gp.wf_version as process_version,
                     gp.wf_p_id as process_id,
                     gia.wf_user as user,
                     ga.wf_activity_id as id,
                     ga.wf_name as name,
                     ga.wf_type as type,
                     ga.wf_is_interactive as is_interactive,
                     ga.wf_is_autorouted as is_autorouted,
                     gia.wf_status as status';
    if ($instance_id==0)
    {//TODO: this gives all activities, rstrict to standalone and start
      $query.=' from '.GALAXIA_TABLE_PREFIX.'activities ga
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'instance_activities gia ON ga.wf_activity_id=gia.wf_activity_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'instances gi ON gia.wf_activity_id = gi.wf_instance_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activity_roles gar ON gia.wf_activity_id=gar.wf_activity_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'user_roles gur ON gur.wf_role_id=gar.wf_role_id
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'processes gp ON gp.wf_p_id=ga.wf_p_id '.$mid;
    }
    else
    {
      $query.=' from '.GALAXIA_TABLE_PREFIX.'instances gi
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'instance_activities gia ON gi.wf_instance_id=gia.wf_instance_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activities ga ON gia.wf_activity_id = ga.wf_activity_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'activity_roles gar ON gia.wf_activity_id=gar.wf_activity_id
                LEFT JOIN '.GALAXIA_TABLE_PREFIX.'user_roles gur ON gur.wf_role_id=gar.wf_role_id
                INNER JOIN '.GALAXIA_TABLE_PREFIX.'processes gp ON gp.wf_p_id=gi.wf_p_id '.$mid;
    }
    $result = $this->query($query,$bindvars);
    $retinst = Array();
    $retacts = Array();
    if (!!$result)
    {
      while($res = $result->fetchRow())
      {
        // Get instances per activity
        if (count($retinst)==0)
        {//the first time we retain instance data
          $retinst[] = array_slice($res,0,-7);
        }
        $retacts[] = array_slice($res,10);
      }
    }
    $retval = Array();
    $retval["instance"] = $retinst{0};
    $retval["activities"] = $retacts;
    return $retval;
  }

  //!Abort an instance - this terminates the instance with status 'aborted', and removes all running activities
  function gui_abort_instance($activityId,$instanceId)
  {
    $user = galaxia_retrieve_running_user();

    // start a transaction
    $this->db->StartTrans();

    if (!($this->wf_security->checkUserAction($activityId, $instanceId,'abort')))
    {
      $this->error[] = ($this->wf_security->get_error());
      $this->db->FailTrans();
    }
    else
    {
      //the security object said everything was fine
      $instance = new Instance($this->db);
      $instance->loadInstance($instanceId);
      if (!empty($instance->instanceId))
      {
          if (!($instance->abort($activityId,$user)))
          {
            $this->error[] = ($instance->get_error());
            $this->db->FailTrans();
          }
      }
      unset($instance);
    }
    // perform commit (return true) or Rollback (return false) if Failtrans it will automatically rollback
    return $this->db->CompleteTrans();
  }

  //!Exception handling for an instance - this sets the instance status to 'exception', but keeps all running activities.
  /*!
  * The instance can be resumed afterwards via gui_resume_instance().
  */
  function gui_exception_instance($activityId,$instanceId)
  {
    $user = galaxia_retrieve_running_user();

    // start a transaction
    $this->db->StartTrans();

    if (!($this->wf_security->checkUserAction($activityId, $instanceId,'exception')))
    {
      $this->error[] = ($this->wf_security->get_error());
      $this->db->FailTrans();
    }
    else
    {
      //the security object said everything was fine
      $query = "update ".GALAXIA_TABLE_PREFIX."instances
              set wf_status=?
              where wf_instance_id=?";
      $this->query($query, array('exception',$instanceId));
    }
    // perform commit (return true) or Rollback (return false) if Failtrans it will automatically rollback
    return $this->db->CompleteTrans();
  }

  /*!
  Resume an instance - this sets the instance status from 'exception' back to 'active'
  */
  function gui_resume_instance($activityId,$instanceId)
  {
    $user = galaxia_retrieve_running_user();

    // start a transaction
    $this->db->StartTrans();

    if (!($this->wf_security->checkUserAction($activityId, $instanceId,'resume')))
    {
      $this->error[] = ($this->wf_security->get_error());
      $this->db->FailTrans();
    }
    else
    {
      //the security object said everything was fine
      $query = "update ".GALAXIA_TABLE_PREFIX."instances
              set wf_status=?
              where wf_instance_id=?";
      $this->query($query, array('active',$instanceId));
    }
    // perform commit (return true) or Rollback (return false) if Failtrans it will automatically rollback
    return $this->db->CompleteTrans();
  }

  /*!
  * This function restart an automated activity (non-interactive) which is still in running mode (maybe it failed)
  * @param $activityId is the activity Id (the starting point)
  * @param $instanceId is the instance Id
  * @return the result true or false, if false nothing was done
  */
  function gui_restart_instance($activityId,$instanceId)
  {
    $user = galaxia_retrieve_running_user();

    //start a transaction
    $this->db->StartTrans();

    if (!($this->wf_security->checkUserAction($activityId, $instanceId,'restart')))
    {
      $this->error[] = ($this->wf_security->get_error());
      $this->db->FailTrans();
    }
    else
    {
      //the security object said everything was fine
      $instance =& new Instance($this->db);
      $instance->loadInstance($instanceId);
      // we force the execution of the activity
      $result = $instance->executeAutomaticActivity($activityId, $instanceId);
      //TODO handle information returned in the sendAutorouted like in the completed activity template
      //_debug_array($result);
      $this->error[] = $instance->get_error();
      unset($instance);
    }
    // perform commit (return true) or Rollback (return false) if Failtrans it will automatically rollback
    return $this->db->CompleteTrans();
  }

  /*!
  * This function send a non autorouted activity i.e. take the transition which was not
  * taken automatically. It can be as well used to walk a transition which failed the first time
  * by the admin.
  * @param $activityId is the activity Id (the starting point)
  * @param $instanceId is the instance Id
  * @return the result true or false, if false nothing was done
  */
  function gui_send_instance($activityId,$instanceId)
  {
    $user = galaxia_retrieve_running_user();

    //start a transaction
    $this->db->StartTrans();

    if (!($this->wf_security->checkUserAction($activityId, $instanceId,'send')))
    {
      $this->error[] = ($this->wf_security->get_error());
      $this->db->FailTrans();
    }
    else
    {
      //the security object said everything was fine
      $instance =& new Instance($this->db);
      $instance->loadInstance($instanceId);
      // we force the continuation of the flow
      $result = $instance->sendAutorouted($activityId,true);
      //TODO handle information returned in the sendAutorouted like in the completed activity template
      //_debug_array($result);
      $this->error[] = $instance->get_error();
      unset($instance);
    }
    // perform commit (return true) or Rollback (return false) if Failtrans it will automatically rollback
    return $this->db->CompleteTrans();
  }


  function gui_release_instance($activityId,$instanceId)
  {
    $user = galaxia_retrieve_running_user();

    // start a transaction
    $this->db->StartTrans();

    if (!($this->wf_security->checkUserAction($activityId, $instanceId,'release')))
    {
      $this->error[] = ($this->wf_security->get_error());
      $this->db->FailTrans();
    }
    else
    {
      //the security object said everything was fine
      $query = "update ".GALAXIA_TABLE_PREFIX."instance_activities
                set wf_user = ?
                where wf_instance_id=? and wf_activity_id=?";
      $this->query($query, array('*',$instanceId,$activityId));
    }
    // perform commit (return true) or Rollback (return false) if Failtrans it will automatically rollback
    return $this->db->CompleteTrans();
  }

  //! grab the instance for this activity and user if the security object agreed
  function gui_grab_instance($activityId,$instanceId)
  {
    $user = galaxia_retrieve_running_user();

    // start a transaction
    $this->db->StartTrans();
    //this check will as well lock the table rows
    if (!($this->wf_security->checkUserAction($activityId, $instanceId,'grab')))
    {
      $this->error[] = ($this->wf_security->get_error());
      $this->db->FailTrans();
    }
    else
    {
      //the security object said everything was fine
      $query = "update ".GALAXIA_TABLE_PREFIX."instance_activities
                set wf_user = ?
                where wf_instance_id=? and wf_activity_id=?";
      $this->query($query, array($user,$instanceId,$activityId));
    }
    // perform commit (return true) or Rollback (return false) if Failtrans it will automatically rollback
    return $this->db->CompleteTrans();
  }


  //! Return avaible actions for a given user on a given activity and a given instance assuming he already have access to it.
  /*!
  * @public
  * To be able to decide this function needs the user id, instance_id and activity_id.
  * @param $user must be the user id
  * @param $instanceId must be the instance id (can be 0 if you have no instance - for start or standalone activities)
  * @param $activityId must be the activity id (can be 0 if you have no activity - for aborted or completed instances)
  * @param $readonly is he role mode, if true this is a readonly access, if false it is a not-only-read access
  * All other datas can be retrieved by internal queries BUT if you want this function to be fast and if you already
  * have theses datas you should give as well theses fields (all or none):
  * @param $pId the process id
  * @param $actType is the activity type string ('split', 'activity', 'switch', etc.)
  * @param $actInteractive is the activity interactivity ('y' or 'n')
  * @param $actAutorouted is the activity routage ('y' or 'n')
  * @param $actStatus is tha activity status ('completed' or 'running')
  * @param $instanceOwner is the instance owner user id
  * @param $instanceStatus is the instance status ('completed', 'active', 'exception', 'aborted')
  * @param $currentUser is the actual user of the instance (user id or '*')
  * @return an array of this form:
  * array('action name' => 'action description')
  * 'actions names' are: 'grab', 'release', 'run', 'send', 'view', 'exception', 'resume' and 'monitor'
  * Some config values can change theses rules but basically here they are:
  * 	* 'grab'	: be the user of this activity. User has access to it and instance status is ok.
  * 	* 'release'	: let * be the user of this activity. Must be the actual user or the owner of the instance.
  * 	* 'run'		: run an associated form. This activity is interactive, user has access, instance status is ok.
  * 	* 'send'	: send this instance, activity was non-autorouted and he has access and status is ok.
  * 	* 'view'	: view the instance, activity ok, always avaible except for start or standalone act or processes with view activities.
  *	* 'viewrun'	: view the instance in a view activity, need to have a role on this view activity
  * 	* 'abort'	: abort an instance, ok when we are the user
  * 	* 'exception' 	: set the instance status to exception, need to be the user
  * 	* 'resume'	: back to running when instance status was exception, need to be the user
  * 	* 'monitor' 	: special user rights to administer the instance
  * 'actions description' are translated explanations like 'release access to this activity'
  * WARNING: this is a snapshot, the engine give you a snaphsots of the rights a user have on an instance-activity
  * at a given time, this is not meaning theses rights will still be there when the user launch the action.
  * You should absolutely use the GUI Object to execute theses actions (except monitor) and they could be rejected.
  * WARNING: we do not check the user access rights. If you launch this function for a list of instances obtained via this
  * GUI object theses access rights are allready checked.
  */
  function getUserActions($user, $instanceId, $activityId, $readonly, $pId=0, $actType='not_set', $actInteractive='not_set', $actAutorouted='not_set', $actStatus='not_set', $instanceOwner='not_set', $instanceStatus='not_set', $currentUser='not_set')
  {
    $result= array();//returned array

    //check if we have all the args and retrieve the ones whe did not have:
    if ((!($pId)) ||
      ($actType=='not_set') ||
      ($actInteractive=='not_set') ||
      ($actAutorouted=='not_set') ||
      ($actStatus=='not_set') ||
      ($instanceOwner=='not_set') ||
      ($currentUser=='not_set') ||
      ($instanceStatus=='not_set'))
    {
      // get process_id, type, interactivity, autorouting and act status and others for this instance
      // we retrieve info even if ended or in exception or aborted instances
      // and if $instanceId is 0 we get all standalone and start activities
      //echo '<br> call gui_get_user_instance_status:'.$pId.':'.$actType.':'.$actInteractive.':'.$actAutorouted.':'.$actStatus.':'.$instanceOwner.':'.$currentUser.':'.$instanceStatus;
      $array_info = $this->gui_get_user_instance_status($user,$instanceId,0,true,true,true);

      //now set our needed values
      $instance = $array_info['instance'];
      $pId = $instance['instance_id'];
      $instanceStatus = $instance['instance_status'];
      $instanceOwner = $instance['owner'];

      if (!((int)$activityId))
      {
        //we have no activity Id, like for aborted or completed instances, we set default values
        $actType = '';
        $actInteractive = 'n';
        $actAutorouted = 'n';
        $actstatus = '';
        $currentUser = 0;
      }
      else
      {
        $find=false;
        foreach ($array_info['activities'] as $activity)
        {
          //_debug_array($activity);
          //echo "<br> ==>".$activity['id']." : ".$activityId;
          if ((int)$activity['id']==(int)$activityId)
          {
            $actType = $activity['type'];
            $actInteractive = $activity['is_interactive'];
            $actAutorouted = $activity['is_autorouted'];
            $actstatus = $activity['status'];
            $currentUser = $activity['user'];
            $find = true;
            break;
          }
        }
        //if the activity_id can't be find we return empty actions
        if (!($find))
        {
          return array();
        }
      }
    }

    //now use the security object to get actions avaible, this object know the rules
    $view_activity = $this->gui_get_process_view_activity($pId);
    $result =& $this->wf_security->getUserActions($user, $instanceId, $activityId, $readonly, $pId, $actType, $actInteractive, $actAutorouted, $actStatus, $instanceOwner, $instanceStatus, $currentUser, $view_activity);
    return $result;
  }


}
?>

