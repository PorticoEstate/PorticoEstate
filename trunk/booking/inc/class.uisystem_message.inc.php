<?php
	phpgw::import_class('booking.uicommon');

	class booking_uisystem_message extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'toggle_show_inactive'	=>	true,
		);

        protected $module;
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bosystem_message');
			self::set_active_menu('booking::system_message');

            $this->module = "booking";
		}

		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'hidden' => true,
								'value' => lang('New message'),
								'href' => self::link(array('menuaction' => 'booking.uisystem_message.edit'))
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
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uisystem_message.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'title',
							'label' => lang('Title'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'created',
							'label' => lang('Created')
						),
						array(
							'key' => 'building_id',
							'label' => lang('Building')
						),
						array(
							'key' => 'type',
							'label' => lang('Type')
						),
						array(
							'key' => 'status',
							'label' => lang('Status')
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
			if ( !isset($GLOBALS['phpgw_info']['user']['apps']['admin']) &&
			     !$this->bo->has_role(booking_sopermission::ROLE_MANAGER) )
			{
				$filters['id'] = $this->bo->accessable_applications($GLOBALS['phpgw_info']['user']['id']);
			}
			$filters['status'] = 'NEW';
			if(isset($_SESSION['showall']))
			{
				$filters['status'] = array('NEW', 'CLOSED');
			}
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);

			$system_messages = $this->bo->read();

			foreach($system_messages["results"] as &$system_message) {
				$system_message['created'] = pretty_timestamp($system_message['created']);
				$system_message['type'] = lang($system_message['type']);
				$system_message['status'] = lang($system_message['status']);
			}
			array_walk($system_messages["results"], array($this, "_add_links"), $this->module.".uisystem_message.show");
			$results = $this->yui_results($system_messages);
			
			return $results;
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));

			if ($id)
			{
				$system_message = $this->bo->read_single($id);
				$system_message['id'] = $id;
				$system_message['cancel_link'] = self::link(array('menuaction' => 'booking.uisystem_message.index'));
				
					
			} else {
				date_default_timezone_set("Europe/Oslo");
				$date = new DateTime(phpgw::get_var('date'));
				$system_message = array();
				$system_message['building_id'] = intval(phpgw::get_var('building_id', 'GET'));
				$system_message['created'] =  $date->format('Y-m-d  H:m');
				$system_message['cancel_link'] = self::link(array('menuaction' => 'booking.uisystem_message.index'));
			}


			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$system_message = array_merge($system_message, extract_values($_POST, array('time', 'title', 'message')));
				if (!isset($system_message["Status"]))
				{
					$system_message['status'] = 'NEW';
				}
				if ($system_message['message'] == '')
				{
					$errors['system_message'] = lang('No message');
				}
				if(!$errors)
				{
					if ($id)
					{
						$receipt = $this->bo->update($system_message);
					} else {
						$receipt = $this->bo->add($system_message);
					}
				
					$this->redirect(array('menuaction' => 'booking.uisystem_message.edit', 'id'=>$receipt['id'], 'warnings'=>$errors));
				}
			}
			$this->flash_form_errors($errors);

			$this->use_yui_editor();
			self::render_template('system_message_edit', array('system_message' => $system_message, 'module' => $this->module));
		}
		
		public function show()
		{
			$id = intval(phpgw::get_var('id', 'GET'));

			$system_message = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$system_message['system_messages_link'] = self::link(array('menuaction' => $this->module . '.uisystem_message.index'));
			$system_message['system_message_link'] = self::link(array('menuaction' => $this->module . '.uisystem_message.show', 'id' => $system_message['system_message_id']));
			$system_message['back_link'] = self::link(array('menuaction' => $this->module . '.uidashboard.index'));

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if($_POST['status'] == 'CLOSED') {
					$system_message['status'] = 'CLOSED';
					$receipt = $this->bo->update($system_message);
					$this->redirect(array('menuaction' => 'booking.uisystem_message.show', 'id'=>$receipt['id'], 'warnings'=>$errors));
				}
			}	
			
			$system_message['created'] = pretty_timestamp($system_message['created']);
			$system_message['type'] = lang($system_message['type']);
			$system_message['status'] = lang($system_message['status']);

			$data = array(
				'system_message'	=>	$system_message
			);
			$loggedin = (int) true; // FIXME: Some sort of authentication!

			self::render_template('system_message', array('system_message' => $system_message, 'loggedin' => $loggedin));
		}
	}
