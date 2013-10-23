<?php

		// this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example
		
		// out: 'deliver'
		// in: 'pickup'

	class catch_ticket_export extends property_botts
	{
		protected $db;
		protected $config = array();
		protected $status_text = array();
		protected $connection = false;
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

			if(!isset($this->config['catch_export']) || !$this->config['catch_export'])
			{
				$this->custom_config = $custom_config;
				$this->initiate_config();
			}
		}
		
		protected function initiate_config()
		{
			$receipt_section = $this->custom_config->add_section(array
				(
					'name' => 'catch_export',
					'descr' => 'Catch export'
				)
			);
			$receipt = $this->custom_config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'text',
					'name'			=> 'host',
					'descr'			=> 'Host'
				)
			);
			$receipt = $this->custom_config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'text',
					'name'			=> 'user',
					'descr'			=> 'User'
				)
			);
			$receipt = $this->custom_config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'password',
					'name'			=> 'password',
					'descr'			=> 'Password'
				)
			);
			$receipt = $this->custom_config->add_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'input_type'	=> 'listbox',
					'name'			=> 'export_method',
					'descr'			=> 'Export method'
				)
			);
				$receipt = $this->custom_config->edit_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'attrib_id'		=> $receipt['attrib_id'],
					'input_type'	=> 'listbox',
					'name'			=> 'export_method',
					'descr'			=> 'Export method',
					'new_choice' 	=> 'ftp'
				)
			);
			$receipt = $this->custom_config->edit_attrib(array
				(
					'section_id'	=> $receipt_section['section_id'],
					'attrib_id'		=> $receipt['attrib_id'],
					'input_type'	=> 'listbox',
					'name'			=> 'export_method',
					'descr'			=> 'Export method',
					'new_choice' 	=> 'ssh'
				)
			);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiconfig2.list_attrib', 'section_id' => $receipt_section['section_id'] , 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.ticket')) );
		}

		function export_ticket($ticket)
		{

//			_debug_array($ticket);
//_debug_array($receipt);

			$export_values = array();
			$sql = 'SELECT unitid FROM fm_catch_1_1 WHERE user_ = ' . (int) $ticket['assignedto'] . ' ORDER BY id ASC';
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();

			$export_values['unitid'] = $this->db->f('unitid',true);
			$solocation = CreateObject('property.solocation');
			$location = $solocation->read_single($ticket['location_code']);

			$values = $this->so->read_single($ticket['id']);
//_debug_array($values);


			$export_values['melding_id'] = $ticket['id'];

			$export_values['eiendom_navn'] = $location['loc1_name'];
			$export_values['eiendomid'] = $location['loc1'];
			$export_values['byggid'] = $location['loc2'];
			$export_values['byggnavn'] = $location['loc2_name'];
			$export_values['etasjeid'] = $location['loc3'];
			$export_values['etasjenavn'] = $location['loc3_name'];
			$export_values['bruksenhetid'] = $location['loc4'];
			$export_values['bruksenhet_navn'] = $location['loc4_name'];
			$export_values['rom_id_navn'] = $location['rom_nr_id'];
			$export_values['rom_navn'] = $location['loc5_name'];
			$export_values['romid'] = $location['loc5'];
			$export_values['prioritet'] = $ticket['priority'];
			$export_values['overskrift_melding'] = $ticket['subject'];
			$export_values['detaljer_melding'] = "{$values['user_name']}:: {$values['details']}";
			$export_values['meldingskategori'] = $ticket['priority'];
			$export_values['cat_id'] = $ticket['cat_id'];
			$export_values['kommentarer'] = '';
			$export_values['status_melding'] = $this->status_text[$ticket['status']];
			$export_values['status'] = $ticket['status'];
			$export_values['egne_timer'] = $ticket['billable_hours'];

			$additional_notes = $this->read_additional_notes($ticket['id']);
			foreach ($additional_notes as $additional_note)
			{
				$export_values['detaljer_melding'] .= "\n{$additional_note['value_user']}::{$additional_note['value_note']}";
			}

//_debug_array($additional_notes); die();
		
			if (function_exists('com_create_guid') === true)
			{
				$guid = trim(com_create_guid(), '{}');
			}
			else
			{
				$guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
			}

			phpgw::import_class('phpgwapi.xmlhelper');
			$xmldata = phpgwapi_xmlhelper::toXML($export_values, 'PPCC');
			$doc = new DOMDocument;
			$doc->preserveWhiteSpace = true;
			$doc->loadXML( $xmldata );
			$domElement = $doc->getElementsByTagName('PPCC')->item(0);
			$domAttribute = $doc->createAttribute('UUID');
			$domAttribute->value = $guid;

			// Don't forget to append it to the element
			$domElement->appendChild($domAttribute);

			// Append it to the document itself
			$doc->appendChild($domElement);
			$doc->formatOutput = true;
			
			$xml = $doc->saveXML();

//			echo $xml;
//			_debug_array($this->config);
		
			$filename = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/{$guid}.xml";

			$fp = fopen($filename, "wb");
			fwrite($fp,$xml);
				
			if(fclose($fp))
			{
				$this->transfer($xml, $filename);
			}
//_debug_array($filename);
			die();
		}

		protected function transfer($xml,$filename)
		{			
			if($this->config['catch_export']['export_method']=='ftp' || $this->config['catch_export']['export_method']=='ssh')
			{
				if(!$connection = $this->connection)
				{
					$connection	= $this->phpftp_connect();
				}
				
				$basedir = $this->config['catch_export']['basedir'];
				if($basedir)
				{
					$remote_file = $basedir . '/' . basename($filename);
				}
				else
				{
					$remote_file = basename($filename);
				}

				switch ($this->config['catch_export']['export_method'])
				{
					case 'ftp';
						$transfer_ok = ftp_put($connection,$remote_file, $filename, FTP_BINARY);
						break;
					case 'ssh';
						$sftp = ssh2_sftp($connection);
						$stream = @fopen("ssh2.sftp://$sftp$remote_file", 'w');
						$data_to_send = @file_get_contents($filename);
						fwrite($stream, $data_to_send);
						$transfer_ok = @fclose($stream);
						break;
					default:
						$transfer_ok = false;
				}
				if ($send_ok)
				{
					// log ok
				}
				else
				{
					// log ok fail
				}
				if(!$transfer_ok)
				{
					unlink($filename);
				}
			}
			return 	$transfer_ok;
		}

		protected function phpftp_connect() 
		{
			$server				= $this->config['catch_export']['host'];
			$user				= $this->config['catch_export']['user'];
			$password			= $this->config['catch_export']['password'];
			$port				= 22;
			
			switch ($this->config['catch_export']['export_method'])
			{
				case 'ftp';
					if($connection = ftp_connect($server))
					{
						ftp_login($connection,$user,$password);
					}
					break;
				case 'ssh';
					if (!function_exists("ssh2_connect"))
					{
						die("function ssh2_connect doesn't exist");
					}
					if(!($connection = ssh2_connect("$server", $port)))
					{
						$message = "fail: unable to establish connection";
						_debug_array($message);
						//$receipt['error'][]= array('msg' => $message);
					}
					else
					{
						// try to authenticate with username root, password secretpassword
						if(!ssh2_auth_password($connection, $user, $password))
						{
							$message = "fail: unable to authenticate";
							_debug_array($message);
							//$receipt['error'][]= array('msg' => $message);
						}
					}
					break;
			}
			$this->connection = $connection;
			return $connection;
		}
	}

	$export = new catch_ticket_export();
	$export->export_ticket($ticket);

