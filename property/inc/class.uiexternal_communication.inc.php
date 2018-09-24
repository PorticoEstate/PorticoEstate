<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage helpdesk
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	class property_uiexternal_communication extends phpgwapi_uicommon
	{

		var $public_functions = array
		(
			'index'			=> true,
			'query'			=> true,
			'view'			=> true,
			'add' 			=> true,
			'edit'			=> true,
			'save'			=> true,
			'delete'		=> true,
		);

		var $acl, $historylog;
		private $acl_location, $acl_read, $acl_add, $acl_edit, $acl_delete,
			$bo, $botts, $bocommon, $config, $dateformat, $preview_html;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('external communication');
			self::set_active_menu("property::helpdesk");
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.ticket';
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->bo = createObject('property.boexternal_communication');
			$this->botts = createObject('property.botts');
			$this->bocommon = createObject('property.bocommon');
			$this->config = CreateObject('phpgwapi.config', 'property')->read();
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->historylog = & $this->bo->historylog;
			$this->preview_html = phpgw::get_var('preview_html', 'bool');

		}

		public function add()
		{
			if(!$this->acl_add)
			{
				phpgw::no_access();
			}

			$this->edit();
		}

		public function view()
		{
			if(!$this->acl_read)
			{
				phpgw::no_access();
			}

			/**
			 * Do not allow save / send here
			 */
			if(phpgw::get_var('save', 'bool') || phpgw::get_var('send', 'bool'))
			{
				phpgw::no_access();
			}
			$this->edit(array(), 'view');
		}


		public function edit( $values = array(), $mode = 'edit' , $error = false)
		{
			if(!$this->acl_add || !$this->acl_edit)
			{
				phpgw::no_access();
			}

			if(!$error && (phpgw::get_var('save', 'bool') || phpgw::get_var('send', 'bool')))
			{
				$this->save();
			}

			$id = phpgw::get_var('id', 'int');
			if( $this->preview_html)
			{
				$this->_send($id);
			}

			$ticket_id = phpgw::get_var('ticket_id', 'int');

			if(!$error && $id)
			{
				$values = $this->bo->read_single($id);
				$ticket_id = $values['ticket_id'];
			}

			$additional_message_notes = $this->bo->read_additional_notes($id);

			$message_note_def = array(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_user', 'label' => lang('who'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_note', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);

			foreach ($additional_message_notes as &$message_note)
			{
				$message_note['value_note'] = preg_replace("/[[:alpha:]]+:\/\/[^<>[:space:]]+[[:alnum:]\/]/","<a href=\"\\0\">\\0</a>", $message_note['value_note']);
				$message_note['value_note'] = nl2br($message_note['value_note']);
			}

			$ticket = $this->botts->read_single($ticket_id);
			$additional_notes = $this->botts->read_additional_notes($ticket_id);

			$notes = array(
				array(
					'value_id' => '', //not from historytable
					'value_count' => 1,
					'value_date' => $GLOBALS['phpgw']->common->show_date($ticket['timestamp']),
					'value_user' => $ticket['user_name'],
					'value_note' => $ticket['details'],
				)
			);

			$additional_notes = array_merge($notes, $additional_notes);

	//		_debug_array($additional_notes);die();

			$note_def = array(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_user', 'label' => lang('who'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_note', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);

			foreach ($additional_notes as &$note)
			{
				$note['value_note'] = preg_replace("/[[:alpha:]]+:\/\/[^<>[:space:]]+[[:alnum:]\/]/","<a href=\"\\0\">\\0</a>", $note['value_note']);
				$note['value_note'] = nl2br($note['value_note']);
			}


			$datatable_def = array();
			$custom_code = <<<JS

				var message = '';
				var space = '';
				var api = oTable0.api();
				var selected = api.rows( { selected: true } ).data();

				var numSelected = 	selected.length;

				if (numSelected ==0)
				{
					alert('None selected');
					return false;
				}
				for ( var n = 0; n < selected.length; ++n )
				{
					var aData = selected[n];

					if($("#communication_message").val())
					{
						space =' ';
					}
					else
					{
						space = '';
					}
					message = $("#communication_message").val() + space + aData['value_note'];
					message = message.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
					message = message.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
					$("#communication_message").val(message);

				}
JS;
			$tabletools[] = array
			(
				'my_name' => 'edit',
				'text' => lang('copy'),
				'type' => 'custom',
				'custom_code' => $custom_code
			);


			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $note_def,
				'data' => json_encode($additional_notes),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(0,'asc'))),
				)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_3',
				'requestUrl' => "''",
				'ColumnDefs' => $message_note_def,
				'data' => json_encode($additional_message_notes),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(0,'asc'))),
				)
			);

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id' => $ticket['vendor_id'],
				'vendor_name' => $ticket['vendor_name'],
				'type' => 'form'
			));

			$contact_data = $this->get_contact_data($ticket);

			$content_email = $this->bocommon->get_vendor_email(isset($ticket['vendor_id']) ? $ticket['vendor_id'] : 0, 'mail_recipients');

			if (isset($values['mail_recipients']) && is_array($values['mail_recipients']))
			{
				$_recipients_found = array();
				foreach ($content_email as &$vendor_email)
				{
					if (in_array($vendor_email['value_email'], $values['mail_recipients']))
					{
						$vendor_email['value_select'] = str_replace("type='checkbox'", "type='checkbox' checked='checked'", $vendor_email['value_select']);
						$_recipients_found[] = $vendor_email['value_email'];
					}
				}
				$value_extra_mail_address = implode(', ', array_diff($values['mail_recipients'], $_recipients_found));
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key' => 'value_email', 'label' => lang('email'),
						'sortable' => true, 'resizeable' => true),
					array('key' => 'value_select', 'label' => lang('select'), 'sortable' => false,
						'resizeable' => true)),
				'data' => json_encode($content_email),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitts.view_file'));

			$file_attachments = isset($values['file_attachments']) && is_array($values['file_attachments']) ? $values['file_attachments'] : array();

			$content_files = array();
			$lang_view_file = lang('click to view file');
			$lang_attach_file = lang('Check to attach file') ;

			foreach ($ticket['files'] as $_entry)
			{
				$_checked = '';
				if (in_array($_entry['file_id'], $file_attachments))
				{
					$_checked = 'checked="checked"';
				}

				$content_files[] = array(
					'file_name' => "<a href=\"{$link_view_file}&amp;file_id={$_entry['file_id']}\" target=\"_blank\" title=\"{$lang_view_file}\">{$_entry['name']}</a>",
					'attach_file' => "<input type=\"checkbox\" {$_checked} class=\"mychecks\" name=\"file_attachments[]\" value=\"{$_entry['file_id']}\" title=\"$lang_attach_file\">"
				);
			}

			$attach_file_def = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'attach_file', 'label' => lang('attach file'),
					'sortable' => false, 'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter')
			);

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);
			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'ColumnDefs' => $attach_file_def,
				'data' => json_encode($content_files),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$record_history = $this->bo->read_record_history($id);
			$z = 1;
			foreach ($record_history as &$history_entry)
			{
				$history_entry['sort_key'] = $z++;

			}
			$datatable_def[] = array
				(
				'container' => 'datatable-container_4',
				'requestUrl' => "''",
				'ColumnDefs' => array(
					array('key' => 'sort_key', 'label' => '#', 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => false,
						'resizeable' => true),
					array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
					array('key' => 'value_action', 'label' => lang('Action'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_old_value', 'label' => lang('old value'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_new_value', 'label' => lang('New value'), 'sortable' => true,
						'resizeable' => true)),
				'data' => json_encode($record_history),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$tabs = array();
			$tabs['main'] = array(
				'label' => lang('message'),
				'link' => '#main'
			);
			$tabs['history'] = array(
				'label' => lang('history'),
				'link' => '#history'
			);

			$type_list = array();
			$type_list =  execMethod('property.bogeneric.get_list', array('type' => 'external_com_type',
				'selected' => (int) $values['type_id']));

			if(count($type_list) > 1)
			{
				array_unshift($type_list, array('id' => '', 'name' => lang('select')));
			}


			$data = array(
				'type_list' => array('options' => $type_list),
				'datatable_def' => $datatable_def,
				'form_action' => self::link(array('menuaction' => "property.uiexternal_communication.{$mode}", 'id' => $id)),
				'edit_action' => self::link(array('menuaction' => "property.uiexternal_communication.edit", 'id' => $id)),
				'cancel_url' => self::link(array('menuaction' => "property.uitts.view", 'id' => $ticket_id)),
				'value_ticket_id' => $ticket_id,
				'value_ticket_subject' => $ticket['subject'],
				'value_subject'		=> !empty($values['subject']) ? $values['subject'] : $ticket['subject'],
				'value_extra_mail_address'=> $value_extra_mail_address,
				'value_id'	=> $id,
				'vendor_data' => $vendor_data,
				'contact_data'	=> $contact_data,
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab' => 0,
				'base_java_url' => "{menuaction:'property.uitts.update_data',id:{$ticket_id}}",
			);
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang($mode);

			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('property', 'portico', 'external_communication.edit.js');
			self::render_template_xsl(array('external_communication', 'datatable_inline'), array('edit' => $data));

		}


		public function save( )
		{
			$id = phpgw::get_var('id', 'int');

			/*
			 * Overrides with incoming data from POST
			 */
			$values = $this->_populate($id);


			if ($this->receipt['error'])
			{
				self::message_set($this->receipt);
				$this->edit($values, 'edit', $error = true);
			}
			else
			{
				try
				{
					$receipt = $this->bo->save($values);
					$id = $receipt['id'];
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit($values, 'edit', $error = true);
						return;
					}
				}

				$this->receipt['message'][] = array('msg' => lang('message has been saved'));

				self::message_set($this->receipt);
				if( phpgw::get_var('send', 'bool'))
				{
					$this->_send($id);
				}
				else
				{
					self::redirect(array('menuaction' => 'property.uiexternal_communication.edit',
						'id' => $id,
						'ticket_id' => $values['ticket_id'],
						'type_id' => $values['type_id']));
				}
			}

		}

		private function _send($id )
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$message_info = $this->bo->read_single($id);

			$ticket_id = $message_info['ticket_id'];
			$ticket = $this->botts->read_single($ticket_id);
			$recipients = $message_info['mail_recipients'];

			$contact_data = $this->get_contact_data($ticket);

			$coordinator_email = $contact_data['user_email'];
			$coordinator_name = $contact_data['user_name'];

			$cc = '';
			$bcc = '';
			$cc_arr = array();
			if($contact_data['contact_email'])
			{
				$cc_arr[] = $contact_data['contact_email'];
			}
			if($contact_data['contact_email2'])
			{
				$cc_arr[] = $contact_data['contact_email2'];
			}

			if($cc_arr)
			{
				$cc = implode(';', $cc_arr);
			}

			$html_content = $this->_get_html_content($message_info, $contact_data);

			$html = $html_content['html'];
			$subject = $html_content['subject'];

			$preview = $this->preview_html;

			$attachment_log = '';
			$attachment_text = '';
			if (!empty($message_info['file_attachments']) && is_array($message_info['file_attachments']))
			{
				$attachments = CreateObject('property.bofiles')->get_attachments($message_info['file_attachments']);
				$_attachment_log = array();
				foreach ($attachments as $_attachment)
				{
					$_attachment_log[] = $_attachment['name'];
				}
				$attachment_log = implode(', ', $_attachment_log);
			}

			$lang_attachments = lang('attachments');

			if($attachment_log)
			{
				$attachment_html_row = "<tr><td>$lang_attachments</td><td>:&nbsp;$attachment_log</td></tr>";
				$attachment_text = " {$lang_attachments} : {$attachment_log}";
			}
			else
			{
				$attachment_html_row = '';
			}

			$html = str_replace('__ATTACHMENTS__', $attachment_html_row, $html);

			if ($preview)
			{

				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
				echo $html;
				$GLOBALS['phpgw']->common->phpgw_exit();
			}


			if (empty($GLOBALS['phpgw_info']['server']['smtp_server']))
			{
				phpgwapi_cache::message_set(lang('SMTP server is not set! (admin section)'),'error' );
			}
			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$_to = !empty($recipients[0]) ? implode(';', $recipients) : '';

			if (!$_to)
			{
				phpgwapi_cache::message_set(lang('missing recipient for order %1', $ticket['order_id']),'error' );
				return false;
			}

			$historylog_ticket = CreateObject('property.historylog', 'tts');
			$config_admin = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'))->read();

			if(!empty($config_admin['xPortico']['sender_email_address']))
			{
				$coordinator_email = $config_admin['xPortico']['sender_email_address'];
			}

			try
			{
				$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($html), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html', '', $attachments, true);
				phpgwapi_cache::message_set(lang('%1 is notified', $_to),'message' );
				
				$lang_external = lang('external communication');

				$historylog_ticket->add('M', $ticket_id, "($lang_external) {$_to}{$attachment_text}");
				$this->historylog->add('M', $id, "{$_to}{$attachment_text}");
				$this->bo->update_msg($id, $_to, $attachment_log);
			}
			catch (Exception $exc)
			{
				phpgwapi_cache::message_set($exc->getMessage(),'error' );
			}
		}

		private function _get_html_content( $message_info, $contact_data )
		{
			$preview = $this->preview_html;
			$id = $message_info['id'];

			$ticket_id = $message_info['ticket_id'];
			$ticket = $this->botts->read_single($ticket_id);

			$lang_print = lang('print');

			$body = '';
			if($preview)
			{
				$body = <<<HTML

				<script type="text/javascript">
						document.write("<form><input type=button "
						+"value=\"{$lang_print}\" onClick=\"window.print();\"></form>");
				</script>

HTML;
			}

			$body .= <<<HTML
				<br/>
				======== Ved svar: Svar over denne linjen og behold saksnummer i emnefelt. ========
				<H2>__SUBJECT__</H2>

HTML;
			if(!$preview)
			{
				$body .= '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiexternal_communication.view',
					'id' => $id), false, true) . '">' . lang('Ticket') . " # {$ticket_id}::{$id} </a>" . "\n";
			}

			$entry_date = $GLOBALS['phpgw']->common->show_date($message_info['created_on'], $this->dateformat);
			$body .= "<table>\n";
			if($preview)
			{
				$value_recipients = implode('<br/>:&nbsp;', $message_info['mail_recipients']);

				$body .= '<tr><td valign = "top">'. lang('to')."</td><td>:&nbsp;{$value_recipients}</td></tr>\n";
			}

			$body .= '<tr><td>'. lang('Date Opened').'</td><td>:&nbsp;'.$entry_date."</td></tr>\n";
			$body .= '<tr><td>'. lang('Location') . '</td><td>:&nbsp;' . $ticket['location_code'] ."</td></tr>\n";
			$body .= '<tr><td>'. lang('Address') . '</td><td>:&nbsp;' . $ticket['address'] ."</td></tr>\n";
			if (isset($address_element) AND is_array($address_element))
			{
				foreach ($address_element as $address_entry)
				{
					$body .= '<tr><td>'. $address_entry['text'] . '</td><td>:&nbsp;' . $address_entry['value'] ."</td></tr>\n";
				}
			}

			$body .= '<tr><td>'. lang('from') . "</td><td>:&nbsp;{$contact_data['user_name']}</td></tr>\n";
			$body .= "<tr><td></td><td>:&nbsp;{$contact_data['user_email']}</td></tr>\n";
			$body .= "<tr><td></td><td>:&nbsp;{$contact_data['user_phone']}</td></tr>\n";

			if($contact_data['organisation'])
			{
				$body .= "<tr><td></td><td>:&nbsp;{$contact_data['organisation']}</td></tr>\n";
			}
			if($contact_data['department'])
			{
				$body .= "<tr><td></td><td>:&nbsp;{$contact_data['department']}</td></tr>\n";
			}
			if($contact_data['contact_name'])
			{
				$body .= '<tr><td>'. lang('contact') . " 1</td><td>:&nbsp;{$contact_data['contact_name']}</td></tr>\n";
			}
			if($contact_data['contact_email'])
			{
				$body .= "<tr><td></td><td>:&nbsp;{$contact_data['contact_email']}</td></tr>\n";
			}
			if($contact_data['contact_phone'])
			{
				$body .= "<tr><td></td><td>:&nbsp;{$contact_data['contact_phone']}</td></tr>\n";
			}
			if($contact_data['contact_name2'])
			{
				$body .= '<tr><td>'. lang('contact') . " 2</td><td>:&nbsp;{$contact_data['contact_name2']}</td></tr>\n";
			}
			if($contact_data['contact_email2'])
			{
				$body .= "<tr><td></td><td>:&nbsp;{$contact_data['contact_email2']}</td></tr>\n";
			}
			if($contact_data['contact_phone2'])
			{
				$body .= "<tr><td></td><td>:&nbsp;{$contact_data['contact_phone2']}</td></tr>\n";
			}
			if($ticket['assignedto'])
			{
				$body .= '<tr><td>'. lang('Assigned To').'</td><td>:&nbsp;'.$GLOBALS['phpgw']->accounts->id2name($ticket['assignedto'])."</td></tr>\n";
			}

			$body .= "__ATTACHMENTS__\n</table><br/><br/>\n";

			$lang_date = lang('date');
			$lang_who = lang('who');
			$lang_note = lang('note');
			$table_content = <<<HTML
			<thead>
				<tr>
					<th>
						#
					</th>
					<th>
						{$lang_date}
					</th>
					<th>
						{$lang_who}
					</th>
					<th>
						{$lang_note}
					</th>
				</tr>
			</thead>
