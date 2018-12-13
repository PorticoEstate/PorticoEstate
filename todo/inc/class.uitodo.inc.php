<?php
	/**
	* Todo user interface
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille [ceb@phpgroupware.org]
	* @copyright Copyright (C) 2000-2003,2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package todo
	* @version $Id$
	*/

	/*
	* Import required classes
	*/
	phpgw::import_class('phpgwapi.sbox');

	/**
	* Todo user interface
	*  
	* @package todo
	*/
	class uitodo
	{
		var $grants;
		var $historylog;
		var $t;
		var $public_functions = array
		(
			'show_list'	=> True,
			'view'      => True,
			'add'       => True,
			'edit'      => True,
			'delete'	=> True,
			'matrix'	=> True
		);

		function __construct()
		{
			$this->botodo		= CreateObject('todo.botodo',True);
			$this->nextmatchs	= CreateObject('phpgwapi.nextmatchs');
			$this->historylog	= CreateObject('phpgwapi.historylog','todo');
			$this->historylog->types = array
			(
				'A' => lang('Entry added'),
				'C' => lang('Category changed'),
				'S' => lang('Start date changed'),
				'E' => lang('End date changed'),
				'U' => lang('Urgency changed'),
				's' => lang('Status changed'),
				'T' => lang('Title changed'),
				'D' => lang('Description changed'),
				'a' => lang('Access changed'),
				'P' => lang('Parent changed')
			);

			$this->historylog->alternate_handlers = array
			(
				'S' => '$GLOBALS[\'phpgw\']->common->show_date',
				'E' => '$GLOBALS[\'phpgw\']->common->show_date',
				'C' => '$GLOBALS[\'phpgw\']->categories->id2name'
			);

			$this->cats       = CreateObject('phpgwapi.categories');
			$GLOBALS['phpgw']->categories = $this->cats;
			$this->matrix     = CreateObject('phpgwapi.matrixview');
			$this->account    = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->t          = CreateObject('phpgwapi.template',$GLOBALS['phpgw']->common->get_tpl_dir('todo'));
			$this->grants     = $GLOBALS['phpgw']->acl->get_grants('todo','.');

			$this->start      = $this->botodo->start;
			$this->query      = $this->botodo->query;
			$this->filter     = $this->botodo->filter;
			$this->order      = $this->botodo->order;
			$this->sort       = $this->botodo->sort;
			$this->cat_id     = $this->botodo->cat_id;
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'filter'	=> $this->filter,
				'order'		=> $this->order,
				'sort'		=> $this->sort,
				'cat_id'	=> $this->cat_id
			);
			$this->botodo->save_sessiondata($data);
		}

		function set_app_langs()
		{
			$this->t->set_var('lang_category',lang('Category'));
			$this->t->set_var('lang_select',lang('Select'));
			$this->t->set_var('lang_descr',lang('Description'));
			$this->t->set_var('lang_title',lang('Title'));
			$this->t->set_var('lang_none',lang('None'));
			$this->t->set_var('lang_nobody',lang('Nobody'));
			$this->t->set_var('lang_urgency',lang('Urgency'));
			$this->t->set_var('lang_completed',lang('Completed'));
			$this->t->set_var('lang_start_date',lang('Start Date'));
			$this->t->set_var('lang_end_date',lang('End Date'));
			$this->t->set_var('lang_date_due',lang('date due'));
			$this->t->set_var('lang_access',lang('Private'));
			$this->t->set_var('lang_parent',lang('Parent project'));
			$this->t->set_var('lang_submit',lang('Submit'));
			$this->t->set_var('lang_save',lang('Save'));
			$this->t->set_var('lang_done',lang('Done'));
			$this->t->set_var('lang_assigned_group',lang('Assigned to group'));
			$this->t->set_var('lang_assigned_user',lang('Assigned to user'));
			$this->t->set_var('lang_owner',lang('Created by'));
		}

		function show_list()
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
			echo $this->show_list_body(True);
		}

		function show_list_body($show_page_header=True)
		{
			$this->t->set_file('todo_list_t', 'list.tpl');
			$this->t->set_block('todo_list_t','page_header','page_header');
			$this->t->set_block('todo_list_t','table_header','table_header');
			$this->t->set_block('todo_list_t','todo_list','todo_list');
			$this->t->set_block('todo_list_t','table_footer','table_footer');
			$this->t->set_block('todo_list_t','page_footer','page_footer');

			$body = '';
			$this->set_app_langs();

			$this->t->set_var('lang_action', lang('todo list'));
			$this->t->set_var('lang_all',lang('All'));

			if (!$this->start)
			{
				$this->start = 0;
			}

			$todo_list = $this->botodo->_list($this->start, True, $this->query,$this->filter,$this->order,$this->sort,$this->cat_id,'all');

// --------------------- nextmatch variable template-declarations ------------------------

			if($show_page_header)
			{
				$left = $this->nextmatchs->left('/index.php',$this->start,$this->botodo->total_records,'&menuaction=todo.uitodo.show_list');
				$right = $this->nextmatchs->right('/index.php',$this->start,$this->botodo->total_records,'&menuaction=todo.uitodo.show_list');
				$this->t->set_var('left',$left);
				$this->t->set_var('right',$right);

				$this->t->set_var('total_matchs',$this->nextmatchs->show_hits($this->botodo->total_records,$this->start));

// ------------------------- end nextmatch template --------------------------------------

				$this->t->set_var('cat_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.show_list')));
				$this->t->set_var('categories',$this->cats->formatted_list('select','all',$this->cat_id,'True'));
				$this->t->set_var('filter_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.show_list')));
				$this->t->set_var('filter_list',$this->nextmatchs->filter(1,array('yours' => 1,'filter' => $this->filter)));
				$this->t->set_var('search_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.show_list')));
				$this->t->set_var('search_list',$this->nextmatchs->search(array('search_obj' => 1,'query' => $this->query)));

				$body .= $this->t->fp('out','page_header');
			}

