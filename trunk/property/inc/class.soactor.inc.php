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
 	* @version $Id: class.soactor.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package property
	 */

	class property_soactor
	{
		var $role;

		function property_soactor()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db			= $this->bocommon->new_db();
			$this->db2			= $this->bocommon->new_db();

			$this->join			= $this->bocommon->join;
			$this->left_join	= $this->bocommon->left_join;
			$this->like			= $this->bocommon->like;
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
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:'');
				$allrows 	= (isset($data['allrows'])?$data['allrows']:'');
				$member_id 	= (isset($data['member_id'])?$data['member_id']:0);
			}

			$sql = $this->bocommon->fm_cache('sql_actor_' . $this->role);

			$entity_table = 'fm_' . $this->role;
			$category_table = 'fm_' . $this->role . '_category';
			$choice_table = 'fm_' . $this->role . '_choice';
			$attribute_table = 'fm_' . $this->role . '_attribute';
			if(!$sql)
			{
				$cols = $entity_table . ".*,$category_table.descr as category";

				$cols_return[] 				= 'id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'id';
				$uicols['descr'][]			= lang('ID');
				$uicols['statustext'][]		= lang('ID');

				$cols_return[] 				= 'id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'id';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;

				$cols_return[] 				= 'category';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'category';
				$uicols['descr'][]			= lang('category');
				$uicols['statustext'][]		= lang('category');

				$cols_return[] 				= 'entry_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'entry_date';
				$uicols['descr'][]			= lang('entry date');
				$uicols['statustext'][]		= lang('entry date');


				$paranthesis .='(';

				$joinmethod .= " $this->join  " . $entity_table . "_category ON ( $entity_table" . ".category =" .$entity_table . "_category.id))";

				$from .= " FROM $paranthesis $entity_table ";

				$sql = "SELECT $cols $from $joinmethod";

				$this->bocommon->fm_cache('sql_actor_' . $this->role,$sql);
				$this->bocommon->fm_cache('uicols_actor_' . $this->role,$uicols);
				$this->bocommon->fm_cache('cols_return_actor_' . $this->role,$cols_return);

			}
			else
			{
				$uicols 						= $this->bocommon->fm_cache('uicols_actor_'. $this->role);
				$cols_return					= $this->bocommon->fm_cache('cols_return_actor_' . $this->role);
			}

			$i	= count($uicols['name']);

			$this->db->query("SELECT * FROM $attribute_table WHERE list=1 ORDER BY attrib_sort ASC");
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

			$user_columns=isset($GLOBALS['phpgw_info']['user']['preferences']['property']['actor_columns_' . $this->role])?$GLOBALS['phpgw_info']['user']['preferences']['property']['actor_columns_' . $this->role]:'';

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
			if ($order)
			{
				$ordermethod = " order by $entity_table.$order $sort";
			}
			else
			{
				$ordermethod = " order by $entity_table.id DESC";
			}

			$where= 'WHERE';

			$grants 	= $GLOBALS['phpgw']->session->appsession('grants_' . $this->role ,'property');

			if(!$grants)
			{
				$this->acl 		= & $GLOBALS['phpgw']->acl;
				$grants	= $this->acl->get_grants('property','.' . $this->role);
				$GLOBALS['phpgw']->session->appsession('grants_' . $this->role,'property',$grants);
			}

			$filtermethod = '';
			if (is_array($grants))
			{
				foreach($grants as $user => $right)
				{
					$public_user_list[] = $user;
				}
				reset($public_user_list);
				$filtermethod .= " $where ( $entity_table.owner_id IN(" . implode(',',$public_user_list) . "))";
				$where= 'AND';
			}

			if ($cat_id)
			{
				$filtermethod .= " $where $entity_table.category='$cat_id' ";
				$where= 'AND';
			}

			if ($member_id>0)
			{
				$filtermethod .= " $where $entity_table.member_of $this->like '%,$member_id,%' ";
				$where= 'AND';
			}

