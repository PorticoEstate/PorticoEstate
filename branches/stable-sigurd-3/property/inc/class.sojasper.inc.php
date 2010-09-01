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

	class property_sojasper
	{
		function __construct()
		{
			$this->account	= 	$GLOBALS['phpgw_info']['user']['account_id'];
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->like			= & $this->db->like;
		}

		function read($data)
		{
			$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id asc';
			}

			$table = 'fm_jasper';

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " WHERE id $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$jasper = array();
			while ($this->db->next_record())
			{
				$jasper[] = array
				(
					'id'				=> $this->db->f('id'),
					'descr'				=> $this->db->f('descr',true),
					'location_id'		=> $this->db->f('location_id'),
					'title'				=> $this->db->f('title',true),
					'file_name'			=> $this->db->f('file_name',true),
					'version'			=> $this->db->f('version'),
					'user_id'			=> $this->db->f('user_id'),
					'access'			=> $this->db->f('access'),
					'entry_date'		=> $this->db->f('entry_date'),
					'modified_by'		=> $this->db->f('modified_by'),
					'modified_date'		=> $this->db->f('modified_date')
				);
			}
			return $jasper;
		}

		function read_single($id)
		{

			$table = 'fm_jasper';

			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			$jasper = array();
			if ($this->db->next_record())
			{
				$jasper['id']		= $this->db->f('id');
				$jasper['descr']		= $this->db->f('descr');
				$jasper['cat_id']		= $this->db->f('category');
				$jasper['responsible']	= $this->db->f('responsible');
			}
			return $jasper;
		}

		function add($jasper)
		{
			$table = 'fm_jasper';

			$jasper['descr'] = $this->db->db_addslashes($jasper['descr']);

			$this->db->query("INSERT INTO $table (id, descr,category,responsible) "
				. "VALUES ('" . $jasper['id'] . "','" . $jasper['descr']. "','" .$jasper['cat_id'] . "','" . $jasper['responsible'] . "')",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('budget account %1 has been saved',$jasper['id']));
			return $receipt;
		}

		function edit($jasper)
		{

			$table = 'fm_jasper';

			$jasper['descr'] = $this->db->db_addslashes($jasper['descr']);

			$this->db->query("UPDATE $table set"
					. " descr='" . $jasper['descr'] . "',"
					. "responsible=" . $jasper['responsible'] . ","
					. "category=" . (int)$jasper['cat_id']
					. " WHERE id='" . $jasper['id']. "'",__LINE__,__FILE__);


			$receipt['message'][]=array('msg'=>lang('budget account %1 has been edited',$jasper['id']));
			return $receipt;
		}

		function delete($id)
		{
			$table = 'fm_jasper';

			$this->db->query("DELETE FROM $table WHERE id='" . $id . "'",__LINE__,__FILE__);
		}

		public function get_input_type_list($selected)
		{
			$this->db->query('SELECT * FROM fm_jasper_input_type',__LINE__,__FILE__);

			$input_types = array();
			while ($this->db->next_record())
			{
				$input_types[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name',true)
				);
			}
			return $input_types;

		}

	}
