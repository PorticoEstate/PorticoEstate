<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage job
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_sojob
	{
		var $total_records = 0;
		
		/**
		* @var array $move_child the children to be moved
		* @internal I don't think this is really needed - skwashd nov07
		*/
		private $move_child = array();
		
		public function __construct()
		{
			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->db 			= & $GLOBALS['phpgw']->db;
			$this->like 		= & $this->db->like;
			$this->join 		= & $this->db->join;
			$this->left_join	= & $this->db->left_join;
		}

		function read($data)
		{
			if(is_array($data))
			{
				$start		= isset($data['start']) && $data['start'] ? $data['start'] : 0;
				$query		= isset($data['query']) ? $data['query'] : '';
				$sort		= isset($data['sort']) && $data['sort'] == 'ASC' ? $data['sort'] : 'DESC';
				$order		= isset($data['order']) ? $data['order'] : '';
				$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			}

			$ordermethod = ' order by name asc';
			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}

			$table = 'phpgw_hrm_job';

			$parent_select = ' WHERE job_level =0';

			$where = '';
			$querymethod = '';
			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$where = ' AND';
				$querymethod = " name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table ";

			$this->db->query($sql . $parent_select . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$jobs = array();
			while ($this->db->next_record())
			{
				$jobs[] = array
				(
					'id'	=> $this->db->f('id'),
					'level'	=> (int)$this->db->f('job_level'),
					'owner'	=> (int)$this->db->f('owner'),
					'name'	=> stripslashes($this->db->f('name')),
					'descr'	=> stripslashes($this->db->f('descr')),
					'parent'=> 0
				);
			}

			if ($querymethod)
			{
				$where = ' WHERE';
				$and = ' AND';
			}
			else
			{
				$where = '';
				$and = ' WHERE';
			}
			$num_jobs = count($jobs);
			for ( $i = 0 ; $i < $num_jobs; ++$i )
			{
				$sub_select = $and . ' job_parent=' . (int) $jobs[$i]['id'] . " AND job_level=" . ++$jobs[$i]['level'];

				$this->db->query($sql . $where . $querymethod . $sub_select . $ordermethod,__LINE__,__FILE__);

				$this->total_records += $this->db->num_rows();

				$subjobs = array();
				while ($this->db->next_record())
				{
					$subjobs[] = array
					(
						'id'		=> (int)$this->db->f('id'),
						'owner'		=> (int)$this->db->f('owner'),
						'level'		=> (int)$this->db->f('job_level'),
						'parent'	=> (int)$this->db->f('job_parent'),
						'name'		=> $this->db->f('name'),
						'descr'		=> $this->db->f('descr')
					);
				}

				$num_subjobs = count($subjobs);
				if ($num_subjobs != 0)
				{
					$newjobs = array();
					for ($k = 0; $k <= $i; $k++)
					{
						$newjobs[$k] = $jobs[$k];
					}
					for ($k = 0; $k < $num_subjobs; $k++)
					{
						$newjobs[$k+$i+1] = $subjobs[$k];
					}
					for ($k = $i+1; $k < $num_jobs; $k++)
					{
						$newjobs[$k+$num_subjobs] = $jobs[$k];
					}
					$jobs = $newjobs;
					$num_jobs = count($jobs);
				}

			}

			if(!$allrows)
			{
				$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])?intval($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']):15;
				$page = ceil( ( $start / $this->total_records ) * ($this->total_records/ $num_rows) );
				$out = array_chunk($jobs, $num_rows);
				$jobs = $out[$page];
			}

			$sql = "SELECT count(*) as quali_count,job_id FROM phpgw_hrm_quali GROUP BY job_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$quali = array();
			while ($this->db->next_record())
			{
				$quali[$this->db->f('job_id')]  = $this->db->f('quali_count');
			}

			$sql = "SELECT count(*) as task_count,job_id FROM phpgw_hrm_task GROUP BY job_id";
			$this->db->query($sql,__LINE__,__FILE__);
			$task = array();
			while ($this->db->next_record())
			{
				$task[$this->db->f('job_id')]  = $this->db->f('task_count');
			}

			if (is_array($jobs))
			{
				foreach ( $jobs as &$job )
				{
					$job['quali_count'] =  isset($quali[$job['id']]) ? $quali[$job['id']] : 0;
					$job['task_count']  =  isset($task[$job['id']]) ? $task[$job['id']] : 0;
				}

			}

			return $jobs;
		}


		function read_single_job($id)
		{

			$table = 'phpgw_hrm_job';

			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$job['id']		= $id;
				$job['parent_id']	= (int)$this->db->f('job_parent');
				$job['entry_date']	= $this->db->f('entry_date');
				$job['name']	= stripslashes($this->db->f('name'));
				$job['descr']	= stripslashes($this->db->f('descr'));

				return $job;
			}
		}

		function read_single_task($id)
		{

			$table = 'phpgw_hrm_task';

			$sql = "SELECT * FROM $table  where id='$id'";

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$task['id']		= $id;
				$task['parent_id']	= (int)$this->db->f('task_parent');
				$task['entry_date']	= $this->db->f('entry_date');
				$task['name']	= stripslashes($this->db->f('name'));
				$task['descr']	= stripslashes($this->db->f('descr'));

				return $task;
			}
		}


		function read_qualification($data)
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
			}

			$job_id = $data['job_id'];

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by value_sort asc';
			}

			$groupmethod = ' GROUP BY phpgw_hrm_quali.category';

			$sql = "SELECT phpgw_hrm_quali.id as quali_id, phpgw_hrm_quali.remark as remark,phpgw_hrm_quali_category.descr as category,phpgw_hrm_quali_type.name,phpgw_hrm_quali_type.descr from phpgw_hrm_quali"
				. " $this->join phpgw_hrm_quali_type ON phpgw_hrm_quali.quali_type_id = phpgw_hrm_quali_type.id"
				. " $this->join phpgw_hrm_quali_category ON phpgw_hrm_quali.category = phpgw_hrm_quali_category.id"
				. " WHERE job_id=" . intval($job_id);

			$parent_select = ' AND (is_parent =1 OR (is_parent = 0 AND quali_parent IS NULL))';

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " AND name $this->like '%$query%'";
			}


			$this->db->query($sql . $parent_select . $querymethod . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$value_sort = 1;
			while ($this->db->next_record())
			{
				$qualifications[] = array
				(
					'quali_id'	=> $this->db->f('quali_id'),
					'level'	=> 0,
					'name'	=> stripslashes($this->db->f('name')),
					'descr'	=> stripslashes($this->db->f('descr')),
					'remark'	=> stripslashes($this->db->f('remark')),
					'category'	=> stripslashes($this->db->f('category')),
					'parent'=> 0,
					'value_sort'	=> $value_sort,
				);
				$value_sort ++;
			}

			$num_qualifications = count($qualifications);
			for ($i=0;$i < $num_qualifications;$i++)
			{
				$sub_select = ' AND quali_parent=' . (int) $qualifications[$i]['quali_id'] . " AND is_parent = 0";

				$this->db->query($sql . $querymethod . $sub_select . $ordermethod,__LINE__,__FILE__);

				$this->total_records += $this->db->num_rows();

				$subqualifications = array();
				$j = 0;
				$value_sort = 1;
				while ($this->db->next_record())
				{
					$subqualifications[$j]['quali_id']	= (int)$this->db->f('quali_id');
					$subqualifications[$j]['level']		= 1;
					$subqualifications[$j]['parent']	= (int)$this->db->f('quali_parent');
					$subqualifications[$j]['name']		= $this->db->f('name');
					$subqualifications[$j]['descr'] 	= $this->db->f('descr');
					$subqualifications[$j]['value_sort']	= $value_sort;
					$j++;
					$value_sort ++;
				}

				$num_subqualifications = count($subqualifications);
				if ($num_subqualifications != 0)
				{
					$newqualifications = array();
					for ($k = 0; $k <= $i; $k++)
					{
						$newqualifications[$k] = $qualifications[$k];
					}
					for ($k = 0; $k < $num_subqualifications; $k++)
					{
						$newqualifications[$k+$i+1] = $subqualifications[$k];
					}
					for ($k = $i+1; $k < $num_qualifications; $k++)
					{
						$newqualifications[$k+$num_subqualifications] = $qualifications[$k];
					}
					$qualifications = $newqualifications;
					$num_qualifications = count($qualifications);
				}

			}
//_debug_array($qualifications);

			return $qualifications;
		}


		function read_single_qualification($id)
		{

			$sql = "SELECT * , phpgw_hrm_quali_type.id as quali_type_id FROM phpgw_hrm_quali $this->join phpgw_hrm_quali_type ON phpgw_hrm_quali.quali_type_id = phpgw_hrm_quali_type.id WHERE phpgw_hrm_quali.id=" . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$values['id']		= $id;
				$values['quali_type_id']	= $this->db->f('quali_type_id');
				$values['name']		= stripslashes($this->db->f('name'));
				$values['descr']	= stripslashes($this->db->f('descr'));
				$values['remark']	= stripslashes($this->db->f('remark'));
				$values['job_id']	= $this->db->f('job_id');
				$values['cat_id']	= $this->db->f('category');
				$values['entry_date']	= $this->db->f('entry_date');
				$values['skill_id']	= $this->db->f('skill_id');
				$values['experience_id']= $this->db->f('experience_id');
				$values['owner']	= $this->db->f('owner');
			}
			return $values;
		}

		function read_single_qualification_type($id)
		{
			$sql = "SELECT *  FROM phpgw_hrm_quali_type WHERE id=" . intval($id);

			$this->db->query($sql,__LINE__,__FILE__);

			if ($this->db->next_record())
			{
				$values['quali_type_id']		= $id;
				$values['name']		= stripslashes($this->db->f('name'));
				$values['descr']	= stripslashes($this->db->f('descr'));
				$values['entry_date']	= $this->db->f('entry_date');
				$values['owner']	= $this->db->f('type_owner');
			}
			return $values;
		}

		function read_qualification_type($data)
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
			}

			if ($order)
			{
				$ordermethod = " order by $order $sort";

			}
			else
			{
				$ordermethod = ' order by name asc';
			}

			$table = 'phpgw_hrm_quali_type';

			if($query)
			{
				$query = preg_replace("/'/",'',$query);
				$query = preg_replace('/"/','',$query);

				$querymethod = " WHERE name $this->like '%$query%'";
			}

			$sql = "SELECT * FROM $table $filtermethod $querymethod";

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
				$qualification_type[] = array
				(
					'id'	=> $this->db->f('id'),
					'name'	=> stripslashes($this->db->f('name')),
					'descr'	=> stripslashes($this->db->f('descr')),
				);
			}

			return $qualification_type;
		}


		function read_task($data)
		{
			$start		= isset($data['start']) && $data['start'] ? $data['start']:0;
			$query		= isset($data['query']) ? $data['query'] : '';
			$sort		= isset($data['sort']) && $data['sort']? $data['sort'] : 'DESC';
			$order		= isset($data['order']) ? $data['order'] : '';
			$allrows	= isset($data['allrows']) ? $data['allrows'] : '';
			$job_id 	= $data['job_id'];
			$filter_id = isset($data['filter_id']) ? $data['filter_id'] : '';

			if ($order)
			{
				$ordermethod = " ORDER BY $order $sort";
			}
			else
			{
				$ordermethod = ' ORDER BY value_sort asc';
			}

			$sql = "SELECT * from phpgw_hrm_task  WHERE job_id=" . intval($job_id);

			$parent_select = ' AND task_level =0';

			$querymethod = '';
			if($filter_id)
			{
				$querymethod = " AND id != $filter_id";
			}

			if($query)
			{
				$query = $this->db->db_addslashes($query);
				$querymethod .= " AND name {$this->like} '%{$query}%'";
			}

			$this->db->query($sql . $parent_select . $querymethod . $ordermethod,__LINE__,__FILE__);
			$this->total_records = $this->db->num_rows();

			$value_sort = 1;
			while ($this->db->next_record())
			{
				$tasks[] = array
				(
					'id'	=> $this->db->f('id'),
					'level'	=> 0,
					'name'	=> stripslashes($this->db->f('name')),
					'descr'	=> stripslashes($this->db->f('descr')),
					'parent'=> 0,
					'value_sort'=> $value_sort
				);
				$value_sort++;
			}

			$num_tasks = count($tasks);
			for ($i=0;$i < $num_tasks;$i++)
			{
				$sub_select = ' AND task_parent=' . $tasks[$i]['id'] . " AND task_level=" . ($tasks[$i]['level']+1);

				$this->db->query($sql . $querymethod . $sub_select . $ordermethod,__LINE__,__FILE__);

				$this->total_records += $this->db->num_rows();

				$subtasks = array();
				$j = 0;
				$value_sort = 1;
				while ($this->db->next_record())
				{
					$subtasks[$j]['id']          = (int)$this->db->f('id');
					$subtasks[$j]['level']       = 1;
					$subtasks[$j]['parent']      = (int)$this->db->f('task_parent');
					$subtasks[$j]['name']        = $this->db->f('name');
					$subtasks[$j]['descr'] = $this->db->f('descr');
					$subtasks[$j]['value_sort'] = $value_sort;

					$j++;
					$value_sort ++;
				}

				$num_subtasks = count($subtasks);
				if ($num_subtasks != 0)
				{
					$newtasks = array();
					for ($k = 0; $k <= $i; $k++)
					{
						$newtasks[$k] = $tasks[$k];
					}
					for ($k = 0; $k < $num_subtasks; $k++)
					{
						$newtasks[$k+$i+1] = $subtasks[$k];
					}
					for ($k = $i+1; $k < $num_tasks; $k++)
					{
						$newtasks[$k+$num_subtasks] = $tasks[$k];
					}
					$tasks = $newtasks;
					$num_tasks = count($tasks);
				}

			}
//_debug_array($tasks);

			return $tasks;
		}

		function select_task_list($id='',$job_id='')
		{
			$task = $this->read_task(array('job_id' => $job_id, 'allrows'=>true, 'filter_id' => $id));
			return $task;
		}


		function add_job($values)
		{
			$receipt = array();
			$this->db->transaction_begin();
			$table = 'phpgw_hrm_job';

			if($values['parent_id'])
			{
				$this->db->query("SELECT job_level FROM $table  where id=" . intval($values['parent_id']),__LINE__,__FILE__);
				$this->db->next_record();
				$level	= (int)$this->db->f('job_level') +1;
			}
			else
			{
				$level	= 0;
			}

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$insert_values=array(
				$values['name'],
				$values['descr'],
				intval($values['parent_id']),
				$level,
				time(),
				$this->account
				);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->query("INSERT INTO $table (name,descr,job_parent,job_level,entry_date,owner) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['id'] = $this->db->get_last_insert_id($table,'id');
			
			if($this->db->transaction_commit())
			{
				$receipt['message'][]=array('msg'=>lang('job has been saved'));
			}
			return $receipt;
		}

		function edit_job($values)
		{
			$this->db->transaction_begin();
			$table = 'phpgw_hrm_job';

			$this->db->query("SELECT job_level FROM $table  where id=" . intval($values['id']),__LINE__,__FILE__);
			$this->db->next_record();
			$old_level	= (int)$this->db->f('job_level');

			if($values['parent_id'])
			{
				$this->db->query("SELECT job_level FROM $table  where id=" . intval($values['parent_id']),__LINE__,__FILE__);
				$this->db->next_record();
				$level	= (int)$this->db->f('job_level') +1;
			}
			else
			{
				$level	= 0;
			}

			if($old_level !=$level)
			{
				$this->level = $level;
				$this->parent_gap = 1;
				$this->job_parent = $values['id'];
				while ($this->job_parent)
				{
					$this->check_move_child();
				}

				if ( count($this->move_child) )
				{
					foreach ($this->move_child as $child)
					{
						$new_level = $child['new_level'];
						$this->db->query("UPDATE $table set job_level= $new_level WHERE id=" . intval($child['id']),__LINE__,__FILE__);
					}
				}

			}

			$value_set['descr']			= $this->db->db_addslashes($values['descr']);
			$value_set['name']			= $this->db->db_addslashes($values['name']);
			$value_set['job_parent']		= intval($values['parent_id']);
			$value_set['job_level']		= $level;

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE $table set $value_set WHERE id=" . $values['id'],__LINE__,__FILE__);

			$receipt['id'] = $values['id'];

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('job has been edited'));
			return $receipt;
		}

		/**
		* ???
		*
		* @param bool $recursive is the function being called recursively
		* @return a list of children to be moved
		*/
		private function check_move_child($recursive = false)
		{
			// New run so lets reset the data
			if ( !$recursive )
			{
				$this->move_child = array();
			}

			$continue = false;
			$move_child = array();
			$this->db->query("SELECT id FROM phpgw_hrm_job  where job_parent=" . intval($this->job_parent),__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$this->move_child[] = array(
					'id' 		=>(int)$this->db->f('id'),
					'job_parent' 	=>(int)$this->job_parent,
					'new_level' 	=> ($this->level + $this->parent_gap)
					);

				$move_child[] = (int)$this->db->f('id');
				$continue = true;
			}
			if($continue)
			{
				$this->parent_gap++;
				foreach ($move_child as $parent_id)
				{
					$this->job_parent = $parent_id;
					$this->check_move_child(true);
				}

			}
			else
			{
				$this->job_parent = false;
			}
		}


		function check_move_child_delete()
		{
			$continue = false;
			$move_child = array();

			$this->db->query("SELECT id FROM phpgw_hrm_job  where job_parent=" . intval($this->job_id),__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$this->move_child[] = array(
					'id' 		=>(int)$this->db->f('id'),
					'job_parent' 	=>$this->job_parent,
					'new_level' 	=> ($this->level)
					);

				$move_child[] = (int)$this->db->f('id');
				$continue = true;
			}
			unset ($this->job_parent);
			if($continue)
			{
				$this->level++;
				foreach ($move_child as $job_id)
				{
					$this->job_id = $job_id;
					$this->check_move_child_delete();
				}

			}
			else
			{
				$this->check_parent = false;
			}
		}


		function delete_job($id)
		{
			$this->db->transaction_begin();

			$this->db->query("SELECT job_parent,job_level FROM phpgw_hrm_job  where id=" . intval($id),__LINE__,__FILE__);
			$this->db->next_record();
			$this->level		= (int)$this->db->f('job_level');
			$this->job_parent	= (int)$this->db->f('job_parent');

			$this->check_parent = true;
			$this->job_id = $id;
			while ($this->check_parent)
			{
				$this->check_move_child_delete();
			}

			if (is_array($this->move_child))
			{
				foreach ($this->move_child as $child)
				{
					$new_level = $child['new_level'];
					$child['job_parent'];
					if($child['job_parent'] || $child['job_parent']===0)
					{
						$sql = "UPDATE phpgw_hrm_job set job_level= $new_level,job_parent = " . intval($child['job_parent']) .  " WHERE id=" . intval($child['id']);
					}
					else
					{
						$sql = "UPDATE phpgw_hrm_job set job_level= $new_level WHERE id=" . intval($child['id']);
					}
					$this->db->query($sql,__LINE__,__FILE__);
				}
			}

			$this->db->query("DELETE FROM phpgw_hrm_job WHERE id=" . intval($id),__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function select_job_list()
		{
			$params = array('allrows' => true);
			return $this->read($params);
		}

		function reset_job_type_hierarchy()
		{
			$sql = "UPDATE phpgw_hrm_job set job_level= 0,job_parent = 0";
			$this->db->query($sql,__LINE__,__FILE__);
		}



		function add_task($values)
		{
			$this->db->transaction_begin();
			$table = 'phpgw_hrm_task';

			$this->db->query("SELECT max(value_sort) as value_sort FROM $table WHERE job_id = " . (int)$values['job_id'],__LINE__,__FILE__);
			$this->db->next_record();
			$value_sort	= (int)$this->db->f('value_sort') +1;


			if($values['parent_id'])
			{
				$values['parent_id'] = (int)$values['parent_id'];
				$this->db->query("SELECT task_level FROM {$table} WHERE id={$values['parent_id']}",__LINE__,__FILE__);
				$this->db->next_record();
				$level	= (int)$this->db->f('task_level') +1;
			}
			else
			{
				$level	= 0;
			}

			$values['name'] = $this->db->db_addslashes($values['name']);
			$values['descr'] = $this->db->db_addslashes($values['descr']);

			$insert_values=array(
				$values['job_id'],
				$values['name'],
				$values['descr'],
				intval($values['parent_id']),
				$level,
				time(),
				$this->account,
				$value_sort
				);

			$insert_values	= $this->db->validate_insert($insert_values);


			$this->db->query("INSERT INTO $table (job_id,name,descr,task_parent,task_level,entry_date,owner,value_sort) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('task has been saved'));

			$receipt['id'] = $this->db->get_last_insert_id($table,'id');

			$this->db->transaction_commit();
			return $receipt;
		}

		function edit_task($values)
		{
			$this->db->transaction_begin();
			$table = 'phpgw_hrm_task';

			$this->db->query("SELECT task_level FROM $table  where id=" . intval($values['id']),__LINE__,__FILE__);
			$this->db->next_record();
			$old_level	= (int)$this->db->f('task_level');

			if($values['parent_id'])
			{
				$this->db->query("SELECT task_level FROM $table  where id=" . intval($values['parent_id']),__LINE__,__FILE__);
				$this->db->next_record();
				$level	= (int)$this->db->f('task_level') +1;
			}
			else
			{
				$level	= 0;
			}

			if($old_level !=$level)
			{
				$this->level = $level;
				$this->parent_gap = 1;
				$this->task_parent = $values['id'];
				while ($this->task_parent)
				{
					$this->check_move_task_child();

				}

				if (is_array($this->move_task_child))
				{
					foreach ($this->move_task_child as $child)
					{
						$new_level = $child['new_level'];
						$this->db->query("UPDATE $table set task_level= $new_level WHERE id=" . intval($child['id']),__LINE__,__FILE__);
					}
				}

			}

			$value_set['descr']			= $this->db->db_addslashes($values['descr']);
			$value_set['name']			= $this->db->db_addslashes($values['name']);
			$value_set['task_parent']		= intval($values['parent_id']);
			$value_set['task_level']		= $level;

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE $table set $value_set WHERE id=" . $values['id'],__LINE__,__FILE__);

			$receipt['id'] = $values['id'];

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('task has been edited'));
			return $receipt;
		}

		function check_move_task_child()
		{
			$continue = false;
			$move_task_child = array();
			$this->db->query("SELECT id FROM phpgw_hrm_task  where task_parent=" . intval($this->task_parent),__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$this->move_task_child[] = array(
					'id' 		=>(int)$this->db->f('id'),
					'task_parent' 	=>(int)$this->task_parent,
					'new_level' 	=> ($this->level + $this->parent_gap)
					);

				$move_task_child[] = (int)$this->db->f('id');
				$continue = true;
			}
			if($continue)
			{
				$this->parent_gap++;
				foreach ($move_task_child as $parent_id)
				{
					$this->task_parent = $parent_id;
					$this->check_move_task_child();
				}

			}
			else
			{
				$this->task_parent = false;
			}
		}


		function check_move_task_child_delete()
		{
			$continue = false;
			$move_task_child = array();

			$this->db->query("SELECT id FROM phpgw_hrm_task  where task_parent=" . intval($this->task_id),__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$this->move_task_child[] = array(
					'id' 		=>(int)$this->db->f('id'),
					'task_parent' 	=>$this->task_parent,
					'new_level' 	=> ($this->level)
					);

				$move_task_child[] = (int)$this->db->f('id');
				$continue = true;
			}
			unset ($this->task_parent);
			if($continue)
			{
				$this->level++;
				foreach ($move_task_child as $task_id)
				{
					$this->task_id = $task_id;
					$this->check_move_task_child_delete();
				}

			}
			else
			{
				$this->check_parent = false;
			}
		}


		function delete_task($id)
		{
			$this->db->transaction_begin();

			$this->db->query("SELECT task_parent,task_level FROM phpgw_hrm_task  where id=" . intval($id),__LINE__,__FILE__);
			$this->db->next_record();
			$this->level		= (int)$this->db->f('task_level');
			$this->task_parent	= (int)$this->db->f('task_parent');

			$this->check_parent = true;
			$this->task_id = $id;
			while ($this->check_parent)
			{
				$this->check_move_task_child_delete();
			}

			if (is_array($this->move_task_child))
			{
				foreach ($this->move_task_child as $child)
				{
					$new_level = $child['new_level'];
					$child['task_parent'];
					if($child['task_parent'] || $child['task_parent']===0)
					{
						$sql = "UPDATE phpgw_hrm_task set task_level= $new_level,task_parent = " . intval($child['task_parent']) .  " WHERE id=" . intval($child['id']);
					}
					else
					{
						$sql = "UPDATE phpgw_hrm_task set task_level= $new_level WHERE id=" . intval($child['id']);
					}
					$this->db->query($sql,__LINE__,__FILE__);
				}
			}

			$this->db->query("DELETE FROM phpgw_hrm_task WHERE id=" . intval($id),__LINE__,__FILE__);
			$this->db->transaction_commit();
		}


		function add_qualification_type($values)
		{
			$values['descr'] = $this->db->db_addslashes($values['descr']);
			$values['name'] = $this->db->db_addslashes($values['name']);

			$values['quali_type_id'] = $this->db->next_id('phpgw_hrm_quali_type');

			$insert_values=array(
				$values['quali_type_id'],
				$values['name'],
				$values['descr'],
				time(),
				$this->account
				);

			$insert_values	= $this->db->validate_insert($insert_values);
			$this->db->query("INSERT INTO phpgw_hrm_quali_type (id,name,descr,entry_date,type_owner) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$receipt['message'][]=array('msg'=>lang('qualification type item has been saved'));

			$receipt['quali_type_id']= $values['quali_type_id'];
			return $receipt;
		}


		function edit_qualification_type($values)
		{
			$this->db->transaction_begin();

			$value_set['descr'] = $this->db->db_addslashes($values['descr']);
			$value_set['name'] = $this->db->db_addslashes($values['name']);

			$value_set	= $this->db->validate_update($value_set);

			$this->db->query("UPDATE phpgw_hrm_quali_type set $value_set WHERE id=" . $values['quali_type_id'],__LINE__,__FILE__);

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('qualification type item has been edited'));

			$receipt['quali_type_id']= $values['quali_type_id'];
			return $receipt;
		}


		function check_qualification_type($values)
		{
			$sql = "SELECT * FROM  phpgw_hrm_quali_type WHERE id=" . intval($values['quali_type_id']);
			$this->db->query($sql,__LINE__,__FILE__);

			 if($this->db->next_record())
			 {
			 	if ($values['name'] == stripslashes($this->db->f('name')) && $values['descr'] == stripslashes($this->db->f('descr')))
			 	{
			 		return true;
			 	}
			 }

			 return false;
		}

		function add_qualification($values)
		{
			$value['remark'] = $this->db->db_addslashes($values['remark']);

			$this->db->query("SELECT  max(value_sort) as value_sort FROM phpgw_hrm_quali WHERE job_id = " . (int)$values['job_id'],__LINE__,__FILE__);
			$this->db->next_record();
			$value_sort	= (int)$this->db->f('value_sort') +1;

			$this->db->transaction_begin();

			$insert_values=array(
				$values['job_id'],
				$values['quali_type_id'],
				$values['cat_id'],
				$values['skill_id'],
				$values['experience_id'],
				time(),
				$this->account,
				$value['remark'],
				$value_sort
				);

			$insert_values	= $this->db->validate_insert($insert_values);

			$this->db->query("INSERT INTO phpgw_hrm_quali (job_id,quali_type_id,category,skill_id,experience_id,entry_date,quali_owner,remark,value_sort) "
				. "VALUES ($insert_values)",__LINE__,__FILE__);

			$quali_id = $this->db->get_last_insert_id('phpgw_hrm_quali','id');

			if(is_array($values['alternative_qualification']))
			{
				$this->db->query("UPDATE phpgw_hrm_quali set is_parent = 1 WHERE id= $quali_id",__LINE__,__FILE__);

				foreach($values['alternative_qualification'] as $alternative_qualification)
				{
					$this->db->query("UPDATE phpgw_hrm_quali set quali_parent = $quali_id WHERE id= $alternative_qualification",__LINE__,__FILE__);
				}
			}
			else
			{
				$this->db->query("UPDATE phpgw_hrm_quali set is_parent = 0 WHERE id=" . (int)$quali_id,__LINE__,__FILE__);
			}

			$receipt['message'][]=array('msg'=>lang('qualification item has been saved'));

			$receipt['quali_id'] = $quali_id;

			$this->db->transaction_commit();
			return $receipt;
		}

		function edit_qualification($values)
		{

			$this->db->transaction_begin();

			$value_set['quali_type_id']		= $values['quali_type_id'];
			$value_set['category']			= $values['cat_id'];
			$value_set['skill_id']			= $values['skill_id'];
			$value_set['experience_id']		= $values['experience_id'];
			$value_set['remark'] 			= $this->db->db_addslashes($values['remark']);

			$value_set	= $this->db->validate_update($value_set);

			$table='phpgw_hrm_quali';

			$this->db->query("UPDATE $table set $value_set WHERE id=" . $values['quali_id'],__LINE__,__FILE__);
			$this->db->query("UPDATE phpgw_hrm_quali set quali_parent = NULL WHERE quali_parent = " . $values['quali_id'] ,__LINE__,__FILE__);

			if(is_array($values['alternative_qualification']))
			{
				$this->db->query("UPDATE phpgw_hrm_quali set is_parent = 1 WHERE id= " . $values['quali_id'],__LINE__,__FILE__);

				foreach($values['alternative_qualification'] as $alternative_qualification)
				{
					$this->db->query("UPDATE phpgw_hrm_quali set quali_parent = " . $values['quali_id'] . " WHERE id= $alternative_qualification",__LINE__,__FILE__);
				}
			}
			else
			{
				$this->db->query("UPDATE phpgw_hrm_quali set is_parent = 0 WHERE id= " . $values['quali_id'],__LINE__,__FILE__);
			}

			$this->db->transaction_commit();

			$receipt['message'][]=array('msg'=>lang('qualification item has been edited'));

			$receipt['quali_id']= $values['quali_id'];
			return $receipt;
		}


		function select_qualification_list($job_id,$quali_id='')
		{
			$sql = "SELECT * ,phpgw_hrm_quali.id as quali_id from phpgw_hrm_quali $this->join phpgw_hrm_quali_type ON phpgw_hrm_quali.quali_type_id = phpgw_hrm_quali_type.id WHERE job_id=" . intval($job_id);

			if($quali_id)
			{
				$sql .= " AND phpgw_hrm_quali.id != $quali_id AND (is_parent = 0 AND (quali_parent is null or quali_parent = $quali_id ))";
			}
			else
			{
				$sql .= " AND (is_parent = 0 AND (quali_parent is null))";
			}

			$this->db->query($sql,__LINE__,__FILE__);

			$i=0;
			while ($this->db->next_record())
			{
				$qualification_list[$i]['id']	= $this->db->f('quali_id');
				$qualification_list[$i]['name']	= stripslashes($this->db->f('name'));
				if($this->db->f('quali_parent'))
				{
					$qualification_list[$i]['selected'] = 'selected';
				}
				$i++;
			}
			return $qualification_list;
		}


		function delete_qualification($job_id,$id)
		{
			$this->db->transaction_begin();
			$this->db->query('UPDATE phpgw_hrm_quali set quali_parent = NULL WHERE quali_parent = '  . intval($id) . ' AND job_id='  . intval($job_id),__LINE__,__FILE__);
			$this->db->query('DELETE FROM phpgw_hrm_quali WHERE id='  . intval($id) . ' AND job_id='  . intval($job_id),__LINE__,__FILE__);
			$this->db->transaction_commit();
		}

		function resort_value($data)
		{
			if(is_array($data))
			{
				$resort = (isset($data['resort'])?$data['resort']:'up');
				$job_id = (isset($data['job_id'])?$data['job_id']:'');
				$id = (isset($data['id'])?$data['id']:'');
				$type = (isset($data['type'])?$data['type']:'');
			}

			switch($type)
			{
				case 'task':
					$table = 'phpgw_hrm_task';
					break;
				case 'qualification':
					$table = 'phpgw_hrm_quali';
					break;
			}

			if(!$table)
			{
				return;
			}

			$this->db->transaction_begin();
			$sql = "SELECT value_sort FROM $table where job_id=$job_id AND id=$id";
			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$value_sort	= $this->db->f('value_sort');
			$sql2 = "SELECT max(value_sort) as max_sort FROM $table where job_id=$job_id";
			$this->db->query($sql2,__LINE__,__FILE__);
			$this->db->next_record();
			$max_sort	= $this->db->f('max_sort');

			switch($resort)
			{
				case 'up':
					if($value_sort>1)
					{
						$sql = "UPDATE $table set value_sort=$value_sort WHERE job_id=$job_id AND value_sort =" . ($value_sort-1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE $table set value_sort=" . ($value_sort-1) ." WHERE job_id=$job_id AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				case 'down':
					if($max_sort > $value_sort)
					{
						$sql = "UPDATE $table set value_sort=$value_sort WHERE job_id=$job_id AND value_sort =" . ($value_sort+1);
						$this->db->query($sql,__LINE__,__FILE__);
						$sql = "UPDATE $table set value_sort=" . ($value_sort+1) ." WHERE job_id=$job_id AND id=$id";
						$this->db->query($sql,__LINE__,__FILE__);
					}
					break;
				default:
					return;
					break;
			}
			$this->db->transaction_commit();
		}
	}
