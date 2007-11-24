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
 	* @version $Id: class.sodemo.inc.php,v 1.7 2007/04/20 09:11:05 sigurdne Exp $
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
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
			$this->account			=& $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 				= clone($GLOBALS['phpgw']->db);

			$this->like 			=& $this->db->like;
			$this->join 			=& $this->db->join;
			$this->left_join		=& $this->db->left_join;
			$this->acl_location 	= $acl_location;
			
			$this->grants			= $GLOBALS['phpgw']->acl->get_grants('demo', $this->acl_location);
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
				$allrows	= (isset($data['allrows'])?$data['allrows']:'');
				$cat_id 	= (isset($data['cat_id'])?$data['cat_id']:0);
				$filter		= (isset($data['filter'])?$data['filter']:'');
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

			$sql = "SELECT COUNT(phpgw_demo_table.id) FROM $table $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$sql = "SELECT * FROM $table $filtermethod $querymethod $ordermethod";

			if ( $allrows )
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__);
			}

			$demo_info = '';
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
			$db2 = clone($this->db);

			if(is_array($data))
			{
				if ($data['start'])
				{
					$start = $data['start'];
				}
				else
				{
					$start=0;
				}
				$query				= (isset($data['query'])?$data['query']:'');
				$sort				= (isset($data['sort'])?$data['sort']:'DESC');
				$order				= (isset($data['order'])?$data['order']:'');
				$allrows			= (isset($data['allrows'])?$data['allrows']:'');
				$cat_id 			= (isset($data['cat_id'])?$data['cat_id']:0);
				$filter				= (isset($data['filter'])?$data['filter']:'');
				$custom_attributes	= (isset($data['custom_attributes'])?$data['custom_attributes']:'');
			}
			
			$contacts			= CreateObject('phpgwapi.contacts');

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

			$ordermethod = ' ORDER BY name ASC';
			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}

			$querymethod = '';
			if($query)
			{
				$query = ereg_replace("'",'',$query);
				$query = ereg_replace('"','',$query);

				$querymethod = " $where name $this->like '%$query%'";
			}


			$cols = $table . '.*';

			$cols_return[] 			= 'id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]		= 'id';
			$uicols['descr'][]		= 'ID';
			$uicols['statustext'][]	= 'Demo ID';
			$uicols['datatype'][]	= 'I';

			$cols_return[] 			= 'entry_date';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]		= 'entry_date';
			$uicols['descr'][]		= lang('Time created');
			$uicols['statustext'][]	= lang('Time created');
			$uicols['datatype'][]	= 'timestamp';
			$cols_return_extra[]= array
								(
									'name'	=> 'entry_date',
									'datatype'	=> 'timestamp'
								);

			$cols_return[] 			= 'user_id';
			$uicols['input_type'][]		= 'text';
			$uicols['name'][]		= 'user_id';
			$uicols['descr'][]		= lang('Owner');
			$uicols['statustext'][]	= lang('Owner of this record');
			$uicols['datatype'][]	= 'user_id';
			$cols_return_extra[]= array
								(
									'name'	=> 'user_id',
									'datatype'	=> 'user_id'
								);


				$i	= count($uicols['name']);
				if(isset($custom_attributes) && is_array($custom_attributes))
				{
					foreach($custom_attributes as $column_info)
					{
						if($column_info['list'])
						{
							if($column_info['datatype'] == 'link')
							{
								$uicols['input_type'][]		= 'link';
							}
							else
							{
								$uicols['input_type'][]		= 'text';
							}
							$cols_return[] 				= $column_info['column_name'];
							$uicols['name'][]			= $column_info['column_name'];
							$uicols['descr'][]			= $column_info['input_text'];
							$uicols['statustext'][]		= $column_info['statustext'];
							$uicols['datatype'][$i]		= $column_info['datatype'];
							$cols_return_extra[]= array(
								'name'	=> $column_info['column_name'],
								'datatype'	=> $column_info['datatype'],
								'attrib_id'	=> $column_info['id']
							);
							$i++;
						}
					}
				}

			$this->uicols	= $uicols;

			$sql = "SELECT COUNT(phpgw_demo_table.id) FROM $table $filtermethod $querymethod";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$sql = "SELECT * FROM $table $filtermethod $querymethod $ordermethod";
			if ( $allrows )
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__);
			}

			$demo_info = '';

			$j=0;
			$n=count($cols_return);

			while ($this->db->next_record())
			{
				for ($i=0;$i<$n;$i++)
				{
					$demo_info[$j][$cols_return[$i]] = $this->db->f($cols_return[$i]);
					$demo_info[$j]['grants'] = (int)$this->grants[$this->db->f('user_id')];
				}

				for ($i=0;$i<count($cols_return_extra);$i++)
				{
					$value='';
					$value=$this->db->f($cols_return_extra[$i]['name']);

					if(($cols_return_extra[$i]['datatype']=='R' || $cols_return_extra[$i]['datatype']=='LB') && $value)
					{
						$sql="SELECT value FROM phpgw_cust_choice WHERE appname= 'demo' AND location= '{$this->acl_location}' AND attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $value;
						$db2->query($sql);
						$db2->next_record();
						$demo_info[$j][$cols_return_extra[$i]['name']] = $db2->f('value');
					}
					else if($cols_return_extra[$i]['datatype']=='AB' && $value)
					{
						$contact_data	= $contacts->read_single_entry($value,array('n_given'=>'n_given','n_family'=>'n_family','email'=>'email'));
						$demo_info[$j][$cols_return_extra[$i]['name']]	= $contact_data[0]['n_family'] . ', ' . $contact_data[0]['n_given'];
					}
					else if($cols_return_extra[$i]['datatype']=='VENDOR' && $value)
					{
						$sql="SELECT org_name FROM fm_vendor where id=$value";
						$db2->query($sql);
						$db2->next_record();
						$demo_info[$j][$cols_return_extra[$i]['name']] = $db2->f('org_name', true);
					}
					else if($cols_return_extra[$i]['datatype']=='CH' && $value)
					{
						$ch= unserialize($value);

						if (isset($ch) AND is_array($ch))
						{
							for ($k=0;$k<count($ch);$k++)
							{
								$sql="SELECT value FROM phpgw_cust_choice WHERE appname= '{'demo'}' AND location= '{$this->acl_location}' AND attrib_id=" .$cols_return_extra[$i]['attrib_id']. "  AND id=" . $ch[$k];
								$db2->query($sql);
								while ($db2->next_record())
								{
									$ch_value[] = $db2->f('value');
								}
							}
							$demo_info[$j][$cols_return_extra[$i]['name']] = @implode(",", $ch_value);
							unset($ch_value);
						}
					}
					else if($cols_return_extra[$i]['datatype']=='D' && $value)
					{
						$demo_info[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],strtotime($value));
					}
					else if($cols_return_extra[$i]['datatype']=='timestamp' && $value)
					{
						$demo_info[$j][$cols_return_extra[$i]['name']]=date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],$value);
					}
					else if($cols_return_extra[$i]['datatype']=='user_id' && $value)
					{
						$demo_info[$j][$cols_return_extra[$i]['name']]= $GLOBALS['phpgw']->accounts->id2name($value);
					}
					else
					{
						$demo_info[$j][$cols_return_extra[$i]['name']]=$value;
					}
				}

				$j++;
			}
//_debug_array($demo_info);
			return $demo_info;
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
									$cols[]	= $entry['column_name'];
									$vals[]	= md5($entry['value']);
								}
								else
								{
									$receipt['error'][]=array('msg'=>lang('Passwords do not match!'));
								}
							}
							else
							{
								$cols[]	= $entry['column_name'];
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
									$value_set[$entry['column_name']]	= md5($entry['value']);
								}
								else
								{
									$receipt['error'][]=array('msg'=>lang('Passwords do not match!'));
								}
							}
						}
						else
						{
							$value_set[$entry['column_name']]	= $entry['value'];
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
