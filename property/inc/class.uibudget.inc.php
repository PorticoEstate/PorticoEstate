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
	phpgw::import_class('phpgwapi.yui');
	class property_uibudget
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;

		var $public_functions = array
			(
				'index'			=> true,
				'basis'			=> true,
				'obligations'	=> true,
				'view'			=> true,
				'edit'			=> true,
				'edit_basis'	=> true,
				'download'		=> true,
				'delete'		=> true,
				'delete_basis'	=> true
			);
		function property_uibudget()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::budget';

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.bobudget',true);
			$this->bocommon		= & $this->bo->bocommon;
			$this->cats			= & $this->bo->cats;

			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->dimb_id		= $this->bo->dimb_id;
			$this->allrows		= $this->bo->allrows;
			$this->district_id	= $this->bo->district_id;
			$this->year			= $this->bo->year;
			$this->month		= $this->bo->month;
			$this->grouping		= $this->bo->grouping;
			$this->revision		= $this->bo->revision;
			$this->details		= $this->bo->details;
			$this->direction	= $this->bo->direction;

			$this->acl 			= & $GLOBALS['phpgw']->acl;

		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'			=> $this->start,
					'query'			=> $this->query,
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'filter'		=> $this->filter,
					'cat_id'		=> $this->cat_id,
					'dimb_id'		=> $this->dimb_id,
					'allrows'		=> $this->allrows,
					'direction'		=> $this->direction,
					'month'			=> $this->month
				);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$acl_location	= '.budget';
			$acl_read 		= $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

			$acl_add		= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit		= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete 	= $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			$revision_list	= $this->bo->get_revision_filter_list($this->revision); // reset year
			$this->year		= $this->bo->year;
			$this->revision = $this->bo->revision;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::budget';

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=>'property.uibudget.index',
						'sort'			=>$this->sort,
						'order'			=>$this->order,
						'cat_id'		=>$this->cat_id,
						'dimb_id'		=>$this->dimb_id,
						'filter'		=>$this->filter,
						'query'			=>$this->query,
						'district_id'	=>$this->district_id,
						'year'			=>$this->year,
						'grouping'		=>$this->grouping,
						'revision'		=>$this->revision
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uibudget.index',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."cat_id: '{$this->cat_id}',"
					."dimb_id: '{$this->dimb_id}',"
					."filter:'{$this->filter}',"
					."query:'{$this->query}',"
					."district_id:'{$this->district_id}',"
					."year:'{$this->year}',"
					."grouping:'{$this->grouping}',"
					."revision:'{$this->revision}',"
					."download:'budget'";

				$values_combo_box[0]  = $this->bo->get_year_filter_list($this->year);
				$default_value = array ('id'=>'','name'=>lang('no year'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bo->get_revision_filter_list($this->revision);
				$default_value = array ('id'=>'','name'=>lang('no revision'));
				if (count($values_combo_box[1]))
				{
					array_unshift ($values_combo_box[1],$default_value);
				}
				else
				{
					$values_combo_box[1][] = $default_value;
				}

				$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
				if(count($values_combo_box[2]))
				{
					$default_value = array ('id'=>'','name'=>lang('no district'));
					array_unshift ($values_combo_box[2],$default_value);
				}


				$values_combo_box[3] =  $this->bo->get_grouping_filter_list($this->grouping);
				$default_value = array ('id'=>'','name'=>lang('no grouping'));
				if (count($values_combo_box[3]))
				{
					array_unshift ($values_combo_box[3],$default_value);
				}
				else
				{
					$values_combo_box[3][] = $default_value;
				}

				$cat_filter =  $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => True,'link_data' => $link_data));
				foreach($cat_filter['cat_list'] as $_cat)
				{
					$values_combo_box[4][] = array
					(
						'id' => $_cat['cat_id'],
						'name' => $_cat['name'],
						'selected' => $_cat['selected'] ? 1 : 0
					);
				}

				array_unshift ($values_combo_box[4],array ('id'=>'', 'name'=>lang('no category')));

				$values_combo_box[5]  = $this->bocommon->select_category_list(array('type'=>'dimb'));
				foreach($values_combo_box[5] as & $_dimb)
				{
					$_dimb['name'] = "{$_dimb['id']}-{$_dimb['name']}";
				}
				$default_value = array ('id'=>'','name'=>lang('no dimb'));
				array_unshift ($values_combo_box[5],$default_value);


				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uibudget.index',
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	YEAR
									'id'		=> 'btn_year',
									'name'		=> 'year',
									'value'		=> lang('year'),
									'type'		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	REVISION
									'id' 		=> 'btn_revision',
									'name' 		=> 'revision',
									'value'		=> lang('revision'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	DISTRICT
									'id' 		=> 'btn_district_id',
									'name' 		=> 'district_id',
									'value'		=> lang('district_id'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	GROUPING
									'id' 		=> 'btn_grouping',
									'name' 		=> 'grouping',
									'value'		=> lang('grouping'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 4
								),
								array
								( //boton 	USER
									//	'id' => 'btn_user_id',
									'id' => 'sel_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[4],
									'onchange'=> 'onChangeSelect("cat_id");',
									'tab_index' => 5
								),
								array
								(
									'id' => 'sel_dimb_id',
									'name' => 'dimb_id',
									'value'	=> lang('dimb'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[5],
									'onchange'=> 'onChangeSelect("dimb_id");',
									'tab_index' => 6
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 9
								),
								array
								( //boton     add
									'id' 		=> 'btn_new',
									'name' 		=> 'new',
									'value'    	=> lang('add'),
									'type' 		=> 'button',
									'tab_index' => 9
								),
								array
								( //boton     SEARCH
									'id' 		=> 'btn_search',
									'name' 		=> 'search',
									'value'    	=> lang('search'),
									'type' 		=> 'button',
									'tab_index' => 8
								),
								array
								( // TEXT IMPUT
									'name'     	=> 'query',
									'id'     	=> 'txt_query',
									'value'    	=> $this->query,
									'type' 		=> 'text',
									'size'    	=> 28,
									'onkeypress'=> 'return pulsar(event)',
									'tab_index' => 7
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
								),
								array(
									//div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								),
								array
								( //div values  combo_box_3
									'id' => 'values_combo_box_3',
									'value'	=> $this->bocommon->select2String($values_combo_box[3])
								),
/*
								array
								( //div values  combo_box_4
									'id' => 'values_combo_box_4',
									'value'	=> $this->bocommon->select2String($values_combo_box[4]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_5
									'id' => 'values_combo_box_5',
									'value'	=> $this->bocommon->select2String($values_combo_box[5])
								)
*/
							)
						)
					)
				);

			}

			$location_list = array();
			$location_list = $this->bo->read();
			$uicols = array (
				array(
					'visible'=>false,	'name'=>'budget_id',		'label'=>'dummy',				'className'=>'',			'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'year',			'label'=>lang('year'),			'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'revision',		'label'=>lang('revision'),		'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'b_account_id',	'label'=>lang('budget account'),'className'=>'rightClasss', 'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'b_account_name',	'label'=>lang('name'),			'className'=>'leftClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'grouping',		'label'=>lang('grouping'),		'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'category',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'district_id',	'label'=>lang('district_id'),	'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'district_id','formatter'=>''),
				array(
					'visible'=>true,	'name'=>'ecodimb',	'label'=>lang('dimb'),	'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'fm_budget.ecodimb','formatter'=>''),
				array(
					'visible'=>true,	'name'=>'category',	'label'=>lang('category'),	'className'=>'rightClasss', 'sortable'=>false,	'sort_field'=>'','formatter'=>''),
				array(
					'visible'=>true,	'name'=>'budget_cost',	'label'=>lang('budget_cost'),	'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'budget_cost','formatter'=>'myFormatDate'),
			);

			$content = array();
			$j = 0;
			if (isset($location_list) && is_array($location_list))
			{
				foreach($location_list as $location)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols[$i]['name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']	= $location[$uicols[$i]['name']];
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

			$parameters = array('parameter' => array(array(	'name'=> 'budget_id',
				'source'=> 'budget_id')));

			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'edit',
				'text' 			=> lang('edit'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uibudget.edit')),
				'parameters'	=> $parameters
			);

			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'delete',
				'text' 			=> lang('delete'),
				'confirm_msg'	=> lang('do you really want to delete this entry'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uibudget.delete')),
				'parameters'	=> $parameters
			);

			if($acl_add)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name'		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit'))
				);
			}
			unset($parameters);

			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()

			for ($i=0;$i<count($uicols);$i++)
			{
				$datatable['headers']['header'][$i]['name']			= $uicols[$i]['name'];
				$datatable['headers']['header'][$i]['text'] 		= $uicols[$i]['label'];
				$datatable['headers']['header'][$i]['visible'] 		= $uicols[$i]['visible'];
				$datatable['headers']['header'][$i]['sortable']		= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']	= $uicols[$i]['sort_field'];
				$datatable['headers']['header'][$i]['className']	= $uicols[$i]['className'];
				$datatable['headers']['header'][$i]['formatter']	= ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($location_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;



			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $uicols[0]['name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'sum_budget'		=> $this->bo->sum_budget_cost
				);

			// values for datatable
			$json_row = array();
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					foreach( $row['column'] as $column)
					{
						$json_row[$column['name']] = $column['value'];
					}
					$json['records'][] = $json_row;
				}
			}
			//Depended select: REVISION
			$opt_cb_depend  = $this->bo->get_revision_filter_list($this->revision);
			$default_value = array ('id'=>'','name'=>lang('no revision'));
			if (count($opt_cb_depend))
			{
				array_unshift ($opt_cb_depend,$default_value);
			}
			else
			{
				$opt_cb_depend[] = $default_value;
			}
			$json['hidden']['dependent'][] = array ('id' 	=> $this->revision,
				'value' => $this->bocommon->select2String($opt_cb_depend)
			);

			//Depended select: GROPING
			$opt_cb_depend  = $this->bo->get_grouping_filter_list($this->grouping);
			$default_value = array ('id'=>'','name'=>lang('no grouping'));
			if (count($opt_cb_depend))
			{
				array_unshift ($opt_cb_depend,$default_value);
			}
			else
			{
				$opt_cb_depend[] = $default_value;
			}
			$json['hidden']['dependent'][] = array ('id' 	=> $this->grouping,
				'value' => $this->bocommon->select2String($opt_cb_depend)
			);

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			$json ['revision'] = $this->revision;
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'budget.index', 'property' );
		}

		function basis()
		{
			$acl_location	= '.budget';
			$acl_read 		= $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

			$acl_add		= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit		= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete 	= $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			$revision_list	= $this->bo->get_revision_filter_list($this->revision,$basis=true); // reset year
			$this->year		= $this->bo->year;
			$this->revision = $this->bo->revision;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::basis';

			$datatable = array();
			$values_combo_box = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=>'property.uibudget.basis',
						'sort'			=>$this->sort,
						'order'			=>$this->order,
						'cat_id'		=>$this->cat_id,
						'filter'		=>$this->filter,
						'query'			=>$this->query,
						'district_id'	=>$this->district_id,
						'year'			=>$this->year,
						'grouping'		=>$this->grouping,
						'revision'		=>$this->revision,
						'dimb_id'		=>$this->dimb_id
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uibudget.basis',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."cat_id: '{$this->cat_id}',"
					."filter:'{$this->filter}',"
					."query:'{$this->query}',"
					."district_id:'{$this->district_id}',"
					."year:'{$this->year}',"
					."grouping:'{$this->grouping}',"
					."dimb_id: '{$this->dimb_id}',"
					."revision:'{$this->revision}',"
					."download:'basis'";

				$values_combo_box[0]  = $this->bo->get_year_filter_list($this->year,$basis=true);
				$default_value = array ('id'=>'','name'=>lang('no year'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bo->get_revision_filter_list($this->revision,$basis=true);
				$default_value = array ('id'=>'','name'=>lang('no revision'));
				if (count($values_combo_box[1]))
				{
					array_unshift ($values_combo_box[1],$default_value);
				}
				else
				{
					$values_combo_box[1][] = $default_value;
				}

				$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
				if(count($values_combo_box[2]))
				{
					$default_value = array ('id'=>'','name'=>lang('no district'));
					array_unshift ($values_combo_box[2],$default_value);
				}


				$values_combo_box[3] =  $this->bo->get_grouping_filter_list($this->grouping,$basis=true);
				$default_value = array ('id'=>'','name'=>lang('no grouping'));
				if (count($values_combo_box[3]))
				{
					array_unshift ($values_combo_box[3],$default_value);
				}
				else
				{
					$values_combo_box[3][] = $default_value;
				}

				$values_combo_box[4]  = $this->bocommon->select_category_list(array('type'=>'dimb'));
				$default_value = array ('id'=>'','name'=>lang('no dimb'));
				array_unshift ($values_combo_box[4],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uibudget.basis',
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	YEAR
									'id'		=> 'btn_year',
									'name'		=> 'year',
									'value'		=> lang('year'),
									'type'		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	REVISION
									'id' 		=> 'btn_revision',
									'name' 		=> 'revision',
									'value'		=> lang('revision'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	DISTRICT
									'id' 		=> 'btn_district_id',
									'name' 		=> 'district_id',
									'value'		=> lang('district_id'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	GROUPING
									'id' 		=> 'btn_grouping',
									'name' 		=> 'grouping',
									'value'		=> lang('grouping'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 4
								),
								array
								( //boton 	GROUPING
									'id' 		=> 'btn_dimb_id',
									'name' 		=> 'dimb_id',
									'value'		=> lang('dimb'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 5
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 9
								),
								array
								( //boton     add
									'id' 		=> 'btn_new',
									'name' 		=> 'new',
									'value'    	=> lang('add'),
									'type' 		=> 'button',
									'tab_index' => 8
								),
								array
								( //boton     SEARCH
									'id' 		=> 'btn_search',
									'name' 		=> 'search',
									'value'    	=> lang('search'),
									'type' 		=> 'button',
									'tab_index' => 7
								),
								array
								( // TEXT IMPUT
									'name'     	=> 'query',
									'id'     	=> 'txt_query',
									'value'    	=> $this->query,
									'type' 		=> 'text',
									'size'    	=> 28,
									'onkeypress'=> 'return pulsar(event)',
									'tab_index' => 6
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								),
								array
								( //div values  combo_box_3
									'id' => 'values_combo_box_3',
									'value'	=> $this->bocommon->select2String($values_combo_box[3])
								),
								array
								( //div values  combo_box_4
									'id' => 'values_combo_box_4',
									'value'	=> $this->bocommon->select2String($values_combo_box[4])
								)
							)
						)
					)
				);

			}

			$location_list = array();
			$location_list = $this->bo->read_basis();
			//_debug_array($location_list);

			$uicols = array (
				array(
					'visible'=>false,	'name'=>'budget_id',		'label'=>'dummy',				'className'=>'',			'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'year',			'label'=>lang('year'),			'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'revision',		'label'=>lang('revision'),		'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'grouping',		'label'=>lang('grouping'),		'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'b_group',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>'district_id',	'label'=>lang('district_id'),	'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'district_id','formatter'=>''),
				array(
					'visible'=>true,	'name'=>'ecodimb',	'label'=>lang('dimb'),	'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'fm_budget.ecodimb','formatter'=>''),
				array(
					'visible'=>true,	'name'=>'category',	'label'=>lang('category'),	'className'=>'rightClasss', 'sortable'=>false,	'sort_field'=>'','formatter'=>''),
				array(
					'visible'=>true,	'name'=>'budget_cost',	'label'=>lang('budget_cost'),	'className'=>'rightClasss', 'sortable'=>true,	'sort_field'=>'budget_cost','formatter'=>myFormatDate),
			);

			$content = array();
			$j = 0;
			if (isset($location_list) && is_array($location_list))
			{
				foreach($location_list as $location)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols[$i]['name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']	= $location[$uicols[$i]['name']];
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

			$parameters = array('parameter' => array(array(	'name'=> 'budget_id',
				'source'=> 'budget_id')));

			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'edit',
				'text' 			=> lang('edit'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uibudget.edit_basis')),
				'parameters'	=> $parameters
			);

			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'delete',
				'text' 			=> lang('delete'),
				'confirm_msg'	=> lang('do you really want to delete this entry'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'property.uibudget.delete_basis')),
				'parameters'	=> $parameters
			);

			if($acl_add)
			{
				$datatable['rowactions']['action'][] = array(
					'my_name'		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.edit_basis'))
				);
			}
			unset($parameters);

			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()

			for ($i=0;$i<count($uicols);$i++)
			{
				$datatable['headers']['header'][$i]['name']			= $uicols[$i]['name'];
				$datatable['headers']['header'][$i]['text'] 		= $uicols[$i]['label'];
				$datatable['headers']['header'][$i]['visible'] 		= $uicols[$i]['visible'];
				$datatable['headers']['header'][$i]['sortable']		= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']	= $uicols[$i]['sort_field'];
				$datatable['headers']['header'][$i]['className']	= $uicols[$i]['className'];
				$datatable['headers']['header'][$i]['formatter']	= ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($location_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;



			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $uicols[3]['name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

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
			$json_row = array();
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					foreach( $row['column'] as $column)
					{
						$json_row[$column['name']] = $column['value'];
					}
					$json['records'][] = $json_row;
				}
			}
			//Depended select: REVISION
			$opt_cb_depend  = $this->bo->get_revision_filter_list($this->revision,$basis=true);
			$default_value = array ('id'=>'','name'=>lang('no revision'));
			if (count($opt_cb_depend))
			{
				array_unshift ($opt_cb_depend,$default_value);
			}
			else
			{
				$opt_cb_depend[] = $default_value;
			}
			$json['hidden']['dependent'][] = array ('id' 	=> $this->revision,
				'value' => $this->bocommon->select2String($opt_cb_depend)
			);

			//Depended select: GROPING
			$opt_cb_depend  = $this->bo->get_grouping_filter_list($this->grouping,$basis=true);
			$default_value = array ('id'=>'','name'=>lang('no grouping'));
			if (count($opt_cb_depend))
			{
				array_unshift ($opt_cb_depend,$default_value);
			}
			else
			{
				$opt_cb_depend[] = $default_value;
			}
			$json['hidden']['dependent'][] = array ('id' 	=> $this->grouping,
				'value' => $this->bocommon->select2String($opt_cb_depend)
			);

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}

			$json ['revision'] = $this->revision;
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list budget');

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'budget.basis', 'property' );
		}

		function obligations()
		{
			//$this->allrows = 1;
			$acl_location	= '.budget.obligations';
			$acl_read 	= $this->acl->check($acl_location, PHPGW_ACL_READ, 'property');

			if(!$acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $acl_location));
			}

			$acl_add 	= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit 	= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');
			$acl_delete 	= $this->acl->check($acl_location, PHPGW_ACL_DELETE, 'property');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::obligations';

			$datatable = array();
			$values_combo_box = array();
			$dry_run = false;
			$this->save_sessiondata();
			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uibudget.obligations',
						'cat_id'		=> $this->cat_id,
						'filter'		=> $this->filter,
						'query'			=> $this->query,
						'district_id'	=> $this->district_id,
						'grouping'		=> $this->grouping,
						'year'			=> $this->year,
						'month'			=> $this->month,
						'details'		=> $this->details,
						'allrows'		=> $this->allrows,
						'dimb_id'		=> $this->dimb_id,
						'direction'		=> $this->direction
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uibudget.obligations',"
					."cat_id: '{$this->cat_id}',"
					."filter:'{$this->filter}',"
					."query:'{$this->query}',"
					."district_id:'{$this->district_id}',"
					."grouping:'{$this->grouping}',"
					."year:'{$this->year}',"
					."month:'{$this->month}',"
					."details:'{$this->details}',"
					."dimb_id:'{$this->dimb_id}',"
					."direction:'{$this->direction}',"
					."allrows:'{$this->allrows}',"
					."download:'obligations'";

				$values_combo_box[0]  = $this->bo->get_year_filter_list($this->year,$basis=false);
				$default_value = array ('id'=>'','name'=>lang('no year'));
				array_unshift ($values_combo_box[0],$default_value);



				for ($i=1;$i< 13 ;$i++)
				{
					$values_combo_box[1][] = array ('id'=> $i,'name'=> sprintf("%02s",$i));
				}

				array_unshift ($values_combo_box[1], array ('id'=>'','name'=>lang('month')));

				$values_combo_box[2]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[2],$default_value);

