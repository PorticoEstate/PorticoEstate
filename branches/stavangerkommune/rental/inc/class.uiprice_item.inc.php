<?php
phpgw::import_class('rental.uicommon');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.soprice_item');
phpgw::import_class('rental.socontract_price_item');
phpgw::import_class('rental.socontract');
phpgw::import_class('rental.soadjustment');

include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract_price_item', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'adjustment', 'inc/model/');

class rental_uiprice_item extends rental_uicommon
{
	public $public_functions = array
	(
			'add' => true,
			'index' => true,
			'query' => true,
			'view'		=> true,
			'edit'		=> true,
			'set_value' => true,
			'manual_adjustment' => true,
			'adjust_price' => true
	);

	public function __construct()
	{
		parent::__construct();
		//self::set_active_menu('admin::rental::contract_type_list');
		self::set_active_menu('rental::contracts::price_item_list');
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('price_list');
	}

	public function index()
	{
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
		$this->render('admin_price_item_list.php');
	}

	/*
	 * View the price item with the id given in the http variable 'id'
	 */
	public function view()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');

		if(!self::isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
		$id = (int)phpgw::get_var('id');
		$price_item = rental_price_item::get($id);
		return $this->viewedit(false, $price_item);
	}

	/*
	 * Edit the price item with the id given in the http variable 'id'
	 */
	public function edit()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
		if(!self::isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}

		$id = (int)phpgw::get_var('id');
		$price_item = rental_price_item::get($id);

		// Save the price item if it was posted
		if(isset($_POST['save']))
		{
			$price_item->set_title(phpgw::get_var('title'));
			$price_item->set_agresso_id(phpgw::get_var('agresso_id'));
			$price_item->set_is_area(phpgw::get_var('is_area') == 'true' ? true : false);
			$price_item->set_is_inactive(phpgw::get_var('is_inactive') == 'on' ? true : false);
			$price_item->set_is_adjustable(phpgw::get_var('is_adjustable') == 'on' ? true : false);
			$price_item->set_standard(phpgw::get_var('standard') == 'on' ? true : false);
			$price_item->set_price(phpgw::get_var('price'));
			if($price_item->get_agresso_id() == null)
			{
				return $this->viewedit(true, $price_item, '', lang('missing_agresso_id'));
			}
			else
			{
				if (rental_soprice_item::get_instance()->store($price_item)) {
					return $this->viewedit(true, $price_item, lang('messages_saved_form'));
				} else {
					return $this->viewedit(true, $price_item, '', lang('messages_form_error'));
				}
			}
		}

