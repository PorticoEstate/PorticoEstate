<?php
	/**************************************************************************\
	* phpGroupWare - Administration                                            *
	* http://www.phpgroupware.org                                              *
	*  This file written by Joseph Engo <jengo@phpgroupware.org>               *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: class.uicurrentsessions.inc.php 18039 2007-03-16 08:59:37Z sigurdne $ */

	class admin_uicurrentsessions
	{
		private $template;

		private $bo;

		public $public_functions = array
		(
			'list_sessions' => true,
			'kill'          => true
		);

		public function __construct()
		{
			$this->bo         = createobject('admin.bocurrentsessions');

			$this->template   =& $GLOBALS['phpgw']->template;
		}

		private function header()
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
			// header sets the tpl dir to the api/tpl dir - so we reset it
			$this->template->set_root(PHPGW_APP_TPL);
		}

		private function store_location($info)
		{
			$GLOBALS['phpgw']->session->appsession('currentsessions_session_data','admin',$info);
		}

		public function list_sessions()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::admin::sessions';

			$info = $GLOBALS['phpgw']->session->appsession('currentsessions_session_data','admin');
			if (! is_array($info))
			{
				$info = array
				(
					'start' => 0,
					'sort'  => 'asc',
					'order' => 'session_dla'
				);
				$this->store_location($info);
			}

			if ((isset($_GET['start']) && $_GET['start']) || ( isset($_GET['sort']) && $_GET['sort']) || ( isset($_GET['order']) && $_GET['order']))
			{
				if ($_GET['start'] == 0 || $_GET['start'] && $_GET['start'] != $info['start'])
				{
					$info['start'] = $_GET['start'];
				}

				if ($_GET['sort'] && $_GET['sort'] != $info['sort'])
				{
					$info['sort'] = $_GET['sort'];
				}

				if ($_GET['order'] && $_GET['order'] != $info['order'])
				{
					$info['order'] = $_GET['order'];
				}

				$this->store_location($info);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Admin').' - '.lang('List of current users');
			$this->header();

			$this->template->set_file('current','currentusers.tpl');
			$this->template->set_block('current','list','list');
			$this->template->set_block('current','row','row');

			if (! $GLOBALS['phpgw']->acl->check('current_sessions_access',4,'admin'))
			{
				$can_view_ip = True;
			}

			if (! $GLOBALS['phpgw']->acl->check('current_sessions_access',2,'admin'))
			{
				$can_view_action = True;
			}

			$total = $this->bo->total();

			$nextmatchs = createobject('phpgwapi.nextmatchs');

			$this->template->set_var('left_next_matchs',$nextmatchs->left('/admin/currentusers.php',$info['start'],$total));
			$this->template->set_var('right_next_matchs',$nextmatchs->right('/admin/currentusers.php',$info['start'],$total));

			$this->template->set_var('sort_loginid',$nextmatchs->show_sort_order($info['sort'],'session_lid',$info['order'],
				'/admin/currentusers.php',lang('LoginID')));
			$this->template->set_var('sort_ip',$nextmatchs->show_sort_order($info['sort'],'session_ip',$info['order'],
				'/admin/currentusers.php',lang('IP')));
			$this->template->set_var('sort_login_time',$nextmatchs->show_sort_order($info['sort'],'session_logintime',$info['order'],
				'/admin/currentusers.php',lang('Login Time')));
			$this->template->set_var('sort_action',$nextmatchs->show_sort_order($info['sort'],'session_action',$info['order'],
				'/admin/currentusers.php',lang('Action')));
			$this->template->set_var('sort_idle',$nextmatchs->show_sort_order($info['sort'],'session_dla',$info['order'],
				'/admin/currentusers.php',lang('idle')));
			$this->template->set_var('lang_kill',lang('Kill'));

			$values = $this->bo->list_sessions($info['start'],$info['order'],$info['sort']);

			while (list(,$value) = @each($values))
			{
				$nextmatchs->template_alternate_row_class($this->template);

				$this->template->set_var('row_loginid',$value['session_lid']);

				if ($can_view_ip)
				{
					$this->template->set_var('row_ip',$value['session_ip']);
				}
				else
				{
					$this->template->set_var('row_ip','&nbsp; -- &nbsp;');
				}

				$this->template->set_var('row_logintime',$value['session_logintime']);
				$this->template->set_var('row_idle',$value['session_idle']);

				if ($value['session_action'] && $can_view_action)
				{
					$this->template->set_var('row_action',$GLOBALS['phpgw']->strip_html($value['session_action']));
				}
				elseif(! $can_view_action)
				{
					$this->template->set_var('row_action','&nbsp; -- &nbsp;');
				}
				else
				{
					$this->template->set_var('row_action','&nbsp;');
				}

				if ($value['session_id'] != $GLOBALS['phpgw_info']['user']['sessionid'] && ! $GLOBALS['phpgw']->acl->check('current_sessions_access',8,'admin'))
				{
					$this->template->set_var('row_kill','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uicurrentsessions.kill',
						'ksession'=> $value['session_id'], 'kill'=>'true')) . '">' . lang('Kill').'</a>');
				}
				else
				{
					$this->template->set_var('row_kill','&nbsp;');
				}

				$this->template->parse('rows','row',True);
			}

			$this->template->pfp('out','list');
		}

		public function kill()
		{
			if ($GLOBALS['phpgw']->acl->check('current_sessions_access',8,'admin'))
			{
				$this->list_sessions();
				return False;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Admin').' - '.lang('Kill session');
			$this->header();
			$this->template->set_file('form','kill_session.tpl');

			$this->template->set_var('lang_message',lang('Are you sure you want to kill this session ?'));
			$this->template->set_var('link_no','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uicurrentsessions.list_sessions')) . '">' . lang('No') . '</a>');
			$this->template->set_var('link_yes','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.bocurrentsessions.kill', 'ksession'=> $_GET['ksession'])) . '">' . lang('Yes') . '</a>');

			$this->template->pfp('out','form');
		}
	}
