<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This file written by Jason Wies (Zone) <zone@users.sourceforge.net>      *
	* Based on calendar/inc/class.soholiday.inc.php by Mark Peters             *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class somanagefields
	{
		var $debug = False;
		var $db;
		var $db_fields;

		function somanagefields()
		{
			$this->db = & $GLOBALS['phpgw']->db;

			$this->db_fields = array ('field_name', 'field_text', 'field_type', 'field_values', 'field_required', 'field_order');
		}

		function update_field ($field_info)
		{

			if ($field_info['field_values'])
			{
				$field_info['field_values'] = base64_encode (serialize ($field_info['field_values']));
			}

			$value_set	= $this->db->validate_update($field_info);

			return $this->db->query("UPDATE phpgw_reg_fields SET {$value_set} WHERE field_name='{$field_info[field_name]}'",__LINE__,__FILE__);
		}

		function insert_field ($field_info)
		{
			if ($field_info['field_values'])
			{
				$field_info['field_values'] = base64_encode (serialize ($field_info['field_values']));
			}

			$sql = "INSERT INTO phpgw_reg_fields (" . implode(', ', array_keys($field_info)) .  ') VALUES (' . $this->db->validate_insert(array_values($field_info)) . ')';
			$rv = $this->db->query($sql,__LINE__,__FILE__);

			return $rv;
		}

		function remove_field ($field_info)
		{
			$rv = $this->db->query ("DELETE FROM phpgw_reg_fields WHERE field_name='{$field_info[field_name]}'");

			return $rv;
		}

		function get_field_list ()
		{
			$rarray = array();
			$sql = 'SELECT  ' . implode(', ', $this->db_fields) . ' FROM phpgw_reg_fields ORDER BY field_order';
			$this->db->query ($sql);
			while ($this->db->next_record ())
			{
				$rarray[] = $this->db->Record;
			}

			return $rarray;
		}
	}
