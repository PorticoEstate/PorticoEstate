<?php
	/**
	 * phpGroupWare - sms: A SMS Gateway
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package sms
	 * @subpackage sms
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package sms
	 */
	$entity_id = 1;
	$cat_id = 2;
	$id_field = 'securitnet_nr';
	$dateformat = "Y-m-d"; //postgres
	$target_field = 'last_alarm';

//	$entity_table = 'fm_entity_' . $entity_id . '_' . $cat_id;

	$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".entity.{$entity_id}.{$cat_id}");

	$this->db->query("SELECT id FROM phpgw_cust_attribute WHERE column_name = '$target_field' AND location_id = $location_id", __LINE__, __FILE__);

	$this->db->next_record();
	$attrib_id = $this->db->f('id');

	$param = explode(' ', $command_param);

	if (ctype_digit($param[0]) && $location_id)
	{
		$id = $this->db->db_addslashes($param[0]);
		//	$status = $status_code[$param[1]];

//		$sql = "SELECT id as record_id FROM $entity_table WHERE $id_field='$id'";
		$sql = "SELECT id as record_id FROM fm_bim_item"
			. " WHERE location_id = {$location_id}"
			. " AND json_representation->>'securitnet_nr' = '$id'";

		$this->db->query($sql, __LINE__, __FILE__);
		if ($this->db->next_record())
		{
			$record_id = $this->db->f('record_id');
			$date = date($dateformat, time());

			$sql = "UPDATE fm_bim_item SET json_representation=jsonb_set(json_representation, '{last_alarm}', '\"{$date}\"', true)"
				. " WHERE location_id = {$location_id}"
				. " AND json_representation->>'securitnet_nr' = '$id'";
			$this->db->query($sql, __LINE__, __FILE__);
			$historylog = CreateObject('property.historylog', 'entity_' . $entity_id . '_' . $cat_id);
			// temporary - fix this
			$historylog->account = 6;
			$historylog->add('SO', $record_id, $date, False, $attrib_id);
			$command_output = 'success';
		}
	}
