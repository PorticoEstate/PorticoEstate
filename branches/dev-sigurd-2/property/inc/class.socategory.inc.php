<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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

	class property_socategory
	{
		var $location_info = array();

		function __construct()
		{
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->_db		= & $GLOBALS['phpgw']->db;
			$this->_like	= & $this->_db->like;
			$this->_join	= & $this->_db->join;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start']:0;
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$allrows	= isset($data['allrows'])?$data['allrows']:'';
			}

			$values = array();
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY id ASC';
			}

			if($query)
			{
				$query = $this->_db->db_addslashes($query);
				$querymethod = " WHERE descr $this->_like '%$query%'";
			}

			$sql = "SELECT * FROM $table $querymethod";

			$this->_db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->_db->num_rows();

			if(!$allrows)
			{
				$this->_db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->_db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			while ($this->_db->next_record())
			{
				$values[] = array
				(
					'id'	=> $this->_db->f('id'),
					'descr'	=> $this->_db->f('descr',true)
				);
			}
			return $values;
		}


		function get_location_info($type,$type_id)
		{
			$type_id = (int)$type_id;
			$info = array();
			switch($type)
			{
				case 'project_group':
					$info = array
					(
						'table' => 'fm_project_group',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::project_group'
					);
					break;
				case 'dim_b':
					$info = array
					(
						'table' => 'fm_ecodimb',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dim_b'
					);
					break;
				case 'dim_d':
					$info = array
					(
						'table' => 'fm_ecodimd',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dim_d'
					);
					break;
				case 'tax':
					$info = array
					(
						'table' => 'fm_ecomva',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_tax'
					);
					break;
				case 'voucher_cat':
					$info = array
					(
						'table' => 'fm_ecobilag_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::voucher_cats'
					);
					break;
				case 'voucher_type':
					$info = array
					(
						'table' => 'fm_ecoart',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::voucher_type'
					);
					break;
				case 'tender_chapter':
					$info = array
					(
						'table' => 'fm_chapter',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tender'
					);
					break;
				case 'location':

					$this->_db->query("SELECT id FROM fm_location_type WHERE id ={$type_id}",__LINE__,__FILE__);

					if($this->_db->next_record())
					{
						$info = array
						(
							'table' => "fm_location{$type_id}_category",
							'edit_msg'	=> lang('edit'),
							'add_msg'	=> lang('add'),
							'name'		=> '',
							'acl_location' => '.admin',
							'menu_selection' => "admin::property::location::location::category_{$type_id}"
						);
					}
					else
					{
						throw new Exception(lang('ERROR: illegal type %1', $type_id));
					}
					break;
				case 'owner':
					$info = array
					(
						'table' => 'fm_owner_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::owner::owner_cats'
					);
					break;
				case 'tenant':
					$info = array
					(
						'table' => 'fm_tenant_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('tenant category'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tenant::tenant_cats'
					);
					break;
				case 'vendor':
					$info = array
					(
						'table' => 'fm_vendor_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('vendor category'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::vendor::vendor_cats'
					);
					break;
				case 'district':
					$info = array
					(
						'table' => 'fm_district',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('district'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::district'
					);
					break;
				case 'street':
					$info = array
					(
						'table' => 'fm_streetaddress',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('streetaddress'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::location::street'
					);
					break;
				case 's_agreement':
					$info = array
					(
						'table' => 'fm_s_agreement_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::agreement::service_agree_cats'
					);
					break;
				case 'tenant_claim':
					$info = array
					(
						'table' => 'fm_tenant_claim_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::tenant::claims_cats'
					);
					break;
				case 'wo_hours':
					$info = array
					(
						'table' => 'fm_wo_hours_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::workorder_detail'
					);
					break;
				case 'r_condition_type':
					$info = array
					(
						'table' => 'fm_request_condition_type',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::request_condition'
					);
					break;
				case 'r_agreement':
					$info = array
					(
						'table' => 'fm_r_agreement_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::agreement::rental_agree_cats'
					);
					break;
				case 'b_account':
					$info = array
					(
						'table' => 'fm_b_account_category',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('budget account'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_cats'
					);
					break;
/*				case 'branch':
					$info = array
					(
						'table' => 'fm_branch',
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection'	=>''
					);
					break;
*/
				case 'ecoorg_unit':
					$info = array
					(
						'table' => 'fm_ecoorg_unit',
						'edit_msg'	=> lang('edit unit'),
						'add_msg'	=> lang('add unit'),
						'name'		=> lang('Accounting organisation unit'),
						'acl_location' => '.invoice.org_unit',
						'menu_selection' => 'admin::property::accounting::org_unit'
					);
					break;
				default:
					throw new Exception(lang('ERROR: illegal type %1', $type));
			}

			$this->location_info = $info;
			return $info;
		}

		function read_single($data,$values = array())
		{
			$id = (int) $data['id'];
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

			$sql = "SELECT * FROM $table WHERE id={$id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			if ($this->_db->next_record())
			{
				$values = array
				(
					'id'	=> $this->_db->f('id'),
					'descr'	=> $this->_db->f('descr', true)
				);

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}
			}
			return $values;
		}

/*
		function select_category_list($data)
		{
			$values = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}
			$order		= isset($data['order']) && $data['order'] == 'id' ? 'id' :'descr';

			$this->_db->query("SELECT id, descr FROM $table ORDER BY $order");

			while ($this->_db->next_record())
			{
				$values[] = array
				(
					'id'	=> $this->_db->f('id'),
					'name'	=> $this->_db->f('descr', true)
				);
			}
			return $values;
		}
*/

		function add($data)
		{
			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			$this->_db->query("SELECT id from {$table} WHERE id = {$data['id']}",__LINE__,__FILE__);
			if($this->_db->next_record())
			{
				$receipt['error'][]=array('msg'=>lang('duplicate key value'));
				$receipt['error'][]=array('msg'=>lang('record has not been saved'));
				return $receipt;
			}

			$data['descr'] = $this->_db->db_addslashes($data['descr']);

			$this->_db->query("INSERT INTO $table (id, descr) "
				. "VALUES ('" . $data['id'] . "','" . $data['descr']. "')",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('record has been saved'));
			return $receipt;
		}

		function edit($data)
		{
			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			$data['descr'] = $this->_db->db_addslashes($data['descr']);

			$this->_db->query("UPDATE $table set descr='" . $data['descr']
							. "' WHERE id='" . $data['id']. "'",__LINE__,__FILE__);


			$receipt['message'][]=array('msg'=>lang('record has been edited'));
			return $receipt;
		}

		function delete($id)
		{
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return false;
			}
			$this->_db->query("DELETE FROM $table WHERE id='{$id}'",__LINE__,__FILE__);
		}
	}

