<?php
/**
	 * phpGroupWare
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset HF
	 * @package phpgwapi
	 * @subpackage utilities
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.datetime');


	class phpgwapi_uicommon extends phpgwapi_uicommon_jquery
	{

		public $public_functions = array(
			'add' => true,
			'index' => true,
			'query' => true,
			'query_relaxed'=> true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'get_list' => true
		);

		protected
			$fields,
			$composite_types,
			$payment_methods,
			$permissions,
			$called_class_arr,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$called_class = get_called_class();
			$this->currentapp = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->called_class_arr = explode('_', $called_class, 2);
		}


		protected function _get_fields()
		{
			$values = array();
			foreach ($this->fields as $field => $field_info)
			{
				if($field_info['action'] & PHPGW_ACL_READ)
				{
					$label = !empty($field_info['translated_label'])  ? $field_info['translated_label'] :'';
					if(!$label)
					{
						$label =!empty($field_info['label']) ? lang($field_info['label']) : $field;
					}

					$data = array(
						'key' => $field,
						'label' =>  $label,
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
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				if ($ajax)
				{
					return array(
						'status_kode' => 'error',
						'status' => lang('error'),
						'msg' => lang('no access')
					);
				}
				else
				{
					phpgw::no_access();
				}
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
					$class_info = explode('_', get_class($object), 2);

					$this->_handle_files($class_info[0], $class_info[1], $object->get_id());

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
							'menuaction' => "{$this->currentapp}.{$this->called_class_arr[1]}.edit",
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
					$messages = phpgwapi_cache::message_get(true);
					return array(
						'status_kode' => 'error',
						'status' => lang('error'),
						'msg' => $messages ? $messages : lang('did not validate')
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

		private function get_data($relaxe_acl = false)
		{
			if (!$relaxe_acl && empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}
			$params = $this->bo->build_default_read_params();
			$params['relaxe_acl'] = $relaxe_acl;
			return $this->bo->read($params);
		}

		/**
		 * (non-PHPdoc)
		 * @see eventplanner/inc/eventplanner_uicommon#query()
		 */
		public function query($relaxe_acl = false)
		{
			$values = $this->get_data($relaxe_acl);
			array_walk($values["results"], array($this, "_add_links"), "{$this->currentapp}.{$this->called_class_arr[1]}.edit");

			return $this->jquery_results($values);
		}

		public function query_relaxed()
		{
			$relaxe_acl = true;
			return $this->query($relaxe_acl);
		}

		/**
		 * Returns a minimum for - let say - autocomplete
		 * @param void
		 * @return array An associative array
		 */
		public function get_list()
		{
			$values = $this->get_data(true);

			$results = array();
			foreach ($values['results'] as $row)
			{
				$results[] = array(
					'id' => $row['id'],
					'name' =>$row['name'],
					'title' =>$row['title']
					);
			}
			$values['results'] = $results;
			return $this->jquery_results($values);
		}

		/**
		 * Called from  subclasses
		 * @param type $fakebase
		 * @param type $sub_module
		 * @param type $id
		 * @return type
		 * @throws Exception
		 */
		protected function _handle_files( $fakebase, $sub_module, $id  )
		{
			$id = (int)$id;
			if (!$id)
			{
				throw new Exception(__CLASS__.'::' . __FUNCTION__.'() - missing id');
			}
			if (!$sub_module)
			{
				throw new Exception(__CLASS__.'::' . __FUNCTION__.'() - missing sub_module');
			}
			if (!$fakebase)
			{
				throw new Exception(__CLASS__.'::' . __FUNCTION__.'() - missing fakebase');
			}

			$bofiles = CreateObject('property.bofiles', '/' . ltrim($fakebase, '/'));

			if (isset($_POST['delete_file']) && is_array($_POST['delete_file']))
			{
				$bofiles->delete_file("/{$sub_module}/{$id}/",array('file_action' => $_POST['delete_file']));
			}
			$file_name = str_replace(' ', '_', $_FILES['file']['name']);

			if ($file_name)
			{
				if (!is_file($_FILES['file']['tmp_name']))
				{
					phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error');
					return;
				}

				$to_file = "{$bofiles->fakebase}/{$sub_module}/{$id}/{$file_name}";
				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					phpgwapi_cache::message_set(lang('This file already exists !'), 'error');
				}
				else
				{
					$bofiles->create_document_dir("{$sub_module}/{$id}");
					$bofiles->vfs->override_acl = 1;

					if (!$bofiles->vfs->cp(array(
							'from' => $_FILES['file']['tmp_name'],
							'to' => $to_file,
							'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
					{
						phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error');
					}
					$bofiles->vfs->override_acl = 0;
				}
			}
		}

		public function get_files($fakebase, $sub_module, $menuaction,  $id)
		{

			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			$id = (int)$id;

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$values = (array)$vfs->ls (array(
				'string' => "/{$fakebase}/{$sub_module}/{$id}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$link_view_file = self::link(array('menuaction' => $menuaction));

			$content_files = array();
			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$lang_view =  lang('click to view file');
			$lang_delete =  lang('Check to delete file');
			$z = 0;
			foreach ($values as $_entry)
			{
				$content_files[] = array(
					'id'	=> $_entry['file_id'],
					'file_name' => "<a href=\"{$link_view_file}&file_id={$_entry['file_id']}\" target=\"_blank\" title=\"{$lang_view}\">{$_entry['name']}</a>",
					'delete_file' => "<input type=\"checkbox\" name=\"delete_file[]\" value=\"{$_entry['file_id']}\" title=\"{$lang_delete}\">",
				);
				if ( in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name'] = $_entry['name'];
					$content_files[$z]['img_id'] = $_entry['file_id'];
					$content_files[$z]['img_url'] = self::link(array(
							'menuaction' => $menuaction,
							'file_id'	=>  $_entry['file_id'],
							'file' => $_entry['directory'] . '/' . urlencode($_entry['name'])
					));
					$content_files[$z]['thumbnail_flag'] = 'thumb=1';
				}
				$z ++;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{

				$total_records = count($content_files);

				return array
					(
					'data' => $content_files,
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => $total_records,
					'recordsFiltered' => $total_records
				);
			}
			return $content_files;
		}

		public function view_file()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				phpgw::no_access();
			}

			$thumb = phpgw::get_var('thumb', 'bool');
			$file_id = phpgw::get_var('file_id', 'int');

			$bofiles = CreateObject('property.bofiles');

			if($file_id)
			{
				$file_info = $bofiles->vfs->get_info($file_id);
				$file = "{$file_info['directory']}/{$file_info['name']}";
			}
			else
			{
				$file = urldecode(phpgw::get_var('file'));
			}

			$source = "{$bofiles->rootdir}{$file}";
			$thumbfile = "$source.thumb";

			// prevent path traversal
			if (preg_match('/\.\./', $source))
			{
				return false;
			}

			$uigallery = CreateObject('property.uigallery');

			$re_create = false;
			if ($uigallery->is_image($source) && $thumb && $re_create)
			{
				$uigallery->create_thumb($source, $thumbfile, $thumb_size = 50);
				readfile($thumbfile);
			}
			else if ($thumb && is_file($thumbfile))
			{
				readfile($thumbfile);
			}
			else if ($uigallery->is_image($source) && $thumb)
			{
				$uigallery->create_thumb($source, $thumbfile, $thumb_size = 50);
				readfile($thumbfile);
			}
			else if ($file_id)
			{
				$bofiles->get_file($file_id);
			}
			else
			{
				$bofiles->view_file('', $file);
			}
		}

	}