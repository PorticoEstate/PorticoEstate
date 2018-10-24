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

	class property_uiwo_hour extends phpgwapi_uicommon_jquery
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
			'index' => true,
			'query' => true,
			'tender' => true,
			'view' => true,
			'template' => true,
			'save_template' => true,
			'prizebook' => true,
			'add' => true,
			'edit' => true,
			'delete' => true,
			'deviation' => true,
			'edit_deviation' => true,
			'pdf_order' => true,
			'import_calculation' => true,
	//		'send_all_orders'	=> true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs = CreateObject('phpgwapi.nextmatchs');
			$this->create_html = CreateObject('phpgwapi.xslttemplates');
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo = CreateObject('property.bowo_hour', true);
			$this->boworkorder = CreateObject('property.boworkorder');
			$this->boproject = CreateObject('property.boproject');
			$this->bopricebook = CreateObject('property.bopricebook');

			$this->bocommon = CreateObject('property.bocommon');
			$this->config = CreateObject('phpgwapi.config', 'property');

			$this->config->read();

			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.project';
			$this->acl_read = $this->acl->check('.project', PHPGW_ACL_READ, 'property');
			$this->acl_add = $this->acl->check('.project', PHPGW_ACL_ADD, 'property');
			$this->acl_edit = $this->acl->check('.project', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete = $this->acl->check('.project', PHPGW_ACL_DELETE, 'property');

			$this->start = $this->bopricebook->start;
			$this->query = $this->bopricebook->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->chapter_id = $this->bo->chapter_id;
			$this->allrows = $this->bopricebook->allrows;
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
				'chapter_id' => $this->chapter_id,
				'allrows' => $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function deviation()
		{

			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$hour_id = phpgw::get_var('hour_id', 'int');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_deviation();
			}

			$link_data = array
				(
				'menuaction' => 'property.uiwo_hour.edit_deviation',
				'workorder_id' => $workorder_id,
				'hour_id' => $hour_id
			);

			$parameters_edit = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

			$parameters_delete = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'deviation_id',
						'source' => 'id'
					)
				)
			);

			$tabletools = array
				(
				array('my_name' => 'select_all'),
				array('my_name' => 'select_none')
			);

			$tabletools[] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => self::link(array(
					'menuaction' => 'property.uiwo_hour.edit_deviation',
					'workorder_id' => $workorder_id,
					'hour_id' => $hour_id
				)),
				'parameters' => json_encode($parameters_edit)
			);

			$tabletools[] = array
				(
				'my_name' => 'delete',
				'text' => lang('delete'),
				'action' => self::link(array(
					'menuaction' => 'property.uiwo_hour.delete',
					'workorder_id' => $workorder_id,
					'hour_id' => $hour_id
				)),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'parameters' => json_encode($parameters_delete)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiwo_hour.deviation',
						'workorder_id' => $workorder_id, 'hour_id' => $hour_id, 'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => array
					(
					array('key' => 'id', 'label' => lang('ID'), 'sortable' => true),
					array('key' => 'amount', 'label' => lang('amount'), 'sortable' => true, 'className' => 'right'),
					array('key' => 'descr', 'label' => lang('Descr'), 'sortable' => true),
					array('key' => 'entry_date', 'label' => lang('date'), 'sortable' => true, 'className' => 'right')
				),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'lang_add' => lang('add'),
				'lang_add_statustext' => lang('add a deviation'),
				'add_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_done' => lang('done'),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.index',
					'workorder_id' => $workorder_id))
			);

			$appname = lang('Workorder');
			$function_msg = lang('list deviation');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::add_javascript('property', 'portico', 'wo_hour.deviation.js');
			self::render_template_xsl(array('wo_hour', 'datatable_inline'), array('list_deviation' => $data));
		}

		public function query_deviation()
		{
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$hour_id = phpgw::get_var('hour_id', 'int');
			$draw = phpgw::get_var('draw', 'int');

			$list = $this->bo->read_deviation(array('workorder_id' => $workorder_id, 'hour_id' => $hour_id));

			$sum_deviation = 0;
			$values = array();

			if (isset($list) and is_array($list))
			{
				$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				foreach ($list as $entry)
				{
					$sum_deviation = $sum_deviation + $entry['amount'];

					$entry_date = (isset($entry['entry_date']) ? $GLOBALS['phpgw']->common->show_date($entry['entry_date'], $dateformat) : '');

					$values[] = array
						(
						'id' => $entry['id'],
						'amount' => $entry['amount'],
						'descr' => $entry['descr'],
						'entry_date' => $entry_date,
						'link_edit' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.edit_deviation',
							'workorder_id' => $workorder_id, 'hour_id' => $hour_id, 'id' => $entry['id'])),
						'lang_edit_statustext' => lang('edit the deviation'),
						'text_edit' => lang('edit'),
						'link_delete' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.delete',
							'workorder_id' => $workorder_id, 'hour_id' => $hour_id, 'deviation_id' => $entry['id'])),
						'lang_delete_statustext' => lang('delete the deviation'),
						'text_delete' => lang('delete')
					);
				}
			}

			$result_data = array('results' => $values);
			$result_data['total_records'] = count($values);
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function edit_deviation()
		{
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$hour_id = phpgw::get_var('hour_id', 'int');
			$id = phpgw::get_var('id', 'int');
			$values = phpgw::get_var('values');
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			if ($values['save'])
			{
				$values['workorder_id'] = $workorder_id;
				$values['hour_id'] = $hour_id;
				if (!$values['amount'])
				{
					$receipt['error'][] = array('msg' => lang('amount not entered!'));
				}

				if ($id)
				{
					$values['id'] = $id;
					$action = 'edit';
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_deviation($values, $action);
					if (!$id)
					{
						$id = $receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Status has NOT been saved'));
				}
			}

			if ($id)
			{
				$values = $this->bo->read_single_deviation(array('workorder_id' => $workorder_id,
					'hour_id' => $hour_id, 'id' => $id));
				$function_msg = lang('edit deviation');
				$action = 'edit';
			}
			else
			{
				$function_msg = lang('add deviation');
				$action = 'add';
			}

			$link_data = array
				(
				'menuaction' => 'property.uiwo_hour.edit_deviation',
				'entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id,
				'id' => $id
			);

			$hour = $this->bo->read_single_hour($hour_id);

			//_debug_array($workorder);
			//_debug_array($hour);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$entry_date = (isset($values['entry_date']) ? $GLOBALS['phpgw']->common->show_date($values['entry_date'], $dateformat) : '');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$data = array
				(
				'lang_workorder' => lang('Workorder ID'),
				'lang_hour_id' => lang('Post'),
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id, 'hour_id' => $hour_id)),
				'lang_id' => lang('deviation ID'),
				'lang_descr' => lang('Descr'),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_date' => lang('date'),
				'value_id' => $id,
				'value_workorder_id' => $workorder_id,
				'value_hour_id' => $hour_id,
				'entry_date' => $entry_date,
				'value_id' => $id,
				'lang_descr_standardtext' => lang('Enter a description of the deviation'),
				'lang_done_standardtext' => lang('Back to the list'),
				'lang_save_standardtext' => lang('Save the deviation'),
				'lang_amount' => lang('amount'),
				'value_amount' => $values['amount'],
				'value_descr' => $values['descr'],
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			$appname = lang('workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl(array('wo_hour'), array('edit_deviation' => $data));
		}

		function common_data( $workorder_id, $view = '' )
		{

			$workorder = $this->boworkorder->read_single($workorder_id);
			$hour_list = $this->bo->read($workorder_id);
			//_debug_array($hour_list);
			$grouping_descr_old = '';
			$content = array();

			if (isset($hour_list) AND is_array($hour_list))
			{
				foreach ($hour_list as $hour)
				{
					$sum_hour = $sum_hour + $hour['cost'];
					$sum_deviation = $sum_deviation + $hour['deviation'];

					if ($hour['grouping_descr'] != $grouping_descr_old)
					{
						$new_grouping = true;
					}
					else
					{
						$new_grouping = false;
					}

					$grouping_descr_old = $hour['grouping_descr'];

					if ($hour['activity_num'])
					{
						$code = $hour['activity_num'];
					}
					else
					{
						$code = str_replace("-", $hour['tolerance'], $hour['ns3420_id']);
					}

					if ($hour['count_deviation'] || $view)
					{
						$deviation = $hour['deviation'];
					}
					else
					{
						$deviation = lang('edit');
					}

					$content[] = array
						(
						'post' => sprintf("%02s", $workorder['chapter_id']) . '.' . sprintf("%02s", $hour['building_part']) . '.' . sprintf("%02s", $hour['grouping_id']) . '.' . sprintf("%03s", $hour['record']),
						'hour_id' => $hour['hour_id'],
						'activity_num' => $hour['activity_num'],
						'hours_descr' => $hour['hours_descr'],
						'activity_descr' => $hour['activity_descr'],
						'new_grouping' => $new_grouping,
						'grouping_id' => $hour['grouping_id'],
						'grouping_descr' => $hour['grouping_descr'],
						'ns3420_id' => $hour['ns3420_id'],
						'code' => $code,
						'remark' => $hour['remark'],
						'building_part' => $hour['building_part'],
						'quantity' => $hour['quantity'],
						'cost' => $hour['cost'],
						'unit' => $hour['unit'],
						'unit_name' => $hour['unit_name'],
						'billperae' => $hour['billperae'],
						'deviation' => $deviation,
						'result' => ($hour['deviation'] + $hour['cost']),
						'wo_hour_category' => $hour['wo_hour_category'],
						'cat_per_cent' => $hour['cat_per_cent'],
						'link_deviation' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.deviation',
							'workorder_id' => $workorder_id, 'hour_id' => $hour['hour_id'])),
						'link_edit' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.edit',
							'workorder_id' => $workorder_id, 'hour_id' => $hour['hour_id'])),
						'lang_edit_statustext' => lang('edit/customise this hour'),
						'lang_delete_statustext' => lang('delete this hour'),
						'text_edit' => lang('edit'),
						'text_delete' => lang('delete')
					);
				}
			}

			$this->bo->update_deviation(array('workorder_id' => $workorder_id, 'sum_deviation' => $sum_deviation));

			//_debug_array($content);

			$table_header[] = array
				(
				'lang_post' => lang('Post'),
				'lang_code' => lang('Code'),
				'lang_descr' => lang('descr'),
				'lang_unit' => lang('Unit'),
				'lang_billperae' => lang('Bill per unit'),
				'lang_quantity' => lang('Quantity'),
				'lang_cost' => lang('cost'),
				'lang_deviation ' => lang('deviation'),
				'lang_result' => lang('result'),
				'lang_view' => lang('view'),
				'lang_edit' => lang('edit'),
				'lang_delete' => lang('delete'),
				'lang_category' => lang('category'),
				'lang_per_cent' => lang('percent'),
			);

			$tax = $this->config->config_data['fm_tax'];

			$sum_result = $sum_hour + $sum_deviation;

			$addition_percentage = $sum_result * $workorder['addition_percentage'] / 100;
			$sum_tax = ($sum_result + $addition_percentage + $workorder['addition_rs']) * $tax / 100;
			$total_sum = $sum_result + $addition_percentage + $workorder['addition_rs'] + $sum_tax;

			$this->bo->update_calculation(array('workorder_id' => $workorder_id, 'calculation' => ($sum_result + $addition_percentage + $workorder['addition_rs'])));

			$table_sum[] = array
				(
				'lang_sum_calculation' => lang('Sum calculation'),
				'value_sum_calculation' => number_format($sum_hour, 2, ',', ''),
				'lang_addition_rs' => lang('Rig addition'),
				'value_addition_rs' => number_format($workorder['addition_rs'], 2, ',', ''),
				'lang_addition_percentage' => lang('Percentage addition'),
				'value_addition_percentage' => number_format($addition_percentage, 2, ',', ''),
				'lang_sum_tax' => lang('Sum tax'),
				'value_sum_tax' => number_format($sum_tax, 2, ',', ''),
				'lang_total_sum' => lang('Total sum'),
				'value_total_sum' => number_format($total_sum, 2, ',', ''),
				'lang_sum_deviation' => lang('Sum deviation'),
				'sum_deviation' => number_format($sum_deviation, 2, ',', ''),
				'sum_result' => number_format($sum_result, 2, ',', '')
			);

			$workorder_data = array
				(
				'link_workorder' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiworkorder.edit',
					'id' => $workorder_id)),
				'lang_vendor_name' => lang('Vendor'),
				'vendor_name' => $workorder['vendor_name'],
				'vendor_email' => $workorder['vendor_email'],
				'descr' => htmlentities($workorder['descr']),
				'lang_workorder_id' => lang('Workorder ID'),
				'workorder_id' => $workorder['workorder_id'],
				'lang_project_id' => lang('Project ID'),
				'link_project' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiproject.edit',
					'id' => $workorder['project_id'])),
				'project_id' => $workorder['project_id'],
				'lang_workorder_title' => lang('Workorder title'),
				'workorder_title' => $workorder['title']
			);



			$common_data = array
				(
				'content' => $content,
				'total_hours_records' => count($content),
				'table_header' => $table_header,
				'table_sum' => $table_sum,
				'workorder' => $workorder,
				'workorder_data' => $workorder_data,
			);

			return $common_data;
		}

		function save_template()
		{
			$values = phpgw::get_var('values');
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint

			if ($values['name'])
			{
				$receipt = $this->bo->add_template($values, $workorder_id);
			}

			$common_data = $this->common_data($workorder_id);

			$link_data = array
				(
				'menuaction' => 'property.uiwo_hour.index',
				'workorder_id' => $workorder_id
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			for ($i = 0; $i < count($common_data['content']); $i++)
			{
				if ($common_data['content'][$i]['remark'] != "")
				{
					if (trim($common_data['content'][$i]["hours_descr"]) == "")
					{
						$conector = "";
					}
					else
					{
						$conector = "<br>";
					}
					$extra = $common_data['content'][$i]["hours_descr"] . $conector . $common_data['content'][$i]["remark"];
				}
				else
				{
					$extra = $common_data['content'][$i]["hours_descr"];
				}
				$common_data['content'][$i]['extra_hours_descr'] = $extra;
			}

			$column_def = array
				(
				array('key' => 'post', 'label' => $common_data['table_header'][0]['lang_post'],
					'sortable' => true),
				array('key' => 'code', 'label' => $common_data['table_header'][0]['lang_code'],
					'sortable' => true),
				array('key' => 'extra_hours_descr', 'label' => $common_data['table_header'][0]['lang_descr'],
					'sortable' => true),
				array('key' => 'unit', 'label' => $common_data['table_header'][0]['lang_unit'],
					'sortable' => true),
				array('key' => 'quantity', 'label' => $common_data['table_header'][0]['lang_quantity'],
					'sortable' => true, 'className' => 'right'),
				array('key' => 'billperae', 'label' => $common_data['table_header'][0]['lang_billperae'],
					'sortable' => true, 'className' => 'right'),
				array('key' => 'cost', 'label' => $common_data['table_header'][0]['lang_cost'],
					'sortable' => true, 'className' => 'right'),
				array('key' => 'result', 'label' => $common_data['table_header'][0]['lang_result'],
					'sortable' => true, 'className' => 'right'),
				array('key' => 'wo_hour_category', 'label' => $common_data['table_header'][0]['lang_category'],
					'sortable' => true),
				array('key' => 'cat_per_cent', 'label' => $common_data['table_header'][0]['lang_per_cent'],
					'sortable' => true, 'className' => 'center')
			);

			$datatable_def = array();
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'data' => json_encode($common_data['content']),
				'ColumnDefs' => $column_def,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$data = array
				(
				'datatable_def' => $datatable_def,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'add_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.save_template',
					'workorder_id' => $workorder_id)),
				'lang_done_statustext' => lang('Back to the workorder list'),
				'lang_add_statustext' => lang('Adds this workorders calculation as a template for later use'),
				'lang_search_statustext' => lang('Adds a new workorder to an existing project'),
				'lang_done' => lang('Done'),
				'lang_add' => lang('Add'),
				'lang_search' => lang('Search'),
				'lang_name' => lang('name'),
				'lang_name_statustext' => lang('Enter the name the template'),
				'lang_descr' => lang('Description'),
				'lang_descr_statustext' => lang('Enter a short description of this template'),
				//'total_hours_records'		=> $common_data['total_hours_records'],
				//'lang_total_records'		=> lang('Total records'),
				//'table_header_hour'		=> $common_data['table_header'],
				//'values_hour'				=> $common_data['content'],
				'workorder_data' => $common_data['workorder_data'],
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date',
					'security', 'file'))
			);

			$appname = lang('Workorder');
			$function_msg = lang('Add template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl(array('wo_hour', 'datatable_inline'), array('add_template' => $data));
		}

		function index()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint

			if ($delete && $hour_id)
			{
				$receipt = $this->bo->delete($hour_id, $workorder_id);
				return "hour_id " . $hour_id . " " . lang("has been deleted");
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			phpgwapi_jquery::load_widget('numberformat');

			$appname = lang('Workorder');
			$function_msg = lang('list hours');

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(),
				'datatable' => array
					(
					'source' => self::link(array(
						'menuaction' => 'property.uiwo_hour.index',
						'workorder_id' => $workorder_id,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				),
				'top-toolbar' => array
					(
					'fields' => array
						(
						'field' => array
							(
							array
								(// mensaje
								'type' => 'label',
								'id' => 'msg_header',
								'value' => '',
								'style' => 'filter'
							),
							array
								(
								'type' => 'button',
								'id' => 'btn_save_template',
								'tab_index' => 4,
								'value' => lang('Save as template'),
								'href' => self::link(array
									(
									'menuaction' => 'property.uiwo_hour.save_template',
									'from' => 'index',
									'workorder_id' => $workorder_id
								))
							),
							array
								(
								'type' => 'button',
								'id' => 'btn_add_custom',
								'tab_index' => 3,
								'value' => lang('Add custom'),
								'href' => self::link(array
									(
									'menuaction' => 'property.uiwo_hour.edit',
									'from' => 'index',
									'workorder_id' => $workorder_id
								))
							),
							array
								(
								'type' => 'button',
								'id' => 'btn_add_template',
								'tab_index' => 2,
								'value' => lang('Add from template'),
								'href' => self::link(array
									(
									'menuaction' => 'property.uitemplate.index',
									'lookup' => true,
									'workorder_id' => $workorder_id
								))
							),
							array
								(
								'type' => 'button',
								'id' => 'btn_add_prizebook',
								'tab_index' => 1,
								'value' => lang('Add from prizebook'),
								'href' => self::link(array
									(
									'menuaction' => 'property.uiwo_hour.prizebook',
									'workorder_id' => $workorder_id
								))
							),
							array
								(
								'type' => 'button',
								'id' => 'btn_import_calculation',
								'tab_index' => 1,
								'value' => lang('import calculation'),
								'href' => self::link(array
									(
									'menuaction' => 'property.uiwo_hour.import_calculation',
									'workorder_id' => $workorder_id
								))
							)
						)
					)
				),
				'end-toolbar' => array
					(
					'fields' => array
						(
						'field' => array
							(
							array
								(
								'type' => 'button',
								'id' => 'btn_print_preview',
								'value' => lang('Print view'),
								'tab_index' => 5,
								'style' => 'filter',
								'group' => '1',
								'url' => self::link(array
									(
									'menuaction' => 'property.uiwo_hour.view',
									'from' => 'index',
									'workorder_id' => $workorder_id
								)),
								'params' => json_encode(array(
									array('obj' => 'check_calculated_cost', 'param' => 'show_cost'),
									array('obj' => 'check_show_details', 'param' => 'show_details')
								))
							),
							array
								(// check label
								'type' => 'label',
								'id' => 'lbl_check_details',
								'value' => lang('Show details'),
								'style' => 'filter',
								'group' => '1'
							),
							array
								(
								'id' => 'check_show_details',
								'value' => 0,
								'type' => 'checkbox',
								'checked' => 1,
								'tab_index' => 6,
								'style' => 'filter',
								'group' => '1'
							),
							array
								(// check label
								'type' => 'label',
								'id' => 'lbl_check_cost',
								'value' => lang('Show calculated cost'),
								'style' => 'filter',
								'group' => '1'
							),
							array
								(
								'id' => 'check_calculated_cost',
								'value' => 0,
								'type' => 'checkbox',
								'tab_index' => 7,
								'style' => 'filter',
								'group' => '1'
							),
							array
								(
								'type' => 'button',
								'id' => 'btn_view_tender',
								'value' => lang('View tender'),
								'tab_index' => 8,
								'group' => '2',
								'url' => self::link(array
									(
									'menuaction' => 'property.uiwo_hour.tender',
									'from' => 'index',
									'workorder_id' => $workorder_id
								)),
								'params' => json_encode(array(
									array('obj' => 'check_calculated_cost_tender', 'param' => 'show_cost'),
									array('obj' => 'check_mark_draft', 'param' => 'mark_draft')
								))
							),
							array
								(// check label
								'type' => 'label',
								'id' => 'lbl_check_cost_tender',
								'value' => lang('Show calculated cost'),
								'group' => '2'
							),
							array
								(
								'id' => 'check_calculated_cost_tender',
								'value' => 0,
								'type' => 'checkbox',
								'tab_index' => 9,
								'group' => '2'
							),
							array
								(// check label
								'type' => 'label',
								'id' => 'lbl_check_mark',
								'value' => lang('Mark as DRAFT'),
								'group' => '2'
							),
							array
								(
								'id' => 'check_mark_draft',
								'value' => 0,
								'type' => 'checkbox',
								'tab_index' => 10,
								'group' => '2'
							)
						)
					)
				)
			);

			$uicols = array(
				'name' => array('hour_id', 'post', 'code', 'hours_descr', 'unit_name', 'billperae',
					'quantity', 'cost', 'deviation', 'result', 'wo_hour_category', 'cat_per_cent'),
				'input_type' => array('hidden', 'text', 'text', 'text', 'text', 'text', 'text',
					'text', 'text', 'text', 'text', 'text'),
				'descr' => array('', lang('Post'), lang('Code'), lang('Descr'), lang('Unit'),
					lang('Bill per unit'), lang('Quantity'), lang('Cost'), lang('deviation'), lang('result'),
					lang('Category'), lang('percent')),
				'className' => array('', '', '', '', '', 'dt-right', 'dt-right', 'dt-right',
					'dt-right', 'dt-right', '', 'dt-right')
			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'className' => $uicols['className'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);
				switch ($uicols['name'][$k])
				{
					case 'billperae':
					case 'cost':
					case 'deviation':
					case 'result':
					case 'quantity':
						$params['formatter'] = 'JqueryPortico.FormatterAmount2';
						break;
				}

				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'hour_id',
						'source' => 'hour_id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'deviation',
				'text' => lang('Deviation'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'deviation',
				'text' => lang('open deviation in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('Edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.edit',
					'workorder_id' => $workorder_id,
					'from' => 'index'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('open edit in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.edit',
					'workorder_id' => $workorder_id,
					'from' => 'index'
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'delete',
				'text' => lang('Delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.index',
					'workorder_id' => $workorder_id,
					'delete' => true
				)),
				'parameters' => json_encode($parameters)
			);

			unset($parameters);

			$common_data = $this->common_data($workorder_id);

			$data['datatable']['table_sum'] = $common_data['table_sum'][0];
			$data['datatable']['workorder_data'] = $common_data['workorder_data'];

			self::add_javascript('property', 'portico', 'wo_hour.index.js');
			self::render_template_xsl(array('wo_hour.index', 'datatable_inline'), $data);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
		}

		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			$workorder_id = phpgw::get_var('workorder_id');
			$draw = phpgw::get_var('draw', 'int');

			$uicols = array(
				'name' => array('hour_id', 'post', 'code', 'hours_descr', 'unit_name', 'billperae',
					'quantity', 'cost', 'deviation', 'result', 'wo_hour_category', 'cat_per_cent'),
				'input_type' => array('hidden', 'text', 'text', 'text', 'text', 'text', 'text',
					'text', 'text', 'text', 'text', 'text'),
				'descr' => array('', lang('Post'), lang('Code'), lang('Descr'), lang('Unit'),
					lang('Bill per unit'), lang('Quantity'), lang('Cost'), lang('deviation'), lang('result'),
					lang('Category'), lang('percent')),
				'className' => array('', '', '', '', '', 'dt-right', 'dt-right', 'dt-right',
					'dt-right', 'dt-right', '', 'dt-right')
			);

			$common_data = $this->common_data($workorder_id);
			$content = $common_data['content'];
			$values = array();
			$k = 0;
			foreach ($content as $row)
			{
				foreach ($uicols['name'] as $name)
				{
					if ($name == 'deviation')
					{
						if (is_numeric($row[$name]))
						{
							$values[$k][$name] = $row[$name];
						}
						else
						{
							$values[$k][$name] = '';
						}
					}
					else
					{
						$values[$k][$name] = $row[$name];
					}
				}
				$k ++;
			}

			$result_data = array('results' => $values);
			$result_data['table_sum'] = $common_data['table_sum'][0];
			$result_data['workorder_data'] = $common_data['workorder_data'];
			$result_data['total_records'] = $common_data['total_hours_records'];
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function view()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			if (phpgw::get_var('done', 'bool'))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiwo_hour.index',
					'workorder_id' => $workorder_id));
			}

			$show_cost = phpgw::get_var('show_cost', 'bool');
			$show_details = true;//phpgw::get_var('show_details', 'bool');
			$to_email = phpgw::get_var('to_email', 'string');
			$update_email = phpgw::get_var('update_email', 'bool');
			$send_order = phpgw::get_var('send_order', 'bool');
			$no_email = phpgw::get_var('no_email', 'bool');
			$values = phpgw::get_var('values');
			$print = phpgw::get_var('print', 'bool');
			$sent_ok = phpgw::get_var('print', 'bool');
			$send_as_pdf = phpgw::get_var('send_as_pdf', 'bool');
			$email_receipt = phpgw::get_var('email_receipt', 'bool');

			/*
			  if($update_email)
			  {
			  $this->bo->update_email($to_email,$workorder_id);
			  }
			 */

			$sms_client_order_notice = isset($this->config->config_data['sms_client_order_notice']) ? $this->config->config_data['sms_client_order_notice'] : '';

			if ($sms_client_order_notice)
			{
				$sms_client_order_notice = str_replace(array('__order_id__'), array($workorder_id), $sms_client_order_notice);
			}

			$workorder = $this->boworkorder->read_single($workorder_id);
			$workorder_history = $this->boworkorder->read_record_history($workorder_id);

			$table_header_history[] = array
				(
				'lang_date' => lang('Date'),
				'lang_user' => lang('User'),
				'lang_action' => lang('Action'),
				'lang_new_value' => lang('New value')
			);


			$common_data = $this->common_data($workorder_id);
			if ($show_details)
			{
				$values_hour = $common_data['content'];
			}

			$project = $this->boproject->read_single($common_data['workorder']['project_id'], array(), true);

			$bolocation = CreateObject('property.bolocation');

			$location_data = $bolocation->initiate_ui_location(array(
				'values' => $workorder['location_data'] ? $workorder['location_data'] : $project['location_data'],
				'type_id' => $workorder['location_data'] ? count(explode('-', $workorder['location_data']['location_code'])) : count(explode('-', $project['location_data']['location_code'])),
				'no_link' => false, // disable lookup links for location type less than type_id
				'tenant' => $workorder['location_data']['tenant_id'] ? $workorder['location_data']['tenant_id'] : $project['location_data']['tenant_id'],
				'lookup_type' => 'view'
			));

			if ($project['contact_phone'])
			{
				for ($i = 0; $i < count($location_data['location']); $i++)
				{
					if ($location_data['location'][$i]['input_name'] == 'contact_phone')
					{
						unset($location_data['location'][$i]['value']);
					}
				}
			}

			if (!$show_cost)
			{
				$m = count($values_hour);
				for ($i = 0; $i < $m; $i++)
				{
					unset($values_hour[$i]['cost']);
					unset($values_hour[$i]['billperae']);
				}
				unset($common_data['table_sum'][0]['value_total_sum']);
			}

			$table_header[] = array
				(
				'lang_post' => lang('Post'),
				'lang_code' => lang('Code'),
				'lang_descr' => lang('descr'),
				'lang_unit' => lang('Unit'),
				'lang_billperae' => lang('Bill per unit'),
				'lang_quantity' => lang('Quantity'),
				'lang_cost' => lang('cost')
			);


			if (!$print && !$no_email)
			{
				$table_send[] = array
					(
					'lang_send_order' => lang('Send Order'),
					'lang_send_order_statustext' => lang('Send this order by email')
				);

				$table_done[] = array
					(
					'lang_done' => lang('Done'),
					'lang_done_statustext' => lang('Back to calculation'),
					'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.index',
						'workorder_id' => $workorder_id))
				);
			}

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date(time(), $dateformat);


			$GLOBALS['phpgw']->preferences->set_account_id($workorder['user_id'], true);

			$from_name = $GLOBALS['phpgw']->accounts->get($workorder['user_id'])->__toString();
			$from_email = "{$from_name}<{$GLOBALS['phpgw']->preferences->data['property']['email']}>";

			if ($this->config->config_data['wo_status_sms'])
			{
				$sms_location_id = $GLOBALS['phpgw']->locations->get_id('sms', 'run');
				$config_sms = CreateObject('admin.soconfig', $sms_location_id);

				$sms_data['heading'] = lang('Send the following SMS-message to %1 to update status for this order:', $config_sms->config_data['common']['gateway_number']);
				$sms_data['message'] = 'status ' . $workorder_id . ' [' . lang('status code') . ']';
				$sms_data['status_code'][0]['name'] = '1 => ' . lang('performed');
				$sms_data['status_code'][1]['name'] = '2 => ' . lang('No access');
				$sms_data['status_code'][2]['name'] = '3 => ' . 'I arbeid';
				$sms_data['status_code_text'] = lang('status code');
				$sms_data['example'] = 'status ' . $workorder_id . ' 1';
				$sms_data['lang_example'] = lang('Example');

				phpgw::import_class('phpgwapi.phpqrcode');
				$code_text = "SMSTO:{$config_sms->config_data['common']['gateway_number']}: STATUS {$workorder_id} ";
				$filename = $GLOBALS['phpgw_info']['server']['temp_dir'] . '/' . md5($code_text) . '.png';
				QRcode::png($code_text, $filename);
				$sms_data['encoded_text'] = 'data:image/png;base64,' . base64_encode(file_get_contents($filename));
			}
			$lang_reminder = '';
			if ($this->boworkorder->order_sent_adress || $sent_ok)
			{
				$lang_reminder = lang('reminder');
			}

			$contact_data = $this->bocommon->initiate_ui_contact_lookup(array(
				'contact_id' => $project['contact_id'],
				'field' => 'contact',
				'type' => 'view'));


			$location_code = isset($common_data['workorder']['location_code']) && $common_data['workorder']['location_code'] ? $common_data['workorder']['location_code'] : $project['location_code'];

			$formatted_gab_id = $this->get_gab_id($location_code);

			$location_exceptions = createObject('property.solocation')->get_location_exception($location_code, $alert_vendor = true);

			$important_imformation = '';
			if($location_exceptions)
			{
				$important_imformation .= "<h4>" . lang('important information') . '</h4>';
				$important_imformation_arr = array();
				foreach ($location_exceptions as $location_exception)
				{
					$important_imformation_arr[] = $location_exception['category_text'];

					if($location_exception['location_descr'])
					{
						$important_imformation_arr[] = $location_exception['location_descr'];
					}
				}
				$important_imformation .= implode("<br/>", $important_imformation_arr);
			}

			$contract_list = $this->bocommon->get_vendor_contract($workorder['vendor_id'], $workorder['contract_id']);
			foreach ($contract_list as $contract)
			{
				if($contract['selected'])
				{
					$contract_name = $contract['name'];
					break;
				}
			}

			$email_data = array
			(
				'contract_name' => $contract_name,
				'formatted_gab_id' => $formatted_gab_id,
				'org_name' => isset($this->config->config_data['org_name']) ? "{$this->config->config_data['org_name']}::" : '',
				'location_data_local' => $location_data,
				'lang_workorder' => lang('Workorder ID'),
				'workorder_id' => $workorder_id,
				'lang_reminder' => $lang_reminder,
				'lang_date' => lang('Date'),
				'date' => $date,
				'lang_start_date' => lang('Start date'),
				'start_date' => $workorder['start_date'],
				'lang_end_date' => lang('End date'),
				'end_date' => $workorder['end_date'],
				'lang_from' => lang('From'),
				'from_name' => $from_name,
				'from_email' => $from_email,
				'from_phone' => $GLOBALS['phpgw']->preferences->data['property']['cellphone'],
				'lang_district' => lang('District'),
				'district' => $project['location_data']['district_id'],
				'ressursnr' => isset($GLOBALS['phpgw']->preferences->data['property']['ressursnr']) ? 'Brukes ikke for denne ordren' : '',
				'lang_to' => lang('To'),
				'to_name' => $workorder['vendor_name'],
				'lang_title' => lang('Title'),
				'title' => $workorder['title'],
				'lang_descr' => lang('Description'),
				'descr' => $workorder['descr'] . $important_imformation,
				'lang_budget_account' => lang('Budget account'),
				'budget_account' => $workorder['b_account_id'],
				'lang_sum_calculation' => lang('Sum of calculation'),
				'sum_calculation' => $common_data['table_sum'][0]['value_total_sum'],
				'lang_contact_phone' => lang('Contact phone'),
				'contact_phone' => $project['contact_phone'],
				'lang_branch' => lang('branch'),
				'branch_list' => $this->boproject->select_branch_p_list($project['project_id']),
				'other_branch' => $project['other_branch'],
				'key_responsible_list' => $this->boproject->select_branch_list($project['key_responsible']),
				'lang_key_responsible' => lang('key responsible'),
				'key_fetch_list' => $this->boproject->select_key_location_list($workorder['key_fetch']),
				'lang_key_fetch' => lang('Where to pick up the key'),
				'key_deliver_list' => $this->boproject->select_key_location_list($workorder['key_deliver']),
				'lang_key_deliver' => lang('Where to deliver the key'),
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'lang_cost_tax' => lang('Cost (incl tax):'),
				'lang_materials' => lang('Materials:__________'),
				'lang_work' => lang('work:____________'),
				'table_header_view_order' => $table_header,
				'values_view_order' => $values_hour,
				'sms_data' => $sms_data,
				'use_yui_table' => true,
				'contact_data' => $contact_data,
				'order_footer_header' => $this->config->config_data['order_footer_header'],
				'order_footer' => $this->config->config_data['order_footer']
			);

			if ($send_order && !$to_email && !$workorder['mail_recipients'])
			{
				$receipt['error'][] = array('msg' => lang('No mailaddress is selected'));
			}

			if ($to_email || $print || ($workorder['mail_recipients'][0] && $_POST['send_order']))
			{
				if (isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
				{
					$approve_role = execMethod('property.boinvoice.check_role', $project['ecodimb'] ? $project['ecodimb'] : $workorder['ecodimb']);

					$_ok = false;
					if ($approve_role['is_supervisor'])
					{
						$_ok = true;
					}
					else if ($approve_role['is_budget_responsible'])
					{
						$_ok = true;
					}
					else if ($workorder['approved'])
					{
						$_ok = true;
					}
					else
					{
						$_ok = $this->_validate_purchase_grant($workorder_id, $project['ecodimb'] ? $project['ecodimb'] : $workorder['ecodimb'], $project['id']);
					}

					if (!$_ok)
					{
						phpgwapi_cache::message_set(lang('order is not approved'), 'error');
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiwo_hour.view',
							'workorder_id' => $workorder_id, 'from' => phpgw::get_var('from')));
					}
					unset($_ok);
				}
				else
				{
					if(!$this->_validate_purchase_grant($workorder_id, $project['ecodimb'] ? $project['ecodimb'] : $workorder['ecodimb'], $project['id']))
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiwo_hour.view',
							'workorder_id' => $workorder_id, 'from' => phpgw::get_var('from')));
					}
				}

				$criteria = array
					(
					'appname' => 'property',
					'location' => '.project.workorder.transfer',
					'allrows' => true
				);

				$transfer_action = 'workorder'; // trigger for transfer

				$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

				foreach ($custom_functions as $entry)
				{
					// prevent path traversal
					if (preg_match('/\.\./', $entry['file_name']))
					{
						continue;
					}

					$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
					if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
					{
						require $file;
					}
				}

				$_to = isset($workorder['mail_recipients'][0]) && $workorder['mail_recipients'][0] ? implode(';', $workorder['mail_recipients']) : $to_email;
				$email_data['use_yui_table'] = false;

				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/wo_hour'));
				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/location_view'));
				$this->create_html->add_file(array(PHPGW_SERVER_ROOT . '/property/templates/base/contact_view'));

				$this->create_html->set_var('phpgw', array('email_data' => $email_data));

				$email_data['use_yui_table'] = true;

				$this->create_html->set_output('html');
				$this->create_html->xsl_parse();
				$this->create_html->xml_parse();

				$xml = new DOMDocument;
				$xml->loadXML($this->create_html->xmldata);
				$xsl = new DOMDocument;
				$xsl->loadXML($this->create_html->xsldata);

				// Configure the transformer
				$proc = new XSLTProcessor;
				$proc->registerPHPFunctions(); // enable php functions
				$proc->importStyleSheet($xsl); // attach the xsl rules
				$css = file_get_contents(PHPGW_SERVER_ROOT . "/phpgwapi/templates/pure/css/pure-min.css");
				$css .= file_get_contents(PHPGW_SERVER_ROOT . "/phpgwapi/templates/pure/css/pure-extension.css");

				$header = <<<HTML
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<style TYPE="text/css">
			<!--{$css}-->
		</style>
	</head>
		<body>
			<div class="pure-form pure-form-aligned">
