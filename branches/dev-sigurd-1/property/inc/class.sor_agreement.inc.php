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

	class property_sor_agreement
	{
		var $role;

		function property_sor_agreement()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->db2           	= $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->left_join	= $this->bocommon->left_join;
			$this->like			= $this->bocommon->like;
		}

		function select_vendor_list()
		{
			return ;
			$table = 'fm_r_agreement';
			$this->db->query("SELECT vendor_id,org_name FROM $table $this->join fm_vendor on fm_r_agreement.vendor_id=fm_vendor.id GROUP BY org_name,vendor_id ");

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
				$customer_id		= (isset($data['customer_id'])?$data['customer_id']:'');
				$allrows		= (isset($data['allrows'])?$data['allrows']:'');
				$member_id		= (isset($data['member_id'])?$data['member_id']:0);
				$r_agreement_id	= (isset($data['r_agreement_id'])?$data['r_agreement_id']:'');
				$detail			= (isset($data['detail'])?$data['detail']:'');
				$loc1			= (isset($data['loc1'])?$data['loc1']:'');

			}

			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';

			if(!$detail)
			{
				$entity_table = 'fm_r_agreement';
				$category_table = 'fm_r_agreement_category';
				$attribute_filter = " appname = 'property' AND location = '.r_agreement'";

				$paranthesis .='(';
				$joinmethod .= " $this->join $category_table ON ( $entity_table.category =$category_table.id))";

				$cols = $entity_table . ".*,$category_table.descr as category";

/*				if($loc1)
				{
					$paranthesis .='(';
					$joinmethod .= " $this->join fm_r_agreement_item ON ( $entity_table.id =fm_r_agreement_item.agreement_id))";


					$cols .= ",location_code";
				}
`*/
//				$cols .= ",org_name";
//				$paranthesis .='(';
//				$joinmethod .= " $this->join fm_tenant ON ( $entity_table.customer_id =fm_tenant.id))";

				$cols_return[] 			= 'id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'id';
				$uicols['descr'][]		= lang('ID');
				$uicols['statustext'][]		= lang('ID');

				$cols_return[] 			= 'name';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'name';
				$uicols['descr'][]		= lang('name');
				$uicols['statustext'][]		= lang('name');

				$cols_return[] 			= 'customer_name';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'customer_name';
				$uicols['descr'][]		= lang('customer');
				$uicols['statustext'][]		= lang('customer');

				$cols_return[] 			= 'category';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'category';
				$uicols['descr'][]		= lang('category');
				$uicols['statustext'][]		= lang('category');

				$cols_return[] 			= 'start_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'start_date';
				$uicols['descr'][]		= lang('start');
				$uicols['statustext'][]		= lang('start date');

				$cols_return[] 			= 'end_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'end_date';
				$uicols['descr'][]		= lang('end');
				$uicols['statustext'][]		= lang('end date');

			}
			else
			{
				$allrows=true;
				$entity_table = 'fm_r_agreement_item';
				$attribute_filter = " appname = 'property' AND location = '.r_agreement.detail'";

				$paranthesis .='(';
				$joinmethod .= " $this->join  fm_r_agreement_item_history ON ( $entity_table.agreement_id =fm_r_agreement_item_history.agreement_id AND $entity_table.id =fm_r_agreement_item_history.item_id))";



				$cols = "$entity_table.*, fm_r_agreement_item_history.cost,fm_r_agreement_item_history.id as index_count,fm_r_agreement_item_history.index_date,fm_r_agreement_item_history.item_id,fm_r_agreement_item_history.this_index, rental_type_id";

				$cols_return[] 			= 'agreement_id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]		= 'agreement_id';
				$uicols['descr'][]		= lang('agreement_id');
				$uicols['statustext'][]		= lang('agreement_id');

				$cols_return[] 			= 'item_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'item_id';
				$uicols['descr'][]		= lang('ID');
				$uicols['statustext'][]		= lang('ID');

				$cols_return[] 			= 'id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]		= 'id';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;

				$cols_return[] 			= 'location_code';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'location_code';
				$uicols['descr'][]		= lang('location');
				$uicols['statustext'][]		= lang('location');

				$cols_return[] 			= 'address';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'address';
				$uicols['descr'][]		= lang('address');
				$uicols['statustext'][]		= lang('address');

/*				$cols_return[] 			= 'p_num';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'p_num';
				$uicols['descr'][]		= lang('entity num');
				$uicols['statustext'][]		= lang('entity num');
*/
				$cols_return[] 			= 'cost';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'cost';
				$uicols['descr'][]		= lang('cost');
				$uicols['statustext'][]		= lang('cost');

				$cols_return[] 			= 'this_index';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'this_index';
				$uicols['descr'][]		= lang('index');
				$uicols['statustext'][]		= lang('index');

				$cols_return[] 			= 'index_count';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'index_count';
				$uicols['descr'][]		= lang('index_count');
				$uicols['statustext'][]		= lang('index_count');

				$cols_return[] 			= 'index_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'index_date';
				$uicols['descr'][]		= lang('date');
				$uicols['statustext'][]		= lang('date');

				$cols_return[] 			= 'rental_type_id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]		= 'rental_type_id';
				$uicols['descr'][]		= lang('rental type');
				$uicols['statustext'][]		= lang('rental type');
			}

			$from .= " FROM $paranthesis $entity_table ";

			$sql = "SELECT $cols $from $joinmethod";

			$i	= count($uicols['name']);

			$user_columns=$GLOBALS['phpgw_info']['user']['preferences']['property']['r_agreement_columns' . !!$r_agreement_id];
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

