<?php
	/**
	 * phpGroupWare - booking: a part of a Facilities Management System.
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
	 * @internal Development of this article was funded by http://www.bergen.kommune.no/
	 * @package booking
	 * @subpackage article
	 * @version $Id: $
	 */
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('phpgwapi.datetime');

	include_class('booking', 'service', 'inc/model/');

	class booking_uiservice extends phpgwapi_uicommon
	{

		public $public_functions = array(
			'add'						 => true,
			'index'						 => true,
			'query'						 => true,
			'view'						 => true,
			'edit'						 => true,
			'save'						 => true,
			'get'						 => true,
			'get_services'				 => true,
			'get_reserved_resources'	 => true,
			'handle_multi_upload_file'	 => true,
			'_get_files'				 => true,
			'view_file'					 => true,
			'update_file_data'			 => true,
			'set_mapping'				 => true
		);
		protected
			$fields,
			$permissions,
			$currentapp;

		public function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['app_header']	 .= '::' . lang('Service');
			$this->bo										 = createObject('booking.boservice');
			$this->fields									 = booking_service::get_fields();
			$this->permissions								 = booking_service::get_instance()->get_permission_array();
			$this->currentapp								 = $GLOBALS['phpgw_info']['flags']['currentapp'];
			self::set_active_menu("{$this->currentapp}::commerce::service");
		}

		private function get_status_options( $selected = 1 )
		{
			$status_options	 = array();
			$status_list	 = booking_service::get_status_list();

			array_unshift($status_list, array(0 => lang('select')));

			foreach ($status_list as $status_id => $status_name)
			{
				$status_options[] = array(
					'id'		 => $status_id,
					'name'		 => $status_name,
					'selected'	 => $status_id == $selected ? 1 : 0
				);
			}
			return $status_options;
		}

		private function get_unit_list( $selected = 0 )
		{
			$unit_list	 = array();
			$unit_list[] = array('id' => 'each', 'name' => lang('each'));
			$unit_list[] = array('id' => 'kg', 'name' => lang('kg'));
			$unit_list[] = array('id' => 'm', 'name' => lang('meter'));
			$unit_list[] = array('id' => 'm2', 'name' => lang('square meter'));
			$unit_list[] = array('id' => 'minute', 'name' => lang('minute'));
			$unit_list[] = array('id' => 'hour', 'name' => lang('hour'));
			$unit_list[] = array('id' => 'day', 'name' => lang('day'));

			foreach ($unit_list as &$unit)
			{
				$unit['selected'] = $unit['id'] == $selected ? 1 : 0;
			}
			return $unit_list;
		}

		public function get_reserved_resources()
		{
			$building_id = phpgw::get_var('building_id', 'int');
			return $this->bo->get_reserved_resources($building_id);
		}

		public function get_services( $selected = 0 )
		{
			$services_list = execMethod('booking.bogeneric.get_list', array('type' => 'article_service'));

			$alredy_taken = $this->bo->get_mapped_services();

			foreach ($services_list as $service)
			{
				if ($selected != $service['id'] && in_array($service['id'], $alredy_taken))
				{
					continue;
				}

				$service_options[] = array(
					'id'		 => $service['id'],
					'name'		 => $service['name'],
					'selected'	 => $service['id'] == $selected ? 1 : 0
				);
			}
			return $service_options;
		}

		public function set_mapping()
		{
			if (empty($this->permissions[PHPGW_ACL_EDIT]))
			{
				phpgw::no_access();
			}
			$service_id = phpgw::get_var('service_id', 'int','POST');
			$selected_resources = phpgw::get_var('selected_resources', 'int','POST');

			$ret = $this->bo->set_mapping($service_id, $selected_resources);
			return array(
				'message' => $ret ? lang('mapping updated') : lang('transactio failed'),
				'status'  => $ret ? 'Ok' : 'error'
			);

		}
		
		public function index()
		{
			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				$message = '';
				if ($this->currentapp == 'bookingfrontend')
				{
					$message = lang('you need to log in to access this page.');
				}
				phpgw::no_access(false, $message);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('autocomplete');

			$function_msg = lang('article');

			$data = array(
				'datatable_name' => $function_msg,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'	 => 'filter',
								'name'	 => 'filter_active',
								'text'	 => lang('status'),
								'list'	 => $this->get_status_options()
							),
//							array(
//								'type' =>  $this->currentapp == 'booking' ? 'checkbox' : 'hidden',
//								'name' => 'filter_active',
//								'text' => lang('showall'),
//								'value' =>  1,
//								'checked'=> 1,
//							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => "{$this->currentapp}.uiservice.index",
						'phpgw_return_as'	 => 'json'
					)),
					'allrows'		 => true,
					'new_item'		 => self::link(array('menuaction' => "{$this->currentapp}.uiservice.add")),
					'editor_action'	 => '',
					'field'			 => parent::_get_fields()
				)
			);

			$parameters = array(
				'parameter' => array(
					array(
						'name'	 => 'id',
						'source' => 'id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'view',
				'text'		 => lang('view'),
				'action'	 => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uiservice.view"
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'edit',
				'text'		 => lang('edit'),
				'action'	 => self::link(array
					(
					'menuaction' => "{$this->currentapp}.uiservice.edit"
				)),
				'parameters' => json_encode($parameters)
			);

			self::add_javascript($this->currentapp, 'base', 'service.index.js', 'text/javascript', true);
			phpgwapi_jquery::load_widget('numberformat');

			self::render_template_xsl('datatable_jquery', $data);
		}
		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */

		public function edit( $values = array(), $mode = 'edit' )
		{
			$active_tab										 = !empty($values['active_tab']) ? $values['active_tab'] : phpgw::get_var('active_tab', 'string', 'REQUEST', 'first_tab');
			$GLOBALS['phpgw_info']['flags']['app_header']	 .= '::' . lang('edit');
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			if (!empty($values['object']))
			{
				$service = $values['object'];
			}
			else
			{
				$id		 = !empty($values['id']) ? $values['id'] : phpgw::get_var('id', 'int');
				$service = $this->bo->read_single($id);
			}

			$id = (int)$id;

			$tabs				 = array();
			$tabs['first_tab']	 = array(
				'label'	 => lang('service'),
				'link'	 => '#first_tab',
			);
			$tabs['mapping']	 = array(
				'label'		 => lang('mapping'),
				'link'		 => '#mapping',
				'disable'	 => empty($id) ? true : false
			);

			$collapse_links = array(
				'show_inactive'	 => self::link(array('menuaction' => "{$this->currentapp}.uiservice.edit",
					'show_all'	 => 'true', 'id'		 => $id, 'active_tab' => $active_tab)),
				'hide_inactive'	 => self::link(array('menuaction' => "{$this->currentapp}.uiservice.edit",
					'show_all'	 => '', 'id'		 => $id, 'active_tab' => $active_tab))
			);

			$show_all = phpgw::get_var('show_all') || false;

			$this->building_so	 = CreateObject('booking.sobuilding');
			$this->resource_so	 = CreateObject('booking.soresource');

			$buildings	 = $this->building_so->read(array('filters' => array(), 'sort' => 'name', 'dir' => 'ASC', 'results' => -1));
			$children	 = array();

			$building_ids = array();
			foreach ($buildings['results'] as & $building)
			{
				$resources					 = $this->resource_so->read(array('filters' => array('building_id' => $building['id'], 'active' => 1), 'results' => -1));
				$building['id']				 = -1 * (int)$building['id'];
				$children[0][]				 = $building;
				$children[$building['id']]	 = $resources['results'];
			}

			$mapped_resources = $this->bo->get_mapping($id);

			$treedata = json_encode(array(
				"type"		 => "text",
				'label'		 => 'Top',
				'text'		 => lang('buildings'),
				'state'      => array(
					'disabled'  => $mode == 'edit' ? false : true,  // is the node disabled
				),
				'children'	 => $this->treeitem($children, 0, $show_all, $mapped_resources, $mode)
			));

			$data = array(
//				'datatable_def'		 => $datatable_def,
				'form_action'		 => self::link(array('menuaction' => "{$this->currentapp}.uiservice.save")),
				'cancel_url'		 => self::link(array('menuaction' => "{$this->currentapp}.uiservice.index",)),
				'service'			 => $service,
				'treedata'			 => $treedata,
				'service_categories' => array('options' => $this->get_status_options($service->service_cat_id)),
				'tabs'				 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'value_active_tab'	 => $active_tab,
				'collapse_links'	 => $collapse_links
			);

			self::rich_text_editor('field_description');
			phpgwapi_jquery::load_widget('treeview');
			phpgwapi_jquery::formvalidator_generate(array());
			self::add_javascript('booking', 'base', 'service.js');
			self::render_template_xsl(array('service'), array($mode => $data));
		}

		function treeitem( $children, $parent_id, $show_all,$mapped_resources, $mode)
		{
			static $item_ids = array();
			$nodes			 = array();
			if (is_array($children[$parent_id]))
			{
				foreach ($children[$parent_id] as $item)
				{
					if (in_array($item['id'], $item_ids))
					{
						continue;
					}

					$item_ids[] = $item['id'];

					if ($item['active'] == false && $show_all == false)
					{
						continue;
					}
					if ($item['id'] < 0)
					{
						$href = self::link(array('menuaction' => 'booking.uibuilding.edit',
								'id'		 => abs($item['id'])));
					}
					else
					{
						$href = self::link(array('menuaction' => 'booking.uiresource.edit',
								'id'		 => $item['id']));
					}

					$is_mapped = in_array($item['id'], $mapped_resources);

					$node = array(
						'id'		 => $item['id'],
						"type"		 => "text",
						"href"		 => $href,
						'target'	 => '_self',
						'label'		 => $item['name'],
						'text'		 => $item['name'],
						'state'      => array(
							'opened'    => $is_mapped,  // is the node open
							'disabled'  => $mode == 'edit' ? false : true,  // is the node disabled
							'selected'  => $is_mapped,  // is the node selected
						),
						
						'children'	 => $this->treeitem($children, $item['id'], $show_all, $mapped_resources, $mode)
					);

					$nodes[] = $node;
				}
			}
			return $nodes;
		}
		/*
		 * Get the article with the id given in the http variable 'id'
		 */

		public function get( $id = 0 )
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$id = !empty($id) ? $id : phpgw::get_var('id', 'int');

			$article = $this->bo->read_single($id)->toArray();

			unset($article['secret']);

			return $article;
		}

		public function update_file_data()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				return array();
			}

			$section			 = phpgw::get_var('section', 'string', 'REQUEST', 'documents');
			$location_item_id	 = phpgw::get_var('location_item_id', 'int');
			$ids				 = phpgw::get_var('ids', 'int');
			$action				 = phpgw::get_var('action', 'string');
			$tags				 = phpgw::get_var('tags', 'string');

			$fakebase	 = '/booking';
			$bofiles	 = CreateObject('property.bofiles', $fakebase);

			if ($action == 'delete_file' && $ids && $location_item_id)
			{
				$bofiles->delete_file("/article/{$location_item_id}/{$section}/", array('file_action' => $ids));
			}
			else if ($action == 'set_tag' && $ids)
			{
				$bofiles->set_tags($ids, $tags);
			}
			else if ($action == 'remove_tag' && $ids)
			{
				$bofiles->remove_tags($ids, $tags);
			}

			return $action;
		}

		function _get_files()
		{
			$id		 = phpgw::get_var('id', 'int');
			$section = phpgw::get_var('section', 'string', 'REQUEST', 'documents');

			if (empty($this->permissions[PHPGW_ACL_READ]))
			{
				return array();
			}


			$vfs				 = CreateObject('phpgwapi.vfs');
			$vfs->override_acl	 = 1;

			$files = $vfs->ls(array(
				'string'	 => "/booking/article/{$id}/$section",
				'relatives'	 => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$content_files	 = array();
			$lang_view		 = lang('click to view file');
			$lang_delete	 = lang('Check to delete file');

			$z = 0;
			foreach ($files as $_entry)
			{
				$link_file_data = array
					(
					'menuaction' => "{$this->currentapp}.uiservice.view_file",
					'file_id'	 => $_entry['file_id']
				);

				$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

				$content_files[] = array(
					'file_id'		 => $_entry['file_id'],
					'file_name'		 => "<a href='{$link_view_file}' target='_blank' title='{$lang_view}'>{$_entry['name']}</a>",
					'delete_file'	 => "<input type='checkbox' name='values[file_action][]' value='{$_entry['file_id']}' title='{$lang_delete}'>",
				);
				if (in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name']		 = $_entry['name'];
					$content_files[$z]['img_id']		 = $_entry['file_id'];
					$content_files[$z]['img_url']		 = self::link(array(
							'menuaction' => "{$this->currentapp}.uiservice.view_file",
							'file_id'	 => $_entry['file_id'],
							'file'		 => $_entry['directory'] . '/' . urlencode($_entry['name'])
					));
					$content_files[$z]['thumbnail_flag'] = 'thumb=1';
				}
				$z++;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$total_records = count($content_files);

				return array
					(
					'data'				 => $content_files,
					'draw'				 => phpgw::get_var('draw', 'int'),
					'recordsTotal'		 => $total_records,
					'recordsFiltered'	 => $total_records
				);
			}
			return $content_files;
		}

		public function handle_multi_upload_file()
		{
			if (empty($this->permissions[PHPGW_ACL_ADD]))
			{
				phpgw::no_access();
			}

			$section = phpgw::get_var('section', 'string', 'REQUEST', 'documents');
			$id		 = phpgw::get_var('id', 'int', 'GET');

			phpgw::import_class('property.multiuploader');

			$options = array();
			$options['fakebase']	 = "/booking";
			$options['base_dir']	 = "article/{$id}/{$section}";
			$options['upload_dir']	 = $GLOBALS['phpgw_info']['server']['files_dir'] . '/booking/' . $options['base_dir'] . '/';
			$options['script_url']	 = html_entity_decode(self::link(array('menuaction' => "{$this->currentapp}.uiservice.handle_multi_upload_file", 'id' => $id, 'section' => $section)));
			$upload_handler			 = new property_multiuploader($options, false);

			switch ($_SERVER['REQUEST_METHOD'])
			{
				case 'OPTIONS':
				case 'HEAD':
					$upload_handler->head();
					break;
				case 'GET':
					$upload_handler->get();
					break;
				case 'PATCH':
				case 'PUT':
				case 'POST':
					$upload_handler->add_file();
					break;
				case 'DELETE':
					$upload_handler->delete_file();
					break;
				default:
					$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}
	}