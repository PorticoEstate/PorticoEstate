<?php
phpgw::import_class('rental.uicommon');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.soadjustment');

class rental_uiadjustment extends rental_uicommon {
	
	public $public_functions = array
	(
		'index'					=> true,
		'add'					=> true,
		'query'					=> true,
		'edit'					=> true,
		'view'					=> true
	);
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('rental::contracts::adjustment');
	}
	
	public function index()
	{
		$this->render('adjustment_list.php');
	}
	
	public function query()
	{
		// YUI variables for paging and sorting
		$start_index	= phpgw::get_var('startIndex', 'int');
		$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 10);
		$sort_field		= phpgw::get_var('sort');
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		// Form variables
		$search_for 	= phpgw::get_var('query');
		$search_type	= phpgw::get_var('search_option');
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		
		$type = phpgw::get_var('type');
		switch($type)
		{
			case 'manual_adjustments':
				$filters = array('manual_adjustment' => 'true');
				break;
			case 'non_manual_adjustments':
			default:
				$filters = array('non_manual_adjustment' => 'true');
		}

		$result_objects = rental_soadjustment::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
		$result_count = rental_soadjustment::get_instance()->get_count($search_for, $search_type, $filters);
			
		//Serialize the contracts found
		$rows = array();
		foreach ($result_objects as $result) {
			if(isset($result))
			{
				$rows[] = $result->serialize();
			}
		}
		
		//Add context menu columns (actions and labels)
		array_walk($rows, array($this, 'add_actions'), array($type));

		//Build a YUI result from the data
		$result_data = array('results' => $rows, 'total_records' => $result_count);
		return $this->yui_results($result_data, 'total_records', 'results');
	}
	
	public function add_actions(&$value, $key, $params)
	{
		$value['ajax'] = array();
		$value['actions'] = array();
		$value['labels'] = array();

		$type = $params[0];
		
		switch($type)
		{
			default:
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiadjustment.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');
			}
	}
	
	/**
	 * Create a new empty adjustment
	 */
	public function add()
	{
		$responsibility_id = phpgw::get_var('responsibility_id');
		if(isset($responsibility_id) && $responsibility_id > 0)
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiadjustment.edit', 'responsibility_id' => $responsibility_id));
		}
	}
	
	public function edit()
	{
		$adjustment_id = (int)phpgw::get_var('id');
		$responsibility_id = (int)phpgw::get_var('responsibility_id');
		
		$message = null;
		$error = null;
		
		if(isset($_POST['save']))
		{
			if(isset($adjustment_id) && $adjustment_id > 0)
			{
				$adjustment = rental_soadjustment::get_instance()->get_single($adjustment_id);
				if(!$adjustment->has_permission(PHPGW_ACL_EDIT))
				{
					unset($adjustment);
					$this->render('permission_denied.php',array('error' => lang('permission_denied_edit_adjustment')));
				}
			}
			else
			{
				if(isset($responsibility_id) && ($this->isExecutiveOfficer() || $this->isAdministrator())){
					$adjustment = new rental_adjustment();
					$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
					$adjustment->set_responsibility_id($responsibility_id);
				}
			}
			$adjustment_date =  strtotime(phpgw::get_var('adjustment_date_hidden'));
			
			if(isset($adjustment)){
				$adjustment->set_adjustment_date($adjustment_date);
				$adjustment->set_price_item_id(0);
				if(isset($responsibility_id) && $responsibility_id > 0)
				{
					$adjustment->set_responsibility_id($responsibility_id); // only present when new contract
				}
				/*$new_price=phpgw::get_var('new_price');
				if(!isset($new_price)){
					$new_price = 0;
				}*/
				$adjustment->set_new_price(0);
				$adjustment->set_percent(phpgw::get_var('percent'));
				
				$so_adjustment = rental_soadjustment::get_instance();
				if($so_adjustment->store($adjustment))
				{
						$message = lang('messages_saved_form');
						$adjustment_id = $adjustment->get_id();
				}
				else
				{
					$error = lang('messages_form_error');
				}
			}
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiadjustment.edit', 'id' => $adjustment->get_id(), 'message' => $message, 'error' => $error));
		}
			
		return $this->viewedit(true, $adjustment_id, null, $responsibility_id, $message, $error);
	}
	
	/**
	 * View an adjustment
	 */
	public function view() {
		$adjustment_id = (int)phpgw::get_var('id');
		return $this->viewedit(false, $adjustment_id);
	}
	
	public function viewedit($editable, $adjustment_id, $adjustment = null, $responsibility_id = null, string $message = null, string $error = null)
	{
		
		if (isset($adjustment_id) && $adjustment_id > 0) {
			if($adjustment == null){
				$adjustment = rental_soadjustment::get_instance()->get_single($adjustment_id);
			}
			if ($adjustment) {
				
				if($editable && !$adjustment->has_permission(PHPGW_ACL_EDIT))
				{
					$editable = false;
					$error .= '<br/>'.lang('permission_denied_edit_adjustment');
				}
				
				if(!$editable && !$adjustment->has_permission(PHPGW_ACL_READ))
				{
					$this->render('permission_denied.php',array('error' => lang('permission_denied_view_adjustment')));
					return;
				}
				
				$data = array
				(
					'adjustment' 	=> $adjustment,
					'editable' => $editable,
					'message' => isset($message) ? $message : phpgw::get_var('message'),
					'error' => isset($error) ? $error : phpgw::get_var('error'),
					'cancel_link' => self::link(array('menuaction' => 'rental.uiadjustment.index'))
				);
				$this->render('adjustment.php', $data);
			}
		}
		else
		{
			if($this->isAdministrator() || $this->isExecutiveOfficer()){
				$adjustment = new rental_adjustment();
				$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
				$adjustment->set_responsibility_id($responsibility_id);
				if ($adjustment) {
					$data = array
					(
						'adjustment' => $adjustment,
						'editable' => true,
						'message' => isset($message) ? $message : phpgw::get_var('message'),
						'error' => isset($error) ? $error : phpgw::get_var('error'),
						'cancel_link' => self::link(array('menuaction' => 'rental.uiadjustment.index'))
					);
					$this->render('adjustment.php', $data);
				}
			}
			else
			{
				$this->render('permission_denied.php',array('error' => lang('permission_denied_new_adjustment')));
				return;	
			}
		}
	}
}
?>