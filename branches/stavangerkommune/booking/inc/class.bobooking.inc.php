<?php
	phpgw::import_class('booking.bocommon_authorized');
	
	require_once "schedule.php";

function array_minus($a, $b)
{
	$b = array_flip($b);
	$c = array();
	foreach($a as $x)
	{
		if(!array_key_exists($x, $b))
		$c[] = $x;
	}
	return $c;
}

	
	class booking_bobooking extends booking_bocommon_authorized
	{
		const ROLE_ADMIN = 'organization_admin';

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sobooking');
			$this->allocation_so = CreateObject('booking.soallocation');
			$this->resource_so = CreateObject('booking.soresource');
			$this->event_so = CreateObject('booking.soevent');
			$this->season_bo = CreateObject('booking.boseason');
		}

		/**
		 * @ Send message about cancelation to users of building. 
		 */
		function send_notification($booking, $allocation, $maildata, $mailadresses, $valid_dates=null)
		{
			if (!(isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server']))
				return;
			$send = CreateObject('phpgwapi.send');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			$external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];


            if(($maildata['outseason'] != 'on' && $maildata['recurring'] != 'on' && $maildata['delete_allocation'] != 'on') || 
                ($maildata['outseason'] != 'on' && $maildata['recurring'] != 'on' && $maildata['delete_allocation'] == 'on' && 
                 $maildata['allocation'] == 0))
            {
			    $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add&building_id=';
                $link .= $booking['building_id'].'&building_name='.urlencode($booking['building_name']).'&from_[]=';
                $link .= urlencode($booking['from_']).'&to_[]='.urlencode($booking['to_']).'&resource='.$booking['resources'][0];

    			$subject = $config->config_data['booking_canceled_mail_subject'];

                $body = "<p>".$config->config_data['booking_canceled_mail'];
                $body .= '</p><p>'.$booking['group_name'].' har avbestilt tid i '.$booking['building_name'].':<br />';
                $body .= $this->so->get_resource($booking['resources'][0]).' den '.pretty_timestamp($booking['from_']);
                $body .=' til '.pretty_timestamp($booking['to_']); 
    			$body .= ' - <a href="'.$link.'">'.lang('Apply for time').'</a></p>';

            } elseif (($maildata['outseason'] == 'on' || $maildata['recurring'] == 'on') && $maildata['delete_allocation'] != 'on') {
                $res_names = '';
				foreach ($booking['resources'] as $res) {
					$res_names = $res_names.$this->so->get_resource($res)." ";
				}
				$info_deleted = ':<p>';
				foreach ($valid_dates as $valid_date) {
    				$info_deleted = $info_deleted."".$res_names." - ";
                    $info_deleted .= pretty_timestamp($valid_date['from_'])." - ";
                    $info_deleted .= pretty_timestamp($valid_date['to_']);
    			    $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add&building_id=';
                    $link .= $booking['building_id'].'&building_name='.urlencode($booking['building_name']).'&from_[]=';
                    $link .= urlencode($valid_date['from_']).'&to_[]='.urlencode($valid_date['to_']).'&resource='.$booking['resources'][0];
                    $info_deleted .= ' - <a href="'.$link.'">'.lang('Apply for time').'</a><br />';
				}

    			$subject = $config->config_data['booking_canceled_mail_subject'];

                $body = "<p>".$config->config_data['booking_canceled_mail'];
                $body .= '<br />'.$booking['group_name'].' har avbestilt tid i '.$booking['building_name'];
                $body .= $info_deleted.'</p>';

            } elseif (($maildata['outseason'] == 'on' || $maildata['recurring'] == 'on') && $maildata['delete_allocation'] == 'on') {
                $res_names = '';
				foreach ($booking['resources'] as $res) {
					$res_names = $res_names.$this->so->get_resource($res)." ";
				}
				$info_deleted = ':<p>';
				foreach ($valid_dates as $valid_date) {
                    if (!in_array($valid_date,$maildata['delete'])) {
        				$info_deleted = $info_deleted."".$res_names." - ";
                        $info_deleted .= pretty_timestamp($valid_date['from_'])." - ";
                        $info_deleted .= pretty_timestamp($valid_date['to_']);
        			    $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add&building_id=';
                        $link .= $booking['building_id'].'&building_name='.urlencode($booking['building_name']).'&from_[]=';
                        $link .= urlencode($valid_date['from_']).'&to_[]='.urlencode($valid_date['to_']).'&resource='.$booking['resources'][0];
                        $info_deleted .= ' - <a href="'.$link.'">'.lang('Apply for time').'</a><br />';
                    }
				}
				foreach ($maildata['delete'] as $valid_date) {
       				$info_deleted = $info_deleted."".$res_names." - ";
                    $info_deleted .= pretty_timestamp($valid_date['from_'])." - ";
                    $info_deleted .= pretty_timestamp($valid_date['to_']);
      			    $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add&building_id=';
                    $link .= $booking['building_id'].'&building_name='.urlencode($booking['building_name']).'&from_[]=';
                    $link .= urlencode($valid_date['from_']).'&to_[]='.urlencode($valid_date['to_']).'&resource='.$booking['resources'][0];                    
                    $info_deleted .= ' - <a href="'.$link.'">'.lang('Apply for time').'</a><br />';
				}
                

    			$subject = $config->config_data['allocation_canceled_mail_subject'];
                $body = "<p>".$config->config_data['allocation_canceled_mail'];
                $body .= '<br />'.$booking['group_name'].' har avbestilt tid i '.$booking['building_name'];
    			$body .= $info_deleted.'</p>';

            } else {
                $res_names = '';
				foreach ($booking['resources'] as $res) {
					$res_names = $res_names.$this->so->get_resource($res)." ";
				}
				$info_deleted = ':<p>';
   				$info_deleted = $info_deleted."".$res_names." - ";
                $info_deleted .= pretty_timestamp($allocation['from_'])." - ";
                $info_deleted .= pretty_timestamp($allocation['to_']);
   			    $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uiapplication.add&building_id=';
                $link .= $booking['building_id'].'&building_name='.urlencode($booking['building_name']).'&from_[]=';
                $link .= urlencode($valid_date['from_']).'&to_[]='.urlencode($valid_date['to_']).'&resource='.$booking['resources'][0];                    
                $info_deleted .= ' - <a href="'.$link.'">'.lang('Apply for time').'</a><br />';

    			$subject = $config->config_data['allocation_canceled_mail_subject'];
                $body = "<p>".$config->config_data['allocation_canceled_mail'];
                $body .= '<br />'.$booking['group_name'].' har avbestilt tid i '.$booking['building_name'];
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
		 * @see booking_bocommon_authorized
		 */
		protected function get_subject_roles($for_object = null, $initial_roles=array())
		{
			if ($this->current_app() == 'bookingfrontend') {
				$bouser = CreateObject('bookingfrontend.bouser');
				
				$group_id = is_array($for_object) ? $for_object['group_id'] : (!is_null($for_object) ? $for_object : null);
				
				if ($bouser->is_group_admin($group_id)) {
					$initial_roles[] = array('role' => self::ROLE_ADMIN);
				}
			}
			
			return parent::get_subject_roles($for_object, $initial_roles);
		}
		
		/**
		 * @see bocommon_authorized
		 */
		protected function get_object_role_permissions(array $forObject, $defaultPermissions)
		{
			if ($this->current_app() == 'bookingfrontend') {	             
				$defaultPermissions[self::ROLE_ADMIN] = array
				(
					'create' => true,
					'write' => true,
				);
			}
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
			if ($this->current_app() == 'bookingfrontend')
			{
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['create'] = true;
				$defaultPermissions[booking_sopermission::ROLE_DEFAULT]['write'] = true;
				return $defaultPermissions;
			}
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

		/**
		 * Return a building's schedule for a given week in a YUI DataSource
		 * compatible format
		 * 
		 * @param int	$building_id
		 * @param $date 
		 *
		 * @return array containing values from $array for the keys in $keys.
		 */
		function building_schedule($building_id, $date)
		{

			$from = clone $date;
			$from->setTime(0, 0, 0);
			// Make sure $from is a monday
			if($from->format('w') != 1)
			{
				$from->modify('last monday');
			}
			$to = clone $from;
			$to->modify('+7 days');
			$allocation_ids = $this->so->allocation_ids_for_building($building_id, $from, $to);
			$allocations = $this->allocation_so->read(array('filters'=> array('id' => $allocation_ids)));
			$allocations = $allocations['results'];
			foreach($allocations as &$allocation)
			{
				$allocation['name'] = $allocation['organization_name'];
				$allocation['shortname'] = $allocation['organization_shortname'];
				$allocation['type'] = 'allocation';
			}
			$booking_ids = $this->so->booking_ids_for_building($building_id, $from, $to);
			$bookings = $this->so->read(array('filters'=> array('id' => $booking_ids)));
			$bookings = $bookings['results'];
			foreach($bookings as &$booking)
			{
				$booking['name'] = $booking['group_name'];
				$booking['shortname'] = $booking['group_shortname'];
				$booking['type'] = 'booking';
			}
			$allocations = $this->split_allocations($allocations, $bookings);

			$event_ids = $this->so->event_ids_for_building($building_id, $from, $to);
			$events = $this->event_so->read(array('filters'=> array('id' => $event_ids)));
			$events = $events['results'];
			foreach($events as &$event)
			{
				$event['name'] = $event['description'];
				$event['type'] = 'event';
			}
			$bookings = array_merge($allocations, $bookings);
			$bookings = $this->_remove_event_conflicts($bookings, $events);
			$bookings = array_merge($events, $bookings);

			$resource_ids = $this->so->resource_ids_for_bookings($booking_ids);
			$resource_ids = array_merge($resource_ids, $this->so->resource_ids_for_allocations($allocation_ids));
			$resource_ids = array_merge($resource_ids, $this->so->resource_ids_for_events($event_ids));
			$resources = $this->resource_so->read(array('filters' => array('id' => $resource_ids, 'active' => 1)));
			$resources = $resources['results'];
			foreach ($resources as $key => $row) {
    			$sort[$key] = $row['sort'];
			}

			// Sort the resources with sortkey ascending
			// Add $resources as the last parameter, to sort by the common key
			array_multisort($sort, SORT_ASC, $resources);
			$bookings = $this->_split_multi_day_bookings($bookings, $from, $to);
			$results = build_schedule_table($bookings, $resources);
			return array('total_records'=>count($results), 'results'=>$results);
		}

		function building_extraschedule($building_id, $date)
		{
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			$from = clone $date;
			$from->setTime(0, 0, 0);
			// Make sure $from is a monday
			if($from->format('w') != 1)
			{
				$from->modify('last monday');
			}
			$to = clone $from;
			$to->modify('+7 days');
			$allocation_ids = $this->so->allocation_ids_for_building($building_id, $from, $to);
            
            $orgids = explode(",", $config->config_data['extra_schedule_ids']);
            
			$allocations = $this->allocation_so->read(array('filters'=> array('id' => $allocation_ids, 'organization_id' => $orgids)));
			$allocations = $allocations['results'];
			foreach($allocations as &$allocation)
			{
				$allocation['name'] = $allocation['organization_name'];
				$allocation['shortname'] = $allocation['organization_shortname'];
				$allocation['type'] = 'allocation';
			}
			$booking_ids = $this->so->booking_ids_for_building($building_id, $from, $to);
			$bookings = $this->so->read(array('filters'=> array('id' => $booking_ids)));
			$bookings = $bookings['results'];
			foreach($bookings as &$booking)
			{
				$booking['name'] = $booking['group_name'];
				$booking['shortname'] = $booking['group_shortname'];
				$booking['type'] = 'booking';
			}
			$allocations = $this->split_allocations($allocations, $bookings);

			$event_ids = $this->so->event_ids_for_building($building_id, $from, $to);
			$events = $this->event_so->read(array('filters'=> array('id' => $event_ids)));
			$events = $events['results'];
			foreach($events as &$event)
			{
				$event['name'] = $event['description'];
				$event['type'] = 'event';
			}
			$bookings = array_merge($allocations, $bookings);
			$bookings = $this->_remove_event_conflicts($bookings, $events);

			$resource_ids = $this->so->resource_ids_for_bookings($booking_ids);
			$resource_ids = array_merge($resource_ids, $this->so->resource_ids_for_allocations($allocation_ids));
			$resource_ids = array_merge($resource_ids, $this->so->resource_ids_for_events($event_ids));
			$resources = $this->resource_so->read(array('filters' => array('id' => $resource_ids, 'active' => 1)));
			$resources = $resources['results'];
			foreach ($resources as $key => $row) {
    			$sort[$key] = $row['sort'];
			}

			// Sort the resources with sortkey ascending
			// Add $resources as the last parameter, to sort by the common key
			array_multisort($sort, SORT_ASC, $resources);
			$bookings = $this->_split_multi_day_bookings($bookings, $from, $to);
			$results = build_schedule_table($bookings, $resources);
			return array('total_records'=>count($results), 'results'=>$results);
		}

		/**
		 * Return a resource's schedule for a given week in a YUI DataSource
		 * compatible format
		 * 
		 * @param int	$resource_id
		 * @param $date 
		 *
		 * @return array containg values from $array for the keys in $keys.
		 */
		function resource_schedule($resource_id, $date)
		{
			$from = clone $date;
			$from->setTime(0, 0, 0);
			// Make sure $from is a monday
			if($from->format('w') != 1)
			{
				$from->modify('last monday');
			}
			$to = clone $from;
			$to->modify('+7 days');
			$resource = $this->resource_so->read_single($resource_id);
			$allocation_ids = $this->so->allocation_ids_for_resource($resource_id, $from, $to);
			$allocations = $this->allocation_so->read(array('filters'=> array('id' => $allocation_ids)));
			$allocations = $allocations['results'];
			foreach($allocations as &$allocation)
			{
				$allocation['name'] = $allocation['organization_name'];
				$allocation['shortname'] = $allocation['organization_shortname'];
				$allocation['type'] = 'allocation';
			}
			$booking_ids = $this->so->booking_ids_for_resource($resource_id, $from, $to);
			$bookings = $this->so->read(array('filters'=> array('id' => $booking_ids)));
			$bookings = $bookings['results'];
			foreach($bookings as &$booking)
			{
				$booking['name'] = $booking['group_name'];
				$booking['shortname'] = $booking['group_shortname'];
				$booking['type'] = 'booking';
                unset($booking['audience']);
                unset($booking['agegroups']);
			}
			$allocations = $this->split_allocations($allocations, $bookings);

			$event_ids = $this->so->event_ids_for_resource($resource_id, $from, $to);
			$events = $this->event_so->read(array('filters'=> array('id' => $event_ids)));
			$events = $events['results'];
			foreach($events as &$event)
			{
				$event['name'] = $event['description'];
				$event['type'] = 'event';
                unset($event['comments']);
                unset($event['audience']);
                unset($event['agegroups']);
			}
			$bookings = array_merge($allocations, $bookings);
			$bookings = $this->_remove_event_conflicts($bookings, $events);
			$bookings = array_merge($events, $bookings);
			
			$bookings = $this->_split_multi_day_bookings($bookings, $from, $to);
			$results = build_schedule_table($bookings, array($resource));
			return array('total_records'=>count($results), 'results'=>$results);
		}

		/**
		 * Split allocations overlapped by bookings into multiple allocations
		 * to avoid overlaps
		 */
		function split_allocations($allocations, $all_bookings)
		{
			function get_from2($a) {return $a['from_'];};
			function get_to2($a) {return $a['to_'];};
			$new_allocations = array();
			foreach($allocations as $allocation)
			{
				// $ Find all associated bookings
				$bookings = array();
				foreach($all_bookings as $b)
				{
					if($b['allocation_id'] == $allocation['id'])
						$bookings[] = $b;
				}
				$times = array($allocation['from_'], $allocation['to_']);
				$times = array_merge(array_map("get_from2", $bookings), $times);
				$times = array_merge(array_map("get_to2", $bookings), $times);
				$times = array_unique($times);
				sort($times);
				while(count($times) >= 2)
				{
					$from_ = $times[0];
					$to_ = $times[1];
					$resources = $allocation['resources'];
					$used = array();
					foreach($all_bookings as $b)
					{
						
						if(($b['from_'] >= $from_ && $b['from_'] < $to_) || ($b['to_'] > $from_ && $b['to_'] <= $to_) || ($b['from_'] <= $from_ && $b['to_'] >= $to_))
							$resources = array_minus($resources, $b['resources']);
					}
					if($resources)
					{
						$a = $allocation;
						$a['from_'] = $times[0];
						$a['to_'] = $times[1];
						$new_allocations[] = $a;
					}
					array_shift($times);
				}
			}
			return $new_allocations;
		}

		/**
		 * Split Multi-day bookings into separate single-day bookings
		**/
		function _split_multi_day_bookings($bookings, $t0, $t1)
		{
			if($t1->format('H:i') == '00:00')
				$t1->modify('-1 day');
			$new_bookings = array();
			foreach($bookings as $booking)
			{
				$from = new DateTime($booking['from_']);
				$to = new DateTime($booking['to_']);
				// Basic one-day booking
				if($from->format('Y-m-d') == $to->format('Y-m-d'))
				{
					$booking['date'] = $from->format('Y-m-d');
					$booking['wday']  = date_format(date_create($booking['date']), 'D');
					$booking['from_'] = $from->format('H:i');
					$booking['to_'] = $to->format('H:i');
					// We need to use 24:00 instead of 00:00 to sort correctly
					$booking['to_'] = $booking['to_'] == '00:00' ? '24:00' : $booking['to_'];
					$new_bookings[] = $booking;
				}
				// Multi-day booking
				else
				{
					$start = clone max($from, $t0);
					$end = clone min($to, $t1);
					$date = clone $start;
					do
					{
						$new_booking = $booking;
						$new_booking['date'] = $date->format('Y-m-d');
						$new_booking['wday']  = date_format($date, 'D');
						$new_booking['from_'] = '00:00';
						$new_booking['to_'] = '00:00';
						if($new_booking['date'] == $from->format('Y-m-d'))
						{
							$new_booking['from_'] = $from->format('H:i');
						}
						else if($new_booking['date'] == $to->format('Y-m-d'))
						{
							$new_booking['to_'] = $to->format('H:i');
						}
						// We need to use 24:00 instead of 00:00 to sort correctly
						$new_booking['to_'] = $new_booking['to_'] == '00:00' ? '24:00' : $new_booking['to_'];
						$new_bookings[] = $new_booking;
						if($date->format('Y-m-d') == $end->format('Y-m-d'))
							break;
						$date->modify('+1 day');
					}
					while(true);
				}
			}
			return $new_bookings;
		}

		function _remove_event_conflicts($bookings, &$events)
		{
			foreach($events as &$e)
			{
				$e['conflicts'] = array();
			}
			$new_bookings = array();
			foreach($bookings as $b)
			{
                
				$keep = true;
				foreach($events as &$e)
				{
					if((($b['from_'] >= $e['from_'] && $b['from_'] < $e['to_']) || 
					   ($b['to_'] > $e['from_'] && $b['to_'] <= $e['to_']) || 
					   ($b['from_'] <= $e['from_'] && $b['to_'] >= $e['to_'])) && (array_intersect($b['resources'], $e['resources']) != array()))
					{
						$keep = false;

						$e['conflicts'][] = $b;

                        $bf = $b['from_'];
                        $bt = $b['to_'];            

                        if ($bf < $e['from_'])
                        {
                            $b['to_'] = $e['from_'];                       
                            $b['from_'] = $bf;                       
                            $new_bookings[] = $b;        
                        }

                        if ($e['to_'] < $bt)
                        {
                            $b['from_'] = $e['to_'];                       
                            $b['to_'] = $bt;                       
                            $new_bookings[] = $b;        
                        }

						break;
					}
				}
				if($keep)
				{
					$new_bookings[] = $b;
				}
			}
			return $new_bookings;
		}
		
		public function complete_expired(&$bookings) {
			$this->so->complete_expired($bookings);
		}
		
		public function find_expired() {
			return $this->so->find_expired();
		}

		function validate(&$entry)
		{
			$entry['allocation_id'] = $this->so->calculate_allocation_id($entry);
			return parent::validate($entry);
		}
		
	}
