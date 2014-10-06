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
	* @subpackage agreement
 	* @version $Id$
	*/

	phpgw::import_class('phpgwapi.datetime');

	/**
	 * Description
	 * @package property
	 */

	class property_sos_agreement
	{
		var $role;
		var $uicols = array();

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->socommon		= CreateObject('property.socommon');
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->db2          = clone($this->db);

			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}

		function select_vendor_list()
		{
			$table = 'fm_s_agreement';
			$this->db->query("SELECT vendor_id,org_name FROM $table $this->join fm_vendor on fm_s_agreement.vendor_id=fm_vendor.id GROUP BY org_name,vendor_id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$vendor[$i]['id']				= $this->db->f('vendor_id');
				$vendor[$i]['name']				= stripslashes($this->db->f('org_name'));
				$i++;
			}
			return $vendor;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start			= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$filter			= isset($data['filter'])?$data['filter']:'none';
				$query 			= isset($data['query'])?$data['query']:'';
				$sort 			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
				$order			= isset($data['order'])?$data['order']:'';
				$cat_id			= isset($data['cat_id'])?$data['cat_id']:'';
				$vendor_id		= isset($data['vendor_id'])?$data['vendor_id']:'';
				$allrows		= isset($data['allrows'])?$data['allrows']:'';
				$member_id		= isset($data['member_id']) && $data['member_id'] ? $data['member_id'] : 0;
				$s_agreement_id	= isset($data['s_agreement_id'])?$data['s_agreement_id']:'';
				$detail			= isset($data['detail'])?$data['detail']:'';
				$p_num			= isset($data['p_num']) ? $data['p_num'] : '';
				$status_id		= isset($data['status_id']) && $data['status_id'] ? (int)$data['status_id']:0;
				$location_code	= isset($data['location_code'])?$data['location_code']:'';
			}

			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';

			if(!$detail)
			{
				$entity_table = 'fm_s_agreement';
				$category_table = 'fm_s_agreement_category';
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.s_agreement'); 
				$attribute_filter = " location_id = {$location_id}";

				$paranthesis ='(';
				$joinmethod = " {$this->join} {$category_table} ON ( $entity_table.category =$category_table.id))";
				$paranthesis .='(';
				$joinmethod .= " {$this->left_join} fm_vendor ON ( $entity_table.vendor_id =fm_vendor.id))";
				$paranthesis .='(';
				$joinmethod .= " {$this->left_join} fm_s_agreement_detail ON ( fm_s_agreement.id = fm_s_agreement_detail.agreement_id))";

				$cols = $entity_table . ".*,$category_table.descr as category, org_name";

				$cols_return[] 				= 'id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'id';
				$uicols['descr'][]			= lang('ID');
				$uicols['statustext'][]		= lang('ID');

				$cols_return[] 				= 'name';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'name';
				$uicols['descr'][]			= lang('name');
				$uicols['statustext'][]		= lang('name');

				$cols_return[] 				= 'org_name';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'org_name';
				$uicols['descr'][]			= lang('vendor');
				$uicols['statustext'][]		= lang('vendor');

				$cols_return[] 				= 'category';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'category';
				$uicols['descr'][]			= lang('category');
				$uicols['statustext'][]		= lang('category');

				$cols_return[] 				= 'start_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'start_date';
				$uicols['descr'][]			= lang('start');
				$uicols['statustext'][]		= lang('start date');

				$cols_return[] 				= 'termination_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'termination_date';
				$uicols['descr'][]			= lang('termination date');
				$uicols['statustext'][]		= lang('termination date');
	//			$uicols['datatype'][]		= 'D';

				$cols_return[] 				= 'end_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'end_date';
				$uicols['descr'][]			= lang('end');
				$uicols['statustext'][]		= lang('end date');

			}
			else
			{
				$query = '';
				$allrows=true;
				$entity_table = 'fm_s_agreement_detail';
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.s_agreement.detail'); 
				$attribute_filter = " location_id = {$location_id}";

				$paranthesis .='(';
				$joinmethod .= " {$this->join}  fm_s_agreement_pricing ON ( $entity_table.agreement_id =fm_s_agreement_pricing.agreement_id AND {$entity_table}.id =fm_s_agreement_pricing.item_id))";

				$cols = "$entity_table.*, fm_s_agreement_pricing.cost,fm_s_agreement_pricing.id as index_count,fm_s_agreement_pricing.index_date,fm_s_agreement_pricing.item_id,fm_s_agreement_pricing.this_index";

				$cols_return[] 				= 'agreement_id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'agreement_id';
				$uicols['descr'][]			= lang('agreement_id');
				$uicols['statustext'][]		= lang('agreement_id');
				$uicols['import'][]			= false;

				$cols_return[] 				= 'item_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'item_id';
				$uicols['descr'][]			= lang('ID');
				$uicols['statustext'][]		= lang('ID');
				$uicols['import'][]			= false;

				$cols_return[] 				= 'id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'id';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;
				$uicols['import'][]			= false;

				$cols_return[] 				= 'location_code';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'location_code';
				$uicols['descr'][]			= lang('location');
				$uicols['statustext'][]		= lang('location');
				$uicols['import'][]			= true;

				$cols_return[] 				= 'address';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'address';
				$uicols['descr'][]			= lang('address');
				$uicols['statustext'][]		= lang('address');
				$uicols['import'][]			= true;

				$cols_return[] 				= 'p_entity_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'p_entity_id';
				$uicols['descr'][]			= 'entity_id';
				$uicols['statustext'][]		= false;
				$uicols['import'][]			= true;

				$cols_return[] 				= 'p_cat_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'p_cat_id';
				$uicols['descr'][]			= 'cat_id';
				$uicols['statustext'][]		= false;
				$uicols['import'][]			= true;

				$cols_return[] 				= 'p_num';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'p_num';
				$uicols['descr'][]			= lang('entity num');
				$uicols['statustext'][]		= lang('entity num');
				$uicols['import'][]			= true;

				$cols_return[] 				= 'cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'cost';
				$uicols['descr'][]			= lang('cost');
				$uicols['statustext'][]		= lang('cost');
				$uicols['import'][]			= true;

				$cols_return[] 				= 'this_index';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'this_index';
				$uicols['descr'][]			= lang('index');
				$uicols['statustext'][]		= lang('index');
				$uicols['import'][]			= false;

				$cols_return[] 				= 'index_count';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'index_count';
				$uicols['descr'][]			= lang('index_count');
				$uicols['statustext'][]		= lang('index_count');
				$uicols['import'][]			= false;

				$cols_return[] 				= 'index_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'index_date';
				$uicols['descr'][]			= lang('date');
				$uicols['statustext'][]		= lang('date');
				$uicols['import'][]			= false;
			}

			$sql = "SELECT DISTINCT $cols FROM $paranthesis $entity_table $joinmethod";

			$i	= count($uicols['name']);

			$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['s_agreement_columns' . !!$s_agreement_id]) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['s_agreement_columns' . !!$s_agreement_id] :'';
			$user_column_filter = '';
			if (is_array($user_columns) && $user_columns[0])
			{
				$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',',$user_columns) .'))';
			}

			$this->db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter $user_column_filter ");

			while ($this->db->next_record())
			{
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= $this->db->f('column_name');
				$uicols['descr'][]			= $this->db->f('input_text');
				$uicols['statustext'][]		= $this->db->f('statustext');
				$uicols['datatype'][$i]		= $this->db->f('datatype');
				$uicols['import'][]			= true;
				$cols_return_extra[]= array(
					'name'	=> $this->db->f('column_name'),
					'datatype'	=> $this->db->f('datatype'),
					'attrib_id'	=> $this->db->f('id')
				);

				$i++;
			}

			$this->uicols	= $uicols;

			if(!$s_agreement_id > 0 && $detail)
			{
				return;
			}

			if ($order)
			{
				if(!$detail)
				{
					switch ($order)
					{
						case 'id':
						case 'status':
							$ordermethod = " ORDER BY {$entity_table}.{$order} {$sort}";
							break;
						case 'category':
							$ordermethod = " ORDER BY {$category_table}.descr {$sort}";					
							break;
						default:
							$ordermethod = " ORDER BY {$order} {$sort}";
					}
				}
				else
				{
					switch ($order)
					{
						case 'id':
							$ordermethod = " ORDER BY {$entity_table}.{$order} {$sort}";
							break;
					}
				}
			}
			else
			{
				$ordermethod = " ORDER BY {$entity_table}.id DESC";
			}


			$filtermethod = '';
			$where= 'WHERE';

