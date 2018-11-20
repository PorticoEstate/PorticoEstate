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
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uilookup extends phpgwapi_uicommon_jquery
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
			'addressbook' => true,
			'organisation' => true,
			'vendor' => true,
			'b_account' => true,
			'location' => true,
			'entity' => true,
			'ns3420' => true,
			'street' => true,
			'tenant' => true,
			'phpgw_user' => true,
			'external_project' => true,
			'ecodimb' => true,
			'order_template' => true,
			'response_template' => true,
			'custom' => true
		);

		function __construct()
		{
			parent::__construct();

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->bo = CreateObject('property.bolookup', true);

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->cat_id = $this->bo->cat_id;
			$this->part_of_town_id = $this->bo->part_of_town_id;
			$this->district_id = $this->bo->district_id;

			if (!isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css))
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
		}

		public function query()
		{

		}

		function addressbook()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$search = phpgw::get_var('search');
				$order = phpgw::get_var('order');
				$draw = phpgw::get_var('draw', 'int');
				$columns = phpgw::get_var('columns');
				$order_field = '';

				switch ($columns[$order[0]['column']]['data'])
				{
					case 'lastname':
						$order_field = 'account_lastname';
						break;
					case 'firstname':
						$order_field = 'account_firstname';
						break;
					default:
						$order_field = "account_lastname";
				}

				$params = array(
					'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
					'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
					'query' => $search['value'],
					'order' => $order_field,
					'sort' => strtoupper($order[0]['dir']),
					'dir' => $order[0]['dir'],
					'allrows' => phpgw::get_var('length', 'int') == -1,
					'filter' => '',
					'offset' => 0
				);

				$result_data = array('results' =>  $this->bo->read_addressbook($params));

				$result_data['total_records'] = $this->bo->total_records;
				$result_data['draw'] = $draw;

				$ret = $this->jquery_results($result_data);
				return $ret;
			}

			$this->cats = CreateObject('phpgwapi.categories', -1, 'addressbook');
			$this->cats->supress_info = true;

			$column = phpgw::get_var('column');

			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['default_category'];

			if ($default_category && !isset($_REQUEST['cat_id']))
			{
				$this->bo->cat_id = $default_category;
				$this->cat_id = $default_category;
			}


			if ($column)
			{
				$contact_id = $column;
				$contact_name = $column . '_name';
			}
			else
			{
				$contact_id = 'contact_id';
				$contact_name = 'contact_name';
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("' . $contact_id . '")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("' . $contact_name . '")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("' . $contact_id . '")[0].value = aData["contact_id"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("' . $contact_name . '")[0].value = aData["fullname"];' . "\r\n";
			//trigger ajax-call
			$action .= "parent.document.getElementsByName('{$contact_id}')[0].setAttribute('{$contact_id}','{$contact_id}',0);\r\n";

			$action .= <<<JS
   try
				{
					window.parent.on_contact_updated(aData["contact_id"]);
				}
				catch(err)
				{}
				parent.JqueryPortico.onPopupClose("close");
JS;


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
						'menuaction' => 'property.uilookup.addressbook',
						'query' => $this->query,
						'filter' => $this->filter,
						'cat_id' => $this->cat_id,
						'column' => $column,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'name' => array('id', 'lid', 'lastname','firstname' ,'email', 'mobile'),
				'sort_field' => array('id', 'lid', 'lastname','firstname' ,'email', 'mobile'),
				'sortable' => array(false, true, true, true, false, false, false),
				'formatter' => array('', '', '', '', '', '', ''),
				'descr' => array(lang('ID'), lang('lid'), lang('lastname'),lang('firstname'), lang('email'),  lang('mobile'))
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

			$appname = lang('addressbook');
			$function_msg = lang('list contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function organisation()
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

				$values = $this->bo->read_organisation($params);

				/**
				 * Sigurd: For some reason - this one starts on 1 - not 0 as it is supposed to.
				 */
				$results = array();
				foreach ($values as $entry)
				{
					$results[] = $entry;
				}
				$result_data = array('results' => $results);

				$result_data['total_records'] = $this->bo->total_records;
				$result_data['draw'] = $draw;

				$ret = $this->jquery_results($result_data);
				return $ret;
			}

			$this->cats = CreateObject('phpgwapi.categories', -1, 'addressbook');
			$this->cats->supress_info = true;

			$column = phpgw::get_var('column');

			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['default_category'];

			if ($default_category && !isset($_REQUEST['cat_id']))
			{
				$this->bo->cat_id = $default_category;
				$this->cat_id = $default_category;
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			if ($column)
			{
				$contact_id = $column;
				$contact_name = $column . '_name';
			}
			else
			{
				$contact_id = 'contact_id';
				$contact_name = 'contact_name';
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("' . $contact_id . '")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("' . $contact_name . '")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("' . $contact_id . '")[0].value = aData["contact_id"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("' . $contact_name . '")[0].value = aData["org_name"];' . "\r\n";
			//trigger ajax-call
			$action .= "parent.document.getElementsByName('{$contact_id}')[0].setAttribute('{$contact_id}','{$contact_id}',0);\r\n";

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
						'menuaction' => 'property.uilookup.organisation',
						'query' => $this->query,
						'filter' => $this->filter,
						'cat_id' => $this->cat_id,
						'column' => $column,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$values_combo_box = $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,
				'globals' => true));
			foreach ($values_combo_box['cat_list'] as &$val)
			{
				$val['id'] = $val['cat_id'];
			}
			$default_value = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box['cat_list'], $default_value);

			$filter = array('type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('Category'),
				'list' => $values_combo_box['cat_list']
			);

			array_unshift($data['form']['toolbar']['item'], $filter);

			$uicols = array(
				'name' => array('contact_id', 'org_name', 'email', 'wphone'),
				'sort_field' => array('person_id', 'last_name', '', ''),
				'sortable' => array(true, true, false, false),
				'formatter' => array('', '', '', '', ''),
				'descr' => array(lang('ID'), lang('Name'), lang('email'), lang('phone'))
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

			$appname = lang('addressbook');
			$function_msg = lang('list contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function vendor()
		{
			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['property']['default_vendor_category'];

			if ($default_category && !isset($_REQUEST['cat_id']))
			{
				$this->bo->cat_id = $default_category;
				$this->cat_id = $default_category;
			}

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
					'filter' => array()
				);

				$values = $this->bo->read_vendor($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $this->bo->total_records,
					'draw' => $draw
				);

				return $this->jquery_results($result_data);
			}

			$this->cats = CreateObject('phpgwapi.categories', -1, 'property', '.vendor');

			$column = phpgw::get_var('column');

			if ($column)
			{
				$contact_id = $column;
				$org_name = $column . '_org_name';
			}
			else
			{
				$contact_id = 'vendor_id';
				$org_name = 'vendor_name';
			}

			$action = <<<JS
			parent.document.getElementsByName("{$contact_id}")[0].value = "";
			parent.document.getElementsByName("{$org_name}")[0].value = "";
			parent.document.getElementsByName("{$contact_id}")[0].value = aData["id"];
			parent.document.getElementsByName("{$org_name}")[0].value = aData["org_name"];
JS;
			if ($contact_id == 'vendor_id')
			{
				$action .= <<<JS
				parent.document.getElementsByName("{$contact_id}")[0].setAttribute("vendor_id","{$contact_id}",0);
				parent.document.getElementsByName("{$contact_id}")[0].removeAttribute("vendor_id");
JS;
			}

			$action .= <<<JS
				try
				{
					window.parent.on_vendor_updated();
				}
				catch(err)
				{}
				parent.JqueryPortico.onPopupClose("close");
JS;

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
						'menuaction' => 'property.uilookup.vendor',
						'query' => $this->query,
						'filter' => $this->filter,
						'column' => $column,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$values_combo_box = $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,
				'globals' => true));
			foreach ($values_combo_box['cat_list'] as &$val)
			{
				$val['id'] = $val['cat_id'];
			}
			$default_value = array('id' => '', 'name' => lang('no category'));
			array_unshift($values_combo_box['cat_list'], $default_value);

			$filter = array('type' => 'filter',
				'name' => 'cat_id',
				'text' => lang('Category'),
				'list' => $values_combo_box['cat_list']
			);

			array_unshift($data['form']['toolbar']['item'], $filter);

			$uicols = array(
				'input_type' => array('text', 'text', 'text'),
				'name' => array('id', 'org_name', 'status'),
				'formatter' => array('', '', ''),
				'descr' => array(lang('ID'), lang('Name'), lang('status')),
				'sortable' => array(true, true, false),
				'dir' => array(false, "asc", false)

			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => $uicols['sortable'][$k],
					'hidden' => false,
					'dir' => $uicols['dir'][$k] ? $uicols['dir'][$k] : '',
				);

				array_push($data['datatable']['field'], $params);
			}

			$appname = lang('vendor');
			$function_msg = lang('list vendors');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function b_account()
		{
			$role = phpgw::get_var('role');

			$cat_id = phpgw::get_var('cat_id', 'int', 'POST');

			if (isset($_POST['cat_id']))
			{
				$parent = $cat_id;
			}
			else
			{
				$parent = phpgw::get_var('parent');
			}

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
					'filter' => '',
					'role' => $role,
					'parent' => $parent
				);

				$values = $this->bo->read_b_account($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $this->bo->total_records,
					'draw' => $draw
				);

				return $this->jquery_results($result_data);
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("b_account_id")[0].value = "";' . "\r";
			$action .= 'parent.document.getElementsByName("b_account_name")[0].value = "";' . "\r";
			$action .= 'parent.document.getElementsByName("b_account_id")[0].value = aData["id"];' . "\r";
			$action .= 'parent.document.getElementsByName("b_account_name")[0].value = aData["descr"];' . "\r";

