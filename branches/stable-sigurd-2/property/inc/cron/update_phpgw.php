<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage custom
 	* @version $Id$
	*/

	/**
	 * Description
	 * usage:
	 * @package property
	 */

	class update_phpgw
	{
		var	$function_name = 'update_phpgw';

		function update_phpgw()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db     			= & $GLOBALS['phpgw']->db;
		}

		function pre_run($data='')
		{
			if(isset($data['enabled']) && $data['enabled']==1)
			{
				$confirm	= true;
				$cron		= true;
			}
			else
			{
				$confirm	= phpgw::get_var('confirm', 'bool', 'POST');
				$execute	= phpgw::get_var('execute', 'bool', 'GET');
				$cron = false;
			}


			if (isset($confirm) && $confirm)
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
				'function'	=> $this->function_name,
				'execute'	=> $execute,
			);

			if(!$execute)
			{
				$lang_confirm_msg 	= lang('Do you want to execute this action?');
			}

			$lang_yes			= lang('yes');

			$GLOBALS['phpgw']->xslttpl->add_file(array('confirm_custom'));

			$msgbox_data = isset($this->receipt)?$this->bocommon->msgbox_data($this->receipt):'';

			$data = array
			(
				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php'),
				'run_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'message'				=> isset($this->receipt['message'])?$this->receipt['message']:'',
				'lang_confirm_msg'		=> $lang_confirm_msg,
				'lang_yes'				=> $lang_yes,
				'lang_yes_statustext'	=> lang('Update database for all applications'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> lang('cancel'),
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('Async service');
			$function_msg	= 'Forward email as SMS';
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{
			$this->perform_update_db();

			if(isset($this->receipt) && $this->receipt)
			{
				$this->cron_log($this->receipt,$cron);
			}

			if(!$cron)
			{
				$this->confirm($execute=false);
			}
		}

		function cron_log($receipt='',$cron='')
		{
			$insert_values= array(
				$cron,
				date($this->bocommon->datetimeformat),
				$this->function_name,
				$receipt
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function perform_update_db()
		{
			$GLOBALS['phpgw_setup'] = CreateObject('phpgwapi.setup', true, true);
			$setup_info = $GLOBALS['phpgw_setup']->detection->get_versions();
			$GLOBALS['phpgw_setup']->db = CreateObject('phpgwapi.db');
			$GLOBALS['phpgw_info']['setup']['stage']['db'] = $GLOBALS['phpgw_setup']->detection->check_db();
			$setup_info = $GLOBALS['phpgw_setup']->detection->get_db_versions($setup_info);
			$setup_info = $GLOBALS['phpgw_setup']->detection->compare_versions($setup_info);
			$setup_info = $GLOBALS['phpgw_setup']->detection->check_depends($setup_info);
			ksort($setup_info);
			$clear_cache = '';
			foreach($setup_info as $app => $appinfo)
			{
				if(isset($appinfo['status']) && $appinfo['status']=='U' && isset($appinfo['currentver']) && $appinfo['currentver'])
				{
					$terror = array();
					$terror[] = $setup_info[$appinfo['name']];
					$GLOBALS['phpgw_setup']->process->upgrade($terror,false);
					$GLOBALS['phpgw_setup']->process->upgrade_langs($terror,false);
					$this->receipt['message'][]=array('msg'=> 'Upgraded application: ' . $appinfo['name']);
					if($appinfo['name']=='property')
					{
						$clear_cache = true;
					}
				}
			}
			if($clear_cache)
			{
				$this->db->query('DELETE FROM fm_cache');
			}
		}
	}

