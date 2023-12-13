<?php
	/*
	 * This class will push tickets to an external smartsheet based on category.
	 * A config section will be defined where conditions on status and target can be configured.
	 */

	class EBF_ticket_smartsheet_integration extends property_botts
	{
		private $_config = array();
		private $db;
		private $custom_config;

		function __construct()
		{
			parent::__construct();
			$this->db		 = & $GLOBALS['phpgw']->db;
			$custom_config	 = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.ticket'));
			$this->_config	 = $custom_config->config_data;
			if ($this->acl_location != '.ticket')
			{
				throw new Exception("'catch_ticket_export'  is intended for location = '.ticket'");
			}

			if (!isset($this->_config['smartsheet_integration']) || !$this->_config['smartsheet_integration'])
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
				'descr'	 => 'smartsheet integration based on ticket status'
				)
			);
			$receipt		 = $this->custom_config->add_attrib(array(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name'		 => 'status',
				'descr'		 => 'Ticket status that initiate post to smartsheet'
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
			$status = $this->_config['smartsheet_integration']['status'];

			if ($data['status'] == $status)
			{
				$this->post_to_smartsheet($data);
			}
		}

		function post_to_smartsheet( $data )
		{
			$access_token	 = $this->_config['smartsheet_integration']['access_token'];
			$sheet_name		 = $this->_config['smartsheet_integration']['sheet_name'];
			$sheet_id		 = $this->_config['smartsheet_integration']['sheet_id'];

			$link_data = array(
				'menuaction' => 'property.uitts.view',
				'id'		 => $data['id']
			);

			$hyperlink = str_replace(array("http:","&amp;"), array("https:","&"), $GLOBALS['phpgw']->link('/index.php', $link_data, false, true));

			require_once PHPGW_API_INC . '/smartsheet/vendor/autoload.php';

			$config			 = array('token' => $access_token);

			if (!empty($GLOBALS['phpgw_info']['server']['httpproxy_server']))
			{
				$proxy = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
				$config['proxy'] = array(
					'http'	 => $proxy,
					'https'	 => $proxy
				);
			}
//			$proxy = 'http://proxy.bergen.kommune.no:8080';
//
//			$config['proxy'] = array(
//					'http'	 => $proxy,
//					'https'	 => $proxy
//				);

			$smartsheetClient	 = new \Smartsheet\SmartsheetClient($config);

			try
			{
				$sheets				 = $smartsheetClient->listSheets();
			}
			catch(Exception $e)
			{
				phpgwapi_cache::message_set($e->getMessage(), 'error');
				$this->historylog->add('RM', (int)$data['id'], 'Overføring til EFU feilet');
				return;
			}

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
			 Meldt til EBF
			 Meldt EFU

			 */
			$rows									 = array();
			$rows['SakID']							 = $data['id'];
			$rows['Beskrivelse av sak/reklamasjon']	 = $ticket['details'];
			$rows['Type sak']						 = 'Reklamasjon';
			$rows['Hvor']							 = $ticket['location_code'] . ' : ' . $ticket['address'];
			$rows['Referanse']						 = array(
				'value'		 => 'Link til saken i Portico',
				'hyperlink'	 => $hyperlink
			);
			$rows['Meldt til EBF']						 = date('Y-m-d\TH:i:sp', $ticket['timestamp']);// ISO 8601 date format
			$rows['Meldt EFU']							 = date('Y-m-d\TH:i:sp');

			try
			{
				$sheet->addRow($rows);
				$sql = "UPDATE fm_tts_tickets SET handyman_checklist_id = 1 WHERE id = " . (int)$data['id'];
				$this->db->query($sql, __LINE__, __FILE__);
				$this->historylog->add('RM', (int)$data['id'], 'Saken er meldt til EFU');
			}
			catch(Exception $e)
			{
				phpgwapi_cache::message_set($e->getMessage(), 'error');
				$this->historylog->add('RM', (int)$data['id'], 'Overføring til EFU feilet');
			}
		}
	}
	$ticket_smartsheet = new EBF_ticket_smartsheet_integration();
	$ticket_smartsheet->check_category($data);
