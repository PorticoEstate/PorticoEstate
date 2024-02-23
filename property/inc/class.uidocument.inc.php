<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage document
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	/**
	 * Description
	 * @package property
	 */
	class property_uidocument extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;
		var $allrows;
		var $public_functions = array(
			'index'						 => true,
			'list_doc'					 => true,
			'view'						 => true,
			'view_file'					 => true,
			'edit'						 => true,
			'delete'					 => true,
			'download'					 => true,
			'handle_multi_upload_file'	 => true,
			'build_multi_upload_file'	 => true,
		);
		var $bocommon, $account, $bo, $cats, $bolocation,$config, $acl, $boadmin_entity, $acl_location, $acl_read,
		$acl_add, $acl_edit, $acl_delete, $bofiles, $fakebase, $status_id, $entity_id, $doc_type,$query_location;

		function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = "property::documentation";
			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$this->account										 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo											 = CreateObject('property.bodocument', true);
			$this->bocommon										 = & $this->bo->bocommon;
			$this->cats											 = & $this->bo->cats;
			$this->bolocation									 = CreateObject('property.bolocation');
			$this->config										 = CreateObject('phpgwapi.config', 'property');
			$this->boadmin_entity								 = CreateObject('property.boadmin_entity');

			$this->acl			 = & $GLOBALS['phpgw']->acl;
			$this->acl_location	 = '.document';
			$this->acl_read		 = $this->acl->check('.document', PHPGW_ACL_READ, 'property');
			$this->acl_add		 = $this->acl->check('.document', PHPGW_ACL_ADD, 'property');
			$this->acl_edit		 = $this->acl->check('.document', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete	 = $this->acl->check('.document', PHPGW_ACL_DELETE, 'property');

			//$this->rootdir 				= $this->bo->rootdir;
			$this->bofiles			 = & $this->bo->bofiles;
			$this->fakebase			 = $this->bo->fakebase;
			$this->start			 = $this->bo->start;
			$this->query			 = $this->bo->query;
			$this->sort				 = $this->bo->sort;
			$this->order			 = $this->bo->order;
			$this->filter			 = $this->bo->filter;
			$this->cat_id			 = $this->bo->cat_id;
			$this->status_id		 = $this->bo->status_id;
			$this->entity_id		 = $this->bo->entity_id;
			$this->doc_type			 = $this->bo->doc_type;
			$this->query_location	 = $this->bo->query_location;
			$this->allrows			 = $this->bo->allrows;

			// FIXME: $this->entity_id always has a value set here - skwashd jan08
			if ($this->entity_id)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$this->entity_id}";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::location';
			}
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start'			 => $this->start,
				'query'			 => $this->query,
				'sort'			 => $this->sort,
				'order'			 => $this->order,
				'filter'		 => $this->filter,
				'cat_id'		 => $this->cat_id,
				'status_id'		 => $this->status_id,
				'entity_id'		 => $this->entity_id,
				'doc_type'		 => $this->doc_type,
				'query_location' => $this->query_location
			);
			$this->bo->save_sessiondata($data);
		}

		public function query()
		{
			$search		 = phpgw::get_var('search');
			$order		 = phpgw::get_var('order');
			$draw		 = phpgw::get_var('draw', 'int');
			$columns	 = phpgw::get_var('columns');
			$export		 = phpgw::get_var('export', 'bool');
			$entity_id	 = phpgw::get_var('entity_id', 'int');

			$params = array(
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => $columns[$order[0]['column']]['data'],
				'sort'		 => $order[0]['dir'],
				'entity_id'	 => $entity_id,
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export,
			);

			$values = $this->bo->read($params);

			if ($export)
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			array_walk($result_data['results'], array($this, '_add_links'), "property.uidocument.list_doc");

			return $this->jquery_results($result_data);
		}

		function index()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

