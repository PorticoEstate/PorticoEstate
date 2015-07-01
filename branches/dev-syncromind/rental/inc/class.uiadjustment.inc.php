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
		'view'					=> true,
		'show_affected_contracts' =>	true,
		'delete'				=> true,
		'run_adjustments'		=> true
	);
	
	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('rental::contracts::adjustment');
		$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('adjustment');
	}
	
	private function _get_filters()
	{
		$filters = array();

		$types = rental_socontract::get_instance()->get_fields_of_responsibility();
		$party_types = array();
		foreach($types as $id => $label)
		{
			$names = $this->locations->get_name($id);
			if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
			{
				if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
				{
					$party_types[] = array('id' => $id, 'name' =>lang($label));
				}
			}
		}
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'responsibility_id',
						'text'   => lang('new_adjustment'),
						'list'   => $party_types
					);
		
		return $filters;
	}
	
	
	public function index()
	{
		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}
			
		$appname = lang('adjustment_list');
		$type = 'non_manual_adjustments';

		$data = array(
			'datatable_name'	=> $appname,
			'form' => array(
				'toolbar' => array(
					'item' => array(
						array(
							'type'   => 'link',
							'value'  => lang('new'),
							'onclick'=> 'onNew_adjustment()',
							'class'  => 'new_item'
						)						
					)
				)
			),
			'datatable' => array(
				'source'	=> self::link(array(
					'menuaction'	=> 'rental.uiadjustment.index', 
					'type'			=> $type,
					'phpgw_return_as' => 'json'
				)),
				'allrows'	=> true,
				'editor_action' => '',
				'field' => array(
					array(
						'key'		=> 'year', 
						'label'		=> lang('year'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'adjustment_date', 
						'label'		=> lang('adjustment_date'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'price_item_id', 
						'label'		=> lang('price_item'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> true
					),
					array(
						'key'		=> 'new_price', 
						'label'		=> lang('new_price'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> true
					),
					array(
						'key'		=> 'adjustment_type', 
						'label'		=> lang('adjustment_type'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'percent', 
						'label'		=> lang('percent'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'interval', 
						'label'		=> lang('interval'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'responsibility_title', 
						'label'		=> lang('responsibility'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'extra_adjustment', 
						'label'		=> lang('extra_adjustment'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'is_executed', 
						'label'		=> lang('is_executed'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					)
				)
			)
		);
				
		$filters = $this->_get_Filters();
		krsort($filters);
		foreach($filters as $filter){
			array_unshift($data['form']['toolbar']['item'], $filter);
		}
			
		$parameters = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'id',
						'source'	=> 'id'
					),
				)
			);
		
		$data['datatable']['actions'][] = array
			(
				'my_name'		=> 'view',
				'text' 			=> lang('show'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'rental.uiadjustment.show_affected_contracts'
				)),
				'parameters'	=> json_encode($parameters)
			);
					
		$data['datatable']['actions'][] = array
			(
				'my_name'		=> 'edit',
				'text' 			=> lang('edit'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'rental.uiadjustment.edit'
				)),
				'parameters'	=> json_encode($parameters)
			);
		
		$data['datatable']['actions'][] = array
			(
				'my_name'		=> 'delete',
				'text' 			=> lang('delete'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'rental.uiadjustment.delete'
				)),
				'parameters'	=> json_encode($parameters)
			);
		
		self::add_javascript('rental', 'rental', 'adjustment.index.js');
		self::render_template_xsl('datatable_jquery', $data);
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
		
		$order			= phpgw::get_var('order');
		$draw			= phpgw::get_var('draw', 'int');
		$columns		= phpgw::get_var('columns');

		$start_index	= phpgw::get_var('start', 'int', 'REQUEST', 0);
		$num_of_objects	= (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
		$sort_field		= ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'year'; 
		$sort_ascending	= ($order[0]['dir'] == 'desc') ? false : true;
		// Form variables
		$search_for 	= '';
		$search_type	= '';
		
		if($sort_field == 'responsibility_title'){
			$sort_field = "responsibility_id";
		}

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
		//array_walk($rows, array($this, 'add_actions'), array($type));

		$result_data    =   array('results' =>  $rows);
		$result_data['total_records']	= $result_count;
		$result_data['draw']    = $draw;

		return $this->jquery_results($result_data);
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
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiadjustment.show_affected_contracts', 'id' => $value['id'])));
				$value['labels'][] = lang('show');
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiadjustment.edit', 'id' => $value['id'])));
				$value['labels'][] = lang('edit');
				/*$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiadjustment.run_adjustments')));
				$value['labels'][] = lang('execute_adjustments');*/
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiadjustment.delete', 'id' => $value['id'])));
				$value['labels'][] = lang('delete');
				//$value['ajax'][] = false;
				//$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiadjustment.show_affected_contracts', 'id' => $value['id'])));
				//$value['labels'][] = lang('show_affected_contracts');
				
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
				$adjustment->set_year(phpgw::get_var('adjustment_year'));
				$adjustment->set_adjustment_date($adjustment_date);
				$adjustment->set_price_item_id(0);
				if(isset($responsibility_id) && $responsibility_id > 0)
				{
					$adjustment->set_responsibility_id($responsibility_id); // only present when new contract
				}

				$adjustment->set_new_price(0);
				$adjustment->set_percent(phpgw::get_var('percent'));
				$adjustment->set_interval(phpgw::get_var('interval'));
				$adjustment->set_adjustment_type(phpgw::get_var('adjustment_type'));
                                $adjustment->set_extra_adjustment(phpgw::get_var('extra_adjustment') == 'on' ? true : false);
				
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
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiadjustment.edit', 'id' => $adjustment->get_id(), 'message' => $message, 'error' => $error));
		}
			
		$current_adjustment_type = $adjustment->get_adjustment_type();
		$adjustment_type_options[] = array('id'=>'adjustment_type_KPI', 'name'=>'adjustment_type_KPI', 'selected'=>(($current_adjustment_type == 'adjustment_type_KPI') ? 1 : 0));
		$adjustment_type_options[] = array('id'=>'adjustment_type_deflator', 'name'=>'adjustment_type_deflator', 'selected'=>(($current_adjustment_type == 'adjustment_type_deflator') ? 1 : 0));
			
		$current_interval = $adjustment->get_interval();
		$interval_options[] = array('id'=>'1', 'name'=>'1 '.lang('year'), 'selected'=>(($current_interval == '1') ? 1 : 0));
		$interval_options[] = array('id'=>'2', 'name'=>'2 '.lang('year'), 'selected'=>(($current_interval == '2') ? 1 : 0));
		$interval_options[] = array('id'=>'10', 'name'=>'10 '.lang('year'), 'selected'=>(($current_interval == '10') ? 1 : 0));
		
		$adjustment_year = $adjustment->get_year();
		$years = rental_contract::get_year_range();
		$years_options = array();
		foreach($years as $year)
		{
			$years_options[] = array('id'=>$year, 'name'=>$year, 'selected'=>(($adjustment_year == $year) ? 1 : 0));
		}					
		
		$link_index = array
			(
				'menuaction'	=> 'rental.uiadjustment.index',
				'populate_form'	=> 'yes'
			);
			
		$tabs = array();
		$tabs['details']	= array('label' => lang('Details'), 'link' => '#details');
		$active_tab = 'details';
		
		$data = array
		(
			'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, $active_tab),		
			'form_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'rental.uiadjustment.edit')),
			'cancel_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_index),
			'lang_save'						=> lang('save'),
			'lang_cancel'					=> lang('cancel'),			
			'editable'						=> true,
			
			'lang_field_of_responsibility'	=> lang('field_of_responsibility'),
			'lang_adjustment_type'			=> lang('adjustment_type'),
			'lang_percent'					=> lang('percent'),
			'lang_interval'					=> lang('interval'),
			'lang_year'						=> lang('year'),
			
			'value_field_of_responsibility'	=> lang(rental_socontract::get_instance()->get_responsibility_title($adjustment->get_responsibility_id())),
			'list_adjustment_type'			=> array('options' => $adjustment_type_options),
			'value_percent'					=> $adjustment->get_percent(),
			'list_interval'					=> array('options' => $interval_options),
			'list_years'					=> array('options' => $years_options),
					
			'validator'						=> phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'))
		);
		
		self::render_template_xsl(array('adjustment'), array('edit' => $data));
		
		//return $this->viewedit(true, $adjustment_id, null, $responsibility_id, $message, $error);
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
	
	public function delete()
	{
		
		$adjustment_id = (int)phpgw::get_var('id');
		$result = rental_soadjustment::get_instance()->delete($adjustment_id);
		if($result)
		{
			$this->render('adjustment_list.php', array('error' => lang('adjustment_not_deleted')));
		}
		else
		{
			$this->render('adjustment_list.php', array('message' => lang('adjustment_deleted')));	
		}
	}
	
	public function show_affected_contracts()
	{
		$adjustment_id = (int)phpgw::get_var('id');
		$adjustment = rental_soadjustment::get_instance()->get_single($adjustment_id);

		//if there exist another regulation that has been exectued after current regulation with the same filters, the affected list will be out of date.
		$show_affected_list = true;
		if($adjustment->is_executed()){
			if(rental_soadjustment::get_instance()->newer_executed_regulation_exists($adjustment))$show_affected_list = false;
		}

		$this->render('contracts_for_regulation_list.php', array('adjustment_id' => $adjustment_id, 
																	'adjustment' => $adjustment,
																	'cancel_link' => self::link(array('menuaction' => 'rental.uiadjustment.index')),
																	'show_affected_list' => $show_affected_list));
	}
	
	public function run_adjustments()
	{
		rental_soadjustment::get_instance()->run_adjustments();
	}
}
?>
