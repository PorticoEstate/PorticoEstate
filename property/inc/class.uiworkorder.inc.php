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
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uiworkorder extends phpgwapi_uicommon_jquery
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
		var $criteria_id;
		var $filter_year;
		var $account;
		var $public_functions = array
			(
			'columns' => true,
			'query' => true,
			'download' => true,
			'index' => true,
			'view' => true,
			'add' => true,
			'edit' => true,
			'save' => true,
			'delete' => true,
			'view_file' => true,
			'columns' => true,
			'add_invoice' => true,
			'recalculate' => true,
			'save' => true,
			'get_vendor_contract'=> true,
			'get_eco_service'=> true,
			'get_ecodimb'	=> true,
			'get_b_account'	=> true,
			'get_unspsc_code'=> true,
			'receive_order'	=> true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true,
			'get_files'				=> true,
			'view_image'			=> true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::project::workorder';

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo = CreateObject('property.boworkorder', true);
			$this->bocommon = CreateObject('property.bocommon');
			$this->cats = & $this->bo->cats;
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.project.workorder';
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
			$this->start_date = $this->bo->start_date;
			$this->end_date = $this->bo->end_date;
			$this->b_group = $this->bo->b_group;
			$this->ecodimb = $this->bo->ecodimb;
			$this->paid = $this->bo->paid;
			$this->b_account = $this->bo->b_account;
			$this->district_id = $this->bo->district_id;
			$this->criteria_id = $this->bo->criteria_id;
			$this->obligation = $this->bo->obligation;
			$this->filter_year = $this->bo->filter_year;
			$this->decimal_separator = ',';
		}

		function download()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
				return;
			}

			$values = $this->query();
			$uicols = $this->bo->uicols;
			$this->bocommon->download($values, $uicols['name'], $uicols['descr'], $uicols['input_type']);
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array(
					'menuaction' => 'property.uilocation.stop',
					'perm' => 1,
					'acl_location' => $this->acl_location));
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

		function get_files()
		{
			$id = phpgw::get_var('id', 'int');

			if (!$this->acl_read)
			{
				return;
			}

			$link_file_data = array
				(
				'menuaction' => 'property.uiworkorder.view_file',
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
							'menuaction' => 'property.uiworkorder.view_image',
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

			$options['base_dir'] = 'workorder/'.$id;
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/property/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => 'property.uiworkorder.handle_multi_upload_file', 'id' => $id)));
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

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.handle_multi_upload_file', 'id' => $id));

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
			$GLOBALS['phpgw']->xslttpl->add_file(array(
				'columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values = phpgw::get_var('values');

			$GLOBALS['phpgw']->preferences->set_account_id($this->account, true);

			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->add('property', 'workorder_columns', $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();
				$receipt['message'][] = array(
					'msg' => lang('columns is updated'));
			}

			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => 'property.uiworkorder.columns',
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
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array(
				'columns' => $data));
		}

		private function _get_filters( $selected = 0 )
		{
			$values_combo_box = array();
			$combos = array();

			$values_combo_box[0] = $this->bocommon->select_district_list('filter', $this->district_id);
			$default_value = array(
				'id' => '',
				'name' => lang('no district'));
			array_unshift($values_combo_box[0], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'district_id',
				'extra' => '',
				'text' => lang('district'),
				'list' => $values_combo_box[0]
			);


			$_cats = $this->cats->return_sorted_array(0, false, '', '', '', false, false);
			$values_combo_box[1] = array();
			foreach ($_cats as $_cat)
			{
				if ($_cat['level'] == 0)
				{
					$values_combo_box[1][] = $_cat;
				}
			}
			$default_value = array(
				'id' => '',
				'name' => lang('no category'));
			array_unshift($values_combo_box[1], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'cat_id',
				'extra' => '',
				'text' => lang('Category'),
				'list' => $values_combo_box[1]
			);


			$values_combo_box[2] = $this->bo->select_status_list('filter', $this->status_id);
			array_unshift($values_combo_box[2], array(
				'id' => 'open',
				'name' => lang('open')));
			array_unshift($values_combo_box[2], array(
				'id' => 'all',
				'name' => lang('all')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'status_id',
				'extra' => '',
				'text' => lang('status'),
				'list' => $values_combo_box[2]
			);
			//

			$values_combo_box[3] = $this->bocommon->select_category_list(array(
				'format' => 'filter',
				'selected' => $this->wo_hour_cat_id,
				'type' => 'wo_hours',
				'order' => 'id'));
			$default_value = array(
				'id' => '',
				'name' => lang('no hour category'));
			array_unshift($values_combo_box[3], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'wo_hour_cat_id',
				'extra' => '',
				'text' => lang('Hour Category'),
				'list' => $values_combo_box[3]
			);

			$values_combo_box[4] = $this->bo->get_criteria_list($this->criteria_id);
			$default_value = array(
				'id' => '',
				'name' => lang('no criteria'));
			array_unshift($values_combo_box[4], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'criteria_id',
				'extra' => '',
				'text' => lang('search criteria'),
				'list' => $values_combo_box[4]
			);

			$values_combo_box[5] = execMethod('property.boproject.get_filter_year_list', $this->filter_year);
			array_unshift($values_combo_box[5], array(
				'id' => 'all',
				'name' => lang('all') . ' ' . lang('year')));
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'filter_year',
				'extra' => '',
				'text' => lang('Year'),
				'list' => $values_combo_box[5]
			);

			$values_combo_box[6] = $this->bo->get_user_list($this->filter);
			array_unshift($values_combo_box[6], array(
				'id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'name' => lang('mine orders')));
			$default_value = array(
				'id' => '',
				'name' => lang('no user'));
			array_unshift($values_combo_box[6], $default_value);
			$combos[] = array
				(
				'type' => 'filter',
				'name' => 'filter',
				'extra' => '',
				'text' => lang('User'),
				'list' => $values_combo_box[6]
			);
			return $combos;
		}

		public function query()
		{
			$start_date = urldecode($this->start_date);
			$end_date = urldecode($this->end_date);

			if ($start_date && empty($end_date))
			{
				$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $dateformat);
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array
				(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'start_date' => $start_date,
				'end_date' => $end_date
			);

			$values = $this->bo->read($params);
			if ($export)
			{
				return $values;
			}
			$result_data = array(
				'results' => $values);
			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array(
					'menuaction' => 'property.uilocation.stop',
					'perm' => 1,
					'acl_location' => $this->acl_location));
			}

			$lookup = '';

			$default_district = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'] : '');

			if ($default_district && !isset($_REQUEST['district_id']))
			{
				$this->bo->district_id = $default_district;
				$this->district_id = $default_district;
			}

			$start_date = urldecode($this->start_date);
			$end_date = urldecode($this->end_date);

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');
			self::add_javascript('property', 'portico', 'workorder.index.js');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('datepicker');

			$appname = lang('Workorder');
			$function_msg = lang('list workorder');
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
								'value' => $start_date,
								'text' => lang('from')
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'end_date',
								'name' => 'end_date',
								'value' => $end_date,
								'text' => lang('to')
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uiworkorder.index',
						'district_id' => $this->district_id,
						'start_date' => $start_date,
						'end_date' => $end_date,
						'b_group' => $this->b_group,
						'b_account' => $this->b_account,
						'paid' => $this->paid,
						'obligation' => $this->obligation,
						'ecodimb'	=> $this->ecodimb,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array(
						'menuaction' => 'property.uiworkorder.download',
						'start_date' => $start_date,
						'end_date' => $end_date,
						'b_group' => $this->b_group,
						'b_account' => $this->b_account,
						'paid' => $this->paid,
						'obligation' => $this->obligation,
						'ecodimb'	=> $this->ecodimb,
						'export' => true,
						'allrows' => true
					)),
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'property.uiworkorder.columns', appname:'{$this->bo->appname}',type:'{$this->type}', type_id:'{$this->type_id}'}, {closeAction:'reload'})"),
					'new_item' => self::link(array(
						'menuaction' => 'property.uiworkorder.add'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$filters = $this->_get_filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$this->bo->read(array(
				'dry_run' => true));
			$uicols = $this->bo->uicols;

			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$uicols_count = count($uicols['name']);
			for ($k = 0; $k < $uicols_count; $k++)
			{
				$params = array
					(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden' ) ? true : false
				);

				#if(!empty($uicols['formatter'][$k]))
				#{
				#    $params['formatter'] = $uicols['formatter'][$k];
				#}

				switch ($uicols['name'][$k])
				{
					case 'project_id':
						$params['formatter'] = 'linktToProject';
						break;
					case 'workorder_id':
						$params['formatter'] = 'linktToOrder';
						break;
					case 'loc1':
						$params['formatter'] = 'JqueryPortico.searchLink';
						break;
					case 'actual_cost':
					case 'obligation':
					case 'combined_cost':
					case 'diff':
					case 'budget':
						$params['formatter'] = 'JqueryPortico.FormatterAmount0';
						break;
					default:
						break;
				}
				array_push($data['datatable']['field'], $params);
			}


			// NO pop-up
			if (!$lookup)
			{
				$parameters = array
					(
					'parameter' => array
						(
						array
							(
							'name' => 'id',
							'source' => 'workorder_id'
						),
					)
				);

				$parameters2 = array
					(
					'parameter' => array
						(
						array
							(
							'name' => 'workorder_id',
							'source' => 'workorder_id'
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
							'menuaction' => 'property.uiworkorder.view'
						)),
						'parameters' => json_encode($parameters)
					);
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'view',
						'text' => lang('open view in new window'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiworkorder.view'
						)),
						'target' => '_blank',
						'parameters' => json_encode($parameters)
					);

					$jasper = execMethod('property.sojasper.read', array(
						'location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

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
						'text' => lang('edit'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiworkorder.edit'
						)),
						'parameters' => json_encode($parameters)
					);
					$data['datatable']['actions'][] = array
						(
						'my_name' => 'edit',
						'text' => lang('open edit in new window'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiworkorder.edit'
						)),
						'target' => '_blank',
						'parameters' => json_encode($parameters)
					);

					$data['datatable']['actions'][] = array
						(
						'my_name' => 'calculate',
						'text' => lang('calculate'),
						'action' => $GLOBALS['phpgw']->link('/index.php', array
							(
							'menuaction' => 'property.uiwo_hour.index'
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
							'menuaction' => 'property.uiworkorder.delete'
						)),
						'parameters' => json_encode($parameters)
					);
				}
				unset($parameters);
			}

			self::render_template_xsl('datatable_jquery', $data);
		}
		/*
		 * Overrides with incoming data from POST
		 */

		private function _populate()
		{
			$id = phpgw::get_var('id');
			$boproject = CreateObject('property.boproject');
			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();
			$project_id = phpgw::get_var('project_id', 'int');
			$values = phpgw::get_var('values');
			$values['vendor_id'] = phpgw::get_var('vendor_id', 'int');
			$values['vendor_name'] = phpgw::get_var('vendor_name', 'string');
			$values['event_id'] = phpgw::get_var('event_id', 'int');
			$origin = phpgw::get_var('origin');
			$origin_id = phpgw::get_var('origin_id', 'int');

			if (!$id)
			{
				$p_entity_id = phpgw::get_var('p_entity_id', 'int');
				$p_cat_id = phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id'] = $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id'] = $p_cat_id;
				$values['p'][$p_entity_id]['p_num'] = phpgw::get_var('p_num');
			}


			if ($project_id && !isset($values['project_id']))
			{
				$values['project_id'] = $project_id;
			}

			$project = (isset($values['project_id']) ? $boproject->read_single_mini($values['project_id']) : '');

			if ($GLOBALS['phpgw']->session->is_repost())
			{
				$this->receipt['error'][] = array(
					'msg' => lang('Hmm... looks like a repost!'));
			}

			if (isset($config->config_data['invoice_acl']) && $config->config_data['invoice_acl'] == 'dimb' && !empty($values['do_approve']))
			{
				if (!$this->acl_manage)
				{
					foreach ($values['do_approve'] as $_account_id => $_dummy)
					{
						if($_account_id != $this->account)
						{
							continue;
						}

						$approve_role = execMethod('property.boinvoice.check_role', $project['ecodimb'] ? $project['ecodimb'] : $values['ecodimb']);
						if (!$approve_role['is_janitor'] && !$approve_role['is_supervisor'] && !$approve_role['is_budget_responsible'])
						{
							$this->receipt['error'][] = array(
								'msg' => lang('you are not approved for this dimb: %1', $project['ecodimb'] ? $project['ecodimb'] : $values['ecodimb'] ));
							$error_id = true;
						}

						if (!$approve_role['is_supervisor'] && !$approve_role['is_budget_responsible'])
						{
							$this->receipt['error'][] = array(
								'msg' => lang('you do not have permission to approve this order'));
							$values['approved'] = false;
							$error_id = true;
						}
					}
				}
			}

			$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');

			if (is_array($insert_record))
			{
				$values = $this->bocommon->collect_locationdata($values, $insert_record);
			}

			if (isset($values['new_project_id']) && $values['new_project_id'] && !$boproject->read_single_mini($values['new_project_id']))
			{
				$this->receipt['error'][] = array(
					'msg' => lang('the project %1 does not exist', $values['new_project_id']));
			}

			if (isset($values['new_project_id']) && $values['new_project_id'] && $values['new_project_id'] == $values['project_id'])
			{
				unset($values['new_project_id']);
			}

			if (!$values['title'])
			{
				$this->receipt['error'][] = array(
					'msg' => lang('Please enter a workorder title !'));
			}
			if (!$values['project_id'])
			{
				$this->receipt['error'][] = array(
					'msg' => lang('Please select a valid project !'));
			}

			if (empty($values['ecodimb']))
			{
				$values['ecodimb'] = $project['ecodimb'];
				if(!$values['ecodimb'])
				{
					$this->receipt['error'][] = array('msg' => lang('Please select dimb!'));
					$error_id = true;
				}
				else
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
			}

			if (!$values['status'])
			{
				$this->receipt['error'][] = array(
					'msg' => lang('Please select a status !'));
			}

			if (isset($config->config_data['workorder_require_vendor']) && $config->config_data['workorder_require_vendor'] == 1 && !$values['vendor_id'])
			{
				$this->receipt['error'][] = array(
					'msg' => lang('no vendor'));
			}

			if (!$values['b_account_id'])
			{
				$this->receipt['error'][] = array(
					'msg' => lang('Please select a budget account !'));
			}
			else
			{
				$_b_account = execMethod('property.bogeneric.read_single', array(
					'id' => $values['b_account_id'],
					'location_info' => array(
						'type' => 'budget_account')));
				if (!$_b_account || !$_b_account['active'])
				{
					$values['b_account_id'] = '';
					$values['b_account_name'] = '';
					$this->receipt['error'][] = array(
						'msg' => lang('Please select a valid budget account !'));
				}
			}

			if (isset($values['budget']) && $values['budget'] && !ctype_digit(ltrim($values['budget'], '-')))
			{
				$this->receipt['error'][] = array(
					'msg' => lang('budget') . ': ' . lang('Please enter an integer !'));
			}

			if (!$id && (!$values['contract_sum'] && !$values['budget']))
			{
				$this->receipt['error'][] = array(
					'msg' => lang('please enter either a budget or contrakt sum'));
			}

			if (((int)$values['contract_sum'] && (int)$values['budget']) && (abs($values['contract_sum']) > abs($values['budget'])))
			{
				$values['budget'] = $values['contract_sum'];
			}

			if (isset($values['addition_rs']) && $values['addition_rs'] && !ctype_digit(ltrim($values['addition_rs'], '-')))
			{
				$this->receipt['error'][] = array(
					'msg' => lang('Rig addition') . ': ' . lang('Please enter an integer !'));
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$_category = $this->cats->return_single($values['cat_id']);
				if (!$_category[0]['active'])
				{
					$this->receipt['error'][] = array(
						'msg' => lang('invalid category'));
				}
			}

			if (isset($values['addition_percentage']) && $values['addition_percentage'] && !ctype_digit($values['addition_percentage']))
			{
				$this->receipt['error'][] = array(
					'msg' => lang('Percentage addition') . ': ' . lang('Please enter an integer !'));
			}

			if (!empty($values['approval']) && !empty($config->config_data['workorder_approval']))
			{
				if (!empty($config->config_data['workorder_approval_status']))
				{
					$values['status'] = $config->config_data['workorder_approval_status'];
				}
			}

			return $values;
		}

		private function _handle_files( $values )
		{
			$id = (int)$values['id'];
			if (empty($id))
			{
				throw new Exception('uiworkorder::_handle_files() - missing id');
			}

			$bofiles = CreateObject('property.bofiles');
			if (isset($values['file_action']) && is_array($values['file_action']))
			{
				$bofiles->delete_file("/workorder/{$id}/", $values);
			}

			$values['file_name'] = @str_replace(' ', '_', $_FILES['file']['name']);

			if ($values['file_name'])
			{
				$to_file = $bofiles->fakebase . '/workorder/' . $id . '/' . $values['file_name'];

				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => array(
							RELATIVE_NONE)
					)))
				{
					$this->receipt['error'][] = array(
						'msg' => lang('This file already exists !'));
				}
				else
				{
					$bofiles->create_document_dir("workorder/$id");
					$bofiles->vfs->override_acl = 1;

					if (!$bofiles->vfs->cp(array(
							'from' => $_FILES['file']['tmp_name'],
							'to' => $to_file,
							'relatives' => array(
								RELATIVE_NONE | VFS_REAL,
								RELATIVE_ALL))))
					{
						$this->receipt['error'][] = array(
							'msg' => lang('Failed to upload file !'));
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
			$config = CreateObject('phpgwapi.config', 'property');
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$config->read();

			/*
			 * Overrides with incoming data from POST
			 */
			$values = $this->_populate();

			if ($id)
			{
				$action = 'edit';
				$values['id'] = $id;
			}

			if ($values['copy_workorder'])
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
					$receipt = $this->bo->save($values, $action);
					$values['id'] = $receipt['id'];
					$id = $receipt['id'];
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

				$historylog = CreateObject('property.historylog', 'workorder');

				$this->_handle_files($values);

				// start approval
				if (!is_object($GLOBALS['phpgw']->send))
				{
					$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
				}

				$_budget_amount = $this->bo->get_budget_amount($id);
				$sosubstitute = CreateObject('property.sosubstitute');

				if (isset($values['approval']) && $values['approval'] && $config->config_data['workorder_approval'])
				{
					if (empty($GLOBALS['phpgw_info']['server']['smtp_server']))
					{
						$receipt['error'][] = array('msg' => lang('SMTP server is not set! (admin section)'));
					}

					$coordinator_name = $GLOBALS['phpgw_info']['user']['fullname'];
					$coordinator_email = $GLOBALS['phpgw_info']['user']['preferences']['property']['email'];


					$approval_level = !empty($config->config_data['approval_level']) ? $config->config_data['approval_level'] : 'order';

					switch ($approval_level)
					{
						case 'project':
							$approval_menuaction = 'property.uiproject.edit';
							$subject = lang('Approval') . ": {$values['project_id']}";
							$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $approval_menuaction,
									'id' => $values['project_id']), false, true) . '">' . lang('project %1 needs approval', $values['project_id']) . '</a>';

							//already approved?

							$pending_action = CreateObject('property.sopending_action');

							foreach ($values['approval'] as $_account_id => $_address)
							{
								$action_params_approved = array
								(
									'appname' => 'property',
									'location' => '.project',
									'id' => $values['project_id'],
									'responsible' => $_account_id,
									'responsible_type' => 'user',
									'action' => 'approval',
									'remark' => '',
									'deadline' => '',
									'closed' => true
								);

								$approvals = $pending_action->get_pending_action($action_params_approved);

								//Not approved
								if(!$approvals)
								{
									$substitute = $sosubstitute->get_substitute($_account_id);

									if($substitute)
									{
										$_account_id = $substitute;
									}

									$_budget_amount = $this->bo->get_accumulated_budget_amount($values['project_id']);

									$pending_action->set_pending_action($action_params_approved);
									if (isset($config->config_data['project_approval_status']) && $config->config_data['project_approval_status'])
									{
										$_project_status = $config->config_data['project_approval_status'];
										createObject('property.soproject')->set_status($values['project_id'],$_project_status);
									}

									$prefs = $this->bocommon->create_preferences('property', $_account_id);
									if (!empty($prefs['email']))
									{
										$_address = $prefs['email'];
									}
									else
									{
										$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';
										$_address = $GLOBALS['phpgw']->accounts->id2lid($_account_id) . "@{$email_domain}";
									}

									try
									{
										CreateObject('property.historylog', 'project')->add('AP', $values['project_id'], $GLOBALS['phpgw']->accounts->get($_account_id)->__toString() . "::{$_budget_amount}");

										$rcpt = $GLOBALS['phpgw']->send->msg('email', $_address, $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html');
										if ($rcpt)
										{
											phpgwapi_cache::message_set(lang('%1 is notified', $_address),'message');
										}
									}
									catch (Exception $exc)
									{
										phpgwapi_cache::message_set($exc->getMessage(),'error');
									}

									//add request for order as well
									$action_params = array(
										'appname' => 'property',
										'location' => '.project.workorder',
										'id' => $id,
										'responsible' => $_account_id,
										'responsible_type' => 'user',
										'action' => 'approval',
										'remark' => '',
										'deadline' => ''
									);

									if(!execMethod('property.sopending_action.get_pending_action', $action_params))
									{
										execMethod('property.sopending_action.set_pending_action', $action_params);
									}
								}
								else // implicite approved
								{
										$action_params = array(
											'appname' => 'property',
											'location' => '.project.workorder',
											'id' => $id,
											'responsible' => $_account_id,
											'responsible_type' => 'user',
											'action' => 'approval',
											'remark' => '',
											'deadline' => ''
										);

										if(!execMethod('property.sopending_action.get_pending_action', $action_params))
										{
											execMethod('property.sopending_action.set_pending_action', $action_params);
										}
										execMethod('property.sopending_action.close_pending_action', $action_params);
										$lang_implicitly = lang('implicitly from project');
										$historylog->add('OA', $id, $GLOBALS['phpgw']->accounts->get($_account_id)->__toString() . ", {$lang_implicitly}::{$_budget_amount}");
								}
							}

							$_orders = $this->bo->get_order_list($values['project_id']);
							break;
						default:
							$approval_menuaction = 'property.uiworkorder.edit';
							$subject = lang('Approval') . ": {$id}";
							$message = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $approval_menuaction,
							'id' => $id), false, true) . '">' . lang('Workorder %1 needs approval', $id) . '</a>';
							$_orders = array($id);

							$action_params = array(
								'appname' => 'property',
								'location' => '.project.workorder',
								'id' => $id,
								'responsible' => '',
								'responsible_type' => 'user',
								'action' => 'approval',
								'remark' => '',
								'deadline' => ''
							);
							$bcc = '';//$coordinator_email;
							foreach ($values['approval'] as $_account_id => $_address)
							{
								$substitute = $sosubstitute->get_substitute($_account_id);

								/**
								 * Alert the substitute
								 */
								if($substitute)
								{
									$_account_id = $substitute;
								}

								$prefs = $this->bocommon->create_preferences('property', $_account_id);
								if (!empty($prefs['email']))
								{
									$_address = $prefs['email'];
								}
								else
								{
									$email_domain = !empty($GLOBALS['phpgw_info']['server']['email_domain']) ? $GLOBALS['phpgw_info']['server']['email_domain'] : 'bergen.kommune.no';
									$_address = $GLOBALS['phpgw']->accounts->id2lid($_account_id) . "@{$email_domain}";
								}

								if($approval_level == 'order')
								{
									foreach ($_orders as $_order_id)
									{
										$action_params['responsible'] = $_account_id;
										$action_params['id'] = $_order_id;
										try
										{
											$historylog->add('AP', $id, $GLOBALS['phpgw']->accounts->get($_account_id)->__toString() . "::{$_budget_amount}");
											execMethod('property.sopending_action.set_pending_action', $action_params);
											$rcpt = $GLOBALS['phpgw']->send->msg('email', $_address, $subject, stripslashes($message), '', $cc, $bcc, $coordinator_email, $coordinator_name, 'html');
											if ($rcpt)
											{
												phpgwapi_cache::message_set(lang('%1 is notified', $_address),'message');
											}
										}
										catch (Exception $exc)
										{
											phpgwapi_cache::message_set($exc->getMessage(),'error');
										}
									}
								}
							}
							break;
					}
				}

				if (!empty($values['do_approve']) && is_array($values['do_approve']))
				{
					$action_params = array(
						'appname' => 'property',
						'location' => '.project.workorder',
						'id' => $id,
						'responsible' => '',
						'responsible_type' => 'user',
						'action' => 'approval',
						'remark' => '',
						'deadline' => ''
					);

					foreach ($values['do_approve'] as $_account_id => $_dummy)
					{
						$users_for_substitute = $sosubstitute->get_users_for_substitute($_account_id);

						$approvals = execMethod('property.sopending_action.get_pending_action', $action_params);

						$take_responsibility_for = array($_account_id);
						foreach ($approvals as $approval)
						{
							if(in_array($approval['responsible'],$users_for_substitute))
							{
								$take_responsibility_for[] = $approval['responsible'];
							}
						}
						foreach ($take_responsibility_for as $__account_id)
						{
							$action_params['responsible'] = $__account_id;
							if(!execMethod('property.sopending_action.get_pending_action', $action_params))
							{
								execMethod('property.sopending_action.set_pending_action', $action_params);
							}
							execMethod('property.sopending_action.close_pending_action', $action_params);
							$historylog->add('OA', $id, $GLOBALS['phpgw']->accounts->get($__account_id)->__toString() . "::{$_budget_amount}");
						}
						unset($action_params['responsible']);
					}
				}

				//end approval
				$toarray = array();
				$toarray_sms = array();

				if (isset($receipt['notice_owner']) && is_array($receipt['notice_owner']) && $config->config_data['mailnotification'])
