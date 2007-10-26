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
 	* @version $Id: class.socategory.inc.php,v 1.21 2007/01/26 14:53:46 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_socategory
	{
		function property_socategory()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->like			= $this->bocommon->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				if ($data['start'])
				{
					$start=$data['start'];
				}
				else
				{
					$start=0;
				}
				$query		= (isset($data['query'])?$data['query']:'');
				$sort		= (isset($data['sort'])?$data['sort']:'DESC');
				$order		= (isset($data['order'])?$data['order']:'');
				$type		= (isset($data['type'])?$data['type']:'');
				$type_id		= (isset($data['type_id'])?$data['type_id']:'');
				$allrows	= (isset($data['allrows'])?$data['allrows']:'');
			}

			if(!$type)
			{
				return;
			}
			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by id asc';
			}

			$table = $this->select_table($type,$type_id);

			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " where id $this->like '%$query%' or descr $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

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
				$category[] = array
				(
					'id'	=> $this->db->f('id'),
					'descr'	=> stripslashes($this->db->f('descr'))
				);
			}
			return $category;
		}


		function select_table($type,$type_id)
		{

			switch($type)
			{
				case 'dim_b':
					$table='fm_ecodimb';
					break;
				case 'dim_d':
					$table='fm_ecodimd';
					break;
				case 'tax':
					$table='fm_ecomva';
					break;
				case 'voucher_cat':
					$table='fm_ecobilag_category';
					break;
				case 'voucher_type':
					$table='fm_ecoart';
					break;
				case 'tender_chapter':
					$table='fm_chapter';
					break;
				case 'ticket':
					$table='fm_tts_category';
					break;
				case 'request':
					$table='fm_workorder_category';
					break;
				case 'project':
					$table='fm_workorder_category';
					break;
				case 'wo':
					$table='fm_workorder_category';
					break;
				case 'location':
					$table='fm_location' . $type_id . '_category';
					break;
				case 'meter':
					$table='fm_meter_category';
					break;
				case 'document':
					$table='fm_document_category';
					break;
				case 'owner':
					$table='fm_owner_category';
					break;
				case 'tenant':
					$table='fm_tenant_category';
					break;
				case 'vendor':
					$table='fm_vendor_category';
					break;
				case 'district':
					$table='fm_district';
					break;
				case 'street':
					$table='fm_streetaddress';
					break;
				case 's_agreement':
					$table='fm_s_agreement_category';
					break;
				case 'tenant_claim':
					$table='fm_tenant_claim_category';
					break;
				case 'wo_hours':
					$table='fm_wo_hours_category';
					break;
				case 'r_condition_type':
					$table='fm_request_condition_type';
					break;
				case 'r_agreement':
					$table='fm_r_agreement_category';
					break;
				case 'b_account':
					$table='fm_b_account_category';
					break;
				case 'branch':
					$table='fm_branch';
					break;

			}

			return $table;
		}


		function read_single($id,$type,$type_id)
		{

			$table = $this->select_table($type,$type_id);

			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$category['id']		= $this->db->f('id');
				$category['descr']	= stripslashes($this->db->f('descr'));

				return $category;
			}
		}


		function select_category_list($data)
		{
			$table = $this->select_table($data['type'],$data['type_id']);
			if($data['order'] == 'id')
			{
				$order='id';
			}
			else
			{
				$order='descr';
			}
			
			$this->db->query("SELECT id, descr FROM $table ORDER BY $order");

			while ($this->db->next_record())
			{
				$categories[] = array(
					'id'	=> $this->db->f('id'),
					'name'	=> stripslashes($this->db->f('descr'))
					);
			}
			return (isset($categories)?$categories:false);
		}


		function add($category,$type,$type_id)
		{
			$table = $this->select_table($type,$type_id);

			$category['descr'] = $this->db->db_addslashes($category['descr']);

			$this->db->query("INSERT INTO $table (id, descr) "
				. "VALUES ('" . $category['id'] . "','" . $category['descr']. "')",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('category has been saved'));
			return $receipt;
		}

		function edit($category,$type,$type_id)
		{

			$table = $this->select_table($type,$type_id);

			$category['descr'] = $this->db->db_addslashes($category['descr']);

			$this->db->query("UPDATE $table set descr='" . $category['descr']
							. "' WHERE id='" . $category['id']. "'",__LINE__,__FILE__);


			$receipt['message'][]=array('msg'=>lang('category has been edited'));
			return $receipt;
		}

		function delete($id,$type,$type_id)
		{
			$table = $this->select_table($type,$type_id);

			$this->db->query("DELETE FROM $table WHERE id='" . $id . "'",__LINE__,__FILE__);
		}
	}
?>
