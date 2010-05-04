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

			$config	= CreateObject('phpgwapi.config','frontend');
			$config->read();

			$db = createObject('phpgwapi.db', null, null, true);

			$db->debug = !!$config->config_data['external_db_debug'];
			$db->Host = $config->config_data['external_db_host'];
			$db->Type = $config->config_data['external_db_type'];
			$db->Database = $config->config_data['external_db_name'];
			$db->User = $config->config_data['external_db_user'];
			$db->Password = $config->config_data['external_db_password'];

			try
			{
				$db->connect();
			}
			catch(Exception $e)
			{
				$GLOBALS['phpgw']->redirect_link('/home.php');
			}
			return $db;
		}
    	
        /**
         * Method for retrieving the result units this user has access to
         * 
         * @param string $username the username
         * @return an array of (result unit number => result unit name)
         */
        public function get_result_units(array $usernames)
        {
        	/* 1. Get all organizational units this user has access to
             * 2. Check level for each unit and traverse if necessary
             * 3. Build an array of result units this user has access to
             */
        	$result_units = array();
        	$org_unit_ids = array();
        	
        	foreach($usernames as $username)
        	{
	        	$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NIVAA, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.ENHET_ID, V_ORG_ENHET.RESULTATENHET";
	        	$table = "V_ORG_ENHET";
	        	$joins = 	"LEFT JOIN V_ORG_PERSON_ENHET ON (V_ORG_PERSON_ENHET.ORG_ENHET_ID = V_ORG_ENHET.ORG_ENHET_ID) ".
	        				"LEFT JOIN V_ORG_PERSON ON (V_ORG_PERSON.ORG_PERSON_ID = V_ORG_PERSON_ENHET.ORG_PERSON_ID)";
	        	
	        	$sql = "SELECT $columns FROM $table $joins WHERE V_ORG_PERSON.BRUKERNAVN = '$username'";
	        	
	        	$db = $this->get_db();
				$db1 = $this->get_db();
	        	$db->query($sql,__LINE__,__FILE__);
	        	
	        	
	        	
	       		while ($db->next_record())
				{
					$identifier  = (int)$db->f('ORG_ENHET_ID');
					$level = (int)$db->f('ORG_NIVAA','int');
					$name = $db->f('ORG_NAVN');
					$unit_id = $db->f('RESULTATENHET');
					
					switch($level)
					{
						case 1: break;	// TODO: Access to all result units
						case 2: 		// LEVEL: Byrådsavdeling 
							//Must traverse down the hierarchy
							$columns = "V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NIVAA, V_ORG_ENHET.ORG_NAVN, V_ORG_ENHET.ENHET_ID, V_ORG_ENHET.RESULTATENHET";
							$tables = "V_ORG_ENHET";
							$joins = "LEFT JOIN V_ORG_KNYTNING ON (V_ORG_KNYTNING.ORG_ENHET_ID = V_ORG_ENHET.ORG_ENHET_ID)";
							$sql = "SELECT $columns FROM $tables $joins WHERE V_ORG_ENHET.ORG_NIVAA = 4 AND V_ORG_KNYTNING.ORG_ENHET_ID_KNYTNING = {$identifier}";
							
	        				$db1->query($sql,__LINE__,__FILE__);
	        				while ($db1->next_record())
							{
								if(!isset($org_unit_ids[(int)$db1->f('ORG_ENHET_ID')]))
								{
									$result_units[] = array(
										"ORG_UNIT_ID" => (int)$db1->f('ORG_ENHET_ID'),
										"ORG_NAME" => $db1->f('ORG_NAVN'),
										"UNIT_ID" => $db1->f('RESULTATENHET')
									);
									
									$org_unit_ids[(int)$db1->f('ORG_ENHET_ID')] = true;
								}
							}
							break;
						case 3:	break;	// LEVEL: Seksjon (not in use)
						case 4:			// LEVEL: Resultatenhet
							//Insert in result array
							if(!isset($org_unit_ids[$identifier]))
							{	
								$result_units[] = array(
									"ORG_UNIT_ID" => $identifier,
									"ORG_NAME" => $name,
									"UNIT_ID" => $unit_id
								);
								$org_unit_ids[$identifier] = true;
							}
							break;	
					}
				}
        	}
        	return $result_units;
        	/*
			return array(
				array(
					"ORG_UNIT_ID" => 1,
					"ORG_NAME" => "Seksjon informasjon",
					"UNIT_ID" => "0130"
				),
				array(
					"ORG_UNIT_ID" => 1,
					"ORG_NAME" => "Byrådsleders avdeling, stab",
					"UNIT_ID" => "0133"
    			)
			);
			*/
        }
        
        /**
         * 
         * @param int $number the result unit number
         */
        public function get_organisational_unit_name($number) {
        	$sql = "SELECT V_ORG_ENHET.ORG_NAVN FROM V_ORG_ENHET WHERE V_ORG_ENHET.RESULTATENHET = $number";
        	$db = $this->get_db();
        	$db->query($sql,__LINE__,__FILE__);
        	if($db->num_rows() > 0)
        	{
        		$db->next_record();
        		return 	$db->f('ORG_NAVN', true);
        	} 
        	else
        	{
        		return lang('no_name_organisational_unit');
        	}
        	//return "No name";
        }
        
        /**
         * Get user info from Fellesdata based on a username
         * 
         * @param $username the username in question
         * @return an array containing username, firstname, lastname and email if user exist, false otherwise
         */
        public function get_user(string $username)
        {
        	$sql = "SELECT BRUKERNAVN, FORNAVN, ETTERNAVN, EPOST FROM V_AD_BRUKERE WHERE BRUKERNAVN = '{$username}'";
        	$db = $this->get_db();
        	$db->query($sql,__LINE__,__FILE__);
        	if($db->num_rows() > 0)
        	{
        		$db->next_record();
        		return array(
        		 	'username' 	=> $db->f('BRUKERNAVN', true),
        			'firstname'	=> $db->f('FORNAVN', true),
        			'lastname'	=> $db->f('ETTERNAVN', true),
        			'email'		=> $db->f('EPOST', true)
        		);
        	} 
        	else
        	{
        		return false;
        	}		
        }
    }
