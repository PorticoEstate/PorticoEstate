<?php

	/*
	* This class will update classification records baed on input.
	*/

	class ikt_systemoversikt extends property_boentity
	{

		function __construct()
		{
			parent::__construct();
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
			unset($entry);

			reset($values_attribute);

			foreach($values_attribute as &$entry)
			{
				if($value_set[$entry['name']])
				{
					$entry['value'] = $value_set[$entry['name']];
				}
			}

			$_values = $values;
			$_values['id'] = (int)$receipt['id'];
			$this->so->edit($_values,$values_attribute,$entity_id,$cat_id);
		}
	}

	$systemoversikt = new ikt_systemoversikt();
	$systemoversikt->set_classification($values,$values_attribute,$entity_id,$cat_id,$receipt);