//			$entity_id	 = phpgw::get_var('entity_id', 'int');
			$preserve	 = phpgw::get_var('preserve', 'bool');

			if ($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start	 = $this->bo->start;
				$this->query	 = $this->bo->query;
				$this->sort		 = $this->bo->sort;
				$this->order	 = $this->bo->order;
				$this->filter	 = $this->bo->filter;
				$this->cat_id	 = $this->bo->cat_id;
				$this->status_id = $this->bo->status_id;
				$this->entity_id = $this->bo->entity_id;
			}

			$link_data = array
				(
				'menuaction' => 'property.uidocument.index',
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'cat_id'	 => $this->cat_id,
				'filter'	 => $this->filter,
				'status_id'	 => $this->status_id,
				'query'		 => $this->query,
				'doc_type'	 => $this->doc_type,
				'entity_id'	 => $this->entity_id
			);


			$categories		 = $this->cats->formatted_xslt_list(array('format'	 => 'filter',
				'selected'	 => $this->doc_type, 'globals'	 => True));
			$default_value	 = array('cat_id' => '', 'name' => lang('no document type'));
			array_unshift($categories['cat_list'], $default_value);
			foreach ($categories['cat_list'] as &$cat)
			{
				$cat['id'] = $cat['cat_id'];
			}
			$status_list	 = $this->bo->select_status_list('select', $this->status_id);
			$default_value	 = array('id' => '', 'name' => lang('no status'));
			array_unshift($status_list, $default_value);

			$data = array(
				'datatable_name' => lang('documents'),
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array('type'	 => 'filter',
								'name'	 => 'doc_type',
								'text'	 => lang('doc type'),
								'list'	 => $categories['cat_list'],
							),
							array('type'	 => 'filter',
								'name'	 => 'status_id',
								'text'	 => lang('status'),
								'list'	 => $status_list,
							),
						),
					),
				),
				'datatable'		 => array(
					'source'	 => self::link(array(
						'menuaction'		 => 'property.uidocument.index',
						'doc_type'			 => $this->doc_type,
						'entity_id'			 => $this->entity_id,
						'phpgw_return_as'	 => 'json'
						)
					),
					'allrows'	 => true,
					'download'	 => self::link(array('menuaction' => 'property.uidocument.index',
						'doc_type'	 => $this->doc_type,
						'entity_id'	 => $this->entity_id,
						'export'	 => true, 'allrows'	 => true
						)
					),
					'query'		 => $this->query,
					'field'		 => array(),
				),
			);

			if ($this->acl_add)
			{
				$data['datatable']['new_item'] = self::link(array(
						'menuaction' => 'property.uidocument.edit',
						'entity_id'	 => $this->entity_id,
						'cat_id'	 => $this->cat_id
						)
				);
			}
			$this->bo->read(array('dry_run' => true));
			$uicols				 = $this->bo->uicols;
			$count_uicols_name	 = count($uicols['name']);

			$type_id = 4;
			for ($i = 1; $i < $type_id; $i++)
			{
				$searc_levels[] = "loc{$i}";
			}

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key'		 => $uicols['name'][$k],
					'label'		 => $uicols['descr'][$k],
					'sortable'	 => ($uicols['sortable'][$k]) ? true : false,
					'hidden'	 => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if (!empty($uicols['formatter'][$k]))
				{
					$params['formatter'] = $uicols['formatter'][$k];
				}

				if (in_array($uicols['name'][$k], $searc_levels))
				{
					$params['formatter'] = 'JqueryPortico.searchLink';
				}

				if ($uicols['name'][$k] == 'nhk_link')
				{
					$params['formatter'] = 'JqueryPortico.formatLinkGeneric';
				}

				if ($uicols['name'][$k] == 'num')
				{
					$params['formatter'] = 'JqueryPortico.formatLink';
					$params['hidden']	 = false;
				}

				$denied = array('merknad');
				if (in_array($uicols['name'][$k], $denied))
				{
					$params['sortable'] = false;
				}
				else if (isset($uicols['cols_return_extra'][$k]) && ($uicols['cols_return_extra'][$k] != 'T' || $uicols['cols_return_extra'][$k] != 'CH'))
				{
					$params['sortable'] = true;
				}

				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array(
					array
						(
						'name'	 => 'doc_type',
						'source' => 'doc_type'
					),
					array
						(
						'name'	 => 'location_code',
						'source' => 'location_code'
					),
				)
			);

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'view_documents',
				'text'		 => lang('documents'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uidocument.list_doc',
					'entity_id'	 => $this->entity_id,
				)),
				'parameters' => json_encode($parameters)
			);

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query_at_location()
		{
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export	 = phpgw::get_var('export', 'bool');
			$entity_id	 = phpgw::get_var('entity_id', 'int');


			$location_code = phpgw::get_var('location_code');
			if ($this->query_location)
			{
				$location_code = $this->query_location;
			}

			$params = array(
				'start'			 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'		 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'			 => $search['value'],
				'order'			 => $columns[$order[0]['column']]['data'],
				'sort'			 => $order[0]['dir'],
				'allrows'		 => phpgw::get_var('length', 'int') == -1 || $export,
				'location_code'	 => $location_code,
				'entity_id'	 => $entity_id
			);

			$values = $this->bo->read_at_location($params);


			if ($this->cat_id)
			{
				$directory = "{$this->fakebase}/document/entity_{$this->entity_id}_{$this->cat_id}/{$p_num}/{$this->doc_type}";
			}
			else
			{
				$directory = "{$this->fakebase}/document/{$location_code}/{$this->doc_type}";
			}

			$this->config->read();
			$files_url = $this->config->config_data['files_url'];

			foreach ($values as &$document_entry)
			{
				if ($document_entry['link'])
				{
					if (!preg_match('/^HTTP/i', $document_entry['link']))
					{
						$document_entry['link'] = 'file:///' . str_replace(':', '|', $document_entry['link']);
					}

//					$link_view_file					 = $document_entry['link'];
					$document_entry['document_name'] = $document_entry['title'];
					unset($link_to_files);
				}
				else
				{
					if (!$link_to_files)
					{
						$link_view_file	 = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uidocument.view_file',
							'id'		 => $document_entry['id'], 'entity_id'	 => $this->entity_id,
							'cat_id'	 => $this->cat_id, 'p_num'		 => $p_num));
						$link_to_files	 = $files_url;
					}
					else
					{
						$link_view_file = "{$files_url}/{$directory}/{$document_entry['document_name']}";
					}
					$document_entry['link'] = $link_view_file;
				}
			}

			if ($export)
			{
				return $values;
			}
			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		function get_uicols_at_location()
		{

			$uicols = array();

			$uicols['name'][]		 = 'location_code';
			$uicols['descr'][]		 = lang('location');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'loc1_name';
			$uicols['descr'][]		 = lang('name');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'owner';
			$uicols['descr'][]		 = lang('owner');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'location_category';
			$uicols['descr'][]		 = lang('category');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'document_name';
			$uicols['descr'][]		 = lang('Document name');
			$uicols['datatype'][]	 = 'link';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'title';
			$uicols['descr'][]		 = lang('Title');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'doc_type';
			$uicols['descr'][]		 = lang('Doc type');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'user';
			$uicols['descr'][]		 = lang('coordinator');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'id';
			$uicols['descr'][]		 = lang('document id');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			$uicols['name'][]		 = 'document_date';
			$uicols['descr'][]		 = lang('document date');
			$uicols['datatype'][]	 = 'text';
			$uicols['sortable'][]	 = true;
			return $uicols;
		}

		function download()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$list	 = $this->query_at_location();
			$uicols = $this->get_uicols_at_location();
			
			$uicols['name'][]		 = 'link';
			$uicols['descr'][]		 = lang('link');
			$uicols['datatype'][]	 = 'text';


			$this->bocommon->download($list, $uicols['name'], $uicols['descr'], $uicols['input_type']);
		}

		function list_doc()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_at_location();
			}

			$preserve = phpgw::get_var('preserve', 'bool');

			if ($preserve)
			{
				$this->bo->read_sessiondata();

				$this->filter	 = $this->bo->filter;
				$this->entity_id = $this->bo->entity_id;
				$this->cat_id	 = $this->bo->cat_id;
				$this->status_id = $this->bo->status_id;
			}
			$location_code = phpgw::get_var('location_code');
			if ($this->query_location)
			{
				$location_code = $this->query_location;
			}

			$categories		 = $this->cats->formatted_xslt_list(array('format'	 => 'filter',
				'selected'	 => $this->doc_type, 'globals'	 => True));
			$default_value	 = array('cat_id' => '', 'name' => lang('no document type'));
			array_unshift($categories['cat_list'], $default_value);
			foreach ($categories['cat_list'] as &$cat)
			{
				$cat['id'] = $cat['cat_id'];
			}


			$status_list	 = $this->bo->select_status_list('select', $this->status_id);
			$default_value	 = array('id' => '', 'name' => lang('no status'));
			array_unshift($status_list, $default_value);

			$datatable_name		 = array();
			$datatable_name[]	 = lang('documents');
			if ($location_code)
			{
				$solocation		 = CreateObject('property.solocation');
				$location_data	 = $solocation->read_single($location_code);

				$location_types	 = execMethod('property.soadmin_location.select_location_type');
				$type_id		 = count(explode('-', $location_code));

				for ($i = 1; $i < $type_id + 1; $i++)
				{
//					$address_element[] = array
//						(
//						'text' => $location_types[($i - 1)]['name'],
//						'value' => $location_data["loc{$i}"] . '  ' . $location_data["loc{$i}_name"]
//					);
					$datatable_name[] = "{$location_types[($i - 1)]['name']} [{$location_data["loc{$i}"]} - {$location_data["loc{$i}_name"]}]";
				}
			}

			$data = array(
				'datatable_name' => implode('::', $datatable_name),
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array('type'	 => 'filter',
								'name'	 => 'doc_type',
								'text'	 => lang('doc type'),
								'list'	 => $categories['cat_list'],
							),
							array('type'	 => 'filter',
								'name'	 => 'status_id',
								'text'	 => lang('status'),
								'list'	 => $status_list,
							),
						),
					),
				),
				'datatable'		 => array(
					'source'	 => self::link(array(
						'menuaction'		 => 'property.uidocument.list_doc',
						'entity_id'			 => $this->entity_id,
						'cat_id'			 => $this->cat_id,
						'p_num'				 => $p_num,
						'doc_type'			 => $this->doc_type,
						'location_code'		 => $location_code,
						'phpgw_return_as'	 => 'json'
						)
					),
					'allrows'	 => true,
					'download'	 => self::link(array('menuaction' => 'property.uidocument.download',
						'doc_type'	 => $this->doc_type,
						'entity_id'	 => $this->entity_id,
						'location_code'	 => $location_code,
						'export'	 => true,
						'allrows'	 => true
						)
					),
					'query'		 => $this->query,
					'field'		 => array(),
				),
			);

			if ($this->acl_add)
			{
				$data['datatable']['new_item'] = self::link(array(
						'menuaction'	 => 'property.uidocument.edit',
						'from'			 => 'property.uidocument.list_doc',
						'location_code'	 => $location_code,
						'p_entity_id'	 => $this->entity_id,
						'p_cat_id'		 => $this->cat_id,
						'p_num'			 => $p_num
						)
				);
			}
			$uicols = $this->get_uicols_at_location();

			$count_uicols_name = count($uicols['name']);


			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key'		 => $uicols['name'][$k],
					'label'		 => $uicols['descr'][$k],
					'sortable'	 => ($uicols['sortable'][$k]) ? true : false,
					'hidden'	 => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if ($uicols['name'][$k] == 'document_name')
				{
					$params['formatter'] = 'JqueryPortico.formatLink';
					$params['hidden']	 = false;
				}

				$denied = array('merknad');
				if (in_array($uicols['name'][$k], $denied))
				{
					$params['sortable'] = false;
				}

				array_push($data['datatable']['field'], $params);
			}

			$data['datatable']['actions'] = array();

			$parameters = array
				(
				'parameter' => array(
					array(
						'name'	 => 'id',
						'source' => 'id'
					)
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array(
					'my_name'	 => 'view',
					'statustext' => lang('view this entity'),
					'text'		 => lang('view'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uidocument.view',
						'from'		 => 'property.uidocument.list_doc'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_edit)
			{
				$data['datatable']['actions'][] = array(
					'my_name'	 => 'edit',
					'statustext' => lang('edit this entity'),
					'text'		 => lang('edit'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uidocument.edit',
						'from'		 => 'property.uidocument.list_doc'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array(
					'my_name'		 => 'delete',
					'statustext'	 => lang('delete this entity'),
					'text'			 => lang('delete'),
					'confirm_msg'	 => lang('do you really want to delete this entry'),
					'action'		 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction'	 => 'property.uidocument.delete',
						'location_code'	 => $location_code,
						'p_num'			 => $p_num
					)),
					'parameters'	 => json_encode($parameters)
				);
			}

			self::render_template_xsl('datatable_jquery', $data);
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $this->acl_location));
			}

			$id = phpgw::get_var('id', 'int');

			$file = $this->bo->get_file($id);

			$this->bofiles->view_file('', $file);
		}

		function edit()
		{
			if (!$this->acl_add && !$this->acl_edit)
			{
				phpgw::no_access();
			}

			$_from	 = phpgw::get_var('from');
			$id		 = phpgw::get_var('id', 'int');
			//			$location_code 		= phpgw::get_var('location_code');
			$values	 = phpgw::get_var('values');

			if (!$_from)
			{
				$from = 'property.uidocument.index';
			}
			else
			{
				$from = $_from;
			}

			$bypass = phpgw::get_var('bypass', 'bool');

			$receipt = array();

			if ($_POST && !$bypass)
			{
				$insert_record			 = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
				$insert_record_entity	 = (array)$GLOBALS['phpgw']->session->appsession('insert_record_entity', 'property');

				for ($j = 0; $j < count($insert_record_entity); $j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
				}

				$values = $this->bocommon->collect_locationdata($values, $insert_record);
			}
			else
			{
				$location_code								 = phpgw::get_var('location_code');
				$p_entity_id								 = phpgw::get_var('p_entity_id', 'int');
				$p_cat_id									 = phpgw::get_var('p_cat_id', 'int');
				$this->entity_id							 = $p_entity_id;
				$this->cat_id								 = $p_cat_id;

				$values['p'][$p_entity_id]['p_entity_id']	 = $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		 = $p_cat_id;
				$values['p'][$p_entity_id]['p_num']			 = phpgw::get_var('p_num');
				$values['p_entity_id']						 = $p_entity_id;
				$values['p_cat_id']							 = $p_cat_id;

				if ($p_entity_id && $p_cat_id)
				{
					$entity_category						 = $this->boadmin_entity->read_single_category($p_entity_id, $p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if (phpgw::get_var('p_num'))
				{
					$_values		 = execMethod('property.soentity.read_single', array('entity_id'	 => $p_entity_id,
						'cat_id'	 => $p_cat_id, 'id'		 => phpgw::get_var('p_num')));
					$location		 = $this->bo->read_location_data($_values['location_code']);
					$location_code	 = $_values['location_code'];
					unset($_values);
				}

				if ($location_code)
				{
					$values['location_data'] = $this->bolocation->read_single($location_code, array(
						'view' => true));
				}
			}

			if ($values['extra']['p_entity_id'])
			{
				$this->entity_id = $values['extra']['p_entity_id'];
				$this->cat_id	 = $values['extra']['p_cat_id'];
				$p_num			 = $values['extra']['p_num'];
			}

			/*
			  if($this->cat_id)
			  {
			  $entity = $this->boadmin_entity->read_single($this->entity_id,false);
			  $category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
			  $values['entity_name']=$entity['name'];
			  $values['category_name']=$category['name'];
			  }
			 */
			if ($values['save'] || $values['apply'])
			{
				$values['vendor_id'] = phpgw::get_var('vendor_id', 'int', 'POST');

				if ($values['link'])
				{
					$values['document_name'] = null;
				}

				if ((!$values['document_name'] && !$values['document_name_orig']) && !$values['link'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a file to upload !'));
				}

				if (!$this->bofiles->vfs->securitycheck(array('string' => $values['document_name'])))
				{
					$receipt['error'][] = array('msg' => lang('Invalid file name !'));
				}

				if (!$values['doc_type'])
				{
					$receipt['error'][]	 = array('msg' => lang('Please select a category !'));
					$error_id			 = true;
				}

				if (!$values['status'])
				{
					//					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}
				if (!$values['location'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a location !'));
				}

				$values['location_code'] = isset($values['location_code']) && $values['location_code'] ? $values['location_code'] : implode('-', $values['location']);


				if ($id && !$receipt['error'])
				{
					$values['id'] = $id;
					$old_file = $this->bo->get_file($id);
					$old_values = $this->bo->read_single($id);

					if($old_file && $old_values['doc_type'] != $values['doc_type'])
					{
						$document_dir = "document/{$values['location_code']}";
						if ($values['extra']['p_num'])
						{
							$document_dir = "document/entity_{$this->entity_id}_{$this->cat_id}/{$values['extra']['p_num']}";
						}
						$document_dir .= "/{$values['doc_type']}";

						$this->bofiles->create_document_dir($document_dir);
						$this->bofiles->vfs->override_acl = 1;

						if (!$this->bofiles->vfs->mv(array(
								'from'		 => $old_file,
								'to'		 => "{$this->bofiles->fakebase}/{$document_dir}/" . basename($old_file),
								'relatives'	 => array(RELATIVE_ALL, RELATIVE_ALL))))
						{
							$receipt['error'][] = array('msg' => lang('Failed to move file !'));
						}
						$this->bofiles->vfs->override_acl = 0;
					}
				}
				else if ($receipt['error'] && phpgw::get_var('phpgw_return_as') == 'json')
				{
					return array(
						'status'		 => 'error',
						'id'			 => null,
						'message'		 => $receipt['error'],
						'redirect_link'	 => null
					);
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save($values);
					$GLOBALS['phpgw']->session->appsession('session_data', 'document_receipt', $receipt);

					$redirect_link = $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => $_from ?  $from : 'property.uidocument.list_doc',
						'location_code'	 => implode("-", $values['location']),
						'entity_id'		 => $this->entity_id,
						'cat_id'		 => $this->cat_id,
						'p_num'			 => $values['extra']['p_num'],
						'id'			 => $this->entity_id ? $values['extra']['p_num'] : ''
					));

					if (phpgw::get_var('phpgw_return_as') == 'json')
					{
						return array(
							'status'		 => 'saved',
							'id'			 => $receipt['id'],
							'message'		 => isset($receipt['error']) && $receipt['error'] ? implode(', ', $receipt['error']) : '',
							'redirect_link'	 => $redirect_link
						);
					}
					else
					{
						header('Location: ' . $redirect_link);
					}
				}
				else
				{
					$values['document_name'] = '';
					if ($values['location'])
					{
						//				$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $this->bolocation->read_single($values['location_code'], $values['extra']);
					}
					if ($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']		 = $values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id'] = $values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']	 = $values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']	 = $_POST['entity_cat_name_' . $values['extra']['p_entity_id']];
					}
				}
			}

			if ($id || (!$receipt['error'] && $values['id']))
			{
				$values			 = $this->bo->read_single($id);
				$record_history	 = $this->bo->read_record_history($id);
				$function_msg	 = lang('Edit document');
			}
			else
			{
				$function_msg = lang('Add document');
			}

			$datatable_def = array();

			//---datatable settings---------------------------------------------------

			$datatable_def[] = array(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key'		 => 'value_date', 'label'		 => lang('Date'),
						'sortable'	 => true,
						'resizeable' => true),
					array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
					array('key'		 => 'value_action', 'label'		 => lang('Action'), 'sortable'	 => true,
						'resizeable' => true),
					array('key'		 => 'value_old_value', 'label'		 => lang('old value'), 'sortable'	 => true,
						'resizeable' => true),
					array('key'		 => 'value_new_value', 'label'		 => lang('New value'), 'sortable'	 => true,
						'resizeable' => true)),
				'data'		 => json_encode($record_history),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			if ($values['doc_type'])
			{
				$this->doc_type = $values['doc_type'];
			}
			if ($values['location_code'])
			{
				$location_code = $values['location_code'];
			}
			/* 			if ($values['p_num'])
			  {
			  $p_num = $values['p_num'];
			  }
			 */
			$location_data = $this->bolocation->initiate_ui_location(array(
				'values'		 => $values['location_data'],
				'type_id'		 => -1, // calculated from location_types
				'no_link'		 => false, // disable lookup links for location type less than type_id
				'tenant'		 => false,
				'required_level' => 1,
				'lookup_type'	 => 'form2',
				'lookup_entity'	 => $this->bocommon->get_lookup_entity('document'),
				'entity_data'	 => isset($values['p']) ? $values['p'] : ''
			));

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id'		 => $values['vendor_id'],
				'vendor_name'	 => $values['vendor_name']));

			$link_data = array(
				'menuaction'	 => 'property.uidocument.edit',
				'id'	 => $id,
				'from'			 => $from,
				'location_code'	 => $values['location_code'],
				'entity_id'		 => $this->entity_id,
				'cat_id'		 => $this->cat_id,
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$GLOBALS['phpgw']->jqcal->add_listener('values_document_date');

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('General'), 'link' => '#general');
			$tabs['history'] = array('label' => lang('History'), 'link' => '#history');
			$active_tab		 = 'general';

			$data = array(
				'datatable_def'					 => $datatable_def,
				'msgbox_data'					 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'vendor_data'					 => $vendor_data,
				'record_history'				 => $record_history,
				'table_header_history'			 => $table_header_history,
				'lang_history'					 => lang('History'),
				'lang_no_history'				 => lang('No history'),
				'value_document_date'			 => $values['document_date'],
				'vendor_data'					 => $vendor_data,
				'location_data2'				 => $location_data,
				'location_type'					 => 'form',
				'form_action'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_year'						 => lang('Year'),
				'done_action'					 => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction'	 => $from,
					'location_code'	 => $location_code,
					'entity_id'		 => $this->entity_id,
					'cat_id'		 => $this->cat_id,
					'p_num'			 => $p_num,
					'preserve'		 => 1,
					'id'			 => $this->entity_id ? ($values['extra']['p_num'] ? $values['extra']['p_num'] : phpgw::get_var('p_num')) : ''
					)
				),
				'value_id'				 => $id,
				'value_document_name'			 => $values['document_name'],
				'value_floor_id'				 => $values['floor_id'],
				'value_title'					 => $values['title'],
				'value_version'					 => $values['version'],
				'value_link'					 => $values['link'],
				'value_descr'					 => $values['descr'],
				'lang_no_cat'					 => lang('Select category'),
				'lang_cat_statustext'			 => lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
				'value_cat_id'					 => $values['doc_type'],
				'cat_select'					 => $this->cats->formatted_xslt_list(
					array(
						'select_name'	 => 'values[doc_type]',
						'selected'		 => $values['doc_type'] ? $values['doc_type'] : $this->doc_type,
						'required'		 => true)),
				'lang_user_statustext'			 => lang('Select the coordinator the document belongs to. To do not use a category select NO USER'),
				'select_user_name'				 => 'values[coordinator]',
				'lang_no_user'					 => lang('Select coordinator'),
				'user_list'						 => $this->bocommon->get_user_list_right2('select', 4, $values['coordinator'] ? $values['coordinator'] : $this->account, $this->acl_location),
				'status_list'					 => $this->bo->select_status_list('select', $values['status']),
				'status_name'					 => 'values[status]',
				'status_required'				 => true,
				'lang_no_status'				 => lang('Select status'),
				'lang_status_statustext'		 => lang('What is the current status of this document ?'),
				'value_location_code'			 => $values['location_code'],
				'branch_list'					 => $this->bo->select_branch_list($values['branch_id']),
				'multi_upload_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uidocument.handle_multi_upload_file','id' => $id)),
				'tabs'							 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
			);
			//_debug_array($data);
			phpgwapi_jquery::formvalidator_generate(array('date', 'security',
				'file'));
			phpgwapi_jquery::load_widget('file-upload-minimum');
			self::add_javascript('property', 'base', 'document.edit.js', false, array('combine' => false ));

			$appname = lang('document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->add_file(array('document','multi_upload_file_inline', 'datatable_inline'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit' => $data));
		}

		public function handle_multi_upload_file()
		{
			if (!$this->acl_add && !$this->acl_edit)
			{
				phpgw::no_access();
			}

			$id = phpgw::get_var('id', 'int', 'GET');

			$values			 = $this->bo->read_single($id);

			phpgw::import_class('property.multiuploader');

			$location_code = isset($values['location_code']) && $values['location_code'] ? $values['location_code'] : implode('-', $values['location']);

			$document_dir = "document/{$location_code}";

			if ($values['p_num'])
			{
				$document_dir = "document/entity_{$values['p_entity_id']}_{$values['p_cat_id']}/{$values['p_num']}";
			}

			$document_dir .= "/{$values['doc_type']}";
			$this->bofiles->create_document_dir($document_dir);

			$options = array();
			$options['base_dir']	 = $document_dir;
			$options['upload_dir']	 = "{$GLOBALS['phpgw_info']['server']['files_dir']}/property/{$options['base_dir']}/";
			$options['script_url']	 = html_entity_decode(self::link(array('menuaction' => 'property.uidocument.handle_multi_upload_file',
					'id'		 => $id)));
			$upload_handler			 = new property_multiuploader($options, false);
			
			if(!$this->bofiles->vfs->securitycheck(array('string' => "/property/{$options['base_dir']}/" . $_FILES['files']['name'][0])))
			{
				$response = array('files' => array(array('error' => lang('Invalid file name !'))));
				$upload_handler->generate_response($response);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
		
			$old_file = $this->bo->get_file($id);

			if ($old_file == "/property/{$options['base_dir']}/" . $_FILES['files']['name'][0])
			{				
				$this->bofiles->vfs->override_acl = 1;

				if (!$this->bofiles->vfs->rm(array(
						'string'	 => $old_file,
						'relatives'	 => array(
							RELATIVE_NONE
						)
					)))
				{
					$response = array('files' => array(array('error' => lang('failed to delete file') . ' :' . $old_file)));
					$upload_handler->generate_response($response);
					$GLOBALS['phpgw']->common->phpgw_exit();
				}

				$this->bofiles->vfs->override_acl = 0;
			}

			if(!$id)
			{
				$response = array('files' => array(array('error' => 'missing id in request')));
				$upload_handler->generate_response($response);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

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
					if($this->simple)
					{
						$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
					}
					else
					{
						$upload_handler->delete_file();
					}
					break;
				default:
					$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		public function build_multi_upload_file()
		{
			phpgwapi_jquery::init_multi_upload_file();
			$id = phpgw::get_var('id', 'int');

			$GLOBALS['phpgw_info']['flags']['noframework']	 = true;
			$GLOBALS['phpgw_info']['flags']['nofooter']		 = true;

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uidocument.handle_multi_upload_file',
				'id'		 => $id));

			$data = array(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}

		function delete()
		{
			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 8, 'acl_location'	 => $this->acl_location));
			}

			$location_code	 = phpgw::get_var('location_code');
			$p_num			 = phpgw::get_var('p_num');
			$id	 = phpgw::get_var('id', 'int');
			$confirm		 = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array(
				'menuaction'	 => 'property.uidocument.list_doc',
	//			'location_code'	 => $location_code,
				'p_num'			 => $p_num
			);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($id);
				return "id " . $id . " " . lang("has been deleted");
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uidocument.delete',
					'id'	 => $id, 'location_code'	 => $location_code, 'p_num'			 => $p_num)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_statustext'	 => lang('Delete the entry'),
				'lang_no_statustext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('document');
			$function_msg	 = lang('delete document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{

			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			$_from		 = phpgw::get_var('from');
			$id = phpgw::get_var('id', 'int');

			if (!$_from)
			{
				$from = 'property.uidocument.index';
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('document', 'datatable_inline'));

			$values			 = $this->bo->read_single($id);
			$function_msg	 = lang('view document');
			$record_history	 = $this->bo->read_record_history($id);

			$datatable_def = array();

			//---datatable settings---------------------------------------------------

			$datatable_def[] = array
				(
				'container'	 => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key'		 => 'value_date', 'label'		 => lang('Date'),
						'sortable'	 => true,
						'resizeable' => true),
					array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
					array('key'		 => 'value_action', 'label'		 => lang('Action'), 'sortable'	 => true,
						'resizeable' => true),
					array('key'		 => 'value_old_value', 'label'		 => lang('old value'), 'sortable'	 => true,
						'resizeable' => true),
					array('key'		 => 'value_new_value', 'label'		 => lang('New value'), 'sortable'	 => true,
						'resizeable' => true)),
				'data'		 => json_encode($record_history),
				'config'	 => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			if ($values['doc_type'])
			{
				$this->cat_id = $values['doc_type'];
			}

			$location_data = $this->bolocation->initiate_ui_location(array
				(
				'values'		 => $values['location_data'],
				'type_id'		 => count(explode('-', $values['location_data']['location_code'])),
				'no_link'		 => false, // disable lookup links for location type less than type_id
				'tenant'		 => false,
				'lookup_type'	 => 'view',
				'lookup_entity'	 => $this->bocommon->get_lookup_entity('document'),
				'entity_data'	 => $values['p']
			));


			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'type'			 => 'view',
				'vendor_id'		 => $values['vendor_id'],
				'vendor_name'	 => $values['vendor_name']));


			$link_data = array
				(
				'menuaction'	 => 'property.uidocument.edit',
				'id'	 => $id
			);

			$categories = $this->cats->formatted_xslt_list(array('selected' => $values['doc_type']));

			$data = array
				(
				'datatable_def'					 => $datatable_def,
				'vendor_data'					 => $vendor_data,
				'table_header_history'			 => $table_header_history,
				'lang_history'					 => lang('History'),
				'lang_no_history'				 => lang('No history'),
				'lang_document_date'			 => lang('document date'),
				'value_document_date'			 => $values['document_date'],
				'vendor_data'					 => $vendor_data,
				'location_data'					 => $location_data,
				'location_type'					 => 'form',
				'form_action'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $from,
					'location_code'	 => $values['location_code'], 'entity_id'		 => $values['p_entity_id'],
					'cat_id'		 => $values['p_cat_id'], 'preserve'		 => 1)),
				'lang_year'						 => lang('Year'),
				'lang_category'					 => lang('category'),
				'lang_save'						 => lang('save'),
				'lang_done'						 => lang('done'),
				'lang_update_file'				 => lang('Update file'),
				'lang_id'				 => lang('document ID'),
				'value_id'				 => $id,
				'lang_document_name'			 => lang('document name'),
				'value_document_name'			 => $values['document_name'],
				'lang_document_name_statustext'	 => lang('Enter document Name'),
				'lang_floor_id'					 => lang('Floor ID'),
				'value_floor_id'				 => $values['floor_id'],
				'lang_floor_statustext'			 => lang('Enter the floor ID'),
				'lang_title'					 => lang('title'),
				'value_title'					 => $values['title'],
				'lang_title_statustext'			 => lang('Enter document title'),
				'lang_version'					 => lang('Version'),
				'value_version'					 => $values['version'],
				'lang_version_statustext'		 => lang('Enter document version'),
				'lang_descr_statustext'			 => lang('Enter a description of the document'),
				'lang_descr'					 => lang('Description'),
				'value_descr'					 => $values['descr'],
				'lang_done_statustext'			 => lang('Back to the list'),
				'cat_list'						 => $categories['cat_list'],
				'lang_coordinator'				 => lang('Coordinator'),
				'lang_user_statustext'			 => lang('Select the coordinator the document belongs to. To do not use a category select NO USER'),
				'select_user_name'				 => 'values[coordinator]',
				'lang_no_user'					 => lang('Select coordinator'),
				'user_list'						 => $this->bocommon->get_user_list('select', $values['coordinator'], $extra							 = false, $default						 = false, $start							 = -1, $sort							 = 'ASC', $order							 = 'account_lastname', $query							 = '', $offset							 = -1),
				'status_list'					 => $this->bo->select_status_list('select', $values['status']),
				'status_name'					 => 'values[status]',
				'lang_no_status'				 => lang('Select status'),
				'lang_status'					 => lang('Status'),
				'lang_status_statustext'		 => lang('What is the current status of this document ?'),
				'branch_list'					 => $this->bo->select_branch_list($values['branch_id']),
				'lang_no_branch'				 => lang('No branch'),
				'lang_branch'					 => lang('branch'),
				'lang_branch_statustext'		 => lang('Select the branch for this document'),
				'edit_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uidocument.edit',
					'id'	 => $id, 'from'			 => $from)),
				'lang_edit_statustext'			 => lang('Edit this entry'),
				'lang_edit'						 => lang('Edit')
			);

			$appname = lang('document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('view' => $data));
		}
	}