<?php
	/**
	* Notes
	* @author Andy Holman
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @author Dave Hall skwashd at phpgroupware.org
	* @copyright Copyright (C) 2000-2003,2005,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package notes
	* @version $Id$
	*/

	/*
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	*/

	/**
	* Notes storage object class
	*
	* @package notes
	*/
	class sonotes
	{
		var $grants;
		var $db;
		var $account;

		function sonotes()
		{
			$this->db		= &$GLOBALS['phpgw']->db;
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$GLOBALS['phpgw']->acl->set_account_id($this->account);
			$this->grants	= $GLOBALS['phpgw']->acl->get_grants('notes');
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start'])		? $data['start'] :0 ;
				$filter		= isset($data['filter'])	? $data['filter'] : 'none';
				$query		= isset($data['query'])		? $data['query'] : '';
				$sort		= isset($data['sort'])		? $data['sort'] : 'DESC';
				$order		= isset($data['order'])		? $data['order'] : '';
				$cat_id		= isset($data['cat_id'])	? $data['cat_id'] : 0;
				$lastmod 	= isset($data['lastmod'])	? $data['lastmod'] : -1;
			}

			$start	= intval($start);
			$cat_id	= intval($cat_id);

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY note_date DESC';
			}

			if ($filter == 'none')
			{
				$filtermethod = ' ( note_owner = ' . $this->account;
				if (is_array($this->grants))
				{
					$grants = $this->grants;
					while (list($user) = each($grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " OR (note_access='public' AND note_owner IN(" . implode(',',$public_user_list) . ")))";
				}
				else
				{
					$filtermethod .= ' )';
				}
			}
			elseif ($filter == 'yours')
			{
				$filtermethod = " note_owner='" . $this->account . "'";
			}
			else
			{
				$filtermethod = " note_owner='" . $this->account . "' AND note_access='private'";
			}

			if ($cat_id > 0)
			{
				$filtermethod .= " AND note_category='$cat_id' ";
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " AND note_content LIKE '%$query%' ";
			}
			else
			{
				$querymethod = '';
			}

			if ( $lastmod > 0 )
			{
				$lastmod = (int) $lastmod;
				$filtermethod .= " AND note_lastmod > $lastmod ";
			}

			$sql = "SELECT * FROM phpgw_notes WHERE $filtermethod $querymethod";

			$this->db->query($sql,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			if($start)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}

			$notes = array();
			while ($this->db->next_record())
			{
				$ngrants = $this->grants[$this->db->f('note_owner')];
				$id = $this->db->f('note_id');
				$notes[$id] = array
				(
					'note_id'	=> $id,
					'owner_id'	=> $this->db->f('note_owner'),
					'owner'		=> $this->db->f('note_owner'),
					'access'	=> $this->db->f('note_access'),
					'date'		=> $GLOBALS['phpgw']->common->show_date($this->db->f('note_date')),
					'cat_id'	=> $this->db->f('note_category'),
					'content'	=> $this->db->f('note_content', true),
					'grants'	=> $ngrants
				);
			}
			return $notes;
		}

		function read_single($note_id)
		{
			$this->db->query('SELECT * FROM phpgw_notes WHERE note_id=' . intval($note_id),__LINE__,__FILE__);

			$note = array();
			if ($this->db->next_record())
			{
				$note['id']			= $this->db->f('note_id');
				$note['owner']		= $this->db->f('note_owner');
				$note['content']	= stripslashes($this->db->f('note_content'));
				$note['access']		= $this->db->f('note_access');
				$note['date']		= $this->db->f('note_date');
				$note['cat_id']		= $this->db->f('note_category');

				return $note;
			}
		}

		function add($note)
		{
			$note['content'] = $this->db->db_addslashes($note['content']);

			$this->db->query('INSERT INTO phpgw_notes (note_owner, note_access, note_date, note_content, note_category, note_lastmod) '
				. 'VALUES (' . $this->account . ",'" . $note['access'] . "'," . time() . ",'" . $note['content']
				. "'," . (isset($note['cat_id']) ? intval($note['cat_id']) : 0) . ', ' . time() . ')',__LINE__,__FILE__);
			return $this->db->get_last_insert_id('phpgw_notes','note_id');
		}

		function edit($note)
		{
			$note['content'] = $this->db->db_addslashes($note['content']);

			$this->db->query("UPDATE phpgw_notes set note_content='" . $note['content'] . "', note_category=" . (isset($note['cat_id']) ? intval($note['cat_id']) : 0) . ', '
							. "note_access='" . $note['access'] . "', "
							. 'note_lastmod=' . time()
							. ' WHERE note_id=' . (int) $note['note_id'] ,__LINE__,__FILE__);

			return $this->db->affected_rows();
		}

		function delete($note_id)
		{
			$this->db->query('DELETE FROM phpgw_notes WHERE note_id=' . intval($note_id),__LINE__,__FILE__);
			
			return $this->db->affected_rows() > 0;
		}
	}