HTML;

				$footer = <<<HTML
			</div>
	</body>
</html>
HTML;

				$html = trim($proc->transformToXML($xml));
				$html = preg_replace('/<\?xml version([^>])+>/', '', $html);
				$html = preg_replace('/<!DOCTYPE([^>])+>/', '', $html);

				if ($print)
				{
					echo $header;
					echo <<<HTML
						<script language="Javascript1.2">
						<!--
							document.write("<form><input type=button "
							+"value=\"Print Page\" onClick=\"window.print();\"></form>");
						//-->
						</script>
HTML;

					echo $html;
					echo $footer;
					exit;
				}

				if ($GLOBALS['phpgw']->preferences->data['property']['order_email_rcpt'] == 1)
				{
					$bcc = $from_email;
				}

				$subject = lang('Workorder') . ": " . $workorder_id;

				$address_element = execMethod('property.botts.get_address_element', $location_code);
				$_address = array();
				foreach ($address_element as $entry)
				{
					$_address[] = "{$entry['text']}: {$entry['value']}";
				}

				if ($_address)
				{
					$_address_txt = $_address ? implode(', ', $_address) : '';
				}
				unset($_address);
				unset($address_element);

				$attachments = array();
				$attachment_log = '';
				if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
				{
					if (isset($values['file_action']) && is_array($values['file_action']))
					{
						$bofiles = CreateObject('property.bofiles');
						$attachments = $bofiles->get_attachments($values['file_action']['project']);
						$attachments = array_merge($attachments, $bofiles->get_attachments($values['file_action']['workorder']));
						$_attachment_log = array();
						foreach ($attachments as $_attachment)
						{
							$_attachment_log[] = $_attachment['name'];
						}

						$attachment_log = lang('attachments') . ': ' . implode(', ', $_attachment_log);
					}

					if ($send_as_pdf)
					{
						$pdfcode = $this->pdf_order($workorder_id, $show_cost);
						if ($pdfcode)
						{
							$dir = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/pdf_files";

							//save the file
							if (!file_exists($dir))
							{
								mkdir($dir, 0777);
							}
							$fname = tempnam($dir . '/', 'PDF_') . '.pdf';
							$fp = fopen($fname, 'w');
							fwrite($fp, $pdfcode);
							fclose($fp);

							$attachments[] = array
								(
								'file' => $fname,
								'name' => "order_{$workorder_id}.pdf",
								'type' => 'application/pdf'
							);
						}
						$body = lang('order') . " {$workorder_id}.</br></br>{$_address_txt}</br></br>" . lang('see attachment');
					}
					else
					{
						$body = $header . $html . $footer;
					}

					$_status = isset($this->config->config_data['workorder_ordered_status']) && $this->config->config_data['workorder_ordered_status'] ? $this->config->config_data['workorder_ordered_status'] : 0;

					if (!$_status)
					{
		//				throw new Exception('status on ordered not given in config');
						phpgwapi_cache::message_set("Automatisk endring av status for bestilt er ikke konfigurert", 'error');

					}

					if (!is_object($GLOBALS['phpgw']->send))
					{
						$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
					}

					try
					{
						$rcpt = $GLOBALS['phpgw']->send->msg('email', $_to, $subject, $body, '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments, $email_receipt);
					}
					catch (Exception $e)
					{
						if ($e)
						{
							phpgwapi_cache::message_set($e->getMessage(), 'error');
						}
					}

				}
				else
				{
					$receipt['error'][] = array('msg' => lang('SMTP server is not set! (admin section)'));
				}


				if ($rcpt)
				{
					$_attachment_log = $attachment_log ? "::$attachment_log" : '';
					$historylog = CreateObject('property.historylog', 'workorder');
					$historylog->add('M', $workorder_id, "{$_to}{$_attachment_log}");
					$receipt['message'][] = array('msg' => lang('Workorder %1 is sent by email!', $workorder_id));
					if ($attachment_log)
					{
						$receipt['message'][] = array('msg' => $attachment_log);
					}

					if (phpgw::get_var('notify_client_by_sms', 'bool') && $sms_client_order_notice && (isset($project['contact_phone']) && $project['contact_phone'] || phpgw::get_var('to_sms_phone')))
					{
						$to_sms_phone = phpgw::get_var('to_sms_phone');
						$to_sms_phone = $to_sms_phone ? $to_sms_phone : $project['contact_phone'];
						$project['contact_phone'] = $to_sms_phone;

						$sms = CreateObject('sms.sms');
						$sms->websend2pv($this->account, $to_sms_phone, str_replace(array('__order_id__'), array(
							$workorder_id), $this->config->config_data['sms_client_order_notice']));
						$historylog->add('MS', $workorder_id, $to_sms_phone);
					}

					if($_status)
					{
						try
						{
							execMethod('property.soworkorder.update_status', array('order_id' => $workorder_id,
								'status' => $_status));
						}
						catch (Exception $e)
						{
							if ($e)
							{
								throw $e;
							}
						}
					}

					//Sigurd: Consider remove
					/*
					  if( $this->boworkorder->order_sent_adress )
					  {
					  $action_params = array
					  (
					  'appname'			=> 'property',
					  'location'			=> '.project.workorder',
					  'id'				=> $workorder_id,
					  'responsible'		=> $workorder['vendor_id'],
					  'responsible_type'  => 'vendor',
					  'action'			=> 'remind',
					  'remark'			=> '',
					  'deadline'			=> ''
					  );

					  $reminds = execMethod('property.sopending_action.set_pending_action', $action_params);
					  }
					 */
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('The recipient did not get the email:'));
					$receipt['error'][] = array('msg' => lang('From') . ' ' . $from_email);
					$receipt['error'][] = array('msg' => lang('To') . ' ' . $_to);
				}
			}

			if ($this->boworkorder->order_sent_adress)
			{
				$to_email = $this->boworkorder->order_sent_adress;
			}

			$email_list = $this->bo->get_email($to_email, $workorder['vendor_id']);
			if (count($email_list) == 1)
			{
				$to_email = $email_list[0]['email'];
				unset($email_list);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$link_file_data = array
				(
				'menuaction' => 'property.uiworkorder.view_file',
				'id' => $workorder_id
			);

			//---datatable settings---------------------------------------------------			

			$table_view_order = array();
			if (count($email_data['values_view_order']))
			{

				for ($i = 0; $i < count($email_data['values_view_order']); $i++)
				{
					$table_view_order[$i]['post'] = $email_data['values_view_order'][$i]['post'];
					$table_view_order[$i]['code'] = $email_data['values_view_order'][$i]['code'];
					$table_view_order[$i]['descr'] = $email_data['values_view_order'][$i]['hours_descr'] . "<br>" . $email_data['values_view_order']['remark'];
					$table_view_order[$i]['unit'] = $email_data['values_view_order'][$i]['unit'];
					$table_view_order[$i]['unit_name'] = $email_data['values_view_order'][$i]['unit_name'];
					$table_view_order[$i]['quantity'] = $email_data['values_view_order'][$i]['quantity'];
					$table_view_order[$i]['billperae'] = $email_data['values_view_order'][$i]['billperae'];
					$table_view_order[$i]['cost'] = $email_data['values_view_order'][$i]['cost'];
				}
			}

			$datatable_def = array();

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => "''",
				'ColumnDefs' => array
					(
					array('key' => 'post', 'label' => lang('Post'), 'sortable' => true),
					array('key' => 'code', 'label' => lang('Code'), 'sortable' => true),
					array('key' => 'descr', 'label' => lang('descr'), 'sortable' => true),
					array('key' => 'unit_name', 'label' => lang('Unit'), 'sortable' => true),
					array('key' => 'quantity', 'label' => lang('Quantity'), 'sortable' => true),
					array('key' => 'billperae', 'label' => lang('Bill per unit'), 'sortable' => true),
					array('key' => 'cost', 'label' => lang('cost'), 'sortable' => true)
				),
				'data' => json_encode($table_view_order),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$workorder_history = $this->boworkorder->read_record_history($workorder_id); // second time...(after the order is sendt)

			$datatable_def[] = array
				(
				'container' => 'datatable-container_1',
				'requestUrl' => "''",
				'ColumnDefs' => array
					(
					array('key' => 'value_date', 'label' => lang('Date'), 'sortable' => true),
					array('key' => 'value_user', 'label' => lang('User'), 'sortable' => true),
					array('key' => 'value_action', 'label' => lang('Action'), 'sortable' => true),
					array('key' => 'value_new_value', 'label' => lang('New value'), 'sortable' => true)
				),
				'data' => json_encode($workorder_history),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$content_files = array();
			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$files = $workorder['files'] ? $workorder['files'] : array();
			$lang_view_file = lang('click to view file');
			$lang_select_file = lang('select');

			for ($z = 0; $z < count($files); $z++)
			{
				$content_files[$z]['file_name'] = "<a href='{$link_view_file}&amp;file_id={$files[$z]['file_id']}' target='_blank' title='{$lang_view_file}'>{$files[$z]['name']}</a>";
				$content_files[$z]['select_file'] = "<input type='checkbox' name='values[file_action][workorder][]' value='{$files[$z]['file_id']}' title='{$lang_select_file}'>";
			}

			$project_link_file_data = array
				(
				'menuaction' => 'property.uiproject.view_file',
				'id' => $project['project_id']
			);
			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $project_link_file_data);


			$files = $this->boproject->get_files($project['project_id']);

			$i = $z;
			for ($z = 0; $z < count($files); $z++)
			{
				$content_files[$i]['file_name'] = "<a href='{$link_view_file}&amp;file_id={$files[$z]['file_id']}' target='_blank' title='{$lang_view_file}'>{$files[$z]['name']}</a>";
				$content_files[$i]['select_file'] = "<input type='checkbox' name='values[file_action][project][]' value='{$files[$z]['file_id']}' title='{$lang_select_file}'>";
				$i ++;
			}

			$files_def = array
				(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
					'resizeable' => true),
				array('key' => 'select_file', 'label' => lang('select'), 'sortable' => false,
					'resizeable' => true)
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_2',
				'requestUrl' => "''",
				'data' => json_encode($content_files),
				'ColumnDefs' => $files_def,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_mail' => lang('E-Mail'),
				'lang_update_email' => lang('Update email'),
				'lang_update_email_statustext' => lang('Check to update the email-address for this vendor'),
				'value_sms_client_order_notice' => $sms_client_order_notice,
				'value_sms_phone' => $project['contact_phone'],
				'lang_to_email_address_statustext' => lang('The address to which this order will be sendt'),
				'to_email' => $to_email,
				'email_list' => $email_list,
				'requst_email_receipt' => isset($GLOBALS['phpgw']->preferences->data['request_order_email_rcpt']) && $GLOBALS['phpgw']->preferences->data['property']['request_order_email_rcpt'] == 1 ? 1 : 0,
				'lang_select_email' => lang('Select email'),
				'send_order_action' => $GLOBALS['phpgw']->link('/index.php', array(
					'menuaction' => 'property.uiwo_hour.view',
					'send' => true,
					'workorder_id' => $workorder_id,
					'show_details' => $show_details,
					'sent_ok' => $rcpt)),
				'lang_no_history' => lang('No history'),
				'lang_history' => lang('History'),
				'workorder_history' => $workorder_history,
				'table_header_history' => $table_header_history,
				'email_data' => $email_data,
				'no_email' => $no_email,
				'table_send' => $table_send,
				'table_done' => $table_done,
				'link_view_file' => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				'files' => $content_files,
				'lang_files' => lang('files'),
				'lang_filename' => lang('Filename'),
				'lang_file_action' => lang('attach file'),
				'lang_view_file_statustext' => lang('click to view file'),
				'lang_file_action_statustext' => lang('Check to attach file'),
				'lang_print' => lang('print'),
				'value_show_cost' => $show_cost,
				'lang_print_statustext' => lang('open this page as printerfrendly'),
				'print_action' => "javascript:openwindow('"
				. $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.view',
					'workorder_id' => $workorder_id,
					'show_cost' => $show_cost,
					'show_details' => $show_details,
					'print' => true
				)) . "','1000','1200')",
				'pdf_action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.pdf_order',
					'workorder_id' => $workorder_id,
					'show_cost' => $show_cost,
					'show_details' => $show_details,
					'preview' => true)),
				'mail_recipients' => isset($workorder['mail_recipients']) && is_array($workorder['mail_recipients']) ? implode(';', $workorder['mail_recipients']) : ''
			);

			$appname = lang('Workorder');
			$function_msg = $this->boworkorder->order_sent_adress ? lang('ReSend order') : lang('Send order');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl(array('wo_hour', 'datatable_inline'), array(
				'view' => $data));
		}

		protected function _get_order_details( $values_hour, $show_cost = false )
		{
			$translations = array
				(
				'post' => lang('post'),
				'code' => lang('code'),
				'descr' => lang('descr'),
				'unit' => lang('unit'),
				'quantity' => lang('quantity'),
				'billperae' => lang('bill per unit'),
				'cost' => lang('cost')
			);

			$grouping_descr_old = '';
			$content = array();
			foreach ($values_hour as $hour)
			{
				$descr = $hour['hours_descr'];

				if ($hour['remark'])
				{
					$descr .= "\n" . $hour['remark'];
				}

				if (!$show_cost)
				{
					unset($hour['billperae']);
					unset($hour['cost']);
				}

				if ($hour['grouping_descr'] != $grouping_descr_old)
				{
					$content[] = array
						(
						$translations['post'] => $hour['grouping_descr'],
						$translations['code'] => '',
						$translations['descr'] => '',
						$translations['unit'] => '',
						$translations['quantity'] => '',
						$translations['billperae'] => '',
						$translations['cost'] => ''
					);
				}

				$grouping_descr_old = $hour['grouping_descr'];

				$content[] = array
					(
					$translations['post'] => $hour['post'],
					$translations['code'] => $hour['code'],
					$translations['descr'] => $descr,
					$translations['unit'] => $hour['unit_name'],
					$translations['quantity'] => $hour['quantity'],
					$translations['billperae'] => $hour['billperae'],
					$translations['cost'] => $hour['cost']
				);
			}

			return $content;
		}

		function pdf_order( $workorder_id = '', $show_cost = false )
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}
			if (!$workorder_id)
			{
				$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
				$show_cost = phpgw::get_var('show_cost', 'bool');
				$GLOBALS['phpgw_info']['flags']['noheader'] = true;
				$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			}
			if (!$show_cost)
			{
				$show_cost = phpgw::get_var('show_cost', 'bool');
			}

			$preview = phpgw::get_var('preview', 'bool');

			$common_data = $this->common_data($workorder_id);
			$project = $this->boproject->read_single($common_data['workorder']['project_id'], array(), true);

			if (isset($this->config->config_data['invoice_acl']) && $this->config->config_data['invoice_acl'] == 'dimb')
			{
				$approve_role = execMethod('property.boinvoice.check_role', $project['ecodimb'] ? $project['ecodimb'] : $common_data['workorder']['ecodimb']);

				$_ok = false;
				if ($approve_role['is_supervisor'])
				{
					$_ok = true;
				}
				else if ($approve_role['is_budget_responsible'])
				{
					$_ok = true;
				}
				else if ($common_data['workorder']['approved'])
				{
					$_ok = true;
				}
				else
				{
					$_ok = $this->_validate_purchase_grant($workorder_id, $project['ecodimb'] ? $project['ecodimb'] : $workorder['ecodimb'], $project['id']);
				}

				if (!$_ok)
				{
					throw new Exception(lang('order %1 is not approved'), $workorder_id);
//					phpgwapi_cache::message_set(lang('order is not approved'), 'error');
//					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiwo_hour.view',
//						'workorder_id' => $workorder_id, 'from' => phpgw::get_var('from')));
				}
				unset($_ok);
			}
			else
			{
				try
				{
					$_validated = $this->_validate_purchase_grant($workorder_id, $project['ecodimb'] ? $project['ecodimb'] : $workorder['ecodimb'], $project['id']);
				}
				catch (Exception $ex)
				{
					throw $ex;
				}

				if(!$_validated)
				{
					throw new Exception(lang('order %1 is not approved'), $workorder_id);
//					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uiwo_hour.view',
//						'workorder_id' => $workorder_id, 'from' => phpgw::get_var('from')));
				}
			}

			$content = $this->_get_order_details($common_data['content'], $show_cost);

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date(time(), $dateformat);

			set_time_limit(1800);

			$pdf = CreateObject('phpgwapi.pdf');
			$pdf->ezSetMargins(50, 70, 50, 50);

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();

			if (isset($this->config->config_data['order_logo']) && $this->config->config_data['order_logo'])
			{
				$pdf->addJpegFromFile($this->config->config_data['order_logo'], 40, 800, isset($this->config->config_data['order_logo_width']) && $this->config->config_data['order_logo_width'] ? $this->config->config_data['order_logo_width'] : 80
				);
			}
			$pdf->setStrokeColor(0, 0, 0, 1);
			$pdf->line(20, 40, 578, 40);
			//	$pdf->line(20,820,578,820);
			//	$pdf->addText(50,823,6,lang('order'));
			$pdf->addText(50, 28, 6, $this->config->config_data['org_name']);
			$pdf->addText(300, 28, 6, $date);
			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all, 'all');

			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

