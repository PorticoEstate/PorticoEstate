<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_bocommon
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $district_id;


		var $public_functions = array
		(
			'select_part_of_town'	=> True,
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		function property_bocommon($currentapp='')
		{
			if($currentapp)
			{
			//	$this->currentapp	= $currentapp;
			}
			else
			{
			//	$this->currentapp	= 'property';
			}

			$this->socommon			= CreateObject('property.socommon','property');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			if (!isset($GLOBALS['phpgw']->asyncservice))
			{
				$GLOBALS['phpgw']->asyncservice = CreateObject('phpgwapi.asyncservice');
			}
			$this->async = &$GLOBALS['phpgw']->asyncservice;

			if(!isset($GLOBALS['phpgw']->js) || !is_object($GLOBALS['phpgw']->js))
			{
				$GLOBALS['phpgw']->js = CreateObject('phpgwapi.javascript');
			}

			$this->join		= $this->socommon->join;
			$this->left_join	= $this->socommon->left_join;
			$this->like		= $this->socommon->like;

			switch($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mssql':
					$this->dateformat 		= "M d Y";
					$this->datetimeformat 	= "M d Y g:iA";
					break;
				case 'mysql':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
					break;
				case 'pgsql':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
//					$this->dateformat 		= "F j, Y";
//					$this->datetimeformat 	= "F j, Y g:iA";
					break;
				case 'postgres':
					$this->dateformat 		= "Y-m-d";
					$this->datetimeformat 	= "Y-m-d G:i:s";
					break;
			}

		}

		function check_perms($rights, $required)
		{
			return ($rights & $required);
		}

		function create_preferences($app='',$user_id='')
		{
			return $this->socommon->create_preferences($app,$user_id);
		}

		function get_lookup_entity($location='')
		{
			return $this->socommon->get_lookup_entity($location);
		}

		function get_start_entity($location='')
		{
			return $this->socommon->get_start_entity($location);
		}

		function msgbox_data($receipt)
		{
			$msgbox_data_error=array();
			if (isSet($receipt['error']) AND is_array($receipt['error']))
			{
				foreach($receipt['error'] as $errors)
				{
					$msgbox_data_error += array($errors['msg']=> False);
				}
			}

			$msgbox_data_message=array();

			if (isSet($receipt['message']) AND is_array($receipt['message']))
			{
				foreach($receipt['message'] as $messages)
				{
					$msgbox_data_message += array($messages['msg']=> True);
				}
			}

			$msgbox_data = $msgbox_data_error + $msgbox_data_message;

			return $msgbox_data;
		}

		function moneyformat($amount)
		{
			if ($GLOBALS['phpgw_info']['server']['db_type']=='mssql')
			{
				$moneyformat	= "CONVERT(MONEY,"."'$amount'".",0)";
			}
			else
			{
				$moneyformat	= "'" . $amount . "'";
			}

			return $moneyformat;
		}


		function date_array($datestr = '')
		{
			if(!$datestr)
			{
				return false;
			}
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$fields = split('[./-]',$datestr);
			foreach(split('[./-]',$dateformat) as $n => $field)
			{
				$date[$field] = intval($fields[$n]);

				if($field == 'M')
				{
					for($i=1; $i <=12; $i++)
					{
						if(date('M',mktime(0,0,0,$i,1,2000)) == $fields[$n])
						{
							$date['m'] = $i;
						}
					}
				}
			}

			$ret = array(
				'year'  => $date['Y'],
				'month' => $date['m'],
				'day'   => $date['d']
			);
			return $ret;
		}

		function date_to_timestamp($date='')
		{
			if (!$date)
			{
				return False;
			}

			$date_array	= $this->date_array($date);
			$date	= mktime (8,0,0,$date_array['month'],$date_array['day'],$date_array['year']);

			return $date;
		}

		function select_multi_list($selected='',$input_list)
		{
			$j=0;
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					$output_list[$j]['id'] = $entry['id'];
					$output_list[$j]['name'] = $entry['name'];

					if(isset($selected) && is_array($selected))
					{
						for ($i=0;$i<count($selected);$i++)
						{
							if($selected[$i] == $entry['id'])
							{
								$output_list[$j]['selected'] = 'selected';
							}
						}
					}
					$j++;
				}
			}
			return $output_list;
		}

		function select_list($selected='',$input_list='')
		{
			$entry_list = array();
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					if ($entry['id']==$selected)
					{
						$entry_list[] = array
						(
							'id'		=> $entry['id'],
							'name'		=> $entry['name'],
							'selected'	=> 'selected'
						);
					}
					else
					{
						$entry_list[] = array
						(
							'id'		=> $entry['id'],
							'name'		=> $entry['name'],
						);
					}
				}
				return $entry_list;
			}
		}


		function get_user_list($format='',$selected='',$extra='',$default='',$start='', $sort='', $order='', $query='',$offset='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_filter'));
					break;
			}

			if(!$selected && $default)
			{
				$selected = $default;
			}

			if (is_array($extra))
			{
				foreach($extra as $extra_user)
				{
					$users_extra[]=array
					(
						'account_id' => $extra_user,
						'account_firstname' => lang($extra_user)
					);
				}
			}

			$accounts 	= CreateObject('phpgwapi.accounts');
			$users = $accounts->get_list('accounts', $start, $sort, $order, $query,$offset);
			unset($accounts);
			if (isset($users_extra) && is_array($users_extra) && is_array($users))
			{
				$users = $users_extra + $users;
			}

			if (isSet($users) AND is_array($users))
			{
				foreach($users as $user)
				{
					$sel_user = '';
					if ($user['account_id']==$selected)
					{
						$sel_user = 'selected';
					}

					$user_list[] = array
					(
						'user_id'	=> $user['account_id'],
						'name'		=> $user['account_lastname'].' '.$user['account_firstname'],
						'selected'	=> $sel_user
					);
				}
			}

			$user_count= count($user_list);
			for ($i=0;$i<$user_count;$i++)
			{
				if ($user_list[$i]['selected'] != 'selected')
				{
					unset($user_list[$i]['selected']);
				}
			}

