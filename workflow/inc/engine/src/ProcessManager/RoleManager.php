<?php

require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'ProcessManager'.'/'.'BaseManager.php');
//!! RoleManager
//! A class to manipulate roles.
/*!
  This class is used to add,remove,modify and list
  roles used in the Workflow engine.
  Roles are managed in a per-process level, each
  role belongs to some process.
*/

/*!TODO
  Add a method to check if a role name exists in a process (to be used
  to prevent duplicate names)
*/

class RoleManager extends BaseManager {

  /*!
  *  Constructor takes a PEAR::Db object to be used
  *  to manipulate roles in the database.
  */
  function RoleManager(&$db)
  {
    parent::Base($db);
    $this->child_name = 'RoleManager';
  }

  function get_role_id($pid,$name)
  {
    $name = addslashes($name);
    return ($this->getOne('select wf_role_id from '.GALAXIA_TABLE_PREFIX.'roles where wf_name=? and wf_p_id=?', array($name, $pid)));
  }

  /*!
  * Gets a role
  * @param $pId is the process Id
  * @param $roleId is the role Id
  * @return fields are returned as an associative array
  */
  function get_role($pId, $roleId)
  {
    $query = 'select * from `'.GALAXIA_TABLE_PREFIX.'roles` where `wf_p_id`=? and `wf_role_id`=?';
    $result = $this->query($query,array($pId, $roleId));
    $res = $result->fetchRow();
    return $res;
  }

  /*!
  * Indicates if a role exists
  * @param $pid is the process Id
  * @param $name is the name of the role
  * @return the number of roles with this name on this process
  */
  function role_name_exists($pid,$name)
  {
    $name = addslashes($name);
    return ($this->getOne('select count(*) from '.GALAXIA_TABLE_PREFIX.'roles where wf_p_id=? and wf_name=?', array($pid, $name)));
  }

  /*!
    Maps a user to a role
  */
  function map_user_to_role($pId,$user,$roleId,$account_type='u')
  {
  $query = 'delete from `'.GALAXIA_TABLE_PREFIX.'user_roles` where wf_p_id=? AND wf_account_type=? and `wf_role_id`=? and `wf_user`=?';
  $this->query($query,array($pId, $account_type,$roleId, $user));
  $query = 'insert into '.GALAXIA_TABLE_PREFIX.'user_roles (wf_p_id, wf_user, wf_role_id ,wf_account_type)
  values(?,?,?,?)';
  $this->query($query,array($pId,$user,$roleId,$account_type));
  }

  /*!
    Removes a mapping
  */
  function remove_mapping($user,$roleId)
  {
    $query = 'delete from `'.GALAXIA_TABLE_PREFIX.'user_roles` where `wf_user`=? and `wf_role_id`=?';
    $this->query($query,array($user, $roleId));
  }

  //! Removes an user from the role mappings
  /*!
  * This function delete all existing mappings concerning one user
  * @param $user is the user id
  */
  function remove_user($user)
  {
    $query = 'delete from '.GALAXIA_TABLE_PREFIX.'user_roles where wf_user=?';
    $this->query($query,array($user));
  }

  //! Transfer all roles from an user to another one
  /*!
  * This function transfer all existing mappings concerning one user to another user
  * @param $user_array is an associative arrays, keys are:
  *     * 'old_user' : the actual user id
  *     * 'new_user' : the new user id
  */
  function transfer_user($user_array)
  {
    $query = 'update '.GALAXIA_TABLE_PREFIX.'user_roles set wf_user=? where wf_user=?';
    $this->query($query,array($user_array['new_user'], $user_array['old_user']));
  }

