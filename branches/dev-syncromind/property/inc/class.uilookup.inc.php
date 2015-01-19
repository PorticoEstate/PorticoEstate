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
				'addressbook'		=> true,
				'organisation'		=> true,
				'vendor'			=> true,
				'b_account'			=> true,
				'location'			=> true,
				'entity'			=> true,
				'ns3420'			=> true,
				'street'			=> true,
				'tenant'			=> true,
				'phpgw_user'		=> true,
				'project_group'		=> true,
				'ecodimb'			=> true,
				'order_template'	=> true,
				'response_template'	=> true,
				'custom'			=> true
			);

		function __construct()
		{
			parent::__construct();
			
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['headonly']=true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->bo					= CreateObject('property.bolookup',true);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}
			// Prepare CSS Style
			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

		}

		public function query()
		{
		}
		
		function save_sessiondata()
		{
			$data = array
				(
					'start'				=> $this->start,
					'query'				=> $this->query,
					'sort'				=> $this->sort,
					'order'				=> $this->order,
					'filter'			=> $this->filter,
					'cat_id'			=> $this->cat_id,
					'part_of_town_id'	=> $this->part_of_town_id,
					'district_id'		=> $this->district_id
				);
			$this->bo->save_sessiondata($data);
		}

		function addressbook()
		{
			if( phpgw::get_var('phpgw_return_as') == 'json' )
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

				$values = $this->bo->read_addressbook($params);
				/**
				 * Sigurd: For some reason - this one starts on 1 - not 0 as it is supposed to.
				 */
				$results = array();
				foreach($values as $entry)
				{
					$results[] = $entry;
				}
				$result_data = array('results' => $results);

				$result_data['total_records'] = $this->bo->total_records;
				$result_data['draw'] = $draw;

				$ret = $this->jquery_results($result_data);
				return $ret;
			}

			$this->cats		= CreateObject('phpgwapi.categories', -1,  'addressbook');
			$this->cats->supress_info	= true;

			$column = phpgw::get_var('column');

			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['default_category'];

			if ($default_category && !$second_display)
			{
				$this->bo->cat_id	= $default_category;
				$this->cat_id		= $default_category;
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			if($column)
			{
				$contact_id	=$column;
				$contact_name	=$column . '_name';
			}
			else
			{
				$contact_id	='contact_id';
				$contact_name	='contact_name';
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].value = "";' ."\r\n";
			$action .= 'parent.document.getElementsByName("'.$contact_name.'")[0].value = "";' ."\r\n";
			$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].value = aData["contact_id"];' ."\r\n";
			$action .= 'parent.document.getElementsByName("'.$contact_name.'")[0].value = aData["contact_name"];' ."\r\n";
			//trigger ajax-call
			$action .= "parent.document.getElementsByName('{$contact_id}')[0].setAttribute('{$contact_id}','{$contact_id}',0);\r\n";

			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";

			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.addressbook',
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'cat_id'			=> $this->cat_id,
							'column'			=> $column,
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$values_combo_box = $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,'globals' => true));
			foreach ($values_combo_box['cat_list'] as &$val)
			{
				$val['id'] = $val['cat_id'];
			}
			$default_value = array ('id'=>'', 'name'=>lang('no category'));
			array_unshift ($values_combo_box['cat_list'], $default_value);

			$filter = array('type' => 'filter',
						'name' => 'cat_id',
						'text' => lang('Category'),
						'list' => $values_combo_box['cat_list']
					);

			array_unshift ($data['form']['toolbar']['item'], $filter);

			$uicols = array (
				'name'			=>	array('contact_id','contact_name','email','wphone','mobile','is_user'),
				'sort_field'	=>	array('person_id','last_name','','','',''),
				'sortable'		=>	array(true,true,false,false,false,false),
				'formatter'		=>	array('','','','','',''),
				'descr'			=>	array(lang('ID'),lang('Name'),lang('email'),lang('phone'),lang('mobile'), lang('is user'))
			);

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => $uicols['sortable'][$k],
								'hidden' => false
							);

				array_push ($data['datatable']['field'], $params);
			}

			$appname						= lang('addressbook');
			$function_msg					= lang('list contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function organisation()
		{
			if( phpgw::get_var('phpgw_return_as') == 'json' )
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
				foreach($values as $entry)
				{
					$results[] = $entry;
				}
				$result_data = array('results' => $results);

				$result_data['total_records'] = $this->bo->total_records;
				$result_data['draw'] = $draw;

				$ret = $this->jquery_results($result_data);
				return $ret;
			}

			$this->cats		= CreateObject('phpgwapi.categories', -1,  'addressbook');
			$this->cats->supress_info	= true;

			$column = phpgw::get_var('column');

			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['addressbook']['default_category'];

			if ($default_category && !$second_display)
			{
				$this->bo->cat_id	= $default_category;
				$this->cat_id		= $default_category;
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			if($column)
			{
				$contact_id	=$column;
				$contact_name	=$column . '_name';
			}
			else
			{
				$contact_id	='contact_id';
				$contact_name	='contact_name';
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].value = "";' ."\r\n";
			$action .= 'parent.document.getElementsByName("'.$contact_name.'")[0].value = "";' ."\r\n";
			$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].value = aData["contact_id"];' ."\r\n";
			$action .= 'parent.document.getElementsByName("'.$contact_name.'")[0].value = aData["org_name"];' ."\r\n";
			//trigger ajax-call
			$action .= "parent.document.getElementsByName('{$contact_id}')[0].setAttribute('{$contact_id}','{$contact_id}',0);\r\n";

			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";

			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.organisation',
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'cat_id'			=> $this->cat_id,
							'column'			=> $column,
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$values_combo_box = $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,'globals' => true));
			foreach ($values_combo_box['cat_list'] as &$val)
			{
				$val['id'] = $val['cat_id'];
			}
			$default_value = array ('id'=>'', 'name'=>lang('no category'));
			array_unshift ($values_combo_box['cat_list'], $default_value);

			$filter = array('type' => 'filter',
						'name' => 'cat_id',
						'text' => lang('Category'),
						'list' => $values_combo_box['cat_list']
					);

			array_unshift ($data['form']['toolbar']['item'], $filter);

			$uicols = array (
				'name'			=>	array('contact_id','org_name','email','wphone'),
				'sort_field'	=>	array('person_id','last_name','',''),
				'sortable'		=>	array(true,true,false,false),
				'formatter'		=>	array('','','','',''),
				'descr'			=>	array(lang('ID'),lang('Name'),lang('email'),lang('phone'))
			);

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => $uicols['sortable'][$k],
								'hidden' => false
							);

				array_push ($data['datatable']['field'], $params);
			}

			$appname						= lang('addressbook');
			$function_msg					= lang('list contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}


		function vendor()
		{
			$default_category = $GLOBALS['phpgw_info']['user']['preferences']['property']['default_vendor_category'];

			if ($default_category)
			{
				$this->bo->cat_id	= $default_category;
				$this->cat_id		= $default_category;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
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

				$values = $this->bo->read_vendor($params);

				$result_data = array
				(
					'results'		=> $values,
					'total_records' => $this->bo->total_records,
					'draw'			=> $draw
				);

				return $this->jquery_results($result_data);
			}

			$this->cats		= CreateObject('phpgwapi.categories', -1,  'property', '.vendor');

			$column = phpgw::get_var('column');
			
			if($column)
			{
				$contact_id	=$column;
				$org_name	=$column . '_org_name';
			}
			else
			{
				$contact_id	='vendor_id';
				$org_name	='vendor_name';
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].value = "";'."\r";
			$action .= 'parent.document.getElementsByName("'.$org_name.'")[0].value = "";'."\r";
			$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].value = aData["id"];'."\r";
			$action .= 'parent.document.getElementsByName("'.$org_name.'")[0].value = aData["org_name"];'."\r";
			if($contact_id	== 'vendor_id')
			{
				$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].setAttribute("vendor_id","'.$contact_id.'",0);'."\r";
				$action .= 'parent.document.getElementsByName("'.$contact_id.'")[0].removeAttribute("vendor_id");'."\r";
			}
			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";
			
			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.vendor',
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'column'			=> $column,
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);
			
			$values_combo_box = $this->cats->formatted_xslt_list(array('selected' => $this->cat_id,'globals' => true));
			foreach ($values_combo_box['cat_list'] as &$val) 
			{
				$val['id'] = $val['cat_id'];
			}
			$default_value = array ('id'=>'', 'name'=>lang('no category'));
			array_unshift ($values_combo_box['cat_list'], $default_value);
			
			$filter = array('type' => 'filter',
						'name' => 'cat_id',
						'text' => lang('Category'),
						'list' => $values_combo_box['cat_list']
					);

			array_unshift ($data['form']['toolbar']['item'], $filter);
			
			$uicols = array (
				'input_type'	=>	array('text','text','text'),
				'name'			=>	array('id','org_name','status'),
				'formatter'		=>	array('','',''),
				'descr'			=>	array(lang('ID'),lang('Name'), lang('status'))
			);

			$count_uicols_name = count($uicols['name']);
	
			for($k=0;$k<$count_uicols_name;$k++)
			{						
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => false,
								'hidden' => false
							);
				
				array_push ($data['datatable']['field'], $params);
			}

			$appname = lang('vendor');
			$function_msg = lang('list vendors');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			
			self::render_template_xsl('datatable_jquery', $data);
		}


		function b_account()
		{
			$role = phpgw::get_var('role');
			$parent = phpgw::get_var('parent');

			$parent = $this->cat_id ? $this->cat_id : $parent;

			if( phpgw::get_var('phpgw_return_as') == 'json' )
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
					'role'	=> $role,
					'parent' => $parent
				);

				$values = $this->bo->read_b_account($params);

				$result_data = array
				(
					'results'		=> $values,
					'total_records' => $this->bo->total_records,
					'draw'			=> $draw
				);

				return $this->jquery_results($result_data);
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("b_account_id")[0].value = "";'."\r";
			$action .= 'parent.document.getElementsByName("b_account_name")[0].value = "";'."\r";
			$action .= 'parent.document.getElementsByName("b_account_id")[0].value = aData["id"];'."\r";
			$action .= 'parent.document.getElementsByName("b_account_name")[0].value = aData["descr"];'."\r";