// ---------------- list header variable template-declarations --------------------------

			$this->t->set_var('sort_status',$this->nextmatchs->show_sort_order($this->sort,'todo_status',$this->order,'/todo/index.php',lang('Status')));
			$this->t->set_var('sort_urgency',$this->nextmatchs->show_sort_order($this->sort,'todo_pri',$this->order,'/todo/index.php',lang('Urgency')));
			$this->t->set_var('sort_title',$this->nextmatchs->show_sort_order($this->sort,'todo_title',$this->order,'/todo/index.php',lang('title')));
			$this->t->set_var('sort_sdate',$this->nextmatchs->show_sort_order($this->sort,'todo_startdate',$this->order,'/todo/index.php',lang('start date')));
			$this->t->set_var('sort_edate',$this->nextmatchs->show_sort_order($this->sort,'todo_enddate',$this->order,'/todo/index.php',lang('end date')));
			$this->t->set_var('sort_owner',$this->nextmatchs->show_sort_order($this->sort,'todo_owner',$this->order,'/todo/index.php',lang('created by')));
			$this->t->set_var('sort_assigned',$this->nextmatchs->show_sort_order($this->sort,'todo_assigned',$this->order,'/todo/index.php',lang('assigned to')));
			$this->t->set_var('h_lang_sub',lang('Add Sub'));
			$this->t->set_var('h_lang_view',lang('View'));
			$this->t->set_var('h_lang_edit',lang('Edit'));

			$body .= $this->t->fp('out','table_header');