//			$action .= 'parent.document.getElementsByName("b_account_id")[0].setAttribute("b_account_id","b_account_id",0);'."\r";
//			$action .= 'parent.document.getElementsByName("b_account_id")[0].removeAttribute("b_account_id");'."\r";

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
						'menuaction' => 'property.uilookup.b_account',
						'query' => $this->query,
						'filter' => $this->filter,
						'role' => $role,
						'parent' => $parent,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			if ($role != 'group')
			{
				$values_combo_box = execMethod('property.bogeneric.get_list', array('type' => 'b_account',
					'selected' => $parent, 'filter' => array('active' => 1)));
				$default_value = array('id' => '', 'name' => lang('select'));
				array_unshift($values_combo_box, $default_value);

				$filter = array('type' => 'filter',
					'name' => 'cat_id',
					'text' => lang('Category'),
					'list' => $values_combo_box
				);

				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$uicols = array(
				'input_type' => array('text', 'text'),
				'name' => array('id', 'descr'),
				'formatter' => array('', ''),
				'descr' => array(lang('ID'), lang('Name'))
			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => true,
					'hidden' => false
				);

				array_push($data['datatable']['field'], $params);
			}

			$appname = lang('vendor');
			$function_msg = lang('list vendors');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function street()
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
				$bo = CreateObject('property.bogeneric');
				$bo->get_location_info('street');
				$values = $bo->read($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'parent.document.getElementsByName("street_id")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("street_name")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("street_id")[0].value = aData["id"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("street_name")[0].value = aData["descr"];' . "\r\n";
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
						'menuaction' => 'property.uilookup.street',
						'type' => 'street',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'name' => array('id', 'descr'),
				'formatter' => array('', ''),
				'descr' => array(lang('ID'), lang('Street name'))
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

			$appname = lang('street');
			$function_msg = lang('list street');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function tenant()
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
				$bo = CreateObject('property.bogeneric');
				$bo->get_location_info('tenant');
				$values = $bo->read($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'parent.document.getElementsByName("tenant_id")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("last_name")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("first_name")[0].value = "";' . "\r\n";

			$action .= 'parent.document.getElementsByName("tenant_id")[0].value = aData["id"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("last_name")[0].value = aData["last_name"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("first_name")[0].value = aData["first_name"];' . "\r\n";

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
						'menuaction' => 'property.uilookup.tenant',
						'type' => 'tenant',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => false,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'name' => array('id', 'last_name', 'first_name'),
				'formatter' => array('', '', ''),
				'sortable' => array(true, true, true),
				'descr' => array(lang('ID'), lang('last name'), lang('first name'))
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

			$appname = lang('tenant');
			$function_msg = lang('list tenant');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function ns3420()
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
				$bo = CreateObject('property.bogeneric');
				$bo->get_location_info('ns3420');
				$values = $bo->read($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'parent.document.getElementsByName("ns3420_id")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("ns3420_descr")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("ns3420_id")[0].value = aData["num"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("ns3420_descr")[0].value = aData["tekst1"];' . "\r\n";
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
						'menuaction' => 'property.uilookup.ns3420',
						'query' => $this->query,
						'filter' => $this->filter,
						'cat_id' => $this->cat_id,
						'type' => 'ns3420',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'input_type' => array('text', 'text'),
				'name' => array('num', 'tekst1'),
				'sortable' => array(true, true),
				'descr' => array(lang('ID'), lang('ns3420 description'))
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
			$function_msg = lang('list order template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function entity()
		{
			$bocommon = CreateObject('property.bocommon');
			$boentity = CreateObject('property.boentity');
			$boadmin_entity = CreateObject('property.boadmin_entity');
			$this->start = $boentity->start;
			$this->query = $boentity->query;
			$this->sort = $boentity->sort;
			$this->order = $boentity->order;
			$this->filter = $boentity->filter;
			$this->cat_id = $boentity->cat_id;
			$this->part_of_town_id = $boentity->part_of_town_id;
			$this->district_id = $boentity->district_id;
			$this->entity_id = $boentity->entity_id;
			$this->location_code = $boentity->location_code;
			$this->criteria_id = $boentity->criteria_id;

			$default_district = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'] : '');

			if ($default_district && ! isset($_REQUEST['district_id']))
			{
				$this->bo->district_id = $default_district;
				$this->district_id = $default_district;
			}


			$input_name = phpgwapi_cache::session_get('property', 'lookup_fields');
			$input_name_entity = phpgwapi_cache::session_get('property', 'lookup_fields_entity');
			$input_name = $input_name ? $input_name : array();
			$input_name_entity = $input_name_entity ? $input_name_entity : array();

			$input_name = array_merge($input_name, $input_name_entity);

			$action = '';
			for ($i = 0; $i < count($input_name); $i++)
			{
				$action .= "parent.document.getElementsByName('{$input_name[$i]}')[0].value = ''; \r\n";
			}
			for ($i = 0; $i < count($input_name); $i++)
			{
				$action .= "if (typeof aData['{$input_name[$i]}'] !== 'undefined'){ parent.document.getElementsByName('{$input_name[$i]}')[0].value = aData['{$input_name[$i]}']; } \r\n";
			}
			$action .= 'parent.JqueryPortico.onPopupClose("close");' . "\r";

			$values = $boentity->read(array('lookup' => true, 'dry_run' => true));
			$uicols = $boentity->uicols;

			if (count($uicols['name']) > 0)
			{
				for ($m = 0; $m < count($input_name); $m++)
				{
					if (!array_search($input_name[$m], $uicols['name']))
					{
						$uicols['name'][] = $input_name[$m];
						$uicols['descr'][] = '';
						$uicols['input_type'][] = 'hidden';
					}
				}
			}
			else
			{
				$uicols['name'][] = 'num';
				$uicols['descr'][] = 'ID';
				$uicols['input_type'][] = 'text';
			}


			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				if (phpgw::get_var('head'))
				{
					$entity_def = array();
					$head = '<thead>';
					$count_uicols_name = count($uicols['name']);
					for ($k = 0; $k < $count_uicols_name; $k++)
					{
						$params = array(
							'key' => $uicols['name'][$k],
							'label' => $uicols['descr'][$k],
							'sortable' => false,
							'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
						);

						if ($uicols['name'][$k] == 'loc1' || $uicols['name'][$k] == 'num')
						{
							$params['sortable'] = true;
						}

						array_push($entity_def, $params);

						if ($uicols['input_type'][$k] != 'hidden')
						{
							$head .= '<th>' . $uicols['descr'][$k] . '</th>';
						}
					}
					$head .= '</thead>';

					$datatable_def = array
						(
						'container' => 'datatable-container',
						'requestUrl' => self::link(array(
							'menuaction' => 'property.uilookup.entity',
							'cat_id' => $this->cat_id,
							'entity_id' => $this->entity_id,
							'district_id' => $this->district_id,
							'criteria_id' => $this->criteria_id,
							'phpgw_return_as' => 'json'
						)),
						'ColumnDefs' => $entity_def
					);

					$data = array
						(
						'datatable_def' => $datatable_def,
						'datatable_head' => $head,
					);

					return $data;
				}
				else
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
						'allrows' => phpgw::get_var('length', 'int') == -1,
						'lookup' => true
					);

					$values = $boentity->read($params);

					$result_data = array('results' => $values);

					$result_data['total_records'] = $boentity->total_records;
					$result_data['draw'] = $draw;

					return $this->jquery_results($result_data);
				}
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

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
						'menuaction' => 'property.uilookup.entity',
						'second_display' => 1,
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'query' => $this->location_code,
					'field' => array()
				)
			);

			$values_combo_box[0] = $boentity->select_category_list('filter', $this->cat_id);
			array_unshift($values_combo_box[0], array('id' => '', 'name' => lang('no category')));
			$filters[0] = array('type' => 'filter-category',
				'name' => 'cat_id',
				'text' => lang('category'),
				'list' => $values_combo_box[0]
			);

			$values_combo_box[1] = $bocommon->select_district_list('filter', $this->district_id);
			array_unshift($values_combo_box[1], array('id' => '', 'name' => lang('no district')));
			$filters[1] = array('type' => 'filter',
				'name' => 'district_id',
				'text' => lang('district'),
				'list' => $values_combo_box[1]
			);

			$values_combo_box[2] = $boentity->get_criteria_list($this->criteria_id);
			array_unshift($values_combo_box[2], array('id' => '', 'name' => lang('no criteria')));
			$filters[2] = array('type' => 'filter',
				'name' => 'criteria_id',
				'text' => lang('search criteria'),
				'list' => $values_combo_box[2]
			);

			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}


			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if ($uicols['name'][$k] == 'loc1' || $uicols['name'][$k] == 'num')
				{
					$params['sortable'] = true;
				}

				array_push($data['datatable']['field'], $params);
			}

			if ($this->entity_id)
			{
				$entity = $boadmin_entity->read_single($this->entity_id, false);
				$appname = $entity['name'];
			}
			if ($this->cat_id)
			{
				$category = $boadmin_entity->read_single_category($this->entity_id, $this->cat_id);
				$function_msg = lang('lookup') . ' ' . $category['name'];
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('lookup.entity', $data);
		}

		function phpgw_user()
		{
			$column = phpgw::get_var('column');
			$acl_app = phpgw::get_var('acl_app');
			$acl_location = phpgw::get_var('acl_location');
			$acl_required = phpgw::get_var('acl_required', 'int');

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$search = phpgw::get_var('search');
				$order = phpgw::get_var('order');
				$draw = phpgw::get_var('draw', 'int');
				$columns = phpgw::get_var('columns');

				switch ($columns[$order[0]['column']]['data'])
				{
					case 'id':
						$ordering = 'account_id';
						break;
					case 'first_name':
						$ordering = 'account_firstname';
						break;
					case 'last_name':
						$ordering = 'account_lastname';
						break;
					default:
						$ordering = "";
				}
				$params = array(
					'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
					'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
					'query' => $search['value'],
					'order' => $ordering,
					'sort' => $order[0]['dir'],
					'dir' => $order[0]['dir'],
					'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
					'allrows' => phpgw::get_var('length', 'int') == -1,
					'acl_app' => $acl_app,
					'acl_location' => $acl_location,
					'acl_required' => $acl_required
				);

				$values = $this->bo->read_phpgw_user($params);

				$result_data = array('results' => $values);

				$result_data['total_records'] = $this->bo->total_records;
				$result_data['draw'] = $draw;

				return $this->jquery_results($result_data);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			if ($column)
			{
				$user_id = $column;
				$user_name = $column . '_user_name';
			}
			else
			{
				$user_id = 'user_id';
				$user_name = 'user_name';
			}

			$action = '';
			$action .= 'parent.document.getElementById("' . $user_id . '").value = "";' . "\r";
			$action .= 'parent.document.getElementById("' . $user_name . '").value = "";' . "\r";
			$action .= 'parent.document.getElementById("' . $user_id . '").value = aData["id"];' . "\r";
			$action .= 'parent.document.getElementById("' . $user_name . '").value = aData["first_name"] + " " + aData["last_name"];' . "\r";
			$action .= 'window.parent.JqueryPortico.onPopupClose("close");' . "\r";

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
						'menuaction' => 'property.uilookup.phpgw_user',
						'second_display' => true,
						'cat_id' => $this->cat_id,
						'query' => $this->query,
						'filter' => $this->filter,
						'column' => $column,
						'acl_app' => $acl_app,
						'acl_location' => $acl_location,
						'acl_required' => $acl_required,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => false,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'input_type' => array('text', 'hidden', 'text', 'text'),
				'name' => array('id', 'account_lid', 'first_name', 'last_name'),
				'sort_field' => array('account_id', 'account_lid', 'account_firstname', 'account_lastname'),
				'descr' => array(lang('ID'), '', lang('first name'), lang('last name'))
			);

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => true,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				array_push($data['datatable']['field'], $params);
			}

			$appname = lang('standard description');
			$function_msg = lang('list standard description');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function external_project()
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
				$bo = CreateObject('property.bogeneric');
				$bo->get_location_info('external_project');
				$values = $bo->read($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'parent.document.getElementsByName("external_project_id")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("external_project_name")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("external_project_id")[0].value = aData["id"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("external_project_name")[0].value = aData["name"];' . "\r\n";
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
						'menuaction' => 'property.uilookup.external_project',
						'query' => $this->query,
						'type' => 'external_project',
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'name' => array('id', 'name', 'budget'),
				'sortable' => array(true, false, true),
				'formatter' => array('', '', 'JqueryPortico.FormatterRight'),
				'descr' => array(lang('ID'), lang('Name'), lang('budget'))
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

			$appname = lang('external project');
			$function_msg = lang('list external project');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function ecodimb()
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

				$values = $this->bo->read_ecodimb($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $this->bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("ecodimb")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("ecodimb_descr")[0].value = "";' . "\r\n";
			$action .= 'parent.document.getElementsByName("ecodimb")[0].value = aData["id"];' . "\r\n";
			$action .= 'parent.document.getElementsByName("ecodimb_descr")[0].value = aData["descr"];' . "\r\n";
			//trigger ajax-call
			$action .= "parent.document.getElementsByName('ecodimb')[0].setAttribute('ecodimb','ecodimb',0);\r\n";

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
						'menuaction' => 'property.uilookup.ecodimb',
						'query' => $this->query,
						'filter' => $this->filter,
						'cat_id' => $this->cat_id,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array(
				'name' => array('id', 'descr'),
				'sortable' => array(true, true),
				'formatter' => array('', ''),
				'descr' => array(lang('ID'), lang('Name'))
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

			$appname = lang('ecodimb');
			$function_msg = lang('lookup');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function order_template()
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
				$bo = CreateObject('property.bogeneric');
				$bo->get_location_info('order_template');
				$values = $bo->read($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'var temp = parent.document.getElementsByName("values[order_descr]")[0].value;' . "\r\n";
			$action .= 'if(temp){temp = temp + "\n";}' . "\r\n";
			$action .= 'parent.document.getElementsByName("values[order_descr]")[0].value = temp + aData["content"];' . "\r\n";
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
						'menuaction' => 'property.uilookup.order_template',
						'query' => $this->query,
						'filter' => $this->filter,
						'cat_id' => $this->cat_id,
						'type' => 'order_template',
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
			$function_msg = lang('list order template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function response_template()
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
				$bo = CreateObject('property.bogeneric');
				$bo->get_location_info('response_template');
				$values = $bo->read($params);

				$result_data = array
					(
					'results' => $values,
					'total_records' => $bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'var temp = parent.document.getElementsByName("values[response_text]")[0].value;' . "\r\n";
			$action .= 'if(temp){temp = temp + "\n";}' . "\r\n";
			$action .= 'parent.document.getElementsByName("values[response_text]")[0].value = temp + aData["content"];' . "\r\n";
			$action .= 'parent.SmsCountKeyUp(160);' . "\r\n";

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
						'menuaction' => 'property.uilookup.response_template',
						'query' => $this->query,
						'filter' => $this->filter,
						'cat_id' => $this->cat_id,
						'type' => 'response_template',
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
			$function_msg = lang('list response template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function custom()
		{
			$type = phpgw::get_var('type');
			$column = phpgw::get_var('column');

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
					'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
					'allrows' => phpgw::get_var('length', 'int') == -1
				);

				$bogeneric = CreateObject('property.bogeneric');
				$bogeneric->get_location_info(phpgw::get_var('type', 'string'));
				$values = $bogeneric->read($params);

				$result_data = array('results' => $values);

				$result_data['total_records'] = $bogeneric->total_records;
				$result_data['draw'] = $draw;

				return $this->jquery_results($result_data);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$custom_id = $column;
			$custom_name = "label_{$column}";

			$action = '';
			$action .= 'window.parent.document.getElementById("' . $custom_id . '").value = "";' . "\r";
			$action .= 'window.parent.document.getElementById("' . $custom_name . '").innerHTML = "";' . "\r";
			$action .= 'window.parent.document.getElementById("' . $custom_id . '").value = aData["id"];' . "\r";
			$action .= 'window.parent.document.getElementById("' . $custom_name . '").innerHTML = aData["name"];' . "\r";
			$action .= 'window.parent.JqueryPortico.onPopupClose("close");' . "\r";
			$action .= 'window.parent.filterData("' . $custom_id . '", aData["id"]);';

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
						'menuaction' => 'property.uilookup.custom',
						'cat_id' => $this->cat_id,
						'filter' => $this->filter,
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$bogeneric = CreateObject('property.bogeneric');
			$bogeneric->get_location_info(phpgw::get_var('type', 'string'));
			$values = $bogeneric->read();

			$uicols = $bogeneric->uicols;

			$count_uicols_name = count($uicols['name']);

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => false,
					'hidden' => false
				);

				array_push($data['datatable']['field'], $params);
			}

			$appname = lang('template');
			$function_msg = lang('list order template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}
	}