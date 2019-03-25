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

	class property_uitemplate extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $public_functions = array
			(
			'query'			 => true,
			'index'			 => true,
			'view'			 => true,
			'edit_template'	 => true,
			'edit_hour'		 => true,
			'delete'		 => true,
			'hour'			 => true,
			'columns'		 => true,
			'save'			 => true,
			'query_hour'	 => true,
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'property::project::template';

			$this->bo		 = CreateObject('property.botemplate', true);
			$this->bowo_hour = CreateObject('property.bowo_hour');
			$this->bocommon	 = CreateObject('property.bocommon');

			$this->start		 = $this->bo->start;
			$this->query		 = $this->bo->query;
			$this->sort			 = $this->bo->sort;
			$this->order		 = $this->bo->order;
			$this->filter		 = $this->bo->filter;
			$this->cat_id		 = $this->bo->cat_id;
			$this->chapter_id	 = $this->bo->chapter_id;
			$this->allrows		 = $this->bo->allrows;
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start'		 => $this->start,
				'query'		 => $this->query,
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'filter'	 => $this->filter,
				'cat_id'	 => $this->cat_id,
				'chapter_id' => $this->chapter_id,
				'allrows'	 => $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		private function _get_Filters()
		{

			$values_combo_box	 = array();
			$combos				 = array();

			$values_combo_box[0] = $this->bowo_hour->get_chapter_list('filter', $this->chapter_id);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('select chapter')));
			$combos[]			 = array('type'	 => 'filter',
				'name'	 => 'chapter_id',
				'text'	 => lang('select chapter'),
				'list'	 => $values_combo_box[0]
			);

			$values_combo_box[1] = $this->bocommon->get_user_list('filter', $this->filter, $extra				 = false, $default			 = false, $start				 = -1, $sort				 = 'ASC', $order				 = 'account_lastname', $query				 = '', $offset				 = -1);
			foreach ($values_combo_box[1] as &$valor)
			{
				$valor['id'] = $valor['user_id'];
				unset($valor['user_id']);
			}
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('no user')));
			$combos[] = array('type'	 => 'filter',
				'name'	 => 'filter',
				'text'	 => lang('no user'),
				'list'	 => $values_combo_box[1]
			);

			return $combos;
		}

		function index()
		{
			$workorder_id	 = phpgw::get_var('workorder_id'); // in case of bigint
			$lookup			 = phpgw::get_var('lookup', 'bool');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('template');
			$function_msg	 = lang('list template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uitemplate.index',
						'chapter_id'		 => $this->chapter_id,
						'phpgw_return_as'	 => 'json'
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array(
							'key'		 => 'template_id',
							'label'		 => lang('ID'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'name',
							'label'		 => lang('Name'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'descr',
							'label'		 => lang('Description'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'chapter',
							'label'		 => lang('Chapter'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'owner',
							'label'		 => lang('owner'),
							'sortable'	 => false
						),
						array(
							'key'		 => 'entry_date',
							'label'		 => lang('Entry Date'),
							'sortable'	 => false
						)
					)
				)
			);

			$filters = $this->_get_Filters();
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			if (!empty($lookup))
			{
				$params2 = array(
					'key'		 => 'template_id',
					'label'		 => lang('Select'),
					'sortable'	 => false,
					'hidden'	 => false,
					'formatter'	 => 'JqueryPortico.formatRadio'
				);

				$params3 = array(
					'type'	 => 'link',
					'value'	 => lang('cancel'),
					'href'	 => self::link(array(
						'menuaction'	 => 'property.uiwo_hour.index',
						'workorder_id'	 => $workorder_id
					)),
					'class'	 => 'new_item'
				);

				array_push($data['form']['toolbar']['item'], $params3);
				array_push($data['datatable']['field'], $params2);
			}
			else
			{
				$data['datatable']['new_item'] = self::link(array(
						'menuaction' => 'property.uitemplate.edit_template'));
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'template_id',
						'source' => 'template_id'
					),
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name'	 => 'id',
						'source' => 'template_id'
					),
				)
			);

			if (!$lookup)
			{


				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'view',
					'statustext' => lang('view the claim'),
					'text'		 => lang('view'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uitemplate.hour'
						)
					),
					'parameters' => json_encode($parameters)
				);

				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'edit',
					'statustext' => lang('edit the claim'),
					'text'		 => lang('edit'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uitemplate.edit_template'
						)
					),
					'parameters' => json_encode($parameters)
				);

				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'delete',
					'statustext' => lang('delete the claim'),
					'text'		 => lang('delete'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uitemplate.delete'
						)
					),
					'parameters' => json_encode($parameters2)
				);

				unset($parameters);
				unset($parameters2);
			}
			else
			{
				$data['datatable']['actions'][] = array
					(
					'my_name'	 => 'Select',
					'statustext' => lang('select'),
					'text'		 => lang('select'),
					'action'	 => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction'	 => 'property.uiwo_hour.template',
						'workorder_id'	 => $workorder_id
						)
					),
					'parameters' => json_encode($parameters)
				);
				unset($parameters);
			}

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function add()
		{
			$this->edit_template();
		}

		public function query()
		{
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$export	 = phpgw::get_var('export', 'bool');

			$params = array(
				'filter'	 => $this->filter,
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				//'order' => $columns[$order[0]['column']]['data'],
				//'sort' => $order[0]['dir'],
				//'dir' => $order[0]['dir'],
				'order'		 => '',
				'sort'		 => phpgw::get_var('sort'),
				//'dir' => phpgw::get_var('dir'),
				'chapter_id' => $this->chapter_id,
				//'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export,
			);

			$result_objects	 = array();
			$result_count	 = 0;

			$values = $this->bo->read($params);

			if ($export)
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		public function query_hour( $template_id )
		{
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export	 = phpgw::get_var('export', 'bool');

			$params = array(
				'start'			 => $this->start,
				'results'		 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'			 => $search['value'],
				'sort'			 => $order[0]['dir'],
				'order'			 => $columns[$order[0]['column']]['data'],
				'allrows'		 => phpgw::get_var('length', 'int') == -1 || $export,
				'chapter_id'	 => $this->chapter_id,
				'template_id'	 => $template_id
			);

			$template_list = $this->bo->read_template_hour($params);

//            
			$i					 = 0;
			$grouping_descr_old	 = '';
			if (is_array($template_list))
			{
				foreach ($template_list as $template)
				{

					if ($template['grouping_descr'] != $grouping_descr_old)
					{
						$new_grouping = true;
					}
					else
					{
						$new_grouping = false;
					}

					$grouping_descr_old = $template['grouping_descr'];

					if ($template['activity_num'])
					{
						$code = $template['activity_num'];
					}
					else
					{
						$code = str_replace("-", $template['tolerance'], $template['ns3420_id']);
					}

					$content[] = array
						(
						'hour_id'		 => $template['hour_id'],
						'template_id'	 => $template_id,
						'counter'		 => $i,
						'record'		 => $template['record'],
						'grouping_descr' => $template['grouping_descr'],
						'building_part'	 => $template['building_part'],
						'code'			 => $code,
						'hours_descr'	 => $template['remark'] != "" ? $template['hours_descr'] . "<br>" . $template['remark'] : $template['hours_descr'],
						'unit'			 => $template['unit'],
						'billperae'		 => $template['billperae'],
					);
					unset($new_grouping);
					unset($grouping_descr_old);
					unset($code);

					$i++;
				}
			}
//            
//            echo '<pre>'; print_r($content); echo '</pre>';exit('hour');
			if ($export)
			{
				return $content;
			}

			$result_data					 = array('results' => $content);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		function hour()
		{
			$delete	 = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$template_id = phpgw::get_var('template_id', 'int');

			if ($delete && $hour_id)
			{
				$receipt = $this->bo->delete_hour($hour_id, $template_id);
				return "hour " . $hour_id . " " . lang("has been deleted");
			}
			else
			{
				$receipt = array();
			}


			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_hour($template_id);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.editable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$appname		 = lang('template');
			$function_msg	 = lang('view template detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'datatable_name' => $appname,
				'form'			 => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'	 => 'link',
								'value'	 => lang('Cancel'),
								'href'	 => self::link(array(
									'menuaction'	 => 'property.uitemplate.index',
									'template_id'	 => $template_id,
								)),
								'class'	 => 'new_item'
							)
						)
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uitemplate.hour',
						'template_id'		 => $template_id,
						'phpgw_return_as'	 => 'json'
					)),
					'new_item'		 => self::link(array(
						'menuaction'	 => 'property.uitemplate.edit_hour',
						'template_id'	 => $template_id,
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array(
						array('key' => 'hour_id', 'hidden' => true, 'label' => '', 'sortable' => false),
						array('key' => 'template_id', 'hidden' => true, 'label' => '', 'sortable' => false),
						array('key' => 'counter', 'hidden' => true, 'label' => '', 'sortable' => false),
						array('key' => 'record', 'hidden' => false, 'label' => lang('Record'), 'sortable' => false),
						array('key'		 => 'building_part', 'hidden'	 => false, 'label'		 => lang('Building part'),
							'sortable'	 => true),
						array('key' => 'code', 'hidden' => false, 'label' => lang('Code'), 'sortable' => false),
						array('key'		 => 'grouping_descr', 'hidden'	 => false, 'label'		 => lang('Grouping'),
							'sortable'	 => false),
						array('key'		 => 'hours_descr', 'hidden'	 => false, 'label'		 => lang('Description'),
							'sortable'	 => false),
						array('key' => 'unit', 'hidden' => false, 'label' => lang('Unit'), 'sortable' => false),
						array('key'		 => 'billperae', 'hidden'	 => false, 'label'		 => lang('Bill per unit'),
							'sortable'	 => true)
					)
				)
			);

			$parameters		 = array();
			$parameters[]	 = array('parameter' => array(array('name' => 'hour_id', 'source' => 'hour_id'),
					array('name' => 'template_id', 'source' => 'template_id')));

			$parameters[] = array('parameter' => array(array('name' => 'hour_id', 'source' => 'hour_id'),
					array('name' => 'template_id', 'source' => 'template_id'),
					array('name' => 'delete', 'source' => 'template_id')));

			$data['datatable']['actions'][] = array
				(
				'my_name'	 => 'edit',
				'text'		 => lang('edit'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitemplate.edit_hour')),
				'parameters' => json_encode($parameters[0])
			);

			$data['datatable']['actions'][] = array
				(
				'my_name'		 => 'delete',
				'text'			 => lang('delete'),
				'confirm_msg'	 => lang('do you really want to delete this entry'),
				'action'		 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitemplate.hour')),
				'parameters'	 => json_encode($parameters[1])
			);

			unset($parameters);

			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit_template( $values = array() )
		{
			$template_id = isset($values['template_id']) && $values['template_id'] ? $values['template_id'] : (int)phpgw::get_var('template_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('template'));

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			if ($template_id)
			{
				$values			 = $this->bo->read_single_template($template_id);
				$function_msg	 = lang('Edit template');
			}
			else
			{
				$function_msg = lang('Add template');
			}

			$link_data = array
				(
				'menuaction'	 => 'property.uitemplate.save',
				'template_id'	 => $template_id
			);


			$data = array
				(
				'form_action'				 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'				 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uitemplate.index',
					'template_id'	 => $template_id)),
				'lang_template_id'			 => lang('Template ID'),
				'value_template_id'			 => $template_id,
				'lang_name'					 => lang('Name'),
				'value_name'				 => $values['name'],
				'lang_save'					 => lang('save'),
				'lang_done'					 => lang('done'),
				'lang_descr'				 => lang('description'),
				'value_descr'				 => $values['descr'],
				'lang_descr_statustext'		 => lang('Enter the description for this template'),
				'lang_done_statustext'		 => lang('Back to the list'),
				'lang_save_statustext'		 => lang('Save the building'),
				'lang_remark'				 => lang('Remark'),
				'value_remark'				 => isset($values['remark']) ? $values['remark'] : '',
				'lang_remark_statustext'	 => lang('Enter additional remarks to the description - if any'),
				'lang_chapter'				 => lang('chapter'),
				'chapter_list'				 => $this->bowo_hour->get_chapter_list('select', $values['chapter_id']),
				'select_chapter'			 => 'values[chapter_id]',
				'lang_no_chapter'			 => lang('Select chapter'),
				'lang_chapter_statustext'	 => lang('Select the chapter (for tender) for this activity.'),
				'lang_add'					 => lang('add a hour'),
				'lang_add_statustext'		 => lang('add a hour to this template'),
				'add_action'				 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uitemplate.edit_hour',
					'template_id'	 => $template_id)),
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'					 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname		 = lang('Workorder template');
			$function_msg	 = lang('view ticket detail');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_template' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit_template();
			}
			$template_id = (int)phpgw::get_var('template_id');
			$values		 = phpgw::get_var('values');
			$receipt	 = array();

			$values['template_id'] = $template_id;
			if (!isset($receipt['error']) || !$receipt['error'])
			{
				try
				{
					$receipt	 = $this->bo->save_template($values);
					$template_id = $receipt['template_id'];
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit_template($values);
						return;
					}
				}

				self::message_set($receipt);
				phpgwapi_cache::message_set($message[0]['msgbox_text'], 'message');
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uitemplate.edit_template',
					'template_id'	 => $template_id));
			}
			else
			{
				$this->edit_template($values);
			}
		}

		function edit_hour()
		{
			$template_id			 = phpgw::get_var('template_id', 'int');
			$activity_id			 = phpgw::get_var('activity_id', 'int');
			$hour_id				 = phpgw::get_var('hour_id', 'int');
			$values					 = phpgw::get_var('values');
			$values['ns3420_id']	 = phpgw::get_var('ns3420_id');
			$values['ns3420_descr']	 = phpgw::get_var('ns3420_descr');
			$error_id				 = false;
			$receipt				 = array();

			$tabs			 = array();
			$tabs['general'] = array('label' => lang('general'), 'link' => '#general');
			$active_tab		 = 'general';

			$bopricebook = CreateObject('property.bopricebook');

			$GLOBALS['phpgw']->xslttpl->add_file(array('template'));

			if (isset($values['save']) && $values['save'])
			{
				if (isset($values['copy_hour']) && $values['copy_hour'])
				{
					unset($hour_id);
				}

				$values['hour_id'] = $hour_id;
				if (!isset($values['ns3420_descr']) || !$values['ns3420_descr'])
				{
					$receipt['error'][]	 = array('msg' => lang('Please enter a description!'));
					$error_id			 = true;
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_hour($values, $template_id);
					$hour_id = $receipt['hour_id'];
				}
			}

			if ($hour_id)
			{
				$values			 = $this->bo->read_single_hour($hour_id);
				$function_msg	 = lang('Edit hour');
			}
			else
			{
				$function_msg = lang('Add hour');
			}

			$template = $this->bo->read_single_template($template_id);

			if ($error_id)
			{
				unset($values['hour_id']);
			}

			$link_data = array
				(
				'menuaction'	 => 'property.uitemplate.edit_hour',
				'template_id'	 => $template_id,
				'hour_id'		 => $hour_id
			);

			$config = CreateObject('phpgwapi.config', 'property');
			$config->read();

			$_filter_buildingpart	 = array();
			$filter_buildingpart	 = isset($config->config_data['filter_buildingpart']) ? $config->config_data['filter_buildingpart'] : array();

			if ($filter_key = array_search('.project', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data'					 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction'	 => 'property.uitemplate.hour',
					'template_id'	 => $template_id)),
				'lang_template'					 => lang('template'),
				'value_template_id'				 => $template['template_id'],
				'value_template_name'			 => $template['name'],
				'lang_hour_id'					 => lang('Hour ID'),
				'value_hour_id'					 => $hour_id,
				'lang_copy_hour'				 => lang('Copy hour ?'),
				'lang_copy_hour_statustext'		 => lang('Choose Copy Hour to copy this hour to a new hour'),
				'lang_activity_num'				 => lang('Activity code'),
				'value_activity_num'			 => isset($values['activity_num']) ? $values['activity_num'] : '',
				'value_activity_id'				 => isset($values['activity_id']) ? $values['activity_id'] : '',
				'lang_unit'						 => lang('Unit'),
				'lang_save'						 => lang('save'),
				'lang_done'						 => lang('done'),
				'lang_descr'					 => lang('description'),
				'value_descr'					 => isset($values['hours_descr']) ? $values['hours_descr'] : '',
				'lang_descr_statustext'			 => lang('Enter the description for this activity'),
				'lang_done_statustext'			 => lang('Back to the list'),
				'lang_save_statustext'			 => lang('Save the building'),
				'lang_remark'					 => lang('Remark'),
				'value_remark'					 => isset($values['remark']) ? $values['remark'] : '',
				'lang_remark_statustext'		 => lang('Enter additional remarks to the description - if any'),
				'lang_quantity'					 => lang('quantity'),
				'value_quantity'				 => isset($values['quantity']) ? $values['quantity'] : '',
				'lang_quantity_statustext'		 => lang('Enter quantity of unit'),
				'lang_billperae'				 => lang('Cost per unit'),
				'value_billperae'				 => isset($values['billperae']) ? $values['billperae'] : '',
				'lang_billperae_statustext'		 => lang('Enter the cost per unit'),
				'lang_total_cost'				 => lang('Total cost'),
				'value_total_cost'				 => isset($values['cost']) ? $values['cost'] : '',
				'lang_total_cost_statustext'	 => lang('Enter the total cost of this activity - if not to be calculated from unit-cost'),
				'lang_dim_d'					 => lang('Dim D'),
				'dim_d_list'					 => $bopricebook->get_dim_d_list(isset($values['dim_d']) ? $values['dim_d'] : ''),
				'select_dim_d'					 => 'values[dim_d]',
				'lang_no_dim_d'					 => lang('No Dim D'),
				'lang_dim_d_statustext'			 => lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),
				'lang_unit'						 => lang('Unit'),
				'unit_list'						 => $bopricebook->get_unit_list(isset($values['unit']) ? $values['unit'] : ''),
				'select_unit'					 => 'values[unit]',
				'lang_no_unit'					 => lang('Select Unit'),
				'lang_unit_statustext'			 => lang('Select the unit for this activity.'),
				'lang_chapter'					 => lang('chapter'),
				'chapter_list'					 => $this->bowo_hour->get_chapter_list('select', $template['chapter_id']),
				'select_chapter'				 => 'values[chapter_id]',
				'lang_no_chapter'				 => lang('Select chapter'),
				'lang_chapter_statustext'		 => lang('Select the chapter (for tender) for this activity.'),
				'lang_tolerance'				 => lang('tolerance'),
				'tolerance_list'				 => $this->bowo_hour->get_tolerance_list(isset($values['tolerance_id']) ? $values['tolerance_id'] : ''),
				'select_tolerance'				 => 'values[tolerance_id]',
				'lang_no_tolerance'				 => lang('Select tolerance'),
				'lang_tolerance_statustext'		 => lang('Select the tolerance for this activity.'),
				'lang_grouping'					 => lang('grouping'),
				'grouping_list'					 => $this->bo->get_grouping_list(isset($values['grouping_id']) ? $values['grouping_id'] : '', isset($template_id) ? $template_id : ''),
				'select_grouping'				 => 'values[grouping_id]',
				'lang_no_grouping'				 => lang('Select grouping'),
				'lang_grouping_statustext'		 => lang('Select the grouping for this activity.'),
				'lang_new_grouping'				 => lang('New grouping'),
				'lang_new_grouping_statustext'	 => lang('Enter a new grouping for this activity if not found in the list'),
				'building_part_list'			 => array('options' => $this->bocommon->select_category_list(array(
						'type'		 => 'building_part', 'selected'	 => $values['building_part_id'], 'order'		 => 'id',
						'id_in_name' => 'num', 'filter'	 => $_filter_buildingpart))),
				'ns3420_link'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilookup.ns3420')),
				'lang_ns3420'					 => lang('NS3420'),
				'value_ns3420_id'				 => $values['ns3420_id'],
				'lang_ns3420_statustext'		 => lang('Select a standard-code from the norwegian standard'),
				'currency'						 => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'tabs'							 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'						 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$appname = lang('Workorder template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_hour' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			$id = (int)phpgw::get_var('id');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($id);
				return "id " . $id . " " . lang("has been deleted");
			}

			$confirm	 = phpgw::get_var('confirm', 'bool', 'POST');
			$link_data	 = array
				(
				'menuaction' => 'property.uitemplate.index'
			);
			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uitemplate.delete',
					'id'		 => $id)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_statustext'	 => lang('Delete the entry'),
				'lang_no_statustext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('Workorder template');
			$function_msg	 = lang('delete template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}
	}