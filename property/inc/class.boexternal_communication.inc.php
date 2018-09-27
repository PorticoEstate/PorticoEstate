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

	class property_boexternal_communication
	{

		var $so, $historylog, $config, $bocommon,$preview_html, $dateformat;

		public function __construct()
		{
			$this->so = createObject('property.soexternal_communication');
			$this->historylog = & $this->so->historylog;
			$this->bocommon = createObject('property.bocommon');
			$this->config = CreateObject('phpgwapi.config', 'property')->read();
			$this->preview_html = phpgw::get_var('preview_html', 'bool');
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

		}

		function read_additional_notes( $id = 0)
		{
			$additional_notes = array();

			$history_array = $this->so->get_messages($id);

			$i = 1;
			foreach ($history_array as $value)
			{
				if($value['sender_email_address'])
				{
					$value_user = $value['sender_email_address'];
				}
				else if($value['created_by'])
				{
					$value_user = $GLOBALS['phpgw']->accounts->get($value['created_by'])->__toString();
				}

				$additional_notes[] = array
					(
					'value_id' => $value['id'],
					'value_count' => $i,
					'value_date' => $GLOBALS['phpgw']->common->show_date($value['created_on']),
					'value_user' => $value_user,
					'value_note' => stripslashes($value['message']),
				);
				$i++;
			}
			return $additional_notes;
		}

		function get_fields()
		{
			return $this->so->get_fields();
		}

		function read_single( $id )
		{
			return $this->so->read_single($id);
		}

		function save( $values )
		{

			if (empty($values['id']))
			{
				$action = 'add';
			}
			else
			{
				$action = 'edit';
			}

			$criteria = array
				(
				'appname' => 'property',
				'location' => ".ticket.external_communication",
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && is_file($file) && !$entry['client_side'] && $entry['pre_commit'])
				{
					require_once $file;
				}
			}

			if ($action == 'edit')
			{
				try
				{
					$receipt = $this->so->edit($values);
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}
			else
			{
				try
				{
					$receipt = $this->so->add($values);
					$values['id'] = $receipt['id'];
				}
				catch (Exception $e)
				{
					throw $e;
				}
			}

			reset($custom_functions);
			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require_once $file;
				}
			}

			return $receipt;

		}
		function read_record_history( $id )
		{
			$history_array = $this->historylog->return_array(array('C', 'O'), array(), '', '', $id);

			$record_history = array();
			$i = 0;

			foreach ($history_array as $value)
			{
				$record_history[$i]['value_date'] = $GLOBALS['phpgw']->common->show_date($value['datetime']);
				$record_history[$i]['value_user'] = $value['owner'];

				switch ($value['status'])
				{
					case 'S': $type = lang('Subject changed');
						break;
					case 'M':
						$type = lang('Sent by email to');
						$this->order_sent_adress = $value['new_value']; // in case we want to resend the order as an reminder
						break;
					default:
					// nothing
				}

				$record_history[$i]['value_action'] = $type ? $type : '';
				unset($type);
				if ($value['status'] != 'O' && $value['new_value'])
				{
					$record_history[$i]['value_new_value'] = $value['new_value'];
					$record_history[$i]['value_old_value'] = $value['old_value'];
				}
				else
				{
					$record_history[$i]['value_new_value'] = '';
				}

				$i++;
			}

			return $record_history;
		}

		function update_msg($id, $to, $attachment_log = '' )
		{
			$this->so->update_msg($id, $to, $attachment_log);
		}


		function alert_assigned( $id )
		{
			$message_info = $this->read_single($id);

			$ticket_id = $message_info['ticket_id'];
			$botts = createObject('property.botts');
			$ticket = $botts->read_single($ticket_id);

			$contact_data = $this->get_contact_data($ticket);

			$_to = $contact_data['user_email'];
			$from_email = 'ikkesvar@bergen.kommune.no';
			$from_name = $GLOBALS['phpgw_info']['server']['site_title'];

			$cc = '';
			$bcc = '';

			$html_content = $this->get_html_content($message_info, $contact_data);

			$subject = $html_content['subject'];

			$html = str_replace('__ATTACHMENTS__', '', $html_content['html']);

			if (!$_to)
			{
				return false;
			}

			$config_admin = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'))->read();

			if(!empty($config_admin['xPortico']['sender_email_address']))
			{
				$from_email = $config_admin['xPortico']['sender_email_address'];
			}

			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			try
			{
				$GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($html), '', $cc, $bcc, $from_email, $from_name, 'html', '', false);
			}
			catch (Exception $exc)
			{
			}

		}

		function get_html_content( $message_info, $contact_data )
		{
			$preview = $this->preview_html;
			$id = $message_info['id'];

			$ticket_id = $message_info['ticket_id'];
			$botts = createObject('property.botts');

			$ticket = $botts->read_single($ticket_id);

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

			$additional_notes = $this->read_additional_notes($id);

			$i = 0;
			foreach ($additional_notes as $value)
			{
				$value_note = nl2br($value['value_note']);
				$table_content .= "<tr><td>{$value['value_count']}</td><td>{$value['value_date']}</td><td>{$value['value_user']}</td><td>{$value_note}</td></tr>\n";
				$i++;
			}

			$body.= "<table class='details'>{$table_content}</table>\n";

			$subject = "[PorticoTicket::{$ticket_id}::{$id}] {$location_code} {$message_info['subject']}({$i})";

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

			if (!empty($this->config['contact_at_location']))
			{
				$contact_at_location = $this->config['contact_at_location'];

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