//			$action .= 'parent.document.getElementsByName("b_account_id")[0].setAttribute("b_account_id","b_account_id",0);'."\r";
//			$action .= 'parent.document.getElementsByName("b_account_id")[0].removeAttribute("b_account_id");'."\r";

			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";

			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.b_account',
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'role'				=> $role,
							'parent'			=> $parent,
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			if ( $role != 'group' )
			{
				$values_combo_box = execMethod('property.bogeneric.get_list',array('type' => 'b_account','selected' => $parent,'filter' => array('active' =>1)));
				$default_value = array ('id'=>'','name'=> lang('select'));
				array_unshift ($values_combo_box,$default_value);

				$filter = array('type' => 'filter',
							'name' => 'cat_id',
							'text' => lang('Category'),
							'list' => $values_combo_box
						);

				array_unshift ($data['form']['toolbar']['item'], $filter);
			}

			$uicols = array (
				'input_type'	=>	array('text','text'),
				'name'			=>	array('id','descr'),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Name'))
			);

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => false,
								'hidden' => false
							);

				array_push ($data['datatable']['field'], $params);
			}

			$appname = lang('vendor');
			$function_msg = lang('list vendors');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}


		function street()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uilookup.street',
						'second_display'	=> true,
						'cat_id'			=> $this->cat_id,
						'query'				=> $this->query,
						'filter'			=> $this->filter
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.street',"
					."second_display:true,"
					."cat_id:'{$this->cat_id}',"
					."query:'{$this->query}',"
					."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uilookup.street',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//'',//$query,
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 1
								)
							),
							'hidden_value' => array
							(
								)
							)
						)
					);
			}

			$uicols = array (
				'input_type'	=>	array('text','text'),
				'name'			=>	array('id','street_name'),
				'sort_field'	=>	array('id','descr'),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Street name'))
			);

			$street_list = array();
			$street_list = $this->bo->read_street();

			$content = array();
			$j=0;
			if (isset($street_list) && is_array($street_list))
			{
				foreach($street_list as $street_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $street_entry[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']	= $uicols['sort_field'][$i];
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']		= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("street_id")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("street_name")[0].value = "";' ."\r\n";

			$function_exchange_values .= 'opener.document.getElementsByName("street_id")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("street_name")[0].value = data.getData("street_name");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($street_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('street');
			$function_msg					= lang('list street');


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()

				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}

		function tenant()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uilookup.tenant',
						'second_display'	=> true,
						'cat_id'			=> $this->cat_id,
						'query'				=> $this->query,
						'filter'			=> $this->filter
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.tenant',"
					."second_display:true,"
					."cat_id:'{$this->cat_id}',"
					."query:'{$this->query}',"
					."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uilookup.tenant',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//'',//$query,
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 1
								)
							),
							'hidden_value' => array
							(
								)
							)
						)
					);
			}

			$uicols = array (
				'input_type'	=>	array('text','text','text'),
				'name'			=>	array('id','last_name','first_name'),
				'formatter'		=>	array('','',''),
				'descr'			=>	array(lang('ID'),lang('last name'),lang('first name'))
			);

			$tenant_list = array();
			$tenant_list = $this->bo->read_tenant();

			$content = array();
			$j=0;
			if (isset($tenant_list) && is_array($tenant_list))
			{
				foreach($tenant_list as $tenant_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $tenant_entry[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['name'][$i];
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("tenant_id")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("last_name")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("first_name")[0].value = "";' ."\r\n";

			$function_exchange_values .= 'opener.document.getElementsByName("tenant_id")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("last_name")[0].value = data.getData("last_name");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("first_name")[0].value = data.getData("first_name");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($tenant_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('tenant');
			$function_msg					= lang('list tenant');


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}

		function ns3420()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uilookup.ns3420',
						'second_display'	=> true,
						'cat_id'			=> $this->cat_id,
						'query'				=> $this->query,
						'filter'			=> $this->filter
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.ns3420',"
					."second_display:true,"
					."cat_id:'{$this->cat_id}',"
					."query:'{$this->query}',"
					."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uilookup.ns3420',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//'',//$query,
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 1
								)
							),
							'hidden_value' => array
							(
								)
							)
						)
					);
			}

			$uicols = array (
				'input_type'	=>	array('text','text'),
				'name'			=>	array('id','ns3420_descr'),
				'sort_field'	=>	array('id','tekst1'),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('ns3420 description'))
			);

			$ns3420_list = array();
			$ns3420_list = $this->bo->read_ns3420();

			$content = array();
			$j=0;
			if (isset($ns3420_list) && is_array($ns3420_list))
			{
				foreach($ns3420_list as $ns3420_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $ns3420_entry[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['sort_field'][$i];
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_id")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_descr")[0].value = "";' ."\r\n";


			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_id")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("ns3420_descr")[0].value = data.getData("ns3420_descr");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($ns3420_entry);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('standard description');
			$function_msg					= lang('list standard description');


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}


		function entity()
		{
			$bocommon					= CreateObject('property.bocommon');
			$boentity					= CreateObject('property.boentity');
			$boadmin_entity				= CreateObject('property.boadmin_entity');
			$this->start				= $boentity->start;
			$this->query				= $boentity->query;
			$this->sort					= $boentity->sort;
			$this->order				= $boentity->order;
			$this->filter				= $boentity->filter;
			$this->cat_id				= $boentity->cat_id;
			$this->part_of_town_id		= $boentity->part_of_town_id;
			$this->district_id			= $boentity->district_id;
			$this->entity_id			= $boentity->entity_id;
			$this->location_code		= $boentity->location_code;
			$this->criteria_id			= $boentity->criteria_id;

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			
			$input_name			= phpgwapi_cache::session_get('property', 'lookup_fields');
			$input_name_entity	= phpgwapi_cache::session_get('property', 'lookup_fields_entity');
			$input_name = $input_name ? $input_name : array();
			$input_name_entity = $input_name_entity ? $input_name_entity : array();
			
			$input_name = array_merge($input_name, $input_name_entity);
			
			$action = '';
			for ($i=0;$i<count($input_name);$i++)
			{
				$action .= "parent.document.getElementsByName('{$input_name[$i]}')[0].value = ''; \r\n";
			}
			for ($i=0;$i<count($input_name);$i++)
			{
				$action .= "if (typeof aData['{$input_name[$i]}'] !== 'undefined'){ parent.document.getElementsByName('{$input_name[$i]}')[0].value = aData['{$input_name[$i]}']; } \r\n";
			}
			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";
			
			$values = $boentity->read(array('lookup'=>true, 'dry_run'=>true));
			$uicols	= $boentity->uicols;
			
			if (count($uicols['name']) > 0)
			{
				for ($m = 0; $m<count($input_name); $m++)
				{
					if (!array_search($input_name[$m],$uicols['name']))
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
			
			
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				if (phpgw::get_var('head'))
				{
					$entity_def = array();
					$head = '<thead>';
					$count_uicols_name = count($uicols['name']);
					for ($k=0;$k<$count_uicols_name;$k++)
					{						
						$params = array(
										'key' => $uicols['name'][$k],
										'label' => $uicols['descr'][$k],
										'sortable' => false,
										'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
									);
						
						if ($uicols['name'][$k]=='loc1' || $uicols['name'][$k]=='num')
						{
							$params['sortable']	= true;
						}
				
						array_push ($entity_def, $params);

						if ($uicols['input_type'][$k] != 'hidden')
						{
							$head .= '<th>'.$uicols['descr'][$k].'</th>';
						}
					}
					$head .= '</thead>';

					$datatable_def = array
					(
						'container'		=> 'datatable-container',
						'requestUrl'	=> self::link(array(
												'menuaction'		=> 'property.uilookup.entity',
												'second_display'	=> $second_display,
												'cat_id'			=> $this->cat_id,
												'entity_id'			=> $this->entity_id,
												'district_id'		=> $this->district_id,
												'criteria_id'		=> $this->criteria_id,
												'phpgw_return_as'	=> 'json'
											)),
						'ColumnDefs'	=> $entity_def
					);

					$data = array
					(
							'datatable_def'		=> $datatable_def,
							'datatable_head'	=> $head,
					);

					return $data;
				
				}
				else {
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
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.entity',
							'second_display'	=> 1,
							'entity_id'			=> $this->entity_id,
							'cat_id'			=> $this->cat_id,
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);
			
			$values_combo_box[0] = $boentity->select_category_list('filter', $this->cat_id);
			array_unshift ($values_combo_box[0], array('id'=>'', 'name'=>lang('no category')));
			$filters[0] = array('type' => 'filter-category',
						'name' => 'cat_id',
						'text' => lang('category'),
						'list' => $values_combo_box[0]
					);

			$values_combo_box[1]  = $bocommon->select_district_list('filter',$this->district_id);
			array_unshift ($values_combo_box[1], array('id'=>'', 'name'=>lang('no district')));
			$filters[1] = array('type' => 'filter',
						'name' => 'district_id',
						'text' => lang('district'),
						'list' => $values_combo_box[1]
					);
			
			$values_combo_box[2]  = $boentity->get_criteria_list($this->criteria_id);
			array_unshift ($values_combo_box[2], array('id'=>'', 'name'=>lang('no criteria')));
			$filters[2] = array('type' => 'filter',
						'name' => 'criteria_id',
						'text' => lang('criteria'),
						'list' => $values_combo_box[2]
					);	

			foreach ($filters as $filter) 
			{
				array_unshift ($data['form']['toolbar']['item'], $filter);
			}
			

			$count_uicols_name = count($uicols['name']);
	
			for($k=0;$k<$count_uicols_name;$k++)
			{						
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => false,
								'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
							);
				
				if ($uicols['name'][$k]=='loc1' || $uicols['name'][$k]=='num')
				{
					$params['sortable']	= true;
				}
						
				array_push ($data['datatable']['field'], $params);
			}

			if($this->entity_id)
			{
				$entity 	= $boadmin_entity->read_single($this->entity_id,false);
				$appname	= $entity['name'];
			}
			if($this->cat_id)
			{
				$category = $boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg	= lang('lookup') . ' ' . $category['name'];
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			
			self::render_template_xsl('lookup.entity', $data);
		}	

		
		function phpgw_user()
		{
			$column = phpgw::get_var('column');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
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
						$ordering =  "";
				}
				$params = array(
					'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
					'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
					'query' => $search['value'],
					'order' => $ordering,
					'sort' => $order[0]['dir'],
					'dir' => $order[0]['dir'],
					'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
					'allrows' => phpgw::get_var('length', 'int') == -1
				);

				$values = $this->bo->read_phpgw_user($params);

				$result_data = array('results' => $values);

				$result_data['total_records'] = $this->bo->total_records;
				$result_data['draw'] = $draw;

				return $this->jquery_results($result_data);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');
			
			if($column)
			{
				$user_id	=$column;
				$user_name	=$column . '_user_name';
			}
			else
			{
				$user_id	='user_id';
				$user_name	='user_name';
			}

			$action = '';
			$action .= 'parent.document.getElementsByName("'.$user_id.'")[0].value = "";'."\r";
			$action .= 'parent.document.getElementsByName("'.$user_name.'")[0].value = "";'."\r";
			$action .= 'parent.document.getElementsByName("'.$user_id.'")[0].value = aData["id"];'."\r";
			$action .= 'parent.document.getElementsByName("'.$user_name.'")[0].value = aData["first_name"] + " " + aData["last_name"];'."\r";
			$action .= 'window.parent.JqueryPortico.onPopupClose("close");'."\r";
			
			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'	=> 'property.uilookup.phpgw_user',
							'second_display'	=> true,
							'cat_id'			=> $this->cat_id,
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'column'			=> $column,
							'phpgw_return_as' => 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array (
				'input_type'	=>	array('text','hidden','text','text'),
				'name'			=>	array('id','account_lid','first_name','last_name'),
				'sort_field'	=>	array('account_id','account_lid','account_firstname','account_lastname'),
				'descr'			=>	array(lang('ID'),'',lang('first name'),lang('last name'))
			);

			$count_uicols_name = count($uicols['name']);
	
			for($k=0;$k<$count_uicols_name;$k++)
			{						
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => true,
								'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
							);
				
				array_push ($data['datatable']['field'], $params);
			}

			$appname						= lang('standard description');
			$function_msg					= lang('list standard description');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			
			self::render_template_xsl('datatable_jquery', $data);
		}

		function project_group()
		{
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uilookup.project_group',
						'second_display'	=> true,
						'cat_id'			=> $this->cat_id,
						'query'				=> $this->query,
						'filter'			=> $this->filter
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilookup.project_group',"
					."second_display:true,"
					."cat_id:'{$this->cat_id}',"
					."query:'{$this->query}',"
					."filter:'{$this->filter}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uilookup.project_group',
								'second_display'	=> true,
								'cat_id'			=> $this->cat_id,
								'query'				=> $this->query,
								'filter'			=> $this->filter
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton  SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 2
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//'',//$query,
									'type' => 'text',
									'size'    => 28,
									'onkeypress' => 'return pulsar(event)',
									'tab_index' => 1
								)
							),
							'hidden_value' => array
							(
								)
							)
						)
					);
			}

			$uicols = array (
				'input_type'	=>	array('text','text','text'),
				'name'			=>	array('id','descr','budget'),
				'formatter'		=>	array('','','FormatterRight'),
				'descr'			=>	array(lang('ID'),lang('Name'),lang('budget'))
			);

			$project_group_list = array();
			$project_group_list = $this->bo->read_project_group();

			$content = array();
			$j=0;
			if (isset($project_group_list) && is_array($project_group_list))
			{
				foreach($project_group_list as $project_group_entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $project_group_entry[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
					}
					$j++;
				}
			}

			$uicols_count	= count($uicols['descr']);
			$datatable['rowactions']['action'] = array();
			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= true;
				$datatable['headers']['header'][$i]['sort_field'] 	= $uicols['name'][$i];
			}

			$function_exchange_values = '';

			$function_exchange_values .= 'opener.document.getElementsByName("project_group")[0].value = "";' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("project_group_descr")[0].value = "";' ."\r\n";

			$function_exchange_values .= 'opener.document.getElementsByName("project_group")[0].value = data.getData("id");' ."\r\n";
			$function_exchange_values .= 'opener.document.getElementsByName("project_group_descr")[0].value = data.getData("descr");' ."\r\n";

			$function_exchange_values .= 'window.close()';

			$datatable['exchange_values'] = $function_exchange_values;
			$datatable['valida'] = '';

			// path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($project_group_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname						= lang('project group');
			$function_msg					= lang('list project group');


			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array()
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='".$column['link']."' target='_blank'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			// Prepare template variables and process XSLT
			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);


			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'lookup.tenant.index', 'property' );

			$this->save_sessiondata();
		}

		function ecodimb()
		{
			if( phpgw::get_var('phpgw_return_as') == 'json' )
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
			$action .= 'parent.document.getElementsByName("ecodimb")[0].value = "";' ."\r\n";
			$action .= 'parent.document.getElementsByName("ecodimb_descr")[0].value = "";' ."\r\n";
			$action .= 'parent.document.getElementsByName("ecodimb")[0].value = aData["id"];' ."\r\n";
			$action .= 'parent.document.getElementsByName("ecodimb_descr")[0].value = aData["descr"];' ."\r\n";
			//trigger ajax-call
			$action .= "parent.document.getElementsByName('ecodimb')[0].setAttribute('ecodimb','ecodimb',0);\r\n";

			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";

			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.ecodimb',
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'cat_id'			=> $this->cat_id,
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array (
				'name'			=>	array('id','descr'),
				'sortable'		=>	array(true,true),
				'formatter'		=>	array('',''),
				'descr'			=>	array(lang('ID'),lang('Name'))
			);

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => $uicols['sortable'][$k],
								'hidden' => false
							);

				array_push ($data['datatable']['field'], $params);
			}

			$appname						= lang('ecodimb');
			$function_msg					= lang('lookup');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function order_template()
		{
			if( phpgw::get_var('phpgw_return_as') == 'json' )
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
				$bo	= CreateObject('property.bogeneric',true);
				$bo->get_location_info('order_template');
				$values = $bo->read($params);

				$result_data = array
				(
					'results' => $values,
					'total_records' => $this->bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'var temp = parent.document.getElementsByName("values[order_descr]")[0].value;' ."\r\n";
			$action .= 'if(temp){temp = temp + "\n";}' ."\r\n";
			$action .= 'parent.document.getElementsByName("values[order_descr]")[0].value = temp + aData["content"];' ."\r\n";
			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";

			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.order_template',
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'cat_id'			=> $this->cat_id,
							'type'				=> 'order_template',
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array (
				'input_type'	=>	array('text','text','text'),
				'name'			=>	array('id','name','content'),
				'formatter'		=>	array('','',''),
				'descr'			=>	array(lang('ID'),lang('name'),lang('content'))
			);

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => $uicols['sortable'][$k],
								'hidden' => false
							);

				array_push ($data['datatable']['field'], $params);
			}

			$appname						= lang('template');
			$function_msg					= lang('list order template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function response_template()
		{
			if( phpgw::get_var('phpgw_return_as') == 'json' )
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
				$bo	= CreateObject('property.bogeneric',true);
				$bo->get_location_info('response_template');
				$values = $bo->read($params);

				$result_data = array
				(
					'results' => $values,
					'total_records' => $this->bo->total_records,
					'draw' => $draw
				);
				return $this->jquery_results($result_data);
			}

			$action = 'var temp = parent.document.getElementsByName("values[response_text]")[0].value;' ."\r\n";
			$action .= 'if(temp){temp = temp + "\n";}' ."\r\n";
			$action .= 'parent.document.getElementsByName("values[response_text]")[0].value = temp + aData["content"];' ."\r\n";
			$action .= 'parent.SmsCountKeyUp(160);' ."\r\n";

			$action .= 'parent.JqueryPortico.onPopupClose("close");'."\r";

			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'		=> 'property.uilookup.response_template',
							'query'				=> $this->query,
							'filter'			=> $this->filter,
							'cat_id'			=> $this->cat_id,
							'type'				=> 'response_template',
							'phpgw_return_as'	=> 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);

			$uicols = array (
				'input_type'	=>	array('text','text','text'),
				'name'			=>	array('id','name','content'),
				'formatter'		=>	array('','',''),
				'descr'			=>	array(lang('ID'),lang('name'),lang('content'))
			);

			$count_uicols_name = count($uicols['name']);

			for($k=0;$k<$count_uicols_name;$k++)
			{
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => $uicols['sortable'][$k],
								'hidden' => false
							);

				array_push ($data['datatable']['field'], $params);
			}

			$appname						= lang('template');
			$function_msg					= lang('list response template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function custom()
		{
			$type	= phpgw::get_var('type');
			$column = phpgw::get_var('column');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
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
				$bogeneric->get_location_info(phpgw::get_var('type','string'));
				$values = $bogeneric->read($params);

				$result_data = array('results' => $values);

				$result_data['total_records'] = $bogeneric->total_records;
				$result_data['draw'] = $draw;

				return $this->jquery_results($result_data);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');
			
			$custom_id		= $column;
			$custom_name	= "label_{$column}";

			$action = '';
			$action .= 'window.parent.document.getElementById("'.$custom_id.'").value = "";'."\r";
			$action .= 'window.parent.document.getElementById("'.$custom_name.'").innerHTML = "";'."\r";
			$action .= 'window.parent.document.getElementById("'.$custom_id.'").value = aData["id"];'."\r";
			$action .= 'window.parent.document.getElementById("'.$custom_name.'").innerHTML = aData["name"];'."\r";
			$action .= 'window.parent.JqueryPortico.onPopupClose("close");'."\r";
			$action .= 'window.parent.filterData("'.$custom_id.'", aData["id"]);';
			
			$data = array(
				'left_click_action'	=> $action,
				'datatable_name'	=> '',
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
							'menuaction'	=> 'property.uilookup.custom',
							'cat_id'		=> $this->cat_id,
							'filter'		=> $this->filter,
							'type'			=> $type,
							'phpgw_return_as' => 'json'
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array()
				)
			);
								
			$bogeneric = CreateObject('property.bogeneric');
			$bogeneric->get_location_info(phpgw::get_var('type','string'));
			$values = $bogeneric->read();

			$uicols = $bogeneric->uicols;

			$count_uicols_name = count($uicols['name']);
	
			for($k=0;$k<$count_uicols_name;$k++)
			{						
				$params = array(
								'key' => $uicols['name'][$k],
								'label' => $uicols['descr'][$k],
								'sortable' => false,
								'hidden' => false
							);
				
				array_push ($data['datatable']['field'], $params);
			}

			$appname						= lang('template');
			$function_msg					= lang('list order template');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			
			self::render_template_xsl('datatable_jquery', $data);

		}

	}