HTML;

			$additional_notes = $this->bo->read_additional_notes($id);

			$i = 0;
			foreach ($additional_notes as $value)
			{
				$value_note = nl2br($value['value_note']);
				$table_content .= "<tr><td>{$value['value_count']}</td><td>{$value['value_date']}</td><td>{$value['value_user']}</td><td>{$value_note}</td></tr>\n";
				$i++;
			}

			$body.= "<table class='details'>{$table_content}</table>\n";

			$subject = "[PorticoTicket #{$ticket_id}::{$id}] : {$location_code}  {$message_info['subject']}({$i})";

			$body = str_replace('__SUBJECT__', $subject, $body);


			$html = <<<HTML
<!DOCTYPE html>
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<title>{$subject}</title>
					<style>

						html {
							font-family: arial;
							}

						.details {
						  width: 100%;
						  border: 1px solid black;
						  border-collapse: collapse;
						}
						.details th {
						  background: darkblue;
						  color: white;
						}
						.details td,
						.details th {
						  border: 1px solid black;
						  text-align: left;
						  padding: 5px 10px;
						}
						.details tr:nth-child(even) {
						  background: lightblue;
						}


						@page {
						size: A4;
						}

						#order_deadline{
							width: 800px;
							border:0px solid transparent;
						}

						#order_deadline td{
							border:0px solid transparent;
						}
						@media print {
						li {page-break-inside: avoid;}
						h1, h2, h3, h4, h5 {
						page-break-after: avoid;
						}

						table, figure {
						page-break-inside: avoid;
						}
						}


						@page:left{
						@bottom-left {
						content: "Page " counter(page) " of " counter(pages);
						}
						}
						@media print
						{
							.btn
							{
								display: none !important;
							}
						}

						.btn{
						background: none repeat scroll 0 0 #2647A0;
						color: #FFFFFF;
						display: inline-block;
						margin-right: 5px;
						padding: 5px 10px;
						text-decoration: none;
						border: 1px solid #173073;
						cursor: pointer;
						}

						ul{
						list-style: none outside none;
						}

						li{
						list-style: none outside none;
						}

						li.list_item ol li{
						list-style: decimal;
						}

						ul.groups li {
						padding: 3px 0;
						}

						ul.groups li.odd{
						background: none repeat scroll 0 0 #DBE7F5;
						}

						ul.groups h3 {
						font-size: 18px;
						margin: 0 0 5px;
						}

					</style>
				</head>
				<body>
					<div style='width: 800px;'>
						{$body}
					</div>
				</body>
			</html>
