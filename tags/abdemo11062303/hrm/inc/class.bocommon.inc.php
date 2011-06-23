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
 	* @version $Id$
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
			'select_part_of_town'	=> true,
			'menu'	=> true,
		);


		function hrm_bocommon()
		{
			$this->socommon			= CreateObject('hrm.socommon');
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

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
					$msgbox_data_error += array($errors['msg']=> false);
				}
			}

			$msgbox_data_message=array();

			if (isSet($receipt['message']) AND is_array($receipt['message']))
			{
				foreach($receipt['message'] as $messages)
				{
					$msgbox_data_message += array($messages['msg']=> true);
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
				return false;
			}

			$date_array	= $this->date_array($date);
			$date	= mktime (8,0,0,$date_array['month'],$date_array['day'],$date_array['year']);

			return $date;
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

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('hrm') . ' - ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('no_access' => $data));
		}
	}
