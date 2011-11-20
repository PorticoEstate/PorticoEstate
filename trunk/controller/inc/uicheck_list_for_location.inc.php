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
	* @subpackage location
 	* @version $Id: class.uilocation.inc.php 7895 2011-10-19 06:58:43Z sigurdne $
	*/

	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.yui');

	class controller_uicheck_list_for_location extends controller_uicommon
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $currentapp;
		var $type_id;
		var $location_code;

		var $public_functions = array
			(
				'index'  				=> true,
			);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::location';
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->soadmin_location		= CreateObject('property.soadmin_location');
			$this->acl 					= & $GLOBALS['phpgw']->acl;

			$this->type_id				= $this->bo->type_id;

			$this->acl_location			= $this->bo->acl_location;
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->part_of_town_id		= $this->bo->part_of_town_id;
			$this->district_id			= $this->bo->district_id;
			$this->status				= $this->bo->status;
			$this->allrows				= $this->bo->allrows;
			$this->lookup				= $this->bo->lookup;
			$this->location_code		= $this->bo->location_code;
		}	
	
		function index()
		{

			$type_id	= $this->type_id;
			// $lookup use for pop-up
			$lookup 	= $this->lookup;
			// $lookup_name use in pop-up option "project"
			$lookup_name 	= phpgw::get_var('lookup_name');
			// use in option menu TENANT
			$lookup_tenant 	= phpgw::get_var('lookup_tenant', 'bool');
			$block_query	= phpgw::get_var('block_query', 'bool');
			$dry_run=false;

			if(!$type_id)
			{
				$type_id = 1;
			}
			if($lookup)
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}

			if ( $type_id && !$lookup_tenant )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::loc_$type_id";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::tenant';
			}

			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$second_display = phpgw::get_var('second_display', 'bool');
			$default_district 	= (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'])?$GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']:'');

			if ($default_district && !$second_display && !$this->district_id)
			{
				$this->bo->district_id	= $default_district;
				$this->district_id		= $default_district;
			}

			$datatable = array();
			$values_combo_box = array();

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
			$custom_config	= CreateObject('admin.soconfig',$location_id);
			$_config = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();

			$_integration_set = array();
			foreach ($_config as $_config_section => $_config_section_data)
			{
				$integrationurl = '';
				if(isset($_config_section_data['url']) && !isset($_config_section_data['tab']))
				{
					if(isset($_config_section_data['auth_hash_name']) && $_config_section_data['auth_hash_name'] && isset($_config_section_data['auth_url']) && $_config_section_data['auth_url'])
					{
						//get session key from remote system

						$arguments = array($_config_section_data['auth_hash_name'] => $_config_section_data['auth_hash_value']);
						$query = http_build_query($arguments);
						$auth_url = $_config_section_data['auth_url'];
						$request = "{$auth_url}?{$query}";

						$aContext = array
							(
								'http' => array
								(
									'request_fulluri' => true,
								),
							);

						if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
						{
							$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
						}

						$cxContext = stream_context_create($aContext);
						$response = trim(file_get_contents($request, False, $cxContext));
					}


					$_config_section_data['url']		= htmlspecialchars_decode($_config_section_data['url']);
					$_config_section_data['parametres']= htmlspecialchars_decode($_config_section_data['parametres']);
					$integration_name = isset($_config_section_data['name']) && $_config_section_data['name'] ? $_config_section_data['name'] : lang('integration');

					parse_str($_config_section_data['parametres'], $output);

					foreach ($output as $_dummy => $_substitute)
					{
						$_keys[] = $_substitute;
						$__substitute = trim($_substitute, '_');
						$_values[] = $this->$__substitute;
					}
					unset($output);

					$_sep = '?';
					if (stripos($_config_section_data['url'],'?'))
					{
						$_sep = '&';
					}
					$_param = str_replace($_keys, $_values, $_config_section_data['parametres']);

					$integrationurl = "{$_config_section_data['url']}{$_sep}{$_param}";
					$integrationurl .= "&{$_config_section_data['auth_key_name']}={$response}";

					$_config_section_data['location_data']= htmlspecialchars_decode($_config_section_data['location_data']);

					$parameters_integration = array();
					if($_config_section_data['location_data'])
					{
						parse_str($_config_section_data['location_data'], $output);

						foreach ($output as $_name => $_substitute)
						{
							if($_substitute == '__loc1__') // This one is a link...
							{
								$_substitute = '__location_code__';
							}

							$parameters_integration['parameter'][] = array
							(
								'name'		=> $_name,
								'source'	=> trim($_substitute, '_'),
							);
						}
					}
					
					$_integration_set[] = array
					(
						'name'			=> $integration_name,
						'parameters'	=> $parameters_integration,
						'url'			=> $integrationurl
					);
				}
			}

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				if(!$lookup)
				{
					$datatable['menu']				= $this->bocommon->get_menu();
				}

				$datatable['config']['base_url']	= $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uilocation.index',
						'type_id'        		=> $type_id,
						'query'            		=> $this->query,
						'district_id'        	=> $this->district_id,
						'part_of_town_id'    	=> $this->part_of_town_id,
						'lookup'        		=> $lookup,
						'lookup_tenant'        	=> $lookup_tenant,
						'lookup_name'        	=> $lookup_name,
						'cat_id'        		=> $this->cat_id,
						'status'        		=> $this->status,
						'location_code'			=> $this->location_code
					));
				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uilocation.index',"
					."type_id:'{$type_id}',"
					."query:'{$this->query}',"
					."district_id: '{$this->district_id}',"
					."part_of_town_id:'{$this->part_of_town_id}',"
					."lookup:'{$lookup}',"
					."second_display:1,"
					."lookup_tenant:'{$lookup_tenant}',"
					."lookup_name:'{$lookup_name}',"
					."cat_id:'{$this->cat_id}',"
					."status:'{$this->status}',"
					."location_code:'{$this->location_code}',"
					."block_query:'{$block_query}'";

				// $values_combo_box  se usar� para escribir en el HTML, usando el XSLT
				$values_combo_box[0]  = $this->bocommon->select_category_list(array
					('format'=>'filter',
					'selected' => $this->cat_id,
					'type' =>'location',
					'type_id' =>$type_id,
					'order'=>'descr')
				);
				$default_value = array ('id'=>'','name'=>lang('no category'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->select_district_list('filter',$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no district'));
				array_unshift ($values_combo_box[1],$default_value);

				$values_combo_box[2] =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
				$default_value = array ('id'=>'','name'=>lang('no part of town'));
				array_unshift ($values_combo_box[2],$default_value);

				if(isset($GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['property_filter'] == 'owner')
				{
					$values_combo_box[3] = $this->bo->get_owner_list('filter', $this->filter);
				}
				else
				{
					$values_combo_box[3] = $this->bo->get_owner_type_list('filter', $this->filter);
				}
				$default_value = array ('id'=>'','name'=>lang('show all'));
				array_unshift ($values_combo_box[3],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uilocation.index',
								'type_id' 			=> $type_id,
								'district_id'       => $this->district_id,
								'part_of_town_id'   => $this->part_of_town_id,
								'lookup'        	=> $lookup,
								'lookup_tenant'     => $lookup_tenant,
								'lookup_name'       => $lookup_name,
								'cat_id'        	=> $this->cat_id,
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	CATEGORY
									'id' => 'btn_cat_id',
									'name' => 'cat_id',
									'value'	=> lang('Category'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	DISTINT
									'id' => 'btn_district_id',
									'name' => 'district_id',
									'value'	=> lang('District'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								( //boton 	PART OF TOWN
									'id' => 'btn_part_of_town_id',
									'name' => 'part_of_town_id',
									'value'	=> lang('Part of Town'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 3
								),
								array
								( //boton 	FILTER
									'id' => 'btn_owner_id',
									'name' => 'owner_id',
									'value'	=> lang('Filter'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 4
								),
								//for link "columns", next to Export button
								array
								(
									'type' => 'link',
									'id' => 'btn_columns',
									'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
									array
									(
										'menuaction' => 'property.uilocation.columns',
										'type_id'  => $type_id,
										'lookup'  => $this->lookup
									))."','','width=300,height=600,scrollbars=1')",
									'value' => lang('columns'),
									'tab_index' => 9
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 8
								),
								array
								( //hidden type_id
									'type'	=> 'hidden',
									'id'	=> 'type_id',
									'value'	=> $type_id
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
								)
							)
						)
					)
				);

				$button_def[] = "oNormalButton_0";
				$button_def[] = "oNormalButton_1";
				$button_def[] = "oNormalButton_2";
				$code_inner[] = "{order:0, name:'btn_search',funct:'onSearchClick'}";
				$code_inner[] = "{order:1, name:'btn_new',	funct:'onNewClick'}";
				$code_inner[] = "{order:2, name:'btn_export',funct:'onDownloadClick'}";
				$_js_functions = '';

				foreach ($_integration_set as $i => $_integration)
				{	

					$button_def[] = 'oNormalButton_' . ($i + 3); 
					$code_inner[] = "{order:" . ($i + 3)  .", name:'btn_integration_{$i}',funct:'onIntegrationClick_{$i}'}";

					$datatable['actions']['form'][0]['fields']['field'][] =  array
					(
						'type'	=> 'button',
						'id'	=> "btn_integration_{$i}",
						'value'	=> $_integration['name'],
						'tab_index' => 10 + $i
					);

					$_js_functions .= <<<JS
						this.onIntegrationClick_{$i} = function()
						{
							window.open(values_ds.integrationurl_{$i},'window');
						}
JS;
				}

				$code = 'var ' . implode(',', $button_def)  . ";\n";
				$code .= 'var normalButtons = [' . "\n" . implode(",\n",$code_inner) . "\n];";
				$code .= $_js_functions;

				$GLOBALS['phpgw']->js->add_code('', $code);

				if(!$block_query)
				{	
					$datatable['actions']['form'][0]['fields']['field'][] =  array
						(
							'id' => 'btn_search',
							'name' => 'search',
							'value'    => lang('search'),
							'type' => 'button',
							'tab_index' => 6
						);

					$datatable['actions']['form'][0]['fields']['field'][] = array
						(
							'name'     => 'query',
							'id'     => 'txt_query',
							'value'    => $this->query,//'',//$query,
							'type' => 'text',
							'size'    => 28,
							'onkeypress' => 'return pulsar(event)',
							'tab_index' => 5
						);
				}

				if(!$lookup)
				{
					$datatable['actions']['form'][0]['fields']['field'][] =  array
						(
							'type'	=> 'button',
							'id'	=> 'btn_new',
							'value'	=> lang('add'),
							'tab_index' => 7
						);
				}

				$dry_run=true;

			}

			$location_list = array();
			//cramirez: $dry_run avoid to load all data the first time
			$location_list = $this->bo->read(array('type_id'=>$type_id,'lookup_tenant'=>$lookup_tenant,'lookup'=>$lookup,'allrows'=>$this->allrows,'dry_run' =>$dry_run));

			$uicols = $this->bo->uicols;

			$content = array();
			$j=0;
			if (isset($location_list) && is_array($location_list))
			{
				foreach($location_list as $location)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($location['query_location'][$uicols['name'][$i]]))
							{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $location[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $location['query_location'][$uicols['name'][$i]];
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $location[$uicols['name'][$i]];
								//$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $i;
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $location[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']		= $location[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
								}
							}
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
							$datatable['rows']['row'][$j]['column'][$i]['value']			= $location[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $location[$uicols['name'][$i]];
						$datatable['rows']['row'][$j]['hidden'][$i]['name'] 			= $uicols['name'][$i];
					}

					$j++;
				}
			}
			// NO pop-up
			$datatable['rowactions']['action'] = array();
			if(!$lookup)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'location_code',
								'source'	=> 'location_code'
							),
						)
					);

				$parameters2 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'sibling',
								'source'	=> 'location_code'
							),
						)
					);

				$parameters3 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'search_for',
								'source'	=> 'location_code'
							),
						)
					);

				if($this->acl->check('run', PHPGW_ACL_READ, 'rental'))
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'view',
							'text' 			=> lang('contracts'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	  => 'rental.uicontract.index',
								'search_type'	  => 'location_id',
								'contract_status' => 'all',
								'populate_form'   => 'yes'
							)),
							'parameters'	=> $parameters3
						);
						
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'view',
							'text' 			=> lang('composites'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	  => 'rental.uicomposite.index',
								'search_type'	  => 'location_id',
								'populate_form'   => 'yes'
							)),
							'parameters'	=> $parameters3
						);
				}
				
				

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'view',
							'text' 			=> lang('view'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.view',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> $parameters
						);
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'view',
							'text' 			=> lang('open view in new window'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.view',
								'lookup_tenant'	=> $lookup_tenant,
								'target'		=> '_blank'
							)),
							'parameters'	=> $parameters
						);
				}
				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.edit',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> $parameters2
						);
				}
				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.edit',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> $parameters
						);
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'edit',
							'text' 			=> lang('open edit in new window'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.edit',
								'lookup_tenant'	=> $lookup_tenant,
								'target'		=> '_blank'
							)),
							'parameters'	=> $parameters
						);

				}
				$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location)));

				foreach ($jasper as $report)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'edit',
							'text'	 		=> lang('open JasperReport %1 in new window', $report['title']),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uijasper.view',
								'jasper_id'			=> $report['id'],
								'target'		=> '_blank'
							)),
							'parameters'			=> $parameters
						);
				}

				foreach ($_integration_set as $_integration )
				{
					$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'integration',
						'text'	 		=> $_integration['name'],
						'action'		=> $_integration['url'].'&target=_blank',
						'parameters'	=> $_integration['parameters']
					);
				}

				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'delete',
							'text' 			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.delete',
								'lookup_tenant'	=> $lookup_tenant
							)),
							'parameters'	=> $parameters
						);
				}
				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uilocation.edit',
								'type_id'		=>	$type_id,
								'parent'		=>  $this->location_code
							))
						);
				}

				unset($parameters);
			}
			//$uicols_count indicates the number of columns to display in actuall option-menu. this variable was set in $this->bo->read()
			$uicols_count	= count($uicols['descr']);

			for ($i=0;$i<$uicols_count;$i++)
			{
				//all colums should be have formatter
				$datatable['headers']['header'][$i]['formatter'] = !isset($uicols['formatter'][$i])  || !$uicols['formatter'][$i] ?  '""' : $uicols['formatter'][$i];

				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;
					//$datatable['headers']['header'][$i]['formatter']		= (isset($uicols['formatter'][$i])? $uicols['formatter'][$i]:"");
					if($uicols['name'][$i]=='loc1')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'fm_location1.loc1';
					}
					else if($uicols['name'][$i]=='street_name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'street_name';
					}
					else if(isset($uicols['cols_return_extra'][$i]) && ($uicols['cols_return_extra'][$i]!='T' || $uicols['cols_return_extra'][$i]!='CH'))
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
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
			// for POP-UPs
			if($lookup)
			{
				$input_name		= $GLOBALS['phpgw']->session->appsession('lookup_fields','property');

				$function_exchange_values = '';

				if(is_array($input_name))
				{
					for ($k=0;$k<count($input_name);$k++)
					{
						$function_exchange_values .= "opener.document.forms[0]." . $input_name[$k] . ".value = '';" ."\r\n";
					}
				}

	/*			for ($i=0;$i<count($uicols['name']);$i++)
				{
					if(isset($uicols['exchange'][$i]) && $uicols['exchange'][$i])
					{
						$function_exchange_values .= 'opener.document.getElementsByName("'.$uicols['name'][$i].'")[0].value = "";' ."\r\n";
					}
				}
	 */
				for ($i=0;$i<count($uicols['name']);$i++)
				{
					if(isset($uicols['exchange'][$i]) && $uicols['exchange'][$i])
					{
						$function_exchange_values .= 'opener.document.getElementsByName("'.$uicols['name'][$i].'")[0].value = "";' ."\r\n";
						$function_exchange_values .= 'opener.document.getElementsByName("'.$uicols['name'][$i].'")[0].value = valida(data.getData("'.$uicols['name'][$i].'"));' ."\r\n";
						//$function_exchange_values .= 'opener.document.forms[0].' . $uicols['name'][$i] .'.value = valida(data.getData("'.$uicols['name'][$i].'"));' ."\r\n";
					}
				}

				$function_exchange_values .='window.close()';

				$datatable['exchange_values'] = $function_exchange_values;

				$function_valida  = "var pos = data.indexOf('</a>');"."\r\n";
				$function_valida .= "if(pos==-1){"."\r\n";
				$function_valida .= "return data;"."\r\n";
				$function_valida .= "}else{"."\r\n";
				$function_valida .= "pos = data.indexOf('>');"."\r\n";
				$function_valida .= "var valor = data.slice(pos+1);"."\r\n";
				$function_valida .= "pos = valor.indexOf('<');"."\r\n";
				$function_valida .= "valor = valor.slice(0,pos);"."\r\n";
				$function_valida .= "return valor;"."\r\n";
				$function_valida .= "}"."\r\n";

				$datatable['valida'] = $function_valida;
			}

			// path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			if($dry_run)
			{
				$datatable['pagination']['records_returned'] = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];			
			}
			else
			{
				$datatable['pagination']['records_returned']= count($location_list);
			}

			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'loc1'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
				$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
			}

			$appname = lang('location');

			if($lookup)
			{
				$lookup_list	= $GLOBALS['phpgw']->session->appsession('lookup_name','property');
				$function_msg	= $lookup_list[$lookup_name];
			}
			else
			{
				if($lookup_tenant)
				{
					$function_msg	= lang('Tenant');
				}
				else
				{
					$function_msg					= $uicols['descr'][($type_id)];
				}
			}


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

				foreach ($_integration_set as $i => $_integration)
				{	
					$json["integrationurl_{$i}"]	= $_integration['url'];
				}

			// values for datatable
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row'])){
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && isset($column['java_link']) && $column['java_link']==true)
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

			// values for control select
			//cr@ccfirst.com 10/09/08 values passed for update select in YUI
			$opt_cb_depend =  $this->bocommon->select_part_of_town('filter',$this->part_of_town_id,$this->district_id);
			$default_value = array ('id'=>'','name'=>'!no part of town');
			array_unshift ($opt_cb_depend,$default_value);

			$json['hidden']['dependent'][] = array ( 'id' => $this->part_of_town_id,
				'value' => $this->bocommon->select2String($opt_cb_depend)
			);

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

			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			//// cramirez: necesary for include a partucular js
			phpgwapi_yui::load_widget('loader');
			//cramirez: necesary for use opener . Avoid error JS
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			//FIXME this one is only needed when $lookup==true - so there is probably an error
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

			//Title of Page
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'location.index', 'property' );

			//$this->save_sessiondata();
		}
	}