//						&& isset($GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['notify_project_owner'])
				{
					if ($this->account != $project['coordinator'] && $config->config_data['notify_project_owner'])
					{
						$prefs_coordinator = $this->bocommon->create_preferences('property', $project['coordinator']);
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

				$subject = lang('workorder %1 has been edited', $id);
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
							$receipt['message'][] = array(
								'msg' => lang('%1 is notified', "{$entry['first_name']} {$entry['last_name']}"));
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
					$body = '<a href ="' . $GLOBALS['phpgw']->link('/index.php', array(
							'menuaction' => 'property.uiworkorder.edit',
							'id' => $id), false, true) . '">' . lang('workorder %1 has been edited', $id) . '</a>' . "\n";
					foreach ($receipt['notice_owner'] as $notice)
					{
						$body .= $notice . "\n";
					}
					$body .= lang('Altered by') . ': ' . $from_name . "\n";
					$body .= lang('remark') . ': ' . $values['remark'] . "\n";
					$body = nl2br($body);

					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}

					$returncode = $GLOBALS['phpgw']->send->msg('email', $to, $subject, $body, false, false, false, $from_email, $from_name, 'html');

					if (!$returncode) // not nice, but better than failing silently
					{
						$this->receipt['error'][] = array(
							'msg' => "uiworkorder::edit: sending message to '$to' subject='$subject' failed !!!");
						$this->receipt['error'][] = array(
							'msg' => $GLOBALS['phpgw']->send->err['desc']);
					}
					else
					{
						$historylog->add('ON', $id, lang('%1 is notified', $to));
						$this->receipt['message'][] = array(
							'msg' => lang('%1 is notified', $to));
					}
				}

				if (phpgw::get_var('send_workorder', 'bool') && !$this->receipt['error'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array(
						'menuaction' => 'property.uiwo_hour.view',
						'workorder_id' => $id,
						'from' => 'index'
						)
					);
				}

				if (phpgw::get_var('calculate_workorder', 'bool') && !$this->receipt['error'])
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array(
						'menuaction' => 'property.uiwo_hour.index',
						'workorder_id' => $id,
						)
					);
				}

				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					if (!$this->receipt['error'])
					{
						$result = array
							(
							'status' => 'updated'
						);
					}
					else
					{
						$result = array
							(
							'status' => 'error'
						);
					}
					$result['receipt'] = $receipt;

					return $result;
				}

				if ($id)
				{
					self::message_set($this->receipt);
					$active_tab = phpgw::get_var('active_tab');
					self::redirect(array(
						'menuaction' => 'property.uiworkorder.edit',
						'id' => $id,
						'active_tab' => $active_tab));
				}
				$this->edit($values);

				return;
			}
		}

		function edit( $values = array(), $mode = 'edit' )
		{

			if ($GLOBALS['phpgw_info']['flags']['nonavbar'] = phpgw::get_var('nonavbar', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noheader_xsl'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			}

			$_lean = phpgw::get_var('lean', 'bool');

			// in case of bigint
			$id = isset($values['id']) && $values['id'] ? $values['id'] : phpgw::get_var('id');

			if ($mode == 'edit' && (!$this->acl_add && !$this->acl_edit))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array(
					'menuaction' => 'property.uiworkorder.view',
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
					phpgwapi_cache::message_set('ID is required for the function uiworkorder::view()', 'error');
					$GLOBALS['phpgw']->redirect_link('/index.php', array(
						'menuaction' => 'property.uiworkorder.index'));
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

			$boproject = CreateObject('property.boproject');
			$bolocation = CreateObject('property.bolocation');
			$config = CreateObject('phpgwapi.config', 'property');
			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$config->read();
			$project_id = phpgw::get_var('project_id', 'int');
			$origin = phpgw::get_var('origin');
			$origin_id = phpgw::get_var('origin_id', 'int');

			if ($origin == '.ticket' && $origin_id && !$values['descr'])
			{
				$boticket = CreateObject('property.botts');
				$ticket = $boticket->read_single($origin_id);
				$values['descr'] = $ticket['details'];
				$values['title'] = $ticket['subject'] ? $ticket['subject'] : $ticket['category_name'];
				$ticket_notes = $boticket->read_additional_notes($origin_id);
				$i = count($ticket_notes) - 1;
				if (isset($ticket_notes[$i]['value_note']) && $ticket_notes[$i]['value_note'])
				{
					$values['descr'] .= ": " . $ticket_notes[$i]['value_note'];
				}

				$values['location_data'] = $ticket['location_data'];
			}
			else if (preg_match("/(^.entity.|^.catch.)/i", $origin) && $origin_id)
			{
				$_origin = explode('.', $origin);
				$_boentity = CreateObject('property.boentity', false, $_origin[1], $_origin[2], $_origin[3]);
				$_entity = $_boentity->read_single(array(
					'entity_id' => $_origin[2],
					'cat_id' => $_origin[3],
					'id' => $origin_id,
					'view' => true));
				$values['location_data'] = $_entity['location_data'];
				unset($_origin);
				unset($_boentity);
				unset($_entity);
			}
			else if ($origin == '.project.request' && $origin_id)
			{
				$_borequest = CreateObject('property.borequest', false);
				$_request = $_borequest->read_single($origin_id, array(), true);
				$values['descr'] = $_request['descr'];
				$values['title'] = $_request['title'];
				$values['location_data'] = $_request['location_data'];
				unset($_origin);
				unset($_borequest);
				unset($_request);
			}

			if (isset($values['origin']) && $values['origin'])
			{
				$origin = $values['origin'];
				$origin_id = $values['origin_id'];
			}

			$interlink = & $this->bo->interlink;
			if (isset($origin) && $origin)
			{
				$values['origin_data'][0]['location'] = $origin;
				$values['origin_data'][0]['descr'] = $interlink->get_location_name($origin);
				$values['origin_data'][0]['data'][] = array
					(
					'id' => $origin_id,
					'link' => $interlink->get_relation_link(array(
						'location' => $origin), $origin_id),
				);
			}

			if ($project_id && !isset($values['project_id']))
			{
				$values['project_id'] = $project_id;
			}

			$project = (isset($values['project_id']) ? $boproject->read_single_mini($values['project_id']) : '');

			if (!$this->receipt['error'])
			{
				if ($values['id'])
				{
					$id = $values['id'];
				}

				if ($id)
				{
					$values = $this->bo->read_single($id);

					if (!isset($values['origin']))
					{
						$values['origin'] = '';
					}
				}
				if ($project_id && !isset($values['project_id']))
				{
					$values['project_id'] = $project_id;
				}

				if (!$project && isset($values['project_id']) && $values['project_id'])
				{
					$project = $boproject->read_single_mini($values['project_id']);
				}

				$acl_required = $mode == 'edit' ? PHPGW_ACL_EDIT : PHPGW_ACL_READ;
				if (!$this->bocommon->check_perms2($project['coordinator'], $this->bo->so->grants, PHPGW_ACL_EDIT))
				{
					$this->receipt['error'][] = array(
						'msg' => lang('You have no edit right for this project'));
					$GLOBALS['phpgw']->session->appsession('receipt', 'property', $this->receipt);

					switch ($mode)
					{
						case 'edit':
							self::redirect(array('menuaction' => 'property.uiworkorder.view','id' => $id));
							break;
						default:
							self::redirect(array('menuaction' => 'property.uiworkorder.index'));
							break;
					}
				}

				if ($project['key_fetch'] && !$values['key_fetch'])
				{
					$values['key_fetch'] = $project['key_fetch'];
				}

				if ($project['key_deliver'] && !$values['key_deliver'])
				{
					$values['key_deliver'] = $project['key_deliver'];
				}

				if ($project['start_date'] && !$values['start_date'])
				{
					if ($project['project_type_id'] == 1)//operation
					{
						phpgw::import_class('phpgwapi.datetime');
						if( $project['end_date'] && phpgwapi_datetime::date_to_timestamp($project['end_date']) < time() )
						{
							$values['start_date'] = $GLOBALS['phpgw']->common->show_date(
								phpgwapi_datetime::date_to_timestamp($project['end_date']),
								$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
							);
						}
						else
						{
							$values['start_date'] = $GLOBALS['phpgw']->common->show_date(
								time(),
								$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
							);
						}
					}
					else
					{
						$values['start_date'] = $project['start_date'];
					}
				}

				$last_day_of_year = mktime(13, 0, 0, 12, 31, date("Y"));


				if ($project['end_date'] && !$values['end_date'])
				{
					if ($project['project_type_id'] == 1 && isset($config->config_data['delay_operation_workorder_end_date']) && $config->config_data['delay_operation_workorder_end_date'] == 1)//operation
					{
						$values['end_date'] = $GLOBALS['phpgw']->common->show_date($last_day_of_year, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}
					else
					{
						$values['end_date'] = $project['end_date'];
					}
				}
				else if (!$project['end_date'] && !$values['end_date'])
				{
					if ($project['project_type_id'] == 1 && isset($config->config_data['delay_operation_workorder_end_date']) && $config->config_data['delay_operation_workorder_end_date'] == 1)//operation
					{
						$values['end_date'] = $GLOBALS['phpgw']->common->show_date($last_day_of_year, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}
					else
					{
						$values['end_date'] = $GLOBALS['phpgw']->common->show_date(time(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
					}
				}

				if ($project['name'] && !isset($values['title']))
				{
					$values['title'] = $project['name'];
				}
				if ($project['descr'] && !isset($values['descr']))
				{
					$values['descr'] = $project['descr'];
				}
			}


			if ($id)
			{
				$record_history = $this->bo->read_record_history($id);
			}
			else
			{
				$record_history = array();
			}

			if ($id)
			{
				$function_msg = lang("{$mode} workorder");
			}
			else
			{
				$function_msg = lang('Add workorder');
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			if (isset($config->config_data['location_at_workorder']) && $config->config_data['location_at_workorder'])
			{
				$admin_location = & $bolocation->soadmin_location;
				$location_types = $admin_location->select_location_type();
				$max_level = count($location_types);

				$location_level = isset($project['location_data']['location_code']) && $project['inherit_location'] ? count(explode('-', $project['location_data']['location_code'])) : 0;
				$location_template_type = 'form';
				$_location_data = array();

				if (!$values['location_data'] && ($values['location_code'] || $values['location']))
				{
					$location_code = isset($values['location_code']) && $values['location_code'] ? $values['location_code'] : implode("-", $values['location']);
					$values['extra']['view'] = true;
					$values['location_data'] = $bolocation->read_single($location_code, $values['extra']);
				}

				if ($values['location_data'])
				{
					$_location_data = $values['location_data'];
				}
				else if (isset($values['location']) && is_array($values['location']))
				{
					$location_code = implode("-", $values['location']);
					$values['extra']['view'] = true;
					$_location_data = $bolocation->read_single($location_code, $values['extra']);
				}
				else
				{
					if (isset($project['location_data']) && $project['location_data'] && $project['inherit_location'])
					{
						$_location_data = $project['location_data'];
					}
				}

				if ($mode == 'view')
				{
					$location_template_type = 'view';
				}

				$location_data = $bolocation->initiate_ui_location(array(
					'values' => $_location_data,
					'type_id' => $mode == 'edit' ? $max_level : count(explode('-', $_location_data['location_data']['location_code'])),
					'no_link' => false, // disable lookup links for location type less than type_id
					'tenant' => true,
					'block_parent' => $location_level,
					'lookup_type' => $location_template_type,
					'lookup_entity' => $this->bocommon->get_lookup_entity('project'),
					'entity_data' => (isset($values['p']) ? $values['p'] : ''),
					'filter_location' => $project['inherit_location'] ? $project['location_data']['location_code'] : false,
					'required_level' => 1
				));
			}
			else
			{
				$location_template_type = 'view';
				$_location_data = !empty($project['location_data']) ? $project['location_data'] : '';
				$location_data = $bolocation->initiate_ui_location(array(
					'values' => $_location_data,
					'type_id' => (isset($project['location_data']['location_code']) ? count(explode('-', $project['location_data']['location_code'])) : ''),
					'no_link' => false, // disable lookup links for location type less than type_id
					'tenant' => (isset($project['location_data']['tenant_id']) ? $project['location_data']['tenant_id'] : ''),
					'lookup_type' => 'view'
				));
			}

			if (isset($project['contact_phone']))
			{
				for ($i = 0; $i < count($location_data['location']); $i++)
				{
					if ($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id' => $values['vendor_id'],
				'vendor_name' => $values['vendor_name'],
				'type' => $mode,
				'required' => isset($config->config_data['workorder_require_vendor']) && $config->config_data['workorder_require_vendor'] == 1
			));


			$b_group_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id' => $project['b_account_group'],
				'role' => 'group',
				'type' => $mode));

			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id' => $values['b_account_id'] ? $values['b_account_id'] : $project['b_account_id'],
//				'b_account_name' => $values['b_account_name'],
				'disabled' => '',
				'parent' => $project['b_account_group'],
				'type' => $mode,
				'required' => true
			));

			$b_account_list_favorite = ExecMethod('property.sob_account_user.get_favorite', $this->account);

			if($b_account_list_favorite)
			{
				$b_account_list = $b_account_list_favorite;
			}
			else
			{
				$b_account_list = execMethod('property.bogeneric.get_list', array(
						'type' => 'budget_account', 'selected' => $values['b_account_id'] ? $values['b_account_id'] : $project['b_account_id'], 'add_empty' => true, 'filter' => array('active' => 1)));

			}

			$_b_account_found = false;
			foreach ($b_account_list as &$entry)
			{
				$entry['name'] = "{$entry['id']} {$entry['name']}";
				if(!empty($b_account_data['value_b_account_id']) && $b_account_data['value_b_account_id'] == $entry['id'])
				{
					$_b_account_found = true;
				}
			}
			if(!empty($b_account_data['value_b_account_id']) && !$_b_account_found)
			{
				array_unshift($b_account_list, array('id' => $b_account_data['value_b_account_id'], 'name' => "{$b_account_data['value_b_account_id']} {$b_account_data['value_b_account_name']}"));
			}

			unset($entry);

			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array
				(
				'ecodimb' => $project['ecodimb'] ? $project['ecodimb'] : $values['ecodimb'],
				'ecodimb_descr' => $values['ecodimb_descr'],
				'disabled' => $project['ecodimb'] || $mode == 'view'
				)
			);

			$event_criteria = array
				(
				'location' => $this->acl_location,
				'name' => 'event_id',
				'event_name' => lang('schedule'),
				'event_id' => $values['event_id'],
				'item_id' => $id,
				'type' => $mode
			);
			$event_data = $this->bocommon->initiate_event_lookup($event_criteria);

			if (isset($event_data['count']) && $event_data['count'])
			{
				$sum_estimated_cost = $event_data['count'] * $values['calculation'];
			}
			else
			{
				$sum_estimated_cost = $values['calculation'];
			}

			$sum_estimated_cost = number_format($sum_estimated_cost, 2, $this->decimal_separator, '.');
			$values['calculation'] = number_format($values['calculation'], 2, $this->decimal_separator, '.');

			$link_data = array
				(
				'menuaction' => 'property.uiworkorder.save',
				'id' => $id
			);

			$workorder_status = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_status']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['workorder_status'] : '');
			if (!$values['status'])
			{
				$values['status'] = $workorder_status;
			}

			$GLOBALS['phpgw']->jqcal->add_listener('values_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('values_end_date');

			$GLOBALS['phpgw']->jqcal->add_listener('values_tender_deadline');
			$GLOBALS['phpgw']->jqcal->add_listener('values_tender_received');
			$GLOBALS['phpgw']->jqcal->add_listener('values_inspection_on_completion');

			/* if( isset($receipt) && is_array($receipt))
			  {
			  $msgbox_data = $this->bocommon->msgbox_data($receipt);
			  }
			  else
			  {
			  $msgbox_data = '';
			  } */

			$link_file_data = array
				(
				'menuaction' => 'property.uiworkorder.view_file',
				'id' => $id
			);

			$categories = $this->cats->formatted_xslt_list(array(
				'selected' => $project['cat_id']));

			$history_def = array
				(
				array(
					'key' => '#',
					'label' => '#',
					'sortable' =>true,
					'resizeable' => true),
				array(
					'key' => 'value_date',
					'label' => lang('Date'),
					'sortable' =>false,
					'resizeable' => true),
				array(
					'key' => 'value_user',
					'label' => lang('User'),
					'sortable' =>false,
					'resizeable' => true),
				array(
					'key' => 'value_action',
					'label' => lang('Action'),
					'sortable' => true,
					'resizeable' => true),
				array(
					'key' => 'value_old_value',
					'label' => lang('old value'),
					'sortable' => false,
					'resizeable' => true),
				array(
					'key' => 'value_new_value',
					'label' => lang('New Value'),
					'sortable' => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($record_history),
				'ColumnDefs' => $history_def,
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
				)
			);

			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$content_files = array();
			for ($z = 0; $z < count($values['files']); $z++)
			{
				$content_files[$z]['file_name'] = '<a href="' . $link_view_file . '&amp;file_id=' . $values['files'][$z]['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $values['files'][$z]['name'] . '</a>';
				$content_files[$z]['delete_file'] = '<input type="checkbox" name="values[file_action][]" value="' . $values['files'][$z]['file_id'] . '" title="' . lang('Check to delete file') . '">';
			}

			$files_def = array
				(
				array(
					'key' => 'file_name',
					'label' => lang('Filename'),
					'sortable' => false,
					'resizeable' => true),
				array('key' => 'picture',
					'label' => lang('picture'),
					'sortable' => false,
					'resizeable' => true,
					'formatter' => 'JqueryPortico.showPicture'
					),
				array(
					'key' => 'delete_file',
					'label' => lang('Delete file'),
					'sortable' => false,
					'resizeable' => true,
					'formatter' => 'JqueryPortico.FormatterCenter'
			));

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiworkorder.get_files',
						'id' => $id, 'phpgw_return_as' => 'json'))),
//				'data' => json_encode($content_files),
				'data' => json_encode(array()),
				'ColumnDefs' => $files_def,
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
				)
			);

			$invoices = array();
			if ($id)
			{
				$active_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array(
					'order_id' => $id));
				$historical_invoices = execMethod('property.soinvoice.read_invoice_sub_sum', array(
					'order_id' => $id,
					'paid' => true));
				$invoices = array_merge($active_invoices, $historical_invoices);
			}

			$link_data_invoice1 = array
				(
				'menuaction' => 'property.uiinvoice.index',
				'user_lid' => 'all'
			);
			$link_data_invoice2 = array
				(
				'menuaction' => 'property.uiinvoice2.index'
			);

			$_disable_link = $_lean;
			$content_invoice = array();
			$amount = 0;
			$approved_amount = 0;
			foreach ($invoices as $entry)
			{
				$entry['voucher_id'] = $entry['transfer_time'] ? -1 * $entry['voucher_id'] : $entry['voucher_id'];
				if ($entry['voucher_out_id'])
				{
					$voucher_out_id = $entry['voucher_out_id'];
				}
				else
				{
					$voucher_out_id = abs($entry['voucher_id']);
				}

				if ($config->config_data['invoicehandler'] == 2)
				{
					$voucher_id = $entry['transfer_time'] ? -1 * $entry['voucher_id'] : $entry['voucher_id'];
					if ($entry['voucher_id'] > 0)
					{
						$link_data_invoice2['voucher_id'] = $entry['voucher_id'];
						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice2);
					}
					else
					{
						$link_data_invoice1['voucher_id'] = abs($entry['voucher_id']);
						$link_data_invoice1['paid'] = 'true';
						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice1);
					}
				}
				else
				{
					$_disable_link = true;
					$voucher_id = $entry['external_voucher_id'];

//					if ($entry['voucher_id'] > 0)
//					{
//						$link_data_invoice1['voucher_id'] = $entry['voucher_id'];
//						$link_data_invoice1['query'] = $entry['voucher_id'];
//						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice1);
//					}
//					else
//					{
//						$link_data_invoice1['voucher_id'] = abs($entry['voucher_id']);
//						$link_data_invoice1['paid'] = 'true';
//						$url = $GLOBALS['phpgw']->link('/index.php', $link_data_invoice1);
//					}
				}

				$link_voucher_id = "<a href='" . $url . "'>" . $voucher_out_id . "</a>";

				$content_invoice[] = array
				(
					'voucher_id' => ($_disable_link) ? $voucher_id : $link_voucher_id,
					'voucher_out_id' => $entry['voucher_out_id'],
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

				$amount += $entry['amount'];
				$approved_amount += $entry['approved_amount'];
			}
			unset($entry);

			if($invoices)
			{
				$invoice_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.invoice'));
			}

			$attachmen_list = array();
			foreach ($invoices as $entry)
			{
				$directory_attachment = rtrim($invoice_config->config_data['import']['local_path'], '/') . '/attachment/' .$entry['external_voucher_id'];
				try
				{
					$dir = new DirectoryIterator("$directory_attachment/");
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
								'key'=> $entry['external_voucher_id']
								));

							$attachmen_list[] = array(
								'voucher_id'	=> $entry['external_voucher_id'],
								'file_name'		=> "<a href='{$url}' target='_blank'>" . (string)$file . "</a>"
							);
						}
					}
				}
				catch (Exception $e)
				{

				}
			}
			unset($entry);

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

			$invoice_def = array
				(
				array(
					'key' => 'voucher_id',
					'label' => lang('bilagsnr'),
					'sortable' => true,
					'value_footer' => lang('Sum')),
				array(
					'key' => 'voucher_out_id',
					'hidden' => true),
				array(
					'key' => 'invoice_id',
					'label' => lang('invoice number'),
					'sortable' => false),
				array(
					'key' => 'vendor',
					'label' => lang('vendor'),
					'sortable' => false),
				array(
					'key' => 'amount',
					'label' => lang('amount'),
					'sortable' => true,
					'className' => 'right',
					'value_footer' => number_format($amount, 2, $this->decimal_separator, '.')),
//				array(
//					'key' => 'approved_amount',
//					'label' => lang('approved amount'),
//					'sortable' => true,
//					'className' => 'right',
//					'value_footer' => number_format($approved_amount, 2, $this->decimal_separator, '.')),
				array(
					'key' => 'period',
					'label' => lang('period'),
					'sortable' => true),
				array(
					'key' => 'periodization',
					'label' => lang('periodization'),
					'sortable' => false),
				array(
					'key' => 'periodization_start',
					'label' => lang('periodization start'),
					'sortable' => false),
				array(
					'key' => 'currency',
					'label' => lang('currency'),
					'sortable' => false),
				array(
					'key' => 'type',
					'label' => lang('type'),
					'sortable' => false),
				array(
					'key' => 'budget_responsible',
					'label' => lang('budget responsible'),
					'sortable' => false),
				array(
					'key' => 'budsjettsigndato',
					'label' => lang('budsjettsigndato'),
					'sortable' => false),
				array(
					'key' => 'transfer_time',
					'label' => lang('transfer time'),
					'sortable' => false)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'data' => json_encode($content_invoice),
				'ColumnDefs' => $invoice_def,
				'config' => array(
					array('disableFilter' => true),
//					array('disablePagination' => true)
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
				'requestUrl' => json_encode(self::link(array(
						'menuaction' => 'property.notify.update_data',
						'location_id' => $location_id,
						'location_item_id' => $id,
						'action' => 'refresh_notify_contact',
						'phpgw_return_as' => 'json'))),
				)
			);

			$datatable_def[] = array(
				'container' => 'datatable-container_6',
				'requestUrl' => "''",
				'data' => json_encode($attachmen_list),
				'ColumnDefs' => $attachmen_def,
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
				)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_3',
				'requestUrl' => json_encode(self::link(array(
						'menuaction' => 'property.notify.update_data',
						'location_id' => $location_id,
						'location_item_id' => $id,
						'action' => 'refresh_notify_contact',
						'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $notify_info['column_defs']['values'],
				'data' => json_encode(array()),
				'tabletools' => $mode == 'edit' ? $notify_info['tabletools'] : array(),
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
				)
			);

			$content_email = execMethod('property.bocommon.get_vendor_email', isset($values['vendor_id']) ? $values['vendor_id'] : 0 );

			if (isset($values['mail_recipients']) && is_array($values['mail_recipients']))
			{
				$_recipients_found = array();
				foreach ($content_email as &$vendor_email)
				{
					if (in_array($vendor_email['value_email'], $values['mail_recipients']))
					{
						$vendor_email['value_select'] = str_replace("type='checkbox'", "type='checkbox' checked='checked'", $vendor_email['value_select']);
						$_recipients_found[] = $vendor_email['value_email'];
					}
				}
				$value_extra_mail_address = implode(',', array_diff($values['mail_recipients'], $_recipients_found));
			}

			$email_def = array
				(
				array(
					'key' => 'value_email',
					'label' => lang('email'),
					'sortable' => true,
					'resizeable' => true),
				array(
					'key' => 'value_select',
					'label' => lang('select'),
					'sortable' => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_4',
				'requestUrl' => "''",
				'data' => json_encode($content_email),
				'ColumnDefs' => $email_def,
				'config' => array(
					array(
						'disableFilter' => true),
					array(
						'disablePagination' => true)
				)
			);

			$content_budget = $this->bo->get_budget($id);

			$lang_delete = lang('Check to delete period');
			$lang_close = lang('Check to close period');
			$lang_active = lang('Check to activate period');
			$lang_fictive = lang('fictive');

			$rows_per_page = 10;
			$initial_page = 1;

			if ($content_budget && $project['periodization_id'])
			{
				$_year_count = array();
				foreach ($content_budget as $key => $row)
				{
					$_year_count[$row['year']] +=1;
					$rows_per_page = $_year_count[$row['year']];
				}
				$initial_page = floor(count($content_budget) / $rows_per_page);
			}

			$budget = 0;
			$sum_orders = 0;
			$sum_oblications = 0;
			$actual_cost = 0;
			$diff = 0;
			$deviation = 0;
			foreach ($content_budget as & $b_entry)
			{
				$checked = $b_entry['active'] ? 'checked="checked"' : '';
				$b_entry['flag_active'] = $b_entry['active'] == 1;
				if ($b_entry['fictive'])
				{
					$b_entry['delete_period'] = $lang_fictive;
					$disabled = 'disabled="disabled"';
				}
				else
				{
					$b_entry['delete_period'] = "<input type='checkbox' name='values[delete_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_delete}'>";
				}

				if ($b_entry['active'] == 2)
				{
					$b_entry['month'] = 'Split';
					$b_entry['closed'] = 'Split';
				}
				else
				{
					$b_entry['closed'] = $b_entry['closed'] ? 'X' : '';
				}

				if ($b_entry['active'] == 1)
				{
					$budget += $b_entry['budget'];
					$sum_orders += $b_entry['sum_orders'];
					$sum_oblications += $b_entry['sum_oblications'];
					$actual_cost += $b_entry['actual_cost'];
					$diff += $b_entry['diff'];
					$deviation += $b_entry['deviation_period'];
				}

				$b_entry['active'] = "<input type='checkbox' name='values[active_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' title='{$lang_active}' {$checked} {$disabled}>";
				$b_entry['active_orig'] = "<input type='checkbox' name='values[active_orig_b_period][]' value='{$b_entry['year']}_{$b_entry['month']}' {$checked} {$disabled} style='display:none'>";
			}
			unset($b_entry);

			$budget_def = array
				(
				array(
					'key' => 'year',
					'label' => lang('year'),
					'sortable' => true,
					'className' => 'center',
					'value_footer' => lang('Sum')),
				array(
					'key' => 'month',
					'label' => lang('month'),
					'sortable' => false,
					'className' => 'center'),
				array(
					'key' => 'budget',
					'label' => lang('budget'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0',
					'value_footer' => number_format($budget, 0, $this->decimal_separator, '.')),
				array(
					'key' => 'sum_orders',
					'label' => lang('order'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0',
					'value_footer' => number_format($sum_orders, 0, $this->decimal_separator, '.')),
				array(
					'key' => 'sum_oblications',
					'label' => lang('sum orders'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0',
					'value_footer' => number_format($sum_oblications, 0, $this->decimal_separator, '.')),
				array(
					'key' => 'actual_cost',
					'label' => lang('actual cost'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0',
					'value_footer' => number_format($actual_cost, 0, $this->decimal_separator, '.')),
				array(
					'key' => 'diff',
					'label' => lang('difference'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0',
					'value_footer' => number_format($diff, 0, $this->decimal_separator, '.')),
				array(
					'key' => 'deviation_period',
					'label' => lang('deviation'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0',
					'value_footer' => number_format($deviation, 0, $this->decimal_separator, '.')),
				array(
					'key' => 'deviation_acc',
					'label' => lang('deviation') . '::' . lang('accumulated'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount0'),
				array(
					'key' => 'deviation_percent_period',
					'label' => lang('deviation') . '::' . lang('percent'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount2'),
				array(
					'key' => 'deviation_percent_acc',
					'label' => lang('percent') . '::' . lang('accumulated'),
					'sortable' => false,
					'className' => 'right',
					'formatter' => 'JqueryPortico.FormatterAmount2'),
				array(
					'key' => 'closed',
					'label' => lang('closed'),
					'sortable' => false,
					'className' => 'center'),
				array(
					'key' => 'active',
					'label' => lang('active'),
					'sortable' => false,
					'className' => 'center',
					'formatter' => 'JqueryPortico.FormatterActive'),
				array(
					'key' => 'delete_period',
					'label' => lang('Delete'),
					'sortable' => false,
					'className' => 'center')
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_5',
				'requestUrl' => "''",
				'data' => json_encode($content_budget),
				'ColumnDefs' => $budget_def,
				'config' => array(
					array('disableFilter' => true),
//					array('disablePagination' => true),
					array('rows_per_page' => $rows_per_page),
				)
			);

			$link_claim = '';
			if (isset($values['charge_tenant']) ? $values['charge_tenant'] : '')
			{
				$claim = execMethod('property.sotenant_claim.read', array(
					'project_id' => $project['project_id']));
				if ($claim)
				{
					$link_claim = $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => 'property.uitenant_claim.edit',
						'claim_id' => $claim[0]['claim_id']));
				}
				else
				{
					$link_claim = $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => 'property.uitenant_claim.check',
						'project_id' => $project['project_id']));
				}
			}

			$_cat_sub = $this->cats->return_sorted_array($start = 0, $limit = false, $query = '', $sort = '', $order = '', $globals = False, false);

			$selected_cat = $values['cat_id'] ? $values['cat_id'] : $project['cat_id'];
			$validatet_category = '';
			$cat_sub = array();
			foreach ($_cat_sub as $entry)
			{
				if ($entry['active'] == 2 && $entry['id'] != $selected_cat)//hidden
				{
					continue;
				}

				if(!$validatet_category)
				{
					if ($entry['active'] && $entry['id'] == $selected_cat)
					{
						$_category = $this->cats->return_single($entry['id']);
						if($_category[0]['is_node'])
						{
							$validatet_category = 1;
						}
					}
				}
				$entry['name'] = str_repeat(' . ', (int)$entry['level']) . $entry['name'];
				$entry['title'] = $entry['description'];
				$cat_sub[] = $entry;
			}

			$suppresscoordination = isset($config->config_data['project_suppresscoordination']) && $config->config_data['project_suppresscoordination'] ? 1 : '';
			$user_list = $this->bocommon->get_user_list('select', isset($values['user_id']) && $values['user_id'] ? $values['user_id'] : $this->account, false, false, -1, false, false, '', -1);
			foreach ($user_list as &$user)
			{
				$user['id'] = $user['user_id'];
			}

			$value_coordinator = isset($project['coordinator']) ? $GLOBALS['phpgw']->accounts->get($project['coordinator'])->__toString() : $GLOBALS['phpgw']->accounts->get($this->account)->__toString();

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

			if (isset($this->receipt['error']) && $this->receipt['error'])
			{
				$year_list = $this->bocommon->select_list($_POST['values']['budget_year'], $year_list);
			}

			$sogeneric = CreateObject('property.sogeneric');
			$sogeneric->get_location_info('periodization', false);
			$periodization_data = $sogeneric->read_single(array(
				'id' => (int)$project['periodization_id']), array());

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$active_tab = phpgw::get_var('active_tab', 'string', 'REQUEST', 'general');

			$collect_building_part = false;
			$building_part_list = array();
			$order_dim1_list = array();
			if(isset($config->config_data['workorder_require_building_part']))
			{
				if($config->config_data['workorder_require_building_part'] == 1)
				{
				$collect_building_part = true;
				$filter_buildingpart = isset($config->config_data['filter_buildingpart']) ? $config->config_data['filter_buildingpart'] : array();

				$_filter_buildingpart = array();
				if ($filter_key = array_search('.b_account', $filter_buildingpart))
				{
					$_filter_buildingpart = array("filter_{$filter_key}" => 1);
				}
				$building_part_list = array('options' => $this->bocommon->select_category_list(array(
						'type' => 'building_part', 'selected' => $values['building_part'], 'order' => 'id',
						'id_in_name' => 'num', 'filter' => $_filter_buildingpart)));
				$order_dim1_list = array('options' => $this->bocommon->select_category_list(array(
						'type' => 'order_dim1', 'selected' => $values['order_dim1'], 'order' => 'id',
						'id_in_name' => 'num')));
				}
			}

			$unspsc_code = $values['unspsc_code'] ? $values['unspsc_code'] : $GLOBALS['phpgw_info']['user']['preferences']['property']['unspsc_code'];

			$enable_unspsc = isset($config->config_data['enable_unspsc']) && $config->config_data['enable_unspsc'] ? true : false;
			$enable_order_service_id = isset($config->config_data['enable_order_service_id']) && $config->config_data['enable_order_service_id'] ? true : false;

			$approval_level = !empty($config->config_data['approval_level']) ? $config->config_data['approval_level'] : 'order';

			$accumulated_budget_amount = 0;
			if($approval_level == 'project')
			{
				$accumulated_budget_amount = $this->bo->get_accumulated_budget_amount($values['project_id']);
			}

			$_origin = array();
			if (isset($values['origin_data']) && $values['origin_data'])
			{
				foreach ($values['origin_data'] as $__origin)
				{
					foreach ($__origin['data'] as $_origin_data)
					{
						$_origin[] = array
							(
							'url' => "<a href='{$_origin_data['link']}'>{$_origin_data['id']} </a>",
							'type' => $__origin['descr'],
							'title' => $_origin_data['title'],
							'status' => $_origin_data['statustext'],
						);
					}
				}
			}

			$origin_def = array
				(
				array('key' => 'url', 'label' => lang('id'), 'sortable' => true),
				array('key' => 'type', 'label' => lang('type'), 'sortable' => true),
				array('key' => 'title', 'label' => lang('title'), 'sortable' => false),
				array('key' => 'status', 'label' => lang('status'), 'sortable' => false)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_7',
				'requestUrl' => "''",
				'data' => json_encode($_origin),
				'ColumnDefs' => $origin_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$attach_file_def = array
				(
				array(
					'key' => 'file_name',
					'label' => lang('Filename'),
					'sortable' => false,
					'resizeable' => true
					),
//				array('key' => 'picture',
//					'label' => lang('picture'),
//					'sortable' => false,
//					'resizeable' => true,
//					'formatter' => 'JqueryPortico.showPicture'
//					),
				array(
					'key' => 'attach_file',
					'label' => lang('attach file'),
					'sortable' => false,
					'resizeable' => true,
					'formatter' => 'JqueryPortico.FormatterCenter')
			);
			$file_attachments = isset($values['file_attachments']) && is_array($values['file_attachments']) ? $values['file_attachments'] : array();

			$content_attachments = array();
			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);
			$lang_view_file = lang('click to view file');
			$lang_select_file = lang('Check to attach file');
			$lang_workorder = lang('workorder');
			foreach ($values['files'] as $_entry)
			{
				$_checked = '';
				if (in_array($_entry['file_id'], $file_attachments))
				{
					$_checked = 'checked="checked"';
				}

				$content_attachments[] = array(
					'file_name' => "<a href='{$link_view_file}&amp;file_id={$_entry['file_id']}' target='_blank' title='{$lang_view_file}'>{$lang_workorder}::${_entry['name']}</a>",
					'attach_file' => "<input type='checkbox' $_checked  name='values[file_attach][]' value='{$_entry['file_id']}' title='{$lang_select_file}'>"
				);
			}
			unset($_entry);

			$project_link_file_data = array
				(
				'menuaction' => 'property.uiproject.view_file',
				'id' => $project['project_id']
			);
			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $project_link_file_data);

			$files = $boproject->get_files($project['project_id']);
			$lang_project = lang('project');

			foreach ($files as $_entry)
			{

				$_checked = '';
				if (in_array($_entry['file_id'], $file_attachments))
				{
					$_checked = 'checked="checked"';
				}
				$content_attachments[] = array(
					'file_name' => "<a href='{$link_view_file}&amp;file_id={$_entry['file_id']}' target='_blank' title='{$lang_view_file}'>{$lang_project}::${_entry['name']}</a>",
					'attach_file' => "<input type='checkbox' $_checked  name='values[file_attach][]' value='{$_entry['file_id']}' title='{$lang_select_file}'>"
				);
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_8',
				'requestUrl' => "''",
				'ColumnDefs' => $attach_file_def,
				'data' => json_encode($content_attachments),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$delivery_address	= $values['delivery_address'] ? $values['delivery_address'] : $project['delivery_address'];

			if(!$delivery_address && !empty($_location_data['loc1']))
			{
				$delivery_address = CreateObject('property.solocation')->get_delivery_address($_location_data['loc1']);
			}

			$data = array(
				'datatable_def' => $datatable_def,
				'periodization_data' => $periodization_data,
				'year_list' => array(
					'options' => $year_list),
				'mode' => $mode,
				'value_coordinator' => $value_coordinator,
				'event_data' => $event_data,
				'link_claim' => $link_claim,
				'lang_claim' => lang('claim'),
				'suppressmeter' => isset($config->config_data['project_suppressmeter']) && $config->config_data['project_suppressmeter'] ? 1 : '',
				'suppresscoordination' => $suppresscoordination,
				'enable_unspsc' => $enable_unspsc,
				'enable_order_service_id' => $enable_order_service_id,
				'tabs' => self::_generate_tabs(array(), $active_tab, $_disable = array(
					'budget' => !$id && empty($this->receipt['error']) ? true : false,
					'coordination' => $id ? false : true,
					'documents' => $id ? false : true,
					'history' => $id ? false : true)),
				'value_active_tab'	=> $active_tab,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'value_origin' => isset($values['origin_data']) ? $values['origin_data'] : '',
				'value_origin_type' => isset($origin) ? $origin : '',
				'value_origin_id' => isset($origin_id) ? $origin_id : '',
				'lang_calculate' => lang('Calculate Workorder'),
				'lang_calculate_statustext' => lang('Calculate workorder by adding items from vendors prizebook or adding general hours'),
				'lang_send' => $this->bo->order_sent_adress ? lang('ReSend Workorder') : lang('Send Workorder'),
				'lang_send_statustext' => lang('send this workorder to vendor'),
				'project_link' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiproject.edit')),
				'b_group_data' => $b_group_data,
				'b_account_data' => $b_account_data,
				'b_account_as_listbox' => $GLOBALS['phpgw_info']['user']['preferences']['property']['b_account_as_listbox'],
				'b_account_list'	=> array('options' => $b_account_list),
				'value_start_date' => $values['start_date'],
				'value_end_date' => $values['end_date'],
				'value_tender_deadline' => $values['tender_deadline'],
				'value_tender_received' => $values['tender_received'],
				'value_tender_delay' => $values['tender_delay'],
				'value_inspection_on_completion' => $values['inspection_on_completion'],
				'value_end_date_delay' => $values['end_date_delay'],
				'lang_copy_workorder' => lang('Copy workorder ?'),
				'lang_copy_workorder_statustext' => lang('Choose Copy Workorder to copy this workorder to a new workorder'),
				'lang_contact_phone' => lang('Contact phone'),
				'contact_phone' => (isset($project['contact_phone']) ? $project['contact_phone'] : ''),
				'lang_charge_tenant' => lang('Charge tenant'),
				'lang_charge_tenant_statustext' => lang('Choose charge tenant if the tenant i to pay for this project'),
				'charge_tenant' => (isset($values['charge_tenant']) ? $values['charge_tenant'] : ''),
				'lang_power_meter' => lang('Power meter'),
				'lang_power_meter_statustext' => lang('Enter the power meter'),
				'value_power_meter' => (isset($project['power_meter']) ? $project['power_meter'] : ''),
				'lang_addition_rs' => lang('Rig addition'),
				'lang_addition_rs_statustext' => lang('Enter any round sum addition per order'),
				'value_addition_rs' => (isset($values['addition_rs']) ? $values['addition_rs'] : ''),
				'lang_addition_percentage' => lang('Percentage addition'),
				'lang_addition_percentage_statustext' => lang('Enter any persentage addition per unit'),
				'value_addition_percentage' => (isset($values['addition_percentage']) ? $values['addition_percentage'] : ''),
				'lang_budget' => lang('Budget'),
				'value_budget' => isset($this->receipt['error']) && $this->receipt['error'] ? $_POST['values']['budget'] : '',
				'check_for_budget' => abs($budget),
				'local_value_budget' => $budget,
				'accumulated_budget_amount' => $accumulated_budget_amount ? $accumulated_budget_amount : $budget,
				'lang_budget_statustext' => lang('Enter the budget'),
				'lang_incl_tax' => lang('incl tax'),
				'lang_calculation' => lang('Calculation'),
				'value_calculation' => (isset($values['calculation']) ? $values['calculation'] : ''),
				'value_sum_estimated_cost' => $sum_estimated_cost,
				'value_contract_sum' => isset($this->receipt['error']) && $this->receipt['error'] ? $_POST['values']['contract_sum'] : '',
				'ecodimb_data' => $ecodimb_data,
				'project_ecodimb' => $project['ecodimb'],
				'vendor_data' => $vendor_data,
				'location_data' => $location_data,
				'location_template_type' => $location_template_type,
				'form_action' => $mode == 'edit' ? $GLOBALS['phpgw']->link('/index.php', $link_data) : $GLOBALS['phpgw']->link('/home.php'), //avoid accidents
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiworkorder.index')),
				'lang_year' => lang('Year'),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_title' => lang('Title'),
				'value_title' => $values['title'],
				'lang_project_name' => lang('Project name'),
				'value_project_name' => (isset($project['name']) ? $project['name'] : ''),
				'lang_project_id' => lang('Project ID'),
				'value_project_id' => $values['project_id'],
				'lang_workorder_id' => lang('Workorder ID'),
				'value_workorder_id' => (isset($id) ? $id : ''),
				'lang_title_statustext' => lang('Enter Workorder title'),
				'lang_other_branch' => lang('Other branch'),
				'lang_other_branch_statustext' => lang('Enter other branch if not found in the list'),
				'value_other_branch' => (isset($project['other_branch']) ? $project['other_branch'] : ''),
				'lang_descr_statustext' => lang('Enter a short description of the workorder'),
				'lang_descr' => lang('Description'),
				'value_descr' => $values['descr'],
				'lang_remark_statustext' => lang('Enter a remark to add to the history of the order'),
				'lang_remark' => lang('remark'),
				'value_remark' => (isset($values['remark']) ? $values['remark'] : ''),
				'lang_done_statustext' => lang('Back to the list'),
				'lang_save_statustext' => lang('Save the workorder'),
				'lang_cat_sub' => lang('category'),
				'cat_sub_list' => $this->bocommon->select_list($selected_cat, $cat_sub),
				'cat_sub_name' => 'values[cat_id]',
				'lang_cat_sub_statustext' => lang('select sub category'),
				'validatet_category'	=> $validatet_category,
				'sum_workorder_budget' => (isset($values['sum_workorder_budget']) ? $values['sum_workorder_budget'] : ''),
				'workorder_budget' => (isset($values['workorder_budget']) ? $values['workorder_budget'] : ''),
				'lang_coordinator' => lang('Coordinator'),
				'lang_sum' => lang('Sum'),
				'select_user_name' => 'values[coordinator]',
				'user_list' => array(
					'options' => $user_list),
				'status_list' => $this->bo->select_status_list('select', $values['status']),
				'status_name' => 'values[status]',
				'status_required' => true,
				'lang_no_status' => lang('Select status'),
				'lang_status' => lang('Status'),
				'lang_status_statustext' => lang('What is the current status of this workorder ?'),
				'lang_confirm_status' => lang('Confirm status'),
				'lang_confirm_statustext' => lang('Confirm status to the history'),
				'branch_list' => $boproject->select_branch_p_list($project['project_id']),
				'lang_branch' => lang('branch'),
				'lang_branch_statustext' => lang('Select the branches for this project'),
				'key_responsible_list' => $boproject->select_branch_list($project['key_responsible']),
				'lang_key_responsible' => lang('key responsible'),
				'key_fetch_list' => $this->bo->select_key_location_list((isset($values['key_fetch']) ? $values['key_fetch'] : '')),
				'lang_no_key_fetch' => lang('Where to fetch the key'),
				'lang_key_fetch' => lang('key fetch location'),
				'lang_key_fetch_statustext' => lang('Select where to fetch the key'),
				'key_deliver_list' => $this->bo->select_key_location_list((isset($values['key_deliver']) ? $values['key_deliver'] : '')),
				'lang_no_key_deliver' => lang('Where to deliver the key'),
				'lang_key_deliver' => lang('key deliver location'),
				'lang_key_deliver_statustext' => lang('Select where to deliver the key'),
				'value_approved' => isset($values['approved']) ? $values['approved'] : '',
				'value_continuous' => isset($values['continuous']) ? $values['continuous'] : '',
				'value_fictive_periodization' => isset($values['fictive_periodization']) ? $values['fictive_periodization'] : '',
				'need_approval' => !empty($config->config_data['workorder_approval']),
				'lang_ask_approval' => lang('Ask for approval'),
				'lang_ask_approval_statustext' => lang('Check this to send a mail to your supervisor for approval'),
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'link_view_file' => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'link_to_files' => (isset($config->config_data['files_url']) ? $config->config_data['files_url'] : ''),
				'files' => isset($values['files']) ? $values['files'] : '',
				'lang_files' => lang('files'),
				'lang_filename' => lang('Filename'),
				'lang_file_action' => lang('Delete file'),
				'lang_view_file_statustext' => lang('click to view file'),
				'lang_file_action_statustext' => lang('Check to delete file'),
				'lang_upload_file' => lang('Upload file'),
				'lang_file_statustext' => lang('Select file to upload'),
				'value_billable_hours' => $values['billable_hours'],
				'base_java_url' => "{menuaction:'property.bocommon.get_vendor_email',phpgw_return_as:'json'}",
				'location_item_id' => $id,
				'edit_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiworkorder.edit',
					'id' => $id)),
				'lang_edit_statustext' => lang('Edit this entry '),
				'lang_edit' => lang('Edit'),
				'value_extra_mail_address' => $value_extra_mail_address,
				'lean' => $_lean ? 1 : 0,
				'decimal_separator' => $this->decimal_separator,
				'value_service_id' => $values['service_id'],
				'value_service_name' => $this->_get_eco_service_name($values['service_id']),
				'tax_code_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'tax', 'selected' => $values['tax_code'], 'order' => 'id',
						'id_in_name' => 'num'))),
				'contract_list' => array('options' => $this->get_vendor_contract($values['vendor_id'], $values['contract_id']) ),
				'value_unspsc_code' => $unspsc_code,
				'value_unspsc_code_name' => $this->_get_unspsc_code_name($unspsc_code),
				'collect_building_part'	=> $collect_building_part,
				'building_part_list' => $building_part_list,
				'order_dim1_list' => $order_dim1_list,
				'value_order_sent'	=> !!$values['order_sent'],
				'value_order_received'	=> $values['order_received'] ? $GLOBALS['phpgw']->common->show_date($values['order_received']) : '[ DD/MM/YYYY - H:i ]',
				'value_order_received_amount' => (int) $values['order_received_amount'],
				'value_delivery_address'	=> $delivery_address,
				'multiple_uploader' => true,
				'multi_upload_parans' => "{menuaction:'property.uiworkorder.build_multi_upload_file', id:'{$id}'}",
			);

			$appname = lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			phpgwapi_jquery::formvalidator_generate(array('date','security','file'));
			phpgwapi_jquery::load_widget('core');
			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'workorder.edit.js');

			self::render_template_xsl(array(
				'workorder',
				'datatable_inline',
				'files',
				'cat_sub_select'), array(
				'edit' => $data));
		}

		function add()
		{
			if (!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array(
					'menuaction' => 'property.uilocation.stop',
					'perm' => 2,
					'acl_location' => $this->acl_location));
			}

			$link_data = array
				(
				'menuaction' => 'property.uiworkorder.index'
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array(
				'workorder',
				'search_field'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'add_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiproject.edit')),
				'search_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiproject.index',
					'lookup' => true,
					'from' => 'workorder')),
				'lang_done_statustext' => lang('Back to the workorder list'),
				'lang_add_statustext' => lang('Adds a new project - then a new workorder'),
				'lang_search_statustext' => lang('Adds a new workorder to an existing project'),
				'lang_done' => lang('Done'),
				'lang_add' => lang('Add'),
				'lang_search' => lang('Search')
			);

			$appname = lang('Workorder');
			$function_msg = lang('Add workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array(
				'add' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			$id = phpgw::get_var('id');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($id);
				return "id " . $id . " " . lang("has been deleted");
			}

			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array(
					'menuaction' => 'property.uilocation.stop',
					'perm' => 8,
					'acl_location' => $this->acl_location));
			}
			//$id = phpgw::get_var('id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uiworkorder.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array(
				'app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiworkorder.delete',
					'id' => $id)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('workorder');
			$function_msg = lang('delete workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array(
				'delete' => $data));
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

		function add_invoice()
		{
			if (!$this->acl_add)
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				echo lang('No Access');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$order_id = phpgw::get_var('order_id');

			$receipt = array();

			$bolocation = CreateObject('property.bolocation');
			$boinvoice = CreateObject('property.boinvoice');

			$referer = parse_url(phpgw::get_var('HTTP_REFERER', 'string', 'SERVER'));
			parse_str($referer['query']); // produce $menuaction
			if (phpgw::get_var('cancel', 'bool'))
			{
				$redirect = true;
			}

			if ($add_invoice = phpgw::get_var('add', 'bool'))
			{
				$values = phpgw::get_var('values');

				if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['currency']))
				{
					$values['amount'] = str_ireplace($GLOBALS['phpgw_info']['user']['preferences']['common']['currency'], '', $values['amount']);
				}
				$values['amount'] = str_replace(array(' ',','), array('','.'), $values['amount']);

				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
				$values = $this->bocommon->collect_locationdata($values, $insert_record);
				$values['b_account_id'] = phpgw::get_var('b_account_id');
				$values['external_project_id'] = phpgw::get_var('external_project_id');
				$values['dimb'] = phpgw::get_var('ecodimb');
				$values['vendor_id'] = phpgw::get_var('vendor_id');
			}


			if ($add_invoice && is_array($values))
			{
				if ($values['order_id'] && !ctype_digit($values['order_id']))
				{
					$receipt['error'][] = array(
						'msg' => lang('Please enter an integer for order!'));
					unset($values['order_id']);
				}

				if (!execMethod('property.soXport.check_order', $values['order_id']))
				{
					$receipt['error'][] = array(
						'msg' => lang('Not a valid order!'));
				}

				if (!$values['amount'])
				{
					$receipt['error'][] = array(
						'msg' => lang('Please - enter an amount!'));
				}
				if (!$values['artid'])
				{
					$receipt['error'][] = array(
						'msg' => lang('Please - select type invoice!'));
				}

				if ($values['vendor_id'] == 99)
				{
					$values['invoice_id'] = $boinvoice->get_auto_generated_invoice_num($values['vendor_id']);
				}
				else if (!$values['vendor_id'])
				{
					$receipt['error'][] = array(
						'msg' => lang('Please - select Vendor!'));
				}
				else if (!$boinvoice->check_vendor($values['vendor_id']))
				{
					$receipt['error'][] = array(
						'msg' => lang('That Vendor ID is not valid !') . ' : ' . $values['vendor_id']);
				}

				if (!$values['typeid'])
				{
					$receipt['error'][] = array(
						'msg' => lang('Please - select type order!'));
				}

				if (!$values['budget_responsible'])
				{
					$receipt['error'][] = array(
						'msg' => lang('Please - select budget responsible!'));
				}

				if (!$values['invoice_id'])
				{
					$receipt['error'][] = array(
						'msg' => lang('please enter a invoice num!'));
				}

				if (!$values['payment_date'] && !$values['num_days'])
				{
					$receipt['error'][] = array(
						'msg' => lang('Please - select either payment date or number of days from invoice date !'));
				}

				//_debug_array($values);
				if (!is_array($receipt['error']))
				{
					$values['regtid'] = date($GLOBALS['phpgw']->db->datetime_format());

					$_receipt = array();//local errors
					$receipt = $boinvoice->add_manual_invoice($values);

					if (!isset($receipt['error'])) // all ok
					{
						execMethod('property.soXport.update_actual_cost_from_archive', array(
							$values['order_id'] => true));
						$redirect = true;
					}
				}
				else
				{
					if ($values['location'])
					{
						$location_code = implode("-", $values['location']);
						$_location = $bolocation->read_single($location_code, isset($values['extra']) ? $values['extra'] : '');
						unset($_location['attributes']);
						$values['location_data'] = $_location;
					}
				}
			}

			if ($workorder = $this->bo->read_single($values['order_id'] ? $values['order_id'] : $order_id))
			{
				$project = execMethod('property.boproject.read_single_mini', $workorder['project_id']);

				if (!$add_invoice && !$redirect)
				{
					$_criteria = array
						(
						'dimb' => $workorder['ecodimb']
					);
					$_responsible = $boinvoice->set_responsible($_criteria, $workorder['user_id'], $workorder['b_account_id'] ? $workorder['b_account_id'] : $values['b_account_id']);
					$values['janitor'] = $_responsible['janitor'];
					$values['supervisor'] = $_responsible['supervisor'];
					$values['budget_responsible'] = $_responsible['budget_responsible'];
				}
			}

			if (isset($values['location_data']) && $values['location_data'])
			{
				$_location_data = $values['location_data'];
			}
			else if (isset($workorder['location_data']) && $workorder['location_data'])
			{
				$_location_data = $workorder['location_data'];
			}
			else if (isset($project['location_data']) && $project['location_data'])
			{
				$_location_data = $project['location_data'];
			}
			else
			{
				$_location_data = array();
			}