// -------------- end header declaration --------------------------------------- 

			$tr_class = '';
			for ($i=0;$i<count($todo_list);$i++)
			{
				$this->t->set_var('tr_class', $this->nextmatchs->alternate_row_class($tr_class));
				$level = $todo_list[$i]['level'];

				$title = $GLOBALS['phpgw']->strip_html($todo_list[$i]['title']); 

				if (!$title)
				{
					$words = explode(' ',$GLOBALS['phpgw']->strip_html($todo_list[$i]['descr']));
					$title = "$words[0] $words[1] $words[2] $words[3] ...";
				}

				if ($level == 0)
				{
					$title = '<b>' . $title . '</b>';
				}
				else
				{
					$space = '&nbsp;&nbsp;';
					$spaceset = str_repeat($space,$level);
					$title = $spaceset . $title;
				}

				switch ($todo_list[$i]['pri'])
				{
					case 1: $pri = lang('Low'); break;
					case 2: $pri = '<b>' . lang('normal') . '</b>'; break;
					case 3: $pri = '<font color="#CC0000"><b>' . lang('high') . '</b></font>'; break;
				}

				if ($todo_list[$i]['edate_epoch'] == 0)
				{
					$datedueout = '&nbsp;';
				}
				else
				{
					$datedue = $todo_list[$i]['edate_epoch'];
					$datedue = $datedue - $this->botodo->datetime->tz_offset;

					$month	= $GLOBALS['phpgw']->common->show_date(time(),'n');
					$day	= $GLOBALS['phpgw']->common->show_date(time(),'d');
					$year	= $GLOBALS['phpgw']->common->show_date(time(),'Y');
					$currentdate = mktime(2,0,0,$month,$day,$year);

					if (($currentdate >= $datedue) && ($todo_list[$i]['status'] < 100))
					{
						$datedueout =  '<font color="#CC0000"><b>';
					}
					else
					{
						$datedueout = '';
					}

					$datedueout .= $todo_list[$i]['edate'];
					if ($currentdate >= $datedue)
					{
						$datedueout .= '</b></font>';
					}
				}

				$assigned = $this->botodo->list_assigned($todo_list[$i]['assigned']);
				$assigned .= $this->botodo->list_assigned($todo_list[$i]['assigned_group']);

// --------------- template declaration for list records -------------------------------------

 				$this->t->set_var(array
				(
					'status'		=> $todo_list[$i]['status'],
					'pri'			=> $pri,
					'title'			=> $title,
					'datecreated'	=> $todo_list[$i]['sdate'],
					'datedue'		=> $datedueout,
					'owner'			=> $todo_list[$i]['owner'],
					'assigned'		=> $assigned
				));

				$this->t->set_var('view','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.view', 'todo_id' => $todo_list[$i]['id']) )
					. '">' . lang('View') . '</a>');

				if ($this->botodo->check_perms($todo_list[$i]['owner_id'], $this->grants, PHPGW_ACL_EDIT))
				{
					$this->t->set_var('edit','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.edit', 'todo_id' => $todo_list[$i]['id']) )
											. '">' . lang('Edit') . '</a>');
				}
				else
				{
					$this->t->set_var('edit','&nbsp;');
				}

				if ($this->botodo->check_perms($todo_list[$i]['owner_id'],$this->grants, PHPGW_ACL_DELETE))
				{
					$this->t->set_var('delete','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.delete', 'todo_id' => $todo_list[$i]['id']) )
												. '">' . lang('Delete') . '</a>');
				}
				else
				{
					$this->t->set_var('delete','&nbsp;');
				}

				if ($this->botodo->check_perms($todo_list[$i]['owner_id'],$this->grants,PHPGW_ACL_ADD))
				{
					$this->t->set_var('subadd', '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.add', 'parent' => $todo_list[$i]['id'], 'cat_id' => $this->cat_id) )
												. '">' . lang('Add Sub') . '</a>');
				}
				else
				{
					$this->t->set_var('subadd','&nbsp;');
				}

				$body .= $this->t->fp('out','todo_list');
			}

			$body .= $this->t->fp('out','table_footer');

// ------------------------- end record declaration ------------------------

