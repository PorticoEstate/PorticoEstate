<?php
	/*
	 * This class will enable status conditional redirect on tickets.
	 * A config section will be defined where conditions on status and target can be configured.
	 */

	class ticket_smartsheet_integration extends property_botts
	{

		protected $db;
		protected $config		 = array();
		protected $status_text	 = array();
		protected $custom_config;

		function __construct()
		{
			parent::__construct();
			$this->db			 = & $GLOBALS['phpgw']->db;
			$custom_config		 = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.ticket'));
			$this->config		 = $custom_config->config_data;
			$this->status_text	 = parent::get_status_text();
			if ($this->acl_location != '.ticket')
			{
				throw new Exception("'catch_ticket_export'  is intended for location = '.ticket'");
			}

			if (!isset($this->config['smartsheet_integration']) || !$this->config['smartsheet_integration'])
			{
				$this->custom_config = $custom_config;
				$this->initiate_config();
			}
		}

		protected function initiate_config()
		{
			$receipt_section = $this->custom_config->add_section(array
				(
				'name'	 => 'smartsheet_integration',
				'descr'	 => 'smartsheet integration based on category'
				)
			);
			$receipt		 = $this->custom_config->add_attrib(array(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'category',
				'descr'		 => 'commaseparated list of status that initiate post to smartsheet'
				)
			);
			$receipt		 = $this->custom_config->add_attrib(array(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'access_token',
				'descr'		 => 'Access token'
				)
			);
			$receipt		 = $this->custom_config->add_attrib(array(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'sheet_name',
				'descr'		 => 'Sheet name'
				)
			);

			$receipt = $this->custom_config->add_attrib(array(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'sheet_id',
				'descr'		 => 'sheet id'
				)
			);

			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'admin.uiconfig2.list_attrib',
				'section_id'	 => $receipt_section['section_id'], 'location_id'	 => $GLOBALS['phpgw']->locations->get_id('property', '.ticket')));
		}

		function check_category( $data )
		{
			$category_arr = explode(',', $this->config['smartsheet_integration']['category']);

			if (in_array($data['cat_id'], $category_arr))
			{
				$this->post_to_smartsheet($data);
			}
		}

		function post_to_smartsheet( $data )
		{
			$access_token	 = $this->config['smartsheet_integration']['access_token'];
			$sheet_name		 = $this->config['smartsheet_integration']['sheet_name'];
			$sheet_id		 = $this->config['smartsheet_integration']['sheet_id'];

			$link_data = array(
				'menuaction' => 'property.uitts.view',
				'id'		 => $data['id']
			);

			$hyperlink = $GLOBALS['phpgw']->link('/index.php', $link_data, false, true);

			require_once PHPGW_API_INC . '/smartsheet/vendor/autoload.php';

			$proxy = 'http://proxy.bergen.kommune.no:8080';

			$config			 = array('token' => $access_token);
			$config['proxy'] = array(
				'http'	 => $proxy,
				'https'	 => $proxy
			);

			$smartsheetClient	 = new \Smartsheet\SmartsheetClient($config);
			$sheets				 = $smartsheetClient->listSheets();

			if (!$sheet_id)
			{
				foreach ($sheets as $sheet_info)
				{
					if ($sheet_info->getname() == $sheet_name)
					{
						$sheet_id = $sheet_info->getid();
					}
					unset($sheet_info);
				}
			}

			$sheet = $smartsheetClient->getSheet($sheet_id);

			$ticket = parent::read_single($data['id']);

			if ($ticket['handyman_checklist_id'])
			{
				return;
			}

			/**
			 * SakID
			  Beskrivelse av sak/reklamasjon
			  Type sak
			  Beskrivelse av utførte tiltak for å utbedre

			 */
			$rows									 = array();
			$rows['SakID']							 = $data['id'];
			$rows['Beskrivelse av sak/reklamasjon']	 = $ticket['details'];
			$rows['Type sak']						 = 'reklamasjon';
			$rows['Hvor']							 = $ticket['location_code'] . ' : ' . $ticket['address'];
			$rows['Referanse']						 = array(
				'value'		 => 'Link til saken i Portico',
				'hyperlink'	 => $hyperlink
			);

			$sheet->addRow($rows);

			$sql = "UPDATE fm_tts_tickets SET handyman_checklist_id = 1 WHERE id = " . (int)$data['id'];
			$this->db->query($sql, __LINE__, __FILE__);
		}
	}
	$ticket_smartsheet = new ticket_smartsheet_integration();
	$ticket_smartsheet->check_category($data);
