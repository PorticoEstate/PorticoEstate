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
	 * @subpackage budget
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uibudget extends phpgwapi_uicommon_jquery
	{

		private $receipt		 = array();
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $public_functions = array
			(
			'index'					 => true,
			'query'					 => true,
			'basis'					 => true,
			'query_basis'			 => true,
			'obligations'			 => true,
			'get_filters_dependent'	 => true,
			'view'					 => true,
			'edit'					 => true,
			'add'					 => true,
			'save'					 => true,
			'edit_basis'			 => true,
			'add_basis'				 => true,
			'save_basis'			 => true,
			'download'				 => true,
			'delete'				 => true,
			'delete_basis'			 => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app']			 = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection']	 = 'property::economy::budget';

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo		 = CreateObject('property.bobudget', true);
			$this->bocommon	 = & $this->bo->bocommon;
			$this->cats		 = & $this->bo->cats;

			$this->start		 = $this->bo->start;
			$this->query		 = $this->bo->query;
			$this->sort			 = $this->bo->sort;
			$this->order		 = $this->bo->order;
			$this->filter		 = $this->bo->filter;
			$this->cat_id		 = $this->bo->cat_id;
			$this->dimb_id		 = $this->bo->dimb_id;
			$this->allrows		 = $this->bo->allrows;
			$this->district_id	 = $this->bo->district_id;
			$this->year			 = $this->bo->year;
			$this->month		 = $this->bo->month;
			$this->grouping		 = $this->bo->grouping;
			$this->revision		 = $this->bo->revision;
			$this->details		 = $this->bo->details;
			$this->direction	 = $this->bo->direction;

			$this->acl = & $GLOBALS['phpgw']->acl;
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
				'dimb_id'	 => $this->dimb_id,
				'allrows'	 => $this->allrows,
				'direction'	 => $this->direction,
				'month'		 => $this->month
			);
			$this->bo->save_sessiondata($data);
		}

		private function _get_filters( $selected = 0 )
		{
			$link = self::link(array(
					'menuaction'		 => 'property.uibudget.get_filters_dependent',
					'phpgw_return_as'	 => 'json'
			));

			$code = '
				var link = "' . $link . '";
				var data = {"year": $(this).val()};
				clearFilterParam("revision");
				clearFilterParam("grouping");
				
				execute_ajax(link,
					function(result){
						var $el_revision = $("#revision");
						$el_revision.empty();
						$.each(result.revision, function(key, value) {
						  $el_revision.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
						var $el_grouping = $("#grouping");
						$el_grouping.empty();
						$.each(result.grouping, function(key, value) {
						  $el_grouping.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
					}, data, "GET", "json"
				);
				';

			$values_combo_box[0] = $this->bo->get_year_filter_list($this->year);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no year')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'year',
				'extra'	 => $code,
				'text'	 => lang('year'),
				'list'	 => $values_combo_box[0]
			);

			$values_combo_box[1] = $this->bo->get_revision_filter_list($this->revision);
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('no revision')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'revision',
				'extra'	 => '',
				'text'	 => lang('revision'),
				'list'	 => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bocommon->select_district_list('filter', $this->district_id);
			array_unshift($values_combo_box[2], array('id' => '', 'name' => lang('no district')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'district_id',
				'extra'	 => '',
				'text'	 => lang('district'),
				'list'	 => $values_combo_box[2]
			);


			$values_combo_box[3] = $this->bo->get_grouping_filter_list($this->grouping);
			array_unshift($values_combo_box[3], array('id' => '', 'name' => lang('no grouping')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'grouping',
				'extra'	 => '',
				'text'	 => lang('grouping'),
				'list'	 => $values_combo_box[3]
			);

			$cat_filter = $this->cats->formatted_xslt_list(array('select_name'	 => 'cat_id',
				'selected'		 => $this->cat_id, 'globals'		 => True, 'link_data'		 => $link_data));
			foreach ($cat_filter['cat_list'] as $_cat)
			{
				$values_combo_box[4][] = array
					(
					'id'		 => $_cat['cat_id'],
					'name'		 => $_cat['name'],
					'selected'	 => $_cat['selected'] ? 1 : 0
				);
			}
			array_unshift($values_combo_box[4], array('id' => '', 'name' => lang('no category')));
			$combos[] = array
				(
				'type'	 => 'filter',
				'name'	 => 'cat_id',
				'extra'	 => '',
				'text'	 => lang('Category'),
				'list'	 => $values_combo_box[4]
			);

			$values_combo_box[5] = $this->bocommon->select_category_list(array('type' => 'dimb'));
			foreach ($values_combo_box[5] as & $_dimb)
			{
				$_dimb['name'] = "{$_dimb['id']}-{$_dimb['name']}";
			}
			array_unshift($values_combo_box[5], array('id' => '', 'name' => lang('no dimb')));
			$combos[] = array
				(
				'type'	 => 'filter',
				'name'	 => 'dimb_id',
				'extra'	 => '',
				'text'	 => lang('dimb'),
				'list'	 => $values_combo_box[5]
			);

			return $combos;
		}

		private function _get_filters_basis( $selected = 0 )
		{
			$basis = true;

			$link = self::link(array(
					'menuaction'		 => 'property.uibudget.get_filters_dependent',
					'phpgw_return_as'	 => 'json'
			));

			$code = '
				var link = "' . $link . '";
				var data = {"year": $(this).val(), "basis":1};
				clearFilterParam("revision");
				clearFilterParam("grouping");
				
				execute_ajax(link,
					function(result){
						var $el_revision = $("#revision");
						$el_revision.empty();
						$.each(result.revision, function(key, value) {
						  $el_revision.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
						var $el_grouping = $("#grouping");
						$el_grouping.empty();
						$.each(result.grouping, function(key, value) {
						  $el_grouping.append($("<option></option>").attr("value", value.id).text(value.name));
						});
						
					}, data, "GET", "json"
				);
				';

			$values_combo_box[0] = $this->bo->get_year_filter_list($this->year, $basis);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no year')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'year',
				'extra'	 => $code,
				'text'	 => lang('year'),
				'list'	 => $values_combo_box[0]
			);

			$values_combo_box[1] = $this->bo->get_revision_filter_list($this->revision, $basis);
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('no revision')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'revision',
				'extra'	 => '',
				'text'	 => lang('revision'),
				'list'	 => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bocommon->select_district_list('filter', $this->district_id);
			array_unshift($values_combo_box[2], array('id' => '', 'name' => lang('no district')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'district_id',
				'extra'	 => '',
				'text'	 => lang('district'),
				'list'	 => $values_combo_box[2]
			);


			$values_combo_box[3] = $this->bo->get_grouping_filter_list($this->grouping, $basis);
			array_unshift($values_combo_box[3], array('id' => '', 'name' => lang('no grouping')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'grouping',
				'extra'	 => '',
				'text'	 => lang('grouping'),
				'list'	 => $values_combo_box[3]
			);


			$values_combo_box[4] = $this->bocommon->select_category_list(array('type' => 'dimb'));
			array_unshift($values_combo_box[4], array('id' => '', 'name' => lang('no dimb')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'dimb_id',
				'extra'	 => '',
				'text'	 => lang('dimb'),
				'list'	 => $values_combo_box[4]
			);

			return $combos;
		}

		private function _get_filters_obligations( $selected = 0 )
		{
			$values_combo_box[0] = $this->bo->get_year_filter_list($this->year, $basis				 = false);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no year')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'year',
				'extra'	 => '',
				'text'	 => lang('year'),
				'list'	 => $values_combo_box[0]
			);

			for ($i = 1; $i < 13; $i++)
			{
				$values_combo_box[1][] = array('id' => $i, 'name' => sprintf("%02s", $i));
			}
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('month')));
			$combos[] = array
				(
				'type'	 => 'filter',
				'name'	 => 'month',
				'extra'	 => '',
				'text'	 => lang('month'),
				'list'	 => $values_combo_box[1]
			);

			$values_combo_box[2] = $this->bocommon->select_district_list('filter', $this->district_id);
			array_unshift($values_combo_box[2], array('id' => '', 'name' => lang('no district')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'district_id',
				'extra'	 => '',
				'text'	 => lang('district'),
				'list'	 => $values_combo_box[2]
			);


			$values_combo_box[3] = $this->bo->get_b_group_list($this->grouping);
			array_unshift($values_combo_box[3], array('id' => '', 'name' => lang('no grouping')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'grouping',
				'extra'	 => '',
				'text'	 => lang('grouping'),
				'list'	 => $values_combo_box[3]
			);


			$cat_filter = $this->cats->formatted_xslt_list(array('select_name'	 => 'cat_id',
				'selected'		 => $this->cat_id, 'globals'		 => True, 'link_data'		 => $link_data));
			foreach ($cat_filter['cat_list'] as $_cat)
			{
				$values_combo_box[4][] = array
					(
					'id'		 => $_cat['cat_id'],
					'name'		 => $_cat['name'],
					'selected'	 => $_cat['selected'] ? 1 : 0
				);
			}
			array_unshift($values_combo_box[4], array('id' => '', 'name' => lang('no category')));
			$combos[] = array
				(
				'type'	 => 'filter',
				'name'	 => 'cat_id',
				'extra'	 => '',
				'text'	 => lang('Category'),
				'list'	 => $values_combo_box[4]
			);

			$values_combo_box[5] = $this->bocommon->select_category_list(array('type' => 'org_unit'));
			array_unshift($values_combo_box[5], array('id' => '', 'name' => lang('department')));
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'org_unit_id',
				'extra'	 => '',
				'text'	 => lang('department'),
				'list'	 => $values_combo_box[5]
			);

			$values_combo_box[6] = $this->bocommon->select_category_list(array('type' => 'dimb'));
			foreach ($values_combo_box[6] as & $_dimb)
			{
				$_dimb['name'] = "{$_dimb['id']}-{$_dimb['name']}";
			}
			array_unshift($values_combo_box[6], array('id' => '', 'name' => lang('no dimb')));
			$combos[] = array
				(
				'type'	 => 'filter',
				'name'	 => 'dimb_id',
				'extra'	 => '',
				'text'	 => lang('dimb'),
				'list'	 => $values_combo_box[6]
			);

			$values_combo_box[7] = array
				(
				array
					(
					'id'		 => 'expenses',
					'name'		 => lang('expenses'),
					'selected'	 => $this->direction == 'expenses' ? 1 : 0
				),
				array
					(
					'id'		 => 'income',
					'name'		 => lang('income'),
					'selected'	 => $this->direction == 'income' ? 1 : 0
				),
				array
					(
					'id'		 => 'both',
					'name'		 => lang('both'),
					'selected'	 => $this->direction == 'both' ? 1 : 0
				)
			);
			$combos[]			 = array
				(
				'type'	 => 'filter',
				'name'	 => 'direction',
				'extra'	 => '',
				'text'	 => lang('direction'),
				'list'	 => $values_combo_box[7]
			);

			return $combos;
		}

		function get_filters_dependent()
		{
			$basis = phpgw::get_var('draw', 'bool');

			$revision = $this->bo->get_revision_filter_list($this->revision, $basis);
			array_unshift($revision, array('id' => '', 'name' => lang('no revision')));

			$grouping = $this->bo->get_grouping_filter_list($this->grouping, $basis);
			array_unshift($grouping, array('id' => '', 'name' => lang('no grouping')));

			return $result = array('revision' => $revision, 'grouping' => $grouping);
		}

		function index()
		{
			$acl_location	 = '.budget';
			$acl_read		 = $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if (!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $acl_location));
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$acl_add	 = $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit	 = $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete	 = $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::budget';

			$data = array(
				'datatable_name' => lang('list budget'),
				'form'			 => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uibudget.index',
						'phpgw_return_as'	 => 'json'
					)),
					'download'		 => self::link(array(
						'menuaction' => 'property.uibudget.download',
						'export'	 => true,
						'allrows'	 => true,
						'download'	 => 'budget'
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array()
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			if ($acl_add)
			{
				$data['datatable']['new_item'] = self::link(array(
						'menuaction' => 'property.uibudget.edit'
				));
			}

			$uicols = array(
				array('hidden' => true, 'key' => 'budget_id', 'label' => 'dummy', 'sortable' => false),
				array('hidden'	 => false, 'key'		 => 'year', 'label'		 => lang('year'), 'className'	 => 'center',
					'sortable'	 => false),
				array('hidden'	 => false, 'key'		 => 'revision', 'label'		 => lang('revision'),
					'className'	 => 'center',
					'sortable'	 => false),
				array('hidden'	 => false, 'key'		 => 'b_account_id', 'label'		 => lang('budget account'),
					'className'	 => 'center', 'sortable'	 => false),
				array('hidden'	 => false, 'key'		 => 'b_account_name', 'label'		 => lang('name'),
					'sortable'	 => false),
				array('hidden'	 => false, 'key'		 => 'grouping', 'label'		 => lang('grouping'),
					'className'	 => 'right',
					'sortable'	 => true),
				array('hidden'	 => false, 'key'		 => 'district_id', 'label'		 => lang('district'),
					'className'	 => 'right', 'sortable'	 => true),
				array('hidden'	 => false, 'key'		 => 'ecodimb', 'label'		 => lang('dimb'), 'className'	 => 'right',
					'sortable'	 => true),
				array('hidden' => false, 'key' => 'category', 'label' => lang('category'), 'sortable' => false),
				array('hidden'	 => false, 'key'		 => 'budget_cost', 'label'		 => lang('budget cost'),
					'className'	 => 'right', 'sortable'	 => true, 'formatter'	 => 'JqueryPortico.FormatterAmount0'),
			);

			foreach ($uicols as $col)
			{
				array_push($data['datatable']['field'], $col);
			}

			$parameters = array('parameter' => array(array('name' => 'budget_id', 'source' => 'budget_id')));

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'edit',
				'text'		 => lang('edit'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.edit')),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array(
				'my_name'		 => 'delete',
				'text'			 => lang('delete'),
				'confirm_msg'	 => lang('do you really want to delete this entry'),
				'action'		 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.delete')),
				'parameters'	 => json_encode($parameters)
			);

			/* if($acl_add)
			  {
			  $data['datatable']['actions'][] = array(
			  'my_name'		=> 'add',
			  'text' 			=> lang('add'),
			  'action'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit'))
			  );
			  } */
			unset($parameters);
//                        var_dump($data['form']['toolbar']['item']);
//                        echo "<hr>";
//                        var_dump($data['form']['toolbar']['item'][0]);
//                        echo "<hr>";
//                        var_dump($data['form']['toolbar']['item'][1]);
//                        echo "<hr>";
//                        var_dump($data['form']['toolbar']['item'][2]);
//                        echo "<hr>";
//                        var_dump($data['form']['toolbar']['item'][3]);
//                        echo "<hr>";
//                        var_dump($data['form']['toolbar']['item'][4]);
//                        echo "<hr>";
//                        var_dump($data['form']['toolbar']['item'][5]);
//                        exit();
			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');

			phpgwapi_jquery::load_widget('numberformat');

			self::add_javascript('property', 'portico', 'budget.index.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$search		 = phpgw::get_var('search');
			$order		 = phpgw::get_var('order');
			$draw		 = phpgw::get_var('draw', 'int');
			$columns	 = phpgw::get_var('columns');
			$export		 = phpgw::get_var('export', 'bool');
			$order_field = '';

			switch ($columns[$order[0]['column']]['data'])
			{
				case 'ecodimb':
					$order_field = 'fm_budget.ecodimb';
					break;
				case 'grouping':
					$order_field = 'category';
					break;
				default:
					$order_field = $columns[$order[0]['column']]['data'];
			}

			$params = array(
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => $order_field,
				'sort'		 => $order[0]['dir'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export
			);

			$values = $this->bo->read($params);

			if ($export)
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;
			$result_data['sum_budget']		 = number_format((float)$this->bo->sum_budget_cost, 0, ',', ' ');

			return $this->jquery_results($result_data);
		}

		function basis()
		{
			$acl_location	 = '.budget';
			$acl_read		 = $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if (!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $acl_location));
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_basis();
			}

			$acl_add	 = $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit	 = $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete	 = $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::basis';

			$data = array(
				'datatable_name' => lang('list budget'),
				'form'			 => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uibudget.basis',
						'phpgw_return_as'	 => 'json'
					)),
					'download'		 => self::link(array(
						'menuaction' => 'property.uibudget.download',
						'export'	 => true,
						'allrows'	 => true,
						'download'	 => 'basis'
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array()
				)
			);

			$filters = $this->_get_Filters_basis();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			if ($acl_add)
			{
				$data['datatable']['new_item'] = self::link(array(
						'menuaction' => 'property.uibudget.add_basis'
				));
			}

			$uicols = array(
				array('hidden' => true, 'key' => 'budget_id', 'label' => 'dummy', 'sortable' => false),
				array('hidden'	 => false, 'key'		 => 'year', 'label'		 => lang('year'), 'className'	 => 'center',
					'sortable'	 => false),
				array('hidden'	 => false, 'key'		 => 'revision', 'label'		 => lang('revision'),
					'className'	 => 'center',
					'sortable'	 => false),
				array('hidden'	 => false, 'key'		 => 'grouping', 'label'		 => lang('grouping'),
					'className'	 => 'right',
					'sortable'	 => true),
				array('hidden'	 => false, 'key'		 => 'district_id', 'label'		 => lang('district_id'),
					'className'	 => 'right', 'sortable'	 => true),
				array('hidden'	 => false, 'key'		 => 'ecodimb', 'label'		 => lang('dimb'), 'className'	 => 'right',
					'sortable'	 => true),
				array('hidden'	 => false, 'key'		 => 'category', 'label'		 => lang('category'),
					'className'	 => 'right',
					'sortable'	 => false),
				array('hidden'	 => false, 'key'		 => 'budget_cost', 'label'		 => lang('budget_cost'),
					'className'	 => 'right', 'sortable'	 => true, 'formatter'	 => 'JqueryPortico.FormatterAmount0')
			);

			foreach ($uicols as $col)
			{
				array_push($data['datatable']['field'], $col);
			}

			$parameters = array('parameter' => array(array('name' => 'budget_id', 'source' => 'budget_id')));

			$data['datatable']['actions'][] = array(
				'my_name'	 => 'edit',
				'text'		 => lang('edit'),
				'action'	 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.edit_basis')),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array(
				'my_name'		 => 'delete',
				'text'			 => lang('delete'),
				'confirm_msg'	 => lang('do you really want to delete this entry'),
				'action'		 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.delete_basis')),
				'parameters'	 => json_encode($parameters)
			);

			/* if($acl_add)
			  {
			  $datatable['rowactions']['action'][] = array(
			  'my_name'		=> 'add',
			  'text' 			=> lang('add'),
			  'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit_basis'))
			  );
			  } */

			unset($parameters);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('property', 'portico', 'budget.basis.js');

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query_basis()
		{
			$search		 = phpgw::get_var('search');
			$order		 = phpgw::get_var('order');
			$draw		 = phpgw::get_var('draw', 'int');
			$columns	 = phpgw::get_var('columns');
			$export		 = phpgw::get_var('export', 'bool');
			$order_field = '';

			switch ($columns[$order[0]['column']]['data'])
			{
				case 'ecodimb':
					$order_field = 'fm_budget.ecodimb';
					break;
				case 'grouping':
					$order_field = 'b_group';
					break;
				default:
					$order_field = $columns[$order[0]['column']]['data'];
			}

			$params = array(
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => $order_field,
				'sort'		 => $order[0]['dir'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export
			);

			$values = $this->bo->read_basis($params);

			if ($export)
			{
				return $values;
			}

			$result_data					 = array('results' => $values);
			$result_data['total_records']	 = $this->bo->total_records;
			$result_data['draw']			 = $draw;

			return $this->jquery_results($result_data);
		}

		function obligations()
		{
			$acl_location	 = '.budget.obligations';
			$acl_read		 = $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if (!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 1, 'acl_location'	 => $acl_location));
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_obligations();
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::obligations';

			$data = array(
				'datatable_name' => lang('list obligations'),
				'form'			 => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable'		 => array(
					'source'		 => self::link(array(
						'menuaction'		 => 'property.uibudget.obligations',
						'phpgw_return_as'	 => 'json'
					)),
					'download'		 => self::link(array(
						'menuaction' => 'property.uibudget.download',
						'export'	 => true,
						'allrows'	 => true,
						'download'	 => 'obligations'
					)),
					'allrows'		 => true,
					'editor_action'	 => '',
					'field'			 => array()
				)
			);

			$filters = $this->_get_filters_obligations();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$uicols = array(
				array('key'		 => 'grouping', 'hidden'	 => true, 'label'		 => '', 'className'	 => '',
					'sortable'	 => false),
				array('key'		 => 'b_account', 'hidden'	 => false, 'label'		 => lang('grouping'),
					'className'	 => 'center',
					'sortable'	 => true, 'formatter'	 => 'myformatLinkPGW'),
				array('key'		 => 'ecodimb', 'hidden'	 => false, 'label'		 => lang('dimb'), 'className'	 => 'center',
					'sortable'	 => false),
				array('key'		 => 'hits_ex', 'hidden'	 => true, 'label'		 => '', 'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'hits', 'hidden'	 => false, 'label'		 => lang('hits'), 'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'budget_cost_ex', 'hidden'	 => true, 'label'		 => '', 'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'budget_cost', 'hidden'	 => false, 'label'		 => lang('budget'),
					'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'obligation_ex', 'hidden'	 => true, 'label'		 => '', 'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'obligation', 'hidden'	 => false, 'label'		 => lang('sum orders'),
					'className'	 => 'right', 'sortable'	 => false, 'formatter'	 => 'myFormatLink_Count'),
				array('key' => 'link_obligation', 'hidden' => true, 'label' => '', 'sortable' => false),
				array('key'		 => 'actual_cost_ex', 'hidden'	 => true, 'label'		 => '', 'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'actual_cost_period', 'hidden'	 => false, 'label'		 => lang('paid') . ' ' . lang('period'),
					'className'	 => 'right', 'sortable'	 => false),
				array('key'		 => 'actual_cost', 'hidden'	 => false, 'label'		 => lang('paid'),
					'className'	 => 'right',
					'sortable'	 => false, 'formatter'	 => 'myFormatLink_Count'),
				array('key'		 => 'link_actual_cost', 'hidden'	 => true, 'label'		 => '', 'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'diff_ex', 'hidden'	 => true, 'label'		 => '', 'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'diff', 'hidden'	 => false, 'label'		 => lang('difference'),
					'className'	 => 'right',
					'sortable'	 => false),
				array('key'		 => 'percent', 'hidden'	 => false, 'label'		 => lang('percent'),
					'className'	 => 'right',
					'sortable'	 => false)
			);

			foreach ($uicols as $col)
			{
				array_push($data['datatable']['field'], $col);
			}

			$data['datatable']['actions'][] = array();

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list obligations');

			phpgwapi_jquery::load_widget('numberformat');
			self::add_javascript('property', 'portico', 'budget.obligations.js');

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query_obligations()
		{
			$search	 = phpgw::get_var('search');
			$order	 = phpgw::get_var('order');
			$draw	 = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export	 = phpgw::get_var('export', 'bool');

			$params = array(
				'start'		 => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results'	 => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query'		 => $search['value'],
				'order'		 => $columns[$order[0]['column']]['data'],
				'sort'		 => $order[0]['dir'],
				'allrows'	 => phpgw::get_var('length', 'int') == -1 || $export
			);

			$location_list = $this->bo->read_obligations($params);

			if ($export)
			{
				return $location_list;
			}

			if (isset($location_list) && is_array($location_list))
			{
				//$details = $this->details ? false : true;

				$start_date	 = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, 1, 1, $this->year), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$end_date	 = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, 12, 31, $this->year), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				//$sum_obligation = $sum_hits = $sum_budget_cost = $sum_actual_cost = 0;
				foreach ($location_list as $entry)
				{
					$values[] = array
						(
						'grouping'			 => $entry['grouping'],
						'b_account'			 => $entry['b_account'],
						'district_id'		 => $entry['district_id'],
						'ecodimb'			 => $entry['ecodimb'],
						'hits_ex'			 => $entry['hits'],
						'hits'				 => number_format((float)$entry['hits'], 0, ',', ' '),
						'budget_cost_ex'	 => $entry['budget_cost'],
						'budget_cost'		 => number_format((float)$entry['budget_cost'], 0, ',', ' '),
						'obligation_ex'		 => $entry['obligation'],
						'obligation'		 => number_format((float)$entry['obligation'], 0, ',', ' '),
						'link_obligation'	 => urldecode($GLOBALS['phpgw']->link('/index.php', array(
								'menuaction'		 => 'property.uiworkorder.index', 'filter'			 => 'all', // 'paid' => 1,
								'district_id'		 => $entry['district_id'], 'b_group'			 => $entry['grouping'],
								'b_account'			 => $entry['b_account'],
								'filter_start_date'	 => $start_date, 'filter_end_date'	 => $end_date, 'ecodimb'			 => $entry['ecodimb'],
								'status_id'			 => 'all', 'obligation'		 => true))),
						'actual_cost_ex'	 => $entry['actual_cost'],
						'actual_cost_period' => number_format((float)$entry['actual_cost_period'], 0, ',', ' '),
						'actual_cost'		 => number_format((float)$entry['actual_cost'], 0, ',', ' '),
						'link_actual_cost'	 => urldecode($GLOBALS['phpgw']->link('/index.php', array(
								'menuaction'		 => 'property.uiinvoice.consume', 'district_id'		 => $entry['district_id'],
								'b_account_class'	 => $entry['grouping'], 'b_account'			 => $entry['b_account'],
								'start_date'		 => $start_date, 'end_date'			 => $end_date, 'ecodimb'			 => $entry['ecodimb'],
								'submit_search'		 => true))),
						'diff_ex'			 => $entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation'],
						'diff'				 => number_format((float)$entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation'], 0, ',', ' '),
						'percent'			 => (int)$entry['percent'],
						'year'				 => $this->year,
						'month'				 => $this->month
					);
				}
			}

			$result_data						 = array('results' => $values);
			$result_data['total_records']		 = $this->bo->total_records;
			$result_data['draw']				 = $draw;
			$result_data['sum_budget']			 = number_format((float)$this->bo->sum_budget_cost, 0, ',', ' ');
			$result_data['sum_obligation']		 = number_format((float)$this->bo->sum_obligation_cost, 0, ',', ' ');
			$result_data['sum_actual']			 = number_format((float)$this->bo->sum_actual_cost, 0, ',', ' ');
			$result_data['sum_actual_period']	 = number_format((float)$this->bo->sum_actual_cost_period, 0, ',', ' ');
			$result_data['sum_diff']			 = number_format((float)($this->bo->sum_budget_cost - $this->bo->sum_actual_cost - $this->bo->sum_obligation_cost), 0, ',', ' ');
			$result_data['sum_hits']			 = number_format((float)$this->bo->sum_hits, 0, ',', ' ');

			return $this->jquery_results($result_data);
		}

		public function add()
		{
			$this->edit();
		}

		function edit( $values = array() )
		{
			$acl_location	 = '.budget';
			$acl_add		 = $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit		 = $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');

			if (!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $acl_location));
			}

			$budget_id = phpgw::get_var('budget_id', 'int');

			if ($values['budget_id'])
			{
				$budget_id = $values['budget_id'];
			}

			if ($budget_id)
			{
				$values = $this->bo->read_single($budget_id);
			}

			$link_data = array
				(
				'menuaction' => 'property.uibudget.save',
				'budget_id'	 => $budget_id
			);

			//$msgbox_data = $this->bocommon->msgbox_data($receipt);
			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$b_account_data = $this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id'	 => $values['b_account_id'],
				'b_account_name' => isset($values['b_account_name']) ? $values['b_account_name'] : '',
				'type'			 => isset($values['b_account_id']) && $values['b_account_id'] > 0 ? 'view' : 'form',
				'required'		 => true
			));

			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array(
				'ecodimb'		 => $values['ecodimb'],
				'ecodimb_descr'	 => $values['ecodimb_descr']));

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab		 = 'generic';

			$data											 = array
				(
				'ecodimb_data'				 => $ecodimb_data,
				'lang_category'				 => lang('category'),
				'lang_no_cat'				 => lang('Select category'),
				'cat_select'				 => $this->cats->formatted_xslt_list(array('select_name'	 => 'values[cat_id]',
					'selected'		 => $values['cat_id'])),
				'b_account_data'			 => $b_account_data,
				'value_b_account'			 => $values['b_account_id'],
				'lang_revision'				 => lang('revision'),
				'lang_revision_statustext'	 => lang('Select revision'),
				'revision_list'				 => $this->bo->get_revision_list($values['revision']),
				'lang_year'					 => lang('year'),
				'lang_year_statustext'		 => lang('Budget year'),
				'year'						 => $this->bocommon->select_list($values['year'] ? $values['year'] : date('Y'), $this->bo->get_year_list()),
				'lang_district'				 => lang('District'),
				'lang_no_district'			 => lang('no district'),
				'lang_district_statustext'	 => lang('Select the district'),
				'select_district_name'		 => 'values[district_id]',
				'district_list'				 => $this->bocommon->select_district_list('select', $values['district_id']),
				'msgbox_data'				 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'				 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.index')),
				'lang_budget_id'			 => lang('ID'),
				'value_budget_id'			 => $budget_id,
				'lang_budget_cost'			 => lang('budget cost'),
				'lang_remark'				 => lang('remark'),
				'lang_save'					 => lang('save'),
				'lang_cancel'				 => lang('cancel'),
				'lang_apply'				 => lang('apply'),
				'value_remark'				 => $values['remark'],
				'value_budget_cost'			 => $values['budget_cost'],
				'lang_name_statustext'		 => lang('Enter a name for the query'),
				'lang_remark_statustext'	 => lang('Enter a remark'),
				'lang_apply_statustext'		 => lang('Apply the values'),
				'lang_cancel_statustext'	 => lang('Leave the budget untouched and return to the list'),
				'lang_save_statustext'		 => lang('Save the budget and return to the list'),
				'tabs'						 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'					 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);
			$GLOBALS['phpgw_info']['flags']['app_header']	 = lang('budget') . ': ' . ($budget_id ? lang('edit budget') : lang('add budget'));

			self::render_template_xsl(array('budget'), array('edit' => $data));
		}

		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$budget_id	 = phpgw::get_var('budget_id', 'int');
			$values		 = phpgw::get_var('values');

			$values['b_account_id']		 = phpgw::get_var('b_account_id', 'int', 'POST');
			$values['b_account_name']	 = phpgw::get_var('b_account_name', 'string', 'POST');
			$values['ecodimb']			 = phpgw::get_var('ecodimb');

			if (!$values['b_account_id'] > 0)
			{
				$values['b_account_id']		 = '';
				$this->receipt['error'][]	 = array('msg' => lang('Please select a budget account !'));
			}
			if ($budget_id)
			{
				$values['budget_id'] = $budget_id;
			}

			if ($this->receipt['error'])
			{
				$this->edit();
			}
			else
			{
				try
				{
					$receipt			 = $this->bo->save($values);
					$budget_id			 = $values['budget_id'] = $receipt['budget_id'];
					$this->receipt		 = $receipt;
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

				if ($values['apply'])
				{
					if ($budget_id)
					{
						self::message_set($this->receipt);
						self::redirect(array('menuaction' => 'property.uibudget.edit', 'budget_id' => $budget_id));
					}

					$this->edit($values);
					return;
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uibudget.index'));
			}
		}

		public function add_basis()
		{
			$this->edit_basis();
		}

		function edit_basis( $values = array() )
		{
			$acl_location	 = '.budget';
			$acl_add		 = $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit		 = $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');

			if (!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'	 => 'property.uilocation.stop',
					'perm'			 => 2, 'acl_location'	 => $acl_location));
			}

			$budget_id = phpgw::get_var('budget_id', 'int');

			if ($this->receipt['error'])
			{
				$year_selected	 = $values['year'];
				$district_id	 = $values['district_id'];
				$revision		 = $values['revision'];
				$b_group		 = $values['b_group'];

				unset($values['year']);
				unset($values['district_id']);
				unset($values['revision']);
				unset($values['b_group']);
			}

			if ($values['budget_id'])
			{
				$budget_id = $values['budget_id'];
			}

			if ($budget_id)
			{
				unset($values);
				$values = $this->bo->read_single_basis($budget_id);
			}

			$link_data = array
				(
				'menuaction' => 'property.uibudget.save_basis',
				'budget_id'	 => $budget_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$year[0]['id']	 = date(Y);
			$year[1]['id']	 = date(Y) + 1;
			$year[2]['id']	 = date(Y) + 2;
			$year[3]['id']	 = date(Y) + 3;

			$ecodimb_data = $this->bocommon->initiate_ecodimb_lookup(array(
				'ecodimb'		 => $values['ecodimb'],
				'ecodimb_descr'	 => $values['ecodimb_descr']));

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab		 = 'generic';

			$data = array
				(
				'ecodimb_data'						 => $ecodimb_data,
				'lang_category'						 => lang('category'),
				'lang_no_cat'						 => lang('Select category'),
				'cat_select'						 => $this->cats->formatted_xslt_list(array('select_name'	 => 'values[cat_id]',
					'selected'		 => $values['cat_id'])),
				'lang_distribute'					 => lang('distribute'),
				'lang_distribute_year'				 => lang('distribute year'),
				'lang_distribute_year_statustext'	 => lang('of years'),
				'distribute_year_list'				 => $this->bo->get_distribute_year_list($values['distribute_year']),
				'lang_revision'						 => lang('revision'),
				'lang_revision_statustext'			 => lang('Select revision'),
				'revision_list'						 => $this->bo->get_revision_list($revision),
				'lang_b_group'						 => lang('budget group'),
				'lang_b_group_statustext'			 => lang('Select budget group'),
				'b_group_list'						 => $this->bo->get_b_group_list($b_group),
				'lang_year'							 => lang('year'),
				'lang_year_statustext'				 => lang('Budget year'),
				'year'								 => $this->bocommon->select_list($year_selected, $year),
				'lang_district'						 => lang('District'),
				'lang_no_district'					 => lang('no district'),
				'lang_district_statustext'			 => lang('Select the district'),
				'select_district_name'				 => 'values[district_id]',
				'district_required'					 => 1,
				'district_list'						 => $this->bocommon->select_district_list('select', $district_id),
				'value_year'						 => $values['year'],
				'value_district_id'					 => $values['district_id'],
				'value_b_group'						 => $values['b_group'],
				'value_revision'					 => $values['revision'],
				'msgbox_data'						 => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'							 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action'						 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.basis')),
				'lang_budget_id'					 => lang('ID'),
				'value_budget_id'					 => $budget_id,
				'value_distribute_id'				 => $budget_id ? $budget_id : 'new',
				'lang_budget_cost'					 => lang('budget cost'),
				'lang_remark'						 => lang('remark'),
				'lang_save'							 => lang('save'),
				'lang_cancel'						 => lang('cancel'),
				'lang_apply'						 => lang('apply'),
				'value_remark'						 => $values['remark'],
				'value_budget_cost'					 => $values['budget_cost'],
				'lang_name_statustext'				 => lang('Enter a name for the query'),
				'lang_remark_statustext'			 => lang('Enter a remark'),
				'lang_apply_statustext'				 => lang('Apply the values'),
				'lang_cancel_statustext'			 => lang('Leave the budget untouched and return to the list'),
				'lang_save_statustext'				 => lang('Save the budget and return to the list'),
				'tabs'								 => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator'							 => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . ($budget_id ? lang('edit budget') : lang('add budget'));

			self::render_template_xsl(array('budget'), array('edit_basis' => $data));
		}

		public function save_basis()
		{
			if (!$_POST)
			{
				return $this->edit_basis();
			}

			$budget_id			 = phpgw::get_var('budget_id', 'int');
			$values				 = phpgw::get_var('values');
			$values['ecodimb']	 = phpgw::get_var('ecodimb');

			if (!$values['b_group'] && !$budget_id)
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a budget group !'));
			}

			if (!$values['district_id'] && !$budget_id)
			{
				$this->receipt['error'][] = array('msg' => lang('Please select a district !'));
			}

			if (!$values['budget_cost'])
			{
				$this->receipt['error'][] = array('msg' => lang('Please enter a budget cost !'));
			}

			if ($budget_id)
			{
				$values['budget_id'] = $budget_id;
			}

			if ($this->receipt['error'])
			{
				$this->edit_basis($values);
			}
			else
			{
				try
				{
					$receipt			 = $this->bo->save_basis($values);
					$budget_id			 = $values['budget_id'] = $receipt['budget_id'];
					$this->receipt		 = $receipt;
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit_basis($values);
						return;
					}
				}

				if ($values['apply'])
				{
					if ($budget_id)
					{
						self::message_set($this->receipt);
						self::redirect(array('menuaction' => 'property.uibudget.edit_basis', 'budget_id' => $budget_id));
					}
					$this->edit_basis($values);
					return;
				}
				self::redirect(array('menuaction' => 'property.uibudget.basis'));
			}
		}

		function delete()
		{
			$budget_id = phpgw::get_var('budget_id', 'int');
			//cramirez add JsonCod for Delete
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($budget_id);
				return "budget_id " . $budget_id . " " . lang("has been deleted");
			}

			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uibudget.index'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.delete',
					'budget_id'	 => $budget_id)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_statustext'	 => lang('Delete the entry'),
				'lang_no_statustext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('budget');
			$function_msg	 = lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function delete_basis()
		{
			$budget_id = phpgw::get_var('budget_id', 'int');
			//JsonCod for Delete
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete_basis($budget_id);
				return "budget_id " . $budget_id . " " . lang("has been deleted");
			}



			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uibudget.basis'
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_basis($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action'			 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action'			 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.delete_basis',
					'budget_id'	 => $budget_id)),
				'lang_confirm_msg'		 => lang('do you really want to delete this entry'),
				'lang_yes'				 => lang('yes'),
				'lang_yes_statustext'	 => lang('Delete the entry'),
				'lang_no_statustext'	 => lang('Back to the list'),
				'lang_no'				 => lang('no')
			);

			$appname		 = lang('budget');
			$function_msg	 = lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function view()
		{
			$budget_id = phpgw::get_var('budget_id', 'int', 'GET');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget', 'nextmatchs'));

			$list	 = $this->bo->read_budget($budget_id);
			$uicols	 = $this->bo->uicols;

			//_debug_array($uicols);

			$j = 0;
			if (isSet($list) AND is_array($list))
			{
				foreach ($list as $entry)
				{
					for ($i = 0; $i < count($uicols); $i++)
					{
						$content[$j]['row'][$i]['value'] = $entry[$uicols[$i]['name']];
					}

					$j++;
				}
			}

			for ($i = 0; $i < count($uicols); $i++)
			{
				$table_header[$i]['header']	 = $uicols[$i]['descr'];
				$table_header[$i]['width']	 = '15%';
				$table_header[$i]['align']	 = 'left';
			}

			//_debug_array($content);


			$budget_name = $this->bo->read_budget_name($budget_id);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . $budget_name;

			$link_data = array
				(
				'menuaction' => 'property.uibudget.view',
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'budget_id'	 => $budget_id,
				'filter'	 => $this->filter,
				'query'		 => $this->query
			);


			if (!$this->allrows)
			{
				$record_limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit = $this->bo->total_records;
			}

			$link_download = array
				(
				'menuaction' => 'property.uibudget.download',
				'sort'		 => $this->sort,
				'order'		 => $this->order,
				'filter'	 => $this->filter,
				'query'		 => $this->query,
				'budget_id'	 => $budget_id,
				'allrows'	 => $this->allrows
			);

			self::add_javascript('property', 'overlib', 'overlib.js');

			$data = array
				(
				'lang_download'					 => 'download',
				'link_download'					 => $GLOBALS['phpgw']->link('/index.php', $link_download),
				'lang_download_help'			 => lang('Download table to your browser'),
				'allow_allrows'					 => true,
				'allrows'						 => $this->allrows,
				'start_record'					 => $this->start,
				'record_limit'					 => $record_limit,
				'num_records'					 => count($list),
				'all_records'					 => $this->bo->total_records,
				'link_url'						 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path'						 => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
				'select_action'					 => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_searchfield_statustext'	 => lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'	 => lang('Submit the search string'),
				'query'							 => $this->query,
				'lang_search'					 => lang('search'),
				'table_header'					 => $table_header,
				'values'						 => $content,
				'done_action'					 => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uibudget.index')),
				'lang_done'						 => lang('done'),
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('view' => $data));
		}

		function download()
		{
			switch (phpgw::get_var('download'))
			{
				case 'basis':
					//$list= $this->bo->read_basis();
					$list	 = $this->query_basis();
					$names	 = array
						(
						'year',
						'revision',
						'grouping',
						'district_id',
						'ecodimb',
						'category',
						'budget_cost'
					);
					$descr	 = array
						(
						lang('year'),
						lang('revision'),
						lang('grouping'),
						lang('district_id'),
						lang('dimb'),
						lang('category'),
						lang('budget')
					);
					break;
				case 'budget':
					//$list= $this->bo->read();
					$list	 = $this->query();
					$names	 = array
						(
						'year',
						'revision',
						'b_account_id',
						'b_account_name',
						'grouping',
						'district_id',
						'ecodimb',
						'category',
						'budget_cost'
					);
					$descr	 = array
						(
						lang('year'),
						lang('revision'),
						lang('budget account'),
						lang('name'),
						lang('grouping'),
						lang('district_id'),
						lang('dimb'),
						lang('category'),
						lang('budget')
					);
					break;
				case 'obligations':

					//$gross_list= $this->bo->read_obligations();
					$gross_list	 = $this->query_obligations();
					//$sum_obligation = $sum_hits = $sum_budget_cost = $sum_actual_cost = 0;
					$list		 = array();
					foreach ($gross_list as $entry)
					{
						$list[] = array
							(
							'grouping'			 => $entry['grouping'],
							'b_account'			 => $entry['b_account'],
							'district_id'		 => $entry['district_id'],
							'ecodimb'			 => $entry['ecodimb'],
							'hits'				 => $entry['hits'],
							'budget_cost'		 => $entry['budget_cost'],
							'obligation'		 => $entry['obligation'],
							'actual_cost_period' => $entry['actual_cost_period'],
							'actual_cost'		 => $entry['actual_cost'],
							'diff'				 => ($entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation']),
						);
					}
					$names	 = array
						(
						'grouping',
						'b_account',
						'district_id',
						'ecodimb',
						'hits',
						'budget_cost',
						'obligation',
						'actual_cost_period',
						'actual_cost',
						'diff'
					);
					$descr	 = array
						(
						lang('grouping'),
						lang('budget account'),
						lang('district_id'),
						lang('dimb'),
						lang('hits'),
						lang('budget'),
						lang('sum orders'),
						lang('paid') . ' ' . lang('period'),
						lang('paid'),
						lang('difference')
					);
					break;
				default:
					return;
			}

			if ($list)
			{
				$this->bocommon->download($list, $names, $descr);
			}
		}
	}