// --------------- template declaration for Add Form --------------------------

			if($show_page_header)
			{
				$cat = array();
				if ($this->cat_id && $this->cat_id != 0)
				{
					$cat = $this->cats->return_single($this->cat_id);
				}

				if ( !count($cat) || $cat[0]['app_name'] == 'phpgw' || $cat[0]['owner'] == '-1' || !$this->cat_id)
				{
					$this->t->set_var('add','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.add', 'cat_id' => $this->cat_id) )
						. '"><input type="submit" name="Add" value="' . lang('Add') .'"></form>');
				}
				else
				{
					if ($this->botodo->check_perms($cat[0]['owner'], $this->grants,PHPGW_ACL_ADD) || $cat[0]['owner'] == $GLOBALS['phpgw_info']['user']['account_id'])
					{
						$this->t->set_var('add','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.add', 'cat_id' => $this->cat_id) )
							. '"><input type="submit" name="Add" value="' . lang('Add') .'"></form>');
					}
					else
					{
						$this->t->set_var('add','');
					}
				}

// ----------------------- end Add form declaration ----------------------------

// ------------ get actual date and year for matrixview arguments --------------

				$year = date('Y');
				$month = date('m');

				$this->t->set_var('matrixview', '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.matrix', 'month' => $month, 'year' => $year) ) . '">'
					. lang('View matrix of actual month') . "</a>\n");

				$body .= $this->t->fp('out','page_footer');
			}
			$this->save_sessiondata();
			return $body;
		}

		function formatted_user($selected = '',$type)
		{
			if (!$selected)
			{
				$selected = $this->account;
			}

			if (! is_array($selected))
			{
				$selected = explode(',',$selected);
			}

			$user_list = '';

			$accounts = $this->botodo->employee_list($type);
            //_debug_array($accounts);
			foreach ( $accounts as $account )
			{
				$user_list .= '<option value="' . $account->id . '"';
				if (in_array($account->id, $selected))
				{
					$user_list .= ' selected';
				}
				$user_list .= '>' . $GLOBALS['phpgw']->accounts->id2name($account->id) . "</option>\n";
			}
			return $user_list;
		}

		function formatted_todo($selected = '')
		{
			$todos = $this->botodo->_list(0, False);

			$todo_select = '';
			foreach ( $todos as $todo )
			{
				$todo_select .= '<option value="' . $todo['id'] . '"';
				if ($todo['id'] == $selected)
				{
					$todo_select .= ' selected';
				}
				if (! $todo['title'])
				{
					$words = explode(' ',$GLOBALS['phpgw']->strip_html($todo['descr']));
					$title = "$words[0] $words[1] $words[2] $words[3] ...";
					$todo_select .= ">$title";
				}
				else
				{
					$todo_select .= '>' . $GLOBALS['phpgw']->strip_html($todo['title']);
				}
				$todo_select .= '</option>';
			}
			return $todo_select;
		}

		function add()
		{
			$cat_id			= phpgw::get_var('cat_id', 'int', 'REQUEST', 0);
			$new_cat		= phpgw::get_var('new_cat', 'int', 'REQUEST', 0);
			$values			= phpgw::get_var('values');
			$submit			= phpgw::get_var('submit', 'bool');
			$new_parent		= phpgw::get_var('new_parent', 'int', 'REQUEST', 0);
			$parent			= phpgw::get_var('parent', 'int', 'REQUEST', 0);
			$assigned		= phpgw::get_var('assigned');
			$assigned_group	= phpgw::get_var('assigned_group');

			if ($new_parent)
			{
				$parent = $new_parent;
			}

			if ($new_cat)
			{
				$cat_id = $new_cat;
			}

			if ($submit)
			{
				$values['cat'] = $cat_id;
				$values['parent'] = $parent;
				if ( !isset($values['main']) )
				{
					$values['main'] = $cat_id;
					$values['level'] = 0;
				}

				$values['assigned'] = '';
				if ( is_array($assigned) )
				{
					$values['assigned'] = implode(',', $assigned);
					if (count($assigned) > 1)
					{
						$values['assigned'] = ", {$values['assigned']} ,";
					}
				}

				$values['assigned_group'] = '';
				if ( is_array($assigned_group) )
				{
					$values['assigned_group'] = implode(',', $assigned_group);
					if (count($assigned_group) > 1)
					{
						$values['assigned_group'] = ',' . $values['assigned_group'] . ',';
					}
				}

				$error = $this->botodo->check_values($values);
				if (is_array($error))
				{
					$this->t->set_var('error',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->botodo->save($values);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'todo.uitodo.show_list', 'cat_id' => (int) $cat_id) );
					exit;
				}
			}

			$GLOBALS['phpgw']->common->phpgw_header(true);

			$this->t->set_file('todo_add','form.tpl');
			$this->t->set_block('todo_add','add','addhandle');
			$this->t->set_block('todo_add','edit','edithandle');

			$this->set_app_langs();
			$this->t->set_var('actionurl', $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.add')));

			if ($parent)
			{
				$this->t->set_var('lang_todo_action',lang('Add sub project'));
			}
			else
			{
				$this->t->set_var('lang_todo_action',lang('Add main project'));
			}

			if ( !isset($values['descr']) || !$values['descr'] )
			{
				$values['descr'] = '';
			}

			if ( !isset($values['smonth']) || !$values['smonth'])
			{
				$values['smonth'] = date('m',time());
			}

			if ( !isset($values['sday']) || !$values['sday'] )
			{
				$values['sday'] = date('d',time());
			}

			if ( ! isset($values['syear']) || !$values['syear'] )
			{
				$values['syear'] = date('Y',time());
			}

			$plus1week = strtotime('+1 week');
			if ( !isset($values['eday']) || !$values['eday'] )
			{
				$values['eday'] = date('d', $plus1week);
			}

			if ( !isset($values['emonth']) || !$values['emonth'])
			{
				$values['emonth'] = date('m', $plus1week);
			}

			if ( !isset($values['eyear']) || !$values['eyear'] )
			{
				$values['eyear'] = date('Y', $plus1week);
			}
			unset($plus1week);

			$this->t->set_var($values + array
			(
				'cat_list'			=> $this->cats->formatted_list('select','all',$cat_id,'True'),
				'todo_list'			=> $this->formatted_todo($parent),
				'pri_list'			=> phpgwapi_sbox::getPriority('values[pri]'),
				'stat_list'			=> phpgwapi_sbox::getPercentage('values[status]',0),
				'user_list'			=> $this->formatted_user($assigned,'accounts'),
				'group_list'		=> $this->formatted_user($assigned_group,'groups'),
				'lang_selfortoday'	=> lang('or: select for today:'),
				'lang_daysfromstartdate' => lang('or: days from startdate:'),
				'lang_submit'		=> lang('Submit'),
				'lang_reset'		=> lang('Clear form'),
				'edithandle'		=> '',
				'addhandle'			=> '',
				'start_select_date'	=> $GLOBALS['phpgw']->common->dateformatorder(phpgwapi_sbox::getYears('values[syear]',$values['syear']),
										phpgwapi_sbox::getMonthText('values[smonth]',$values['smonth']),phpgwapi_sbox::getDays('values[sday]',$values['sday'])),
				'end_select_date'	=> $GLOBALS['phpgw']->common->dateformatorder(phpgwapi_sbox::getYears('values[eyear]',$values['eyear']),
										phpgwapi_sbox::getMonthText('values[emonth]',$values['emonth']),phpgwapi_sbox::getDays('values[eday]',$values['eday'])),
				'selfortoday'		=> '<input type="checkbox" name="values[seltoday]" value="True">',
				'daysfromstartdate'	=> '<input type="text" name="values[daysfromstart]" size="3" maxlength="3">',
				'access_list'		=> '<input type="checkbox" name="values[access]" value="True"' . (!isset($values['access']) || $values['access'] == 'private' ? ' checked' : '') . '>'
			));

			$this->t->pfp('out','todo_add');
			$this->t->pfp('addhandle','add');
		}

		function view()
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$values = $this->botodo->read($_REQUEST['todo_id']);
			$this->t->set_file('_view','view.tpl');

			$this->set_app_langs();

			$this->t->set_var('lang_todo_action',lang('View todo item'));
			$this->t->set_var('value_title',$GLOBALS['phpgw']->strip_html($values['title']));
			$this->t->set_var('value_descr',$GLOBALS['phpgw']->strip_html($values['descr']));
			$this->t->set_var('value_category',$this->cats->id2name($values['cat']));

			$sdate = $values['sdate'] - $this->botodo->datetime->tz_offset;
			$this->t->set_var('value_start_date',$GLOBALS['phpgw']->common->show_date($sdate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']));

			if ($values['edate'] && $values['edate'] != 0)
			{
				$edate = $values['edate'] - $this->botodo->datetime->tz_offset;
				$this->t->set_var('value_end_date',$GLOBALS['phpgw']->common->show_date($edate,$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']));
			}

			$parent_values = $this->botodo->read($values['parent']);
			if ( is_array($parent_values) && count($parent_values) )
			{
				$this->t->set_var('value_parent',$GLOBALS['phpgw']->strip_html($parent_values['title']));
			}
			else
			{
				$this->t->set_var('value_parent', '');
			}

			$this->t->set_var('value_completed',$values['status']);

			$assigned = $this->botodo->list_assigned($this->botodo->format_assigned($values['assigned']));
			$assigned .= $this->botodo->list_assigned($this->botodo->format_assigned($values['assigned_group']));

			$this->t->set_var('assigned',$assigned);

			$cached_data = $this->botodo->cached_accounts($values['owner']);


            /**
             * Begin Orlando Fix
             *
             * I had to change how $cached_data variables were used( as arrays)
             * so they can be read as: object -> attribute
             */
			$this->t->set_var('owner',$GLOBALS['phpgw']->common->display_fullname($cached_data->lid,
									$cached_data->firstname,$cached_data->lastname));
            /**
             * End Orlando Fix
             */


			switch ($values['pri'])
			{
				case 1:
					$pri = lang('Low');
					break;
				case 2:
					$pri = lang('normal');
					break;
				case 3:
					$pri = '<font color="CC0000"><b>' . lang('high') . '</b></font>';
					break;
			}

			$this->t->set_var('value_urgency',$pri);

			$this->t->set_var('lang_access',lang('Access'));
			$this->t->set_var('access',lang($values['access']));

			$this->t->set_var('history',$this->historylog->return_html(array(),'','',$_REQUEST['todo_id']));
			$this->t->set_var('done_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.show_list') ) );
			$this->t->pfp('out','_view');
		}

		function edit()
		{
			$todo_id = isset($_REQUEST['todo_id']) ? (int) $_REQUEST['todo_id'] : 0;
			$cat_id = isset($_POST['cat_id']) ? (int) $_POST['cat_id'] : 0;
			$new_cat = isset($_POST['new_cat']) ? (int) $_POST['new_cat'] : 0;
			$values = isset($_POST['values']) ? (array) $_POST['values'] : array();
			$submit = isset($_POST['submit']) ? !!$_POST['submit'] : false;
			$new_parent = isset($_POST['new_parent']) ? $_POST['new_parent'] : 0;
			$parent = isset($_POST['parent']) ? (int) $_POST['parent'] : 0;
			$assigned = isset($_POST['assigned']) ? $_POST['assigned'] : 0;
			$assigned_group = isset($_POST['assigned_group']) ? $_POST['assigned_group'] : 0;

			if ($new_parent)
			{
				$parent = $new_parent;
			}

			if ($new_cat)
			{
				$cat_id = $new_cat;
			}

            if ($submit)
            {
                $values['cat'] = $cat_id;
                $values['parent'] = $parent;
                $values['id'] = $todo_id;

				if (is_array($assigned))
				{
					$values['assigned'] = implode(',',$assigned);
					if (count($assigned) > 1)
					{
						$values['assigned'] = ',' . $values['assigned'] . ',';
					}
				}

				if (is_array($assigned_group))
				{
					$values['assigned_group'] = implode(',',$assigned_group);
					if (count($assigned_group) > 1)
					{
						$values['assigned_group'] = ',' . $values['assigned_group'] . ',';
					}
				}

				$error = $this->botodo->check_values($values);
				if (is_array($error))
				{
					$this->t->set_var('error',$GLOBALS['phpgw']->common->error_list($error));
				}
				else
				{
					$this->botodo->save($values,'edit');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'todo.uitodo.show_list', 'cat_id' => $cat_id) );
					$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
					exit;
				}
			}

			$GLOBALS['phpgw']->common->phpgw_header(true);

			$this->t->set_file(array('todo_edit' => 'form.tpl'));
			$this->t->set_block('todo_edit','add','addhandle');
			$this->t->set_block('todo_edit','edit','edithandle');

			$this->set_app_langs();
			$this->t->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.edit', 'todo_id' => $todo_id) ) );

			$values = $this->botodo->read($todo_id);

			if ($values['parent'] != 0)
			{
				$this->t->set_var('lang_todo_action',lang('Edit sub project'));
			}
			else
			{
				$this->t->set_var('lang_todo_action',lang('Edit main project'));
			}

			$this->t->set_var('cat_list',$this->cats->formatted_list('select','all',$values['cat'],'True'));
			$this->t->set_var('todo_list',$this->formatted_todo($values['parent']));

			$this->t->set_var('descr',$GLOBALS['phpgw']->strip_html($values['descr']));
			$this->t->set_var('title',$GLOBALS['phpgw']->strip_html($values['title']));

			$this->t->set_var('pri_list', phpgwapi_sbox::getPriority('values[pri]',$values['pri']));
			$this->t->set_var('stat_list', phpgwapi_sbox::getPercentage('values[status]',$values['status']));
			$this->t->set_var('user_list',$this->formatted_user($this->botodo->format_assigned($values['assigned']),'accounts'));
			$this->t->set_var('group_list',$this->formatted_user($this->botodo->format_assigned($values['assigned_group']),'groups'));

			if ($values['sdate'] == 0)
			{
				$values['sday'] = 0;
				$values['smonth'] = 0;
				$values['syear'] = 0;
			}
			else
			{
				$values['sday'] = date('d',$values['sdate']);
				$values['smonth'] = date('m',$values['sdate']);
				$values['syear'] = date('Y',$values['sdate']);
			}

			$this->t->set_var('start_select_date',$GLOBALS['phpgw']->common->dateformatorder(phpgwapi_sbox::getYears('values[syear]',$values['syear']),
										phpgwapi_sbox::getMonthText('values[smonth]',$values['smonth']),phpgwapi_sbox::getDays('values[sday]',$values['sday'])));

			if ($values['edate'] == 0)
			{
				$values['eday'] = 0;
				$values['emonth'] = 0;
				$values['eyear'] = 0;
			}
			else
			{
				$values['eday'] = date('d',$values['edate']);
				$values['emonth'] = date('m',$values['edate']);
				$values['eyear'] = date('Y',$values['edate']);
			}

			$this->t->set_var('end_select_date',$GLOBALS['phpgw']->common->dateformatorder(phpgwapi_sbox::getYears('values[eyear]',$values['eyear']),
										phpgwapi_sbox::getMonthText('values[emonth]',$values['emonth']),phpgwapi_sbox::getDays('values[eday]',$values['eday'])));

			$this->t->set_var('selfortoday','&nbsp;');
			$this->t->set_var('lang_selfortoday','&nbsp;');
			$this->t->set_var('lang_daysfromstartdate','&nbsp;');
			$this->t->set_var('daysfromstartdate','&nbsp;');

			$this->t->set_var('access_list', '<input type="checkbox" name="values[access]" value="True"' . ($values['access'] == 'private'?' checked':'') . '>');

			if ($this->botodo->check_perms($values['owner'], $this->grants,PHPGW_ACL_DELETE) || $values['owner'] == $GLOBALS['phpgw_info']['user']['account_id'])
			{
				$this->t->set_var('delete','<form method="POST" action="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.delete', 'todo_id' => $values['id']) )
                                    . '"><input type="submit" value="' . lang('Delete') .'"></form>');
			}
			else
			{
				$this->t->set_var('delete','&nbsp;');
			}

			$this->t->set_var('lang_submit',lang('Submit'));
			$this->t->set_var('edithandle','');
			$this->t->set_var('addhandle','');
			$this->t->pfp('out','todo_edit');
			$this->t->pfp('edithandle','edit');
		}

		function delete()
		{
			$todo_id = isset($_REQUEST['todo_id']) ? (int)$_REQUEST['todo_id'] : 0;

			if ( isset($_POST['confirm']) && $_POST['confirm'] )
			{
				if ( isset($_POST['subs']) && $_POST['subs'] )
				{
					$this->botodo->delete($todo_id, true);
				}
				else
				{
					$this->botodo->delete($todo_id);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'todo.uitodo.show_list') );
			}
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$this->t->set_file('todo_delete', 'delete.tpl');
			$this->t->set_var('actionurl',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.delete', 'todo_id' => $todo_id) ) );

			$exists = $this->botodo->exists($todo_id);

			if ($exists)
			{
				$this->t->set_var('lang_subs',lang('Do you also want to delete all sub projects ?'));
				$this->t->set_var('subs','<input type="checkbox" name="subs" value="True">');
			}
			else
			{
				$this->t->set_var('lang_subs','');
				$this->t->set_var('subs', '');
			}

			$this->t->set_var('nolink',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.show_list') ) );
			$this->t->set_var('deleteheader',lang('Are you sure you want to delete this entry'));
			$this->t->set_var('lang_no',lang('No'));
			$this->t->set_var('lang_yes',lang('Yes'));

			$this->t->pfp('out','todo_delete');
		}

		function matrix()
		{
			$o = 0;
			$month = isset($_REQUEST['month']) ? (int) $_REQUEST['month'] : date('n');
			$year = isset($_REQUEST['year']) ? (int) $_REQUEST['year'] : date('Y');

			$GLOBALS['phpgw']->common->phpgw_header(true);

			$colors = array
			(
				'#cc0033',
				'#006600',
				'#00ccff',
				'#ff6600',
				'#0000ff'
			);

			$this->matrix->matrixview($month, $year);

			$entries = $this->botodo->_list(0, 0, '', '', '', '', '','mains');

			foreach ( $entries as $entry )
			{
				++$o;
				$ind = $o % count($colors);

				if ($entry['sdate_epoch'] > 0 && $entry['edate_epoch'] > 0)
				{
					$title = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.view', 'todo_id' => $entry['id'])) . '">' . $GLOBALS['phpgw']->strip_html($entry['title']) . '</a>';
					$startd = date('Y',$entry['sdate_epoch']) . date('m',$entry['sdate_epoch']) . date('d',$entry['sdate_epoch']);
					$endd = date('Y',$entry['edate_epoch']) . date('m',$entry['edate_epoch']) . date('d',$entry['edate_epoch']);
					$this->matrix->setPeriod($title, $startd, $endd, $colors[$ind]);

					$subentries = $this->botodo->_list(0, 0, '', '', '', '', '', 'subs', $entry['id']);
					foreach ( $subentries as $subentry )
					{
						if ($subentry['sdate_epoch'] > 0 && $subentry['edate_epoch'] > 0)
						{
							$title = '<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.view', 'todo_id' => $subentry['id'])) . '">' . $GLOBALS['phpgw']->strip_html($subentry['title']) . '</a>';
							$startd = date('Y',$subentry['sdate_epoch']) . date('m',$subentry['sdate_epoch']) . date('d',$subentry['sdate_epoch']);                                                        
							$endd = date('Y',$subentry['edate_epoch']) . date('m',$subentry['edate_epoch']) . date('d',$subentry['edate_epoch']);
							$this->matrix->setPeriod($GLOBALS['phpgw']->strip_html($subentry['title']),$startd,$endd,$colors[$ind]);
						}
					}
				}
			}
			$this->matrix->out($GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'todo.uitodo.matrix') ) );
		}
	}