		return $this->viewedit(true, $price_item);
	}

	/*
	 * Add a new price item to the database.  Requires only a title.
	 */
	public function add()
	{
		if(!self::isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
			
		$title = phpgw::get_var('price_item_title');
		$responsibility_id = phpgw::get_var('responsibility_id');
		if ($title) {
			$price_item = new rental_price_item();
			$price_item->set_title($title);
			$price_item->set_responsibility_id($responsibility_id);
			if (rental_soprice_item::get_instance()->store($price_item)) {
				// The object was stored, forward to edit it further
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiprice_item.edit', 'id' => $price_item->get_id()));
			}
		}

		return $this->index();
	}

	public function set_value()
	{
		if(!self::isExecutiveOfficer())
		{
			return;
		}

		$field = phpgw::get_var('field');
		$value = phpgw::get_var('value');
		$id = phpgw::get_var('id');

		$price_item = rental_socontract_price_item::get_instance()->get_single($id);
		$price_item->set_field($field, $value);	
		rental_socontract_price_item::get_instance()->store($price_item);

	}

	/**
	 * View or edit rental price_item
	 *
	 * @param $editable true renders fields editable, false renders fields disabled
	 * @param $price_item the price item to display
	 */
	protected function viewedit($editable, $price_item, $message = '', $error = '')
	{
		$data = array
		(
				'price_item' 	=> $price_item,
				'editable' => $editable,
				'message' => $message,
				'error' => $error,
				'cancel_link' => self::link(array('menuaction' => 'rental.uiprice_item.index'))
		);
		$this->render('admin_price_item.php', $data);
	}
	/**
	 * (non-PHPdoc)
	 * @see rental/inc/rental_uicommon#query()
	 */
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
		if($sort_field == null)
		{
			$sort_field = 'agresso_id';
		}
		$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
		//Create an empty result set
		$records = array();
		
		//Retrieve a contract identifier and load corresponding contract
		$contract_id = phpgw::get_var('contract_id');
		if(isset($contract_id))
		{
			$contract = rental_socontract::get_instance()->get_single($contract_id);
		}
		
		//Retrieve the type of query and perform type specific logic
		$type = phpgw::get_var('type');
		switch($type)
		{
			case 'included_price_items':
				if(isset($contract))
				{
					$filters = array('contract_id' => $contract->get_id());
					$result_objects = rental_socontract_price_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socontract_price_item::get_instance()->get_count($search_for, $search_type, $filters);
				}
				break;
			case 'not_included_price_items': // We want to show price items in the source list even after they've been added to a contract
				$filters = array('price_item_status' => 'active','responsibility_id' => phpgw::get_var('responsibility_id'));
				$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			case 'manual_adjustment':
				$filters = array('price_item_status' => 'active','is_adjustable' => 'false');
				$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
				break;
			default:
				//$filters = array('price_item_status' => 'active','responsibility_id' => phpgw::get_var('responsibility_id'));
				$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
				$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
				break;
		}

		// Create an empty row set
		$rows = array();
		foreach ($result_objects as $record) {
			if(isset($record))
			{
				// ... add a serialized record
				$rows[] = $record->serialize();
			}
		}
		$data = array('results' => $rows, 'total_records' => $object_count);

		$editable = phpgw::get_var('editable') == 'true' ? true : false;

		//Add action column to each row in result table
		array_walk(
			$data['results'], 
			array($this, 'add_actions'), 
			array(
				$contract_id,
				$type,
				$editable
			)
		);
		return $this->yui_results($data, 'total_records', 'results');
	}

	/**
	 * Add action links and labels for the context menu of the list items
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [price_item.id, type of query, editable]
	 */
	public function add_actions(&$value, $key, $params)
	{

		$value['actions'] = array();
		$value['labels'] = array();

		// Get parameters
		$contract_id = $params[0];
		$type = $params[1];
		$editable = $params[2];

		// Depending on the type of query: set an ajax flag and define the action and label for each row
		switch($type)
		{
			case 'included_price_items':
				if($editable == true)
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_price_item', 'price_item_id' => $value['id'], 'contract_id' => $contract_id)));
					$value['labels'][] = lang('remove');

					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.reset_price_item', 'price_item_id' => $value['id'], 'contract_id' => $contract_id)));
					$value['labels'][] = lang('reset');
				}
				break;
			case 'not_included_price_items':
				if($editable == true)
				{
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_price_item', 'price_item_id' => $value['id'], 'contract_id' => $contract_id)));
					$value['labels'][] = lang('add');
				}
				break;
			default:
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.view', 'id' => $value['id'])));
				$value['labels'][] = lang('show');

				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.edit', 'id' => $value['id'])));
				$value['labels'][] = lang('edit');
		}
	}
	
	public function manual_adjustment()
	{
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('manual_adjustment');
		if(!$this->isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
		self::set_active_menu('rental::contracts::price_item_list::manual_adjustment');		
		$this->render('admin_price_item_manual_adjustment.php');
	}
	
	public function adjust_price()
	{
		if(!self::isExecutiveOfficer())
		{
			$this->render('permission_denied.php');
			return;
		}
		$id = (int)phpgw::get_var('price_item_id');
		$new_price = phpgw::get_var('new_price');
		$new_price = str_replace(',','.',$new_price);
		
		if($new_price != null && is_numeric($new_price)){
			$price_item = rental_price_item::get($id);
			$price_item->set_price($new_price);
			if (rental_soprice_item::get_instance()->store($price_item)) {
				$adjustment = new rental_adjustment();
				$adjustment->set_price_item_id($price_item->get_id());
				$adjustment->set_new_price($new_price);
				$adjustment->set_percent(0);
				$adjustment->set_responsibility_id($price_item->get_responsibility_id());
				$adjustment->set_is_manual(true);
				$adjustment->set_adjustment_date(time());
				rental_soadjustment::get_instance()->store($adjustment);
				$message[] = "Priselement med Agresso id {$price_item->get_agresso_id()} er oppdatert med ny pris {$new_price}";
				//update affected contract_price_items
				$no_of_contracts_updated = rental_soprice_item::get_instance()->adjust_contract_price_items($id, $new_price);
				if($no_of_contracts_updated > 0){
					$message[] = $no_of_contracts_updated .' priselementer p&aring; kontrakter er oppdatert';
				}
				else{
					$message[] = "Ingen kontrakter er oppdatert";
				}
				$data = array
				(
					'price_item_id' => $id,
					'message' => $message
				);
				self::set_active_menu('rental::contracts::price_item_list::manual_adjustment');	
				$this->render('admin_price_item_manual_adjustment.php', $data);
			} else {
				$data = array
				(
					'price_item_id' => $id,
					'error' => $error
				);
				self::set_active_menu('rental::contracts::price_item_list::manual_adjustment');	
				$this->render('admin_price_item_manual_adjustment.php', $data);
			}
		}
		else{
			$data = array
			(
				'price_item_id' => $id,
				'error' => lang('price_not_numeric')
			);
			self::set_active_menu('rental::contracts::price_item_list::manual_adjustment');	
			$this->render('admin_price_item_manual_adjustment.php', $data);
		}
	}
}
