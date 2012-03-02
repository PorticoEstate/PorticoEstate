<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgroupware
	* @subpackage property
	* @category core
 	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * ResponsibleMatrix - handles automated assigning of tasks based on (physical)location and category.
	 *
	 * @package phpgroupware
	 * @subpackage property
	 * @category core
	 */
	 
	phpgw::import_class('phpgwapi.yui');
	
	class property_uiresponsible
	{

		/**
		* @var integer $start for pagination
		*/
		protected $start = 0;

		/**
		 * @var string $sort how to sort the queries - ASC/DESC
		 */
		protected $sort;

		/**
		 * @var string $order field to order by in queries
		 */
		protected $order;

		/**
		 * @var object $nextmatchs paging handler
		 */
		private $nextmatchs;

		/**
		 * @var object $bo business logic
		 */
		protected $bo;

		/**
		 * @var object $acl reference to global access control list manager
		 */
		protected $acl;

		/**
		 * @var string $acl_location the access control location
		 */
		protected $acl_location;

		/**
		 * @var string $appname the application name
		 */
		protected $appname;

		/**
		 * @var bool $acl_read does the current user have read access to the current location
		 */
		protected $acl_read;

		/**
		 * @var bool $acl_add does the current user have add access to the current location
		 */
		protected $acl_add;

		/**
		 * @var bool $acl_edit does the current user have edit access to the current location
		 */
		protected $acl_edit;

		/**
		 * @var bool $allrows display all rows of result set?
		 */
		protected $allrows;

		/**
		 * @var array $public_functions publicly available methods of the class
		 */
		public $public_functions = array
			(
				'index' 		=> true,
				'contact' 		=> true,
				'edit' 			=> true,
				'edit_role'		=> true,
				'edit_contact' 	=> true,
				'no_access'		=> true,
				'delete_type'	=> true
			);

		/**
		 * Constructor
		 */

		public function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->bo					= CreateObject('property.boresponsible', true);
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location 		= $this->bo->get_acl_location();
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->bolocation			= CreateObject('preferences.boadmin_acl');
			$this->appname				= $this->bo->appname;
			$this->bolocation->acl_app 	= $this->appname;
			$this->location				= $this->bo->location;
			$this->cats					= & $this->bo->cats;
			$this->query				= $this->bo->query;
			$this->allrows				= $this->bo->allrows;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->cat_id				= $this->bo->cat_id;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$this->appname}::responsible_matrix";
		}

		/**
		 * Save sessiondata
		 *
		 * @return void
		 */

		private function _save_sessiondata()
		{
			$data = array
				(
					'start'		=> $this->start,
					'query'		=> $this->query,
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'location'	=> $this->location,
					'allrows'	=> $this->allrows,
					'cat_id'	=> $this->cat_id
				);
			$this->bo->save_sessiondata($data);
		}

		/**
		 * list available responsible types
		 *
		 * @return void
		 */

		public function index()
		{
			$bocommon	= CreateObject('property.bocommon');

			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$lookup = phpgw::get_var('lookup', 'bool');

			if($lookup)
			{
				$GLOBALS['phpgw_info']['flags']['noframework']	= true;
				$GLOBALS['phpgw_info']['flags']['headonly']		= true;
			}

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{


				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'=> 'property.uiresponsible.index',
						'query'		=> $this->query,
						'location'	=> $this->location,
						'lookup'	=> $lookup,
						'appname'	=> $this->appname

					));

				$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiresponsible.index',"	    											
					."query:'{$this->query}',"
					."location:'{$this->location}',"
					."lookup:'{$lookup}',"          
					."appname:'{$this->appname}'";          

				$values_combo_box = array();

				$locations = $GLOBALS['phpgw']->locations->get_locations(false, $this->appname, false, false, true);
				foreach ( $locations as $loc_id => $loc_descr )
				{
					$values_combo_box[0][] = array
					(
						'id'	=> $loc_id,
						'name'	=> "{$loc_id} [{$loc_descr}]",
					);
				}

				$default_value = array ('id'=>'','name'=>lang('No location'));
				array_unshift ($values_combo_box[0],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 	=> 'property.uiresponsible.index',
								'query'		=> $this->query,
								'location'	=> $this->location,
								'lookup'	=> $lookup,
								'appname'	=> $this->appname
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								(
									'type' => 'select',
									'id' => 'sel_location',
									'name' => 'location',
									'value'	=> lang('location'),			                                            
									'style' => 'filter',
									'values' => $values_combo_box[0],
									'onchange'=> 'onChangeSelect();',
									'tab_index' => 1
								),					                                        	                                        
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 4
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 3
								),
								array
								( // TEXT IMPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 2
								)		                                        
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $bocommon->select2String($values_combo_box[0], 'id','name') //i.e.  id,value/id,vale/
								)
							)
						)
					)
				);
			}

			$responsible_info = array();
			$responsible_info = $this->bo->read_type();

			$uicols = array (
				'input_type'	=>	array('text','text','text','hidden','hidden','hidden','text','hidden','hidden','hidden'),
				'name'			=>	array('id','name','descr','category','created_by','created_on','appname','active','loc','location'),
				'formatter'		=>	array('','','','','','','','','',''),
				'descr'			=>	array(lang('id'),lang('name'),lang('descr'),lang('category'),lang('user'),'',lang('application'),lang('active'),'','')
			);

			$j=0;
			if (isset($responsible_info) && is_array($responsible_info))
			{
				foreach($responsible_info as $entry)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{							
						$datatable['rows']['row'][$j]['column'][$i]['name'] 	= $uicols['name'][$i];

						if ($uicols['name'][$i] == 'active')
						{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= ($entry[$uicols['name'][$i]] == 1 ? 'X' : '');
						}
						else if ($uicols['name'][$i] == 'loc')
						{
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= str_replace('property', '', $entry['app_name']);
						}
						else {
							$datatable['rows']['row'][$j]['column'][$i]['value'] 	= $entry[$uicols['name'][$i]];
						}
					}
					$j++;
				}
			}


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

					if($uicols['name'][$i]=='name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'name';
					}		
					else if($uicols['name'][$i]=='id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field'] 	= 'id';
					}		
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}				

			$datatable['rowactions']['action'] = array();

			if(!$lookup)
			{
				$parameters = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'id',
								'source'	=> 'id'
							),					
						)
					);

				$parameters2 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'type_id',
								'source'	=> 'id'
							),
							array
							(
								'name'		=> 'location',
								'source'	=> 'location'
							)
						)
					);

				$parameters3 = array
					(
						'parameter' => array
						(
							array
							(
								'name'		=> 'id',
								'source'	=> 'id'
							),
							array
							(
								'name'		=> 'location',
								'source'	=> 'location'
							),						
						)
					);

				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'		=> 'edit',
							'text' 			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiresponsible.edit',
								'appname'	=> $this->appname
