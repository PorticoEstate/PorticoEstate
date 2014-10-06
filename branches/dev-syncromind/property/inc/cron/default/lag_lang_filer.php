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
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class lag_lang_filer
	{
		var	$function_name = 'lag_lang_filer';

		function lag_lang_filer()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db 		= $this->bocommon->new_db();
		}

		function pre_run($data='')
		{
			if($data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
			}
			if ($confirm)
			{
				$this->execute($cron);
			}
			else
			{
				$this->confirm($execute=false);
			}
		}


		function confirm($execute='')
		{
			$link_data = array
			(
				'menuaction' => 'property.custom_functions.index',
				'function'	=>$this->function_name,
				'execute'	=> $execute,
			);

			if(!$execute)
			{
				$lang_confirm_msg 	= lang('do you want to perform this action');
			}
			$lang_yes			= lang('yes');
			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));
			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);
			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> 'lag_lang_filer fra database',
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= 'lag_lang_filer';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{

			$sql = "SELECT * from phpgw_lang WHERE app_name = 'property' AND lang='no' ORDER BY message_id ASC";

			$this->db->query($sql,__LINE__,__FILE__);

			$i=0;
			while ($this->db->next_record())
			{
					$str.=$this->db->f('message_id') ."\t";
					$str.=$this->db->f('app_name') ."\t";
					$str.=$this->db->f('lang') ."\t";
					$str.=$this->db->f('content') ."\n";
					$i++;
			}

_debug_array($str);
/*			   $filename= 'phpgw_no_lang';

				$size=strlen($str);

				$browser = CreateObject('phpgwapi.browser');
				$browser->content_header($filename,'application/txt',$size);

				echo $str;
*/

			$this->receipt['message'][]=array('msg'=> $i . ' tekster lagt til');

			if(!$cron)
			{
				$this->confirm($execute=false);
			}
		}
	}

