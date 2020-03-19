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

	class property_uiorder_template extends phpgwapi_uicommon
	{

		var $public_functions = array
			(
			'index'					 => true,
			'query'					 => true,
			'view'					 => true,
			'add'					 => true,
			'edit'					 => true,
			'save'					 => true,
			'delete'				 => true,
			'add_deviation'			 => true,
			'get_other_deviations'	 => true
		);
		var $acl, $historylog, $bo;
		private $acl_location, $acl_read, $acl_add, $acl_edit, $acl_delete,
			 $botts, $bocommon, $config, $dateformat, $preview_html, $account;

		public function __construct()
		{
			parent::__construct();

			switch ($this->currentapp)
			{
				case 'property':
					self::set_active_menu("property::helpdesk");
					break;
				case 'helpdesk':
					self::set_active_menu("helpdesk");
					break;
				default:
					throw new Exception("External communication for {$this->currentapp} is not supported");
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('quick order template');

			$this->account		 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.ticket';
			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->currentapp);
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->currentapp);
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->currentapp);
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->currentapp);
			$this->bo			 = createObject("{$this->currentapp}.boorder_template");
			$this->botts		 = createObject("{$this->currentapp}.botts");
			$this->bocommon		 = createObject('property.bocommon');
			$this->config		 = CreateObject('phpgwapi.config', $this->currentapp)->read();
			$this->dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$this->historylog	 = & $this->bo->historylog;
			$this->preview_html	 = $this->bo->preview_html;
		}


		function get_vendor_options( $selected = 0 )
		{
				$values = $this->bo->get_vendors();
				$default_value		 = array('cat_id' => '', 'name' => lang('select'));
				array_unshift($values, $default_value);

				$_categories = array();
				foreach ($values as & $value)
				{
					$value['selected'] = $value['id'] == $selected ? 1 : 0;
				}
				
				return $values;
		}


		public function index()
		{
			self::set_active_menu("property::helpdesk::quick_order_template");

			if (!$this->acl_read)
			{
				phpgw::no_access();
			}


			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query(true);
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('quick order template');
			
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->currentapp) ."::{$function_msg}";
			
			$_fields = $this->bo->get_fields();
			
			$fields = array();
			
			foreach ($_fields as $key => $_field)
			{
				if(!($_field['action'] & PHPGW_ACL_READ))
				{
					continue;
				}
				$_field['key'] = $key;
				$_field['label'] = lang($_field['label']);
				$fields[] = $_field;
			}
			
			

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'filter',
								'name' => 'filter_vendor_id',
								'text' => lang('vendor'),
								'list' =>  $this->get_vendor_options()
							),
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => "{$this->currentapp}.uiorder_template.index",
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => "{$this->currentapp}.uiorder_template.edit")),
					'editor_action' => '',
					'field' => $fields
				)
			);
//			_debug_array($this->bo->get_fields());
			$parameters = array(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

/*			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uicustomer.view"
				)),
				'parameters' => json_encode($parameters)
			);
*/
			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uiorder_template.edit"
				)),
				'parameters' => json_encode($parameters)
			);