/*			if ($filter=='all')
			{
				if (is_array($grants))
				{
					while (list($user) = each($grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where ( $entity_table.user_id IN(" . implode(',',$public_user_list) . "))";

					$where= 'AND';
				}

			}
			else
			{
				$filtermethod = " $where $entity_table.user_id=$filter ";
				$where= 'AND';
			}
 */

			if ($s_agreement_id)
			{
				$filtermethod .= " $where $entity_table.agreement_id=$s_agreement_id AND current_index = 1";
				$where= 'AND';
			}

			if ($location_code)
			{
				$filtermethod .= " $where location_code {$this->like} '{$location_code}%'";
				$where= 'AND';
			}

			if ($cat_id && !$detail)
			{
				$filtermethod .= " $where $entity_table.category='$cat_id' ";
				$where= 'AND';
			}

			if ($vendor_id && !$detail)
			{
				$filtermethod .= " $where $entity_table.vendor_id='$vendor_id' ";
				$where= 'AND';
			}

			if ($member_id > 0  && !$detail)
			{
				$filtermethod .= " $where fm_vendor.member_of {$this->like} '%,$member_id,%' ";
				$where= 'AND';
			}

			if (!$detail && $status_id)
			{
				$filtermethod .= " $where $entity_table.status='$status_id' ";
				$where= 'AND';
			}


			$_querymethod = array();
			$__querymethod = array();
			$_joinmethod_datatype = array();
			$_joinmethod_datatype_custom = array();

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);

				if($p_num)
				{
					$query=explode(".",$query);
					$querymethod = " {$where} (fm_s_agreement_detail.p_entity_id='" . (int)$query[1] . "' AND fm_s_agreement_detail.p_cat_id='" . (int)$query[2] . "' AND fm_s_agreement_detail.p_num='{$query[3]}')";
					$where = 'AND';
				}
				else
				{
					$query_arr = array();
					$this->db->query("SELECT * FROM $attribute_table WHERE search='1' AND $attribute_filter");

					while ($this->db->next_record())
					{
						switch ($this->db->f('datatype'))
						{
							case 'V':
							case 'email':
							case 'T':
								if(!$criteria_id)
								{
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " {$this->like} '%{$query}%'";
									$__querymethod = array(); // remove block
								}
								break;
							case 'CH':
								if(!$criteria_id)
								{
									// from filter
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " {$this->like} '%,{$query},%'";
									$__querymethod = array(); // remove block

									// from text-search
									$_filter_choise = "WHERE (phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										." AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id')
										." AND phpgw_cust_choice.value {$this->like} '%{$query}%')";

									$this->db2->query("SELECT phpgw_cust_choice.id FROM phpgw_cust_choice {$_filter_choise}",__LINE__,__FILE__);
									while ($this->db2->next_record())
									{
										$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " {$this->like} '%,". $this->db2->f('id') . ",%'";
									}
								}
								break;
							case 'R':
							case 'LB':
								if(!$criteria_id)
								{
									$_filter_choise = "WHERE (phpgw_cust_choice.location_id =" . (int)$this->db->f('location_id')
										." AND phpgw_cust_choice.attrib_id =" . (int)$this->db->f('id')
										." AND phpgw_cust_choice.value {$this->like} '%{$query}%')";

									$this->db2->query("SELECT phpgw_cust_choice.id FROM phpgw_cust_choice {$_filter_choise}",__LINE__,__FILE__);
									$__filter_choise = array();
									while ($this->db2->next_record())
									{
										$__filter_choise[] = $this->db2->f('id');
									}

									if($__filter_choise)
									{
										$_querymethod[]= "$entity_table." . $this->db->f('column_name') . ' IN (' . implode(',', $__filter_choise) . ')';
									}
	
									$__querymethod = array(); // remove block
								}
								break;
							case 'I':
								if(ctype_digit($query) && !$criteria_id)
								{
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " = " . (int)$query;
									$__querymethod = array(); // remove block
								}
								break;
							case 'VENDOR':
								if($criteria_id == 'vendor')
								{
									$_joinmethod_datatype[] = "{$this->join} fm_vendor ON ({$entity_table}." . $this->db->f('column_name') . " = fm_vendor.id AND fm_vendor.org_name {$this->like} '%{$query}%') ";
									$__querymethod = array(); // remove block
								}
								break;
							case 'AB':
								if($criteria_id == 'ab')
								{
									$_joinmethod_datatype[] = "{$this->join} phpgw_contact_person ON ({$entity_table}." . $this->db->f('column_name') . " = pphpgw_contact_person.person_id AND (phpgw_contact_person.first_name {$this->like} '%{$query}%' OR phpgw_contact_person.last_name {$this->like} '%{$query}%'))";
									$__querymethod = array(); // remove block
								}
								break;
							case 'ABO':
								if($criteria_id == 'abo')
								{
									$_joinmethod_datatype[] = "{$this->join} phpgw_contact_org ON ({$entity_table}." . $this->db->f('column_name') . " = phpgw_contact_org.org_id AND phpgw_contact_org.name {$this->like} '%{$query}%')";
									$__querymethod = array(); // remove block
								}
								break;
							default:
								if(!$criteria_id)
								{
									$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " = '{$query}'";
									$__querymethod = array(); // remove block
								}
						}
					}
				}
			}


			$_joinmethod_datatype = array_merge($_joinmethod_datatype, $_joinmethod_datatype_custom);
			foreach($_joinmethod_datatype as $_joinmethod)
			{
				$sql .= $_joinmethod;
			}

			$_querymethod = array_merge($__querymethod, $_querymethod);
			if ($_querymethod)
			{
				$querymethod = " $where (" . implode (' OR ',$_querymethod) . ')';
				unset($_querymethod);
			}

			$sql .= " $filtermethod $querymethod";
