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
	 * @version $Id: class.uilookup.inc.php 11909 2014-04-13 16:49:53Z sigurdne $
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class controller_uilookup extends phpgwapi_uicommon_jquery
	{

		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $public_functions = array
			(
			'control' => true,
			'query' => true
		);

		function __construct()
		{
			parent::__construct();
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;


			if (!isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
		}

		function control()
		{
			$action = 'parent.document.getElementById("control_id").value = "";' . "\r\n";
			$action .= 'parent.document.getElementById("control_name").value = "";' . "\r\n";
			$action .= 'parent.document.getElementById("control_id").value = aData["id"];' . "\r\n";
			$action .= 'parent.document.getElementById("control_name").value = aData["title"];' . "\r\n";
			$action .= 'parent.JqueryPortico.onPopupClose("close");' . "\r";

			$appname = lang('controller');
			$function_msg = 'lookup';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$data = array(
				'left_click_action' => $action,
				'datatable_name' => $appname,
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'controller.uilookup.query',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'name' => array('id', 'title'),
				'sortable' => array(true, true),
				'formatter' => array('', ''),
				'descr' => array(lang('ID'), lang('title'))
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


			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			$values = array();
			$uicontrol = CreateObject('controller.uicontrol');
			$values = $uicontrol->query();
			return $values;
		}
	}