//_debug_array($user_list);
			return $user_list;
		}

		function get_group_list($format='',$selected='',$start='', $sort='', $order='', $query='',$offset='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('group_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('group_filter'));
					break;
			}

			$accounts 	= CreateObject('phpgwapi.accounts');
			$users = $accounts->get_list('groups', $start, $sort, $order, $query,$offset);
			unset($accounts);
			if (isSet($users) AND is_array($users))
			{
				foreach($users as $user)
				{
					$sel_user = '';
					if ($user['account_id']==$selected)
					{
						$sel_user = 'selected';
					}

					$user_list[] = array
					(
						'id'	=> $user['account_id'],
						'name'		=> $user['account_firstname'],
						'selected'	=> $sel_user
					);
				}
			}

			$user_count= count($user_list);
			for ($i=0;$i<$user_count;$i++)
			{
				if ($user_list[$i]['selected'] != 'selected')
				{
					unset($user_list[$i]['selected']);
				}
			}

//_debug_array($user_list);
			return $user_list;
		}


		function get_user_list_right($right='',$selected='',$acl_location='',$extra='',$default='')
		{
			if(!$selected && $default)
			{
				$selected = $default;
			}

			if (is_array($extra))
			{
				foreach($extra as $extra_user)
				{
					$users_extra[]=array
					(
						'account_lid' 		=> $extra_user,
						'account_firstname'	=> lang($extra_user),
						'account_lastname'	=> ''
					);
				}
			}

			if(!$users = $this->socommon->fm_cache('acl_userlist_'. $right . '_' . $acl_location))
			{
				$users=$this->socommon->get_user_list_right($right,$acl_location);
				$this->socommon->fm_cache('acl_userlist_'. $right . '_' . $acl_location,$users);
			}

			if (isset($users_extra) && is_array($users_extra) && is_array($users))
			{
				$users = $users_extra + $users;
			}


			while (is_array($users) && list(,$user) = each($users))
			{
				if ($user['account_lid']==$selected)
				{
					$user_list[] = array
					(
						'lid'			=> $user['account_lid'],
						'firstname'		=> $user['account_firstname'],
						'lastname'		=> $user['account_lastname'],
						'selected'		=> 'selected'
					);
				}
				else
				{
					$user_list[] = array
					(
						'lid'			=> $user['account_lid'],
						'firstname'		=> $user['account_firstname'],
						'lastname'		=> $user['account_lastname'],
					);
				}
			}
			return $user_list;
		}

		function get_user_list_right2($format='',$right='',$selected='',$acl_location='',$extra='',$default='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('user_id_filter'));
					break;
			}

			if(!$selected && $default)
			{
				$selected = $default;
			}

			if (isset($extra) AND is_array($extra))
			{
				foreach($extra as $extra_user)
				{
					$users_extra[]=array
					(
						'account_id' => $extra_user,
						'account_firstname' => lang($extra_user)
					);
				}
			}

			if(!$users = $this->socommon->fm_cache('acl_userlist_'. $right . '_' . $acl_location))
			{
				$users=$this->socommon->get_user_list_right($right,$acl_location);
				$this->socommon->fm_cache('acl_userlist_'. $right . '_' . $acl_location,$users);
			}

			if ((isset($users_extra) && is_array($users_extra)) && is_array($users))
			{
				foreach($users as $users_entry)
				{
					array_push($users_extra,$users_entry);
				}
				$users=$users_extra;
			}

			while (is_array($users) && list(,$user) = each($users))
			{
				$name = (isset($user['account_lastname'])?$user['account_lastname'].' ':'').$user['account_firstname'];
				if ($user['account_id']==$selected)
				{
					$user_list[] = array
					(
						'user_id'	=> $user['account_id'],
						'name'		=> $name,
						'selected'	=> 'selected'
					);
				}
				else
				{
					$user_list[] = array
					(
						'user_id'	=> $user['account_id'],
						'name'		=> $name
					);
				}
			}

			if(isset($user_list) && is_array($user_list))
			{
				return $user_list;
			}
		}

		function initiate_ui_vendorlookup($data)
		{
//_debug_array($data);

			if( isset($data['type']) && $data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('vendor_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('vendor_form'));
			}

			$vendor['value_vendor_id']		= $data['vendor_id'];
			$vendor['value_vendor_name']		= $data['vendor_name'];

			if(isset($data['vendor_id']) && $data['vendor_id'] && !$data['vendor_name'])
			{
				$contacts	= CreateObject('property.soactor');
				$contacts->role='vendor';
				$custom 		= createObject('phpgwapi.custom_fields');
				$vendor_data['attributes'] = $custom->get_attribs('property','.vendor', 0, '', 'ASC', 'attrib_sort', true, true);

				$vendor_data	= $contacts->read_single($data['vendor_id'],$vendor_data);
				if(is_array($vendor_data))
				{
					foreach($vendor_data['attributes'] as $attribute)
					{
						if($attribute['name']=='org_name')
						{
							$vendor['value_vendor_name']=$attribute['value'];
							break;
						}
					}
				}
				unset($contacts);
			}

			$vendor['vendor_link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.vendor'));
			$vendor['lang_vendor']			= lang('Vendor');
			$vendor['lang_select_vendor_help']	= lang('Klick this link to select vendor');
			$vendor['lang_vendor_name']		= lang('Vendor Name');

//_debug_array($vendor);
			return $vendor;
		}

		function initiate_ui_tenant_lookup($data)
		{
			if($data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('tenant_form'));
			}

			$tenant['value_tenant_id']			= $data['tenant_id'];
			$tenant['value_first_name']			= $data['first_name'];
			$tenant['value_last_name']			= $data['last_name'];
			$tenant['tenant_link']				= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.tenant'));
			if($data['role']=='customer')
			{
				$tenant['lang_select_tenant_help']		= lang('Klick this link to select customer');
				$tenant['lang_tenant']				= lang('Customer');

			}
			else
			{
				$tenant['lang_select_tenant_help']		= lang('Klick this link to select tenant');
				$tenant['lang_tenant']				= lang('Tenant');
			}


			if($data['tenant_id'] && !$data['tenant_name'])
			{
				$tenant_object	= CreateObject('property.soactor');
				$tenant_object->role = 'tenant';
				$custom 		= createObject('phpgwapi.custom_fields');
				$tenant_data['attributes'] = $custom->get_attribs('property','.tenant', 0, '', 'ASC', 'attrib_sort', true, true);
				$tenant_data	= $tenant_object->read_single($data['tenant_id'],$tenant_data);
				if(is_array($tenant_data['attributes']))
				{
//_debug_array($tenant_data);
					foreach ($tenant_data['attributes'] as $entry)
					{

						if ($entry['name'] == 'first_name')
						{
							$tenant['value_first_name']	= $entry['value'];
						}
						if ($entry['name'] == 'last_name')
						{
							$tenant['value_last_name']	= $entry['value'];
						}
					}
				}
			}

