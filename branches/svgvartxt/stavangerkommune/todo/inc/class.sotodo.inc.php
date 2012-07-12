<?php
	/**
	* Todo storage
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2003,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @version $Id$
	*/


	/**
	* Todo storage
	*  
	* @package todo
	*/
	class sotodo
	{
		var $db;
		var $grants;
		var $historylog;
		var $owner;

		function sotodo()
		{
			$this->db          =& $GLOBALS['phpgw']->db;
			$this->grants      = $GLOBALS['phpgw']->acl->get_grants('todo');
			$this->account     = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->user_groups = $GLOBALS['phpgw']->accounts->membership($this->account);
			$this->historylog  = CreateObject('phpgwapi.historylog','todo', '.');

			// This is so our transactions follow across classes
			$this->historylog->db =& $this->db;

			$this->owner = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function type($tree)
		{
			switch ($tree)
			{
				case 'mains':
					return ' AND todo_id_parent = 0'; 
					break;
				case 'subs':
					return ' AND todo_id_parent != 0';
					break;
				default:
			}
			return '';
		}

		function read_todos($start = 0, $limit = True, $query = '', $filter = '', $order = '', $sort = '', $cat_id = '', $tree = '', $parent = '')
		{
			$type = $this->type($tree);

			if($order)
			{
				$order = $this->db->db_addslashes($order);
				$sort = $this->db->db_addslashes($sort);
				$ordermethod = "ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = 'ORDER BY todo_id_main, todo_id_parent, todo_level, todo_datecreated ASC';
			}

			$filter = strtolower($filter);

			if(!$filter)
			{
				$filter = 'none';
			}

			$filtermethod = "(( todo_owner = {$this->account} OR todo_assigned = '{$this->account}'";

             /**
              * Begin Orlando Fix
              *
              * I had to change the way $group variables were read to
              * object -> attributes
              */
			if ( is_array($this->user_groups) && count($this->user_groups) )
			{                
				$filtermethod .= " OR assigned_group IN('0'";
				foreach ( $this->user_groups as $group )
				{                    
                    $filtermethod .= ",'" . $group->id."' ";
				}
				$filtermethod .= ')';
			}
            /**
             * End Orlando Fix
             */

			$filtermethod .= ')';

			if($filter == 'none')
			{
				if(is_array($this->grants))
				{
					$grants = $this->grants;
					while(list($user) = each($grants))
					{
						$public_user_list[] = $user;
					}
					reset($public_user_list);
					$filtermethod .= " OR (todo_access='public' AND todo_owner IN(" . implode(',', $public_user_list) . '))';
				}
			}

			$filtermethod .= ')';

			if($filter == 'private')
			{
				$filtermethod .=  " AND todo_access = 'private'";
			}

			if($cat_id)
			{
				$filtermethod .= ' AND todo_cat = ' . (int) $cat_id;
			}

           
			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod = " AND (todo_des LIKE '%$query%' OR todo_title LIKE '%$query%')";
			}
            

			$parentmethod = '';
			if($parent)
			{
				$parentmethod = ' AND todo_id_parent=' . (int) $parent;
			}

			$sql = "SELECT * FROM phpgw_todo WHERE $filtermethod $querymethod $type $parentmethod ";

			if($limit)
			{
				$this->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->query($sql . $ordermethod,__LINE__,__FILE__);
			}

			$this->total_records = $this->db->num_rows();

			$todos = array();
			while($this->db->next_record())
			{
				$todos[] = array
				(
					'id'				=> (int)$this->db->f('todo_id'),
					'main'				=> (int)$this->db->f('todo_id_main'),
					'parent'			=> (int)$this->db->f('todo_id_parent'),
					'level'				=> (int)$this->db->f('todo_level'),
					'owner'				=> $this->db->f('todo_owner'),
					'owner_id'			=> $this->db->f('todo_owner'),
					'access'			=> $this->db->f('todo_access'),
					'cat'				=> (int)$this->db->f('todo_cat'),
					'title'				=> $this->db->f('todo_title', true),
					'descr'				=> $this->db->f('todo_des', true),
					'pri'				=> (int)$this->db->f('todo_pri'),
					'status'			=> (int)$this->db->f('todo_status'),
					'sdate'				=> $this->db->f('todo_startdate'),
					'edate'				=> $this->db->f('todo_enddate'),
					'grants'			=> (int)$this->grants[$this->db->f('todo_owner')],
					'sdate_epoch'		=> (int)$this->db->f('todo_startdate'),
					'edate_epoch'		=> (int)$this->db->f('todo_enddate'),
					'assigned'			=> $this->db->f('todo_assigned'),
					'assigned_group'	=> $this->db->f('assigned_group')
				);
			}
			return $todos;
		}

		function read_single_todo($todo_id)
		{
			$this->db->query('SELECT * FROM phpgw_todo WHERE todo_id = ' . (int) $todo_id, __LINE__, __FILE__);

			$todo = array();
			if ($this->db->next_record())
			{
				$todo['id']				= $this->db->f('todo_id');
				$todo['main']			= $this->db->f('todo_id_main');
				$todo['parent']			= $this->db->f('todo_id_parent');
				$todo['level']			= $this->db->f('todo_level');
				$todo['owner']			= $this->db->f('todo_owner');
				$todo['access']			= $this->db->f('todo_access');
				$todo['cat']			= $this->db->f('todo_cat');
				$todo['title']			= $this->db->f('todo_title');
				$todo['descr']			= $this->db->f('todo_des');
				$todo['pri']			= $this->db->f('todo_pri');
				$todo['status']			= $this->db->f('todo_status');
				$todo['sdate']			= $this->db->f('todo_startdate');
				$todo['edate']			= $this->db->f('todo_enddate');
				$todo['assigned']		= $this->db->f('todo_assigned');
				$todo['assigned_group']	= $this->db->f('assigned_group');
			}
			return $todo;
		}

		function add_todo($values)
		{
			$GLOBALS['phpgw']->log->message(array
			(
				'text'		=> 'debug, so add_todo values: %1',
				'p1'		=> print_r($values, true),
				'severity'	=> 'D',
				'line'		=> __LINE__,
				'file'		=> __FILE__
			));
			$GLOBALS['phpgw']->log->commit();

			$values['parent'] = (int)$values['parent'];
			if ($values['parent'] > 0)
			{
				$values['main']		= $this->return_value($values['parent']);
				$values['level']	= $this->return_value($values['parent'],'level')+1;
			}

			$values['title'] = $this->db->db_addslashes($values['title']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['assigned'] = $this->db->db_addslashes($values['assigned']);
			$values['assigned_group'] = $this->db->db_addslashes($values['assigned_group']);

            /**
             * Begin Orlando Fix
             *
             * I had to include another field in the INSERT query: entry_date
             * because it didn't accept null values, and it now stores the actual time()
             */
            $this->db->transaction_begin();
            $sql=   "insert into phpgw_todo (todo_id_main,todo_id_parent,todo_level,todo_owner,todo_access,todo_cat,todo_des,todo_title,todo_pri,todo_status,todo_datecreated,todo_startdate,todo_enddate,todo_assigned,assigned_group,entry_date) "
                    ."values ("
                    .(int)$values['main']
                    ."," . (int)$values['parent']
                    ."," . (int)$values['level']
                    ." ," . $this->account . ","
                    .(int)!!$values['access']
                    .",". (int)$values['cat']
                    .",'" . $values['descr'] ."' "
                    .",'" . $values['title'] ."' "
                    ."," . (int)$values['pri']
                    .",". (int)$values['status']
                    .",'" . time() ."'"
                    .",'" . (int)$values['sdate'] ."' "
                    .',' . (int)$values['edate']
                    .",'" . $values['assigned']
                    ."','" . $values['assigned_group'] ."'"
                    ."," .time() . ")";
           
			$this->db->query($sql, __LINE__, __FILE__);
			$todo_id = $this->db->get_last_insert_id('phpgw_todo','todo_id');
            /**
             * End Orlando Fix
             */

			if (!$values['parent'] || $values['parent'] == 0)
			{
				$this->db->query('update phpgw_todo set todo_id_main=' . $todo_id . ' where todo_id=' . $todo_id,__LINE__,__FILE__);
			}
			$this->historylog->add('A', $todo_id, '', '');
			$this->db->transaction_commit();
			return $todo_id;
		}

		function find_subs($list_parents='', $list='')
		{
			if ($list_parents == '')
			{
				return $list;
			}
			$query = "SELECT todo_id FROM phpgw_todo WHERE todo_id_parent IN ($list_parents)";
			if ($list <> '')
			{
			 	$query .= " AND todo_id NOT IN ($list)";
			}
			$this->db->query($query,__LINE__,__FILE__);
			$subs = array();
			while ($this->db->next_record())
			{
				$subs[] = $this->db->f('todo_id');
			}
			if (count($subs))
			{
				$list_subs = implode(',', $subs);
				if ($list <> '')
				{
					$list .= ',';
				}
				$list = $this->find_subs($list_subs, $list . $list_subs);
			}
			return $list;
		}

		function delete_todo($todo_id, $sub = False)
		{
			$this->db->transaction_begin();
			$sub_todos = $this->find_subs($todo_id);
			$subdelete = '';
			$parent = 0;
			if ($sub_todos)
			{
				if($sub)
				{
 					$subdelete = " OR todo_id in ($sub_todos)";
				}
				else
				{
					$parent = $this->return_value($todo_id,'parent');
				}
			}

			$this->db->query('DELETE from phpgw_todo where todo_id=' . intval($todo_id) . $subdelete . " AND ((todo_access='public' "
							. 'AND todo_owner != ' . $this->owner . ') OR (todo_owner=' . $this->owner . '))',__LINE__,__FILE__);

			if (!$sub && $sub_todos)
			{
				$this->db->query('UPDATE phpgw_todo set todo_id_parent=' . $parent . ' where todo_id_parent=' . $todo_id,__LINE__,__FILE__);
				$this->db->query("UPDATE phpgw_todo set todo_level=todo_level-1 where todo_id in ($sub_todos)",__LINE__,__FILE__);
 			}
			$this->historylog->delete($todo_id);
			$this->db->transaction_commit();
		}

		function edit_todo($values)
		{
			$values['parent']	= intval($values['parent']);
			$values['id']		= intval($values['id']);

			if($values['parent'] > 0)
			{
				$values['main']		= $this->return_value($values['parent']);
				$values['level']	= $this->return_value($values['parent'],'level')+1;
			}
			else
			{
				$values['main']		= $values['id'];
				$values['level']	= 0;
			}

			$old_values = $this->read_single_todo($values['id']);

			$this->db->transaction_begin();
			if($old_values['descr'] != $values['descr'])
			{
				$this->historylog->add('D',$values['id'],$values['descr'], $old_values['descr']);
			}

			if(($old_values['parent'] || $values['parent']) && ($old_values['parent'] != $values['parent']))
			{
				$this->historylog->add('P',$values['id'],$values['parent'], $old_values['parent']);
			}

			if($old_values['pri'] != $values['pri'])
			{
				$this->historylog->add('U',$values['id'],$values['pri'], $old_values['pri']);
			}

			if($old_values['status'] != $values['status'])
			{
				$this->historylog->add('s',$values['id'],$values['status'], $old_values['status']);
			}

			if($old_values['access'] != $values['access'])
			{
				$this->historylog->add('a',$values['id'],$values['access'], $old_values['access']);
			}

			if(($old_values['sdate'] || $values['sdate']) && ($old_values['sdate'] != $values['sdate']))
			{
				$this->historylog->add('S',$values['id'],$values['sdate'], $old_values['sdate']);
			}

			if(($old_values['edate'] || $values['edate']) && ($old_values['edate'] != $values['edate']))
			{
				$this->historylog->add('E',$values['id'],$values['edate'], $old_values['edate']);
			}

			if($old_values['title'] != $values['title'])
			{
				$this->historylog->add('T',$values['id'],$values['title'], $old_values['title']);
			}

			if($old_values['cat'] != $values['cat'])
			{
				$this->historylog->add('C',$values['id'],$values['cat'],$old_values['cat']);
			}

			$values['title'] = $this->db->db_addslashes($values['title']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$this->db->query("update phpgw_todo set todo_des='". $values['descr'] . "', todo_id_parent=" . $values['parent']
				. ', todo_pri=' . intval($values['pri']) . ", todo_status='" . $values['status'] . "', todo_id_main=" . intval($values['main'])
				. ", todo_access='" . $values['access'] . "', todo_level=" . intval($values['level'])
				. ', todo_startdate=' . intval($values['sdate']) . ', todo_enddate=' . intval($values['edate']) . ", todo_title='" . $values['title']
				. "', todo_cat=" . intval($values['cat']) . ", todo_assigned='" . $values['assigned'] . "', assigned_group='" . $values['assigned_group']
				. "' where todo_id=" . $values['id'],__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function return_value($todo_id,$action = 'main')
		{
			switch($action)
			{
				case 'main':  $item = ' todo_id_main '; break;
				case 'level': $item = ' todo_level '; break;
			}

			$this->db->query("select $item from phpgw_todo where todo_id=" . intval($todo_id),__LINE__,__FILE__);
			if($this->db->next_record())
			{
				return $this->db->f($item);
			}
		}

		function exists($todo_id)
		{
			$this->db->query('select count(*) as cnt from phpgw_todo where todo_id_parent=' . intval($todo_id),__LINE__,__FILE__);
			$this->db->next_record();

			if($this->db->f('cnt'))
			{
				return True;
			}
			else
			{
				return False;
			}
		}
	}
?>
