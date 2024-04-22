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
		);
		var $acl, $bo,$receipt;
		private $acl_location, $acl_read, $acl_add, $acl_edit, $acl_delete,
			 $bocommon, $config, $dateformat, $account;

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu("property::helpdesk::quick_order_template");

			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('quick order template');

			$this->account		 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.admin';
			$this->acl_read		 = $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->currentapp);
			$this->acl_add		 = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->currentapp);
			$this->acl_edit		 = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->currentapp);
			$this->acl_delete	 = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->currentapp);
			$this->bo			 = createObject("{$this->currentapp}.boorder_template");
			$this->bocommon		 = createObject('property.bocommon');
			$this->config		 = CreateObject('phpgwapi.config', $this->currentapp)->read();
			$this->dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		}


		function get_vendor_options( $selected = 0 )
		{
			$values = $this->bo->get_vendors();
			$default_value		 = array('cat_id' => '', 'name' => lang('select'));
			array_unshift($values, $default_value);

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
			$parameters = array(
				'parameter' => array(
					array(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

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

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function edit($values = array(), $mode = 'edit', $error = false )
		{
			if (!$this->acl_add)
			{
				phpgw::no_access();
			}

			/**
			 * Save
			 */

			if (!$error && (phpgw::get_var('save', 'bool')))
			{
				$this->save();

				self::redirect(array('menuaction'	 => "{$this->currentapp}.uiorder_template.edit"));
			}

			$id = phpgw::get_var('id', 'int');
			if (!$error && $id)
			{
				$values	= $this->bo->read_single($id);
			}

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id'		 => $values['vendor_id'],
				'vendor_name'	 => $values['vendor_name'],
				'type'			 => 'form'
			));

			$content_email = $this->bocommon->get_vendor_email(isset($values['vendor_id']) ? $values['vendor_id'] : 0, 'mail_recipients');

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

			$datatable_def = array();
			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_1',
				'requestUrl' => "''",
				'ColumnDefs' => array(
					array('key' => 'value_email', 'label'	=> lang('email'),	'sortable'	 => true, 'resizeable' => true),
					array('key'	=> 'value_select', 'label'	=> lang('select'), 'sortable'	 => false, 'resizeable' => true)
				),
				'data'		 => json_encode($content_email),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$tabs			 = array();
			$tabs['main']	 = array(
				'label'	 => lang('template'),
				'link'	 => '#main'
			);

			$_filter_buildingpart	 = array();
			$filter_buildingpart	 = isset($this->config['filter_buildingpart']) ? $this->config['filter_buildingpart'] : array();

			if ($filter_key = array_search('.b_account', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

			$cats					 = CreateObject('phpgwapi.categories', -1, 'property', '.project');
			$cats->supress_info	 = true;

			$_cat_sub	= $cats->return_sorted_array(0, false, '', '', '', false, false);

			$selected_cat		 = $values['order_cat_id'] ? $values['order_cat_id'] : 0;
			$validatet_category	 = '';
			$cat_sub			 = array();
			foreach ($_cat_sub as $entry)
			{
				if ($entry['active'] == 2 && $entry['id'] != $selected_cat)//hidden
				{
					continue;
				}

				if (!$validatet_category)
				{
					if ($entry['active'] && $entry['id'] == $selected_cat)
					{
						$_category = $cats->return_single($entry['id']);
						if ($_category[0]['is_node'])
						{
							$validatet_category = 1;
						}
					}
				}
				$entry['name']	 = str_repeat(' . ', (int)$entry['level']) . $entry['name'];
				$entry['title']	 = $entry['description'];
				$cat_sub[]		 = $entry;
			}

			array_unshift($cat_sub, array('id' => '', 'name' => lang('category')));
		
			$data = array(
				'values'					 =>$values,
				'value_external_project_name' => $this->bocommon->get_external_project_name($values['external_project_id']),
				'contract_list'				 => array('options' => $this->get_vendor_contract($values['vendor_id'], $values['contract_id'])),
				'value_extra_mail_address'	=> $value_extra_mail_address,
				'value_service_name'		=> $this->bocommon->get_eco_service_name($values['service_id']),
				'ecodimb_data' => $this->bocommon->initiate_ecodimb_lookup(array(
						'ecodimb'		 => $values['ecodimb'],
						'ecodimb_descr'	 => $values['ecodimb_descr']
					)
				),
				'b_account_data' => $this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id'	 => $values['b_account_id'],
						'b_account_name' => isset($values['b_account_name']) ? $values['b_account_name'] : ''
					)
				),
				'datatable_def'				 => $datatable_def,
				'form_action'				 => self::link(array('menuaction' => "{$this->currentapp}.uiorder_template.edit", 'id' => $id)),
				'cancel_url'				 => self::link(array('menuaction' => "{$this->currentapp}.uiorder_template.index")),
				'vendor_data'				 => $vendor_data,
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, 0),
				'value_active_tab'			 => 0,
				'base_java_url'				 => "{menuaction:'{$this->currentapp}.uitts.update_data'}",
				'building_part_list'		 => array('options' => $this->bocommon->select_category_list(array(
						'type'		 => 'building_part',
						'selected'	 => $values['building_part'],
						'order'		 => 'id',
						'id_in_name' => 'num',
						'filter'	 => $_filter_buildingpart
					))),
				'order_dim1_list'				 => array('options' => $this->bocommon->select_category_list(array(
						'type'		 => 'order_dim1',
						'selected'	 => $values['order_dim1'],
						'order'		 => 'id',
						'id_in_name' => 'num'
					))),
				'tax_code_list'					 => array('options' => $this->bocommon->select_category_list(array(
						'type'		 => 'tax',
					'selected'	 => $values['tax_code'],
					'order'		 => 'id',
						'id_in_name' => 'num'
					))),
				'enable_unspsc'					=> !empty($this->config['enable_unspsc']) ? true : false,
				'value_unspsc_code_name'		=> $this->bocommon->get_unspsc_code_name($values['unspsc_code']),
				'lang_cat_sub'					=> lang('category'),
				'cat_sub_list'					=> $this->bocommon->select_list($selected_cat, $cat_sub),
				'cat_sub_name'					=> 'order_cat_id',
				'lang_cat_sub_statustext'		=> lang('select sub category'),
				'validatet_category'			=> $validatet_category,
				'collect_building_part'			 => !!$this->config['workorder_require_building_part']

//				'branch_list'			 => !empty($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_branch_list']) ? array
//					(
//						'options' => execMethod('property.boproject.select_branch_list', $values['branch_id'])
//					) : '',
			);
			$GLOBALS['phpgw_info']['flags']['app_header']	 .= '::' . lang('edit');

//			self::rich_text_editor('order_descr');

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript($this->currentapp, 'base', 'order_template.edit.js');
			self::render_template_xsl(array('order_template', 'datatable_inline', 'cat_sub_select'), array(
				'edit' => $data));
		}

		public function get_vendor_contract( $vendor_id = 0, $selected = 0 )
		{
			$contract_list	 = $this->bocommon->get_vendor_contract($vendor_id, $selected);
			$config			 = CreateObject('phpgwapi.config', 'property')->read();

			if ($contract_list)
			{
				if (!empty($config['alternative_to_contract_1']))
				{
					$contract_list[] = array('id' => -1, 'name' => $config['alternative_to_contract_1']);
				}
				else
				{
					$contract_list[] = array('id' => -1, 'name' => lang('outside contract'));
				}

				if (!empty($config['alternative_to_contract_2']))
				{
					$contract_list[] = array('id' => -2, 'name' => $config['alternative_to_contract_2']);
				}
				if (!empty($config['alternative_to_contract_3']))
				{
					$contract_list[] = array('id' => -3, 'name' => $config['alternative_to_contract_3']);
				}
				if (!empty($config['alternative_to_contract_4']))
				{
					$contract_list[] = array('id' => -4, 'name' => $config['alternative_to_contract_4']);
				}
			}

			if ($selected)
			{
				foreach ($contract_list as &$contract)
				{
					$contract['selected'] = $selected == $contract['id'] ? 1 : 0;
				}
			}
			return $contract_list;
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
			if (phpgw::get_var('save', 'bool'))
			{
				phpgw::no_access();
			}
			$this->edit(array(), 'view');
		}

		public function save( $init_preview = null )
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
					$id		 = $receipt['id'];
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

				self::redirect(array(
					'menuaction'	 => "{$this->currentapp}.uiorder_template.edit",
					'id'			 => $id)
				);
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