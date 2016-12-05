<?php
/**
	 * phpGroupWare - eventplanner: a part of a Facilities Management System.
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
	 * @package eventplanner
	 * @subpackage common
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.datetime');


	class eventplanner_uicommon extends phpgwapi_uicommon_jquery
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
		);

		protected
			$fields,
			$composite_types,
			$payment_methods,
			$permissions,
			$called_class_arr;

		public function __construct()
		{
			parent::__construct();
			$called_class = get_called_class();
			$this->called_class_arr = explode('_', $called_class);
		}


		protected function _get_fields()
		{
			$values = array();
			foreach ($this->fields as $field => $field_info)
			{
				if($field_info['action'] & PHPGW_ACL_READ)
				{
					$data = array(
						'key' => $field,
						'label' => !empty($field_info['label']) ? lang($field_info['label']) : $field,
						'sortable' => !empty($field_info['sortable']) ? true : false,
						'hidden' => !empty($field_info['hidden']) ? true : false,
					);

					if(!empty($field_info['formatter']))
					{
						$data['formatter'] = $field_info['formatter'];
					}

					$values[] = $data;
				}
			}
			return $values;
		}

		/*
		 * View the price item with the id given in the http variable 'id'
		 */

		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('view');

			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			$this->edit(array(), 'view');
		}
	/*
		 * To be removed
		 * Add a new  item to the database.  Requires only a title.
		 */

		public function add()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$this->edit();
		}

		public function save($ajax = false)
		{
			$called_class = get_called_class();

			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}
			$active_tab = phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');

			$id = phpgw::get_var('id', 'int');

			$object = $this->bo->read_single($id, true);

			/*
			 * Overrides with incoming data from POST
			 */
			$object = $this->bo->populate($object);

			if($object->validate())
			{
				if($object->store($object))
				{
					if($ajax)
					{
						phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
						return array(
							'status_kode' => 'ok',
							'status' => lang('ok'),
							'msg' => lang('messages_saved_form')
						);
					}
					else
					{
						phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
						self::redirect(array(
							'menuaction' => "{$this->called_class_arr[0]}.{$this->called_class_arr[1]}.edit",
							'id'		=> $object->get_id(),
							'active_tab' => $active_tab
							)
						);
					}
				}
				else
				{
					if($ajax)
					{
						phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
						return array(
							'status_kode' => 'error',
							'status' => lang('error'),
							'msg' => lang('messages_form_error')
						);
					}
					else
					{
						phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
						$this->edit(array('object'	=> $object, 'active_tab' => $active_tab));
					}
				}
			}
			else
			{
				if($ajax)
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					return array(
						'status_kode' => 'error',
						'status' => lang('error'),
						'msg' => lang('Did not validate')
					);
				}
				else
				{
					foreach ($this->fields as $field => $field_info)
					{
						$_temp = $object->$field;
						if($_temp && !is_array($_temp))
						{
							$object->$field = htmlspecialchars_decode(str_replace(array('&amp;','&#40;', '&#41;', '&#61;','&#8722;&#8722;','&#59;'), array('&','(', ')', '=', '--',';'), $_temp),ENT_QUOTES);
						}
					}

					$this->edit(array('object'	=> $object, 'active_tab' => $active_tab));
				}
			}
		}

		/**
		 * (non-PHPdoc)
		 * @see eventplanner/inc/eventplanner_uicommon#query()
		 */
		public function query()
		{
			$params = $this->bo->build_default_read_params();
			$values = $this->bo->read($params);
			array_walk($values["results"], array($this, "_add_links"), "{$this->called_class_arr[0]}.{$this->called_class_arr[1]}.edit");

			return $this->jquery_results($values);
		}
	}