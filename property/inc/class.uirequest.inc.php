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
	 * @subpackage project
	 * @version $Id$
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	/**
	 * Description
	 * @package property
	 */
	class property_uirequest extends phpgwapi_uicommon_jquery
	{

		private $receipt = array();
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
		var $nonavbar;
		var $public_functions = array
			(
			'index' => true,
			'view' => true,
			'edit' => true,
			'add' => true,
			'save' => true,
			'delete' => true,
			'priority_key' => true,
			'view_file' => true,
			'download' => true,
			'columns' => true,
			'get_related' => true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true,
			'get_files'				=> true,
			'view_image'			=> true,
		);

		public function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project::request';
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo = CreateObject('property.borequest', true);
			$this->boproject = CreateObject('property.boproject');
			$this->bocommon = & $this->bo->bocommon;
			$this->cats = & $this->bo->cats;
			$this->bolocation = CreateObject('property.bolocation');
			$this->config = CreateObject('phpgwapi.config', 'property');
			$this->config->read();
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = $this->bo->acl_location;
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check($this->acl_location, 16, 'property');

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->property_cat_id = $this->bo->property_cat_id;
			$this->cat_id = $this->bo->cat_id;
			$this->status_id = $this->bo->status_id;
			$this->degree_id = $this->bo->degree_id;
			$this->district_id = $this->bo->district_id;
			$this->start_date = $this->bo->start_date;
			$this->end_date = $this->bo->end_date;
			$this->building_part = $this->bo->building_part;
			$this->allrows = $this->bo->allrows;
			$this->p_num = $this->bo->p_num;
			$this->condition_survey_id = $this->bo->condition_survey_id;
			$this->nonavbar = phpgw::get_var('nonavbar', 'bool');
			$this->responsible_unit = $this->bo->responsible_unit;
			$this->recommended_year = $this->bo->recommended_year;


			if ($this->nonavbar)
			{
				$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}
		}

		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$make_relation =  phpgw::get_var('make_relation', 'bool');
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$start_date = urldecode(phpgw::get_var('start_date'));
			$end_date = urldecode(phpgw::get_var('end_date'));
			$list_descr = phpgw::get_var('list_descr', 'bool');
			$export = phpgw::get_var('export', 'bool');

			$query = phpgw::get_var('query');
			if (!empty($query))
			{
				$search['value'] = $query;
			}

			if ($start_date && empty($end_date))
			{
				$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $dateformat);
			}

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'make_relation' => $make_relation,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'list_descr' => $list_descr
			);

			$values = $this->bo->read($params);
			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;
			$result_data['amount_investment'] = number_format($this->bo->sum_investment, 0, ',', '.');
			$result_data['amount_operation'] = number_format($this->bo->sum_operation, 0, ',', '.');
			$result_data['amount_potential_grants'] = number_format($this->bo->sum_potential_grants, 0, ',', '.');
			$result_data['consume'] = number_format($this->bo->sum_consume, 0, ',', '.');

			return $this->jquery_results($result_data);
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start' => $this->start,
				'query' => $this->query,
				'sort' => $this->sort,
				'order' => $this->order,
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'status_id' => $this->status_id,
				'degree_id' => $this->degree_id,
				'district_id' => $this->district_id,
				'allrows' => $this->allrows,
				'start_date' => $this->start_date,
				'end_date' => $this->end_date,
				'property_cat_id' => $this->property_cat_id,
				'building_part' => $this->building_part,
				'responsible_unit' => $this->responsible_unit,
				'recommended_year' => $this->recommended_year
			);
			$this->bo->save_sessiondata($data);
		}


		function get_files()
		{
			$id = phpgw::get_var('id', 'int');

			if (!$this->acl_read)
			{
				return;
			}

			$link_file_data = array
			(
				'menuaction' => 'property.uirequest.view_file',
			);


			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$values = $this->bo->get_files($id);

			$content_files = array();
			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$z = 0;
			foreach ($values as $_entry)
			{
				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
					'attach_file' => '<input type="checkbox" name="values[file_attach][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to attach file') . '">'
				);
				if ( in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name'] = $_entry['name'];
					$content_files[$z]['img_id'] = $_entry['file_id'];
					$content_files[$z]['img_url'] = self::link(array(
							'menuaction' => 'property.uirequest.view_image',
							'img_id'	=>  $_entry['file_id'],
							'file' => $_entry['directory'] . '/' . $_entry['file_name']
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

		public function handle_multi_upload_file()
		{
			$id = phpgw::get_var('id');

			phpgw::import_class('property.multiuploader');

			$options['base_dir'] = 'request/'.$id;
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/property/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => 'property.uirequest.handle_multi_upload_file', 'id' => $id)));
			$upload_handler = new property_multiuploader($options, false);

			switch ($_SERVER['REQUEST_METHOD']) {
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

		public function build_multi_upload_file()
		{
			phpgwapi_jquery::init_multi_upload_file();
			$id = phpgw::get_var('id', 'int');

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uirequest.handle_multi_upload_file', 'id' => $id));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}

		function view_image()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$thumb = phpgw::get_var('thumb', 'bool');
			$img_id = phpgw::get_var('img_id', 'int');

			$bofiles = CreateObject('property.bofiles');

			if($img_id)
			{
				$file_info = $bofiles->vfs->get_info($img_id);
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
				$uigallery->create_thumb($source, $thumbfile, $thumb_size = 100);
				readfile($thumbfile);
			}
			else if ($thumb && is_file($thumbfile))
			{
				readfile($thumbfile);
			}
			else if ($uigallery->is_image($source) && $thumb)
			{
				$uigallery->create_thumb($source, $thumbfile, $thumb_size = 100);
				readfile($thumbfile);
			}
			else if ($img_id)
			{
				$bofiles->get_file($img_id);
			}
			else
			{
				$bofiles->view_file('', $file);
			}
		}

		private function _get_filters()
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bocommon->select_category_list(array
				(
				'format' => 'filter',
				'type' => 'location',
				'type_id' => 1,
				'order' => 'descr'
			));

			if (count($values_combo_box[0]))
			{
				$default_value = array('id' => '', 'name' => lang('no type'));
				array_unshift($values_combo_box[0], $default_value);
				$combos[] = array
					(
					'type' => 'filter',
					'name' => 'property_cat_id',
					'extra' => '',
					'text' => lang('property type'),
					'list' => $values_combo_box[0]
				);
			}
			else
			{
				unset($values_combo_box[0]);
			}

			$count = count($values_combo_box);
			$values_combo_box[$count] = $this->bocommon->select_district_list('filter', $this->district_id);
			$default_value = array('id' => '', 'name' => lang('no district'));
			array_unshift($values_combo_box[$count], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'district_id',
				'extra' => '',
				'text' => lang('district'),
				'list' => $values_combo_box[$count]
			);

			$count = count($values_combo_box);
			$categories = $this->cats->formatted_xslt_list(array('select_name' => 'cat_id',
				'selected' => $this->cat_id, 'globals' => True));
			$default_value = array('cat_id' => '', 'name' => lang('no category'));
			array_unshift($categories['cat_list'], $default_value);
			foreach ($categories['cat_list'] as & $_category)
			{
				$_category['id'] = $_category['cat_id'];
			}
			$values_combo_box[$count] = $categories['cat_list'];

			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'extra' => '',
				'text' => lang('category'),
				'list' => $values_combo_box[$count]
			);

			$count = count($values_combo_box);
			$values_combo_box[$count] = $this->bo->select_status_list('filter');
			array_unshift($values_combo_box[$count], array('id' => 'all', 'name' => lang('all')));
			array_unshift($values_combo_box[$count], array('id' => 'open', 'name' => lang('open')));
			foreach ($values_combo_box[$count] as &$_entry)
			{
				$_entry['selected'] = $_entry['id'] == $this->status_id ? 1 : 0;
			}
			unset($_entry);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'status_id',
				'extra' => '',
				'text' => lang('Status'),
				'list' => $values_combo_box[$count]
			);

			$count = count($values_combo_box);
			$values_combo_box[$count] = $this->bo->select_degree_list();
			foreach ($values_combo_box[$count] as &$_degree)
			{
				$_degree['id'] ++;
			}
			array_unshift($values_combo_box[$count], array('id' => '', 'name' => lang('condition degree')));
			foreach ($values_combo_box[$count] as &$_entry)
			{
				$_entry['selected'] = $_entry['id'] == $this->degree_id ? 1 : 0;
			}
			unset($_entry);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'degree_id',
				'extra' => '',
				'text' => lang('condition degree'),
				'list' => $values_combo_box[$count]
			);

			$count = count($values_combo_box);
			$_filter_buildingpart = array();
			$filter_buildingpart = isset($this->bo->config->config_data['filter_buildingpart']) ? $this->bo->config->config_data['filter_buildingpart'] : array();

			if ($filter_key = array_search('.project.request', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

			$building_part_list = $this->bocommon->select_category_list(array('type' => 'building_part',
				'selected' => $this->building_part, 'order' => 'id', 'id_in_name' => 'num', 'filter' => $_filter_buildingpart));
			array_unshift($building_part_list, array('id' => '', 'name' => lang('building part')));
			$values_combo_box[$count] = $building_part_list;

			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'building_part',
				'extra' => '',
				'text' => lang('building part'),
				'list' => $values_combo_box[$count]
			);

			$count = count($values_combo_box);
			$responsible_unit_list = $this->bocommon->select_category_list(array('type' => 'request_responsible_unit',
				'selected' => $this->responsible_unit, 'order' => 'id', 'fields' => array('descr')));
			array_unshift($responsible_unit_list, array('id' => '0', 'name' => lang('responsible unit')));
			$values_combo_box[$count] = $responsible_unit_list;
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'responsible_unit',
				'extra' => '',
				'text' => lang('responsible unit'),
				'list' => $values_combo_box[$count]
			);

			$count = count($values_combo_box);
			$recommended_year_list = $this->bo->get_recommended_year_list($this->recommended_year);
			array_unshift($recommended_year_list, array('id' => '0', 'name' => lang('recommended year')));
			$values_combo_box[$count] = $recommended_year_list;
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'recommended_year',
				'extra' => '',
				'text' => lang('year'),
				'list' => $values_combo_box[$count]
			);

			$count = count($values_combo_box);
			$values_combo_box[$count] = $this->bo->get_user_list();
			foreach ($values_combo_box[$count] as &$valor)
			{
				$valor['id'] = $valor['user_id'];
				unset($valor['user_id']);
			}
			array_unshift($values_combo_box[$count], array('id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'name' => $GLOBALS['phpgw_info']['user']['fullname']));
			$default_value = array('id' => '', 'name' => lang('no user'));
			array_unshift($values_combo_box[$count], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'filter',
				'extra' => '',
				'text' => lang('user'),
				'list' => $values_combo_box[$count]
			);

			return $combos;
		}

		private function _populate()
		{
			$id = phpgw::get_var('id', 'int');
			$values = phpgw::get_var('values');
			$values_attribute = phpgw::get_var('values_attribute');

			$bypass = phpgw::get_var('bypass', 'bool');

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
			$insert_record_entity = $GLOBALS['phpgw']->session->appsession("insert_record_values{$this->acl_location}", 'property');

			for ($j = 0; $j < count($insert_record_entity); $j++)
			{
				$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
			}
			$values = $this->bocommon->collect_locationdata($values, $insert_record);

			if (!$values['location'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a location !'));
				$error_id = true;
			}

			if (!$values['title'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please enter a request TITLE !'));
				$error_id = true;
			}

			if (!$values['cat_id'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a category !'));
				$error_id = true;
			}

			if (!$values['status'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a status !'));
			}

			if (!$values['building_part'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a building part!'));
			}

			if ($values['consume_value'] && !$values['consume_date'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a date !'));
			}
			if ($values['planning_value'] && !$values['planning_date'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a date !'));
			}

			if (isset($values['amount_investment']) && $values['amount_investment'])
			{
				$values['amount_investment'] = str_replace(' ', '', $values['amount_investment']);
				if (!ctype_digit($values['amount_investment']))
				{
					$this->receipt['error'][] = array('msg' => lang('investment') . ': ' . lang('Please enter an integer !'));
					$error_id = true;
				}
			}
			if (isset($values['amount_operation']) && $values['amount_operation'])
			{
				$values['amount_operation'] = str_replace(' ', '', $values['amount_operation']);
				if (!ctype_digit($values['amount_operation']))
				{
					$this->receipt['error'][] = array('msg' => lang('operation') . ': ' . lang('Please enter an integer !'));
					$error_id = true;
				}
			}
			if (isset($values['amount_potential_grants']) && $values['amount_potential_grants'])
			{
				$values['amount_potential_grants'] = str_replace(' ', '', $values['amount_potential_grants']);
				if (!ctype_digit($values['amount_potential_grants']))
				{
					$this->receipt['error'][] = array('msg' => lang('potential grants') . ': ' . lang('Please enter an integer !'));
					$error_id = true;
				}
			}

			$_condition = array_keys($values['condition']);
			$__condition = isset($_condition[0]) && $_condition[0] ? $_condition[0] : 0;

			if (!isset($values['condition'][$__condition]['condition_type']) || !isset($values['condition'][$__condition]['degree']))
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a condition!'));
			}

			if (is_array($values_attribute))
			{
				foreach ($values_attribute as $attribute)
				{
					if ($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
					{
						$this->receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
					}
				}
			}

			if (!$id && $bypass)
			{
				$p_entity_id = phpgw::get_var('p_entity_id', 'int');
				$p_cat_id = phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id'] = $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id'] = $p_cat_id;
				$values['p'][$p_entity_id]['p_num'] = phpgw::get_var('p_num');
			}

			return $values;
		}

		private function _handle_files( $values )
		{
			$id = (int)$values['id'];
			if (empty($id))
			{
				throw new Exception('uirequest::_handle_files() - missing id');
			}

			$bofiles = CreateObject('property.bofiles');
			if (isset($values['file_action']) && is_array($values['file_action']))
			{
				$bofiles->delete_file("/request/{$id}/", $values);
			}

			$values['file_name'] = str_replace(" ", "_", $_FILES['file']['name']);
			$to_file = "{$bofiles->fakebase}/request/{$id}/{$values['file_name']}";

			if (!$values['document_name_orig'] && $bofiles->vfs->file_exists(array(
					'string' => $to_file,
					'relatives' => array(RELATIVE_NONE)
				)))
			{
				$this->receipt['error'][] = array('msg' => lang('This file already exists !'));
			}

			if ($values['file_name'])
			{
				$bofiles->create_document_dir("request/{$id}");
				$bofiles->vfs->override_acl = 1;

				if (!$bofiles->vfs->cp(array(
						'from' => $_FILES['file']['tmp_name'],
						'to' => $to_file,
						'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
				{
					$this->receipt['error'][] = array('msg' => lang('Failed to upload file !'));
				}
				$bofiles->vfs->override_acl = 0;
			}
		}

		function columns()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values = phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->add('property', 'request_columns', $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => 'property.uirequest.columns',
			);

			$selected = isset($values['columns']) && $values['columns'] ? $values['columns'] : array();
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list' => $this->bo->column_list($selected, $this->type_id, $allrows = true),
				'function_msg' => $function_msg,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_columns' => lang('columns'),
				'lang_none' => lang('None'),
				'lang_save' => lang('save'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('columns' => $data));
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

		function download()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$values = $this->query();
			$uicols = $this->bo->uicols;
			$this->bocommon->download($values, $uicols['name'], $uicols['descr'], $uicols['input_type']);
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			//$receipt = $GLOBALS['phpgw']->session->appsession('session_data', "general_receipt_{$this->type}_{$this->type_id}");
			//$this->save_sessiondata();

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = "general.index.{$this->type}";

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$make_relation =  phpgw::get_var('make_relation', 'bool'); // lookup for maintenance planning
			$relation_id = phpgw::get_var('relation_id', 'int');
			$relation_type = phpgw::get_var('relation_type');

			switch ($relation_type)
			{
				case 'ticket':
					$update_menuaction = 'property.uitts.view';
					$lang_update_relation = lang('update ticket');
					break;
				case 'project':
					$update_menuaction = 'property.uiproject.edit';
					$lang_update_relation = lang('update project');
					break;
				default:
					break;
			}

			$query = phpgw::get_var('query');

			if ($make_relation)
			{
				$lookup = true;
			}

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('datepicker');

			$appname = lang('request');
			$function_msg = lang('list request');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type' => 'link',
								'value' => lang('Priority key'),
								'href' => '#',
								'class' => '',
								'onclick' => "JqueryPortico.openPopup({menuaction:'property.uirequest.priority_key'})"
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'start_date',
								'name' => 'start_date',
								'value' => '',
								'text' => lang('from')
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'end_date',
								'name' => 'end_date',
								'value' => '',
								'text' => lang('to')
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uirequest.index',
						'lookup' => $lookup,
						'make_relation' => $make_relation,
						'relation_id' => $relation_id,
						'relation_type' => $relation_type,
						'nonavbar' => $this->nonavbar,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array('menuaction' => 'property.uirequest.download',
						'export' => true,
						'allrows' => true,
						'list_descr' => true)),
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'property.uirequest.columns'}, {closeAction:'reload'})"),
					'new_item' => self::link(array(
						'menuaction' => 'property.uirequest.add'
					)),
					'allrows' => true,
					'select_all' => $make_relation,
					'editor_action' => array(),
					'field' => array(),
					'query'	=> phpgw::get_var('query')
				)
			);

			$filters = $this->_get_filters();

			$custom = createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->find('property', $this->acl_location, 0, '', '', '', true, true);
			if ($attrib_data)
			{
				foreach ($attrib_data as $attrib)
				{
					if ($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'CH' || $attrib['datatype'] == 'R')
					{

						$_values = array();
						$_values[] = array('id' => '', 'name' => lang('select') . ' ' . $attrib['input_text']);
						foreach ($attrib['choice'] as $choice)
						{
							$_values[] = array
								(
								'id' => $choice['id'],
								'name' => htmlspecialchars($choice['value'], ENT_QUOTES, 'UTF-8'),
							);
						}

						$filters[] = array
							(
							'type' => 'filter',
							'id' => "sel_{$attrib['column_name']}",
							'name' => $attrib['column_name'],
							'extra' => '',
							'text' => lang($attrib['input_text']),
							'list' => $_values
						);
					}
				}
			}

			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$this->bo->read(array('make_relation' => $make_relation, 'allrows' => $this->allrows,
				'dry_run' => true));
			$uicols = $this->bo->uicols;
			//_debug_array($uicols);die();
			$count_uicols_name = count($uicols['name']);

			$type_id = 4;
			for ($i = 1; $i < $type_id; $i++)
			{
				$searc_levels[] = "loc{$i}";
			}

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if ($uicols['name'][$k] == 'request_id')
				{
					$params['formatter'] = 'linkToRequest';
				}

				if (in_array($uicols['name'][$k], $searc_levels))
				{
					$params['formatter'] = 'JqueryPortico.searchLink';
				}
				if ($uicols['name'][$k] == 'amount_investment')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'amount_operation')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'amount_potential_grants')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}

				array_push($data['datatable']['field'], $params);
			}

			if ($lookup)
			{
				$params = array(
					'key' => 'select',
					'label' => lang('Select'),
					'sortable' => false,
					'hidden' => false,
					'formatter' => 'formatRadio',
					'className' => 'dt-center all'
				);
				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'request_id'
					),
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'view',
					'text' => lang('view'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uirequest.view'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			if (!$lookup)
			{
				if ($this->acl_read)
				{
					$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

					foreach ($jasper as $report)
					{
						$data['datatable']['actions'][] = array
							(
							'my_name' => 'edit',
							'statustext' => lang('edit the actor'),
							'text' => lang('open JasperReport %1 in new window', $report['title']),
							'action' => $GLOBALS['phpgw']->link('/index.php', array
								(
								'menuaction' => 'property.uijasper.view',
								'jasper_id' => $report['id']
							)),
							'target' => '_blank',
							'parameters' => json_encode($parameters)
						);
					}
				}

				if ($this->acl_edit)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'edit',
						'statustext' => lang('edit the actor'),
						'text' => lang('edit'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uirequest.edit'
						)),
						'parameters' => json_encode($parameters)
					);
				}

				if ($this->acl_delete)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'delete',
						'statustext' => lang('delete the actor'),
						'text' => lang('delete'),
						'confirm_msg' => lang('do you really want to delete this entry'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uirequest.delete'
						)),
						'parameters' => json_encode($parameters)
					);
				}
				unset($parameters);
			}
			else
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'update_project',
					'statustext' => $lang_update_relation,
					'text' => $lang_update_relation,
					'type' => 'custom',
					'custom_code' => "
											
											var myChecks = $('.mychecks:checked');
											if (myChecks.length == 0) {
												alert('Any box selected');
												return;
											}

											for(i=0;i<myChecks.length;i++)
				{
												   $('<input>').attr({
													   type: 'hidden',
													   id: 'add_request[request_id][]',
													   name: 'add_request[request_id][]',
													   value: myChecks[i].value
												   }).appendTo('#custom_values_form');			 
			}

											var path_update = new Array();
											path_update['menuaction'] = '{$update_menuaction}';
											path_update['id'] = '{$relation_id}';

											var sUrl = phpGWLink('index.php', path_update);

											$('#custom_values_form').attr('action', sUrl);
											$('#custom_values_form').attr('method', 'POST');
											$('#custom_values_form').submit();"
				);

				if (!empty($query))
				{
					$code = <<<JS
						function initCompleteDatatable(oSettings, json, oTable) 
					{
							setTimeout(function() {
								var api = oTable.api();
								api.search( '$query' ).draw();
							}, 1);
					}
JS;

					$GLOBALS['phpgw']->js->add_code('', $code, true);
				}
			}

			self::add_javascript('property', 'portico', 'request.index.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		function priority_key()
		{
			if (!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 16, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$values = phpgw::get_var('values');

			$values['authorities_demands'] = $values['authorities_demands'] ? $values['authorities_demands'] : $this->config->config_data['authorities_demands'];

			if ($values['update'])
			{
				$receipt = $this->bo->update_priority_key($values);
				$this->config->config_data['authorities_demands'] = (int)$values['authorities_demands'];
				$this->config->save_repository();
			}

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$function_msg = lang('Edit priority key');
			$link_data = array('menuaction' => 'property.uirequest.priority_key');

			$priority_key = $this->bo->read_priority_key();

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$function_exchange_values = '';
			if ($receipt != '')
			{
				$function_exchange_values = "window.parent.reloadData();";
			}

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'function_msg' => $function_msg,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_priority_key_statustext' => lang('Weight for prioritising'),
				'lang_save' => lang('save'),
				'priority_key' => $priority_key,
				'exchange_values' => $function_exchange_values,
				'value_authorities_demands' => $values['authorities_demands'],
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			self::render_template_xsl('request', array('priority_form' => $data));
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id = phpgw::get_var('id', 'int');
			$values_attribute = phpgw::get_var('values_attribute');

			$values = $this->_populate();

			if ($id)
			{
				$action = 'edit';
				$values['id'] = $id;
			}

			if ($values['copy_request'])
			{
				$action = 'add';
			}

			if ($this->receipt['error'])
			{
				$this->edit($values);
			}
			else
			{
				try
				{
					$receipt = $this->bo->save($values, $action, $values_attribute);
					$id = $values['id'] = $receipt['id'];
					$this->receipt = $receipt;
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit($values);
						return;
					}
				}

				$this->_handle_files($values);

				if ($values['notify'])
				{
					$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
					$coordinator_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
					$headers = "Return-Path: <" . $coordinator_email . ">\r\n";
					$headers .= "From: " . $coordinator_name . "<" . $coordinator_email . ">\r\n";
					$headers .= "Bcc: " . $coordinator_name . "<" . $coordinator_email . ">\r\n";
					$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";

					$subject = lang(notify) . ": " . $values['id'];
					$message = lang(request) . " " . $values['id'] . " " . lang('is registered');

					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						$bcc = $coordinator_email;
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$rcpt = $GLOBALS['phpgw']->send->msg('email', $values['mail_address'], $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'plain');
					}
					else
					{
						$this->receipt['error'][] = array('msg' => lang('SMTP server is not set! (admin section)'));
					}
				}

				if ($rcpt)
				{
					$this->receipt['message'][] = array('msg' => lang('%1 is notified', $values['mail_address']));
				}

				if ($values['save_new'])
				{
					$values = $this->bo->read_single($values['id']);
					$GLOBALS['phpgw']->redirect_link('/index.php', array
						(
						'menuaction' => 'property.uirequest.add',
						'location_code' => $values['location_code'],
						'p_entity_id' => $values['p_entity_id'],
						'p_cat_id' => $values['p_cat_id'],
						'p_num' => $values['p_num'],
						'origin' => isset($values['origin_data'][0]) ? $values['origin_data'][0]['location'] : '',
						'origin_id' => isset($values['origin_data'][0]) ? $values['origin_data'][0]['data'][0]['id'] : ''
						)
					);
				}

				if($id)
				{
					self::message_set($this->receipt);
					self::redirect(array('menuaction' => 'property.uirequest.edit', 'id' => $id));
				}
				$this->edit($values);
			}
		}

		public function add()
		{
			$this->edit();
		}

		function edit( $values = array(), $mode = 'edit' )
		{
			$id = isset($values['id']) && $values['id'] ? $values['id'] : phpgw::get_var('id', 'int');

			if ($mode == 'view')
			{
				if (!$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if (!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$bypass = phpgw::get_var('bypass', 'bool');

			if ($mode == 'edit')
			{
				$location_code = phpgw::get_var('location_code');
				$tenant_id = phpgw::get_var('tenant_id', 'int');

				if (phpgw::get_var('p_num'))
				{
					$p_entity_id = phpgw::get_var('p_entity_id', 'int');
					$p_cat_id = phpgw::get_var('p_cat_id', 'int');
					$values['p'][$p_entity_id]['p_entity_id'] = $p_entity_id;
					$values['p'][$p_entity_id]['p_cat_id'] = $p_cat_id;
					$values['p'][$p_entity_id]['p_num'] = phpgw::get_var('p_num');
				}

				$origin = phpgw::get_var('origin');

				$origin_id = phpgw::get_var('origin_id', 'int');

				//23.jun 08: This will be handled by the interlink code - just doing a quick hack for now...
				if ($origin == '.ticket' && $origin_id && !$values['descr'])
				{
					$boticket = CreateObject('property.botts');
					$ticket = $boticket->read_single($origin_id);
					$values['descr'] = $ticket['details'];
					$values['title'] = $ticket['subject'];
					$ticket_notes = $boticket->read_additional_notes($origin_id);
					$i = count($ticket_notes) - 1;
					if (isset($ticket_notes[$i]['value_note']) && $ticket_notes[$i]['value_note'])
					{
						$values['descr'] .= ": " . $ticket_notes[$i]['value_note'];
					}
				}

				if ($p_entity_id && $p_cat_id)
				{
					$boadmin_entity = CreateObject('property.boadmin_entity');
					$entity_category = $boadmin_entity->read_single_category($p_entity_id, $p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if ($location_code)
				{
					$values['location_data'] = $this->bolocation->read_single($location_code, array(
						'tenant_id' => $tenant_id, 'p_num' => $p_num, 'view' => true));
				}
			}

			if (!empty($values['origin']))
			{
				$origin = $values['origin'];
				$origin_id = $values['origin_id'];
			}

			$interlink = CreateObject('property.interlink');

			if (isset($origin) && $origin)
			{
				$values['origin_data'][0]['location'] = $origin;
				$values['origin_data'][0]['descr'] = $interlink->get_location_name($origin);
				$values['origin_data'][0]['data'][] = array(
					'id' => $origin_id,
					'link' => $interlink->get_relation_link(array('location' => $origin), $origin_id),
				);
			}

			if (empty($id))
			{
				$id = $values['id'];
			}

			if (($values['save'] || $values['save_new']) && $mode == 'edit')
			{
				if ($this->receipt['error'])
				{
					if ($values['location'])
					{
						$location_code = implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $this->bolocation->read_single($location_code, $values['extra']);
					}

					if ($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num'] = $values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id'] = $values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id'] = $values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name'] = phpgw::get_var('entity_cat_name_' . $values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			if (!$this->receipt['error'] && !$bypass && $id)
			{
				$values = $this->bo->read_single($id);
				$record_history = $this->bo->read_record_history($id);
			}

			if ($id)
			{
				$function_msg = lang("{$mode} request");
			}
			else
			{
				$function_msg = lang('Add request');
				$values = $this->bo->read_single(0, $values);
			}

			if ($values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$lookup_type = $mode == 'edit' ? 'form2' : 'view2';

			$location_data = $this->bolocation->initiate_ui_location(array(
				'values' => $values['location_data'],
				'type_id' => isset($this->config->config_data['request_location_level']) && $this->config->config_data['request_location_level'] ? $this->config->config_data['request_location_level'] : -1,
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => true,
				'required_level' => 1,
				'lookup_type' => $lookup_type,
				'lookup_entity' => $this->bocommon->get_lookup_entity('request'),
				'entity_data' => $values['p']
				)
			);

			if ($values['contact_phone'])
			{
				for ($i = 0; $i < count($location_data['location']); $i++)
				{
					if ($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						$location_data['location'][$i]['value'] = $values['contact_phone'];
					}
				}
			}

			$link_data = array
				(
				'menuaction' => "property.uirequest.save",
				'id' => $id
			);

			if (!$values['coordinator'])
			{
				$values['coordinator'] = $this->account;
			}

			$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];

			$notify = $this->config->config_data['workorder_approval'];

			if ($supervisor_id && ($notify == 'yes'))
			{
				$prefs = $this->bocommon->create_preferences('property', $supervisor_id);
				$supervisor_email = $prefs['email'];
			}

			if ($values['project_id'])
			{
				$project_lookup_data = array
					(
					'menuaction' => 'property.uiproject.view'
				);
			}

			$show_dates = isset($this->config->config_data['request_show_dates']) && $this->config->config_data['request_show_dates'] ? 1 : '';

			if ($show_dates)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
				$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');
			}

			$GLOBALS['phpgw']->jqcal->add_listener('values_consume_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_planning_date');

			$link_file_data = array
				(
				'menuaction' => 'property.uirequest.view_file',
				'location_code' => $values['location_data']['location_code'],
				'id' => $id
			);


			$j = count($values['files']);
			for ($i = 0; $i < $j; $i++)
			{
				$values['files'][$i]['file_name'] = urlencode($values['files'][$i]['name']);
			}

			$datatable_def = array();
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true, 'resizeable' => true),
					array('key' => 'value_action', 'label' => lang('Action'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_old_value', 'label' => lang('old value'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'value_new_value', 'label' => lang('New Value'), 'sortable' => true,
						'resizeable' => true)),
				'data' => json_encode($record_history),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$content_files = array();

			for ($z = 0; $z < count($values['files']); $z++)
			{
				$content_files[$z]['file_name'] = '<a href="' . $link_view_file . '&amp;file_id=' . $values['files'][$z]['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $values['files'][$z]['name'] . '</a>';
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="' . $values['files'][$z]['file_id'] . '" title="' . lang('Check to delete file') . '" >';
			}

			$files_def = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'picture', 'label' => lang('picture'), 'sortable' => false,
					'resizeable' => true, 'formatter' => 'JqueryPortico.showPicture'),
				array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
//				'requestUrl' => "''",
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uirequest.get_files',
						'id' => $id, 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $files_def,
				'data' => json_encode($content_files),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$_consume_amount = 0;
			$_planning_amount = 0;
			if ($this->acl_edit)
			{
				$_lang_delete = lang('Check to delete');
				foreach ($values['consume'] as & $consume)
				{
					$_consume_amount = $_consume_amount + $consume['amount'];
					$consume['delete'] = "<input type='checkbox' name='values[delete_consume][]' value='{$consume['id']}' title='{$_lang_delete}'>";
				}
				foreach ($values['planning'] as & $planning)
				{
					$_planning_amount = $_planning_amount + $planning['amount'];
					$planning['delete'] = "<input type='checkbox' name='values[delete_planning][]' value='{$planning['id']}' title='{$_lang_delete}'>";
				}
			}

			$value_diff = (int)$values['budget'] - ($_consume_amount + $_planning_amount);
			$value_diff2 = (int)$values['budget'] - $_consume_amount;

			if ($value_diff < 0 || $value_diff2 < 0)
			{
				$receipt['error'][] = array('msg' => lang('negative value for budget'));
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$related = $this->get_related($id);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'ColumnDefs' => array(array('key' => 'id', 'label' => lang('id'), 'sortable' => true,
						'resizeable' => false),
					array('key' => 'type', 'label' => lang('type'), 'sortable' => true, 'resizeable' => true),
					array('key' => 'status', 'label' => lang('status'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'title', 'label' => lang('title'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'start_date', 'label' => lang('start date'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'end_date', 'label' => lang('end date'), 'sortable' => true,
						'resizeable' => true),
					array('key' => 'budget', 'label' => lang('budget'), 'sortable' => true, 'resizeable' => false,
						'formatter' => 'JqueryPortico.FormatterAmount0')),
				'data' => json_encode($related),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction' => 'property.uirequest.attrib_history',
							'attrib_id' => $attribute['id'],
							'id' => $id,
							'edit' => true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php', $link_history_data);
					}
				}
			}

			$_filter_buildingpart = array();
			$filter_buildingpart = isset($this->config->config_data['filter_buildingpart']) ? $this->config->config_data['filter_buildingpart'] : array();

			if ($filter_key = array_search('.project.request', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}


			$ticket_link_data = array
				(
				'menuaction' => 'property.uitts.add',
				'bypass' => true,
				'location_code' => $values['location_code'],
				//	'p_num'				=> 0,
				//	'p_entity_id'		=> 0,
				///	'p_cat_id'			=> 0,
				'origin' => $this->acl_location,
				'origin_id' => $id
			);

			$conditions = "";

			$link_survey = '';
			if(empty($values['origin_data']) && !empty($values['condition_survey_id']))
			{
				$link_survey = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uicondition_survey.edit', 'id' => $values['condition_survey_id']));
			}

			$data = array
				(
				'datatable_def' => $datatable_def,
				'mode' => $mode,
				'ticket_link' => $GLOBALS['phpgw']->link('/index.php', $ticket_link_data),
				'value_authorities_demands' => isset($this->config->config_data['authorities_demands']) && $this->config->config_data['authorities_demands'] ? $this->config->config_data['authorities_demands'] : 0,
				'suppressmeter' => isset($this->config->config_data['project_suppressmeter']) && $this->config->config_data['project_suppressmeter'] ? 1 : '',
				'value_condition_survey_id' => $values['condition_survey_id'],
				'value_link_survey' => $link_survey,
				'show_dates' => $show_dates,
				'custom_attributes' => array('attributes' => $values['attributes']),
				'tabs' => self::_generate_tabs(),
				'fileupload' => true,
				'link_view_file' => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'link_to_files' => $link_to_files,
				'files' => $values['files'],
				'lang_files' => lang('files'),
				'lang_filename' => lang('Filename'),
				'lang_file_action' => lang('Delete file'),
				'lang_view_file_statustext' => lang('click to view file'),
				'lang_file_action_statustext' => lang('Check to delete file'),
				'lang_upload_file' => lang('Upload file'),
				'lang_file_statustext' => lang('Select file to upload'),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_acl_location' => $this->acl_location,
				'value_target' => $values['target'],
				'value_origin' => $values['origin_data'],
				'value_origin_type' => $origin,
				'value_origin_id' => $origin_id,
				'lang_origin_statustext' => lang('Link to the origin for this request'),
				'generate_project_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiproject.edit')),
				'edit_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uirequest.edit',
					'id' => $id)),
				'acl_add_project' => $mode == 'view' ? 0 : $this->acl->check('.project', PHPGW_ACL_ADD, 'property'),
				'lang_generate_project' => lang('Generate project'),
				'lang_generate_project_statustext' => lang('Generate a project from this request'),
				'location_code' => $values['location_code'],
				'p_num' => $values['p_num'],
				'p_entity_id' => $values['p_entity_id'],
				'p_cat_id' => $values['p_cat_id'],
				'tenant_id' => $values['tenant_id'],
				'lang_importance' => lang('Importance'),
				'importance_weight' => $importance_weight,
				'lang_no_workorders' => lang('No workorder budget'),
				'workorder_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit')),
				'lang_history' => lang('History'),
				'lang_no_history' => lang('No history'),
				'value_entry_date' => $values['entry_date'],
				'value_closed_date' => $values['closed_date'],
				'value_in_progress_date' => $values['in_progress_date'],
				'value_delivered_date' => $values['delivered_date'],
				'lang_start_date_statustext' => lang('Select the estimated start date for the request'),
				'lang_start_date' => lang('request start date'),
				'value_start_date' => $values['start_date'],
				'lang_end_date_statustext' => lang('Select the estimated end date for the request'),
				'lang_end_date' => lang('request end date'),
				'value_end_date' => $values['end_date'],
				'lang_copy_request' => lang('Copy request ?'),
				'lang_copy_request_statustext' => lang('Choose Copy request to copy this request to a new request'),
				'lang_power_meter' => lang('Power meter'),
				'lang_power_meter_statustext' => lang('Enter the power meter'),
				'value_power_meter' => $values['power_meter'],
				'lang_budget' => lang('Budget'),
				'value_budget' => number_format($values['budget'], 0, ',', ' '),
				'lang_budget_statustext' => lang('Enter the budget'),
				'value_diff' => number_format($value_diff, 0, ',', ' '),
				'value_diff2' => number_format($value_diff2, 0, ',', ' '),
				'value_amount_potential_grants' => number_format($values['amount_potential_grants'], 0, ',', ''),
				'value_amount_investment' => number_format($values['amount_investment'], 0, ',', ''),
				'value_amount_operation' => number_format($values['amount_operation'], 0, ',', ''),
				'loc1' => $values['location_data']['loc1'],
				'location_data2' => $location_data,
				//		'location_type'						=> 'form2',
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uirequest.index')),
				'lang_request_id' => lang('request ID condition'),
				'value_request_id' => $id,
				'value_title' => $values['title'],
				'value_descr' => $values['descr'],
				'lang_score' => lang('Score'),
				'value_score' => $values['score'],
				'lang_done_statustext' => lang('Back to the list'),
				'lang_save_statustext' => lang('Save the request'),
				'lang_no_cat' => lang('Select category'),
				'lang_cat_statustext' => lang('Select the category the request belongs to. To do not use a category select NO CATEGORY'),
				'value_cat_id' => $values['cat_id'],
				'cat_select' => $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]',
					'selected' => $values['cat_id'], 'class' => 'required pure-input-1-2', 'required' => true)),
				'lang_coordinator' => isset($this->config->config_data['lang_request_coordinator']) && $this->config->config_data['lang_request_coordinator'] ? $this->config->config_data['lang_request_coordinator'] : lang('request coordinator'),
				'lang_user_statustext' => lang('Select the coordinator the request belongs to. To do not use a category select NO USER'),
				'select_user_name' => 'values[coordinator]',
				'lang_no_user' => lang('Select coordinator'),
				'user_list' => $this->bocommon->get_user_list_right2('select', 4, $values['coordinator'], $this->acl_location),
				'status_list' => array('options' => $this->bo->select_status_list('select', $values['status'])),
				'lang_no_status' => lang('Select status'),
				'lang_status' => lang('Status'),
				'lang_status_statustext' => lang('What is the current status of this request ?'),
				'responsible_unit_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'request_responsible_unit', 'selected' => $values['responsible_unit'],
						'order' => 'id', 'fields' => array('descr')))),
				'value_recommended_year' => $values['recommended_year'],
				'branch_list' => array('options' => $this->boproject->select_branch_list($values['branch_id'])),
				'lang_branch' => lang('branch'),
				'lang_no_branch' => lang('Select branch'),
				'lang_branch_statustext' => lang('Select the branches for this request'),
				'notify' => $notify,
				'lang_notify' => lang('Notify'),
				'lang_notify_statustext' => lang('Check this to notify your supervisor by email'),
				'value_notify_mail_address' => $supervisor_email,
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'authorities_demands' => array('options' => execMethod('property.bogeneric.get_list', array(
						'type' => 'authorities_demands', 'selected' => $values['authorities_demands']))),
				'regulations' => execMethod('property.bogeneric.get_list', array('type' => 'regulations',
					'selected' => $values['regulations'], 'fields' => array('descr', 'external_ref'))),
				'condition_list' => $this->bo->select_conditions($id),
				'building_part_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'building_part', 'selected' => $values['building_part'], 'order' => 'id',
						'id_in_name' => 'num', 'filter' => $_filter_buildingpart))),
				'value_consume' => isset($receipt['error']) ? $values['consume_value'] : '',
				'value_multiplier' => $values['multiplier'],
				'value_total_cost_estimate' => $values['multiplier'] ? number_format(($values['budget'] * $values['multiplier']), 0, ',', ' ') : '',
				'value_representative' => $values['representative'],
				'value_total_cost_estimate' => $values['representative'] ? number_format(($values['budget'] * $values['representative']), 0, ',', ' ') : '',
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file')),
				'multiple_uploader' => !!$id,
				'multi_upload_parans' => "{menuaction:'property.uirequest.build_multi_upload_file', id:'{$id}'}",
			);

			$appname = lang('request');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::add_javascript('property', 'portico', 'request.edit.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('request', 'datatable_inline', 'files', 'attributes_form'), array(
				'edit' => $data));
		}

		function delete()
		{
			$id = phpgw::get_var('id', 'int');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($id);
				return "id " . $id . " " . lang("has been deleted");
			}

			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 8, 'acl_location' => $this->acl_location));
			}


			//$id = phpgw::get_var('id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uirequest.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uirequest.delete',
					'id' => $id)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('request');
			$function_msg = lang('delete request');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit(array(), $mode = 'view');
		}

		function get_related( $id )
		{
			if (!$this->acl_read)
			{
				return array();
			}

			$interlink = CreateObject('property.interlink');
			$target = $interlink->get_relation('property', $this->acl_location, $id, 'target');

			$values = array();
			if ($target)
			{
				foreach ($target as $_target_section)
				{

					foreach ($_target_section['data'] as $_target_entry)
					{
						switch ($_target_section['location'])
						{
							case '.ticket':
								$ticket = execMethod('property.sotts.read_single', (int)$_target_entry['id']);
								$budget = $ticket['budget'];
								$start_date = $GLOBALS['phpgw']->common->show_date($ticket['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
								break;
							case '.project':
								$project = execMethod('property.soproject.read_single', (int)$_target_entry['id']);
								$budget = $project['budget'];
								$start_date = $GLOBALS['phpgw']->common->show_date($project['start_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
								$end_date = $GLOBALS['phpgw']->common->show_date($project['end_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
								break;
							case '.project.workorder':
								$workorder = execMethod('property.soworkorder.read_single', (int)$_target_entry['id']);
								$budget = $workorder['budget'];
								$start_date = $GLOBALS['phpgw']->common->show_date($workorder['start_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
								$end_date = $GLOBALS['phpgw']->common->show_date($workorder['end_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
								break;
							default:
							// nothing
						}

						$values[] = array
							(
							'id' => "<a href=\"{$_target_entry['link']}\" > {$_target_entry['id']}</a>",
							'type' => ucfirst($_target_section['descr']),
							'title' => $_target_entry['title'],
							'status' => $_target_entry['statustext'],
							'budget' => $budget,
							'start_date' => $start_date,
							'end_date' => $end_date,
						);
					}
				}
			}

//------ Start pagination

			$start = phpgw::get_var('startIndex', 'int', 'REQUEST', 0);

			$total_records = count($values);

			$num_rows = isset($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] ? (int)$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] : 15;

			if ($allrows)
			{
				$out = $values;
			}
			else
			{
				$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
				$values_part = array_chunk($values, $num_rows);
				$out = $values_part[$page];
			}

//------ End pagination

			return $out;
		}

		protected function _generate_tabs()
		{
			$active_tab = 'general';
			$tabs = array
				(
				'general' => array('label' => lang('general'), 'link' => '#general'),
				'budget' => array('label' => lang('documents'), 'link' => '#documents'),
				'history' => array('label' => lang('history'), 'link' => '#history')
			);
			return phpgwapi_jquery::tabview_generate($tabs, $active_tab, 'request_tabview');
		}
	}