//			self::add_javascript($this->currentapp, 'portico', 'customer.index.js');
//			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}
		
		public function edit($values = array(), $mode = 'edit', $error = false )
		{
			if (!$this->acl_add)
			{
				phpgw::no_access();
			}

	//		self::set_active_menu("property::helpdesk::deviation::add_deviation");

			/**
			 * Save first, then preview - first pass
			 */
			$init_preview = phpgw::get_var('init_preview', 'bool');

			if (!$error && (phpgw::get_var('save', 'bool') || phpgw::get_var('send', 'bool') || $init_preview))
			{
				$receipt = $this->save_ticket();
				if($init_preview)
				{
					self::redirect(array('menuaction'	 => "{$this->currentapp}.uiorder_template.edit",
						'id'			 => $receipt['id'],
						'ticket_id'		 => $receipt['ticket_id'],
						'type_id'		 => $receipt['type_id'],
						'init_preview2'	 => $init_preview)
					);
				}
				else if (phpgw::get_var('send', 'bool') && !empty($receipt['id']))
				{
					$this->_send($receipt['id']);
				}

				self::redirect(array('menuaction'	 => "{$this->currentapp}.uiorder_template.edit"));
			}

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id'		 => $receipt['vendor_id'],
				'vendor_name'	 => $receipt['vendor_name'],
				'type'			 => 'form'
			));

			$contact_data = $this->bo->get_contact_data($ticket);

			$content_email = $this->bocommon->get_vendor_email(isset($ticket['vendor_id']) ? $ticket['vendor_id'] : 0, 'mail_recipients');

			if (isset($values['mail_recipients']) && is_array($values['mail_recipients']))
			{
				$_recipients_found = array();
				foreach ($content_email as &$vendor_email)
				{
					if (in_array($vendor_email['value_email'], $values['mail_recipients']))
					{
						$vendor_email['value_select']	 = str_replace("type='checkbox'", "type='checkbox' checked='checked'", $vendor_email['value_select']);
						$_recipients_found[]			 = $vendor_email['value_email'];
					}
				}
				$value_extra_mail_address = implode(', ', array_diff($values['mail_recipients'], $_recipients_found));
			}

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_1',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key'		 => 'value_email', 'label'		 => lang('email'),
						'sortable'	 => true, 'resizeable' => true),
					array('key'		 => 'value_select', 'label'		 => lang('select'), 'sortable'	 => false,
						'resizeable' => true)),
				'data'		 => json_encode($content_email),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$other_orders_def	 = array
				(
				array('key' => 'url', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'location_code', 'label' => lang('location'), 'sortable' => true),
				array('key' => 'name', 'label' => lang('name'), 'sortable' => false),
				array('key' => 'start_date', 'label' => lang('start date'), 'sortable' => false),
				array('key' => 'coordinator', 'label' => lang('coordinator'), 'sortable' => true),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
				array('key' => 'select', 'label' => lang('select'), 'sortable' => false, 'formatter'	 => 'JqueryPortico.FormatterCenter'),
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_2',
				'requestUrl' => "''",
				'data'		 => json_encode(array()),
				'ColumnDefs' => $other_orders_def,
				'config'	 => array(
				//	array('disableFilter' => true),
				//	array('disablePagination' => true),
					array('singleSelect' => true),
				//	array('order' => json_encode(array(1, 'desc')))
				)
			);


			$other_deviations_def	 = array
				(
				array('key' => 'url', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'location_code', 'label' => lang('location'), 'sortable' => true),
				array('key' => 'subject', 'label' => lang('subject'), 'sortable' => false),
				array('key' => 'entry_date', 'label' => lang('entry date'), 'sortable' => false),
				array('key' => 'user', 'label' => lang('user'), 'sortable' => true),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
			);

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_3',
				'requestUrl' => "''",
				'data'		 => json_encode(array()),
				'ColumnDefs' => $other_deviations_def,
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('singleSelect' => true),
				//	array('order' => json_encode(array(0, 'desc')))
				)
			);

			$tabs			 = array();
			$tabs['main']	 = array(
				'label'	 => lang('deviation'),
				'link'	 => '#main'
			);

			$type_list	 = array();
			$type_list	 = execMethod("{$this->currentapp}.bogeneric.get_list", array('type'		 => 'external_com_type',
				'selected'	 => (int)$values['type_id']));

			if (count($type_list) > 1)
			{
				array_unshift($type_list, array('id' => '', 'name' => lang('select')));
			}
			$_filter_buildingpart	 = array();
			$filter_buildingpart	 = isset($this->config['filter_buildingpart']) ? $this->config['filter_buildingpart'] : array();

			if ($filter_key = array_search('.b_account', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

			$data = array(
				'type_list'					 => array('options' => $type_list),
				'datatable_def'				 => $datatable_def,
				'form_action'				 => self::link(array('menuaction' => "{$this->currentapp}.uiorder_template.edit")),
				'cancel_url'				 => self::link(array('menuaction' => "{$this->currentapp}.uitts.index")),
				'vendor_data'				 => $vendor_data,
				'contact_data'				 => $contact_data,
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab'			 => 0,
				'base_java_url'				 => "{menuaction:'{$this->currentapp}.uitts.update_data'}",
				'building_part_list'		 => array('options' => $this->bocommon->select_category_list(array(
						'type'		 => 'building_part',
						'selected'	 => $ticket['building_part'],
						'order'		 => 'id',
						'id_in_name' => 'num',
						'filter'	 => $_filter_buildingpart
					))),
				'order_dim1_list'				 => array('options' => $this->bocommon->select_category_list(array(
						'type'		 => 'order_dim1',
						'selected'	 => $ticket['order_dim1'],
						'order'		 => 'id',
						'id_in_name' => 'num'
					))),
				'tax_code_list'					 => array('options' => $this->bocommon->select_category_list(array(
						'type'		 => 'tax',
					'selected'	 => $ticket['tax_code'],
					'order'		 => 'id',
						'id_in_name' => 'num'
					))),
				'branch_list'			 => !empty($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']) ? array
					(
						'options' => execMethod('property.boproject.select_branch_list', $values['branch_id'])
					) : '',
			);
			$GLOBALS['phpgw_info']['flags']['app_header']	 .= '::' . lang('deviation');

//			self::rich_text_editor('order_descr');

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript($this->currentapp, 'portico', 'order_template.edit.js');
			self::render_template_xsl(array('order_template', 'datatable_inline'), array(
				'edit' => $data));

		}

		public function add()
		{
			if (!$this->acl_add)
			{
				phpgw::no_access();
			}

			$this->edit();
		}

		public function view()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			/**
			 * Do not allow save / send here
			 */
			if (phpgw::get_var('save', 'bool') || phpgw::get_var('send', 'bool') || phpgw::get_var('init_preview', 'bool'))
			{
				phpgw::no_access();
			}
			$this->edit(array(), 'view');
		}

		public function save_ticket()
		{
			$order_id = phpgw::get_var('order_id');
			$vendor_id = phpgw::get_var('vendor_id', 'int');
			$location_code = phpgw::get_var('location_code');
			$contract_id = phpgw::get_var('contract_id');
			$subject =  phpgw::get_var('subject');
			$message =  phpgw::get_var('message', 'html');
			$mail_recipients =  phpgw::get_var('mail_recipients');
			$type_id =  phpgw::get_var('type_id', 'int');

			$ticket = array
			(
				'assignedto'			 => $this->account,
				'group_id'				 => false,//$group_id,
				'location_code'			 => $location_code ? $location_code : '9999',
				'cat_id'				 => $this->config['tts_deviation_category'],//10102, //"avvik" $message_cat_id,
				'priority'				 => 3, //$priority, //valgfri (1-3)
				'title'					 => $subject,
				'details'				 => $message,
	//			'external_ticket_id'	 => $external_ticket_id,
	//			'external_origin_email'	 => $sender
			);

			$GLOBALS['phpgw']->db->transaction_begin();

			$ticket_id = CreateObject('property.botts')->add_ticket($ticket);

			$external_message_id = 0;

			try
			{
				$external_message = array(
					'type_id'			 => $type_id ? $type_id : 1,
					'ticket_id'			 => $ticket_id,
					'subject'			 => $subject,
					'message'			 => $message,
					'mail_recipients'	 => $mail_recipients,
					'vendor_id'			 => $vendor_id
				);

				$external_message_receipt =  CreateObject('property.soorder_template')->add($external_message);

				$location_id_ticket = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');

				if($order_id)
				{
					$interlink_data = array
					(
						'location1_id' => $location_id_ticket,
						'location1_item_id' => $ticket_id,
						'location2_id' => $GLOBALS['phpgw']->locations->get_id('property', '.project.workorder'),
						'location2_item_id' => $order_id,
						'account_id' => $this->account
					);
					execMethod('property.interlink.add', $interlink_data);
				}

			}
			catch (Exception $exc)
			{
				echo $exc->getTraceAsString();
			}

			if ($GLOBALS['phpgw']->db->transaction_commit())
			{
				phpgwapi_cache::message_set(lang('Saved'), 'message');
			}

			return array(
				'id'			 => $external_message_receipt['id'],
				'ticket_id'		 => $ticket_id,
				'type_id'		 => $type_id,
				'vendor_id'		 => $vendor_id
			);
		}

		public function save( $init_preview = null )
		{
			$id = phpgw::get_var('id', 'int');

			/*
			 * Overrides with incoming data from POST
			 */
			$values = $this->_populate($id);

			$_closed = false;

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
					$id		 = $receipt['id'];

					if(!$init_preview && $values['ticket_status'])
					{
						$new_status = $values['ticket_status'];
						$ticket_id 	= $values['ticket_id'];
						$receipt 	= $this->botts->update_status(array('status'=>$new_status),$ticket_id);
						self::message_set($receipt);

						$custom_status = $this->botts->get_custom_status();

						$_closed = $new_status == 'X' ? true : false;
						foreach ($custom_status as $entry)
						{
							if("C{$entry['id']}" == $new_status && $entry['closed'] == 1)
							{
								$_closed = true;
								break;
							}
						}

						if ($this->botts->fields_updated && $_closed)
						{
							$receipt = $this->botts->mail_ticket($ticket_id, $this->botts->fields_updated, $receipt, false, true);
							self::message_set($receipt);
						}

					}
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
				if (phpgw::get_var('send', 'bool'))
				{
					$this->_send($id);
				}
				else if($_closed)
				{
					$ticket = $this->botts->read_single($values['ticket_id']);

					if($ticket)
					{
						$cat_path = $this->botts->cats->get_path($ticket['cat_id']);

						if(count($cat_path) > 1)
						{
							$parent_cat_id = $cat_path[0]['id'];
						}
					}

					self::redirect(array('menuaction'	 => "{$this->currentapp}.uitts.index", 'parent_cat_id' => $parent_cat_id));
				}
				else
				{
					self::redirect(array('menuaction'	 => "{$this->currentapp}.uiorder_template.edit",
						'id'			 => $id,
						'ticket_id'		 => $values['ticket_id'],
						'type_id'		 => $values['type_id'],
						'init_preview2'	 => $init_preview)
					);
				}
			}
		}

		private function _send( $id )
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$message_info = $this->bo->read_single($id);

			$ticket_id	 = $message_info['ticket_id'];
			$ticket		 = $this->botts->read_single($ticket_id);
			$recipients	 = $message_info['mail_recipients'];

			$contact_data = $this->bo->get_contact_data($ticket);

			$coordinator_email	 = $contact_data['user_email'];
			$coordinator_name	 = $contact_data['user_name'];

			$cc		 = '';
			$bcc	 = '';
			$cc_arr	 = array();
			if ($contact_data['contact_email'])
			{
				$cc_arr[] = $contact_data['contact_email'];
			}
			if ($contact_data['contact_email2'])
			{
				$cc_arr[] = $contact_data['contact_email2'];
			}

			if ($cc_arr)
			{
				$cc = implode(';', $cc_arr);
			}

			$html_content = $this->bo->get_html_content($message_info, $contact_data);

			$html	 = $html_content['html'];
			$subject = $html_content['subject'];

			$preview = $this->preview_html;

			$attachment_log	 = '';
			$attachment_text = '';
			if (!empty($message_info['file_attachments']) && is_array($message_info['file_attachments']))
			{
				$attachments	 = CreateObject('property.bofiles')->get_attachments($message_info['file_attachments']);
				$_attachment_log = array();
				foreach ($attachments as $_attachment)
				{
					$_attachment_log[] = $_attachment['name'];
				}
				$attachment_log = implode(', ', $_attachment_log);
			}

			$lang_attachments = lang('attachments');

			if ($attachment_log)
			{
				$attachment_html_row = "<tr><td>$lang_attachments</td><td>:&nbsp;$attachment_log</td></tr>";
				$attachment_text	 = " {$lang_attachments} : {$attachment_log}";
			}
			else
			{
				$attachment_html_row = '';
			}

			$html = str_replace('__ATTACHMENTS__', $attachment_html_row, $html);

			if ($preview)
			{

				$GLOBALS['phpgw_info']['flags']['noheader']	 = true;
				$GLOBALS['phpgw_info']['flags']['nofooter']	 = true;
				$GLOBALS['phpgw_info']['flags']['xslt_app']	 = false;
				echo $html;
				$GLOBALS['phpgw']->common->phpgw_exit();
			}


			if (empty($GLOBALS['phpgw_info']['server']['smtp_server']))
			{
				phpgwapi_cache::message_set(lang('SMTP server is not set! (admin section)'), 'error');
			}
			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$_to = !empty($recipients[0]) ? implode(';', $recipients) : '';

			if (!$_to)
			{
				phpgwapi_cache::message_set(lang('missing recipient for order %1', $ticket['order_id']), 'error');
				return false;
			}

			switch ($this->currentapp)
			{
				case 'property':
					$historylog_ticket	 = CreateObject('property.historylog', 'tts');
					break;
				case 'helpdesk':
					$historylog_ticket	 = CreateObject('phpgwapi.historylog', 'helpdesk');
					break;
				default:
					break;
			}

			$config_admin = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id($this->currentapp, '.admin'))->read();

			if (!empty($config_admin['xPortico']['sender_email_address']))
			{
				$coordinator_email	 = $config_admin['xPortico']['sender_email_address'];
				$coordinator_name	 = $GLOBALS['phpgw_info']['server']['site_title'];
			}

			try
			{
				$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, stripslashes($html), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html', '', $attachments);
				phpgwapi_cache::message_set(lang('%1 is notified', $_to), 'message');

				$lang_external = lang('external communication');

				$historylog_ticket->add('M', $ticket_id, "($lang_external [$id]) {$_to}{$attachment_text}");
				$this->historylog->add('M', $id, "{$_to}{$attachment_text}");
				$this->bo->update_msg($id, $_to, $attachment_log);
			}
			catch (Exception $exc)
			{
				phpgwapi_cache::message_set($exc->getMessage(), 'error');
			}
		}

		private function _populate( $id = false )
		{
			$fields = $this->bo->get_fields();

			$values = array();

			foreach ($fields as $field => $field_info)
			{
				if (($field_info['action'] & PHPGW_ACL_ADD) || ($field_info['action'] & PHPGW_ACL_EDIT))
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

			$values['ticket_status'] = phpgw::get_var('ticket_status');

			return $values;
		}
	}