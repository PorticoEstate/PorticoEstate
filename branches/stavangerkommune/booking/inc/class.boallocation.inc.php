<?php
	phpgw::import_class('booking.bocommon_authorized');
	
	class booking_boallocation extends booking_bocommon_authorized
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soallocation');
		}
		
		/**
		 * @ Send message about cancelation to users of building. 
		 */
		function send_notification($allocation, $maildata, $mailadresses)
		{
			if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
				return;
			$send = CreateObject('phpgwapi.send');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];

            if($maildata['outseason'] != 'on' && $maildata['recurring'] != 'on')
            {
                $res_names = '';
				foreach ($allocation['resources'] as $res) {
					$res_names = $res_names.$this->so->get_resource($res)." ";
				}
				$info_deleted = ':<p>';
   				$info_deleted = $info_deleted."".$res_names." - ";
                $info_deleted .= pretty_timestamp($allocation['from_'])." - ";
                $info_deleted .= pretty_timestamp($allocation['to_']);
   			    $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add&building_id=';
                $link .= $allocation['building_id'].'&building_name='.urlencode($allocation['building_name']).'&from_[]=';
                $link .= urlencode($allocation['from_']).'&to_[]='.urlencode($allocation['to_']).'&resource='.$allocation['resources'][0];                    
                $info_deleted .= ' - <a href="'.$link.'">'.lang('Apply for time').'</a><br />';

    			$subject = $config->config_data['allocation_canceled_mail_subject'];
                $body = "<p>".$config->config_data['allocation_canceled_mail'];
                $body .= '<br />'.$allocation['organization_name'].' har avbestilt tid i '.$allocation['building_name'];
    			$body .= $info_deleted.'</p>';

            } else {
                $res_names = '';
				foreach ($allocation['resources'] as $res) {
					$res_names = $res_names.$this->so->get_resource($res)." ";
				}
				$info_deleted = ':<p>';
				foreach ($maildata['delete'] as $valid_date) {
       				$info_deleted = $info_deleted."".$res_names." - ";
                    $info_deleted .= pretty_timestamp($valid_date['from_'])." - ";
                    $info_deleted .= pretty_timestamp($valid_date['to_']);
      			    $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add&building_id=';
                    $link .= $allocation['building_id'].'&building_name='.urlencode($allocation['building_name']).'&from_[]=';
                    $link .= urlencode($valid_date['from_']).'&to_[]='.urlencode($valid_date['to_']).'&resource='.$allocation['resources'][0];                    
                    $info_deleted .= ' - <a href="'.$link.'">'.lang('Apply for time').'</a><br />';
				}

    			$subject = $config->config_data['allocation_canceled_mail_subject'];
                $body = "<p>".$config->config_data['allocation_canceled_mail'];
                $body .= '<br />'.$allocation['organization_name'].' har avbestilt tid i '.$allocation['building_name'];
    			$body .= $info_deleted.'</p>';

            }


			$body .= "<p>".$config->config_data['application_mail_signature']."</p>";

            foreach ($mailadresses as $adr)
            {
    			try
    			{
				    $send->msg('email', $adr, $subject, $body, '', '', '', $from, '', 'html');
    			}
    			catch (phpmailerException $e)
    			{
    				// TODO: Inform user if something goes wrong
    			}
            }

		}

		/**
		 * @see bocommon_authorized
		 */
		protected function include_subject_parent_roles(array $for_object = null)
		{
			$this->season_bo = CreateObject('booking.boseason');
			$parent_roles = null;
			$parent_season = null;
			
			if (is_array($for_object)) {
				if (!isset($for_object['season_id'])) {
					throw new InvalidArgumentException('Cannot initialize object parent roles unless season_id is provided');
				}
				$parent_season = $this->season_bo->read_single($for_object['season_id']);
			}
			
			//Note that a null value for $parent_season is acceptable. That only signifies
			//that any roles specified for any season are returned instead of roles for a specific season.
			$parent_roles['season'] = $this->season_bo->get_subject_roles($parent_season);
			return $parent_roles;
		}
		
		
		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions(array $forObject, $defaultPermissions)
		{
			return array_merge(
				array
				(
					'parent_role_permissions' => array
					(
						'season' => array
						(
							booking_sopermission::ROLE_MANAGER => array(
								'write' => true,
								'create' => true,
							),
							booking_sopermission::ROLE_CASE_OFFICER => array(
								'write' => true,
								'create' => true,
							),
							'parent_role_permissions' => array(
								'building' => array(
									booking_sopermission::ROLE_MANAGER => array(
										'write' => true,
										'create' => true,
									),
								),
							)
						),
					),
					'global' => array
					(
						booking_sopermission::ROLE_MANAGER => array
						(
							'write' => true,
							'delete' => true,
							'create' => true
						),
					),
				),
				$defaultPermissions
			);
		}
		
		/**
		 * @see bocommon_authorized
		 */
		protected function get_collection_role_permissions($defaultPermissions)
		{
			return array_merge(
				array
				(
					'parent_role_permissions' => array
					(
						'season' => array
						(
							booking_sopermission::ROLE_MANAGER => array(
								'create' => true,
							),
							booking_sopermission::ROLE_CASE_OFFICER => array(
								'create' => true,
							),
							'parent_role_permissions' => array(
								'building' => array(
									booking_sopermission::ROLE_MANAGER => array(
										'create' => true,
									),
								),
							)
						)
					),
					'global' => array
					(
						booking_sopermission::ROLE_MANAGER => array
						(
							'create' => true
						)
					),
				),
				$defaultPermissions
			);
		}
		
		public function complete_expired(&$allocations) {
			$this->so->complete_expired($allocations);
		}
		
		public function find_expired() {
			return $this->so->find_expired();
		}
	}
