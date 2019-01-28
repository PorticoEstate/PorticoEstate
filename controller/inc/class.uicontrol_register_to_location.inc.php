<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage controller
	 * @version $Id$
	 */

	/**
	 * Import the jQuery class
	 */
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('phpgwapi.uicommon_jquery');

	class controller_uicontrol_register_to_location extends phpgwapi_uicommon_jquery
	{

		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $type_id;
		var $location_code;
		private $bo;
		private $bocommon;
		private $so_control;
		var $public_functions = array
			(
			'index' => true,
			'query' => true,
			'edit_location' => true,
			'get_location_category' => true,
			'get_district_part_of_town' => true,
			'get_category_by_entity' => true,
			'get_entity_table_def' => true,
			'get_locations' => true,
			'get_location_type_category' => true
		);

		function __construct()
		{
			parent::__construct();

			$this->bo = CreateObject('property.bolocation', true);
			$this->bocommon = & $this->bo->bocommon;
			$this->so_control = CreateObject('controller.socontrol');

			$this->type_id = $this->bo->type_id;

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->part_of_town_id = $this->bo->part_of_town_id;
			$this->district_id = $this->bo->district_id;
			$this->status = $this->bo->status;
			$this->allrows = $this->bo->allrows;
			$this->lookup = $this->bo->lookup;
			$this->location_code = $this->bo->location_code;

			self::set_active_menu('controller::control::location_for_check_list');
//			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/base.css');
		}

		function index()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$receipt = array();

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$msgbox_data = array();
			if (phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
				$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			}

			$GLOBALS['phpgw']->translation->add_app('property');

			$district_list = $this->bocommon->select_district_list('filter', $this->district_id);

			$part_of_town_list = execMethod('property.bogeneric.get_list', array('type' => 'part_of_town',
				'selected' => $part_of_town_id));
			$location_type_list = execMethod('property.soadmin_location.select_location_type');

			array_unshift($district_list, array('id' => '', 'name' => lang('select')));
			array_unshift($part_of_town_list, array('id' => '', 'name' => lang('select')));
			array_unshift($location_type_list, array('id' => '', 'name' => lang('select')));

			$cats = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info = true;

			$control_area = $cats->formatted_xslt_list(array('format' => 'filter', 'globals' => true,
				'use_acl' => $this->_category_acl));


			$control_area_list = array();
			foreach ($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
					(
					'id' => $cat_list['cat_id'],
					'name' => $cat_list['name'],
				);
			}

			array_unshift($control_area_list, array('id' => '', 'name' => lang('select')));



			$data = array
				(
				'msgbox_data' => $msgbox_data,
				'control_area_list' => array('options' => $control_area_list),
				'filter_form' => array
					(
					'control_area_list' => array('options' => $control_area_list),
					'district_list' => array('options' => $district_list),
					'part_of_town_list' => array('options' => $part_of_town_list),
					'location_type_list' => array('options' => $location_type_list),
				),
				'update_action' => self::link(array('menuaction' => 'controller.uicontrol_register_to_location.edit_location'))
			);

			self::add_javascript('controller', 'base', 'ajax_control_to_location.js');

			self::render_template_xsl(array('control_location/register_control_to_location'), $data);
		}
		/*
		 * Return categories based on chosen location
		 */

		public function get_location_category()
		{
			$type_id = phpgw::get_var('type_id');
			$category_types = $this->bocommon->select_category_list(array(
				'format' => 'filter',
				'selected' => 0,
				'type' => 'location',
				'type_id' => $type_id,
				'order' => 'descr'
			));
			$default_value = array('id' => '', 'name' => lang('no category selected'));
			array_unshift($category_types, $default_value);
			return json_encode($category_types);
		}
		/*
		 * Return parts of town based on chosen district
		 */

		public function get_district_part_of_town()
		{
			$district_id = phpgw::get_var('district_id');
			$part_of_town_list = $this->bocommon->select_part_of_town('filter', null, $district_id);
			$default_value = array('id' => '', 'name' => lang('no part of town'));
			array_unshift($part_of_town_list, $default_value);

			return json_encode($part_of_town_list);
		}
		/*

		 * Return parts of town based on chosen district
		 */

		public function get_category_by_entity()
		{
			$entity_id = phpgw::get_var('entity_id');
			$entity = CreateObject('property.soadmin_entity');

			$category_list = $entity->read_category(array('allrows' => true, 'entity_id' => $entity_id));

			return $category_list;
		}

		public function get_location_type_category()
		{
			$location_type = phpgw::get_var('location_type', 'int');

			$values = $this->bocommon->select_category_list(array
				(
				'format' => 'filter',
				//	'selected' => $this->cat_id,
				'type' => 'location',
				'type_id' => $location_type,
				'order' => 'descr'
				)
			);

			return $values;
		}

		public function get_entity_table_def()
		{

			$location_level = phpgw::get_var('location_level', 'int', 'REQUEST', 1);
			$solocation = CreateObject('property.solocation');
			$solocation->read(array('dry_run' => true, 'type_id' => $location_level));
			$uicols = $solocation->uicols;

			$columndef = array();

			$columndef[] = array
				(
				'data' => 'select',
				'title' => lang('select'),
				'orderable' => false,
				'formatter' => false,
				'visible' => true,
				'className' => 'center'
			);

			$columndef[] = array
				(
				'data' => 'delete',
				'title' => lang('delete'),
				'orderable' => false,
				'formatter' => false,
				'visible' => true,
				'className' => 'center'
			);

			$count_fields = count($uicols['name']);

			for ($i = 0; $i < $count_fields; $i++)
			{
				if ($uicols['name'][$i])
				{
					if ($uicols['input_type'][$i] == 'hidden')
					{
						continue;
					}

					$params = array(
						'data' => $uicols['name'][$i],
						'title' => $uicols['descr'][$i],
						'orderable' => !!$uicols['sortable'][$i],
						'visible' => $uicols['input_type'][$i] == 'hidden' ? false : true,
						'className' => 'left',
					);

					switch ($uicols['datatype'][$i])
					{
						case 'link':
				//			$params['formatter'] = 'JqueryPortico.formatLinkGeneric';
							break;
						case 'loc1':
				//			$params['formatter'] = 'JqueryPortico.searchLink';
							break;
						default:
					}

					$columndef[] = $params;
				}
			}

			foreach ($columndef as &$entry)
			{
				if ($entry['formatter'])
				{
					$render = <<<JS
					function (dummy1, dummy2, oData) {
							try {
								var ret = {$entry['formatter']}("{$entry['data']}", oData);
							}
							catch(err) {
								return err.message;
							}
							return ret;
                         }
JS;
					$entry['render'] = $render;
				}
				unset($entry['formatter']);
			}
			return $columndef;
		}

		public function get_locations()
		{
			$location_code = phpgw::get_var('location_code');
			$child_level = phpgw::get_var('child_level', 'int', 'REQUEST', 1);
			$part_of_town_id = phpgw::get_var('part_of_town_id', 'int');

			$criteria = array
				(
				'location_code' => $location_code,
				'child_level' => $child_level,
				'field_name' => "loc{$child_level}_name",
				'part_of_town_id' => $part_of_town_id
			);

			$locations = execMethod('property.solocation.get_children', $criteria);
			return $locations;
		}

		public function query()
		{

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$control_id = phpgw::get_var('control_id', 'int');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
				'control_registered' => phpgw::get_var('control_registered', 'bool'),
				'district_id' => phpgw::get_var('district_id', 'int'),
				'cat_id' => phpgw::get_var('cat_id', 'int'),
				'status' => phpgw::get_var('status'),
				'part_of_town_id' => phpgw::get_var('part_of_town_id', 'int'),
				'location_code' => phpgw::get_var('location_code'),
				'type_id' => phpgw::get_var('location_level', 'int', 'REQUEST', 1),
				'control_id' => $control_id
			);

			$values = $this->bo->read($params);

			foreach ($values as &$entry)
			{
				$entry['select'] = '';
				$entry['delete'] = '';
				if ($control_id)
				{
					$checked = '';
					if ($this->so_control->get_control_location($control_id, $entry['location_code']))
					{
						$checked = 'checked = "checked" disabled = "disabled"';
						$entry['delete'] = "<input class =\"mychecks_delete\" type =\"checkbox\" name=\"values[delete][]\" value=\"{$control_id}_{$entry['location_code']}\">";
					}
					$entry['select'] = "<input class =\"mychecks_add\" type =\"checkbox\" $checked name=\"values[register_location][]\" value=\"{$control_id}_{$entry['location_code']}\">";
				}
			}

			$result_data = array
				(
				'results' => $values,
				'total_records' => $this->bo->total_records,
				'draw' => phpgw::get_var('draw', 'int')
			);

			return $this->jquery_results($result_data);
		}

		public function edit_location()
		{
			if ($values = phpgw::get_var('values'))
			{
				if (!$GLOBALS['phpgw']->acl->check('.admin', PHPGW_ACL_EDIT, 'property'))
				{
					$receipt['error'][] = true;
					phpgwapi_cache::message_set(lang('you are not approved for this task'), 'error');
				}
				if (!$receipt['error'])
				{

					if ($this->so_control->register_control_to_location($values))
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
				}
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if ($receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$result['receipt'] = $receipt;
				}
				else
				{
					$result['receipt'] = array();
				}
				return $result;
			}
			else
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicontrol_register_to_location.index'));
			}
		}
	}