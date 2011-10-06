<?php
	phpgw::import_class('booking.uicommon');

	class booking_uidashboard extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'	=> true,
			'toggle_show_all_dashboard_applications' => true,
			'toggle_show_all_dashboard_messages' => true,
		);
		
		const SHOW_ALL_DASHBOARD_APPLICATIONS_SESSION_KEY = "show_all_dashboard_applications";
		const SHOW_ALL_DASHBOARD_MESSAGES_SESSION_KEY = "show_all_dashboard_messages";

		public function __construct()
		{
         parent::__construct();
			$this->bo = CreateObject('booking.boapplication');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->system_message_bo = CreateObject('booking.bosystem_message');
			self::set_active_menu('booking::dashboard');
		}
		
		public function toggle_show_all_dashboard_applications()
		{
			if($this->show_all_dashboard_applications())
			{
				unset($_SESSION[self::SHOW_ALL_DASHBOARD_APPLICATIONS_SESSION_KEY]);

			} else {
				$_SESSION[self::SHOW_ALL_DASHBOARD_APPLICATIONS_SESSION_KEY] = true;
				unset($_SESSION[self::SHOW_ALL_DASHBOARD_MESSAGES_SESSION_KEY]);
			}
			$this->redirect(array('menuaction' => $this->url_prefix.'.index'));
		}
		
		public function show_all_dashboard_applications() {
			return array_key_exists(self::SHOW_ALL_DASHBOARD_APPLICATIONS_SESSION_KEY, $_SESSION);
		}

		public function toggle_show_all_dashboard_messages()
		{
			if($this->show_all_dashboard_messages())
			{
				unset($_SESSION[self::SHOW_ALL_DASHBOARD_MESSAGES_SESSION_KEY]);
			} else {
				$_SESSION[self::SHOW_ALL_DASHBOARD_MESSAGES_SESSION_KEY] = true;
				unset($_SESSION[self::SHOW_ALL_DASHBOARD_APPLICATIONS_SESSION_KEY]);
			}
			$this->redirect(array('menuaction' => $this->url_prefix.'.index'));
		}
		
		public function show_all_dashboard_messages() {
			return array_key_exists(self::SHOW_ALL_DASHBOARD_MESSAGES_SESSION_KEY, $_SESSION);
		}

		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'booking_manual';
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter', 
								'name' => 'status',
                                'text' => lang('Status').':',
                                'list' => array(
                                    array(
                                        'id' => 'not',
                                        'name' => lang('Not selected')
                                    ), 
                                    array(
                                        'id' => 'NEW',
                                        'name' => lang('NEW')
                                    ), 
                                    array(
                                        'id' => 'PENDING',
                                        'name' =>  lang('PENDING')
                                    ), 
                                    array(
                                        'id' => 'REJECTED',
                                        'name' => lang('REJECTED')
                                    ), 
                                    array(
                                        'id' => 'ACCEPTED',
                                        'name' => lang('ACCEPTED')
                                    ),
                                    array(
                                        'id' => 'CLOSED',
                                        'name' => lang('CLOSED')
                                    )
                                )
                            ),
							array('type' => 'autocomplete', 
								'name' => 'building',
								'ui' => 'building',
								'text' => lang('Building').':',
							),
							array('type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => $this->show_all_dashboard_applications() ? lang('Show only applications assigned to me') : lang('Show all applications'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_all_dashboard_applications'))
							),
						)
					),
				),
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
		}

		public function index_json()
		{
			$this->db = $GLOBALS['phpgw']->db;
			$applications = $this->bo->read_dashboard_data($this->show_all_dashboard_applications() ? null : $this->current_account_id());
			foreach($applications['results'] as &$application)
			{
				$application['status'] = lang($application['status']);
				$application['type'] = lang($application['type']);
				$application['created'] = pretty_timestamp($application['created']);
				$application['modified'] = pretty_timestamp($application['modified']);
				$application['frontend_modified'] = pretty_timestamp($application['frontend_modified']);
				$application['resources'] = $this->resource_bo->so->read(array('filters'=>array('id'=>$application['resources'])));
				$application['resources'] = $application['resources']['results'];
				if($application['resources'])
				{
					$names = array();
					foreach($application['resources'] as $res)
					{
						$names[] = $res['name'];
					}
					$application['what'] = $application['resources'][0]['building_name']. ' ('.join(', ', $names).')';
				}

				$sql = "SELECT account_lastname, account_firstname FROM phpgw_accounts WHERE account_lid = '".$application['case_officer_name']."'";
				$this->db->query($sql);
				while ($record = array_shift($this->db->resultSet)) {
					$application['case_officer_name'] = $record['account_firstname']." ".$record['account_lastname'];
				}
			}
			array_walk($applications["results"], array($this, "_add_links"), "booking.uiapplication.show");

			return $this->yui_results($applications);
		}

	}
