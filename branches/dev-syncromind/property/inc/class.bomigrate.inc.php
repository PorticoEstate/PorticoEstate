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
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
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
				$this->use_session = true;
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

		public function migrate($values,$download_script=false)
		{
			//_debug_array($GLOBALS['phpgw_domain']);die();
//			_debug_array($values);
			$oProc							= createObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$oProc->m_odb					= $GLOBALS['phpgw']->db;
			$oProc->m_odb->Halt_On_Error	= 'yes';
			$GLOBALS['phpgw_setup']->oProc	= $oProc;

			$tables = $GLOBALS['phpgw']->db->table_names();

//			$tables = array('fm_entity_history');

			$setup = createObject('phpgwapi.setup_process');

			$table_def = array();
			foreach($tables as $table)
			{
				$tableinfo = $setup->sql_to_array($table);
				//_debug_array($tableinfo);

				$fd_temp = '$fd = array(' . str_replace("\t",'',$tableinfo[0]) .');';
				@eval($fd_temp);
				$table_def[$table]['fd'] = $fd;
				$table_def[$table]['pk'] = $tableinfo[1];
				$table_def[$table]['fk'] = $tableinfo[2];
				$table_def[$table]['ix'] = $tableinfo[3];
				$table_def[$table]['uc'] = $tableinfo[4];
				//_debug_array($table_def);				
				//die();
				/* Work out the order of how the tables can be created
				 */
				if($tableinfo[2])
				{
					foreach ($tableinfo[2] as $ref_set => $ref_fields)
					{
						$fk_temp = '$fk = array(' . $ref_fields .');';
						@eval($fk_temp);
						$fk_table = array_keys($fk);
						$ForeignKeys[$table][] = $fk_table[0];
					}
				}
			}

			set_time_limit(0);
			foreach ($values as $domain)
			{
				$this->oProc = createObject('phpgwapi.schema_proc',$GLOBALS['phpgw_domain'][$domain]['db_type']);
				if(!$download_script)
				{
					$this->oProc->m_odb           = CreateObject('phpgwapi.db');//$GLOBALS['phpgw']->db;
					$this->oProc->m_odb->Type     = $GLOBALS['phpgw_domain'][$domain]['db_type'];
					$this->oProc->m_odb->Host     = $GLOBALS['phpgw_domain'][$domain]['db_host'];
					$this->oProc->m_odb->Database = $GLOBALS['phpgw_domain'][$domain]['db_name'];
					$this->oProc->m_odb->User     = $GLOBALS['phpgw_domain'][$domain]['db_user'];
					$this->oProc->m_odb->Password = $GLOBALS['phpgw_domain'][$domain]['db_pass'];
					$this->oProc->m_odb->Halt_On_Error = 'yes';
					$this->oProc->m_odb->connect();
				}

				if($download_script)
				{
					$script = $this->GenerateScripts($table_def, false, true);
					$filename = $domain . '_' . $GLOBALS['phpgw_domain'][$domain]['db_name'] . '_' . $GLOBALS['phpgw_domain'][$domain]['db_type'] . '.sql';
					$this->download_script($script, $filename);
				}
				else
				{
					$this->oProc->ExecuteScripts($table_def, true);
					$this->copy_data($table_def);
				}
			}
		}


		function copy_data($table_def = array())
		{
			$db = $GLOBALS['phpgw']->db;

			foreach ($table_def as $table => $fd)
			{
				if($table=='fm_ecobilagoverf' || $table== 'phpgw_lang')
				{
					continue;
				}
				switch ($table)
				{
				case 'fm_document_history':
				case 'fm_entity_history':
				case 'fm_request_history':
				case 'fm_project_history':
				case 'fm_tts_history':
				case 'fm_s_agreement_history':
				case 'fm_workorder_history':
				case 'phpgw_history_log':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET history_new_value = 'NIL' WHERE history_new_value = ''", __LINE__ , __FILE__, true);
					break;
				case 'fm_project':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET name = 'NIL' WHERE name = ''", __LINE__ , __FILE__, true);
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET loc1 = '0000', location_code = '0000' WHERE loc1 = ''", __LINE__ , __FILE__, true);
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET status = 'closed' WHERE status = ''", __LINE__ , __FILE__, true);
					break;
				case 'fm_workorder':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET title = 'NIL' WHERE title = ''", __LINE__ , __FILE__, true);
					break;
				case 'fm_ecodimd':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET descr = 'NIL' WHERE descr = ''", __LINE__ , __FILE__, true);
					break;
				case 'phpgw_categories':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET cat_description = 'NIL' WHERE cat_description = ''", __LINE__ , __FILE__, true);
					break;
				case 'phpgw_contact_others':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET other_value = 'NIL' WHERE other_value = ''", __LINE__ , __FILE__, true);
					break;
				case 'phpgw_contact_person':
					$GLOBALS['phpgw']->db->query("DELETE FROM {$table} WHERE first_name = ''", __LINE__ , __FILE__, true);
					break;
				case 'phpgw_log_msg':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET log_msg_parms = 'NIL' WHERE log_msg_parms = ''", __LINE__ , __FILE__, true);
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET log_msg_file = 'NIL' WHERE log_msg_file = ''", __LINE__ , __FILE__, true);
					break;
				case 'phpgw_sms_tbluserphonebook':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET p_email = 'NIL' WHERE p_email = ''", __LINE__ , __FILE__, true);
					break;
				case 'phpgw_sms_tblsmsoutgoing':
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET p_footer = 'NIL' WHERE p_footer = ''", __LINE__ , __FILE__, true);
					$GLOBALS['phpgw']->db->query("UPDATE {$table} SET p_src = 'NIL' WHERE p_src = ''", __LINE__ , __FILE__, true);
					break;
				default:
				}

				$db->query("SELECT * FROM {$table}");
				foreach($db->resultSet as $row)
				{
					$insert_values = $db->validate_insert(array_values($row));
					$insert_fields = implode(',', array_keys($row));
					$this->oProc->m_odb->query("INSERT INTO {$table} ({$insert_fields}) VALUES ({$insert_values})");	
				}
			}
		}

		private function download_script($script, $filename)
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$browser = CreateObject('phpgwapi.browser');
			$size=strlen($script);
			$browser->content_header($filename,'',$size);
			echo $script;
		}


		/**
		 * Generate Script for db-schema
		 *
		 * @param array	$aTables 		array holding schema definition for the database
		 * @param bool	$bOutputHTML	print to browser - or not
		 * @param bool	$return_script  return sql-sqript - or not
		 *
		 * @return string sql-script for generate database for chosen db-platform.
		 */

		function GenerateScripts($aTables, $bOutputHTML=false, $return_script=false)
		{
			if (!is_array($aTables))
			{
				return false;
			}
			$this->oProc->m_aTables = $aTables;

			$sAllTableSQL = '';
			foreach ($this->oProc->m_aTables as $sTableName => $aTableDef)
			{
				$sSequenceSQL = '';
				$sTriggerSQL = '';
				$this->oProc->m_oTranslator->indexes_sql = array();
				if($this->oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL, $sTriggerSQL))
				{
					$sTableSQL = "CREATE TABLE $sTableName (\n$sTableSQL\n)"
						. $this->oProc->m_oTranslator->m_sStatementTerminator;
					if($sSequenceSQL != '')
					{
						$sAllTableSQL .= $sSequenceSQL . "\n";
					}

					if($sTriggerSQL != '')
					{
						$sAllTableSQL .= $sTriggerSQL . "\n";
					}

					$sAllTableSQL .= $sTableSQL . "\n\n";

					// postgres and mssql
					if(isset($this->oProc->m_oTranslator->indexes_sql) && is_array($this->oProc->m_oTranslator->indexes_sql) && count($this->oProc->m_oTranslator->indexes_sql)>0)
					{
						foreach($this->oProc->m_oTranslator->indexes_sql as $key => $sIndexSQL)
						{
							$ix_name = $key.'_'.$sTableName.'_idx';
							$IndexSQL = str_replace(array('__index_name__','__table_name__'), array($ix_name,$sTableName), $sIndexSQL);
							$sAllTableSQL .= $IndexSQL . "\n\n";
						}
					}
				}
				else
				{
					if($bOutputHTML)
					{
						print('<br>Failed generating script for <b>' . $sTableName . '</b><br>');
						echo '<pre style="text-align: left;">'.$sTableName.' = '; print_r($aTableDef); echo "</pre>\n";
					}

					return false;
				}
			}

			if($bOutputHTML)
			{
				print('<pre>' . $sAllTableSQL . '</pre><br><br>');
			}

			if($return_script)
			{
				return $sAllTableSQL;
			}
		}
	}
