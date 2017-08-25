<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.socontract');

	class rental_uifrontpage extends rental_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental');
		}

		private function _get_tableDef_working()
		{
			$columns_def = array(
				array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'sortable' => true),
				array('key' => 'composite', 'label' => lang('composite'), 'sortable' => false),
				array('key' => 'last_edited_by_current_user', 'label' => lang('last_edited_by_current_user'),
					'sortable' => false),
				array('key' => 'last_updated', 'label' => lang('last_updated'), 'sortable' => true),
				array('key' => 'term_label', 'label' => lang('billing_term'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'total_price', 'label' => lang('total_price') . " ({$this->currency_suffix})", 'sortable' => false,
					'className' => 'right', 'formatter' => 'formatterPrice'),
				array('key' => 'rented_area', 'label' => lang('area') . " ({$this->area_suffix})", 'sortable' => false,
					'hidden' => false,
					'className' => 'right', 'formatter' => 'formatterArea'),
				array('key' => 'contract_status', 'label' => lang('contract_status'), 'sortable' => false,
					'hidden' => false, 'className' => 'center')
			);

			$tabletools[] = array
				(
				'my_name' => 'excelHtml5',
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'copy',
				'text' => lang('copy'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.copy_contract'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'show',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);
/*
			$tabletools[] = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uicontract.download',
					'type' => 'last_edited',
					'export' => true))
			);
*/
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uicontract.query',
						'type' => 'last_edited', 'editable' => false, 'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => $columns_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
//					array('allrows' => true),

				)
			);

			return $datatable_def;
		}

		private function _get_tableDef_executive_officer()
		{
			$columns_def = array(
				array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'sortable' => true),
				array('key' => 'composite', 'label' => lang('composite'), 'sortable' => true),
				array('key' => 'party', 'label' => lang('party'), 'sortable' => true),
				array('key' => 'date_start', 'label' => lang('date_start'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'date_end', 'label' => lang('date_end'), 'sortable' => true, 'className' => 'center'),
				array('key' => 'term_label', 'label' => lang('billing_term'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'total_price', 'label' => lang('total_price') . " ({$this->currency_suffix})", 'sortable' => false,
					'className' => 'right', 'formatter' => 'formatterPrice'),
				array('key' => 'rented_area', 'label' => lang('area') . " ({$this->area_suffix})", 'sortable' => false,
					'hidden' => false,
					'className' => 'right', 'formatter' => 'formatterArea'),
				array('key' => 'contract_status', 'label' => lang('contract_status'), 'sortable' => false,
					'className' => 'center')
			);

			$tabletools[] = array
				(
				'my_name' => 'excelHtml5',
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'show',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uicontract.download',
					'type' => 'contracts_for_executive_officer',
					'export' => true))
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'data' => json_encode(array()),
				'ColumnDefs' => $columns_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('allrows' => true),
				)
			);

			return $datatable_def;
		}

		private function _get_tableDef_contracts_under_dismissal()
		{
			$columns_def = array(
				array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'sortable' => true),
				array('key' => 'composite', 'label' => lang('composite'), 'sortable' => true),
				array('key' => 'party', 'label' => lang('party'), 'sortable' => true),
				array('key' => 'type', 'label' => lang('type'), 'sortable' => true),
				array('key' => 'date_end', 'label' => lang('date_end'), 'sortable' => true, 'className' => 'center'),
				array('key' => 'term_label', 'label' => lang('billing_term'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'total_price', 'label' => lang('total_price') . " ({$this->currency_suffix})", 'sortable' => false,
					'className' => 'right', 'formatter' => 'formatterPrice'),
				array('key' => 'rented_area', 'label' => lang('area') . " ({$this->area_suffix})", 'sortable' => false,
					'hidden' => false,
					'className' => 'right', 'formatter' => 'formatterArea'),
				array('key' => 'contract_status', 'label' => lang('contract_status'), 'sortable' => false,
					'className' => 'center')
			);

			$tabletools[] = array
				(
				'my_name' => 'excelHtml5',
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uicontract.download',
					'type' => 'ending_contracts',
					'export' => true))
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'data' => json_encode(array()),
				'ColumnDefs' => $columns_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('allrows' => true),
				)
			);

			return $datatable_def;
		}

		private function _get_tableDef_contracts_closing_due_date()
		{
			$columns_def = array(
				array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'sortable' => true),
				array('key' => 'composite', 'label' => lang('composite'), 'sortable' => true),
				array('key' => 'date_start', 'label' => lang('date_start'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'date_end', 'label' => lang('date_end'), 'sortable' => true, 'className' => 'center'),
				array('key' => 'term_label', 'label' => lang('billing_term'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'total_price', 'label' => lang('total_price') . " ({$this->currency_suffix})", 'sortable' => false,
					'className' => 'right', 'formatter' => 'formatterPrice'),
				array('key' => 'rented_area', 'label' => lang('area') . " ({$this->area_suffix})", 'sortable' => false,
					'hidden' => false,
					'className' => 'right', 'formatter' => 'formatterArea'),
				array('key' => 'contract_status', 'label' => lang('contract_status'), 'sortable' => false,
					'className' => 'center')
			);

			$tabletools[] = array
				(
				'my_name' => 'excelHtml5',
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uicontract.download',
					'type' => 'closing_due_date',
					'export' => true))
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_3',
				'requestUrl' => "''",
				'data' => json_encode(array()),
				'ColumnDefs' => $columns_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('allrows' => true),
				)
			);

			return $datatable_def;
		}

		private function _get_tableDef_terminated_contracts()
		{
			$columns_def = array(
				array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'sortable' => true),
				array('key' => 'composite', 'label' => lang('composite'), 'sortable' => true),
				array('key' => 'date_end', 'label' => lang('date_end'), 'sortable' => true, 'className' => 'center'),
				array('key' => 'term_label', 'label' => lang('billing_term'), 'sortable' => true,
					'className' => 'center'),
				array('key' => 'rented_area', 'label' => lang('area'), 'sortable' => false,
					'hidden' => false,
					'className' => 'right', 'formatter' => 'formatterArea'),
				array('key' => 'contract_status', 'label' => lang('contract_status'), 'sortable' => false,
					'className' => 'center')
			);

			$tabletools[] = array
				(
				'my_name' => 'excelHtml5',
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'download',
				'text' => lang('download'),
				'download' => self::link(array('menuaction' => 'rental.uicontract.download',
					'type' => 'terminated_contracts',
					'export' => true))
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_4',
				'requestUrl' => "''",
				'data' => json_encode(array()),
				'ColumnDefs' => $columns_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('allrows' => true),
				)
			);

			return $datatable_def;
		}

		private function _get_tableDef_notifications()
		{
			$columns_def = array(
				array('key' => 'date', 'label' => lang('date'), 'sortable' => false),
				array('key' => 'message', 'label' => lang('message'), 'sortable' => false),
				array('key' => 'recurrence', 'label' => lang('recurrence'), 'sortable' => false,
					'className' => 'center'),
				array('key' => 'name', 'label' => lang('user_or_group'), 'sortable' => false),
				array('key' => 'field_of_responsibility', 'label' => lang('field_of_responsibility'),
					'sortable' => false, 'className' => 'center')
			);

			$tabletools[] = array
				(
				'my_name' => 'view',
				'text' => lang('view_contract'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'contract_id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'excelHtml5',
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit_contract'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicontract.edit'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'contract_id'))))
			);

			$tabletools[] = array
				(
				'my_name' => 'remove_from_workbench',
				'text' => lang('remove_from_workbench'),
				'type' => 'custom',
				'custom_code' => "
						var oArgs = " . json_encode(array(
					'menuaction' => 'rental.uinotification.dismiss_notification',
					'phpgw_return_as' => 'json'
				)) . ";
						var parameters = " . json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id')))) . ";
						dismissNotification(oArgs, parameters);
					"
			);

			$tabletools[] = array
				(
				'my_name' => 'remove_from_all_workbenches',
				'text' => lang('remove_from_all_workbenches'),
				'type' => 'custom',
				'custom_code' => "
						var oArgs = " . json_encode(array(
					'menuaction' => 'rental.uinotification.dismiss_notification_for_all',
					'phpgw_return_as' => 'json'
				)) . ";
						var parameters = " . json_encode(array('parameter' => array(
						array('name' => 'id', 'source' => 'originated_from'),
						array('name' => 'contract_id', 'source' => 'contract_id')
				))) . ";
						dismissNotificationAll(oArgs, parameters);
					"
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_5',
				'requestUrl' => "''",
				'data' => json_encode(array()),
				'ColumnDefs' => $columns_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('allrows' => true),
				)
			);

			return $datatable_def;
		}

		public function query()
		{
			
		}

		public function index()
		{
			$tabs = array();
			$tabs['working_on'] = array('label' => lang('working_on'), 'link' => '#working_on');
			$tabs['executive_officer'] = array('label' => lang('executive_officer'),
				'link' => '#executive_officer',
				'function' => 'getData_executive_officer()');
			$tabs['contracts_under_dismissal'] = array('label' => lang('contracts_under_dismissal'),
				'link' => '#contracts_under_dismissal', 'function' => 'getData_contracts_under_dismissal()');
			$tabs['contracts_closing_due_date'] = array('label' => lang('contracts_closing_due_date'),
				'link' => '#contracts_closing_due_date', 'function' => 'getData_contracts_closing_due_date()');
			$tabs['terminated_contracts'] = array('label' => lang('terminated_contracts'),
				'link' => '#terminated_contracts', 'function' => 'getData_terminated_contracts()');
			$tabs['notifications'] = array('label' => lang('notifications'), 'link' => '#notifications',
				'function' => 'getData_notifications()');
			$active_tab = 'working_on';

			$tableDef_working = $this->_get_tableDef_working();
			$tableDef_executive_officer = $this->_get_tableDef_executive_officer();
			$tableDef_contracts_under_dismissal = $this->_get_tableDef_contracts_under_dismissal();
			$tableDef_contracts_closing_due_date = $this->_get_tableDef_contracts_closing_due_date();
			$tableDef_terminated_contracts = $this->_get_tableDef_terminated_contracts();
			$tableDef_notifications = $this->_get_tableDef_notifications();

			$datatable_def = array_merge(
				$tableDef_working, $tableDef_executive_officer, $tableDef_contracts_under_dismissal, $tableDef_contracts_closing_due_date, $tableDef_terminated_contracts, $tableDef_notifications
			);

			$params_executive_officer = json_encode(array('menuaction' => 'rental.uicontract.query',
				'type' => 'contracts_for_executive_officer', 'editable' => false, 'phpgw_return_as' => 'json'));
			$params_contracts_under_dismissal = json_encode(array('menuaction' => 'rental.uicontract.query',
				'type' => 'ending_contracts', 'editable' => false, 'phpgw_return_as' => 'json'));
			$params_contracts_closing_due_date = json_encode(array('menuaction' => 'rental.uicontract.query',
				'type' => 'closing_due_date', 'editable' => false, 'phpgw_return_as' => 'json'));
			$params_terminated_contracts = json_encode(array('menuaction' => 'rental.uicontract.query',
				'type' => 'terminated_contracts', 'editable' => false, 'phpgw_return_as' => 'json'));
			$params_notifications = json_encode(array('menuaction' => 'rental.uinotification.query',
				'type' => 'notifications_for_user', 'editable' => false, 'phpgw_return_as' => 'json'));

			$data = array
				(
				'datatable_def' => $datatable_def,
				'params_executive_officer' => $params_executive_officer,
				'params_contracts_under_dismissal' => $params_contracts_under_dismissal,
				'params_contracts_closing_due_date' => $params_contracts_closing_due_date,
				'params_terminated_contracts' => $params_terminated_contracts,
				'params_notifications' => $params_notifications,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$code = <<<JS
				var thousandsSeparator = '$this->thousandsSeparator';
				var decimalSeparator = '$this->decimalSeparator';
				var decimalPlaces = '$this->decimalPlaces';
				var currency_suffix = '$this->currency_suffix';
				var area_suffix = '$this->area_suffix';
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			self::add_javascript('rental', 'rental', 'frontpage.index.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('frontpage', 'datatable_inline'), array('edit' => $data));
		}
	}