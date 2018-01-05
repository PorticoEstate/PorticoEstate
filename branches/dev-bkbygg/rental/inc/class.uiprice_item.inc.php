<?php
	phpgw::import_class('rental.uicommon');
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
			'view' => true,
			'edit' => true,
			'save' => true,
			'set_value' => true,
			'manual_adjustment' => true,
			'adjust_price' => true
		);

		public function __construct()
		{
			parent::__construct();
			//self::set_active_menu('admin::rental::contract_type_list');
			self::set_active_menu('rental::contracts::price_item_list');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('price_list');
		}

		public function index()
		{
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			$types_options = array();
			foreach ($types as $id => $label)
			{
				$names = $this->locations->get_name($id);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if ($this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
					{
						$types_options[] = array('id' => $id, 'name' => lang($label));
					}
				}
			}

			$function_msg = lang('price_list');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							/* array
							  (
							  'type'   => 'text',
							  'name'   => 'ctrl_add_price_item_name',
							  'text'   => lang('name')
							  ), */
							array
								(
								'type' => 'filter',
								'name' => 'responsibility_id',
								'text' => lang('t_new_price_item'),
								'list' => $types_options
							),
							array(
								'type' => 'link',
								'value' => lang('new'),
								'onclick' => 'onNew_price_item()',
								'class' => 'new_item'
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uiprice_item.index',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'agresso_id',
							'label' => lang('agresso_id'),
							'className' => '',
							'sortable' => false,
							'hidden' => false
						),
						array(
							'key' => 'title',
							'label' => lang('name'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'is_area',
							'label' => lang('type'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'price',
							'label' => lang('price'),
							'className' => 'right',
							'sortable' => true,
							'hidden' => false,
							'formatter' => 'formatterPrice'
						),
						array(
							'key' => 'is_inactive',
							'label' => lang('status'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'is_adjustable',
							'label' => lang('is_adjustable'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'responsibility_title',
							'label' => lang('responsibility'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'standard',
							'label' => lang('is_standard'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'price_type_title',
							'label' => lang('type'),
							'className' => '',
							'sortable' => false,
							'hidden' => false
						)
					)
				)
			);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uiprice_item.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uiprice_item.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			$code = <<<JS
				var thousandsSeparator = '$this->thousandsSeparator';
				var decimalSeparator = '$this->decimalSeparator';
				var decimalPlaces = '$this->decimalPlaces';
				var currency_suffix = '$this->currency_suffix';
JS;

			$GLOBALS['phpgw']->js->add_code('', $code);

			self::add_javascript('rental', 'rental', 'price_item.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}
		/*
		 * View the price item with the id given in the http variable 'id'
		 */

		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');

			if (!self::isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			$this->edit(array(), 'view');
		}
		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (!self::isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			$responsibility_id = phpgw::get_var('responsibility_id');
			$price_item_id = phpgw::get_var('id', 'int');

			if (!empty($values['price_item_id']))
			{
				$price_item_id = $values['price_item_id'];
			}

			if (!empty($price_item_id))
			{
				$price_item = rental_price_item::get($price_item_id);
			}
			else
			{
				$title = phpgw::get_var('price_item_title');

				$price_item = new rental_price_item();
				$price_item->set_title($title);
				$price_item->set_responsibility_id($responsibility_id);
				$price_item->set_price_type_id(1); // defaults to year
			}

			$responsibility_title = ($price_item->get_responsibility_title()) ? $price_item->get_responsibility_title() : rental_socontract::get_instance()->get_responsibility_title($responsibility_id);

			$link_save = array
				(
				'menuaction' => 'rental.uiprice_item.save'
			);

			$link_index = array
				(
				'menuaction' => 'rental.uiprice_item.index',
			);

			$tabs = array();
			$tabs['showing'] = array('label' => lang('Showing'), 'link' => '#showing');
			$active_tab = 'showing';

			$current_price_type_id = $price_item->get_price_type_id();
			$types_options = array();
			foreach ($price_item->get_price_types() as $price_type_id => $price_type_title)
			{
				$selected = ($current_price_type_id == $price_type_id) ? 1 : 0;
				$types_options[] = array('id' => $price_type_id, 'name' => lang($price_type_title),
					'selected' => $selected);
			}

			$data = array
				(
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_save),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_index),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'lang_current_price_type' => lang($price_item->get_price_type_title()),
				'lang_adjustable_text' => $price_item->get_adjustable_text(),
				'lang_standard_text' => $price_item->get_standard_text(),
				'value_title' => $price_item->get_title(),
				'value_field_of_responsibility' => lang($responsibility_title),
				'value_agresso_id' => $price_item->get_agresso_id(),
				'is_area' => ($price_item->is_area()) ? 1 : 0,
				'list_type' => array('options' => $types_options),
				'value_price' => $price_item->get_price(),
				'value_price_formatted' => number_format($price_item->get_price(), $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator) . ' ' . $this->currency_suffix,
				'has_active_contract' => (rental_soprice_item::get_instance()->has_active_contract($price_item->get_id())) ? 1 : 0,
				'is_inactive' => ($price_item->is_inactive()) ? 1 : 0,
				'is_adjustable' => ($price_item->is_adjustable()) ? 1 : 0,
				'is_standard' => ($price_item->is_standard()) ? 1 : 0,
				'price_item_id' => $price_item->get_id(),
				'responsibility_id' => $responsibility_id,
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date',
					'security', 'file'))
			);

			self::render_template_xsl(array('price_item'), array($mode => $data));
		}
		/*
		 * To be removed
		 * Add a new price item to the database.  Requires only a title.
		 */

		public function add()
		{
			if (!self::isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			$this->edit();
		}

		public function save()
		{
			$price_item_id = phpgw::get_var('id', 'int');

			if (!empty($price_item_id))
			{
				$price_item = rental_price_item::get($price_item_id);
			}
			else
			{
				$title = phpgw::get_var('price_item_title');
				$responsibility_id = phpgw::get_var('responsibility_id');
				$price_item = new rental_price_item();
				$price_item->set_title($title);
				$price_item->set_responsibility_id($responsibility_id);
				$price_item->set_price_type_id(1); // defaults to year
			}
			$price_item->set_title(phpgw::get_var('title'));
			$price_item->set_agresso_id(phpgw::get_var('agresso_id'));
			$price_item->set_is_area(phpgw::get_var('is_area') == 'true' ? true : false);
			$price_item->set_is_inactive(phpgw::get_var('is_inactive') == 'on' ? true : false);
			$price_item->set_is_adjustable(phpgw::get_var('is_adjustable') == 'on' ? true : false);
			$price_item->set_standard(phpgw::get_var('is_standard') == 'on' ? true : false);
			$price_item->set_price(phpgw::get_var('price'));
			$price_item->set_price_type_id(phpgw::get_var('price_type_id', 'int'));
			if ($price_item->get_agresso_id() == null)
			{
				phpgwapi_cache::message_set(lang('missing_agresso_id'), 'error');
			}
			else
			{
				if (rental_soprice_item::get_instance()->store($price_item))
				{
					phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
					$price_item_id = $price_item->get_id();
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
				}
			}
			$this->edit(array('price_item_id' => $price_item_id));
		}

		public function set_value()
		{
			if (!self::isExecutiveOfficer())
			{
				return;
			}

			$field_name = phpgw::get_var('field_name');
			$value = phpgw::get_var('value');
			$id = phpgw::get_var('id');

			switch ($field_name)
			{
				case 'count':
					$value = (int) $value;
					break;
				case 'price':
					$value = trim(str_replace(array($this->currency_suffix, " "), '', $value));
					break;
				case 'date_start':
				case 'date_end':
					$value = phpgwapi_datetime::date_to_timestamp(phpgw::get_var('value'));
					break;
				default:
					$value = phpgw::get_var('value');
					break;
			}

			$price_item = rental_socontract_price_item::get_instance()->get_single($id);
			$price_item->set_field($field_name, $value);
			$result = rental_socontract_price_item::get_instance()->store($price_item);

			$message = array();
			if ($result)
			{
				$message['message'][] = array('msg' => lang('data has been saved'));
			}
			else
			{
				$message['error'][] = array('msg' => lang('data has not been saved'));
			}

			return $message;
		}

		/**
		 * (non-PHPdoc)
		 * @see rental/inc/rental_uicommon#query()
		 */
		public function query()
		{
			$length = phpgw::get_var('length', 'int');
			$user_rows_per_page = $length > 0 ? $length : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$num_of_objects = $length == -1 ? 0 : $user_rows_per_page;


			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'agresso_id';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;

			$search_for = '';
			$search_type = '';

			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');
			if (isset($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
			}

			//Retrieve the type of query and perform type specific logic
			$type = phpgw::get_var('type');
			switch ($type)
			{
				case 'included_price_items':
					if (isset($contract))
					{
						$filters = array('contract_id' => $contract->get_id());
						$result_objects = rental_socontract_price_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
						$object_count = rental_socontract_price_item::get_instance()->get_count($search_for, $search_type, $filters);
					}
					break;
				case 'not_included_price_items': // We want to show price items in the source list even after they've been added to a contract
					$filters = array('price_item_status' => 'active', 'responsibility_id' => phpgw::get_var('responsibility_id'));
					$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'manual_adjustment':
					$filters = array('price_item_status' => 'active', 'is_adjustable' => 'false');
					$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				default:
					//$filters = array('price_item_status' => 'active','responsibility_id' => phpgw::get_var('responsibility_id'));
					$filters = array();
					$result_objects = rental_soprice_item::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_soprice_item::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}

			// Create an empty row set
			$rows = array();
			foreach ($result_objects as $record)
			{
				if (isset($record))
				{
					// ... add a serialized record
					$rows[] = $record->serialize();
				}
			}

			/* $editable = phpgw::get_var('editable') == 'true' ? true : false;

			  array_walk(
			  $rows,
			  array($this, 'add_actions'),
			  array(
			  $contract_id,
			  $type,
			  $editable
			  )
			  ); */

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $object_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Add action links and labels for the context menu of the list items
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [price_item.id, type of query, editable]
		 */
		/* public function add_actions(&$value, $key, $params)
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

		  $sogeneric 			= CreateObject('rental.sogeneric','composite_standard');
		  $composite_standards = $sogeneric->read(array('allrows' => true));
		  foreach($composite_standards as $composite_standard)
		  {
		  $value['ajax'][] = true;
		  $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_price_item', 'price_item_id' => $value['id'], 'contract_id' => $contract_id, 'factor' => $composite_standard['factor'])));
		  $value['labels'][] = lang('add') . " {$composite_standard['name']}";
		  }
		  }
		  break;
		  }
		  } */

		public function manual_adjustment()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('manual_adjustment');
			if (!$this->isExecutiveOfficer())
			{
				phpgw::no_access();
			}
			self::set_active_menu('rental::contracts::price_item_list::manual_adjustment');

			$types_options = array();
			$types_options[] = array('id' => '', 'name' => 'Velg priselement');
			$types = rental_soprice_item::get_instance()->get_manual_adjustable();
			foreach ($types as $id => $label)
			{
				$types_options[] = array('id' => $id, 'name' => lang($label));
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					),
					array
						(
						'name' => 'responsibility_id',
						'source' => 'responsibility_id'
					)
				)
			);

			$tabletools[] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uiadjustment.show_affected_contracts'
				)),
				'parameters' => json_encode($parameters)
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'rental.uiadjustment.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			$tabletools[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'type' => 'custom',
				'custom_code' => "
					var oArgs = " . json_encode(array(
					'menuaction' => 'rental.uiadjustment.delete',
					'phpgw_return_as' => 'json'
				)) . ";
					var parameters = " . json_encode($parameters) . ";
					removePrice(oArgs, parameters);
				"
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uiadjustment.query',
						'type' => 'manual_adjustments', 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => array(
					array(
						'key' => 'year',
						'label' => lang('year'),
						'className' => '',
						'sortable' => true,
						'hidden' => false
					),
					array(
						'key' => 'adjustment_date',
						'label' => lang('adjustment_date'),
						'className' => '',
						'sortable' => true,
						'hidden' => false
					),
					array(
						'key' => 'price_item_id',
						'label' => lang('price_item'),
						'className' => '',
						'sortable' => false,
						'hidden' => true
					),
					array(
						'key' => 'new_price',
						'label' => lang('new_price'),
						'className' => '',
						'sortable' => false,
						'hidden' => true
					),
					array(
						'key' => 'adjustment_type',
						'label' => lang('adjustment_type'),
						'className' => '',
						'sortable' => false,
						'hidden' => false
					),
					array(
						'key' => 'percent',
						'label' => lang('percent'),
						'className' => '',
						'sortable' => false,
						'hidden' => true
					),
					array(
						'key' => 'interval',
						'label' => lang('interval'),
						'className' => '',
						'sortable' => false,
						'hidden' => true
					),
					array(
						'key' => 'responsibility_title',
						'label' => lang('responsibility'),
						'className' => '',
						'sortable' => true,
						'hidden' => false
					),
					array(
						'key' => 'extra_adjustment',
						'label' => lang('extra_adjustment'),
						'className' => '',
						'sortable' => true,
						'hidden' => false
					),
					array(
						'key' => 'is_executed',
						'label' => lang('is_executed'),
						'className' => '',
						'sortable' => true,
						'hidden' => false
					)
				),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'list_type' => array('options' => $types_options),
			);

			self::add_javascript('rental', 'rental', 'price_item.adjust_price.js');
			self::render_template_xsl(array('price_item', 'datatable_inline'), array('adjustment_price' => $data));
		}

		public function adjust_price()
		{
			if (!self::isExecutiveOfficer())
			{
				phpgw::no_access();
			}

			$id = (int)phpgw::get_var('price_item_id');
			$new_price = str_replace(',', '.', phpgw::get_var('new_price'));
			$receipt = array();

			if ($new_price != null && is_numeric($new_price))
			{
				$price_item = rental_price_item::get($id);
				$price_item->set_price($new_price);
				if (rental_soprice_item::get_instance()->store($price_item))
				{
					$adjustment = new rental_adjustment();
					$adjustment->set_price_item_id($price_item->get_id());
					$adjustment->set_new_price($new_price);
					$adjustment->set_year(date('Y'));
					$adjustment->set_percent(0);
					$adjustment->set_interval(0);
					$adjustment->set_responsibility_id($price_item->get_responsibility_id());
					$adjustment->set_is_manual(true);
					$adjustment->set_adjustment_date(time());
					rental_soadjustment::get_instance()->store($adjustment);
					//$message[] = "Priselement med Agresso id {$price_item->get_agresso_id()} er oppdatert med ny pris {$new_price}";
					$receipt['message'][] = array('msg' => "Priselement med Agresso id {$price_item->get_agresso_id()} er oppdatert med ny pris {$new_price}");
					//update affected contract_price_items
					$no_of_contracts_updated = rental_soprice_item::get_instance()->adjust_contract_price_items($id, $new_price);
					if ($no_of_contracts_updated > 0)
					{
						$message = $no_of_contracts_updated . ' priselementer p&aring; kontrakter er oppdatert';
					}
					else
					{
						$message = "Ingen kontrakter er oppdatert";
					}
					$receipt['message'][] = array('msg' => $message);

					$types_options[] = array('id' => '', 'name' => 'Velg priselement');
					$types = rental_soprice_item::get_instance()->get_manual_adjustable();
					foreach ($types as $id => $label)
					{
						$types_options[] = array('id' => $id, 'name' => lang($label));
					}
					$receipt['types_options'] = $types_options;
				}
				else
				{
					$receipt['error'][] = array('msg' => 'error');
				}
			}
			else
			{
				$receipt['error'][] = array('msg' => lang('price_not_numeric'));
			}

			return $receipt;
		}
	}