//			$pdf->ezSetDy(-100);
			$pdf->openHere('Fit');

			$pdf->ezStartPageNumbers(500, 28, 6, 'right', '{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}', 1);

			$data = array
				(
				array('col1' => "{$this->config->config_data['org_name']}\n\nOrg.nr: {$this->config->config_data['org_unit_id']}",
					'col2' => lang('Order'), 'col3' => lang('order id') . "\n\n{$workorder_id}")
			);

			$pdf->ezTable($data, array('col1' => '', 'col2' => '', 'col3' => ''), '', array(
				'showHeadings' => 0, 'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500,
				'gridlines' => EZ_GRIDLINE_ALL,
				//'outerLineThickness'=>1,
				//'innerLineThickness'=> 1,
				//	'showLines'	=> 0,
				'cols' => array
					(
					'col1' => array('width' => 200, 'justification' => 'left'),
					'col2' => array('width' => 100, 'justification' => 'center'),
					'col3' => array('width' => 200, 'justification' => 'right'),
				)
			));

			$location_code = isset($common_data['workorder']['location_code']) && $common_data['workorder']['location_code'] ? $common_data['workorder']['location_code'] : $project['location_code'];

			$delivery_address = lang('delivery address') . ':';

			if($common_data['workorder']['delivery_address'])
			{
				$delivery_address .= "\n{$common_data['workorder']['delivery_address']}";
			}
			else if (isset($this->config->config_data['delivery_address']) && $this->config->config_data['delivery_address'])
			{
				$delivery_address .= "\n{$this->config->config_data['delivery_address']}";
			}
			else
			{
				$address_element = execMethod('property.botts.get_address_element', $location_code);
				foreach ($address_element as $entry)
				{
					$delivery_address .= "\n{$entry['text']}: {$entry['value']}";
				}

				if(!empty($project['location_data']['last_name']))
				{
					$lang_tenant = lang('tenant');
					$delivery_address .= "\n{$lang_tenant}: {$project['location_data']['first_name']} {$project['location_data']['last_name']}";
				}
				if(!empty($project['contact_phone']))
				{
					$lang_contact_phone = lang('Contact phone');
					$delivery_address .= "\n{$lang_contact_phone}: {$project['contact_phone']}";
				}

			}

			$formatted_gab_id = $this->get_gab_id($location_code);
			$delivery_address .= "\nGnr/Bnr: {$formatted_gab_id}";

			$invoice_address = lang('invoice address') . ":\n{$this->config->config_data['invoice_address']}";

			$GLOBALS['phpgw']->preferences->set_account_id($common_data['workorder']['user_id'], true);

			$from_name = $GLOBALS['phpgw']->accounts->get($common_data['workorder']['user_id'])->__toString();

			$ecodimb = !empty($common_data['workorder']['ecodimb']) ? $common_data['workorder']['ecodimb'] : $project['ecodimb'];
			$from = lang('date') . ": {$date}\n";
			$from .= lang('dimb') . ": {$ecodimb}\n";
			$from .= lang('from') . ":\n   {$from_name}";
			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['email']}";
			$from .= "\n   {$GLOBALS['phpgw']->preferences->data['property']['cellphone']}";

			$data = array
				(
				array('col1' => lang('vendor') . ":\n{$common_data['workorder']['vendor_name']}",
					'col2' => $delivery_address),
				array('col1' => $from, 'col2' => $invoice_address)
			);

			if($common_data['workorder']['start_date'])
			{
				$data[] = array('col1' => lang('deadline for start'), 'col2' =>"<b>{$common_data['workorder']['start_date']}</b>");
			}
			if($common_data['workorder']['end_date'])
			{
				$data[] = array('col1' => lang('deadline for execution'), 'col2' =>"<b>{$common_data['workorder']['end_date']}</b>");
			}

			$contract_list = $this->bocommon->get_vendor_contract($common_data['workorder']['vendor_id'],$common_data['workorder']['contract_id']);
			foreach ($contract_list as $contract)
			{
				if($contract['selected'])
				{
					$data[] = array('col1' => lang('contract'), 'col2' =>"<b>{$contract['name']}</b>");
					break;
				}
			}


			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), ''
				, array('showHeadings' => 0, 'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500, 'gridlines' => EZ_GRIDLINE_ALL,
				'cols' => array
					(
					'col1' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
					'col2' => array('justification' => 'right', 'width' => 250, 'justification' => 'left'),
				)
			));

			$pdf->ezText(lang('title') . ':', 20);
			$pdf->ezText($common_data['workorder']['title'], 14);
			$pdf->ezSetDy(-20);

			$pdf->ezText(lang('descr') . ':', 20);
			$pdf->ezText($common_data['workorder']['descr'], 14);

			if ($content)
			{
				$pdf->ezSetDy(-20);
				$pdf->ezTable($content, '', lang('details'), array('xPos' => 0, 'xOrientation' => 'right',
					'width' => 500, 0, 'shaded' => 0, 'fontSize' => 8,
					'gridlines' => EZ_GRIDLINE_ALL,
					'titleFontSize' => 12, 'outerLineThickness' => 1, 'cols' => array(
						lang('bill per unit') => array('justification' => 'right', 'width' => 50)
						, lang('quantity') => array('justification' => 'right', 'width' => 50)
						, lang('cost') => array('justification' => 'right', 'width' => 50)
						, lang('unit') => array('width' => 40)
						, lang('descr') => array('width' => 120))
				));
			}

			$sms_location_id = $GLOBALS['phpgw']->locations->get_id('sms', 'run');
			$config_sms = CreateObject('admin.soconfig', $sms_location_id);
			phpgw::import_class('phpgwapi.phpqrcode');
			$code_text = "SMSTO:{$config_sms->config_data['common']['gateway_number']}: STATUS {$workorder_id} ";
			$filename = $GLOBALS['phpgw_info']['server']['temp_dir'] . '/' . md5($code_text) . '.png';
			QRcode::png($code_text, $filename);
			$pdf->ezSetDy(-20);
			//		$pdf->ezImage($filename,$pad = 0,$width = 0,$resize = '',$just = 'left',$border = '');
			//		$pdf->ezText(lang('status code') .': 1 => ' . lang('performed'). ', 2 => ' . lang('No access') . ', 3 => I arbeid',10);

			$data = array(
				array('col1' => "<C:showimage:{$filename} 90>", 'col2' => "\n" . lang('status code') . ":\n\n 1 => " . lang('performed') . "\n 2 => " . lang('No access') . "\n 3 => I arbeid")
			);


			$pdf->ezTable($data, array('col1' => '', 'col2' => ''), '', array('showHeadings' => 0,
				'shaded' => 0, 'xPos' => 0,
				'xOrientation' => 'right', 'width' => 500,
				'gridlines' => EZ_GRIDLINE_ALL,
				'cols' => array
					(
					'col1' => array('width' => 150, 'justification' => 'left'),
					'col2' => array('width' => 350, 'justification' => 'left'),
				)
			));

			$location_exceptions = createObject('property.solocation')->get_location_exception($location_code, $alert_vendor = true);

			if($location_exceptions)
			{
				$pdf->ezSetDy(-10);
				$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica-Bold.afm');
				$pdf->ezText(lang('important information'), 14);
				$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');
			}

			foreach ($location_exceptions as $location_exception)
			{
				$pdf->ezText($location_exception['category_text'], 12);

				if($location_exception['location_descr'])
				{
					$pdf->ezText($location_exception['location_descr'], 12);
				}
			}

			if (isset($this->config->config_data['order_footer_header']) && $this->config->config_data['order_footer_header'])
			{
				if ($content)
				{
					$pdf->ezSetDy(-10);
				}
				else
				{
					$pdf->ezSetDy(-80);
				}
				$pdf->ezText($this->config->config_data['order_footer_header'], 12);
				$pdf->ezText($this->config->config_data['order_footer'], 10);
			}

			if ($preview)
			{
				//	$pdf->print_pdf($document,'order');
				$pdf->ezStream();
			}
			else
			{
				return $pdf->ezOutput();
			}
		}

		function tender()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$pdf = CreateObject('phpgwapi.pdf');
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}
			$show_cost = phpgw::get_var('show_cost', 'bool');
			$mark_draft = phpgw::get_var('mark_draft', 'bool');
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint

			$common_data = $this->common_data($workorder_id);
			$project = $this->boproject->read_single($common_data['workorder']['project_id']);

			$content = $this->_get_order_details($common_data['content'], $show_cost);


			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date = $GLOBALS['phpgw']->common->show_date('', $dateformat);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf->ezSetMargins(50, 70, 50, 50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0, 0, 0, 1);
			$pdf->line(20, 40, 578, 40);
			$pdf->line(20, 822, 578, 822);
			$pdf->addText(50, 823, 6, lang('Chapter') . ' ' . $common_data['workorder']['chapter_id'] . ' ' . $common_data['workorder']['chapter']);
			$pdf->addText(50, 34, 6, $this->config->config_data['org_name']);
			$pdf->addText(300, 34, 6, $date);
			if ($mark_draft)
			{
				$pdf->setColor(1, 0, 0);
				//		$pdf->setColor(66,66,99);
				$pdf->addText(200, 400, 40, lang('DRAFT'), -10);
				$pdf->setColor(1, 0, 0);
			}
			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all, 'all');

			$pdf->ezSetDy(-100);


			$pdf->ezStartPageNumbers(500, 28, 10, 'right', '{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}', 1);

			$pdf->ezText($project['name'], 20);
			$pdf->ezText($project['descr'], 14);
			$pdf->ezSetDy(-50);
			$pdf->ezText(lang('Order') . ': ' . $workorder_id . ' ' . $common_data['workorder']['title'], 14);
			$pdf->ezText(lang('Chapter') . ' ' . $common_data['workorder']['chapter_id'] . ' ' . $common_data['workorder']['chapter'], 14);

			if ($content)
			{
				$pdf->ezNewPage();
				$pdf->ezTable($content, '', $project['name'], array('xPos' => 70, 'xOrientation' => 'right',
					'width' => 500, 0, 'shaded' => 0, 'fontSize' => 8,
					'gridlines' => EZ_GRIDLINE_ALL, 'titleFontSize' => 12, 'outerLineThickness' => 2,
					'cols' => array(
						lang('bill per unit') => array('justification' => 'right', 'width' => 50)
						, lang('quantity') => array('justification' => 'right', 'width' => 50)
						, lang('cost') => array('justification' => 'right', 'width' => 50)
						, lang('unit') => array('width' => 40)
						, lang('descr') => array('width' => 120))
				));
			}

			$document = $pdf->ezOutput();
			$pdf->print_pdf($document, 'tender');
		}

		function prizebook()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$values = phpgw::get_var('values');

			if ($delete && $hour_id)
			{
				$receipt = $this->bo->delete($hour_id, $workorder_id);
				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					return "hour " . $hour_id . " " . lang("has been deleted");
				}
			}

			if ($values['add'])
			{
				$receipt = $this->bo->add_hour($values, $workorder_id);
				return $receipt;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_prizebook();
			}

			$uicols = array(
				'input_type' => array('hidden', 'text', 'hidden', 'hidden', 'text', 'text', 'text',
					'text', 'text', 'text', 'hidden', 'varchar', 'select', 'varchar'),
				'type' => array('', '', '', '', '', '', '', '', '', '', '', 'text', '', ''),
				'sortable' => array('', true, '', '', '', '', '', true, '', '', '', 'text',
					'', ''),
				'name' => array('activity_id', 'num', 'branch', 'vendor_id', 'descr', 'base_descr',
					'unit_name', 'w_cost', 'm_cost', 'total_cost', 'this_index', 'quantity', 'wo_hour_cat',
					'cat_per_cent'),
				'formatter' => array('', '', '', '', '', '', '', '', '', '', '', '', '', ''),
				'descr' => array('', lang('Activity Num'), lang('Branch'), lang('Vendor'),
					lang('Description'), lang('Base'), lang('Unit'), lang('Labour cost'), lang('Material cost'),
					lang('Total Cost'), '', lang('Quantity'), lang('category'), lang('percent')),
				'className' => array('', '', '', '', '', '', '', 'rightClasss', 'rightClasss',
					'rightClasss', '', '', '', '')
			);

			$count_uicols = count($uicols['name']);
			$price_book_def = array();

			for ($k = 0; $k < $count_uicols; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'className' => $uicols['className'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				array_push($price_book_def, $params);
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiwo_hour.prizebook',
						'workorder_id' => $workorder_id, 'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => $price_book_def
			);

			//*************************************************/
			$data = array();
			$data['datatable_def'] = $datatable_def;
			$data['datatable_name'] = '';
			$data['form'] = array();
			$data['datatable'] = array
				(
				'source' => self::link(array(
					'menuaction' => 'property.uiwo_hour.index',
					'workorder_id' => $workorder_id,
					'phpgw_return_as' => 'json'
				)),
				'allrows' => true,
				'editor_action' => '',
				'field' => array()
			);

			$data['top-toolbar'] = array
				(
				'fields' => array
					(
					'field' => array
						(
						array
							(
							'type' => 'button',
							'id' => 'btn_save',
							'value' => lang('Save'),
							'href' => '#',
							'onclick' => 'onSave();'
						),
						array
							(
							'type' => 'button',
							'id' => 'btn_done',
							'value' => lang('done'),
							'href' => self::link(array(
								'menuaction' => 'property.uiwo_hour.index',
								'workorder_id' => $workorder_id
							))
						)
					)
				)
			);

			$uicols_hour = array(
				'name' => array('hour_id', 'post', 'code', 'hours_descr', 'unit_name', 'billperae',
					'quantity', 'cost', 'deviation', 'result', 'wo_hour_category', 'cat_per_cent'),
				'input_type' => array('hidden', 'text', 'text', 'text', 'text', 'text', 'text',
					'text', 'text', 'text', 'text', 'text'),
				'descr' => array('', lang('Post'), lang('Code'), lang('Descr'), lang('Unit'),
					lang('Bill per unit'), lang('Quantity'), lang('Cost'), lang('deviation'), lang('result'),
					lang('Category'), lang('percent')),
				'className' => array('', '', '', '', '', 'dt-right', 'dt-right', 'dt-right',
					'dt-right', 'dt-right', '', 'dt-right')
			);

			$count_uicols_name = count($uicols_hour['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols_hour['name'][$k],
					'label' => $uicols_hour['descr'][$k],
					'className' => $uicols_hour['className'][$k],
					'sortable' => ($uicols_hour['sortable'][$k]) ? true : false,
					'hidden' => ($uicols_hour['input_type'][$k] == 'hidden') ? true : false
				);

				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'hour_id',
						'source' => 'hour_id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'deviation',
				'text' => lang('Deviation'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'deviation',
				'text' => lang('open deviation in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('Edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.edit',
					'workorder_id' => $workorder_id,
					'from' => 'prizebook'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('open edit in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.edit',
					'workorder_id' => $workorder_id,
					'from' => 'prizebook'
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'delete',
				'text' => lang('Delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.prizebook',
					'workorder_id' => $workorder_id,
					'delete' => true
				)),
				'parameters' => json_encode($parameters)
			);

			unset($parameters);

			$common_data = $this->common_data($workorder_id);
			$data['datatable']['table_sum'] = $common_data['table_sum'][0];
			$data['datatable']['workorder_data'] = $common_data['workorder_data'];

			$appname = lang('pricebook');
			$function_msg = lang('list pricebook');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::add_javascript('property', 'portico', 'wo_hour.index.js');
			self::add_javascript('property', 'portico', 'wo_hour.prizebook.js');
			self::render_template_xsl(array('wo_hour.index', 'datatable_inline'), $data);
		}

		public function query_prizebook()
		{
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$search = phpgw::get_var('search');
			$columns = phpgw::get_var('columns');

			$common_data = $this->common_data($workorder_id);
			$workorder = $common_data['workorder'];

			$uicols = array(
				'input_type' => array('hidden', 'text', 'hidden', 'hidden', 'text', 'text', 'text',
					'text', 'text', 'text', 'hidden', 'varchar', 'select', 'varchar'),
				'type' => array('', '', '', '', '', '', '', '', '', '', '', 'text', '', ''),
				'name' => array('activity_id', 'num', 'branch', 'vendor_id', 'descr', 'base_descr',
					'unit_name', 'w_cost', 'm_cost', 'total_cost', 'this_index', 'quantity', 'wo_hour_cat',
					'cat_per_cent'),
				'formatter' => array('', '', '', '', '', '', '', '', '', '', '', '', '', ''),
				'descr' => array('', lang('Activity Num'), lang('Branch'), lang('Vendor'),
					lang('Description'), lang('Base'), lang('Unit'), lang('Labour cost'), lang('Material cost'),
					lang('Total Cost'), '', lang('Quantity'), lang('category'), lang('percent')),
				'className' => array('', '', '', '', '', '', '', 'rightClasss', 'rightClasss',
					'rightClasss', '', '', '', '')
			);

			if ($workorder['vendor_id'])
			{
				$params = array
					(
					'query' => $search['value'],
					'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
					'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
					'order' => $columns[$order[0]['column']]['data'],
					'sort' => $order[0]['dir'],
					'allrows' => phpgw::get_var('length', 'int') == -1,
					'cat_id' => $workorder['vendor_id']
				);
				$pricebook_list = $this->bopricebook->read($params);
			}

			$values_combo_box = $this->bocommon->select_category_list(array('format' => 'filter',
				'selected' => $this->wo_hour_cat_id, 'type' => 'wo_hours', 'order' => 'id'));
			$default_value = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box, $default_value);

			$content = array();
			$j = 0;
			if (isset($pricebook_list) && is_array($pricebook_list))
			{
				foreach ($pricebook_list as $pricebook)
				{
					$json_row = array();

					$hidden = '';
					$hidden .= " <input counter='" . $j . "' name='values[activity_id][" . $j . "]' id='values[activity_id][" . $j . "]' class='activity_id'  type='hidden' value='" . $pricebook['activity_id'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[activity_num][" . $j . "]' id='values[activity_num][" . $j . "]' class='activity_num'  type='hidden' value='" . $pricebook['num'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[unit][" . $j . "]' id='values[unit][" . $j . "]' class='unit'  type='hidden' value='" . $pricebook['unit'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[dim_d][" . $j . "]' id='values[dim_d][" . $j . "]' class='dim_d'  type='hidden' value='" . $pricebook['dim_d'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[ns3420_id][" . $j . "]' id='values[ns3420_id][" . $j . "]' class='ns3420_id'  type='hidden' value='" . $pricebook['ns3420_id'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[descr][" . $j . "]' id='values[descr][" . $j . "]' class='descr'  type='hidden' value='" . $pricebook['descr'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[total_cost][" . $j . "]' id='values[total_cost][" . $j . "]' class='total_cost'  type='hidden' value='" . $pricebook['total_cost'] . "'/>";

					for ($i = 0; $i < count($uicols['name']); $i++)
					{
						$json_row[$uicols['name'][$i]] = $pricebook[$uicols['name'][$i]];

						if ($uicols['name'][$i] == 'quantity')
						{
							$json_row[$uicols['name'][$i]] = "<input counter='" . $j . "' name='values[" . $uicols['name'][$i] . "][" . $j . "]' id='values[" . $uicols['name'][$i] . "][" . $j . "]' size='4' class='quantity'/>";
						}
						if ($uicols['name'][$i] == 'cat_per_cent')
						{
							$json_row[$uicols['name'][$i]] = "<input counter='" . $j . "' name='values[" . $uicols['name'][$i] . "][" . $j . "]' id='values[" . $uicols['name'][$i] . "][" . $j . "]' size='4' class='cat_per_cent'/>";
						}
						$select = '';
						if ($uicols['input_type'][$i] == 'select')
						{
							$select .= "<select counter='" . $j . "' name='values[" . $uicols['name'][$i] . "_list][" . $j . "]' id='values[" . $uicols['name'][$i] . "_list][" . $j . "]' class='wo_hour_cat'>";
							for ($k = 0; $k < count($values_combo_box); $k++)
							{
								$select .= "<option value='" . $values_combo_box[$k]['id'] . "'>" . $values_combo_box[$k]['name'] . "</option>";
							}
							$select .= "</select>";
							$json_row[$uicols['name'][$i]] = $select . $hidden;
						}
					}
					$content[] = $json_row;
					$j++;
				}
			}

			$result_data = array('results' => $content);
			$result_data['total_records'] = $this->bopricebook->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function template()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$delete = phpgw::get_var('delete', 'bool');
			$hour_id = phpgw::get_var('hour_id', 'int');

			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$template_id = phpgw::get_var('template_id', 'int');

			$values = $_POST['values'] ? phpgw::get_var('values') : array();

			if ($delete && $hour_id)
			{
				$receipt = $this->bo->delete($hour_id, $workorder_id);

				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					return "hour " . $hour_id . " " . lang("has been deleted");
				}
			}

			if ($values['add'])
			{
				$receipt = $this->bo->add_hour_from_template($values, $workorder_id);
				return $receipt;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query_template();
			}

			$uicols = array(
				'input_type' => array('text', 'text', 'text', 'text', 'text', 'varchar', 'combo',
					'varchar', 'hidden', 'hidden', 'hidden', 'hidden', 'hidden', 'hidden', 'hidden',
					'hidden', 'hidden', 'hidden'),
				'type' => array('', '', '', '', '', 'text', '', '', 'text', '', '', '', ''),
				'name' => array('building_part', 'code', 'hours_descr', 'unit_name', 'billperae',
					'quantity', 'wo_hour_cat', 'cat_per_cent', 'chapter_id', 'grouping_descr', 'new_grouping',
					'activity_id', 'activity_num', 'remark', 'ns3420_id', 'tolerance', 'cost', 'dim_d'),
				'formatter' => array('', '', '', '', '', '', '', '', '', '', '', '', '', '',
					'', '', '', '', ''),
				'descr' => array(lang('Building part'), lang('Code'), lang('Description'),
					lang('Unit'), lang('Bill per unit'), lang('Quantity'), '', '', '', '', '', '',
					'', '', '', '', '', ''),
				'className' => array('', '', '', '', 'rightClasss', '', '', '', '', '', '',
					'', '', '', '', '', '', '')
			);

			$count_uicols = count($uicols['name']);
			$template_def = array();

			for ($k = 0; $k < $count_uicols; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'className' => $uicols['className'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				array_push($template_def, $params);
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiwo_hour.template',
						'workorder_id' => $workorder_id, 'template_id' => $template_id, 'phpgw_return_as' => 'json'))),
				'data' => json_encode(array()),
				'ColumnDefs' => $template_def
			);

			$data = array();
			$data['datatable_def'] = $datatable_def;
			$data['datatable_name'] = '';
			$data['form'] = array();
			$data['datatable'] = array
				(
				'source' => self::link(array(
					'menuaction' => 'property.uiwo_hour.index',
					'workorder_id' => $workorder_id,
					'phpgw_return_as' => 'json'
				)),
				'allrows' => true,
				'editor_action' => '',
				'field' => array()
			);

			$data['top-toolbar'] = array
				(
				'fields' => array
					(
					'field' => array
						(
						array
							(
							'type' => 'button',
							'id' => 'btn_save',
							'value' => lang('Save'),
							'href' => '#',
							'onclick' => 'onSave();'
						),
						array
							(
							'type' => 'button',
							'id' => 'btn_done',
							'value' => lang('done'),
							'href' => self::link(array(
								'menuaction' => 'property.uiwo_hour.index',
								'workorder_id' => $workorder_id
							))
						)
					)
				)
			);

			$uicols_hour = array(
				'name' => array('hour_id', 'post', 'code', 'hours_descr', 'unit_name', 'billperae',
					'quantity', 'cost', 'deviation', 'result', 'wo_hour_category', 'cat_per_cent'),
				'input_type' => array('hidden', 'text', 'text', 'text', 'text', 'text', 'text',
					'text', 'text', 'text', 'text', 'text'),
				'descr' => array('', lang('Post'), lang('Code'), lang('Descr'), lang('Unit'),
					lang('Bill per unit'), lang('Quantity'), lang('Cost'), lang('deviation'), lang('result'),
					lang('Category'), lang('percent')),
				'className' => array('', '', '', '', '', 'dt-right', 'dt-right', 'dt-right',
					'dt-right', 'dt-right', '', 'dt-right')
			);

			$count_uicols_name = count($uicols_hour['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols_hour['name'][$k],
					'label' => $uicols_hour['descr'][$k],
					'className' => $uicols_hour['className'][$k],
					'sortable' => ($uicols_hour['sortable'][$k]) ? true : false,
					'hidden' => ($uicols_hour['input_type'][$k] == 'hidden') ? true : false
				);

				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'hour_id',
						'source' => 'hour_id'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'deviation',
				'text' => lang('Deviation'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id,
					'from' => 'template'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'deviation',
				'text' => lang('open deviation in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id,
					'from' => 'template'
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('Edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.edit',
					'workorder_id' => $workorder_id,
					'template_id' => $template_id,
					'from' => 'template'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('open edit in new window'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.edit',
					'workorder_id' => $workorder_id,
					'template_id' => $template_id,
					'from' => 'template'
				)),
				'target' => '_blank',
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'delete',
				'text' => lang('Delete'),
				'confirm_msg' => lang('do you really want to delete this entry'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'property.uiwo_hour.template',
					'workorder_id' => $workorder_id,
					'template_id' => $template_id,
					'delete' => true
				)),
				'parameters' => json_encode($parameters)
			);

			unset($parameters);

			$common_data = $this->common_data($workorder_id);

			$data['datatable']['table_sum'] = $common_data['table_sum'][0];
			$data['datatable']['workorder_data'] = $common_data['workorder_data'];

			$appname = lang('Template');
			$function_msg = lang('list template');

			// Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::add_javascript('property', 'portico', 'wo_hour.index.js');
			self::add_javascript('property', 'portico', 'wo_hour.template.js');
			self::render_template_xsl(array('wo_hour.index', 'datatable_inline'), $data);

			//$this->save_sessiondata();
		}

		public function query_template()
		{
			$template_id = phpgw::get_var('template_id', 'int');

			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$search = phpgw::get_var('search');
			$columns = phpgw::get_var('columns');

			$botemplate = CreateObject('property.botemplate');

			$uicols = array(
				'input_type' => array('text', 'text', 'text', 'text', 'text', 'varchar', 'combo',
					'varchar', 'hidden', 'hidden', 'hidden', 'hidden', 'hidden', 'hidden', 'hidden',
					'hidden', 'hidden', 'hidden'),
				'type' => array('', '', '', '', '', 'text', '', '', 'text', '', '', '', ''),
				'name' => array('building_part', 'code', 'hours_descr', 'unit_name', 'billperae',
					'quantity', 'wo_hour_cat', 'cat_per_cent', 'chapter_id', 'grouping_descr', 'new_grouping',
					'activity_id', 'activity_num', 'remark', 'ns3420_id', 'tolerance', 'cost', 'dim_d'),
				'formatter' => array('', '', '', '', '', '', '', '', '', '', '', '', '', '',
					'', '', '', '', ''),
				'descr' => array(lang('Building part'), lang('Code'), lang('Description'),
					lang('Unit'), lang('Bill per unit'), lang('Quantity'), '', '', '', '', '', '',
					'', '', '', '', '', ''),
				'className' => array('', '', '', '', 'rightClasss', '', '', '', '', '', '',
					'', '', '', '', '', '', '')
			);

			$values_combo_box = $this->bocommon->select_category_list(array('format' => 'filter',
				'selected' => $this->wo_hour_cat_id, 'type' => 'wo_hours', 'order' => 'id'));
			$default_value = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box, $default_value);

			$params = array
				(
				'query' => $search['value'],
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
				'template_id' => $template_id,
				'chapter_id' => $this->chapter_id
			);

			$template_list = $botemplate->read_template_hour($params);

			$grouping_descr_old = '';
			$content = array();
			$j = 0;
			if (isset($template_list) && is_array($template_list))
			{
				foreach ($template_list as $template)
				{
					$json_row = array();

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

					$hidden = '';
					$hidden .= " <input counter='" . $j . "' name='values[chapter_id][" . $j . "]' id='values[chapter_id][" . $j . "]'  class='chapter_id'  type='hidden' value='" . $template['chapter_id'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[grouping_descr][" . $j . "]' id='values[grouping_descr][" . $j . "]'  class='grouping_descr'  type='hidden' value='" . $template['grouping_descr'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[activity_id][" . $j . "]' id='values[activity_id][" . $j . "]'  class='activity_id'  type='hidden' value='" . $template['activity_id'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[activity_num][" . $j . "]' id='values[activity_num][" . $j . "]'  class='activity_num'  type='hidden' value='" . $template['activity_num'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[unit][" . $j . "]' id='values[unit][" . $j . "]'  class='unit'  type='hidden' value='" . $template['unit'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[dim_d][" . $j . "]' id='values[dim_d][" . $j . "]'  class='dim_d'  type='hidden' value='" . $template['dim_d'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[ns3420_id][" . $j . "]' id='values[ns3420_id][" . $j . "]' class='ns3420_id'  type='hidden' value='" . $template['ns3420_id'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[tolerance][" . $j . "]' id='values[tolerance][" . $j . "]' class='tolerance'  type='hidden' value='" . $template['tolerance'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[building_part][" . $j . "]' id='values[building_part][" . $j . "]' class='building_part'  type='hidden' value='" . $template['building_part'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[hours_descr][" . $j . "]' id='values[hours_descr][" . $j . "]' class='hours_descr'  type='hidden' value='" . $template['hours_descr'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[remark][" . $j . "]' id='values[remark][" . $j . "]' class='remark'  type='hidden' value='" . $template['remark'] . "'/>";
					$hidden .= " <input counter='" . $j . "' name='values[billperae][" . $j . "]' id='values[billperae][" . $j . "]' class='billperae'  type='hidden' value='" . $template['billperae'] . "'/>";

					for ($i = 0; $i < count($uicols['name']); $i++)
					{
						$json_row[$uicols['name'][$i]] = $template[$uicols['name'][$i]];
						if ($uicols['name'][$i] == 'code')
						{
							$json_row[$uicols['name'][$i]] = $code;
						}
						if ($uicols['name'][$i] == 'activity_num')
						{
							$json_row[$uicols['name'][$i]] = $new_grouping;
						}

						//$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];

						if ($uicols['input_type'][$i] == 'varchar')
						{
							$json_row[$uicols['name'][$i]] = "<input counter='" . $j . "' name='values[{$uicols['name'][$i]}][{$j}]' id='values[{$uicols['name'][$i]}][{$j}]' size='4' class='" . $uicols['name'][$i] . "'/>";
						}

						/* if ($uicols['input_type'][$i]=='select')
						  {
						  $json_row[$uicols['name'][$i]] = "<input name='values[".$uicols['name'][$i]."][".$j."]' id='values[".$uicols['name'][$i]."][".$j."]' class='myValuesForPHP CheckClass' type='hidden' value=''/> <input type='checkbox' name='values[".$uicols['name'][$i]."_tmp][".$j."]' id='values[".$uicols['name'][$i]."_tmp][".$j."]' class='CheckClass_tmp' value='".$j."' />";
						  } */

						$select = '';
						if ($uicols['input_type'][$i] == 'combo')
						{
							$select .= "<select counter='" . $j . "' name='values[" . $uicols['name'][$i] . "_list][" . $j . "]' id='values[" . $uicols['name'][$i] . "_list][" . $j . "]' class='" . $uicols['name'][$i] . "'>";
							for ($k = 0; $k < count($values_combo_box); $k++)
							{
								$select .= "<option value='" . $values_combo_box[$k]['id'] . "'>" . $values_combo_box[$k]['name'] . "</option>";
							}
							$select .= "</select>";
							//$select  .= " <input name='values[".$uicols['name'][$i]."][".$j."]' id='values[".$uicols['name'][$i]."][".$j."]'  class='combo'  type='hidden' value=''/>";
							$json_row[$uicols['name'][$i]] = $select . $hidden;
						}
					}
					$content[] = $json_row;
					$j++;
				}
			}

			$result_data = array('results' => $content);
			$result_data['total_records'] = $botemplate->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function edit()
		{
			if (!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 2, 'acl_location' => $this->acl_location));
			}
			$from = phpgw::get_var('from');
			$template_id = phpgw::get_var('template_id', 'int');
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$activity_id = phpgw::get_var('activity_id', 'int');
			$hour_id = phpgw::get_var('hour_id', 'int');
			$values = phpgw::get_var('values');
			$values['ns3420_id'] = phpgw::get_var('ns3420_id');
			$values['ns3420_descr'] = phpgw::get_var('ns3420_descr');

			$receipt = array();

			if ($values['save'])
			{
				if ($values['copy_hour'])
				{
					unset($hour_id);
				}

				$values['hour_id'] = $hour_id;

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_hour($values, $workorder_id);

					$hour_id = $receipt['hour_id'];
				}
			}

			if ($hour_id)
			{
				$values = $this->bo->read_single_hour($hour_id);
				$function_msg = lang('Edit hour');
			}
			else
			{
				$function_msg = lang('Add hour');
			}

			$workorder = $this->boworkorder->read_single($workorder_id);

			if ($error_id)
			{
				unset($values['hour_id']);
			}

			$link_data = array
				(
				'menuaction' => 'property.uiwo_hour.edit',
				'activity_id' => $activity_id,
				'workorder_id' => $workorder_id,
				'template_id' => $template_id,
				'hour_id' => $hour_id,
				'from' => $from
			);

			$_filter_buildingpart = array();
			$filter_buildingpart = isset($this->config->config_data['filter_buildingpart']) ? $this->config->config_data['filter_buildingpart'] : array();

			if ($filter_key = array_search('.project', $filter_buildingpart))
			{
				$_filter_buildingpart = array("filter_{$filter_key}" => 1);
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('generic'), 'link' => '#generic');
			$active_tab = 'generic';

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.' . $from,
					'workorder_id' => $workorder_id, 'template_id' => $template_id)),
				'lang_workorder' => lang('Workorder'),
				'value_workorder_id' => $workorder['workorder_id'],
				'value_workorder_title' => $workorder['title'],
				'lang_hour_id' => lang('Hour ID'),
				'value_hour_id' => $hour_id,
				'lang_copy_hour' => lang('Copy hour ?'),
				'lang_copy_hour_statustext' => lang('Choose Copy Hour to copy this hour to a new hour'),
				'lang_activity_num' => lang('Activity code'),
				'value_activity_num' => $values['activity_num'],
				'value_activity_id' => $values['activity_id'],
				'lang_unit' => lang('Unit'),
				'lang_save' => lang('save'),
				'lang_done' => lang('done'),
				'lang_descr' => lang('description'),
				'value_descr' => $values['hours_descr'],
				'lang_descr_statustext' => lang('Enter the description for this activity'),
				'lang_done_statustext' => lang('Back to the list'),
				'lang_save_statustext' => lang('Save the building'),
				'lang_remark' => lang('Remark'),
				'value_remark' => $values['remark'],
				'lang_remark_statustext' => lang('Enter additional remarks to the description - if any'),
				'lang_quantity' => lang('quantity'),
				'value_quantity' => $values['quantity'],
				'lang_quantity_statustext' => lang('Enter quantity of unit'),
				'lang_billperae' => lang('Cost per unit'),
				'value_billperae' => $values['billperae'],
				'lang_billperae_statustext' => lang('Enter the cost per unit'),
				'lang_total_cost' => lang('Total cost'),
				'value_total_cost' => $values['cost'],
				'lang_total_cost_statustext' => lang('Enter the total cost of this activity - if not to be calculated from unit-cost'),
				'lang_vendor' => lang('Vendor'),
				'value_vendor_id' => $workorder['vendor_id'],
				'value_vendor_name' => $workorder['vendor_name'],
				'lang_dim_d' => lang('Dim D'),
				'dim_d_list' => $this->bopricebook->get_dim_d_list($values['dim_d']),
				'select_dim_d' => 'values[dim_d]',
				'lang_no_dim_d' => lang('No Dim D'),
				'lang_dim_d_statustext' => lang('Select the Dim D for this activity. To do not use Dim D -  select NO DIM D'),
				'lang_unit' => lang('Unit'),
				'unit_list' => $this->bopricebook->get_unit_list($values['unit']),
				'select_unit' => 'values[unit]',
				'lang_no_unit' => lang('Select Unit'),
				'lang_unit_statustext' => lang('Select the unit for this activity.'),
				'lang_chapter' => lang('chapter'),
				'chapter_list' => $this->bo->get_chapter_list('select', $workorder['chapter_id']),
				'select_chapter' => 'values[chapter_id]',
				'lang_no_chapter' => lang('Select chapter'),
				'lang_chapter_statustext' => lang('Select the chapter (for tender) for this activity.'),
				'lang_tolerance' => lang('tolerance'),
				'tolerance_list' => $this->bo->get_tolerance_list($values['tolerance_id']),
				'select_tolerance' => 'values[tolerance_id]',
				'lang_no_tolerance' => lang('Select tolerance'),
				'lang_tolerance_statustext' => lang('Select the tolerance for this activity.'),
				'lang_grouping' => lang('grouping'),
				'grouping_list' => $this->bo->get_grouping_list($values['grouping_id'], $workorder_id),
				'select_grouping' => 'values[grouping_id]',
				'lang_no_grouping' => lang('Select grouping'),
				'lang_grouping_statustext' => lang('Select the grouping for this activity.'),
				'lang_new_grouping' => lang('New grouping'),
				'lang_new_grouping_statustext' => lang('Enter a new grouping for this activity if not found in the list'),
				'building_part_list' => array('options' => $this->bocommon->select_category_list(array(
						'type' => 'building_part', 'selected' => $values['building_part_id'], 'order' => 'id',
						'id_in_name' => 'num', 'filter' => $_filter_buildingpart))),
				'ns3420_link' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uilookup.ns3420')),
				'lang_ns3420' => lang('NS3420'),
				'value_ns3420_id' => $values['ns3420_id'],
				'lang_ns3420_statustext' => lang('Select a standard-code from the norwegian standard'),
				'currency' => $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
				'lang_wo_hour_category' => lang('category'),
				'lang_select_wo_hour_category' => lang('no category'),
				'wo_hour_cat_list' => $this->bocommon->select_category_list(array('format' => 'select',
					'selected' => $values['wo_hour_cat'], 'type' => 'wo_hours', 'order' => 'id')),
				'lang_cat_per_cent_statustext' => lang('the percentage of the category'),
				'value_cat_per_cent' => $values['cat_per_cent'],
				'lang_per_cent' => lang('percent'),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
			);

			$appname = lang('Workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl(array('wo_hour'), array('edit_hour' => $data));
		}

		function delete()
		{
			if (!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 2, 'acl_location' => $this->acl_location));
			}
			$id = phpgw::get_var('id', 'int');
			$workorder_id = phpgw::get_var('workorder_id'); // in case of bigint
			$hour_id = phpgw::get_var('hour_id', 'int');
			$deviation_id = phpgw::get_var('deviation_id', 'int');
			$confirm = phpgw::get_var('confirm', 'bool', 'POST');


			//delete for JSON proerty2
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete_deviation($workorder_id, $hour_id, $deviation_id);
				return "";
			}

			if ($deviation_id)
			{
				$link_data = array
					(
					'menuaction' => 'property.uiwo_hour.deviation',
					'workorder_id' => $workorder_id,
					'hour_id' => $hour_id
				);
				$delete_link_data = array
					(
					'menuaction' => 'property.uiwo_hour.delete',
					'workorder_id' => $workorder_id,
					'hour_id' => $hour_id,
					'deviation_id' => $deviation_id
				);

				$function_msg = lang('delete deviation');
			}
			else
			{
				$link_data = array
					(
					'menuaction' => 'property.uiwo_hour.index'
				);
				$delete_link_data = array
					(
					'menuaction' => 'property.uiwo_hour.delete',
					'id' => $id
				);
				$function_msg = lang('delete hour');
			}

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				if ($deviation_id)
				{
					$this->bo->delete_deviation($workorder_id, $hour_id, $deviation_id);
				}
				else
				{
					$this->bo->delete($id);
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', $delete_link_data),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('workorder');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function import_calculation()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$workorder_id = phpgw::get_var('workorder_id');
			if ($_FILES)
			{
				$this->_import_calculation($workorder_id);

				$bofiles = CreateObject('property.bofiles');

				$file_name = @str_replace(' ', '_', $_FILES['file']['name']);

				$to_file = "{$bofiles->fakebase}/workorder/{$workorder_id}/{$file_name}";

				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					phpgwapi_cache::message_set(lang('This file already exists !'), 'error');
				}
				else
				{
					$bofiles->create_document_dir("workorder/{$workorder_id}");
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

			if ($receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
			{
				phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
			}

			$tabs = array();
			$tabs['upload_file'] = array('label' => lang('Upload file'), 'link' => '#upload_file');
			$active_tab = 'upload_file';

			$data = array
				(
				'redirect' => $redirect ? $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiinvoice.list_sub',
						'user_lid' => $user_lid, 'voucher_id' => $voucher_id, 'paid' => $paid)) : null,
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($receipt)),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.import_calculation')),
				'workorder_id' => $workorder_id,
				'lang_done' => lang('done'),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiwo_hour.index',
					'workorder_id' => $workorder_id)),
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			self::render_template_xsl(array('wo_hour'), array('import_calculation' => $data));
		}

		private function _import_calculation( $workorder_id )
		{
			$error = false;

			$data = array();
			if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'])
			{
				$file = array
					(
					'name' => $_FILES['file']['tmp_name'],
					'type' => $_FILES['file']['type']
				);
			}
			else
			{
				phpgwapi_cache::message_set('Ingen fil er valgt', 'error');
				return;
			}

			switch ($file['type'])
			{
				case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
				case 'application/vnd.oasis.opendocument.spreadsheet':
				case 'application/vnd.ms-excel':
					$data = $this->getexceldata($file['name']);
					break;
				default:
					phpgwapi_cache::message_set("Not a valid filetype: {$file['type']}", 'error');
					$error = true;
			}

			if ($data)
			{
				try
				{
					//Import
					$this->bo->import_calculation($data, $workorder_id);
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$error = true;
					}
				}
			}

			if (!$error)
			{
				phpgwapi_cache::message_set(lang('workorder is updated'), 'message');
			}
		}

		protected function getexceldata( $path )
		{
			phpgw::import_class('phpgwapi.phpexcel');

			$inputFileType = PHPExcel_IOFactory::identify($path); // Identify the type of file.
			$objReader = PHPExcel_IOFactory::createReader($inputFileType); // Create a reader of the identified file type.
			$worksheetNames = $objReader->listWorksheetNames($path);
//			_debug_array($worksheetNames);

			$objPHPExcel = PHPExcel_IOFactory::load($path);

			$result = array();

			foreach ($worksheetNames as $_index => $sheet_name)
			{
				if($_index == 0)
				{
					continue;
				}
				$result[$_index]['name'] = $sheet_name;
				$objPHPExcel->setActiveSheetIndex($_index);

//				$objWorksheet = $objPHPExcel->getActiveSheet();
//				_debug_array($objWorksheet->getTitle());

				$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);

				$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();

				$start = 9; // Read the first line to get the headers out of the way

				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$this->fields[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, 1)->getCalculatedValue();
				}

				$rows = $rows ? $rows + 1 : 0;
				for ($row = $start; $row < $rows; $row++)
				{
					$_data = array();

					for ($j = 0; $j < $highestColumnIndex; $j++)
					{
						$_data[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $row)->getCalculatedValue();
					}

					$result[$_index]['data'][] = $_data;
				}
			}
			return $result;
		}


		function send_all_orders( )
		{
			if(!$this->acl->check('.admin', PHPGW_ACL_ADD, 'property'))
			{
				phpgw::no_access();
			}

//			$start_from = 45001336;
//			$sql = "SELECT id, status FROM fm_workorder WHERE id >= $start_from";
			$sql = "SELECT id, status FROM fm_workorder WHERE id IN (45000012,45000106,45000111,45000114,45000134,45000137,45000138,45000139,45000283,45000332,45000414,45000455,45000456,45000515,45000524,45000525,45000577,45000623,45000624,45000838,45000860,45001028,45001031,45001033,45001297,45001380,45001384,45001407,45001498,45001500,45001521,45001595,45001620,45001712,45001824,45001831,45001861,45001909,45001922,45001958,45001959,45002045,45002084,45002086,45002350,45002481,45002569,45002716,45002854,45002873,45002944,45003056,45003132,45003405,45003626,45003695,45003699,45003701,45003703,45003707,45003752,45003753,45003754,45003816,45003940,45003941,45004107)";

			$db = & $GLOBALS['phpgw']->db;
			$db->query($sql, __LINE__, __FILE__);
			$ids = array();

			while ($db->next_record())
			{
				$status = $db->f('status');
				if($status == 'Avbrutt' || $status == 'Dublisert')
				{
					phpgwapi_cache::message_set("Hopper over [{$status}]: " . $db->f('id') , 'error');
				}
				else
				{
					$ids[] = $db->f('id');
				}
			}

			foreach ($ids as $workorder_id)
			{
				try
				{
					$this->send_order( $workorder_id );
				}
				catch (Exception $e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
				}
			}
		}

		function send_order( $workorder_id )
		{
			$workorder = $this->boworkorder->read_single($workorder_id);
			$show_cost = false;
			$email_receipt = true;

			try
			{
				$pdfcode = $this->pdf_order($workorder_id, $show_cost);
			}
			catch (Exception $e)
			{
				phpgwapi_cache::message_set($e->getMessage(), 'error');
				throw $e;
			}

			$criteria = array
			(
				'appname' => 'property',
				'location' => '.project.workorder.transfer',
				'allrows' => true
			);

			$transfer_action = 'workorder';

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/property/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require $file;
				}
			}

			$dir = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/pdf_files";
			$attachments = array();
			if (!empty($workorder['file_attachments']) && is_array($workorder['file_attachments']))
			{
				$attachments = CreateObject('property.bofiles')->get_attachments($workorder['file_attachments']);
				$_attachment_log = array();
				foreach ($attachments as $_attachment)
				{
					$_attachment_log[] = $_attachment['name'];
				}
				$attachment_log = ' ' . lang('attachments') . ' : ' . implode(', ', $_attachment_log);
			}

			//save the file
			if (!file_exists($dir))
			{
				mkdir($dir, 0777);
			}
			$fname = tempnam($dir . '/', 'PDF_') . '.pdf';
			$fp = fopen($fname, 'w');
			fwrite($fp, $pdfcode);
			fclose($fp);

			$attachments[] = array
				(
				'file' => $fname,
				'name' => "order_{$workorder_id}.pdf",
				'type' => 'application/pdf'
			);

			$_to = isset($workorder['mail_recipients'][0]) && $workorder['mail_recipients'][0] ? implode(';', $workorder['mail_recipients']) : '';