//_debug_array($cols_return_extra);
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

			if ($r_agreement_id)
			{
				$filtermethod .= " $where $entity_table.agreement_id=$r_agreement_id AND current_index = 1";
				$where= 'AND';
			}

			if ($cat_id)
			{
				$filtermethod .= " $where $entity_table.category='$cat_id' ";
				$where= 'AND';
			}

			if ($customer_id)
			{
				$filtermethod .= " $where $entity_table.customer_id='$customer_id' ";
				$where= 'AND';
			}

			if ($member_id>0)
			{
				$filtermethod .= " $where $entity_table.member_of $this->like '%,$member_id,%' ";
				$where= 'AND';
			}

			if ($loc1)
			{
				$this->db->query("SELECT agreement_id FROM fm_r_agreement_item WHERE location_code $this->like '$loc1%' group by agreement_id");
				while ($this->db->next_record())
				{
					$filter_id[]			= $this->db->f('agreement_id');

				}

				if(is_array($filter_id))
				{
					$filtermethod .= " $where $entity_table.id in (" . implode(',', $filter_id)  .")";
					$where= 'AND';
				}
			}

			if ($status)
			{
				$filtermethod .= " $where $entity_table.status='$status' ";
				$where= 'AND';
			}


			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$this->db->query("SELECT * FROM $attribute_table WHERE search=1 AND $attribute_filter");

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
					$r_agreement_list[$j][$cols_return[$i]] = stripslashes($this->db->f($cols_return[$i]));
					$r_agreement_list[$j]['grants'] = (int)$grants[$this->db->f('user_id')];
				}

				for ($i=0;$i<count($cols_return_extra);$i++)
				{
					$value='';
					$value=$this->db->f($cols_return_extra[$i]['name']);

					if(($cols_return_extra[$i]['datatype']=='R' || $cols_return_extra[$i]['datatype']=='LB') && $value)
					{
						$sql="SELECT value FROM $choice_table WHERE $attribute_filter AND attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $value;
						$this->db2->query($sql);
						$this->db2->next_record();
						$r_agreement_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('value');
					}
					else if($cols_return_extra[$i]['datatype']=='AB' && $value)
					{
						$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$r_agreement_list[$j][$cols_return_extra[$i]['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];

					}
					else if($cols_return_extra[$i]['datatype']=='VENDOR' && $value)
					{
						$sql="SELECT org_name FROM fm_vendor where id=$value";
						$this->db2->query($sql);
						$this->db2->next_record();
						$r_agreement_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('org_name');
					}
					else if($cols_return_extra[$i]['datatype']=='CH' && $value)
					{
						$ch= unserialize($value);

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
							$r_agreement_list[$j][$cols_return_extra[$i]['name']] = @implode(",", $ch_value);
							unset($ch_value);
						}
					}
					else if($cols_return_extra[$i]['datatype']=='D' && $value)
					{
						$r_agreement_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
					}
					else if($cols_return_extra[$i]['datatype']=='timestamp' && $value)
					{
						$r_agreement_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$value);
					}
					else if($cols_return_extra[$i]['datatype']=='link' && $value)
					{
						$r_agreement_list[$j][$cols_return_extra[$i]['name']]= phpgw::safe_redirect($value);
					}
					else
					{
						$r_agreement_list[$j][$cols_return_extra[$i]['name']]=$value;
					}
				}

				$j++;
			}