//								'location'		=> $this->location
							)),
							'parameters'	=> $parameters3
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
								'menuaction'	=> 'property.uiresponsible.delete_type',
								'appname'	=> $this->appname
							)),
							'parameters'	=> $parameters
						);
				}

				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'contacts',
						'text' 			=> lang('contacts'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uiresponsible.contact',
							'appname'	=> $this->appname
						)),
						'parameters'	=> $parameters2
					);

				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name'			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'	=> 'property.uiresponsible.edit',
								'appname'		=> $this->appname,
								'location'		=> $this->location
							))
						);
				}				

				unset($parameters);
			}

			if($lookup)
			{

				$function_exchange_values = '';

				$function_exchange_values .= 'opener.document.getElementsByName("responsibility_id")[0].value = "";' ."\r\n";
				$function_exchange_values .= 'opener.document.getElementsByName("responsibility_name")[0].value = "";' ."\r\n";

				$function_exchange_values .= 'opener.document.getElementsByName("responsibility_id")[0].value = data.getData("id");' ."\r\n";
				$function_exchange_values .= 'opener.document.getElementsByName("responsibility_name")[0].value = data.getData("name");' ."\r\n";

				$function_exchange_values .= 'window.close()';

				$datatable['exchange_values'] = $function_exchange_values;
				$datatable['valida'] = '';

			}																																																													

			// path for property.js
			$datatable['property_js'] =  $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned'] = count($responsible_info);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'name'; // name key Column in myColumnDef
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
							$json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
						}
						else
						{
							$json_row[$column['name']] = $column['value'];
						}
					}
					$json['records'][] = $json_row;
				}
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'responsible_receipt');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_receipt', '');

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
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');

			//Title of Page
			$function_msg= lang('list available responsible types');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";

			// Prepare YUI Library
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'responsible.index', 'property' );

			$this->_save_sessiondata();
		}


		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$id			= phpgw::get_var('id', 'int');
			$location	= phpgw::get_var('location', 'string');
			$values		= phpgw::get_var('values');

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
	//				$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

				if(!isset($values['location']) || !$values['location'])
				{
//					$receipt['error'][]=array('msg'=>lang('Please select a location!'));
				}

				if(!isset($values['name']) || !$values['name'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a name!'));
				}

				if($id)
				{
					$values['id']=$id;
				}
				else
				{
					$id = $values['id'];
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_type($values);
					$id = $receipt['id'];

					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','responsible_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiresponsible.index', 'appname' => $this->appname));
					}
				}
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiresponsible.index', 'appname' => $this->appname));
			}

			if ($id)
			{
				$values = $this->bo->read_single($id);
				$function_msg = lang('edit responsible');
/*
				$this->acl->set_account_id($this->account);
				$grants	= $this->acl->get_grants('property','.responsible');
				if(!$this->bocommon->check_perms($grants[$values['user_id']], PHPGW_ACL_READ))
				{
					$values = array();
					$receipt['error'][]=array('msg'=>lang('You are not granted sufficient rights for this entry'));
				}

*/
			}
			else
			{
				$function_msg = lang('add responsible');
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uiresponsible.edit',
					'id'		=> $id,
					'app'		=> $this->appname
				);

			$locations = $GLOBALS['phpgw']->locations->get_locations(false, $this->appname, false, false, true);

			$selected_location = $location ? $location : $values['location'];
			if(isset($values['location_id']) && $values['location_id'] && !$selected_location)
			{
				$locations_info = $GLOBALS['phpgw']->locations->get_name($values['location_id']);
				$selected_location = $locations_info['location'];
			}

			$location_list = array();
			foreach ( $locations as $_location => $descr )
			{
				$location_list[] = array
					(
						'id'		=> $_location,
						'name'		=> "{$_location} [{$descr}]",
						'selected'	=> $_location == $selected_location
					);
			}

			$module_def = array
			(
				array('key' => 'appname',	'label'=>lang('appname'),'sortable'=>true,'resizeable'=>true),
				array('key' => 'location',	'label'=>lang('location'),'sortable'=>true,'resizeable'=>true),
				array('key' => 'category',	'label'=>lang('category'),'sortable'=>true,'resizeable'=>true),
				array('key' => 'active',	'label'=>lang('active'),'sortable'=>true,'resizeable'=>true),
				array('key' => 'delete_module','label'=>lang('delete'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter')
			);

			$responsibility_module = isset($values['module']) && $values['module'] ? $values['module'] : array();

			foreach($responsibility_module as &$module)
			{
				$_location_info = $GLOBALS['phpgw']->locations->get_name($module['location_id']);
				$module['appname'] = $_location_info['appname'];
				$module['location'] = $_location_info['location'];
				$category = $this->cats->return_single($module['cat_id']);
				$module['category'] = $category[0]['name'];

				if ($this->acl->check('admin', PHPGW_ACL_EDIT, $module['appname']))
				{
					$_checked = $module['active'] ? 'checked = "checked"' : '';
					$module['active'] = "<input type='checkbox' name='values[set_active][]' {$_checked} value='{$module['location_id']}_{$module['cat_id']}' title='".lang('Check to set active')."'>";
					$module['delete_module'] = "<input type='checkbox' name='values[delete_module][]' value='{$module['location_id']}_{$module['cat_id']}' title='".lang('Check to delete')."'>";
				}
			}

			//---datatable settings--------------------------
			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($responsibility_module),
					'total_records'			=> count($responsibility_module),
					'is_paginator'			=> 0,
					'footer'				=> 0
				);					
			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode($module_def)
				);		
			//-----------------------------------------------

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'value_appname'					=> $this->appname,
					'value_location'				=> $location,
					'value_id'						=> $id,
					'value_name'					=> $values['name'],
					'value_descr'					=> $values['descr'],
					'value_access'					=> $values['access'],
					'apps_list'						=> array('options' => execMethod('property.bojasper.get_apps', $this->appname)), 
					'location_list'					=> array('options' => $location_list),
					'td_count'						=> '""',
					'base_java_url'					=> "{menuaction:'property.uiresponsible.edit'}",
					'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'datatable'						=> $datavalues,
					'myColumnDefs'					=> $myColumnDefs,
					'lang_category'					=> lang('category'),
					'lang_no_cat'					=> lang('no category'),
					'cat_select'					=> $this->cats->formatted_xslt_list(array
					(
						'select_name' => 'values[cat_id]',
						'selected' => isset($values['cat_id'])?$values['cat_id']:''
					)),
				);

			//---datatable settings--------------------
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('loader');

			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'responsible.edit', 'property' );
			//-----------------------datatable settings---

			$appname						= 'Responsible';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::$function_msg::".lang($this->appname);
			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		function edit_role()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$id			= phpgw::get_var('id', 'int');
			$location	= phpgw::get_var('location', 'string');
			$values		= phpgw::get_var('values');

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
	//				$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}

				if(!isset($values['location']) || !$values['location'])
				{
	//				$receipt['error'][]=array('msg'=>lang('Please select a location!'));
				}

				if(!isset($values['name']) || !$values['name'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a name!'));
				}

				if($id)
				{
					$values['id']=$id;
				}
				else
				{
					$id = $values['id'];
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save_role($values);
					$id = $receipt['id'];

					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','responsible_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uigeneric.index', 'type' => 'responsibility_role', 'appname' => $this->appname));
					}
				}
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uigeneric.index', 'type' => 'responsibility_role', 'appname' => $this->appname));
			}

			if ($id)
			{
				$values = $this->bo->read_single_role($id);
				$function_msg = lang('edit role');
/*
				$this->acl->set_account_id($this->account);
				$grants	= $this->acl->get_grants('property','.responsible');
				if(!$this->bocommon->check_perms($grants[$values['user_id']], PHPGW_ACL_READ))
				{
					$values = array();
					$receipt['error'][]=array('msg'=>lang('You are not granted sufficient rights for this entry'));
				}

*/
			}
			else
			{
				$function_msg = lang('add role');
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiresponsible.edit_role',
				'id'		=> $id,
				'app'		=> $this->appname
			);

			$location_types = execMethod('property.soadmin_location.get_location_type');

			$levels = isset($values['location_level']) && $values['location_level'] ? $values['location_level'] : array();
			$level_list = array();
			foreach ( $location_types as $location_type )
			{
				$level_list[] = array
					(
						'id'		=> $location_type['id'],
						'name'		=>  $location_type['name'],
						'selected'	=> in_array($location_type['id'], $levels)
					);
			}
			//-----------------------------------------------

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'value_appname'					=> $this->appname,
					'value_location'				=> $location,
					'value_id'						=> $id,
					'value_name'					=> $values['name'],
					'value_remark'					=> $values['remark'],
					'value_access'					=> $values['access'],
					'responsibility_list'			=> array('options' => execMethod('property.boresponsible.get_responsibilities', array('appname' => $this->appname,	'selected' => $values['responsibility_id']))),
					'level_list'					=> array('checkbox' => $level_list),
					'td_count'						=> '""',
					'base_java_url'					=> "{menuaction:'property.uiresponsible.edit'}",
					'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'datatable'						=> $datavalues,
					'myColumnDefs'					=> $myColumnDefs,
				);

			//---datatable settings--------------------
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('loader');

			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'responsible.edit', 'property' );
			//-----------------------datatable settings---

			$appname						= 'Responsible';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::$function_msg::".lang($this->appname);
			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_role' => $data));
		}



		/**
		 * List of contacts given responsibilities within locations
		 *
		 * @return void
		 */

		public function contact()
		{
			if(!$this->acl_read)
			{
				$this->no_access();
				return;
			}

			$type_id		= phpgw::get_var('type_id', 'int');

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible', 'nextmatchs','search_field'));

			$responsible_info = $this->bo->read_contact($type_id);

			$content = array();
			foreach ( $responsible_info as $entry )
			{
				$link_edit					= '';
				$lang_edit_demo_text		= '';
				$text_edit					= '';
				if ($this->acl_edit)
				{
					$link_edit				= $GLOBALS['phpgw']->link('/index.php', array
						(
							'menuaction'	=> 'property.uiresponsible.edit_contact',
							'id'			=> $entry['id'],
							'appname'		=> $this->appname,
							'location'		=> str_replace($this->appname, '', $entry['app_name']),
							'type_id'		=> $type_id
						));
					$lang_edit_text			= lang('edit type');
					$text_edit				= lang('edit');
				}

				$link_delete				= '';
				$text_delete				= '';
				$lang_delete_demo_text		= '';
			/*	if ($this->acl_delete)
				{
					$link_delete			= $GLOBALS['phpgw']->link('/index.php', array
																(
																	'menuaction'=> 'property.uiresponsible.delete_contact',
																	'id'=> $entry['id']
																));
					$text_delete			= lang('delete');
					$lang_delete_text		= lang('delete type');
				}
			 */

				$content[] = array
					(
						'location_code'			=> $entry['location_code'],
						'item'					=> $entry['item'],
						'active_from'			=> $entry['active_from'],
						'active_to'				=> $entry['active_to'],
						'created_by'			=> $entry['created_by'],
						'created_on'			=> $entry['created_on'],
						'contact_name'			=> $entry['contact_name'],
						'remark'				=> $entry['remark'],
						'ecodimb'				=> $entry['ecodimb'],
						'link_edit'				=> $link_edit,
						'text_edit'				=> $text_edit,
						'lang_edit_text'		=> $lang_edit_text,
						'link_delete'			=> $link_delete,
						'text_delete'			=> $text_delete,
						'lang_delete_text'		=> $lang_delete_text
					);
			}

			$table_header[] = array
				(
					'sort_location'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'location_code',
						'order'	=> $this->order,
						'extra'	=> array
						(
							'menuaction'	=> 'property.uiresponsible.contact',
							'allrows'		=> $this->allrows,
							'appname'		=> $this->appname,
							'location'		=> $this->location,
							'type_id'		=> $type_id
						)
					)),
					'sort_active_from'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'active_from',
						'order'	=> $this->order,
						'extra'	=> array
						(
							'menuaction'	=> 'property.uiresponsible.contact',
							'allrows'		=> $this->allrows,
							'appname'		=> $this->appname,
							'location'		=> $this->location,
							'type_id'		=> $type_id
						)
					)),
					'sort_active_to'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'active_to',
						'order'	=> $this->order,
						'extra'	=> array
						(
							'menuaction'	=> 'property.uiresponsible.contact',
							'allrows'		=> $this->allrows,
							'appname'		=> $this->appname,
							'location'		=> $this->location,
							'type_id'		=> $type_id
						)
					)),
					'sort_ecodimb'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'ecodimb',
						'order'	=> $this->order,
						'extra'	=> array
						(
							'menuaction'	=> 'property.uiresponsible.contact',
							'allrows'		=> $this->allrows,
							'appname'		=> $this->appname,
							'location'		=> $this->location,
							'type_id'		=> $type_id
						)
					)),
					'lang_contact'		=> lang('contact'),
					'lang_location'		=> lang('location'),
					'lang_item'			=> lang('item'),
					'lang_active_from'	=> lang('active from'),
					'lang_active_to'	=> lang('active to'),
					'lang_created_on'	=> lang('created'),
					'lang_created_by'	=> lang('supervisor'),
					'lang_remark'		=> lang('remark'),
					'lang_ecodimb'		=> lang('dimb'),
					'lang_edit'			=> $this->acl_edit ? lang('edit') : '',
					//		'lang_delete'		=> $this->acl_delete ? lang('delete') : '',
				);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uiresponsible.contact',
					'sort'			=> $this->sort,
					'order'			=> $this->order,
					'query'			=> $this->query,
					'appname'		=> $this->appname,
					'location'		=> $this->location,
					'type_id'		=> $type_id

				);

			$link_add_action = array
				(
					'menuaction'	=> 'property.uiresponsible.edit_contact',
					'appname'		=> $this->appname,
					'location'		=> $this->location,
					'type_id'		=> $type_id
				);

			$table_add[] = array
				(
					'lang_add'					=> lang('add'),
					'lang_add_statustext'		=> lang('add contact'),
					'add_action'				=> $GLOBALS['phpgw']->link('/index.php', $link_add_action),
					'lang_cancel'				=> lang('cancel'),
					'lang_cancel_statustext'	=> lang('back to list type'),
					'cancel_action'				=> $GLOBALS['phpgw']->link('/index.php', array
						(
							'menuaction'	=> 'property.uiresponsible.index',
							'appname'		=> $this->appname
						)
					)
			);

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt');
			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
			$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt', '');

			$type_info = $this->bo->read_single_type($type_id);
			$category = $this->cats->return_single($type_info['cat_id']);
			$data = array
				(
					'msgbox_data'							=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'allow_allrows'							=> true,
					'allrows'								=> $this->allrows,
					'start_record'							=> $this->start,
					'record_limit'							=> $record_limit,
					'num_records'							=> $responsible_info ? count($responsible_info) : 0,
					'all_records'							=> $this->bo->total_records,
					'select_action'							=> $GLOBALS['phpgw']->link('/index.php', $link_data),
					'link_url'								=> $GLOBALS['phpgw']->link('/index.php', $link_data),
					'img_path'								=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default'),
					'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
					'lang_searchbutton_statustext'			=> lang('Submit the search string'),
					'query'									=> $this->query,
					'lang_search'							=> lang('search'),
					'table_header_contact'					=> $table_header,
					'table_add'								=> $table_add,
					'values_contact'						=> $content,
					'lang_no_location'						=> lang('No location'),
					'lang_location_statustext'				=> lang('Select submodule'),
					'select_name_location'					=> 'location',
					'location_name'							=> "property{$this->location}", //FIXME once interlink is settled
					'lang_no_cat'							=> lang('no category'),
					'type_name'								=> $type_info['name'],
					'category_name'							=> $category[0]['name']
				);

			$function_msg= lang('list available responsible contacts');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('list_contact' => $data));
			$this->_save_sessiondata();
		}


		/**
		 * Add or Edit available contact related to responsible types and (physical) locations
		 *
		 * @return void
		 */

		public function edit_contact()
		{
			if(!$this->acl_add)
			{
				$this->no_access();
				return;
			}

			$id						= phpgw::get_var('id', 'int');
			$type_id				= phpgw::get_var('type_id', 'int');
			$values					= phpgw::get_var('values', 'string', 'POST');
			$contact_id				= phpgw::get_var('contact', 'int');
			$contact_name			= phpgw::get_var('contact_name', 'string');
			$responsibility_id		= phpgw::get_var('responsibility_id', 'int');
			$responsibility_name	= phpgw::get_var('responsibility_name', 'string');
			$bolocation				= CreateObject('property.bolocation');
			$bocommon				= CreateObject('property.bocommon');

			$GLOBALS['phpgw']->xslttpl->add_file(array('responsible'));

			if (isset($values) && is_array($values))
			{
				$values['ecodimb']			= phpgw::get_var('ecodimb');

				if(!$this->acl_edit)
				{
					$this->no_access();
					return;
				}

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
					$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_entity', 'property');

					if(isset($insert_record_entity) && is_array($insert_record_entity))
					{
						foreach ($insert_record_entity as $insert_record_entry)
						{
							$insert_record['extra'][$insert_record_entry]	= $insert_record_entry;
						}
					}

					$values = $bocommon->collect_locationdata($values, $insert_record);

					if($id)
					{
						$values['id']=$id;
					}
					if($contact_id)
					{
						$values['contact_id']=$contact_id;
					}

					if($contact_name)
					{
						$values['contact_name']=$contact_name;
					}

					if($responsibility_id)
					{
						$values['responsibility_id']=$responsibility_id;
					}

					if($contact_name)
					{
						$values['responsibility_name']=$responsibility_name;
					}

					if(!isset($values['responsibility_id']))
					{
						$receipt['error'][]=array('msg'=>lang('Please select a responsibility!'));
					}

					if(!isset($values['contact_id']))
					{
						$receipt['error'][]=array('msg'=>lang('Please select a contact!'));
					}

					if(!isset($values['location']['loc1']))
					{
						//			$receipt['error'][]=array('msg'=>lang('Please select a location!'));
					}

					if($GLOBALS['phpgw']->session->is_repost())
					{
						$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
					}

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$receipt = $this->bo->save_contact($values);
						$id = $receipt['id'];

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', 'responsible_contact_receipt', $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php', array
								(
									'menuaction'=> 'property.uiresponsible.contact',
									'appname'		=> $this->appname,
									'location'	=> $this->location,
									'type_id'	=> $type_id
								));
						}
						else if (isset($values['apply']) && $values['apply'])
						{
							$GLOBALS['phpgw']->redirect_link('/index.php', array
								(
									'menuaction'=> 'property.uiresponsible.edit_contact',
									'appname'		=> $this->appname,
									'location'	=> $this->location,
									'type_id'	=> $type_id,
									'id'		=> $id
								));
						}
					}
					else
					{
						if(isset($values['location']) && $values['location'])
						{
							$location_code=implode("-", $values['location']);
							$values['location_data'] = $bolocation->read_single($location_code, isset($values['extra']) ? $values['extra'] : false);
						}
						if(isset($values['extra']['p_num']) && $values['extra']['p_num'])
						{
							$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
							$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
							$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=phpgw::get_var('entity_cat_name_'.$values['extra']['p_entity_id'], 'string', 'POST');
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php', array
						(
							'menuaction'=> 'property.uiresponsible.contact',
							'appname'		=> $this->appname,
							'location' => $this->location,
							'type_id' => $type_id
						));
				}
			}


			if ($id)
			{
				$function_msg = lang('edit responsible type');
				$values = $this->bo->read_single_contact($id);
			}
			else
			{
				$function_msg = lang('add responsible type');
			}

			$location_data = $bolocation->initiate_ui_location(array
				(
					'values'	=> $values['location_data'],
					'type_id'	=> -1, // calculated from location_types
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'tenant'	=> false,
					'lookup_type'	=> 'form',
					'lookup_entity'	=> $bocommon->get_lookup_entity('project'),
					'entity_data'	=> isset($values['p']) ? $values['p'] : ''
				)
			);

			$ecodimb_data=$bocommon->initiate_ecodimb_lookup(array
				(
					'ecodimb'			=> $values['ecodimb'],
					'ecodimb_descr'		=> $values['ecodimb_descr'])
				);

			$link_data = array
				(
					'menuaction'	=> 'property.uiresponsible.edit_contact',
					'id'			=> $id,
					'appname'		=> $this->appname,
					'location'		=> $this->location,
					'type_id'		=> $type_id
				);

			$msgbox_data = (isset($receipt)?$GLOBALS['phpgw']->common->msgbox_data($receipt):'');

			$lookup_link_contact		= "menuaction:'property.uilookup.addressbook', column:'contact'";
			$lookup_link_responsibility	= "menuaction:'property.uiresponsible.index', location:'{$this->location}', lookup:1";

			$lookup_function = "\n"
				. '<script type="text/javascript">' ."\n"
				. '//<[CDATA[' ."\n"
				. 'function lookup_contact()' ."\r\n"
				. "{\r\n"
				. ' var oArgs = {' . $lookup_link_contact . "};\n"
				. " var strURL = phpGWLink('index.php', oArgs);\n"
				. ' Window1=window.open(strURL,"Search","left=50,top=100,width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");' . "\r\n"
				. '}'."\r\n"
				//				. 'function lookup_responsibility()' ."\r\n"
				//				. "{\r\n"
				//				. ' var oArgs = {' . $lookup_link_responsibility . "};\n"
				//				. " var strURL = phpGWLink('index.php', oArgs);\n"
				//				. ' Window1=window.open(strURL,"Search","width=800,height=700,toolbar=no,scrollbars=yes,resizable=yes");' . "\r\n"
				//				. '}'."\r\n"
				. '//]]' ."\n"
				. "</script>\n";

			if(!isset($GLOBALS['phpgw_info']['flags']['java_script']))
			{
				$GLOBALS['phpgw_info']['flags']['java_script'] = '';
			}

			$GLOBALS['phpgw_info']['flags']['java_script'] .= $lookup_function;

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_active_from');
			$jscal->add_listener('values_active_to');

			$type = $this->bo->read_single_type($type_id);

			$data = array
				(
					'ecodimb_data'					=> $ecodimb_data,
					'value_entry_date'				=> isset($values['entry_date']) ? $values['entry_date'] : '',
					'value_name'					=> isset($values['name']) ? $values['name'] : '',
					'value_remark'					=> isset($values['remark']) ? $values['remark'] : '',
					'lang_entry_date'				=> lang('Entry date'),
					'lang_remark'					=> lang('remark'),

					'lang_responsibility'			=> lang('responsibility'),
					'lang_responsibility_status_text'=> lang('responsibility'),
					'value_responsibility_id'		=> $type_id,
					'value_responsibility_name'		=> $type['name'],

					'lang_contact'					=> lang('contact'),
					'lang_contact_status_text'		=> lang('click to select contact'),
					'value_contact_id'				=> isset($values['contact_id']) ? $values['contact_id'] : '',
					'value_contact_name'			=> isset($values['contact_name']) ? $values['contact_name'] : '',

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php', $link_data),
					'lang_id'						=> lang('ID'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'value_id'						=> $id,
					'lang_cancel_status_text'		=> lang('Back to the list'),
					'lang_save_status_text'			=> lang('Save the responsible type'),
					'lang_apply'					=> lang('apply'),
					'lang_apply_status_text'		=> lang('Apply the values'),

					'lang_location'					=> lang('location'),
					'value_location_name'			=> "property{$this->location}", //FIXME once interlink is settled
					'location_data'					=> $location_data,

					'lang_active_from'				=> lang('active from'),
					'lang_active_to'				=> lang('active to'),
					'value_active_from'				=> isset($values['active_from']) ? $values['active_from'] : '',
					'value_active_to'				=> isset($values['active_to']) ? $values['active_to'] : '',
					'img_cal'						=> $GLOBALS['phpgw']->common->image('phpgwapi', 'cal'),
					'lang_datetitle'				=> lang('Select date'),
					'lang_active_from_statustext'	=> lang('Select the start date for this responsibility'),
					'lang_active_to_statustext'		=> lang('Select the closing date for this responsibility'),

				);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . "::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('edit_contact' => $data));
		}

		/**
		 * Display an error in case of missing rights
		 *
		 * @return void
		 */

		public function no_access()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('no_access'));

			$receipt['error'][]=array('msg'=>lang('NO ACCESS'));

			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data)
				);

			$function_msg	= lang('No access');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('responsible matrix') . ":: {$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('no_access' => $data));
		}

		/**
		 * Delete a responsibility type
		 *
		 * @return void
		 */

		public function delete_type()
		{
			if(!$this->acl_delete)
			{
				return 'No access';
			}

			$id	= phpgw::get_var('id', 'int');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete_type($id);
				return lang('id %1 has been deleted', $id);
			}
		}
	}
