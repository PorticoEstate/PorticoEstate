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

	/**
	 * Description
	 * @package property
	 */

	class property_soagreement
	{
		var $role;

		function __construct()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           = clone($GLOBALS['phpgw']->db);
			$this->db2          = clone($this->db);
			$this->join			= $this->db->join;
			$this->left_join	= $this->db->left_join;
			$this->like			= $this->db->like;
			//			$this->role		= 'agreement';
		}

		function select_vendor_list()
		{
			$table = 'fm_agreement';
			$this->db->query("SELECT vendor_id,org_name FROM $table $this->join fm_vendor on fm_agreement.vendor_id=fm_vendor.id GROUP BY org_name,vendor_id ");

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
//_debug_array($data);die();
			$start			= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$filter			= isset($data['filter']) && $data['filter'] ? $data['filter'] : 'none';
			$query 			= isset($data['query']) ? $data['query'] : '';
			$sort 			= isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$cat_id			= isset($data['cat_id']) ? (int) $data['cat_id'] : '';
			$vendor_id		= isset($data['vendor_id']) ? (int)$data['vendor_id']:'';
			$allrows		= isset($data['allrows']) ? $data['allrows']:'';
			$member_id		= isset($data['member_id']) ? (int)$data['member_id']:0;
			$agreement_id	= isset($data['agreement_id'])? (int) $data['agreement_id']:'';
			$status_id 		= isset($data['status_id']) ? $data['status_id'] : '';

			$filtermethod = '';
			$querymethod = '';

			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';

			$entity_table = 'fm_agreement';
			$category_table = 'fm_branch';
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.agreement'); 
			$attribute_filter = " location_id = {$location_id}";
			$paranthesis ='(';
			$joinmethod = " {$this->join} {$category_table} ON ( {$entity_table}.category = {$category_table}.id)";
			$joinmethod .= " {$this->join}  fm_vendor ON ( {$entity_table}.vendor_id =fm_vendor.id )";
			$joinmethod .= " {$this->join} fm_agreement_status ON ( {$entity_table}.status = fm_agreement_status.id))";

			$cols = "{$entity_table}.*,{$category_table}.descr as category, org_name, fm_agreement_status.descr as status";

			$cols_return[] 				= 'id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= lang('ID');
			$uicols['statustext'][]		= lang('ID');
			$uicols['datatype'][]		= 'I';

			$cols_return[] 				= 'name';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'name';
			$uicols['descr'][]			= lang('name');
			$uicols['statustext'][]		= lang('name');
			$uicols['datatype'][]		= 'V';

			$cols_return[] 				= 'org_name';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'org_name';
			$uicols['descr'][]			= lang('Vendor');
			$uicols['statustext'][]		= lang('Vendor');
			$uicols['datatype'][]		= 'V';

			$cols_return[] 				= 'category';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'category';
			$uicols['descr'][]			= lang('category');
			$uicols['statustext'][]		= lang('category');
			$uicols['datatype'][]		= 'V';

			$cols_return[] 				= 'start_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'start_date';
			$uicols['descr'][]			= lang('start');
			$uicols['statustext'][]		= lang('start date');
			$uicols['datatype'][]		= 'D';

			$cols_return[] 				= 'termination_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'termination_date';
			$uicols['descr'][]			= lang('termination date');
			$uicols['statustext'][]		= lang('termination date');
			$uicols['datatype'][]		= 'D';

			$cols_return[] 				= 'end_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'end_date';
			$uicols['descr'][]			= lang('end');
			$uicols['statustext'][]		= lang('end date');
			$uicols['datatype'][]		= 'D';

			$cols_return[] 				= 'status';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'status';
			$uicols['descr'][]			= lang('status');
			$uicols['statustext'][]		= lang('status');
			$uicols['datatype'][]		= 'V';

			if ($order)
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
				$ordermethod = " ORDER BY {$entity_table}.id DESC";
			}
