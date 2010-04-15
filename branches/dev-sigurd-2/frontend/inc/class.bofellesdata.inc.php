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
        public function get_result_units(string $username)
        {
        	/* 1. Get all organizational units this user has access to
             * 2. Check level for each unit and traverse if necessary
             * 3. Build an array of result units this user has access to
             */
        	
        	$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NIVAA, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.ENHET_ID";
        	$table = "V_ORG_ENHET";
        	$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_PERSON_ENHET.ORG_ENHET_ID = V_ORG_ENHET.ORG_ENHET_ID) ".
        				"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";
        	
        	$sql = "SELECT $columns FROM $table $joins WHERE V_ORG_PERSON.BRUKERNAVN = '$username'";
        	
        	$db = $this->get_db();
        	$db->query($sql,__LINE__,__FILE__);
        	
        	$db1 = clone($db);
        	$result_units = array();
       		while ($db->next_record())
			{
				$identifier  = (int)$db->f('ORG_ENHET_ID');
				$level = (int)$db->f('ORG_NIVAA','int');
				$name = $db->f('ORG_NAVN',true);
				$unit_id = (int)$db->f('ENHET_ID'); // string or int?
				
				switch($level)
				{
					case 1: break;	// TODO: Access to all result units
					case 2: 		// LEVEL: Byrådsavdeling 
						//Must traverse down the hierarchy
						$tables = "V_ORG_ENHET";
						$joins = "LEFT JOIN ORG_KNYTNING (ORG_KNYTNING.ORG_ENHET_ID_KNYTNING = V_ORG_ENHET.ORG_ENHET_ID)";
						$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4 AND ORG_KNYTNING.ORG_ENHET_ID = 2";
						
        				//$db1 = $this->get_db();
        				$db1->query($sql,__LINE__,__FILE__);
        				while ($db1->next_record())
						{
							$result_unit[] = array(
								"ORG_ENHET_ID" => (int)$db1->f('ORG_ENHET_ID'),
								"ORG_NIVAA" => (int)$db1->f('ORG_NIVAA'),
								"ORG_NAVN" => $db1->f('ORG_NAVN',true),
								"ENHET_ID" => (int)$db1->f('ENHET_ID')
							);
						}
						break;
					case 3:	break;	// LEVEL: Seksjon (not in use)
					case 4:			// LEVEL: Resultatenhet
						//Insert in result array	
						$result_units[] = array(
							"ORG_ENHET_ID" => (int)$db1->f('ORG_ENHET_ID'),
							"ORG_NIVAA" => (int)$db1->f('ORG_NIVAA'),
							"ORG_NAVN" => $db1->f('ORG_NAVN',true),
							"ENHET_ID" => (int)$db1->f('ENHET_ID')
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
        	$sql = "SELECT V_ORG_ENHET.ORG_NAVN FROM V_ORG_ENHET WHERE V_ORG_ENHET.ORG_ENHET_ID = $number";
        	$db = $this->get_db();
        	$db->query($sql,__LINE__,__FILE__);
        	if($db->num_rows() > 0)
        	{
        		return 	$db->f('ORG_NAVN', true);
        	} 
        	else
        	{
        		return "Enhet har ingen navn";
        	}
        	
        }
    }
