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

	class admin_uiloglevels
	{
		private $template;
		private $select_template;
		public $public_functions = array
		(
			'edit_log_levels' => True
		);

		public function __construct()
		{

			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::admin::log_levels';

			$this->template   = $GLOBALS['phpgw']->template;
			$this->template->set_file(array
			(
				'loglevels' => 'loglevels.tpl',
				'log_level_select' => 'log_level_select.tpl'
			));

  			// foo removes the module template.  I don't understand Templates enough to
  			// know why I needed it.  Trial and error.
			$this->template->set_block('loglevels','module', 'foo');
			$this->template->set_block('loglevels','module_add', 'foo');
		}

		public function edit_log_levels()
		{
			if ($GLOBALS['phpgw']->acl->check('error_log_access',1,'admin'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php');
			}

			// If log_levels have ever been set before, go ahead and set them.
			// There's probably a more correct place to do this.

			if ( ! isset($GLOBALS['phpgw_info']['server']['log_levels'] ) )
			{
				$GLOBALS['phpgw_info']['server']['log_levels'] = array( 'global_level' => 'E', 'module' => array(), 'user' => array());
			}

			// If they've updated something, save the change.
			$level_type = phpgw::get_var('level_type');
		    if ( $level_type )
		    {
		    	$level_key = phpgw::get_var('level_key');
		    	$new_level = phpgw::get_var( $level_type . '_' . $level_key . '_select');
		    	$this->update_level($level_type, $level_key, $new_level);
		    }
			else
			{
				$level_key = phpgw::get_var('module_add_name_select');
				if ( $level_key )
				{
					$this->update_level('module', $level_key, phpgw::get_var('module_add_level_select'));
				}
				$level_key = phpgw::get_var('user_add_name_select');
				if ( $level_key )
				{
					$this->update_level('user', $level_key, phpgw::get_var('user_add_level_select'));
				}
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('Admin').' - '.lang('Edit Log Levels');

			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();

			$this->add_modules_list();
			$this->add_users_list();

			$var['lang_set_levels'] = "Set Logging Levels";
			$var['lang_global_level'] = "Global logging level";
			$var['lang_module_level'] = "Module Logging Levels";
			$var['lang_user_level'] = "User Logging Levels";
			$var['global_option'] = $this->create_select_box('global', '', $GLOBALS['phpgw_info']['server']['log_levels']['global_level']);
			$this->template->set_var($var);

			$this->template->pfp('out','loglevels');
		}


		private function add_modules_list()
		{
			$apps_with_logging = $GLOBALS['phpgw_info']['server']['log_levels']['module'];
			$sorted_apps = array();
			$app_add_list = array();
			foreach ( $GLOBALS['phpgw_info']['apps'] as $app => $app_data )
			{
				$sorted_apps[$app] = $app_data['title'];
			}

			$sorted_apps['login'] = 'Login';

			asort($sorted_apps);

			$add_options = '';
			$tr_class = 'pure-table-odd';
		    foreach ( $sorted_apps as $app => $title)
		    {
		    	if ( isset($GLOBALS['phpgw_info']['server']['log_levels']['module'][$app]) )
		    	{
					$var = array(
						'tr_class' 		=> $tr_class,
						'type'   		=> 'module',
						'module_name'   => $title,
						'module_option' => $this->create_select_box('module', $app, $GLOBALS['phpgw_info']['server']['log_levels']['module'][$app]),
						'remove_url' 	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiloglevels.edit_log_levels', 'level_type' => 'module', 'level_key' => $app) ),
						'lang_remove'   => lang('remove')
					);
					$this->template->set_var($var);
					$this->template->fp('module_list','module',True);
					if ($tr_class == 'pure-table-odd' )
					{
						$tr_class = '';
					}
					else
					{
						$tr_class = 'pure-table-odd';
					}
					$add_options .= "<option disabled=\"disabled\">{$title}</option>\n";
		    	}
		    	else
		    	{
					$add_options .= "<option value=\"{$app}\">{$title}</option>\n";
		    	}
		    }

			if ( $add_options )
			{
				$var = array(
					'tr_class' 		=> $tr_class,
					'type'   		=> 'module',
					'module_add_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiloglevels.edit_log_levels') ),
					'lang_add' => lang('add'),
					'module_add_options'   => $add_options,
					'lang_fatal'    => lang('fatal'),
					'lang_error'    => lang('error'),
					'lang_warn' 	=> lang('warn'),
					'lang_notice' 	=> lang('notice'),
					'lang_info'    	=> lang('info'),
					'lang_debug'    => lang('debug'),
					'lang_add'   	=> lang('add')
				);
				$this->template->set_var($var);
				$this->template->fp('module_add_row','module_add',True);
			}

		}

		private function add_users_list()
		{
			$add_options = '';
			$tr_class = 'pure-table-odd';
			$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts');
			foreach ( $accounts as $account )
			{
				$account_lid = $account->lid;
				$name = (string) $account;
		    	if ( isset($GLOBALS['phpgw_info']['server']['log_levels']['user'][$account_lid]) )
		    	{
					$var = array
					(
						'tr_class' 		=> $tr_class,
						'module_name'   => (string) $account,
						'module_option' => $this->create_select_box('user', $account_lid, $GLOBALS['phpgw_info']['server']['log_levels']['user'][$account_lid]),
						'remove_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiloglevels.edit_log_levels', 'level_type' => 'user', 'level_key' => $account_lid) ),
						'lang_remove'   => lang('remove')
					);

					$this->template->set_var($var);
					$this->template->fp('user_list', 'module', true);

					if ($tr_class == 'pure-table-odd')
					{
						$tr_class = '';
					}
					else
					{
						$tr_class = 'pure-table-odd';
					}
		    	}
		    	else
		    	{
					$add_options .= "<option value=\"{$account_lid}\">{$name}</option>\n";
		    	}
		    }

			if ( $add_options )
			{
				$var = array(
					'type'   		=> 'user',
					'tr_class' 		=> $tr_class,
					'module_add_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiloglevels.edit_log_levels') ),
					'lang_add' => lang('add'),
					'module_add_options'   => $add_options,
					'lang_fatal'    => lang('fatal'),
					'lang_error'    => lang('error'),
					'lang_warn' 	=> lang('warn'),
					'lang_info'    	=> lang('info'),
					'lang_debug'    => lang('debug'),
					'lang_add'   	=> lang('add')
				);
				$this->template->set_var($var);
				$this->template->fp('user_add_row','module_add',True);
			}
		}

		private function create_select_box($level_type, $level_key, $current_level)
		{
			$select_name = "{$level_type}_{$level_key}_select";

			$var = array
			(
				'level_type'	=> $level_type,
				'level_key'		=> $level_key,
				'select_link'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiloglevels.edit_log_levels') ),
				'select_name'	=> $select_name,
				'lang_fatal'	=> lang('fatal'),
				'lang_error'	=> lang('error'),
				'lang_warn'		=> lang('warn'),
				'lang_info'		=> lang('info'),
				'lang_notice'	=> lang('notice'),
				'lang_debug'	=> lang('debug'),
				'lang_strict'	=> 'strict',
				'lang_deprecated'	=> 'deprecated',
				'lang_all'		=> lang('all'),
				'F_selected'	=> '',
				'E_selected'	=> '',
				'N_selected'	=> '',
				'W_selected'	=> '',
				'I_selected'	=> '',
				'D_selected'	=> '',
				'S_selected'	=> '',
				'DP_selected'	=> '',
				'A_selected'	=> ''
			);

			if ( $current_level )
			{
				$var[$current_level . '_selected'] = 'selected';
			}
			else
			{
				$var['I_selected'] = 'selected';
			}

			$this->template->set_var($var);
			return $this->template->fp('select', 'log_level_select');
		}

		private function update_level($level_type, $level_key, $new_level)
		{
			if ( $new_level )
			{
				if ( $level_type == "global" )
				{
					log_info('Setting %1 log level to %2', $level_type, $new_level);
					$GLOBALS['phpgw_info']['server']['log_levels']['global_level'] = $new_level;
				}
				else
				{
					log_info('Setting log level for %1 %2 to %3', $level_type, $level_key, $new_level);
					$GLOBALS['phpgw_info']['server']['log_levels'][$level_type][$level_key] = $new_level;
				}
			}
			else
			{
				log_info('Removing log level for %1 %2', $level_type, $level_key);
				unset($GLOBALS['phpgw_info']['server']['log_levels'][$level_type][$level_key]);
			}

			// save it...  It would be nice if phpgwapi.config had an method for just saving one setting.

			$c = CreateObject('phpgwapi.config','phpgwapi');
			$c->read();
			$c->value('log_levels', $GLOBALS['phpgw_info']['server']['log_levels']);
			$c->save_repository();
		}

	}
