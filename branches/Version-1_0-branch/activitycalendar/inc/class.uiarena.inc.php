<?php
phpgw::import_class('activitycalendar.uicommon');
phpgw::import_class('activitycalendar.soarena');

include_class('activitycalendar', 'arena', 'inc/model/');

class activitycalendar_uiarena extends activitycalendar_uicommon
{
	public $public_functions = array
	(
		'index'     		=> true,
		'query'			    => true,
		'view'			    => true,
		'add'				=> true,
		'edit'				=> true,
		'download'			=> true,
		'get_address_search'	=> true
	);
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('activitycalendar::arena');
		$config	= CreateObject('phpgwapi.config','activitycalendar');
		$config->read();
	}
	
	/**
	 * Public method. Forwards the user to edit mode.
	 */
	public function add()
	{
		$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiarena.edit'));
	}
	
	/**
	 * Public method.
	 */
	public function get_address_search()
	{
		$search_string = phpgw::get_var('search');
		//var_dump($search_string);
		return activitycalendar_soarena::get_instance()->get_address($search_string);
	}
	
	public function index()
	{
		// No messages so far
		$errorMsgs = array();
		$warningMsgs = array();
		$infoMsgs = array();

		
		$data = array();
		$this->render('arena_list.php');
	}
	
	/**
	 * Displays info about one single arena.
	 */
	public function view()
	{

		$errorMsgs = array();
		$infoMsgs = array();
		$saved_OK = phpgw::get_var('saved_ok');
		if($saved_OK)
		{
			$message = lang('arena_saved_form');
		}
		$arena = activitycalendar_soarena::get_instance()->get_single((int)phpgw::get_var('id'));
		$cancel_link = self::link(array('menuaction' => 'activitycalendar.uiarena.index'));
		
		if($arena == null) // Not found
		{
			$errorMsgs[] = lang('Could not find specified arena.');
		}
		$data = array
		(
			'arena' => $arena,
			'cancel_link' => $cancel_link,
			'message' => $message,
			'errorMsgs' => $errorMsgs,
			'infoMsgs' => $infoMsgs
		);
		$this->render('arena.php', $data);
	}
	
	public function edit()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
		// Get the contract part id
		$arena_id = (int)phpgw::get_var('id');
		$cancel_link = self::link(array('menuaction' => 'activitycalendar.uiarena.index'));
		
		$buildings = activitycalendar_soarena::get_instance()->get_buildings();
		//var_dump($buildings);
		
		// Retrieve the arena object or create a new one
		if(isset($arena_id) && $arena_id > 0)
		{	
			$arena = activitycalendar_soarena::get_instance()->get_single($arena_id); 
		}
		else
		{
			$arena = new activitycalendar_arena();
		}
		
		if(isset($_POST['save_arena'])) // The user has pressed the save button
		{
			if(isset($arena)) // If a arena object is created
			{
				// ... set all parameters
				$arena->set_internal_arena_id(phpgw::get_var('internal_arena_id'));
				$arena->set_arena_name(phpgw::get_var('arena_name'));
				$arena->set_address(phpgw::get_var('address') . ' ' . phpgw::get_var('address_no'));
				$arena->set_active(phpgw::get_var('arena_active') == 'yes' ? true : false);
				
				if(activitycalendar_soarena::get_instance()->store($arena)) // ... and then try to store the object
				{
					$message = lang('messages_saved_form');	
				}
				else
				{
					$error = lang('messages_form_error');
				}
			}
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'activitycalendar.uiarena.view', 'id' => $arena->get_id(), 'saved_ok' => 'yes'));
		}

		return $this->render('arena.php', array
			(
				'arena' 	=> $arena,
				'buildings' => $buildings,
				'editable' => true,
				'cancel_link' => $cancel_link,
				'message' => isset($message) ? $message : phpgw::get_var('message'),
				'error' => isset($error) ? $error : phpgw::get_var('error')
			)	
		);
	}
	
	public function query()
	{
/*		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
*/
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
		
		//var_dump($query_type);
		
		switch($query_type)
		{
			case 'all_arenas':
				$filters = array('arena_type' => phpgw::get_var('arena_type'), 'active' => phpgw::get_var('active'));
				$result_objects = activitycalendar_soarena::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = activitycalendar_soarena::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}
		//var_dump($result_objects);
		//Create an empty row set
		$rows = array();
		foreach($result_objects as $result) {
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
			case 'all_arenas':
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiarena.edit', 'id' => $value['id'])));
				$value['labels'][] = lang('edit');
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'activitycalendar.uiarena.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');
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
