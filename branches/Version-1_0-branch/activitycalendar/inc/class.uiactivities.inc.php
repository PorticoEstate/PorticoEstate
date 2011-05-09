<?php
phpgw::import_class('activitycalendar.uicommon');
phpgw::import_class('activitycalendar.soactivity');
phpgw::import_class('activitycalendar.soarena');
phpgw::import_class('activitycalendar.soorganization');
phpgw::import_class('activitycalendar.sogroup');

include_class('activitycalendar', 'activity', 'inc/model/');

class activitycalendar_uiactivities extends activitycalendar_uicommon
{
	public $public_functions = array
	(
		'index'     		=> true,
		'query'			    => true,
		'view'			    => true,
		'add'				=> true,
		'edit'				=> true,
		'download'			=> true,
		'download_export'	=> true
	);
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('activitycalendar::activities');
		$config	= CreateObject('phpgwapi.config','activitycalendar');
		$config->read();
	}
	
	/**
	 * Public method. Forwards the user to edit mode.
	 */
	public function add()
	{
		$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiactivities.edit'));
	}
	
	public function index()
	{
		$this->render('activity_list.php');
	}
	
	/**
	 * Displays info about one single billing job.
	 */
	public function view()
	{
		$errorMsgs = array();
		$infoMsgs = array();
		$activity = activitycalendar_soactivity::get_instance()->get_single((int)phpgw::get_var('id'));
		
		if($activity == null) // Not found
		{
			$errorMsgs[] = lang('Could not find specified activity.');
		}

		$data = array
		(
			'activity' => $activity,
			'errorMsgs' => $errorMsgs,
			'infoMsgs' => $infoMsgs
		);
		$this->render('activity.php', $data);
	}
	
	public function edit()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
		// Get the contract part id
		$activity_id = (int)phpgw::get_var('id');
		//var_dump($activity_id);
		
		$categories = activitycalendar_soactivity::get_instance()->get_categories();
		$targets = activitycalendar_soactivity::get_instance()->get_targets();
		$districts = activitycalendar_soactivity::get_instance()->select_district_list();
				
		// Retrieve the arena object or create a new one
		if(isset($activity_id) && $activity_id > 0)
		{	
			$activity = activitycalendar_soactivity::get_instance()->get_single($activity_id); 
		}
		else
		{
			$activity = new activitycalendar_activity();
		}
		$g_id = phpgw::get_var('group_id');
		$o_id = phpgw::get_var('organization_id');
		if(isset($g_id) && $g_id > 0)
		{
			$persons = activitycalendar_sogroup::get_instance()->get_contacts($g_id);
		}
		else if(isset($o_id) && $o_id > 0)
		{
			$persons = activitycalendar_soorganization::get_instance()->get_contacts($o_id);
		}
		$arenas = activitycalendar_soarena::get_instance()->get(null, null, null, null, null, null, null);
		$organizations = activitycalendar_soorganization::get_instance()->get(null, null, null, null, null, null, null);
		$groups = activitycalendar_sogroup::get_instance()->get(null, null, null, null, null, null, null);

		if(isset($_POST['save_activity'])) // The user has pressed the save button
		{
			if(isset($activity)) // If a activity object is created
			{
				// ... set all parameters
				$activity->set_title(phpgw::get_var('title'));
				$activity->set_organization_id(phpgw::get_var('organization_id'));
				$activity->set_group_id(phpgw::get_var('group_id'));
				$activity->set_arena(phpgw::get_var('arena_id'));
				$activity->set_district(phpgw::get_var('district'));
				$activity->set_state(phpgw::get_var('state'));
				$activity->set_category(phpgw::get_var('category'));
				$target_array = phpgw::get_var('target');
				$activity->set_target(implode(",", $target_array));
				$activity->set_description(phpgw::get_var('description'));
				$activity->set_time(phpgw::get_var('time'));
				$activity->set_contact_persons($persons);
				$activity->set_special_adaptation(phpgw::get_var('special_adaptation'));
				
				if(activitycalendar_soactivity::get_instance()->store($activity)) // ... and then try to store the object
				{
					$message = lang('messages_saved_form');	
				}
				else
				{
					$error = lang('messages_form_error');
				}
			}
		}

		return $this->render('activity.php', array
			(
				'activity' 	=> $activity,
				'organizations' => $organizations,
				'groups' => $groups,
				'arenas' => $arenas,
				'categories' => $categories,
				'targets' => $targets,
				'districts' => $districts,
				'editable' => true,
				'message' => isset($message) ? $message : phpgw::get_var('message'),
				'error' => isset($error) ? $error : phpgw::get_var('error')
			)	
		);
	}
	
	public function query()
	{
		if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
		{
			$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		}
		else {
			$user_rows_per_page = 10;
		}
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
		$sort_field		= phpgw::get_var('sort');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		//Retrieve the type of query and perform type specific logic
		$query_type = phpgw::get_var('type');
		
		$exp_param 	= phpgw::get_var('export');
		$export = false;
		if(isset($exp_param)){
			$export=true;
			$num_of_objects = null;
		}
		
		switch($query_type)
		{
			case 'all_activities':
			default:
				$filters = array('activity_state' => phpgw::get_var('activity_state'), 'activity_district' => phpgw::get_var('activity_district'));
				$result_objects = activitycalendar_soactivity::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = activitycalendar_soactivity::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}
		
		//Create an empty row set
		$rows = array();
		foreach($result_objects as $result) {
			//var_dump($result);
			if(isset($result))
			{
				// ... add a serialized result
				$rows[] = $result->serialize();
			}
		}
		
		// ... add result data
		$result_data = array('results' => $rows, 'total_records' => $object_count);
		
		if(!$export){
			//Add action column to each row in result table
			array_walk($result_data['results'], array($this, 'add_actions'), array($query_type));
		}

		return $this->yui_results($result_data, 'total_records', 'results');
	}
		
	/**
	 * Add action links and labels for the context menu of the list items
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [composite_id, type of query, editable]
	 */
	public function add_actions(&$value, $key, $params)
	{
		//Defining new columns
		$value['ajax'] = array();
		$value['actions'] = array();
		$value['labels'] = array();

		$query_type = $params[0];
		
		switch($query_type)
		{
			case 'all_activities':
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiactivities.edit', 'id' => $value['id'])));
				$value['labels'][] = lang('edit');
				break;
		}
    }
}
?>
