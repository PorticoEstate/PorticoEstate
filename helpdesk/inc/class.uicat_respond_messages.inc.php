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

	class helpdesk_uicat_respond_messages extends phpgwapi_uicommon
	{

		var $public_functions = array
		(
			'edit'			=> true,
		);

		private $acl_location, $acl_read, $acl_add, $acl_edit, $acl_delete,
			$bo, $cats;

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu("admin::helpdesk::cat_respond_messages");
			
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('category respond messages');

			$this->bo			= CreateObject('helpdesk.bocat_respond_messages');
			$this->cats			= $this->bo->cats;
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.ticket';
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->currentapp);
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->currentapp);
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->currentapp);
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->currentapp);

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
			if(phpgw::get_var('save', 'bool') || phpgw::get_var('send', 'bool') || phpgw::get_var('init_preview', 'bool'))
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


			$categories = $this->cats->return_sorted_array(0, false);
			$cat_respond_messages = $this->bo->read();
//			_debug_array($cat_respond_messages);die();

			$cat_header[] = array
			(
				'lang_name'				=> lang('name'),
				'lang_status'			=> lang('status'),
				'lang_edit'				=> lang('edit'),
			);

			$content = array();
			foreach ($categories as $cat)
			{
				$level		= $cat['level'];
				$cat_name	= $GLOBALS['phpgw']->strip_html($cat['name']);

				$main = 'yes';
				if ($level > 0)
				{
					continue;
					$space = ' . ';
					$spaceset = str_repeat($space,$level);
					$cat_name = $spaceset . $cat_name;
					$main = 'no';
				}

				$content[] = array
				(
					'cat_id'					=> $cat['id'],
					'name'						=> $cat_name,
					'include_content'			=> $cat_respond_messages[$cat['id']]['include_content'],
					'new_message'				=> $cat_respond_messages[$cat['id']]['new_message'],
					'set_user_message'			=> $cat_respond_messages[$cat['id']]['set_user_message'],
					'update_message'			=> $cat_respond_messages[$cat['id']]['update_message'],
					'close_message'				=> $cat_respond_messages[$cat['id']]['close_message'],
					'main'						=> $main,
					'status'					=> $cat['active'],
					'status_text'				=> $cat['active'] == 1 ? 'active' : 'disabled',
				);
			}

			$link_data['menuaction'] = 'helpdesk.uicat_respond_messages.edit';

			$cat_add[] = array
			(
				'lang_add'				=> lang('add'),
				'lang_add_statustext'	=> lang('add a category'),
				'action_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_done'				=> lang('done'),
				'lang_done_statustext'	=> lang('return to admin mainscreen')
			);
			$data = array
			(
				'form_action'	=> self::link(array('menuaction' => "{$this->currentapp}.uicat_respond_messages.edit")),
				'edit_action' => self::link(array('menuaction' => "{$this->currentapp}.uicat_respond_messages.edit")),
				'cancel_url' => self::link(array('menuaction' => "{$this->currentapp}.uitts.index")),
				'cat_header'	=> $cat_header,
				'cat_data'		=> $content,
				'cat_add'		=> $cat_add
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang($mode);

			self::render_template_xsl(array('cat_respond_messages'), array('edit' => $data));

		}

		public function save($init_preview = null )
		{
			$values = phpgw::get_var('values', 'html');
			
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

			$this->receipt['message'][] = array('msg' => lang('category respond messages has been saved'));

			self::message_set($this->receipt);
			self::redirect(array('menuaction' => "{$this->currentapp}.uicat_respond_messages.edit"));
		}
	}