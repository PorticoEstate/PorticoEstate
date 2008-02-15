<?php
	/**
	* Project Manager
	*
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package projects
	* @version $Id$
	* $Source: /sources/phpgroupware/projects/inc/class.boconfig.inc.php,v $
	*/

	class boconfig
	{
		var $action;
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;

		var $public_functions = array
		(
			'save_prefs'              => True,
			'selected_employees'      => True,
			'read_accounting_factors' => True,
			'save_accounting_factor'  => True,
			'read_admins'				=> True,
			'list_admins'				=> True,
			'selected_admins'			=> True,
			'edit_admins'				=> True,
			'read_single_activity'		=> True,
			'exists'					=> True,
			'check_pa_values'			=> True,
			'list_activities'			=> True,
			'save_activity'				=> True,
			'delete_pa'					=> True,
			'list_roles'				=> True,
			'save_role'					=> True,
			'save_event'				=> True
		);

		function boconfig()
		{
			$action           = get_var('action',array('GET'));
			$this->debug      = false;
			$this->boprojects	= CreateObject('projects.boprojects',True,$action);
			$this->soconfig   = $this->boprojects->soconfig;
			$this->start      = $this->boprojects->start;
			$this->query      = $this->boprojects->query;
			$this->filter     = $this->boprojects->filter;
			$this->order      = $this->boprojects->order;
			$this->sort       = $this->boprojects->sort;
			$this->cat_id     = $this->boprojects->cat_id;
		}

		function save_prefs($prefs)
		{
			$GLOBALS['phpgw']->preferences->read_repository();

			if(!is_array($prefs['cols']))
			{
				$prefs['cols'] = array();
			}
			if(!is_array($prefs['cscols']))
			{
				$prefs['cscols'] = array();
			}
			if(!isset($prefs['send_status_mail']))
			{
				$prefs['send_status_mail'] = true;
			}

			$GLOBALS['phpgw']->preferences->change('projects','columns',implode(',',$prefs['cols']));
			$GLOBALS['phpgw']->preferences->change('projects','cscolumns',implode(',',$prefs['cscols']));
			$GLOBALS['phpgw']->preferences->change('projects','send_status_mail',$prefs['send_status_mail']);
			$GLOBALS['phpgw']->preferences->change('projects','mainscreen_showevents',$prefs['mainscreen_showevents']);	

			$GLOBALS['phpgw']->preferences->save_repository(True);
		}

		function selected_employees()
		{
			$emps = $this->boprojects->read_projects_acl();
			$empl = array();
			if(is_array($emps))
			{
				$emps = array_unique($emps);
				for($i=0;$i<count($emps);$i++)
				{
					$GLOBALS['phpgw']->accounts->get_account_name($emps[$i],$lid,$fname,$lname);
					$fullname = $GLOBALS['phpgw']->common->display_fullname($lid,$fname,$lname);

					$empl[] = array
					(
						'account_id'		=> $emps[$i],
						'account_lid'		=> $lid,
						'account_firstname'	=> $fname,
						'account_lastname'	=> $lname,
						'account_fullname'	=> $fullname
					);
					$lid = $fname = $lname = $fullname = '';
				}
			}

			if(is_array($empl))
			{
				usort($empl, array('boconfig', 'cmp_employees'));
			}

			return $empl;
		}

		function cmp_employees($a, $b) 
		{
			return strcasecmp($a['account_fullname'], $b['account_fullname']);
		}

		function read_accounting_factors($data = 0)
		{
			$factors = $this->soconfig->read_employees(array('start' => $this->start,'sort' => $this->sort,'order' => $this->order,
																		'query' => $this->query,'limit' => isset($data['limit'])?$data['limit']:$this->limit,
																		'account_id' => $data['account_id'],'id' => $data['id']));
			$this->total_records = $this->soconfig->total_records;
			if(is_array($factors))
			{
				foreach($factors as $emp)
				{
					$edate = $emp['edate']>0?$this->boprojects->format_date($emp['edate']):'';
					$sdate = $emp['sdate']>0?$this->boprojects->format_date($emp['sdate']):'';
					$location = $this->get_single_location($emp['location_id']);
					
					$emps[] = array
					(
						'id'				=> $emp['id'],
						'account_id'		=> $emp['account_id'],
						'account_name'		=> $GLOBALS['phpgw']->common->grab_owner_name($emp['account_id']),
						'accounting'		=> $emp['accounting'],
						'd_accounting'		=> $emp['d_accounting'],
						'weekly_workhours'	=> $emp['weekly_workhours'],
						'cost_centre'		=> $emp['cost_centre'],
						'location'		=> $location,
						'sdate'				=> $emp['sdate'],
						'edate'				=> $emp['edate'],
						'sdate_formatted'	=> $sdate['date_formatted'],
						'edate_formatted'	=> $edate['date_formatted']
					);
				}
				
				if(!$this->order || $this->order == 'account_id')
				{
					if(is_array($emps))
					{
						usort($emps, array("boconfig", "cmp_account_name"));
						if($this->sort == 'DESC')
						{
							$emps = array_reverse($emps);
						}
					}
				}
				
				return $emps;
			}
			return false;
		}

		function cmp_account_name($a, $b)
		{
			return strcasecmp($a['account_name'], $b['account_name']);
		}

		function save_accounting_factor($values)
		{
			$h = $this->boprojects->siteconfig['hwday'];

			if(intval($values['accounting']) > 0)
			{
				$values['d_accounting'] = round($values['accounting']* $h,2);
			}
			else if(intval($values['d_accounting']) > 0)
			{
				$values['accounting'] = round($values['d_accounting']/$h,2);
			}
			$this->boprojects->soconfig->save_accounting_factor($values);
		}

		function read_single_afactor($id)
		{
			return $this->boprojects->soconfig->read_single_afactor($id);
		}

		function read_admins($action,$type)
		{
			$admins = $this->boprojects->soconfig->read_admins($action,$type);
			$this->total_records = $this->boprojects->soconfig->total_records;
			return $admins;
		}

		function list_admins($action)
		{
			$admins = $this->boprojects->soconfig->read_admins($action,$type='');

			//_debug_array($admins);

			$this->total_records = $this->boprojects->soconfig->total_records;

			if(is_array($admins))
			{
				foreach($admins as $ad)
				{
					$accounts = CreateObject('phpgwapi.accounts',$ad['account_id']);
					$accounts->read_repository();
					$admin_data[] = array
					(
						'account_id'	=> $ad['account_id'],
						'lid'			=> $accounts->data['account_lid'],
						'firstname'		=> $accounts->data['firstname'],
						'lastname'		=> $accounts->data['lastname'],
						'type'			=> $accounts->get_type($ad['account_id'])
					);
					unset($accounts);
				}
			}
			return $admin_data;
		}

		function selected_admins($action,$type = 'user')
		{
			$is_admin = $this->read_admins($action,$type);
			$selected = array();
			$i = 0;
			if(is_array($is_admin))
			{
				foreach($is_admin as $ad)
				{
					$selected[$i] = $ad['account_id'];
					++$i;
				}
			}

			$aclusers = $this->boprojects->read_projects_acl(False);

			$alladmins = $type=='user'?$aclusers['users']:$aclusers['groups'];

			if (is_array($alladmins))
			{
				for($i=0;$i<count($alladmins);++$i)
				{
					$selected_admins .= '<option value="' . $alladmins[$i] . '"';
					if(in_array($alladmins[$i],$selected))
					{
						$selected_admins .= ' selected';
					}
					$selected_admins .= '>' . $GLOBALS['phpgw']->common->grab_owner_name($alladmins[$i]) . '</option>' . "\n";
				}
			}
			return $selected_admins;
		}

		function edit_admins($action,$users,$groups)
		{
			$this->boprojects->soconfig->edit_admins($action,$users,$groups);
		}

		function read_single_activity($activity_id)
		{
			$single_act = $this->boprojects->soconfig->read_single_activity($activity_id);
			return $single_act;
		}

		function exists($values)
		{
			return $this->boprojects->soconfig->exists($values);
		}

		function check_pa_values($values, $action = 'activity')
		{
			switch($action)
			{
				case 'role':
					if (strlen($values['role_name']) > 250)
					{
						$error[] = lang('name not exceed 250 characters in length');
					}

					if (!$values['role_name'])
					{
						$error[] = lang('Please enter a name');
					}
					break;
				case 'accounting':
					//_debug_array($values);

					if(!$values['sdate'])
					{
						$error[] = lang('please set the start date');
						$overlap = False;
					}
					elseif($values['sdate'] && $values['edate'])
					{
						$existing = $this->read_accounting_factors(array('limit' => False,'account_id' => $values['account_id'],'id' => $values['id']));

						if(is_array($existing))
						{
							//_debug_array($existing);
							foreach($existing as $exists)
							{
								if($exists['edate'] && $values['sdate'] <= $exists['sdate'] && $values['edate'] >= $exists['edate'])
								{
									$overlap = True;
								}
								elseif($values['sdate'] <= $exists['sdate'] && $values['edate'] >= $exists['sdate'] && $values['edate'] <= $exists['edate'])
								{
									$overlap = True;
								}
								elseif($values['sdate'] >= $exists['sdate'] && $values['edate'] <= $exists['edate'])
								{
									$overlap = True;
								}
								elseif($values['sdate'] >= $exists['sdate'] && $values['sdate'] <= $exists['edate'] && $values['edate'] >= $exists['edate'])
								{
									$overlap = True;
								}
							}
						}
					}
					elseif($values['sdate'] && !$values['edate'])
					{
						$existing = $this->read_accounting_factors(array('limit' => False,'account_id' => $values['account_id'],'id' => $values['id']));

						if(is_array($existing))
						{
							//_debug_array($existing);
							foreach($existing as $exists)
							{
								if(($exists['sdate'] <= $values['sdate']) && ($values['sdate'] <= $exists['edate']))
								{
									$overlap = True;
								}
								elseif(($exists['sdate'] <= $values['sdate']) && !$exists['edate'])
								{
									$overlap = True;
								}
								elseif($values['sdate'] <= $exists['sdate'])
								{
									$overlap = True;
								}
							}
						}
					}

					if($overlap)
					{
						$error[] = lang('the choosen timeframe interleaves an already existing timeframe');
					}
					
					if (!is_numeric($values['weekly_workhours']))
					{
						$error[] = lang('please set the weekly workhours');
					}
					if (!is_numeric($values['cost_centre']))
					{
						$error[] = lang('please set the cost centre');
					}
					break;
				default:
					if (strlen($values['descr']) > 250)
					{
						$error[] = lang('Description can not exceed 250 characters in length');
					}

					if (! $values['choose'])
					{
						if (! $values['number'])
						{
							$error[] = lang('Please enter an ID');
						}
						else
						{
							$exists = $this->exists(array('check' => 'number', 'number' => $values['number'],'pa_id' => $values['activity_id']));

							if ($exists)
							{
								$error[] = lang('That ID has been used already');
							}

							if (strlen($values['number']) > 20)
							{
								$error[] = lang('id can not exceed 20 characters in length');
							}
						}
					}

					if ((! $values['billperae']) || ($values['billperae'] == 0))
					{
						$error[] = lang('please enter the bill');
					}

					if ($this->boprojects->siteconfig['activity_bill'] == 'wu')
					{
						if ((! $values['minperae']) || ($values['minperae'] == 0))
						{
							$error[] = lang('please enter the minutes per workunit');
						}
					}
					break;
			}

			if (is_array($error))
			{
				return $error;
			}
			return False;
		}

		function list_activities()
		{
			$act_list = $this->boprojects->soconfig->read_activities(array('start' => $this->start,'limit' => $this->limit,'query' => $this->query,
																'sort' => $this->sort,'order' => $this->order,'cat_id' => $this->cat_id));
			$this->total_records = $this->boprojects->soconfig->total_records;
			return $act_list;
		}

		function save_activity($values)
		{
			if ($values['choose'])
			{
				$values['number'] = $this->boprojects->soprojects->create_activityid();
			}

			if ($values['activity_id'])
			{
				if ($values['activity_id'] && intval($values['activity_id']) > 0)
				{
					$this->boprojects->soconfig->edit_activity($values);

					if ($values['minperae'])
					{
						$this->boprojects->sohours->update_hours_act($values['activity_id'],$values['minperae']);
					}
				}
			}
			else
			{
				$this->boprojects->soconfig->add_activity($values);
			}
		}

		function delete_pa($action, $pa_id)
		{
			$this->boprojects->soconfig->delete_pa($action, $pa_id);
		}

		function list_roles()
		{
			$roles = $this->boprojects->list_roles();
			$this->total_records = $this->boprojects->total_records;
			return $roles;
		}

		function save_role($role_name)
		{
			$this->boprojects->soconfig->save_role($role_name);
		}

		function save_event($values)
		{
			$this->soconfig->save_event($values);
		}

		function save_surcharge($values)
		{
			$this->soconfig->save_surcharge($values);
		}

		function config_proid_help_msg($params)
		{
			switch($params['action'])
			{
				case 'get':
					$config = $this->soconfig->get_site_config(array('helpmsg' => True,'default' => False));
					return $config['proid_help_msg'];
					break;
				case 'save':
					$config = CreateObject('phpgwapi.config','projects');
					$config->read_repository();
					$config->value('proid_help_msg',$params['proid_help_msg']);
					$config->save_repository();					
					break;
			}
		}

		/**
		* Configure the worktime statusmail. The method can used for get and set the configuration (mail_type => (off | weekly | monthly).
		* @param array $values contains the action (get or set) and the value set by the user
		* @return mixed array when get the configuration; boolean if set the configuration: True if config was saved, otherwise false
		*/
		function config_worktime_statusmail($values)
		{
			if(!isset($values['action']))
			{
				return False;
			}

			$config = CreateObject('phpgwapi.config','projects');
			$config->read_repository();

			switch($values['action'])
			{
				case 'get':
					if(isset($config->config_data['worktime_statusmail']))
						$mail_type = $config->config_data['worktime_statusmail'];
					else
						$mail_type = 'off';

					return $mail_type;
				break;
				case 'save':
					if(!isset($values['mail_type']))
						return false;

					switch($values['mail_type'])
					{
						case 'off':
							$mail_type = 'off';
							$start     = 0;
						break;
						case 'weekly':
							$mail_type = 'weekly';
							$dow_1 = date('d') - date('w') + 1;
							if (date('w') == 0)
								$dow_1 = $dow_1 - 7;
							$start = mktime(0,0,0,date('m'),$dow_1,date('Y'));
						break;
						case 'monthly':
							$mail_type = 'monthly';
							$start = mktime(0,0,0,date('m'),1,date('Y'));
						break;
						default:
							return false;
						break;
					}

					$config->value('worktime_statusmail', $mail_type);
					$config->save_repository();

					// set async service
					$this->boprojects->update_async($mail_type, $start);

					return True;
				break;
				default:
					return false;
				break;
			}
		}

		/**
		* Configure the worktime warnmail. The method can used for get and set the configuration (mail_type => (off | 0 | 1 | ... ).
		* @param array $values contains the action (get or set) and the value set by the user
		* @return mixed array when get the configuration; boolean if set the configuration: True if config was saved, otherwise false
		*/
		function config_worktime_warnmail($values)
		{
			if(!isset($values['action']))
				return false;
			
			$config = CreateObject('phpgwapi.config','projects');
			$config->read_repository();

			switch($values['action'])
			{
				case 'get':
					$warnmail = array();
					if(isset($config->config_data['worktime_warnmail']))
						$warnmail['type'] = $config->config_data['worktime_warnmail'];
					else
						$warnmail['type'] = -1;
					if(isset($config->config_data['warnmail_email_address']))
						$warnmail['warnmail_email_address'] = $config->config_data['warnmail_email_address'];
					else
						$warnmail['warnmail_email_address'] = '';
					return $warnmail;
				break;
				case 'save':
					if(!isset($values['warnmail_type']))
						return false;
					$warnmail_type = intval($values['warnmail_type']);
					$warnmail_email_address  = $values['warnmail_email_address'];

					$config->value('worktime_warnmail', $warnmail_type);
					$config->value('warnmail_email_address', $warnmail_email_address);
					$config->save_repository();

					// set async service
					$async = CreateObject('phpgwapi.asyncservice');
					$aid = 'projects-worktime-warnmail-';

					// set async service
					if($warnmail_type == -1)
					{ // remove async
						$jobs = $async->read($aid.'%');
						if(is_array($jobs))
						{
							foreach($jobs as $job)
							{
								$async->delete($job['id']);
							}
						}
						return True;
					}
					else
					{ // update async setting
						$jobs = $async->read($aid.'%');
						if($jobs)
						{
							foreach($jobs as $job)
							{
								$async->delete($job['id']);
							}
						}

						$warnmail_month = date('n', time());
						$warnmail_year  = date('Y', time());

						if($this->boprojects->update_async_warnmail($warnmail_month, $warnmail_year, $warnmail_type))
							return True;
						else
							return false;
					}
				break;
				default:
					return false;
				break;
			}
		}


		/**
		* Configure the workhours booking setting. The method can used for get and set the configuration (book_type => (0, 1 .. 5).
		* @param array $values contains the action (get or set) and the value set by the user
		* @return mixed array when get the configuration; boolean if set the configuration: True if config was saved, otherwise false
		*/
		function config_workhours_booking($values)
		{
			if(!isset($values['action']))
				return false;

			$config = CreateObject('phpgwapi.config','projects');
			$config->read_repository();

			switch($values['action'])
			{
				case 'get':
					if(isset($config->config_data['workhours_booking']))
						$book_type = $config->config_data['workhours_booking'];
					else
						$book_type = 0;

					return $book_type;
				break;
				case 'save':
					if(!isset($values['book_type']))
						return false;

					$book_type = intval($values['book_type']);
					if(($book_type < 0) || ($book_type > 5))
						return false;

					$config->value('workhours_booking', $book_type);
					$config->save_repository();

					$async = CreateObject('phpgwapi.asyncservice');
					$aid = 'projects-workhours-booking-';

					// set async service
					if($book_type == 0)
					{ // remove async
						$jobs = $async->read($aid.'%');
						if(is_array($jobs))
						{
							foreach($jobs as $job)
							{
								$async->delete($job['id']);
							}
						}
					}
					else
					{ // update async setting
						$jobs = $async->read($aid.'%');
						if($jobs)
						{
							foreach($jobs as $job)
							{
								$async->delete($job['id']);
							}
						}

						$holidays = CreateObject('phpgwapi.calendar_holidays');
						$sbox = createobject('phpgwapi.sbox');
//						$country = ucfirst($GLOBALS['phpgw']->translation->retranslate($sbox->country_array[$GLOBALS['phpgw']->preferences->data['common']['country']]));
						$country = ucfirst(lang($sbox->country_array[$GLOBALS['phpgw']->preferences->data['common']['country']]));
						$federal_state = $holidays->federal_states[$country][$GLOBALS['phpgw']->preferences->data['common']['federalstate']]; // Achtung: bisher existiert nur germany!
						$religion = $holidays->religions[$GLOBALS['phpgw']->preferences->data['common']['religion']];

						$ts_now = time();
						$now_d = date('d', $ts_now);
						$now_m = date('n', $ts_now);
						$now_y = date('Y', $ts_now);

						$workdays = $book_type;
						
						// time of booking in this month

						$new_days = $holidays->add_number_of_workdays(1,$now_m,$now_y,$workdays,$country,$federal_state,$religion);
						$new_d = $new_days['newday'];
						$new_m = $new_days['newmonth'];
						$new_y = $new_days['newyear'];
						unset($new_days);

						$ts_book = mktime(0,0,0,$new_m,$new_d,$new_y)-1;

						if($ts_book < $ts_now)
						{
							// book is in past -> book next month
							if($now_m == 12)
							{
								$next_y = $now_y + 1;
								$next_m = 1;
							}
							else
							{
								$next_y = $now_y;
								$next_m = $now_m + 1;
							}
							
							$new_days = $holidays->add_number_of_workdays(1,$next_m,$next_y,$workdays,$country,$federal_state,$religion);
							$new_d = $new_days['newday'];
							$new_m = $new_days['newmonth'];
							$new_y = $new_days['newyear'];
							unset($new_days);

							$ts_book = mktime(0,0,0,$new_m,$new_d,$new_y)-1;
						}

						// calc book month
						$book_run_m = date('n', $ts_book);
						$book_run_y = date('Y', $ts_book);
						if($book_run_m == 1)
						{
							// book december last year
							$book_for_month = 12;
							$book_for_year  = $book_run_y - 1;
						}
						else
						{
							$book_for_month = $book_run_m - 1;
							$book_for_year  = $book_run_y;
						}
						
						$async_data = array(
							'id' => $aid.$book_for_year.'-'.$book_for_month,
							'next' => $ts_book,
							'times' => $ts_book,
							'account_id' => $GLOBALS['phpgw_info']['user']['account_id'],
							'method' => 'projects.boprojects.async_workhours_booking',
							'data' => array(
								'book_type'  => $book_type,
								'book_year'  => $book_for_year,
								'book_month' => $book_for_month
							)
						);
						$async->write($async_data);
					}
					return True;
				break;
				default:
					return false;
				break;
			}
		}
		
		function save_location($location_data)
		{
			$location_data['location_name'] = trim($location_data['location_name']);
			$location_data['location_ident'] = trim($location_data['location_ident']);
			$location_data['location_custnum'] = trim($location_data['location_custnum']);
			$this->soconfig->save_location($location_data);
			$this->get_locations(true);
		}

		function get_locations($reload=false)
		{
			if($this->location_loaded && is_array($this->locations) && !$reload)
			{
				return $this->locations;
			}
			
			$this->locations = $this->soconfig->get_locations();
			$this->location_loaded = true;
			return $this->locations;
		}
		
		function get_single_location($location_id, $reload=false)
		{
			$location_id = intval($location_id);
			if(isset($this->locations[$location_id]) && !$reload)
			{
				return $this->locations[$location_id];
			}

			$location = $this->soconfig->get_single_location($location_id);
			if(isset($location['location_id']))
			{
				$this->locations[$location['location_id']] = $location;
				$this->location_idents[$location_ident] = $location;
			}

			return $location;
		}

		function get_location_for_ident($location_ident, $reload=false)
		{
			if(isset($this->location_idents[$location_ident]) && !$reload)
			{
				return $this->location_idents[$location_ident];
			}

			$location = $this->soconfig->get_location_for_ident($location_ident);
			if(isset($location['location_id']))
			{
				$this->location_idents[$location_ident] = $location;
			}
			
			return $location;
		}

		function delete_location($location_id)
		{
			$this->soconfig->delete_location($location_id);
			$this->get_locations(true);
		}
	}
?>
