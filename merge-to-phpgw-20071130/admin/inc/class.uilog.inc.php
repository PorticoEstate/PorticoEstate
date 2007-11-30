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

	/* $Id: class.uilog.inc.php 18358 2007-11-27 04:43:37Z skwashd $ */

	class uilog
	{
		var $template;
		var $public_functions = array
		(
			'list_log'	=> true,
			'purge_log'	=> true
		);

		function uilog()
		{
			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}
			
			$this->bo         = createobject('admin.bolog');
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');
			$this->template   =& $GLOBALS['phpgw']->template;

		}

		function list_log()
		{
			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}
			
			$account_id		= phpgw::get_var('account_id', 'int');
			$start			= phpgw::get_var('start', 'int', 'POST', 0);
			$sort			= phpgw::get_var('sort', 'int', 'POST',0);
			$order			= phpgw::get_var('order', 'int', 'POST',0);
			
			$this->template->set_file(array
			(
				'errorlog'		=> 'errorlog_view.tpl',
				'form_button'	=> 'form_button_script.tpl'
			));

			$this->template->set_block('errorlog','list');
			$this->template->set_block('errorlog','row');
			$this->template->set_block('errorlog','row_empty');
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Admin').' - '.lang('View error log');
			if ( $account_id )
			{
				$GLOBALS['phpgw_info']['flags']['app_header'] .= ' ' . lang('for') . ' ' . $GLOBALS['phpgw']->common->grab_owner_name($account_id);
			}
			
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$total_records = $this->bo->total($account_id);

			$var = array
			(
				'nextmatchs_left'  => $this->nextmatchs->left('/index.php',$start,$total_records,'&menuaction=admin.uilog.list_log&account_id=' . $account_id),
				'nextmatchs_right' => $this->nextmatchs->right('/index.php',$start,$total_records,'&menuaction=admin.uilog.list_log&account_id=' . $account_id),
				'showing'          => $this->nextmatchs->show_hits($total_records,$start),
				'lang_loginid'     => lang('LoginID'),
				'lang_date'        => lang('time'),
				'lang_app'         => lang('module'),
				'lang_severity'    => lang('severity'),
				'lang_line'        => lang('line'),
				'lang_file'        => lang('file'),
				'lang_message'     => lang('log message'),
				'lang_total'       => lang('Total')
			);

			$this->template->set_var($var);

			$records = $this->bo->list_log($account_id,$start,$order,$sort);
			if ( !is_array($records) || !count($records) )
			{
				$this->template->set_var(array
				(
					'row_message'	=> lang('No error log records exist for this user'),
					'tr_class'		=> 'row_on'
				));
				$this->template->fp('rows_access','row_empty',True);
			}
			else
			{
				$tr_class = '';
				foreach ( $records as $record )
				{

					$tr_class = $this->nextmatchs->alternate_row_class($tr_class);
					$this->template->set_var(array
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
					$this->template->fp('rows_access','row',True);
				}
			}

			if ( $total_records ) 
			{
				if ( $account_id ) 
				{
					$var = Array(
						'submit_button' => lang('Submit'),
						'action_url_button'     => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uilog.purge_log', 'account_id' => $account_id)),
						'action_text_button'    => ' '.lang('Delete all log records for ').$GLOBALS['phpgw']->common->grab_owner_name($account_id),
						'action_confirm_button' => '',
						'action_extra_field'    => ''
					);
				}
				else 
				{
					$var = Array(
						'submit_button' => lang('Submit'),
						'action_url_button'     => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uilog.purge_log') ),
						'action_text_button'    => ' '.lang('Delete all log records'),
						'action_confirm_button' => '',
						'action_extra_field'    => ''
					);
				}
				$this->template->set_var($var);
				$var['purge_log_button'] = $this->template->fp('button', 'form_button', True);
				$this->template->set_var($var);
			}

			if ( $account_id ) 
			{
				$var = array('footer_total' => lang('Total records for %1 : %2', $GLOBALS['phpgw']->common->grab_owner_name($account_id), $total_records) );
			}
			else
			{
				$var = array('footer_total' => lang('Total records: %1', $total_records));
			}

			// create the menu on the left, if needed
			$menuClass = CreateObject('admin.uimenuclass');
			$var['rows'] = $menuClass->createHTMLCode('view_account');

			$this->template->set_var($var);
			$this->template->pfp('out','list');
		}
		
		function purge_log()
		{
			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}
			$this->bo->purge_log( phpgw::get_var('account_id', 'int') );
			$GLOBALS['phpgw']->redirect_link('index.php', array('menuaction', 'admin.uilog.list_log'));
		}
	}