//			_debug_array($_to);die();
			$GLOBALS['phpgw']->preferences->set_account_id($workorder['user_id'], true);

			$from_name = $GLOBALS['phpgw']->accounts->get($workorder['user_id'])->__toString();
			$from_email = "{$from_name}<{$GLOBALS['phpgw']->preferences->data['property']['email']}>";
			$bcc = !empty($GLOBALS['phpgw']->preferences->data['property']['email']) ? $from_email : '';

			$subject = lang('Workorder') . ": " . $workorder_id;

			$address_element = execMethod('property.botts.get_address_element', $workorder['location_code']);
			$_address = array();
			foreach ($address_element as $entry)
			{
				$_address[] = "{$entry['text']}: {$entry['value']}";
			}

			if ($_address)
			{
				$_address_txt = $_address ? implode(', ', $_address) : '';
			}

			$body = lang('order') . " {$workorder_id}.</br></br>{$_address_txt}</br></br>" . lang('see attachment');

			if (!is_object($GLOBALS['phpgw']->send))
			{
				$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
			}

			$_status = isset($this->config->config_data['workorder_ordered_status']) && $this->config->config_data['workorder_ordered_status'] ? $this->config->config_data['workorder_ordered_status'] : 0;

			if (!$_status)
			{
				phpgwapi_cache::message_set("Automatisk endring av status for bestilt er ikke konfigurert", 'error');
			}

			try
			{
				$GLOBALS['phpgw']->send->msg('email', $_to, $subject, $body, '', $cc, $bcc, $from_email, $from_name, 'html', '', $attachments, $email_receipt);
				phpgwapi_cache::message_set(lang('Workorder %1 is sent by email to %2', $workorder_id, $_to),'message');
				phpgwapi_cache::message_set(lang('%1 is notified', $bcc),'message');
			}
			catch (Exception $e)
			{
				if ($e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
					phpgwapi_cache::message_set("Bestilling {$workorder_id} er ikke sendt", 'error');
					throw $e;
				}
			}

			if($_status)
			{
				try
				{
					execMethod('property.soworkorder.update_status', array('order_id' => $workorder_id,
						'status' => $_status));
				}
				catch (Exception $e)
				{
					if ($e)
					{
						throw $e;
					}
				}
			}

			$_attachment_log = $attachment_log ? "::$attachment_log" : '';
			$historylog = CreateObject('property.historylog', 'workorder');
			$historylog->add('M', $workorder_id, "{$_to}{$_attachment_log}");
		}

		function get_gab_id( $location_code )
		{
			$formatted_gab_id = '';
			$gabinfos = execMethod('property.sogab.read', array('location_code' => $location_code,
				'allrows' => true));
			if ($gabinfos != null && is_array($gabinfos) && count($gabinfos) == 1)
			{
				$gabinfo = array_shift($gabinfos);
				$gab_id = $gabinfo['gab_id'];
			}

			$formatted_gab_id = '';
			if (isset($gab_id))
			{
				$formatted_gab_id = substr($gab_id, 4, 5) . ' / ' . substr($gab_id, 9, 4) . ' / ' . substr($gab_id, 13, 4) . ' / ' . substr($gab_id, 17, 3);
			}
			return $formatted_gab_id;
		}


		private function _validate_purchase_grant( $id, $ecodimb, $project_id )
		{

			$approval_level = !empty($this->config->config_data['approval_level']) ? $this->config->config_data['approval_level'] : 'order';

			$_accumulated_budget_amount = 0;
			if($approval_level == 'project')
			{
				$_budget_amount = $this->boworkorder->get_accumulated_budget_amount($project_id);
			}
			else
			{
				$_budget_amount = $this->boworkorder->get_budget_amount($id);
			}

			try
			{
				$purchase_grant_ok = CreateObject('property.botts')->validate_purchase_grant( $ecodimb, $_budget_amount, $id);
			}
			catch (Exception $ex)
			{
				throw $ex;
			}

			return $purchase_grant_ok;
		}
	}