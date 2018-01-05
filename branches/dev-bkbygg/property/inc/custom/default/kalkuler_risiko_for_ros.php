<?php
//_debug_array($values);
//_debug_array($values_attribute);
//_debug_array($action);
	// this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example

	$db = & $GLOBALS['phpgw']->db;

	if (isSet($values_attribute) AND is_array($values_attribute))
	{

		foreach ($values_attribute as $entry)
		{
			switch ($entry['name'])
			{
				case 'sansynlighet':
					$sansynlighet = (int)$entry['value'];
					break;
			}
		}

		reset($values_attribute);

		$value_set['r_tverrfaglig'] = 0;

		foreach ($values_attribute as $entry)
		{
			$risk = $entry['value'] * $sansynlighet;
			switch ($entry['name'])
			{
				case 'k_beboer':
					$value_set['r_beboer'] = $risk;
					if ($risk > $value_set['r_tverrfaglig'])
					{
						$value_set['r_tverrfaglig'] = $risk;
					}
					break;
				case 'k_miljo':
					$value_set['r_miljo'] = $risk;
					if ($risk > $value_set['r_tverrfaglig'])
					{
						$value_set['r_tverrfaglig'] = $risk;
					}
					break;
				case 'k_ok_verdier':
					$value_set['r_ok_verdier'] = $risk;
					if ($risk > $value_set['r_tverrfaglig'])
					{
						$value_set['r_tverrfaglig'] = $risk;
					}
					break;
				case 'k_drift':
					$value_set['r_drift'] = $risk;
					if ($risk > $value_set['r_tverrfaglig'])
					{
						$value_set['r_tverrfaglig'] = $risk;
					}
					break;
				case 'k_ansatte':
					$value_set['r_ansatte'] = $risk;
					if ($risk > $value_set['r_tverrfaglig'])
					{
						$value_set['r_tverrfaglig'] = $risk;
					}
					break;
				case 'k_annet':
					$value_set['r_annet'] = $risk;
					if ($risk > $value_set['r_tverrfaglig'])
					{
						$value_set['r_tverrfaglig'] = $risk;
					}
					break;
			}
		}

		$db->transaction_begin();

		$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.entity.2.6');

//		$sql = "UPDATE fm_entity_2_6 set $value_set WHERE id=" . (int)$receipt['id'];

		foreach ($value_set as $_key => $_value)
		{
			$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{{$_key}}', '\"{$_value}\"', true)"
				. " WHERE location_id = {$location_id}"
				. " AND id=" . (int)$receipt['id'];
			$db->query($sql, __LINE__, __FILE__);
		}

		$db->transaction_commit();
	}
