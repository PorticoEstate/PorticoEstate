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
	 * @subpackage core
	 * @version $Id: class.uilookup.inc.php 15854 2016-10-19 11:39:12Z sigurdne $
	 */
	/**
	 * Description
	 * @package rental
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class rental_uilookup extends phpgwapi_uicommon_jquery
	{

		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $district_id;
		var $sub;
		var $currentapp;
		var $public_functions = array
			(
			'phpgw_user' => true,
			'external_project' => true,
			'ecodimb' => true,
			'order_template' => true,
			'email_template' => true,
			'custom' => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
		}

		public function query()
		{

		}


		function email_template()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$search = phpgw::get_var('search');
				$order = phpgw::get_var('order');
				$draw = phpgw::get_var('draw', 'int');
				$columns = phpgw::get_var('columns');

				$params = array(
					'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
					'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
					'query' => $search['value'],
					'order' => $columns[$order[0]['column']]['data'],
					'sort' => $order[0]['dir'],
					'dir' => $order[0]['dir'],
					'allrows' => phpgw::get_var('length', 'int') == -1,
					'filter' => ''
				);

				$values = array();
				$bo = CreateObject('rental.bogeneric');
				$bo->get_location_info('email_template');
				$values = $bo->read($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'var temp = parent.document.getElementById("content").value;' . "\r\n";
			$action .= 'if(temp){temp = temp + "\n";}' . "\r\n";
			$action .= 'parent.document.getElementById("content").value = temp + aData["content"];' . "\r\n";
			$action .= 'parent.JqueryPortico.onPopupClose("close");' . "\r";

			$data = array(
				'left_click_action' => $action,
				'datatable_name' => '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uilookup.email_template',
						'query' => $this->query,
						'filter' => $this->filter,
						'cat_id' => $this->cat_id,
						'type' => 'email_template',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'input_type' => array('text', 'text', 'text'),
				'name' => array('id', 'name', 'content'),
				'formatter' => array('', '', ''),
				'descr' => array(lang('ID'), lang('name'), lang('content'))
			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => $uicols['sortable'][$k],
					'hidden' => false
				);

				array_push($data['datatable']['field'], $params);
			}

			$appname = lang('template');
			$function_msg = lang('list email template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}
	}