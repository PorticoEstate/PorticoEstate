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
 	* @version $Id: class.soadmin_custom.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soadmin_custom
	{

		function property_soadmin_custom()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();
			$this->join		= $this->bocommon->join;
			$this->like		= $this->bocommon->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$query = (isset($data['start'])?$data['start']:0);
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$acl_location = (isset($data['acl_location'])?$data['acl_location']:'');
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by custom_sort asc';
			}

			$table = 'fm_custom_function';

			$filtermethod = " WHERE acl_location='$acl_location'";
			$where = 'AND';

			$querymethod = '';
			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " $where file_name $this->like '%$query%' or descr $this->like '%$query%'";
			}


			$sql = "SELECT * FROM $table $filtermethod $querymethod";

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
				$custom_function[] = array
				(
					'id'		=> $this->db->f('id'),
					'acl_location'	=> $this->db->f('acl_location'),
					'file_name'	=> $this->db->f('file_name'),
					'sorting'	=> $this->db->f('custom_sort'),
					'descr'		=> $this->db->f('descr'),
					'active'	=> $this->db->f('active')
				);
			}
			return (isset($custom_function)?$custom_function:'');
		}


		function read_single_custom_function($id,$acl_location)
		{

			$sql = "SELECT * FROM fm_custom_function where acl_location='$acl_location' AND id=$id";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$custom_function['id']			= $this->db->f('id');
				$custom_function['descr']		= $this->db->f('descr');
				$custom_function['custom_function_file']= $this->db->f('file_name');
				$custom_function['active']		= $this->db->f('active');

				return $custom_function;
			}

		}

		function add_custom_function($custom_function)
		{
			if(!$custom_function['acl_location'])
			{
				return 	$receipt['error'][] = array('msg' => lang('acl_locastion is missing'));
			}

			$acl_location = $custom_function['acl_location'];
			
			$custom_function['descr'] = $this->db->db_addslashes($custom_function['descr']);


			$this->db->query("SELECT max(id) as maximum FROM fm_custom_function WHERE acl_location='$acl_location'",__LINE__,__FILE__);
			$this->db->next_record();
			$custom_function['id'] = $this->db->f('maximum')+1;

			$sql = "SELECT max(custom_sort) as max_sort FROM fm_custom_function where acl_location='$acl_location'";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('max_sort')+1;

			$values= array(
				$acl_location,
				$custom_function['id'],
				$custom_function['custom_function_file'],
				$custom_function['descr'],
				$custom_function['active'],
				$custom_sort
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO fm_custom_function (acl_location, id, file_name, descr, active, custom_sort) "
				. "VALUES ($values)",__LINE__,__FILE__);

			$receipt['id']= $custom_function['id'];

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit_custom_function($custom_function)
		{
			$acl_location = $custom_function['acl_location'];

			if(!$acl_location)
			{
				return 	$receipt['error'][] = array('msg' => lang('acl_locastion is missing'));
			}

			$custom_function['descr'] = $this->db->db_addslashes($custom_function['descr']);

			$this->db->transaction_begin();

				$value_set=array(
					'descr'		=> $custom_function['descr'],
					'file_name'	=> $custom_function['custom_function_file'],
					'active'	=> $custom_function['active']
					);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE fm_custom_function set $value_set WHERE acl_location='" . $acl_location . "' AND id=" . $custom_function['id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'	=> lang('Custom function has been edited'));

			return $receipt;
		}

		function resort_custom_function($data)
		{
			if(is_array($data))
			{
				$resort = (isset($data['resort'])?$data['resort']:'up');
				$acl_location = (isset($data['acl_location'])?$data['acl_location']:'');
				$id = (isset($data['id'])?$data['id']:'');
			}

			if(!$acl_location)
			{
				return 	$receipt['error'][] = array('msg' => lang('acl_locastion is missing'));
			}

			$sql = "SELECT custom_sort FROM fm_custom_function where acl_location='$acl_location' AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('custom_sort');
			$sql2 = "SELECT max(custom_sort) as max_sort FROM fm_custom_function where acl_location='$acl_location'";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			switch($resort)
			{
				case 'up':
					if($custom_sort>1)
					{
						$sql = "UPDATE fm_custom_function set custom_sort=$custom_sort WHERE acl_location='$acl_location' AND custom_sort =" . ($custom_sort-1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_custom_function set custom_sort=" . ($custom_sort-1) ." WHERE acl_location='$acl_location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				case 'down':
					if($max_sort > $custom_sort)
					{
						$sql = "UPDATE fm_custom_function set custom_sort=$custom_sort WHERE acl_location='$acl_location' AND custom_sort =" . ($custom_sort+1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE fm_custom_function set custom_sort=" . ($custom_sort+1) ." WHERE acl_location='$acl_location' AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				default:
					return;
					break;
			}
		}

		function delete_custom_function($id,$acl_location)
		{
			$sql = "SELECT custom_sort FROM fm_custom_function where acl_location='$acl_location' AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$custom_sort	= $this->db->f('custom_sort');
			$sql2 = "SELECT max(custom_sort) as max_sort FROM fm_custom_function where acl_location='$acl_location'";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');
			if($max_sort>$custom_sort)
			{
				$sql = "UPDATE fm_custom_function set custom_sort=custom_sort-1 WHERE acl_location='$acl_location' AND custom_sort > $custom_sort";
				$this->db->query($sql,__LINE__,__FILE__);
			}
			$this->db->query("DELETE FROM fm_custom_function WHERE acl_location='$acl_location' AND id=$id",__LINE__,__FILE__);
		}
	}
?>
