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
	* @subpackage eco
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');
	class property_uiinvestment
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $currentapp;

		var $public_functions = array
			(
				'index'		=> true,
				'history'	=> true,
				'add'		=> true,
				'delete'	=> true
			);

		function property_uiinvestment()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::invoice::investment';

			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject('property.boinvestment',true);
			$this->bocommon			= CreateObject('property.bocommon');
			$this->bolocation		= CreateObject('property.bolocation');
			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.invoice';
			$this->acl_read 		= $this->acl->check('.invoice', PHPGW_ACL_READ, 'property');
			$this->acl_add 			= $this->acl->check('.invoice', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 		= $this->acl->check('.invoice', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 		= $this->acl->check('.invoice', PHPGW_ACL_DELETE, 'property');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->part_of_town_id	= $this->bo->part_of_town_id;
			$this->allrows			= $this->bo->allrows;
			$this->admin_invoice	= $this->acl->check('.invoice', 16, 'property');
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
					'this->allrows'		=> $this->allrows
				);
			$this->bo->save_sessiondata($data);
		}


		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$preserve	= phpgw::get_var('preserve', 'bool');
			$values		= phpgw::get_var('values');
			$msgbox_data= "";

			if($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start			= $this->bo->start;
				$this->query			= $this->bo->query;
				$this->sort				= $this->bo->sort;
				$this->order			= $this->bo->order;
				$this->filter			= $this->bo->filter;
				$this->cat_id			= $this->bo->cat_id;
				$this->part_of_town_id	= $this->bo->part_of_town_id;
				$this->allrows			= $this->bo->allrows;
			}

			if($values && phpgw::get_var('phpgw_return_as') == 'json')
			{
//				_debug_array($values);
				$receipt	=$this->update_investment($values);
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}


			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'		=> 'property.uiinvestment.index',
						'chapter_id'		=> $this->chapter_id,
						'cat_id'			=> $this->cat_id,
						'part_of_town_id'	=> $this->part_of_town_id,
						'filter'			=> $this->filter 	               
					)
				);

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction	:'property.uiinvestment.index',"
					."chapter_id: '{$this->chapter_id}',"
					."cat_id: '{$this->chapter_id}',"
					."part_of_town_id: '{$this->part_of_town_id}',"
					."filter: '{$this->filter}'";

				$values_combo_box[0] = $this->bo->select_category('select',$this->cat_id);				
				$default_value = array ('id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_part_of_town('',$this->part_of_town_id);
				//$values_combo_box[1] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('Part of town'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2]  = $this->bo->filter('select',$this->filter);
				$default_value = array ('id'=>'','name'=>lang('Show all'));
				array_unshift ($values_combo_box[2],$default_value);	

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uiinvestment.index'/*,
								'query'            	=> $this->query,
								'chapter_id'		=> $this->chapter_id*/
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //container of  control's Form
									'type'	=> 'label',
									'id'	=> 'controlsForm_container',
									'value'	=> ''
								),
								array
								( //category
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //User pafrt of town
									'id' => 'btn_part_of_town_id',
									'name' => 'part_of_town_id',
									'value'	=> lang('Part of Town'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //User filter
									'id' => 'btn_filter',
									'name' => 'filter',
									'value'	=> lang('Filter'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
								array
								( // boton ADD
									'type'	=> 'button',//'submit',
									'id'	=> 'btn_new',
									'tab_index' => 4,
									'value'	=> lang('add')
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0])
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
								)
							)
						)
					)
				);
				$datatable['actions']['down-toolbar'] = array
					(
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //container of  control's Form
									'type'	=> 'label',
									'id'	=> 'controlsForm_container',
									'value'	=> ''
								),array
								( // Voucher link
									'type' 	=> 'link',
									'id' 	=> 'lnk_index',
									'url' 	=> "",
									'value' => lang('New index'),
									'tab_index' => 5,
									'style' => 'filter'
								),
								array
								( // Voucher box
									'name'	=> 'values[new_index]',
									'id'	=> 'txt_index',
									'value' => '',
									'type'	=> 'text',
									'size'	=> 8,
									'tab_index' => 6,
									'class' => 'myValuesForPHP down-toolbar_button',
									'style' => 'filter'
								),
								array
								( // imag calendar1
									'type'	=> 'img',
									'id'	=> 'start_date-trigger',
									'src'	=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
									'alt'	=> lang('Select date'),
									'tab_index' => 7,
									'style' => 'filter'
								),
								array
								( // calendar1 start_date
									'type'	=> 'text',
									'name'	=> 'values[date]',
									'id'	=> 'start_date',
									'value' => '',
									'size'  => 7,
									'readonly' => 'readonly',
									'tab_index' => 8,
									'class' => 'myValuesForPHP down-toolbar_button',
									'style' => 'filter'
								),
								array
								( //boton   SEARCH
									'id' => 'btn_update',
									'name' => 'update',
									'value'    => lang('Update'),
									'tab_index' => 9,
									'type' => 'button',
									'style' => 'filter'
								)
							)
						)
					);
			}	


			$uicols = array (
				array(
					'visible'=>false,	'name'=>order_dummy,	'label'=>'',					'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>district_id,	'label'=>lang('District'),		'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>part_of_town,	'label'=>lang('Part of town'),	'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>entity_id,		'label'=>lang('entity id'),		'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>investment_id,	'label'=>lang('investment id'),	'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>descr,			'label'=>lang('Descr'),			'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>entity_name,	'label'=>lang('Entity name'),	'className'=>'leftClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>false,	'name'=>initial_value_ex,'label'=>'',					'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>initial_value,	'label'=>lang('Initial value'),	'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>'myFormatCount2'),
				array(
					'visible'=>false,	'name'=>value_ex,		'label'=>'',					'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>value,			'label'=>lang('Value'),			'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>'myFormatCount2'),
				array(
					'visible'=>true,	'name'=>this_index,		'label'=>lang('Last index'),	'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>false,	'name'=>this_write_off_ex,'label'=>'',					'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>this_write_off,	'label'=>lang('Write off'),		'className'=>'rightClasss',	'sortable'=>false,	'sort_field'=>'',	'formatter'=>'myFormatCount2'),
				array(
					'visible'=>true,	'name'=>date,			'label'=>lang('Date'),			'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>index_count,	'label'=>lang('Index count'),	'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>link_history,	'label'=>lang('History'),		'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>''),
				array(
					'visible'=>true,	'name'=>check,			'label'=>lang('Select'),		'className'=>'centerClasss','sortable'=>false,	'sort_field'=>'',	'formatter'=>'')
				);
			$investment_list = $this->bo->read();

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')]	= 'Y';
			$dlarr[strpos($dateformat,'m')] = 'm';
			$dlarr[strpos($dateformat,'d')] = 'd';
			ksort($dlarr);
			$dateformat	= (implode($sep,$dlarr));

			$GLOBALS['phpgw']->jqcal->add_listener('start_date');
			$counter = $sum_initial_value = $sum_value = 0;

			while (is_array($investment_list) && list(,$investment) = each($investment_list))
			{
				$link_history = $check = "";
				if($this->admin_invoice)
				{
					$link_history = "<a href=\"".$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.history', 'entity_id'=> $investment['entity_id'], 'investment_id'=> $investment['investment_id'], 'entity_type'=> $this->cat_id))."\">".lang('History')."</a>";
					if($investment['value']!= 0)
					{
						$check = "<input type=\"hidden\" name=\"values[update][".$counter."]\" value=\"\" class=\"myValuesForPHP select_hidden\"  />";
						$check .= "<input type=\"checkbox\" name=\"values[update_tmp][".$counter."]\" value=\"".$counter."\" class=\"select_check\"  />";
					}
				}

				$my_district = "";
				$my_district .= "<input type=\"hidden\" name=\"values[entity_id][".$counter."]\" value=\"".$investment['entity_id']."\" class=\"myValuesForPHP\"  />";
				$my_district .= "<input type=\"hidden\" name=\"values[investment_id][".$counter."]\" value=\"".$investment['investment_id']."\" class=\"myValuesForPHP\"  />";
				$my_district .= "<input type=\"hidden\" name=\"values[initial_value][".$counter."]\" value=\"".$investment['initial_value']."\" class=\"myValuesForPHP\"  />";
				$my_district .= "<input type=\"hidden\" name=\"values[value][".$counter."]\" value=\"".$investment['value']."\" class=\"myValuesForPHP\"  />";
				$my_district .= $investment['district_id'];

				$content[] = array
					(
						'order_dummy'		=> $investment['part_of_town'],
						'district_id'		=> $my_district,	
						'part_of_town'		=> $investment['part_of_town'],			
						'entity_id'			=> $investment['entity_id'],
						'investment_id'		=> $investment['investment_id'],
						'descr'				=> $investment['descr'],
						'entity_name'		=> $investment['entity_name'],
						'initial_value_ex'	=> ($investment['initial_value']==""?0:$investment['initial_value']),
						'initial_value'		=> number_format($investment['initial_value'], 0, ',', ''), //to avoid error in YUI's sum
						'value_ex'			=> ($investment['value']==""?0:$investment['value']),
						'value'				=> number_format($investment['value'], 0, ',', ''),//to avoid error in YUI's sum
						'this_index'		=> $investment['this_index'],
						'this_write_off_ex'	=> $investment['this_write_off'],
						'this_write_off'	=> number_format($investment['this_write_off'], 0, ',', ''),
						'date'				=> date($dateformat,strtotime($investment['date'])),
						'index_count'		=> $investment['index_count'],
						'link_history'		=> $link_history,
						'check'				=> $check
					);

				//$sum_initial_value	+= $investment['initial_value'];
				//$sum_value			+= $investment['value'];
				//$sum_this_write_off_ex			+= $investment['this_write_off'];
				$counter++;
			}	

			$j=0;
			if (isset($content) && is_array($content))
			{
				foreach($content as $investment)
				{
					for ($i=0;$i<count($uicols);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 		= $uicols[$i]['name'];
						$datatable['rows']['row'][$j]['column'][$i]['value']		= $investment[$uicols[$i]['name']];
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();
			$datatable['rowactions']['action'][] = array(
				'my_name'		=> 'add',
				'text' 			=> lang('add'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.add'))
			);


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
			//$datatable['pagination']['records_returned']= count($content);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				//avoid ,in the last page, reformate paginator when records are lower than records_returned
				if(count($content) <= $datatable['pagination']['records_limit'])
				{
					$datatable['pagination']['records_returned']= count($content);
				}
				else
				{
					$datatable['pagination']['records_returned']= $datatable['pagination']['records_limit'];
				}

				$datatable['sorting']['currentPage']	= 1;
				$datatable['sorting']['order'] 			= $uicols[0]['name']; // name key Column in myColumnDef
				$datatable['sorting']['sort']			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['currentPage']	= phpgw::get_var('currentPage');
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort']			= phpgw::get_var('sort', 'string'); // ASC / DESC
				$datatable['pagination']['records_returned']= phpgw::get_var('recordsReturned', 'int');
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
					'currentPage'		=> $datatable['sorting']['currentPage'],
					'records'			=> array()
				);

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						$json_row[$column['name']] = $column['value'];
					}
					$json['records'][] = $json_row;
				}
			}

			// right in datatable
			if(isset($datatable['rowactions']['action']) && is_array($datatable['rowactions']['action']))
			{
				$json ['rights'] = $datatable['rowactions']['action'];
			}
			$json['message']			= $GLOBALS['phpgw']->common->msgbox($msgbox_data);
			//$json ['sum_initial_value'] = number_format($sum_initial_value, 0, ',', '');
			//$json ['sum_value'] 		= number_format($sum_value, 0, ',', '');
			//$json['sum_this_write_off_ex'] 		= number_format($sum_this_write_off_ex, 0, ',', '');
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
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . lang('investment') . ': ' . lang('list investment');

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'investment.index', 'property' );

			//$this->save_sessiondata();			
		}


		function update_investment($values='')
		{
			//_debug_array($values);

			$receipt = array();

			if(!$values['date'])
			{
				$receipt['error'][]=array('msg'=>lang('Please select a date !'));
			}
			if(!$values['new_index'])
			{
				$receipt['error'][]=array('msg'=>lang('Please set a new index !'));
			}
			if(!$values['update'])
			{
				$receipt['error'][]=array('msg'=>lang('Nothing to do!'));
			}

			if(!$receipt['error'])
			{
				$receipt=$this->bo->update_investment($values);
			}
			return $receipt;
		}

		function history()
		{

			$values		= phpgw::get_var('values');
			$entity_type	= phpgw::get_var('entity_type');
			$entity_id	= phpgw::get_var('entity_id', 'int');
			$investment_id	= phpgw::get_var('investment_id', 'int');

			if($values)
			{
				$receipt= $this->update_investment($values);
				$msgbox_data = $this->bocommon->msgbox_data($receipt);
			}

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uiinvestment.history',
						'entity_id'	=> $entity_id, 
						'investment_id'	=> $investment_id, 
						'entity_type'		=> $entity_type
					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiinvestment.history',"
					."entity_id:'{$entity_id}',"
					."investment_id:'{$investment_id}',"
					."entity_type:'{$entity_type}'";

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uiinvestment.history',
								'entity_id'			=> $entity_id, 
								'investment_id'		=> $investment_id, 
								'entity_type'		=> $entity_type								
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( // mensaje
									'type'	=> 'label',
									'id'	=> 'msg_header',
									'value'	=> '',
									'style' => 'filter'
								),
								array
								( // mensaje
									'type'	=> 'label',
									'id'	=> 'msg',
									'value'	=> '',
									'style' => 'filter'
								),													
								array
								( // boton done
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'tab_index' => 1,
									'value'	=> lang('done')
								)																								
							),
							'hidden_value' => array
							(
								)
							)
						)
					);

			}

			$datatable['actions']['down-toolbar'] = array
				(
					'fields'	=> array
					(
						'field' => array
						(
							array
							( // link New index
								'type' 	=> 'link',
								'id' 	=> 'lnk_index',
								'url' 	=> "",
								'value' => lang('New index'),
								'tab_index' => 6,
								'style' => 'filter'
							),
							array
							( // New index box
								'name'	=> 'values[new_index]',
								'id'	=> 'txt_index',
								'value' => '',
								'type'	=> 'text',
								'size'	=> 8,
								'tab_index' => 2,
								'class' => 'myValuesForPHP down-toolbar_button',
								'style' => 'filter'
							),
							array
							( // imag calendar1
								'type'	=> 'img',
								'id'	=> 'start_date-trigger',
								'src'	=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
								'alt'	=> lang('Select date'),
								'tab_index' => 5,
								'style' => 'filter'
							),
							array
							( // calendar1 start_date
								'type'	=> 'text',
								'name'	=> 'values[date]',
								'id'	=> 'start_date',
								'value' => '',
								'size'  => 7,
								'readonly' => 'readonly',
								'tab_index' => 3,
								'class' => 'myValuesForPHP down-toolbar_button',
								'style' => 'filter'
							),
							array
							( //hidden update
								'type'	=> 'hidden',
								'name'     => 'values[update][0]',
								'value'	=> 0,
								'class' => 'myValuesForPHP',
								'style' => 'filter'
							),	   
							array
							( //hidden entity_id
								'type'	=> 'hidden',
								'name'     => 'values[entity_id][0]',
								'value'	=> $entity_id,
								'class' => 'myValuesForPHP',
								'style' => 'filter'
							),	
							array
							( //hidden investment_id
								'type'	=> 'hidden',
								'name'     => 'values[investment_id][0]',
								'value'	=> $investment_id,
								'class' => 'myValuesForPHP',
								'style' => 'filter'
							),			                            		                                                         
							array
							( //boton   SEARCH
								'id' => 'btn_update',
								'name' => 'update',
								'value'    => lang('Update'),
								'tab_index' => 4,
								'type' => 'button',
								'style' => 'filter'
							)
						)
					)
				); 

			$investment_list = $this->bo->read_single($entity_id,$investment_id);

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] 		= 'Y';
			$dlarr[strpos($dateformat,'m')] 		= 'm';
			$dlarr[strpos($dateformat,'d')] 		= 'd';
			ksort($dlarr);
			$dateformat								= (implode($sep,$dlarr));

			$GLOBALS['phpgw']->jqcal->add_listener('start_date');

			$uicols = array (
				'input_type'	=>	array('text','text','text','text','text','text','hidden'),
				'name'			=>	array('initial_value','value','this_index','this_write_off','date','index_count','is_admin'),
				'formatter'		=>	array('','','','','','',''),
				'descr'			=>	array(lang('Initial value'),lang('Value'),lang('Last index'),lang('Write off'),lang('Date'),lang('Index count'),''),
				'className'		=> 	array('rightClasss','rightClasss','rightClasss','rightClasss','','rightClasss','')
			);


			$j=0;
			if (isset($investment_list) && is_array($investment_list))
			{
				foreach($investment_list as $investment)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];
						if ($uicols['name'][$i] == 'date')
						{							
							$datatable['rows']['row'][$j]['column'][$i]['value']	= date($dateformat,strtotime($investment[$uicols['name'][$i]]));								
						}
						else if ($uicols['name'][$i] == 'is_admin') 
						{
							$datatable['rows']['row'][$j]['column'][$i]['value']	= $this->admin_invoice;
						} 
						else
						{
							if ($uicols['name'][$i] == 'initial_value' || $uicols['name'][$i] == 'value' || $uicols['name'][$i] == 'this_write_off')
							{
								$datatable['rows']['row'][$j]['column'][$i]['value']	= number_format($investment[$uicols['name'][$i]], 0, ',', '');							
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value']	= $investment[$uicols['name'][$i]];
							}
						}
					}
					$j++;
				}
			}			

			$datatable['rowactions']['action'] = array();

			$info = array ();
			$info[0]['name'] = lang('Entity Type');		
			$info[0]['value'] = lang($entity_type);
			$info[1]['name'] = lang('Entity Id');		
			$info[1]['value'] = $entity_id;																												
			$info[2]['name'] = lang('Investment Id');		
			$info[2]['value'] = $investment_id;			

			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['className']		= $uicols['className'][$i];

					/*if($uicols['name'][$i]=='initial_value')
					{
						$datatable['headers']['header'][$i]['sortable']		= false;
						$datatable['headers']['header'][$i]['sort_field'] 	= '';
					}		*/
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

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($investment_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;			

			$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column

			$appname	= lang('investment');
			$function_msg	= lang('investment history');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order']		= ''; // name key Column in myColumnDef
				$datatable['sorting']['sort']		= ''; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']   	= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 		= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('tabview');


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
					'info'				=> $info
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
			$json ['message']			= $GLOBALS['phpgw']->common->msgbox($msgbox_data);

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
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'investment.history', 'property' );

		}


		function add()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}
			$values					= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('investment'));

			if (isset($values['save']) && $values['save'])
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);

				if(!$values['type'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a type !'));
				}

				if(!$values['period'] && !$values['new_period'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a period for write off !'));
				}

				if(!$values['date'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a date !'));
				}

				if(!$values['initial_value'])
				{
					$receipt['error'][]=array('msg'=>lang('Please set an initial value!'));
				}

				if(!$values['location']['loc1'] && !$values['extra']['p_num'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location - or an entity!'));
				}

				//_debug_array($values['extra']);
				if(!$receipt['error'])
				{
					$receipt=$this->bo->save_investment($values);
					unset($values);
				}
				else
				{
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['location_data'] = $this->bolocation->read_single($location_code,$values['extra']);
					}

					if($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
					}
				}
			}

			$location_data=$this->bolocation->initiate_ui_location(array
				(
					'values'	=> $values['location_data'],
					'type_id'	=> -1, // calculated from location_types
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'lookup_type'	=> 'form',
					'lookup_entity'	=> $this->bocommon->get_lookup_entity('investment'),
					'entity_data'	=> $values['p']
				)
			);


			$link_data = array
				(
					'menuaction'	=> 'property.uiinvestment.add'
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$GLOBALS['phpgw']->jqcal->add_listener('values_date');

			$data = array
				(
					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'location_data'						=> $location_data,
					'lang_date_statustext'				=> lang('insert the date for the initial value'),
					'lang_date'							=> lang('Date'),
					'lang_location'						=> lang('Location'),
					'lang_select_location_statustext'	=> lang('select either a location or an entity'),
					'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.index', 'preserve'=>1)),
					'lang_write_off_period'				=> lang('Write off period'),
					'lang_new'							=> lang('New'),
					'lang_select'						=> lang('Select'),
					'cat_list'							=> $this->bo->write_off_period_list($values['period']),
					'lang_descr'						=> lang('Description'),
					'lang_type'							=> lang('Type'),
					'lang_amount'						=> lang('Amount'),
					'lang_value_statustext'				=> lang('insert the value at the start-date as a positive amount'),
					'lang_new_period_statustext'		=> lang('Enter a new writeoff period if it is NOT in the list'),
					'filter_list'						=> $this->bo->filter('select',$values['type']),
					'filter_name'						=> 'values[type]',
					'lang_filter_statustext'			=> lang('Select the type of value'),
					'lang_show_all'						=> lang('Select'),
					'lang_name'							=> lang('name'),
					'lang_save'							=> lang('save'),
					'lang_done'							=> lang('done'),
					'value_new_period'					=> $values['new_period'],
					'value_inital_value'				=> $values['initial_value'],
					'value_date'						=> $values['date'],
					'value_descr'						=> $values['descr'],
					'lang_done_statustext'				=> lang('Back to the list'),
					'lang_save_statustext'				=> lang('Save the investment'),
					'lang_no_cat'						=> lang('Select'),
					'lang_cat_statustext'				=> lang('Select the category the investment belongs to. To do not use a category select NO CATEGORY'),
					'select_name'						=> 'values[period]',
					'investment_type_id'				=> $investment['investment_type_id']
				);

			$appname		= lang('investment');
			$function_msg	= lang('add investment');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('add' => $data));
		}

		function delete()
		{
			$entity_id = phpgw::get_var('entity_id', 'int');
			$investment_id = phpgw::get_var('investment_id', 'int');
			$index_count = phpgw::get_var('index_count', 'int');
			$entity_type = phpgw::get_var('entity_type');

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction'	=> 'property.uiinvestment.history',
					'entity_id'	=> $entity_id,
					'investment_id'	=> $investment_id,
					'index_count'	=> $index_count,
					'entity_type'	=> $entity_type
				);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{

				$this->bo->delete($entity_id,$investment_id,$index_count);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiinvestment.delete', 'entity_id'=> $entity_id, 'investment_id'=> $investment_id, 'index_count'=> $index_count, 'entity_type'=> $entity_type)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname	= lang('investment');
			$function_msg	= lang('delete investment history element');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}