//html_print_r($r_agreement_list);
			return $r_agreement_list;
		}

		function read_prizing($data)
		{
			if(is_array($data))
			{
				$r_agreement_id	= (isset($data['r_agreement_id'])?$data['r_agreement_id']:0);
				$item_id	= (isset($data['item_id'])?$data['item_id']:0);
			}

			$entity_table = 'fm_r_agreement_item_history';

			$cols = "fm_r_agreement_item_history.cost,fm_r_agreement_item_history.id as index_count,"
				. " fm_r_agreement_item_history.index_date,fm_r_agreement_item_history.item_id,"
				. " fm_r_agreement_item_history.this_index,tenant_id,to_date,from_date";

			$cols_return[] 			= 'item_id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'item_id';
			$uicols['descr'][]			= lang('ID');
			$uicols['statustext'][]		= lang('ID');

			$cols_return[] 			= 'id';
			$uicols['input_type'][]		= 'hidden';
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= false;
			$uicols['statustext'][]		= false;

			$cols_return[] 			= 'cost';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'cost';
			$uicols['descr'][]			= lang('cost');
			$uicols['statustext'][]		= lang('cost');

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
			$uicols['descr'][]			= lang('entry date');
			$uicols['statustext'][]		= lang('entry date');

			$cols_return[] 			= 'from_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'from_date';
			$uicols['descr'][]			= lang('from date');
			$uicols['statustext'][]		= lang('from date');

			$cols_return[] 			= 'to_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'to_date';
			$uicols['descr'][]			= lang('to date');
			$uicols['statustext'][]		= lang('to date');

			$cols_return[] 			= 'tenant_id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'tenant';
			$uicols['descr'][]			= lang('Tenant');
			$uicols['statustext'][]		= lang('Tenant');

			$from .= " FROM $entity_table ";

			$sql = "SELECT $cols $from $joinmethod";


			$this->uicols	= $uicols;

			$ordermethod = " order by $entity_table.id ASC";

			$where= 'WHERE';


			if ($r_agreement_id)
			{
				$filtermethod .= " $where $entity_table.agreement_id=$r_agreement_id AND item_id=$item_id";
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
					$r_agreement_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$r_agreement_list[$j]['agreement_id'] = $r_agreement_id;
				}
				$j++;
			}
