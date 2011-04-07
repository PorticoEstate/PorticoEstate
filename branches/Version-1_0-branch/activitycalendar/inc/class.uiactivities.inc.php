<?php
phpgw::import_class('activitycalendar.uicommon');
phpgw::import_class('activitycalendar.soactivity');
phpgw::import_class('activitycalendar.soarena');
phpgw::import_class('activitycalendar.soorganization');

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
		/*public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			//$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'booking_manual';
			//self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uidashboard.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'status',
							'label' => lang('Status')
						),
						array(
							'key' => 'type',
							'label' => lang('Type')
						),
						array(
							'key' => 'created',
							'label' => lang('Created')
						),
						array(
							'key' => 'modified',
							'label' => lang('Last modified')
						),
						array(
							'key' => 'what',
							'label' => lang('What')
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity')
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact')
						),
						array(
							'key' => 'case_officer_name',
							'label' => lang('Case Officer')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			self::render_template('datatable', $data);
		}*/
	
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
		
		
		// Retrieve the arena object or create a new one
		if(isset($activity_id) && $activity_id > 0)
		{	
			$arena = activitycalendar_soactivity::get_instance()->get_single($activity_id); 
		}
		else
		{
			$activity = new activitycalendar_activity();
		}
		
		$arenas = activitycalendar_soarena::get_instance()->get(null, null, null, null, null, null, null);
		$organizations = activitycalendar_soorganization::get_instance()->get(null, null, null, null, null, null, null);

		if(isset($_POST['save_activity'])) // The user has pressed the save button
		{
			if(isset($activity)) // If a arena object is created
			{
				// ... set all parameters
				$activity->set_internal_arena_id(phpgw::get_var('internal_arena_id'));
				$activity->set_arena_name(phpgw::get_var('arena_name'));
				$activity->set_address(phpgw::get_var('address'));
				
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
				'arenas' => $arenas,
				'editable' => true,
				'message' => isset($message) ? $message : phpgw::get_var('message'),
				'error' => isset($error) ? $error : phpgw::get_var('error')
			)	
		);
	}
	
	public function query()
	{
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
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
			case 'all_billings':
				$filters = array();
				if($sort_field == 'responsibility_title'){
					$sort_field = 'location_id';
				}
				$result_objects = rental_sobilling::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_sobilling::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			case 'invoices':
				if($sort_field == 'term_label'){
					$sort_field = 'term_id';
				}
				$filters = array('billing_id' => phpgw::get_var('billing_id'));
				$result_objects = rental_soinvoice::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_soinvoice::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}
		
		//Create an empty row set
		$rows = array();
		foreach($result_objects as $result) {
			if(isset($result))
			{
				if($result->has_permission(PHPGW_ACL_READ))
				{
					// ... add a serialized result
					$rows[] = $result->serialize();
				}
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
    
    public function download_export()
    {
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
    	//$browser = CreateObject('phpgwapi.browser');
		//$browser->content_header('export.txt','text/plain');
		
		$stop = phpgw::get_var('date');
		
		$cs15 = phpgw::get_var('generate_cs15');
		if($cs15 == null){
			$export_format = explode('_',phpgw::get_var('export_format'));
			$file_ending = $export_format[1];
			if($file_ending == 'gl07')
			{
				$type = 'intern';
			}
			else if($file_ending == 'lg04')
			{
				$type = 'faktura';
			}
			$date = date('Ymd', $stop);
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=PE_{$type}_{$date}.{$file_ending}");
			
			$id = phpgw::get_var('id');
			$path = "/rental/billings/{$id}";
			
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;
			
			print $vfs->read
			(
				array
				(
					'string' => $path,
					RELATIVE_NONE
				)
			);
			
			//print rental_sobilling::get_instance()->get_export_data((int)phpgw::get_var('id'));
		}
		else{
			$file_ending = 'cs15';
			$type = 'kundefil';
			$date = date('Ymd', $stop);
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=PE_{$type}_{$date}.{$file_ending}");
			print rental_sobilling::get_instance()->generate_customer_export((int)phpgw::get_var('id'));
		}
    }

}
?>
