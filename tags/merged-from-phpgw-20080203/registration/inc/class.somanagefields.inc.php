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
			$this->db = $GLOBALS['phpgw']->db;

			$this->db_fields = array ('field_name', 'field_text', 'field_type', 'field_values', 'field_required', 'field_order');
		}

		function update_field ($field_info)
		{
			if ($field_info['field_values'])
			{
				$field_info['field_values'] = base64_encode (serialize ($field_info['field_values']));
			}

			$sql = "UPDATE phpgw_reg_fields SET ";

			reset ($this->db_fields);
			while (list ($num, $field) = each ($this->db_fields))
			{
				if ($num)
				{
					$sql .= ", ";
				}
				$sql .= "$field='$field_info[$field]'";
			}

			$sql .= " WHERE field_name='$field_info[field_name]'";
			$rv = $this->db->query ($sql);

			return $rv;
		}

		function insert_field ($field_info)
		{
			if ($field_info['field_values'])
			{
				$field_info['field_values'] = base64_encode (serialize ($field_info['field_values']));
			}

			$sql = "INSERT INTO phpgw_reg_fields (";
			$sql2 = "(";

			reset ($this->db_fields);
			while (list ($num, $field) = each ($this->db_fields))
			{
				if ($num)
				{
					$sql .= ', ';
					$sql2 .= ', ';
				}
				$sql .= $field;
				$sql2 .= "'$field_info[$field]'";
			}

			$sql  .= ') VALUES ';
			$sql2 .= ')';

			$sql .= $sql2;

			$rv = $this->db->query ($sql);

			return $rv;
		}

		function remove_field ($field_info)
		{
			$rv = $this->db->query ("DELETE FROM phpgw_reg_fields WHERE field_name='$field_info[field_name]'");

			return $rv;
		}

		function get_field_list ()
		{
			$sql = "SELECT ";

			reset ($this->db_fields);
			while (list ($num, $db_field_name) = each ($this->db_fields))
			{
				if ($num)
				{
					$sql .= ', ';
				}
				$sql .= $db_field_name;
			}

			$sql .= " FROM phpgw_reg_fields ORDER BY field_order";
			$this->db->query ($sql);
			while ($this->db->next_record ())
			{
				$rarray[] = $this->db->Record;
			}

			return $rarray;
		}
	}
?>
