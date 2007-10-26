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
 	* @version $Id: class.soagreement.inc.php,v 1.20 2007/01/26 14:53:46 sigurdne Exp $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soagreement
	{
		var $role;

		function property_soagreement()
		{
			$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           = $this->bocommon->new_db();
			$this->db2          = $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->left_join	= $this->bocommon->left_join;
			$this->like			= $this->bocommon->like;
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
			if(is_array($data))
			{
				$start			= (isset($data['start'])?$data['start']:0);
				$filter			= (isset($data['filter'])?$data['filter']:'none');
				$query 			= (isset($data['query'])?$data['query']:'');
				$sort 			= (isset($data['sort'])?$data['sort']:'DESC');
				$order			= (isset($data['order'])?$data['order']:'');
				$cat_id			= (isset($data['cat_id'])?$data['cat_id']:'');
				$vendor_id		= (isset($data['vendor_id'])?$data['vendor_id']:'');
				$allrows		= (isset($data['allrows'])?$data['allrows']:'');
				$member_id		= (isset($data['member_id'])?$data['member_id']:0);
				$agreement_id	= (isset($data['agreement_id'])?$data['agreement_id']:'');
				$status 		= (isset($data['status'])?$data['status']:'');
			}

			$filtermethod = '';
			$querymethod = '';

			$choice_table = 'fm_agreement_choice';
			$attribute_table = 'fm_agreement_attribute';

			$entity_table = 'fm_agreement';
			$category_table = 'fm_branch';
			$attribute_filter = " AND attrib_detail = 1";
			$paranthesis ='(';
			$joinmethod = " $this->join $category_table ON ( $entity_table.category =$category_table.id)";
			$joinmethod .= " $this->join  fm_vendor ON ( $entity_table.vendor_id =fm_vendor.id ))";

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
			$uicols['descr'][]			= lang('Vendor');
			$uicols['statustext'][]		= lang('Vendor');

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

			$cols_return[] 				= 'end_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'end_date';
			$uicols['descr'][]			= lang('end');
			$uicols['statustext'][]		= lang('end date');

			$cols_return[] 				= 'status';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'status';
			$uicols['descr'][]			= lang('status');
			$uicols['statustext'][]		= lang('status');

			if ($order)
			{
				if ($order=='id')
				{
					$ordermethod = " order by $entity_table.$order $sort";
				}
				else
				{
					$ordermethod = " order by $order $sort";
				}
			}
			else
			{
				$ordermethod = " order by $entity_table.id DESC";
			}


			$from = " FROM $paranthesis $entity_table ";

			$sql = "SELECT $cols $from $joinmethod";

			$i	= count($uicols['name']);

			$this->db->query("SELECT * FROM $attribute_table WHERE list=1 $attribute_filter ");
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

			$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['agreement_columns' . !!$agreement_id])?$GLOBALS['phpgw_info']['user']['preferences'][$this->currentapp]['agreement_columns' . !!$agreement_id]:'';

//_debug_array($user_columns);

			if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
			{
				foreach($user_columns as $column_id)
				{
					$this->db->query("SELECT * FROM $attribute_table WHERE id= $column_id");

					$this->db->next_record();
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
			}

			$this->uicols	= $uicols;

//_debug_array($cols_return_extra);

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

			if ($status)
			{
				$filtermethod .= " $where $entity_table.status='$status' ";
				$where= 'AND';
			}


			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$this->db->query("SELECT * FROM $attribute_table where search='1'");

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

			$contacts			= CreateObject('phpgwapi.contacts');

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

						if(($cols_return_extra[$i]['datatype']=='R' || $cols_return_extra[$i]['datatype']=='LB') && $value):
						{
							$sql="SELECT value FROM $choice_table where attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $value . $attribute_filter;
							$this->db2->query($sql);
							$this->db2->next_record();
							$agreement_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('value');
						}
						elseif($cols_return_extra[$i]['datatype']=='AB' && $value):
						{
							$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
							$agreement_list[$j][$cols_return_extra[$i]['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
						}
						elseif($cols_return_extra[$i]['datatype']=='VENDOR' && $value):
						{
							$sql="SELECT org_name FROM fm_vendor where id=$value";
							$this->db2->query($sql);
							$this->db2->next_record();
							$agreement_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('org_name');

						}
						elseif($cols_return_extra[$i]['datatype']=='CH' && $value):
						{
							$ch= unserialize($value);
	
							if (isset($ch) AND is_array($ch))
							{
								for ($k=0;$k<count($ch);$k++)
								{
									$sql="SELECT value FROM $choice_table where attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $ch[$k] . $attribute_filter;
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
						elseif($cols_return_extra[$i]['datatype']=='D' && $value):
						{
//_debug_array($value);

							$agreement_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
						}
						else:
						{
							$agreement_list[$j][$cols_return_extra[$i]['name']]=$value;
						}
						endif;
					}

				}
				$j++;
			}
//_debug_array($agreement_list);
			return $agreement_list;
		}

		function read_details($data)
		{
			if(is_array($data))
			{
				$start			= (isset($data['start'])?$data['start']:0);
				$filter			= (isset($data['filter'])?$data['filter']:'none');
				$query 			= (isset($data['query'])?$data['query']:'');
				$sort 			= (isset($data['sort'])?$data['sort']:'DESC');
				$order			= (isset($data['order'])?$data['order']:'');
				$cat_id			= (isset($data['cat_id'])?$data['cat_id']:'');
				$allrows		= (isset($data['allrows'])?$data['allrows']:'');
				$agreement_id	= (isset($data['agreement_id'])?$data['agreement_id']:'');
			}

			$entity_table = 'fm_activity_price_index';

			$paranthesis .='(';
			$joinmethod .= " $this->join fm_activities ON ( fm_activities.id = $entity_table.activity_id))";

			$cols = "fm_activities.*, $entity_table.m_cost,$entity_table.w_cost,"
				. " $entity_table.total_cost,$entity_table.index_count,"
				. " $entity_table.index_date,$entity_table.activity_id,"
				. " $entity_table.this_index,$entity_table.agreement_id";


			$uicols['name'][]			= 'activity_id';
			$uicols['descr'][]			= lang('ID');

			$uicols['name'][]			= 'num';
			$uicols['descr'][]			= lang('Code');

			$uicols['name'][]			= 'descr';
			$uicols['descr'][]			= lang('descr');

			$uicols['name'][]			= 'unit';
			$uicols['descr'][]			= lang('unit');

			$uicols['name'][]			= 'm_cost';
			$uicols['descr'][]			= lang('Material cost');

			$uicols['name'][]			= 'w_cost';
			$uicols['descr'][]			= lang('Labour cost');

			$uicols['name'][]			= 'total_cost';
			$uicols['descr'][]			= lang('Total cost');

			$uicols['name'][]			= 'this_index';
			$uicols['descr'][]			= lang('index');

			$uicols['name'][]			= 'index_count';
			$uicols['descr'][]			= lang('index_count');

			$uicols['name'][]			= 'index_date';
			$uicols['descr'][]			= lang('Date');

			if ($order)
			{
				$ordermethod = " order by $entity_table.$order $sort";
			}
			else
			{
				$ordermethod = " order by $entity_table.activity_id DESC";
			}


			$from .= " FROM $paranthesis $entity_table ";

			$sql = "SELECT $cols $from $joinmethod";

			$this->uicols	= $uicols;

			$where= 'WHERE';

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

			if ($status)
			{
				$filtermethod .= " $where $entity_table.status='$status' ";
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
					'descr'				=> $this->db->f('descr'),
					'unit'				=> $this->db->f('unit'),
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

			$cols_return[] 			= 'activity_id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'activity_id';
			$uicols['descr'][]			= lang('activity ID');
			$uicols['statustext'][]		= lang('activity ID');

			$cols_return[] 			= 'id';
			$uicols['input_type'][]		= 'hidden';
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= False;
			$uicols['statustext'][]		= False;

			$cols_return[] 			= 'm_cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'm_cost';
			$uicols['descr'][]			= lang('m_cost');
			$uicols['statustext'][]		= lang('m_cost');
			$cols_return[] 			= 'w_cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'w_cost';
			$uicols['descr'][]			= lang('w_cost');
			$uicols['statustext'][]		= lang('w_cost');
			$cols_return[] 			= 'total_cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'total_cost';
			$uicols['descr'][]			= lang('total cost');
			$uicols['statustext'][]		= lang('total cost');

			$cols_return[] 			= 'this_index';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'this_index';
			$uicols['descr'][]			= lang('index');
			$uicols['statustext'][]		= lang('index');

			$cols_return[] 			= 'index_count';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'index_count';
			$uicols['descr'][]			= lang('index_count');
			$uicols['statustext'][]		= lang('index_count');

			$cols_return[] 			= 'index_date';
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


		function read_single($data)
		{
			$attribute_table = 'fm_agreement_attribute';
			$table = 'fm_agreement';

			$agreement_id =$data['agreement_id'];

			$this->db->query("SELECT * FROM $attribute_table WHERE attrib_detail = 1 ORDER BY attrib_sort");

			while ($this->db->next_record())
			{
				$agreement['attributes'][] = array
				(
					'attrib_id'		=> $this->db->f('id'),
					'name'			=> $this->db->f('column_name'),
					'input_text'	=> stripslashes($this->db->f('input_text')),
					'statustext'	=> stripslashes($this->db->f('statustext')),
					'datatype'		=> $this->db->f('datatype')
				);
			}

			if($agreement_id)
			{
				$this->db->query("SELECT $table.*,fm_vendor.member_of FROM $table $this->join fm_vendor ON $table.vendor_id = fm_vendor.id where $table.id='$agreement_id'");

				if($this->db->next_record())
				{
					$agreement['id']			= (int)$this->db->f('id');
					$agreement['entry_date']	= $this->db->f('entry_date');
					$agreement['cat_id']		= $this->db->f('category');
					$agreement['start_date']	= $this->db->f('start_date');
					$agreement['end_date']		= $this->db->f('end_date');
					$agreement['termination_date']= $this->db->f('termination_date');
					$agreement['vendor_id']		= $this->db->f('vendor_id');
					$agreement['b_account_id']	= $this->db->f('account_id');
					$agreement['name']			= stripslashes($this->db->f('name'));
					$agreement['descr']			= stripslashes($this->db->f('descr'));
					$agreement['user_id']		= $this->db->f('user_id');
					$agreement['group_id']		= $this->db->f('group_id');
					$agreement['status']		= $this->db->f('status');
					$agreement['member_of']		= explode(',',$this->db->f('member_of'));

					for ($i=0;$i<count($agreement['attributes']);$i++)
					{
						$agreement['attributes'][$i]['value'] 	= $this->db->f($agreement['attributes'][$i]['name']);
						$agreement['attributes'][$i]['datatype_text'] 	= $this->bocommon->translate_datatype($agreement['attributes'][$i]['datatype']);
					}

				}
			}
//_debug_array($agreement);
			return $agreement;
		}

		function read_single_item($data)
		{
			$attribute_table = 'fm_agreement_attribute';
			$table = 'fm_activities';

			$agreement_id =$data['agreement_id'];
			$id =$data['id'];

			$this->db->query("SELECT * FROM $attribute_table WHERE attrib_detail = 2 ORDER BY attrib_sort");

			while ($this->db->next_record())
			{
				$item['attributes'][] = array
				(
					'attrib_id'		=> $this->db->f('id'),
					'name'			=> $this->db->f('column_name'),
					'input_text'	=> stripslashes($this->db->f('input_text')),
					'statustext'	=> stripslashes($this->db->f('statustext')),
					'datatype'		=> $this->db->f('datatype')
				);
			}

			if($id && $agreement_id)
			{
				$this->db->query("SELECT * from $table $this->join fm_activity_price_index on $table.id = fm_activity_price_index.activity_id where $table.id=$id AND agreement_id=$agreement_id and index_count = 1");

				if($this->db->next_record())
				{
					$item['agreement_id']	= (int)$this->db->f('agreement_id');
					$item['id']				= (int)$this->db->f('id');
					$item['num']			= $this->db->f('num');
					$item['entry_date']		= $this->db->f('entry_date');
					$item['m_cost']			= $this->db->f('m_cost');
					$item['w_cost']			= $this->db->f('w_cost');
					$item['total_cost']		= $this->db->f('total_cost');

					for ($i=0;$i<count($item['attributes']);$i++)
					{
						$item['attributes'][$i]['value'] 	= $this->db->f($item['attributes'][$i]['name']);
						$item['attributes'][$i]['datatype_text'] 	= $this->bocommon->translate_datatype($item['attributes'][$i]['datatype']);
					}

				}
			}
			return $item;
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


			while (is_array($agreement['extra']) && list($input_name,$value) = each($agreement['extra']))
			{
				if($value)
				{
					$cols[] = $input_name;
					$vals[] = $value;
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
				$vals	= $this->bocommon->validate_db_insert($vals);
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
						$value_set[$entry['name']]	= $entry['value'];
					}
				}
			}

			$value_set['name']	= $values['name'];
			$value_set['descr']	= $values['descr'];
			$value_set['group_id']	= $values['group_id'];
			$value_set['status']	= $values['status'];
			if($value_set)
			{
				$value_set	= ',' . $this->bocommon->validate_db_update($value_set);
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
				$value_set	= ',' . $this->bocommon->validate_db_update($value_set);
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
			$floatValue = ereg_replace("(^[0-9]*)(\\.|,)([0-9]*)(.*)", "\\1.\\3", $strValue);
			if(!is_numeric($floatValue))
			{
				$floatValue = ereg_replace("(^[0-9]*)(.*)", "\\1", $strValue);
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

		function read_attrib($data)
		{
			$attribute_table = 'fm_agreement_attribute';
//html_print_r($data);
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
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$allrows = (isset($data['allrows'])?$data['allrows']:'');
				$column_list = (isset($data['column_list'])?$data['column_list']:'');
			}

			$where = 'WHERE';
			if ($column_list)
			{
				$filtermethod = " $where list !=1 or list is null";
				$where = 'AND';
			}
			if ($this->role=='detail')
			{
				$filtermethod .= " $where attrib_detail=2 ";
			}
			else
			{
				$filtermethod .= " $where attrib_detail=1 ";
			}

			$where = 'AND';

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by attrib_sort asc';
			}

			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " $where ($attribute_table.input_text $this->like '%$query%' or $attribute_table.column_name $this->like '%$query%')";
			}

			$sql = "SELECT * FROM $attribute_table $filtermethod $querymethod";

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

			while ($this->db->next_record())
			{
				$attrib[] = array
				(
					'id'			=> $this->db->f('id'),
					'attrib_sort'	=> $this->db->f('attrib_sort'),
					'list'			=> $this->db->f('list'),
					'lookup_form'	=> $this->db->f('lookup_form'),
					'column_name'	=> $this->db->f('column_name'),
					'name'			=> $this->db->f('input_text'),
					'size'			=> $this->db->f('size'),
					'statustext'	=> $this->db->f('statustext'),
					'input_text'	=> $this->db->f('input_text'),
					'type_name'		=> $this->db->f('type'),
					'datatype'		=> $this->db->f('datatype'),
					'search'		=> $this->db->f('search')
				);
			}
			return $attrib;
		}

		function read_single_attrib($id)
		{
			$attribute_table = 'fm_agreement_attribute';

			if ($this->role=='detail')
			{
				$filtermethod = " AND attrib_detail=2 ";
			}
			else
			{
				$filtermethod = " AND attrib_detail=1 ";
			}

			$sql = "SELECT * FROM $attribute_table where id=$id $filtermethod";

			$this->db->query($sql);

			if($this->db->next_record())
			{
				$attrib['id']						= $this->db->f('id');
				$attrib['column_name']				= $this->db->f('column_name');
				$attrib['input_text']				= $this->db->f('input_text');
				$attrib['statustext']				= $this->db->f('statustext');
				$attrib['column_info']['precision']	= $this->db->f('precision_');
				$attrib['column_info']['scale']		= $this->db->f('scale');
				$attrib['column_info']['default']	= $this->db->f('default_value');
				$attrib['column_info']['nullable']	= $this->db->f('nullable');
				$attrib['column_info']['type']		= $this->db->f('datatype');
				$attrib['type_name']				= $this->db->f('type_name');
				$attrib['lookup_form']				= $this->db->f('lookup_form');
				$attrib['list']						= $this->db->f('list');
				$attrib['search']					= $this->db->f('search');
				if($this->db->f('datatype')=='R' || $this->db->f('datatype')=='CH' || $this->db->f('datatype')=='LB')
				{
					$attrib['choice'] = $this->read_attrib_choice($id);
				}

				return $attrib;
			}
		}

		function read_attrib_choice($attrib_id)
		{
			$choice_table = 'fm_agreement_choice';

			if ($this->role=='detail')
			{
				$filtermethod = " AND attrib_detail=2 ";
			}
			else
			{
				$filtermethod = " AND attrib_detail=1 ";
			}

			$sql = "SELECT * FROM $choice_table WHERE attrib_id=$attrib_id $filtermethod";
			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$choice[] = array
				(
					'id'	=> $this->db->f('id'),
					'value'	=> $this->db->f('value')
				);

			}
			return $choice;
		}

		function add_attrib($attrib)
		{
			$attribute_table = 'fm_agreement_attribute';
			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['default'] = $this->db->db_addslashes($attrib['default']);
			$attrib['id'] = $this->bocommon->next_id($attribute_table, array('attrib_detail'=>!!$this->role +1));

			if($this->role=='detail')
			{
				$filtermethod= 'WHERE attrib_detail=2';
				$table = 'fm_agreement_detail';
			}
			else
			{
				$filtermethod= 'WHERE attrib_detail=1';
				$table = 'fm_agreement';
			}

			$sql = "SELECT * FROM $attribute_table $filtermethod AND column_name = '{$attrib['column_name']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				$receipt['id'] = '';
				$receipt['error'] = array();
				$receipt['error'][] = array('msg' => lang('field already exists, please choose another name'));
				$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				return $receipt; //no point continuing
			}

			$sql = "SELECT max(attrib_sort) as max_sort FROM $attribute_table $filtermethod";
			$this->db->query($sql);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('max_sort')+1;

			$values= array(
				!!$this->role +1,
				$attrib['id'],
				$attrib['column_name'],
				$attrib['input_text'],
				$attrib['statustext'],
				$attrib['lookup_form'],
				$attrib['search'],
				$attrib['list'],
				$attrib_sort,
				$attrib['column_info']['type'],
				$attrib['column_info']['precision'],
				$attrib['column_info']['scale'],
				$attrib['column_info']['default'],
				$attrib['column_info']['nullable']
				);

			$values	= $this->bocommon->validate_db_insert($values);

			$this->db->transaction_begin();

			$this->db->query("INSERT INTO $attribute_table (attrib_detail,id,column_name, input_text, statustext,lookup_form,search,list,attrib_sort,datatype,precision_,scale,default_value,nullable) "
				. "VALUES ($values)");

			$receipt['id']= $attrib['id'];

			if($attrib['column_info']['type']=='email' && !$attrib['column_info']['precision'])
			{
				$attrib['column_info']['precision']=64;
			}

			$attrib['column_info']['type']  = $this->bocommon->translate_datatype_insert($attrib['column_info']['type']);

			if($attrib['column_info']['type']=='int' && !$attrib['column_info']['precision'])
			{
				$attrib['column_info']['precision']=4;
			}

			if(!$attrib['column_info']['default'])
			{
				unset($attrib['column_info']['default']);
			}

			$this->init_process();

			if($this->oProc->AddColumn($table,$attrib['column_name'], $attrib['column_info']))
			{
				$receipt['message'][] = array('msg'	=> lang('Attribute has been saved')	);
				$this->db->transaction_commit();

			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('column could not be added')	);
				if($this->db->Transaction)
				{
					$GLOBALS['phpgw']->db->rollbacktrans();
				}
				else
				{
					$GLOBALS['phpgw']->db->Execute("DELETE FROM $attribute_table WHERE id='" . $receipt['id'] . "'");
					unset($receipt['id']);

				}
			}

			return $receipt;
		}

		function init_process()
		{
			$this->oProc 						= CreateObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$this->oProc->m_odb					= $this->db;
			$this->oProc->m_odb->Halt_On_Error	= 'report';
		}

		function get_default_column_def($table)
		{
			switch($table)
			{
				case 'fm_agreement':
					$fd=array(
						'group_id' => array('type' => 'int','precision' => '4','nullable' => False),
						'id' => array('type' => 'int','precision' => '4','nullable' => False),
						'vendor_id' => array('type' => 'int','precision' => '4','nullable' => False),
						'name' => array('type' => 'varchar','precision' => '100','nullable' => False),
						'descr' => array('type' => 'text','nullable' => True),
						'status' => array('type' => 'varchar','precision' => '10','nullable' => True),
						'entry_date' => array('type' => 'int','precision' => '4','nullable' => True),
						'start_date' => array('type' => 'int','precision' => '4','nullable' => True),
						'end_date' => array('type' => 'int','precision' => '4','nullable' => True),
						'termination_date' => array('type' => 'int','precision' => '4','nullable' => True),
						'category' => array('type' => 'int','precision' => '4','nullable' => True),
						'user_id' => array('type' => 'int','precision' => '4','nullable' => True)
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


		function edit_attrib($attrib)
		{
			$attribute_table = 'fm_agreement_attribute';
			$table = 'fm_agreement';

			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['default'] = $this->db->db_addslashes($attrib['default']);

			$choice_table = 'fm_agreement_choice';

			if($this->role=='detail')
			{
				$filtermethod= ' AND attrib_detail=2';
				$table = 'fm_agreement_detail';
			}
			else
			{
				$filtermethod= ' AND attrib_detail=1';
				$table = 'fm_agreement';
			}

			$this->db->query("SELECT column_name, datatype, precision_ FROM $attribute_table WHERE id='" . $attrib['id']. "' $filtermethod");
			$this->db->next_record();
			$OldColumnName		= $this->db->f('column_name');
			$OldDataType		= $this->db->f('datatype');
			$OldPrecision		= $this->db->f('precision_');

			$table_def = $this->get_table_def($table);	

			$this->db->transaction_begin();

			$value_set=array(
				'input_text'	=> $attrib['input_text'],
				'statustext'	=> $attrib['statustext'],
				'search'	=> $attrib['search'],
				'list'		=> $attrib['list'],
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE $attribute_table set $value_set WHERE id=" . $attrib['id'] . $filtermethod);

			$attrib_type=$attrib['column_info']['type'];

			$this->init_process();
			
			$this->oProc->m_odb->transaction_begin();

			$this->oProc->m_aTables = $table_def;


			if($OldColumnName !=$attrib['column_name'])
			{
				$value_set=array('column_name'	=> $attrib['column_name']);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE $attribute_table set $value_set WHERE id=" . $attrib['id'] . $filtermethod);

				$this->oProc->RenameColumn($table, $OldColumnName, $attrib['column_name']);
			}


			if (($OldDataType != $attrib['column_info']['type']) || ($OldPrecision != $attrib['column_info']['precision']) )
			{
				if($attrib['column_info']['type']!='R' && $attrib['column_info']['type']!='CH' && $attrib['column_info']['type']!='LB')
				{
					$this->db->query("DELETE FROM $choice_table WHERE  attrib_id=" . $attrib['id'] . $filtermethod);
				}

				if(!$attrib['column_info']['precision'])
				{
					if($precision = $this->bocommon->translate_datatype_precision($attrib['column_info']['type']))
					{
						$attrib['column_info']['precision']=$precision;
					}
				}

				if(!$attrib['column_info']['default'])
				{
					unset($attrib['column_info']['default']);
				}

				$value_set=array(
					'column_name'	=> $attrib['column_name'],
					'datatype'	=> $attrib['column_info']['type'],
					'precision_'	=> $attrib['column_info']['precision'],
					'scale'		=> $attrib['column_info']['scale'],
					'default_value'	=> $attrib['column_info']['default'],
					'nullable'	=> $attrib['column_info']['nullable']
					);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE $attribute_table set $value_set WHERE id=" . $attrib['id'] . $filtermethod);

				$attrib['column_info']['type']  = $this->bocommon->translate_datatype_insert($attrib['column_info']['type']);
				$this->oProc->AlterColumn($table,$attrib['column_name'],$attrib['column_info']);
			}

			if($attrib['new_choice'])
			{
				$this->db->query("SELECT max(id) as id FROM $choice_table WHERE attrib_id='" . $attrib['id']. "' $filtermethod");
				$this->db->next_record();
				$choice_id		= $this->db->f('id')+1;

	//			$choice_id = $this->bocommon->next_id($choice_table ,array('attrib_detail'=>2,'attrib_id'=>$attrib['id']));

				$values= array(
					$attrib['id'],
					$choice_id,
					!!$this->role +1,
					$attrib['new_choice']
					);

				$values	= $this->bocommon->validate_db_insert($values);

				$this->db->query("INSERT INTO $choice_table (attrib_id,id,attrib_detail,value) "
				. "VALUES ($values)");
			}

			if($attrib['delete_choice'])
			{
				for ($i=0;$i<count($attrib['delete_choice']);$i++)
				{
					$this->db->query("DELETE FROM $choice_table WHERE  attrib_id=" . $attrib['id']  ." AND id=" . $attrib['delete_choice'][$i] . $filtermethod);
				}
			}

			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();

			$receipt['message'][] = array('msg'	=> lang('Attribute has been edited'));

			return $receipt;
		}

		function resort_attrib($data)
		{
//html_print_r($data);
			$attribute_table = 'fm_agreement_attribute';
			if(is_array($data))
			{
				$resort = (isset($data['resort'])?$data['resort']:'up');
				$id = (isset($data['id'])?$data['id']:'');
			}

			$sql = "SELECT attrib_sort FROM $attribute_table where id=$id";
			$this->db->query($sql);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('attrib_sort');
			$sql = "SELECT max(attrib_sort) as max_sort FROM $attribute_table";
			$this->db->query($sql);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');
			switch($resort)
			{
				case 'up':
					if($attrib_sort>1)
					{
						$sql = "UPDATE $attribute_table set attrib_sort=$attrib_sort WHERE attrib_sort =" . ($attrib_sort-1);
						$this->db->query($sql);
						$sql = "UPDATE $attribute_table set attrib_sort=" . ($attrib_sort-1) ." WHERE id=$id";
						$this->db->query($sql);
					}
					break;
				case 'down':
					if($max_sort > $attrib_sort)
					{
						$sql = "UPDATE $attribute_table set attrib_sort=$attrib_sort WHERE attrib_sort =" . ($attrib_sort+1);
						$this->db->query($sql);
						$sql = "UPDATE $attribute_table set attrib_sort=" . ($attrib_sort+1) ." WHERE id=$id";
						$this->db->query($sql);
					}
					break;
				default:
					return;
					break;
			}
		}

		function delete_attrib($attrib_id)
		{
			$table = 'fm_agreement';
			$attribute_table = 'fm_agreement_attribute';
			$table_def = $this->get_table_def($table);	

			$this->init_process();
			$this->oProc->m_odb->transaction_begin();
			$this->db->transaction_begin();

			$sql = "SELECT * FROM $attribute_table WHERE id=$attrib_id";

			$this->db->query($sql);
			$this->db->next_record();
			$ColumnName		= $this->db->f('column_name');

			if($this->oProc->DropColumn($table,$table_def[$table], $ColumnName))
			{
				$sql = "SELECT attrib_sort FROM $attribute_table where id=$attrib_id";
				$this->db->query($sql);
				$this->db->next_record();
				$attrib_sort	= $this->db->f('attrib_sort');
				$sql2 = "SELECT max(attrib_sort) as max_sort FROM $attribute_table";
				$this->db->query($sql2);
				$this->db->next_record();
				$max_sort	= $this->db->f('max_sort');
				if($max_sort>$attrib_sort)
				{
					$sql = "UPDATE $attribute_table set attrib_sort=attrib_sort-1 WHERE attrib_sort > $attrib_sort";
					$this->db->query($sql);
				}

				$this->db->query("DELETE FROM $attribute_table WHERE id=$attrib_id");
			}
			else
			{
				$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been deleted'));
			}

			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();

			return $receipt;
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

		function add_activity($values='',$agreement_id='')
		{
			if (isset($values['select']) AND is_array($values['select']))
			{
				$this->db->transaction_begin();

				$this->db->query("SELECT start_date FROM fm_agreement WHERE id=" . $values['agreement_id']);
				$this->db->next_record();
				$date	= $this->db->f('start_date');

				foreach($values['select'] as $activity_id)
				{
					$this->db->query("INSERT INTO fm_activity_price_index ( agreement_id, activity_id,index_count,current_index,index_date,entry_date,user_id) "
					. "VALUES ($agreement_id,$activity_id,-1,1,$date," . time() . "," . $this->account . ")");
				}

				$this->db->transaction_commit();
			}


			$receipt['agreement_id']= $id;//$this->db->get_last_insert_id($table,'id');

			$receipt['message'][] = array('msg'=>lang('agreement %1 has been saved',$receipt['agreement_id']));

			return $receipt;
		}

		function select_status_list()
		{
			$this->db->query("SELECT id, descr FROM fm_agreement_status ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$status_entries[$i]['id']				= $this->db->f('id');
				$status_entries[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}
			return $status_entries;
		}

		function get_activity_descr($id)
		{
			$this->db->query("SELECT descr FROM fm_activities WHERE id = $id",__LINE__,__FILE__);
			$this->db->next_record();
			return stripslashes($this->db->f('descr'));
		}

	}
?>
