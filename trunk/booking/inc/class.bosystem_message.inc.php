<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_bosystem_message extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sosystem_message');
		}
		/**
		* Returns an array of application ids from applications assocciated with buildings
		* which the given user has access to
		*
		* @param int $user_id
		*/

		function read_message_data($for_case_officer_id = null)
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$messages =  array(); 
			
			if (!is_null($for_case_officer_id)) {
				$sql = "SELECT id, type, status, title, name, created, building_id FROM bb_system_message WHERE status ='NEW' ORDER BY id DESC";
			}
			else {
				$sql = "SELECT id, type, status, title, name, created, building_id FROM bb_system_message ORDER BY id DESC";
			}

			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];
			$this->db->query($sql);
			$data = $this->db->resultSet;
			while ($messagedata = array_shift($data)) {
				$building_case_officers_data =  array(); 
				$building_case_officers =  array(); 
				$sql = "SELECT account_id, account_lid, account_firstname, account_lastname FROM phpgw_accounts WHERE account_id IN (SELECT subject_id FROM bb_permission WHERE object_id=".$messagedata['building_id']." AND role='case_officer')";
				$this->db->query($sql);
				while ($record = array_shift($this->db->resultSet)) {
					 $building_case_officers_data[] = array('account_id' => $record['account_id'], 'account_lid' => $record['account_lid'],'account_name' => $record['account_firstname']." ".$record['account_lastname']);
					 $building_case_officers[] = $record['account_id'];
				}

				if(in_array($for_case_officer_id, $building_case_officers, true) || is_null($for_case_officer_id)) {
					$message =	array('id' =>  $messagedata['id'],
						'type' => lang($messagedata['type']),
                	    'status' => lang($messagedata['status']),
                	    'created' => pretty_timestamp($messagedata['created']),
                	    'modified' => '',
                	    'activity_name' => '',
                	    'contact_name' => $messagedata['name'],                	    
						'case_officer_name' => $for_case_officer_id,
                	    'what' => $messagedata['title'],
                	    'link' => $external_site_address."/index.php?menuaction=booking.uisystem_message.show&amp;id=".$messagedata['id']."&amp;");			

						while($case_officer = array_shift($building_case_officers_data)) {
							if ($message['case_officer_name'] = $case_officer['account_id'])
								$message['case_officer_name'] = $case_officer['account_name'];
						}
					$messages[] = $message;
				}
			}
			return $messages;
		}
	}
