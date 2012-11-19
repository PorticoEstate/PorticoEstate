<?php

	/*
	* This class will update classification records baed on input.
	*/
	$systemoversikt = new ikt_systemoversikt();
	$systemoversikt->set_classification($values,$values_attribute,$entity_id,$cat_id,$receipt);

	class ikt_systemoversikt extends property_boentity
	{
		protected $db;

		function __construct()
		{
			parent::__construct();
			$this->db 		= & $GLOBALS['phpgw']->db;
			if($this->acl_location != '.entity.5.1')
			{
				throw new Exception("'ikt_systemoversikt'  is intended for location = '.entity.5.1'");
			}

		}

		function set_classification($values,$values_attribute,$entity_id,$cat_id,$receipt)
		{

			$value_set = array();
			$value_set['konf_rangering']			= 0;
			$value_set['integritet_rangering']		= 0;
			$value_set['tilgjengelighet_rangering']	= 0;

			foreach($values_attribute as $entry)
			{
				$konf_rangering =  $entry['value'];
				switch($entry['name'])
				{
					case 'konf_1':
						if($entry['value'] && $value_set['konf_rangering'] < 1)
						{
							$value_set['konf_rangering'] = 1;
						}
						break;
					case 'konf_2':
						if($entry['value'] && $value_set['konf_rangering'] < 2)
						{
							$value_set['konf_rangering'] = 2;
						}
						break;
					case 'konf_3':
						if($entry['value'] && $value_set['konf_rangering'] < 3)
						{
							$value_set['konf_rangering'] = 3;
						}
						break;
					case 'konf_4':
						if($entry['value'] && $value_set['konf_rangering'] < 4)
						{
							$value_set['konf_rangering'] = 4;
						}
						break;

					case 'integritet_1':
						if($entry['value'] && $value_set['integritet_rangering'] < 1)
						{
							$value_set['integritet_rangering'] = 1;
						}
						break;
					case 'integritet_2':
						if($entry['value'] && $value_set['integritet_rangering'] < 2)
						{
							$value_set['integritet_rangering'] = 2;
						}
						break;
					case 'integritet_3':
						if($entry['value'] && $value_set['integritet_rangering'] < 3)
						{
							$value_set['integritet_rangering'] = 3;
						}
						break;
					case 'integritet_4':
						if($entry['value'] && $value_set['integritet_rangering'] < 4)
						{
							$value_set['integritet_rangering'] = 4;
						}
						break;
					case 'tilgjengelighet_1':
						if($entry['value'] && $value_set['tilgjengelighet_rangering'] < 1)
						{
							$value_set['tilgjengelighet_rangering'] = 1;
						}
						break;
					case 'tilgjengelighet_2':
						if($entry['value'] && $value_set['tilgjengelighet_rangering'] < 2)
						{
							$value_set['tilgjengelighet_rangering'] = 2;
						}
						break;
					case 'tilgjengelighet_3':
						if($entry['value'] && $value_set['tilgjengelighet_rangering'] < 3)
						{
							$value_set['tilgjengelighet_rangering'] = 3;
						}
						break;
					case 'tilgjengelighet_4':
						if($entry['value'] && $value_set['tilgjengelighet_rangering'] < 4)
						{
							$value_set['tilgjengelighet_rangering'] = 4;
						}
						break;
				}
			}

			$value_set	= $this->db->validate_update($value_set);

			$sql = "UPDATE fm_entity_5_1 SET {$value_set} WHERE id =" . (int)$receipt['id'];

			$this->db->query($sql,__LINE__,__FILE__);
		}
	}
