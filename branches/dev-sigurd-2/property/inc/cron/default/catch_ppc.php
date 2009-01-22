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
	* @subpackage catch
 	* @version $Id: catch_ppc.php 1993 2008-12-25 12:54:58Z sigurd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class catch_ppc
	{
		var	$function_name = 'catch_ppc';

		public function __construct()
		{
			$this->bocommon			= CreateObject('property.bocommon');
			$this->soadmin_location	= CreateObject('property.soadmin_location');
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;

		//	$this->saveto			= '/mnt/filer2/VaktPC_filer';
		//	$this->saveto			= '/tmp';
		//	$this->export_method = 'csv';
		//	$this->export_method = 'excel';
 		//	$this->export_method = 'xml';
 		//	$this->dateformat = 'd/m/Y';
 			
 			$config = CreateObject('phpgwapi.config', 'catch');
 			$config->read();
 			$this->pickup_path = $config->config_data['pickup_path'];
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
				'lang_yes_statustext'	=> lang('Export info as files'),
				'lang_no_statustext'	=> 'tilbake',
				'lang_no'				=> lang('no'),
				'lang_done'				=> 'Avbryt',
				'lang_done_statustext'	=> 'tilbake'
			);

			$appname		= lang('location');
			$function_msg	= lang('Export info as files');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('confirm' => $data));
			$GLOBALS['phpgw']->xslttpl->pp();
		}

		function execute($cron='')
		{

			$this->import_ppc();

			if(!$cron)
			{
				$this->confirm($execute=false);
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$insert_values= array(
				$cron,
				date($this->bocommon->datetimeformat),
				$this->function_name,
				implode(',',(array_keys($msgbox_data)))
				);

			$insert_values	= $this->bocommon->validate_db_insert($insert_values);

			$sql = "INSERT INTO fm_cron_log (cron,cron_date,process,message) "
					. "VALUES ($insert_values)";
			$this->db->query($sql,__LINE__,__FILE__);
		}

		function import_ppc()
		{
			//do the actual import
			$xmlparse = CreateObject('property.XmlToArray');
			$xmlparse->setEncoding('UTF-8');

			$file_list = $this->get_files();

			foreach ($file_list as $file)
			{
				$var_result = $xmlparse->parseFile($file);
				_debug_array($var_result);
			}
		}

		public function get_files()
		{
			$dirname = $this->pickup_path;
			// prevent path traversal
			if ( preg_match('/\./', $dirname) 
			 || !is_dir($dirname) )
			{
				return array();
			}

			$file_list = array();
			$dir = new DirectoryIterator($dirname); 
			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						|| mime_content_type($file->getPathname()) != 'text/xml')
					{
						continue;
					}

					$file_list[] = (string) "{$dirname}/{$file}";
				}
			}

			return $file_list;
		}
	}
