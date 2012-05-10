<?php
    class rental_bofellesdata {

    	// Instance variable
	    protected static $bo;
	    protected $connected = false;
	    protected $status;
		
    	/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_instance()
		{
			if (self::$bo == null) {
				self::$bo = CreateObject('rental.bofellesdata');
			}
			return self::$bo;
		}
		
		public function get_db()
		{
			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();

			$db = createObject('phpgwapi.db', null, null, true);
//			$db = createObject('property.db_oci8'); // this one was intended for premilay testing

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
				$connected = true;
			}
			catch(Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}
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
				$db = $this->get_db();
				$sql = "SELECT $column FROM $table $joins WHERE V_ANSVAR.ANSVAR = '$responsibility_id' AND V_ORG_ENHET.ORG_NIVAA = 4";
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{	
					return array(
						'UNIT_ID' => $db->f('ORG_ENHET_ID'),
						'UNIT_NAME' => $db->f('ORG_NAVN')
					);				
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
				$db = $this->get_db();
				$sql = "SELECT $column FROM $table WHERE V_ORG_ENHET.RESULTATENHET = $result_unit";
				if($level) $sql = "$sql AND V_ORG_ENHET.ORG_NIVAA = $level";
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{
					return array(
						'UNIT_ID' => $db->f('ORG_ENHET_ID'),
						'UNIT_NAME' => $db->f('ORG_NAVN')
					);
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
				$db = $this->get_db();
				$sql = "SELECT $column FROM $table WHERE V_ORG_ENHET.ORG_ENHET_ID = $org_unit_id";
				if($level) $sql = "$sql AND V_ORG_ENHET.ORG_NIVAA = $level";
				$db->query($sql,__LINE__,__FILE__);
				if($db->next_record())
				{
					return array(
						'UNIT_ID' => $db->f('ORG_ENHET_ID'),
						'UNIT_NAME' => $db->f('ORG_NAVN')
					);				
				}
			}
			return false;
		}
		
		public function get_result_unit($org_unit_id)
		{   
			$this->log(__class__, __function__);

	        //Must traverse down u hierarchy
			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NIVAA, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.ENHET_ID, V_ORG_ENHET.RESULTATENHET";
			$tables = "V_ORG_ENHET";
			$joins = "LEFT JOIN V_ORG_KNYTNING ON (V_ORG_KNYTNING.ORG_ENHET_ID = V_ORG_ENHET.ORG_ENHET_ID)";
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4 AND V_ORG_KNYTNING.ORG_ENHET_ID = {$org_unit_id}";
			$db = $this->get_db();
			$db->query($sql,__LINE__,__FILE__);			
	        
			if($db->next_record())
			{
				$level = (int)$db->f('ORG_NIVAA');
				if($level == 4)
				{
					return array(
							"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
							"ORG_NAME" => $db->f('ORG_NAVN'),
							"UNIT_ID" => $db->f('RESULTATENHET')
						);
				}
			}
		}
	
		
		public function get_result_units()
		{
			$this->log(__class__, __function__);			

			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.RESULTATENHET";
			$tables = "V_ORG_ENHET";
			$sql = "SELECT $columns FROM $tables WHERE V_ORG_ENHET.ORG_NIVAA = 4 ORDER BY V_ORG_ENHET.RESULTATENHET ASC";
			$db = $this->get_db();
			$db->query($sql,__LINE__,__FILE__);			
	        
			$result_units = array();
			while($db->next_record())
			{
				$result_units[] = array(
						"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
						"ORG_UNIT_NAME" => $db->f('ORG_NAVN'),
						"UNIT_ID" => $db->f('RESULTATENHET')
					);
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
			$db = $this->get_db();
			$db->query($sql,__LINE__,__FILE__);			
	        
			$values = array();
			while($db->next_record())
			{
				$values[] = array
				(
					'id' 	=> (int)$db->f('ORG_ENHET_ID'),
					'name'	=> $db->f('ORG_NAVN'),
				);
			}
						
			return $values;
		}

		
		public function get_result_unit_with_leader($org_unit_id)
		{
			$this->log(__class__, __function__);

			$columns = 	"V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.EPOST, V_ORG_PERSON.FORNAVN, V_ORG_PERSON.ETTERNAVN, V_ORG_PERSON.BRUKERNAVN";
			$tables = 	"V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_PERSON_ENHET.ORG_ENHET_ID AND V_ORG_PERSON_ENHET.prioritet = 1) ".
						"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";
			
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4 AND V_ORG_ENHET.ORG_ENHET_ID = {$org_unit_id}";
			$db = $this->get_db();
			$db->query($sql,__LINE__,__FILE__);
	        
			if($db->next_record())
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
			
    	public function get_department_for_org_unit($org_unit_id)
		{
			$this->log(__class__, __function__);

			$columns = 	"DEP_ORG_ENHET.ORG_ENHET_ID, DEP_ORG_ENHET.ORG_NAVN";
			$tables = 	"V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_KNYTNING ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_KNYTNING.ORG_ENHET_ID) " .
						"LEFT JOIN V_ORG_ENHET DEP_ORG_ENHET ON (V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING = DEP_ORG_ENHET.ORG_ENHET_ID) ";						
			
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4 AND V_ORG_ENHET.ORG_ENHET_ID = {$org_unit_id}";
					
			$db = $this->get_db();
			$db->query($sql,__LINE__,__FILE__);
						
			if($db->next_record())
			{
				
				return array(
						"DEP_ORG_ID" => (int)$db->f('ORG_ENHET_ID'),
						"DEP_ORG_NAME" => $db->f('ORG_NAVN')
					);
			}
		}
		
		
		public function get_result_units_with_leader($start_index, $num_of_objects, $sort_field, $sort_ascending,$search_for, $search_type)
		{
			$this->log(__class__, __function__);			

			$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN, V_ORG_PERSON.FORNAVN, V_ORG_PERSON.ETTERNAVN, V_ORG_PERSON.BRUKERNAVN";
			$tables = "V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_PERSON_ENHET.ORG_ENHET_ID AND V_ORG_PERSON_ENHET.prioritet = 1) ".
						"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA > 1";
			if($search_for){
				$selector = "";
				switch($search_type){
					case 'unit_leader':
							$search_words = split(' ', $search_for);
							$count = 0;
							$selector = "(";
							foreach($search_words as $search_word){
								$selector = $selector." (FORNAVN LIKE '%$search_word%' OR ".
										"ETTERNAVN LIKE '%$search_word%' OR ".
										"BRUKERNAVN LIKE '%$search_word%')";
								if($count < (count($search_words)-1)) $selector = $selector." OR ";
								$count = ($count + 1);
							}
							$selector = $selector.")";
							
						break;
					default:
							$selector = "ORG_NAVN LIKE '%".$search_for."%'";
						break; 
				}
				$sql = "$sql AND $selector";
			}
			
			$dir = $sort_ascending ? 'ASC' : 'DESC';
			
			switch($sort_field){
				case "ORG_UNIT_ID":
					$order_by = "ORDER BY V_ORG_ENHET.ORG_ENHET_ID $dir";
					break;
				case "ORG_UNIT_NAME":
					$order_by = "ORDER BY V_ORG_ENHET.ORG_NAVN $dir";
					break;
				case "LEADER_FULLNAME":
					$order_by = "ORDER BY V_ORG_PERSON.FORNAVN $dir, V_ORG_PERSON.ETTERNAVN $dir";
					break;
				default:
					$order_by = "ORDER BY V_ORG_ENHET.ORG_ENHET_ID $dir";
					break;
			}
			$sql = "$sql $order_by";
			
			
			$db = $this->get_db();
			$db->limit_query($sql,$start_index,__LINE__,__FILE__,$num_of_objects);
			
			$result_units = array();
			while($db->next_record())
			{
				
				$full_name = $db->f('FORNAVN')." ".$db->f('ETTERNAVN');
				
				$result_units[] = array(
						"ORG_UNIT_ID" => (int)$db->f('ORG_ENHET_ID'),
						"ORG_UNIT_NAME" => $db->f('ORG_NAVN'),
						"LEADER_FIRSTNAME" => $db->f('FORNAVN'),
						"LEADER_LASTNAME" => $db->f('ETTERNAVN'),
						"LEADER_FULLNAME" => $full_name,
						"LEADER_USERNAME" => $db->f('BRUKERNAVN')
					);
			}
			return $result_units;
		}
		
		public function get_result_units_count($search_for, $search_type)
		{
			$this->log(__class__, __function__);

			$columns = "count(*)";
			$tables = "V_ORG_ENHET";
			$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_ENHET.ORG_ENHET_ID = V_ORG_PERSON_ENHET.ORG_ENHET_ID AND V_ORG_PERSON_ENHET.prioritet = 1) ".
						"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";
			$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4";
			if($search_for){
				$selector = "";
				switch($search_type){
					case 'unit_leader':
							$search_words = split(' ', $search_for);
							$count = 0;
							$selector = "(";
							foreach($search_words as $search_word){
								$selector = $selector." (FORNAVN LIKE '%$search_word%' OR ".
										"ETTERNAVN LIKE '%$search_word%' OR ".
										"BRUKERNAVN LIKE '%$search_word%')";
								if($count < (count($search_words)-1)) $selector = $selector." OR ";
								$count = ($count + 1);
							}
							$selector = $selector.")";
							
						break;
					default:
							$selector = "ORG_NAVN LIKE '%".$search_for."%'";
						break; 
				}
				$sql = "$sql AND $selector";
			}
			
			$db = $this->get_db();
			$db->query($sql);
			
			if($db->next_record())
			{
				return $db->f('count(*)');
			}
			return 0;
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
		
