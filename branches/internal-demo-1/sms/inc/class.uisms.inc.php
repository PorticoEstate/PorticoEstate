<?php
	/**
	* phpGroupWare - sms: A SMS Gateway
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package sms
	* @subpackage sms
 	* @version $Id: class.uisms.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	/**
	 * Description
	 * @package sms
	 */

	class sms_uisms
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
		(
			'index'  => True,
			'outbox'   => True,
			'send'   => True,
			'send_group'=> True,
			'delete_in' => True,
			'delete_out' => True
		);

		function sms_uisms()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = True;
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bocommon				= CreateObject('sms.bocommon');
//			$this->bocategory			= CreateObject('sms.bocategory');
			$this->config				= CreateObject('sms.soconfig');
			$this->config->read_repository();
			$this->gateway_number			= $this->config->config_data['common']['gateway_number'];
			$this->bo				= CreateObject('sms.bosms',False);
			$this->acl				= CreateObject('phpgwapi.acl');
			$this->menu				= CreateObject('sms.menu');
			$this->grants 				= $this->bo->grants;
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;
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
			$acl_location = '.inbox';

			$GLOBALS['phpgw']->xslttpl->add_file(array('sms','nextmatchs','menu',
										'search_field'));

			$this->bo->acl_location = $acl_location;
			$this->menu->sub = $acl_location;
			$links = $this->menu->links();

			if(!$this->acl->check($acl_location, PHPGW_ACL_READ))
			{
				$this->bocommon->no_access($links);
				return;
			}

			$sms_info = $this->bo->read_inbox();

			if($this->acl->check($acl_location, PHPGW_ACL_ADD))
			{
				$add_right 				= true;
				$text_answer			= lang('answer');
				$lang_answer_sms_text 	= lang('answer this sms');
			}
			else
			{
				$text_answer 			= '';
				$lang_answer_sms_text	= '';
			}


			while (is_array($sms_info) && list(,$entry) = each($sms_info))
			{
				if($this->bocommon->check_perms($entry['grants'], PHPGW_ACL_DELETE))
				{
					$link_delete		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.delete_in', 'id'=> $entry['id']));
					$text_delete		= lang('delete');
					$lang_delete_sms_text 	= lang('delete the sms from inbox');
				}
				else
				{
					$link_delete			= '';
					$text_delete			= '';
					$lang_delete_sms_text	= '';
				}

				if($add_right)
				{
					$link_answer		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.send' ,'p_num'=> $entry['sender']));
				}


				$content[] = array
				(
					'id'					=> $entry['id'],
					'sender'				=> $entry['sender'],
					'user'					=> $entry['user'],
					'message'				=> $entry['message'],
					'entry_time'			=> $entry['entry_time'],
					'link_delete'			=> $link_delete,
					'text_delete'			=> $text_delete,
					'lang_delete_sms_text'	=> $lang_delete_sms_text,
					'link_answer'			=> $link_answer,
					'text_answer'			=> $text_answer,
					'lang_answer_sms_text'	=> $lang_answer_sms_text,

				);
			}

