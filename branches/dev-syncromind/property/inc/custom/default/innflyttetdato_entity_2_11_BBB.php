<?php

//_debug_array($values);
//_debug_array($values_attribute);
//_debug_array($action);

		// this routine will only work with the exact configuration of Bergen Bolig og Byfornyelse - but can serve as an example

		$db = & $GLOBALS['phpgw']->db;

		$sql = "SELECT innflyttet FROM fm_entity_2_11 WHERE location_code ='" . $values['location_code'] . "'";
		$db->query($sql,__LINE__,__FILE__);
		$db->next_record();
		$innflyttetdato_old = $db->f('innflyttet');

		$sql = "SELECT innflyttetdato, tenant_id FROM fm_location4 WHERE location_code ='" . $values['location_code'] . "'";
		$db->query($sql,__LINE__,__FILE__);
		$db->next_record();
		$innflyttetdato = $db->f('innflyttetdato');
		$tenant_id = $db->f('tenant_id');

		if($tenant_id == $values['extra']['tenant_id'] && !$innflyttetdato_old)
		{
			$value_set['innflyttet']	= $innflyttetdato;
			$value_set	= $db->validate_update($value_set);
			$db->transaction_begin();
			$db->query("UPDATE fm_entity_2_11 set $value_set WHERE id=" . (int) $receipt['id'],__LINE__,__FILE__);
			$db->transaction_commit();
		}


