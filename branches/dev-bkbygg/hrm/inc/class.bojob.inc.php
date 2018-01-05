<?php
	/**
	* phpGroupWare - HRM: a  human resource competence management system.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package hrm
	* @subpackage user
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package hrm
	 */

	class hrm_bojob
	{
		var $start = 0;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;

		/**
		* @var bool return all rows for a search - not a limited subset
		*/
		public $allrows;

		var $public_functions = array
		(
			'read'			=> true,
			'read_single'		=> true,
			'save'			=> true,
			'delete'		=> true,
			'check_perms'		=> true
		);

		var $soap_functions = array(
			'list' => array(
				'in'  => array('int','int','struct','string','int'),
				'out' => array('array')
			),
			'read' => array(
				'in'  => array('int','struct'),
				'out' => array('array')
			),
			'save' => array(
				'in'  => array('int','struct'),
				'out' => array()
			),
			'delete' => array(
				'in'  => array('int','struct'),
				'out' => array()
			)
		);

		public function __construct($session=false)
		{
		//	$this->currentapp	= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->so 		= CreateObject('hrm.sojob');
			$this->socommon = CreateObject('hrm.socommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$this->start	= (int)phpgw::get_var('start', 'int');
			$this->query	= phpgw::get_var('query');
			$this->sort		= phpgw::get_var('sort');
			$this->order	= phpgw::get_var('order');
			$this->filter	= phpgw::get_var('filter', 'int');
			$this->cat_id	= phpgw::get_var('cat_id', 'int');
			$this->allrows	= phpgw::get_var('allrows', 'bool');
		}


		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','phpgw_hrm_job',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','phpgw_hrm_job');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->sort		= $data['sort'];
			$this->order	= $data['order'];
			$this->cat_id	= $data['cat_id'];
		}


		function read()
		{
			$params = array
			(
				'start'		=> $this->start,
				'query' 	=> $this->query,
				'sort'		=> $this->sort,
				'order' 	=> $this->order,
				'allrows'	=> $this->allrows
			);
			$account_info = $this->so->read($params);
			$this->total_records = $this->so->total_records;
			return $account_info;
		}

		function read_single_job($id)
		{
			return $this->so->read_single_job($id);
		}

		function read_qualification($job_id)
		{
			$qualification_list = $this->so->read_qualification(array('job_id'=>$job_id,'start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			for ($i=0;$i<count($qualification_list);$i++)
			{
				if ($qualification_list[$i]['level'] > 0)
				{
					$space = '--> ';
					$spaceset = str_repeat($space,$qualification_list[$i]['level']);
					$qualification_list[$i]['name'] = $spaceset . $qualification_list[$i]['name'];
				}
			}

			return $qualification_list;
		}

		function read_qualification_type()
		{
			$qualification_list = $this->so->read_qualification_type(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));
			$this->total_records = $this->so->total_records;
			return $qualification_list;
		}

		function read_single_qualification($id)
		{
			$values =$this->so->read_single_qualification($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}
			return $values;
		}

		function read_single_task($id)
		{
			$values =$this->so->read_single_task($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}
			return $values;
		}

		function read_single_qualification_type($id)
		{
			$values =$this->so->read_single_qualification_type($id);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if($values['entry_date'])
			{
				$values['entry_date']	= $GLOBALS['phpgw']->common->show_date($values['entry_date'],$dateformat);
			}
			return $values;
		}


		function read_task($job_id)
		{
			$task_list = $this->so->read_task(array('job_id'=>$job_id,'start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
											'allrows'=>$this->allrows));

			$this->total_records = $this->so->total_records;

			for ($i=0;$i<count($task_list);$i++)
			{
				if ($task_list[$i]['level'] > 0)
				{
					$space = '--> ';
					$spaceset = str_repeat($space,$task_list[$i]['level']);
					$task_list[$i]['name'] = $spaceset . $task_list[$i]['name'];
				}
			}

			return $task_list;
		}


		function save_job($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_job($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_job($values);
			}
			return $receipt;
		}

		function save_task($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_task($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_task($values);
			}
			return $receipt;
		}

		function save_qualification($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['quali_id'] != '')
				{
					$receipt = $this->so->edit_qualification($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_qualification($values);
			}

			return $receipt;
		}

		function save_qualification_type($values,$action='')
		{
			if ($action=='edit')
			{
				if ($values['quali_type_id'] != '')
				{
					$receipt = $this->so->edit_qualification_type($values);
				}
				else
				{
					$receipt['error'][]=array('msg'=>lang('Error'));
				}
			}
			else
			{
				$receipt = $this->so->add_qualification_type($values);
			}

			return $receipt;
		}

		function delete_task($id='')
		{
			$this->so->delete_task($id);
		}

		function delete_qualification($job_id,$id)
		{
			$this->so->delete_qualification($job_id,$id);
		}

		function delete_job($id)
		{
			$this->so->delete_job($id);
		}

		function reset_job_type_hierarchy()
		{
			$this->so->reset_job_type_hierarchy();
		}

		function select_job_list($selected='')
		{
			$jobs = $this->so->select_job_list();
			$job_list = array();
			foreach ( $jobs as $job )
			{
				if ($job['level'] > 0)
				{
					$space = '--';
					$spaceset = str_repeat($space,$job['level']);
					$job['name'] = $spaceset . $job['name'];
				}

				$sel_job = '';

				if ($job['id']==$selected)
				{
					$sel_job = 'selected';
				}

				$job_list[] = array
				(
					'id'		=> $job['id'],
					'name'		=> $job['name'],
					'selected'	=> $sel_job
				);
			}

			for ($i=0;$i<count($job_list);$i++)
			{
				if ($job_list[$i]['selected'] != 'selected')
				{
					unset($job_list[$i]['selected']);
				}
			}

			return $job_list;
		}

		function select_task_list($selected='',$id='', $job_id='')
		{
			$tasks= $this->so->select_task_list($id,$job_id);
			while (is_array($tasks) && list(,$task) = each($tasks))
			{
				if ($task['level'] > 0)
				{
					$space = '--';
					$spaceset = str_repeat($space,$task['level']);
					$task['name'] = $spaceset . $task['name'];
				}

				$sel_task = '';

				if ($task['id']==$selected)
				{
					$sel_task = 'selected';
				}

				$task_list[] = array
				(
					'id'		=> $task['id'],
					'name'		=> $task['name'],
					'selected'	=> $sel_task
				);
			}

			for ($i=0;$i<count($task_list);$i++)
			{
				if ($task_list[$i]['selected'] != 'selected')
				{
					unset($task_list[$i]['selected']);
				}
			}

			return $task_list;
		}

		function select_qualification_list($job_id,$quali_id='')
		{
			$qualification_list = $this->so->select_qualification_list($job_id,$quali_id);
			return $qualification_list;
		}

		function resort_value($data)
		{
			$this->so->resort_value($data);
		}

	}
