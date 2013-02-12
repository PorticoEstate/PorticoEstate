<?php
  /**************************************************************************\
  * phpGroupWare - Calendar                                                  *
  * http://www.phpgroupware.org                                              *
  * Based on Webcalendar by Craig Knudsen <cknudsen@radix.net>               *
  *          http://www.radix.net/~cknudsen                                  *
  * Modified by Mark Peters <skeeter@phpgroupware.org>                       *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	phpgw::import_class('phpgwapi.datetime');

	class calendar_bocalendar
	{
		var $public_functions = array
		(
			'read_entry'      => True,
			'delete_entry'    => True,
			'delete_calendar' => True,
			'change_owner'    => True,
			'update'          => True,
			'check_set_default_prefs' => True,
			'store_to_cache'  => True,
			'export_event'    => True,
			'send_alarm'      => True,
			'reinstate'       => True
		);

		var $soap_functions = array(
			'read_entry' => array(
				'in' => array(
					'int'
				),
				'out' => array(
					'SOAPStruct'
				)
			),
			'delete_entry' => array(
				'in' => array(
					'int'
				),
				'out' => array(
					'int'
				)
			),
			'delete_calendar' => array(
				'in' => array(
					'int'
				),
				'out' => array(
					'int'
				)
			),
			'change_owner' => array(
				'in' => array(
					'array'
				),
				'out' => array(
					'int'
				)
			),
			'update' => array(
				'in' => array(
					'array',
					'array',
					'array',
					'array',
					'array'
				),
				'out' => array(
					'array'
				)
			),
			'store_to_cache'	=> array(
				'in' => array(
					'struct'
				),
				'out' => array(
					'SOAPStruct'
				)
			),
			'store_to_cache'	=> array(
				'in' => array(
					'array'
				),
				'out' => array(
					'string'
				)
			)
		);

		var $debug = False;
//		var $debug = True;

		var $so;
		var $contacts;

		var $cached_events;
		var $repeating_events;
		var $day;
		var $month;
		var $year;
		var $prefs;
		var $date;

		var $owner;
		var $holiday_class = 'cal_holiday';

		var $cached_holidays;

		var $g_owner = array();
		
		var $filter;
		var $cat_id;
		var $users_timeformat;
		
		var $modified;
		var $deleted;
		var $added;

		var $is_group = False;

		var $soap = False;
		
		var $use_session = False;

		var $today;
		var $debug_string;

		var $sortby;
		var $num_months;

		var $save_owner;
		var $return_to;

		protected $_jscal;

		public function __construct($session = false)
		{
			$this->cat = CreateObject('phpgwapi.categories');
			$this->contacts = createObject('phpgwapi.contacts');
			$this->grants = $GLOBALS['phpgw']->acl->get_grants('calendar','.');

			if(DEBUG_APP)
			{
				if(floor(phpversion()) >= 4)
				{
					$this->debug_string = '';
					ob_start();
				}	

				foreach($this->grants as $grantor => $rights)
				{
					print_debug('Grantor', $grantor);
					print_debug('Rights', $rights);
				}
			}

			print_debug('Read use_session',$session);

			if($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}
			print_debug('BO Filter', $this->filter);
			print_debug('Owner', $this->owner);

			$this->prefs['calendar']    = $GLOBALS['phpgw_info']['user']['preferences']['calendar'];
			$this->check_set_default_prefs();

			$owner = phpgw::get_var('owner', 'int');
			if ( !$owner )
			{	
				$owner = $this->contacts->is_contact($GLOBALS['phpgw_info']['user']['account_id']);
			}
//_debug_array($owner);
			$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			ereg('menuaction=([a-zA-Z.]+)', $referer, $regs);
			$from = $regs[1];
			if ((substr($_SERVER['PHP_SELF'],-8) == 'home.php' && substr($this->prefs['calendar']['defaultcalendar'],0,7) == 'planner'
				 || ( isset($GLOBALS['phpgw_info']['menuaction']) && $GLOBALS['phpgw_info']['menuaction'] == 'calendar.uicalendar.planner')
				 && $from  != 'calendar.uicalendar.planner' && !$this->save_owner)
				 && intval($this->prefs['calendar']['planner_start_with_group']) > 0)
			{
				// entering planner for the first time ==> saving owner in save_owner, setting owner to default
				//
	//			$this->save_owner = $this->owner;
	//			$owner = 'g_'.$this->prefs['calendar']['planner_start_with_group'];

				$owner = 'g_' . $this->prefs['calendar']['planner_start_with_group'];
				$this->owner = $owner;
				$this->save_owner = $this->owner;
			}
			else if ( isset($GLOBALS['phpgw_info']['menuaction'])
				&& $GLOBALS['phpgw_info']['menuaction'] != 'calendar.uicalendar.planner'
				&& $this->save_owner )
			{
				// leaving planner with an unchanged user/owner ==> setting owner back to save_owner
				//
				$owner = phpgw::get_var('owner', 'int', 'GET', $this->save_owner);
				unset($this->save_owner);
			}
			elseif (!empty($owner) && $owner != $this->owner && $from == 'calendar.uicalendar.planner')
			{
				// user/owner changed within planner ==> forgetting save_owner
				//
				unset($this->save_owner);
			}
			
			if( isset($owner) && $owner != '' && substr($owner,0,2) == 'g_')
			{
				$this->set_owner_to_group(substr($owner,2));
			}
			elseif(isset($owner) && $owner!='')
			{
				$this->owner = intval($owner);
			}
			elseif(!isset($this->owner) || !$this->owner)
			{
				$this->owner = $this->contacts->is_contact($GLOBALS['phpgw_info']['user']['account_id']);
			}

			$this->prefs['common'] = $GLOBALS['phpgw_info']['user']['preferences']['common'];

			if ($this->prefs['common']['timeformat'] == '12')
			{
				$this->users_timeformat = 'h:ia';
			}
			else
			{
				$this->users_timeformat = 'H:i';
			}

			$friendly = phpgw::get_var('friendly', 'bool', 'REQUEST'); 

			$this->filter = phpgw::get_var('filter', 'string', 'POST', " {$this->prefs['calendar']['defaultfilter']} ");
			$this->sortby = phpgw::get_var('sortby', 'string', 'POST', $this->prefs['calendar']['defaultcalendar'] == 'planner_user' ? 'user' : 'category');
			$this->cat_id = phpgw::get_var('cat_id', 'int', 'POST');

			if(isset($this->g_owner) && $this->g_owner)
			{
				$this->filter = ' all ';
			}

			$this->so = CreateObject('calendar.socalendar', array
			(
				'owner'		=> $this->owner,
				'filter'	=> $this->filter,
				'category'	=> $this->cat_id,
				'g_owner'	=> $this->g_owner
			));

			$this->rpt_day = array
			(	// need to be after creation of socalendar
				MCAL_M_SUNDAY    => 'Sunday',
				MCAL_M_MONDAY    => 'Monday',
				MCAL_M_TUESDAY   => 'Tuesday',
				MCAL_M_WEDNESDAY => 'Wednesday',
				MCAL_M_THURSDAY  => 'Thursday',
				MCAL_M_FRIDAY    => 'Friday',
				MCAL_M_SATURDAY  => 'Saturday'
			);
			if ( !isset($this->bo->prefs['calendar']['weekdaystarts'])
				|| $this->bo->prefs['calendar']['weekdaystarts'] != 'Sunday')
			{
				$mcals = array_keys($this->rpt_day);
				$days  = array_values($this->rpt_day);
				$this->rpt_day = array();
				list($n) = $found = array_keys($days,$this->prefs['calendar']['weekdaystarts']);
				for ($i = 0; $i < 7; ++$i,++$n)
				{
					$this->rpt_day[$mcals[$n % 7]] = $days[$n % 7];
				}
			}
			$this->rpt_type = array(
				MCAL_RECUR_NONE		=> 'None',
				MCAL_RECUR_DAILY	=> 'Daily',
				MCAL_RECUR_WEEKLY	=> 'Weekly',
				MCAL_RECUR_MONTHLY_WDAY	=> 'Monthly (by day)',
				MCAL_RECUR_MONTHLY_MDAY	=> 'Monthly (by date)',
				MCAL_RECUR_YEARLY	=> 'Yearly'
			);
			
			$localtime = phpgwapi_datetime::user_localtime();

			$date = phpgw::get_var('date', 'int');

			$year = phpgw::get_var('year', 'int');

			$month = phpgw::get_var('month', 'int');

			$day = phpgw::get_var('day', 'int');

			$num_months = phpgw::get_var('num_months', 'int');

			if ( $date )
			{
				$this->year = substr($date, 0, 4);
				$this->month = substr($date, 4, 2);
				$this->day = substr($date, 6, 2);
			}
			else
			{
				if ( $year )
				{
					$this->year = $year;
				}
				else
				{
					$this->year = date('Y', $localtime);
				}
				if ( $month )
				{
					$this->month = $month;
				}
				else
				{
					$this->month = date('m', $localtime);
				}
				if ( $day )
				{
					$this->day = $day;
				}
				else
				{
					$this->day = date('d', $localtime);
				}
			}

			if ( $num_months )
			{
				$this->num_months = $num_months;
			}
			else
			{
				$this->num_months = 1;
			}

			$this->today = date('Ymd', $localtime );

			if(DEBUG_APP)
			{
				print_debug('BO Filter','('.$this->filter.')');
				print_debug('Owner',$this->owner);
				print_debug('Today',$this->today);
				if(floor(phpversion()) >= 4)
				{
					$this->debug_string .= ob_get_contents();
					ob_end_clean();
				}
			}
		}

		function list_methods($_type='xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			*/
			if (is_array($_type))
			{
				$_type = $_type['type'];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
 						),
						'read_entry' => array(
							'function'  => 'read_entry',
							'signature' => array(array(xmlrpcStruct,xmlrpcInt)),
							'docstring' => lang('Read a single entry by passing the id and fieldlist.')
						),
						'add_entry' => array(
							'function'  => 'update',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Add a single entry by passing the fields.')
						),
						'update_entry' => array(
							'function'  => 'update',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Update a single entry by passing the fields.')
						),
						'delete_entry' => array(
							'function'  => 'delete_entry',
							'signature' => array(array(xmlrpcInt,xmlrpcInt)),
							'docstring' => lang('Delete a single entry by passing the id.')
						),
						'delete_calendar' => array(
							'function'  => 'delete_calendar',
							'signature' => array(array(xmlrpcInt,xmlrpcInt)),
							'docstring' => lang('Delete an entire users calendar.')
						),
						'change_owner' => array(
							'function'  => 'change_owner',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Change all events for $params[\'old_owner\'] to $params[\'new_owner\'].')
						),
						'store_to_cache' => array(
							'function'  => 'store_to_cache',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Read a list of entries.')
						),
						'export_event' => array(
							'function'  => 'export_event',
							'signature' => array(array(xmlrpcString,xmlrpcStruct)),
							'docstring' => lang('Export a list of entries in iCal format.')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
					break;
			}
		}

		function set_owner_to_group($owner)
		{
			print_debug('calendar::bocalendar::set_owner_to_group:owner',$owner);
			$this->owner = intval($owner);
			$this->is_group = True;
			settype($this->g_owner,'array');
			$this->g_owner = array();
			$group_owners = $GLOBALS['phpgw']->accounts->member($owner);
			if ( is_array($group_owners) && count($group_owners) )
			{
				foreach ( $group_owners as $group_info )
				{
					if($account_id = $this->contacts->is_contact($group_info['account_id']))
					{
						$this->g_owner[] = $account_id;
					}

			//		$this->g_owner[] = $this->contacts->is_contact($group_info['account_id']);
				}
			}
		}

		function member_of_group($owner=0)
		{
			$owner = ( $owner == 0 ? $GLOBALS['phpgw_info']['user']['account_id'] : $owner);
			$group_owners = $GLOBALS['phpgw']->accounts->membership();
			while($group_owners && list($index,$group_info) = each($group_owners))
			{
				if($this->owner == $this->contacts->is_contact($group_info['account_id']) )
				{
					return True;
				}
			}
			return False;
		}

		function save_sessiondata($data='')
		{
			if ($this->use_session)
			{
				if (!is_array($data))
				{
					$data = array(
						'filter'     => $this->filter,
						'cat_id'     => $this->cat_id,
						'owner'      => $this->owner,
						'save_owner' => isset($this->save_owner)?$this->save_owner:'',
						'year'       => $this->year,
						'month'      => $this->month,
						'day'        => $this->day,
						'date'       => $this->date,
						'sortby'     => $this->sortby,
						'num_months' => $this->num_months,
						'return_to'  => $this->return_to
					);
				}
				if($this->debug)
				{
					if(floor(phpversion()) >= 4)
					{
						ob_start();
					}
					echo '<!-- '."\n".'Save:'."\n"._debug_array($data,False)."\n".' -->'."\n";
					if(floor(phpversion()) >= 4)
					{
						$this->debug_string .= ob_get_contents();
						ob_end_clean();
					}
				}
				$GLOBALS['phpgw']->session->appsession('session_data','calendar',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','calendar');
			print_debug('Read',_debug_array($data,False));
			// no data is returned as an empty string
			if ( !$data )
			{
				return;
			}

			$this->filter = $data['filter'];
			$this->cat_id = $data['cat_id'];
			$this->sortby = isset($data['sortby']) && $data['sortby'] ? $data['sortby']:'';
			$this->owner  = (int) $data['owner'];
			$this->save_owner = isset($data['save_owner']) ? (int) $data['save_owner'] : 0;
			$this->year   = (int) $data['year'];
			$this->month  = (int) $data['month'];
			$this->day    = (int) $data['day'];
			$this->num_months = isset($data['num_months']) ? (int) $data['num_months'] : 0;
			$this->return_to = isset($data['return_to']) ? $data['return_to'] : array();
		}

		function read_entry($id = 0)
		{
			$id = (int) $id;
			if ( !$id )
			{
				$id = phpgw::get_var('id', 'int', 'GET');
			}

			if($this->check_perms(PHPGW_ACL_READ,$id))
			{
				$event = $this->so->read_entry($id);
				
				if(!isset($event['participants'][$this->owner]) && $this->user_is_a_member($event,$this->owner))
				{
					$this->so->add_attribute('participants','U',intval($this->owner));
					$this->so->add_entry($event);
					$event = $this->get_cached_event();
				}
				return $event;
			}
		}

		function delete_single($param)
		{
			if($this->check_perms(PHPGW_ACL_DELETE,intval($param['id'])))
			{
				$temp_event = $this->get_cached_event();
				$event = $this->read_entry(intval($param['id']));
//				if($this->owner == $event['owner'])
//				{
				$exception_time = mktime($event['start']['hour'],$event['start']['min'],0,$param['month'],$param['day'],$param['year']) - phpgwapi_datetime::user_timezone();
				$event['recur_exception'][] = intval($exception_time);
				$this->so->cal->event = $event;
//				print_debug('exception time',$event['recur_exception'][count($event['recur_exception']) -1]);
//				print_debug('count event exceptions',count($event['recur_exception']));
				$this->so->add_entry($event);
				$cd = 16;
				
				$this->so->cal->event = $temp_event;
				unset($temp_event);
			}
			else
			{
				$cd = 60;
			}
//			}
			return $cd;
		}

		function delete_entry($id)
		{
			if($this->check_perms(PHPGW_ACL_DELETE,$id))
			{
//				$temp_event = $this->read_entry($id);
//				if($this->owner == $temp_event['owner'])
//				{
				$this->so->delete_entry($id);
				$cd = 16;
			}
			else
			{
				$cd = 60;
			}
//			}
			return $cd;
		}

		function reinstate($params='')
		{
			if($this->check_perms(PHPGW_ACL_EDIT,$params['cal_id']) && isset($params['reinstate_index']))
			{
				$event = $this->so->read_entry($params['cal_id']);
				@reset($params['reinstate_index']);
				print_debug('Count of reinstate_index',count($params['reinstate_index']));
				if(count($params['reinstate_index']) > 1)
				{
					while(list($key,$value) = each($params['reinstate_index']))
					{
						print_debug('reinstate_index ['.$key.']',intval($value));
						print_debug('exception time',$event['recur_exception'][intval($value)]);
						unset($event['recur_exception'][intval($value)]);
						print_debug('count event exceptions',count($event['recur_exception']));
				 	}
				}
				else
				{
					print_debug('reinstate_index[0]',intval($params['reinstate_index'][0]));
					print_debug('exception time',$event['recur_exception'][intval($params['reinstate_index'][0])]);
					unset($event['recur_exception'][intval($params['reinstate_index'][0])]);
					print_debug('count event exceptions',count($event['recur_exception']));
				}
				$this->so->cal->event = $event;
				$this->so->add_entry($event);
				return 42;
			}
			else
			{
				return 43;
			}
		}

		function delete_calendar($owner)
		{
			if($GLOBALS['phpgw_info']['user']['apps']['admin'])
			{
				$this->so->delete_calendar($this->contacts->is_contact($owner));
			}
		}

		function change_owner($params='')
		{
			if($GLOBALS['phpgw_info']['server']['calendar_type'] == 'sql')
			{
				if(is_array($params))
				{
					$this->so->change_owner($this->contacts->is_contact($params['old_owner']),
								$this->contacts->is_contact($params['new_owner']));
				}
			}
		}

		function expunge()
		{
			reset($this->so->cal->deleted_events);
			while(list($i,$event_id) = each($this->so->cal->deleted_events))
			{
				$event = $this->so->read_entry($event_id);
				if($this->check_perms(PHPGW_ACL_DELETE,$event))
				{
					$this->send_update(MSG_DELETED,$event['participants'],$event);
				}
				else
				{
					unset($this->so->cal->deleted_events[$i]);
				}
			}
			$this->so->expunge();
		}

		//FIXME fix security
		function search_keywords($keywords)
		{
			if($this->is_group) 
			{
				$members = $GLOBALS['phpgw']->acl->get_ids_for_location($this->owner, 1, 'phpgw_group');
			}
			else
			{
				$members = array_keys($this->grants);

				if (!in_array($this->owner,$members))
				{
					$members[] = $this->owner;
				}
			}
			foreach($members as $n => $uid)
			{
				if (!($this->grants[$uid] & PHPGW_ACL_READ))
				{
					unset($members[$n]);
				}
			}
			return $this->so->list_events_keyword($keywords,$members);
		}

		function update($params = '')
		{
			$l_cal = isset($params['cal']) && $params['cal'] ? $params['cal'] : phpgw::get_var('cal', 'string', 'POST');
			$l_participants = isset($params['participants']) ? $params['participants'] : phpgw::get_var('participants', 'string', 'POST');
			$l_categories = isset($params['categories']) ? $params['categories'] : phpgw::get_var('categories', 'string', 'POST');
			$l_start = isset($params['start']) && $params['start'] ? $params['start'] : phpgw::get_var('start', 'string', 'POST');
			$l_end = isset($params['end']) && $params['end'] ? $params['end'] : phpgw::get_var('end', 'string', 'POST');
			$l_recur_enddate = isset($params['recur_enddate']) && $params['recur_enddate'] ? $params['recur_enddate'] : phpgw::get_var('recur_enddate', 'string', 'POST'); // probbaly can be bool
			$l_recur_exception = explode(',', phpgw::get_var('recur_exception', 'string', 'POST') );

			$send_to_ui = true;
			if($this->debug)
			{
				$send_to_ui = true;
			}

			/* no idea what is meant to happen here and triggers a lot of notices
			if($p_cal || $p_participants || $p_start || $p_end || $p_recur_enddata)
			{
				$send_to_ui = false;
			}
			*/
			
			print_debug('ID',$l_cal['id']);

			if( phpgw::get_var('readsess', 'bool', 'GET') )
			{
				$event = $this->restore_from_appsession();
				$event['title'] = stripslashes($event['title']);
				$event['description'] = stripslashes($event['description']);
				$datetime_check = $this->validate_update($event);
				if($datetime_check)
				{
					ExecMethod('calendar.uicalendar.edit',
						array(
							'cd'		=> $datetime_check,
							'readsess'	=> 1
						)
					);
					$GLOBALS['phpgw']->common->phpgw_exit(True);
				}
				$overlapping_events = False;
			}
			else
			{
				if((!$l_cal['id'] && !$this->check_perms(PHPGW_ACL_ADD)) || ($l_cal['id'] && !$this->check_perms(PHPGW_ACL_EDIT,$l_cal['id'])))
				{
					ExecMethod('calendar.uicalendar.index');
					$GLOBALS['phpgw']->common->phpgw_exit();
				}

				print_debug('Prior to fix_update_time()');
				$this->fix_update_time($l_start);
				$this->fix_update_time($l_end);

				if(!isset($l_cal['private']))
				{
					$l_cal['private'] = 'public';
				}

				if(!isset($l_categories))
				{
					$l_categories = array();
					$l_categories[] = 0;
				}

				$is_public = ($l_cal['private'] == 'public' ? 1 : 0);
				$this->so->event_init();
				if ( is_array($l_categories) && count($l_categories) >= 2)
				{
					$this->so->set_category(implode(',',$l_categories));
				}
				else
				{
					$this->so->set_category(strval($l_categories[0]));
				}
				$this->so->set_title($l_cal['title']);
				$this->so->set_description($l_cal['description']);
				$this->so->set_start($l_start['year'],$l_start['month'],$l_start['mday'],$l_start['hour'],$l_start['min'],0);
				$this->so->set_end($l_end['year'],$l_end['month'],$l_end['mday'],$l_end['hour'],$l_end['min'],0);
				$this->so->set_class($is_public);
				$this->so->add_attribute('reference',(@isset($l_cal['reference']) && $l_cal['reference']?$l_cal['reference']:0));
				$this->so->add_attribute('location',(@isset($l_cal['location']) && $l_cal['location']?$l_cal['location']:''));
				if($l_cal['id'])
				{
					$this->so->add_attribute('id',$l_cal['id']);
				}

				if ( !isset($l_cal['rpt_use_end'])
					|| $l_cal['rpt_use_end'] != 'y')
				{
					$l_recur_enddate['year'] = 0;
					$l_recur_enddate['month'] = 0;
					$l_recur_enddate['mday'] = 0;
				}
				elseif (isset($l_recur_enddate['str']))
				{
					$l_recur_enddate = $this->_jscal->input2date($l_recur_enddate['str'],False,'mday');
				}

				switch(intval($l_cal['recur_type']))
				{
					case MCAL_RECUR_NONE:
						$this->so->set_recur_none();
						break;
					case MCAL_RECUR_DAILY:
						$this->so->set_recur_daily(intval($l_recur_enddate['year']),intval($l_recur_enddate['month']),intval($l_recur_enddate['mday']),intval($l_cal['recur_interval']));
						break;
					case MCAL_RECUR_WEEKLY:
						$l_cal['recur_data'] = intval($l_cal['rpt_sun']) + intval($l_cal['rpt_mon']) + intval($l_cal['rpt_tue']) + intval($l_cal['rpt_wed']) + intval($l_cal['rpt_thu']) + intval($l_cal['rpt_fri']) + intval($l_cal['rpt_sat']);
						if (is_array($l_cal['rpt_day']))
						{
							foreach ($l_cal['rpt_day'] as $mask)
							{
								$l_cal['recur_data'] |= intval($mask);
							}
						}
						$this->so->set_recur_weekly(intval($l_recur_enddate['year']),intval($l_recur_enddate['month']),intval($l_recur_enddate['mday']),intval($l_cal['recur_interval']),$l_cal['recur_data']);
						break;
					case MCAL_RECUR_MONTHLY_MDAY:
						$this->so->set_recur_monthly_mday(intval($l_recur_enddate['year']),intval($l_recur_enddate['month']),intval($l_recur_enddate['mday']),intval($l_cal['recur_interval']));
						break;
					case MCAL_RECUR_MONTHLY_WDAY:
						$this->so->set_recur_monthly_wday(intval($l_recur_enddate['year']),intval($l_recur_enddate['month']),intval($l_recur_enddate['mday']),intval($l_cal['recur_interval']));
						break;
					case MCAL_RECUR_YEARLY:
						$this->so->set_recur_yearly(intval($l_recur_enddate['year']),intval($l_recur_enddate['month']),intval($l_recur_enddate['mday']),intval($l_cal['recur_interval']));
						break;
				}

				if ( is_array($l_participants) && count($l_participants) )
				{
					$parts = $l_participants;
					$minparts = min($l_participants);
					foreach ( $l_participants as $participant )
					{
						if ( substr($participant, 0, 2) == 'g_' )
						{
							$members = $GLOBALS['phpgw']->accounts->member(substr($participant, 2) );
							if ( is_array($members) && count($members) )
							{
								foreach ( $members as $member )
								{
									$participant = intval($this->contacts->is_contact($member['account_id']) );
									if ( $participant )
									{
										$this->so->add_attribute('participants', $accept_type, $participant);
									}
								}
							}
						}
						else
						{
							if (($accept_type = substr($participant, -1, 1)) == '0' || intval($accept_type) > 0)
							{
								$accept_type = 'U';
							}
							$this->so->add_attribute('participants', $accept_type, (int) $participant);
						}
					}
				}

				$event = $this->get_cached_event();
				if(!is_int($minparts))
				{
					$minparts = $this->owner;
				}
				if(!isset($event['participants'][$l_cal['owner']]))
				{
					$this->so->add_attribute('owner',$minparts);
				}
				else
				{
					$this->so->add_attribute('owner',$l_cal['owner']);
				}
				$this->so->add_attribute('priority',$l_cal['priority']);

				foreach($l_cal as $name => $value)
				{
					if ($name[0] == '#')	// Custom field
					{
						$this->so->add_attribute($name,stripslashes($value));
					}
				}
				
				$preserved = unserialize(phpgw::get_var('preserved', 'raw', 'POST'));
				if ( is_array($preserved) )
				{
					foreach($preserved as $name => $value)
					{
						switch($name)
						{
							case 'owner':
								$this->so->add_attribute('participants', (int) $value, $l_cal['owner']);
								break;
							default:
								$this->so->add_attribute($name, phpgw::clean_value($value, 'string') );
						}
					}
				}
				$event = $this->get_cached_event();

				if ($l_cal['alarmdays'] > 0 || $l_cal['alarmhours'] > 0 ||
						$l_cal['alarmminutes'] > 0)
				{
					$time = $this->maketime($event['start']) -
						($l_cal['alarmdays'] * 24 * 3600) -
						($l_cal['alarmhours'] * 3600) -
						($l_cal['alarmminutes'] * 60);

					$event['alarm'][] = array(
						'time'    => $time,
						'owner'   => $this->owner,
						'enabled' => 1
					);
				}

				$event['recur_exception'] = $l_recur_exception;

				$this->store_to_appsession($event);
				$datetime_check = $this->validate_update($event);
				print_debug('bo->validated_update() returnval',$datetime_check);
				if($datetime_check)
				{
				   ExecMethod('calendar.uicalendar.edit',
				   	array(
				   		'cd'		=> $datetime_check,
				   		'readsess'	=> 1
				   	)
				   );
					$GLOBALS['phpgw']->common->phpgw_exit(True);
				}

				if ( isset($event['id']) )
				{
					$event_ids[] = $event['id'];
				}

				if ( isset($event['reference']) )
				{
					$event_ids[] = $event['reference'];
				}

				$overlapping_events = $this->overlap(
					$this->maketime($event['start']),
					$this->maketime($event['end']),
					$event['participants'],
					$event['owner'],
					$event_ids
				);
			}
			if($overlapping_events)
			{
				if($send_to_ui)
				{
					unset($GLOBALS['phpgw_info']['flags']['noheader']);
					unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
					ExecMethod('calendar.uicalendar.overlap',
				   		array(
				   			'o_events'	=> $overlapping_events,
				   			'this_event'	=> $event
				   		)
					);
					$GLOBALS['phpgw']->common->phpgw_exit(True);
				}
				else
				{
					return $overlapping_events;
				}
			}
			else
			{
				if ( !isset($event['id']) )
				{
					$this->so->cal->event = $event;
					$this->so->add_entry($event);
					$this->send_update(MSG_ADDED,$event['participants'],'',$this->get_cached_event());
				}
				else
				{
					print_debug('Updating Event ID',$event['id']);
					$new_event = $event;
					$old_event = $this->read_entry($event['id']);
					$this->so->cal->event = $event;
					$this->so->add_entry($event);
					$this->prepare_recipients($new_event,$old_event);
				}
				$date = sprintf("%04d%02d%02d",$event['start']['year'],$event['start']['month'],$event['start']['mday']);
				if($send_to_ui)
				{
					$this->read_sessiondata();
					if ($this->return_to)
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', $this->return_to);
						$GLOBALS['phpgw']->common->phpgw_exit();
					}
					Execmethod('calendar.uicalendar.index');
//					$GLOBALS['phpgw']->common->phpgw_exit();
				}
			}
		}

		/* Private functions */
		function read_holidays($year=0)
		{
			if(!$year)
			{
				$year = $this->year;
			}
			$holiday = CreateObject('calendar.boholiday');
			
			$account = $this->contacts->are_users($this->owner);
	//		_debug_array($account[0]['account_id']);
			
			$holiday->prepare_read_holidays($year,$account[0]['account_id']);
			$this->cached_holidays = $holiday->read_holiday();
			unset($holiday);
		}

		function user_is_a_member($event, $contact)
		{
			$uim = False;
			$security_equals = $GLOBALS['phpgw']->accounts->membership($GLOBALS['phpgw']->accounts->search_person($contact));

			if ( !is_array($event['participants']) )
			{
				return false;
			}
			
			while(!$uim && $event['participants'] && $security_equals && list($participant,$status) = each($event['participants']))
			{
				if($GLOBALS['phpgw']->accounts->get_type($participant) == 'g')
				{
					@reset($security_equals);
					while(list($key,$group_info) = each($security_equals))
					{
						if($group_info['account_id'] == $participant)
						{
							return True;
							$uim = True;
						}
					}
				}
			}
			return $uim;
		}

		function maketime($time)
		{
			return mktime($time['hour'],$time['min'],$time['sec'],$time['month'],$time['mday'],$time['year']);
		}

		/**
		 * returns a date-array suitable for the start- or endtime of an event from a timestamp
		*
		 * @param $time the timestamp for the values of the array
		 * @param $alarm (optional) alarm field of the array, defaults to 0
		 * @author ralfbecker
		 */
		function time2array($time,$alarm = 0)
		{
			return array(
				'year'  => intval(date('Y',$time)),
				'month' => intval(date('m',$time)),
				'mday'  => intval(date('d',$time)),
				'hour'  => intval(date('H',$time)),
				'min'   => intval(date('i',$time)),
				'sec'   => intval(date('s',$time)),
				'alarm' => intval($alarm)
			);
		}

		/**
		 * set the start- and enddates of a recuring event for a recur-date
		*
		 * @param $event the event which fields to set (has to be the original event for start-/end-times)
		 * @param $date  the recuring date in form 'Ymd', eg. 20030226
		 * @author ralfbecker
		 */
		function set_recur_date(&$event,$date)
		{
			$org_start = $this->maketime($event['start']);
			$org_end   = $this->maketime($event['end']);
			$start = mktime($event['start']['hour'],$event['start']['min'],0,substr($date,4,2),substr($date,6,2),substr($date,0,4));
			$end   = $org_end + $start - $org_start;
			$event['start'] = $this->time2array($start);
			$event['end']   = $this->time2array($end);
		}

		function fix_update_time(&$time_param)
		{
			if (isset($time_param['str']))
			{
				if (!is_object($this->_jscal))
				{
					$this->_jscal = CreateObject('phpgwapi.jscalendar');
				}
				$time_param += $this->_jscal->input2date($time_param['str'],False,'mday');
				unset($time_param['str']);
			}
			if ($this->prefs['common']['timeformat'] == '12')
			{
				if ($time_param['ampm'] == 'pm')
				{
					if ($time_param['hour'] <> 12)
					{
						$time_param['hour'] += 12;
					}
				}
				elseif ($time_param['ampm'] == 'am')
				{
					if ($time_param['hour'] == 12)
					{
						$time_param['hour'] -= 12;
					}
				}
		
				if($time_param['hour'] > 24)
				{
					$time_param['hour'] -= 12;
				}
			}
		}

		/**
		* Ensure an event is valid
		*
		* @param array $event event data
		* @return int 0 for ok > 0 == error
		*/
		function validate_update($event)
		{
			if (!is_array($event['participants']) || !count($event['participants']))
			{
				return 43;
			}
			else
			{
				$acct_part = false;
				foreach ( $event['participants'] as $participant => $status)
				{
					if ( $GLOBALS['phpgw']->accounts->search_person($participant) )
					{
						$acct_part = true;
						break;
					}
				}

				if ( !$acct_part )
				{
					return 43;
				}
			}
			
			if ($event['title'] == '')
			{
				return 40;
			}
			
			if ( !phpgwapi_datetime::time_valid($event['start']['hour'], $event['start']['min'], 0) || !phpgwapi_datetime::time_valid($event['end']['hour'], $event['end']['min'], 0) )
			{
				return 41;
			}
			
			if ( !phpgwapi_datetime::date_valid($event['start']['year'],$event['start']['month'],$event['start']['mday']) || !phpgwapi_datetime::date_valid($event['end']['year'],$event['end']['month'],$event['end']['mday'])
				|| phpgwapi_datetime::date_compare($event['start']['year'],$event['start']['month'],$event['start']['mday'],$event['end']['year'],$event['end']['month'],$event['end']['mday']) == 1 )
			{
				return 42;
			}
			
			if (phpgwapi_datetime::date_compare($event['start']['year'],$event['start']['month'],$event['start']['mday'],$event['end']['year'],$event['end']['month'],$event['end']['mday']) == 0)
			{
				if (phpgwapi_datetime::time_compare($event['start']['hour'],$event['start']['min'],0,$event['end']['hour'],$event['end']['min'],0) == 1)
				{
					return 42;
				}
			}
			return 0;
		}

		/**
		 * checks if any of the $particpants participates in $event and has not rejected it
		*
		 */
		function participants_not_rejected($participants,$event)
		{
			//echo "participants_not_rejected()<br />participants =<pre>"; print_r($participants); echo "</pre><br />event[participants]=<pre>"; print_r($event['participants']); echo "</pre>\n";
			foreach($participants as $uid => $status)
			{
				//echo "testing event[participants][uid=$uid] = '".$event['participants'][$uid]."'<br />\n";
				if (isset($event['participants'][$uid]) && $event['participants'][$uid] != 'R' &&
					$status != 'R')
				{
					return True;	// found not rejected participant in event
				}
			}
			return False;
		}

		function overlap($starttime,$endtime,$participants,$owner=0,$id=0,$restore_cache=False)
		{
			$retval = array();
//			$ok = False;

/* This needs some attention.. by commenting this chunk of code it will fix bug #444265 */

			if($restore_cache)
			{
				$temp_cache_events = $this->cached_events;
			}

//			$temp_start = intval($GLOBALS['phpgw']->common->show_date($starttime,'Ymd'));
//			$temp_start_time = intval($GLOBALS['phpgw']->common->show_date($starttime,'Hi'));
//			$temp_end = intval($GLOBALS['phpgw']->common->show_date($endtime,'Ymd'));
//			$temp_end_time = intval($GLOBALS['phpgw']->common->show_date($endtime,'Hi'));
			$temp_start = intval(date('Ymd',$starttime));
			$temp_start_time = intval(date('Hi',$starttime));
			$temp_end = intval(date('Ymd',$endtime));
			$temp_end_time = intval(date('Hi',$endtime));
			if($this->debug)
			{
				echo "<!-- Temp_Start: {$temp_start} (epoch {$starttime}) -->\n"
					. "<!-- Temp_End: {$temp_end} (epoch {$endtime}) -->\n";
			}

			$users = array();
			if(count($participants))
			{
				while(list($user,$status) = each($participants))
				{
					$users[] = $user;
				}
			}
			else
			{
				$users[] = $this->owner;
			}

			$possible_conflicts = $this->store_to_cache(
				array(
					'smonth'=> substr(strval($temp_start),4,2),
					'sday'	=> substr(strval($temp_start),6,2),
					'syear'	=> substr(strval($temp_start),0,4),
					'emonth'=> substr(strval($temp_end),4,2),
					'eday'	=> substr(strval($temp_end),6,2),
					'eyear'	=> substr(strval($temp_end),0,4),
					'owner'	=> $users
				)
			);

			if($this->debug)
			{
				echo '<!-- Possible Conflicts ('.($temp_start - 1).'): '.count($possible_conflicts[$temp_start - 1]).' -->'."\n";
				echo '<!-- Possible Conflicts ('.$temp_start.'): '.count($possible_conflicts[$temp_start]).' '.count($id).' -->'."\n";
			}

			if ( !is_array($possible_conflicts) )
			{
				$possible_conflicts = array();
			}

			if( isset($possible_conflicts[$temp_start]) || isset($possible_conflicts[$temp_end]) )
			{
				if($temp_start == $temp_end)
				{
					if($this->debug)
					{
						echo '<!-- Temp_Start == Temp_End -->'."\n";
					}
					foreach ( $possible_conflicts[$temp_start] as $key => $event )
					{
						$found = False;
						if($id)
						{
							@reset($id);
							while(list($key,$event_id) = each($id))
							{
								if($this->debug)
								{
									echo '<!-- $id['.$key.'] = '.$id[$key].' = '.$event_id.' -->'."\n";
									echo '<!-- '.$event['id'].' == '.$event_id.' -->'."\n";
								}
								if($event['id'] == $event_id)
								{
									$found = True;
								}
							}
						}
						if($this->debug)
						{
							echo '<!-- Item found: '.$found.' -->'."<br />\n";
						}
						if(!$found)
						{
							if($this->debug)
							{
								echo '<!-- Checking event id #'.$event['id'];
							}
							$temp_event_start = sprintf("%d%02d",$event['start']['hour'],$event['start']['min']);
							$temp_event_end = sprintf("%d%02d",$event['end']['hour'],$event['end']['min']);					
//							if((($temp_start_time <= $temp_event_start) && ($temp_end_time >= $temp_event_start) && ($temp_end_time <= $temp_event_end)) ||
							if(($temp_start_time <= $temp_event_start && 
								$temp_end_time > $temp_event_start && 
								$temp_end_time <= $temp_event_end ||
								$temp_start_time >= $temp_event_start && 
								$temp_start_time < $temp_event_end && 
								$temp_end_time >= $temp_event_end ||
								$temp_start_time <= $temp_event_start && 
								$temp_end_time >= $temp_event_end ||
								$temp_start_time >= $temp_event_start && 
								$temp_end_time <= $temp_event_end) && 
							   $this->participants_not_rejected($participants,$event))
							{
								if($this->debug)
								{
									echo ' Conflicts';
								}
								$retval[] = $event['id'];
							}
							if($this->debug)
							{
								echo ' -->'."\n";
							}
						}
					}
				}
				else
				{
					foreach ( $possible_conflicts as $event_list )
					{
						if ( is_array($event_list) )
						{
							foreach ($event_list as $event)
							{
								$found = False;
								if ( is_array($id) )
								{
									foreach ( $id as $key => $event_id)
									{
										if($this->debug)
										{
											echo '<!-- $id['.$key.'] = '.$id[$key].' = '.$event_id.' -->'."\n";
											echo '<!-- '.$event['id'].' == '.$event_id.' -->'."\n";
										}
										if($event['id'] == $event_id)
										{
											$found = True;
										}
									}
								}
								if($this->debug)
								{
									echo "<!-- Item found: $found -->\n";
								}
								if(!$found)
								{
									if($this->debug)
									{
										echo "'<!-- \nChecking event id #{$event['id']}\n";
									}
									$temp_event_start = mktime($event['start']['hour'], $event['start']['min'], 1, $event['start']['month'], $event['start']['day'], $event['start']['year']);
									$temp_event_end = mktime($event['end']['hour'], $event['end']['min']-1, 59, $event['end']['month'], $event['end']['day'], $event['end']['year']);
									if ( $this->debug)
									{
										echo "Temp Event Start (epoch) = $temp_event_start\n"
											. "Temp Event End (epoch) = $temp_event_end\n";
									}

									if ( ( ($starttime >= $temp_event_start && $starttime <= $temp_event_end )
										|| ($endtime <= $temp_event_end && $endtime >= $temp_event_start ) )
										&& !$this->participants_not_rejected($participants,$event) )
									{
										if($this->debug)
										{
											echo ' Conflicts';
										}
										$retval[] = $event['id'];
									}
									if($this->debug)
									{
										
										echo " -->\n";
									}
								}
							}
						}
					}
				}
				if ( $this->debug )
				{
					echo "<!-- \nretval = " . var_export($retval,true) . "-->\n";
				}
			}

			if($restore_cache)
			{
				$this->cached_events = $temp_cache_events;
			}

			return $retval;
		}

		/**
		 * Checks if the current user has the necessary ACL rights 
		 *
		 * @author ralfbecker
		 * @author skwashd
		 * The check is performed on an event or general on the cal of an other user
		 * @param $needed necessary ACL right: PHPGW_ACL_{READ|EDIT|DELETE}
		 * @param $event event as array or the event-id or 0 for general check
		 * @param $other uid to check (if event==0) or 0 to check against $this->owner
		 * Participating in an event is considered as haveing read-access on that event, \
		 * 	even if you have no general read-grant from that user.
		 */
		function check_perms($needed, $event=0, $other=0)
		{

			if ( $event === 0)
			{
				//convert back to phpgw account id for acls
				$owner =  $GLOBALS['phpgw']->accounts->search_person($other > 0 
						? $other
						: $this->owner
					);
			}
			else
			{
				if (!is_array($event))
				{
					$event = $this->so->read_entry((int) $event);
				}
				if (!is_array($event))
				{
					return False;
				}
				$owner = $GLOBALS['phpgw']->accounts->search_person($event['owner']);

				$private = $event['public'] == False || $event['public'] == 0;
			}
			$user = $GLOBALS['phpgw_info']['user']['account_id'];

			$grants = (isset($this->grants[$owner])?$this->grants[$owner]:'');

			if ( $owner == $user ) //if the current user is the owner they have full rights
			{
				return True;
			}
			
			if (is_array($event) && is_array($event['participants']) && ($needed == PHPGW_ACL_READ))
			{
				// Check if the $user is one of the participants or has a read-grant from one of them
				//

/*				this logic must be wrong - gets mapped to the wrong user - ask Dave				
				foreach($event['participants'] as $contact_id => $accept)
				{
					if (isset($this->grants[$GLOBALS['phpgw']->accounts->search_person($contact_id)])
						&& ($this->grants[$GLOBALS['phpgw']->accounts->search_person($contact_id)] & PHPGW_ACL_READ) 
						|| $GLOBALS['phpgw']->accounts->search_person($contact_id) == $user)
					{
						$grants |= PHPGW_ACL_READ;
						break;
					}
				}

*/
				// the contact is the person - right ?
				foreach($event['participants'] as $contact_id => $accept)
				{
					if (isset($this->grants[$contact_id])
						&& ($this->grants[$contact_id] & PHPGW_ACL_READ) 
						|| $contact_id == $user)
					{
						$grants |= PHPGW_ACL_READ;
						break;
					}
				}
			}

			if ( $this->is_group && $needed == PHPGW_ACL_ADD)
			{
				$access = False;	// a group can't be the owner of an event
			}
			else
			{
				$access = $user == $owner || $grants & $needed && ((!isset($private) || !$private) || $grants & PHPGW_ACL_PRIVATE);
			}
			//echo "<p>bo_calendar::check_perms for user $user and needed_acl $needed: event=$event[title]: owner=$owner, privat=$private, grants=$grants ==> access=$access is_group: {$this->is_group}</p>\n";

			return $access;
		}


		function display_status($user_status)
		{
			if( isset($this->prefs['calendar']['display_status']) && $this->prefs['calendar']['display_status'] && $user_status)
			{
				$user_status = substr($this->get_long_status($user_status), 0, 1);

				return ' ('.$user_status.')';
			}
			else
			{
				return '';
			}
		}

		function get_event_etags($event_id = 0)
		{
			//this is a little convoluted, but it is a decent compromise
			$can_read = array();
			foreach ( $this->grants as $acct_id => $rights )
			{
				if ( $rights & PHPGW_ACL_READ )
				{
					$person_id = $this->contacts->is_contact($acct_id);
					if ( $person_id ) 
					{
						$can_read[] = $person_id;
					}
				}
			}
			return $this->so->get_event_etags( $can_read, $event_id );
		}

		function get_long_status($status_short)
		{
			$status = '';
			switch ($status_short)
			{
				case 'A':
					$status = lang('Accepted');
					break;
				case 'R':
					$status = lang('Rejected');
					break;
				case 'T':
					$status = lang('Tentative');
					break;
				case 'U':
					$status = lang('No Response');
					break;
			}
			return $status;
		}

		function is_private($event,$owner)
		{
			if($owner == 0)
			{
				$owner = $this->owner;
			}
			if ( $event['public'] == 1 || ($this->check_perms(PHPGW_ACL_PRIVATE, $event) && $event['public'] == 0) || $event['owner'] == $GLOBALS['phpgw_info']['user']['person_id'] )
			{
				return False;
			}
			elseif($event['public'] == 0)
			{
				return True;
			}
			elseif($event['public'] == 2)
			{
				$is_private = True;
				$groups = $GLOBALS['phpgw']->accounts->membership($owner);
				while (list($key,$group) = each($groups))
				{
					if (strpos(' '.implode(',',$event['groups']).' ',$group['account_id']))
					{
						return False;
					}
				}
			}
			else
			{
				return False;
			}

			return $is_private;
		}

		function get_short_field($event,$is_private=True,$field='')
		{
			if($is_private)
			{
				return 'private';
			}
			else
			{
				return $event[$field];
			}
		}

		function long_date($first,$last=0)
		{
			$range = '';
			if (!is_array($first))
			{
				$first = $this->time2array($raw = $first);
				$first['raw'] = $raw;
				$first['day'] = $first['mday'];
			}
			if ($last && !is_array($last))
			{
				$last = $this->time2array($raw = $last);
				$last['raw'] = $raw;
				$last['day'] = $last['mday'];
			}
			$datefmt = $this->prefs['common']['dateformat'];
			
			$month_before_day = $datefmt[0] == 'm' || $datefmt[2] == 'm' && $datefmt[4] == 'd';

			for ($i = 0; $i < 5; $i += 2)
			{
				switch($datefmt[$i])
				{
					case 'd':
						$range .= $first['day'] . ($datefmt[1] == '.' ? '.' : '');
						if ($first['month'] != $last['month'] || $first['year'] != $last['year'])
						{
							if (!$month_before_day)
							{
								$range .= ' '.lang(strftime('%B',$first['raw']));
							}
							if ($first['year'] != $last['year'] && $datefmt[0] != 'Y')
							{
								$range .= ($datefmt[0] != 'd' ? ', ' : ' ') . $first['year'];
							}
							if (!$last)
							{
								return $range;
							}
							$range .= ' - ';
							
							if ($first['year'] != $last['year'] && $datefmt[0] == 'Y')
							{
								$range .= $last['year'] . ', ';
							}

							if ($month_before_day)
							{
								$range .= lang(strftime('%B',$last['raw']));
							}
						}
						else
						{
							$range .= ' - ';
						}
						$range .= ' ' . $last['day'] . ($datefmt[1] == '.' ? '.' : '');
						break;
					case 'm':
					case 'M':
						$range .= ' '.lang(strftime('%B',$month_before_day ? $first['raw'] : $last['raw'])) . ' ';
						break;
					case 'Y':
						$range .= ($datefmt[0] == 'm' ? ', ' : ' ') . ($datefmt[0] == 'Y' ? $first['year'].', ' : $last['year'].' ');
						break;
				}
			}
			return $range;
		}

		function get_week_label()
		{
			$first = phpgwapi_datetime::gmtdate(phpgwapi_datetime::get_weekday_start($this->year, $this->month, $this->day));
			$last = phpgwapi_datetime::gmtdate($first['raw'] + 518400);
		 
			return ($this->long_date($first,$last));
		}
		
		function normalizeminutes($minutes)
		{
			$hour = 0;
			$min = intval($minutes);
			if($min >= 60)
			{
				$hour += $min / 60;
				$min %= 60;
			}
			settype($minutes,'integer');
			$minutes = $min;
			$time['hour'] = $hour;
			$time['minutes'] = $minutes;

			return $time;
		}

		function splittime($time,$follow_24_rule=True)
		{
			$temp = array('hour','minute','second','ampm');
			$time = strrev($time);
			$second = intval(strrev(substr($time,0,2)));
			$minute = intval(strrev(substr($time,2,2)));
			$hour   = intval(strrev(substr($time,4)));
//			$hour += $this->normalizeminutes(&$minute);
			$time_new = $this->normalizeminutes($minute);
			$hour += $time_new['hour'];
			$minute = $time_new['minutes'];
			unset($time_new);
			$temp['second'] = $second;
			$temp['minute'] = $minute;
			$temp['hour']   = $hour;
			$temp['ampm']   = '  ';
			if($follow_24_rule == True)
			{
				if ($this->prefs['common']['timeformat'] == '24')
				{
					return $temp;
				}
		
				$temp['ampm'] = 'am';
		
				if ((int)$temp['hour'] > 12)
				{
					$temp['hour'] = (int)((int)$temp['hour'] - 12);
					$temp['ampm'] = 'pm';
				}
				elseif ((int)$temp['hour'] == 12)
				{
					$temp['ampm'] = 'pm';
				}
			}
			return $temp;
		}

		function get_exception_array($exception_str='')
		{
			$exception = array();
			if(strpos(' '.$exception_str,','))
			{
				$exceptions = explode(',',$exception_str);
				for($exception_count=0;$exception_count<count($exceptions);$exception_count++)
				{
					$exception[] = intval($exceptions[$exception_count]);
				}
			}
			elseif($exception_str != '')
			{
				$exception[] = intval($exception_str);
			}
			return $exception;
		}

		function build_time_for_display($fixed_time)
		{
			$time = $this->splittime($fixed_time);
			$str = $time['hour'].':'.((int)$time['minute']<=9?'0':'').$time['minute'];
		
			if ($this->prefs['common']['timeformat'] == '12')
			{
				$str .= ' ' . $time['ampm'];
			}
		
			return $str;
		}
	
		function sort_event($event,$date)
		{
			$inserted = False;
			if(isset($event['recur_exception']))
			{
				$event_time = mktime($event['start']['hour'],$event['start']['min'],0,intval(substr($date,4,2)),intval(substr($date,6,2)),intval(substr($date,0,4))) - phpgwapi_datetime::user_timezone();
				while($inserted == False && list($key,$exception_time) = each($event['recur_exception']))
				{
					if($this->debug)
					{
						echo '<!-- checking exception datetime '.$exception_time.' to event datetime '.$event_time.' -->'."\n";
					}
					if($exception_time == $event_time)
					{
						$inserted = True;
					}
				}
			}
			if(isset($this->cached_events[$date]) && $this->cached_events[$date] && $inserted == False)
			{
				
				if($this->debug)
				{
					echo '<!-- Cached Events found for '.$date.' -->'."\n";
				}
				$year = substr($date,0,4);
				$month = substr($date,4,2);
				$day = substr($date,6,2);

				if($this->debug)
				{
					echo '<!-- Date : '.$date.' Count : '.count($this->cached_events[$date]).' -->'."\n";
				}
				
				for($i=0;$i<count($this->cached_events[$date]);$i++)
				{
					$events = $this->cached_events[$date][$i];
					if($this->cached_events[$date][$i]['id'] == $event['id'] || $this->cached_events[$date][$i]['reference'] == $event['id'])
					{
						if($this->debug)
						{
							echo '<!-- Item already inserted! -->'."\n";
						}
						$inserted = True;
						break;
					}
					/* This puts all spanning events across multiple days up at the top. */
					if($this->cached_events[$date][$i]['recur_type'] == MCAL_RECUR_NONE)
					{
						if($this->cached_events[$date][$i]['start']['mday'] != $day && $this->cached_events[$date][$i]['end']['mday'] >= $day)
						{
							continue;
						}
					}
					if(date('Hi',mktime($event['start']['hour'],$event['start']['min'],$event['start']['sec'],$month,$day,$year)) < date('Hi',mktime($this->cached_events[$date][$i]['start']['hour'],$this->cached_events[$date][$i]['start']['min'],$this->cached_events[$date][$i]['start']['sec'],$month,$day,$year)))
					{
		//				for($j=count($this->cached_events[$date]);$j>=$i;$j--)
						for($j=count($this->cached_events[$date]);$j>=($i+1);$j--)
						{
							$this->cached_events[$date][$j] = $this->cached_events[$date][$j-1];
						}
						if($this->debug)
						{
							echo '<!-- Adding event ID: '.$event['id'].' to cached_events -->'."\n";
						}
						$inserted = True;
						$this->cached_events[$date][$i] = $event;
						break;
					}
				}
			}
			if(!$inserted)
			{
				if($this->debug)
				{
					echo '<!-- Adding event ID: '.$event['id'].' to cached_events -->'."\n";
				}
				$this->cached_events[$date][] = $event;
			}					
		}

		function check_repeating_events($datetime)
		{
			@reset($this->repeating_events);
			$search_date_full = date('Ymd',$datetime);
			$search_date_year = date('Y',$datetime);
			$search_date_month = date('m',$datetime);
			$search_date_day = date('d',$datetime);
			$search_date_dow = date('w',$datetime);
			$search_beg_day = mktime(0,0,0,$search_date_month,$search_date_day,$search_date_year);
			if($this->debug)
			{
				echo '<!-- Search Date Full = '.$search_date_full.' -->'."\n";
			}
			$repeated = $this->repeating_events;
			$r_events = count($repeated);
			for ($i=0;$i<$r_events;$i++)
			{
				if($this->repeating_events[$i]['recur_type'] !=0)
				{
					$rep_events = $this->repeating_events[$i];
					$id = $rep_events['id'];
					$event_beg_day = mktime(0,0,0,$rep_events['start']['month'],$rep_events['start']['mday'],$rep_events['start']['year']);
					if($rep_events['recur_enddate']['month'] != 0 && $rep_events['recur_enddate']['mday'] != 0 && $rep_events['recur_enddate']['year'] != 0)
					{
						$event_recur_time = $this->maketime($rep_events['recur_enddate']);
					}
					else
					{
						$event_recur_time = mktime(0,0,0,1,1,2030);
					}
					$end_recur_date = date('Ymd',$event_recur_time);
					$full_event_date = date('Ymd',$event_beg_day);

					if($this->debug)
					{
						echo '<!-- check_repeating_events - Processing ID - '.$id.' -->'."\n";
						echo '<!-- check_repeating_events - Recurring End Date - '.$end_recur_date.' -->'."\n";
					}

					// only repeat after the beginning, and if there is an rpt_end before the end date
					if (($search_date_full > $end_recur_date) || ($search_date_full < $full_event_date))
					{
						continue;
					}

					if ($search_date_full == $full_event_date)
					{
						$this->sort_event($rep_events,$search_date_full);
						continue;
					}
					else
					{				
						$freq = (isset($rep_events['recur_interval']) && $rep_events['recur_interval'] ?$rep_events['recur_interval']:1);
						$type = $rep_events['recur_type'];
						switch($type)
						{
							case MCAL_RECUR_DAILY:
								if($this->debug)
								{
									echo '<!-- check_repeating_events - MCAL_RECUR_DAILY - '.$id.' -->'."\n";
								}
								if ($freq == 1 && $rep_events['recur_enddate']['month'] != 0 && $rep_events['recur_enddate']['mday'] != 0 && $rep_events['recur_enddate']['year'] != 0 && $search_date_full <= $end_recur_date)
								{
									$this->sort_event($rep_events,$search_date_full);
								}
								elseif (floor(($search_beg_day - $event_beg_day)/86400) % $freq)
								{
									continue;
								}
								else
								{
									$this->sort_event($rep_events,$search_date_full);
								}
							break;
							case MCAL_RECUR_WEEKLY:
								if (floor(($search_beg_day - $event_beg_day)/604800) % $freq)
								{
									continue;
								}
								$check = 0;
								switch($search_date_dow)
								{
									case 0:
										$check = MCAL_M_SUNDAY;
										break;
									case 1:
										$check = MCAL_M_MONDAY;
										break;
									case 2:
										$check = MCAL_M_TUESDAY;
										break;
									case 3:
										$check = MCAL_M_WEDNESDAY;
										break;
									case 4:
										$check = MCAL_M_THURSDAY;
										break;
									case 5:
										$check = MCAL_M_FRIDAY;
										break;
									case 6:
										$check = MCAL_M_SATURDAY;
										break;
								}
								if ($rep_events['recur_data'] & $check)
								{
									$this->sort_event($rep_events,$search_date_full);
								}
								break;
							case MCAL_RECUR_MONTHLY_WDAY:
								if ((($search_date_year - $rep_events['start']['year']) * 12 + $search_date_month - $rep_events['start']['month']) % $freq)
								{
									continue;
								}
	  
								if ((phpgwapi_datetime::day_of_week($rep_events['start']['year'],$rep_events['start']['month'],$rep_events['start']['mday']) == phpgwapi_datetime::day_of_week($search_date_year,$search_date_month,$search_date_day)) &&
									(ceil($rep_events['start']['mday']/7) == ceil($search_date_day/7)))
								{
									$this->sort_event($rep_events,$search_date_full);
								}
								break;
							case MCAL_RECUR_MONTHLY_MDAY:
								if ((($search_date_year - $rep_events['start']['year']) * 12 + $search_date_month - $rep_events['start']['month']) % $freq)
								{
									continue;
								}
								if ($search_date_day == $rep_events['start']['mday'])
								{
									$this->sort_event($rep_events,$search_date_full);
								}
								break;
							case MCAL_RECUR_YEARLY:
								if (($search_date_year - $rep_events['start']['year']) % $freq)
								{
									continue;
								}
								if (date('dm',$datetime) == date('dm',$event_beg_day))
								{
									$this->sort_event($rep_events,$search_date_full);
								}
								break;
						}
					}
				}
			}	// end for loop
		}	// end function

		function store_to_cache($params)
		{
			if(!is_array($params))
			{
				return False;
			}

			$syear = $params['syear'];
			$smonth = $params['smonth'];
			$sday = $params['sday'];
			$eyear = (isset($params['eyear'])?$params['eyear']:0);
			$emonth = (isset($params['emonth'])?$params['emonth']:0);
			$eday = (isset($params['eday'])?$params['eday']:0);
			$owner_id = (isset($params['owner'])?$params['owner']:0);
			if($owner_id==0 && $this->is_group)
			{
				unset($owner_id);
				$owner_id = $this->g_owner;
				if($this->debug)
				{
					echo '<!-- owner_id in ('.implode($owner_id,',').') -->'."\n";
				}
			}
			
			if(!$eyear && !$emonth && !$eday)
			{
				$edate = mktime(23,59,59,$smonth + 1,$sday + 1,$syear);
				$eyear = date('Y',$edate);
				$emonth = date('m',$edate);
				$eday = date('d',$edate);
			}
			else
			{
				if(!$eyear)
				{
					$eyear = $syear;
				}
				if(!$emonth)
				{
					$emonth = $smonth + 1;
					if($emonth > 12)
					{
						$emonth = 1;
						$eyear++;
					}
				}
				if(!$eday)
				{
					$eday = $sday + 1;
				}
				$edate = mktime(23,59,59,$emonth,$eday,$eyear);
			}
			
			if($this->debug)
			{
				echo '<!-- Start Date : '.sprintf("%04d%02d%02d",$syear,$smonth,$sday).' -->'."\n";
				echo '<!-- End   Date : '.sprintf("%04d%02d%02d",$eyear,$emonth,$eday).' -->'."\n";
			}

			if($owner_id)
			{
				$cached_event_ids = $this->so->list_events($syear,$smonth,$sday,$eyear,$emonth,$eday,$owner_id);
				$cached_event_ids_repeating = $this->so->list_repeated_events($syear,$smonth,$sday,$eyear,$emonth,$eday,$owner_id);
			}
			else
			{
				$cached_event_ids = $this->so->list_events($syear,$smonth,$sday,$eyear,$emonth,$eday);
				$cached_event_ids_repeating = $this->so->list_repeated_events($syear,$smonth,$sday,$eyear,$emonth,$eday);
			}

			$c_cached_ids = count($cached_event_ids);
			$c_cached_ids_repeating = count($cached_event_ids_repeating);

			if($this->debug)
			{
				echo '<!-- events cached : '.$c_cached_ids.' : for : '.sprintf("%04d%02d%02d",$syear,$smonth,$sday).' -->'."\n";
				echo '<!-- repeating events cached : '.$c_cached_ids_repeating.' : for : '.sprintf("%04d%02d%02d",$syear,$smonth,$sday).' -->'."\n";
			}

			$this->cached_events = array();
			
			if($c_cached_ids == 0 && $c_cached_ids_repeating == 0)
			{
				return;
			}

			if($c_cached_ids)
			{
				for($i=0;$i<$c_cached_ids;$i++)
				{
					$event = $this->so->read_entry($cached_event_ids[$i]);
					$startdate = intval(date('Ymd',$this->maketime($event['start'])));
					$enddate = intval(date('Ymd',$this->maketime($event['end'])));
					$this->cached_events[$startdate][] = $event;
					if($startdate != $enddate)
					{
						$start['year'] = intval(substr($startdate,0,4));
						$start['month'] = intval(substr($startdate,4,2));
						$start['mday'] = intval(substr($startdate,6,2));
						for($j=$startdate,$k=0;$j<=$enddate;$k++,$j=intval(date('Ymd',mktime(0,0,0,$start['month'],$start['mday'] + $k,$start['year']))))
						{
							$c_evt_day = 0;
							if(isset($this->cached_events[$j]) && is_array($this->cached_events[$j]))
							{
								$c_evt_day = count($this->cached_events[$j]);
							}

							if($this->debug)
							{
								echo 'Date: '.$j.' Count : '.$c_evt_day."\n";
							}
							if(!isset($this->cached_events[$j][$c_evt_day])
								||$this->cached_events[$j][$c_evt_day]['id'] != $event['id'])
							{
								if($this->debug)
								{
									echo 'Adding Event for Date: '.$j."\n";
								}
								$this->cached_events[$j][] = $event;
							}
						}
					}
				}
			}

			$this->repeating_events = array();
			if($c_cached_ids_repeating)
			{
				for($i=0;$i<$c_cached_ids_repeating;$i++)
				{
					$this->repeating_events[$i] = $this->so->read_entry($cached_event_ids_repeating[$i]);
					if($this->debug)
					{
						echo '<!-- Cached Events ID: '.$cached_event_ids_repeating[$i].' ('.sprintf("%04d%02d%02d",$this->repeating_events[$i]['start']['year'],$this->repeating_events[$i]['start']['month'],$this->repeating_events[$i]['start']['mday']).') -->'."\n";
					}
				}
//				$edate -= phpgwapi_datetime::user_timezone();
//				for($date=mktime(0,0,0,$smonth,$sday,$syear) - phpgwapi_datetime::tz_offset;$date<=$edate;$date += 86400)
				for($date=mktime(0,0,0,$smonth,$sday,$syear);$date<=$edate;$date += phpgwapi_datetime::SECONDS_IN_DAY)
				{
					if($this->debug)
					{
//						$search_date = $GLOBALS['phpgw']->common->show_date($date,'Ymd');
						$search_date = date('Ymd',$date);
						echo '<!-- Calling check_repeating_events('.$search_date.') -->'."\n";
					}
					$this->check_repeating_events($date);
					if($this->debug)
					{
						echo '<!-- Total events found matching '.$search_date.' = '.count($this->cached_events[$search_date]).' -->'."\n";
						for($i=0;$i<count($this->cached_events[$search_date]);$i++)
						{
							echo '<!-- Date: '.$search_date.' ['.$i.'] = '.$this->cached_events[$search_date][$i]['id'].' -->'."\n";
						}
					}
				}
			}
			$retval = array();
			for($j=date('Ymd',mktime(0,0,0,$smonth,$sday,$syear)),$k=0;$j<=date('Ymd',mktime(0,0,0,$emonth,$eday,$eyear));$k++,$j=date('Ymd',mktime(0,0,0,$smonth,$sday + $k,$syear)))
			{
				if(isset($this->cached_events[$j]) && is_array($this->cached_events[$j]))
				{
					$retval[$j] = $this->cached_events[$j];
				}
			}
			return $retval;
//			return $this->cached_events;
		}

		/* Begin Appsession Data */
		function store_to_appsession($event)
		{
			$GLOBALS['phpgw']->session->appsession('entry','calendar',$event);
		}

		function restore_from_appsession()
		{
			$this->event_init();
			$event = $GLOBALS['phpgw']->session->appsession('entry','calendar');
			$this->so->cal->event = $event;
			return $event;
		}
		/* End Appsession Data */

		/* Begin of SO functions */
		function get_cached_event()
		{
			return $this->so->get_cached_event();
		}
		
		function add_attribute($var,$value,$index='**(**')
		{
			$this->so->add_attribute($var,$value,$index);
		}

		function event_init()
		{
			$this->so->event_init();
		}

		function set_start($year,$month,$day=0,$hour=0,$min=0,$sec=0)
		{
			$this->so->set_start($year,$month,$day,$hour,$min,$sec);
		}

		function set_end($year,$month,$day=0,$hour=0,$min=0,$sec=0)
		{
			$this->so->set_end($year,$month,$day,$hour,$min,$sec);
		}

		function set_title($title='')
		{
			$this->so->set_title($title);
		}

		function set_description($description='')
		{
			$this->so->set_description($description);
		}

		function set_class($class)
		{
			$this->so->set_class($class);
		}

		function set_category($category='')
		{
			$this->so->set_category($category);
		}

		function set_alarm($alarm)
		{
			$this->so->set_alarm($alarm);
		}

		function set_recur_none()
		{
			$this->so->set_recur_none();
		}

		function set_recur_daily($year,$month,$day,$interval)
		{
			$this->so->set_recur_daily($year,$month,$day,$interval);
		}

		function set_recur_weekly($year,$month,$day,$interval,$weekdays)
		{
			$this->so->set_recur_weekly($year,$month,$day,$interval,$weekdays);
		}

		function set_recur_monthly_mday($year,$month,$day,$interval)
		{
			$this->so->set_recur_monthly_mday($year,$month,$day,$interval);
		}

		function set_recur_monthly_wday($year,$month,$day,$interval)
		{
			$this->so->set_recur_monthly_wday($year,$month,$day,$interval);
		}

		function set_recur_yearly($year,$month,$day,$interval)
		{
			$this->so->set_recur_yearly($year,$month,$day,$interval);
		}
		/* End of SO functions */

		function prepare_matrix($interval,$increment,$part,$fulldate)
		{
			for($h=0;$h<24;$h++)
			{
				for($m=0;$m<$interval;$m++)
				{
					$index = (($h * 10000) + (($m * $increment) * 100));
					$time_slice[$index]['marker'] = '&nbsp';
					$time_slice[$index]['description'] = '';
				}
			}
			foreach($this->cached_events[$fulldate] as $event)
			{
				if ($event['participants'][$part] == 'R')
				{
					continue;	// dont show rejected invitations, as they are free time
				}
				$eventstart = phpgwapi_datetime::localdates($this->maketime($event['start']) - phpgwapi_datetime::user_timezone());
				$eventend = phpgwapi_datetime::localdates($this->maketime($event['end']) - phpgwapi_datetime::user_timezone());
				$start = ($eventstart['hour'] * 10000) + ($eventstart['minute'] * 100);
				$starttemp = $this->splittime("$start",False);
				$subminute = 0;
				for($m=0;$m<$interval;$m++)
				{
					$minutes = $increment * $m;
					if(intval($starttemp['minute']) > $minutes && intval($starttemp['minute']) < ($minutes + $increment))
					{
						$subminute = ($starttemp['minute'] - $minutes) * 100;
					}
				}
				$start -= $subminute;
				$end =  ($eventend['hour'] * 10000) + ($eventend['minute'] * 100);
				$endtemp = $this->splittime("$end",False);
				$addminute = 0;
				for($m=0;$m<$interval;$m++)
				{
					$minutes = ($increment * $m);
					if($endtemp['minute'] < ($minutes + $increment) && $endtemp['minute'] > $minutes)
					{
						$addminute = ($minutes + $increment - $endtemp['minute']) * 100;
					}
				}
				$end += $addminute;
				$starttemp = $this->splittime("$start",False);
				$endtemp = $this->splittime("$end",False);
					
				for($h=$starttemp['hour'];$h<=$endtemp['hour'];$h++)
				{
					$startminute = 0;
					$endminute = $interval;
					$hour = $h * 10000;
					if($h == intval($starttemp['hour']))
					{
						$startminute = ($starttemp['minute'] / $increment);
					}
					if($h == intval($endtemp['hour']))
					{
						$endminute = ($endtemp['minute'] / $increment);
					}
					$private = $this->is_private($event,$part);
					$time_display = $GLOBALS['phpgw']->common->show_date($eventstart['raw'],$this->users_timeformat).'-'.$GLOBALS['phpgw']->common->show_date($eventend['raw'],$this->users_timeformat);
					$time_description = '('.$time_display.') '.$this->get_short_field($event,$private,'title').$this->display_status($event['participants'][$part]);
					for($m=$startminute;$m<$endminute;$m++)
					{
						$index = ($hour + (($m * $increment) * 100));
						$time_slice[$index]['marker'] = '-';
						$time_slice[$index]['description'] = $time_description;
						$time_slice[$index]['id'] = $event['id'];
					}
				}
			}
			return $time_slice;
		}

		/**
		 * set the participant response $status for event $cal_id and notifies the owner of the event
		*
		 */
		function set_status($cal_id,$status)
		{
			$status2msg = array(
				REJECTED  => MSG_REJECTED,
				TENTATIVE => MSG_TENTATIVE,
				ACCEPTED  => MSG_ACCEPTED
			);
			if (!isset($status2msg[$status]))
			{
				return False;
			}
			$this->so->set_status($cal_id,$status);
			$event = $this->so->read_entry($cal_id);
			$this->send_update($status2msg[$status],$event['participants'],$event);

			return True;
		}

		/**
		 * checks if $userid has requested (in $part_prefs) updates for $msg_type
		*
		 * @param $userid numerical user-id
		 * @param $part_prefs preferces of the user $userid
		 * @param $msg_type type of the notification: MSG_ADDED, MSG_MODIFIED, MSG_ACCEPTED, ...
		 * @param $old_event Event before the change
		 * @param $new_event Event after the change
		 * @return 0 = no update requested, > 0 update requested
		 */
		function update_requested($userid,$part_prefs,$msg_type,$old_event,$new_event)
		{
			if ($msg_type == MSG_ALARM)
			{
				return True;	// always True for now
			}
			$want_update = 0;
			
			// the following switch fall-through all cases, as each included the following too
			//
			$msg_is_response = $msg_type == MSG_REJECTED || $msg_type == MSG_ACCEPTED || $msg_type == MSG_TENTATIVE;

			switch($ru = $part_prefs['calendar']['receive_updates'])
			{
				case 'responses':
					if ($msg_is_response)
					{
						++$want_update;
					}
				case 'modifications':
					if ($msg_type == MSG_MODIFIED)
					{
						++$want_update;
					}
				case 'time_change_4h':
				case 'time_change':
					$diff = max(abs($this->maketime($old_event['start'])-$this->maketime($new_event['start'])),
						abs($this->maketime($old_event['end'])-$this->maketime($new_event['end'])));
					$check = $ru == 'time_change_4h' ? 4 * 60 * 60 - 1 : 0;
					if ($msg_type == MSG_MODIFIED && $diff > $check)
					{
						++$want_update;
					}
				case 'add_cancel':
					if ($old_event['owner'] == $userid && $msg_is_response ||
						$msg_type == MSG_DELETED || $msg_type == MSG_ADDED)
					{
						++$want_update;
					}
					break;
				case 'no':
					break;
			}
			//echo "<p>bocalendar::update_requested(user=$userid,pref=".$part_prefs['calendar']['receive_updates'] .",msg_type=$msg_type,".($old_event?$old_event['title']:'False').",".($old_event?$old_event['title']:'False').") = $want_update</p>\n";
			return $want_update > 0;
		}

		/**
		 * sends update-messages to certain participants of an event
		*
		 * @param $msg_type type of the notification: MSG_ADDED, MSG_MODIFIED, MSG_ACCEPTED, ...
		 * @param $to_notify array with numerical user-ids as keys (!) (value is not used)
		 * @param $old_event Event before the change
		 * @param $new_event Event after the change
		 */
		function send_update($msg_type,$to_notify,$old_event,$new_event=False,$user=False)
		{
			$returncode = true;
			//echo "<p>bocalendar::send_update(type=$msg_type,to_notify="; print_r($to_notify); echo ", old_event="; print_r($old_event); echo ", new_event="; print_r($new_event); echo ", user=$user)</p>\n";
			if (!is_array($to_notify))
			{
				$to_notify = array();
			}
			$owner = $old_event ? $old_event['owner'] : $new_event['owner'];
			if ($owner && !isset($to_notify[$owner]) && $msg_type != MSG_ALARM)
			{
				$to_notify[$owner] = 'owner';	// always include the event-owner
			}
			$version = $GLOBALS['phpgw_info']['apps']['calendar']['version'];

			$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->create_email_preferences();
			$sender = $GLOBALS['phpgw_info']['user']['preferences']['email']['address'];

			$temp_tz_offset = $this->prefs['common']['tz_offset'];
			$temp_timeformat = $this->prefs['common']['timeformat'];
			$temp_dateformat = $this->prefs['common']['dateformat'];

			$tz_offset = ((60 * 60) * intval($temp_tz_offset));

			if($old_event != False)
			{
				$t_old_start_time = $this->maketime($old_event['start']);
				if($t_old_start_time < (time() - 86400))
				{
					return False;
				}
			}

			$temp_user = $GLOBALS['phpgw_info']['user'];

			if (!$user)
			{
				$user =  $GLOBALS['phpgw']->accounts->search_person($this->owner);
			}
			$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->create_email_preferences($user);

			$user_timezone = phpgwapi_datetime::user_timezone();

			$event = $msg_type == MSG_ADDED || $msg_type == MSG_MODIFIED ? $new_event : $old_event;
			if($old_event != False)
			{
				$old_starttime = $t_old_start_time - $user_timezone;
			}
			$starttime = $this->maketime($event['start']) - $user_timezone;
			$endtime   = $this->maketime($event['end']) - $user_timezone;

			switch($msg_type)
			{
				case MSG_DELETED:
					$action = lang('Canceled');
					$msg = 'Canceled';
					$msgtype = '"calendar";';
					$method = 'cancel';
					break;
				case MSG_MODIFIED:
					$action = lang('Modified');
					$msg = 'Modified';
					$msgtype = '"calendar"; Version="'.$version.'"; Id="'.$new_event['id'].'"';
					$method = 'request';
					break;
				case MSG_ADDED:
					$action = lang('Added');
					$msg = 'Added';
					$msgtype = '"calendar"; Version="'.$version.'"; Id="'.$new_event['id'].'"';
					$method = 'request';
					break;
				case MSG_REJECTED:
					$action = lang('Rejected');
					$msg = 'Response';
					$msgtype = '"calendar";';
					$method = 'reply';
					break;
				case MSG_TENTATIVE:
					$action = lang('Tentative');
					$msg = 'Response';
					$msgtype = '"calendar";';
					$method = 'reply';
					break;
				case MSG_ACCEPTED:
					$action = lang('Accepted');
					$msg = 'Response';
					$msgtype = '"calendar";';
					$method = 'reply';
					break;
				case MSG_ALARM:
					$action = lang('Alarm');
					$msg = 'Alarm';
					$msgtype = '"calendar";';
					$method = 'publish';	// duno if thats right
					break;
				default:
					$method = 'publish';
			}
			$notify_msg = $this->prefs['calendar']['notify'.$msg];
			if (empty($notify_msg))
			{
				$notify_msg = $this->prefs['calendar']['notifyAdded'];	// use a default
			}
			$details = array(			// event-details for the notify-msg
				'id'          => $msg_type == MSG_ADDED ? $new_event['id'] : $old_event['id'],
				'action'      => $action,
			);
			$event_arr = $this->event2array($event);
			foreach($event_arr as $key => $val)
			{
				$details[$key] = $val['data'];
			}
			$details['participants'] = implode("\n",$details['participants']);

			if(!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}
			$send = &$GLOBALS['phpgw']->send;

			foreach($to_notify as $userid => $statusid)
			{
				$userid = intval($userid);

				if ($statusid == 'R')
				{
					continue;	// dont notify rejected participants
				}
				if($userid != $GLOBALS['phpgw_info']['user']['account_id'] ||  $msg_type == MSG_ALARM)
				{
					print_debug('Msg Type',$msg_type);
					print_debug('UserID',$userid);

					$preferences = CreateObject('phpgwapi.preferences',$userid);
					$part_prefs = $preferences->read();

					if (!$this->update_requested($userid,$part_prefs,$msg_type,$old_event,$new_event))
					{
						continue;
					}
					$details['to-fullname'] = (string) $GLOBALS['phpgw']->accounts->get($userid);

					$to = $preferences->email_address($userid);
					if (empty($to) || $to[0] == '@' || $to[0] == '$')	// we have no valid email-address
					{
						//echo "<p>bocalendar::send_update: Empty email adress for user '".$details['to-fullname']."' ==> ignored !!!</p>\n";
						continue;
					}
					print_debug('Email being sent to',$to);

					$GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'] = $part_prefs['common']['tz_offset'];
					$GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] = $part_prefs['common']['timeformat'];
					$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] = $part_prefs['common']['dateformat'];

					//FIXME i think this is dodgy
					$tz_offset = ((60 * 60) * intval($GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']));

					if($old_starttime)
					{
						$details['olddate'] = $GLOBALS['phpgw']->common->show_date($old_starttime);
					}
					$details['startdate'] = $GLOBALS['phpgw']->common->show_date($starttime);
					$details['enddate']   = $GLOBALS['phpgw']->common->show_date($endtime);
				
					list($subject,$body) = split("\n",$GLOBALS['phpgw']->preferences->parse_notify($notify_msg,$details),2);
					$subject = trim($send->encode_subject($subject));
					switch($part_prefs['calendar']['update_format'])
 					{
						case  'extended':
							$body .= "\n\n".lang('Event Details follow').":\n";
							foreach($event_arr as $key => $val)
							{
								if ($key != 'access' && $key != 'priority' && strlen($details[$key]))
								{
									$body .= sprintf("%-20s %s\n",$val['field'].':',$details[$key]);
								}
							}
							break;

						case 'ical':
							$content_type = "calendar; method=$method; name=calendar.ics";
/* would be nice, need to get it working
							if ($body != '')
							{
								$boundary = '----Message-Boundary';
								$body .= "\n\n\n$boundary\nContent-type: text/$content_type\n".
									"Content-Disposition: inline\nContent-transfer-encoding: 7BIT\n\n";
								$content_type = '';
							}
*/
							$body = ExecMethod('calendar.boicalendar.export',array(
								'l_event_id'  => $event['id'],
								'method'      => $method,
								'chunk_split' => False
							));
							break;
					}
					$returncode = $send->msg('email',$to,$subject,$body,''/*$msgtype*/,'','','',$sender, $content_type/*,$boundary*/);
					//echo "<p>send(to='$to', sender='$sender'<br />subject='$subject') returncode=$returncode<br />".nl2br($body)."</p>\n";
					
					if (!$returncode)	// not nice, but better than failing silently
					{
						echo '<p><b>bocalendar::send_update</b>: '.lang("Failed sending message to '%1' #%2 subject='%3', sender='%4' !!!",$to,$userid,htmlspecialchars($subject), $sender)."<br />\n";
						echo '<i>'.$send->err['desc']."</i><br />\n";
						echo lang('This is mostly caused by a not or wrongly configured SMTP server. Notify your administrator.')."</p>\n";
						echo '<p>'.lang('Click %1here%2 to return to the calendar.','<a href="'.$GLOBALS['phpgw']->link('/calendar/').'">','</a>')."</p>\n";
					}
				}
			}
			unset($send);
		
			if( isset($this->user) 
				&& ( (is_int($this->user) && $this->user != $temp_user['account_id'])
				|| (is_string($this->user) && $this->user != $temp_user['account_lid'])) )
			{
				$GLOBALS['phpgw_info']['user'] = $temp_user;
			}

			$GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'] = $temp_tz_offset;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] = $temp_timeformat;
			$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'] = $temp_dateformat;
			
			return $returncode;
		}

		function send_alarm($alarm)
		{
			//echo "<p>bocalendar::send_alarm("; print_r($alarm); echo ")</p>\n";
			$GLOBALS['phpgw_info']['user']['account_id'] = $this->owner = $alarm['owner'];

			if (!$alarm['enabled'] || !$alarm['owner'] || !$alarm['cal_id'] || !($event = $this->so->read_entry($alarm['cal_id'])))
			{
				return False;	// event not found
			}
			if ($alarm['all'])
			{
				$to_notify = $event['participants'];
			}
			elseif ($this->check_perms(PHPGW_ACL_READ,$event))	// checks agains $this->owner set to $alarm[owner]
			{
				$to_notify[$alarm['owner']] = 'A';
			}
			else
			{
				return False;	// no rights
			}
			return $this->send_update(MSG_ALARM,$to_notify,$event,False,$alarm['owner']);
		}

		function get_alarms($event_id)
		{
			return $this->so->get_alarm($event_id);
		}

		function alarm_today($event,$today,$starttime)
		{
			$found = False;
			@reset($event['alarm']);
			$starttime_hi = $GLOBALS['phpgw']->common->show_date($starttime,'Hi');
			$t_appt['month'] =$GLOBALS['phpgw']->common->show_date($today,'m');
			$t_appt['mday'] = $GLOBALS['phpgw']->common->show_date($today,'d');
			$t_appt['year'] = $GLOBALS['phpgw']->common->show_date($today,'Y');
			$t_appt['hour'] = $GLOBALS['phpgw']->common->show_date($starttime,'H');
			$t_appt['min']  = $GLOBALS['phpgw']->common->show_date($starttime,'i');
			$t_appt['sec']  = 0;
			$t_time = $this->maketime($t_appt) - phpgwapi_datetime::user_timezone();
			$y_time = $t_time - 86400;
			$tt_time = $t_time + 86399;
			print_debug('T_TIME',$t_time.' : '.$GLOBALS['phpgw']->common->show_date($t_time));
			print_debug('Y_TIME',$y_time.' : '.$GLOBALS['phpgw']->common->show_date($y_time));
			print_debug('TT_TIME',$tt_time.' : '.$GLOBALS['phpgw']->common->show_date($tt_time));
			while(list($key,$alarm) = @each($event['alarm']))
			{
				if($alarm['enabled'])
				{
					print_debug('TIME',$alarm['time'].' : '.$GLOBALS['phpgw']->common->show_date($alarm['time']).' ('.$event['id'].')');
					if($event['recur_type'] != MCAL_RECUR_NONE)   /* Recurring Event */
					{
						print_debug('Recurring Event');
						if($alarm['time'] > $y_time && $GLOBALS['phpgw']->common->show_date($alarm['time'],'Hi') < $starttime_hi && $alarm['time'] < $t_time)
						{
							$found = True;
						}
					}
					elseif($alarm['time'] > $y_time && $alarm['time'] < $t_time)
					{
						$found = True;
					}
				}
			}
			print_debug('Found',$found);
			return $found;
		}
		
		function prepare_recipients(&$new_event,$old_event)
		{
			// Find modified and deleted users.....
			while(list($old_userid,$old_status) = each($old_event['participants']))
			{
				if(isset($new_event['participants'][$old_userid]))
				{
					print_debug('Modifying event for user',$old_userid);
					$this->modified[intval($old_userid)] = $new_status;
				}
				else
				{
					print_debug('Deleting user from the event',$old_userid);
					$this->deleted[intval($old_userid)] = $old_status;
				}
			}
			// Find new users.....
			while(list($new_userid,$new_status) = each($new_event['participants']))
			{
				if(!isset($old_event['participants'][$new_userid]))
				{
					print_debug('Adding event for user',$new_userid);
					$this->added[$new_userid] = 'U';
					$new_event['participants'][$new_userid] = 'U';
				}
			}
		
			if(count($this->added) > 0 || count($this->modified) > 0 || count($this->deleted) > 0)
			{
				if(count($this->added) > 0)
				{
					$this->send_update(MSG_ADDED,$this->added,'',$new_event);
				}
				if(count($this->modified) > 0)
				{
					$this->send_update(MSG_MODIFIED,$this->modified,$old_event,$new_event);
				}
				if(count($this->deleted) > 0)
				{
					$this->send_update(MSG_DELETED,$this->deleted,$old_event);
				}
			}
		}

		function remove_doubles_in_cache($firstday,$lastday)
		{
			$already_moved = array();
			for($v=$firstday;$v<=$lastday;$v++)
			{
				if (!isset($this->cached_events[$v]) || !$this->cached_events[$v])
				{
					continue;
				}
				$cached = $this->cached_events[$v];
				$this->cached_events[$v] = array();
				while (list($g,$event) = each($cached))
				{
					$end = date('Ymd',$this->maketime($event['end']));
					print_debug('EVENT',_debug_array($event,False));
				//	print_debug('start',$start);
					print_debug('v',$v);

					if (!isset($already_moved[$event['id']]) || $event['recur_type'] && $v > $end)
					{
						$this->cached_events[$v][] = $event;
						$already_moved[$event['id']] = 1;
						print_debug('Event moved');
					}
				}
			}
		}
		
		function get_dirty_entries($lastmod=-1)
		{
			$events = false;
			$event_ids = $this->so->cal->list_dirty_events($lastmod);
			if(is_array($event_ids))
			{
				foreach($event_ids as $key => $id)
				{
					$events[$id] = $this->so->cal->fetch_event($id);
				}
			}
			unset($event_ids);

			$rep_event_ids = $this->so->cal->list_dirty_events($lastmod,$true);
			if(is_array($rep_event_ids))
			{
				foreach($rep_event_ids as $key => $id)
				{
					$events[$id] = $this->so->cal->fetch_event($id);
				}
			}
			unset($rep_event_ids);
			
			return $events;
		}

		function _debug_array($data)
		{
			echo '<br />UI:';
			_debug_array($data);
		}
		
		/**
		 * checks if event is rejected from user and he's not the owner and dont want rejected
		*
		 * @param $event to check
		 * @return True if event should not be shown
		 */
		function rejected_no_show($event)
		{
			$ret = !$this->prefs['calendar']['show_rejected'] && 
				   $event['owner'] != $this->owner && 
				   isset($event['participants'][$this->owner]) &&
				   $event['participants'][$this->owner] == 'R';
			//echo "<p>rejected_no_show($event[title])='$ret': user=$this->owner, event-owner=$event[owner], status='".$event['participants'][$this->owner]."', show_rejected='".$this->prefs['calendar']['show_rejected']."'</p>\n";
			return $ret;
		}
		
		/**
		 * generate list of user- / group-calendars for the selectbox in the header
		*
		 * @return alphabeticaly sorted array with groups first and then users
		 */
		function list_cals()
		{
			$users = $groups = array();
			foreach($this->grants as $id => $rights)
			{
				$this->list_cals_add($id,$users,$groups);
			}
			/*
			if ($memberships = $GLOBALS['phpgw']->accounts->membership($GLOBALS['phpgw_info']['user']['account_id']))
			{
				foreach($memberships as $group_info)
				{
					//Now group membership doesn't automatically grant rights
					//$this->list_cals_add($group_info['account_id'],$users,$groups);

					if ($account_perms = $GLOBALS['phpgw']->acl->get_ids_for_location($group_info['account_id'],PHPGW_ACL_READ,'calendar'))
					{
						foreach($account_perms as $id)
						{
							$this->list_cals_add($id,$users,$groups);
						}
					}
				}
			}
			*/
			uksort($users,'strnatcasecmp');
			uksort($groups,'strnatcasecmp');

			return $users + $groups;	// users first and then groups, both alphabeticaly
		}

		/**
		* Add user or group to the list of available groups/users in calendar
		*
		* @internal moved to seperate method to fix php5 problems - skwashd
		* @access private
		* @param int $id user uid
		* @param array ref to array of users
		* @param array ref to array of groups
		*/
		function list_cals_add($id, &$users, &$groups)
		{
			if (($type = $GLOBALS['phpgw']->accounts->get_type($id)) == 'g')
			{
				$arr = &$groups;
				$value = "g_{$id}";
				$name = $GLOBALS['phpgw']->common->grab_owner_name($id);
				//echo "group name:{$name} acct_id:{$id} contact_id:{$value}<br />\n";
			}
			else
			{
				$arr = &$users;
				$value = $this->contacts->is_contact($id);
				$name = $this->contacts->get_name_of_person_id($value);
			}
			$arr[$name] = array(
				'grantor'	=> $id,
				'value'		=> $value,
				'name'		=> $name
			);
		}
		
		/**
		 * create array with name, translated name and readable content of each attributes of an event
		*
		 * @param $event event to use
		 * @return array of attributes with fieldname as key and array with the 'field'=translated name \
		 * 	'data' = readable content (for participants this is an array !)
		 */
		function event2array($event)
		{
			$user_timezone = phpgwapi_datetime::user_timezone();

			if ( !is_object($GLOBALS['phpgw']->contacts) )
			{
				$GLOBALS['phpgw']->contacts = createObject('phpgwapi.contacts');
			}
			
			$var['title'] = array(
				'field'		=> lang('Title'),
				'data'		=> $event['title']
			);

			// Some browser add a \n when its entered in the database. Not a big deal
			// this will be printed even though its not needed.
			$var['description'] = array(
				'field'	=> lang('Description'),
				'data'	=> $event['description']
			);

			$cats = array();
			$cat_string[] = '';

			$this->cat->__construct($GLOBALS['phpgw']->accounts->search_person($this->owner),'calendar');

			if(strpos($event['category'],','))
			{
				$cats = explode(',',$event['category']);
			}
			else
			{
				$cats[] = $event['category'];
			}
			foreach($cats as $cat_id)
			{
				if ( !$cat_id )
				{
					continue;
				}

				list($cat) = $this->cat->return_single($cat_id);
				$cat_string[] = $cat['name'];
			}
			$var['category'] = array(
				'field'	=> lang('Category'),
				'data'	=> implode(', ',$cat_string)
			);

			$var['location'] = array(
				'field'	=> lang('Location'),
				'data'	=> $event['location']
			);

			$var['startdate'] = array(
				'field'	=> lang('Start Date/Time'),
				'data'	=> $GLOBALS['phpgw']->common->show_date($this->maketime($event['start']) - $user_timezone),
			);

			$var['enddate'] = array(
				'field'	=> lang('End Date/Time'),
				'data'	=> $GLOBALS['phpgw']->common->show_date($this->maketime($event['end']) - $user_timezone)
			);

			$pri = array
			(
				1	=> lang('Low'),
				2	=> lang('Normal'),
		  		3	=> lang('High')
			);
			if ( !$event['priority'] )
			{
				$event['priority'] = 1;
			}

			$var['priority'] = array
			(
				'field'	=> lang('Priority'),
				'data'	=> $pri[$event['priority']]
			);

			$var['owner'] = array(
				'field'	=> lang('Created By'),
				'data'	=> $GLOBALS['phpgw']->contacts->get_name_of_person_id($event['owner'])
			);

			$var['updated'] = array
			(
				'field'	=> lang('Updated'),
				'data'	=> $GLOBALS['phpgw']->common->show_date($this->maketime($event['modtime']) - $user_timezone)
			);

			$var['access'] = array
			(
				'field'	=> lang('Access'),
				'data'	=> $event['public'] ? lang('Public') : lang('Privat')
			);

			if ( isset($event['groups'][0]) )
			{
				$cal_grps = '';
				for($i=0;$i<count($event['groups']);$i++)
				{
					if($GLOBALS['phpgw']->accounts->exists($event['groups'][$i]))
					{
						$cal_grps .= ($i>0?'<br />':'')
							. $GLOBALS['phpgw']->contacts->get_name_of_person_id($GLOBALS['phpgw']->contacts->is_contact($event['groups'][$i]));
					}
				}

				$var['groups'] = array
				(
					'field'	=> lang('Groups'),
					'data'	=> $cal_grps
				);
			}

			$participants = array();
			if(is_array($event['participants']) )
			{
				foreach($event['participants'] as $user => $short_status)
				{
					$participants[$user] = $GLOBALS['phpgw']->contacts->get_contact_name($user).' ('.$this->get_long_status($short_status).')';
				}
			}
			$var['participants'] = array(
				'field'	=> lang('Participants'),
				'data'	=> $participants
			);

			// Repeated Events
			if($event['recur_type'] != MCAL_RECUR_NONE)
			{
				$str = lang($this->rpt_type[$event['recur_type']]);

				$str_extra = '';
				if ($event['recur_enddate']['mday'] != 0 && $event['recur_enddate']['month'] != 0 && $event['recur_enddate']['year'] != 0)
				{
					$recur_end = $this->maketime($event['recur_enddate']);
					if($recur_end != 0)
					{
						$recur_end -= phpgwapi_datetime::user_timezone();
						$str_extra .= lang('ends').': '.lang($GLOBALS['phpgw']->common->show_date($recur_end,'l')).', '.$this->long_date($recur_end).' ';
					}
				}
				if($event['recur_type'] == MCAL_RECUR_WEEKLY || $event['recur_type'] == MCAL_RECUR_DAILY)
				{
					$repeat_days = array();
					foreach ($this->rpt_day as $mcal_mask => $dayname)
					{
						if ($event['recur_data'] & $mcal_mask)
						{
							$repeat_days[] = lang($dayname);
						}
					}
					if(count($repeat_days))
					{
						$str_extra .= lang('days repeated').': '.implode(', ',$repeat_days);
					}
				}
				if($event['recur_interval'] != 0)
				{
					$str_extra .= lang('Interval').': '.$event['recur_interval'];
				}

				if($str_extra)
				{
					$str .= ' ('.$str_extra.')';
				}

				$var['recure_type'] = array(
					'field'	=> lang('Repetition'),
					'data'	=> $str,
				);
			}

			if (!isset($this->fields))
			{
				$this->custom_fields = CreateObject('calendar.bocustom_fields');
				$this->fields = &$this->custom_fields->fields;
				$this->stock_fields = &$this->custom_fields->stock_fields;
			}
			foreach($this->fields as $field => $data)
			{
				if (!isset($data['disabled']) || !$data['disabled'])
				{
					if (isset($var[$field]))
					{
						$sorted[$field] = $var[$field];
					}
					elseif (!isset($this->stock_fields[$field]) && strlen($event[$field]))	// Custom field
					{
						$lang = lang($name = substr($field,1));
						$sorted[$field] = array(
							'field' => $lang == $name.'*' ? $name : $lang,
							'data'  => $event[$field]
						);
					}
				}
				unset($var[$field]);
			}
			foreach($var as $name => $v)
			{
				$sorted[$name] = $v;

			}
			return $sorted;
		}

		/**
		 * sets the default prefs, if they are not already set (on a per pref. basis)
		*
		 * It sets a flag in the app-session-data to be called only once per session
		 */
		function check_set_default_prefs()
		{
			if (($set = $GLOBALS['phpgw']->session->appsession('default_prefs_set','calendar')))
			{
				return;
			}
			$GLOBALS['phpgw']->session->appsession('default_prefs_set','calendar','set');

			$default_prefs = $GLOBALS['phpgw']->preferences->default['calendar'];

			$subject = lang('Calendar Event') . ' - $$action$$: $$startdate$$ $$title$$'."\n";
			$defaults = array(
				'defaultcalendar' => 'week',
				'mainscreen_showevents' => '0',
				'summary'         => 'no',
				'receive_updates' => 'no',
				'update_format'   => 'extended',	// leave it to extended for now, as iCal kills the message-body
				'notifyAdded'     => $subject . lang ('You have a meeting scheduled for %1','$$startdate$$'),
				'notifyCanceled'  => $subject . lang ('Your meeting scheduled for %1 has been canceled','$$startdate$$'),
				'notifyModified'  => $subject . lang ('Your meeting that had been scheduled for %1 has been rescheduled to %2','$$olddate$$','$$startdate$$'),
				'notifyResponse'  => $subject . lang ('On %1 %2 %3 your meeting request for %4','$$date$$','$$fullname$$','$$action$$','$$startdate$$'),
				'notifyAlarm'     => lang('Alarm for %1 at %2 in %3','$$title$$','$$startdate$$','$$location$$')."\n".lang ('Here is your requested alarm.'),
				'show_rejected'   => '0',
				'display_status'  => '1',
				'weekdaystarts'   => 'Monday',
				'workdaystarts'   => '9',
				'workdayends'     => '17',
				'interval'        => '30',
				'defaultlength'   => '60',
				'planner_start_with_group' => $GLOBALS['phpgw']->accounts->name2id('Default'),
				'planner_intervals_per_day'=> '4',
				'defaultfilter'   => 'all',
				'default_private' => '0',
				'display_minicals'=> '1',
				'show_descrpt_ovr'=> '1',
				'show_descrpt_title'=> '1',
				'show_time_line'  => '1'
			);
			$need_save = false;
			foreach($defaults as $var => $default)
			{
				if (!isset($default_prefs[$var]) || $default_prefs[$var] == '')
				{
					$GLOBALS['phpgw']->preferences->add('calendar',$var,$default,'default');
					$need_save = True;
				}
			}
			if ($need_save)
			{
				$prefs = $GLOBALS['phpgw']->preferences->save_repository(False,'default');
				$this->prefs['calendar'] = $prefs['calendar'];
			}
			if (isset($this->prefs['calendar']['send_updates']) && $this->prefs['calendar']['send_updates']
				&& !isset($this->prefs['calendar']['receive_updates']))
			{
				$this->prefs['calendar']['receive_updates'] = $this->prefs['calendar']['send_updates'];
				$GLOBALS['phpgw']->preferences->add('calendar','receive_updates',$this->prefs['calendar']['send_updates']);
				$GLOBALS['phpgw']->preferences->delete('calendar','send_updates');
				$prefs = $GLOBALS['phpgw']->preferences->save_repository();
			}
		}

		/**
		* Search for a list of organization contacts based on their [first|last]name
		*
		* @author skwashd
		* @param string $lookup the name to search for
		* @param int $cat_id the category to limit the search to
		* @returns array contacts found
		*/
		function get_org_contacts($lookup, $cat_id)
		{
			$fields = array ('contact_id', 'org_name');

			$criteria_search[] = phpgwapi_sql_criteria::token_begin('org_name', $lookup);

			$criteria[] = phpgwapi_sql_criteria::_append_or($criteria_search);
			$criteria[] = $this->contacts->criteria_for_index($GLOBALS['phpgw_info']['user']['account_id']);

			if ( $cat_id )
			{
				$criteria[] = phpgwapi_sql_criteria::_equal('cat_id', phpgw::get_var('cat_id', 'int', 'bool') );
			}

			$criteria_token = phpgwapi_sql_criteria::_append_and($criteria);
			return $this->contacts->get_orgs($fields, 0, 0, 'org_name', 'ASC', '', $criteria_token);
		}

		/**
		* Search for a list of person contacts based on their [first|last]name
		*
		* @author skwashd
		* @param string $lookup the name to search for
		* @param int $cat_id the category to limit the search to
		* @returns array contacts found
		*/
		function get_per_contacts($lookup, $cat_id)
		{
			//echo "lookup == $lookup and user_id == {$GLOBALS['phpgw_info']['user']['account_id']}";
			$fields = array ('contact_id', 'per_first_name', 'per_last_name');

			$criteria_search[] = phpgwapi_sql_criteria::token_begin('per_first_name', $lookup);
			$criteria_search[] = phpgwapi_sql_criteria::token_begin('per_last_name', $lookup);

			$criteria[] = phpgwapi_sql_criteria::_append_or($criteria_search);
			$criteria[] = $this->contacts->criteria_for_index((int) $GLOBALS['phpgw_info']['user']['account_id']);

			if ( $cat_id )
			{
				$criteria[] = phpgwapi_sql_criteria::_equal('cat_id', phpgw::get_var('cat_id', 'int', 'bool') );
			}

			$criteria_token = phpgwapi_sql_criteria::_append_and($criteria);
			return $this->contacts->get_persons($fields, 0, 0, 'per_first_name, per_last_name', 'ASC', '', $criteria_token);
		}

		/**
		* Get a list of phpgw groups
		*/
		function get_groups($search)
		{
			if ( $search == '%' )
			{
				$groups = $GLOBALS['phpgw']->accounts->get_list('groups', -1, 'ASC', 'account_firstname');
			}
			else
			{
				$groups = $GLOBALS['phpgw']->accounts->get_list('groups', -1, 'account_firstname', 'ASC', $search);
			}

			$group_list = array();
			
			if ( is_array($groups) && count($groups) )
			{
				foreach ( $groups as $group )
				{
					$group_list[] = array
							(
								'contact_id' 	=> "g_{$group['account_id']}",
								'per_first_name'=> $group['account_firstname'],
								'per_last_name'	=> $group['account_lastname']
							);
				}
			}
			return $group_list;
		}
		
		/**
		* Get list of available category colors
		*
		* @internal current returns an empty array as API doesn't support functionality
		* @author Thomas Bott
		* @returns array of html-style colors for each category
		*/
		function get_cat_colors()
		{
			return array();
			$cat_color_ids = $this->cat->get_cat_colors();

			foreach ($cat_color_ids as $cat_id => $entry)
			{
				if ( $entry['cat_color_id'] )
				{
					$cat_colors[$cat_id] = $GLOBALS['phpgw_info']['theme']['cat_color'][$entry['cat_color_id']];
				}
				else # if empty, find next parent with color set
				{
					$cat_parent_id = $entry['cat_parent'];
					$color_to_set = FALSE;
					while ( !$color_to_set )
					{
						$color_to_set = ($cat_color_ids[$cat_parent_id]['cat_color_id']) ? $cat_color_ids[$cat_parent_id]['cat_color_id'] : FALSE;
						if ($cat_parent_id == 0 && ! $color_to_set)
						{
							$color_to_set = "1";
						}
						$cat_parent_id = $cat_color_ids[$cat_parent_id]['cat_parent'];

					}
					$cat_colors[$cat_id] = $GLOBALS['phpgw_info']['theme']['cat_color'][$color_to_set];
				}
			}

			return $cat_colors;
		}
	}
