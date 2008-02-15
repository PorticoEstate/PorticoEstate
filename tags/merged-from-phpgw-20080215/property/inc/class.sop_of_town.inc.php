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

	class property_sop_of_town
	{

		function property_sop_of_town()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->like			= $this->bocommon->like;
		}

		function read_district_name($id)
		{
			$this->db->query("SELECT descr FROM fm_district  where id='$id'");
			$this->db->next_record();
			return $this->db->f('descr');
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start	= (isset($data['start'])?$data['start']:0);
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$district_id = (isset($data['district_id'])?$data['district_id']:0);
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by part_of_town_id ASC';
			}


			$where = 'WHERE';
			if ($district_id > 0)
			{
				$filtermethod .= " $where district_id='$district_id' ";
				$where = 'AND';

			}

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " $where ( name $this->like '%$query%')";
			}

			$sql = "SELECT fm_part_of_town.*, descr as category FROM fm_part_of_town $this->join fm_district on fm_part_of_town.district_id=fm_district.id $filtermethod $querymethod";

			$this->db2->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db2->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->db->next_record())
			{
				$p_of_towns[] = array
				(
					'part_of_town_id'	=> $this->db->f('part_of_town_id'),
					'name'				=> stripslashes($this->db->f('name')),
					'category'			=> stripslashes($this->db->f('category')),
					'district_id'		=> $this->db->f('district_id')
				);
			}
			return $p_of_towns;
		}

		function read_single($part_of_town_id)
		{
			$this->db->query("select * from fm_part_of_town where part_of_town_id='$part_of_town_id'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$p_of_town['id']			= (int)$this->db->f('part_of_town_id');
				$p_of_town['name']			= stripslashes($this->db->f('name'));
				$p_of_town['district_id']	= (int)$this->db->f('district_id');

				return $p_of_town;
			}
		}

		function add($p_of_town)
		{
			$p_of_town['name'] = $this->db->db_addslashes($p_of_town['name']);

			$this->db->query("INSERT INTO fm_part_of_town (name,district_id) "
				. "VALUES ('" . $p_of_town['name']
				. "','" . $p_of_town['district_id'] . "')",__LINE__,__FILE__);

			$receipt['part_of_town_id']= $this->db->get_last_insert_id('fm_part_of_town','part_of_town_id');
			$receipt['message'][] = array('msg'=>lang('Part of town %1 has been saved',$receipt['part_of_town_id']));
			return $receipt;
		}

		function edit($p_of_town)
		{
			$p_of_town['name'] = $this->db->db_addslashes($p_of_town['name']);

			$this->db->query("UPDATE fm_part_of_town set name='" . $p_of_town['name'] . "', district_id='"
							. $p_of_town['district_id'] . "' WHERE part_of_town_id=" . intval($p_of_town['part_of_town_id']),__LINE__,__FILE__);

			$receipt['part_of_town_id']= $p_of_town['part_of_town_id'];
			$receipt['message'][] = array('msg'=>lang('Part of town %1 has been edited',$p_of_town['part_of_town_id']));
			return $receipt;
		}

		function delete($part_of_town_id)
		{
			$this->db->query('DELETE FROM fm_part_of_town WHERE part_of_town_id=' . intval($part_of_town_id),__LINE__,__FILE__);
		}
	}
?>