HTML;


			return array
			(
				'html' => $html,
				'subject' => $subject,
				'contact_data' => $contact_data,
			);
		}

		private function _populate($id = false)
		{
			$fields = $this->bo->get_fields();

			$values = array();

			foreach ($fields as $field	=> $field_info)
			{
				if(($field_info['action'] & PHPGW_ACL_ADD) ||  ($field_info['action'] & PHPGW_ACL_EDIT))
				{
					$value = phpgw::get_var($field, $field_info['type']);

					if ($field_info['required'] && (($value !== '0' && empty($value)) || empty($value)))
					{
						$this->receipt['error'][] = array('msg' => lang("Field %1 is required", lang($field_info['label'])));
					}

					$values[$field] = $value;
				}
			}

			$values['id'] = $id;

			return $values;
		}


		public function get_contact_data( $ticket )
		{
			$organisation = '';
			$contact_name = '';
			$contact_email = '';
			$contact_phone = '';
			$contact_name2  = '';
			$contact_email2 = '';

			if (isset($this->config['org_name']))
			{
				$organisation = $this->config['org_name'];
			}
			if (isset($this->config['department']))
			{
				$department = $this->config['department'];
			}

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $ticket['contact_id'],
				'field' => 'contact',
				'type' => 'form'));

			if (isset($contact_data['value_contact_name']) && $contact_data['value_contact_name'])
			{
				$contact_name = $contact_data['value_contact_name'];
			}
			if (isset($contact_data['value_contact_email']) && $contact_data['value_contact_email'])
			{
				$contact_email = $contact_data['value_contact_email'];
			}
			if (isset($contact_data['value_contact_tel']) && $contact_data['value_contact_tel'])
			{
				$contact_phone = $contact_data['value_contact_tel'];
			}

			$order_id = $ticket['order_id'];
	//account_display
			$user_phone = $GLOBALS['phpgw_info']['user']['preferences']['property']['cellphone'];
			$user_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
			$order_email_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_email_template'];
			$order_contact_block_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_contact_block_1'];

			if (!empty($this->bo->config->config_data['contact_at_location']))
			{
				$contact_at_location = $this->bo->config->config_data['contact_at_location'];

				$_responsible = execMethod('property.boresponsible.get_responsible', array('location'=> explode('-', $ticket['location_code']),
					'cat_id' => $ticket['cat_id'],
					'role_id' => $contact_at_location)
					);

				if($_responsible)
				{
					$prefs					= $this->bocommon->create_preferences('property', $_responsible);
					$GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'] = 'firstname';
					$_responsible_name		= $GLOBALS['phpgw']->accounts->get($_responsible)->__toString();
					$_responsible_email		= $prefs['email'];
					$_responsible_cellphone	= $prefs['cellphone'];
					if($contact_email  && ($contact_data['value_contact_email'] != $_responsible_email))
					{
						$contact_name2 = $_responsible_name;
						$contact_email2 = $_responsible_email;
						$contact_phone2 = $_responsible_cellphone;
						$order_contact_block_template = $GLOBALS['phpgw_info']['user']['preferences']['property']['order_contact_block_2'];
					}
					else
					{
						$contact_name = $_responsible_name;
						$contact_email = $_responsible_email;
						$contact_phone = $_responsible_cellphone;
					}
				}
			}

			$user_phone = str_replace(' ', '', $user_phone);
			$contact_phone = str_replace(' ', '', $contact_phone);
			$contact_phone2 = str_replace(' ', '', $contact_phone2);

			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $user_phone,  $matches ) )
			{
				$user_phone = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}
			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $contact_phone,  $matches ) )
			{
				$contact_phone = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}
			if(  preg_match( '/^(\d{2})(\d{2})(\d{2})(\d{2})$/', $contact_phone2,  $matches ) )
			{
				$contact_phone2 = "{$matches[1]} $matches[2] $matches[3] $matches[4]";
			}

			return array(
				'organisation' => $organisation,
				'department'=> $department,
				'user_name' => $GLOBALS['phpgw_info']['user']['fullname'],
				'user_email' => $user_email,
				'user_phone' => $user_phone,
				'contact_name' => $contact_name,
				'contact_email' => $contact_email,
				'contact_phone' => $contact_phone,
				'contact_name2' => $contact_name2,
				'contact_email2' => $contact_email2,
				'contact_phone2' => $contact_phone2
			);

		}


	}