//_debug_array($tenant);
			return $tenant;
		}

		function initiate_ui_budget_account_lookup($data)
		{
			if( isset($data['type']) && $data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('b_account_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('b_account_form'));
			}

			$b_account['value_b_account_id']		= $data['b_account_id'];
			$b_account['value_b_account_name']		= $data['b_account_name'];
			$b_account['b_account_link']			= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uilookup.b_account'));
			$b_account['lang_select_b_account_help']	= lang('Klick this link to select budget account');
			$b_account['lang_b_account']			= lang('Budget account');
			if($data['b_account_id'] && !$data['b_account_name'])
			{
				$b_account_object	= CreateObject('property.sob_account');
				$b_account_data	= $b_account_object->read_single($data['b_account_id']);
				$b_account['value_b_account_name']	= $b_account_data['descr'];
			}

//_debug_array($b_account);
			return $b_account;
		}


		function initiate_ui_alarm($data)
		{
			$boalarm		= CreateObject('property.boalarm');

			if($data['type']=='view')
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('alarm_view'));
			}
			else
			{
				$GLOBALS['phpgw']->xslttpl->add_file(array('alarm_form'));
			}

			$alarm['header'][] = array
			(
				'lang_time'		=> lang('Time'),
				'lang_text'	=> lang('Text'),
				'lang_user'			=> lang('User'),
				'lang_enabled'		=> lang('Enabled'),
				'lang_select'		=> lang('Select')
				);

			$alarm['values'] = $boalarm->read_alarms($data['alarm_type'],$data['id'],$data['text']);
			if(!count($alarm['values'])>0)
			{
				unset($alarm['values']);
			}

			if($data['type']=='form')
			{
				$alarm['alter_alarm'][] = array
				(
					'lang_enable'		=> lang('Enable'),
					'lang_disable'		=> lang('Disable'),
					'lang_delete'		=> lang('Delete')
					);

				for ($i=1; $i<=31; $i++)
				{
					$alarm['add_alarm']['day_list'][($i-1)]['id'] = $i;
				}
				$alarm['add_alarm']['lang_day']					= lang('Day');
				$alarm['add_alarm']['lang_day_statustext']		= lang('Day');

				for ($i=1; $i<=24; $i++)
				{
					$alarm['add_alarm']['hour_list'][($i-1)]['id'] = $i;
				}
				$alarm['add_alarm']['lang_hour']					= lang('Hour');
				$alarm['add_alarm']['lang_hour_statustext']			= lang('Hour');

				for ($i=1; $i<=60; $i++)
				{
					$alarm['add_alarm']['minute_list'][($i-1)]['id'] = $i;
				}
				$alarm['add_alarm']['lang_minute']					= lang('Minutes before the event');
				$alarm['add_alarm']['lang_minute_statustext']		= lang('Minutes before the event');

				$alarm['add_alarm']['user_list'] = $this->get_user_list_right2('select',4,False,$data['acl_location'],False,$default=$this->account);

				$alarm['add_alarm']['lang_user']					= lang('User');
				$alarm['add_alarm']['lang_user_statustext']			= lang('Select the user the alarm belongs to.');
				$alarm['add_alarm']['lang_no_user']					= lang('No user');
				$alarm['add_alarm']['lang_add']						= lang('Add');
				$alarm['add_alarm']['lang_add_alarm']						= lang('Add alarm');
				$alarm['add_alarm']['lang_add_statustext']			= lang('Add alarm for selected user');

			}

