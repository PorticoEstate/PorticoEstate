<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage core
 	* @version $Id: class.bocommon.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_bocommon
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
			'menu'	=> True,
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

		function hrm_bocommon()
		{
//			$GLOBALS['phpgw_info']['flags']['currentapp']	=	'hrm';
			$this->currentapp		= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->socommon			= CreateObject('hrm.socommon');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

		/*	if (!is_object($GLOBALS['phpgw']->asyncservice))
			{
				$GLOBALS['phpgw']->asyncservice = CreateObject('phpgwapi.asyncservice');
			}
			$this->async = &$GLOBALS['phpgw']->asyncservice;
		*/
			$this->join			= $this->socommon->join;
			$this->left_join	= $this->socommon->left_join;
			$this->like			= $this->socommon->like;

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
					break;
			}

		}
		
		//FIXME Remove the need for this - use the jscal class which now supports xslt
		function jscalendar()
		{
			if ( !isset($GLOBALS['phpgw']->jscal) || !is_object($GLOBALS['phpgw']->jscal) )
			{
				$GLOBALS['phpgw']->jscal = createObject('phpgwapi.jscalendar');
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

		function date_array($datestr)
		{
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

					for ($i=0;$i<count($selected);$i++)
					{
						if($selected[$i] == $entry['id'])
						{
							$output_list[$j]['selected'] = 'selected';
						}
					}
					$j++;
				}
			}

			for ($i=0;$i<count($output_list);$i++)
			{
				if ($output_list[$i]['selected'] != 'selected')
				{
					unset($output_list[$i]['selected']);
				}
			}

			return $output_list;
		}


		function select_list($selected='',$input_list='')
		{
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					$sel_entry = '';
					if ($entry['id']==$selected)
					{
						$sel_entry = 'selected';
					}
					$entry_list[] = array
					(
						'id'		=> $entry['id'],
						'name'		=> $entry['name'],
						'selected'	=> $sel_entry
					);
				}
				for ($i=0;$i<count($entry_list);$i++)
				{
					if ($entry_list[$i]['selected'] != 'selected')
					{
						unset($entry_list[$i]['selected']);
					}
				}
			}
			return $entry_list;
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
			if (is_array($users_extra) && is_array($users))
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


		function initiate_ui_alarm($data)
		{
			$boalarm		= CreateObject('hrm.boalarm');

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
					$alarm['add_alarm']['day_list'][($i-1)][id] = $i;
				}
				$alarm['add_alarm']['lang_day']					= lang('Day');
				$alarm['add_alarm']['lang_day_statustext']		= lang('Day');

				for ($i=1; $i<=24; $i++)
				{
					$alarm['add_alarm']['hour_list'][($i-1)][id] = $i;
				}
				$alarm['add_alarm']['lang_hour']					= lang('Hour');
				$alarm['add_alarm']['lang_hour_statustext']			= lang('Hour');

				for ($i=1; $i<=60; $i++)
				{
					$alarm['add_alarm']['minute_list'][($i-1)][id] = $i;
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


		function select_multi_list_2($selected='',$input_list,$input_type='')
		{
			$j=0;
			if (isset($input_list) AND is_array($input_list))
			{
				foreach($input_list as $entry)
				{
					$output_list[$j]['id'] = $entry['id'];
					$output_list[$j]['value'] = $entry['value'];
					$output_list[$j]['input_type'] = $input_type;

					for ($i=0;$i<count($selected);$i++)
					{
						if($selected[$i] == $entry['id'])
						{
							$output_list[$j]['checked'] = 'checked';
						}
					}
					$j++;
				}
			}

			for ($i=0;$i<count($output_list);$i++)
			{
				if ($output_list[$i]['checked'] != 'checked')
				{
					unset($output_list[$i]['checked']);
				}
			}

			return $output_list;
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

		function add_leading_zero($num)
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

		function next_id($table,$key='')
		{
			return $this->socommon->next_id($table,$key);
		}


		function excel($list,$name,$descr,$input_type='')
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = True;
			$GLOBALS['phpgw_info']['flags'][nofooter] = True;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = False;

 			$filename= $GLOBALS['phpgw_info']['user']['account_lid'].'.xls';

			$workbook	= CreateObject('hrm.excel',"-");
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header($filename,'application/vnd.ms-excel');

			$count_uicols_name=count($name);

			$worksheet1 =& $workbook->add_worksheet('First One');

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
							$worksheet1->write_string(0, $m, $descr[$k]);
							$m++;
						}
					}
					$j++;
				}

				foreach($content as $row)
				{
					$line++;
					for ($i=0; $i<count($row); $i++)
					{
						$worksheet1->write($line,$i,$row[$i]);
					}
				}
			}
			$workbook->close();
		}

		function no_access($links = '')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access','menu'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));

			$msgbox_data = $this->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'		=> $links,
			);

			$appname	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) . ' - ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('no_access' => $data));
		}

		function new_db()
		{
			if ( isset($GLOBALS['phpgw']->db) && is_object($GLOBALS['phpgw']->db) )
			{
				return clone($GLOBALS['phpgw']->db);
			}
			return $this->socommon->new_db();
		}
	}
?>
