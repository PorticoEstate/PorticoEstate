<?php
	/**
	* phpGroupWare - SMS
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_bocommon
	{
		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		function __construct()
		{

		}

		function check_perms($rights, $required)
		{
			return ($rights & $required);
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

		function no_access($message = '')
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));
			if($message)
			{
				$receipt['error'][] = array('msg'=>$message);
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'message'		=> $message,
			);

			$appname	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('no_access' => $data));
		}
	}