//_debug_array($values_combo_box[2]);

				$values_combo_box[3] =  $this->bo->get_b_group_list($this->grouping);
				$default_value = array ('id'=>'','name'=>lang('no grouping'));
				array_unshift ($values_combo_box[3],$default_value);

				$cat_filter =  $this->cats->formatted_xslt_list(array('select_name' => 'cat_id','selected' => $this->cat_id,'globals' => True,'link_data' => $link_data));
				foreach($cat_filter['cat_list'] as $_cat)
				{
					$values_combo_box[4][] = array
					(
						'id' => $_cat['cat_id'],
						'name' => $_cat['name'],
						'selected' => $_cat['selected'] ? 1 : 0
					);
				}

				array_unshift ($values_combo_box[4],array ('id'=>'', 'name'=>lang('no category')));


				$values_combo_box[5]  = $this->bocommon->select_category_list(array('type'=>'department'));
				array_unshift ($values_combo_box[5], array ('id'=>'','name'=>lang('department')));

				$values_combo_box[6]  = $this->bocommon->select_category_list(array('type'=>'dimb'));
				foreach($values_combo_box[6] as & $_dimb)
				{
					$_dimb['name'] = "{$_dimb['id']}-{$_dimb['name']}";
				}
				$default_value = array ('id'=>'','name'=>lang('no dimb'));
				array_unshift ($values_combo_box[6],$default_value);


				$values_combo_box[7]  = array
				(
					array
					(
						'id' => 'expenses',
						'name'	=> lang('expenses'),
						'selected'	=> $this->direction == 'expenses' ? 1 : 0
					),
					array
					(
						'id' => 'income',
						'name'	=> lang('income'),
						'selected'	=> $this->direction == 'income' ? 1 : 0
					)
				);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uibudget.obligations',
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	YEAR
									'id'		=> 'btn_year',
									'name'		=> 'year',
									'value'		=> lang('year'),
									'type'		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	YEAR
									'id'		=> 'btn_month',
									'name'		=> 'month',
									'value'		=> lang('month'),
									'type'		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	DISTRICT
									'id' 		=> 'btn_district_id',
									'name' 		=> 'district_id',
									'value'		=> lang('district_id'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	GROUPING
									'id' 		=> 'btn_grouping',
									'name' 		=> 'grouping',
									'value'		=> lang('grouping'),
									'type' 		=> 'button',
									'style' 	=> 'filter',
									'tab_index' => 4
								),
								array
								(
									'id' => 'sel_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[4],
									'onchange'=> 'onChangeSelect("cat_id");',
									'tab_index' => 5
								),
								array
								( 
									'id' => 'sel_department',
									'name' => 'department',
									'value'	=> lang('department'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[5],
									'onchange'=> 'onChangeSelect("department");',
									'tab_index' => 6
								),
								array
								( 
									'id' => 'sel_dimb_id',
									'name' => 'dimb_id',
									'value'	=> lang('dimb'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[6],
									'onchange'=> 'onChangeSelect("dimb_id");',
									'tab_index' => 7
								),
								array
								(
									'id' => 'sel_direction',
									'name' => 'direction',
									'value'	=> lang('direction'),
									'type' => 'select',
									'style' => 'filter',
									'values' => $values_combo_box[7],
									'onchange'=> 'onChangeSelect("direction");',
									'tab_index' => 8
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 11
								),
								array
								( //boton     SEARCH
									'id' 		=> 'btn_search',
									'name' 		=> 'search',
									'value'    	=> lang('search'),
									'type' 		=> 'button',
									'tab_index' => 10
								),
								array
								( // TEXT IMPUT
									'name'     	=> 'query',
									'id'     	=> 'txt_query',
									'value'    	=> $this->query,
									'type' 		=> 'text',
									'size'    	=> 28,
									'onkeypress'=> 'return pulsar(event)',
									'tab_index' => 9
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								),
								array
								( //div values  combo_box_2
									'id' => 'values_combo_box_2',
									'value'	=> $this->bocommon->select2String($values_combo_box[2])
								),								array
								( //div values  combo_box_3
									'id' => 'values_combo_box_3',
									'value'	=> $this->bocommon->select2String($values_combo_box[3])
								)
							)
						)
					)
				);

				$dry_run = true;
			}

			$uicols = array (

				array(
					'col_name'=>'grouping',		'visible'=>false,	'label'=>'',				'className'=>'',				'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'b_account',		'visible'=>true,	'label'=>lang('grouping'),	'className'=>'centerClasss',	'sortable'=>true,	'sort_field'=>'b_account',	'formatter'=>'myformatLinkPGW'),
//				array(
//					'col_name'=>'district_id',	'visible'=>true,	'label'=>lang('district_id'),'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'ecodimb',		'visible'=>true,	'label'=>lang('dimb'),	'className'=>'centerClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'hits_ex',		'visible'=>false,	'label'=>'',				'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'hits',			'visible'=>true,	'label'=>lang('hits'),		'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'budget_cost_ex',	'visible'=>false,	'label'=>'',				'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'budget_cost',	'visible'=>true,	'label'=>lang('budget'),	'className'=>'rightClasss',		'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'obligation_ex',	'visible'=>false,	'label'=>''					,'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'obligation',		'visible'=>true,	'label'=>lang('sum orders'),'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',			'formatter'=>'myFormatLink_Count'),
				array(
					'col_name'=>'link_obligation','visible'=>false,	'label'=>'',				'className'=>'',				'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'actual_cost_ex',	'visible'=>false,	'label'=>'',				'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'actual_cost_period',	'visible'=>true,	'label'=>lang('paid') . ' ' . lang('period'),		'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'actual_cost',	'visible'=>true,	'label'=>lang('paid'),		'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>'myFormatLink_Count'),
				array(
					'col_name'=>'link_actual_cost','visible'=>false,	'label'=>'',				'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'diff_ex',		'visible'=>false,	'label'=>'',				'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'diff',			'visible'=>true,	'label'=>lang('difference'),'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>''),
				array(
					'col_name'=>'percent',			'visible'=>true,	'label'=>lang('percent'),'className'=>'rightClasss', 	'sortable'=>false,	'sort_field'=>'',			'formatter'=>'')
				);


			//FIXME
			if($dry_run)
			{
				$location_list = array();

			}
		//	else
			{
				$location_list = $this->bo->read_obligations();
			}

			//_debug_array($location_list);

			$entry = $content = array();
			$j = 0;
			//cramirez: add this code because  "mktime" functions fire an error
			if($this->year == "")
			{
				$today = getdate();
				$this->year = $today['year'];
			}

			if (isset($location_list) && is_array($location_list))
			{
				$details = $this->details ? false : true;

				$start_date = $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,1,1,$this->year),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
				$end_date	= $GLOBALS['phpgw']->common->show_date(mktime(0,0,0,12,31,$this->year),$GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				$sum_obligation = $sum_hits = $sum_budget_cost = $sum_actual_cost = 0;
				foreach($location_list as $entry)
				{
					$content[] = array
						(
							'grouping'			=> $entry['grouping'],
							'b_account'			=> $entry['b_account'],
							'district_id'		=> $entry['district_id'],
							'ecodimb'			=> $entry['ecodimb'],
							'hits_ex'			=> $entry['hits'],
							'hits'				=> number_format($entry['hits'], 0, ',', ' '),
							'budget_cost_ex'	=> $entry['budget_cost'],
							'budget_cost'		=> number_format($entry['budget_cost'], 0, ',', ' '),
							'obligation_ex'		=> $entry['obligation'],
							'obligation'		=> number_format($entry['obligation'], 0, ',', ' '),
							'link_obligation'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiworkorder.index', 'filter'=>'all', 'paid'=>1, 'district_id'=> $entry['district_id'], 'b_group'=> $entry['grouping'], 'b_account' =>$entry['b_account'], 'start_date'=> $start_date, 'end_date'=> $end_date, 'ecodimb' => $entry['ecodimb'], 'status_id' => 'all', 'obligation' => true)),
							'actual_cost_ex'	=> $entry['actual_cost'],
							'actual_cost_period'=> number_format($entry['actual_cost_period'], 0, ',', ' '),
							'actual_cost'		=> number_format($entry['actual_cost'], 0, ',', ' '),
							'link_actual_cost'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvoice.consume', 'district_id'=> $entry['district_id'], 'b_account_class'=> $entry['grouping'], 'b_account' =>$entry['b_account'],  'start_date'=> $start_date, 'end_date'=> $end_date, 'ecodimb' => $entry['ecodimb'], 'submit_search'=>true)),
							'diff_ex'			=> $entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation'],
							'diff'				=> number_format($entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation'], 0, ',', ' '),
							'percent'			=> $entry['percent']
						);
				}

			}

			$j=0;
			if (isset($content) && is_array($content))
			{
				foreach($content as $budget)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols[$i]['col_name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']		= $budget[$uicols[$i]['col_name']];
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

			for ($i=0;$i<count($uicols);$i++)
			{
				$datatable['headers']['header'][$i]['name']			= $uicols[$i]['col_name'];
				$datatable['headers']['header'][$i]['text'] 		= $uicols[$i]['label'];
				$datatable['headers']['header'][$i]['visible'] 		= $uicols[$i]['visible'];
				$datatable['headers']['header'][$i]['sortable']		= $uicols[$i]['sortable'];
				$datatable['headers']['header'][$i]['sort_field']	= $uicols[$i]['sort_field'];
				$datatable['headers']['header'][$i]['className']	= $uicols[$i]['className'];
				$datatable['headers']['header'][$i]['formatter']	= ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($location_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $uicols[1]['col_name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			//-- BEGIN----------------------------- JSON CODE ------------------------------

			//values for Pagination
			$json = array
				(
					'recordsReturned' 	=> $datatable['pagination']['records_returned'],
					'totalRecords' 		=> (int)$datatable['pagination']['records_total'],
					'startIndex' 		=> $datatable['pagination']['records_start'],
					'sort'				=> $datatable['sorting']['order'],
					'dir'				=> $datatable['sorting']['sort'],
					'records'			=> array(),
					'sum_budget'		=> $this->bo->sum_budget_cost,
					'sum_obligation'	=> $this->bo->sum_obligation_cost,
					'sum_actual'		=> $this->bo->sum_actual_cost,
					'sum_actual_period'	=> $this->bo->sum_actual_cost_period,
					'sum_diff'			=> $this->bo->sum_budget_cost - $this->bo->sum_actual_cost - $this->bo->sum_obligation_cost,
					'sum_hits'			=> $this->bo->sum_hits
				);

			// values for datatable
			$json_row = array();
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					foreach( $row['column'] as $column)
					{
						$json_row[$column['name']] = $column['value'];
					}
					$json['records'][] = $json_row;
				}
			}
			// right in datatable
			$json ['rights'] = $datatable['rowactions']['action'];

			//				$json ['sum_hits'] 			= number_format($sum_hits, 0, ',', ' ');
			//				$json ['sum_budget_cost']	= number_format($sum_budget_cost, 0, ',', ' ');
			//				$json ['sum_obligation']	= number_format($sum_obligation, 0, ',', ' ');
			//				$json ['sum_actual_cost']	= number_format($sum_actual_cost, 0, ',', ' ');
			//				$json ['sum_diff'] 			= number_format($sum_diff, 0, ',', ' ');

			//_debug_array($json);

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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . lang('list obligations');

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'budget.obligations', 'property' );
		}

		function edit()
		{
			$acl_location	= '.budget';
			$acl_add 	= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit 	= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $acl_location));
			}

			$budget_id	= phpgw::get_var('budget_id', 'int');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget'));

			$receipt = array();
			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				$values['b_account_id']		= phpgw::get_var('b_account_id', 'int', 'POST');
				$values['b_account_name']	= phpgw::get_var('b_account_name', 'string', 'POST');
				$values['ecodimb']			= phpgw::get_var('ecodimb');

				if(!$values['b_account_id'] > 0)
				{
					$values['b_account_id']='';
					$receipt['error'][]=array('msg'=>lang('Please select a budget account !'));
				}

				if(!$values['district_id'] && !$budget_id > 0)
				{
		//			$receipt['error'][]=array('msg'=>lang('Please select a district !'));
				}

				if(!$values['budget_cost'])
				{
//					$receipt['error'][]=array('msg'=>lang('Please enter a budget cost !'));
				}

				if(!isset($receipt['error']) || !$receipt['error'])
				{
					$values['budget_id']	= $budget_id;
					$receipt = $this->bo->save($values);
					$budget_id = $receipt['budget_id'];

					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','budget_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.index'));
					}
				}
				else
				{
					$year_selected = $values['year'];
					$district_id = $values['district_id'];
					$revision = $values['revision'];

					$values['year'] ='';
					$values['district_id'] = '';
					$values['revision'] = '';
				}
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.index'));
			}


			if ($budget_id)
			{
				$values = $this->bo->read_single($budget_id);
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uibudget.edit',
					'budget_id'	=> $budget_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$b_account_data=$this->bocommon->initiate_ui_budget_account_lookup(array(
				'b_account_id'		=> $values['b_account_id'],
				'b_account_name'	=> isset($values['b_account_name'])?$values['b_account_name']:'',
				'type'			=> isset($values['b_account_id']) && $values['b_account_id'] > 0 ?'view':'form'));

			$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array(
				'ecodimb'			=> $values['ecodimb'],
				'ecodimb_descr'		=> $values['ecodimb_descr']));

			$data = array
				(
					'ecodimb_data'					=>	$ecodimb_data,
					'lang_category'					=> lang('category'),
					'lang_no_cat'					=> lang('Select category'),
					'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),
					'b_account_data'				=> $b_account_data,
					'value_b_account'				=> $values['b_account_id'],
					'lang_revision'					=> lang('revision'),
					'lang_revision_statustext'		=> lang('Select revision'),
					'revision_list'					=> $this->bo->get_revision_list($values['revision']),

					'lang_year'						=> lang('year'),
					'lang_year_statustext'			=> lang('Budget year'),
					'year'							=> $this->bocommon->select_list($values['year']?$values['year']:date('Y'),$this->bo->get_year_list()),

					'lang_district'					=> lang('District'),
					'lang_no_district'				=> lang('no district'),
					'lang_district_statustext'		=> lang('Select the district'),
					'select_district_name'			=> 'values[district_id]',
					'district_list'					=> $this->bocommon->select_district_list('select',$values['district_id']),

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_budget_id'				=> lang('ID'),
					'value_budget_id'				=> $budget_id,
					'lang_budget_cost'				=> lang('budget cost'),
					'lang_remark'					=> lang('remark'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'value_remark'					=> $values['remark'],
					'value_budget_cost'				=> $values['budget_cost'],
					'lang_name_statustext'			=> lang('Enter a name for the query'),
					'lang_remark_statustext'		=> lang('Enter a remark'),
					'lang_apply_statustext'			=> lang('Apply the values'),
					'lang_cancel_statustext'		=> lang('Leave the budget untouched and return to the list'),
					'lang_save_statustext'			=> lang('Save the budget and return to the list'),


				);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . ($budget_id?lang('edit budget'):lang('add budget'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));

		}

		function edit_basis()
		{

			$acl_location	= '.budget';
			$acl_add 	= $this->acl->check($acl_location, PHPGW_ACL_ADD, 'property');
			$acl_edit 	= $this->acl->check($acl_location, PHPGW_ACL_EDIT, 'property');

			if(!$acl_add && !$acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $acl_location));
			}

			$budget_id	= phpgw::get_var('budget_id', 'int');

			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget'));

			if ((isset($values['save']) && $values['save'])|| (isset($values['apply']) && $values['apply']))
			{
				$values['ecodimb']	= phpgw::get_var('ecodimb');

				if(!$values['b_group'] && !$budget_id)
				{
					$receipt['error'][]=array('msg'=>lang('Please select a budget group !'));
				}


				if(!$values['district_id'] && !$budget_id)
				{
					$receipt['error'][]=array('msg'=>lang('Please select a district !'));
				}

				if(!$values['budget_cost'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a budget cost !'));
				}

				if(!$receipt['error'])
				{
					$values['budget_id']	= $budget_id;
					$receipt = $this->bo->save_basis($values);
					$budget_id = $receipt['budget_id'];

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','budget_basis_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.basis'));
					}
				}
				else
				{
					$year_selected = $values['year'];
					$district_id = $values['district_id'];
					$revision = $values['revision'];
					$b_group = $values['b_group'];

					unset ($values['year']);
					unset ($values['district_id']);
					unset ($values['revision']);
					unset ($values['b_group']);
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uibudget.basis'));
			}

			if ($budget_id)
			{
				$values = $this->bo->read_single_basis($budget_id);
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uibudget.edit_basis',
					'budget_id'	=> $budget_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$year[0]['id'] = date(Y);
			$year[1]['id'] = date(Y) +1;
			$year[2]['id'] = date(Y) +2;
			$year[3]['id'] = date(Y) +3;

			$ecodimb_data=$this->bocommon->initiate_ecodimb_lookup(array(
				'ecodimb'			=> $values['ecodimb'],
				'ecodimb_descr'		=> $values['ecodimb_descr']));


			$data = array
				(
					'ecodimb_data'						=>	$ecodimb_data,
					'lang_category'						=> lang('category'),
					'lang_no_cat'						=> lang('Select category'),
					'cat_select'						=> $this->cats->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $values['cat_id'])),
					'lang_distribute'					=> lang('distribute'),
					'lang_distribute_year'				=> lang('distribute year'),
					'lang_distribute_year_statustext'	=> lang('of years'),
					'distribute_year_list'				=> $this->bo->get_distribute_year_list($values['distribute_year']),

					'lang_revision'						=> lang('revision'),
					'lang_revision_statustext'			=> lang('Select revision'),
					'revision_list'						=> $this->bo->get_revision_list($revision),

					'lang_b_group'						=> lang('budget group'),
					'lang_b_group_statustext'			=> lang('Select budget group'),
					'b_group_list'						=> $this->bo->get_b_group_list($b_group),

					'lang_year'							=> lang('year'),
					'lang_year_statustext'				=> lang('Budget year'),
					'year'								=> $this->bocommon->select_list($year_selected,$year),

					'lang_district'						=> lang('District'),
					'lang_no_district'					=> lang('no district'),
					'lang_district_statustext'			=> lang('Select the district'),
					'select_district_name'				=> 'values[district_id]',
					'district_list'						=> $this->bocommon->select_district_list('select',$district_id),

					'value_year'						=> $values['year'],
					'value_district_id'					=> $values['district_id'],
					'value_b_group'						=> $values['b_group'],
					'value_revision'					=> $values['revision'],

					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_budget_id'					=> lang('ID'),
					'value_budget_id'					=> $budget_id,
					'value_distribute_id'				=> $budget_id?$budget_id:'new',
					'lang_budget_cost'					=> lang('budget cost'),
					'lang_remark'						=> lang('remark'),
					'lang_save'							=> lang('save'),
					'lang_cancel'						=> lang('cancel'),
					'lang_apply'						=> lang('apply'),
					'value_remark'						=> $values['remark'],
					'value_budget_cost'					=> $values['budget_cost'],
					'lang_name_statustext'				=> lang('Enter a name for the query'),
					'lang_remark_statustext'			=> lang('Enter a remark'),
					'lang_apply_statustext'				=> lang('Apply the values'),
					'lang_cancel_statustext'			=> lang('Leave the budget untouched and return to the list'),
					'lang_save_statustext'				=> lang('Save the budget and return to the list'),
				);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . ($budget_id?lang('edit budget'):lang('add budget'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_basis' => $data));

		}
		function delete()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int');
			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($budget_id);
				return "budget_id ".$budget_id." ".lang("has been deleted");
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uibudget.index'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete', 'budget_id'=> $budget_id)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname		= lang('budget');
			$function_msg		= lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));

		}

		function delete_basis()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int');
			//JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete_basis($budget_id);
				return "budget_id ".$budget_id." ".lang("has been deleted");
			}



			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uibudget.basis'
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete_basis($budget_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.delete_basis', 'budget_id'=> $budget_id)),
					'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
					'lang_yes'				=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'				=> lang('no')
				);

			$appname	= lang('budget');
			$function_msg	= lang('delete budget');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));

		}
		function view()
		{
			$budget_id	= phpgw::get_var('budget_id', 'int', 'GET');

			$GLOBALS['phpgw']->xslttpl->add_file(array('budget','nextmatchs'));

			$list= $this->bo->read_budget($budget_id);
			$uicols	= $this->bo->uicols;

			//_debug_array($uicols);

			$j=0;
			if (isSet($list) AND is_array($list))
			{
				foreach($list as $entry)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$content[$j]['row'][$i]['value'] = $entry[$uicols[$i]['name']];
					}

					$j++;
				}
			}

			for ($i=0;$i<count($uicols);$i++)
			{
				$table_header[$i]['header'] 	= $uicols[$i]['descr'];
				$table_header[$i]['width'] 	= '15%';
				$table_header[$i]['align'] 	= 'left';
			}

			//_debug_array($content);


			$budget_name = $this->bo->read_budget_name($budget_id);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('budget') . ': ' . $budget_name;

			$link_data = array
				(
					'menuaction'	=> 'property.uibudget.view',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'budget_id'	=>$budget_id,
					'filter'	=>$this->filter,
					'query'		=>$this->query
				);


			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_download = array
				(
					'menuaction'	=> 'property.uibudget.download',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'filter'	=>$this->filter,
					'query'		=>$this->query,
					'budget_id'	=>$budget_id,
					'allrows'	=> $this->allrows
				);

			$GLOBALS['phpgw']->js->validate_file('overlib','overlib','property');

			$data = array
				(
					'lang_download'					=> 'download',
					'link_download'					=> $GLOBALS['phpgw']->link('/index.php',$link_download),
					'lang_download_help'			=> lang('Download table to your browser'),

					'allow_allrows'					=> true,
					'allrows'						=> $this->allrows,
					'start_record'					=> $this->start,
					'record_limit'					=> $record_limit,
					'num_records'					=> count($list),
					'all_records'					=> $this->bo->total_records,
					'link_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'img_path'						=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
					'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_searchfield_statustext'	=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
					'lang_searchbutton_statustext'	=> lang('Submit the search string'),
					'query'							=> $this->query,
					'lang_search'					=> lang('search'),
					'table_header'					=> $table_header,
					'values'						=> $content,

					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uibudget.index')),
					'lang_done'						=> lang('done'),
				);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}

		function download()
		{
			switch (phpgw::get_var('download'))
			{
				case 'basis':
					$list= $this->bo->read_basis();
					$names = array
					(
						'year',
						'revision',
						'grouping',
						'district_id',
						'ecodimb',
						'category',
						'budget_cost'
					);
					$descr = array
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
					$list= $this->bo->read();
					$names = array
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
					$descr = array
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
					$gross_list= $this->bo->read_obligations();
					$sum_obligation = $sum_hits = $sum_budget_cost = $sum_actual_cost = 0;
					$list = array();
					foreach($gross_list as $entry)
					{
						$list[] = array
						(
							'grouping'			=> $entry['grouping'],
							'b_account'			=> $entry['b_account'],
							'district_id'		=> $entry['district_id'],
							'ecodimb'			=> $entry['ecodimb'],
							'hits'				=> $entry['hits'],
							'budget_cost'		=> $entry['budget_cost'],
							'obligation'		=> $entry['obligation'],
							'actual_cost'		=> $entry['actual_cost'],
							'diff'				=> ($entry['budget_cost'] - $entry['actual_cost'] - $entry['obligation']),
						);
					}
					$names = array
					(
						'grouping',
						'b_account',
						'district_id',
						'ecodimb',
						'hits',
						'budget_cost',
						'obligation',
						'actual_cost',
						'diff'
					);
					$descr = array
					(
						lang('grouping'),
						lang('budget account'),
						lang('district_id'),
						lang('dimb'),
						lang('hits'),
						lang('budget'),
						lang('sum orders'),
						lang('paid'),
						lang('difference')
					);
					break;
				default:
					return;
			}

			if($list)
			{
				$this->bocommon->download($list,$names,$descr);
			}
		}
	}