//			echo $sql;

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

			$j=0;
			$n=count($cols_return);
			//_debug_array($cols_return);

			$contacts			= CreateObject('phpgwapi.contacts');

			$s_agreement_list = array();
			while ($this->db->next_record())
			{
				for ($i=0;$i<$n;$i++)
				{
					$s_agreement_list[$j][$cols_return[$i]] = stripslashes($this->db->f($cols_return[$i]));
				//	$s_agreement_list[$j]['grants'] = (int)$grants[$this->db->f('user_id')];
				}

				if(isset($cols_return_extra) && is_array($cols_return_extra))
				{
					foreach ($cols_return_extra as $return_extra)
					{
						$value='';
						$value=$this->db->f($return_extra['name']);

						if(($return_extra['datatype']=='R' || $return_extra['datatype']=='LB') && $value)
						{
							$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .$return_extra['attrib_id']. "  AND id=" . $value;
							$this->db2->query($sql);
							$this->db2->next_record();
							$s_agreement_list[$j][$return_extra['name']] = $this->db2->f('value');
						}
						else if($return_extra['datatype']=='AB' && $value)
						{
							$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
							$s_agreement_list[$j][$return_extra['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
						}
						else if($return_extra['datatype']=='VENDOR' && $value)
						{
							$sql="SELECT org_name FROM fm_vendor where id=$value";
							$this->db2->query($sql);
							$this->db2->next_record();
							$s_agreement_list[$j][$return_extra['name']] = $this->db2->f('org_name');
						}
						else if($return_extra['datatype']=='CH' && $value)
						{
							$ch = explode(',', trim($value, ','));
							if (isset($ch) AND is_array($ch))
							{
								for ($k=0;$k<count($ch);$k++)
								{
									$sql="SELECT value FROM $choice_table WHERE  $attribute_filter AND attrib_id=" .$return_extra['attrib_id']. "  AND id=" . $ch[$k];
									$this->db2->query($sql);
									while ($this->db2->next_record())
									{
										$ch_value[]=$this->db2->f('value');
									}
								}
								$s_agreement_list[$j][$return_extra['name']] = @implode(",", $ch_value);
								unset($ch_value);
							}
						}
						else if($return_extra['datatype']=='D' && $value)
						{
							$s_agreement_list[$j][$return_extra['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
						}
						else if($cols_return_extra[$i]['datatype']=='timestamp' && $value)
						{
							$s_agreement_list[$j][$return_extra['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$value);
						}
						else if($cols_return_extra[$i]['datatype']=='link' && $value)
						{
							$s_agreement_list[$j][$return_extra['name']]= phpgw::safe_redirect($value);
						}
						else
						{
							$s_agreement_list[$j][$return_extra['name']]=$value;
						}
					}
				}
				$j++;
			}
//_debug_array($s_agreement_list);
			return $s_agreement_list;
		}

		function read_prizing($data)
		{
			if(is_array($data))
			{
				$s_agreement_id	= (isset($data['s_agreement_id'])?$data['s_agreement_id']:0);
				$item_id	= (isset($data['item_id'])?$data['item_id']:0);
			}

			$entity_table = 'fm_s_agreement_pricing';

			$cols = "fm_s_agreement_pricing.cost,fm_s_agreement_pricing.id as index_count,fm_s_agreement_pricing.index_date,fm_s_agreement_pricing.item_id,fm_s_agreement_pricing.this_index";

			$cols_return[] 				= 'item_id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'item_id';
			$uicols['descr'][]			= lang('ID');
			$uicols['statustext'][]		= lang('ID');

			$cols_return[] 				= 'id';
			$uicols['input_type'][]		= 'hidden';
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= false;
			$uicols['statustext'][]		= false;

			$cols_return[] 				= 'cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'cost';
			$uicols['descr'][]			= lang('cost');
			$uicols['statustext'][]		= lang('cost');

			$cols_return[] 				= 'this_index';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'this_index';
			$uicols['descr'][]			= lang('index');
			$uicols['statustext'][]		= lang('index');

			$cols_return[] 				= 'index_count';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'index_count';
			$uicols['descr'][]			= lang('index_count');
			$uicols['statustext'][]		= lang('index_count');

			$cols_return[] 				= 'index_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'index_date';
			$uicols['descr'][]			= lang('date');
			$uicols['statustext'][]		= lang('date');

			$sql = "SELECT $cols FROM $entity_table $joinmethod";

			$this->uicols	= $uicols;

			$ordermethod = " order by $entity_table.id ASC";

			$where= 'WHERE';


			if ($s_agreement_id)
			{
				$filtermethod .= " $where $entity_table.agreement_id=$s_agreement_id AND item_id=$item_id";
				$where= 'AND';
			}


			$sql .= " $filtermethod";
			//echo $sql;

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

			$j=0;
			$n=count($cols_return);
			//_debug_array($cols_return);
			while ($this->db->next_record())
			{
				for ($i=0;$i<$n;$i++)
				{
					$s_agreement_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$s_agreement_list[$j]['agreement_id'] = $s_agreement_id;
				}
				$j++;
			}
			//_debug_array($s_agreement_list);
			return $s_agreement_list;
		}


		function read_single($s_agreement_id, $values = array())
		{
			$table = 'fm_s_agreement';

			$sql = "SELECT fm_s_agreement.* FROM $table WHERE id='$s_agreement_id'";
			$this->db->query($sql);

			if($this->db->next_record())
			{
				$values['id']				= $this->db->f('id');
				$values['entry_date']		= $this->db->f('entry_date');
				$values['cat_id']			= $this->db->f('category');
				$values['member_of']		= explode(',',trim($this->db->f('member_of'),','));
				$values['cat_id']			= $this->db->f('category');
				$values['start_date']		= $this->db->f('start_date');
				$values['end_date']			= $this->db->f('end_date');
				$values['termination_date']	= $this->db->f('termination_date');
				$values['vendor_id']		= $this->db->f('vendor_id');
				$values['b_account_id']		= $this->db->f('account_id');
				$values['name']				= stripslashes($this->db->f('name'));
				$values['descr']			= stripslashes($this->db->f('descr'));
				$values['user_id']			= $this->db->f('user_id');

				if ( isset($values['attributes']) && is_array($values['attributes']) )
				{
					foreach ( $values['attributes'] as &$attr )
					{
						$attr['value'] 	= $this->db->f($attr['column_name']);
					}
				}

				$sql = "SELECT fm_s_agreement_budget.category as order_category, year, ecodimb,budget_account,budget"
					. " FROM fm_s_agreement_budget WHERE agreement_id='$s_agreement_id' AND fm_s_agreement_budget.year =" . date('Y');
				$this->db->query($sql);
				$this->db->next_record();

				$values['order_category']	= $this->db->f('order_category');
				$values['year']				= $this->db->f('year');
				$values['ecodimb']			= $this->db->f('ecodimb');
				$values['b_account_id']		= $this->db->f('budget_account');
				$values['budget']			= (int)$this->db->f('budget');
				$values['year']				= $this->db->f('year');
			}

			return $values;
		}

		function read_single_item($data, $values = array())
		{
			$table = 'fm_s_agreement_detail';

			$s_agreement_id =$data['s_agreement_id'];
			$id =$data['id'];

			$this->db->query("SELECT * from $table where agreement_id=$s_agreement_id AND id=" . (int)$id );

			if($this->db->next_record())
			{
				$values['agreement_id']		= $this->db->f('agreement_id');
				$values['id']				= (int)$this->db->f('id');
				$values['entry_date']		= $this->db->f('entry_date');
				$values['location_code']	= $this->db->f('location_code');
				$values['p_num']			= $this->db->f('p_num');
				$values['p_entity_id']		= $this->db->f('p_entity_id');
				$values['p_cat_id']			= $this->db->f('p_cat_id');
				$values['cost']				= $this->db->f('cost');
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

		function add($values,$values_attribute='')
		{
			//_debug_array($values);
			$table = 'fm_s_agreement';
			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			if($values['member_of'])
			{
				$member_of =  ',' . implode(',',$values['member_of']) . ',';
				$values['member_of']= $member_of;
			}

			$this->db->transaction_begin();
			$id = $this->socommon->increment_id('workorder');

			$vals = array();
			$vals[]	= $id;
			$vals[]	= $values['name'];
			$vals[]	= $values['descr'];
			$vals[]	= time();
			$vals[]	= $values['cat_id'];
			$vals[]	= $values['member_of'];
			$vals[]	= $values['start_date'];
			$vals[]	= $values['end_date'];
			$vals[]	= $values['termination_date'];
			$vals[]	= $values['vendor_id'];
			$vals[]	= $values['b_account_id'];
			$vals[]	= $this->account;

			if(isset($values['extra']) && is_array($values['extra']))
			{
				foreach ($values['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['value'])
					{
						$cols[]	= $entry['name'];
						$vals[]	= $entry['value'];
					}
				}
			}

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
			}

			$vals	= $this->db->validate_insert($vals);

			if(isset($values['budget']) && $values['budget'])
			{
				$_budget = array
					(
						'agreement_id'		=> $values['s_agreement_id'],
						'category'			=> $values['order_category'],
						'year'				=> (int)$values['year'],
						'ecodimb'			=> (int)$values['ecodimb'],
						'budget_account'	=> $values['b_account_id'],
						'budget'			=> $values['budget'],				
					);

				$this->update_budget($_budget);
			}

			$this->db->query("INSERT INTO $table (id,name,descr,entry_date,category,member_of,start_date,end_date,termination_date,vendor_id,account_id,user_id $cols) "
				. "VALUES ($vals)",__LINE__,__FILE__);

			$this->db->query("INSERT INTO fm_orders (id,type) VALUES ($id,'s_agreement')");

			if( $member_of && $values['vendor_id'])
			{
				$vendor_id = (int)$values['vendor_id'];
				$this->db->query("UPDATE fm_vendor SET member_of = '{$member_of}' WHERE id= {$vendor_id}");				
			}

			$receipt['s_agreement_id']= $id;//$this->db->get_last_insert_id($table,'id');

			$receipt['message'][] = array('msg'=>lang('s_agreement %1 has been saved',$receipt['s_agreement_id']));

			$this->db->transaction_commit();
			return $receipt;
		}

		function add_item($values,$values_attribute='')
		{
			$table = 'fm_s_agreement_detail';

			$cols[] = 'location_code';
			$vals[] = $values['location_code'];

/*			while (is_array($values['location']) && list($input_name,$value) = each($values['location']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
				}
			}
 */

			if(isset($values['extra']) && is_array($values['extra']))
			{
				foreach ($values['extra'] as $input_name => $value)
				{
					if(isset($value) && $value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['value'])
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}

						$cols[]	= $entry['name'];
						$vals[]	= $entry['value'];

						if($entry['history'] == 1)
						{
							$history_set[$entry['attrib_id']] = $entry['value'];
						}
					}
				}
			}

			if($values['street_name'])
			{
				$address[]= $values['street_name'];
				$address[]= $values['street_number'];
				$address = $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($values['location_name']);
			}

			$cols[]	= 'address';
			$vals[]	= $address;
			$cols[]	= 'cost';
			$vals[]	= $this->floatval($values['cost']);

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= "," . $this->db->validate_insert($vals);
			}

			$this->db->transaction_begin();

			$id = $this->db->next_id($table,array('agreement_id'=>$values['s_agreement_id']));

			$this->db->query("INSERT INTO $table (id,agreement_id,entry_date,user_id $cols) "
				. "VALUES ($id," . $values['s_agreement_id'] ."," . time()
				. "," . $this->account . " $vals)");


			$this->db->query("SELECT start_date FROM fm_s_agreement WHERE id=" . $values['s_agreement_id']);
			$this->db->next_record();
			$start_date	= $this->db->f('start_date');


			$this->db->query("INSERT INTO fm_s_agreement_pricing (agreement_id,item_id,id,current_index,this_index,cost,index_date,entry_date,user_id) "
				. "VALUES (" . $values['s_agreement_id'] . "," . $id .",1,1,1," . $this->floatval($values['cost']) . "," . (int)$start_date . "," . time()
				. "," . $this->account . ")");

			$receipt['s_agreement_id']= $values['s_agreement_id'];
			$receipt['id']= $id;

			$receipt['message'][] = array('msg'=>lang('s_agreement %1 has been saved',$receipt['s_agreement_id']));

			//---------- History

			if (isset($history_set) AND is_array($history_set))
			{
				$historylog	= CreateObject('property.historylog','s_agreement');
				while (list($attrib_id,$new_value) = each($history_set))
				{
					$historylog->add('SO',$values['s_agreement_id'],$new_value,false, $attrib_id,false,$id);
				}
			}

			//----------

			$this->db->transaction_commit();
			return $receipt;
		}

		function update_budget($data)
		{
			$sql = "SELECT * FROM fm_s_agreement_budget WHERE agreement_id = {$data['agreement_id']} AND year = {$data['year']}";
			$this->db->query($sql,__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$old_category	= $this->db->f('category');
				$old_ecodimb		= $this->db->f('ecodimb');
				$old_budget_account	= $this->db->f('budget_account');
				$old_budget			= $this->db->f('budget');
				$sql = "UPDATE fm_s_agreement_budget SET"
					. " category = {$data['category']},"
					. " ecodimb = {$data['ecodimb']},"
					. " budget_account = '{$data['budget_account']}',"
					. " budget = {$data['budget']},"
					. ' modified_date=' . time()
					. " WHERE agreement_id = {$data['agreement_id']} AND year = {$data['year']}";

			}
			else
			{
				$sql = "INSERT INTO fm_s_agreement_budget (agreement_id,year,category,ecodimb,budget_account,budget,user_id,entry_date) VALUES("
					. "{$data['agreement_id']},"
					. "{$data['year']},"
					. "{$data['category']},"
					. "{$data['ecodimb']},"
					. "'{$data['budget_account']}',"
					. "{$data['budget']},"
					. "{$this->account},"
					. time() . ')';
			}

			$this->db->query($sql,__LINE__,__FILE__);

		}

		function edit($values,$values_attribute = array())
		{
//			_debug_array($values);
//			_debug_array($values_attribute);

			$table = 'fm_s_agreement';

			$values['s_agreement_id'] = $this->db->db_addslashes($values['s_agreement_id']); // bigint
			$values['name'] = $this->db->db_addslashes($values['name']);

			if($values['member_of'])
			{
				$member_of =  ',' . implode(',',$values['member_of']) . ',';
				$values['member_of']= $member_of;
			}

			if(isset($values['extra']) && is_array($values['extra']))
			{
				foreach ($values['extra'] as $column => $value)
				{
					$value_set[$column]	= $value;
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR')
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$value_set[$entry['name']] = $this->db->db_addslashes($entry['value']);
						}
						else
						{
							$value_set[$entry['name']]	= $entry['value'];
						}
					}
				}
			}

			$value_set['name']	= $values['name'];
			$value_set['descr']	= $values['descr'];
			$value_set['vendor_id']	= $values['vendor_id'];

			if($value_set)
			{
				$value_set	= ',' . $this->db->validate_update($value_set);
			}

			$this->db->transaction_begin();
			if(isset($values['budget']) && $values['budget'])
			{
				$_budget = array
					(
						'agreement_id'		=> $values['s_agreement_id'],
						'category'			=> $values['order_category'],
						'year'				=> (int)$values['year'],
						'ecodimb'			=> (int)$values['ecodimb'],
						'budget_account'	=> $values['b_account_id'],
						'budget'			=> $values['budget'],				
					);

				$this->update_budget($_budget);
			}
			$this->db->query("UPDATE $table set entry_date='" . time() . "', category='"
				. $values['cat_id'] . "', member_of='" . $values['member_of'] . "', start_date=" . intval($values['start_date']) . ", end_date=" . intval($values['end_date']) . ", termination_date=" . intval($values['termination_date']) . ", account_id=" . intval($values['b_account_id']) . "$value_set WHERE id='{$values['s_agreement_id']}'");

			$this->db->query("UPDATE fm_s_agreement_pricing set index_date=" . intval($values['start_date']) . " WHERE id=1 AND agreement_id= '{$values['s_agreement_id']}'");

			if( $member_of && $values['vendor_id'])
			{
				$vendor_id = (int)$values['vendor_id'];
				$this->db->query("UPDATE fm_vendor SET member_of = '{$member_of}' WHERE id= {$vendor_id}");				
			}

			$this->db->transaction_commit();
			$receipt['s_agreement_id']= $values['s_agreement_id'];
			$receipt['message'][] = array('msg'=>lang('s_agreement %1 has been edited',$values['s_agreement_id']));
			return $receipt;
		}

		function edit_item($values,$values_attribute='')
		{
			//_debug_array($values);
			//_debug_array($values_attribute);
			$table = 'fm_s_agreement_detail';

			while (is_array($values['extra']) && list($column,$value) = each($values['extra']))
			{
				$value_set[$column]	= $value;
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR')
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
						{
							$value_set[$entry['name']] = $this->db->db_addslashes($entry['value']);
						}
						else
						{
							$value_set[$entry['name']]	= $entry['value'];
						}
					}

					if($entry['history'] == 1)
					{
						$this->db->query("SELECT " . $entry['name'] . " from $table WHERE agreement_id= " . $values['s_agreement_id'] . " AND id=" . $values['id'],__LINE__,__FILE__);
						$this->db->next_record();
						$old_value = $this->db->f($entry['name']);
						if($entry['value'] != $old_value)
						{
							$history_set[$entry['attrib_id']] = array('value' => $entry['value'],
								'date'  => phpgwapi_datetime::date_to_timestamp($entry['date']));
						}
					}
				}
			}

			if($values['street_name'])
			{
				$address[]= $values['street_name'];
				$address[]= $values['street_number'];
				$address	= $this->db->db_addslashes(implode(" ", $address));
			}

			if(!$address)
			{
				$address = $this->db->db_addslashes($values['location_name']);
			}

			$value_set['location_code']	= $values['location_code'];
			$value_set['cost']	= $values['cost'];
			$value_set['address']	= $address;

			if($value_set)
			{
				$value_set	= ',' . $this->db->validate_update($value_set);
			}

			$this->db->query("UPDATE $table set entry_date=" . time() . "$value_set WHERE agreement_id=" . ($values['s_agreement_id']) . ' AND id=' . intval($values['id']));

			$this->db->query("UPDATE fm_s_agreement_pricing set cost = this_index *" . $this->floatval($values['cost']) . " WHERE agreement_id=" . $values['s_agreement_id'] . ' AND item_id=' . intval($values['id']));

			if (isset($history_set) AND is_array($history_set))
			{
				$historylog	= CreateObject('property.historylog','s_agreement');
				foreach ($history_set as $attrib_id => $history)
				{
					$historylog->add('SO',$values['s_agreement_id'],$history['value'],false, $attrib_id,$history['date'],$values['id']);
				}
			}

			$receipt['s_agreement_id']= $values['s_agreement_id'];
			$receipt['id']= $values['id'];
			$receipt['message'][] = array('msg'=>lang('s_agreement %1 has been edited',$values['s_agreement_id']));

			return $receipt;
		}

		function update($values)
		{
			$values['new_index']=$this->floatval($values['new_index']);
			$this->db->transaction_begin();

			if(isset($values['select']) && is_array($values['select']))
			{
				foreach ($values['select'] as $item_id => $value)
				{
					$this->db->query("UPDATE fm_s_agreement_pricing SET current_index = NULL WHERE agreement_id=" . $values['agreement_id'] . ' AND item_id=' . (int)$item_id);
					$this->db->query("INSERT INTO fm_s_agreement_pricing (agreement_id,item_id,id,current_index,this_index,cost,index_date,entry_date,user_id)"
						. "VALUES (" . $values['agreement_id'] . "," . $item_id ."," . ($values['id'][$item_id]+1) .",1,'" . $values['new_index'] . "','" . ($value * $values['new_index'])  . "'," . (int)$values['date'] . "," . time()
						. "," . $this->account . ")");
				}
			}

			$this->db->transaction_commit();
			$receipt['message'][] = array('msg'=>lang('s_agreement %1 has been updated for index',$values['agreement_id']));

			return $receipt;
		}

		function floatval($strValue)
		{
			$floatValue = preg_replace("/(^[0-9]*)(\\.|,)([0-9]*)(.*)/", "\\1.\\3", $strValue);
			if(!is_numeric($floatValue))
			{
				$floatValue = preg_replace("/(^[0-9]*)(.*)/", "\\1", $strValue);
			}
			if(!is_numeric($floatValue))
			{
				$floatValue = 0;
			}
			return $floatValue;
		}

		function delete_last_index($s_agreement_id,$item_id)
		{
			$this->db->transaction_begin();
			$this->db->query("SELECT max(id) as index_count FROM fm_s_agreement_pricing WHERE agreement_id=$s_agreement_id AND item_id=$item_id");
			$this->db->next_record();
			$index_count	= $this->db->f('index_count');
			if($index_count>1)
			{
				$this->db->query("DELETE FROM fm_s_agreement_pricing WHERE agreement_id=$s_agreement_id AND item_id=$item_id AND id=$index_count");
				$this->db->query("UPDATE fm_s_agreement_pricing set current_index = 1 WHERE agreement_id=$s_agreement_id AND item_id=$item_id AND id =" . ($index_count-1));
			}
			$this->db->transaction_commit();
		}

		function delete_item($s_agreement_id,$item_id)
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_s_agreement_detail WHERE agreement_id=$s_agreement_id AND id=$item_id");
			$this->db->query("DELETE FROM fm_s_agreement_pricing WHERE agreement_id=$s_agreement_id AND item_id=$item_id");
			$this->db->transaction_commit();
		}

		function delete($s_agreement_id)
		{
			$table = 'fm_s_agreement';
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM $table WHERE id=" . $s_agreement_id);
			$this->db->query("DELETE FROM fm_s_agreement_detail WHERE agreement_id=" . $s_agreement_id);
			$this->db->query("DELETE FROM fm_s_agreement_pricing WHERE agreement_id=" . $s_agreement_id);
			$this->db->query("DELETE FROM fm_orders WHERE id=" . $s_agreement_id);
			$this->db->transaction_commit();
		}

		function attrib_choise2id($id,$value = '')
		{
			$value = $this->db->db_addslashes($value);
			$choice_table = 'phpgw_cust_choice';
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.s_agreement.detail'); 
			$attribute_filter = " location_id = {$location_id}";

			$sql = "SELECT id FROM $choice_table WHERE $attribute_filter AND value = '$value' AND attrib_id = $id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('id');
		}

		function request_next_id()
		{
			$this->db->query("SELECT max(id) as id FROM fm_s_agreement");
			$this->db->next_record();
			$next_id= $this->db->f('id')+1;
			return $next_id;
		}

		function get_year_filter_list($agreement_id = 0)
		{
			$table = 'fm_s_agreement_budget';
			$sql = "SELECT year FROM $table WHERE agreement_id = {$agreement_id} group by year ORDER BY year ASC";
			$this->db->query($sql,__LINE__,__FILE__);

			$values = array();

			while ($this->db->next_record())
			{
				$values[]	= $this->db->f('year');
			}

			return $values;
		}

		function get_budget($agreement_id = 0)
		{
			$values = array();

			$sql = "SELECT * FROM fm_s_agreement_budget WHERE agreement_id = {$agreement_id} ORDER BY year ASC";
			$this->db->query($sql,__LINE__,__FILE__);

			while($this->db->next_record())
			{
				$values[] = array
					(
						'agreement_id'		=> $agreement_id,
						'year'				=> $this->db->f('year'),
						'cat_id'			=> $this->db->f('category'),
						'ecodimb'			=> $this->db->f('ecodimb'),
						'budget_account'	=> $this->db->f('budget_account'),
						'budget'			=> $this->db->f('budget'),
						'actual_cost'		=> $this->db->f('actual_cost')
					);
			}

			return $values;
		}

		function delete_year_from_budget($data,$agreement_id)
		{
			$sql = "DELETE FROM fm_s_agreement_budget WHERE agreement_id = {$agreement_id} AND year IN(" . implode(',', $data) . ')';
			$this->db->query($sql,__LINE__,__FILE__);
		}
	}