//_debug_array($ordermethod);
			$sql = "SELECT {$cols} FROM {$paranthesis} {$entity_table} {$joinmethod}";

			$i	= count($uicols['name']);

			$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['agreement_columns' . !!$agreement_id])?$GLOBALS['phpgw_info']['user']['preferences']['property']['agreement_columns' . !!$agreement_id]:'';
			$user_column_filter = '';
			if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
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
				$cols_return_extra[]= array(
					'name'	=> $this->db->f('column_name'),
					'datatype'	=> $this->db->f('datatype'),
					'attrib_id'	=> $this->db->f('id')
				);

				$i++;
			}

			$this->uicols	= $uicols;

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

			if ($agreement_id)
			{
				$filtermethod .= " $where $entity_table.agreement_id=$agreement_id";
				$filtermethod .= " AND current_index = 1";
				$where= 'AND';
			}

			if ($cat_id)
			{
				$filtermethod .= " $where $entity_table.category='$cat_id' ";
				$where= 'AND';
			}

			if ($vendor_id)
			{
				$filtermethod .= " $where $entity_table.vendor_id='$vendor_id' ";
				$where= 'AND';
			}

			if ($member_id>0)
			{
				$filtermethod .= " $where fm_vendor.member_of $this->like '%,$member_id,%' ";
				$where= 'AND';
			}

			if ($status_id)
			{
				$filtermethod .= " {$where} {$entity_table}.status='{$status_id}' ";
				$where= 'AND';
			}


			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod[]= "fm_branch.descr {$this->like} '%{$query}%'";
				$querymethod[]= "{$entity_table}.name {$this->like} '%{$query}%'";

				$this->db->query("SELECT * FROM $attribute_table WHERE search='1' AND $attribute_filter ");

				while ($this->db->next_record())
				{
					if($this->db->f('datatype')=='V' || $this->db->f('datatype')=='email' || $this->db->f('datatype')=='CH')
					{
						$querymethod[]= "$entity_table." . $this->db->f('column_name') . " $this->like '%$query%'";
					}
					else
					{
						$querymethod[]= "$entity_table." . $this->db->f('column_name') . " = '$query'";
					}
				}

				if (isset($querymethod) AND is_array($querymethod))
				{
					$querymethod = " $where (" . implode (' OR ',$querymethod) . ')';
					$where = 'AND';
				}
			}

			$sql .= " $filtermethod $querymethod";

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

			$contacts		= CreateObject('phpgwapi.contacts');
			$agreement_list = array();

			while ($this->db->next_record())
			{
				for ($i=0;$i<$n;$i++)
				{
					$agreement_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$agreement_list[$j]['grants'] = (int)isset($grants[$this->db->f('user_id')])?$grants[$this->db->f('user_id')]:'';
				}

				if(isset($cols_return_extra) && is_array($cols_return_extra))
				{
					for ($i=0;$i<count($cols_return_extra);$i++)
					{
						$value='';
						$value=$this->db->f($cols_return_extra[$i]['name']);

						if(($cols_return_extra[$i]['datatype']=='R' || $cols_return_extra[$i]['datatype']=='LB') && $value)
						{
							$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $value;
							$this->db2->query($sql);
							$this->db2->next_record();
							$agreement_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('value');
						}
						else if($cols_return_extra[$i]['datatype']=='AB' && $value)
						{
							$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
							$agreement_list[$j][$cols_return_extra[$i]['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
						}
						else if($cols_return_extra[$i]['datatype']=='VENDOR' && $value)
						{
							$sql="SELECT org_name FROM fm_vendor where id=$value";
							$this->db2->query($sql);
							$this->db2->next_record();
							$agreement_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('org_name');

						}
						else if($cols_return_extra[$i]['datatype']=='CH' && $value)
						{
//							$ch= unserialize($value);
							$ch = explode(',', trim($data['value'], ','));
							if (isset($ch) AND is_array($ch))
							{
								for ($k=0;$k<count($ch);$k++)
								{
									$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $ch[$k];
									$this->db2->query($sql);
									while ($this->db2->next_record())
									{
										$ch_value[]=$this->db2->f('value');
									}
								}
								$agreement_list[$j][$cols_return_extra[$i]['name']] = @implode(",", $ch_value);
								unset($ch_value);
							}
						}
						else if($cols_return_extra[$i]['datatype']=='D' && $value)
						{
							$agreement_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
						}
						else if($cols_return_extra[$i]['datatype']=='timestamp' && $value)
						{
							$agreement_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$value);
						}
						else if($cols_return_extra[$i]['datatype']=='link' && $value)
						{
							$agreement_list[$j][$cols_return_extra[$i]['name']]= phpgw::safe_redirect($value);
						}
						else
						{
							$agreement_list[$j][$cols_return_extra[$i]['name']]=$value;
						}
					}
				}
				$j++;
			}
			//_debug_array($agreement_list);
			return $agreement_list;
		}

		function read_details($data)
		{
			$start			= isset($data['start']) && $data['start'] ? $data['start']:0;
			$filter			= isset($data['filter']) && $data['filter'] ? $data['filter']:'none';
			$query 			= isset($data['query']) ? $data['query'] : '';
			$sort 			= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
			$order			= isset($data['order']) ? $data['order'] : '';
			$cat_id			= isset($data['cat_id']) ? $data['cat_id'] : '';
			$allrows		= isset($data['allrows']) ? $data['allrows'] : '';
			$agreement_id	= isset($data['agreement_id']) ? $data['agreement_id'] : '';

			$allrows = true; // return all..

			$entity_table = 'fm_activity_price_index';

			$paranthesis ='(';
			$joinmethod = " {$this->join} fm_activities ON ( fm_activities.id = $entity_table.activity_id))";
			$paranthesis .='(';
			$joinmethod .= " {$this->join} fm_standard_unit ON (fm_activities.unit = fm_standard_unit.id))";

			$cols = "fm_activities.*, $entity_table.m_cost,$entity_table.w_cost,"
				. " {$entity_table}.total_cost,$entity_table.index_count,"
				. " {$entity_table}.index_date,$entity_table.activity_id,"
				. " {$entity_table}.this_index,$entity_table.agreement_id,"
				. " fm_standard_unit.name AS unit_name";


			$uicols['name'][]			= 'activity_id';
			$uicols['descr'][]			= lang('ID');
			$uicols['input_type'][]		= 'I';

			$uicols['name'][]			= 'num';
			$uicols['descr'][]			= lang('Code');
			$uicols['input_type'][]		= 'V';

			$uicols['name'][]			= 'descr';
			$uicols['descr'][]			= lang('descr');
			$uicols['input_type'][]		= 'V';

			$uicols['name'][]			= 'unit_name';
			$uicols['descr'][]			= lang('unit');
			$uicols['input_type'][]		= 'V';

			$uicols['name'][]			= 'm_cost';
			$uicols['descr'][]			= lang('Material cost');
			$uicols['input_type'][]		= 'N';

			$uicols['name'][]			= 'w_cost';
			$uicols['descr'][]			= lang('Labour cost');
			$uicols['input_type'][]		= 'N';

			$uicols['name'][]			= 'total_cost';
			$uicols['descr'][]			= lang('Total cost');
			$uicols['input_type'][]		= 'N';

			$uicols['name'][]			= 'this_index';
			$uicols['descr'][]			= lang('index');
			$uicols['input_type'][]		= 'N';

			$uicols['name'][]			= 'index_count';
			$uicols['descr'][]			= lang('index_count');
			$uicols['input_type'][]		= 'I';

			$uicols['name'][]			= 'index_date';
			$uicols['descr'][]			= lang('Date');
			$uicols['input_type'][]		= 'D';

			if ($order)
			{
				switch($order)
				{
					case 'index_date':
					case 'activity_id':
					case 'index_count':
					case 'total_cost':
					case 'w_cost':
					case 'm_cost':
					case 'num':
					case 'descr':
					case 'm_cost':
						$ordermethod = "ORDER BY {$entity_table}.{$order} {$sort}";
						break;
					case 'unit_name':
						$ordermethod = "ORDER BY fm_standard_unit.name {$sort}";
						break;
					default:
						$ordermethod = '';
				}
				
			}
			else
			{
				$ordermethod = "ORDER BY {$entity_table}.activity_id DESC";
			}

			$sql = "SELECT {$cols} FROM {$paranthesis} {$entity_table} {$joinmethod}";

			$this->uicols	= $uicols;

			$where= 'WHERE';
			$filtermethod = '';

			if ($agreement_id)
			{
				$filtermethod .= " $where $entity_table.agreement_id=$agreement_id";
				$filtermethod .= " AND current_index = 1";
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

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			while ($this->db->next_record())
			{
				$details[] = array
					(
						'agreement_id'		=> $this->db->f('agreement_id'),
						'activity_id'		=> $this->db->f('activity_id'),
						'id'				=> $this->db->f('id'),
						'num'				=> $this->db->f('num'),
						'descr'				=> $this->db->f('descr',true),
						'unit'				=> $this->db->f('unit'),
						'unit_name'			=> $this->db->f('unit_name'),
						'm_cost'			=> $this->db->f('m_cost'),
						'w_cost'			=> $this->db->f('w_cost'),
						'total_cost'		=> $this->db->f('total_cost'),
						'this_index'		=> $this->db->f('this_index'),
						'index_count'		=> $this->db->f('index_count'),
						'index_date'		=> $GLOBALS['phpgw']->common->show_date($this->db->f('index_date'),$dateformat)
					);
			}
			//html_print_r($details);
			return $details;
		}

		function read_prizing($data)
		{
			if(is_array($data))
			{
				$agreement_id	= (isset($data['agreement_id'])?$data['agreement_id']:0);
				$activity_id	= (isset($data['activity_id'])?$data['activity_id']:0);
			}

			$entity_table = 'fm_activity_price_index';

			$cols = "fm_activity_price_index.m_cost,fm_activity_price_index.w_cost,fm_activity_price_index.total_cost,"
				. " fm_activity_price_index.index_count,fm_activity_price_index.index_date,fm_activity_price_index.activity_id,fm_activity_price_index.this_index";

			$cols_return[] 				= 'activity_id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'activity_id';
			$uicols['descr'][]			= lang('activity ID');
			$uicols['statustext'][]		= lang('activity ID');

			$cols_return[] 				= 'id';
			$uicols['input_type'][]		= 'hidden';
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= false;
			$uicols['statustext'][]		= false;

			$cols_return[] 				= 'm_cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'm_cost';
			$uicols['descr'][]			= lang('m_cost');
			$uicols['statustext'][]		= lang('m_cost');
			$cols_return[] 				= 'w_cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'w_cost';
			$uicols['descr'][]			= lang('w_cost');
			$uicols['statustext'][]		= lang('w_cost');
			$cols_return[] 				= 'total_cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'total_cost';
			$uicols['descr'][]			= lang('total cost');
			$uicols['statustext'][]		= lang('total cost');

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

			$from = " FROM $entity_table ";

			$sql = "SELECT $cols $from $joinmethod";


			$this->uicols	= $uicols;

			$ordermethod = " order by $entity_table.index_count ASC";

			$where= 'WHERE';


			if ($agreement_id)
			{
				$filtermethod .= " $where $entity_table.agreement_id=$agreement_id AND activity_id=$activity_id";
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
					$agreement_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$agreement_list[$j]['agreement_id'] = $agreement_id;
				}
				$j++;
			}
			//_debug_array($agreement_list);
			return $agreement_list;
		}


		function read_single($agreement_id, $values = array())
		{
			$table = 'fm_agreement';

			$this->db->query("SELECT $table.*,fm_vendor.member_of FROM $table $this->join fm_vendor ON $table.vendor_id = fm_vendor.id where $table.id='$agreement_id'");

			if($this->db->next_record())
			{
				$values['id']				= (int)$this->db->f('id');
				$values['entry_date']		= $this->db->f('entry_date');
				$values['cat_id']			= $this->db->f('category');
				$values['start_date']		= $this->db->f('start_date');
				$values['end_date']			= $this->db->f('end_date');
				$values['termination_date']	= $this->db->f('termination_date');
				$values['vendor_id']		= $this->db->f('vendor_id');
				$values['b_account_id']		= $this->db->f('account_id');
				$values['name']				= stripslashes($this->db->f('name'));
				$values['descr']			= stripslashes($this->db->f('descr'));
				$values['user_id']			= $this->db->f('user_id');
				$values['group_id']			= $this->db->f('group_id');
				$values['status']			= $this->db->f('status');
				$values['member_of']		= explode(',',$this->db->f('member_of'));

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

		function read_single_item($data, $values = array())
		{
			$table = 'fm_activities';

			$agreement_id =$data['agreement_id'];
			$id =$data['id'];

			$this->db->query("SELECT * from $table $this->join fm_activity_price_index on $table.id = fm_activity_price_index.activity_id where $table.id=$id AND agreement_id=$agreement_id and index_count = 1");

			if($this->db->next_record())
			{
				$values['agreement_id']		= (int)$this->db->f('agreement_id');
				$values['id']				= (int)$this->db->f('id');
				$values['num']				= $this->db->f('num');
				$values['entry_date']		= $this->db->f('entry_date');
				$values['m_cost']			= $this->db->f('m_cost');
				$values['w_cost']			= $this->db->f('w_cost');
				$values['total_cost']		= $this->db->f('total_cost');

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

		function add($agreement,$values_attribute='')
		{
			//_debug_array($agreement);
			$table = 'fm_agreement';
			$agreement['name'] = $this->db->db_addslashes($agreement['name']);
			$agreement['descr'] = $this->db->db_addslashes($agreement['descr']);

			if($agreement['member_of'])
			{
				$agreement['member_of']=',' . implode(',',$agreement['member_of']) . ',';
			}

			$this->db->transaction_begin();
			$id = $this->bocommon->next_id($table);
			$vals = array();
			$vals[] = $id;
			$vals[] = $agreement['name'];
			$vals[] = $agreement['descr'];
			$vals[] = time();
			$vals[] = $agreement['cat_id'];
			$vals[] = $agreement['start_date'];
			$vals[] = $agreement['end_date'];
			$vals[] = $agreement['termination_date'];
			$vals[] = $agreement['vendor_id'];
			$vals[] = $this->account;

			if(isset($agreement['extra']) && is_array($agreement['extra']))
			{
				foreach ($agreement['extra'] as $input_name => $value)
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

			$cols[]	= 'group_id';
			$vals[]	= $agreement['group_id'];
			$cols[]	= 'status';
			$vals[]	= $agreement['status'];

			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= $this->db->validate_insert($vals);
			}

			$this->db->query("INSERT INTO $table (id,name,descr,entry_date,category,start_date,end_date,termination_date,vendor_id,user_id $cols) "
				. "VALUES ($vals)",__LINE__,__FILE__);

			$receipt['agreement_id']= $id;//$this->db->get_last_insert_id($table,'id');

			$receipt['message'][] = array('msg'=>lang('agreement %1 has been saved',$receipt['agreement_id']));

			$this->db->transaction_commit();
			return $receipt;
		}

		function add_item($values,$values_attribute='')
		{
			//_debug_array($values);

			$this->db->transaction_begin();

			$this->db->query("SELECT start_date FROM fm_agreement WHERE id=" . $values['agreement_id']);
			$this->db->next_record();
			$start_date	= $this->db->f('start_date');

			$agreement_id = $values['agreement_id'];
			$activity_id = $values['id'];
			$m_cost = $this->floatval($values['m_cost']);
			$w_cost = $this->floatval($values['w_cost']);
			$total_cost = $this->floatval($values['total_cost']);
			$entry_date = time();


			$sql = "UPDATE fm_activity_price_index SET "
				. " index_count = 1,current_index = 1,this_index = 1,"
				. " m_cost = $m_cost,w_cost = $w_cost ,total_cost = $total_cost ,index_date = $start_date,"
				. " entry_date = $entry_date ,user_id =" . $this->account . " WHERE agreement_id = $agreement_id AND activity_id = $activity_id AND index_count = -1";

			$this->db->query($sql);

			$receipt['agreement_id']= $values['agreement_id'];
			$receipt['id']= $values['id'];

			$receipt['message'][] = array('msg'=>lang('activity %1 has been saved',$receipt['id']));

			$this->db->transaction_commit();
			return $receipt;
		}

		function edit($values,$values_attribute='')
		{
			//_debug_array($values);
			//_debug_array($values_attribute);
			$table = 'fm_agreement';

			$values['name'] = $this->db->db_addslashes($values['name']);

			if($values['member_of'])
			{
				$values['member_of']=',' . implode(',',$values['member_of']) . ',';
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
			$value_set['group_id']	= $values['group_id'];
			$value_set['status']	= $values['status'];
			$value_set['vendor_id']	= $values['vendor_id'];

			if($value_set)
			{
				$value_set	= ',' . $this->db->validate_update($value_set);
			}

			$this->db->query("UPDATE $table set entry_date='" . time() . "', category='"
				. $values['cat_id'] . "', start_date=" . intval($values['start_date']) . ", end_date=" . intval($values['end_date']) . ", termination_date=" . intval($values['termination_date']) . "$value_set WHERE id=" . intval($values['agreement_id']));

			$this->db->query("UPDATE fm_activity_price_index set index_date=" . intval($values['start_date']) . " WHERE index_count=1 AND agreement_id= " . intval($values['agreement_id']));

			$receipt['agreement_id']= $values['agreement_id'];
			$receipt['message'][] = array('msg'=>lang('agreement %1 has been edited',$values['agreement_id']));
			return $receipt;
		}

		function edit_item($values)
		{
			//_debug_array($values);

			$value_set['m_cost']		= $values['m_cost'];
			$value_set['w_cost']		= $values['w_cost'];
			$value_set['total_cost']	= $values['total_cost'];

			if($value_set)
			{
				$value_set	= ',' . $this->db->validate_update($value_set);
			}

			$this->db->query("UPDATE fm_activity_price_index set entry_date=" . time() . "$value_set WHERE agreement_id=" . intval($values['agreement_id']) . ' AND activity_id=' . intval($values['id']));

			$this->db->query("UPDATE fm_activity_price_index  set m_cost = this_index *" . $this->floatval($values['m_cost']) . ",w_cost = this_index *" . $this->floatval($values['w_cost']) . ",total_cost = this_index *" . $this->floatval($values['total_cost']) . "  WHERE agreement_id=" . intval($values['agreement_id']) . ' AND activity_id=' . intval($values['id']));

			$receipt['agreement_id']= $values['agreement_id'];
			$receipt['id']= $values['id'];
			$receipt['message'][] = array('msg'=>lang('Activity %1 has been edited',$values['id']));
			return $receipt;
		}

		function update($values)
		{
			//_debug_array($values);
			$values['new_index']=$this->floatval($values['new_index']);
			$this->db->transaction_begin();

			while (is_array($values['select']) && list(,$activity_id) = each($values['select']))
			{

				if($values['id'][$activity_id]>0)
				{
					$this->db->query("UPDATE fm_activity_price_index set current_index = NULL WHERE agreement_id=" . intval($values['agreement_id']) . ' AND activity_id=' . intval($activity_id));

					$this->db->query("INSERT INTO fm_activity_price_index (agreement_id,activity_id,index_count,current_index,this_index,m_cost,w_cost,total_cost,index_date,entry_date,user_id)"
						. "VALUES (" . $values['agreement_id'] . "," . $activity_id ."," . ($values['id'][$activity_id]+1) .",1,'" . $values['new_index'] . "','" . ($values['m_cost'][$activity_id] * $values['new_index']) . "','" . ($values['w_cost'][$activity_id] * $values['new_index']) . "','" . ($values['total_cost'][$activity_id] * $values['new_index'])  . "'," . (int)$values['date'] . "," . time()
						. "," . $this->account . ")");

					$receipt['message'][] = array('msg'=>lang('Activity %1 has been updated for index',$activity_id));
				}

			}

			$this->db->transaction_commit();

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


		function delete_last_index($agreement_id,$activity_id)
		{
			$this->db->transaction_begin();
			$this->db->query("SELECT max(index_count) as index_count FROM fm_activity_price_index WHERE agreement_id=$agreement_id AND activity_id=$activity_id");
			$this->db->next_record();
			$index_count	= $this->db->f('index_count');
			if($index_count>1)
			{
				$this->db->query("DELETE FROM fm_activity_price_index WHERE agreement_id=$agreement_id AND activity_id=$activity_id AND index_count=$index_count");
				$this->db->query("UPDATE fm_activity_price_index set current_index = 1 WHERE agreement_id=$agreement_id AND activity_id=$activity_id AND index_count =" . ($index_count-1));
			}
			else
			{
				$sql = "UPDATE fm_activity_price_index SET "
					. " index_count = -1,current_index = 1,this_index = 1,"
					. " m_cost = NULL,w_cost = NULL ,total_cost = NULL ,index_date = NULL,"
					. " entry_date = NULL ,user_id =" . $this->account . " WHERE agreement_id = $agreement_id AND activity_id = $activity_id";

				$this->db->query($sql);

			}
			$this->db->transaction_commit();
		}

		function delete_item($agreement_id,$activity_id)
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_activity_price_index WHERE agreement_id=$agreement_id AND activity_id=$activity_id");
			$this->db->transaction_commit();
		}

		function delete($agreement_id)
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_agreement WHERE id=" . intval($agreement_id));
			$this->db->query("DELETE FROM fm_activity_price_index WHERE agreement_id=" . intval($agreement_id));
			$this->db->transaction_commit();
		}


		function get_default_column_def($table)
		{
			switch($table)
			{
			case 'fm_agreement':
				$fd=array(
					'group_id' => array('type' => 'int','precision' => '4','nullable' => false),
					'id' => array('type' => 'int','precision' => '4','nullable' => false),
					'vendor_id' => array('type' => 'int','precision' => '4','nullable' => false),
					'name' => array('type' => 'varchar','precision' => '100','nullable' => false),
					'descr' => array('type' => 'text','nullable' => true),
					'status' => array('type' => 'varchar','precision' => '10','nullable' => true),
					'entry_date' => array('type' => 'int','precision' => '4','nullable' => true),
					'start_date' => array('type' => 'int','precision' => '4','nullable' => true),
					'end_date' => array('type' => 'int','precision' => '4','nullable' => true),
					'termination_date' => array('type' => 'int','precision' => '4','nullable' => true),
					'category' => array('type' => 'int','precision' => '4','nullable' => true),
					'user_id' => array('type' => 'int','precision' => '4','nullable' => true)
				);
				break;
			case 'fm_agreement_detail':


				break;
			default:
				return;
				break;
			}

			return $fd;
		}

		function get_table_def($table)
		{
			$metadata = $this->db->metadata($table);

			if(isset($this->db->adodb))
			{
				$i = 0;
				foreach($metadata as $key => $val)
				{
					$metadata_temp[$i]['name'] = $key;
					$i++;
				}
				$metadata = $metadata_temp;
				unset ($metadata_temp);
			}

			if($this->role=='detail')
			{
				$filtermethod= ' AND attrib_detail=2';
				$pk = array('id');
			}
			else
			{
				$filtermethod= ' AND attrib_detail=1';
				$pk = array('group_id','id');
			}

			$fd = $this->get_default_column_def($table);

			for ($i=0; $i<count($metadata); $i++)
			{
				$sql = "SELECT * FROM fm_agreement_attribute WHERE column_name = '" . $metadata[$i]['name'] . "' $filtermethod";

				$this->db->query($sql,__LINE__,__FILE__);
				while($this->db->next_record())
				{
					if(!$precision = $this->db->f('precision_'))
					{
						$precision = $this->bocommon->translate_datatype_precision($this->db->f('datatype'));
					}

					$fd[$metadata[$i]['name']] = array(
						'type' => $this->bocommon->translate_datatype_insert(stripslashes($this->db->f('datatype'))),
						'precision' => $precision,
						'nullable' => stripslashes($this->db->f('nullable')),
						'default' => stripslashes($this->db->f('default_value')),
						'scale' => $this->db->f('scale')
					);
					unset($precision);
				}
			}

			$table_def = array(
				$table =>	array(
					'fd' => $fd
				)
			);

			$table_def[$table]['pk'] = $pk;
			$table_def[$table]['fk'] = array();
			$table_def[$table]['ix'] = array();
			$table_def[$table]['uc'] = array();

			return $table_def;
		}

		function request_next_id()
		{
			$this->db->query("SELECT max(id) as id FROM fm_agreement");
			$this->db->next_record();
			$next_id= $this->db->f('id')+1;
			return $next_id;
		}

		function get_agreement_group_list()
		{
			$this->db->query("SELECT * FROM fm_agreement_group ORDER BY descr asc");
			while ($this->db->next_record())
			{
				$agreement_group_list[]=array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $GLOBALS['phpgw']->strip_html($this->db->f('descr')).' [ '. $GLOBALS['phpgw']->strip_html($this->db->f('status')).' ] '
					);
			}
			return $agreement_group_list;
		}

		function read_group_activity($group_id='',$agreement_id='')
		{
			$uicols = array();
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= lang('ID');
			$uicols['name'][]			= 'num';
			$uicols['descr'][]			= lang('Num');
			$uicols['name'][]			= 'base_descr';
			$uicols['descr'][]			= lang('Base');
			$uicols['name'][]			= 'descr';
			$uicols['descr'][]			= lang('Descr');
			$uicols['name'][]			= 'unit';
			$uicols['descr'][]			= lang('Unit');
			$uicols['name'][]			= 'ns3420';
			$uicols['descr'][]			= lang('ns3420');

			$this->uicols	= $uicols;

			$sql="SELECT fm_activities.* FROM fm_activities WHERE agreement_group_id = $group_id";
			$this->db->query($sql);

			$activity_list = array();
			while ($this->db->next_record())
			{
				$activity_list[$this->db->f('id')]=array
					(
						'id'		=> $this->db->f('id'),
						'num'		=> $this->db->f('num'),
						'base_descr'	=> $this->db->f('base_descr'),
						'descr'		=> $this->db->f('descr'),
						'unit'		=> $this->db->f('unit'),
						'ns3420'	=> $this->db->f('ns3420'),
					);
			}

			$sql="SELECT activity_id FROM fm_activity_price_index WHERE agreement_id = $agreement_id";

			$this->db->query($sql);

			while ($this->db->next_record())
			{
				unset($activity_list[$this->db->f('activity_id')]);
			}

			foreach($activity_list as $entry)
			{
				$activity_list_result[] = $entry;
			}

			return $activity_list_result;
		}

		function add_activity($values,$agreement_id)
		{
			$agreement_id = (int)$agreement_id;

			if (isset($values['select']) AND is_array($values['select']))
			{
				$this->db->transaction_begin();

				$this->db->query("SELECT start_date FROM fm_agreement WHERE id={$agreement_id}");
				$this->db->next_record();
				$date	= $this->db->f('start_date');
				if(!$date)
				{
					throw new Exception("missing start date for agreement {$agreement_id}");
					//					return $receipt['error'][] = array('msg'=>lang('missing start date for agreement %1',$agreement_id));
				}

				$sql = 'INSERT INTO fm_activity_price_index (agreement_id, activity_id, index_count, current_index, index_date, entry_date, user_id)'
					. ' VALUES(?, ?, ?, ?, ?, ?, ?)';
				$valueset=array();

				foreach($values['select'] as $activity_id)
				{
					$valueset[] = array
						(
							1	=> array
							(
								'value'	=> $agreement_id,
								'type'	=> PDO::PARAM_INT
							),
							2	=> array
							(
								'value'	=> $activity_id,
								'type'	=>	PDO::PARAM_INT
							),
							3	=> array
							(
								'value'	=> -1,
								'type'	=> PDO::PARAM_INT
							),
							4	=> array
							(
								'value'	=> 1,
								'type'	=>	PDO::PARAM_INT
							),
							5	=> array
							(
								'value'	=> $date,
								'type'	=> PDO::PARAM_INT
							),
							6	=> array
							(
								'value'	=> time(),
								'type'	=> PDO::PARAM_INT
							),
							7	=> array
							(
								'value'	=> $this->account,
								'type'	=> PDO::PARAM_INT
							)
						);
				}

				$this->db->insert($sql, $valueset, __LINE__, __FILE__);
				$this->db->transaction_commit();
			}

			$receipt['agreement_id']= $agreement_id;

			$receipt['message'][] = array('msg'=>lang('agreement %1 has been saved',$receipt['agreement_id']));

			return $receipt;
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_agreement_status ORDER BY id ");
			$status = array();
			while ($this->db->next_record())
			{
				$status[] = array
					(
						'id'	=> $this->db->f('id'),
						'name'	=> $this->db->f('descr',true)
					);
			}
			return $status;
		}

		function get_activity_descr($id)
		{
			$id = (int)$id;
			$this->db->query("SELECT descr FROM fm_activities WHERE id = $id",__LINE__,__FILE__);
			$this->db->next_record();
			return $this->db->f('descr',true);
		}
	}
