<?php
	/**
	* phpGroupWare - DEMO: a demo aplication.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage demo
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* import db class
	*/
	phpgw::import_class('phpgwapi.db');

	/**
	 * Description
	 * @package demo
	 */
	class demo_sodemo
	{
		var $grants;
		var $db;
		var $account;

		/**
		* @var the total number of records for a search
		*/
		public $total_records = 0;

		function demo_sodemo($acl_location)
		{
			$this->account			= & $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 				= & $GLOBALS['phpgw']->db;

			$this->like 			= & $this->db->like;
			$this->join 			= & $this->db->join;
			$this->left_join		= & $this->db->left_join;
			$this->acl_location 	= $acl_location;

			$this->custom 	= createObject('property.custom_fields');

			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->grants			= $GLOBALS['phpgw']->acl->get_grants('demo', $this->acl_location);
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
				$cat_id 	= isset($data['cat_id']) && $data['cat_id'] ? $data['cat_id'] : 0;
				$filter		= isset($data['filter']) ? $data['filter'] : '';
			}

			$table = 'phpgw_demo_table';
			$where= 'WHERE';
			$filtermethod = '';

			if (!$filter)
			{
				if (is_array($this->grants))
				{
					while (list($user) = each($this->grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where ( $table.user_id IN(" . implode(',',$public_user_list) . "))";
				}
			}
			else if ($filter == 'yours')
			{
				$filtermethod = "$where user_id='" . $this->account . "'";
			}
			else if ($filter == 'private')
			{
				$filtermethod = "$where user_id='" . $this->account . "' AND access='private'";
			}

			$where= 'AND';

			if ($cat_id > 0)
			{
				$filtermethod .= " $where category='$cat_id' ";
				$where= 'AND';
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by name asc';
			}

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " $where name $this->like '%$query%'";
			}

			$sql = "SELECT COUNT(phpgw_demo_table.id) as cnt FROM $table $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$this->total_records = $this->db->f('cnt');

			$sql = "SELECT * FROM $table $filtermethod $querymethod $ordermethod";

			if ( $allrows )
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__);
			}

			$demo_info = array();
			while ($this->db->next_record())
			{
				$demo_info[] = array
				(
					'id'			=> $this->db->f('id'),
					'name'			=> $this->db->f('name', true),
					'entry_date'	=> $this->db->f('entry_date'),
					'grants' 		=> (int)$this->grants[$this->db->f('user_id')]
				);
			}

			return $demo_info;
		}

		function read2($data)
		{
//_debug_array($data);
			$start		= isset($data['start']) && $data['start'] ? (int)$data['start'] : 0;
			$query		= isset($data['query']) ? $data['query'] : '';
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) ? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			$cat_id 	= isset($data['cat_id']) && $data['cat_id'] ? (int)$data['cat_id'] : 0;
			$filter		= isset($data['filter']) ? $data['filter'] : '';
			$dry_run	= isset($data['dry_run']) ? $data['dry_run'] : '';

