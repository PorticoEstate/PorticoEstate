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
	 * @subpackage custom
	 * @version $Id$
	 */

	/**
	 * Description
	 * @package property
	 */
	class property_socustom
	{

		function __construct()
		{
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db		 = & $GLOBALS['phpgw']->db;
			$this->join		 = & $this->db->join;
			$this->like		 = & $this->db->like;
		}

		function read( $data )
		{

			if (is_array($data))
			{
				$start	 = (isset($data['start']) ? $data['start'] : 0);
				$filter	 = (isset($data['filter']) ? $data['filter'] : 'none');
				$query	 = (isset($data['query']) ? $data['query'] : '');
				$sort	 = (isset($data['sort']) ? $data['sort'] : 'DESC');
				$order	 = (isset($data['order']) ? $data['order'] : '');
				$cat_id	 = (isset($data['cat_id']) ? $data['cat_id'] : 0);
				$allrows = (isset($data['allrows']) ? $data['allrows'] : '');
				$results = (isset($data['results']) ? $data['results'] : 0);
			}

			$order = ($order == 'custom_id') ? 'id' : $order;

			if ($order)
			{
				$ordermethod = " order by $order $sort";
			}
			else
			{
				$ordermethod = ' order by id DESC';
			}

			$where = 'WHERE';
			if ($cat_id > 0)
			{
				$filtermethod	 .= " $where category='$cat_id' ";
				$where			 = 'AND';
			}

			if ($query)
			{
				$query		 = $this->db->db_addslashes($query);
				$querymethod = " $where name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM fm_custom $filtermethod $querymethod";

			$this->db->query($sql, __LINE__, __FILE__);
			$this->total_records = $this->db->num_rows();

			if (!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($sql . $ordermethod, __LINE__, __FILE__);
			}

			while ($this->db->next_record())
			{
				$customs[] = array
					(
					'custom_id'	 => $this->db->f('id'),
					'name'		 => stripslashes($this->db->f('name')),
					'entry_date' => $this->db->f('entry_date'),
					'user'		 => $GLOBALS['phpgw']->accounts->id2name($this->db->f('user_id'))
				);
			}
			return $customs;
		}

		function read_single( $custom_id )
		{
			$custom_id = (int)$custom_id;
			$this->db->query("SELECT * from fm_custom where id={$custom_id}", __LINE__, __FILE__);

			$custom = array();
			if ($this->db->next_record())
			{
				$custom = array
					(
					'id'		 => (int)$this->db->f('id'),
					'name'		 => $this->db->f('name', true),
					'sql_text'	 => $this->db->f('sql_text', true),
					'entry_date' => $this->db->f('entry_date'),
					'cols'		 => $this->read_cols($custom_id)
				);
			}

			return $custom;
		}

		function read_cols( $custom_id )
		{
			$custom_id	 = (int)$custom_id;
			$sql		 = "SELECT * FROM fm_custom_cols WHERE custom_id={$custom_id} ORDER by sorting";
			$this->db->query($sql);

			$cols = array();
			while ($this->db->next_record())
			{
				$cols[] = array(
					'id'		 => $this->db->f('id'),
					'name'		 => $this->db->f('name'),
					'descr'		 => $this->db->f('descr', true),
					'sorting'	 => $this->db->f('sorting'),
					'datatype'	 => preg_match('/(name|descr|title)/i', $this->db->f('name')) ? 'text' : ''
				);
			}
			return $cols;
		}

		function read_custom_name( $custom_id )
		{
			$custom_id = (int)$custom_id;
			$this->db->query("SELECT name FROM fm_custom where id={$custom_id}", __LINE__, __FILE__);
			$this->db->next_record();
			return $this->db->f('name', true);
		}

		function add( $custom )
		{
			$custom['name']		 = $this->db->db_addslashes($custom['name']);
			$custom['sql_text']	 = $this->db->db_addslashes(htmlspecialchars_decode($custom['sql_text']));

			$this->db->transaction_begin();

			$id = $this->db->next_id('fm_custom');

			$this->db->query("INSERT INTO fm_custom (id,entry_date,sql_text,name,user_id) "
				. "VALUES ($id,'" . time() . "','" . $custom['sql_text'] . "','" . $custom['name'] . "'," . $this->account . ")", __LINE__, __FILE__);

			$receipt['custom_id'] = $id;


			$this->db->transaction_commit();

			$receipt['message'][] = array('msg' => lang('custom %1 has been saved', $receipt['custom_id']));
			return $receipt;
		}

		function edit( $custom )
		{
			$custom['name']		 = $this->db->db_addslashes($custom['name']);
			$custom['sql_text']	 = $this->db->db_addslashes(htmlspecialchars_decode($custom['sql_text']));

			$this->db->query("UPDATE fm_custom set sql_text='{$custom['sql_text']}', entry_date='" . time() . "', name='{$custom['name']}' WHERE id=" . (int)$custom['custom_id'], __LINE__, __FILE__);

			if ($custom['new_name'])
			{
				$column_id = $this->db->next_id('fm_custom_cols', array('custom_id' => $custom['custom_id']));

				$sql	 = "SELECT max(sorting) as max_sort FROM fm_custom_cols WHERE custom_id=" . $custom['custom_id'];
				$this->db->query($sql);
				$this->db->next_record();
				$sorting = (int)$this->db->f('max_sort') + 1;

				$values = array(
					$custom['custom_id'],
					$column_id,
					$custom['new_name'],
					$this->db->db_addslashes($custom['new_descr']),
					$sorting
				);

				$values = $this->db->validate_insert($values);

				$this->db->query("INSERT INTO fm_custom_cols (custom_id,id,name,descr,sorting) "
					. "VALUES ($values)");
			}


			if ($custom['delete'])
			{
				for ($i = 0; $i < count($custom['delete']); $i++)
				{

					$sql		 = "SELECT sorting FROM fm_custom_cols where custom_id=" . $custom['custom_id'] . " AND id=" . $custom['delete'][$i];
					$this->db->query($sql);
					$this->db->next_record();
					$sorting	 = $this->db->f('sorting');
					$sql2		 = "SELECT max(sorting) as max_sort FROM fm_custom_cols";
					$this->db->query($sql2);
					$this->db->next_record();
					$max_sort	 = $this->db->f('max_sort');

					if ($max_sort > $sorting)
					{
						$sql = "UPDATE fm_custom_cols set sorting=sorting-1 WHERE sorting > $sorting AND custom_id=" . $custom['custom_id'];
						$this->db->query($sql);
					}


					$this->db->query("DELETE FROM fm_custom_cols WHERE  custom_id=" . $custom['custom_id'] . " AND id=" . $custom['delete'][$i]);
				}
			}

			$receipt['custom_id']	 = $custom['custom_id'];
			$receipt['message'][]	 = array('msg' => lang('custom %1 has been edited', $custom['custom_id']));
			return $receipt;
		}

		function resort( $data )
		{
			//html_print_r($data);
			if (is_array($data))
			{
				$resort		 = (isset($data['resort']) ? $data['resort'] : 'up');
				$custom_id	 = (isset($data['id']) ? (int)$data['custom_id'] : '');
				$id			 = (isset($data['id']) ? (int)$data['id'] : 0);
			}

			$sql		 = "SELECT sorting FROM fm_custom_cols WHERE custom_id = $custom_id AND id=$id";
			$this->db->query($sql);
			$this->db->next_record();
			$sorting	 = (int)$this->db->f('sorting');
			$sql		 = "SELECT max(sorting) as max_sort FROM fm_custom_cols WHERE custom_id = $custom_id";
			$this->db->query($sql);
			$this->db->next_record();
			$max_sort	 = (int)$this->db->f('max_sort');
			switch ($resort)
			{
				case 'up':
					if ($sorting > 1)
					{
						$sql = "UPDATE fm_custom_cols set sorting=$sorting WHERE custom_id = $custom_id AND sorting =" . ($sorting - 1);
						$this->db->query($sql);
						$sql = "UPDATE fm_custom_cols set sorting=" . ($sorting - 1) . " WHERE custom_id = $custom_id AND id=$id";
						$this->db->query($sql);
					}
					break;
				case 'down':
					if ($max_sort > $sorting)
					{
						$sql = "UPDATE fm_custom_cols set sorting=$sorting WHERE custom_id = $custom_id AND sorting =" . ($sorting + 1);
						$this->db->query($sql);
						$sql = "UPDATE fm_custom_cols set sorting=" . ($sorting + 1) . " WHERE custom_id = $custom_id AND id=$id";
						$this->db->query($sql);
					}
					break;
				default:
					return;
					break;
			}
		}

		function read_custom( $data )
		{
			$start		 = isset($data['start']) && $data['start'] ? $data['start'] : 0;
			$filter		 = isset($data['filter']) && $data['filter'] ? $data['filter'] : 'none';
			$query		 = isset($data['query']) ? $this->db->db_addslashes($data['query']) : '';
			$sort		 = isset($data['sort']) && $data['sort'] ? $data['sort'] : 'DESC';
			$order		 = isset($data['order']) ? $data['order'] : '';
			$allrows	 = isset($data['allrows']) ? $data['allrows'] : '';
			$custom_id	 = isset($data['custom_id']) && $data['custom_id'] ? (int)$data['custom_id'] : 0;
			$results	 = isset($data['results']) ? (int)$data['results'] : 0;
			$update		 = !empty($data['update']) ? true : false;
			$dry_run	 = !empty($data['dry_run']) ? true : false;


			$this->db->query("SELECT sql_text FROM fm_custom where id={$custom_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$sql = $this->db->f('sql_text', true);

			$uicols			 = $this->read_cols($custom_id);
			$this->uicols	 = $uicols;

			if($dry_run)
			{
				return array();
			}

			$sysadmin	 = $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin');

			if (!$sysadmin && preg_match('/(INSERT INTO|DELETE FROM|CREATE|DROP|ALTER|UPDATE)/i', trim($sql)))
			{
				$message = lang('you are not approved for this task') . ": {$sql}";

					$GLOBALS['phpgw']->log->error(array(
					'text'	=> $message,
					'line'	=> __LINE__,
					'file'	=> __FILE__
				));

				return array(
					array($uicols[0]['name'] => $message)
				);
			}

			//FIXME:
			$ordermethod = '';
			$filtermethod = '';
			$filter_arr = array();

			$sql_arr = explode('order', strtolower($sql));
			$sql_arr1 =  explode('select ', trim(str_replace(array("\n"), ' ', $sql_arr[0])));
			$sql_arr2 =  explode('from ', trim($sql_arr1[1]));

			$filter_col_map = array();
			if($query && !$update)
			{
				$cols = $this->proper_parse_str($sql_arr2[0]);
				foreach ($uicols as $uicol)
				{
					if(!$uicol['datatype'] == 'text')
					{
						continue;
					}

					foreach ($cols as $col_name => $alias)
					{
						if($alias == $uicol['name'])
						{
							$filter_col_map[] = $col_name;
						}
						elseif (preg_match("/.{$uicol['name']}/", $col_name))
						{
							if(in_array($col_name, $filter_col_map))
							{
								continue;
							}
							$filter_col_map[] = $col_name;
						}
					}

				}

				foreach ($filter_col_map as $filter_col)
				{
					$filter_arr[] = "{$filter_col} {$this->db->like} '{$query}%'";
				}

				if(preg_match('/WHERE/i', $sql) && $filter_arr)
				{
					$filtermethod = ' AND (';
					$filtermethod .= implode(' OR ', $filter_arr) . ')';
				}
				else if ($filter_arr)
				{
					$filtermethod = ' WHERE 1=1 AND (';
					$filtermethod .= implode(' OR ', $filter_arr) . ')';
				}
			}

			$_sql = $sql_arr[0] . $filtermethod;

			if(isset($sql_arr[1]))
			{
				$ordermethod .= " ORDER {$sql_arr[1]}";
			}

			if($update)
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else if (!$allrows)
			{
				$this->db->query("SELECT count(*) as cnt FROM ({$_sql}) as t", __LINE__, __FILE__);
				$this->db->next_record();
				$this->total_records = (int)$this->db->f('cnt');

				$this->db->limit_query($_sql . $ordermethod, $start, __LINE__, __FILE__, $results);
			}
			else
			{
				$this->db->query($_sql . $ordermethod, __LINE__, __FILE__);
				$this->total_records = $this->db->num_rows();
			}

			$values = array();

			while ($this->db->next_record())
			{
				$row = array();
				foreach ($uicols as $uicol)
				{
					$row[$uicol['name']] = $this->db->f($uicol['name'], true);
				}
				$values[] = $row;
			}

			return $values;
		}

		function proper_parse_str( $str )
		{
			# result array
			$arr = array();

			# split on outer delimiter
			$pairs = explode(',', $str);

			# loop through each pair
			foreach ($pairs as $i)
			{
				# split into name and value
				list($name, $value) = explode(' as ', trim($i), 2);

				$name = str_replace(array(" ", "|", "'"),'',$name);
				$value = trim($value);

				# if name already exists
				if (isset($arr[$name]))
				{
					# stick multiple values into an array
					if (is_array($arr[$name]))
					{
						$arr[$name][] = $value;
					}
					else
					{
						$arr[$name] = array($arr[$name], $value);
					}
				}
				# otherwise, simply stick it in a scalar
				else
				{
					$arr[$name] = $value;
				}
			}

			# return result array
			return $arr;
		}

		function delete( $custom_id )
		{
			$custom_id = (int)$custom_id;
			$this->db->query("DELETE FROM fm_custom WHERE id={$custom_id}", __LINE__, __FILE__);
			$this->db->query("DELETE FROM fm_custom_cols WHERE custom_id={$custom_id}", __LINE__, __FILE__);
		}
	}