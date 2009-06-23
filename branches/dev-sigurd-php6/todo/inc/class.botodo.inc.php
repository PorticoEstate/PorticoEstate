<?php
	/**
	* Todo business
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2003,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @version $Id$
	*/

	/**
	* Todo business
	* 
	* @package todo
	*/
	class botodo
	{
		var $start;
		var $query;
		var $filter;
		var $order;
		var $sort;
		var $cat_id;
		
		/**
		* @var bool $debug enable debugging
		*/
		var $debug = false;

		var $public_functions = array
		(
			'cached_accounts'  => True,
			'_list'            => True,
			'check_perms'      => True,
			'check_values'     => True,
			'select_todo_list' => True,
			'save'             => True,
			'_read'            => True,
			'delete'           => True,
			'exists'           => True,
			'list_methods'     => True
		);

		function botodo($session=False)
		{
			$this->sotodo	= CreateObject('todo.sotodo');
			$this->datetime	=& $GLOBALS['phpgw']->datetime;

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = True;
			}

			$_start		= isset($_REQUEST['start'])	? $_REQUEST['start']	: 0;
			$_query		= isset($_REQUEST['query'])	? $_REQUEST['query']	: '';
			$_sort		= isset($_REQUEST['sort'])	? $_REQUEST['sort']		: 'ASC';
			$_order		= isset($_REQUEST['order'])	? $_REQUEST['order']	: 'todo_id';
			$_filter	= isset($_REQUEST['filter'])? $_REQUEST['filter']	: '';
			$_cat_id	= isset($_REQUEST['cat_id'])? $_REQUEST['cat_id']	: 0;

			if(!empty($_start) || ($_start == '0') || ($_start == 0))
			{
				if($this->debug) { echo '<br>overriding $start: "' . $this->start . '" now "' . $_start . '"'; }
				$this->start = $_start;
			}

			if((empty($_query) && !empty($this->query)) || !empty($_query))
			{
				$this->query  = $_query;
			}

			if(isset($_cat_id) && !empty($_cat_id))
			{
				$this->cat_id = $_cat_id;
			}
			if((isset($_POST['cat_id']) || isset($_GET['cat_id'])) &&
				($_cat_id == '0' || $_cat_id == 0 || $_cat_id == ''))
			{
				$this->cat_id = 0;
			}

			if(isset($_sort) && !empty($_sort))
			{
				if($this->debug)
				{
					echo '<br>overriding $sort: "' . $this->sort . '" now "' . $_sort . '"';
				}
				$this->sort   = $_sort;
			}

			if(isset($_order) && !empty($_order))
			{
				if($this->debug)
				{
					echo '<br>overriding $order: "' . $this->order . '" now "' . $_order . '"'; 
				}
				$this->order  = $_order;
			}

			if(isset($_filter) && !empty($_filter))
			{
				if($this->debug)
				{
					echo '<br>overriding $filter: "' . $this->filter . '" now "' . $_filter . '"'; 
				}
				$this->filter = $_filter;
			}
		}

		function list_methods($_type)
		{
			if (is_array($_type))
			{
				$_type = $_type['type'];
			}

			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
							'docstring' => lang('Read this list of methods.')
						),
						'list' => array(
							'function'  => '_list',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('Returns an array of todo items')
						),
						'save' => array(
							'function'  => 'save',
							'signature' => array(array(xmlrpcBoolean,xmlrpcStruct)),
							'docstring' => lang('Adds or edits a todo item')
						),
						'delete' => array(
							'function'  => 'delete',
							'signature' => array(array(xmlrpcBoolean,xmlrpcInt)),
							'docstring' => lang('Deletes a todo item')
						),
						'total_records' => array(
							'function'  => 'total_records',
							'signature' => array(array(xmlrpcInt)),
							'docstring' => lang('Returns a the total number of records in the database, must call list_todos first')
						)
					);
					return $xml_functions;
					break;

				case 'soap':
					return $this->soap_functions;
					break;

				default:
					return array();
					break;
			}
		}

		function get_grants()
		{
			return $this->sotodo->grants;
		}

		function save_sessiondata($data)
		{
			if ($this->use_session)
			{
				$GLOBALS['phpgw']->session->appsession('session_data','todo',$data);
			}
		}

		function read_sessiondata()
		{
			$data = $GLOBALS['phpgw']->session->appsession('session_data','todo');

			$this->start	= $data['start'];
			$this->query	= $data['query'];
			$this->filter	= $data['filter'];
			$this->order	= $data['order'];
			$this->sort		= $data['sort'];
			$this->cat_id	= $data['cat_id'];
		}

		function check_perms($has, $needed)
		{
			return (!!($has & $needed) == True);
		}

		function cached_accounts($account_id)
		{
			return $GLOBALS['phpgw']->accounts->get($account_id);
		}

		function employee_list($type)
		{
			$employees = $GLOBALS['phpgw']->accounts->get_list($type);
			return $employees;
		}

		function format_assigned($a = '')
		{
			if (substr($a,0,1) == ',' && (substr($a,strlen($a-1),1)) == ',')
			{
				$a = substr($a,1,strlen($a)-2);
			}
			$a = explode(',',$a);
			return $a;
		}

		function list_assigned($assi = '')
		{
			$aout = '';
			if (is_array($assi))
			{           
				foreach ( $assi as $a )
				{

                    /**
                     * Begin Orlando Fix
                     * 
                     * I had to comment the conditionals because variable $adata
                     * doesn't return the 'type' field of the accounts
                     */
                    
					$adata = $this->cached_accounts($a);
                                                     
                    /*if ($adata[$a]['type'] == 'u')
					{
						$aout  .= $GLOBALS['phpgw']->common->display_fullname($adata[$a]['lid'],
										$adata[$a]['firstname'],$adata[$a]['lastname']) . '<br>';
					}
					elseif($adata[$a]['type'] == 'g')
					{
						$aout .= $adata[$a]['firstname'] . ' ' . lang('Group') . '<br>';
					}*/

                    $aout  .= $GLOBALS['phpgw']->common->display_fullname($adata->lid,$adata->firstname,$adata->lastname) . '<br>';
                    /**
                     * End Orlando Fix
                     */
				}
			}
			return $aout;
		}

		function _list($start = 0, $limit = '', $query = '', $filter = '', $order = '', $sort = '', $cat_id = 0, $tree = '', $parent = '')
		{
			if (is_array($start))
			{
				$params = $start;

				$start  = $params['start'];
				$limit  = $params['limit'];
				$query  = $params['query'];
				$filter = $params['filter'];
				$order  = $params['order'];
				$sort   = $params['sort'];
				$cat_id = $params['cat_id'];
				$tree   = $params['tree'];
				$parent = $params['parent'];
			}

			$todos = $this->sotodo->read_todos($start, $limit, $query, $filter, $order, $sort, $cat_id, $tree, $parent);
			$this->total_records = $this->sotodo->total_records;

			$r = array();
			foreach ( $todos as $todo )
			{
				$sdate = $todo['sdate'] - $this->datetime->tz_offset;
				$todo['sdate'] = $GLOBALS['phpgw']->common->show_date($sdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				if ( isset($todo['edate']) && $todo['edate'] != 0)
				{
					$edate = $todo['edate'] - $this->datetime->tz_offset;
					$todo['edate']	= $GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				}

				if ($todo['assigned'])
				{
					$todo['assigned'] = $this->format_assigned($todo['assigned']);
				}

				if ($todo['assigned_group'])
				{
					$todo['assigned_group'] = $this->format_assigned($todo['assigned_group']);
				}

				$v['owner'] = $GLOBALS['phpgw']->accounts->name2id($todo['owner']);
				$r[] = array
				(
					'id'				=> (int) $todo['id'],
					'main'				=> (int) $todo['main'],
					'parent'			=> (int) $todo['parent'],
					'level'				=> (int) $todo['level'],
					'owner'				=> $todo['owner'],
					'owner_id'			=> (int) $todo['owner_id'],
					'access'			=> $todo['access'],
					'cat'				=> (int) $todo['cat'],
					'title'				=> $todo['title'],
					'descr'				=> $todo['descr'],
					'pri'				=> (int) $todo['pri'],
					'status'			=> (int) $todo['status'],
					'sdate'				=> $todo['sdate'],
					'edate'				=> $todo['edate'],
					'grants'			=> (int) $todo['grants'],
					'sdate_epoch'		=> (int) $todo['sdate_epoch'],
					'edate_epoch'		=> (int) $todo['edate_epoch'],
					'assigned'			=> $todo['assigned'],
					'assigned_group'	=> $todo['assigned_group']
				);
			}
			return $r;
		}

		function read($todo_id)
		{
			return $this->sotodo->read_single_todo($todo_id);
		}

		function check_values($values)
		{
			$error = array();
			if (!$values['title'])
			{
				$error[] = lang('Please enter a title');
			}

			if (strlen($values['descr']) >= 8000)
			{
				$error[] = lang('Description can not exceed 8000 characters in length');
			}

			if ($values['daysfromstart'] && ! ereg('^[0-9]+$',$values[daysfromstart]))
			{
				$error[] = lang('You can only enter numbers for days from now');
			}

			if ($values['smonth'] || $values['sday'] || $values['syear'])
			{
				if(! $this->datetime->date_valid($values['syear'],$values['smonth'],$values['sday']))
				{
					$error[] = lang('You have entered an invalid start date');
				}
			}

			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				if(! $this->datetime->date_valid($values['eyear'],$values['emonth'],$values['eday']))
				{
					$error[] = lang('You have entered an invalid end date');
				}
			}

			/*
			if ($values['edate'] < $values['sdate'] && $values['edate'] && $values['sdate'])
			{
				$error[] = lang('Ending date can not be before start date');
			}
			*/

			if (($values['smonth'] || $values['sday'] || $values['syear']) && ($values['emonth'] || $values['eday'] || $values['eyear']))
			{
				if($this->datetime->date_compare($values['eyear'],$values['emonth'],$values['eday'],$values['syear'],$values['smonth'],$values['sday']) == -1)
				{
					$error[] = lang('Ending date can not be before start date');
				}
			}

			if ( count($error) )
			{
				return $error;
			}
		}

		function save($values)
		{
			if ($values['access'])
			{
				$values['access'] = 'private';
			}
			else
			{
				$values['access'] = 'public';
			}

			if ( isset($values['seltoday']) )
			{
				$values['sdate'] = time();
			}
			else
			{
				if ($values['smonth'] || $values['sday'] || $values['syear'])
				{
					$values['sdate'] = mktime(0,0,0,$values['smonth'], $values['sday'], $values['syear']);
				}
			}

			if (!$values['sdate'])
			{
				$values['sdate'] = time();
			}

			if ($values['emonth'] || $values['eday'] || $values['eyear'])
			{
				$values['edate'] = mktime(2,0,0,$values['emonth'],$values['eday'],$values['eyear']);
			}
			else if ($values['daysfromstart'] > 0)
			{
				$values['edate'] = mktime(0,0,0,date('m',$values['sdate']), date('d',$values['sdate'])+$values['daysfromstart'], date('Y',$values['sdate']));
			}

			if ( isset($values['id']) && (int)$values['id'] > 0)
			{
				$this->sotodo->edit_todo($values);
				$todo_id = $values['id'];
			}
			else
			{
				$todo_id = $this->sotodo->add_todo($values);
			}
			return $todo_id;
		}

		function exists($todo_id)
		{
			$exists = $this->sotodo->exists($todo_id);

			if ($exists)
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function delete($todo_id, $subs = False)
		{
			if (is_array($todo_id))
			{
				$todo_id = $todo_id[0];
			}

			if ($subs)
			{
				$this->sotodo->delete_todo($todo_id,True);
			}
			else
			{
				$this->sotodo->delete_todo($todo_id);
			}
			return True;
		}
	}
?>
