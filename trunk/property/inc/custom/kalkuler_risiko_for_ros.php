<?php

//_debug_array($values);
//_debug_array($values_attribute);
//_debug_array($action);

		// this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example

		$db = $this->bocommon->new_db();


		if (isSet($values_attribute) AND is_array($values_attribute))
		{

			foreach($values_attribute as $entry)
			{
				switch($entry['name'])
				{
					case 'sansynlighet':
						$sansynlighet = (int)$entry['value'];
						break;
				}
			}

			reset($values_attribute);

			foreach($values_attribute as $entry)
			{
				switch($entry['name'])
				{
					case 'k_beboer':
						$value_set['r_beboer']	= $entry['value'] * $sansynlighet;
						break;
					case 'k_miljo':
						$value_set['r_miljo']	= $entry['value'] * $sansynlighet;
						break;
					case 'k_ok_verdier':
						$value_set['r_ok_verdier']	= $entry['value'] * $sansynlighet;
						break;
					case 'k_drift':
						$value_set['r_drift']	= $entry['value'] * $sansynlighet;
						break;
					case 'k_ansatte':
						$value_set['r_ansatte']	= $entry['value'] * $sansynlighet;
						break;
					case 'k_annet':
						$value_set['r_annet']	= $entry['value'] * $sansynlighet;
						break;
				}
			}

			$value_set	= $this->bocommon->validate_db_update($value_set);
			$db->transaction_begin();
			$db->query("UPDATE fm_entity_2_6 set $value_set WHERE id=" . $values['id'],__LINE__,__FILE__);
			$db->transaction_commit();

		}


