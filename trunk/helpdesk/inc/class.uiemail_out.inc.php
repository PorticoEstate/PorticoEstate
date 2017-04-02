<?php
/**
	 * phpGroupWare - helpdesk: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package helpdesk
	 * @subpackage email_out
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('helpdesk', 'email_out', 'inc/model/');

	class helpdesk_uiemail_out extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'edit' => true,
			'save' => true,
			'get' => true,
			'get_candidates' => true,
			'set_candidates' => true,
			'delete_recipients'	=> true,
			'get_recipients'=> true,
			'set_email'		=> true,
			'send_email'	=> true
		);

		protected
			$fields,
			$permissions;

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('helpdesk::email_out');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('email out');
			$this->bo = createObject('helpdesk.boemail_out');
			$this->fields = helpdesk_email_out::get_fields();
			$this->permissions = helpdesk_email_out::get_instance()->get_permission_array();
		}


		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('email out');

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'helpdesk.uiemail_out.index',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'helpdesk.uiemail_out.add')),
					'editor_action' => '',
					'field' => parent::_get_fields()
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
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'helpdesk.uiemail_out.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript('helpdesk', 'portico', 'email_out.index.js');
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}

		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['object']))
			{
				$email_out = $values['object'];
			}
			else
			{
				$id = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$email_out = $this->bo->read_single($id);
			}

			$tabs = array();
			$tabs['first_tab'] = array(
				'label' => lang('email out'),
				'link' => '#first_tab'
			);
			$tabs['recipient'] = array(
				'label' => lang('recipient'),
				'link' => '#recipient',
				'disable' => $email_out->get_id() ? false : true,
			);


			$bocommon = CreateObject('property.bocommon');

			$comments = (array)$email_out->comments;
			foreach ($comments as $key => &$comment)
			{
				$comment['value_count'] = $key +1;
				$comment['value_date'] = $GLOBALS['phpgw']->common->show_date($comment['time']);
			}

			$comments_def = array(
				array('key' => 'value_count', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'author', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'comment', 'label' => lang('Note'), 'sortable' => true, 'resizeable' => true)
			);
 
			$datatable_def[] = array(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => $comments_def,
				'data' => json_encode($comments),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$parties_def = array(
				array('key' => 'id', 'label' => 'ID', 'sortable' => true, 'resizeable' => true,'formatter' => 'JqueryPortico.formatLink'),
				array('key' => 'name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'email', 'label' => lang('email'), 'sortable' => true, 'resizeable' => true, 'editor' =>true),
			);

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$tabletools_candidate = array();
			$tabletools_candidate[] = array
				(
				'my_name' => 'add',
				'text' => lang('add'),
				'type' => 'custom',
				'custom_code' => "
						var api = oTable1.api();
						var selected = api.rows( { selected: true } ).data();

						var numSelected = 	selected.length;

						if (numSelected ==0){
							alert('None selected');
							return false;
						}
						var ids = [];
						for ( var n = 0; n < selected.length; ++n )
						{
							var aData = selected[n];
							ids.push(aData['id']);
						}
						onActionsClick_candidates('add', ids);
						"
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
//				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.notify.update_data',
//						'location_id' => $location_id, 'location_item_id' => $id, 'action' => 'refresh_notify_contact',
//						'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $parties_def,
				'data' => json_encode(array()),
				'tabletools' => array_merge($tabletools,$tabletools_candidate),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('editor_action' => self::link(array('menuaction' => 'helpdesk.uiemail_out.set_email')))
				)
			);


			$tabletools_recipient = array();
			$tabletools_recipient[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'type' => 'custom',
				'custom_code' => "
						var api = oTable2.api();
						var selected = api.rows( { selected: true } ).data();

						var numSelected = 	selected.length;

						if (numSelected ==0){
							alert('None selected');
							return false;
						}
						var ids = [];
						for ( var n = 0; n < selected.length; ++n )
						{
							var aData = selected[n];
							ids.push(aData['id']);
						}
						onActionsClick_recipient('delete_recipients', ids);
						"
			);
			$tabletools_recipient[] = array
				(
				'my_name' => 'send_email',
				'text' => lang('send email'),
				'type' => 'custom',
				'custom_code' => "
						var api = oTable2.api();
						var selected = api.rows( { selected: true } ).data();

						var numSelected = 	selected.length;

						if (numSelected ==0){
							alert('None selected');
							return false;
						}
						var ids = [];
						for ( var n = 0; n < selected.length; ++n )
						{
							var aData = selected[n];
							ids.push(aData['id']);
						}
						onActionsClick_recipient('send_email', ids);
						"
			);


			$parties_def[] = array('key' => 'status', 'label' => lang('status'), 'sortable' => true, 'resizeable' => true);


			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'helpdesk.uiemail_out.get_recipients',
						'id' => $id,'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $parties_def,
				'data' => json_encode(array()),
				'tabletools' => array_merge($tabletools,$tabletools_recipient),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('editor_action' => self::link(array('menuaction' => 'helpdesk.uiemail_out.set_email')))
				)
			);

			$data = array(
				'datatable_def' => $datatable_def,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uiemail_out.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uiemail_out.index',)),
				'email_out' => $email_out,
				'mode' => $mode,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab' => $active_tab
			);
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::formvalidator_generate(array());
			self::rich_text_editor('content');
			self::add_javascript('helpdesk', 'portico', 'email_out.edit.js');
			self::render_template_xsl(array('email_out', 'datatable_inline'), array($mode => $data));
		}

		/*
		 * Get the email_out with the id given in the http variable 'id'
		 */

		public function get( $id = 0 )
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$id = !empty($id) ? $id : phpgw::get_var('id', 'int');

			$email_out = $this->bo->read_single($id)->toArray();

			unset($email_out['secret']);

			return $email_out;
		}

		public function save()
		{
			parent::save();
		}

		public function get_candidates( )
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}
			$type =  phpgw::get_var('type', 'string');
			$set_id =  phpgw::get_var('set_id', 'int');
			$email_out_id =  phpgw::get_var('id', 'int');

			switch ($type)
			{
				case 'recipient_set':
					$values = $this->bo->get_recipient_candidates($set_id, $email_out_id);
					array_walk($values, array($this, "_add_links"), array('menuaction' => 'helpdesk.uigeneric.edit',
								'type' => 'email_recipient_list'));

					break;

				default:
					$values = array();
					break;
			}

			return $this->jquery_results(array('results' => $values));
		}

		public function set_candidates()
		{
			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				phpgw::no_access();
			}
			$id =  phpgw::get_var('id', 'int');
			$ids =  (array) phpgw::get_var('ids', 'int');
			$ret = $this->bo->set_candidates($id, $ids);
		}

		public function delete_recipients()
		{
			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				phpgw::no_access();
			}
			$id =  phpgw::get_var('id', 'int');
			$ids =  (array) phpgw::get_var('ids', 'int');
			$ret = $this->bo->delete_recipients($id, $ids);
		}

		public function get_recipients()
		{
			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				phpgw::no_access();
			}
			$id =  phpgw::get_var('id', 'int');
			$values = $this->bo->get_recipients($id);
			array_walk($values, array($this, "_add_links"), array('menuaction' => 'helpdesk.uigeneric.edit',
								'type' => 'email_recipient_list'));
			return $this->jquery_results(array('results' => $values));

		}

		public function send_email( )
		{
			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				phpgw::no_access();
			}
			$id =  phpgw::get_var('id', 'int');
			$ids =  (array) phpgw::get_var('ids', 'int');
			$ret = $this->bo->send_email($id, $ids);
		}

		public function set_email( )
		{
			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				phpgw::no_access();
			}

			phpgw::import_class('helpdesk.soparty');

			$field_name = phpgw::get_var('field_name');
			$email = phpgw::get_var('value');
			$id = phpgw::get_var('id');
			$email_validator = CreateObject('phpgwapi.EmailAddressValidator');
			$message = array();
			if (!$email_validator->check_email_address($email) )
			{
				$message['error'][] = array('msg' => lang('data has not validated'));
				return $message;
			}

			$party = helpdesk_soparty::get_instance()->get_single($id);
			$party->set_field('email', $email);
			$result = helpdesk_soparty::get_instance()->store($party);

			$message = array();
			if ($result)
			{
				$message['message'][] = array('msg' => lang('data has been saved'));
			}
			else
			{
				$message['error'][] = array('msg' => lang('data has not been saved'));
			}

			return $message;

		}
	}