<?php
    class frontend_bofellesdata {

    	// Instance variable
	    protected static $bo;
		
    	/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_instance()
		{
			if (self::$bo == null) {
				self::$bo = CreateObject('frontend.bofellesdata');
			}
			return self::$bo;
		}
		
		public function get_db()
		{
			$db = createObject('phpgwapi.db', null, null, true);
			$db->Debug = true;	
			$db->Host = '10.11.12.40';
			$db->Type = 'oci8';
			$db->Database = 'FELTEST';
			$db->User = 'FELLES1';
			$db->Password = 'enkel';
			$db->connect();
			return $db;
		}
    	
        /**
         * Method for retrieving the result units this user has access to
         * 
         * @param string $username the username
         * @return an array of (result unit number => result unit name)
         */
        public function get_result_units(string $username) {

        	/* 1. Get all organizational units this user has access to
             * 2. Check level for each unit and traverse if necessary
             * 3. Build an array of result units this user has access to
             */
        	
        	$columns = "ORG_ENHET.ORG_ENHET_ID, ORG_ENHET.ORG_NIVAA, ORG_ENHET.ORG_NAVN, ORG_ENHET.ENHET_ID";
        	$table = "ORG_ENHET";
        	$joins = 	"LEFT JOIN ORG_PERSON_ENHET (ORG_PERSON_ENHET.ORG_ENHET_ID = ORG_ENHET.ORG_ENHET_ID) ".
        				"LEFT JOIN ORG_PERSON (ORG_PERSON.ORG_PERSON_ID = ORG_PERSON_ENHET.ORG_PERSON_ID)";
        	
        	$sql = "SELECT $columns FROM $table $joins WHERE ORG_PERSON.BRUKERNAVN = $username";
        	
        	
        	$db = $this->get_db();
        	$db->query($sql,__LINE__,__FILE__);
        	
        	$result_units = array();
        	
       		while ($db->next_record())
			{
				$identifier  = $db->f('ORG_ENHET_ID','int');
				$level = $db->f('ORG_NIVAA','int');
				$name = $db->f('ORG_NAVN','string');
				$unit_id = $db->f('ENHET_ID','string');
				
				switch($level)
				{
					case 1: break;	// TODO: Access to all result units
					case 2: 		// LEVEL: Byrådsavdeling 
						//Must traverse down the hierarchy
						$tables = "ORG_ENHET";
						$joins = "LEFT JOIN ORG_KNYTNING (ORG_KNYTNING.ORG_ENHET_ID_KNYTNING = ORG_ENHET.ORG_ENHET_ID)";
						$sql = "SELECT $columns FROM $tables $joins WHERE ORG_ENHET.ORG_NIVAA = 4 AND ORG_KNYTNING.ORG_ENHET_ID = 2";
						
        				$db1 = $this->get_db();
        				$db1->query($sql,__LINE__,__FILE__);
        				while ($db1->next_record())
						{
							$result_unit[] = array(
								"ORG_ENHET_ID" => $db1->f('ORG_ENHET_ID','int'),
								"ORG_NIVAA" => $db1->f('ORG_NIVAA','int'),
								"ORG_NAVN" => $db1->f('ORG_NAVN','string'),
								"ENHET_ID" => $db1->f('ENHET_ID','int')
							);
						}
						break;
					case 3:	break;	// LEVEL: Seksjon (not in use)
					case 4:			// LEVEL: Resultatenhet
						//Insert in result array	
						$result_units[] = array(
							"ORG_ENHET_ID" => $db1->f('ORG_ENHET_ID','int'),
							"ORG_NIVAA" => $db1->f('ORG_NIVAA','int'),
							"ORG_NAVN" => $db1->f('ORG_NAVN','string'),
							"ENHET_ID" => $db1->f('ENHET_ID','int')
						);
						break;	
				}
			}
        	
        	return $result_units;
        }
        
        /**
         * 
         * @param int $number the result unit number
         */
        public function get_organisational_unit_name(int $number) {
        	$sql = "SELECT ORG_ENHET.ORG_NAVN FROM ORG_ENHET WHERE ORG_ENHET.ORG_ENHET_ID = $number";
        	$db = $this->get_db();
        	$db->query($sql,__LINE__,__FILE__);
        	if($db->num_rows() > 0)
        	{
        		return 	$db->f('ORG_NAVN','string');
        	} 
        	else
        	{
        		return "Enhet har ingen navn";
        	}
        	
        }
        
        
        
/*
        public function database_logic(){
        	
		
		//lister alle tabller og views
			_debug_array($db->table_names(true));
		
			$table1 = 'V_ORG_ENHET';
			$table2 = 'V_ORG_ENHET_ENDRINGER';
		 	$table3 = 'V_ORG_PERSON';
			$table4 = 'V_ORG_PERSON_ENDRINGER';
		 	$table5 = 'V_ORG_PERSON_ENHET';
			$table6 = 'V_ORG_PERSON_ENHET_ENDRINGER';
			$table7 = 'V_AGRESSO_KUNDE_ADRESSE';
			
		//	$sql = "SELECT * FROM $table7 WHERE 1=1";
			$sql = "SELECT * FROM $table3 WHERE 1=1 ORDER BY ETTERNAVN ASC";// AND org_nivaa = 1";
			
		//	$sql = "SELECT * FROM $table5 JOIN $table1 ON $table1.ORG_ENHET_ID = $table5.ORG_ENHET_ID JOIN $table3 ON $table5.ORG_PERSON_ID = $table3.ORG_PERSON_ID WHERE 1=1 AND org_nivaa = 1";
		//	$sql = "SELECT $table3.NAVN FROM $table5 JOIN $table1 ON $table1.ORG_ENHET_ID = $table5.ORG_ENHET_ID JOIN $table3 ON $table5.ORG_PERSON_ID = $table3.ORG_PERSON_ID WHERE 1=1 AND $table1.ORG_ENHET_ID = 1";
		
		
			$start = 0;
		
			$limit = false;
		//	$limit = true;
			if($limit)
			{
				$db->limit_query($sql,$start,__LINE__,__FILE__);
			}
			else
			{
				$db->query($sql,__LINE__,__FILE__);
			}
		
		//_debug_array($db->num_rows());
		
			$values = array();
			while ($db->next_record())
			{
				$values[] = $db->Record;
			}
		
			_debug_array($values);
			
			
		//	_debug_array($db->table_names(true)); die();
        
        }
 */       



    }
