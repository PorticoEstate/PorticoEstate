<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage admin
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soadmin
	{
		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}

		function get_initials($id)
		{
			$this->db->query("SELECT initials FROM fm_ecouser WHERE id=$id ");
			$this->db->next_record();
			return $this->db->f('initials');
		}

		function set_initials($initials)
		{
			while (is_array($initials) && list($account_id,$value) = each($initials))
			{
				$this->db->query("UPDATE fm_ecouser set initials= '$value' WHERE id=$account_id ",__LINE__,__FILE__);
				if($value)
				{
					if(!$this->get_initials($account_id))
					{
						$account_lid	= $GLOBALS['phpgw']->accounts->id2lid($account_id);
						$this->db->query("INSERT INTO fm_ecouser (id,lid,initials) VALUES ($account_id,'$account_lid','$value' )",__LINE__,__FILE__);
					}
				}
			}
		}


		function read_fm_id()
		{
			$sql = "SELECT * FROM fm_idgenerator ORDER BY descr asc";
			$this->db->query($sql,__LINE__,__FILE__);

			$fm_ids = array();
			while ($this->db->next_record())
			{
				$fm_ids[] = array
				(
					'name'		=> $this->db->f('name'),
					'descr'		=> $this->db->f('descr'),
					'value'		=> $this->db->f('value'),
				);

			}

			return $fm_ids;
		}

		function edit_id($values='')
		{
			$field=$values['field'];
			$select=$values['select'];

			while($entry=each($select))
			{
				$n=$entry[0];

				$sql = "update  fm_idgenerator set value='$values[$n]' where name='$field[$n]'";
				$this->db->query($sql,__LINE__,__FILE__);
			}

			$receipt['message'][] = array('msg' => lang('ID is updated'));
			return $receipt;
		}
	}