//_debug_array($alarm['values']);
			return $alarm;
		}


		function select_multi_list_2($selected='',$list,$input_type='')
		{
			if (isset($list) AND is_array($list))
			{
				foreach($list as &$choice)
				{
					$choice['input_type'] = $input_type;
					if(isset($selected) && is_array($selected))
					{
						foreach ($selected as &$sel)
						{
							if($sel == $choice['id'])
							{
								$choice['checked'] = 'checked';
							}
						}
					}
				}
			}
			return $list;
		}

		function translate_datatype($datatype)
		{
			$datatype_text = array(
				'V' => 'Varchar',
				'I' => 'Integer',
				'C' => 'char',
				'N' => 'Float',
				'D' => 'Date',
				'T' => 'Memo',
				'R' => 'Muliple radio',
				'CH' => 'Muliple checkbox',
				'LB' => 'Listbox',
				'AB' => 'Contact',
				'VENDOR' => 'Vendor',
				'email' => 'Email',
				'link' => 'Link',
				'pwd' => 'Password',
				'user' => 'phpgw user'
			);

			$datatype  = lang($datatype_text[$datatype]);

			return $datatype;
		}

		function translate_datatype_insert($datatype)
		{
			$datatype_text = array(
				'V' => 'varchar',
				'I' => 'int',
				'C' => 'char',
				'N' => 'decimal',
				'D' => 'timestamp',
				'T' => 'text',
				'R' => 'int',
				'CH' => 'text',
				'LB' => 'int',
				'AB' => 'int',
				'VENDOR' => 'int',
				'email' => 'varchar',
				'link' => 'varchar',
				'pwd' => 'varchar',
				'user' => 'int'
			);

			return $datatype_text[$datatype];
		}

		function translate_datatype_precision($datatype)
		{
			$datatype_precision = array(
				'I' => 4,
				'R' => 4,
				'LB' => 4,
				'AB' => 4,
				'VENDOR' => 4,
				'email' => 64,
				'link' => 255,
				'pwd' => 32,
				'user' => 4
			);

			return (isset($datatype_precision[$datatype])?$datatype_precision[$datatype]:'');
		}

		function save_attributes($values_attribute,$type)
		{

			for ($i=0;$i<count($values_attribute);$i++)
			{
				if($values_attribute[$i]['datatype']=='CH' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = serialize($values_attribute[$i]['value']);
				}
				if($values_attribute[$i]['datatype']=='R' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = $values_attribute[$i]['value'][0];
				}

				if($values_attribute[$i]['datatype']=='N' && $values_attribute[$i]['value'])
				{
					$values_attribute[$i]['value'] = str_replace(",",".",$values_attribute[$i]['value']);
				}

				if($values_attribute[$i]['datatype']=='D' && $values_attribute[$i]['value'])
				{
					$dateformat= $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
					$dateformat = str_replace(".","",$dateformat);
					$dateformat = str_replace("-","",$dateformat);
					$dateformat = str_replace("/","",$dateformat);
					$y=strpos($dateformat,'Y');
					$d=strpos($dateformat,'d');
					$m=strpos($dateformat,'m');

			 		$dateparts = explode('/', $values_attribute[$i]['value']);
			 		$day		= $dateparts[$d];
			 		$month		= $dateparts[$m];
			 		$year		= $dateparts[$y];
					$values_attribute[$i]['value'] = date($this->dateformat,mktime(2,0,0,$month,$day,$year));
				}
			}

			$this->socommon->save_attributes($values_attribute,$type);
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
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'read' => array(
							'function'  => 'read',
							'signature' => array(array(xmlrpcInt,xmlrpcStruct)),
							'docstring' => lang('Read a single entry by passing the id and fieldlist.')
						),
						'save' => array(
							'function'  => 'save',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Update a single entry by passing the fields.')
						),
						'delete' => array(
							'function'  => 'delete',
							'signature' => array(array(xmlrpcBoolean,xmlrpcInt)),
							'docstring' => lang('Delete a single entry by passing the id.')
						),
						'list' => array(
							'function'  => '_list',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Read a list of entries.')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
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

		function add_leading_zero($num,$id_type='')
		{
			if ($id_type == "hex")
			{
				$num = hexdec($num);
				$num++;
				$num = dechex($num);
			}
			else
			{
				$num++;
			}

			if (strlen($num) == 4)
				$return = $num;
			if (strlen($num) == 3)
				$return = "0$num";
			if (strlen($num) == 2)
				$return = "00$num";
			if (strlen($num) == 1)
				$return = "000$num";
			if (strlen($num) == 0)
				$return = "0001";

			return strtoupper($return);
		}


		function read_location_data($location_code)
		{
			$soadmin_location	= CreateObject('property.soadmin_location');

			$location_types	= $soadmin_location->select_location_type();
			unset($soadmin_location);

			return $this->socommon->read_location_data($location_code,$location_types);
		}

		function read_single_tenant($tenant_id)
		{
			return $this->socommon->read_single_tenant($tenant_id);
		}

		function check_location($location_code='',$type_id='')
		{
			return $this->socommon->check_location($location_code,$type_id);
		}

		function generate_sql($data)
		{
//_debug_array($data);

			$cols 				= (isset($data['cols'])?$data['cols']:'');
			$entity_table 		= (isset($data['entity_table'])?$data['entity_table']:'');
			$cols_return 		= (isset($data['cols_return'])?$data['cols_return']:'');
			$uicols 			= (isset($data['uicols'])?$data['uicols']:'');
			$joinmethod 		= (isset($data['joinmethod'])?$data['joinmethod']:'');
			$paranthesis 		= (isset($data['paranthesis'])?$data['paranthesis']:'');
			$lookup 			= (isset($data['lookup'])?$data['lookup']:'');
			$location_level 	= (isset($data['location_level'])?$data['location_level']:'');
			$no_address 		= (isset($data['no_address'])?$data['no_address']:'');
			$force_location		= (isset($data['force_location'])?$data['force_location']:'');
			$cols_extra 		= array();
			$cols_return_lookup	= array();

			$soadmin_location	= CreateObject('property.soadmin_location');
			$location_types	= $soadmin_location->select_location_type();
			$config = $soadmin_location->read_config('');

			if($location_level || $force_location)
			{

				if($location_level)
				{
					$type_id = $location_level;
				}
				else
				{
					$type_id	= count($location_types);
				}

				$this->join = $this->socommon->join;
				$joinmethod .= " $this->join  fm_location1 ON ($entity_table.loc1 = fm_location1.loc1))";
				$paranthesis .='(';
				$joinmethod .= " $this->join  fm_part_of_town ON (fm_location1.part_of_town_id = fm_part_of_town.part_of_town_id))";
				$paranthesis .='(';
				$joinmethod .= " $this->join  fm_owner ON (fm_location1.owner_id = fm_owner.id))";
				$paranthesis .='(';
			}
			else
			{
				$type_id	= 0;//count($location_types);
				$no_address	= True;
			}
			$this->type_id	= $type_id;

			for ($i=0; $i<$type_id; $i++)
			{
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'loc' . $location_types[$i]['id'];
				$uicols['descr'][]		= $location_types[$i]['name'];
				$uicols['statustext'][]		= $location_types[$i]['descr'];
			}
/*
			$fm_location_cols = $soadmin_location->read_attrib(array('type_id'=>$type_id,'lookup_type'=>$type_id));
			$location_cols_count	= count($fm_location_cols);

			for ($i=0;$i<$location_cols_count;$i++)
			{
				if($fm_location_cols[$i]['list']==1)
				{
					$cols_extra[] 				= $fm_location_cols[$i]['column_name']; // only for lookup
					$cols_return[] 				= $fm_location_cols[$i]['column_name'];
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= $fm_location_cols[$i]['column_name'];
					$uicols['descr'][]			= $fm_location_cols[$i]['input_text'];
					$uicols['statustext'][]		= $fm_location_cols[$i]['statustext'];
				}
			}

*/
			unset($soadmin_location);

			for ($i=0; $i< $this->type_id; $i++)
			{
				$cols_return[] = 'loc' . $location_types[$i]['id'];
			}

			if($lookup)
			{
				$cols_return[] 				= 'loc1_name';
				$cols_extra[] 				= 'loc1_name';
				$uicols['input_type'][]			= 'text';
				$uicols['name'][]			= 'loc1_name';
				$uicols['descr'][]			= lang('Property Name');
				$uicols['statustext'][]			= lang('Property Name');

				for ($i=2;$i<($type_id+1);$i++)
				{
					$cols_return_lookup[] 		= 'loc' . $i . '_name';
					$uicols['input_type'][]		= 'hidden';
					$uicols['name'][]		= 'loc' . $i . '_name';
					$uicols['descr'][]		= '';
					$uicols['statustext'][]		= '';
				}
			}

			if(!$no_address)
			{
				$cols.= ",$entity_table.address";
				$cols_return[] 				= 'address';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'address';
				$uicols['descr'][]			= lang('address');
				$uicols['statustext'][]		= lang('address');
			}

			$config_count	= count($config);
			for ($i=0;$i<$config_count;$i++)
			{

				if (($config[$i]['location_type'] <= $type_id) && ($config[$i]['query_value'] ==1))
				{

					if($config[$i]['column_name']=='street_id')
					{

						$cols_return[] 				= 'street_name';
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]			= 'street_name';
						$uicols['descr'][]			= lang('street name');
						$uicols['statustext'][]		= lang('street name');

						$cols_return[] 				= 'street_number';
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]			= 'street_number';
						$uicols['descr'][]			= lang('street number');
						$uicols['statustext'][]		= lang('street number');

						$cols_return[] 				= $config[$i]['column_name'];
						$uicols['input_type'][]		= 'hidden';
						$uicols['name'][]			= $config[$i]['column_name'];
						$uicols['descr'][]			= lang($config[$i]['input_text']);
						$uicols['statustext'][]		= lang($config[$i]['input_text']);
						if($lookup)
						{
							$cols_extra[] 			= 'street_name';
							$cols_extra[] 			= 'street_number';
							$cols_extra[] 			= $config[$i]['column_name'];
						}

					}
					else
					{
						$cols_return[] 				= $config[$i]['column_name'];
						$uicols['input_type'][]		= 'text';
						$uicols['name'][]			= $config[$i]['column_name'];
						$uicols['descr'][]			= $config[$i]['input_text'];
						$uicols['statustext'][]		= $config[$i]['input_text'];

						if($lookup)
						{
							$cols_extra[] 		= $config[$i]['column_name'];
						}
					}
				}
			}

			$this->uicols 			= $uicols;
			$this->cols_return		= $cols_return;
			$this->cols_extra		= $cols_extra;
			$this->cols_return_lookup	= $cols_return_lookup;

			$from = " FROM $paranthesis $entity_table ";

			$sql = "SELECT $cols $from $joinmethod";

			return $sql;

		}

		function select_part_of_town($format='',$selected='',$district_id='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_part_of_town'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_part_of_town'));
					break;
			}

			$parts= $this->socommon->select_part_of_town($district_id);

			foreach($parts as $entry)
			{
				if ($entry['id']==$selected)
				{
					$part_of_town_list[] = array
					(
						'id'			=> $entry['id'],
						'name'			=> $entry['name'],
						'district_id'	=> $entry['district_id'],
						'selected'		=> 'selected'
					);
				}
				else
				{
					$part_of_town_list[] = array
					(
						'id'			=> $entry['id'],
						'name'			=> $entry['name'],
						'district_id'	=> $entry['district_id'],
					);
				}
			}
			return $part_of_town_list;
		}

		function select_district_list($format='',$selected='')
		{
			switch($format)
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('select_district'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('filter_district'));
					break;
			}

			$districts= $this->socommon->select_district_list();

			return $this->select_list($selected,$districts);
		}


		function select_category_list($data)
		{
			switch($data['format'])
			{
				case 'select':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_select'));
					break;
				case 'filter':
					$GLOBALS['phpgw']->xslttpl->add_file(array('cat_filter'));
					break;
			}

			$socategory = CreateObject('property.socategory');

			$categories= $socategory->select_category_list(array('type'=>$data['type'],
										'type_id'=>(isset($data['type_id'])?$data['type_id']:''),
										'order'	=>$data['order']));

			return $this->select_list($data['selected'],$categories);
		}


		function validate_db_insert($values)
		{
			foreach($values as $value)
			{
				if($value || $value === 0)
				{
					$insert_value[]	= "'".$value."'";
				}
				else
				{
					$insert_value[]	= 'NULL';
				}
			}

			$values	= implode(",", $insert_value);
			return $values;
		}

		function validate_db_update($value_set)
		{
			while (is_array($value_set) && list($field,$value) = each($value_set))
			{
				if($value || $value === 0)
				{
					$value_entry[]= "$field='$value'";
				}
				else
				{
					$value_entry[]= "$field=NULL";
				}
			}

			$value_set	= implode(",", $value_entry);
			return $value_set;
		}

		function fm_cache($name='',$value='')
		{
			return $this->socommon->fm_cache($name,$value);
		}

		/**
		* Clear all content from cache
		*
		*/

		function reset_fm_cache()
		{
			$this->socommon->reset_fm_cache();
		}

		/**
		* Clear computed userlist for location and rights from cache
		*
		* @return integer number of values was found and cleared
		*/

		function reset_fm_cache_userlist()
		{
			return $this->socommon->reset_fm_cache_userlist();
		}

		function next_id($table,$key='')
		{
			return $this->socommon->next_id($table,$key);
		}

		function select_datatype($selected='', $sub_module = '')
		{
			$datatypes[0]['id']= 'V';
			$datatypes[0]['name']= lang('varchar');
			$datatypes[1]['id']= 'C';
			$datatypes[1]['name']= lang('Character');
			$datatypes[2]['id']= 'I';
			$datatypes[2]['name']= lang('Integer');
			$datatypes[3]['id']= 'N';
			$datatypes[3]['name']= lang('Decimal');
			$datatypes[4]['id']= 'D';
			$datatypes[4]['name']= lang('Date');
			$datatypes[5]['id']= 'T';
			$datatypes[5]['name']= lang('Memo');
			$datatypes[6]['id']= 'R';
			$datatypes[6]['name']= lang('Multiple radio');
			$datatypes[7]['id']= 'CH';
			$datatypes[7]['name']= lang('Multiple Checkbox');
			$datatypes[8]['id']= 'LB';
			$datatypes[8]['name']= lang('ListBox');
			$datatypes[9]['id']= 'AB';
			$datatypes[9]['name']= lang('Contact');
			$datatypes[10]['id']= 'VENDOR';
			$datatypes[10]['name']= lang('Vendor');
			$datatypes[11]['id']= 'email';
			$datatypes[11]['name']= lang('Email');
			$datatypes[12]['id']= 'link';
			$datatypes[12]['name']= lang('Link');

			if($sub_module == 'actor')
			{
				$datatypes[13]['id']= 'pwd';
				$datatypes[13]['name']= lang('Password');
				$datatypes[14]['id']= 'user';
				$datatypes[14]['name']= lang('phpgw user');
			}

			return $this->select_list($selected,$datatypes);

		}

		function select_nullable($selected='')
		{
			$nullable[0]['id']= 'True';
			$nullable[0]['name']= lang('True');
			$nullable[1]['id']= 'False';
			$nullable[1]['name']= lang('False');

			return $this->select_list($selected,$nullable);
		}

		/**
		* Choose which  download format to use - and call the appropriate function
		*
		* @param array $list array with data to export
		* @param array $name array containing keys in $list
		* @param array $descr array containing Names for the heading of the output for the coresponding keys in $list
		* @param array $input_type array containing information whether fields are to be suppressed from the output
		*/
		function download($list,$name,$descr,$input_type='')
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = False;

			$export_format = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['export_format']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['export_format'] : 'csv';

			switch ($export_format)
			{
				case 'csv':
					$this->csv_out($list,$name,$descr,$input_type);
					break;
				case 'excel':
					$this->excel_out($list,$name,$descr,$input_type);
					break;
			}
		}

		/**
		* downloads data as MsExcel to the browser
		*
		* @param array $list array with data to export
		* @param array $name array containing keys in $list
		* @param array $descr array containing Names for the heading of the output for the coresponding keys in $list
		* @param array $input_type array containing information whether fields are to be suppressed from the output
		*/
		function excel_out($list,$name,$descr,$input_type='')
		{
 			$filename= str_replace(' ','_',$GLOBALS['phpgw_info']['user']['account_lid']).'.xls';

			$workbook	= CreateObject('phpgwapi.excel',"-");
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename,'application/vnd.ms-excel');

			$count_uicols_name=count($name);

			$worksheet1 =& $workbook->add_worksheet('First One');

			$m=0;
			for ($k=0;$k<$count_uicols_name;$k++)
			{
				if($input_type[$k]!='hidden')
				{
					$worksheet1->write_string(0, $m, $this->utf2ascii($descr[$k]));
					$m++;
				}
			}

			$j=0;
			if (isset($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					$m=0;
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($input_type[$k]!='hidden')
						{
							$content[$j][$m]	= str_replace("\r\n"," ",$entry[$name[$k]]);
							$m++;
						}
					}
					$j++;
				}

				$line = 0;
				foreach($content as $row)
				{
					$line++;
					for ($i=0; $i<count($row); $i++)
					{
						$worksheet1->write($line,$i, $this->utf2ascii($row[$i]));
					}
				}
			}
			$workbook->close();
		}

		/**
		* downloads data as CSV to the browser
		*
		* @param array $list array with data to export
		* @param array $name array containing keys in $list
		* @param array $descr array containing Names for the heading of the output for the coresponding keys in $list
		* @param array $input_type array containing information whether fields are to be suppressed from the output
		*/
		function csv_out($list, $name, $descr, $input_type = array() )
		{
			$filename= str_replace(' ','_',$GLOBALS['phpgw_info']['user']['account_lid']).'.csv';
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename, 'application/csv');

 			if ( !$fp = fopen('php://output','w') )
 			{
  				die('Unable to write to "php://output" - pleace notify the Administrator');
 			}

			$count_uicols_name=count($name);

			$header = array();
			for ( $i = 0; $i < $count_uicols_name; ++$i )
			{
				if ( $input_type[$i] == 'hidden' )
				{
					continue;
				}
				$header[] = $this->utf2ascii($descr[$i]);
			}
			fputcsv($fp, $header);
			unset($header);

			if ( is_array($list) )
			{
				foreach ( $list as $entry )
				{
					$row = array();
					for ( $i = 0; $i < $count_uicols_name; ++$i )
					{
						if ( $input_type[$i] == 'hidden' )
						{
							continue;
						}
						$row[] = preg_replace("/\r\n/", ' ', $entry[$name[$i]]);
					}
					fputcsv($fp, $row);
				}
			}
			fclose($fp);
		}

		function increment_id($name)
		{
			return $this->socommon->increment_id($name);
		}

		function get_origin_link($type)
		{
			if($type=='tts'):
			{
				$link = array('menuaction' => 'property.uitts.view');
			}
			elseif($type=='request'):
			{
				$link = array('menuaction' => 'property.uirequest.view');
			}
			elseif($type=='project'):
			{
				$link = array('menuaction' => 'property.uiproject.view');
			}
			elseif(substr($type,0,6)=='entity'):
			{
				$type		= explode("_",$type);
				$entity_id	= $type[1];
				$cat_id		= $type[2];
				$link =	array
				(
					'menuaction'	=> 'property.uientity.view',
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id
				);
			}
			endif;

			return (isset($link)?$link:'');
		}

		function new_db()
		{
			return $this->socommon->new_db();
		}

		function get_max_location_level()
		{
			return $this->socommon->get_max_location_level();
		}

		function active_group_members($group_id)
		{
			return $this->socommon->active_group_members($group_id);
		}

		/**
		* Preserve attribute values from post in case of an error
		*
		* @param array $values_attribute attribute definition and values from posting
		* @param array $values value set with
		* @return array Array with attribute definition and values
		*/
		function preserve_attribute_values($values,$values_attribute)
		{
			foreach ( $values_attribute as $key => $attribute )
			{
				for ($i=0;$i<count($values['attributes']);$i++)
				{
					if($values['attributes'][$i]['id'] == $attribute['attrib_id'])
					{
						if(isset($attribute['value']))
						{
							if(is_array($attribute['value']))
							{
								foreach($values['attributes'][$i]['choice'] as &$choice)
								{
									foreach ($attribute['value'] as &$selected)
									{
										if($selected == $choice['id'])
										{
											$choice['checked'] = 'checked';
										}
									}
								}
							}
							else if(isset($values['attributes'][$i]['choice']) && is_array($values['attributes'][$i]['choice']))
							{

								foreach ($values['attributes'][$i]['choice'] as &$choice)
								{
									if($choice['id'] == $attribute['value'])
									{
										$choice['checked'] = 'checked';
									}
								}
							}
							else
							{
								$values['attributes'][$i]['value'] = $attribute['value'];
							}
						}
					}
				}
			}
			return $values;
		}

		/**
		* Converts utf-8 to ascii
		*
		* @param string $text string
		* @return string ascii encoded
		*/
		function utf2ascii($text = '')
		{
			if(!isset($GLOBALS['phpgw_info']['server']['charset']) || $GLOBALS['phpgw_info']['server']['charset']=='utf-8')
			{
				if ($text == utf8_decode($text))
				{
					return $text;
				}
				else
				{
					return utf8_decode($text);
				}
			}
			else
			{
				return $text;
			}
		}

		/**
		* Converts ascii to utf-8
		*
		* @param string $text string
		* @return string utf-8 encoded
		*/
		function ascii2utf($text = '')
		{
			if(!isset($GLOBALS['phpgw_info']['server']['charset']) || $GLOBALS['phpgw_info']['server']['charset']=='utf-8')
			{
				return utf8_encode($text);
			}
			else
			{
				return $text;
			}
		}

		/**
		* Collects locationdata from location form and appends to values
		*
		* @param array $values array with data fom post
		* @param array $insert_record array containing fields to collect from post
		* @return updated values
		*/
		function collect_locationdata($values = '',$insert_record = '')
		{
			if($insert_record)
			{
				for ($i=0; $i<count($insert_record['location']); $i++)
				{
					if(isset($_POST[$insert_record['location'][$i]]) && $_POST[$insert_record['location'][$i]])
					{
						$values['location'][$insert_record['location'][$i]]= phpgw::get_var($insert_record['location'][$i], 'string', 'POST');
					}
				}

				if(isset($insert_record['extra']) && is_array($insert_record['extra']))
				{
					foreach ($insert_record['extra'] as $key => $column)
					{
						if(isset($_POST[$key]) && $_POST[$key])
						{
							$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
						}
					}
				}
			}

			$values['street_name'] 		= phpgw::get_var('street_name');
			$values['street_number']	= phpgw::get_var('street_number');
			if(isset($values['location']) && is_array($values['location']))
			{
				$values['location_name']	= phpgw::get_var('loc' . (count($values['location'])).'_name', 'string', 'POST'); // if not address - get the parent name as address
			}
			return $values;
		}

		function get_menu()
		{
			if(!isset($GLOBALS['phpgw_info']['user']['preferences']['property']['horisontal_menus']) || $GLOBALS['phpgw_info']['user']['preferences']['property']['horisontal_menus'] == 'no')
			{
				return;
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('menu'));

			if(!$menu = $GLOBALS['phpgw']->session->appsession($GLOBALS['phpgw_info']['flags']['menu_selection'], 'menu'))
			{
				$menu_gross = execMethod('property.menu.get_menu');
				$selection = explode('::',$GLOBALS['phpgw_info']['flags']['menu_selection']);
				$level=0;
				$menu['navigation'] = $this->get_sub_menu($menu_gross['navigation'],$selection,$level);
				$GLOBALS['phpgw']->session->appsession(isset($GLOBALS['phpgw_info']['flags']['menu_selection']) && $GLOBALS['phpgw_info']['flags']['menu_selection'] ? $GLOBALS['phpgw_info']['flags']['menu_selection'] : 'property_missing_selection', 'menu', $menu);
				unset($menu_gross);
			}
			return $menu;
		}

		function get_sub_menu($children = array(), $selection=array(),$level='')
		{
			$level++;
			$i=0;
			foreach($children as $key => $vals)
			{
				$menu[] = $vals;
				if($key == $selection[$level])
				{
					$menu[$i]['this'] = true;
					if(isset($menu[$i]['children']))
					{
						$menu[$i]['children'] = $this->get_sub_menu($menu[$i]['children'],$selection,$level);
					}
				}
				else
				{
					if(isset($menu[$i]['children']))
					{
						unset($menu[$i]['children']);
					}
				}
				$i++;
			}
			return $menu;
		}
	}
?>