//_debug_array($project);die();

			$location_data = $bolocation->initiate_ui_location(array
				(
				'values' => $_location_data,
				'type_id' => 2, // calculated from location_types
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => false,
				'lookup_type' => 'form',
				'lookup_entity' => false,
				'entity_data' => false
				)
			);

			$external_project_data = $this->bocommon->initiate_external_project_lookup(array(
				'external_project_id' => $values['external_project_id'] ? $values['external_project_id'] : $project['external_project_id'],
				'external_project_name' => $values['external_project_name']));


			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array
				(
				'b_account_id' => isset($values['b_account_id']) && $values['b_account_id'] ? $values['b_account_id'] : $workorder['b_account_id'],
				'b_account_name' => isset($values['b_account_name']) ? $values['b_account_name'] : '')
			);

			$vendor_data = $this->bocommon->initiate_ui_vendorlookup(array(
				'vendor_id' => $values['vendor_id'] ? $values['vendor_id'] : $workorder['vendor_id'],
				'vendor_name' => $values['vendor_name'],
				'type' => 'edit'));


			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array
				(
				'ecodimb' => $values['ecodimb'] ? $values['ecodimb'] : $workorder['ecodimb'],
				'ecodimb_descr' => $values['ecodimb_descr']
				)
			);


			$link_data = array
				(
				'menuaction' => 'property.uiworkorder.add_invoice'
			);

			if ($_receipt)
			{
				$receipt = array_merge($receipt, $_receipt);
			}
			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$GLOBALS['phpgw']->jqcal->add_listener('invoice_date');
			$GLOBALS['phpgw']->jqcal->add_listener('payment_date');
			$GLOBALS['phpgw']->jqcal->add_listener('paid_date');

			$order_id = isset($values['order_id']) && $values['order_id'] ? $values['order_id'] : $order_id;

			$tabs = array();
			$tabs['invoice'] = array(
				'label' => lang('Invoice'),
				'link' => '#invoice');
			$active_tab = 'invoice';

			$account_lid = $GLOBALS['phpgw']->accounts->get($this->account)->lid;
			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'cancel_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiinvoice.index')),
				'action_url' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property' . '.uiinvoice.add')),
				'value_invoice_date' => isset($values['invoice_date']) ? $values['invoice_date'] : '',
				'value_payment_date' => isset($values['payment_date']) ? $values['payment_date'] : '',
				'value_paid_date' => isset($values['paid_date']) ? $values['paid_date'] : '',
				'vendor_data' => $vendor_data,
				'ecodimb_data' => $ecodimb_data,
				'external_project_data' => $external_project_data,
				'value_service_id' => $values['service_id'],
				'value_service_name' => $this->_get_eco_service_name($values['service_id']),
				'tax_code_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'tax', 'selected' => $values['tax_code'], 'order' => 'id',
						'id_in_name' => 'num'))),
				'contract_list' => array('options' => $this->get_vendor_contract($values['vendor_id'], $values['contract_id']) ),
				'value_unspsc_code' => $values['unspsc_code'],
				'value_unspsc_code_name' => $this->_get_unspsc_code_name($values['unspsc_code']),

				'value_kidnr' => isset($values['kidnr']) ? $values['kidnr'] : '',
				'value_invoice_id' => isset($values['invoice_id']) ? $values['invoice_id'] : '',
				'value_voucher_out_id' => isset($values['voucher_out_id']) ? $values['voucher_out_id'] : '',
				'value_merknad' => isset($values['merknad']) ? $values['merknad'] : '',
				'value_num_days' => isset($values['num_days']) ? $values['num_days'] : '',
				'value_amount' => isset($values['amount']) ? $values['amount'] : '',
				'value_order_id' => $order_id,
				'art_list' => array(
					'options' => $boinvoice->get_lisfm_ecoart(isset($values['artid']) ? $values['artid'] : '')),
				'type_list' => array(
					'options' => $boinvoice->get_type_list(isset($values['typeid']) ? $values['typeid'] : '')),
				'tax_code_list' => array(
					'options' => $boinvoice->tax_code_list(isset($values['tax_code']) ? $values['tax_code'] : '')),
				'janitor_list' => array(
					'options_lid' => $this->bocommon->get_user_list_right(32, isset($values['janitor']) && $values['janitor'] ? $values['janitor'] : $account_lid, '.invoice')),
				'supervisor_list' => array(
					'options_lid' => $this->bocommon->get_user_list_right(64, isset($values['supervisor']) && $values['supervisor'] ? $values['supervisor'] : $account_lid, '.invoice')),
				'budget_responsible_list' => array(
					'options_lid' => $this->bocommon->get_user_list_right(128, isset($values['budget_responsible']) && $values['budget_responsible'] ? $values['budget_responsible'] : $account_lid, '.invoice')),
				'location_data' => $location_data,
				'b_account_data' => $b_account_data,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'redirect' => isset($redirect) && $redirect ? $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => 'property.uiworkorder.edit',
						'id' => $order_id,
						'active_tab' => 'budget')) : null,
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array(
				'workorder'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array(
				'add_invoice' => $data));
		}

		function recalculate()
		{
			if (!$GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin') && !$GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'property'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array(
					'menuaction' => 'property.uilocation.stop',
					'perm' => 8,
					'acl_location' => $this->acl_location));
			}

			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uiworkorder.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->recalculate();
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array(
				'app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiworkorder.recalculate')),
				'lang_confirm_msg' => lang('do you really want to recalculate all actual cost for all workorders'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('recalculate'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('workorder');
			$function_msg = lang('delete workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array(
				'delete' => $data));
		}

		public function get_vendor_contract($vendor_id = 0, $selected = 0)
		{
			$contract_list = $this->bocommon->get_vendor_contract($vendor_id, $selected);
			$config = CreateObject('phpgwapi.config', 'property')->read();

			if($contract_list)
			{
				if(!empty($config['alternative_to_contract_1']))
				{
					$contract_list[] = array('id' => -1, 'name' => $config['alternative_to_contract_1']);
				}
				else
				{
					$contract_list[] = array('id' => -1, 'name' => lang('outside contract'));
				}

				if(!empty($config['alternative_to_contract_2']))
				{
					$contract_list[] = array('id' => -2, 'name' => $config['alternative_to_contract_2']);
				}
				if(!empty($config['alternative_to_contract_3']))
				{
					$contract_list[] = array('id' => -3, 'name' => $config['alternative_to_contract_3']);
				}
			}

			if($selected)
			{
				foreach ($contract_list as &$contract)
				{
					$contract['selected'] = $selected == $contract['id'] ? 1 : 0;
				}
			}
			return $contract_list;
		}

		/**
		 * Gets vendor canidated to be used as vendor - called as ajax from edit form
		 *
		 * @param string  $query
		 *
		 * @return array
		 */
		public function get_eco_service()
		{
			if (!$this->acl_read)
			{
				return;
			}
			return $this->bocommon->get_eco_service();
		}

		public function get_unspsc_code()
		{
			if (!$this->acl_read)
			{
				return;
			}
			return $this->bocommon->get_unspsc_code();
		}

		public function get_ecodimb()
		{
			if (!$this->acl_read)
			{
				return;
			}

			return $this->bocommon->get_ecodimb();
		}

		public function get_b_account()
		{
			if (!$this->acl_read)
			{
				return;
			}
			return $this->bocommon->get_b_account();
		}

		public function receive_order( )
		{
			if (!$this->acl_edit)
			{
				return;
			}

			$id = phpgw::get_var('id', 'int');
			$received_amount = phpgw::get_var('received_amount', 'float');
			return $this->bo->receive_order($id, $received_amount);
		}

		private function _get_eco_service_name( $id )
		{
			return $this->bocommon->get_eco_service_name($id);
		}

		private function _get_unspsc_code_name( $id )
		{
			return $this->bocommon->get_unspsc_code_name($id);
		}

		protected function _generate_tabs( $tabs_ = array(), $active_tab = 'general', $_disable = array() )
		{
			$tabs = array
				(
				'general' => array(
					'label' => lang('general'),
					'link' => '#general'
					),
				'budget' => array(
					'label' => lang('Time and budget'),
					'link' => '#budget'
					),
				'coordination' => array(
					'label' => lang('coordination'),
					'link' => '#coordination'
					),
				'documents' => array(
					'label' => lang('documents'),
					'link' => '#documents'
					),
				'history' => array(
					'label' => lang('history'),
					'link' => '#history'
					),
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
	}