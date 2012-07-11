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
	* @subpackage admin
 	* @version $Id$
	*/
	phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_uiadmin_location
	{
		var $grants;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $sub;
		var $currentapp;

		var $public_functions = array
			(
				'index'  				=> true,
				'config'  				=> true,
				'edit_config'			=> true,
				'view'   				=> true,
				'edit'   				=> true,
				'delete' 				=> true,
				'list_attribute'		=> true,
				'edit_attrib' 			=> true,
				'list_attribute_group'	=> true,
				'edit_attrib_group'		=> true,
			);

		function property_uiadmin_location()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'admin::property::location';
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boadmin_location',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.admin.location';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;

		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'		=> $this->start,
					'query'		=> $this->query,
					'sort'		=> $this->sort,
					'order'		=> $this->order,
				);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::location';

			$this->bocommon->reset_fm_cache();

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uiadmin_location.index'
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_location.index'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
					(
						'menuaction'	=> 'property.uiadmin_location.index'
					);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
						array(
							'menuaction'	=> 'property.uiadmin_location.index'
						)
					),
					'fields'	=> array(
						'field' => array(
							array(
								'type'	=> 'button',
								'id'	=> 'btn_done',
								'value'	=> lang('done'),
								'tab_index' => 1
							),
							array(
								'type'	=> 'button',
								'id'	=> 'btn_new',
								'value'	=> lang('add'),
								'tab_index' => 2
							),
							array( //boton     SEARCH
								'id' => 'btn_search',
								'name' => 'search',
								'value'    => lang('search'),
								'type' => 'button',
								'tab_index' => 3
							),
							array( // TEXT INPUT
								'name'     => 'query',
								'id'     => 'txt_query',
								'value'    => '',//$query,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 28,
								'tab_index' => 4
							)
						),
						'hidden_value' => array(

							)
						)
					)
				);

				$dry_run = true;
			}

			$standard_list = $this->bo->read();
			foreach ($standard_list as &$entry)
			{
				$entry['location_id'] = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$entry['id']}");
			}			

			$uicols['name'][0]	= 'location_id';
			$uicols['descr'][0]	= 'location_id';
			$uicols['name'][1]	= 'id';
			$uicols['descr'][1]	= lang('standard id');
			$uicols['name'][2]	= 'name';
			$uicols['descr'][2]	= lang('Name');
			$uicols['name'][3]	= 'descr';
			$uicols['descr'][3]	= lang('Descr');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($standard_list) AND is_array($standard_list))
			{
				foreach($standard_list as $standard_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $standard_entry[$uicols['name'][$k]];
						}
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

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
					)
				);
			$parameters3 = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'location_id',
							'source'	=> 'location_id'
						),
					)
				);

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'categories',
				'statustext' 	=> lang('categories'),
				'text'			=> lang('Categories'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uigeneric.index',
					'type'			=> 'location'

				)),
				'parameters'	=> $parameters2
			);

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'attribute_groups',
				'statustext' 	=> lang('attribute groups'),
				'text'			=> lang('attribute groups'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uiadmin_location.list_attribute_group'
				)),
				'parameters'	=> $parameters2
			);

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'attributes',
				'statustext' 	=> lang('attributes'),
				'text'			=> lang('Attributes'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uiadmin_location.list_attribute'

				)),
				'parameters'	=> $parameters2
			);

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'config',
				'statustext' 	=> lang('config'),
				'text'			=> lang('config'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'admin.uiconfig2.index'

				)),
				'parameters'	=> $parameters3
			);

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'edit',
				'statustext' 	=> lang('edit'),
				'text'			=> lang('edit'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uiadmin_location.edit'
				)),
				'parameters'	=> $parameters
			);

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'delete',
				'statustext' 	=> lang('delete'),
				'text'			=> lang('delete'),
				'confirm_msg'	=> lang('do you really want to delete this entry'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uiadmin_location.delete'
				)),
				'parameters'	=> $parameters
			);


			$datatable['rowactions']['action'][] = array(
				'my_name' 		=> 'add',
				'text' 			=> lang('add'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uiadmin_location.edit'
				)));

			unset($parameters);
			unset($parameters2);
			unset($parameters3);

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'id';
					}
					if($uicols['name'][$i]=='name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'name';
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($standard_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname		= lang('entity');
			$function_msg	= lang('list entity type');

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
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						elseif(isset($column['format']) && $column['format']== "link")
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

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_location.index', 'property' );
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if (isset($values['save']))
			{
				if (!isset($values['name']) || !$values['name'])
				{
					$receipt['error'][] = array('msg'=>lang('Name not entered!'));
				}

				if($id)
				{
					$values['id']=$id;
				}

				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save($values);
					$id=$receipt['id'];
				}
				else
				{
					$receipt['error'][] = array('msg'=> lang('Table has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single($id);
				$function_msg = lang('edit standard');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add standard');
				$action='add';
			}


			$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_location.edit',
					'id'		=> $id
				);
			//_debug_array($values);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'lang_name_standardtext'		=> lang('Enter a name of the standard'),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index')),
					'lang_id'						=> lang('standard ID'),
					'lang_name'						=> lang('Name'),
					'lang_descr'					=> lang('Descr'),
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),
					'value_id'						=> (isset($id)?$id:''),
					'value_name'					=> (isset($values['name'])?$values['name']:''),
					'lang_id_standardtext'			=> lang('Enter the standard ID'),
					'lang_descr_standardtext'		=> lang('Enter a description of the standard'),
					'lang_done_standardtext'		=> lang('Back to the list'),
					'lang_save_standardtext'		=> lang('Save the standard'),
					'value_descr'					=> (isset($values['descr'])?$values['descr']:''),
					'lang_list_info'				=> lang('list info'),
					'lang_select'					=> lang('select'),
					'value_list_info'				=> $this->bo->get_list_info((isset($id)?$id:''),$values['list_info']),
					'lang_location'					=> lang('location'),
					'lang_list_info_statustext'		=> lang('Names of levels to list at this level'),
					'value_list_address'			=> isset($values['list_address'])?$values['list_address']:'',
					'value_list_documents'			=> isset($values['list_documents'])?$values['list_documents']:''
				);

			$appname	= lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$group_id   = phpgw::get_var('group_id', 'int');
			$attrib		= phpgw::get_var('attrib');
			$type_id	= phpgw::get_var('type_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$receipt =  $this->bo->delete($type_id,$id,$attrib,$group_id);

				//FIXME
				if(isset($receipt['message']))
				{
					return $receipt['message'][0]['msg'];
				}
				else
				{
					return $receipt['error'][0]['msg'];
				}
			}

			if($attrib)
			{
				$function='list_attribute';
			}
			else
			{
				$function='index';
			}
			$link_data = array
				(
					'menuaction' => 'property.uiadmin_location.'.$function,
					'type_id' => $type_id
				);

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.delete', 'id'=> $id, 'attrib'=> $attrib, 'type_id'=> $type_id)),
					'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
					'lang_yes'					=> lang('yes'),
					'lang_yes_standardtext'		=> lang('Delete the entry'),
					'lang_no_standardtext'		=> lang('Back to the list'),
					'lang_no'					=> lang('no')
				);

			$appname		= lang('location');
			$function_msg	= lang('delete location standard');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function list_attribute_group()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			$location	= ".location.{$type_id}";
			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			if($resort)
			{
				$this->bo->resort_attrib_group($location, $id, $resort);
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uiadmin_location.list_attribute',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'query'		=> $this->query,
						'type_id'	=> $type_id
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_location.list_attribute_group',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."query:'{$this->query}',"
					."type_id:'{$type_id}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
					(
						'menuaction'	=> 'property.uiadmin_location.list_attribute',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'query'		=> $this->query,
						'type_id'	=> $type_id,
					);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
						array(
							'menuaction'	=> 'property.uiadmin_location.list_attribute',
							'sort'		=> $this->sort,
							'order'		=> $this->order,
							'query'		=> $this->query,
							'type_id'	=> $type_id
						)
					),
					'fields'	=> array(
						'field' => array(
							array(
								'type'	=> 'button',
								'id'	=> 'btn_done',
								'value'	=> lang('done'),
								'tab_index' => 1
							),
							array(
								'type'	=> 'button',
								'id'	=> 'btn_new',
								'value'	=> lang('add'),
								'tab_index' => 2
							),
							array( //boton     SEARCH
								'id' => 'btn_search',
								'name' => 'search',
								'value'    => lang('search'),
								'type' => 'button',
								'tab_index' => 3
							),
							array( // TEXT INPUT
								'name'     => 'query',
								'id'     => 'txt_query',
								'value'    => '',//$query,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 28,
								'tab_index' => 4
							)
						),
						'hidden_value' => array(

							)
						)
					)
				);

				$dry_run = true;
			}

			$attrib_list = $this->bo->read_attrib_group($location);
			$uicols['name'][0]	= 'name';
			$uicols['descr'][0]	= lang('Name');
			$uicols['name'][1]	= 'descr';
			$uicols['descr'][1]	= lang('Descr');
			$uicols['name'][2]	= 'group_sort';
			$uicols['descr'][2]	= lang('sorting');
			$uicols['name'][3]	= 'up';
			$uicols['descr'][3]	= lang('up');
			$uicols['name'][4]	= 'down';
			$uicols['descr'][4]	= lang('down');
			$uicols['name'][5]	= 'id';
			$uicols['descr'][5]	= lang('id');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($attrib_list) AND is_array($attrib_list))
			{
				foreach($attrib_list as $attrib_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $attrib_entry[$uicols['name'][$k]];
						}

						if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'up')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'up';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']		= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute_group', 'resort'=> 'up', 'type_id'=> $type_id, 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.')';
						}

						if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'down')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'down';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']		= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute_group', 'resort'=> 'down', 'type_id'=> $type_id, 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.')';
						}
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

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
							'name'		=> 'group_id',
							'source'	=> 'id'
						),
					)
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'edit',
					'statustext' 	=> lang('Edit'),
					'text'			=> lang('Edit'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							'menuaction'		=> 'property.uiadmin_location.edit_attrib_group',
							'type_id'			=> $type_id
						)
					),
					'parameters'	=> $parameters
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'delete',
					'statustext' 	=> lang('Delete'),
					'text'			=> lang('Delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							'menuaction'		=> 'property.uiadmin_location.delete',
							'type_id'			=> $type_id
						)
					),
					'parameters'	=> $parameters2
				);

			$datatable['rowactions']['action'][] = array(
				'my_name' 			=> 'add',
				'statustext' 	=> lang('add'),
				'text'			=> lang('add'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uiadmin_location.edit_attrib_group',
					'type_id' 				=> $type_id
				))
			);

			unset($parameters);
			unset($parameters2);


			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
					if($uicols['name'][$i]=='id')
					{
						$datatable['headers']['header'][$i]['visible'] 			= false;
					}

					if($uicols['name'][$i]=='name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'name';
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($attrib_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('attribute');
			$function_msg	= lang('list entity attribute');

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
						if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='#' onclick='".$column['link']."'>" .$column['value']."</a>";
						}else
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

			// query parameters
			if(isset($current_Consult) && is_array($current_Consult))
			{
				$json ['current_consult'] = $current_Consult;
			}

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				return $json;
			}


			$datatable['json_data'] = json_encode($json);
			//-------------------- JSON CODE ----------------------

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_location.attribute_group', 'property' );
		}

		function edit_attrib_group()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";
			$location	= ".location.{$type_id}";
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');
			if(!$values)
			{
				$values=array();
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if (isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$values['location'] = $location;

				if (!$values['group_name'])
				{
					$receipt['error'][] = array('msg'=>lang('group name not entered!'));
				}

				if (!$values['descr'])
				{
					$receipt['error'][] = array('msg'=>lang('description not entered!'));
				}

				if (!$location)
				{
					$receipt['error'][] = array('msg'=>lang('location not chosen!'));
				}


				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib_group($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Attribute group has NOT been saved'));
				}
			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib_group($location,$id);
				$type_name=$values['type_name'];
				$function_msg = lang('edit attribute group'). ' ' . lang($type_name);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute group');
				$action='add';
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_location.edit_attrib_group',
					'type_id'	=> $type_id,
					'id'		=> $id
				);


			$type = $this->bo->read_single( $type_id,false);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
				(
					'lang_entity'						=> lang('location'),
					'entity_name'						=> $type['name'],

					'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute_group', 'type_id'=> $type_id)),
					'lang_id'							=> lang('Attribute group ID'),
					'lang_entity_type'					=> lang('Entity type'),
					'lang_no_entity_type'				=> lang('No entity type'),
					'lang_save'							=> lang('save'),
					'lang_done'							=> lang('done'),
					'value_id'							=> $id,

					'lang_group_name'					=> lang('group name'),
					'value_group_name'					=> $values['group_name'],
					'lang_group_name_statustext'		=> lang('enter the name for the group'),

					'lang_descr'						=> lang('descr'),
					'value_descr'						=> $values['descr'],
					'lang_descr_statustext'				=> lang('enter the input text for records'),

					'lang_remark'						=> lang('remark'),
					'lang_remark_statustext'			=> lang('Enter a remark for the group'),
					'value_remark'						=> $values['remark'],

					'lang_done_attribtext'				=> lang('Back to the list'),
					'lang_save_attribtext'				=> lang('Save the attribute')
				);

			$appname = lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib_group' => $data));
		}


		function list_attribute()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$resort		= phpgw::get_var('resort');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			if($resort)
			{
				$this->bo->resort_attrib(array('resort'=>$resort,'type_id' => $type_id,'id'=>$id));
			}

			$type = $this->bo->read_single($type_id);

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uiadmin_location.list_attribute',
						'sort'			=> $this->sort,
						'order'			=> $this->order,
						'query'			=> $this->query,
						'type_id'		=> $type_id,
						'allrows'		=> $this->allrows
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_location.list_attribute',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."query:'{$this->query}',"
					."type_id:'{$type_id}',"
					."allrows:'{$this->allrows}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
					(
						'menuaction'	=> 'property.uiadmin_location.list_attribute',
						'sort'			=> $this->sort,
						'order'			=> $this->order,
						'query'			=> $this->query,
						'type_id'		=> $type_id
					);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
						array(
							'menuaction'	=> 'property.uiadmin_location.list_attribute',
							'sort'			=> $this->sort,
							'order'			=> $this->order,
							'query'			=> $this->query,
							'type_id'		=> $type_id
						)
					),
					'fields'	=> array(
						'field' => array(
							array(
								'type'	=> 'button',
								'id'	=> 'btn_done',
								'value'	=> lang('done'),
								'tab_index' => 1
							),
							array(
								'type'	=> 'button',
								'id'	=> 'btn_new',
								'value'	=> lang('add'),
								'tab_index' => 2
							),
							array( //boton     SEARCH
								'id' => 'btn_search',
								'name' => 'search',
								'value'    => lang('search'),
								'type' => 'button',
								'tab_index' => 3
							),
							array( // TEXT INPUT
								'name'     => 'query',
								'id'     => 'txt_query',
								'value'    => '',//$query,
								'type' => 'text',
								'onkeypress' => 'return pulsar(event)',
								'size'    => 28,
								'tab_index' => 4
							),
							array(
								'id' => 'txtcategory',
								'name' => 'search',
								'value'    => 'Location type: '.$type['name'],
								'type' => 'label',
								'style' => 'filter'
							)
						),
						'hidden_value' => array(

							)
						)
					)
				);

				$dry_run = true;
			}

			$attrib_list = $this->bo->read_attrib($type_id);
			$uicols['name'][0]	= 'column_name';
			$uicols['descr'][0]	= lang('Name');
			$uicols['name'][1]	= 'input_text';
			$uicols['descr'][1]	= lang('Descr');
			$uicols['name'][2]	= 'trans_datatype';
			$uicols['descr'][2]	= lang('Datatype');
			$uicols['name'][3]	= 'group_id';
			$uicols['descr'][3]	= lang('group');
			$uicols['name'][4]	= 'attrib_sort';
			$uicols['descr'][4]	= lang('sorting');
			$uicols['name'][5]	= 'up';
			$uicols['descr'][5]	= lang('up');
			$uicols['name'][6]	= 'down';
			$uicols['descr'][6]	= lang('down');
			$uicols['name'][7]	= 'id';
			$uicols['descr'][7]	= lang('id');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($attrib_list) AND is_array($attrib_list))
			{
				foreach($attrib_list as $attrib_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
			//			if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= isset($attrib_entry[$uicols['name'][$k]]) ? $attrib_entry[$uicols['name'][$k]] : '';
						}

						if(isset($datatable['rows']['row'][$j]['column'][$k]['name']) && $datatable['rows']['row'][$j]['column'][$k]['name'] == 'up')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'up';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']		= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'resort'=> 'up', 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows, 'type_id' => $type_id)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.',"' . $this->allrows . '")';
						}

						if(isset($datatable['rows']['row'][$j]['column'][$k]['name']) && $datatable['rows']['row'][$j]['column'][$k]['name'] == 'down')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'down';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']		= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'resort'=> 'down', 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows, 'type_id' => $type_id)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.',"' . $this->allrows . '")';
						}
					}
					$j++;
				}
			}

			$datatable['rowactions']['action'] = array();

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

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'edit',
					'statustext' 	=> lang('Edit'),
					'text'			=> lang('Edit'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							'menuaction'		=> 'property.uiadmin_location.edit_attrib',
							'type_id' 			=> $type_id
						)
					),
					'parameters'	=> $parameters
				);

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'delete',
					'statustext' 	=> lang('Delete'),
					'text'			=> lang('Delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link
					(
						'/index.php',array
						(
							'menuaction'		=> 'property.uiadmin_location.delete',
							'type_id'			=> $type_id,
							'attrib' 			=> true
						)
					),
					'parameters'	=> $parameters
				);

			$datatable['rowactions']['action'][] = array(
				'my_name' 		=> 'add',
				'statustext' 	=> lang('add'),
				'text'			=> lang('add'),
				'action'		=> $GLOBALS['phpgw']->link('/index.php',array
				(
					'menuaction'	=> 'property.uiadmin_location.edit_attrib',
					'type_id'		=> $type_id,
				))
			);

			unset($parameters);


			for ($i=0;$i<$count_uicols_name;$i++)
			{
				//				if($uicols['input_type'][$i]!='hidden')
			{
				$datatable['headers']['header'][$i]['formatter'] 		= (!isset($uicols['formatter'][$i]) || !$uicols['formatter'][$i]?  '""' : $uicols['formatter'][$i]);
				$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
				$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= false;
				if($uicols['name'][$i]=='column_name')
				{
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']	= 'column_name';
				}
				if($uicols['name'][$i]=='id')
				{
					$datatable['headers']['header'][$i]['visible'] 		= false;
				}
				if($uicols['name'][$i]=='attrib_sort')
				{
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']	= 'attrib_sort';
				}
			}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($attrib_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('attribute');
			$function_msg	= lang('list entity attribute');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'attrib_sort'; // name key Column in myColumnDef
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
						if(isset($column['format']) && $column['format']== "link" && isset($column['java_link']) && $column['java_link']==true)
						{
							//$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							//$json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
							$json_row[$column['name']] = "<a href='#' onclick='".$column['link']."'>" .$column['value']."</a>";
							//$json_row[$column['name']] = '<a href="#" onclick="delete_record("/index.php?menuaction=property.uiasync.delete")">' .$column['value'].'</a>';
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

			$template_vars = array();
			$template_vars['datatable'] = $datatable;
			$GLOBALS['phpgw']->xslttpl->add_file(array('datatable'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', $template_vars);

			if ( !isset($GLOBALS['phpgw']->css) || !is_object($GLOBALS['phpgw']->css) )
			{
				$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
			}

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_location.attribute', 'property' );

			//			$this->save_sessiondata();
		}

		function edit_attrib()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$type_id	= phpgw::get_var('type_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::location::attribute_loc_{$type_id}";

			if(!$values)
			{
				$values = array();
			}

			//_debug_array($values);
			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if (isset($values['save']) && $values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}
				$type_id			= $values['type_id'];

				if (!$values['column_name'])
				{
					$receipt['error'][] = array('msg'=>lang('Column name not entered!'));
				}

				if(!preg_match('/^[a-z0-9_]+$/i',$values['column_name']))
				{
					$receipt['error'][] = array('msg'=>lang('Column name %1 contains illegal character', $values['column_name']));
				}

				if (!$values['input_text'])
				{
					$receipt['error'][] = array('msg'=>lang('Input text not entered!'));
				}
				if (!$values['statustext'])
				{
					$receipt['error'][] = array('msg'=>lang('Statustext not entered!'));
				}

				if (!$values['type_id'])
				{
					$receipt['error'][] = array('msg'=>lang('Location type not chosen!'));
				}

				if (!$values['column_info']['type'])
				{
					$receipt['error'][] = array('msg'=>lang('Datatype type not chosen!'));
				}

				if(!ctype_digit($values['column_info']['precision']) && $values['column_info']['precision'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter precision as integer !'));
					unset($values['column_info']['precision']);
				}

				if($values['column_info']['scale'] && !ctype_digit($values['column_info']['scale']))
				{
					$receipt['error'][]=array('msg'=>lang('Please enter scale as integer !'));
					unset($values['column_info']['scale']);
				}

				if (!$values['column_info']['nullable'])
				{
					$receipt['error'][] = array('msg'=>lang('Nullable not chosen!'));
				}


				if (!$receipt['error'])
				{

					$receipt = $this->bo->save_attrib($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'	=> lang('Attribute has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib($type_id,$id);
				$function_msg = lang('edit attribute'). ' ' . $values['input_text'];
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute');
				$action='add';
			}


			$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_location.edit_attrib',
					'id'		=> $id
				);
			//_debug_array($values);

			$multiple_choice = '';
			if($values['column_info']['type']=='R' || $values['column_info']['type']=='CH' || $values['column_info']['type']=='LB')
			{
				$multiple_choice= true;
			}


			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
				(
					'lang_choice'					=> lang('Choice'),
					'lang_new_value'				=> lang('New value'),
					'lang_new_value_statustext'		=> lang('New value for multiple choice'),
					'multiple_choice'				=> $multiple_choice,
					'value_choice'					=> (isset($values['choice'])?$values['choice']:''),
					'lang_delete_value'				=> lang('Delete value'),
					'lang_value'					=> lang('value'),
					'lang_delete_choice_statustext'	=> lang('Delete this value from the list of multiple choice'),

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.list_attribute', 'type_id'=> $type_id)),
					'lang_id'						=> lang('Attribute ID'),
					'lang_location_type'			=> lang('Type'),
					'lang_no_location_type'			=> lang('No entity type'),
					'lang_save'						=> lang('save'),
					'lang_done'						=> lang('done'),
					'value_id'						=> $id,

					'lang_column_name'				=> lang('Column name'),
					'value_column_name'				=> $values['column_name'],
					'lang_column_name_statustext'	=> lang('enter the name for the column'),

					'lang_input_text'				=> lang('input text'),
					'value_input_text'				=> $values['input_text'],
					'lang_input_name_statustext'	=> lang('enter the input text for records'),

					'lang_id_attribtext'			=> lang('Enter the attribute ID'),
					'lang_entity_statustext'		=> lang('Select a entity type'),

					'lang_statustext'				=> lang('Statustext'),
					'lang_statustext_attribtext'	=> lang('Enter a statustext for the inputfield in forms'),
					'value_statustext'				=> $values['statustext'],

					'lang_done_attribtext'			=> lang('Back to the list'),
					'lang_save_attribtext'			=> lang('Save the attribute'),
					'type_id'						=> $values['type_id'],
					'entity_list'					=> $this->bo->select_location_type($type_id),
					'select_location_type'			=> 'values[type_id]',

					'datatype_list'					=> $this->bocommon->select_datatype($values['column_info']['type']),
					'value_search'					=> $values['search'],

					'attrib_group_list'				=> $this->bo->get_attrib_group_list($type_id, $values['group_id']),

					'value_precision'				=> $values['column_info']['precision'],
					'value_scale'					=> $values['column_info']['scale'],
					'value_default'					=> $values['column_info']['default'],
					'nullable_list'					=> $this->bocommon->select_nullable($values['column_info']['nullable']),
					'value_lookup_form'				=> $values['lookup_form'],
					'value_list'					=> $values['list'],
				);
			//_debug_array($data);

			$appname = lang('location');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		}

		function config()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::config';

			$GLOBALS['phpgw']->xslttpl->add_file(array(
				'admin_location',
				'nextmatchs',
				'search_field'));

			$standard_list = $this->bo->read_config();

			while (is_array($standard_list) && list(,$standard) = each($standard_list))
			{
				$content[] = array
					(
						'column_name'				=> $standard['column_name'],
						'name'						=> $standard['location_name'],
						'link_edit'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit_config', 'column_name'=> $standard['column_name'])),
						'lang_edit_standardtext'	=> lang('edit the column relation'),
						'text_edit'					=> lang('edit')
					);
			}

			//_debug_array($content);

			$table_header[] = array
				(

					'lang_attribute'	=> lang('Attributes'),
					'lang_edit'			=> lang('edit'),
					'lang_delete'		=> lang('delete'),
					'sort_column_name'	=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'column_name',
						'order'	=> $this->order,
						'extra'	=> array('menuaction'	=> 'property.uiadmin_location.config')
					)),
					'lang_column_name'	=> lang('column name'),
					'sort_name'			=> $this->nextmatchs->show_sort_order(array
					(
						'sort'	=> $this->sort,
						'var'	=> 'name',
						'order'	=> $this->order,
						'extra'	=> array('menuaction'	=> 'property.uiadmin_location.config')
					)),
					'lang_name'			=> lang('Table Name'),
				);

			$table_add[] = array
				(
					'lang_add'				=> lang('add'),
					'lang_add_standardtext'	=> lang('add a standard'),
					'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.edit')),
					'lang_done'				=> lang('done'),
					'lang_done_standardtext'=> lang('back to admin'),
					'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php')
				);


			$data = array
				(
					'allow_allrows'						=> false,
					'start_record'						=> $this->start,
					'record_limit'						=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
					'num_records'						=> count($standard_list),
					'all_records'						=> $this->bo->total_records,
					'link_url'							=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.index')),
					'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
					'lang_searchfield_standardtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
					'lang_searchbutton_standardtext'	=> lang('Submit the search string'),
					'query'								=> $this->query,
					'lang_search'						=> lang('search'),
					'table_header_list_config'			=> $table_header,
					'values_list_config'				=> $content,
					'table_add'							=> $table_add
				);

			$appname	= lang('location');
			$function_msg	= lang('list config');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_config' => $data));
			$this->save_sessiondata();
		}

		function edit_config()
		{
			if(!$this->acl_manage)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 16, 'acl_location'=> $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::config';

			$column_name	= phpgw::get_var('column_name');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_location'));

			if (isset($values['save']) && $values['save'])
			{
				$receipt = $this->bo->save_config($values,$column_name);
			}

			$type_id	= $this->bo->read_config_single($column_name);

			$function_msg = lang('edit location config for') . ' ' .$column_name;

			$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_location.edit_config',
					'column_name'	=> $column_name
				);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
				(
					'msgbox_data'				=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),

					'form_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_location.config')),

					'lang_column_name'			=> lang('Column name'),
					'lang_save'					=> lang('save'),
					'lang_done'					=> lang('done'),
					'column_name'				=> $column_name,
					'value_name'				=> (isset($values['name'])?$values['name']:''),

					'location_list'				=> $this->bo->select_location_type($type_id),

					'lang_config_statustext'	=> lang('Select the level for this information'),
					'lang_done_standardtext'	=> lang('Back to the list'),
					'lang_save_standardtext'	=> lang('Save the standard'),
					'type_id'					=> (isset($values['type_id'])?$values['type_id']:''),
					'value_descr'				=> (isset($values['descr'])?$values['descr']:'')
				);

			$appname	= lang('location');

			//_debug_array($data);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_config' => $data));
		}
	}
