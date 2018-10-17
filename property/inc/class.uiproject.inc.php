<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009,2010,2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	/**
	 * Import the jQuery class
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	/**
	 * Description
	 * @package property
	 */
	class property_uiproject extends phpgwapi_uicommon_jquery
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
		var $district_id;
		var $criteria_id;
		var $project_type_id;
		var $ecodimb;
		var $bypass_error = false;
		var $public_functions = array
			(
			'query' => true,
			'download' => true,
			'index' => true,
			'view' => true,
			'edit' => true,
			'add' => true,
			'delete' => true,
			'save' => true,
			'date_search' => true,
			'columns' => true,
			'bulk_update_status' => true,
			'view_file' => true,
			'get_orders' => true,
			'get_vouchers' => true,
			'check_missing_project_budget' => true,
			'get_external_project'=> true,
			'get_ecodimb'	=> true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true,
			'get_files'				=> true,
			'view_image'			=> true,
			'get_other_projects'	=> true,
			'get_attachment'		=> true
		);

		function __construct()
		{
			parent::__construct();

			//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project';

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo = CreateObject('property.boproject', true);
			$this->bocommon = & $this->bo->bocommon;
			$this->cats = & $this->bo->cats;
			$this->custom = & $this->bo->custom;

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.project';
			$this->acl_read = $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');
			$this->acl_manage = $this->acl->check('.project', 16, 'property');

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->status_id = $this->bo->status_id;
			$this->wo_hour_cat_id = $this->bo->wo_hour_cat_id;
			$this->district_id = $this->bo->district_id;
			$this->user_id = $this->bo->user_id;
			$this->criteria_id = $this->bo->criteria_id;
			$this->project_type_id = $this->bo->project_type_id;
			$this->filter_year = $this->bo->filter_year;
			$this->decimal_separator = ',';
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
				'wo_hour_cat_id' => $this->wo_hour_cat_id,
				'district_id' => $this->district_id,
				'user_id' => $this->user_id,
				'criteria_id' => $this->criteria_id,
				'project_type_id' => $this->project_type_id
			);
			$this->bo->save_sessiondata($data);
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

		function check_missing_project_budget()
		{
			$values = $this->bo->get_missing_project_budget();
			$this->bocommon->download($values, array('project_id', 'year'), array(lang('project'),
				lang('year')));
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

		function get_other_projects($id = 0, $location_code = '')
		{
			if (!$this->acl_read)
			{
				return array();
			}

			if(!$id)
			{
				$id = phpgw::get_var('id', 'int');
			}

			if(!$location_code)
			{
				$location_code = phpgw::get_var('location_code', 'string');
			}

			$values = $this->bo->get_other_projects($id, $location_code);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values as &$entry)
			{
				$link = self::link(array('menuaction' => 'property.uiproject.view', 'id' => $entry['id']));
				$entry['url'] = "<a href='{$link}'>{$entry['id']}</a>";
				$entry['start_date'] =$GLOBALS['phpgw']->common->show_date($entry['start_date'], $dateformat);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{

				$total_records = count($values);

				return array
					(
					'data' => $values,
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => $total_records,
					'recordsFiltered' => $total_records
				);
			}

			return $values;
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
				'menuaction' => 'property.uiproject.view_file',
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
							'menuaction' => 'property.uiproject.view_image',
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

			$options['base_dir'] = 'project/'.$id;
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/property/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => 'property.uiproject.handle_multi_upload_file', 'id' => $id)));
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

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.handle_multi_upload_file', 'id' => $id));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}

		function columns()
		{
			$receipt = array();
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values = phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->add('property', 'project_columns', $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => 'property.uiproject.columns',
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

		private function _get_Filters()
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bo->get_project_types($this->project_type_id);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('project type')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'project_type_id',
				'text' => lang('Project Type'),
				'list' => $values_combo_box[0]
			);

			$values_combo_box[1] = $this->bocommon->select_district_list('filter', $this->district_id);
			$default_value = array('id' => '', 'name' => lang('no district'));
			array_unshift($values_combo_box[1], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'district_id',
				'text' => lang('District'),
				'list' => $values_combo_box[1]
			);

			$_cats = $this->cats->return_sorted_array(0, false, '', '', '', false, false);
			$values_combo_box[2] = array();
			foreach ($_cats as $_cat)
			{
				if ($_cat['level'] == 0 && $_cat['active'] != 2)
				{
					$values_combo_box[2][] = $_cat;
				}
			}

			$default_value = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box[2], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('Category'),
				'list' => $values_combo_box[2]
			);

			$values_combo_box[3] = $this->bo->select_status_list('filter');
			array_unshift($values_combo_box[3], array('id' => 'open', 'name' => lang('open')));
			array_unshift($values_combo_box[3], array('id' => 'all', 'name' => lang('all')));

			foreach ($values_combo_box[3] as &$_entry)
			{
				$_entry['selected'] = $_entry['id'] == $this->status_id ? 1 : 0;
			}
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'status_id',
				'text' => lang('Status'),
				'list' => $values_combo_box[3]
			);

			$values_combo_box[4] = $this->bocommon->select_category_list(array('format' => 'filter',
				'selected' => $this->wo_hour_cat_id, 'type' => 'wo_hours', 'order' => 'id'));
			$default_value = array('id' => '', 'name' => lang('no hour category'));
			array_unshift($values_combo_box[4], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'wo_hour_cat_id',
				'text' => lang('Hour Category'),
				'list' => $values_combo_box[4]
			);

			$values_combo_box[5] = $this->bo->get_criteria_list($this->criteria_id);
			$default_value = array('id' => '', 'name' => lang('no criteria'));
			array_unshift($values_combo_box[5], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'criteria_id',
				'text' => lang('search criteria'),
				'list' => $values_combo_box[5]
			);

			$values_combo_box[6] = $this->bo->get_filter_year_list($this->filter_year);
			array_unshift($values_combo_box[6], array('id' => 'all', 'name' => lang('all') . ' ' . lang('year')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'filter_year',
				'text' => lang('Year'),
				'list' => $values_combo_box[6]
			);

			$values_combo_box[7] = $this->bo->get_user_list($this->filter);
			array_unshift($values_combo_box[7], array('id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'name' => lang('mine projects')));
			$default_value = array('id' => '', 'name' => lang('no user'));
			array_unshift($values_combo_box[7], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'filter',
				'text' => lang('User'),
				'list' => $values_combo_box[7]
			);

			return $combos;
		}

		function index()
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::project';

			if ($this->cat_id)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$this->cat_id}";
			}

			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiproject.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$lookup = phpgw::get_var('lookup', 'bool');
			$from = phpgw::get_var('from');

			$default_district = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'] : '');

			if ($default_district && ! isset($_REQUEST['district_id']))
			{
				$this->bo->district_id = $default_district;
				$this->district_id = $default_district;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('datepicker');

			$appname = lang('Project');
			$function_msg = lang('list Project');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
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
						'menuaction' => 'property.uiproject.index',
						'lookup' => $lookup,
						'from' => $from,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array(
						'menuaction' => 'property.uiproject.download',
						'export' => true,
						'skip_origin' => true,
						'allrows' => true
					)),
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'property.uiproject.columns'},{closeAction:'reload'})"),
					'new_item' => self::link(array(
						'menuaction' => 'property.uiproject.add'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(),
					'query'	=> phpgw::get_var('query')
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$this->bo->read(array('dry_run' => true));
			$uicols = $this->bo->uicols;
			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array
					(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if ($uicols['name'][$k] == 'project_id')
				{
					$params['formatter'] = 'JqueryPortico.formatProject';
				}

				if ($uicols['name'][$k] == 'ticket')
				{
					$params['formatter'] = 'formatLinkTicket';
				}

				if ($uicols['name'][$k] == 'loc1')
				{
					# Aca debe ser el Onclick
					$params['formatter'] = 'JqueryPortico.searchLink';
				}
				if ($uicols['name'][$k] == 'actual_cost')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'combined_cost')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'budget')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'diff')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}
				if ($uicols['name'][$k] == 'obligation')
				{
					$params['formatter'] = 'JqueryPortico.FormatterAmount0';
				}

				array_push($data['datatable']['field'], $params);
			}

			if (!$lookup)
			{
				$parameters = array
					(
					'parameter' => array
						(
						array
							(
							'name' => 'id',
							'source' => 'project_id'
						),
					)
				);

				$parameters2 = array
					(
					'parameter' => array
						(
						array
							(
							'name' => 'project_id',
							'source' => 'project_id'
						),
					)
				);

				if ($this->acl_read)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'view',
						'statustext' => lang('view the project'),
						'text' => lang('view'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiproject.view'
						)),
						'parameters' => json_encode($parameters)
					);

					$data['datatable']['actions'][] = array
						(
						'my_name' => 'view',
						'statustext' => lang('view the project'),
						'text' => lang('open view in new window'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiproject.view'
						)),
						'target' => '_blank',
						'parameters' => json_encode($parameters)
					);

					$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

					foreach ($jasper as $report)
					{
						$data['datatable']['actions'][] = array
							(
							'my_name' => 'edit',
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
						'statustext' => lang('edit the project'),
						'text' => lang('edit'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiproject.edit'
						)),
						'parameters' => json_encode($parameters)
					);

					$data['datatable']['actions'][] = array
						(
						'my_name' => 'edit',
						'statustext' => lang('edit the project'),
						'text' => lang('open edit in new window'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiproject.edit'
						)),
						'target' => '_blank',
						'parameters' => json_encode($parameters)
					);

					$data['datatable']['actions'][] = array
						(
						'my_name' => 'edit',
						'statustext' => lang('Add a workorder to this project'),
						'text' => lang('Add a workorder to this project'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiworkorder.edit'
						)),
						'parameters' => json_encode($parameters2)
					);
				}

				if ($this->acl_delete)
				{
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'delete',
						'text' => lang('delete'),
						'confirm_msg' => lang('do you really want to delete this entry'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiproject.delete'
						)),
						'parameters' => json_encode($parameters2)
					);
				}
			}

			unset($parameters);

			$function_exchange_values = '';
			if ($lookup)
			{
				$oArg = "{menuaction: 'property.ui{$from}.edit',"
					. "origin:'" . phpgw::get_var('origin') . "',"
					. "origin_id:'" .  phpgw::get_var('origin_id') ."',"
					. "project_id: aData['project_id']}";

				$data['left_click_action'] = "window.open(phpGWLink('index.php', {$oArg}),'_self');";
			}

			$query = phpgw::get_var('query');
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
			

			self::add_javascript('property', 'portico', 'project.index.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$start_date = urldecode(phpgw::get_var('start_date'));
			$end_date = urldecode(phpgw::get_var('end_date'));
			$skip_origin = phpgw::get_var('skip_origin', 'bool');
			$export = phpgw::get_var('export', 'bool');

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
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'skip_origin' => $skip_origin
			);

			$values = $this->bo->read($params);

			if ($export)
			{
				return $values;
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;
			$link_data = array
				(
				'menuaction' => 'property.uiproject.edit'
			);
			array_walk($result_data['results'], array($this, '_add_links'), $link_data);

			return $this->jquery_results($result_data);
		}

		function date_search()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('date_search'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true;
			//	$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$values['start_date'] = phpgw::get_var('start_date', 'string', 'POST');
			$values['end_date'] = phpgw::get_var('end_date', 'string', 'POST');

			$function_msg = lang('Date search');
			$appname = lang('project');

			if (!$values['end_date'])
			{
				$values['end_date'] = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('end_date');

			$data = array
				(
				'lang_start_date_statustext' => lang('Select the estimated end date for the Project'),
				'lang_start_date' => lang('Start date'),
				'value_start_date' => $values['start_date'],
				'lang_end_date_statustext' => lang('Select the estimated end date for the Project'),
				'lang_end_date' => lang('End date'),
				'value_end_date' => $values['end_date'],
				'lang_submit_statustext' => lang('Select this dates'),
				'lang_submit' => lang('Submit')
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('date_search' => $data));
		}
		/*
		 * Overrides with incoming data from POST
		 */

		private function _populate()
		{
			$id = (int)phpgw::get_var('id', 'int');

			$values = phpgw::get_var('values');
			$values_attribute = phpgw::get_var('values_attribute');
//			$values['external_project_id'] = phpgw::get_var('external_project_id');
//			$values['ecodimb'] = phpgw::get_var('ecodimb');
			$values['contact_id'] = phpgw::get_var('contact', 'int', 'POST');

			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');

			$insert_record_entity = $GLOBALS['phpgw']->session->appsession("insert_record_values{$this->acl_location}", 'property');

			if (isset($insert_record_entity) && is_array($insert_record_entity))
			{
				for ($j = 0; $j < count($insert_record_entity); $j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
				}
			}
			$bypass = phpgw::get_var('bypass', 'bool');


			if ($_POST && !$bypass && isset($insert_record) && is_array($insert_record))
			{
				$values = $this->bocommon->collect_locationdata($values, $insert_record);
			}

			if (isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
			{
				if (!$this->acl_manage)
				{
					if (!isset($values['ecodimb']) || !$values['ecodimb'])
					{
						$this->receipt['error'][] = array('msg' => lang('Please select dimb!'));
						$error_id = true;
					}

					$approve_role = execMethod('property.boinvoice.check_role', $values['ecodimb']);
					if (!$approve_role['is_supervisor'] && !$approve_role['is_budget_responsible'])
					{
						$this->receipt['error'][] = array('msg' => lang('you are not approved for this dimb: %1', $values['ecodimb']));
						$error_id = true;
					}
				}
			}

			if(!empty($values['ecodimb']) || (!empty($values['ecodimb_name']) && empty($values['ecodimb'])))
			{
				$_ecodimb = execMethod('property.bogeneric.read_single', array(
					'id' => $values['ecodimb'],
					'location_info' => array(
						'type' => 'dimb')));
				if (!$_ecodimb || !$_ecodimb['active'])
				{
					$values['ecodimb'] = '';
					$values['ecodimb_name'] = '';
					$this->receipt['error'][] = array(
						'msg' => lang('Please select a valid dimb!'));
				}

			}

			if (!isset($values['location']))
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a location !'));
				$error_id = true;
			}

			if (!empty($values['b_account_id']))
			{
//				$sogeneric = CreateObject('property.sogeneric');
//				$sogeneric->get_location_info('budget_account', false);
//				$status_data = $sogeneric->read_single(array('id' => (int)$values['b_account_id']), array());
//				$values['b_account_group'] = $status_data['category'];


				$_b_account = execMethod('property.bogeneric.read_single', array(
					'id' => $values['b_account_id'],
					'location_info' => array(
						'type' => 'budget_account')));
				$values['b_account_group'] = $_b_account['category'];

				if (!$_b_account || !$_b_account['active'])
				{
					$values['b_account_id'] = '';
					$values['b_account_name'] = '';
					$values['b_account_group'] = '';
					$this->receipt['error'][] = array(
						'msg' => lang('Please select a valid budget account !'));
				}
			}

			if (isset($values['b_account_group']) && $values['b_account_group'])
			{
				$sogeneric = CreateObject('property.sogeneric');
				$sogeneric->get_location_info('b_account_category', false);
				$status_data = $sogeneric->read_single(array('id' => (int)$values['b_account_group']), array());

				if (isset($status_data['external_project']) && $status_data['external_project'])//mandatory for this account group
				{
					if (!isset($values['external_project_id']) || !$values['external_project_id'])
					{
						$this->receipt['error'][] = array('msg' => lang('Please select an external project!'));
						$error_id = true;
					}
				}
			}

			if (isset($values['new_project_id']) && $values['new_project_id'] && !$this->bo->read_single_mini($values['new_project_id']))
			{
				$this->receipt['error'][] = array('msg' => lang('the project %1 does not exist', $values['new_project_id']));
			}

			if (isset($values['new_project_id']) && $values['new_project_id'] && $values['new_project_id'] == $id)
			{
				unset($values['new_project_id']);
			}

			if (!isset($values['end_date']) || !$values['end_date'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select an end date!'));
				$error_id = true;
			}

			if (!isset($values['project_type_id']) || !$values['project_type_id'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a project type!'));
				$error_id = true;
			}

			if (!$values['name'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please enter a project NAME !'));
				$error_id = true;
			}

			if (!isset($config->config_data['project_optional_category']) || !$config->config_data['project_optional_category'])
			{
				if (!$values['cat_id'])
				{
					$this->receipt['error'][] = array('msg' => lang('Please select a category !'));
					$error_id = true;
				}
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$_category = $this->cats->return_single($values['cat_id']);
				if (!$_category[0]['active'])
				{
					$this->receipt['error'][] = array('msg' => lang('invalid category'));
				}
			}

			if (!$values['coordinator'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a coordinator !'));
				$error_id = true;
			}

			if (!$values['status'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a status !'));
				$error_id = true;
			}

			if(!$id && empty($values['budget']))
			{
				$this->receipt['error'][] = array('msg' => lang('enter the budget'));
				$error_id = true;
			}
			
			if (!empty($values['budget']) && !ctype_digit(ltrim($values['budget'], '-')))
			{
				$this->receipt['error'][] = array('msg' => lang('budget') . ': ' . lang('Please enter an integer !'));
				$error_id = true;
			}

			if (isset($values['reserve']) && $values['reserve'] && !ctype_digit(ltrim($values['reserve'], '-')))
			{
				$this->receipt['error'][] = array('msg' => lang('reserve') . ': ' . lang('Please enter an integer !'));
				$error_id = true;
			}

			if (isset($values_attribute) && is_array($values_attribute))
			{
				foreach ($values_attribute as $attribute)
				{
					if ($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
					{
						$this->receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
					}
				}
			}

			if ($values['approval'] && $values['mail_address'] && $config->config_data['project_approval'])
			{
				if (isset($config->config_data['project_approval_status']) && $config->config_data['project_approval_status'])
				{
					$values['status'] = $config->config_data['project_approval_status'];
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
				throw new Exception('uiproject::_handle_files() - missing id');
			}

			$bofiles = CreateObject('property.bofiles');
			if (isset($values['file_action']) && is_array($values['file_action']))
			{
				$bofiles->delete_file("/project/{$id}/", $values);
			}

			$file_name = @str_replace(' ', '_', $_FILES['file']['name']);

			if ($file_name)
			{
				$to_file = "{$bofiles->fakebase}/project/{$id}/{$file_name}";

				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$this->receipt['error'][] = array('msg' => lang('This file already exists !'));
				}
				else
				{
					$bofiles->create_document_dir("project/$id");
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
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id = phpgw::get_var('id', 'int');
			$values_attribute = phpgw::get_var('values_attribute');
			$config = CreateObject('phpgwapi.config', 'property');
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$config->read();

			$values = $this->_populate();
			if ($id)
			{
				$action = 'edit';
				$values['id'] = $id;
			}

			if ($values['copy_project'])
			{
				$action = 'add';
			}

			if (!$this->receipt['error'])
			{
				try
				{
					$receipt = $this->bo->save($values, $action, $values_attribute);
					$values['id'] = $receipt['id'];
					$id = $receipt['id'];
					$this->receipt = $receipt;
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
					}
				}

				if (!$this->receipt['error'])
				{
					$this->_handle_files($values);

					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						$historylog = CreateObject('property.historylog', 'project');
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						$action_params['responsible'] = $_account_id;
						$from_name = $GLOBALS['phpgw_info']['user']['fullname'];
						$from_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

						$subject = lang('Approval') . ": " . $id;
						$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.edit',
								'id' => $id), false, true) . '">' . lang('project %1 needs approval', $id) . '</a>';

						$bcc = '';//$from_email;

						$action_params = array
							(
							'appname' => 'property',
							'location' => '.project',
							'id' => $id,
							'responsible' => '',
							'responsible_type' => 'user',
							'action' => 'approval',
							'remark' => '',
							'deadline' => ''
						);

						/**
						 * For now - handled by uiworkorder.
						 * consider remove - beware of the substitute logic...see uiworkorder
						 */
						if (isset($values['mail_address']) && is_array($values['mail_address']))
						{
							foreach ($values['mail_address'] as $_account_id => $_address)
							{
								if (isset($values['approval'][$_account_id]) && $values['approval'][$_account_id])
								{
									try
									{
										$rcpt = $GLOBALS['phpgw']->send->msg('email', $_address, $subject, stripslashes($message), '', $cc, $bcc, $from_email, $from_name, 'html');										
									}
									catch (Exception $e)
									{
										phpgwapi_cache::message_set($e->getMessage(), 'error');
									}

									$action_params['responsible'] = $_account_id;
									execMethod('property.sopending_action.set_pending_action', $action_params);
									if (!$rcpt)
									{
										$this->receipt['error'][] = array('msg' => "uiproject::edit: sending message to '" . $_address . "', subject='$subject' failed !!!");
										$this->receipt['error'][] = array('msg' => $GLOBALS['phpgw']->send->err['desc']);
										$this->bypass_error = true;
									}
									else
									{
										$historylog->add('AP', $id, lang('%1 is notified', $_address));
										$this->receipt['message'][] = array('msg' => lang('%1 is notified', $_address));
									}
								}
							}
						}

						$toarray = array();
						$toarray_sms = array();
						if (isset($this->receipt['notice_owner']) && is_array($this->receipt['notice_owner']))
						{
							if ($this->account != $values['coordinator'] && isset($GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner']
							//							 && $config->config_data['mailnotification']
							)
							{
								$prefs_coordinator = $this->bocommon->create_preferences('property', $values['coordinator']);
								if (isset($prefs_coordinator['email']) && $prefs_coordinator['email'])
								{
									$toarray[] = $prefs_coordinator['email'];
								}
							}
						}

						$notify_list = execMethod('property.notify.read', array
							(
							'location_id' => $location_id,
							'location_item_id' => $id
							)
						);

						$subject = lang('project %1 has been edited', $id);

						if (isset($GLOBALS['phpgw_info']['user']['apps']['sms']))
						{
							$sms_text = "{$subject}. \r\n{$GLOBALS['phpgw_info']['user']['fullname']} \r\n{$GLOBALS['phpgw_info']['user']['preferences']['property']['email']}";
							$sms = CreateObject('sms.sms');

							foreach ($notify_list as $entry)
							{
								if ($entry['is_active'] && $entry['notification_method'] == 'sms' && $entry['sms'])
								{
									$sms->websend2pv($this->account, $entry['sms'], $sms_text);
									$toarray_sms[] = "{$entry['first_name']} {$entry['last_name']}({$entry['sms']})";
									$this->receipt['message'][] = array('msg' => lang('%1 is notified', "{$entry['first_name']} {$entry['last_name']}"));
								}
							}
							unset($entry);

							if ($toarray_sms)
							{
								$historylog->add('MS', $id, implode(',', $toarray_sms));
							}
						}

						reset($notify_list);
						foreach ($notify_list as $entry)
						{
							if ($entry['is_active'] && $entry['notification_method'] == 'email' && $entry['email'])
							{
								$toarray[] = "{$entry['first_name']} {$entry['last_name']}<{$entry['email']}>";
							}
						}
						unset($entry);

						if ($toarray)
						{
							$to = implode(';', $toarray);
							$from_name = $GLOBALS['phpgw_info']['user']['fullname'];
							$from_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];

							$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.edit',
									'id' => $id), false, true) . '">' . lang('project %1 has been edited', $id) . '</a>' . "\n";

							foreach ($this->receipt['notice_owner'] as $notice)
							{
								$body .= $notice . "\n";
							}

							$body .= lang('Altered by') . ': ' . $from_name . "\n";
							$body .= lang('remark') . ': ' . $values['remark'] . "\n";

							$body = nl2br($body);

							try
							{
								$returncode = $GLOBALS['phpgw']->send->msg('email', $to, $subject, $body, false, false, false, $from_email, $from_name, 'html');
							}
							catch (Exception $e)
							{
								phpgwapi_cache::message_set($e->getMessage(), 'error');
							}

							if (!$returncode) // not nice, but better than failing silently
							{
								$this->receipt['error'][] = array('msg' => "uiproject::edit: sending message to '$to' subject='$subject' failed !!!");
								$this->receipt['error'][] = array('msg' => $GLOBALS['phpgw']->send->err['desc']);
								$this->bypass_error = true;
							}
							else
							{
								$historylog->add('ON', $id, lang('%1 is notified', $to));
								$this->receipt['message'][] = array('msg' => lang('%1 is notified', $to));
							}
						}
					}
				}
			}

			if ($id)
			{
				self::message_set($this->receipt);
				$active_tab = phpgw::get_var('active_tab');

				self::redirect(array('menuaction' => 'property.uiproject.edit', 'id' => $id, 'active_tab' => $active_tab));
			}

			$this->edit($values, 'edit');

			return;
		}

		public function add()
		{
			$this->edit();
		}

		function edit( $values = array(), $mode = 'edit' )
		{
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::project';

			$id = isset($values['id']) && $values['id'] ? $values['id'] : (int)phpgw::get_var('id', 'int');

			if ($mode == 'edit' && (!$this->acl_add && !$this->acl_edit))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiproject.view',
					'id' => $id));
			}

			if ($mode == 'view')
			{
				if (!$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}

				if (!$id)
				{
					phpgwapi_cache::message_set('ID is required for the function uiproject::view()', 'error');
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiproject.index'));
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
			$add_request = phpgw::get_var('add_request');

			if (phpgw::get_var('origin') == '.project.request' && phpgw::get_var('origin_id', 'int') && !$bypass)
			{
				$id = phpgw::get_var('project_id', 'int');
				$add_request = array('request_id' => array(phpgw::get_var('origin_id', 'int')));
			}

			if ($mode == 'edit' && $add_request && $id)
			{
				$receipt = $this->bo->add_request($add_request, $id);
			}

			$bolocation = CreateObject('property.bolocation');

			// origin

			if (!$id && $bypass)
			{
				$location_code = phpgw::get_var('location_code');
				$tenant_id = phpgw::get_var('tenant_id', 'int');
				$values['descr'] = phpgw::get_var('descr');
				$p_entity_id = phpgw::get_var('p_entity_id', 'int');
				$p_cat_id = phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id'] = $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id'] = $p_cat_id;
				$values['p'][$p_entity_id]['p_num'] = phpgw::get_var('p_num');

				$origin = phpgw::get_var('origin');
				$origin_id = phpgw::get_var('origin_id', 'int');

				if ($origin == '.ticket' && $origin_id && !$values['descr'])
				{
					$boticket = CreateObject('property.botts');
					$ticket = $boticket->read_single($origin_id);
					$values['descr'] = $ticket['details'];
					$values['name'] = $ticket['subject'] ? $ticket['subject'] : $ticket['category_name'];
					$ticket_notes = $boticket->read_additional_notes($origin_id);
					$i = count($ticket_notes) - 1;
					if (isset($ticket_notes[$i]['value_note']) && $ticket_notes[$i]['value_note'])
					{
						$values['descr'] .= ": " . $ticket_notes[$i]['value_note'];
					}
					$values['contact_id'] = $ticket['contact_id'];
					$tts_status_create_project = isset($GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_create_project']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['tts_status_create_project'] : '';
					if ($tts_status_create_project)
					{
						$boticket->update_status(array('status' => $tts_status_create_project), $origin_id);
					}

					if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['auto_create_project_from_ticket']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['auto_create_project_from_ticket'] == 'yes')
					{
						$auto_create = true;
					}
				}

				if ($p_entity_id && $p_cat_id)
				{
					if (!is_object($boadmin_entity))
					{
						$boadmin_entity = CreateObject('property.boadmin_entity');
					}

					$entity_category = $boadmin_entity->read_single_category($p_entity_id, $p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if ($location_code)
				{
					$values['location_data'] = $bolocation->read_single($location_code, array('tenant_id' => $tenant_id,
						'p_num' => $p_num, 'view' => true));
				}

				if (isset($origin) && $origin)
				{
					$interlink = CreateObject('property.interlink');
					$values['origin_data'][0]['location'] = $origin;
					$values['origin_data'][0]['descr'] = $interlink->get_location_name($origin);
					$values['origin_data'][0]['data'][] = array(
						'id' => $origin_id,
						'link' => $interlink->get_relation_link(array('location' => $origin), $origin_id),
					);
				}
			}

			// end origin

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();

			$record_history = array();
			if ($this->bypass_error || ((!$this->receipt['error'] || $add_request) && !$bypass) && $id)
			{
				$_transfer_new_project = isset($values['new_project_id']) && $values['new_project_id'] ? true : false;

				$values = $this->bo->read_single($id);
				if (!isset($values['origin']))
				{
					$values['origin'] = '';
				}

				if (!isset($values['workorder_budget']) && $save && !$_transfer_new_project && !$values['project_type_id'] == 3)
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiworkorder.edit',
						'project_id' => $id));
				}

				if (!$this->bocommon->check_perms2($values['coordinator'], $this->bo->so->grants, PHPGW_ACL_EDIT))
				{
					$this->receipt['error'][] = array('msg' => lang('You have no edit right for this project'));
					$GLOBALS['phpgw']->session->appsession('receipt', 'property', $this->receipt);

					switch ($mode)
					{
						case 'edit':
							self::redirect(array('menuaction' => 'property.uiproject.view','id' => $id));
							break;
						default:
							self::redirect(array('menuaction' => 'property.uiproject.index'));
							break;
					}
				}
				else
				{
					$record_history = $this->bo->read_record_history($id);
				}
			}

			/* Preserve attribute values from post */
			if ($this->receipt['error'] && (isset($values_attribute) && is_array($values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values, $values_attribute);
			}

			if ($id)
			{
				$function_msg = lang("{$mode} project");
			}
			else
			{
				$function_msg = lang('Add Project');
				$values = $this->bo->read_single(0, $values);
			}

			$tabs = array();
			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction' => 'property.uiproject.attrib_history',
							'attrib_id' => $attribute['id'],
							'id' => $id,
							'edit' => true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php', $link_history_data);
					}
				}
			}


			if (isset($values['cat_id']))
			{
				$this->cat_id = $values['cat_id'];
			}

			$lookup_type = $mode == 'edit' ? 'form' : 'view';

