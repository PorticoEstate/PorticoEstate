<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2013,2014 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @subpackage import
 	* @version $Id: class.cron_parent.inc.php 11695 2014-02-14 12:40:00Z sigurdne $
	*/

	/**
	 * Generic parent class for cron-jobs, with and without UI
	 * @package property
	 */


	abstract class  property_cron_parent
	{
		protected $function_name;
		protected $debug = false;
		protected $receipt = array();
		protected $sub_location = 'sub_location';
		protected $function_msg	= 'function_msg';
		protected $cron = false;

		function __construct()
		{
			$this->db			= & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}

		function pre_run($data = array())
		{
			if(isset($data['enabled']) && $data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
				$this->cron	= true;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
			}

			if( isset($data['debug']) && $data['debug'] )
			{
				$this->debug = true;
			}
			else
			{
				$this->debug	= phpgw::get_var('debug', 'bool');
			}

			if ($confirm)
			{
				$this->execute($data);
				$this->cron_log($cron);
				// initiated from ui
				if(!$cron)
				{
					$this->confirm($execute=false);
				}
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
				'menuaction'	=> 'property.custom_functions.index',
				'function'		=> $this->function_name,
				'execute'		=> $execute
			);


			if(!$execute)
			{
				$lang_confirm_msg 	= lang('do you want to perform this action');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->receipt);

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=>'property.uiasync.index')),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> $this->receipt['message'],
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> $this->function_msg,
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake',
				'debug'					=> $this->debug
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $this->sub_location . ': ' . $this->function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}


		abstract public function execute();


		private function cron_log($cron)
		{
			if(!$this->receipt)
			{
				return;
			}

			if($this->debug)
			{
				return;
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($this->receipt);

			$insert_values= array
			(
				$cron,
				date($this->db->datetime_format()),
				$this->function_name,
				$this->db->db_addslashes(implode('::',(array_keys($msgbox_data))))
			);

			$insert_values	= $this->db->validate_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}
	}