  //!  List mappings
  /*!
  * get a list of roles/users mappings for a given process.
  * @param $pId Process Id. The mappings are returned for a complete process.
  * @param $offset first record of the returned array
  * @param $maxRecords maximum number of records for the returned array
  * @param $sort_mode sort order for the query, like 'wf_name__ASC'
  * @param $find searched string in role name, role description or user/group name
  * @return an array containg for each row [wf_name] (role name),[wf_role_id],[wf_user] and [wf_account_type] ('u' user  or 'g' group)
  * warning: you can have the same user or group several time if mapped to several roles.
  */
  function list_mappings($pId,$offset,$maxRecords,$sort_mode,$find)  {
    $sort_mode = $this->convert_sortmode($sort_mode);
    $whereand = ' and gur.wf_p_id=? ';
    $bindvars = Array($pId);
    if($find)
    {
      // no more quoting here - this is done in bind vars already
      $findesc = '%'.$find.'%';
      $whereand .=  ' and ((wf_name like ?) or (wf_user like ?) or (wf_description like ?)) ';
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
      $bindvars[] = $findesc;
    }

    $query = "select wf_name,gr.wf_role_id,wf_user,wf_account_type from
                    ".GALAXIA_TABLE_PREFIX."roles gr,
                    ".GALAXIA_TABLE_PREFIX."user_roles gur
                where gr.wf_role_id=gur.wf_role_id
                $whereand";
    $result = $this->query($query,$bindvars, $maxRecords, $offset, true, $sort_mode);
    $query_cant = "select count(*) from
                      ".GALAXIA_TABLE_PREFIX."roles gr,
                      ".GALAXIA_TABLE_PREFIX."user_roles gur
                  where gr.wf_role_id=gur.wf_role_id
                  $whereand";
    $cant = $this->getOne($query_cant,$bindvars);

    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  //!  List users/groups mapped on a process, this can be restricted to some activities
  /*!
  * Get a list of users/groups mapped for a given process. Can expand groups to real users in the result and can restrict
  * the mappings to a given subset of roles and or activities
  * @param $pId Process Id. The mappings are returned for a complete process by default (see param roles_subset or activities_subset).
  * @param $expand_groups if true (false by default) we are not giving the group mappings but instead expand
  * 	theses groups to real users while avoiding repeating users twice.
  * @param $subset associative array containing a list of roles and/or activities for which we want to restrict the list.
  * empty by default.
  * This array need to contains the [wf_role_name] key with role names values to restrict roles.
  * This array need to contains the [wf_activity_name] key with activity names values to restrict activities.
  * @return an array containg for each row the user or group id and an associated name
  */

  function &list_mapped_users($pId,$expand_groups=false, $subset=Array())
  {
    $whereand = ' where gur.wf_p_id=? ';
    $bindvars = Array($pId);

    if (!(count($subset)==0))
    {
       $roles_subset = Array();
       $activities_subset =Array();
       foreach($subset as $key => $value )
       {
         if ($key=='wf_role_name')
         {
           $roles_subset = $value;
         }
         if ($key=='wf_activity_name')
         {
           $activities_subset = $value;
         }
       }
       if (count($roles_subset)>0)
       {
         if (!(is_array($roles_subset)))
         {
           $roles_subset = explode(',',$roles_subset);
         }
         $whereand .= " and ((gr.wf_name) in ('".implode("','",$roles_subset)."'))";
       }
       if (count($activities_subset)>0)
       {
         if (!(is_array($activities_subset)))
         {
           $activities_subset = explode(',',$activities_subset);
         }
         $whereand .= " and ((ga.wf_name) in ('".implode("','",$activities_subset)."'))";
       }
    }
    $query = "select distinct(wf_user),wf_account_type from
                    ".GALAXIA_TABLE_PREFIX."roles gr
                INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gr.wf_role_id=gur.wf_role_id
                LEFT JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.wf_role_id=gr.wf_role_id
                LEFT JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON ga.wf_activity_id=gar.wf_activity_id
                $whereand ";
    $result = $this->query($query,$bindvars);
    $ret = Array();
    if (!(empty($result)))
    {
      while($res = $result->fetchRow())
      {
        if (($expand_groups) && ($res['wf_account_type']=='g'))
        {
          //we have a group instead of a simple user and we want real users
          $real_users = galaxia_retrieve_group_users($res['wf_user'], true);
          foreach ($real_users as $key => $value)
          {
            $ret[$key]=$value;
          }
        }
        else
        {
          $ret[$res['wf_user']] = galaxia_retrieve_name($res['wf_user']);
        }
      }
    }
    return $ret;
  }

  /*!
    Lists roles at a per-process level
  */
  function list_roles($pId,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = $this->convert_sortmode($sort_mode);
    if($find) {
      // no more quoting here - this is done in bind vars already
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
    $query = 'select * from '.GALAXIA_TABLE_PREFIX."roles $mid";
    $query_cant = 'select count(*) from '.GALAXIA_TABLE_PREFIX."roles $mid";
    $result = $this->query($query,$bindvars,$maxRecords,$offset, 1, $sort_mode);
    $cant = $this->getOne($query_cant,$bindvars);
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval['data'] = $ret;
    $retval['cant'] = $cant;
    return $retval;
  }

  /*!
  * Removes a role.
  * @param $pId is the process Id
  * @param $roleId is the role Id
  * @return true if everything was ok, false in the other case
  */
  function remove_role($pId, $roleId)
  {
    // start a transaction
    $this->db->StartTrans();
    $query = 'delete from `'.GALAXIA_TABLE_PREFIX.'roles` where `wf_p_id`=? and `wf_role_id`=?';
    $this->query($query,array($pId, $roleId));
    $query = 'delete from `'.GALAXIA_TABLE_PREFIX.'activity_roles` where `wf_role_id`=?';
    $this->query($query,array($roleId));
    $query = 'delete from `'.GALAXIA_TABLE_PREFIX.'user_roles` where `wf_role_id`=?';
    $this->query($query,array($roleId));
    // perform commit (return true) or Rollback (return false)
    return $this->db->CompleteTrans();
    }

  /*!
  * Updates or inserts a new role in the database,
  * @param $vars is an associative array containing the fields to update or to insert as needed.
  * @param $pId is the processId
  * @param $roleId is the roleId, 0 in insert mode
  * @return the roleId (the new one if in insert mode) if everything was ok, false in the other case
  */
  function replace_role($pId, $roleId, $vars)
  {
    // start a transaction
    $this->db->StartTrans();
    $TABLE_NAME = GALAXIA_TABLE_PREFIX.'roles';
    $now = date("U");
    if (!(isset($vars['wf_last_modif']))) $vars['wf_last_modif']=$now;
    $vars['wf_p_id']=$pId;

    foreach($vars as $key=>$value)
    {
      $vars[$key]=addslashes($value);
    }

    if($roleId) {
      // update mode
      $first = true;
      $query ="update $TABLE_NAME set";
      $bindvars = Array();
      foreach($vars as $key=>$value)
      {
        if(!$first) $query.= ',';
        //if(!is_numeric($value)) $value="'".$value."'";
        $query.= " $key=? ";
        $bindvars[] = $value;
        $first = false;
      }
      $query .= ' where wf_p_id=? and wf_role_id=? ';
      $bindvars[] = $pId;
      $bindvars[] = $roleId;
      $this->query($query, $bindvars);
    }
    else
    {
      //check unicity
      $name = $vars['wf_name'];
      if ($this->getOne('select count(*) from '.$TABLE_NAME.' where wf_p_id=? and wf_name=?', array($pId,$name)))
      {
        return false;
      }
      unset($vars['wf_role_id']);
      // insert mode
      $bindvars = Array();
      $first = true;
      $query = "insert into $TABLE_NAME(";
      foreach(array_keys($vars) as $key)
      {
        if(!$first) $query.= ',';
        $query.= "$key";
        $first = false;
      }
      $query .=') values(';
      $first = true;
      foreach(array_values($vars) as $value)
      {
        if(!$first) $query.= ',';
        //if(!is_numeric($value)) $value="'".$value."'";
        $query.= '?';
        $bindvars[] = $value;
        $first = false;
      }
      $query .=')';
      $this->query($query, $bindvars);
      //get the last inserted row
      $roleId = $this->getOne('select max(wf_role_id) from '.$TABLE_NAME.' where wf_p_id=?', array($pId));
    }
    // perform commit (return true) or Rollback (return false)
    if ($this->db->CompleteTrans())
    {
      // Get the id
      return $roleId;
    }
    else
    {
      return false;
    }
  }

  /*!
  * List all users and groups recorded in the mappings with their status (user or group)
  * @return an associative array containing a row for each user. This row is an array
  * containing 'wf_user' and 'wf_account_type' keys.
  */
  function get_all_users()
  {
    $final = Array();
    //query for user mappings affected to groups & vice-versa
    $query ='select distinct(gur.wf_user), gur.wf_account_type
            from '.GALAXIA_TABLE_PREFIX.'user_roles gur';
    $result = $this->query($query);
    if (!(empty($result)))
    {
	while ($res = $result->fetchRow())
	{
		$final[] = $res;
	}
    }
    return $final;
  }

}

?>