//			_debug_array($values);
			$location_data = $bolocation->initiate_ui_location(array
				(
				'values' => (isset($values['location_data']) ? $values['location_data'] : ''),
				'type_id' => -1, // calculated from location_types
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => true,
				'lookup_type' => $lookup_type,
				'lookup_entity' => $this->bocommon->get_lookup_entity('project'),
				'entity_data' => (isset($values['p']) ? $values['p'] : ''),
				'required_level' => 1
				)
			);

			$b_account_data = array();
			$ecodimb_data = array();
			if (isset($config->config_data['budget_at_project']) && $config->config_data['budget_at_project'])
			{
				$b_account_group_data = $this->bocommon->initiate_ui_budget_account_lookup(array
					(
					'b_account_id' => $values['b_account_group'],
					'b_account_name' => $values['b_account_group_name'],
					'role' => 'group',
					'type' => $lookup_type
					)
				);


				$b_account_data = array();

				if (isset($config->config_data['budget_account_at_project']) && $config->config_data['budget_account_at_project'])
				{
					$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array
						(
						'b_account_id' => $values['b_account_id'],
						'b_account_name' => $values['b_account_name'],
						'type' => $lookup_type
						)
					);
				}

			}

			$default_ecodimb = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['dimb']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['dimb'] : '');
