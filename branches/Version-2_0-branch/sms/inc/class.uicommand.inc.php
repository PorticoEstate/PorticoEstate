<?php
	/**
	* phpGroupWare - SMS: A SMS Gateway.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage command
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_uicommand
	{
		var $public_functions = array(
			'index'			=> true,
			'log'			=> true,
			'redirect'		=> true,
			'edit'			=> true,
			'edit_command'	=> true,
			'delete'		=> true,
			);

		function __construct()
		{
			$this->nextmatchs		= CreateObject('phpgwapi.nextmatchs');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('sms.bocommand');
			$this->bocommon			= CreateObject('sms.bocommon');
			$this->sms				= CreateObject('sms.sms');
			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location 	= '.command';
			$this->cat_id			= $this->bo->cat_id;
			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;

			$this->db 				= clone($GLOBALS['phpgw']->db);
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'sms::command';
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
			);
			$this->bo->save_sessiondata($data);
		}


		function index()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('command','nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','sms_command_receipt');

			$GLOBALS['phpgw']->session->appsession('session_data','sms_command_receipt','');

			$command_info = $this->bo->read();

			foreach ($command_info as $entry)
			{
				if($this->bocommon->check_perms($entry['grants'], PHPGW_ACL_DELETE))
				{
					$link_delete		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicommand.delete', 'command_id'=> $entry['id']));
					$text_delete		= lang('delete');
					$lang_delete_text 	= lang('delete the command code');
				}

				$content[] = array
				(
					'code'					=> $entry['code'],
					'exec'					=> $entry['exec'],
					'user'					=> $GLOBALS['phpgw']->accounts->id2name($entry['uid']),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicommand.edit_command', 'command_id'=> $entry['id'])),
					'link_delete'				=> $link_delete,
//					'link_view'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicommand.view', 'command_id'=> $entry['id'])),
//					'lang_view_config_text'			=> lang('view the config'),
					'lang_edit_config_text'			=> lang('manage the command code'),
//					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> $text_delete,
					'lang_delete_text'			=> $lang_delete_text,
				);

				unset ($link_delete);
				unset ($text_delete);
				unset ($lang_delete_text);
			}


			$table_header[] = array
			(

				'sort_code'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'command_code',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'sms.uicommand.index',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows' 	=> $this->allrows)
										)),
				'lang_code'		=> lang('code'),
				'lang_delete'		=> lang('delete'),
				'lang_edit'		=> lang('edit'),
				'lang_view'		=> lang('view'),
				'lang_user'		=> lang('user'),
				'lang_exec'		=> lang('exec'),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> 'sms.uicommand.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

//			if($this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'sms'))
			{
				$table_add[] = array
				(
					'lang_add'		=> lang('add'),
					'lang_add_statustext'	=> lang('add a command'),
					'add_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicommand.edit_command')),
				);
			}

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'								=> execMethod('sms.menu.links'),
				'allow_allrows'						=> true,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'num_records'						=> count($command_info),
				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header'						=> $table_header,
				'table_add'							=> $table_add,
				'values'							=> $content
			);

			$appname	= lang('commands');
			$function_msg	= lang('list SMS commands');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
			$this->save_sessiondata();
		}

		function edit_command()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$command_id	= phpgw::get_var('command_id', 'int');
			if($command_id)
			{
				if(!$this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'sms'))
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if(!$this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'sms'))
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('command'));

			if (is_array($values))
			{
				if ($values['save'] || $values['apply'])
				{

					if(!$values['code'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a code !'));
					}
					else
					{
						$values['code'] = strtoupper($values['code']);
					}

					if(!$values['type'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a command type !'));
					}

					if(!$values['exec'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a command exec !'));
					}

					if($command_id)
					{
						$values['command_id']=$command_id;
						$action='edit';
					}
					else
					{
						if(!$this->sms->checkavailablecode($values['code']))
						{
							$receipt['error'][]=array('msg'=>lang('SMS code %1 already exists, reserved or use by other feature!',$values['code']));
							unset($values['code']);
						}
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save_command($values,$action);
						$command_id = $receipt['command_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','sms_command_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'sms.uicommand.index', 'command_id'=> $command_id));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'sms.uicommand.index', 'command_id'=> $command_id));
				}
			}


			if ($command_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single_command($command_id);
				}
				$function_msg = lang('edit command');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add command');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'sms.uicommand.edit_command',
				'command_id'	=> $command_id
			);

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(

				'value_code'					=> $values['code'],
				'value_descr'					=> $values['descr'],
				'lang_code'						=> lang('SMS command code'),
				'lang_descr'					=> lang('descr'),
				'lang_help1'					=> lang('Pass these parameter to command URL field:'),
				'lang_help2'					=> lang('##SMSDATETIME## replaced by SMS incoming date/time'),
				'lang_help3'					=> lang('##SMSSENDER## replaced by sender number'),
				'lang_help4'					=> lang('##COMMANDCODE## replaced by command code'),
				'lang_help5'					=> lang('##COMMANDPARAM## replaced by command parameter passed to server from SMS'),
				'lang_binary_path'				=> lang('SMS command binary path'),
				'value_binary_path'				=> PHPGW_SERVER_ROOT . "/sms/bin/{$GLOBALS['phpgw_info']['user']['domain']}",
				'lang_type'						=> lang('command type'),
				'type_list'						=> $this->bo->select_type_list($values['type']),
				'lang_no_type'					=> lang('no exec type'),
				'lang_lang_type_status_text'	=> lang('input type'),
				'value_exec'					=> $values['exec'],
				'lang_exec'						=> lang('SMS command exec'),
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_id'						=> lang('ID'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'value_id'						=> $command_id,
				'lang_done_status_text'			=> lang('Back to the list'),
				'lang_save_status_text'			=> lang('Save the values'),
				'lang_apply'					=> lang('apply'),
				'lang_apply_status_text'		=> lang('Apply the values'),
			);

			$appname	= lang('command');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_command' => $data));
		}

		function redirect()
		{
			$code		= phpgw::get_var('code');
			$param		= urldecode(phpgw::get_var('param'));

			if(is_file(PHPGW_SERVER_ROOT . "/sms/bin/{$GLOBALS['phpgw_info']['user']['domain']}/config_" . strtoupper(basename($code)) . '_log'))
			{
				include(PHPGW_SERVER_ROOT . "/sms/bin/{$GLOBALS['phpgw_info']['user']['domain']}/config_" . strtoupper(basename($code)) . '_log');

				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::log';
				$this->bocommon->no_access(lang('target not configured'));
			}

		}
		function log()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::log';
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			if(!$this->acl->check($this->acl_location, PHPGW_ACL_READ, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('command','nextmatchs',
										'search_field'));

			$command_info = $this->bo->read_log();

			foreach ($command_info as $entry)
			{
				if($this->bocommon->check_perms($entry['grants'], PHPGW_ACL_DELETE))
				{
					$link_delete		= $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicommand.delete', 'command_id'=> $entry['id']));
					$text_delete		= lang('delete');
					$lang_delete_text 	= lang('delete the command code');
				}

				$content[] = array
				(
					'id'				=> $entry['id'],
					'sender'			=> $entry['sender'],
					'success'			=> $entry['success'],
					'datetime'			=> $entry['datetime'],
					'code'				=> $entry['code'],
					'link_redirect'		=> $entry['success'] == 1 ? $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicommand.redirect','code' => $entry['code'], 'param'=> urlencode($entry['param']))) : '',
					'param'				=> $entry['param'],
					'link_delete'		=> $link_delete,
					'text_delete'		=> $text_delete,
					'lang_delete_text'	=> $lang_delete_text,
				);

				unset ($link_delete);
				unset ($text_delete);
				unset ($lang_delete_text);
			}

			$table_header[] = array
			(

				'sort_code'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'command_log_code',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'sms.uicommand.log',
														'query'		=>$this->query,
														'cat_id'	=>$this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'sort_sender'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'sms_sender',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'sms.uicommand.log',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),

				'sort_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'command_log_id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'sms.uicommand.log',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),

				'lang_id'			=> lang('id'),
				'lang_code'			=> lang('code'),
				'lang_sender'		=> lang('sender'),
				'lang_success'		=> lang('success'),
				'lang_datetime'		=> lang('datetime'),
				'lang_param'		=> lang('param'),
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
			(
				'menuaction'	=> 'sms.uicommand.log',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'menu'							=> execMethod('sms.menu.links'),
				'allow_allrows'					=> true,
				'allrows'						=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($command_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	=> lang('Submit the search string'),
				'query'							=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_log'				=> $table_header,
				'values_log'					=> $content,
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the location belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'cat_id',
				'cat_list'						=> $this->bo->get_category_list(array('format'=>'filter','selected' => $this->cat_id)),
				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
			);

			$appname	= lang('commands');
			$function_msg	= lang('list SMS command log');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('log' => $data));
			$this->save_sessiondata();
		}


		function delete()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			if(!$this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'sms'))
			{
				$this->bocommon->no_access();
				return;
			}

			$command_id	= phpgw::get_var('command_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'sms.uicommand.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$sql = "SELECT command_code FROM phpgw_sms_featcommand WHERE command_id='$command_id'";
				$this->db->query($sql,__LINE__,__FILE__);
				$this->db->next_record();

				$command_code = $this->db->f('command_code');

				if ($command_code)
				{
					$sql = "DELETE FROM phpgw_sms_featcommand WHERE command_code='$command_code'";
					$this->db->transaction_begin();
					$this->db->query($sql,__LINE__,__FILE__);
					if ($this->db->affected_rows())
	    			{
						$error_string = "SMS command code `$command_code` has been deleted!";
					}
					else
					{
						$error_string = "Fail to delete SMS command code `$command_code`";

					}

	    			$this->db->transaction_commit();
				}

				$link_data['err'] = urlencode($error_string);

				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'sms.uicommand.delete', 'command_id'=> $command_id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'				=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'				=> lang('no')
			);

			$function_msg	= lang('delete SMS command code');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
