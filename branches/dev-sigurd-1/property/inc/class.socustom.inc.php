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
		function property_socustom()
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon		= CreateObject('property.bocommon');
			$this->db           	= $this->bocommon->new_db();
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->join			= $this->bocommon->join;
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
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
			}

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
				$filtermethod .= " $where category='$cat_id' ";
				$where = 'AND';

			}

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " $where name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM fm_custom $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

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
				$customs[] = array
				(
					'custom_id'		=> $this->db->f('id'),
					'name'			=> stripslashes($this->db->f('name')),
					'entry_date'	=> $this->db->f('entry_date'),
					'user'			=> $GLOBALS['phpgw']->accounts->id2name($this->db->f('user_id'))
				);
			}
			return $customs;
		}

		function read_single($custom_id)
		{
			$this->db->query("select * from fm_custom where id='$custom_id'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$custom['id']			= (int)$this->db->f('id');
				$custom['name']			= stripslashes($this->db->f('name'));
				$custom['sql_text']			= stripslashes($this->db->f('sql_text'));
				$custom['entry_date']	= $this->db->f('entry_date');
				$custom['cols'] 		= $this->read_cols($custom_id);
			}

			return $custom;
		}

		function read_cols($custom_id)
		{
			$sql = "SELECT * FROM fm_custom_cols WHERE custom_id=$custom_id ORDER by sorting";
			$this->db->query($sql);

			while ($this->db->next_record())
			{
				$choice[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> $this->db->f('name'),
					'descr'	=> $this->db->f('descr'),
					'sorting'=> $this->db->f('sorting')
				);

			}
			return $choice;
		}

		function read_custom_name($custom_id)
		{
			$this->db->query("select name from fm_custom where id='$custom_id'",__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$name			= stripslashes($this->db->f('name'));
			}

			return $name;
		}

		function add($custom)
		{
			$custom['name'] = $this->db->db_addslashes($custom['name']);
			$custom['sql_text'] = $this->db->db_addslashes($custom['sql_text']);

			$this->db->transaction_begin();

			$id = $this->bocommon->next_id('fm_custom');

			$this->db->query("INSERT INTO fm_custom (id,entry_date,sql_text,name,user_id) "
				. "VALUES ($id,'" . time() . "','" . $custom['sql_text'] . "','" . $custom['name'] . "'," . $this->account . ")",__LINE__,__FILE__);

			$receipt['custom_id']= $id;


			$this->db->transaction_commit();

			$receipt['message'][] = array('msg'=>lang('custom %1 has been saved',$receipt['custom_id']));
			return $receipt;
		}

		function edit($custom)
		{
			$custom['name'] = $this->db->db_addslashes($custom['name']);
			$custom['sql_text'] = $this->db->db_addslashes($custom['sql_text']);

			$this->db->query("UPDATE fm_custom set sql_text='" . $custom['sql_text'] . "', entry_date='" . time() . "', name='" . $custom['name'] . "' WHERE id=" . intval($custom['custom_id']),__LINE__,__FILE__);

			if($custom['new_name'])
			{
				$column_id = $this->bocommon->next_id('fm_custom_cols' ,array('custom_id'=>$custom['custom_id']));

				$sql = "SELECT max(sorting) as max_sort FROM fm_custom_cols WHERE custom_id=" . $custom['custom_id'];
				$this->db->query($sql);
				$this->db->next_record();
				$sorting	= $this->db->f('max_sort')+1;


				$values= array(
					$custom['custom_id'],
					$column_id,
					$custom['new_name'],
					$this->db->db_addslashes($custom['new_descr']),
					$sorting
					);

				$values	= $this->bocommon->validate_db_insert($values);

				$this->db->query("INSERT INTO fm_custom_cols (custom_id,id,name,descr,sorting) "
				. "VALUES ($values)");
			}


			if($custom['delete_cols'])
			{
				for ($i=0;$i<count($custom['delete_cols']);$i++)
				{

					$sql = "SELECT sorting FROM fm_custom_cols where custom_id=" . $custom['custom_id'] . " AND id=" . $custom['delete_cols'][$i];
					$this->db->query($sql);
					$this->db->next_record();
					$sorting	= $this->db->f('sorting');
					$sql2 = "SELECT max(sorting) as max_sort FROM fm_custom_cols";
					$this->db->query($sql2);
					$this->db->next_record();
					$max_sort	= $this->db->f('max_sort');

					if($max_sort>$sorting)
					{
						$sql = "UPDATE fm_custom_cols set sorting=sorting-1 WHERE sorting > $sorting AND custom_id=" . $custom['custom_id'];
						$this->db->query($sql);
					}


					$this->db->query("DELETE FROM fm_custom_cols WHERE  custom_id=" . $custom['custom_id']  ." AND id=" . $custom['delete_cols'][$i]);
				}
			}

			$receipt['custom_id']= $custom['custom_id'];
			$receipt['message'][] = array('msg'=>lang('custom %1 has been edited',$custom['custom_id']));
			return $receipt;
		}

		function resort($data)
		{
//html_print_r($data);
			if(is_array($data))
			{
				$resort = (isset($data['resort'])?$data['resort']:'up');
				$custom_id = (isset($data['id'])?$data['custom_id']:'');
				$id = (isset($data['id'])?$data['id']:'');
			}

			$sql = "SELECT sorting FROM fm_custom_cols WHERE custom_id = $custom_id AND id=$id";
			$this->db->query($sql);
			$this->db->next_record();
			$sorting	= $this->db->f('sorting');
			$sql = "SELECT max(sorting) as max_sort FROM fm_custom_cols WHERE custom_id = $custom_id";
			$this->db->query($sql);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');
			switch($resort)
			{
				case 'up':
					if($sorting>1)
					{
						$sql = "UPDATE fm_custom_cols set sorting=$sorting WHERE custom_id = $custom_id AND sorting =" . ($sorting-1);
						$this->db->query($sql);
						$sql = "UPDATE fm_custom_cols set sorting=" . ($sorting-1) ." WHERE custom_id = $custom_id AND id=$id";
						$this->db->query($sql);
					}
					break;
				case 'down':
					if($max_sort > $sorting)
					{
						$sql = "UPDATE fm_custom_cols set sorting=$sorting WHERE custom_id = $custom_id AND sorting =" . ($sorting+1);
						$this->db->query($sql);
						$sql = "UPDATE fm_custom_cols set sorting=" . ($sorting+1) ." WHERE custom_id = $custom_id AND id=$id";
						$this->db->query($sql);
					}
					break;
				default:
					return;
					break;
			}
		}

		function read_custom($data)
		{
			if(is_array($data))
			{
				$start	= (isset($data['start'])?$data['start']:0);
				$filter	= (isset($data['filter'])?$data['filter']:'none');
				$query = (isset($data['query'])?$data['query']:'');
				$sort = (isset($data['sort'])?$data['sort']:'DESC');
				$order = (isset($data['order'])?$data['order']:'');
				$cat_id = (isset($data['cat_id'])?$data['cat_id']:0);
				$allrows 		= (isset($data['allrows'])?$data['allrows']:'');
				$custom_id 		= (isset($data['custom_id'])?$data['custom_id']:'');
			}

			$this->db->query("select sql_text from fm_custom where id='$custom_id'",__LINE__,__FILE__);
			$this->db->next_record();
			$sql = stripslashes($this->db->f('sql_text'));

			$uicols = $this->read_cols($custom_id);
			$this->uicols = $uicols;

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if(!$allrows)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$n=count($uicols);
			$j=0;
			while ($this->db->next_record())
			{
				for ($i=0;$i<$n;$i++)
				{
					$custom[$j][$uicols[$i]['name']] = $this->db->f($uicols[$i]['name']);
					$custom[$j]['grants'] = (int)$grants[$this->db->f('user_id')];
				}
			$j++;
			}

//_debug_array($custom);
			return $custom;
		}

		function delete($custom_id)
		{
			$this->db->query('DELETE FROM fm_custom WHERE id=' . intval($custom_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM fm_custom_cols WHERE custom_id=' . intval($custom_id),__LINE__,__FILE__);
		}
	}

