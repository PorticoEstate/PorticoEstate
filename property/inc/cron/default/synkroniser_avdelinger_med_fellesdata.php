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
	 * @subpackage cron
	 * @version $Id$
	 */
	/**
	 * Description
	 * example cron : /usr/local/bin/php -q /var/www/html/phpgroupware/property/inc/cron/cron.php default synkroniser_avdelinger_med_fellesdata
	 * @package property
	 */
	include_class('property', 'cron_parent', 'inc/cron/');

	class synkroniser_avdelinger_med_fellesdata extends property_cron_parent
	{

		public function __construct()
		{
			parent::__construct();

			$this->function_name = get_class($this);
			$this->sub_location = lang('property');
			$this->function_msg = 'Synkroniser avdelinger med Fellesdata';
		}

		function execute()
		{
			$fellesdata = new property_fellesdata();

			$fellesdata->set_debug($this->debug);

			$fellesdata->update_dimb();
			$fellesdata->get_org_unit_ids_from_top();

			if ($this->debug)
			{
				_debug_array($fellesdata->unit_ids);
			}

			try
			{
				$fellesdata->insert_values();

				if (isset($GLOBALS['phpgw_info']['user']['apps']['rental']))
				{
					$this->update_rental_party();
				}
			}
			catch (Exception $e)
			{
				$this->receipt['error'][] = array('msg' => $e->getMessage());
			}

			$messages = $fellesdata->messages;
			foreach ($messages as $message)
			{
				$this->receipt['message'][] = array('msg' => $message);
			}
		}

		private function update_rental_party()
		{
			$sogeneric = CreateObject('property.sogeneric');
			$sql = "SELECT DISTINCT org_enhet_id FROM rental_party WHERE org_enhet_id IS NOT NULL";
			$this->db->query($sql, __LINE__, __FILE__);
			$parties = array();
			while ($this->db->next_record())
			{
				$parties[] = $this->db->f('org_enhet_id');
			}

			foreach ($parties as $party)
			{
				$sql = "SELECT name, parent_id FROM fm_org_unit WHERE id  = {$party}";
				$this->db->query($sql, __LINE__, __FILE__);
				if ($this->db->next_record())
				{
					$name = $this->db->f('name');
					$parent_id = $this->db->f('parent_id');
					$path = $sogeneric->get_path(array('type' => 'org_unit', 'id' => $parent_id));
					$parent_name = implode(' > ', $path);

					$value_set = array
						(
						'company_name' => $name,
						'department' => $this->db->db_addslashes($parent_name)
					);

					$value_set = $this->db->validate_update($value_set);
					$sql = "UPDATE rental_party SET {$value_set} WHERE org_enhet_id ={$party}";

					$this->db->query($sql, __LINE__, __FILE__);
					if ($this->debug)
					{
						$this->receipt['message'][] = array('msg' => $sql);
					}
				}
			}
		}
	}

	class property_fellesdata
	{

		// Instance variable
		protected static $bo;
		protected $connected = false;
		protected $status;
		protected $db = null;
		protected $unit_ids = array();
		protected $names = array();
		protected $messages = array();
		protected $debug = false;

		function __construct()
		{
			$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));

			if (!isset($this->config->config_data['fellesdata']) || !$this->config->config_data['fellesdata'])
			{
				$this->initiate_config();
			}
		}

		public function set_debug( $debug )
		{
			$this->debug = $debug;
		}

		private function initiate_config()
		{
			$receipt_section = $this->config->add_section(array
				(
				'name' => 'fellesdata',
				'descr' => 'Fellesdata'
				)
			);

			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name' => 'host',
				'descr' => 'Host'
				)
			);
			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name' => 'port',
				'descr' => 'Port'
				)
			);
			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name' => 'db_name',
				'descr' => 'Database'
				)
			);
			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'text',
				'name' => 'user',
				'descr' => 'User'
				)
			);
			$receipt = $this->config->add_attrib(array
				(
				'section_id' => $receipt_section['section_id'],
				'input_type' => 'password',
				'name' => 'password',
				'descr' => 'Password'
				)
			);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'admin.uiconfig2.list_attrib',
				'section_id' => $receipt_section['section_id'], 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.admin')));
		}

		/**
		 * Magic get method
		 *
		 * @param string $varname the variable to fetch
		 *
		 * @return mixed the value of the variable sought - null if not found
		 */
		public function __get( $varname )
		{
			switch ($varname)
			{
				case 'unit_ids':
					return $this->unit_ids;
					break;
				case 'names':
					return $this->names;
					break;
				case 'messages':
					return $this->messages;
					break;
				default:
					return null;
			}
		}
		/* our simple php ping function */

		function ping( $host )
		{
			exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
			return $rval === 0;
		}

		public function get_db()
		{
			if ($this->db && is_object($this->db))
			{
				return $this->db;
			}

			if (!$this->config->config_data['fellesdata']['host'] || !$this->ping($this->config->config_data['fellesdata']['host']))
			{
				$message = "Database server {$this->config->config_data['fellesdata']['host']} is not accessible";
				phpgwapi_cache::message_set($message, 'error');
				return false;
			}

			$db = createObject('phpgwapi.db_adodb', null, null, true);
			$db->debug = false;
			$db->Host = $this->config->config_data['fellesdata']['host'];
			$db->Port = $this->config->config_data['fellesdata']['port'];
			$db->Type = 'oracle';
			$db->Database = $this->config->config_data['fellesdata']['db_name'];
			$db->User = $this->config->config_data['fellesdata']['user'];
			$db->Password = $this->config->config_data['fellesdata']['password'];

			try
			{
				$db->connect();
				$this->connected = true;
			}
			catch (Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}

			$this->db = $db;
			return $db;
		}

		public function insert_values()
		{
			$table = 'fm_org_unit';

			$db = & $GLOBALS['phpgw']->db;
			$db->transaction_begin();
			$db->query("UPDATE {$table} SET active = 0", __LINE__, __FILE__);
			$units = $this->unit_ids;
//			_debug_array($units);
			foreach ($units as $unit)
			{
				$value_set = array(
					'id' => $unit['id'],
					'parent_id' => $unit['parent'],
					'name' => $db->db_addslashes($unit['name']),
					'arbeidssted' => $unit['arbeidssted'],
					'created_on' => time(),
					'created_by' => $GLOBALS['phpgw_info']['user']['account_id'],
					'modified_by' => $GLOBALS['phpgw_info']['user']['account_id'],
					'modified_on' => time()
				);

				$db->query("SELECT count(*) as cnt FROM {$table} WHERE id =" . (int)$unit['id'], __LINE__, __FILE__);
				$db->next_record();

				if ($db->f('cnt'))
				{
					unset($value_set['id']);
					unset($value_set['created_on']);

					$value_set['active'] = 1;
					$value_set = $db->validate_update($value_set);
					$sql = "UPDATE {$table} SET {$value_set} WHERE id =" . (int)$unit['id'];
					if ($this->debug)
					{
						$this->messages[] = "ID finnes fra før: {$unit['id']}, oppdaterer: {$unit['name']}";
						$this->messages[] = $sql;
					}
				}
				else
				{
					$cols = implode(',', array_keys($value_set));
					$values = $db->validate_insert(array_values($value_set));
					$sql = "INSERT INTO {$table} ({$cols}) VALUES ({$values})";
					if ($this->debug)
					{
						$this->messages[] = "ID fantes ikke fra før: {$unit['id']}, legger til: {$unit['name']}";
						$this->messages[] = $sql;
					}
				}

				$db->query($sql, __LINE__, __FILE__);
			}

			$db->transaction_commit();
		}

		function update_dimb()
		{
			if (!$db = $this->get_db())
			{
				return;
			}

			$sql = "SELECT V_ANSVAR.ANSVAR, V_ANSVAR.BESKRIVELSE,V_ANSVAR.STATUS, V_ORG_ENHET.ORG_ENHET_ID"
				. "  FROM V_ANSVAR JOIN V_ORG_ENHET ON (V_ANSVAR.RESULTATENHET = V_ORG_ENHET.RESULTATENHET)";

			$db->query($sql, __LINE__, __FILE__);
			$values = array();
			while ($db->next_record())
			{
				$values[] = array(
					'id'	=> (int)$db->f('ANSVAR'),
					'descr'	=> $GLOBALS['phpgw']->db->db_addslashes($db->f('BESKRIVELSE', true)),
					'active' => $db->f('STATUS') == 'C' ? 0 : 1,
					'org_unit_id' => (int)$db->f('ORG_ENHET_ID')
				);
			}

			foreach ($values as $entry)
			{
				$GLOBALS['phpgw']->db->query("SELECT id FROM fm_ecodimb WHERE id = {$entry['id']}", __LINE__, __FILE__);
				if($GLOBALS['phpgw']->db->next_record())
				{
					$sql = "UPDATE fm_ecodimb SET descr = '{$entry['descr']}', active = {$entry['active']}, org_unit_id = {$entry['org_unit_id']}  WHERE id = {$entry['id']}";
				}
				else
				{
					$sql = "INSERT INTO fm_ecodimb (id, descr, active, org_unit_id)"
						. " VALUES ({$entry['id']}, '{$entry['descr']}',  {$entry['active']}, {$entry['org_unit_id']})";
				}
				$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			}
		}

		function get_org_unit_ids_from_top()
		{
			if (!$db = $this->get_db())
			{
				return;
			}

			$sql = "SELECT ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN FROM V_ORG_ENHET";
			$db->query($sql, __LINE__, __FILE__);
			while ($db->next_record())
			{
				$org_unit_id = $db->f('ORG_ENHET_ID');
				$name = $db->f('ORG_NAVN', true);
				$this->names[$org_unit_id] = $name;
			}
//			_debug_array($db);
//			_debug_array($this->names);die();
			$sql = "SELECT V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.TJENESTESTED, V_ORG_ENHET.ORG_NIVAA FROM V_ORG_ENHET"
				. " WHERE V_ORG_ENHET.ORG_NIVAA = 1 ORDER BY V_ORG_ENHET.ORG_NAVN ASC";

			$db->query($sql);

			while ($db->next_record())
			{
				$org_unit_id = $db->f('ORG_ENHET_ID');
				$arbeidssted = $db->f('TJENESTESTED');
				$this->unit_ids[] = array
					(
					'id' => $org_unit_id,
					'name' => $this->names[$org_unit_id],
					'parent' => '',
					'arbeidssted'	=> $arbeidssted
				);

				$this->get_org_unit_ids_children($org_unit_id);
			}
			return $this->unit_ids;
		}

		function get_org_unit_ids_children( $org_unit_id )
		{
			$org_unit_id = (int)$org_unit_id;
			$db = clone($this->db);

			$sql = "SELECT V_ORG_KNYTNING.*, ANT_ENHETER_UNDER,V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.TJENESTESTED, ORG_NIVAA FROM V_ORG_KNYTNING"
				. " JOIN V_ORG_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_KNYTNING.ORG_ENHET_ID ) WHERE V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING={$org_unit_id}";

			$db->query($sql);

			while ($db->next_record())
			{
				$child_org_unit_id = $db->f('ORG_ENHET_ID');
				$arbeidssted = $db->f('TJENESTESTED');
				$this->unit_ids[] = array(
					'id' => $child_org_unit_id,
					'name' => $this->names[$child_org_unit_id],
					'parent' => $org_unit_id,
					'level' => $db->f('ORG_NIVAA'),
					'arbeidssted'	=> $arbeidssted,
					'ant_enheter_under'	=> $db->f('ANT_ENHETER_UNDER')
				);

				if ($db->f('ANT_ENHETER_UNDER'))
				{
					$this->get_org_unit_ids_children($child_org_unit_id);
				}
			}
//			unset($db);
		}
	}