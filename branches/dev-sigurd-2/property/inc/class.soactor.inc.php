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

	class property_soactor
	{
		var $role;

		function property_soactor()
		{
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->custom 		= createObject('property.custom_fields');
			$this->db           = & $GLOBALS['phpgw']->db;
			$this->join			= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
			$this->like			= & $this->db->like;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start'])?$data['start']:0;
				$filter		= isset($data['filter']) && $data['filter'] ?$data['filter']:'none';
				$query		= isset($data['query'])?$data['query']:'';
				$sort		= isset($data['sort']) && $data['sort'] ? $data['sort']:'DESC';
				$order		= isset($data['order'])?$data['order']:'';
				$cat_id		= isset($data['cat_id'])?$data['cat_id']:'';
				$allrows	= isset($data['allrows'])?$data['allrows']:'';
				$member_id 	= isset($data['member_id']) && $data['member_id'] ? $data['member_id']:0;
				$dry_run	= isset($data['dry_run']) ? $data['dry_run'] : '';
			}

			$sql = $this->bocommon->fm_cache('sql_actor_' . $this->role);

			$entity_table = 'fm_' . $this->role;
			$category_table = 'fm_' . $this->role . '_category';
			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".{$this->role}");
			$attribute_filter = " location_id = {$location_id}";

			if(!$sql)
			{
				$cols_return = array();
				$uicols = array();
				$cols = $entity_table . ".*,$category_table.descr as category";

				$cols_return[] 				= 'id';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'id';
				$uicols['descr'][]			= lang('ID');
				$uicols['statustext'][]		= lang('ID');
				$uicols['datatype'][]		= false;
				$uicols['attib_id'][]		= false;

				$cols_return[] 				= 'id';
				$uicols['input_type'][]		= 'hidden';
				$uicols['name'][]			= 'id';
				$uicols['descr'][]			= false;
				$uicols['statustext'][]		= false;
				$uicols['datatype'][]		= false;
				$uicols['attib_id'][]		= false;

				$cols_return[] 				= 'category';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'category';
				$uicols['descr'][]			= lang('category');
				$uicols['statustext'][]		= lang('category');
				$uicols['datatype'][]		= false;
				$uicols['attib_id'][]		= false;

				$cols_return[] 				= 'entry_date';
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= 'entry_date';
				$uicols['descr'][]			= lang('entry date');
				$uicols['statustext'][]		= lang('entry date');
				$uicols['datatype'][]		= false;
				$uicols['attib_id'][]		= false;

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

			$user_columns=isset($GLOBALS['phpgw_info']['user']['preferences']['property']['actor_columns_' . $this->role])?$GLOBALS['phpgw_info']['user']['preferences']['property']['actor_columns_' . $this->role]:'';
			$user_column_filter = '';
			if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
			{
				$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',',$user_columns) .'))';
			}

			$this->db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter $user_column_filter ORDER BY attrib_sort ASC");

			while ($this->db->next_record())
			{
				$uicols['input_type'][]		= 'text';
				$uicols['name'][]			= $this->db->f('column_name');
				$uicols['descr'][]			= $this->db->f('input_text');
				$uicols['statustext'][]		= $this->db->f('statustext');
				$uicols['datatype'][]		= $this->db->f('datatype');
				$uicols['attib_id'][]		= $this->db->f('id');
			}

			$this->uicols	= $uicols;

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
				$query = $this->db->db_addslashes($query);

				if(ctype_digit($query))
				{
					$_querymethod[]= "$entity_table.id =" . (int)$query;
				}

				$where= 'AND';

				$this->db->query("SELECT * FROM $attribute_table WHERE $attribute_filter AND search='1'");

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
							$_querymethod[]= "$entity_table." . $this->db->f('column_name') . '=' . (int)$query;
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
			$values = array();

			if(!$dry_run)
			{
				$this->db->query('SELECT count(*) as cnt ' . substr($sql,strripos($sql,'from')),__LINE__,__FILE__);
				$this->db->next_record();
				$this->total_records = $this->db->f('cnt');
				if(!$allrows)
				{
					$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
				}
				else
				{
					$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
				}

				$cols_return = $uicols['name'];
				$j=0;

				$dataset = array();
				while ($this->db->next_record())
				{
					foreach($cols_return as $key => $field)
					{
						$dataset[$j][$field] = array
						(
							'value'		=> $this->db->f($field),
							'datatype'	=> $uicols['datatype'][$key],
							'attrib_id'	=> $uicols['attib_id'][$key]
						);
					}
					$j++;				
				}

				$values = $this->custom->translate_value($dataset, $location_id);

				return $values;
			}
			return $values;
		}

		function read_single($actor_id, $values = array())
		{
			if(is_array($actor_id))
			{
				$actor_id = $actor_id['actor_id'];
				$bt = debug_backtrace();
				echo "<b>wrong call to soactor::" . $bt[0]['function'] . "<br/>Called from file: " . $bt[0]['file'] . "<br/> line: " . $bt[0]['line'] . '<br/>args: ' . print_r($bt[0]['args'][0],true) . '</b>';
				unset($bt);
			}
			$table = 'fm_' . $this->role;

			$this->db->query("SELECT * from $table where id='$actor_id'");

			if($this->db->next_record())
			{
				$values['id']			= (int)$this->db->f('id');
				$values['entry_date']	= $this->db->f('entry_date');
				$values['cat_id']		= $this->db->f('category');
				$values['member_of']	= explode(',',$this->db->f('member_of'));

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

		function add($actor,$values_attribute='')
		{
			$table = 'fm_' . $this->role;

			if($actor['member_of'])
			{
				$actor['member_of']=',' . implode(',',$actor['member_of']) . ',';
			}

			if(isset($actor['extra']) && is_array($actor['extra']))
			{
				foreach ($actor['extra'] as $input_name => $value)
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
						if($entry['datatype']!='AB' && $entry['datatype']!='VENDOR' && $entry['datatype']!='user')
						{
							if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
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
						if($entry['datatype'] == 'C' || $entry['datatype'] == 'T' || $entry['datatype'] == 'V' || $entry['datatype'] == 'link')
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
	}