//_debug_array($entry['grants']);

			$table_header[] = array
			(

				'sort_entry_time'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'in_datetime',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'sms.uisms.index',
														'query'		=>$this->query,
														'cat_id'	=>$this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'sort_sender'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'in_sender',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'sms.uisms.index',
														'query'		=>$this->query,
														'cat_id'	=>$this->cat_id,
														'allrows'	=> $this->allrows)
										)),

				'lang_delete'		=> lang('delete'),
				'lang_id'		=> lang('id'),
				'lang_user'		=> lang('user'),
				'lang_sender'		=> lang('sender'),
				'lang_entry_time'	=> lang('time'),
				'lang_message'		=> lang('message'),
				'lang_answer'		=> lang('answer'),
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
				'menuaction'	=> 'sms.uisms.index',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);



			if($this->acl->check($acl_location, PHPGW_ACL_ADD))
			{
				$table_add[] = array
				(
					'lang_send'			=> lang('Send text SMS'),
					'lang_send_statustext'		=> lang('send single'),
					'send_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.send', 'from'=>'index')),
					'lang_send_group'		=> lang('Send broadcast SMS'),
					'lang_send_group_statustext'	=> lang('send group'),
					'send_group_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.send_group', 'from'=>'index')),
				);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'						=> $links,
				'allow_allrows'					=> True,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($sms_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_inbox'				=> $table_header,
				'table_add'					=> $table_add,
				'values_inbox'					=> $content
			);

			$appname	= lang('inbox');
			$function_msg	= lang('list inbox');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_inbox' => $data));
			$this->save_sessiondata();
		}


		function outbox()
		{
			$acl_location = '.outbox';

			$GLOBALS['phpgw']->xslttpl->add_file(array('sms','nextmatchs','menu',
										'search_field'));

			$this->bo->acl_location = $acl_location;
			$this->menu->sub = $acl_location;
			$links = $this->menu->links();

			if(!$this->acl->check($acl_location, PHPGW_ACL_READ))
			{
				$this->bocommon->no_access($links);
				return;
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','sms_send_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','sms_send_receipt','');

			$sms_info = $this->bo->read_outbox();

			while (is_array($sms_info) && list(,$entry) = each($sms_info))
			{
				if($this->bocommon->check_perms($entry['grants'], PHPGW_ACL_DELETE))
				{
					$link_delete		= $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.delete_out', 'id'=> $entry['id']));
					$text_delete		= lang('delete');
					$lang_delete_sms_text = lang('delete the sms from outbox');
				}

				$content[] = array
				(
					'id'					=> $entry['id'],
					'receiver'				=> $entry['p_dst'],
					'user'					=> $entry['user'],
					'message'				=> $entry['message'],
					'dst_group'				=> $entry['dst_group'],
					'entry_time'				=> $entry['entry_time'],
					'status'				=> $entry['status'],
					'link_delete'				=> $link_delete,
					'text_delete'				=> $text_delete,
					'lang_delete_sms_text'			=> $lang_delete_sms_text,
				);

				unset ($link_delete);
				unset ($text_delete);
				unset ($lang_delete_sms_text);

			}

//_debug_array($content);

			$table_header[] = array
			(

				'sort_entry_time'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'p_datetime',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'sms.uisms.outbox',
														'query'		=> $this->query,
														'cat_id'	=> $this->cat_id,
														'allrows'	=> $this->allrows)
										)),
				'lang_delete'		=> lang('delete'),
				'lang_id'		=> lang('id'),
				'lang_user'		=> lang('user'),
				'lang_group'		=> lang('group'),
				'lang_entry_time'	=> lang('time'),
				'lang_status'		=> lang('status'),
				'lang_receiver'		=> lang('receiver'),
				'lang_message'		=> lang('message'),
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
				'menuaction'	=> 'sms.uisms.outbox',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'cat_id'	=> $this->cat_id,
				'filter'	=> $this->filter,
				'query'		=> $this->query
			);


			if($this->acl->check($acl_location, PHPGW_ACL_ADD))
			{
				$table_add[] = array
				(
					'lang_send'			=> lang('Send text SMS'),
					'lang_send_statustext'		=> lang('send single'),
					'send_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.send', 'from'=>'outbox')),
					'lang_send_group'		=> lang('Send broadcast SMS'),
					'lang_send_group_statustext'	=> lang('send group'),
					'send_group_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.send_group', 'from'=>'outbox')),
				);
			}


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'links'						=> $links,
				'allow_allrows'					=> True,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($sms_info),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_outbox'				=> $table_header,
				'table_add'					=> $table_add,
				'values_outbox'					=> $content
			);

			$appname	= lang('outbox');
			$function_msg	= lang('list outbox');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_outbox' => $data));
			$this->save_sessiondata();


		}

		function send()
		{
			$acl_location = '.outbox';
			if(!$this->acl->check($acl_location, PHPGW_ACL_ADD))
			{
				$this->bocommon->no_access($links);
				return;
			}

			$p_num		= phpgw::get_var('p_num');
			$values		= phpgw::get_var('values');
			$from		= phpgw::get_var('from');
			$from 		= $from?$from:'index';

			$GLOBALS['phpgw']->xslttpl->add_file(array('sms'));

			$max_length = 160;

			if (is_array($values))
			{
				$values['p_num_text']		= get_var('p_num_text',array('POST'));
				$values['message']		= phpgw::get_var('message');
				$values['msg_flash']		= phpgw::get_var('msg_flash', 'bool', 'POST');
				$values['msg_unicode']		= phpgw::get_var('msg_unicode', 'bool', 'POST');

				$p_num 		= $values['p_num_text']?$values['p_num_text']:$p_num;
				
				if ($values['save'] || $values['apply'])
				{

					if(!$values['message'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a message !'));
					}
					if(!$values['p_num_text'])
					{
						$receipt['error'][]=array('msg'=>lang('Please enter a recipient !'));
					}

					if(!$receipt['error'])
					{
						$from = 'outbox';
						$receipt = $this->bo->send_sms($values);
						$sms_id = $receipt['sms_id'];

						if ($values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','sms_send_receipt',$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'sms.uisms.' . $from));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'sms.uisms.' . $from));
				}
			}


			if ($sms_id)
			{
				if(!$receipt['error'])
				{
					$values = $this->bo->read_single($sms_id);
				}
				$function_msg = lang('edit place');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add place');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'sms.uisms.send',
				'sms_id'	=> $sms_id,
				'from'		=> $from
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$GLOBALS['phpgw_info']['flags']['java_script'] .= "\n"
				. '<script language="JavaScript">' ."\n"
				. 'function SmsCountKeyUp(maxChar)' ."\n"
				. '{' ."\n"
				. '    var msg  = document.forms.fm_sendsms.message;' ."\n"
				. '    var left = document.forms.fm_sendsms.charNumberLeftOutput;' ."\n"
				. '    var smsLenLeft = maxChar  - msg.value.length;' ."\n"
				. '    if (smsLenLeft >= 0) ' ."\n"
				. '    {' ."\n"
				. '	left.value = smsLenLeft;' ."\n"
				. '    } ' ."\n"
				. '    else ' ."\n"
				. '    {' ."\n"
				. '	var msgMaxLen = maxChar;' ."\n"
				. '	left.value = 0;' ."\n"
				. '	msg.value = msg.value.substring(0, msgMaxLen);' ."\n"
				. '    }' ."\n"
				. '}' ."\n"
				. 'function SmsCountKeyDown(maxChar)' ."\n"
				. '{' ."\n"
				. '    var msg  = document.forms.fm_sendsms.message;' ."\n"
				. '    var left = document.forms.fm_sendsms.charNumberLeftOutput;' ."\n"
				. '    var smsLenLeft = maxChar  - msg.value.length;' ."\n"
				. '    if (smsLenLeft >= 0) ' ."\n"
				. '    {' ."\n"
				. '	left.value = smsLenLeft;' ."\n"
				. '    } ' ."\n"
				. '    else ' ."\n"
				. '    {' ."\n"
				. '	var msgMaxLen = maxChar;' ."\n"
				. '	left.value = 0; ' ."\n"
				. '	msg.value = msg.value.substring(0, msgMaxLen);' ."\n"
				. '    }' ."\n"
				. '}' ."\n"
				. "</script>\n";



			$data = array
			(

				'lang_to'			=> lang('to'),
				'lang_from'			=> lang('from'),
				
				'value_sms_from'		=> $this->gateway_number,
				'value_p_num'			=> $p_num,
				'lang_format'			=> lang('International format'),
				'lang_message'			=> lang('message'),
				'lang_character_left'		=> lang('character left'),				
				
				'lang_send_as_flash'		=> lang('send as flash message'),
				'lang_send_as_unicode'		=> lang('send as unicode'),

				'value_max_length'		=> $max_length,

				'msgbox_data'			=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_save'			=> lang('save'),
				'lang_cancel'			=> lang('cancel'),

				'lang_done_status_text'		=> lang('Back to the list'),
				'lang_save_status_text'		=> lang('Save the training'),
				'lang_apply'			=> lang('apply'),
				'lang_apply_status_text'	=> lang('Apply the values'),
			);

			$appname	= lang('send sms');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('send' => $data));
		}


		function delete_in()
		{
			$acl_location = '.inbox';
			if(!$this->acl->check($acl_location, PHPGW_ACL_DELETE))
			{
				$this->bocommon->no_access($links);
				return;
			}

			$id		= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'sms.uisms.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_in($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.delete_in', 'id'=> $id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname				= lang('outbox');
			$function_msg				= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}


		function delete_out()
		{
			$acl_location = '.outbox';
			if(!$this->acl->check($acl_location, PHPGW_ACL_DELETE))
			{
				$this->bocommon->no_access($links);
				return;
			}

			$id		= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'sms.uisms.outbox'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_out($id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'sms.uisms.delete_out', 'id'=> $id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_statustext'		=> lang('Delete the entry'),
				'lang_no_statustext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang('outbox');
			$function_msg	= lang('delete');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('sms') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
