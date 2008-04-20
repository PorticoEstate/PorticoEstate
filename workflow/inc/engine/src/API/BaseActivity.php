<?php
require_once (GALAXIA_LIBRARY.'/'.'src'.'/'.'common'.'/'.'Base.php');
//!! Abstract class representing activities
//! An abstract class representing activities
/*!
This class represents activities, and must be derived for
each activity type supported in the system. Derived activities extending this
class can be found in the activities subfolder.
This class is observable.
*/
class BaseActivity extends Base {
  var $name;
  var $normalizedName;
  var $description;
  var $isInteractive;
  var $isAutoRouted;
  var $roles=Array();
  var $outbound=Array();
  var $inbound=Array();
  var $pId;
  var $activityId;
  var $type;
  var $defaultUser='*';
  var $agents=Array();

  /*!
  * @deprecated
  * seems to be the rest of a bad object architecture
  */
  function setDb(&$db)
  {
    $this->db =& $db;
  }

  /*!
  * constructor of the BaseActivity Object
  * @param $db is the ADODB object
  */
  function BaseActivity(&$db)
  {
    $this->type='base';
    $this->child_name = 'BaseActivity';
    parent::Base($db);
  }

  /*!
  * Factory method returning an activity of the desired type
  * loading the information from the database and populating the activity object
  * with datas related to his activity type (being more than a BaseActivity then.
  * @param $activityId : it is the id of the wanted activity
  * @param $with_roles : true by default, gives you the basic roles information in the result
  * @param $with_agents : false by default, gives you the basic agents information in the result
  * @param $as_array : boolean false by default, if true the function will return an array instead of an object
  * @return an Activity Object of the right class (Child class) or an associative array containing the activity
  * information if $as_array is set to true
  */
  function &getActivity($activityId, $with_roles= true,$with_agents=false,$as_array=false)
  {
    $query = "select * from `".GALAXIA_TABLE_PREFIX."activities` where `wf_activity_id`=?";
    $result = $this->query($query,array($activityId));
    if(!$result || !$result->numRows() ) return false;
    $res = $result->fetchRow();
    switch($res['wf_type']) {
      case 'start':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'Start.php');
        $act = new Start($this->db);
        break;
      case 'end':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'End.php');
        $act = new End($this->db);
        break;
      case 'join':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'Join.php');
        $act = new Join($this->db);
        break;
      case 'split':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'Split.php');
        $act = new Split($this->db);
        break;
      case 'standalone':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'Standalone.php');
        $act = new Standalone($this->db);
        break;
      case 'view':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'View.php');
        $act = new View($this->db);
        break;
      case 'switch':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'SwitchActivity.php');
        $act = new SwitchActivity($this->db);
        break;
      case 'activity':
        require_once(GALAXIA_LIBRARY.'/'.'src'.'/'.'API'.'/'.'activities'.'/'.'Activity.php');
        $act = new Activity($this->db);
        break;
      default:
        trigger_error('Unknown activity type:'.$res['wf_type'],E_USER_WARNING);
    }

    $act->setName($res['wf_name']);
    $act->setProcessId($res['wf_p_id']);
    $act->setNormalizedName($res['wf_normalized_name']);
    $act->setDescription($res['wf_description']);
    $act->setIsInteractive($res['wf_is_interactive']);
    $act->setIsAutoRouted($res['wf_is_autorouted']);
    $act->setActivityId($res['wf_activity_id']);
    $act->setType($res['wf_type']);
    $act->setDefaultUser($res['wf_default_user']);

    //Now get forward transitions

    //Now get backward transitions

    //Now get roles
    if ($with_roles)
    {
      $query = "select `wf_role_id` from `".GALAXIA_TABLE_PREFIX."activity_roles` where `wf_activity_id`=?";
      $result=$this->query($query,array($activityId));
      if (!(empty($result)))
      {
        while($res = $result->fetchRow())
        {
          $this->roles[] = $res['wf_role_id'];
        }
      }
      $act->setRoles($this->roles);
    }

    //Now get agents if asked so
    if ($with_agents)
    {
      $query = "select wf_agent_id, wf_agent_type from ".GALAXIA_TABLE_PREFIX."activity_agents where wf_activity_id=?";
      $result=$this->query($query,array($activityId));
      if (!(empty($result)))
      {
        while($res = $result->fetchRow())
        {
          $this->agents[] = array(
              'wf_agent_id'	=> $res['wf_agent_id'],
              'wf_agent_type'	=> $res['wf_agent_type'],
            );
        }
      }
      $act->setAgents($this->agents);
    }

    if ($as_array)
    {//we wont return the object but an associative array instead
       $res['wf_name']=$act->getName();
       $res['wf_normalized_name']=$act->getNormalizedName();
       $res['wf_description']=$act->getDescription();
       $res['wf_is_interactive']=$act->isInteractive();
       $res['wf_is_autorouted']=$act->isAutoRouted();
       $res['wf_roles']=$act->getRoles();
       //$res['outbound']=$act->get();
       //$res['inbound']=$act->get();
       $res['wf_p_id']=$act->getProcessId();
       $res['wf_activity_id']=$act->getActivityId();
       $res['wf_type']=$act->getType();
       $res['wf_default_user']=$act->getDefaultUser();
       $res['wf_agents']= $act->getAgents();
       return $res;
    }
    else
    {
      return $act;
    }
  }

  /*! @return an Array of roleIds for the given user */
  function getUserRoles($user) {

    // retrieve user_groups information in an array containing all groups for this user
    $user_groups = galaxia_retrieve_user_groups($GLOBALS['phpgw_info']['user']['account_id'] );
    // and append it to query
    $query = 'select `wf_role_id` from `'.GALAXIA_TABLE_PREFIX."user_roles`
          where (
            (wf_user=? and wf_account_type='u')";
    if (is_array($groups))
    {
      $mid .= '	or (wf_user in ('.implode(',',$groups).") and wf_account_type='g')";
    }
    $mid .= ')';

    $result=$this->query($query,array($user));
    $ret = Array();
    while($res = $result->fetchRow())
    {
      $ret[] = $res['wf_role_id'];
    }
    return $ret;
  }

  //! Returns an Array of associative arrays with roleId and names
  function getActivityRoleNames() {
    $aid = $this->activityId;
    $query = "select gr.`wf_role_id`, `wf_name` from `".GALAXIA_TABLE_PREFIX."activity_roles` gar, `".GALAXIA_TABLE_PREFIX."roles` gr where gar.`wf_role_id`=gr.`wf_role_id` and gar.`wf_activity_id`=?";
    $result=$this->query($query,array($aid));
    $ret = Array();
    while($res = $result->fetchRow()) {
      $ret[] = $res;
    }
    return $ret;
  }

  /*! Returns the normalized name for the activity */
  function getNormalizedName() {
    return $this->normalizedName;
  }

  /*! Sets normalized name for the activity */
  function setNormalizedName($name) {
    $this->normalizedName=$name;
  }

  /*! Sets the name for the activity */
  function setName($name) {
    $this->name=$name;
  }

  /*! Gets the activity name */
  function getName() {
    return $this->name;
  }

  /*!
  * Sets the agents for the activity object (no save)
  * @param $agents is an associative array with ['wf_agent_id'] and ['wf_agent_type'] keys
  * @return false if any problem is detected
  */
  function setAgents($agents)
  {
    if (!(is_array($agents)))
    {
      $this->error[] = tra('bad parameter for setAgents, the parameter should be an array');
      return false;
    }
    $this->agents = $agents;
  }

  /*!
  * Gets the activity agents
  * @return an associative array with the basic agents informations (id an type) or false
  * if no agent is defined for this activity
  */
  function getAgents()
  {
    if (empty($this->agents)) return false;
    return $this->agents;
  }

  /*! Sets the activity description */
  function setDescription($desc) {
    $this->description=$desc;
  }

  /*! Gets the activity description */
  function getDescription() {
    return $this->description;
  }

  /*! Sets the type for the activity - this does NOT allow you to change the actual type */
  function setType($type) {
    $this->type=$type;
  }

  /*! Gets the activity type */
  function getType() {
    return $this->type;
  }

  /*! Sets if the activity is interactive */
  function setIsInteractive($is) {
    $this->isInteractive=$is;
  }

  /*! Returns if the activity is interactive */
  function isInteractive() {
    return $this->isInteractive == 'y';
  }

  /*! Sets if the activity is auto-routed */
  function setIsAutoRouted($is) {
    $this->isAutoRouted = $is;
  }

  /*! Gets if the activity is auto routed */
  function isAutoRouted() {
    return $this->isAutoRouted == 'y';
  }

  /*! Sets the processId for this activity */
  function setProcessId($pid) {
    $this->pId=$pid;
  }

  /*! Gets the processId for this activity*/
  function getProcessId() {
    return $this->pId;
  }

  /*! Gets the activityId */
  function getActivityId() {
    return $this->activityId;
  }

  /*! Sets the activityId */
  function setActivityId($id) {
    $this->activityId=$id;
  }

  /*! Gets array with roleIds asociated to this activity */
  function getRoles() {
    return $this->roles;
  }

  /*! Sets roles for this activities, should receive an
  array of roleIds */
  function setRoles($roles) {
    $this->roles = $roles;
  }

  /*! Gets default user id associated with this activity as he's recorded
  there's no check about validity of this user.
  */
  function getDefaultUser() {
    return $this->defaultUser;
  }

  /*! Sets the default user for an activity */
  function setDefaultUser($default_user)
  {
    if ((!isset($default_user)) || ($default_user=='') || ($default_user==false))
    {
      $default_user='*';
    }
    $this->defaultUser = $default_user;
  }

  //! DEPRECATED: unused function. old API, do not use it. return always false
  /*!
  * Checks if a user has a certain role (by name) for this activity,
  *    e.g. $isadmin = $activity->checkUserRole($user,'admin');
  * @deprecated
  */
  function checkUserRole($user,$rolename)
  {
    $this->error[] = 'use of an old deprecated function checkUserRole, return always false';
    return false;
  }

}
?>