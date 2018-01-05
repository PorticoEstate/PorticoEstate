<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sobilling');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.sodocument');
	phpgw::import_class('rental.soinvoice');
	phpgw::import_class('rental.sonotification');
	phpgw::import_class('rental.soprice_item');
	phpgw::import_class('rental.socontract_price_item');
	phpgw::import_class('rental.soadjustment');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract_price_item', 'inc/model/');
	include_class('rental', 'notification', 'inc/model/');

	phpgw::import_class('phpgwapi.datetime');

	class rental_uicontract extends rental_uicommon
	{

		private $pdf_templates = array();
		private $config;
		public $public_functions = array
			(
			'add' => true,
			'add_from_composite' => true,
			'copy_contract' => true,
			'edit' => true,
			'save' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'get'	=>  true,
			'add_party' => true,
			'remove_party' => true,
			'add_composite' => true,
			'remove_composite' => true,
			'set_payer' => true,
			'add_price_item' => true,
			'remove_price_item' => true,
			'reset_price_item' => true,
			'add_notification' => true,
			'download' => true,
			'get_total_price' => true,
			'notify_on_expire'	=> true
		);

		public function __construct()
		{
			$this->get_pdf_templates();
			parent::__construct();
			self::set_active_menu('rental::contracts');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('contracts');

			$this->config = CreateObject('phpgwapi.config', 'rental');
			$this->config->read();
		}

		private function _get_filters()
		{
			$filters = array();

			if ($this->isAdministrator() || $this->isExecutiveOfficer())
			{
				/* $config	= CreateObject('phpgwapi.config','rental');
				  $config->read(); */
				$valid_contract_types = array();
				if (isset($this->config->config_data['contract_types']) && is_array($this->config->config_data['contract_types']))
				{
					foreach ($this->config->config_data['contract_types'] as $_key => $_value)
					{
						if ($_value)
						{
							$valid_contract_types[] = $_value;
						}
					}
				}
				$new_contract_options = array();
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach ($types as $id => $label)
				{
					if ($valid_contract_types && !in_array($id, $valid_contract_types))
					{
						continue;
					}
					$names = $this->locations->get_name($id);
					if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
					{
						if ($this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
						{
							$new_contract_options[] = array('id' => $id, 'name' => lang($label));
						}
					}
				}
				$filters[] = array
					(
					'type' => 'filter',
					'name' => 'location_id',
					'text' => lang('t_new_contract'),
					'list' => $new_contract_options
				);
			}

			$search_option = array
				(
				array('id' => 'all', 'name' => lang('all')),
				array('id' => 'id', 'name' => lang('contract_id')),
				array('id' => 'party_name', 'name' => lang('party_name')),
				array('id' => 'customer_id', 'name' => lang('customer id') . ' (Agresso)'),
				array('id' => 'composite', 'name' => lang('composite_name')),
				array('id' => 'composite_address', 'name' => lang('composite_address')),
				array('id' => 'location_code', 'name' => lang('object_number'))
			);
			$search_type = phpgw::get_var('search_type');
			foreach ($search_option as &$entry)
			{
				$entry['selected'] = $entry['id'] == $search_type ? 1 : 0;
			}
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'search_option',
				'text' => lang('search_where'),
				'list' => $search_option
			);

			$status_option = array
				(
				array('id' => 'all', 'name' => lang('all')),
				array('id' => 'under_planning', 'name' => lang('under_planning')),
				array('id' => 'active', 'name' => lang('active_plural')),
				array('id' => 'under_dismissal', 'name' => lang('under_dismissal')),
				array('id' => 'ended', 'name' => lang('ended'))
			);
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'contract_status',
				'text' => lang('status'),
				'list' => $status_option
			);

			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			$types_options = array();
			array_unshift($types_options, array('id' => 'all', 'name' => lang('all')));
			foreach ($types as $id => $label)
			{
				$types_options[] = array('id' => $id, 'name' => lang($label));
			}
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'contract_type',
				'text' => lang('field_of_responsibility'),
				'list' => $types_options
			);

			return $filters;
		}

		private function _get_tableDef_composite( $mode, $contract_id )
		{
			$uicols_composite = rental_socomposite::get_instance()->get_uicols();
			$columns_def = array();
			$uicols_count = count($uicols_composite['descr']);
			for ($i = 0; $i < $uicols_count; $i++)
			{
				if ($uicols_composite['input_type'][$i] != 'hidden')
				{
					$columns_def[$i]['key'] = $uicols_composite['name'][$i];
					$columns_def[$i]['label'] = $uicols_composite['descr'][$i];
					$columns_def[$i]['sortable'] = $uicols_composite['sortable'][$i];
				}
			}
			if (!empty($this->config->config_data['contract_future_info']))
			{
				$columns_def[] = array("key" => "contracts", "label" => lang('contract_future_info'),
					"sortable" => false, "hidden" => false);
			}
			if (!empty($this->config->config_data['contract_furnished_status']))
			{
				$columns_def[] = array("key" => "furnished_status", "label" => lang('furnish_type'),
					"sortable" => false, "hidden" => false);
			}

			$tabletools_composite1[] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicomposite.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			if ($mode == 'edit')
			{
				$tabletools_composite1[] = array
					(
					'my_name' => 'delete',
					'text' => lang('remove'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.remove_composite',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'composite_id',
								'source' => 'id')))) . ";
						removeComposite(oArgs, parameters);
					"
				);
			}

			$tabletools_composite1[] = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uicomposite.download',
					'contract_id' => $contract_id,
					'type' => 'included_composites',
					'export' => true,
					'allrows' => true))
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'data' => json_encode(array()),
				'ColumnDefs' => $columns_def,
				'tabletools' => $tabletools_composite1,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			if ($mode == 'edit')
			{
				$tabletools_composite2[] = array
					(
					'my_name' => 'view',
					'text' => lang('show'),
					'action' => self::link(array(
						'menuaction' => 'rental.uicomposite.view'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools_composite2[] = array
					(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.add_composite',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'composite_id',
								'source' => 'id')))) . ";
						addComposite(oArgs, parameters);
					"
				);

				$tabletools_composite2[] = array
					(
					'my_name' => 'download_not_included_composites',
					'text' => lang('download'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicomposite.download',
						'contract_id' => $contract_id,
						'type' => 'not_included_composites',
						'export' => true
					)) . ";
						downloadComposite(oArgs);
					"
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_2',
					'requestUrl' => "''",
					'data' => json_encode(array()),
					'ColumnDefs' => $columns_def,
					'tabletools' => $tabletools_composite2,
					'config' => array(
						array('disableFilter' => true)
					)
				);
			}

			return $datatable_def;
		}

		private function _get_tableDef_party( $mode, $contract_id )
		{
			$columns_def = array(
				array('key' => 'identifier', 'label' => lang('identifier'), 'className' => '',
					'sortable' => true, 'hidden' => false),
				array('key' => 'name', 'label' => lang('name'), 'className' => '', 'sortable' => true,
					'hidden' => false),
				array('key' => 'address', 'label' => lang('address'), 'className' => '',
					'sortable' => true,
					'hidden' => false),
				array('key' => 'is_payer', 'label' => lang('is_payer'), 'sortable' => false,
					'hidden' => false, "className" => 'center')
			);

			$tabletools_party1[] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uiparty.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$download = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uiparty.download',
					'contract_id' => $contract_id,
					'type' => 'included_parties',
					'export' => true,
					'allrows' => true))
			);

			if ($mode == 'edit')
			{
				$columns_def[3]['formatter'] = 'formatterPayer';

				$tabletools_party1[] = array
					(
					'my_name' => 'delete',
					'text' => lang('remove'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.remove_party',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'party_id',
								'source' => 'id')))) . ";
						removeParty(oArgs, parameters);
					"
				);

				$tabletools_party1[] = $download;
				$datatable_def[] = array
					(
					'container' => 'datatable-container_3',
					'requestUrl' => "''",
					'data' => json_encode(array()),
					'ColumnDefs' => $columns_def,
					'tabletools' => $tabletools_party1,
					'config' => array(
						array('disableFilter' => true)
					)
				);

				$tabletools_party2[] = array
					(
					'my_name' => 'view',
					'text' => lang('show'),
					'action' => self::link(array(
						'menuaction' => 'rental.uiparty.view'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools_party2[] = array
					(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.add_party',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'party_id',
								'source' => 'id')))) . ";
						addParty(oArgs, parameters);
					"
				);

				$tabletools_party2[] = array
					(
					'my_name' => 'download_not_included_composites',
					'text' => lang('download'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uiparty.download',
						'contract_id' => $contract_id,
						'type' => 'not_included_parties',
						'export' => true
					)) . ";
						downloadParties(oArgs);
					"
				);

				$columns_def[3]['hidden'] = true;
				$datatable_def[] = array
					(
					'container' => 'datatable-container_4',
					'requestUrl' => "''",
					'data' => json_encode(array()),
					'ColumnDefs' => $columns_def,
					'tabletools' => $tabletools_party2,
					'config' => array(
						array('disableFilter' => true)
					)
				);
			}
			else
			{
				$tabletools_party1[] = $download;
				$datatable_def[] = array
					(
					'container' => 'datatable-container_2',
					'requestUrl' => "''",
					'data' => json_encode(array()),
					'ColumnDefs' => $columns_def,
					'tabletools' => $tabletools_party1,
					'config' => array(
						array('disableFilter' => true)
					)
				);
			}

			return $datatable_def;
		}

		private function _get_tableDef_price( $mode, $contract_id )
		{
			if (empty($this->config->config_data['contract_furnished_status']))
			{
				$columns_def = array(
					array('key' => 'agresso_id', 'label' => lang('agresso_id'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'title', 'label' => lang('name'), 'className' => '', 'sortable' => true,
						'hidden' => false, 'editor' => $mode == 'edit' ? true : false),
					array('key' => 'is_area', 'label' => lang('title'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'price', 'label' => lang('price'), 'sortable' => false, 'hidden' => false,
						'formatter' => 'formatterPrice', 'className' => 'right', 'editor' => $mode == 'edit' ? true : false),
					array("key" => "area", "label" => lang('area'), "formatter" => "formatterArea",
						'className' => 'right'),
					array("key" => "count", "label" => lang('count'), 'editor' => $mode == 'edit' ? true : false),
					array("key" => "total_price", "label" => lang('total_price'), 'formatter' => 'formatterPrice',
						'className' => 'right'),
					array("key" => "date_start", "label" => lang('date_start'), 'formatter' => $mode == 'edit' ? 'formatterDateStart_price_item' : "",
						'className' => 'center'),
					array("key" => "date_end", "label" => lang('date_end'), 'formatter' => $mode == 'edit' ? 'formatterDateEnd_price_item' : "",
						'className' => 'center'),
					array("key" => "is_one_time", "label" => lang('is_one_time'), 'formatter' => $mode == 'edit' ? 'formatterIs_one_time' : "",
						'className' => 'center'),
					array("key" => "price_type_title", "label" => lang('type'), 'sortable' => false,
						'className' => 'center')
				);

			}
			else
			{
					$columns_def = array(
					array('key' => 'agresso_id', 'label' => lang('agresso_id'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'title', 'label' => lang('name'), 'className' => '', 'sortable' => true,
						'hidden' => false, 'editor' => $mode == 'edit' ? true : false),
					array('key' => 'is_area', 'label' => lang('title'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'price', 'label' => lang('price'), 'sortable' => false, 'hidden' => false,
						'formatter' => 'formatterPrice', 'className' => 'right', 'editor' => $mode == 'edit' ? true : false),
					array('key' => 'location_factor', 'label' => lang('location'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'standard_factor', 'label' => lang('standard'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'custom_factor', 'label' => lang('custom price factor'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array("key" => "area", "label" => lang('area'), "formatter" => "formatterArea",
						'className' => 'right'),
					array("key" => "count", "label" => lang('count'), 'editor' => $mode == 'edit' ? true : false),
					array("key" => "total_price", "label" => lang('total_price'), 'formatter' => 'formatterPrice',
						'className' => 'right'),
					array("key" => "date_start", "label" => lang('date_start'), 'formatter' => $mode == 'edit' ? 'formatterDateStart_price_item' : "",
						'className' => 'center'),
					array("key" => "date_end", "label" => lang('date_end'), 'formatter' => $mode == 'edit' ? 'formatterDateEnd_price_item' : "",
						'className' => 'center'),
					array("key" => "is_one_time", "label" => lang('is_one_time'), 'formatter' => $mode == 'edit' ? 'formatterIs_one_time' : "",
						'className' => 'center'),
					array("key" => "price_type_title", "label" => lang('type'), 'sortable' => false,
						'className' => 'center')
				);

			}



			if ($mode == 'edit')
			{
				$tabletools_price1[] = array
					(
					'my_name' => 'remove',
					'text' => lang('remove'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.remove_price_item',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'price_item_id',
								'source' => 'id')))) . ";
						removePrice(oArgs, parameters);
					"
				);

				$tabletools_price1[] = array
					(
					'my_name' => 'reset',
					'text' => lang('reset'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.reset_price_item',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'price_item_id',
								'source' => 'id')))) . ";
						removePrice(oArgs, parameters);
					"
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_5',
					'requestUrl' => "''",
					'data' => json_encode(array()),
					'ColumnDefs' => $columns_def,
					'tabletools' => $tabletools_price1,
					'config' => array(
						array('disableFilter' => true),
						array('editor_action' => self::link(array('menuaction' => 'rental.uiprice_item.set_value')))
					)
				);

				$tabletools_price2[] = array
					(
					'my_name' => 'add',
					'text' => lang('add'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.add_price_item',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'price_item_id',
								'source' => 'id')))) . ";
						addPrice(oArgs, parameters);
					"
				);
/*
				$sogeneric = CreateObject('rental.sogeneric', 'composite_standard');
				$composite_standards = $sogeneric->read(array('allrows' => true));
				foreach ($composite_standards as $composite_standard)
				{
					$tabletools_price2[] = array
						(
						'my_name' => 'add_' . $composite_standard['name'],
						'text' => lang('add') . " {$composite_standard['name']}",
						'type' => 'custom',
						'custom_code' => "
							var oArgs = " . json_encode(array(
							'menuaction' => 'rental.uicontract.add_price_item',
							'contract_id' => $contract_id,
							'factor' => $composite_standard['factor'],
							'phpgw_return_as' => 'json'
						)) . ";
							var parameters = " . json_encode(array('parameter' => array(array('name' => 'price_item_id',
									'source' => 'id')))) . ";
							addPrice(oArgs, parameters);
						"
					);
				}
*/
				if (empty($this->config->config_data['contract_furnished_status']))
				{
					unset($columns_def[4]);
					unset($columns_def[5]);
					unset($columns_def[6]);
					unset($columns_def[7]);
					unset($columns_def[8]);
					unset($columns_def[9]);
				}
				else
				{
					unset($columns_def[4]);
					unset($columns_def[5]);
					unset($columns_def[6]);
					unset($columns_def[7]);
					unset($columns_def[8]);
					unset($columns_def[9]);
					unset($columns_def[10]);
					unset($columns_def[11]);
					unset($columns_def[12]);
				}

				$datatable_def[] = array
					(
					'container' => 'datatable-container_6',
					'requestUrl' => "''",
					'data' => json_encode(array()),
					'ColumnDefs' => $columns_def,
					'tabletools' => $tabletools_price2,
					'config' => array(
						array('disableFilter' => true)
					)
				);
			}
			else
			{
				$datatable_def[] = array
					(
					'container' => 'datatable-container_3',
					'requestUrl' => "''",
					'data' => json_encode(array()),
					'ColumnDefs' => $columns_def,
					'config' => array(
						array('disableFilter' => true)
					)
				);
			}

			return $datatable_def;
		}

		private function _get_tableDef_invoice( $mode, $contract_id )
		{
			$tabletools_invoice[] = array
				(
				'my_name' => 'download_not_included_composites',
				'text' => lang('download'),
				'type' => 'custom',
				'custom_code' => "
						var oArgs = " . json_encode(array(
					'menuaction' => 'rental.uiinvoice_price_item.download',
					'contract_id' => $contract_id,
					'type' => 'invoice_price_items',
					'export' => true
				)) . ";
						downloadInvoice(oArgs);
					"
			);

			if ($mode == 'edit')
			{
				$table_name = 'datatable-container_7';
			}
			else
			{
				$table_name = 'datatable-container_4';
			}

			$datatable_def[] = array
				(
				'container' => $table_name,
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uiinvoice_price_item.query',
						'type' => 'invoice_price_items', 'editable' => true, 'invoice_id' => '-1',
						'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array(
					array('key' => 'title', 'label' => lang('name'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'agresso_id', 'label' => lang('agresso_id'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'is_area', 'label' => lang('type'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'price', 'label' => lang('price'), 'sortable' => false,
						'hidden' => false,
						'formatter' => 'formatterPrice', 'className' => 'right'),
					array('key' => 'area', 'label' => lang('area'), 'sortable' => false, 'hidden' => false,
						'className' => 'right'),
					array('key' => 'count', 'label' => lang('count'), 'sortable' => false,
						'hidden' => false,
						'className' => 'right'),
					array('key' => 'total_price', 'label' => lang('total_price'), 'sortable' => false,
						'hidden' => false, 'formatter' => 'formatterPrice', 'className' => 'right'),
					array('key' => 'timestamp_start', 'label' => lang('date_start'), 'sortable' => false,
						'hidden' => false, 'className' => 'center'),
					array('key' => 'timestamp_end', 'label' => lang('date_end'), 'sortable' => false,
						'hidden' => false, 'className' => 'center')
				),
				'tabletools' => $tabletools_invoice,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			return $datatable_def;
		}

		private function _get_tableDef_document( $mode, $contract_id, $permission = false )
		{
			$tabletools_documents = array();
			$tabletools_documents[] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uidocument.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$table_name = 'datatable-container_5';

			if (($mode == 'edit') && $permission)
			{
				$tabletools_documents[] = array
					(
					'my_name' => 'delete',
					'text' => lang('remove'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uidocument.delete',
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id')))) . ";
						removeDocument(oArgs, parameters);
					"
				);
				$table_name = 'datatable-container_8';
			}

			$datatable_def[] = array
				(
				'container' => $table_name,
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uidocument.query',
						'type' => 'documents_for_contract', 'editable' => true, 'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array(
					array('key' => 'title', 'label' => lang('title'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'type', 'label' => lang('type'), 'className' => '', 'sortable' => true,
						'hidden' => false),
					array('key' => 'name', 'label' => lang('name'), 'className' => '', 'sortable' => true,
						'hidden' => false)
				),
				'tabletools' => $tabletools_documents,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			return $datatable_def;
		}

		private function _get_tableDef_notification( $mode, $contract_id )
		{
			$table_name = 'datatable-container_6';

			$tabletools_notification = array();

			if ($mode == 'edit')
			{
				$tabletools_notification[] = array
					(
					'my_name' => 'delete',
					'text' => lang('delete'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uinotification.delete_notification',
						'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'
					)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id')))) . ";
						deleteNotification(oArgs, parameters);
					"
				);
				$table_name = 'datatable-container_9';
			}

			$datatable_def[] = array
				(
				'container' => $table_name,
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uinotification.query',
						'type' => 'notifications', 'editable' => true, 'contract_id' => $contract_id,
						'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array(
					array('key' => 'date', 'label' => lang('date'), 'className' => '', 'sortable' => true,
						'hidden' => false, 'className' => 'center'),
					array('key' => 'message', 'label' => lang('message'), 'className' => '',
						'sortable' => true,
						'hidden' => false),
					array('key' => 'recurrence', 'label' => lang('recurrence'), 'className' => '',
						'sortable' => true, 'hidden' => false),
					array('key' => 'name', 'label' => lang('user_or_group'), 'sortable' => false,
						'hidden' => false),
					array('key' => 'field_of_responsibility', 'label' => lang('field_of_responsibility'),
						'sortable' => false, 'hidden' => false)
				),
				'tabletools' => $tabletools_notification,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			return $datatable_def;
		}

		public function query()
		{
			$length = phpgw::get_var('length', 'int');

			$user_rows_per_page = $length > 0 ? $length : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			$search = phpgw::get_var('search');
			$query = phpgw::get_var('query');//direct url override
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects = $length == -1 ? 0 : $user_rows_per_page;

			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'old_contract_id';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			if($query)
			{
				$search_for = $query;
			}
			else
			{
				$search_for = $search['value'] ? $search['value'] : '';
			}
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'all');

			$export = phpgw::get_var('export', 'bool');
			//$editable		= phpgw::get_var('editable', 'bool');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			if ($export)
			{
				$num_of_objects = 0;
			}

			$price_items_only = phpgw::get_var('price_items'); //should only export contract price items

			$type = phpgw::get_var('type');
			switch ($type)
			{
				case 'contracts_for_adjustment':
					$adjustment_id = (int)phpgw::get_var('id');
					$adjustment = rental_soadjustment::get_instance()->get_single($adjustment_id);
					$filters = array('contract_type' => $adjustment->get_responsibility_id(),
						'adjustment_interval' => $adjustment->get_interval(), 'adjustment_year' => $adjustment->get_year(),
						'adjustment_is_executed' => $adjustment->is_executed(), 'extra_adjustment' => $adjustment->is_extra_adjustment());
					break;
				case 'contracts_part':	// Contracts for this party
					$filters = array('party_id' => phpgw::get_var('party_id'), 'contract_status' => phpgw::get_var('contract_status'),
						'contract_type' => phpgw::get_var('contract_type'), 'status_date_hidden' => phpgw::get_var('status_date'));
					break;
				case 'contracts_for_executive_officer':  // Contracts for this executive officer
					$filters = array('executive_officer' => $GLOBALS['phpgw_info']['user']['account_id']);
					break;
				case 'ending_contracts':
				case 'ended_contracts':
				case 'last_edited':
				case 'closing_due_date':
				case 'terminated_contracts':
					// Queries that depend on areas of responsibility
					$types = rental_socontract::get_instance()->get_fields_of_responsibility();
					$ids = array();
					$read_access = array();
					foreach ($types as $id => $label)
					{
						$names = $this->locations->get_name($id);
						if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
						{
							if ($this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
							{
								$ids[] = $id;
							}
							else
							{
								$read_access[] = $id;
							}
						}
					}

					if (count($ids) > 0)
					{
						$comma_seperated_ids = implode(',', $ids);
					}
					else
					{
						$comma_seperated_ids = implode(',', $read_access);
					}

					switch ($type)
					{
						case 'ending_contracts':   // Contracts that are about to end in areas of responsibility
							$filters = array('contract_status' => 'under_dismissal', 'contract_type' => $comma_seperated_ids);
							break;
						case 'ended_contracts': // Contracts that are ended in areas of responsibility
							$filters = array('contract_status' => 'ended', 'contract_type' => $comma_seperated_ids);
							break;
						case 'last_edited':  // Contracts that are last edited in areas of resposibility
							$filters = array('contract_type' => $comma_seperated_ids, 'contract_status' => 'active' );
							$sort_field = 'contract.last_updated';
							$sort_ascending = false;
							break;
						case 'closing_due_date':   //Contracts closing due date in areas of responsibility
							$filters = array('contract_status' => 'closing_due_date', 'contract_type' => $comma_seperated_ids);
							break;
						case 'terminated_contracts':
							$filters = array('contract_status' => 'terminated_contracts', 'contract_type' => $comma_seperated_ids);
							break;
					}

					break;
				case 'contracts_for_composite': // ... all contracts this composite is involved in, filters (status and date)
					$filters = array('composite_id' => phpgw::get_var('composite_id'), 'contract_status' => phpgw::get_var('contract_status'),
						'contract_type' => phpgw::get_var('contract_type'));
					$filters['status_date'] = phpgwapi_datetime::date_to_timestamp(phpgw::get_var('status_date'));
					break;
				/* case 'get_contract_warnings':	//get the contract warnings
				  $contract = rental_socontract::get_instance()->get_single(phpgw::get_var('contract_id'));
				  $contract->check_consistency();
				  $rows = $contract->get_consistency_warnings();
				  $result_count = count($rows);
				  $export=true;
				  break; */
				case 'all_contracts':
				default:
					phpgwapi_cache::session_set('rental', 'contract_query', $search_for);
					phpgwapi_cache::session_set('rental', 'contract_search_type', $search_type);
					phpgwapi_cache::session_set('rental', 'contract_status', phpgw::get_var('contract_status'));
					phpgwapi_cache::session_set('rental', 'contract_status_date', phpgw::get_var('date_status'));
					phpgwapi_cache::session_set('rental', 'contract_type', phpgw::get_var('contract_type'));
					$filters = array('contract_status' => phpgw::get_var('contract_status'),
						'contract_type' => phpgw::get_var('contract_type'));
					$filters['status_date'] = phpgwapi_datetime::date_to_timestamp(phpgw::get_var('date_status'));
					$filters['start_date_report'] = phpgwapi_datetime::date_to_timestamp(phpgw::get_var('filter_start_date_report'));
					$filters['end_date_report'] = phpgwapi_datetime::date_to_timestamp(phpgw::get_var('filter_end_date_report'));
			}

			$result_objects = rental_socontract::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_socontract::get_instance()->get_count($search_for, $search_type, $filters);

			//Serialize the contracts found
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
					if (isset($price_items_only))
					{
						//export contract price items
						$result_objects_pi = rental_socontract_price_item::get_instance()->get(0, 0, '', false, '', '', array(
							'contract_id' => $result->get_id(), 'export' => 'true', 'include_billed' => 'true'));
						foreach ($result_objects_pi as $result_pi)
						{
							if (isset($result_pi))
							{
								$rows[] = $result_pi->serialize();
							}
						}
					}
					else
					{
						//export contracts
						$rows[] = $result->serialize();
					}
				}
			}

			/* if(!$export){

			  //Check if user has access to Catch module
			  $access = $this->acl->check('.',PHPGW_ACL_READ,'catch');
			  if($access)
			  {
			  //$config->read();
			  $entity_id_in = $this->config->config_data['entity_config_move_in'];
			  $entity_id_out = $this->config->config_data['entity_config_move_out'];
			  $category_id_in = $this->config->config_data['category_config_move_in'];
			  $category_id_out = $this->config->config_data['category_config_move_out'];
			  }

			  array_walk($rows, array($this, 'add_actions'), array($type,$ids,$adjustment_id,$entity_id_in,$entity_id_out,$category_id_in,$category_id_out));
			  } */

			if ($export)
			{
				/*
				 * reverse of nl2br()
				 */
				foreach ($rows as &$row)
				{
					foreach ($row as $key => &$value)
					{
						$value = preg_replace('#<br\s*?/?>#i', "\n", $value);
					}
				}
				return $rows;
			}

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $result_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * View a list of all contracts
		 */
		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$editable = phpgw::get_var('editable', 'bool');

			$appname = lang('contracts');
			$type = 'all_contracts';

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date_report');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date_report');

			$function_msg = lang('list %1', $appname);

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type' => 'date-picker',
								'id' => 'start_date_report',
								'name' => 'start_date_report',
								'value' => '',
								'text' => 'Export ' . lang('from')
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'end_date_report',
								'name' => 'end_date_report',
								'value' => '',
								'text' => 'Export ' . lang('to')
							),
							array(
								'type' => 'link',
								'value' => lang('export_contracts'),
								'onclick' => 'contract_export("all_contracts")',
								'class' => 'new_item'
							),
							array(
								'type' => 'link',
								'value' => lang('export_contract_price_items'),
								'onclick' => 'contract_export_price_items("all_contracts")',
								'class' => 'new_item'
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uicontract.index',
						'editable' => ($editable) ? 1 : 0,
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array('menuaction' => 'rental.uicontract.download',
						'type' => $type,
						'export' => true,
						'allrows' => true
					)),
//					'new_item' => array('onclick' => 'onNew_contract()'),
					'allrows' => true,
					'editor_action' => '',
					'query' => phpgw::get_var('search_for'),
					'field' => array(
						array(
							'key' => 'old_contract_id',
							'label' => lang('contract_id'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'date_start',
							'label' => lang('date_start'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'date_end',
							'label' => lang('date_end'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'type',
							'label' => lang('responsibility'),
							'sortable' => false,
							'hidden' => false
						),
						array(
							'key' => 'composite',
							'label' => lang('composite'),
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'party',
							'label' => lang('party'),
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'department',
							'label' => lang('department'),
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'term_label',
							'label' => lang('billing_term'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'total_price',
							'label' => lang('total_price') . " ({$this->currency_suffix})",
							'className' => 'right',
							'sortable' => false,
							'hidden' => false,
							'formatter' => 'formatterPrice'
						),
						array(
							'key' => 'rented_area',
							'label' => lang('area') . " ({$this->area_suffix})",
							'className' => 'right',
							'sortable' => false,
							'hidden' => false,
							'formatter' => 'formatterArea'
						),
						array(
							'key' => 'contract_status',
							'label' => lang('contract_status'),
							'className' => 'center',
							'sortable' => false,
							'hidden' => false
						),
						array(
							'key' => 'contract_notification_status',
							'label' => lang('notification_status'),
							'className' => 'center',
							'sortable' => false,
							'hidden' => false
						)
					)
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);

			$filters[] = array('type' => 'link',
								'value' => lang('new'),
								'onclick' => 'onNew_contract()',
								'class' => 'new_item'
							);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('view'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicontract.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'copy',
				'text' => lang('copy'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.copy_contract'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$temlate_counter = 0;
			foreach ($this->pdf_templates as $pdf_template)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'make_pdf_' . $pdf_template[0],
					'text' => lang('make_pdf') . ': ' . $pdf_template[0],
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'rental.uimakepdf.view',
						'pdf_template' => $temlate_counter
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);
				$temlate_counter++;
			}
/*
			$access = $this->acl->check('.', PHPGW_ACL_READ, 'catch');
			if ($access)
			{
				$entity_id_in = $this->config->config_data['entity_config_move_in'];
				$entity_id_out = $this->config->config_data['entity_config_move_out'];
				$category_id_in = $this->config->config_data['category_config_move_in'];
				$category_id_out = $this->config->config_data['category_config_move_out'];

				if (!empty($entity_id_in) && !empty($category_id_in))
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'show_move_in_reports',
						'text' => lang('show_move_in_reports'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uientity.index',
							'entity_id' => $entity_id_in,
							'cat_id' => $category_id_in,
							'type' => 'catch'
						)),
						'parameters' => json_encode(array('parameter' => array(array('name' => 'query',
									'source' => 'old_contract_id'))))
					);
				}

				if (!empty($entity_id_out) && !empty($category_id_out))
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'show_move_out_reports',
						'text' => lang('show_move_out_reports'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uientity.index',
							'entity_id' => $entity_id_out,
							'cat_id' => $category_id_out,
							'type' => 'catch'
						)),
						'parameters' => json_encode(array('parameter' => array(array('name' => 'query',
									'source' => 'old_contract_id'))))
					);
				}
			}
*/
			$code = <<<JS
			var thousandsSeparator = '$this->thousandsSeparator';
			var decimalSeparator = '$this->decimalSeparator';
			var decimalPlaces = '$this->decimalPlaces';
			var currency_suffix = '$this->currency_suffix';
			var area_suffix = '$this->area_suffix';
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			self::add_javascript('rental', 'rental', 'contract.index.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function save()
		{
			$contract_id = (int)phpgw::get_var('id');
			$location_id = (int)phpgw::get_var('location_id');
			$update_price_items = false;

			$message = null;
			$error = null;
			$add_default_price_items = false;

			if (isset($contract_id) && $contract_id > 0)
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);

				if (!($contract && $contract->has_permission(PHPGW_ACL_EDIT)))
				{
					phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_edit_contract'));
				}

				// Gets responsibility area from db (ex: eksternleie, internleie)
				$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($contract->get_location_id());

				// Redirect with error message if responsibility area is eksternleie and contract type not set
				if (!is_numeric(phpgw::get_var('contract_type')) && (strcmp($responsibility_area, "contract_type_eksternleie") == 0))
				{
					//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => $message, 'error' => $error));
					phpgwapi_cache::message_set(lang('billing_removed_external_contract'), 'error');
					$this->edit();
				}
			}
			else
			{
				// Gets responsibility area from db (ex: eksternleie, internleie)
				$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($location_id);

				// Redirect with error message if responsibility area is eksternleie and contract type not set
				if (!is_numeric(phpgw::get_var('contract_type')) && (strcmp($responsibility_area, "contract_type_eksternleie") == 0))
				{
					//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'location_id' => $location_id, 'message' => $message, 'error' => $error));
					phpgwapi_cache::message_set(lang('billing_removed_external_contract'), 'error');
					$this->edit();
				}

				if (isset($location_id) && ($this->isExecutiveOfficer() || $this->isAdministrator()))
				{
					$contract = new rental_contract();
					$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
					$contract->set_location_id($location_id);
					$contract->set_contract_type_title($fields[$location_id]);
					$add_default_price_items = true;
				}
			}

			$date_start = phpgwapi_datetime::date_to_timestamp(phpgw::get_var('date_start'));
			$date_end = phpgwapi_datetime::date_to_timestamp(phpgw::get_var('date_end'));

			if (isset($contract))
			{
				$contract->set_contract_date(new rental_contract_date($date_start, $date_end));
				$contract->set_security_type(phpgw::get_var('security_type', 'int'));
				$contract->set_security_amount(phpgw::get_var('security_amount'));
				$contract->set_executive_officer_id(phpgw::get_var('executive_officer'));
				$contract->set_comment(phpgw::get_var('comment'));

				if (isset($location_id) && $location_id > 0)
				{
					$contract->set_location_id($location_id); // only present when new contract
				}
				$contract->set_term_id(phpgw::get_var('billing_term'));
				$contract->set_billing_start_date(phpgwapi_datetime::date_to_timestamp(phpgw::get_var('billing_start_date')));
				$contract->set_billing_end_date(phpgwapi_datetime::date_to_timestamp(phpgw::get_var('billing_end_date')));
				$contract->set_service_id(phpgw::get_var('service_id'));
				$contract->set_responsibility_id(phpgw::get_var('responsibility_id'));
				$contract->set_reference(phpgw::get_var('reference'));
				$contract->set_customer_order_id(phpgw::get_var('customer_order_id', 'int'));
				$contract->set_invoice_header(phpgw::get_var('invoice_header'));
				$contract->set_account_in(phpgw::get_var('account_in'));

				$contract->set_account_out(phpgw::get_var('account_out'));

				$contract->set_project_id(phpgw::get_var('project_id'));
				$contract->set_due_date(phpgwapi_datetime::date_to_timestamp(phpgw::get_var('due_date')));
				$contract->set_override_adjustment_start(phpgw::get_var('override_adjustment_start', 'int'));
				$contract->set_contract_type_id(phpgw::get_var('contract_type'));
				$old_rented_area = $contract->get_rented_area();
				$new_rented_area = phpgw::get_var('rented_area');
				$new_rented_area = str_replace(',', '.', $new_rented_area);
				$validated_numeric = false;
				if (!isset($new_rented_area) || $new_rented_area == '')
				{
					$new_rented_area = 0;
				}
				if ($old_rented_area != $new_rented_area)
				{
					$update_price_items = true;
				}
				$contract->set_rented_area($new_rented_area);
				$contract->set_adjustment_interval(phpgw::get_var('adjustment_interval'));
				$contract->set_adjustment_share(phpgw::get_var('adjustment_share'));
				$contract->set_adjustable(phpgw::get_var('adjustable') == 'on' ? true : false);
				$contract->set_publish_comment(phpgw::get_var('publish_comment') == 'on' ? true : false);
				$validated_numeric = $contract->validate_numeric();

				if ($validated_numeric)
				{
					$so_contract = rental_socontract::get_instance();
					$db_contract = $so_contract->get_db();
					$db_contract->transaction_begin();
					if ($so_contract->store($contract))
					{
						if ($update_price_items)
						{
							$success = $so_contract->update_price_items($contract->get_id(), $new_rented_area);
							if ($success)
							{
								$db_contract->transaction_commit();
								$message = lang('messages_saved_form');
								$contract_id = $contract->get_id();
							}
							else
							{
								$db_contract->transaction_abort();
								$error = lang('messages_form_error');
							}
						}
						else if ($add_default_price_items)
						{
							$so_price_item = rental_soprice_item::get_instance();
							//get default price items for location_id
							$default_price_items = $so_contract->get_default_price_items($contract->get_location_id());

							//add price_items to contract
							foreach ($default_price_items as $price_item_id)
							{
								$so_price_item->add_price_item($contract->get_id(), $price_item_id);
							}
							$db_contract->transaction_commit();
							$message = lang('messages_saved_form');
							$contract_id = $contract->get_id();
						}
						else
						{
							$db_contract->transaction_commit();
							$message = lang('messages_saved_form');
							$contract_id = $contract->get_id();
						}
					}
					else
					{
						$db_contract->transaction_abort();
						$error = lang('messages_form_error');
					}
				}
				else
				{
					$error = $contract->get_validation_errors();
				}
			}

			if (!empty($error))
			{
				phpgwapi_cache::message_set($error, 'error');
			}
			if (!empty($message))
			{
				phpgwapi_cache::message_set($message, 'message');
			}
			$this->edit(array('contract_id' => $contract_id));
		}


		public function get( $id = 0 )
		{
			$contract_id =  $id ? (int)$id : (int)phpgw::get_var('id');
			if (!empty($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);

				if (!($contract && $contract->has_permission(PHPGW_ACL_READ)))
				{
					phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_view_contract'));
				}

				$ret = $contract->serialize();
				foreach ($ret as &$entry)
				{
					$entry = rtrim($entry, '<br/>');
				}

			//	$ret['last_edited_by'] = $contract->get_last_edited_by();
				$ret['executive_officer'] = $GLOBALS['phpgw']->accounts->id2name($contract->get_executive_officer_id());
				$ret['security_amount'] = $contract->get_security_amount();
				phpgw::import_class('rental.soparty');

				$parties =  $contract->get_parties();
				foreach ($parties as $party_id => $value) // get the last one
				{

				}

				$party =  rental_soparty::get_instance()->get_single($party_id);
	//			_debug_array($party);

				$ret['identifier'] = $party->get_identifier();
				$ret['mobile_phone'] = $party->get_mobile_phone();

				return $ret;
			}
		}

		/**
		 * View a contract
		 */
		public function view()
		{
			$contract_id = (int)phpgw::get_var('id');
			$adjustment_id = (int)phpgw::get_var('adjustment_id');
			$mode = 'view';

			if (!empty($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);

				if (!($contract && $contract->has_permission(PHPGW_ACL_READ)))
				{
					phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_view_contract'));
				}
				$created = date($this->dateFormat, $contract->get_last_updated());
				$created_by = $contract->get_last_edited_by();
				$contract->check_consistency();
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.index'));
			}

			if (!$executive_officer = $contract->get_executive_officer_id())
			{
				$executive_officer = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			if (!$current_term_id = $contract->get_term_id())
			{
				$current_term_id = $this->config->config_data['default_billing_term'];
			}

			$current_contract_type_label = rental_socontract::get_instance()->get_contract_type_label($contract->get_contract_type_id());

			if ($executive_officer)
			{
				$account = $GLOBALS['phpgw']->accounts->get($executive_officer);
				if (!empty($account))
				{
					$executive_officer_label = $account->__toString();
				}
				else
				{
					$executive_officer_label = lang('nobody');
				}
			}
			else
			{
				$executive_officer_label = lang('nobody');
			}

			$billing_term_label = lang(rental_socontract::get_instance()->get_term_label($current_term_id));

			switch ($contract->get_security_type())
			{
				case rental_contract::SECURITY_TYPE_BANK_GUARANTEE:
					$security_type_label = lang('bank_guarantee');
					break;
				case rental_contract::SECURITY_TYPE_DEPOSIT:
					$security_type_label = lang('deposit');
					break;
				case rental_contract::SECURITY_TYPE_ADVANCE:
					$security_type_label = lang('advance');
					break;
				case rental_contract::SECURITY_TYPE_OTHER_GUARANTEE:
					$security_type_label = lang('other_guarantee');
					break;
				default:
					$security_type_label = lang('nobody');
					break;
			}

			$start_date = ($contract->get_contract_date() && $contract->get_contract_date()->has_start_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_start_date()) : '';
			$end_date = ($contract->get_contract_date() && $contract->get_contract_date()->has_end_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_end_date()) : '';
			$due_date = ($contract->get_due_date()) ? date($this->dateFormat, $contract->get_due_date()) : '';

			$account_in = $contract->get_account_in();
			$account_out = $contract->get_account_out();
			$project_id = $contract->get_project_id();

			$billing_start_date = ($contract->get_billing_start_date()) ? date($this->dateFormat, $contract->get_billing_start_date()) : '';
			$billing_end_date = ($contract->get_billing_end_date()) ? date($this->dateFormat, $contract->get_billing_end_date()) : '';

			$cur_responsibility_id = $contract->get_responsibility_id();
			$current_interval = $contract->get_adjustment_interval();
			$current_share = $contract->get_adjustment_share();

			$link_cancel = array
				(
				'menuaction' => 'rental.uicontract.index',
			);
			$cancel_text = 'cancel';

			if ($adjustment_id)
			{
				$link_cancel = array('menuaction' => 'rental.uiadjustment.show_affected_contracts',
					'id' => $adjustment_id);
				$cancel_text = 'contract_regulation_back';
			}

			$tabs = array();
			$tabs['details'] = array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uicontract.get_total_price',
						'contract_id' => $contract_id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => array(
					array('key' => 'total_price', 'label' => lang('total_price'), 'className' => 'right',
						'sortable' => false, 'formatter' => 'formatterPrice'),
					array('key' => 'area', 'label' => lang('area'), 'className' => 'right',
						'sortable' => false,
						'formatter' => 'formatterArea'),
					array('key' => 'price_per_unit', 'label' => lang('price_per_unit'), 'className' => 'right',
						'sortable' => false, 'formatter' => 'formatterPrice')
				),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$tabs['composite'] = array('label' => lang('Composite'), 'link' => '#composite',
				'function' => 'get_composite_data()');
			$tabs['parties'] = array('label' => lang('Parties'), 'link' => '#parties', 'function' => 'get_parties_data()');
			$tabs['price'] = array('label' => lang('Price'), 'link' => '#price', 'function' => 'get_price_data()');
			$tabs['invoice'] = array('label' => lang('Invoice'), 'link' => '#invoice', 'function' => 'initial_invoice_data()');
			$tabs['documents'] = array('label' => lang('Documents'), 'link' => '#documents');
			$tabs['notifications'] = array('label' => lang('Notifications'), 'link' => '#notifications');

			$link_included_composites = json_encode(self::link(array('menuaction' => 'rental.uicomposite.query',
					'type' => 'included_composites', 'editable' => true, 'contract_id' => $contract_id,
					'phpgw_return_as' => 'json')));
			$link_included_parties = json_encode(self::link(array('menuaction' => 'rental.uiparty.query',
					'type' => 'included_parties', 'editable' => true, 'contract_id' => $contract_id,
					'phpgw_return_as' => 'json')));
			$link_included_price_items = json_encode(self::link(array('menuaction' => 'rental.uiprice_item.query',
					'type' => 'included_price_items', 'editable' => true, 'contract_id' => $contract_id,
					'phpgw_return_as' => 'json')));

			$tableDef_composite = $this->_get_tableDef_composite($mode, $contract_id);
			$tableDef_party = $this->_get_tableDef_party($mode, $contract_id);
			$tableDef_price = $this->_get_tableDef_price($mode, $contract_id);
			$tableDef_invoice = $this->_get_tableDef_invoice($mode, $contract_id);
			$tableDef_document = $this->_get_tableDef_document($mode, $contract_id);
			$tableDef_notification = $this->_get_tableDef_notification($mode, $contract_id);

			$datatable_def = array_merge($datatable_def, $tableDef_composite, $tableDef_party, $tableDef_price, $tableDef_invoice, $tableDef_document, $tableDef_notification);

			/*			 * ***************************** invoice filters */
			$invoices = rental_soinvoice::get_instance()->get(0, 0, '', false, '', '', array(
				'contract_id' => $contract->get_id()));
			if ($invoices != null && count($invoices) > 0)
			{
				foreach ($invoices as $invoice)
				{
					$serial = $invoice->get_serial_number();
					$serial_number = isset($serial) ? " - " . $invoice->get_serial_number() : "";
					$invoice_options[] = array('id' => $invoice->get_id(), 'name' => "{$invoice->get_billing_title()} - " . date($this->dateFormat, $invoice->get_timestamp_created()) . " - " . number_format($invoice->get_total_sum(), $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator) . " {$this->currency_suffix}" . $serial_number);
				}
			}
			else
			{
				$invoice_options[] = array('id' => '-1', 'name' => lang('No invoices were found'));
			}
			/*			 * ******************************************************************************** */

			/*			 * ***************************** document filters */
			$document_types = rental_sodocument::get_instance()->get_document_types();
			$document_types_options = array();
			foreach ($document_types as $id => $label)
			{
				$document_types_options[] = array('id' => $id, 'name' => lang($label));
			}

			$document_search_options[] = array('id' => 'all', 'name' => lang('all'));
			$document_search_options[] = array('id' => 'title', 'name' => lang('document_title'));
			$document_search_options[] = array('id' => 'name', 'name' => lang('document_name'));
			/*			 * ******************************************************************************** */


			$moveout_gross = createObject('rental.bomoveout')->read(array('filters' => array('contract_id' => $contract_id)));
			$moveout = $moveout_gross['results'] ? $moveout_gross['results'][0] : array();
			if($moveout)
			{
				$moveout['url'] = self::link(array('menuaction' => 'rental.uimoveout.view','id' => $moveout['id']));
			}
			else
			{
				$moveout['new_report'] = self::link(array('menuaction' => 'rental.uimoveout.edit','contract_id' => $contract_id));
			}
			$movein_gross = createObject('rental.bomovein')->read(array('filters' => array('contract_id' => $contract_id)));
			$movein = $movein_gross['results'] ? $movein_gross['results'][0] : array();
			if($movein)
			{
				$movein['url'] = self::link(array('menuaction' => 'rental.uimovein.view','id' => $movein['id']));
			}
			else
			{
				$movein['new_report'] = self::link(array('menuaction' => 'rental.uimovein.edit','contract_id' => $contract_id));
			}

			$code = <<<JS
				var thousandsSeparator = '$this->thousandsSeparator';
				var decimalSeparator = '$this->decimalSeparator';
				var decimalPlaces = '$this->decimalPlaces';
				var currency_suffix = '$this->currency_suffix';
				var area_suffix = '$this->area_suffix';
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_cancel),
				'lang_cancel' => lang($cancel_text),
				'value_contract_number' => $contract->get_old_contract_id(),
				'value_parties' => $contract->get_party_name_as_list(),
				'value_last_updated' => $created,
				'value_name' => $created_by,
				'value_composite' => $contract->get_composite_name_as_list(),
				'value_field_of_responsibility' => lang($contract->get_contract_type_title()),
				'value_date_start' => $start_date,
				'value_date_end' => $end_date,
				'value_due_date' => $due_date,
				'value_invoice_header' => $contract->get_invoice_header(),
				'value_billing_start' => $billing_start_date,
				'value_billing_end' => $billing_end_date,
				'value_reference' => $contract->get_reference(),
				'value_customer_order_id' => $contract->get_customer_order_id(),
				'value_responsibility_id' => $cur_responsibility_id,
				'value_service' => $contract->get_service_id(),
				'value_account_in' => $account_in,
				'value_account_out' => $account_out,
				'value_project_id' => $project_id,
				'security_amount_simbol' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'value_security_amount' => $contract->get_security_amount(),
				'value_rented_area' => $contract->get_rented_area(),
				'rented_area_simbol' => $this->area_suffix,
				'is_adjustable' => $contract->is_adjustable(),
				'value_adjustment_year' => $contract->get_adjustment_year(),
				'value_override_adjustment_start' => $contract->get_override_adjustment_start(),
				'value_comment' => $contract->get_comment(),
				'value_publish_comment' => $contract->get_publish_comment(),
				'location_id' => $contract->get_location_id(),
				'contract_id' => $contract_id,
				'mode' => $mode,
				'link_included_composites' => $link_included_composites,
				'link_included_parties' => $link_included_parties,
				'link_included_price_items' => $link_included_price_items,
				'list_invoices' => array('options' => $invoice_options),
				'list_document_types' => array('options' => $document_types_options),
				'list_document_search' => array('options' => $document_search_options),
				'value_contract_type' => lang($current_contract_type_label),
				'value_executive_officer' => $executive_officer_label,
				'value_billing_term' => $billing_term_label,
				'value_security_type' => $security_type_label,
				'value_security_amount_view' => ($contract->get_security_amount()) ? $contract->get_security_amount() : '0',
				'value_current_interval' => $current_interval . " " . lang('year'),
				'value_current_share' => $current_share . " %",
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'moveout' => $moveout,
				'movein' => $movein
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');

			self::add_javascript('rental', 'rental', 'contract.view.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('contract', 'datatable_inline'), array('view' => $data));
		}

		/**
		 * Edit a contract
		 */
		public function edit( $values = array(), $mode = 'edit' )
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');

			$contract_id = (int)phpgw::get_var('id');
			$location_id = (int)phpgw::get_var('location_id');
			$adjustment_id = (int)phpgw::get_var('adjustment_id');

			$list_consistency_warnings = array();

			if ($values['contract_id'])
			{
				$contract_id = $values['contract_id'];
			}

			if (!empty($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);

				if (!($contract && $contract->has_permission(PHPGW_ACL_EDIT)))
				{
					phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_edit_contract'));
				}
				$created = date($this->dateFormat, $contract->get_last_updated());
				$created_by = $contract->get_last_edited_by();
				$contract->check_consistency();
				$list_consistency_warnings = $contract->get_consistency_warnings();
			}
			else
			{
				if ($this->isAdministrator() || $this->isExecutiveOfficer())
				{
					$created = date($this->dateFormat, strtotime('now'));
					$created_by = $GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw_info']['user']['account_id']);

					$contract = new rental_contract();
					$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
					$contract->set_location_id($location_id);
					$contract->set_contract_type_title($fields[$location_id]);
				}
				else
				{
					phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_new_contract'));
				}
			}

			if (!$executive_officer = $contract->get_executive_officer_id())
			{
				$executive_officer = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			if (!$current_term_id = $contract->get_term_id())
			{
				$current_term_id = $this->config->config_data['default_billing_term'];
			}

			$GLOBALS['phpgw']->jqcal->add_listener('date_start');
			$GLOBALS['phpgw']->jqcal->add_listener('date_end');
			$GLOBALS['phpgw']->jqcal->add_listener('due_date');
			$GLOBALS['phpgw']->jqcal->add_listener('billing_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('billing_end_date');

			$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($contract->get_location_id());
			$current_contract_type_id = $contract->get_contract_type_id();
			if (strcmp($responsibility_area, "contract_type_eksternleie") != 0)
			{
				$contract_type_options[] = array(
					'id' => '',
					'name' => lang('Ingen type'),
					'selected' => 0
				);
			}
			//rental_socontract::get_instance()->get_contract_types($contract->get_location_id());
			$contract_types = rental_socontract::get_instance()->get_contract_types($contract->get_location_id());
			foreach ($contract_types as $contract_type_id => $contract_type_label)
			{
				if ($contract_type_id)
				{
					$contract_type_options[] = array(
						'id' => $contract_type_id,
						'name' => lang($contract_type_label),
						'selected' => $contract_type_id == $current_contract_type_id ? 1 : 0
					);
				}
			}

			$location_name = $contract->get_field_of_responsibility_name();
			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $location_name, 'rental');
			$executive_officer_options[] = array('id' => '', 'name' => lang('nobody'), 'selected' => 0);
			foreach ($accounts as $account)
			{
				$executive_officer_options[] = array(
					'id' => $account['account_id'],
					'name' => $GLOBALS['phpgw']->accounts->get($account['account_id'])->__toString(),
					'selected' => ($account['account_id'] == $executive_officer) ? 1 : 0
				);
			}

			$billing_terms = rental_sobilling::get_instance()->get_billing_terms();
			$billing_term_options = array();
			foreach ($billing_terms as $term_id => $term_title)
			{
				$billing_term_options[] = array(
					'id' => $term_id,
					'name' => lang($term_title),
					'selected' => $term_id == $current_term_id ? 1 : 0
				);
			}

			$cur_responsibility_id = $contract->get_responsibility_id();
			$contract_responsibility_arr = $contract->get_responsibility_arr($cur_responsibility_id);
			$responsibility_options = array();
			if ($contract_responsibility_arr)
			{
				foreach ($contract_responsibility_arr as $contract_responsibility)
				{
					$responsibility_options[] = array(
						'id' => $contract_responsibility['id'],
						'name' => $contract_responsibility['name'],
						'selected' => ($contract_responsibility['selected'] == 1) ? 1 : 0
					);
				}
			}

			$current_security_type = $contract->get_security_type();
			$security_options[] = array(
				'id' => '',
				'name' => lang('nobody'),
				'selected' => 0);
			$security_options[] = array(
				'id' => rental_contract::SECURITY_TYPE_BANK_GUARANTEE,
				'name' => lang('bank_guarantee'),
				'selected' => (($current_security_type == rental_contract::SECURITY_TYPE_BANK_GUARANTEE) ? 1 : 0));
			$security_options[] = array(
				'id' => rental_contract::SECURITY_TYPE_DEPOSIT,
				'name' => lang('deposit'),
				'selected' => (($current_security_type == rental_contract::SECURITY_TYPE_DEPOSIT) ? 1 : 0));
			$security_options[] = array(
				'id' => rental_contract::SECURITY_TYPE_ADVANCE,
				'name' => lang('advance'),
				'selected' => (($current_security_type == rental_contract::SECURITY_TYPE_ADVANCE) ? 1 : 0));
			$security_options[] = array(
				'id' => rental_contract::SECURITY_TYPE_OTHER_GUARANTEE,
				'name' => lang('other_guarantee'),
				'selected' => (($current_security_type == rental_contract::SECURITY_TYPE_OTHER_GUARANTEE) ? 1 : 0));

			$current_interval = $contract->get_adjustment_interval();
			$adjustment_interval_options[] = array(
				'id' => '1',
				'name' => '1 ' . lang('year'),
				'selected' => (($current_interval == '1') ? 1 : 0));
			$adjustment_interval_options[] = array(
				'id' => '2',
				'name' => '2 ' . lang('year'),
				'selected' => (($current_interval == '2') ? 1 : 0));
			$adjustment_interval_options[] = array(
				'id' => '10',
				'name' => '10 ' . lang('year'),
				'selected' => (($current_interval == '10') ? 1 : 0));

			$current_share = $contract->get_adjustment_share();
			$adjustment_share_options[] = array('id' => '100', 'name' => '100%', 'selected' => (($current_share == '100') ? 1 : 0));
			$adjustment_share_options[] = array('id' => '90', 'name' => '90%', 'selected' => (($current_share == '90') ? 1 : 0));
			$adjustment_share_options[] = array('id' => '80', 'name' => '80%', 'selected' => (($current_share == '80') ? 1 : 0));
			$adjustment_share_options[] = array('id' => '67', 'name' => '67%', 'selected' => (($current_share == '67') ? 1 : 0));

			$start_date = ($contract->get_contract_date() && $contract->get_contract_date()->has_start_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_start_date()) : '';
			$end_date = ($contract->get_contract_date() && $contract->get_contract_date()->has_end_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_end_date()) : '';
			$due_date = ($contract->get_due_date()) ? date($this->dateFormat, $contract->get_due_date()) : '';

			if ($start_date == "") {
				$date = phpgw::get_var('start_date');
				$date = str_replace("-", "/", $date);

				$start_date = ($date) ? ($date) : '';
			}

			$_contract_id = $contract->get_id();
			if (empty($_contract_id))
			{
				$account_in = rental_socontract::get_instance()->get_default_account($contract->get_location_id(), true);
				$account_out = rental_socontract::get_instance()->get_default_account($contract->get_location_id(), false);
				$project_id = rental_socontract::get_instance()->get_default_project_number($contract->get_location_id(), false);
			}
			else
			{
				$account_in = $contract->get_account_in();
				$account_out = $contract->get_account_out();
				$project_id = $contract->get_project_id();
			}

			$billing_start_date = ($contract->get_billing_start_date()) ? date($this->dateFormat, $contract->get_billing_start_date()) : '';
			$billing_end_date = ($contract->get_billing_end_date()) ? date($this->dateFormat, $contract->get_billing_end_date()) : '';

			$link_save = array
				(
				'menuaction' => 'rental.uicontract.save'
			);

			$link_cancel = array
				(
				'menuaction' => 'rental.uicontract.index',
			);
			$cancel_text = 'cancel';

			if ($adjustment_id)
			{
				$link_cancel = array('menuaction' => 'rental.uiadjustment.show_affected_contracts',
					'id' => $adjustment_id);
				$cancel_text = 'contract_regulation_back';
			}

			$tabs = array();
			$tabs['details'] = array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';

			$datatable_def = array();

			if ($contract_id)
			{
				$datatable_def[] = array
					(
					'container' => 'datatable-container_0',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uicontract.get_total_price',
							'contract_id' => $contract_id, 'phpgw_return_as' => 'json'))),
					'ColumnDefs' => array(
						array('key' => 'total_price', 'label' => lang('total_price'), 'className' => 'right',
							'sortable' => false, 'formatter' => 'formatterPrice'),
						array('key' => 'area', 'label' => lang('area'), 'className' => 'right',
							'sortable' => false,
							'formatter' => 'formatterArea'),
						array('key' => 'price_per_unit', 'label' => lang('price_per_unit'), 'className' => 'right',
							'sortable' => false, 'formatter' => 'formatterPrice')
					),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$tabs['composite'] = array('label' => lang('Composite'), 'link' => '#composite',
					'function' => 'get_composite_data()');
				$tabs['parties'] = array('label' => lang('Parties'), 'link' => '#parties',
					'function' => 'get_parties_data()');
				$tabs['price'] = array('label' => lang('Price'), 'link' => '#price', 'function' => 'get_price_data()');
				$tabs['invoice'] = array('label' => lang('Invoice'), 'link' => '#invoice',
					'function' => 'initial_invoice_data()');
				$tabs['documents'] = array('label' => lang('Documents'), 'link' => '#documents');
				$tabs['notifications'] = array('label' => lang('Notifications'), 'link' => '#notifications');

				$link_included_composites = json_encode(self::link(array('menuaction' => 'rental.uicomposite.query',
						'type' => 'included_composites', 'editable' => true, 'contract_id' => $contract_id,
						'phpgw_return_as' => 'json')));
				$link_not_included_composites = json_encode(self::link(array('menuaction' => 'rental.uicomposite.query',
						'type' => 'not_included_composites', 'editable' => true, 'contract_id' => $contract_id,
						'phpgw_return_as' => 'json')));

				$link_included_parties = json_encode(self::link(array('menuaction' => 'rental.uiparty.query',
						'type' => 'included_parties', 'editable' => true, 'contract_id' => $contract_id,
						'phpgw_return_as' => 'json')));
				$link_not_included_parties = json_encode(self::link(array('menuaction' => 'rental.uiparty.query',
						'type' => 'not_included_parties', 'editable' => true, 'contract_id' => $contract_id,
						'phpgw_return_as' => 'json')));

				$link_included_price_items = json_encode(self::link(array('menuaction' => 'rental.uiprice_item.query',
						'type' => 'included_price_items', 'editable' => true, 'contract_id' => $contract_id,
						'phpgw_return_as' => 'json')));
				$link_not_included_price_items = json_encode(self::link(array('menuaction' => 'rental.uiprice_item.query',
						'type' => 'not_included_price_items', 'editable' => true, 'contract_id' => $contract_id,
						'responsibility_id' => $contract->get_location_id(), 'phpgw_return_as' => 'json')));

				$link_upload_document = json_encode(self::link(array('menuaction' => 'rental.uidocument.add',
						'contract_id' => $contract_id, 'phpgw_return_as' => 'json')));

				$tableDef_composite = $this->_get_tableDef_composite($mode, $contract_id);
				$tableDef_party = $this->_get_tableDef_party($mode, $contract_id);
				$tableDef_price = $this->_get_tableDef_price($mode, $contract_id);
				$tableDef_invoice = $this->_get_tableDef_invoice($mode, $contract_id);
				$tableDef_document = $this->_get_tableDef_document($mode, $contract_id, $contract->has_permission(PHPGW_ACL_EDIT));
				$tableDef_notification = $this->_get_tableDef_notification($mode, $contract_id);

				$datatable_def = array_merge($datatable_def, $tableDef_composite, $tableDef_party, $tableDef_price, $tableDef_invoice, $tableDef_document, $tableDef_notification);

				/*				 * ***************************** composite filters */
				$composite_search_options = array
					(
					array('id' => 'all', 'name' => lang('all'), 'selected' => 1),
					array('id' => 'name', 'name' => lang('name'), 'selected' => 0),
					array('id' => 'address', 'name' => lang('address'), 'selected' => 0),
					array('id' => 'location_code', 'name' => lang('object_number'), 'selected' => 0)
				);

				$furnish_types_options = array();
				if (!empty($this->config->config_data['contract_furnished_status']))
				{
					$furnish_types_arr = rental_composite::get_furnish_types();
					array_unshift($furnish_types_options, array('id' => '4', 'name' => lang('Alle')));
					foreach ($furnish_types_arr as $id => $title)
					{
						$furnish_types_options[] = array('id' => $id, 'name' => $title);
					}
				}
				$active_options = array
					(
					array('id' => 'both', 'name' => lang('all')),
					array('id' => 'active', 'name' => lang('in_operation'), 'selected' => 1),
					array('id' => 'non_active', 'name' => lang('out_of_operation')),
				);
				$has_contract_options = array
					(
					array('id' => 'both', 'name' => lang('all')),
					array('id' => 'has_contract', 'name' => lang('composite_has_contract')),
					array('id' => 'has_no_contract', 'name' => lang('composite_has_no_contract')),
				);
				/*				 * ******************************************************************************** */

				/*				 * ***************************** party filters */
				$party_search_options = array
					(
					array('id' => 'all', 'name' => lang('all')),
					array('id' => 'name', 'name' => lang('name')),
					array('id' => 'address', 'name' => lang('address')),
					array('id' => 'identifier', 'name' => lang('identifier')),
					array('id' => 'reskontro', 'name' => lang('reskontro')),
					array('id' => 'result_unit_number', 'name' => lang('result_unit_number')),
				);

				$party_types = rental_socontract::get_instance()->get_fields_of_responsibility();
				$party_types_options = array();
				array_unshift($party_types_options, array('id' => 'all', 'name' => lang('all')));
				foreach ($party_types as $id => $label)
				{
					$party_types_options[] = array('id' => $id, 'name' => lang($label));
				}
				$status_options = array
					(
					array('id' => 'all', 'name' => lang('not_available_nor_hidden')),
					array('id' => 'active', 'name' => lang('available_for_pick')),
					array('id' => 'inactive', 'name' => lang('hidden_for_pick')),
				);
				/*				 * ******************************************************************************** */

				/*				 * ***************************** notification form */
				$GLOBALS['phpgw']->jqcal->add_listener('date_notification');

				$notification_recurrence_options[] = array('id' => rental_notification::RECURRENCE_NEVER,
					'name' => lang('never'), 'selected' => 1);
				$notification_recurrence_options[] = array('id' => rental_notification::RECURRENCE_ANNUALLY,
					'name' => lang('annually'), 'selected' => 0);
				$notification_recurrence_options[] = array('id' => rental_notification::RECURRENCE_MONTHLY,
					'name' => lang('monthly'), 'selected' => 0);
				$notification_recurrence_options[] = array('id' => rental_notification::RECURRENCE_WEEKLY,
					'name' => lang('weekly'), 'selected' => 0);

				$accounts_users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_READ, 'run', 'rental');
				$users[] = array('id' => $GLOBALS['phpgw_info']['user']['account_id'], 'name' => lang('target_me'));
				foreach ($accounts_users as $account)
				{
					if ($account['account_id'] != $GLOBALS['phpgw_info']['user']['account_id'])
					{
						$users[] = array('id' => $account['account_id'], 'name' => $GLOBALS['phpgw']->accounts->get($account['account_id'])->__toString());
					}
				}
				$notification_user_group_options[] = array('label' => lang('notification_optgroup_users'),
					'options' => $users);

				$accounts_groups = $GLOBALS['phpgw']->accounts->get_list('groups');
				foreach ($accounts_groups as $account)
				{
					$groups[] = array('id' => $account->id, 'name' => $account->firstname);
				}
				$notification_user_group_options[] = array('label' => lang('notification_optgroup_groups'),
					'options' => $groups);

				$field_of_responsibility_options = array();
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach ($types as $id => $label)
				{
					$names = $this->locations->get_name($id);
					if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
					{
						$selected = ($id == $contract->get_location_id()) ? 1 : 0;
						$field_of_responsibility_options[] = array('id' => $id, 'name' => lang($label),
							'selected' => $selected);
					}
				}
				/*				 * ******************************************************************************** */

				/*				 * ***************************** invoice filters */
				$invoices = rental_soinvoice::get_instance()->get(0, 0, '', false, '', '', array(
					'contract_id' => $contract->get_id()));
				if ($invoices != null && count($invoices) > 0)
				{
					foreach ($invoices as $invoice)
					{
						$serial = $invoice->get_serial_number();
						$serial_number = isset($serial) ? " - " . $invoice->get_serial_number() : "";
						$invoice_options[] = array('id' => $invoice->get_id(), 'name' => "{$invoice->get_billing_title()} - " . date($this->dateFormat, $invoice->get_timestamp_created()) . " - " . number_format($invoice->get_total_sum(), $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator) . " {$this->currency_suffix}" . $serial_number);
					}
				}
				else
				{
					$invoice_options[] = array('id' => '-1', 'name' => lang('No invoices were found'));
				}
				/*				 * ******************************************************************************** */

				/*				 * ***************************** document filters */
				$document_types = rental_sodocument::get_instance()->get_document_types();
				$document_types_options = array();
				foreach ($document_types as $id => $label)
				{
					$document_types_options[] = array('id' => $id, 'name' => lang($label));
				}

				$document_search_options[] = array('id' => 'all', 'name' => lang('all'));
				$document_search_options[] = array('id' => 'title', 'name' => lang('document_title'));
				$document_search_options[] = array('id' => 'name', 'name' => lang('document_name'));
				/*				 * ******************************************************************************** */

				$moveout_gross = createObject('rental.bomoveout')->read(array('filters' => array('contract_id' => $contract_id)));
				$moveout = $moveout_gross['results'] ? $moveout_gross['results'][0] : array();
				if($moveout)
				{
					$moveout['url'] = self::link(array('menuaction' => 'rental.uimoveout.view','id' => $moveout['id']));
				}
				else
				{
					$moveout['new_report'] = self::link(array('menuaction' => 'rental.uimoveout.edit','contract_id' => $contract_id));
				}
				$movein_gross = createObject('rental.bomovein')->read(array('filters' => array('contract_id' => $contract_id)));
				$movein = $movein_gross['results'] ? $movein_gross['results'][0] : array();
				if($movein)
				{
					$movein['url'] = self::link(array('menuaction' => 'rental.uimovein.view','id' => $movein['id']));
				}
				else
				{
					$movein['new_report'] = self::link(array('menuaction' => 'rental.uimovein.edit','contract_id' => $contract_id));
				}

			}

			$code = <<<JS
				var thousandsSeparator = '$this->thousandsSeparator';
				var decimalSeparator = '$this->decimalSeparator';
				var decimalPlaces = '$this->decimalPlaces';
				var currency_suffix = '$this->currency_suffix';
				var area_suffix = '$this->area_suffix';
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);
			$override_adjustment_start = $contract->get_override_adjustment_start();

			$data = array
				(
				'datatable_def' => $datatable_def,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_save),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_cancel),
				'lang_save' => lang('save'),
				'lang_cancel' => lang($cancel_text),
				'value_contract_number' => $contract->get_old_contract_id(),
				'value_parties' => $contract->get_party_name_as_list(),
				'value_last_updated' => $created,
				'value_name' => $created_by,
				'value_composite' => $contract->get_composite_name_as_list(),
				'value_field_of_responsibility' => lang($contract->get_contract_type_title()),
				'list_contract_type' => array('options' => $contract_type_options),
				'list_executive_officer' => array('options' => $executive_officer_options),
				'value_date_start' => $start_date,
				'value_date_end' => $end_date,
				'value_due_date' => $due_date,
				'value_invoice_header' => $contract->get_invoice_header(),
				'list_billing_term' => array('options' => $billing_term_options),
				'value_billing_start' => $billing_start_date,
				'value_billing_end' => $billing_end_date,
				'value_reference' => $contract->get_reference(),
				'value_customer_order_id' => $contract->get_customer_order_id(),
				'list_responsibility' => array('options' => $responsibility_options),
				'value_responsibility_id' => $cur_responsibility_id,
				'value_service' => $contract->get_service_id(),
				'value_account_in' => $account_in,
				'value_account_out' => $account_out,
				'value_project_id' => $project_id,
				'list_security' => array('options' => $security_options),
				'security_amount_simbol' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'value_security_amount' => $contract->get_security_amount(),
				'value_rented_area' => $contract->get_rented_area(),
				'rented_area_simbol' => $this->area_suffix,
				'is_adjustable' => $contract->is_adjustable(),
				'list_adjustment_interval' => array('options' => $adjustment_interval_options),
				'list_adjustment_share' => array('options' => $adjustment_share_options),
				'value_adjustment_year' => $contract->get_adjustment_year(),
				'value_override_adjustment_start' => $override_adjustment_start ? $override_adjustment_start : '',
				'value_comment' => $contract->get_comment(),
				'value_publish_comment' => $contract->get_publish_comment(),
				'location_id' => $contract->get_location_id(),
				'contract_id' => $contract_id,
				'mode' => $mode,
				'link_included_composites' => $link_included_composites,
				'link_not_included_composites' => $link_not_included_composites,
				'link_included_parties' => $link_included_parties,
				'link_not_included_parties' => $link_not_included_parties,
				'link_included_price_items' => $link_included_price_items,
				'link_not_included_price_items' => $link_not_included_price_items,
				'list_composite_search' => array('options' => $composite_search_options),
				'list_furnish_types' => array('options' => $furnish_types_options),
				'list_active' => array('options' => $active_options),
				'list_has_contract' => array('options' => $has_contract_options),
				'list_party_search' => array('options' => $party_search_options),
				'list_party_types' => array('options' => $party_types_options),
				'list_status' => array('options' => $status_options),
				'list_invoices' => array('options' => $invoice_options),
				'list_notification_recurrence' => array('options' => $notification_recurrence_options),
				'list_notification_user_group' => array('option_group' => $notification_user_group_options),
				'list_field_of_responsibility' => array('options' => $field_of_responsibility_options),
				'list_document_types' => array('options' => $document_types_options),
				'list_document_search' => array('options' => $document_search_options),
				'list_consistency_warnings' => $list_consistency_warnings,
				'link_upload_document' => $link_upload_document,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'img_cal' => json_encode($GLOBALS['phpgw']->common->image('phpgwapi', 'cal')),
				'dateformat' => str_ireplace(array('d', 'm', 'y'), array('dd', 'mm', 'yy'), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
				'moveout' => $moveout,
				'movein' => $movein
			);

			//$appname	=  $this->location_info['name'];
			//$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw']->translation->translate($this->location_info['acl_app'], array(), false, $this->location_info['acl_app']) . "::{$appname}::{$function_msg}";
			phpgwapi_jquery::formvalidator_generate(array('date'));
			self::add_javascript('rental', 'rental', 'contract.edit.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('contract', 'datatable_inline'), array('edit' => $data));
		}

		/**
		 * Create a new empty contract
		 */
		public function add()
		{
			$location_id = phpgw::get_var('location_id');
			if (isset($location_id) && $location_id > 0)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit',
					'location_id' => $location_id));
			}
		}
        
        /**
		 * Create a new contract tied to the composite provided in the composite_id parameter
		 */
		public function add_from_composite()
		{
			$contract = new rental_contract();
			$contract->set_location_id(phpgw::get_var('responsibility_id'));
			$contract->set_account_in(rental_socontract::get_instance()->get_default_account($contract->get_location_id(), true));
			$contract->set_account_out(rental_socontract::get_instance()->get_default_account($contract->get_location_id(), false));
			$contract->set_executive_officer_id($GLOBALS['phpgw_info']['user']['account_id']);

			/* $config	= CreateObject('phpgwapi.config','rental');
			  $config->read(); */
			$default_billing_term = $this->config->config_data['default_billing_term'];

			$contract->set_term_id($default_billing_term);

			$units = rental_socomposite::get_instance()->get_single(phpgw::get_var('id'))->get_units();
			$location_code = $units[0]->get_location()->get_location_code();

			$args = array
				(
				'acl_location' => '.contract',
				'location_code' => $location_code,
				'contract' => &$contract
			);

			$hook_helper = CreateObject('rental.hook_helper');
			$hook_helper->add_contract_from_composite($args);

			//		_debug_array($contract); die();

			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$so_contract = rental_socontract::get_instance();
				$db_contract = $so_contract->get_db();
				$db_contract->transaction_begin();
				if ($so_contract->store($contract))
				{
					// Add standard price items to contract
					if ($contract->get_location_id() && ($this->isExecutiveOfficer() || $this->isAdministrator()))
					{
						$so_price_item = rental_soprice_item::get_instance();
						//get default price items for location_id
						$default_price_items = $so_contract->get_default_price_items($contract->get_location_id());

						foreach ($default_price_items as $price_item_id)
						{
							$so_price_item->add_price_item($contract->get_id(), $price_item_id);
						}
					}
					// Add that composite to the new contract
					$success = $so_contract->add_composite($contract->get_id(), phpgw::get_var('id'));
					if ($success)
					{
						$parameters = array(
							'menuaction' => 'rental.uicontract.edit',
							'id' => $contract->get_id()
						);

						$date = (phpgw::get_var('date')) ? phpgw::get_var('date') : '';
						if ($date != "")
						{
							$parameters['start_date'] = $date;
						}

						$db_contract->transaction_commit();
						$comp_name = rental_socomposite::get_instance()->get_single(phpgw::get_var('id'))->get_name();
						$message = lang('messages_new_contract_from_composite') . ' ' . $comp_name;
						phpgwapi_cache::message_set($message, 'message');
						$GLOBALS['phpgw']->redirect_link('/index.php', $parameters);
					}
					else
					{
						$db_contract->transaction_abort();
						phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit',
							'id' => $contract->get_id()));
					}
				}
				else
				{
					$db_contract->transaction_abort();
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit',
						'id' => $contract->get_id()));
				}
			}

			// If no executive officer
			phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_new_contract'));
		}

		/**
		 * Create a new contract based on an existing contract
		 */
		public function copy_contract()
		{
			$adjustment_id = (int)phpgw::get_var('adjustment_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single(phpgw::get_var('id'));
			$old_contract_old_id = $contract->get_old_contract_id();
			$db_contract = $so_contract->get_db();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$db_contract->transaction_begin();
				//reset id's and contract dates
				$contract->set_id(null);
				$contract->set_old_contract_id(null);
				$contract->set_contract_date(null);
				$contract->set_due_date(null);
				$contract->set_billing_start_date(null);
				$contract->set_billing_end_date(null);
				if ($so_contract->store($contract))
				{
					// copy the contract
					$success = $so_contract->copy_contract($contract->get_id(), phpgw::get_var('id'));
					if ($success)
					{
						$db_contract->transaction_commit();
						$message = lang(messages_new_contract_copied) . ' ' . $old_contract_old_id;
						phpgwapi_cache::message_set($message, 'message');
						//$this->edit(array('contract_id'=>$contract->get_id(), 'adjustment_id' => $adjustment_id));
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit',
							'id' => $contract->get_id(), 'adjustment_id' => $adjustment_id));
					}
					else
					{
						$db_contract->transaction_abort();
						phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
						//$this->edit(array('contract_id'=>$contract->get_id(), 'adjustment_id' => $adjustment_id));
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit',
							'id' => $contract->get_id(), 'adjustment_id' => $adjustment_id));
					}
				}
				else
				{
					$db_contract->transaction_abort();
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
					//$this->edit(array('contract_id'=>$contract->get_id(), 'adjustment_id' => $adjustment_id));
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit',
						'id' => $contract->get_id(), 'adjustment_id' => $adjustment_id));
				}
			}

			// If no executive officer
			phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_new_contract'));
		}

		/**
		 * Public function. Add a party to a contract
		 * @param HTTP::contract_id	the contract id
		 * @param HTTP::party_id the party id
		 * @return true if successful, false otherwise
		 */
		public function add_party()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$list_party_id = phpgw::get_var('party_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				foreach ($list_party_id as $party_id)
				{
					$result = $so_contract->add_party($contract_id, $party_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'party ' . $party_id . ' ' . lang('has been added'));
					}
					else
					{
						$message['error'][] = array('msg' => 'party ' . $party_id . ' ' . lang('not added'));
					}
				}
			}
			return $message;
		}

		/**
		 * Public function. Remove a party from a contract
		 * @param HTTP::contract_id the contract id
		 * @param HTTP::party_id the party id
		 * @return true if successful, false otherwise
		 */
		public function remove_party()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$list_party_id = phpgw::get_var('party_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				foreach ($list_party_id as $party_id)
				{
					$result = $so_contract->remove_party($contract_id, $party_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'party ' . $party_id . ' ' . lang('has been removed'));
					}
					else
					{
						$message['error'][] = array('msg' => 'party ' . $party_id . ' ' . lang('not removed'));
					}
				}
			}
			return $message;
		}

		/**
		 * Public function. Set the payer on a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::party_id	the party id
		 * @return true if successful, false otherwise
		 */
		public function set_payer()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);

			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$result = $so_contract->set_payer($contract_id, $party_id);
				if ($result)
				{
					$message['message'][] = array('msg' => lang('party has been saved'));
				}
				else
				{
					$message['error'][] = array('msg' => lang('party not saved'));
				}
			}
			return $message;
		}

		/**
		 * Public function. Add a composite to a contract.
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::composite_id	the composite id
		 * @return bool true if successful, false otherwise
		 */
		public function add_composite()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$list_composite_id = phpgw::get_var('composite_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				foreach ($list_composite_id as $composite_id)
				{
					$result = $so_contract->add_composite($contract_id, $composite_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'composite ' . $composite_id . ' ' . lang('has been added'));
					}
					else
					{
						$message['error'][] = array('msg' => 'composite ' . $composite_id . ' ' . lang('not added'));
					}
				}
			}
			return $message;
		}

		/**
		 * Public function. Remove a composite from a contract.
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::composite_id	the composite id
		 * @return bool true if successful, false otherwise
		 */
		public function remove_composite()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$list_composite_id = phpgw::get_var('composite_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if (isset($contract) && $contract->has_permission(PHPGW_ACL_EDIT))
			{
				foreach ($list_composite_id as $composite_id)
				{
					$result = $so_contract->remove_composite($contract_id, $composite_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'composite ' . $composite_id . ' ' . lang('has been removed'));
					}
					else
					{
						$message['error'][] = array('msg' => 'composite ' . $composite_id . ' ' . lang('not removed'));
					}
				}
			}
			return $message;
		}

		/**
		 * Public function. Add a price item to a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::price_item_id	the price item id
		 * @return bool true if successful, false otherwise
		 */
		public function add_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$list_price_item_id = phpgw::get_var('price_item_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				//return rental_soprice_item::get_instance()->add_price_item($contract_id, $price_item_id, $factor);
				foreach ($list_price_item_id as $price_item_id)
				{
					$result = rental_soprice_item::get_instance()->add_price_item($contract_id, $price_item_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'price_item ' . $price_item_id . ' ' . lang('has been added'));
					}
					else
					{
						$message['error'][] = array('msg' => 'price_item ' . $price_item_id . ' ' . lang('not added'));
					}
				}
			}
			return $message;
		}

		/**
		 * Public function. Remove a price item from a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::price_item_id	the price item id
		 * @return bool true if successful, false otherwise
		 */
		public function remove_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$list_price_item_id = phpgw::get_var('price_item_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				foreach ($list_price_item_id as $price_item_id)
				{
					$result = rental_soprice_item::get_instance()->remove_price_item($contract_id, $price_item_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'price_item ' . $price_item_id . ' ' . lang('has been removed'));
					}
					else
					{
						$message['error'][] = array('msg' => 'price_item ' . $price_item_id . ' ' . lang('not removed'));
					}
				}
			}
			return $message;
		}

		/**
		 * Public function. Reset a price item on a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::price_item_id	the price item id
		 * @return bool true if successful, false otherwise
		 */
		public function reset_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$list_price_item_id = phpgw::get_var('price_item_id');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				//return rental_soprice_item::get_instance()->reset_contract_price_item($price_item_id);
				foreach ($list_price_item_id as $price_item_id)
				{
					$result = rental_soprice_item::get_instance()->reset_contract_price_item($price_item_id);
					if ($result)
					{
						$message['message'][] = array('msg' => 'price_item ' . $price_item_id . ' ' . lang('has been reseted'));
					}
					else
					{
						$message['error'][] = array('msg' => 'price_item ' . $price_item_id . ' ' . lang('not reseted'));
					}
				}
			}
			return $message;
		}

		public function add_notification()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$account_id = phpgw::get_var('notification_target');
			$location_id = phpgw::get_var('notification_location');
			$date = phpgw::get_var('date_notification');

			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			$message = array();
			if ($contract->has_permission(PHPGW_ACL_EDIT))
			{
				if ($date)
				{
					$date = phpgwapi_datetime::date_to_timestamp($date);
				}
				$notification = new rental_notification(-1, $account_id, $location_id, $contract_id, $date, phpgw::get_var('notification_message'), phpgw::get_var('notification_recurrence'));
				if (rental_sonotification::get_instance()->store($notification))
				{
					$message['message'][] = array('msg' => 'notification ' . lang('has been added'));
				}
				else
				{
					$message['error'][] = array('msg' => 'notification ' . lang('not added'));
				}
			}
			return $message;
		}

		public function get_total_price()
		{
			$draw = phpgw::get_var('draw', 'int');
			$so_contract = rental_socontract::get_instance();
			$so_contract_price_item = rental_socontract_price_item::get_instance();

			$contract_id = (int)phpgw::get_var('contract_id');
			$total_price = $so_contract_price_item->get_total_price($contract_id);
			$contract = $so_contract->get_single($contract_id);
			$area = $contract->get_rented_area();

			if (!empty($area) && !empty($total_price))
			{
				$price_per_unit = $total_price / $area;
			}
			else
			{
				$total_price = 0;
				$area = 0;
				$price_per_unit = 0;
			}

			$result_array[] = array('total_price' => $total_price, 'area' => $area, 'price_per_unit' => $price_per_unit);

			$result_data = array('results' => $result_array);
			$result_data['total_records'] = count($result_array);
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Note: Used?
		 * @return array
		 */
		public function get_max_area()
		{
			$draw = phpgw::get_var('draw', 'int');
			$contract_id = (int)phpgw::get_var('contract_id');
			$total_price = rental_socontract_price_item::get_instance()->get_max_area($contract_id);
			$result_array = array('max_area' => $max_area);
			$result_data = array('results' => $result_array, 'total_records' => 1, 'draw' => $draw);
			return $this->jquery_results($result_data, 'total_records', 'results');
		}

		/**
		 *
		 * Public function scans the contract template directory for pdf contract templates
		 */
		public function get_pdf_templates()
		{
			$get_template_config = true;
			$files = scandir('rental/templates/base/pdf/');
			foreach ($files as $file)
			{
				$ending = substr($file, -3, 3);
				if ($ending == 'php')
				{
					include 'rental/templates/base/pdf/' . $file;
					$template_files = array($template_name, $file);
					$this->pdf_templates[] = $template_files;
				}
			}
		}



		/**
		 * Sending email - consider the workbench instead
		 * run as cron-job
		 */
		public function notify_on_expire(  )
		{
			// Queries that depend on areas of responsibility
			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			$ids = array();
			$read_access = array();
			foreach ($types as $id => $label)
			{
				$names = $this->locations->get_name($id);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					$ids[] = $id;
				}
			}
			$comma_seperated_ids = implode(',', $ids);
			$filters = array('contract_status' => 'under_dismissal', 'contract_type' => $comma_seperated_ids);

			$candidates = array();
			$candidates = rental_socontract::get_instance()->get(0, 0, '', false, '', '', $filters);

			$notify_on_expire_email = $this->config->config_data['notify_on_expire_email'];
			$from_email = $this->config->config_data['from_email_setting'];
			$notify_reminder_days = $this->config->config_data['notify_reminder_days'];

			if(!$notify_on_expire_email)
			{
				throw new Exception(__CLASS__.'::' . __FUNCTION__.'() - missing email target');
			}

			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$do_notify = false;
			foreach ($candidates as $contract_id => $contract)
			{
				$notify_on_expire = $contract->get_notify_on_expire();

				if($notify_on_expire > 1)
				{
					continue;
				}

				$now = time();
				$end_date = $contract->get_contract_date()->get_end_date();
				$datediff = $end_date - $now;
				$days_to_expire =  floor($datediff / (60 * 60 * 24));

				_debug_array($days_to_expire);

				if ($notify_on_expire == 0 && ($notify_reminder_days > $days_to_expire)) // first time
				{
					$do_notify = true;
				}
				else if($notify_reminder_days >= $days_to_expire)// second time
				{
					$do_notify = true;
					$notify_on_expire = 1;
				}

				if($do_notify)
				{
					$subject = lang('contract %1 expires in %2 days', $contract_id, $days_to_expire);
					$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uicontract.edit',
							'id' => $contract_id), false, true) . '">' . $subject . '</a>';
					try
					{
						$rcpt = $GLOBALS['phpgw']->send->msg('email', $notify_on_expire_email, $subject, stripslashes($message), '', $cc, $bcc, $from_email, $from_email, 'html');
					}
					catch (Exception $exc)
					{
						phpgwapi_cache::message_set($exc->getMessage(),'error');
					}

					rental_socontract::get_instance()->set_notified_on_expire($contract_id, $notify_on_expire);
				}

			}
		}
	}