//			$custom_attributes = $this->custom->find('demo', $this->acl_location, 0, '', 'ASC', 'attrib_sort', true, true);

			$table = 'phpgw_demo_table';
			$choice_table = 'phpgw_cust_choice';
			$attribute_table = 'phpgw_cust_attribute';
			$location_id = $GLOBALS['phpgw']->locations->get_id('demo', $this->acl_location);
			$attribute_filter = " location_id = {$location_id}";

			$where= 'WHERE';
			$filtermethod = '';

			if (!$filter)
			{
				if (is_array($this->grants))
				{
					while (list($user) = each($this->grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " $where ( $table.user_id IN(" . implode(',',$public_user_list) . "))";
				}
			}
			else if ($filter == 'yours')
			{
				$filtermethod = "$where user_id='" . $this->account . "'";
			}
			else if ($filter == 'private')
			{
				$filtermethod = "$where user_id='" . $this->account . "' AND access='private'";
			}

			$where= 'AND';

			if ($cat_id > 0)
			{
				$filtermethod .= " $where category='$cat_id' ";
				$where= 'AND';
			}

			$ordermethod = ' ORDER BY name ASC';
			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}

			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);

				$querymethod = " $where name $this->like '%$query%'";
			}

			$cols = $table . '.*';

			$cols_return[] 				= 'id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'id';
			$uicols['descr'][]			= 'ID';
			$uicols['statustext'][]		= 'Demo ID';
			$uicols['datatype'][]		= 'I';

			$cols_return[] 				= 'name';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'name';
			$uicols['descr'][]			= 'Name';
			$uicols['statustext'][]		= 'Name';
			$uicols['datatype'][]		= 'V';

			$cols_return[] 				= 'entry_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'entry_date';
			$uicols['descr'][]			= lang('Time created');
			$uicols['statustext'][]		= lang('Time created');
			$uicols['datatype'][]		= 'timestamp';
			$cols_return_extra[]= array
								(
									'name'		=> 'entry_date',
									'datatype'	=> 'timestamp'
								);

			$cols_return[] 				= 'user_id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]			= 'user_id';
			$uicols['descr'][]			= lang('Owner');
			$uicols['statustext'][]		= lang('Owner of this record');
			$uicols['datatype'][]		= 'user_id';
			$cols_return_extra[]= array
								(
									'name'		=> 'user_id',
									'datatype'	=> 'user_id'
								);


				$user_columns = isset($GLOBALS['phpgw_info']['user']['preferences']['demo']['columns'])?$GLOBALS['phpgw_info']['user']['preferences']['demo']['columns']:array();
				
				$_user_columns = array();
				foreach ($user_columns as $user_column_id)
				{
					if(ctype_digit($user_column_id))
					{
						$_user_columns[] = $user_column_id;
					}
				}
				$user_column_filter = '';
				if (isset($user_columns) AND is_array($user_columns) AND $user_columns[0])
				{
					$user_column_filter = " OR ($attribute_filter AND id IN (" . implode(',',$_user_columns) .'))';
				}

				$this->db->query("SELECT * FROM $attribute_table WHERE list=1 AND $attribute_filter $user_column_filter ORDER BY group_id, attrib_sort ASC");

				$i	= count($uicols['name']);
				while ($this->db->next_record())
				{
					$uicols['input_type'][]		= 'text';
					$uicols['name'][]			= $this->db->f('column_name');
					$uicols['descr'][]			= $this->db->f('input_text');
					$uicols['statustext'][]		= $this->db->f('statustext');
					$uicols['datatype'][$i]		= $this->db->f('datatype');
					$uicols['sortable'][$i]		= true;
					$uicols['exchange'][$i]		= false;
					$uicols['formatter'][$i]	= '';
					$uicols['classname'][$i]	= '';

					$uicols['cols_return_extra'][$i] = array
						(
							'name'	=> $this->db->f('column_name'),
							'datatype'	=> $this->db->f('datatype'),
							'attrib_id'	=> $this->db->f('id')					
						);


					$cols_return_extra[]= array(
						'name'	=> $this->db->f('column_name'),
						'datatype'	=> $this->db->f('datatype'),
						'attrib_id'	=> $this->db->f('id')
					);

					$i++;
				}


			$this->uicols	= $uicols;

			$sql = "SELECT COUNT(phpgw_demo_table.id) as cnt FROM $table $filtermethod $querymethod";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$this->total_records = $this->db->f('cnt');

			if($dry_run)
			{
				return array();
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod $ordermethod";
			if ( $allrows )
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__);
			}

			$values = array();
			$cols_return = $uicols['name'];

			$dataset = array();
			$row = 0;
			while ($this->db->next_record())
			{
				foreach($cols_return as $key => $field)
				{
					$dataset[$row][$field] = array
					(
						'value'		=> $this->db->f($field),
						'datatype'	=> $uicols['datatype'][$key],
						'attrib_id'	=> $uicols['cols_return_extra'][$key]['attrib_id']
					);
				}
				$row ++;
			}

			$values = $this->custom->translate_value($dataset, $location_id);

			return $values;
		}

		/**
		* Read a single record
		*/
		function read_single($id, $values = array() )
		{
			$sql = 'SELECT * FROM phpgw_demo_table WHERE id = ' . (int) $id;

			$this->db->query($sql, __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$values['id']			= $id;
				$values['name']			= $this->db->f('name', true);
				$values['address']		= $this->db->f('address', true);
				$values['remark']		= $this->db->f('remark', true);
				$values['town']			= $this->db->f('town', true);
				$values['zip']			= $this->db->f('zip', true);
				$values['entry_date']	= $this->db->f('entry_date');
				$values['user_id']		= $this->db->f('user_id');
				$values['cat_id']		= $this->db->f('category');
				$values['access']		= $this->db->f('access');
				$values['grants'] 		= (int)$this->grants[$this->db->f('user_id')];

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
			$this->db->transaction_begin();

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['address'] = $this->db->db_addslashes($values['address']);
			$values['town'] = $this->db->db_addslashes($values['town']);
			$values['remark'] = $this->db->db_addslashes($values['remark']);

			$insert_values=array(
				$values['name'],
				$values['address'],
				$values['zip'],
				$values['town'],
				$values['remark'],
				(int)$values['cat_id'],
				(isset($values['access'])?'private':''),
				$this->account,
				time()
				);

			if(isset($values['extra']) && is_array($values['extra']))
			{
				while (is_array($values['extra']) && list($input_name,$value) = each($values['extra']))
				{
					if($value)
					{
						$cols[] = $input_name;
						$vals[] = $value;
					}
				}
			}

			if (isset($values_attribute) && is_array($values_attribute))
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

			$insert_values	= $this->db->validate_insert($insert_values);

			if(isset($cols) && is_array($cols))
			{
				$cols	= "," . implode(",", $cols);
				$vals	= "," . $this->db->validate_insert($vals);
			}
			else
			{
				$cols = '';
				$vals = '';
			}

			$this->db->query("INSERT INTO phpgw_demo_table (name, address, zip, town, remark, category, access, user_id, entry_date $cols) "
				. "VALUES ($insert_values $vals)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('demo item has been saved'));
			$receipt['demo_id']= $this->db->get_last_insert_id('phpgw_demo_table', 'id');

			$this->db->transaction_commit();

			return $receipt;
		}

		function edit($values,$values_attribute='')
		{
			$this->db->transaction_begin();

			$value_set['name']			= $this->db->db_addslashes($values['name']);
			$value_set['address']		= $this->db->db_addslashes($values['address']);
			$value_set['zip']			= $values['zip'];
			$value_set['remark']		= $this->db->db_addslashes($values['remark']);
			$value_set['town']			= $this->db->db_addslashes($values['town']);
			$value_set['category']		= (int)$values['cat_id'];
			$value_set['access']		= (isset($values['access'])?'private':'');

			if(isset($values['extra']) && is_array($values['extra']))
			{
				while (is_array($values['extra']) && list($column,$value) = each($values['extra']))
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

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE phpgw_demo_table set $value_set WHERE id=" . $values['demo_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('demo item has been edited'));

			$receipt['demo_id']= $values['demo_id'];
			return $receipt;
		}

		function delete($id)
		{
			$this->db->query('DELETE FROM phpgw_demo_table WHERE id='  . (int) $id, __LINE__, __FILE__);
		}
	}
