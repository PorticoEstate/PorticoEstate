<?php
	/**
	 * Frontend : a simplified tool for end users.
	 *
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Frontend
	 * @version $Id: class.bofellesdata.inc.php 11396 2013-10-25 13:49:32Z sigurdne $
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

	class rental_bofellesdata
	{
    	// Instance variable
		protected static $bo;
		protected $connected = false;
		protected $status;
		protected $db = null;
		protected $unit_ids = array();

		var $public_functions = array
			(
				'get_all_org_units_autocomplete'		=> true,
			);

    	/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return the storage object
		 */
		public static function get_instance()
		{
			if (self::$bo == null)
			{
				self::$bo = CreateObject('rental.bofellesdata');
			}
			return self::$bo;
		}



		/**
		 * Magic get method
		 *
		 * @param string $varname the variable to fetch
		 *
		 * @return mixed the value of the variable sought - null if not found
		 */
		public function __get($varname)
		{
			switch ($varname)
			{
				case 'unit_ids':
					return $this->unit_ids;
					break;
				default:
					return null;
			}
		}


		/* our simple php ping function */
		function ping($host)
		{
	        exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
	        return $rval === 0;
		}

		public function get_db()
		{
			if($this->db && is_object($this->db))
			{
				return $this->db;
			}

			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();

			if(! $config->config_data['external_db_host'] || !$this->ping($config->config_data['external_db_host']))
			{
				$message ="Database server {$config->config_data['external_db_host']} is not accessible";
				phpgwapi_cache::message_set($message, 'error');
				return false;
			}

			$db = createObject('phpgwapi.db', null, null, true);

			$db->debug = !!$config->config_data['external_db_debug'];
			$db->Host = $config->config_data['external_db_host'];
			$db->Port = $config->config_data['external_db_port'];
			$db->Type = $config->config_data['external_db_type'];
			$db->Database = $config->config_data['external_db_name'];
			$db->User = $config->config_data['external_db_user'];
			$db->Password = $config->config_data['external_db_password'];

			try
			{
				$db->connect();
				$this->connected = true;
			}
			catch(Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}

			$this->db = $db;
			return $db;
		}

		public function responsibility_id_exist($responsibility_id)
		{
			$this->log(__class__, __function__);

			if(isset($responsibility_id))
			{
				$column = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN";
				$table = "V_ORG_ENHET";
				$joins = "LEFT JOIN V_ANSVAR ON (V_ANSVAR.RESULTATENHET = V_ORG_ENHET.RESULTATENHET)";
				if(!$db = $this->get_db())
				{
					return;
				}

				$sql = "SELECT $column FROM $table $joins WHERE V_ANSVAR.ANSVAR = '$responsibility_id' AND V_ORG_ENHET.ORG_NIVAA = 4";
				if($db->Type == "postgres")
				{
					$sql = strtolower($sql);
				}
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{
					if($db->Type == "postgres")
					{
						return array(
							'UNIT_ID' => $db->f('org_enhet_id'),
							'UNIT_NAME' => $db->f('org_navn')
						);
					}
					else
					{
						return array(
							'UNIT_ID' => $db->f('ORG_ENHET_ID'),
							'UNIT_NAME' => $db->f('ORG_NAVN')
						);
					}
				}
			}
			return false;
		}



  		public function result_unit_exist($result_unit, $level)
		{
			$this->log(__class__, __function__);

			if(isset($result_unit) && is_numeric($result_unit))
			{
				$column = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN";
				$table = "V_ORG_ENHET";
				if(!$db = $this->get_db())
				{
					return;
				}

				$sql = "SELECT $column FROM $table WHERE V_ORG_ENHET.RESULTATENHET = $result_unit";
				if($level) $sql = "$sql AND V_ORG_ENHET.ORG_NIVAA = $level";
				if($db->Type == "postgres")
				{
						$sql = strtolower($sql);
				}
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{
					if($db->Type == "postgres")
					{
						return array(
							'UNIT_ID' => $db->f('org_enhet_id'),
							'UNIT_NAME' => $db->f('org_navn')
						);
					}
					else
					{
						return array(
							'UNIT_ID' => $db->f('ORG_ENHET_ID'),
							'UNIT_NAME' => $db->f('ORG_NAVN')
						);
					}
				}
			}
			return false;
		}

		public function org_unit_exist($org_unit_id, $level)
		{
			$this->log(__class__, __function__);

			if(isset($org_unit_id) && is_numeric($org_unit_id))
			{
				$column = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN";
				$table = "V_ORG_ENHET";
				if(!$db = $this->get_db())
				{
					return;
				}

				$sql = "SELECT $column FROM $table WHERE V_ORG_ENHET.ORG_ENHET_ID = $org_unit_id";
				if($level) $sql = "$sql AND V_ORG_ENHET.ORG_NIVAA = $level";

				if($db->Type == "postgres")
				{
						$sql = strtolower($sql);
				}
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{
					if($db->Type == "postgres")
					{
						return array(
							'UNIT_ID' => $db->f('org_enhet_id'),
							'UNIT_NAME' => $db->f('org_navn')
						);
					}
					else
					{
						return array(
							'UNIT_ID' => $db->f('ORG_ENHET_ID'),
							'UNIT_NAME' => $db->f('ORG_NAVN')
						);
					}
				}
			}
			return false;
		}

		public function get_result_unit($org_unit_id, $org_level = 4)
		{
			$this->log(__class__, __function__);

	        //Must traverse down u hierarchy
			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NIVAA, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.ENHET_ID, V_ORG_ENHET.RESULTATENHET";
			$tables = "V_ORG_ENHET";
			$joins = "LEFT JOIN V_ORG_KNYTNING ON (V_ORG_KNYTNING.ORG_ENHET_ID = V_ORG_ENHET.ORG_ENHET_ID)";
			//$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_KNYTNING.ORG_ENHET_ID = {$org_unit_id}";
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = {$org_level} AND V_ORG_KNYTNING.ORG_ENHET_ID = {$org_unit_id}";
			if(!$db = $this->get_db())
			{
				return;
			}

			if($db->Type == "postgres")
			{
			    $sql = strtolower($sql);
			}

			$db->query($sql,__LINE__,__FILE__);

			if($db->next_record())
			{
				if($db->Type == "postgres")
				{
					return array(
							"ORG_UNIT_ID" => (int)$db->f('org_enhet_id'),
							"ORG_NAME" => $db->f('org_navn'),
							"UNIT_ID" => $db->f('resultatenhet')
						);
				}
				else
				{
					return array(
							"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
							"ORG_NAME" => $db->f('ORG_NAVN'),
							"UNIT_ID" => $db->f('RESULTATENHET')
						);
				}
			}
		}


		public function get_all_org_units_autocomplete()
		{
			$query = mb_strtoupper(phpgw::get_var('query'), 'UTF-8');

			$columns = "V_ORG_ENHET.ORG_ENHET_ID , V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.RESULTATENHET,  V_ORG_ENHET.ORG_NIVAA";
			$tables = "V_ORG_ENHET";
			$sql = "SELECT $columns FROM $tables WHERE upper(ORG_NAVN) LIKE '%{$query}%' ORDER BY V_ORG_ENHET.RESULTATENHET ASC";
			if(!$db = $this->get_db())
			{
				return;
			}

			if($db->Type == "postgres")
			{
				$sql = strtolower($sql);
			}
			$db->query($sql,__LINE__,__FILE__);

			$org_enhet_field	= $db->Type == 'postgres' ? 'org_enhet_id' : 'ORG_ENHET_ID';
			$name_field			= $db->Type == 'postgres' ? 'org_navn' : 'ORG_NAVN';
			$unit_id_field		= $db->Type == 'postgres' ? 'resultatenhet' : 'RESULTATENHET';
			$level_field 		= $db->Type == 'postgres' ? 'org_nivaa' : 'ORG_NIVAA';

			$result_units = array();
			while($db->next_record())
			{
				$result[] = array
				(
					'id' => (int)$db->f($org_enhet_field),
					'name' => $db->f($name_field,true) . ' ('  . (int)$db->f($level_field ) . ')',
					'unit_id' => $db->f($unit_id_field)
				);
			}

			return array('ResultSet'=> array('Result'=>$result));
		}


		public function get_org_unit_name($id = 0)
		{
			$sql = "SELECT V_ORG_ENHET.ORG_NAVN FROM V_ORG_ENHET WHERE ORG_ENHET_ID =" . (int)$id;
			if(!$db = $this->get_db())
			{
				return;
			}
			if($db->Type == "postgres")
			{
				$sql = strtolower($sql);
			}
			$db->query($sql,__LINE__,__FILE__);
			$db->next_record();
			if($db->Type == "postgres")
			{
				return $db->f('org_navn',true);
			}
			else
			{
				return $db->f('ORG_NAVN',true);
			}
		}

		public function get_result_units()
		{
			$this->log(__class__, __function__);

			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.RESULTATENHET";
			$tables = "V_ORG_ENHET";
			$sql = "SELECT $columns FROM $tables WHERE V_ORG_ENHET.ORG_NIVAA = 4 ORDER BY V_ORG_ENHET.RESULTATENHET ASC";
			if(!$db = $this->get_db())
			{
				return;
			}

			if($db->Type == "postgres")
			{
				$sql = strtolower($sql);
			}
			$db->query($sql,__LINE__,__FILE__);

			$result_units = array();
			while($db->next_record())
			{
				if($db->Type == "postgres")
				{
					$result_units[] = array(
							"ORG_UNIT_ID" => (int)$db->f('org_enhet_id'),
							"ORG_UNIT_NAME" => $db->f('org_navn'),
							"UNIT_ID" => $db->f('resultatenhet')
						);
				}
				else
				{
					$result_units[] = array(
						"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
						"ORG_UNIT_NAME" => $db->f('ORG_NAVN'),
						"UNIT_ID" => $db->f('RESULTATENHET')
					);
				}
			}

			return $result_units;
		}

		/**
		 * Get id/name for result unit
		 *
		 * @return array values prepared for standardized select/filter
		 */
		public function get_result_units_wrapper()
		{
			$result_units = $this->get_result_units();
			$values = array();
			foreach($result_units as $result_unit)
			{
				$values[] = array
				(
					'id'	=> $result_unit['ORG_UNIT_ID'],
					'name'	=> "{$result_unit['UNIT_ID']} - {$result_unit['ORG_UNIT_NAME']}"
				);
			}
			return $values;
		}


		/**
		 * Get id/name for org unit
		 * @param integer $level level in organization hierarchy
		 *
		 * @return array values prepared for standardized select/filter
		 */
		public function get_org_units($level = 1)
		{
			$this->log(__class__, __function__);

			$level = (int) $level;
			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN";
			$tables = "V_ORG_ENHET";
			$sql = "SELECT {$columns} FROM {$tables} WHERE V_ORG_ENHET.ORG_NIVAA = {$level} ORDER BY V_ORG_ENHET.ORG_NAVN ASC";
			if(!$db = $this->get_db())
			{
				return;
			}

			if($db->Type == "postgres")
			{
				$sql = strtolower($sql);
			}
			$db->query($sql,__LINE__,__FILE__);

			$values = array();
			while($db->next_record())
			{
				if($db->Type == "postgres")
				{
					$values[] = array
					(
						'id' 	=> (int)$db->f('org_enhet_id'),
						'name'	=> $db->f('org_navn'),
					);
				}
				else
				{
					$values[] = array
					(
						'id' 	=> (int)$db->f('ORG_ENHET_ID'),
						'name'	=> $db->f('ORG_NAVN'),
					);
				}
			}

			return $values;
		}


		public function get_result_unit_with_leader($org_unit_id, $org_level = 4)
		{
			$this->log(__class__, __function__);

			$columns = 	"V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.EPOST, V_ORG_PERSON.FORNAVN, V_ORG_PERSON.ETTERNAVN, V_ORG_PERSON.BRUKERNAVN";
			$tables = 	"V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_PERSON_ENHET.ORG_ENHET_ID AND V_ORG_PERSON_ENHET.prioritet = 1) ".
						"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";

			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = {$org_level} AND V_ORG_ENHET.ORG_ENHET_ID = {$org_unit_id}";
			//$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_ENHET_ID = {$org_unit_id}";
			if(!$db = $this->get_db())
			{
				return;
			}

			if($db->Type == "postgres")
			{
				$sql = strtolower($sql);
			}

			$db->query($sql,__LINE__,__FILE__);

			if($db->next_record())
			{
				if($db->Type == "postgres")
				{
					$full_name = $db->f('fornavn')." ".$db->f('etternavn');

					return array(
							"ORG_UNIT_ID" => (int)$db->f('org_enhet_id'),
							"ORG_UNIT_NAME" => $db->f('org_navn'),
							"ORG_EMAIL" => $db->f('epost'),
							"LEADER_FIRSTNAME" => $db->f('fornavn'),
							"LEADER_LASTNAME" => $db->f('etternavn'),
							"LEADER_FULLNAME" => $full_name,
							"LEADER_USERNAME" => $db->f('brukernavn')
						);
				}
				else
				{
					$full_name = $db->f('FORNAVN')." ".$db->f('ETTERNAVN');

				return array(
						"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
						"ORG_UNIT_NAME" => $db->f('ORG_NAVN'),
						"ORG_EMAIL" => $db->f('EPOST'),
						"LEADER_FIRSTNAME" => $db->f('FORNAVN'),
						"LEADER_LASTNAME" => $db->f('ETTERNAVN'),
						"LEADER_FULLNAME" => $full_name,
						"LEADER_USERNAME" => $db->f('BRUKERNAVN')
					);
				}
			}
		}

    	public function get_department_for_org_unit($org_unit_id)
		{
			$this->log(__class__, __function__);

			$columns = 	"DEP_ORG_ENHET.ORG_ENHET_ID, DEP_ORG_ENHET.ORG_NAVN";
			$tables = 	"V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_KNYTNING ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_KNYTNING.ORG_ENHET_ID) " .
						"LEFT JOIN V_ORG_ENHET DEP_ORG_ENHET ON (V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING = DEP_ORG_ENHET.ORG_ENHET_ID) ";

			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4 AND V_ORG_ENHET.ORG_ENHET_ID = {$org_unit_id}";

			if(!$db = $this->get_db())
			{
				return;
			}

			if($db->Type == "postgres")
			{
				$sql = strtolower($str);
			}

			$db->query($sql,__LINE__,__FILE__);

			if($db->next_record())
			{
				if($db->Type == "postgres")
				{
					return array(
							"DEP_ORG_ID" => (int)$db->f('org_enhet_id'),
							"DEP_ORG_NAME" => $db->f('org_navn')
						);
				}
				else
				{
					return array(
						"DEP_ORG_ID" => (int)$db->f('ORG_ENHET_ID'),
						"DEP_ORG_NAME" => $db->f('ORG_NAVN')
					);
				}
			}
		}


		public function get_result_units_with_leader($start_index, $num_of_objects, $sort_field, $sort_ascending,$search_for, $search_type)
		{
			$this->log(__class__, __function__);

			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NIVAA, V_ORG_ENHET.ORG_NAVN, V_ORG_PERSON.FORNAVN, V_ORG_PERSON.ETTERNAVN, V_ORG_PERSON.BRUKERNAVN";
			$tables = "V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_PERSON_ENHET.ORG_ENHET_ID AND V_ORG_PERSON_ENHET.prioritet = 1) ".
						"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA > 1";
			if($search_for)
			{
				$search_for = strtoupper($search_for);
				$selector = "";
				switch($search_type){
					case 'unit_leader':
							$search_words = split(' ', $search_for);
							$count = 0;
							$selector = "(";
							foreach($search_words as $search_word){
								if($db->Type == "postgres")
								{
									$selector = $selector." (upper(fornavn) LIKE '%$search_word%' OR ".
											"upper(etternavn) LIKE '%$search_word%' OR ".
											"upper(brukernavn) LIKE '%$search_word%')";
								}
								else
								{
									$selector = $selector." (upper(FORNAVN) LIKE '%$search_word%' OR ".
										"upper(ETTERNAVN) LIKE '%$search_word%' OR ".
										"upper(BRUKERNAVN) LIKE '%$search_word%')";
								}
								if($count < (count($search_words)-1)) $selector = $selector." OR ";
								$count = ($count + 1);
							}
							$selector = $selector.")";

						break;
					default:
							if($db->Type == "postgres")
							{
								$selector = "upper(org_navn) LIKE '%".$search_for."%'";
							}
							else
							{
								$selector = "upper(ORG_NAVN) LIKE '%".$search_for."%'";
							}
						break;
				}
				$sql = "$sql AND $selector";
			}

			$dir = $sort_ascending ? 'ASC' : 'DESC';

			switch($sort_field){
				case "ORG_UNIT_ID":
					if($db->Type == "postgres")
					{
						$order_by = "ORDER BY V_ORG_ENHET.org_enhet_id $dir";
					}
					else
					{
						$order_by = "ORDER BY V_ORG_ENHET.ORG_ENHET_ID $dir";
					}
					break;
				case "ORG_UNIT_NAME":
					if($db->Type == "postgres")
					{
						$order_by = "ORDER BY V_ORG_ENHET.org_navn $dir";
					}
					else
					{
						$order_by = "ORDER BY V_ORG_ENHET.ORG_NAVN $dir";
					}
					break;
				case "LEADER_FULLNAME":
					if($db->Type == "postgres")
					{
						$order_by = "ORDER BY V_ORG_PERSON.fornavn $dir, V_ORG_PERSON.etternavn $dir";
					}
					else
					{
						$order_by = "ORDER BY V_ORG_PERSON.FORNAVN $dir, V_ORG_PERSON.ETTERNAVN $dir";
					}
					break;
				default:
					if($db->Type == "postgres")
					{
						$order_by = "ORDER BY V_ORG_ENHET.org_enhet_id $dir";
					}
					else
					{
						$order_by = "ORDER BY V_ORG_ENHET.ORG_ENHET_ID $dir";
					}
					break;
			}
			$sql = "$sql $order_by";


			if(!$db = $this->get_db())
			{
				return;
			}

			$db->limit_query($sql,$start_index,__LINE__,__FILE__,$num_of_objects);

			$result_units = array();
			while($db->next_record())
			{
				if($db->Type == "postgres")
				{
					$full_name = $db->f('fornavn')." ".$db->f('etternavn');

					$result_units[] = array(
							"ORG_UNIT_ID" => (int)$db->f('org_enhet_id'),
							"ORG_UNIT_LEVEL" => (int)$db->f('org_nivaa'),
							"ORG_UNIT_NAME" => $db->f('org_navn'),
							"LEADER_FIRSTNAME" => $db->f('fornavn'),
							"LEADER_LASTNAME" => $db->f('etternavn'),
							"LEADER_FULLNAME" => $full_name,
							"LEADER_USERNAME" => $db->f('brukernavn')
						);
				}
				else
				{
					$full_name = $db->f('FORNAVN')." ".$db->f('ETTERNAVN');

				$result_units[] = array(
						"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
						"ORG_UNIT_LEVEL" => (int)$db->f('ORG_NIVAA'),
						"ORG_UNIT_NAME" => $db->f('ORG_NAVN'),
						"LEADER_FIRSTNAME" => $db->f('FORNAVN'),
						"LEADER_LASTNAME" => $db->f('ETTERNAVN'),
						"LEADER_FULLNAME" => $full_name,
						"LEADER_USERNAME" => $db->f('BRUKERNAVN')
					);
				}
			}
			return $result_units;
		}

		public function get_result_units_count($search_for, $search_type)
		{
			$this->log(__class__, __function__);

			$columns = "count(*) as COUNT";
			$tables = "V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_PERSON_ENHET.ORG_ENHET_ID AND V_ORG_PERSON_ENHET.prioritet = 1) ".
						"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4";
			if($search_for)
			{
				$search_for = strtoupper($search_for);
				$selector = "";
				switch($search_type){
					case 'unit_leader':
							$search_words = split(' ', $search_for);
							$count = 0;
							$selector = "(";
							foreach($search_words as $search_word)
							{
								if($db->Type == "postgres")
								{
									$selector = $selector." (upper(fornavn) LIKE '%$search_word%' OR ".
											"upper(etternavn) LIKE '%$search_word%' OR ".
											"upper(brukernavn) LIKE '%$search_word%')";
								}
								else
								{
									$selector = $selector." (upper(FORNAVN) LIKE '%$search_word%' OR ".
										"upper(ETTERNAVN) LIKE '%$search_word%' OR ".
										"upper(BRUKERNAVN) LIKE '%$search_word%')";
								}
								if($count < (count($search_words)-1)) $selector = $selector." OR ";
								$count = ($count + 1);
							}
							$selector = $selector.")";

						break;
					default:
							if($db->Type == "postgres")
							{
								$selector = "upper(org_navn) LIKE '%".$search_for."%'";
							}
							else
							{
								$selector = "upper(ORG_NAVN) LIKE '%".$search_for."%'";
							}
						break;
				}
				$sql = "$sql AND $selector";
			}

			if(!$db = $this->get_db())
			{
				return;
			}

			$db->query($sql);

			if($db->next_record())
			{
				return $db->f('COUNT');
			}
			return 0;
		}



		function org_unit_is_top_level($org_unit_id)
		{
			if(!$db = $this->get_db())
			{
				return;
			}

			$q = "SELECT * FROM V_ORG_ENHET WHERE org_enhet_id=$org_unit_id AND org_nivaa < 4";

			$result = $this->db->query($q);

			if($this->db->next_record())
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function get_org_unit_ids_from_top($org_unit_id)
		{
			if(!$db = $this->get_db())
			{
				return;
			}

			if(!$org_unit_id)
			{
				return array();
			}

			$this->unit_ids[] = $org_unit_id;

			$q = "SELECT V_ORG_KNYTNING.*, ANT_ENHETER_UNDER FROM V_ORG_KNYTNING"
			. " JOIN V_ORG_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING ) WHERE V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING=$org_unit_id";

			if($db->Type == "postgres")
			{
				$q = strtolower($q);
			}
			$result = $db->query($q);

			$org_enhet_field = $db->Type == 'postgres' ? 'org_enhet_id' : 'ORG_ENHET_ID';
			$check_subs = $db->Type == 'postgres' ? 'ant_enheter_under' : 'ANT_ENHETER_UNDER';
		
			while($db->next_record())
			{
				$child_org_unit_id = $db->f($org_enhet_field);
				$this->unit_ids[] = $child_org_unit_id;

				if($db->f($check_subs))
				{
					$this->get_org_unit_ids_children($child_org_unit_id);
				}
			}

			return $this->unit_ids;
		}


		function get_org_unit_ids_children($org_unit_id)
		{
			$org_unit_id = (int)$org_unit_id;
			$db = clone($this->db);
		
			$q = "SELECT V_ORG_KNYTNING.*, ANT_ENHETER_UNDER FROM V_ORG_KNYTNING"
			. " JOIN V_ORG_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING ) WHERE V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING=$org_unit_id";

			if($db->Type == "postgres")
			{
				$q = strtolower($q);
			}
			$db->query($q);

			$org_enhet_field = $db->Type == 'postgres' ? 'org_enhet_id' : 'ORG_ENHET_ID';
			$check_subs = $db->Type == 'postgres' ? 'ant_enheter_under' : 'ANT_ENHETER_UNDER';

			while($db->next_record())
			{
				$child_org_unit_id = $db->f($org_enhet_field);
				$this->unit_ids[] = $child_org_unit_id;
				if($db->f($check_subs))
				{
					$this->get_org_unit_ids_children($child_org_unit_id);
				}
			}
		}


		protected function log($class, $function)
		{
			if(isset($GLOBALS['phpgw_info']['server']['log_levels']['module']['rental']) && $GLOBALS['phpgw_info']['server']['log_levels']['module']['rental'])
			{
				$bt = debug_backtrace();
				$GLOBALS['phpgw']->log->debug(array(
						'text' => "{$class}::{$function}() called from file: {$bt[1]['file']} line: {$bt[1]['line']}",
						'p1'   => '',
						'p2'	 => '',
						'line' => __LINE__,
						'file' => __FILE__
				));
				unset($bt);
			}
		}

		public function is_connected()
		{
			return $this->connected;
		}

		public function get_status()
		{
			return $this->status;
		}
    }
