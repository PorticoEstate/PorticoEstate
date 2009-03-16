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
			$this->custom 	= createObject('property.custom_fields');
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

			$uicols = array();
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= lang('id');
			$uicols['datatype'][]		= $this->location_info['id']['type'] == 'varchar' ? 'V' : 'I';

			foreach($this->location_info['fields'] as $field)
			{
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= $field['name'];
				$uicols['descr'][]			= $field['descr'];
				$uicols['datatype'][]		= 'V';
			}
			
			if($GLOBALS['phpgw']->locations->get_attrib_table('property', $this->location_info['acl_location']))
			{

				$choice_table = 'phpgw_cust_choice';
				$attribute_table = 'phpgw_cust_attribute';
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->location_info['acl_location']);
				$attribute_filter = " location_id = {$location_id}";

				$this->_db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter ORDER BY attrib_sort ASC");

				$i	= count($uicols['name']);
				while ($this->_db->next_record())
				{
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= $this->_db->f('column_name');
					$uicols['descr'][]			= $this->_db->f('input_text');
					$uicols['statustext'][]		= $this->_db->f('statustext');
					$uicols['datatype'][$i]		= $this->_db->f('datatype');
					$uicols['attib_id'][$i]		= $this->_db->f('id');
					$cols_return_extra[]= array(
						'name'	=> $this->_db->f('column_name'),
						'datatype'	=> $this->_db->f('datatype'),
						'attrib_id'	=> $this->_db->f('id')
					);

					$i++;
				}
			}

			$this->uicols = $uicols;

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

			$cols_return = $uicols['name'];
			$j=0;
			$n=count($cols_return);

			$dataset = array();
			while ($this->_db->next_record())
			{
				foreach($cols_return as $key => $field)
				{
					$dataset[$j][$field] = array
					(
						'value'		=> $this->_db->f($field),
						'datatype'	=> $uicols['datatype'][$key],
						'attrib_id'	=> $uicols['attib_id'][$key]
					);
				}
				$j++;				
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			return $values;
		}


		function get_location_info($type,$type_id)
		{
			$type_id = (int)$type_id;
			$info = array();
			switch($type)
			{
//-------- ID type integer
				case 'project_group':
					$info = array
					(
						'table' => 'fm_project_group',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> '',
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::project_group'
					);
					break;
				case 'dimb':
					$info = array
					(
						'table' => 'fm_ecodimb',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('dimb'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dimb'
					);
					break;
				case 'dimd':
					$info = array
					(
						'table' => 'fm_ecodimd',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('dimd'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_dimd'
					);
					break;
				case 'tax':
					$info = array
					(
						'table' => 'fm_ecomva',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
							'id'				=> array('name' => 'id', 'type' => 'int'),
							'fields'			=> array
							(
								array
								(
									'name' => 'descr',
										'descr' => lang('descr'),
								'type' => 'varchar'
								)
							),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
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
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit'),
						'add_msg'	=> lang('add'),
						'name'		=> lang('budget account'),
						'acl_location' => '.admin',
						'menu_selection' => 'admin::property::accounting::accounting_cats'
					);
					break;
				case 'ecoorg_unit':
					$info = array
					(
						'table' => 'fm_ecoorg_unit',
						'id'				=> array('name' => 'id', 'type' => 'int'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'	=> lang('edit unit'),
						'add_msg'	=> lang('add unit'),
						'name'		=> lang('Accounting organisation unit'),
						'acl_location' => '.invoice.org_unit',
						'menu_selection' => 'admin::property::accounting::org_unit'
					);
					break;
//-------- ID type varchar
				case 'workorder_status':
					$info = array
					(
						'table' 			=> 'fm_workorder_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('workorder status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::workorder_status'
					);
					break;
				case 'request_status':
					$info = array
					(
						'table' 			=> 'fm_request_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('request status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::request_status'
					);
					break;
				case 'agreement_status':
					$info = array
					(
						'table' 			=> 'fm_agreement_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('agreement status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::agreement::agreement_status'
					);
					break;
				case 'building_part':
					$info = array
					(
						'table' 			=> 'fm_building_part',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('building part'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::building_part'
					);
					break;
				case 'document_status':
					$info = array
					(
						'table' 			=> 'fm_document_status',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit status'),
						'add_msg'			=> lang('add status'),
						'name'				=> lang('document status'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::document_status'
					);
					break;
				case 'unit':
					$info = array
					(
						'table' 			=> 'fm_standard_unit',
						'id'				=> array('name' => 'id', 'type' => 'varchar'),
						'fields'			=> array
						(
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit unit'),
						'add_msg'			=> lang('add unit'),
						'name'				=> lang('unit'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::unit'
					);
					break;
//-------- ID type auto
				case 'branch':
					$info = array
					(
						'table' 			=> 'fm_branch',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'varchar'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('branch'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::branch'
					);

					break;
				case 'key_location':
					$info = array
					(
						'table' 			=> 'fm_key_loc',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'num',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('key location'),
								'type' => 'text'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('branch'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::key_location'
					);

					break;

				case 'async':
					$info = array
					(
						'table' 			=> 'fm_async_method',
						'id'				=> array('name' => 'id', 'type' => 'auto'),
						'fields'			=> array
						(
							array
							(
								'name' => 'name',
								'descr' => lang('name'),
								'type' => 'varchar'
							),
							array
							(
								'name' => 'data',
								'descr' => lang('data'),
								'type' => 'text'
							),
							array
							(
								'name' => 'descr',
								'descr' => lang('descr'),
								'type' => 'text'
							)
						),
						'edit_msg'			=> lang('edit'),
						'add_msg'			=> lang('add'),
						'name'				=> lang('Async services'),
						'acl_location' 		=> '.admin',
						'menu_selection'	=> 'admin::property::async'
					);
					break;
				default:
					$receipt = array();
					$receipt['error'][]=array('msg'=>lang('ERROR: illegal type %1', $type));
					phpgwapi_cache::session_set('phpgwapi', 'phpgw_messages', $receipt);
				//	throw new Exception(lang('ERROR: illegal type %1', $type));
			}

			$this->location_info = $info;
			return $info;
		}

		function read_single($data,$values = array())
		{
			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				return $values;
			}

			if($this->location_info['id']['type']=='auto' || $this->location_info['id']['type']=='int')
			{
				$id = (int) $data['id'];
			}
			else
			{
				$id = "'{$data['id']}'";
			}

			$sql = "SELECT * FROM $table WHERE id={$id}";

			$this->_db->query($sql,__LINE__,__FILE__);

			if ($this->_db->next_record())
			{
				$values['id'] = $this->_db->f($this->location_info['id']['name']);

				// FIXME - add field to $values['attributes']
				foreach($this->location_info['fields'] as $field)
				{
					$values[$field['name']] = $this->_db->f($field['name'], true);
				}

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->_db->f($attr['column_name']);
					}
				}
			}
			return $values;
		}


		function select_category_list($data)
		{
			$values = array();

			$this->get_location_info($data['type'], $data['type_id']);
			
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


		function add($data,$values_attribute)
		{
			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			if(isset($data['save']))
			{
				unset($data['save']);
			}
			if(isset($data['apply']))
			{
				unset($data['apply']);
			}
			$cols = array();
			$vals = array();
						
			$data['descr'] = $this->_db->db_addslashes($data['descr']);

			if(isset($data['extra']))
			{
				foreach ($data['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}
			unset($data['extra']);

			foreach ($data as $input_name => $value)
			{
				if(isset($value) && $value)
				{
					$cols[] = $input_name;
					$vals[] = $this->_db->db_addslashes($value);
				}
			}

			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute);
			if(isset($data_attribute['value_set']))
			{
				foreach($data_attribute['value_set'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}


			$this->_db->transaction_begin();

			if($this->location_info['id']['type']!='auto')
			{
				$this->_db->query("SELECT id FROM {$table} WHERE id = '{$data['id']}'",__LINE__,__FILE__);
				if($this->_db->next_record())
				{
					$receipt['error'][]=array('msg'=>lang('duplicate key value'));
					$receipt['error'][]=array('msg'=>lang('record has not been saved'));
					return $receipt;
				}
				$id = $data['id'];
			}
			else
			{
				$id = $this->_db->next_id($table);
				$cols[] = 'id';
				$vals[] = $id;
			}

			$cols	= implode(",", $cols);
			$vals	= $this->_db->validate_insert($vals);

			$this->_db->query("INSERT INTO {$table} ({$cols}) VALUES ({$vals})",__LINE__,__FILE__);

/*			if($this->location_info['id']['type']=='auto')
			{
				if(!$data['id'] = $this->_db->get_last_insert_id($table, 'id'))
				{
					$this->_db->transaction_abort();				
					$receipt['error'][]=array('msg'=>lang('record has not been saved'));
				}
			}
*/
			$this->_db->transaction_commit();
			$receipt['id'] = $id;
			$receipt['message'][]=array('msg'=>lang('record has been saved'));
			return $receipt;
		}

		function edit($data,$values_attribute)
		{
			$receipt = array();

			if (!isset($this->location_info['table']) || !$table = $this->location_info['table'])
			{
				$receipt['error'][] = array('msg' => lang('not a valid type'));
				return $receipt;
			}

			$value_set = array();

			if(isset($data['extra']))
			{
				foreach ($data['extra'] as $input_name => $value)
				{
					$value_set[$input_name] = $value;
				}
				unset($data['extra']);
			}

			$data_attribute = $this->custom->prepare_for_db($table, $values_attribute, $data['id']);

			if(isset($data_attribute['value_set']))
			{
				$value_set = array_merge($value_set, $data_attribute['value_set']);
			}
			foreach($this->location_info['fields'] as $field)
			{
				$value_set[$field['name']] = $this->_db->db_addslashes($data[$field['name']]);
			}

			$value_set	= $this->_db->validate_update($value_set);
			$this->_db->transaction_begin();
			$this->_db->query("UPDATE $table SET {$value_set} WHERE id='" . $data['id']. "'",__LINE__,__FILE__);

/*			//FIXME
			if (isset($data_attribute['history_set']) && is_array($data_attribute['history_set']))
			{
				$historylog	= CreateObject('phpgwapi.historylog','property', $this->location_info['acl_location']);
				foreach ($data_attribute['history_set'] as $attrib_id => $history)
				{
					$historylog->add('SO',$data['id'],$history['value'],false, $attrib_id,$history['date']);
				}
			}
*/
			$this->_db->transaction_commit();

			$receipt['id'] = $data['id'];

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