//_debug_array($r_agreement_list);
			return $r_agreement_list;
		}

		function get_tenant_name($id)
		{
			$this->db->query("SELECT first_name,last_name FROM fm_tenant WHERE id = $id");
			$this->db->next_record();
			return stripslashes($this->db->f('first_name')) . ' ' . stripslashes($this->db->f('last_name'));

		}

		function read_single($r_agreement_id, $values = array())
		{
			$table = 'fm_r_agreement';

			$this->db->query("SELECT * from $table where id='$r_agreement_id'");

			if($this->db->next_record())
			{
				$values['id']			= (int)$this->db->f('id');
				$values['entry_date']		= $this->db->f('entry_date');
				$values['cat_id']			= $this->db->f('category');
				$values['member_of']		= explode(',',$this->db->f('member_of'));
				$values['cat_id']			= $this->db->f('category');
				$values['start_date']		= $this->db->f('start_date');
				$values['end_date']		= $this->db->f('end_date');
				$values['termination_date']= $this->db->f('termination_date');
				$values['customer_id']		= $this->db->f('customer_id');
				$values['b_account_id']	= $this->db->f('account_id');
				$values['name']			= stripslashes($this->db->f('name'));
				$values['descr']			= stripslashes($this->db->f('descr'));
				$values['user_id']			= $this->db->f('user_id');

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
			$table = 'fm_r_agreement_item';

			$r_agreement_id =$data['r_agreement_id'];
			$id =$data['id'];

			$this->db->query("SELECT * from $table where agreement_id=$r_agreement_id AND id=$id");

			if($this->db->next_record())
			{
				$values['agreement_id']			= (int)$this->db->f('agreement_id');
				$values['id']					= (int)$this->db->f('id');
				$values['entry_date']			= $this->db->f('entry_date');
				$values['location_code']		= $this->db->f('location_code');
				$values['p_num']				= $this->db->f('p_num');
				$values['p_entity_id']			= $this->db->f('p_entity_id');
				$values['p_cat_id']				= $this->db->f('p_cat_id');
				$values['cost']					= $this->db->f('cost');
				$values['tenant_id']			= $this->db->f('tenant_id');
				$values['rental_type_id']		= $this->db->f('rental_type_id');

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

		function add($r_agreement,$values_attribute='')
		{
//_debug_array($r_agreement);
			$table = 'fm_r_agreement';
			$r_agreement['name'] = $this->db->db_addslashes($r_agreement['name']);
			$r_agreement['descr'] = $this->db->db_addslashes($r_agreement['descr']);

			if($r_agreement['member_of'])
			{
				$r_agreement['member_of']=',' . implode(',',$r_agreement['member_of']) . ',';
			}


			$this->db->transaction_begin();
			$id = $this->bocommon->next_id('fm_r_agreement');

			$vals[]	= $id;
			$vals[]	= $r_agreement['name'];
			$vals[]	= $r_agreement['descr'];
			$vals[]	= time();
			$vals[]	= $r_agreement['cat_id'];
			$vals[]	= $r_agreement['member_of'];
			$vals[]	= $r_agreement['start_date'];
			$vals[]	= $r_agreement['end_date'];
			$vals[]	= $r_agreement['termination_date'];
			$vals[]	= $r_agreement['customer_id'];
			$vals[]	= $r_agreement['customer_name'];
			$vals[]	= $r_agreement['b_account_id'];
			$vals[]	= $this->account;

			if(isset($r_agreement['extra']) && is_array($r_agreement['extra']))
			{
				foreach ($r_agreement['extra'] as $input_name => $value)
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

			$vals	= $this->bocommon->validate_db_insert($vals);

			$this->db->query("INSERT INTO $table (id,name,descr,entry_date,category,member_of,start_date,end_date,termination_date,customer_id,customer_name,account_id,user_id $cols) "
				. "VALUES ($vals)",__LINE__,__FILE__);

//			$this->db->query("INSERT INTO fm_orders (id,type) VALUES ($id,'r_agreement')");

			$receipt['r_agreement_id']= $id;//$this->db->get_last_insert_id($table,'id');

			$receipt['message'][] = array('msg'=>lang('agreement %1 has been saved',$receipt['r_agreement_id']));

			$this->db->transaction_commit();
			return $receipt;
		}

		function add_item($values,$values_attribute='')
		{
//_debug_array($values);
			$table = 'fm_r_agreement_item';

			$cols[] = 'location_code';
			$vals[] = $values['location_code'];
			$cols[] = 'rental_type_id';
			$vals[] = $values['rental_type_id'];

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
						$cols[]	= $entry['name'];
						$vals[]	= $entry['value'];
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
				$vals	= "," . $this->bocommon->validate_db_insert($vals);
			}

			$this->db->transaction_begin();
			$id = $this->bocommon->next_id($table,array('agreement_id'=>$values['r_agreement_id']));

			$this->db->query("INSERT INTO $table (id,agreement_id,entry_date,user_id $cols) "
				. "VALUES ($id," . $values['r_agreement_id'] ."," . time()
				. "," . $this->account . " $vals)");


			$this->db->query("SELECT start_date FROM fm_r_agreement WHERE id=" . $values['r_agreement_id']);
			$this->db->next_record();

			if(!$values['start_date'])
			{
				$start_date	= $this->db->f('start_date');
			}
			else
			{
				$start_date	= $values['start_date'];

			}

			if ($values['end_date'])
			{
				$end_date = $values['end_date'];
			}
			else
			{
				$end_date = mktime(0, 0, 0, 12, 31, date(Y,$start_date)); // last day of start year
			}


			$this->db->query("INSERT INTO fm_r_agreement_item_history (agreement_id,item_id,id,current_index,this_index,cost,index_date,entry_date,user_id,tenant_id,from_date,to_date) "
				. "VALUES (" . $values['r_agreement_id'] . "," . $id .",1,1,1," . $this->floatval($values['cost']) . "," . (int)$start_date . "," . time()
				. "," . $this->account . "," . (int)$values['tenant_id'] . "," . (int)$start_date . "," . (int)$end_date . ")");

			$receipt['r_agreement_id']= $values['r_agreement_id'];
			$receipt['id']= $id;

			$receipt['message'][] = array('msg'=>lang('agreement %1 has been saved',$receipt['r_agreement_id']));

			$this->db->transaction_commit();
			return $receipt;
		}

		function edit($values,$values_attribute='')
		{
//_debug_array($values_attribute);
			$table = 'fm_r_agreement';

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
			$value_set['customer_id']= $values['customer_id'];
			$value_set['customer_name']= $values['customer_name'];
			if($value_set)
			{
				$value_set	= ',' . $this->bocommon->validate_db_update($value_set);
			}

			$this->db->query("UPDATE $table set entry_date='" . time() . "', category='"
							. $values['cat_id'] . "', member_of='" . $values['member_of'] . "', start_date=" . intval($values['start_date']) . ", end_date=" . intval($values['end_date']) . ", termination_date=" . intval($values['termination_date']) . ", account_id=" . intval($values['b_account_id']) .  " $value_set WHERE id=" . intval($values['r_agreement_id']));

			$this->db->query("UPDATE fm_r_agreement_item_history set index_date=" . intval($values['start_date']) . " WHERE id=1 AND agreement_id= " . intval($values['r_agreement_id']));

			$receipt['r_agreement_id']= $values['r_agreement_id'];
			$receipt['message'][] = array('msg'=>lang('agreement %1 has been edited',$values['r_agreement_id']));
			return $receipt;
		}

		function edit_item($values,$values_attribute='')
		{
//_debug_array($values);
//_debug_array($values_attribute);
			$table = 'fm_r_agreement_item';

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
			$value_set['rental_type_id']	= $values['rental_type_id'];


			if($value_set)
			{
				$value_set	= ',' . $this->bocommon->validate_db_update($value_set);
			}

			$this->db->query("UPDATE $table set entry_date=" . time() . "$value_set WHERE agreement_id=" . intval($values['r_agreement_id']) . ' AND id=' . intval($values['id']));

			$this->db->query("UPDATE fm_r_agreement_item_history set cost = this_index *" . $this->floatval($values['cost']) . " WHERE agreement_id=" . intval($values['r_agreement_id']) . ' AND item_id=' . intval($values['id']));

			$receipt['r_agreement_id']= $values['r_agreement_id'];
			$receipt['id']= $values['id'];
			$receipt['message'][] = array('msg'=>lang('agreement %1 has been edited',$values['r_agreement_id']));
			return $receipt;
		}

		function update_item_history($values)
		{
//_debug_array($values);
			$values['new_index']=$this->floatval($values['new_index']);
			$this->db->transaction_begin();

			while (is_array($values['select']) && list($item_id,$value) = each($values['select']))
			{

				$this->db->query("UPDATE fm_r_agreement_item_history set current_index = NULL WHERE agreement_id=" . intval($values['agreement_id']) . ' AND item_id=' . intval($item_id));

				$this->db->query("SELECT tenant_id,to_date from fm_r_agreement_item_history WHERE agreement_id=" . intval($values['agreement_id']) . ' AND item_id=' . intval($item_id) . ' AND id=' . intval($values['id'][$item_id]));

				$this->db->next_record();

				if(!$values['tenant_id'])
				{
					$values['tenant_id'] = $this->db->f('tenant_id');
				}

				if ($values['start_date'])
				{
					$start_date = $values['start_date'];
					if($start_date < $this->db->f('to_date'))
					{
						$start_date = $this->db->f('to_date') + (3600 * 24);
					}
				}
				else
				{
					$start_date	= $this->db->f('to_date') + (3600 * 24);
				}

				if ($values['end_date'])
				{
					$end_date = $values['end_date'];
				}
				else
				{
					$end_date = mktime(0, 0, 0, 12, 31, date(Y,$start_date)); // last day of start year
				}

				$this->db->query("INSERT INTO fm_r_agreement_item_history (agreement_id,item_id,id,current_index,this_index,cost,index_date,entry_date,tenant_id,user_id,from_date,to_date)"
					. "VALUES (" . $values['agreement_id'] . "," . $item_id ."," . ($values['id'][$item_id]+1) .",1,'" . $values['new_index']
					. "','" . ($value * $values['new_index'])  . "'," . (int)$values['date'] . "," . time()  . "," . (int)$values['tenant_id']
					. "," . $this->account . "," . (int)$start_date . "," . (int)$end_date . ")");

			}

			$this->db->transaction_commit();
			$receipt['message'][] = array('msg'=>lang('agreement %1 has been updated for index',$values['agreement_id']));

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


		function delete_last_index($r_agreement_id,$item_id)
		{
			$this->db->transaction_begin();
			$this->db->query("SELECT max(id) as index_count FROM fm_r_agreement_item_history WHERE agreement_id=$r_agreement_id AND item_id=$item_id");
			$this->db->next_record();
			$index_count	= $this->db->f('index_count');
			if($index_count>1)
			{
				$this->db->query("DELETE FROM fm_r_agreement_item_history WHERE agreement_id=$r_agreement_id AND item_id=$item_id AND id=$index_count");
				$this->db->query("UPDATE fm_r_agreement_item_history set current_index = 1 WHERE agreement_id=$r_agreement_id AND item_id=$item_id AND id =" . ($index_count-1));
			}
			$this->db->transaction_commit();
		}

		function delete_item($r_agreement_id,$item_id)
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_r_agreement_item WHERE agreement_id=$r_agreement_id AND id=$item_id");
			$this->db->query("DELETE FROM fm_r_agreement_item_history WHERE agreement_id=$r_agreement_id AND item_id=$item_id");
			$this->db->transaction_commit();
		}


		function delete($r_agreement_id)
		{
			$table = 'fm_r_agreement';
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM $table WHERE id=" . intval($r_agreement_id));
			$this->db->query("DELETE FROM fm_r_agreement_item WHERE agreement_id=" . intval($r_agreement_id));
			$this->db->query("DELETE FROM fm_r_agreement_item_history WHERE agreement_id=" . intval($r_agreement_id));
			$this->db->query("DELETE FROM fm_orders WHERE id=" . intval($r_agreement_id));
			$this->db->transaction_commit();
		}


		function request_next_id()
		{
			$this->db->query("SELECT max(id) as id FROM fm_r_agreement",__LINE__,__FILE__);
			$this->db->next_record();
			$next_id= $this->db->f('id')+1;
			return $next_id;
		}

		function add_common($values)
		{
			$table = 'fm_r_agreement_common';

			$cols[] = 'b_account';
			$vals[] = $values['b_account'];
			$cols[] = 'remark';
			$vals[] = $this->db->db_addslashes($values['remark']);

			$cols	= "," . implode(",", $cols);
			$vals	= "," . $this->bocommon->validate_db_insert($vals);

			$this->db->transaction_begin();
			$c_id = $this->bocommon->next_id($table,array('agreement_id'=>$values['r_agreement_id']));

			$this->db->query("INSERT INTO $table (id,agreement_id,entry_date,user_id $cols) "
				. "VALUES ($c_id," . $values['r_agreement_id'] ."," . time()
				. "," . $this->account . " $vals)",__LINE__,__FILE__);

			$this->db->query("SELECT start_date, end_date FROM fm_r_agreement WHERE id=" . $values['r_agreement_id'],__LINE__,__FILE__);
			$this->db->next_record();
			if ($values['start_date'])
			{
				$start_date = $values['start_date'];
				if ($start_date	< $this->db->f('start_date'))
				{
					$start_date	= $this->db->f('start_date');
				}
			}
			else
			{
				$start_date	= $this->db->f('start_date');
			}

			if ($values['end_date'])
			{
				$end_date = $values['end_date'];
			}
			else
			{
					$end_date = mktime(0, 0, 0, 12, 31, date(Y,$start_date)); // last day of start year
			}


			$this->db->query("INSERT INTO fm_r_agreement_c_history (agreement_id,c_id,id,current_record,budget_cost,from_date,to_date,user_id,override_fraction) "
				. "VALUES (" . $values['r_agreement_id'] . "," . $c_id .",1,1," . $this->floatval($values['budget_cost']) . "," . $start_date . "," . $end_date
				. "," . $this->account . "," . $this->floatval($values['override_fraction']) .")",__LINE__,__FILE__);

			$receipt['r_agreement_id']= $values['r_agreement_id'];
			$receipt['c_id']= $c_id;

			$receipt['message'][] = array('msg'=>lang('agreement %1 has been saved',$receipt['r_agreement_id']));

			$this->db->transaction_commit();
			return $receipt;
		}

		function add_common_history($values)
		{
			$table = 'fm_r_agreement_c_history';

			$this->db->transaction_begin();
			$id = $this->bocommon->next_id($table,array('agreement_id'=>$values['r_agreement_id'],'c_id' =>$values['c_id']));

			$this->db->query("SELECT from_date, to_date FROM $table WHERE agreement_id=" . $values['r_agreement_id'] . " AND c_id=" . $values['c_id'] . " AND id =" .($id-1),__LINE__,__FILE__);
			$this->db->next_record();
			if ($values['start_date'])
			{
				$start_date = $values['start_date'];
				if($start_date < $this->db->f('to_date'))
				{
					$start_date = $this->db->f('to_date') + (3600 * 24);
				}
			}
			else
			{
				$start_date	= $this->db->f('to_date') + (3600 * 24);
			}

			if ($values['end_date'])
			{
				$end_date = $values['end_date'];
			}
			else
			{
					$end_date = mktime(0, 0, 0, 12, 31, date(Y,$start_date)); // last day of start year
			}


			$this->db->query("INSERT INTO fm_r_agreement_c_history (agreement_id,c_id,id,current_record,budget_cost,from_date,to_date,user_id,override_fraction) "
				. "VALUES (" . $values['r_agreement_id'] . "," . $values['c_id'] . "," . $id .",1," . $this->floatval($values['budget_cost']) . "," . $start_date . "," . $end_date
				. "," . $this->account . "," . $this->floatval($values['override_fraction']) .")",__LINE__,__FILE__);


			$this->db->query("UPDATE fm_r_agreement_c_history set current_record = NULL WHERE agreement_id =" . $values['r_agreement_id'] . 'AND c_id=' . $values['c_id'] . 'AND id=' . ($id-1),__LINE__,__FILE__);
			$receipt['r_agreement_id']= $values['r_agreement_id'];
			$receipt['c_id']= $values['c_id'];

			$receipt['message'][] = array('msg'=>lang('agreement %1 has been saved',$receipt['r_agreement_id']));

			$this->db->transaction_commit();
			return $receipt;
		}


		function read_common($agreement_id)
		{
			$sql ="SELECT b_account,budget_cost, actual_cost,fm_r_agreement_c_history.id,from_date,"
			. " to_date,fm_r_agreement_c_history.c_id,override_fraction,remark "
			. " FROM fm_r_agreement_common $this->join  fm_r_agreement_c_history "
			. " ON ( fm_r_agreement_common.agreement_id =fm_r_agreement_c_history.agreement_id "
			. " AND fm_r_agreement_common.id =fm_r_agreement_c_history.c_id)"
			. " WHERE  fm_r_agreement_common.agreement_id = $agreement_id AND current_record = 1 ORDER BY fm_r_agreement_c_history.c_id ASC";

			$this->db->query($sql,__LINE__,__FILE__);

			$this->total_records = $this->db->num_rows();

			while ($this->db->next_record())
			{
				$common[] = array
				(
					'agreement_id'	=> $agreement_id,
					'c_id'		=> $this->db->f('c_id'),
					'b_account_id'	=> $this->db->f('b_account'),
					'from_date'	=> $this->db->f('from_date'),
					'to_date'	=> $this->db->f('to_date'),
					'budget_cost'	=> $this->db->f('budget_cost'),
					'actual_cost'	=> $this->db->f('actual_cost'),
					'override_fraction'	=> $this->db->f('override_fraction'),
					'remark'	=> stripslashes($this->db->f('remark')),
				);
			}

//_debug_array($common);
			return $common;
		}


		function read_single_common($data)
		{
			$r_agreement_id =$data['r_agreement_id'];
			$id =$data['c_id'];

			$this->db->query("SELECT * FROM fm_r_agreement_common WHERE agreement_id=$r_agreement_id AND id=$id",__LINE__,__FILE__);

			if($this->db->next_record())
			{
				$common = array
				(
					'agreement_id'	=> $r_agreement_id,
					'c_id'		=> $id,
					'b_account_id'	=> $this->db->f('b_account'),
					'remark'	=> stripslashes($this->db->f('remark')),
					'override_fraction'	=> $this->db->f('override_fraction')
				);
			}
			return $common;
		}

		function read_common_history($data)
		{
			if(is_array($data))
			{
				$r_agreement_id	= (isset($data['r_agreement_id'])?$data['r_agreement_id']:0);
				$c_id	= (isset($data['c_id'])?$data['c_id']:0);
			}

			$table = 'fm_r_agreement_common';
//echo $sql;
			$sql ="SELECT b_account,budget_cost, actual_cost,fm_r_agreement_c_history.id,from_date,"
			. " to_date,fm_r_agreement_c_history.c_id,override_fraction,remark "
			. " FROM fm_r_agreement_common $this->join  fm_r_agreement_c_history "
			. " ON ( fm_r_agreement_common.agreement_id =fm_r_agreement_c_history.agreement_id "
			. " AND fm_r_agreement_common.id =fm_r_agreement_c_history.c_id)"
			. " WHERE  fm_r_agreement_common.agreement_id = $r_agreement_id AND c_id=$c_id ORDER BY fm_r_agreement_c_history.c_id ASC";

			$this->db->query($sql,__LINE__,__FILE__);

			$this->total_records = $this->db->num_rows();

			while ($this->db->next_record())
			{
				$common[] = array
				(
					'agreement_id'	=> $id,
					'c_id'		=> $this->db->f('c_id'),
					'id'		=> $this->db->f('id'),
					'b_account_id'	=> $this->db->f('b_account'),
					'from_date'	=> $this->db->f('from_date'),
					'to_date'	=> $this->db->f('to_date'),
					'budget_cost'	=> $this->db->f('budget_cost'),
					'actual_cost'	=> $this->db->f('actual_cost'),
					'override_fraction'	=> $this->db->f('override_fraction'),
					'remark'	=> stripslashes($this->db->f('remark')),
				);
			}

			return $common;
		}

		function delete_common_h($r_agreement_id,$c_id,$id)
		{
			$this->db->transaction_begin();
			$this->db->query("DELETE FROM fm_r_agreement_c_history WHERE agreement_id=$r_agreement_id AND c_id=$c_id AND id=$id",__LINE__,__FILE__);
			$this->db->transaction_commit();
		}
	}

