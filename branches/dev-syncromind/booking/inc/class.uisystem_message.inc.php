<?php
	phpgw::import_class('booking.uicommon');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uisystem_message extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'show' => true,
			'edit' => true,
			'toggle_show_inactive' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bosystem_message');
			$this->allocation_bo = CreateObject('booking.boallocation');
			self::set_active_menu('booking::messages');
			$this->url_prefix = 'booking.uisystem_message';
			$this->module = 'booking';
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'booking_manual';
//			self::add_javascript('booking', 'base', 'datatable.js');
			phpgwapi_jquery::load_widget('autocomplete');

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'status',
								'text' => lang('Status') . ':',
								'list' => array(
									array(
										'id' => '',
										'name' => lang('All')
									),
									array(
										'id' => 'NEW',
										'name' => lang('NEW'),
										'selected' => 1
									),
									array(
										'id' => 'CLOSED',
										'name' => lang('CLOSED')
									)
								)
							),
							array(
								'type' => 'filter',
								'name' => 'type',
								'text' => lang('Type') . ':',
								'list' => array(
									array(
										'id' => '',
										'name' => lang('All')
									),
									array(
										'id' => 'message',
										'name' => lang('Message')
									),
									array(
										'id' => 'cancelation',
										'name' => lang('Cancelation')
									),
								)
							),
							array(
								'type' => 'autocomplete',
								'name' => 'building',
								'ui' => 'building',
								'text' => lang('Building') . ':',
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only messages assigned to me') : lang('Show all messages'),
								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => $this->module . '.uisystem_message.index',
						'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'JqueryPortico.formatLink'
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
							'key' => 'what',
							'label' => lang('What'),
							'sortable' => false
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact'),
							'sortable' => false
						),
						array(
							'key' => 'case_officer_name',
							'label' => lang('Case Officer'),
							'sortable' => false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			$data['datatable']['actions'][] = array();
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$this->db = & $GLOBALS['phpgw']->db;

            $current_user = (int)$this->current_account_id();
			$current_user_building_data = array();
			$sql = "SELECT object_id FROM bb_permission WHERE subject_id= {$current_user} AND role='case_officer';";
			$this->db->query($sql);
			while ($record = array_shift($this->db->resultSet))
			{
				$current_user_building_data[] = $record['object_id'];
			}

			$filters['building_id'] = $current_user_building_data;

			if (isset($_SESSION['showall']))
			{
				unset($filters['building_id']);
			}
			else
			{
				$filters['building_id'] = $current_user_building_data;
			}

			$testdata = phpgw::get_var('filter_building_id', 'int', 'REQUEST', null);
			if ($testdata != 0)
			{
				$filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('filter_building_id', 'int', 'REQUEST', null));
			}
			else
			{
				unset($filters['building_name']);
			}
			$testdata2 = phpgw::get_var('type', 'string', 'REQUEST');
			if ($testdata2 != '')
			{
				$filters['type'] = phpgw::get_var('type', 'string', 'REQUEST');
			}
			else
			{
				unset($filters['type']);
			}
			$testdata2 = phpgw::get_var('status', 'string', 'REQUEST');
			if ($testdata2 != '')
			{
				$filters['status'] = phpgw::get_var('status', 'string', 'REQUEST');
			}
			else
			{
				unset($filters['status']);
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', null),
				'query' => $search['value'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'filters' => $filters
			);

			$system_messages = $this->bo->so->read($params);
			array_walk($system_messages["results"], array($this, "_add_links"), $this->module . ".uisystem_message.show");


			foreach ($system_messages['results'] as &$system_message)
			{
				$building_case_officers_data = array();
				$building_case_officers = array();
				$sql = "SELECT account_id, account_lid, account_firstname, account_lastname FROM phpgw_accounts WHERE account_id IN (SELECT subject_id FROM bb_permission WHERE object_id=" . $system_message['building_id'] . " AND role='case_officer')";
				$this->db->query($sql);
				while ($record = array_shift($this->db->resultSet))
				{
					$building_case_officers_data[] = array(
						'account_id' => $record['account_id'],
						'account_lid' => $record['account_lid'],
						'account_name' => $record['account_firstname'] . " " . $record['account_lastname']
					);
					$building_case_officers[] = $record['account_id'];
				}

				$system_message['created'] = pretty_timestamp($system_message['created']);
				$system_message['type'] = lang($system_message['type']);
				$system_message['status'] = lang($system_message['status']);
				$system_message['modified'] = '';
				$system_message['activity_name'] = '';
				$system_message['contact_name'] = $system_message['name'];
				$system_message['case_officer_name'] = $for_case_officer_id;
				$system_message['what'] = $system_message['title'];
				if (strstr($system_message['what'], "%"))
				{
					$search = array('%2C', '%C3%85', '%C3%A5', '%C3%98', '%C3%B8', '%C3%86',
						'%C3%A6');
					$replace = array(',', 'Å', 'å', 'Ø', 'ø', 'Æ', 'æ');
					$system_message['what'] = str_replace($search, $replace, $system_message['what']);
				}

				while ($case_officer = array_shift($building_case_officers_data))
				{
					if ($system_message['case_officer_name'] = $case_officer['account_id'])
					{
						$system_message['case_officer_name'] = $case_officer['account_name'];
					}
				}
			}
			return $this->jquery_results($system_messages);
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');

			if ($id)
			{
				$system_message = $this->bo->read_single($id);
				$system_message['id'] = $id;
				$system_message['cancel_link'] = self::link(array('menuaction' => 'booking.uisystem_message.index'));
			}
			else
			{
				date_default_timezone_set("Europe/Oslo");
				$date = new DateTime(phpgw::get_var('date'));
				$system_message = array();
				$system_message['building_id'] = phpgw::get_var('building_id', 'int');
				$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
				$system_message['created'] = $date->format('Y-m-d  H:m');
				$system_message['cancel_link'] = self::link(array('menuaction' => 'booking.uisystem_message.index'));
			}


			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$system_message = array_merge($system_message, extract_values($_POST, array(
					'name' => 'string', 'time' => 'string', 'title' => 'string', 'message' => 'html',
					'phone' => 'string', 'email' => 'email')
				));
				if (!isset($system_message["Status"]))
				{
					$system_message['status'] = 'NEW';
				}
				if ($system_message['message'] == '')
				{
					$errors['system_message'] = lang('No message');
				}
				if (!$errors)
				{
					if ($id)
					{
						$receipt = $this->bo->update($system_message);
					}
					else
					{
						$receipt = $this->bo->add($system_message);
					}

					$this->redirect(array('menuaction' => 'booking.uisystem_message.edit', 'id' => $receipt['id'],
						'warnings' => $errors));
				}
			}
			$this->flash_form_errors($errors);

			self::rich_text_editor('field-message');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('System message'), 'link' => '#system_message');
			$active_tab = 'generic';

			$system_message['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::rich_text_editor('field_description');

			self::render_template('system_message_edit', array('system_message' => $system_message,
				'module' => $this->module));
		}

		public function show()
		{
			$id = phpgw::get_var('id', 'int');

			$system_message = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$system_message['system_messages_link'] = self::link(array('menuaction' => $this->module . '.uisystem_message.index'));
			$system_message['system_message_link'] = self::link(array('menuaction' => $this->module . '.uisystem_message.show',
					'id' => $system_message['system_message_id']));
			$system_message['back_link'] = self::link(array('menuaction' => $this->module . '.uisystem_message.index'));

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$_POST['create'] = date("Y-m-d H.i:s", phpgwapi_datetime::date_to_timestamp($_POST['created']));
				if ($_POST['status'] == 'CLOSED')
				{
					$system_message['status'] = 'CLOSED';
					$receipt = $this->bo->update($system_message);
					$this->redirect(array('menuaction' => 'booking.uisystem_message.show', 'id' => $receipt['id'],
						'warnings' => $errors));
				}
			}

			$system_message['created'] = pretty_timestamp($system_message['created']);
			$system_message['type'] = lang($system_message['type']);
			$system_message['status'] = lang($system_message['status']);

			$data = array(
				'system_message' => $system_message
			);
			$loggedin = (int)true; // FIXME: Some sort of authentication!

			$tabs = array();
			$tabs['generic'] = array('label' => lang('System message'), 'link' => '#system_message');
			$active_tab = 'generic';

			$system_message['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('system_message', array('system_message' => $system_message,
				'loggedin' => $loggedin));
		}
	}