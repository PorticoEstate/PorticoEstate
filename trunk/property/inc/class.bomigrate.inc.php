<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id: class.uimigrate.inc.php 732 2008-02-10 16:21:14Z sigurd $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Description
	 * @package property
	 */

	class property_bomigrate
	{
		private $use_session;
		public $start;

		public function __construct($session = false)
		{
			$this->acl_location 	= '.admin';

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$start		= phpgw::get_var('start', 'int', 'REQUEST', 0);

		}

		public function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','migrate', $data);
			}
		}

		private function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data', 'migrate');

			//_debug_array($data);
		}

		public function get_acl_location()
		{
			return $this->acl_location;
		}

		public function read()
		{
			$domain_info = $GLOBALS['phpgw_domain'];
			unset($domain_info[$GLOBALS['phpgw_info']['user']['domain']]);

			return $domain_info;
		}

		public function migrate($values,$download_script=true)
		{
//			_debug_array($values);
			$oProc							= createObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$oProc->m_odb					= $GLOBALS['phpgw']->db;
			$oProc->m_odb->Halt_On_Error	= 'yes';
			$GLOBALS['phpgw_setup']->oProc	= $oProc;

			$tables = $GLOBALS['phpgw']->db->table_names();
			
			/* Work out the order of how the tables can be created
			*/
			foreach($tables as $tablename)
			{
				$ForeignKeys = $GLOBALS['phpgw']->db->MetaForeignKeys($tablename);
				foreach($ForeignKeys as $table => $keys)
				{
				}
			}

			$setup = createObject('phpgwapi.setup_process');

			$table_def = array();
			foreach($tables as $table)
			{
				$tableinfo = $setup->sql_to_array($table);
				$fd_temp = '$fd = array(' . str_replace("\t",'',$tableinfo[0]) .');';
				@eval($fd_temp);
				$table_def[$table]['fd'] = $fd;
				$table_def[$table]['pk'] = $tableinfo[1];
				$table_def[$table]['fk'] = $tableinfo[2];		
				$table_def[$table]['ix'] = $tableinfo[3];
				$table_def[$table]['uc'] = $tableinfo[4];
			}
//_debug_array($table_def);
//_debug_array($tables);

			foreach ($values as $domain)
			{
				$this->oProc = createObject('phpgwapi.schema_proc',$GLOBALS['phpgw_domain'][$domain]['db_type']);
				if(!$download_script)
				{
					$this->oProc->m_odb           = $GLOBALS['phpgw']->db;
					$this->oProc->m_odb->Host     = $GLOBALS['phpgw_domain'][$domain]['db_host'];
					$this->oProc->m_odb->Database = $GLOBALS['phpgw_domain'][$domain]['db_name'];
					$this->oProc->m_odb->User     = $GLOBALS['phpgw_domain'][$domain]['db_user'];
					$this->oProc->m_odb->Password = $GLOBALS['phpgw_domain'][$domain]['db_pass'];
					$this->oProc->m_odb->Halt_On_Error = 'yes';
					$this->oProc->m_odb->connect();
				}

				$filename = $domain . '_' . $GLOBALS['phpgw_domain'][$domain]['db_name'] . '_' . $GLOBALS['phpgw_domain'][$domain]['db_type'] . '.sql';

				$script = $this->oProc->GenerateScripts($table_def, false, true);
				if($download_script)
				{
					$this->download_script($script, $filename);
				}
			}
		}

		private function download_script($script, $filename)
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = True;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = True;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = False;

			$browser = CreateObject('phpgwapi.browser');
			$size=strlen($script);
			$browser->content_header($filename,'',$size);
			echo $script;
		}


	}
?>
