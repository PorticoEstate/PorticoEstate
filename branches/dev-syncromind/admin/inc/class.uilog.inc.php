<?php
	/**************************************************************************\
	* phpGroupWare - Administration                                            *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	class admin_uilog
	{
		public $public_functions = array
		(
			'list_log'	=> true,
			'purge_log'	=> true
		);

		public function __construct()
		{
			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}
			
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::admin::error_log';
		}

		public function list_log()
		{
			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/admin/index.php');
			}
			
			$account_id		= phpgw::get_var('account_id', 'int');
			$start			= phpgw::get_var('start', 'int');
			$sort			= phpgw::get_var('sort', 'int');
			$order			= phpgw::get_var('order', 'int');
			
		
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Admin').' - '.lang('View error log');
			if ( $account_id )
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] .= ' ' . lang('for') . ' ' . $GLOBALS['phpgw']->common->grab_owner_name($account_id);
			}
			
			$GLOBALS['phpgw']->common->phpgw_header(true);

			$bo = createObject('admin.bolog');
			$nextmatches = createObject('phpgwapi.nextmatchs');

			$t   =& $GLOBALS['phpgw']->template;
			$t->set_root(PHPGW_APP_TPL);

			$t->set_file(array
			(
				'errorlog'		=> 'errorlog_view.tpl',
				'form_button'	=> 'form_button_script.tpl'
			));

			$t->set_block('errorlog','list');
			$t->set_block('errorlog','row');
			$t->set_block('errorlog','row_empty');
	
			$total_records = $bo->total($account_id);

			$var = array
			(
				'nextmatchs_left'  => $nextmatches->left('/index.php',$start,$total_records,'&menuaction=admin.uilog.list_log&account_id=' . $account_id),
				'nextmatchs_right' => $nextmatches->right('/index.php',$start,$total_records,'&menuaction=admin.uilog.list_log&account_id=' . $account_id),
				'showing'          => $nextmatches->show_hits($total_records,$start),
				'lang_loginid'     => lang('LoginID'),
				'lang_date'        => lang('time'),
				'lang_app'         => lang('module'),
				'lang_severity'    => lang('severity'),
				'lang_line'        => lang('line'),
				'lang_file'        => lang('file'),
				'lang_message'     => lang('log message'),
				'lang_total'       => lang('Total')
			);

			$t->set_var($var);

			$records = $bo->list_log($account_id, $start, $order, $sort);
			if ( !is_array($records) || !count($records) )
			{
				$t->set_var(array
				(
					'row_message'	=> lang('No error log records exist for this user'),
					'tr_class'		=> 'row_on'
				));
				$t->fp('rows_access', 'row_empty', true);
			}
			else
			{
				$tr_class = '';
				foreach ( $records as $record )
				{
					$tr_class = $nextmatches->alternate_row_class($tr_class);
					$t->set_var(array
					(
						'row_date' 		=> $record['log_date'],
						'row_loginid'   => $record['log_account_lid'],
						'row_app'      	=> $record['log_app'],
						'row_severity'  => $record['log_severity'],
						'row_file'      => $record['log_file'],
						'row_line'      => $record['log_line'],
						'row_message'   => $record['log_msg'],
						'tr_class'		=> $tr_class
					));
					$t->parse('rows_access', 'row', true);
				}
			}

			if ( $total_records ) 
			{
				if ( $account_id ) 
				{
					$var = array
					(
						'submit_button'			=> lang('Submit'),
						'action_url_button'     => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uilog.purge_log', 'account_id' => $account_id)),
						'action_text_button'    => ' '.lang('Delete all log records for %1', $GLOBALS['phpgw']->common->grab_owner_name($account_id)),
						'action_confirm_button' => '',
						'action_extra_field'    => ''
					);
				}
				else 
				{
					$var = array
					(
						'submit_button'			=> lang('Submit'),
						'action_url_button'     => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uilog.purge_log') ),
						'action_text_button'    => ' '.lang('Delete all log records'),
						'action_confirm_button' => '',
						'action_extra_field'    => ''
					);
				}
				$t->set_var($var);
				$var['purge_log_button'] = $t->fp('button', 'form_button', true);

				$t->set_var($var);
			}

			if ( $account_id ) 
			{
				$account_name = $GLOBALS['phpgw']->common->grab_owner_name($account_id);
				$var = array('footer_total' => lang('Total records for %1 : %2', $account_name, $total_records) );
			}
			else
			{
				$var = array('footer_total' => lang('Total records: %1', $total_records));
			}

			// create the menu on the left, if needed
			//$var['rows'] = createObject('admin.uimenuclass')->createHTMLCode('view_account');

			//$t->set_var($var);
			$t->pfp('out', 'list');
		}
		
		public function purge_log()
		{
			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}
			execMethod('admin.bolog.purge_log', phpgw::get_var('account_id', 'int') );
			$GLOBALS['phpgw']->redirect_link('index.php', array('menuaction', 'admin.uilog.list_log'));
		}
	}