//			if(isset($values['ecodimb']) && $values['ecodimb'] && $values['ecodimb'] != $default_ecodimb)
//			{
//				$GLOBALS['phpgw']->preferences->add('property', 'dimb', $values['ecodimb'], 'user');
//				$GLOBALS['phpgw']->preferences->save_repository();
//			}

			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array
				(
				'ecodimb' => $values['ecodimb'] ? $values['ecodimb'] : $default_ecodimb,
				'ecodimb_descr' => $values['ecodimb_descr'],
				'disabled' => $mode == 'view'
			));

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array
				(
				'contact_id' => $values['contact_id'],
				'contact_name' => $values['contact_name'],
				'field' => 'contact',
				'type' => $lookup_type
				)
			);


			if (isset($values['contact_phone']))
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
				'menuaction' => 'property.uiproject.save',
				'id' => $id
			);

			$link_request_data = array
				(
				'menuaction' => 'property.uirequest.index',
				'query' => (isset($values['location_data']['loc1']) ? $values['location_data']['loc1'] : ''),
				'make_relation' => !!$id,
				'relation_id' => isset($id) ? $id : 0,
				'relation_type' => 'project'
			);

			$supervisor_email = array();
			if ($need_approval = isset($config->config_data['project_approval']) ? $config->config_data['project_approval'] : '')
			{
				$invoice = CreateObject('property.soinvoice');
				if (isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb')
				{
					$supervisor_id = $invoice->get_default_dimb_role_user(2, $values['ecodimb']);

					$sodimb_role_users = execMethod('property.sodimb_role_user.read', array
						(
						'dimb_id' => $values['ecodimb'],
						'role_id' => 2,
						'query_start' => date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						'get_netto_list' => true
						)
					);
					if (isset($sodimb_role_users[$values['ecodimb']][2]) && is_array($sodimb_role_users[$values['ecodimb']][2]))
					{
						foreach ($sodimb_role_users[$values['ecodimb']][2] as $supervisor_id => $entry)
						{
							$prefs = $this->bocommon->create_preferences('property', $supervisor_id);
							$supervisor_email[] = array
								(
								'id' => $supervisor_id,
								'address' => $prefs['email'],
								'default' => $entry['default_user'],
							);
						}
					}

					$supervisor2_id = $invoice->get_default_dimb_role_user(3, $values['ecodimb']);
					$prefs2 = $this->bocommon->create_preferences('property', $supervisor2_id);
					$supervisor_email[] = array
						(
						'id' => $supervisor2_id,
						'address' => $prefs2['email'],
					);
//					$supervisor_email = array_reverse($supervisor_email);
					unset($prefs);
					unset($prefs2);
					unset($invoice);
				}
				else
				{
					$supervisor_id = 0;

					if (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'])
					{
						$supervisor_id = $GLOBALS['phpgw_info']['user']['preferences']['property']['approval_from'];
					}


					if ($supervisor_id)
					{
						$prefs = $this->bocommon->create_preferences('property', $supervisor_id);
						$supervisor_email[] = array
							(
							'id' => $supervisor_id,
							'address' => $prefs['email'],
						);

						if (isset($prefs['approval_from']))
						{
							$prefs2 = $this->bocommon->create_preferences('property', $prefs['approval_from']);



							if (isset($prefs2['email']))
							{
								$supervisor_email[] = array
									(
									'id' => $prefs['approval_from'],
									'address' => $prefs2['email'],
								);
								$supervisor_email = array_reverse($supervisor_email);
							}
							unset($prefs2);
						}
						unset($prefs);
					}
				}
			}

			$project_status = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_status']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['project_status'] : '');
			$project_category = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['project_category']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['project_category'] : '');
			if (!isset($values['status']))
			{
				$values['status'] = $project_status;
			}

			if (!isset($values['cat_id']))
			{
				$values['cat_id'] = $project_category;
			}

			if (!isset($values['coordinator']))
			{
				$values['coordinator'] = $this->account;
			}

			if (!isset($values['start_date']) || !$values['start_date'])
			{
				$values['start_date'] = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			}

			$values['sum'] = isset($values['budget']) ? $values['budget'] : 0;

			if (isset($values['reserve']) && $values['reserve'] != 0)
			{
				$reserve_remainder = $values['reserve'] - $values['deviation'];
				$remainder_percent = number_format(($reserve_remainder / $values['reserve']) * 100, 2, $this->decimal_separator, '');
				$values['sum'] = $values['sum'] + $values['reserve'];
			}

			$value_remainder = $values['sum'];


			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			$external_project_data = $this->bocommon->initiate_external_project_lookup(array(
				'external_project_id' => $values['external_project_id'],
				'external_project_name' => $values['external_project_name']));


			//---datatable settings---------------------------------------------------

			$sum_actual_cost = 0;
			$sum_oblications = 0;
			$rows_per_page = 10;
			$initial_page = 1;

			$s_budget = 0;
			$s_orders = 0;
			$s_actual_cost = 0;
			$s_diff = 0;
			$s_deviation = 0;

			if ($id)
			{
				$content_budget = $this->bo->get_budget($id);
				$lang_delete = lang('Check to delete period');
				$lang_close = lang('Check to close period');
				$lang_active = lang('Check to activate period');
				$values['sum'] = 0;

				if ($content_budget && $values['periodization_id'])
				{
					$_year_count = array();
					foreach ($content_budget as $key => $row)
					{
						$_year_count[$row['year']] +=1;
						$rows_per_page = $_year_count[$row['year']];
					}
					$initial_page = floor(count($content_budget) / $rows_per_page);
				}

				foreach ($content_budget as & $b_entry)
				{
					if ($b_entry['active'])
					{
						$sum_actual_cost += $b_entry['actual_cost'];
						$sum_oblications += $b_entry['sum_oblications'];
						$values['sum'] += $b_entry['budget'];

						$s_budget += $b_entry['budget'];
						$s_orders += $b_entry['sum_orders'];
						$s_actual_cost += $b_entry['actual_cost'];
						$s_diff += $b_entry['diff'];
						$s_deviation += $b_entry['deviation_period'];
					}

					$checked = $b_entry['closed'] ? 'checked="checked"' : '';
					$checked2 = $b_entry['active'] ? 'checked="checked"' : '';

					$b_entry['delete_year'] = "<input type='checkbox' name='values[delete_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_delete}'>";
					$b_entry['closed'] = "<input type='checkbox' name='values[closed_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_close}' $checked>";
					$b_entry['closed_orig'] = "<input type='checkbox' name='values[closed_orig_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' $checked style='display:none'>";
					$b_entry['active'] = "<input type='checkbox' name='values[active_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_active}' $checked2>";
					$b_entry['active_orig'] = "<input type='checkbox' name='values[active_orig_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' $checked2 style='display:none'>";
				}
				unset($b_entry);
			}

			if (isset($values['reserve']) && $values['reserve'] != 0)
			{
				$reserve_remainder = $values['reserve'] - $values['deviation'];
				$remainder_percent = number_format(($reserve_remainder / $values['reserve']) * 100, 2, $this->decimal_separator, '.');
				$values['sum'] = $values['sum'] + $values['reserve'];
			}

			$value_remainder = $values['sum'] - $sum_actual_cost - $sum_oblications;
			$values['sum'] = number_format($values['sum'], 0, $this->decimal_separator, '.');
			$value_remainder = number_format($value_remainder, 0, $this->decimal_separator, '.');

			$s_amount_in = 0;
			$s_amount_out = 0;

			if (isset($values['project_type_id']) && $values['project_type_id'] == 3)
			{
				$content_budget = $this->bo->get_buffer_budget($id);
				foreach ($content_budget as & $b_entry)
				{
					$b_entry['entry_date'] = $GLOBALS['phpgw']->common->show_date($b_entry['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					$s_amount_in += $b_entry['amount_in'];
					$s_amount_out += $b_entry['amount_out'];
				}
				unset($b_entry);

				$budget_def = array
					(
					array('key' => 'year', 'label' => lang('year'), 'sortable' => true, 'value_footer' => lang('Sum')),
					array('key' => 'entry_date', 'label' => lang('entry date'), 'sortable' => false),
					array('key' => 'amount_in', 'label' => lang('amount in'), 'sortable' => false,
						'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($s_amount_in, 0, $this->decimal_separator, '.')),
					array('key' => 'from_project', 'label' => lang('from project'), 'sortable' => true,
						'className' => 'right', 'formatter' => 'project_link'),
					array('key' => 'amount_out', 'label' => lang('amount out'), 'sortable' => false,
						'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($s_amount_out, 0, $this->decimal_separator, '.')),
					array('key' => 'to_project', 'label' => lang('to project'), 'sortable' => true,
						'formatter' => 'project_link'),
					array('key' => 'remark', 'label' => lang('remark'), 'sortable' => true)
				);
			}
			else
			{
				$budget_def = array
					(
					array('key' => 'year', 'label' => lang('year'), 'sortable' => true, 'value_footer' => lang('Sum')),
					array('key' => 'month', 'label' => lang('month'), 'sortable' => false),
					array('key' => 'budget', 'label' => lang('budget'), 'sortable' => false, 'className' => 'right',
						'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($s_budget, 0, $this->decimal_separator, '.')),
					array('key' => 'sum_oblications', 'label' => lang('sum orders'), 'sortable' => false,
						'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($sum_oblications, 0, $this->decimal_separator, '.')),
					array('key' => 'actual_cost', 'label' => lang('actual cost'), 'sortable' => false,
						'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($s_actual_cost, 0, $this->decimal_separator, '.')),
					array('key' => 'diff', 'label' => lang('difference'), 'sortable' => false, 'className' => 'right',
						'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($s_diff, 0, $this->decimal_separator, '.')),
					array('key' => 'deviation_period', 'label' => lang('deviation'), 'sortable' => false,
						'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0', 'value_footer' => number_format($s_deviation, 0, $this->decimal_separator, '.')),
					array('key' => 'deviation_acc', 'label' => lang('deviation') . '::' . lang('accumulated'),
						'sortable' => false, 'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
					array('key' => 'deviation_percent_period', 'label' => lang('deviation') . '::' . lang('percent'),
						'sortable' => false, 'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount2'),
					array('key' => 'deviation_percent_acc', 'label' => lang('percent') . '::' . lang('accumulated'),
						'sortable' => false, 'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount2'),
					array('key' => 'closed', 'label' => lang('closed'), 'sortable' => false, 'className' => 'center',
						'formatter' => 'JqueryPortico.FormatterClosed'),
					array('key' => 'active', 'label' => lang('active'), 'sortable' => false, 'className' => 'center',
						'formatter' => 'JqueryPortico.FormatterActive'),
					array('key' => 'delete_year', 'label' => lang('Delete'), 'sortable' => false,
						'className' => 'center')
				);
			}

			$check_for_budget = (int)abs($s_budget) + (int)abs($s_amount_in);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($content_budget),
				'ColumnDefs' => $budget_def,
				'config' => array(
					array('disableFilter' => true),
					array('rows_per_page' => $rows_per_page),
//					array('disablePagination' => true),
					array('initial_page' => $initial_page)
				)
			);

			$orders_def = array
				(
				array('key' => 'workorder_id', 'label' => lang('Workorder'), 'sortable' => true,
					'formatter' => 'formatLink', 'value_footer' => lang('Sum')),
				array('key' => 'year', 'label' => lang('year'), 'sortable' => true),
				array('key' => 'title', 'label' => lang('title'), 'sortable' => true),
				array('key' => 'b_account_id', 'label' => lang('Budget account'), 'sortable' => true,
					'className' => 'right'),
				array('key' => 'budget', 'label' => lang('budget'), 'sortable' => true, 'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'cost', 'label' => lang('cost'), 'sortable' => true, 'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'addition_percentage', 'label' => '%', 'sortable' => true, 'className' => 'right'),
				array('key' => 'obligation', 'label' => lang('sum orders'), 'sortable' => true,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'actual_cost', 'label' => lang('actual cost'), 'sortable' => true,
					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'diff', 'label' => lang('difference'), 'sortable' => true, 'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0'),
				array('key' => 'vendor_name', 'label' => lang('Vendor'), 'sortable' => true),
				array('key' => 'status', 'label' => lang('Status'), 'sortable' => true),
				array('key' => 'send_order', 'label' => lang('send workorder'), 'sortable' => false,
					'className' => 'center')
			);

			$order_data = $this->bo->get_orders(array(
				'start' => 0,
				'project_id' => $id,
			//	'year' => date('Y'),
				'order' => 'year',
				'sort' => 'asc',
				'results' => -1,
				'query' => '',
				)
			);

			$first_year = false;
			$_order_data = array();
			foreach ($order_data as & $_order_entry)
			{
				if(!empty($_order_entry['year']) && ! $first_year)
				{
					$first_year = $_order_entry['year'];
				}

				if($first_year && $first_year != $_order_entry['year'])
				{
					continue;
				}

				$_order_entry['send_order'] = '';
				if (isset($_order_entry['mail_recipients'][0]) && $_order_entry['mail_recipients'][0])
				{
					$_title = implode(';', $_order_entry['mail_recipients']);
					$_order_entry['send_order'] = "<input type='checkbox' name='values[send_order][]' value='{$_order_entry['workorder_id']}' title='{$_title}'>";
				}
				$_order_data[] = $_order_entry;
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
//				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiproject.get_orders',
//						'project_id' => $id, 'year' => date('Y'), 'phpgw_return_as' => 'json'))),
				'requestUrl' => "''",
				'data' => json_encode($_order_data),
				'ColumnDefs' => $orders_def,
				'config' => array(
			//		array('disableFilter' => true),
					array('disablePagination' => true),
					array('order' => json_encode(array(1, 'asc')))
				)
			);

			$_formatter_voucher_link = isset($config->config_data['invoicehandler']) && $config->config_data['invoicehandler'] == 2 ? 'formatLink_invoicehandler_2' : '';//formatLink_voucher';

			$invoice_def = array
				(
				array('key' => 'workorder_id', 'label' => lang('Workorder'), 'sortable' => true,
					'formatter' => 'formatLink', 'value_footer' => lang('Sum')),
				array('key' => 'voucher_id', 'label' => lang('bilagsnr'), 'sortable' => true,
					'formatter' => $_formatter_voucher_link),
//				array('key' => 'voucher_out_id', 'hidden' => true),
				array('key' => 'invoice_id', 'label' => lang('invoice number'), 'sortable' => false),
				array('key' => 'vendor', 'label' => lang('vendor'), 'sortable' => false),
				array('key' => 'amount', 'label' => lang('amount'), 'sortable' => true, 'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount2'),
//				array('key' => 'approved_amount', 'label' => lang('approved amount'), 'sortable' => false,
//					'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount2'),
				array('key' => 'period', 'label' => lang('period'), 'sortable' => true),
				array('key' => 'periodization', 'label' => lang('periodization'), 'sortable' => false),
				array('key' => 'periodization_start', 'label' => lang('periodization start'),
					'sortable' => false),
				array('key' => 'currency', 'label' => lang('currency'), 'sortable' => false),
				array('key' => 'type', 'label' => lang('type'), 'sortable' => true),
				array('key' => 'budget_responsible', 'label' => lang('budget responsible'), 'sortable' => true),
				array('key' => 'budsjettsigndato', 'label' => lang('budsjettsigndato'), 'sortable' => true),
				array('key' => 'transfer_time', 'label' => lang('transfer time'), 'sortable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => json_encode(self::link(array(
					'menuaction' => 'property.uiproject.get_vouchers',
					'project_id' => $id,
					'year' => $first_year,
					'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => $invoice_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true),
					array('singleSelect' => true)
				)
			);

			/*
			 * start new notify-table
			 * Sigurd: this one is for the new notify-table
			 */

			$notify_info = execMethod('property.notify.get_jquery_table_def', array
				(
				'location_id' => $location_id,
				'location_item_id' => $id,
				'count' => count($datatable_def), //3
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.notify.update_data',
						'location_id' => $location_id, 'location_item_id' => $id, 'action' => 'refresh_notify_contact',
						'phpgw_return_as' => 'json'))),
				)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_3',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.notify.update_data',
						'location_id' => $location_id, 'location_item_id' => $id, 'action' => 'refresh_notify_contact',
						'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $notify_info['column_defs']['values'],
				'data' => json_encode(array()),
				'tabletools' => $mode == 'edit' ? $notify_info['tabletools'] : array(),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			/* end new notify-table */

			$history_def = array
				(
				array('key' => '#',	'label' => '#',	'sortable' =>true),
				array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => false),
				array('key' => 'value_user', 'label' => lang('User'), 'sortable' => false),
				array('key' => 'value_action', 'label' => lang('action'), 'sortable' => false),
				array('key' => 'value_old_value', 'label' => lang('old value'), 'sortable' => false),
				array('key' => 'value_new_value', 'label' => lang('new value'), 'sortable' => false)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_4',
				'requestUrl' => "''",
				'ColumnDefs' => $history_def,
				'data' => json_encode($record_history),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

//--------------files
			$link_file_data = array
				(
				'menuaction' => 'property.uiproject.view_file',
				'id' => $id
			);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$_files = $this->bo->get_files($id);

			$lang_view_file = lang('click to view file');
			$lang_delete_file = lang('Check to delete file');
			$z = 0;
			$content_files = array();
			foreach ($_files as $_file)
			{
				$content_files[$z]['file_name'] = "<a href=\"{$link_view_file}&amp;file_id={$_file['file_id']}\" target=\"_blank\" title=\"{$lang_view_file}\">{$_file['name']}</a>";
				$content_files[$z]['delete_file'] = "<input type=\"checkbox\" name=\"values[file_action][]\" value=\"{$_file['file_id']}\" title=\"{$lang_delete_file}\">";
				$z++;
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
				'container' => 'datatable-container_5',
//				'requestUrl' => "''",
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiproject.get_files',
						'id' => $id, 'phpgw_return_as' => 'json'))),
//				'data' => json_encode($content_files),
				'data' => json_encode(array()),
				'ColumnDefs' => $files_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

//--------------files

			$lang_delete_request_statustext = lang('Check to delete this request from this project');
			$_origin = array();
			if (isset($values['origin_data']) && $values['origin_data'])
			{
				foreach ($values['origin_data'] as $__origin)
				{
					foreach ($__origin['data'] as $_origin_data)
					{
						$_select = '';
						if ($__origin['location'] == '.project.request')
						{
							$_select = "<input type=\"checkbox\" name=\"values[delete_request][]\" value=\"{$_origin_data['id']}\" title=\"{$lang_delete_request_statustext}\">";
						}

						$_origin[] = array
							(
							'url' => "<a href='{$_origin_data['link']}'>{$_origin_data['id']} </a>",
							'type' => $__origin['descr'],
							'title' => $_origin_data['title'],
							'status' => $_origin_data['statustext'],
							//			'user'			=> $GLOBALS['phpgw']->accounts->get($_origin_data['account_id'])->__toString(),
							//			'entry_date'	=> $GLOBALS['phpgw']->common->show_date($_origin_data['entry_date'],$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
							'select' => $_select
						);
					}
				}
			}

			$origin_def = array
				(
				array('key' => 'url', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'type', 'label' => lang('type'), 'sortable' => true),
				array('key' => 'title', 'label' => lang('title'), 'sortable' => false),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => false),
				array('key' => 'select', 'label' => lang('select'), 'className' => 'center',
					'sortable' => false)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_6',
				'requestUrl' => "''",
				'data' => json_encode($_origin),
				'ColumnDefs' => $origin_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);


		$other_projects = $this->get_other_projects($id, $values['location_data']['location_code']);
			$other_projects_def = array
				(
				array('key' => 'url', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'location_code', 'label' => lang('location'), 'sortable' => true),
				array('key' => 'name', 'label' => lang('name'), 'sortable' => false),
				array('key' => 'start_date', 'label' => lang('start date'), 'sortable' => false),
				array('key' => 'coordinator', 'label' => lang('coordinator'), 'sortable' => true),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_7',
				'requestUrl' => "''",
				'data' => json_encode($other_projects),
				'ColumnDefs' => $other_projects_def,
				'config' => array(
					array('disableFilter' => true),
		//			array('disablePagination' => true),
					array('order' => json_encode(array(1, 'desc')))
				)
			);

			$attachmen_def = array(
				array(
					'key' => 'voucher_id',
					'label' => 'key',
					'hidden' => false
					),
				array(
					'key' => 'file_name',
					'label' => lang('attachments'),
					'hidden' => false,
					'sortable' => true,
					)
			);

			$datatable_def[] = array(
				'container' => 'datatable-container_8',
//				'requestUrl' => "''",
				'requestUrl' => json_encode(self::link(array(
					'menuaction' => 'property.uiproject.get_attachment',
					'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => $attachmen_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			//----------------------------------------------datatable settings--------

			$suppresscoordination = isset($config->config_data['project_suppresscoordination']) && $config->config_data['project_suppresscoordination'] ? 1 : '';

			$year = date('Y') - 1;
			$limit = $year + 8;

			while ($year < $limit)
			{
				$year_list[] = array
					(
					'id' => $year,
					'name' => $year
				);
				$year++;
			}

			$periodization_list = $this->bo->get_periodizations_with_outline();

			$sub_entry_action_data = array();
			$sub_entry_action_data = array
				(
				'menuaction' => 'property.uiworkorder.edit',
				'project_id' => $id
			);

			if ($id && !$values['project_type_id'] == 3)
			{
				$sub_entry_action_data = array
					(
					'menuaction' => 'property.uiworkorder.edit',
					'project_id' => $id
				);
			}
			else if ($id && $values['project_type_id'] == 3)
			{
				$sub_entry_action_data = array
					(
					'menuaction' => 'property.uiproject.edit',
					'bypass' => 1,
					'parent_id' => $id,
					'origin' => '.project',
					'origin_id' => $id
				);
			}
			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$project_type_id = isset($values['project_type_id']) && $values['project_type_id'] ? $values['project_type_id'] : $GLOBALS['phpgw_info']['user']['preferences']['property']['default_project_type'];
			$active_tab = phpgw::get_var('active_tab', 'string', 'REQUEST', 'location');

			$delivery_address = $values['delivery_address'];

			if(!$delivery_address && !empty($values['location_data']['loc1']))
			{
				$delivery_address = CreateObject('property.solocation')->get_delivery_address($values['location_data']['loc1']);
			}

			$data = array
				(
				'datatable_def' => $datatable_def,
				'project_types' => array('options' => $this->bo->get_project_types($project_type_id)),
				'project_type_id' => $values['project_type_id'],
				'inherit_location' => $id ? $values['inherit_location'] : 1,
				'mode' => $mode,
				'suppressmeter' => isset($config->config_data['project_suppressmeter']) && $config->config_data['project_suppressmeter'] ? 1 : '',
				'suppresscoordination' => $suppresscoordination,
				'custom_attributes' => array('attributes' => $values['attributes']),
				'lookup_functions' => isset($values['lookup_functions']) ? $values['lookup_functions'] : '',
				'b_account_group_data' => $b_account_group_data,
				'b_account_data'		=> $b_account_data,
				'ecodimb_data' => $ecodimb_data,
				'contact_data' => $contact_data,
				'tabs' => self::_generate_tabs($tabs, $active_tab, $_disable = array(
					'general' => !$id && empty($this->receipt['error']) ? true : false,
					'budget' => !$id && empty($this->receipt['error']) ? true : false,
					'coordination' => $id ? false : true,
					'documents' => $id ? false : true,
					'history' => $id ? false : true
					)),
				'value_active_tab' => $active_tab,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_origin' => isset($values['origin_data']) ? $values['origin_data'] : '',
				'value_origin_type' => isset($origin) ? $origin : '',
				'value_origin_id' => isset($origin_id) ? $origin_id : '',
				'year_list' => array('options' => $year_list),
				'order_time_span' => array('options' => $this->bo->get_order_time_span($id, $first_year)),
				'periodization_list' => array('options' => $periodization_list),
				'lang_select_request_statustext' => lang('Add request for this project'),
				'lang_request_statustext' => lang('Link to the request for this project'),
				'link_select_request' => $GLOBALS['phpgw']->link('/index.php', $link_request_data),
				'add_sub_entry_action' => $GLOBALS['phpgw']->link('/index.php', $sub_entry_action_data),
				'lang_add_sub_entry' => $values['project_type_id'] == 3 ? lang('add project') : lang('Add workorder'),
				'lang_add_sub_entry_statustext' => $values['project_type_id'] == 3 ? lang('add a project to this buffer') : lang('Add a workorder to this project'),
				'lang_no_workorders' => lang('No workorder budget'),
				'workorder_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit')),
				'record_history' => $record_history,
				'table_header_history' => $table_header_history,
				'lang_history' => lang('History'),
				'lang_no_history' => lang('No history'),
				'lang_start_date_statustext' => lang('Select the estimated end date for the Project'),
				'lang_start_date' => lang('Project start date'),
				'value_start_date' => $values['start_date'],
				'lang_end_date_statustext' => lang('Select the estimated end date for the Project'),
				'lang_end_date' => lang('Project end date'),
				'value_end_date' => isset($values['end_date']) ? $values['end_date'] : '',
				'lang_copy_project' => lang('Copy project ?'),
				'lang_copy_project_statustext' => lang('Choose Copy Project to copy this project to a new project'),
				'lang_charge_tenant' => lang('Charge tenant'),
				'lang_charge_tenant_statustext' => lang('Choose charge tenant if the tenant i to pay for this project'),
				'charge_tenant' => isset($values['charge_tenant']) ? $values['charge_tenant'] : '',
				'lang_power_meter' => lang('Power meter'),
				'lang_power_meter_statustext' => lang('Enter the power meter'),
				'value_power_meter' => isset($values['power_meter']) ? $values['power_meter'] : '',
				'value_budget' => isset($values['budget']) && $this->receipt['error'] ? $values['budget'] : '',
				'check_for_budget'	=> $check_for_budget,
				'lang_reserve' => lang('reserve'),
				'value_reserve' => isset($values['reserve']) ? $values['reserve'] : '',
				'lang_reserve_statustext' => lang('Enter the reserve'),
				'value_sum' => isset($values['sum']) ? $values['sum'] : '',
				'lang_reserve_remainder' => lang('reserve remainder'),
				'value_reserve_remainder' => isset($reserve_remainder) ? $reserve_remainder : '',
				'value_reserve_remainder_percent' => isset($remainder_percent) ? $remainder_percent : '',
//					'lang_planned_cost'					=> lang('planned cost'),
//					'value_planned_cost'				=> $values['planned_cost'],
				'location_data' => $location_data,
				'location_type' => 'form',
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.index')),
				'lang_year' => lang('Year'),
				'lang_category' => lang('category'),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_name' => lang('Name'),
				'lang_project_id' => lang('Project ID'),
				'value_project_id' => isset($id) ? $id : '',
//				'external_project_data' => $external_project_data,
				'value_external_project_id' => $values['external_project_id'],
				'value_external_project_name' => $this->_get_external_project_name($values['external_project_id']),
				'value_name' => isset($values['name']) ? $values['name'] : '',
				'lang_name_statustext' => lang('Enter Project Name'),
				'lang_other_branch' => lang('Other branch'),
				'lang_other_branch_statustext' => lang('Enter other branch if not found in the list'),
				'value_other_branch' => isset($values['other_branch']) ? $values['other_branch'] : '',
				'lang_descr_statustext' => lang('Enter a description of the project'),
				'lang_descr' => lang('Description'),
				'value_descr' => isset($values['descr']) ? $values['descr'] : '',
				'lang_remark_statustext' => lang('Enter a remark to add to the history of the project'),
				'lang_remark' => lang('remark'),
				'value_remark' => isset($values['remark']) ? $values['remark'] : '',
				'value_delivery_address' => $delivery_address,
				'lang_done_statustext' => lang('Back to the list'),
				'lang_save_statustext' => lang('Save the project'),
				'lang_no_cat' => lang('Select category'),
				'value_cat_id' => isset($values['cat_id']) ? $values['cat_id'] : '',
				'cat_select' => $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]',
					'selected' => $values['cat_id'], 'required' => isset($config->config_data['project_optional_category']) && $config->config_data['project_optional_category'] ? false : true,
					'class'=>'pure-input-1-2')),
				'lang_workorder_id' => lang('Workorder ID'),
				'lang_sum' => lang('Sum'),
				'value_remainder' => $value_remainder,
				'lang_remainder' => lang('remainder'),
				'lang_coordinator' => lang('Coordinator'),
				'lang_user_statustext' => lang('Select the coordinator the project belongs to. To do not use a category select NO USER'),
				'select_user_name' => 'values[coordinator]',
				'lang_no_user' => lang('Select coordinator'),
				'user_list' => $this->bocommon->get_user_list_right2('select', 4, $values['coordinator'], $this->acl_location),
				'status_list' => $this->bo->select_status_list('select', $values['status']),
				'status_name' => 'values[status]',
				'status_required' => true,
				'lang_no_status' => lang('Select status'),
				'lang_status' => lang('Status'),
				'lang_status_statustext' => lang('What is the current status of this project ?'),
				'lang_confirm_status' => lang('Confirm status'),
				'lang_confirm_statustext' => lang('Confirm status to the history'),
				'branch_list' => $this->bo->select_branch_p_list((isset($id) ? $id : '')),
				'lang_branch' => lang('branch'),
				'lang_branch_statustext' => lang('Select the branches for this project'),
				'key_responsible_list' => $this->bo->select_branch_list((isset($values['key_responsible']) ? $values['key_responsible'] : '')),
				'lang_no_key_responsible' => lang('Select key responsible'),
				'lang_key_responsible' => lang('key responsible'),
				'lang_key_responsible_statustext' => lang('Select the key responsible for this project'),
				'key_fetch_list' => $this->bo->select_key_location_list((isset($values['key_fetch']) ? $values['key_fetch'] : '')),
				'lang_no_key_fetch' => lang('Where to fetch the key'),
				'lang_key_fetch' => lang('key fetch location'),
				'lang_key_fetch_statustext' => lang('Select where to fetch the key'),
				'key_deliver_list' => $this->bo->select_key_location_list((isset($values['key_deliver']) ? $values['key_deliver'] : '')),
				'lang_no_key_deliver' => lang('Where to deliver the key'),
				'lang_key_deliver' => lang('key deliver location'),
				'lang_key_deliver_statustext' => lang('Select where to deliver the key'),
				'need_approval' => $need_approval,
				'lang_ask_approval' => lang('Ask for approval'),
				'lang_ask_approval_statustext' => lang('Check this to send a mail to your supervisor for approval'),
				'value_approval_mail_address' => $supervisor_email,
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'base_java_url' => "{menuaction:'property.bocommon.get_vendor_email',phpgw_return_as:'json'}",
				'location_item_id' => $id,
				'edit_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.edit',
					'id' => $id)),
				'lang_edit_statustext' => lang('Edit this entry '),
				'lang_edit' => lang('Edit'),
				'decimal_separator' => $this->decimal_separator,
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file')),
				'multiple_uploader' => true,
				'multi_upload_parans' => "{menuaction:'property.uiproject.build_multi_upload_file', id:'{$id}'}",
				'street_name' => $values['location_data']['street_name'],
				'street_number' => $values['location_data']['street_number'],
			);
			if ($auto_create)
			{
				$location = explode('-', $values['location_data']['location_code']);

				$level = count($location);
				for ($i = 1; $i < $level + 1; $i++)
				{
					$values['location']["loc$i"] = $location[($i - 1)];
				}

				$values['location_name'] = $values['location_data']["loc{$level}_name"];
				$values['extra'] = $values['p'][0];

				unset($values['location_data']);


				unset($values['p']);

				$receipt = $this->bo->save($values, 'add', array());

				if (!$receipt['error'])
				{
					$id = $receipt['id'];
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiworkorder.edit',
						'project_id' => $id));
				}
			}

			$appname = lang('project');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');
			phpgwapi_jquery::load_widget('autocomplete');

			self::add_javascript('property', 'portico', 'project.edit.js');
			self::render_template_xsl(array('project', 'datatable_inline', 'files', 'attributes_form'), array(
				'edit' => $data));
		}

		public function get_orders()
		{
			$project_id = phpgw::get_var('project_id', 'int');
			if (empty($project_id))
			{
				$result_data = array('results' => array());
				$result_data['total_records'] = 0;
				$result_data['draw'] = 0;

				return $this->jquery_results($result_data);
			}

			$values = $this->bo->get_orders(array(
				'project_id' => $project_id,
				'year' =>phpgw::get_var('year', 'int'),
				'results' => -1,
				)
			);
			foreach ($values as & $_order_entry)
			{
				$_order_entry['send_order'] = '';
				if (isset($_order_entry['mail_recipients'][0]) && $_order_entry['mail_recipients'][0])
				{
					$_title = implode(';', $_order_entry['mail_recipients']);
					$_order_entry['send_order'] = "<input type='checkbox' name='values[send_order][]' value='{$_order_entry['workorder_id']}' title='{$_title}'>";
				}
			}

			$total_records = $this->bo->so->total_records;

			$result_data = array('results' => $values);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = phpgw::get_var('draw', 'int');

			return $this->jquery_results($result_data);
		}

		public function get_vouchers()
		{
			$project_id = phpgw::get_var('project_id', 'int');
			if (empty($project_id))
			{
				$result_data = array('results' => array());
				$result_data['total_records'] = 0;
				$result_data['draw'] = 0;

				return $this->jquery_results($result_data);
			}

			$year = phpgw::get_var('year', 'int');
			$draw = phpgw::get_var('draw', 'int');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$active_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array(
				'project_id' => $project_id,
				'year' => $year,
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				)
			);
			$historical_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array(
				'project_id' => $project_id,
				'year' => $year,
				'paid' => true,
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				)
			);
			$invoices = array_merge($active_invoices, $historical_invoices);
			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();

			foreach ($invoices as $entry)
			{

				if(isset($config->config_data['invoicehandler']) && $config->config_data['invoicehandler'] == 2)
				{
					$voucher_id = $entry['transfer_time'] ? -1 * $entry['voucher_id'] : $entry['voucher_id'];
				}
				else
				{
					$voucher_id = $entry['external_voucher_id'];
				}


				$values[] = array
					(
					'voucher_id' => $voucher_id,
					'voucher_out_id' => $entry['voucher_out_id'],
					'workorder_id' => $entry['workorder_id'],
					'status' => $entry['status'],
					'period' => $entry['period'],
					'periodization' => $entry['periodization'],
					'periodization_start' => $entry['periodization_start'],
					'invoice_id' => $entry['invoice_id'],
					'budget_account' => $entry['budget_account'],
					'dima' => $entry['dima'],
					'dimb' => $entry['dimb'],
					'dimd' => $entry['dimd'],
					'type' => $entry['type'],
					'amount' => $entry['amount'],
					'approved_amount' => $entry['approved_amount'],
					'vendor' => $entry['vendor'],
					'external_project_id' => $entry['project_id'],
					'currency' => $entry['currency'],
					'budget_responsible' => $entry['budget_responsible'],
					'budsjettsigndato' => $entry['budsjettsigndato'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['budsjettsigndato']), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
					'transfer_time' => $entry['transfer_time'] ? $GLOBALS['phpgw']->common->show_date(strtotime($entry['transfer_time']), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']) : '',
				);
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$total_records = count($values);

				$result_data = array('results' => $values);

				$result_data['total_records'] = $total_records;
				$result_data['draw'] = $draw;

				return $this->jquery_results($result_data);

			}
			else
			{
				return $values;			
			}

		}

		function delete()
		{
			$project_id = phpgw::get_var('project_id', 'int');

//			$project = $this->bo->read_single($project_id);

			if (!$this->acl_delete)// || !$this->bocommon->check_perms2($project['coordinator'], $this->bo->so->grants, PHPGW_ACL_DELETE))
			{
				phpgw::no_access();
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($project_id);
				return "project_id " . $project_id . " " . lang("has been deleted");
			}
		}

		function bulk_update_status()
		{
			if (!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => PHPGW_ACL_EDIT, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::project_bulk_update_status';

			$start_date = phpgw::get_var('start_date');
			$end_date = phpgw::get_var('end_date');
			$get_list = phpgw::get_var('get_list', 'bool', 'POST');
			$execute = phpgw::get_var('execute', 'bool', 'POST');
			$status_filter = phpgw::get_var('status_filter');
			$status_new = phpgw::get_var('status_new');
			$type = phpgw::get_var('type', 'string', 'REQUEST', 'project');
			$ecodimb = phpgw::get_var('ecodimb');
			$ids = phpgw::get_var('ids');
			$paid = phpgw::get_var('paid', 'bool', 'POST');
			$closed_orders = phpgw::get_var('closed_orders', 'bool', 'POST');
			$transfer_budget = phpgw::get_var('transfer_budget', 'integer');
			$b_account_id = phpgw::get_var('b_account_id', 'integer');
			$b_account_name = phpgw::get_var('b_account_name');

			if (isset($_POST['coordinator']))
			{
				$coordinator = phpgw::get_var('coordinator', 'int');
			}
			else
			{
				$coordinator = $this->account;
			}

			$new_coordinator = phpgw::get_var('new_coordinator', 'int');

			$ids = $ids ? $ids : array();

			$new_budget = array();

			foreach ($ids as $id)
			{
				$new_budget[$id]['budget_amount'] = phpgw::get_var("{$id}::budget_amount");
				$new_budget[$id]['obligation'] = phpgw::get_var("{$id}::obligation");
				$new_budget[$id]['order_amount'] = phpgw::get_var("{$id}::order_amount");
				$new_budget[$id]['latest_year'] = phpgw::get_var("{$id}::latest_year");
			}

			$link_data = array
				(
				'menuaction' => 'property.uiproject.index'
			);

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			if (($execute || $get_list) && $type)
			{
				$list = $this->bo->bulk_update_status($start_date, $end_date, $status_filter, $status_new, $execute, $type, $coordinator, $new_coordinator, $ids, $paid, $closed_orders, $ecodimb, $transfer_budget, $new_budget, $b_account_id);
			}

			foreach ($list as &$entry)
			{
				$_obligation = '';
				$entry['new_budget'] = '';

				if ($entry['project_type_id'] == 1 || $entry['project_type_id'] == 4 || $entry['continuous']) // operation or continuous
				{
					$_obligation = 0;
					$_order = 0;

					if (!$entry['closed'] && $type == 'project')
					{
						$_budget_arr = $this->bo->get_budget($entry['id']);
					}

					if (!$entry['closed'] && $type == 'workorder')
					{
						$_budget_arr = execMethod('property.soworkorder.get_budget', $entry['id']);
					}

					if ($_budget_arr)
					{
						foreach ($_budget_arr as $_budget_entry)
						{
							if ($_budget_entry['active'])
							{
								$_obligation += $_budget_entry['sum_oblications'];
								$_order += $_budget_entry['sum_orders'];
							}
						}

						$_obligation = round($_obligation);

						$entry['new_budget'] = "<input type='text'     name='{$entry['id']}::budget_amount' value='{$_obligation}' title=''></input>";
						$entry['new_budget'] .= "<input type='hidden'  name='{$entry['id']}::obligation' value='{$_obligation}' ></input>";
						$entry['new_budget'] .= "<input type='hidden'  name='{$entry['id']}::order_amount' value='{$_order}'></input>";
						$entry['new_budget'] .= "<input type='hidden'  name='{$entry['id']}::latest_year' value='{$entry['latest_year']}'></input>";
					}
				}
				else if ($entry['project_type_id'] == 2)
				{
					$entry['new_budget'] = 'auto';
					$entry['new_budget'] .= "<input type='hidden' class='myValuesForPHP' id='{$entry['id']}::latest_year' name='{$entry['id']}::latest_year' value='{$entry['latest_year']}'></input>";
				}
				else if ($entry['project_type_id'] == 3)
				{
					$entry['budget'] = '';
				}

				$entry['obligation'] = $_obligation;

				$entry['link'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "property.ui{$type}.edit",
					'id' => $entry['id']));
			}

			$total_records = count($list);

			switch ($type)
			{
				case 'project':
					$lang_coordinator = lang('coordinator');
					$lang_new_coordinator = lang('new coordinator');
					$myColumnDefs = array
						(
						array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'formatter' => 'JqueryPortico.formatLink'),
						array('key' => 'coordinator_name', 'label' => lang('coordinator'), 'sortable' => false),
						array('key' => 'start_date', 'label' => lang('date'), 'sortable' => false),
						array('key' => 'title', 'label' => lang('title'), 'sortable' => true),
						array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
						array('key' => 'num_open', 'label' => lang('open'), 'sortable' => true, 'className' => 'right',
							'formatter' => 'JqueryPortico.FormatterAmount0'),
						array('key' => 'project_type', 'label' => lang('project type'), 'sortable' => true),
						array('key' => 'budget', 'label' => lang('budget'), 'sortable' => false),
						array('key' => 'obligation', 'label' => lang('obligation'), 'sortable' => true,
							'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
						array('key' => 'new_budget', 'label' => lang('new'), 'sortable' => false),
						array('key' => 'select', 'label' => lang('select'), 'sortable' => false, 'className' => 'center',
							'formatter' => 'myFormatterCheck')
					);
					$b_account_data = array();
					$td_count = 9;
					break;

				case 'workorder':
					$lang_coordinator = lang('janitor');
					$lang_new_coordinator = lang('new janitor');
					$lang_actual_cost = $paid ? lang('actual cost') . ' ' . lang('total') : lang('actual cost') . ' ' . (date('Y') - 1);

					$myColumnDefs = array
						(
						array('key' => 'project_id', 'label' => lang('project'), 'sortable' => true),
						array('key' => 'id', 'label' => lang('id'), 'sortable' => true, 'formatter' => 'JqueryPortico.formatLink'),
						array('key' => 'coordinator_name', 'label' => lang('janitor'), 'sortable' => false),
						array('key' => 'start_date', 'label' => lang('date'), 'sortable' => false),
						array('key' => 'title', 'label' => lang('title'), 'sortable' => true),
						array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
						array('key' => 'project_type', 'label' => lang('project type'), 'sortable' => true),
						array('key' => 'b_account_id', 'label' => lang('budget account'), 'sortable' => true),
						array('key' => 'budget', 'label' => lang('budget'), 'sortable' => false),
						array('key' => 'obligation', 'label' => lang('obligation'), 'sortable' => true,
							'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
						array('key' => 'continuous', 'label' => lang('continuous'), 'sortable' => true),
						array('key' => 'new_budget', 'label' => lang('new'), 'sortable' => false),
						array('key' => 'actual_cost', 'label' => $lang_actual_cost, 'sortable' => true,
							'className' => 'right', 'formatter' => 'JqueryPortico.FormatterAmount0'),
						array('key' => 'select', 'label' => lang('select'), 'sortable' => false, 'className' => 'center',
							'formatter' => 'myFormatterCheck')
					);

					$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
						'b_account_id' => $b_account_id,
						'disabled' => '',
						'parent' => $project['b_account_id'],
						'type' => 'form'
						)
					);
					$td_count = 12;
					break;
			}

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($list),
				'tabletools' => $tabletools,
				'ColumnDefs' => $myColumnDefs,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$user_list = $this->bocommon->get_user_list_right2('select', PHPGW_ACL_EDIT, $coordinator, $this->acl_location);

			switch ($type)
			{
				case 'project':
					$status_list_filter = execMethod('property.bogeneric.get_list', array('type' => 'project_status'));
					$status_list_new = execMethod('property.bogeneric.get_list', array('type' => 'project_status',
						'selected' => $status_new));
					break;
				case 'workorder':
					$status_list_filter = execMethod('property.bogeneric.get_list', array('type' => 'workorder_status'));
					$status_list_new = execMethod('property.bogeneric.get_list', array('type' => 'workorder_status',
						'selected' => $status_new));
					break;
				default:
					$status_list_filter = array();
			}

			if ($status_list_filter)
			{
				array_unshift($status_list_filter, array('id' => 'open', 'name' => lang('open')));
			}

			$status_list_filter = $this->bocommon->select_list($status_filter, $status_list_filter);

			$type_array = array
				(
				array
					(
					'id' => '0',
					'name' => lang('select')
				),
				array
					(
					'id' => 'workorder',
					'name' => lang('workorder')
				),
				array
					(
					'id' => 'project',
					'name' => lang('project')
				)
			);

			foreach ($type_array as &$entry)
			{
				$entry['selected'] = $entry['id'] == $type ? 1 : 0;
			}

			$year = date('Y') - 2;
			$limit = $year + 4;

			while ($year < $limit)
			{
				$year_list[] = array
					(
					'id' => $year,
					'name' => $year
				);
				$year++;
			}

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$data = array
				(
				'datatable_def' => $datatable_def,
				'year_list' => array('options' => $year_list),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'update_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.bulk_update_status')),
				'status_list_filter' => array('options' => $status_list_filter),
				'status_list_new' => array('options' => $status_list_new),
				'type_list' => array('options' => $type_array),
				'user_list' => array('options' => $user_list),
				'ecodimb_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'dimb', 'selected' => $ecodimb))),
				'start_date' => $start_date,
				'end_date' => $end_date,
				'total_records' => $total_records,
				'paid' => $paid,
				'closed_orders' => $closed_orders,
				'check_paid' => $type == 'workorder' ? 1 : 0,
				'check_closed_orders' => $type == 'project' ? 1 : 0,
				'type' => $type,
				'b_account_data' => $b_account_data,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'lang_coordinator' => $lang_coordinator,
				'lang_new_coordinator' => $lang_new_coordinator,
			);

			$appname = lang('project');
			$function_msg = lang('bulk update status');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'project.bulk_update_status.js');
			self::render_template_xsl(array('project', 'datatable_inline'), array('bulk_update_status' => $data));
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

		public function get_external_project()
		{
			if (!$this->acl_read)
			{
				return;
			}
			return $this->bocommon->get_external_project();
		}

		public function get_ecodimb()
		{
			if (!$this->acl_read)
			{
				return;
			}
			return $this->bocommon->get_ecodimb();
		}

		private function _get_external_project_name( $id )
		{
			return $this->bocommon->get_external_project_name($id);
		}

		protected function _generate_tabs( $tabs_ = array(), $active_tab = 'location', $_disable = array() )
		{
			$tabs = array
				(
				'location' => array('label' => lang('location'), 'link' => '#location'),//, 'function' => "set_tab('location')"),
				'general' => array('label' => lang('general'), 'link' => '#general'),//, 'function' => "set_tab('general')"),
				'budget' => array('label' => lang('Time and budget'), 'link' => '#budget'),//, 'function' => "set_tab('budget')"),
				'coordination' => array('label' => lang('coordination'), 'link' => '#coordination'),//,'function' => "set_tab('coordination')"),
				'documents' => array('label' => lang('documents'), 'link' => '#documents'),//, 'function' => "set_tab('documents')"),
				'history' => array('label' => lang('history'), 'link' => '#history'),//, 'function' => "set_tab('history')"),
			);

			$tabs = array_merge($tabs, $tabs_);
			foreach ($_disable as $tab => $disable)
			{
				if ($disable)
				{
					$tabs[$tab]['disable'] = true;
				}
			}

			return phpgwapi_jquery::tabview_generate($tabs, $active_tab);
		}

		function get_attachment()
		{
			$voucher_id = phpgw::get_var('voucher_id');

			$attachmen_list = array();
			if ($voucher_id)
			{
				$invoice_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
				$directory_attachment = rtrim($invoice_config->config_data['import']['local_path'], '/') . "/attachment/{$voucher_id}/";
				try
				{
					$dir = new DirectoryIterator($directory_attachment);
					if (is_object($dir))
					{
						foreach ($dir as $file)
						{
							if ($file->isDot() || !$file->isFile() || !$file->isReadable())
							{
								continue;
							}

							$url = self::link(array(
								'menuaction'=> 'property.uitts.show_attachment',
								'file_name' => urlencode((string)$file),
								'key'=> $voucher_id
								));

							$attachmen_list[] = array(
								'voucher_id'	=> $voucher_id,
								'file_name'		=> "<a href='{$url}' target='_blank'>" . (string)$file . "</a>"
							);
						}
					}
				}
				catch (Exception $e)
				{

				}
			}

			$total_records = count($attachmen_list);

			return array
				(
				'data' => $attachmen_list,
				'draw' => phpgw::get_var('draw', 'int'),
				'recordsTotal' => $total_records,
				'recordsFiltered' => $total_records
			);

		}

	}