/*			if ($status)
			{
				$filtermethod .= " $where $entity_table.status='$status' ";
				$where= 'AND';
			}
*/

			$querymethod = '';
			$_querymethod = array();
			if($query)
			{
				$query = preg_replace("'",'',$query);
				$query = preg_replace('"','',$query);

			//	$filtermethod .= " $where $entity_table.id ='" . (int)$query . "'";
				$where= 'AND';

				$this->db->query("SELECT * FROM $attribute_table where search='1'");

				while ($this->db->next_record())
				{
					if($this->db->f('datatype')=='V' || $this->db->f('datatype')=='email' || $this->db->f('datatype')=='CH'):
					{
						$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " $this->like '%$query%'";
					}
					elseif($this->db->f('datatype')=='I'):
					{
						if(ctype_digit($query))
						{
							$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " = " . intval($query);
						}
					}
					else:
					{
						$_querymethod[]= "$entity_table." . $this->db->f('column_name') . " = '$query'";
					}
					endif;
				}

				if (isset($_querymethod) AND is_array($_querymethod))
				{
					$querymethod = " $where (" . implode (' OR ',$_querymethod) . ')';
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
					$actor_list[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$actor_list[$j]['grants'] = (int)$grants[$this->db->f('owner_id')];
				}

				for ($i=0;$i<count($cols_return_extra);$i++)
				{
					$value='';
					$value=$this->db->f($cols_return_extra[$i]['name']);

					if(($cols_return_extra[$i]['datatype']=='R' || $cols_return_extra[$i]['datatype']=='LB') && $value):
					{
						$sql="SELECT value FROM $choice_table where attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $value;
						$this->db2->query($sql);
						$this->db2->next_record();
						$actor_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('value');
					}
					elseif($cols_return_extra[$i]['datatype']=='AB' && $value):
					{
						$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$actor_list[$j][$cols_return_extra[$i]['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];

/*						$sql="SELECT org_name FROM phpgw_addressbook where id=$value";
						$this->db2->query($sql);
						$this->db2->next_record();
						$actor_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('org_name');
*/
					}
					elseif($cols_return_extra[$i]['datatype']=='VENDOR' && $value):
					{
						$sql="SELECT org_name FROM fm_vendor where id=$value";
						$this->db2->query($sql);
						$this->db2->next_record();
						$actor_list[$j][$cols_return_extra[$i]['name']] = $this->db2->f('org_name');
					}
					elseif($cols_return_extra[$i]['datatype']=='CH' && $value):
					{
						$ch= unserialize($value);

						if (isset($ch) AND is_array($ch))
						{
							for ($k=0;$k<count($ch);$k++)
							{
								$sql="SELECT value FROM $choice_table where attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $ch[$k];
								$this->db2->query($sql);
								while ($this->db2->next_record())
								{
									$ch_value[]=$this->db2->f('value');
								}
							}
							$actor_list[$j][$cols_return_extra[$i]['name']] = @implode(",", $ch_value);
							unset($ch_value);
						}
					}
					elseif($cols_return_extra[$i]['datatype']=='D' && $value):
					{
//html_print_r($value);

						$actor_list[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
					}
					else:
					{
						$actor_list[$j][$cols_return_extra[$i]['name']]=$value;
					}
					endif;
				}

				$j++;
			}
//html_print_r($actor_list);
			return $actor_list;
		}


		function read_single($data)
		{
			$attribute_table = 'fm_' . $this->role . '_attribute';
			$table = 'fm_' . $this->role;

			$actor_id =$data['actor_id'];

			$this->db->query("SELECT * FROM $attribute_table ORDER BY attrib_sort");

			while ($this->db->next_record())
			{
				$actor['attributes'][] = array
				(
					'attrib_id'		=> $this->db->f('id'),
					'name'			=> $this->db->f('column_name'),
					'input_text'	=> stripslashes($this->db->f('input_text')),
					'statustext'	=> stripslashes($this->db->f('statustext')),
					'datatype'		=> $this->db->f('datatype')
				);
			}

			if($actor_id)
			{
				$this->db->query("SELECT * from $table where id='$actor_id'");

				if($this->db->next_record())
				{
					$actor['id']			= (int)$this->db->f('id');
					$actor['entry_date']		= $this->db->f('entry_date');
					$actor['cat_id']			= $this->db->f('category');
					$actor['member_of']			= explode(',',$this->db->f('member_of'));

					for ($i=0;$i<count($actor['attributes']);$i++)
					{
						$actor['attributes'][$i]['value'] 	= $this->db->f($actor['attributes'][$i]['name']);
						$actor['attributes'][$i]['datatype_text'] 	= $this->bocommon->translate_datatype($actor['attributes'][$i]['datatype']);
					}

				}
			}
			return $actor;
		}

		function add($actor,$values_attribute='')
		{
			$table = 'fm_' . $this->role;

			if($actor['member_of'])
			{
				$actor['member_of']=',' . implode(',',$actor['member_of']) . ',';
			}

			while (is_array($actor['extra']) && list($input_name,$value) = each($actor['extra']))
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
						if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR' && $entry['datatype']!='user')
						{
							if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V')
							{
								$entry['value'] = $this->db->db_addslashes($entry['value']);
							}
						
							if($entry['datatype'] == 'pwd' && $entry['value'] && $entry['value2'])
							{
								if($entry['value'] == $entry['value2'])
								{
									$cols[]	= $entry['name'];
									$vals[]	= md5($entry['value']);
								}
								else
								{
									$receipt['error'][]=array('msg'=>lang('Passwords do not match!'));
								}
							}
							else
							{
								$cols[]	= $entry['name'];
								$vals[]	= $entry['value'];
							}

							if($entry['history'] == 1)
							{
								$history_set[$entry['attrib_id']] = $entry['value'];
							}
						}
					}
				}
			}

			if($this->role == 'vendor')
			{
				$cols[]	= 'member_of';
				$vals[]	= $actor['member_of'];
			}

			$cols[]	= 'owner_id';
			$vals[]	= $this->account;
			
			if($cols)
			{
				$cols	= "," . implode(",", $cols);
				$vals	= "," . $this->bocommon->validate_db_insert($vals);
			}

			$this->db->transaction_begin();
			if($actor['new_actor_id'])
			{
				$id = $actor['new_actor_id'];
			}
			else
			{
				$id = $this->bocommon->next_id($table);
			}

			$this->db->query("INSERT INTO $table (id,entry_date,category $cols) "
				. "VALUES ($id,'" . time() . "','" . $actor['cat_id'] . "' $vals)");

			$receipt['actor_id']= $id;//$this->db->get_last_insert_id($table,'id');

			$receipt['message'][] = array('msg'=>lang('actor %1 has been saved',$receipt['actor_id']));

			$this->db->transaction_commit();
			return $receipt;
		}

		function edit($actor,$values_attribute='')
		{
//_debug_array($actor);
//_debug_array($values_attribute);
			$table = 'fm_' . $this->role;

			if($actor['member_of'])
			{
				$actor['member_of']=',' . implode(',',$actor['member_of']) . ',';
			}

			if(isset($actor['extra']) && is_array($actor['extra']))
			{
				foreach ($actor['extra'] as $column => $value)
				{
					$value_set[$column]	= $value;
				}
			}

			if (isset($values_attribute) AND is_array($values_attribute))
			{
				foreach($values_attribute as $entry)
				{
					if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR' && $entry['datatype']!='user')
					{
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V')
						{
							$entry['value'] = $this->db->db_addslashes($entry['value']);
						}
						if($entry['datatype'] == 'pwd')
						{
							if($entry['value'] || $entry['value2'])
							{
								if($entry['value'] == $entry['value2'])
								{
									$value_set[$entry['name']]	= md5($entry['value']);
								}
								else
								{
									$receipt['error'][]=array('msg'=>lang('Passwords do not match!'));
								}
							}
						}
						else
						{
							$value_set[$entry['name']]	= $entry['value'];
						}
					}
				}
			}

			$value_set['entry_date']	= time();
			$value_set['category']	= $actor['cat_id'];

			if($this->role == 'vendor')
			{
				$value_set['member_of']	= $actor['member_of'];
			}

			if($value_set)
			{
				$value_set	= $this->bocommon->validate_db_update($value_set);
			}

			$this->db->query("UPDATE $table set $value_set WHERE id=" . intval($actor['actor_id']));

			$receipt['actor_id']= $actor['actor_id'];
			$receipt['message'][] = array('msg'=>lang('actor %1 has been edited',$actor['actor_id']));
			return $receipt;
		}

		function delete($actor_id)
		{
			$table = 'fm_' . $this->role;
			$this->db->query("DELETE FROM $table WHERE id=" . intval($actor_id));
		}

		function read_attrib($data)
		{
			$attribute_table = 'fm_' . $this->role . '_attribute';
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
				$query = preg_replace("'",'',$query);
				$query = preg_replace('"','',$query);

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
			$attribute_table = 'fm_' . $this->role . '_attribute';

			$sql = "SELECT * FROM $attribute_table where id=$id";

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
			$choice_table = 'fm_' . $this->role . '_choice';
			$sql = "SELECT * FROM $choice_table WHERE attrib_id=$attrib_id";
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
			$table = 'fm_' . $this->role;
			$attribute_table = 'fm_' . $this->role . '_attribute';
			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['default'] = $this->db->db_addslashes($attrib['default']);
			$attrib['id'] = $this->bocommon->next_id($attribute_table);

			$sql = "SELECT * FROM $attribute_table WHERE column_name = '{$attrib['column_name']}'";
			$this->db->query($sql,__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				$receipt['id'] = '';
				$receipt['error'] = array();
				$receipt['error'][] = array('msg' => lang('field already exists, please choose another name'));
				$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				return $receipt; //no point continuing
			}


			$sql = "SELECT max(attrib_sort) as max_sort FROM $attribute_table";
			$this->db->query($sql);
			$this->db->next_record();
			$attrib_sort	= $this->db->f('max_sort')+1;

			$values= array(
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

			$this->db->query("INSERT INTO $attribute_table (id,column_name, input_text, statustext,lookup_form,search,list,attrib_sort,datatype,precision_,scale,default_value,nullable) "
				. "VALUES ($values)");

			$receipt['id']= $attrib['id'];
			
			if(!$attrib['column_info']['precision'])
			{
				$attrib['column_info']['precision'] = $this->bocommon->translate_datatype_precision($attrib['column_info']['type']);
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
				$this->db->transaction_abort();
				$this->db->query("DELETE FROM $attribute_table WHERE id='" . $receipt['id'] . "'"); // in case transactions is not supported.
				unset($receipt['id']);
			}

			return $receipt;
		}

		function init_process()
		{
			$this->oProc 				= CreateObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$this->oProc->m_odb			= $this->db;
			$this->oProc->m_odb->Halt_On_Error	= 'report';
		}


		function get_table_def()
		{
			$table = 'fm_' . $this->role;
			$attribute_table = 'fm_' . $this->role . '_attribute';

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

			$fd = $this->get_default_column_def();
			
			for ($i=0; $i<count($metadata); $i++)
			{
				$sql = "SELECT * FROM $attribute_table WHERE column_name = '" . $metadata[$i]['name'] . "'";

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
			
			$table_def[$table]['pk'] = array('id');
			$table_def[$table]['fk'] = array();			
			$table_def[$table]['ix'] = array();			
			$table_def[$table]['uc'] = array();			

			return $table_def;
		}


		function get_default_column_def()
		{		
			$fd=array();
			$fd['id'] = array('type' => 'int','precision' => '4','nullable' => False);
			$fd['entry_date'] = array('type' => 'int','precision' => '4','nullable' => True);
			$fd['category'] =array('type' => 'int','precision' => '4','nullable' => False);
			$fd['owner_id'] =array('type' => 'int','precision' => '4','nullable' => False);
			
			switch($this->role)
			{
				case 'owner':
			//		$fd['abid'] =array('type' => 'int','precision' => '4','nullable' => False);
			//		$fd['org_name'] =array('type' => 'varchar','precision' => '50','nullable' => True);
					$fd['contact_name'] =array('type' => 'varchar','precision' => '50','nullable' => True);
					$fd['member_of'] =array('type' => 'varchar','precision' => '255','nullable' => True);
			//		$fd['remark'] =array('type' => 'varchar','precision' => '255','nullable' => True);
					break;
				case 'tenant':
					$fd['member_of'] = array('type' => 'varchar','precision' => '255','nullable' => True);
					$fd['first_name'] = array('type' => 'varchar','precision' => '30','nullable' => True);
					$fd['last_name'] = array('type' => 'varchar','precision' => '30','nullable' => True);
					$fd['contact_phone'] = array('type' => 'varchar','precision' => '20','nullable' => True);

					break;
				case 'vendor':
					$fd['org_name'] = array('type' => 'varchar','precision' => '100','nullable' => True);
					$fd['email'] = array('type' => 'varchar','precision' => '64','nullable' => True);
					$fd['contact_phone'] = array('type' => 'varchar','precision' => '20','nullable' => True);
					$fd['klasse'] = array('type' => 'varchar','precision' => '10','nullable' => True);
					$fd['member_of'] = array('type' => 'varchar','precision' => '255','nullable' => True);
					$fd['mva'] = array('type' => 'int','precision' => '4','nullable' => True);

					break;
				default:
					return;
					break;
			}			
			return $fd;
		}

		function edit_attrib($attrib)
		{
			$attribute_table = 'fm_' . $this->role . '_attribute';
			$table = 'fm_' . $this->role;

			$attrib['column_name'] = strtolower($this->db->db_addslashes($attrib['column_name']));
			$attrib['input_text'] = $this->db->db_addslashes($attrib['input_text']);
			$attrib['statustext'] = $this->db->db_addslashes($attrib['statustext']);
			$attrib['default'] = $this->db->db_addslashes((isset($attrib['default'])?$attrib['default']:''));

			$choice_table = 'fm_' . $this->role . '_choice';

			$this->db->query("SELECT column_name, datatype,precision_ FROM $attribute_table WHERE id='" . $attrib['id']. "'");
			$this->db->next_record();
			$OldColumnName		= $this->db->f('column_name');
			$OldDataType		= $this->db->f('datatype');
			$OldPrecision		= $this->db->f('precision_');			

			$table_def = $this->get_table_def();	

			$this->init_process();
			$this->oProc->m_odb->transaction_begin();
			$this->db->transaction_begin();

			$value_set=array(
				'input_text'		=> $attrib['input_text'],
				'statustext'		=> $attrib['statustext'],
				'lookup_form'		=> (isset($attrib['lookup_form'])?$attrib['lookup_form']:''),
				'search'		=> (isset($attrib['search'])?$attrib['search']:''),
				'list'			=> (isset($attrib['list'])?$attrib['list']:''),
				);

			$value_set	= $this->bocommon->validate_db_update($value_set);

			$this->db->query("UPDATE $attribute_table set $value_set WHERE id=" . $attrib['id']);

			$attrib_type=$attrib['column_info']['type'];
			
			$this->oProc->m_aTables = $table_def;

			if($OldColumnName !=$attrib['column_name'])
			{
				$value_set=array('column_name'	=> $attrib['column_name']);
				$value_set	= $this->bocommon->validate_db_update($value_set);
				$this->db->query("UPDATE $attribute_table set $value_set WHERE  id=" . $attrib['id'],__LINE__,__FILE__);

				$this->oProc->RenameColumn($table, $OldColumnName, $attrib['column_name']);
			}
				
			if (($OldDataType != $attrib['column_info']['type']) || ($OldPrecision != $attrib['column_info']['precision']) )
			{				
				if($attrib_type!='R' && $attrib_type!='CH' && $attrib_type!='LB')
				{
					$this->db->query("DELETE FROM $choice_table WHERE  attrib_id=" . $attrib['id']);
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
					'datatype'		=> $attrib['column_info']['type'],
					'precision_'		=> $attrib['column_info']['precision'],
					'scale'			=> $attrib['column_info']['scale'],
					'default_value'		=> $attrib['column_info']['default'],
					'nullable'		=> $attrib['column_info']['nullable']
					);

				$value_set	= $this->bocommon->validate_db_update($value_set);

				$this->db->query("UPDATE $attribute_table set $value_set WHERE id=" . $attrib['id']);

				$attrib['column_info']['type']  = $this->bocommon->translate_datatype_insert($attrib['column_info']['type']);				
				$this->oProc->AlterColumn($table,$attrib['column_name'],$attrib['column_info']);
			}

			if(isset($attrib['new_choice']) && $attrib['new_choice'])
			{
				$choice_id = $this->bocommon->next_id($choice_table ,array('attrib_id'=>$attrib['id']));

				$values= array(
					$attrib['id'],
					$choice_id,
					$attrib['new_choice']
					);

				$values	= $this->bocommon->validate_db_insert($values);

				$this->db->query("INSERT INTO $choice_table (attrib_id,id,value) "
				. "VALUES ($values)");
			}

			if(isset($attrib['delete_choice']) && is_array($attrib['delete_choice']))
			{
				for ($i=0;$i<count($attrib['delete_choice']);$i++)
				{
					$this->db->query("DELETE FROM $choice_table WHERE  attrib_id=" . $attrib['id']  ." AND id=" . $attrib['delete_choice'][$i]);
				}
			}

			$this->db->transaction_commit();
			$this->oProc->m_odb->transaction_commit();
			
			$receipt['message'][] = array('msg'	=> lang('Attribute has been edited'));

			return $receipt;
		}

		function resort_attrib($data)
		{
			$attribute_table = 'fm_' . $this->role . '_attribute';
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
			$table = 'fm_' . $this->role;
			$attribute_table = 'fm_' . $this->role . '_attribute';
			$this->init_process();

			$sql = "SELECT * FROM $attribute_table WHERE id=$attrib_id";

			$this->db->query($sql);
			$this->db->next_record();
			$ColumnName		= $this->db->f('column_name');

			if($this->oProc->DropColumn($table,'', $ColumnName))
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

			return $receipt;
		}
	}
?>
