<?php

	/*
	* This class will enable status conditional redirect on tickets.
	* A config section will be defined where conditions on status and target can be configured.
	*/

	class ticket_redirect_based_on_status extends property_botts
	{
		protected $db;
		protected $config = array();
		protected $status_text = array();
		protected $custom_config;

		function __construct()
		{
			parent::__construct();
			$this->db 		= & $GLOBALS['phpgw']->db;
			$custom_config	= CreateObject('admin.soconfig',$GLOBALS['phpgw']->locations->get_id('property', '.ticket'));
			$this->config = $custom_config->config_data;
			$this->status_text = parent::get_status_text();
			if($this->acl_location != '.ticket')
			{
				throw new Exception("'catch_ticket_export'  is intended for location = '.ticket'");
			}

			if(!isset($this->config['ticket_redirect']) || !$this->config['ticket_redirect'])
			{
				$this->custom_config = $custom_config;
				$this->initiate_config();
			}
		}

		protected function initiate_config()
		{
			$receipt_section = $this->custom_config->add_section(array
				(
					'name' => 'ticket_redirect',
					'descr' => 'ticket redirect based on status'
				)
			);
			$receipt = $this->custom_config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'text',
					'name'			=> 'status',
					'descr'			=> 'commaseparated list of status that initiate redirect'
				)
			);
			$receipt = $this->custom_config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'text',
					'name'			=> 'target',
					'descr'			=> 'commaseparated list of target of redirect'
				)
			);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiconfig2.list_attrib', 'section_id' => $receipt_section['section_id'] , 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.ticket')) );
		}

		function check_status($data)
		{
			$status_arr = explode(',', $this->config['ticket_redirect']['status']);
			$target_arr = explode(',', $this->config['ticket_redirect']['target']);

			foreach($status_arr as $key => $status_redirect)
			{
				if($data['status'] != $data['old_status'] && trim($data['status'],'C') == $status_redirect && isset($target_arr[$key]) && $target_arr[$key])
				{
					$link_data = array
					(
						'menuaction'		=> $target_arr[$key],
						'bypass'			=> true,
						'location_code'		=> $data['location_code'],
						'p_num'				=> $data['p_num'],
						'p_entity_id'		=> $data['p_entity_id'],
						'p_cat_id'			=> $data['p_cat_id'],
						'tenant_id'			=> $data['tenant_id'],
						'origin'			=> '.ticket',
						'origin_id'			=> $data['id']
					);

					$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
				}
			}
		}
	}

	$ticket_redirect = new ticket_redirect_based_on_status();
	$ticket_redirect->check_status($data);
