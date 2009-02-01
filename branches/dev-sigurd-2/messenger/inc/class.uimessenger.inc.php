<?php
	/**************************************************************************\
	* phpGroupWare - Messenger                                                 *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	class uimessenger
	{
		var $bo;
		var $template;
		var $public_functions = array
		(
			'inbox'          => true,
			'compose'        => true,
			'compose_global' => true,
			'read_message'   => true,
			'reply'          => true,
			'forward'        => true,
			'delete'         => true
		);

		function uimessenger()
		{
			$this->template   = $GLOBALS['phpgw']->template;
			$this->bo         = CreateObject('messenger.bomessenger');
			$this->nextmatchs = createobject('phpgwapi.nextmatchs');
			if ( !$this->bo->is_connected() )
			{
				$this->_error_not_connected();
			}
		}

		function compose($errors = '')
		{
			$message = isset($_POST['message']) ? $_POST['message'] : array('subject' => '', 'content' => '');

			$this->_display_headers();
			$this->_set_compose_read_blocks();

			if (is_array($errors))
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->_set_common_langs();
			$this->template->set_var('header_message',lang('Compose message'));

			$users = $this->bo->get_available_users();
			foreach ( $users as $uid => $name )
			{
				$this->template->set_var(array
				(
					'uid'		=> $uid,
					'full_name'	=> $name
				));
				$this->template->parse('select_tos', 'select_to', true);
			}

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.send_message') ) );
			//$this->template->set_var('value_to','<input name="message[to]" value="' . $message['to'] . '" size="30">');
			$this->template->set_var('value_subject','<input name="message[subject]" value="' . $message['subject'] . '" size="30">');
			$this->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' . $message['content'] . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('to','form_to');
			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function compose_global($errors = '')
		{
			global $message;

			if (! $GLOBALS['phpgw']->acl->check('run',1,'admin'))
			{
				$this->inbox();
				return false;
			}

			$this->_display_headers();
			$this->_set_compose_read_blocks();

			if (is_array($errors))
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->_set_common_langs();
			$this->template->set_var('header_message',lang('Compose global message'));

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.send_global_message') ) );
			$this->template->set_var('value_subject','<input name="message[subject]" value="' . $message['subject'] . '">');
			$this->template->set_var('value_content','<textarea name="message[content]" rows="20" wrap="hard" cols="76">' . $message['content'] . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function delete()
		{
			$messages = $_REQUEST['messages'];
			$this->bo->delete_message($messages);

			$this->inbox();
		}

		function inbox()
		{
			$start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
			$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
			$sort  = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : '';
			$total = $this->bo->total_messages();

			$extra_menuaction = '&menuaction=messenger.uimessenger.inbox';
			$extra_header_info['nextmatchs_left']  = $this->nextmatchs->left('/index.php',$start,$total,$extra_menuaction);
			$extra_header_info['nextmatchs_right'] = $this->nextmatchs->right('/index.php',$start,$total,$extra_menuaction);

			$this->_display_headers($extra_header_info);

			$this->template->set_file('_inbox','inbox.tpl');
			$this->template->set_block('_inbox', 'row', 'rows');
			$this->template->set_block('_inbox','list');
			$this->template->set_block('_inbox','row_empty');

			$this->_set_common_langs();
			$this->template->set_var('sort_date','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_date',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('Date') . '</a>');
			$this->template->set_var('sort_subject','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_subject',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('Subject') . '</a>');
			$this->template->set_var('sort_from','<a href="' . $this->nextmatchs->show_sort_order($sort,'message_from',$order,'/index.php','','&menuaction=messenger.uimessenger.inbox',False) . '" class="topsort">' . lang('From') . '</a>');

			$params = array(
				'start' => $start,
				'order' => $order,
				'sort'  => $sort
			);
			$messages = $this->bo->read_inbox($params);

			if (! is_array($messages))
			{
				$this->template->set_var('lang_empty',lang('You have no messages'));
				$this->template->fp('rows','row_empty',True);
			}
			else
			{
				$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.delete') ) );
				$this->template->set_var('button_delete','<input type="image" src="' . PHPGW_IMAGES . '/delete.gif" name="delete" title="' . lang('Delete selected') . '" border="0">');
				$i = 0;
				foreach ( $messages as $message)
				{
					$status = $message['status'];
					if ($message['status'] == 'N' || $message['status'] == 'O')
					{
						$status = '&nbsp;';
					}
	
					$this->template->set_var(array
					(
						'row_class'		=> $i % 2 ? 'row_on' : 'row_off',
						'row_date'		=> $message['date'],
						'row_from'		=> $message['from'],
						'row_msg_id'	=> $message['id'],
						'row_status'	=> $status,
						'row_subject'	=> $GLOBALS['phpgw']->strip_html($message['subject']),
						'row_url'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.read_message', 'message_id' => $message['id']))
					));
					$this->template->parse('rows', 'row', true);
					++$i;
				}


			}

			$this->template->pfp('out','list');
		}

		function read_message()
		{
			$message_id = $_REQUEST['message_id'];
			$message = $this->bo->read_message($message_id);

			$this->_display_headers();
			$this->_set_compose_read_blocks();
			$this->_set_common_langs();

			$this->template->set_var('header_message',lang('Read message'));

			$this->template->set_var('value_from', $message['from']);
			$this->template->set_var('value_subject', $GLOBALS['phpgw']->strip_html($message['subject']));
			$this->template->set_var('value_date', $message['date']);
			$this->template->set_var('value_content', nl2br(wordwrap($GLOBALS['phpgw']->strip_html($message['content']), 80)));

			$this->template->set_var('link_delete','<a href="'
					. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.delete', 'messages[]' => $message['id']) )
					. '">' . lang('Delete') . '</a>');

			$this->template->set_var('link_reply','<a href="'
					. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.reply', 'message_id' => $message['id']) )
					. '">' . lang('Reply') . '</a>');

			$this->template->set_var('link_forward','<a href="'
					. $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.forward', 'message_id' => $message['id']) )
					. '">' . lang('Forward') . '</a>');

			switch($message['status'])
			{
				case 'N': $this->template->set_var('value_status',lang('New'));		break;
				case 'R': $this->template->set_var('value_status',lang('Replied'));	break;
				case 'F': $this->template->set_var('value_status',lang('Forwarded'));	break;
			}

			if ( isset($message['global_message']) && $message['global_message'] )
			{
				$this->template->fp('read_buttons','form_read_buttons_for_global');
			}
			else
			{
				$this->template->fp('read_buttons','form_read_buttons');
			}

			$this->template->fp('date','form_date');
			$this->template->fp('from','form_from');
			$this->template->pfp('out','form');
		}

		function reply($errors = '', $message = '')
		{
			$message_id = $_REQUEST['message_id'];

			if(is_array($errors))
			{
				$errors  = $errors['errors'];
				$message = $errors['message'];
			}

			if (!$message)
			{
				$message = $this->bo->read_message_for_reply($message_id,'RE');
			}

			$this->_display_headers();
			$this->_set_compose_read_blocks();
			$this->_set_common_langs();
			$this->template->set_block('_form','form_reply_to');

			if (is_array($errors))
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('header_message',lang('Reply to a message'));

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.reply', 'message_id' => $message['id']) ) );
			$this->template->set_var('value_to',"<input type= 'hidden' name='n_message[to]' value={$message['from']}>{$message['from_fullname']}");
			$this->template->set_var('value_subject','<input name="n_message[subject]" value="' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['subject'])) . '" size="30">');
			$this->template->set_var('value_content','<textarea name="n_message[content]" rows="20" wrap="hard" cols="76">' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['content'])) . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('to','form_reply_to');
			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function forward($errors = '', $message = '')
		{
			$message_id = $_REQUEST['message_id'];

			if(is_array($errors))
			{
				$errors  = $errors['errors'];
				$message = $errors['message'];
			}

			if (!$message)
			{
				$message = $this->bo->read_message_for_reply($message_id,'FW');
			}

			$this->_display_headers();
			$this->_set_compose_read_blocks();
			$this->_set_common_langs();

			$users = $this->bo->get_available_users();
			foreach ( $users as $uid => $name )
			{
				$this->template->set_var(array
				(
					'uid'		=> $uid,
					'full_name'	=> $name
				));
				$this->template->parse('select_tos', 'select_to', true);
			}


			if (is_array($errors))
			{
				$this->template->set_var('errors',$GLOBALS['phpgw']->common->error_list($errors));
			}

			$this->template->set_var('header_message',lang('Forward a message'));

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.bomessenger.forward', 'message_id' => $message['id']) ) );
			$this->template->set_var('value_to','<input name="n_message[to]" value="' . $message['from'] . '" size="30">');
			$this->template->set_var('value_subject','<input name="n_message[subject]" value="' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['subject'])) . '" size="30">');
			$this->template->set_var('value_content','<textarea name="n_message[content]" rows="20" wrap="hard" cols="76">' .  $GLOBALS['phpgw']->strip_html(stripslashes($message['content'])) . '</textarea>');

			$this->template->set_var('button_send','<input type="submit" name="send" value="' . lang('Send') . '">');
			$this->template->set_var('button_cancel','<input type="submit" name="cancel" value="' . lang('Cancel') . '">');

			$this->template->fp('to','form_to');
			$this->template->fp('buttons','form_buttons');
			$this->template->pfp('out','form');
		}

		function _display_headers($extras = '')
		{
			$this->template->set_file('_header','header.tpl');
			$this->template->set_block('_header','global_header');
			$this->template->set_var('lang_inbox','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox') ) . '">' . lang('Inbox') . '</a>');
			$this->template->set_var('lang_compose','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.compose') ) . '">' . lang('Compose') . '</a>');

			if ( isset($extras['nextmatchs_left']) && $extras['nextmatchs_left'] )
			{
				$this->template->set_var('nextmatchs_left',$extras['nextmatchs_left']);
			}

			if ( isset($extras['nextmatchs_right']) && $extras['nextmatchs_right'] )
			{
				$this->template->set_var('nextmatchs_right',$extras['nextmatchs_right']);
			}

			$this->template->fp('app_header','global_header');

			$GLOBALS['phpgw']->common->phpgw_header(true);
		}

		function _error_not_connected()
		{
			$this->_display_headers();
			die( lang('exiting with error!') . "<br />\n" . lang('Unable to connect to server, please contact your system administrator') );
		}
		
		function _set_common_langs()
		{
			$this->template->set_var('lang_to',lang('Send message to'));
			$this->template->set_var('lang_from',lang('Message from'));
			$this->template->set_var('lang_subject',lang('Subject'));
			$this->template->set_var('lang_content',lang('Message'));
			$this->template->set_var('lang_date',lang('Date'));
		}

		function _set_compose_read_blocks()
		{
			$this->template->set_file('_form','form.tpl');

			$this->template->set_block('_form','form');
			$this->template->set_block('_form','select_to', 'select_tos');
			$this->template->set_block('_form','form_to');
			$this->template->set_block('_form','form_date');
			$this->template->set_block('_form','form_from');
			$this->template->set_block('_form','form_buttons');
			$this->template->set_block('_form','form_read_buttons');
			$this->template->set_block('_form','form_read_buttons_for_global');
		}
	}
