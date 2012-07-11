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
	* @subpackage document
 	* @version $Id$
	*/
	phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_uidocument
	{
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;
		var $allrows;

		var $public_functions = array
			(
				'index'  	=> true,
				'list_doc'	=> true,
				'view' 		=> true,
				'view_file' => true,
				'edit'   	=> true,
				'delete' 	=> true
			);

		function property_uidocument()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::documentation";

			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bodocument',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->cats					= & $this->bo->cats;
			$this->bolocation			= CreateObject('property.bolocation');
			$this->config				= CreateObject('phpgwapi.config','property');
			$this->boadmin_entity		= CreateObject('property.boadmin_entity');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.document';
			$this->acl_read 			= $this->acl->check('.document', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.document', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.document', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.document', PHPGW_ACL_DELETE, 'property');

			//$this->rootdir 				= $this->bo->rootdir;
			$this->bofiles				= & $this->bo->bofiles;
			$this->fakebase 			= $this->bo->fakebase;
			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->filter				= $this->bo->filter;
			$this->cat_id				= $this->bo->cat_id;
			$this->status_id			= $this->bo->status_id;
			$this->entity_id			= $this->bo->entity_id;
			$this->doc_type				= $this->bo->doc_type;
			$this->query_location		= $this->bo->query_location;
			$this->allrows				= $this->bo->allrows;

			// FIXME: $this->entity_id always has a value set here - skwashd jan08
			if ( $this->entity_id )
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$this->entity_id}";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= '::location';
			}
		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'		=> $this->start,
					'query'		=> $this->query,
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'filter'	=> $this->filter,
					'cat_id'	=> $this->cat_id,
					'status_id'	=> $this->status_id,
					'entity_id'	=> $this->entity_id,
					'doc_type'	=> $this->doc_type,
					'query_location'=> $this->query_location
				);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$entity_id = phpgw::get_var('entity_id', 'int');
			$preserve = phpgw::get_var('preserve', 'bool');

			if($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start				= $this->bo->start;
				$this->query				= $this->bo->query;
				$this->sort					= $this->bo->sort;
				$this->order				= $this->bo->order;
				$this->filter				= $this->bo->filter;
				$this->cat_id				= $this->bo->cat_id;
				$this->status_id			= $this->bo->status_id;
				$this->entity_id			= $this->bo->entity_id;
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uidocument.index',
						'sort'            		=> $this->sort,
						'order'     		   	=> $this->order,
						'cat_id'        		=> $this->cat_id,
						'filter'        		=> $this->filter,
						'status_id'        		=> $this->status_id,
						'query'   	     		=> $this->query,
						'doc_type'        		=> $this->doc_type,
						'entity_id'        		=> $this->entity_id
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uidocument.index',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."cat_id:'{$this->cat_id}',"
					."filter:'{$this->filter}',"
					."status_id:'{$this->status_id}',"
					."query:'{$this->query}',"
					."doc_type:'{$this->doc_type}',"
					."entity_id:'{$this->entity_id}'";

				//_debug_array($datatable['config']['base_java_url']);die;

				$link_data = array
					(
						'menuaction'	=> 'property.uidocument.index',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'cat_id'	=> $this->cat_id,
						'filter'	=> $this->filter,
						'status_id'	=> $this->status_id,
						'query'		=> $this->query,
						'doc_type'	=> $this->doc_type,
						'entity_id'	=> $this->entity_id
					);

				$datatable['config']['allow_allrows'] = false;

				$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->doc_type,'globals' => True));
				$default_value = array ('cat_id'=>'','name'=> lang('no document type'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$values_combo_box[1]  = $this->bocommon->get_user_list_right2('filter',4,$this->filter,$this->acl_location,array('all'),$default=$this->account);
				$default_value = array ('id'=>'','name'=>lang('no status'));
				array_unshift ($values_combo_box[1],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' 		=> 'property.uidocument.index',
								'sort'       => $this->sort,
								'order'       => $this->order,
								'cat_id'       => $this->cat_id,
								'filter'       => $this->filter,
								'status_id'       => $this->status_id,
								'query'       => $this->query,
								'doc_type'       => $this->doc_type,
								'entity_id'       => $this->entity_id
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								( //boton 	CATEGORY
									'id' => 'btn_type_id',
									'name' => 'type_id',
									'value'	=> lang('Type'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	STATUS
									'id' => 'btn_user_id',
									'name' => 'user_id',
									'value'	=> lang('User'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 8
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 7
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 6
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								)
							)
						)
					)
				);

			}

			$document_list = $this->bo->read();
			$uicols	= $this->bo->uicols;
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($document_list) AND is_array($document_list))
			{
				foreach($document_list as $document_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if(isset($document_entry['query_location'][$uicols['name'][$k]]) && $document_entry['query_location'][$uicols['name'][$k]])
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['statustext']		= lang('search');
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $document_entry[$uicols['name'][$k]];
							$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['java_link']		= true;
							$datatable['rows']['row'][$j]['column'][$k]['link']				= $document_entry['query_location'][$uicols['name'][$k]];
						}
						else
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $document_entry[$uicols['name'][$k]];
						}
					}

					if($this->acl_read)
					{
						$datatable['rows']['row'][$j]['column'][$k]['name'] 			= lang('view');
						$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
						$datatable['rows']['row'][$j]['column'][$k]['link']		=	$GLOBALS['phpgw']->link('/index.php', array
							(
								'menuaction'=> 'property.uidocument.list_doc',
								'location_code'=>  $document_entry['location_code'],
								'p_num'=> isset($document_entry['p_num']) ? $document_entry['p_num'] :'',
								'entity_id'=> isset($document_entry['p_entity_id']) ? $document_entry['p_entity_id'] : '',
								'cat_id'=> isset($document_entry['p_cat_id']) ? $document_entry['p_cat_id'] : '',
								'doc_type'=> $this->doc_type
							));
						$datatable['rows']['row'][$j]['column'][$k]['value']		= lang('documents');
						$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
					}

					$j++;
				}

				//_debug_array($datatable['rows']['row']);die;
				$datatable['rowactions']['action'] = array();

				if ($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array
						(
							'my_name' 			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
							(
								'menuaction'=> 'property.uidocument.edit',
								'entity_id'=> $this->entity_id,
								'cat_id'=> $this->cat_id
							))
						);
				}
			}

			$count_uicols_descr = count($uicols['descr']);

			for ($i=0;$i<$count_uicols_descr;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
				}

				if($uicols['name'][$i]=='loc1')
				{
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']	= 'location_code';
				}

				if($uicols['name'][$i]=='address')
				{
					$datatable['headers']['header'][$i]['sortable']		= true;
					$datatable['headers']['header'][$i]['sort_field']	= 'address';
				}
			}

			if($this->acl_read)
			{
				$datatable['headers']['header'][$i]['formatter'] 		= '""';
				$datatable['headers']['header'][$i]['name'] 			= lang('view');
				$datatable['headers']['header'][$i]['text'] 			= 'view';
				$datatable['headers']['header'][$i]['visible'] 			= true;
				$datatable['headers']['header'][$i]['sortable']			= false;
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($document_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname					= lang('documents');
			$function_msg				= lang('list documents');

			//_debug_array($datatable['headers']['header']);die;

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
							$json_row[$column['name']] = "<a href='#' id='{$column['link']}' onclick='javascript:filter_data(this.id);'>{$column['value']}</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='{$column['link']}'>{$column['value']}</a>";
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

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'document.index', 'property' );
			//$this->save_sessiondata();
		}

		function list_doc()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$preserve = phpgw::get_var('preserve', 'bool');

			if($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start				= $this->bo->start;
				$this->query				= $this->bo->query;
				$this->sort					= $this->bo->sort;
				$this->order				= $this->bo->order;
				$this->filter				= $this->bo->filter;
				$this->entity_id			= $this->bo->entity_id;
				$this->cat_id				= $this->bo->cat_id;
				$this->status_id			= $this->bo->status_id;
			}


			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}


			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','document_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','document_receipt','');

			$location_code = phpgw::get_var('location_code');
			if($this->query_location)
			{
				$location_code = $this->query_location;
			}

			$p_num = phpgw::get_var('p_num');

			if($this->cat_id)
			{
				$entity_data[$this->entity_id]['p_num']=$p_num;
				$entity_data[$this->entity_id]['p_entity_id']=$this->entity_id;
				$entity_data[$this->entity_id]['p_cat_id']=$this->cat_id;
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$lookup_entity	= $this->bocommon->get_lookup_entity('document');
				$appname_sub	= $entity['name'];
				$_values	= execMethod('property.soentity.read_single',array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id, 'num' => $p_num));

				$location = $this->bo->read_location_data($_values['location_code']);
				$location_code = $_values['location_code'];
				unset($_values);
			}
			else
			{
				$location = $this->bo->read_location_data($location_code);
				$appname_sub	= lang('location');
			}

			if($category['name'])
			{
				$entity_data[$this->entity_id]['p_cat_name']=$category['name'];
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uidocument.list_doc',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'entity_id'	=> $this->entity_id,
					'cat_id'	=> $this->cat_id,
					'p_num'		=> $p_num,
					'doc_type'	=> $this->doc_type,
					'location_code'	=> $location_code,
					'filter'	=> $this->filter,
					'query'		=> $this->query,
					'query_location'=> $this->query_location,
					'allrows'		=> $this->allrows
				);

			$this->config->read();
			$files_url = $this->config->config_data['files_url'];

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uidocument.list_doc',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'entity_id'	=> $this->entity_id,
						'cat_id'	=> $this->cat_id,
						'p_num'		=> $p_num,
						'doc_type'	=> $this->doc_type,
						'location_code'	=> $location_code,
						'filter'	=> $this->filter,
						'query'		=> $this->query,
						'query_location'=> $this->query_location,
						'allrows'		=> $this->allrows
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uidocument.list_doc',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."entity_id:'{$this->entity_id}',"
					."cat_id:'{$this->cat_id}',"
					."p_num:'{$p_num}',"
					."doc_type:'{$this->doc_type}',"
					."location_code:'{$location_code}',"
					."filter:'{$this->filter}',"
					."query:'{$this->query}',"
					."query_location:'{$this->query_location}',"
					."allrows:'{$this->allrows}'";

				$datatable['config']['allow_allrows'] = true;

				$values_combo_box[0] = $this->cats->formatted_xslt_list(array('format'=>'filter','selected' => $this->doc_type,'globals' => True));
				$default_value = array ('cat_id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$values_combo_box[1]  = $this->bocommon->get_user_list_right2('filter',4,$this->filter,$this->acl_location,array('all'),$default=$this->account);
				$default_value = array ('id'=>'','name'=>lang('no user'));
				array_unshift ($values_combo_box[1],$default_value);

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'property.uidocument.list_doc',
								'sort'		=> $this->sort,
								'order'		=> $this->order,
								'entity_id'	=> $this->entity_id,
								'cat_id'	=> $this->cat_id,
								'p_num'		=> $p_num,
								'doc_type'	=> $this->doc_type,
								'location_code'	=> $location_code,
								'filter'	=> $this->filter,
								'query'		=> $this->query,
								'query_location'=> $this->query_location
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
								( //boton 	CATEGORY
									'id' => 'btn_type_id',
									'name' => 'type_id',
									'value'	=> lang('Type'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 1
								),
								array
								( //boton 	STATUS
									'id' => 'btn_user_id',
									'name' => 'user_id',
									'value'	=> lang('User'),
									'type' => 'button',
									'style' => 'filter',
									'tab_index' => 2
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 8
								),
								array
								( //boton     SEARCH
									'id' => 'btn_search',
									'name' => 'search',
									'value'    => lang('search'),
									'type' => 'button',
									'tab_index' => 7
								),
								array
								( // TEXT INPUT
									'name'     => 'query',
									'id'     => 'txt_query',
									'value'    => '',//$query,
									'type' => 'text',
									'onkeypress' => 'return pulsar(event)',
									'size'    => 28,
									'tab_index' => 6
								),
								array
								( //container of  control's Form
									'type'	=> 'label',
									'id'	=> 'controlsForm_container',
									'value'	=> ''
								)
							),
							'hidden_value' => array
							(
								array
								( //div values  combo_box_0
									'id' => 'values_combo_box_0',
									'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
								),
								array
								( //div values  combo_box_1
									'id' => 'values_combo_box_1',
									'value'	=> $this->bocommon->select2String($values_combo_box[1])
								)
							)
						)
					)
				);
			}

			$document_list = $this->bo->read_at_location($location_code);

			if($this->cat_id)
			{
				$directory = "{$this->fakebase}/document/entity_{$this->entity_id}_{$this->cat_id}/{$p_num}/{$this->doc_type}";
			}
			else
			{
				$directory = "{$this->fakebase}/document/{$location_code}/{$this->doc_type}";
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);


			$uicols['name'][0]		= 'document_name';
			$uicols['descr'][0]		= lang('Document name');
			$uicols['datatype'][0]	= 'link';
			$uicols['name'][1]		= 'title';
			$uicols['descr'][1]		= lang('Title');
			$uicols['datatype'][1]	= 'text';
			$uicols['name'][2]		= 'doc_type';
			$uicols['descr'][2]		= lang('Doc type');
			$uicols['datatype'][2]	= 'text';
			$uicols['name'][3]		= 'user';
			$uicols['descr'][3]		= lang('coordinator');
			$uicols['datatype'][3]	= 'text';
			$uicols['name'][4]		= 'document_id';
			$uicols['descr'][4]		= lang('document id');
			$uicols['datatype'][4]	= 'text';
			$uicols['name'][5]		= 'document_date';
			$uicols['descr'][5]		= lang('document date');
			$uicols['datatype'][5]	= 'text';
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			foreach($document_list as $document_entry )
			{
				for ($k=0;$k<$count_uicols_name;$k++)
				{
					if($document_entry['link'])
					{
						if(!preg_match('/^HTTP/i', $document_entry['link']))
						{
							$document_entry['link'] = 'file:///' . str_replace(':','|',$document_entry['link']);
						}

						$link_view_file=$document_entry['link'];
						$document_entry['document_name']='link';
						unset($link_to_files);
					}
					else
					{
						if(!$link_to_files)
						{
							$link_view_file = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.view_file', 'id'=> $document_entry['document_id'], 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $p_num));
							$link_to_files = $files_url;
						}
						else
						{
							$link_view_file ="{$files_url}/{$directory}/{$document_entry['document_name']}";
						}
					}

					if($uicols['input_type'][$k]!='hidden')
					{
						$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
						$datatable['rows']['row'][$j]['column'][$k]['value']			= $document_entry[$uicols['name'][$k]];

						if(isset($uicols['datatype']) && isset($uicols['datatype'][$k]) && $uicols['datatype'][$k]=='link' && $document_entry[$uicols['name'][$k]])
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 		= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= $link_view_file;
							$datatable['rows']['row'][$j]['column'][$k]['value']		= $document_entry[$uicols['name'][$k]];
							$datatable['rows']['row'][$j]['column'][$k]['target']		= '_blank';
						}
					}
				}
				$j++;
			}

			$location_data = array();

			$location_data = $this->bolocation->initiate_ui_location(array(
				'values'		=> $location,
				'type_id'		=> count(explode('-',$location_code)),
				'no_link'		=> false, // disable lookup links for location type less than type_id
				'tenant'		=> false,
				'lookup_type'		=> 'view',
				'lookup_entity'		=> $lookup_entity,
				'entity_data'		=> $entity_data,
				'link_data'		=> $link_data,
				'query_link'		=> true
			));

			$datatable['locdata'] = $location_data['location'];

			$datatable['rowactions']['action'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'document_id',
							'source'	=> 'document_id'
						),
					)
				);

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'view',
						'statustext' 	=> lang('view this entity'),
						'text'			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uidocument.view',
							'from'			=> 'list_doc'
						)),
						'parameters'	=> $parameters
					);
			}

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'edit',
						'statustext' 	=> lang('edit this entity'),
						'text'			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uidocument.edit',
							'from'			=> 'list_doc'
						)),
						'parameters'	=> $parameters
					);
			}

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'delete',
						'statustext' 	=> lang('delete this entity'),
						'text'			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uidocument.delete',
							'location_code'	=> $location_code,
							'p_num'		=> $p_num
						)),
						'parameters'	=> $parameters
					);
			}

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 			=> 'add',
						'statustext' 	=> lang('add an entity'),
						'text'			=> lang('add'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uidocument.edit',
							'from'			=> 'list_doc',
							'location_code' => $location_code,
							'p_entity_id'	=> $this->entity_id,
							'p_cat_id'		=> $this->cat_id,
							'p_num'			=> $p_num
						))
					);
			}

			unset($parameters);

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols['formatter'][$i]==''?  '""' : $uicols['formatter'][$i]);
					$datatable['headers']['header'][$i]['name'] 			= $uicols['name'][$i];
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;

					if($uicols['name'][$i]=='document_name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'document_name';
					}

					if($uicols['name'][$i]=='document_id')
					{
						$datatable['headers']['header'][$i]['visible'] 		= false;
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($document_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('document');
			$function_msg	= lang('list document');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'document_name'; // name key Column in myColumnDef
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
			if(isset($datatable['rows']['row']) && is_array($datatable['rows']['row']))
			{
				foreach( $datatable['rows']['row'] as $row )
				{
					$json_row = array();
					foreach( $row['column'] as $column)
					{
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='{$column['link']} onclick='javascript:filter_data(this.id);'>{$column['value']}</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
						{
							$json_row[$column['name']] = "<a href='{$column['link']}' target = '_blank'>{$column['value']}</a>";
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

			$json ['toolbar_height'] = 40;
			if(isset($location_data) && is_array($location_data))
			{
				$json ['toolbar_height'] = $json ['toolbar_height'] + (count($datatable['locdata']) * 10);
				$json ['current_consult'] = $datatable['locdata'];
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'document.list_doc', 'property' );

		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$document_id 	= phpgw::get_var('id', 'int');

			$file 			= $this->bo->get_file($document_id);

			$this->bofiles->view_file('', $file);
		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$from 			= phpgw::get_var('from');
			$document_id 		= phpgw::get_var('document_id', 'int');
			//			$location_code 		= phpgw::get_var('location_code');
			$values			= phpgw::get_var('values');

			if(!$from)
			{
				$from='index';
			}
			$GLOBALS['phpgw']->xslttpl->add_file(array('document'));

			$bypass = phpgw::get_var('bypass', 'bool');

			$receipt = array();

			if($_POST && !$bypass)
			{
				$insert_record 		= $GLOBALS['phpgw']->session->appsession('insert_record','property');
				$insert_record_entity	= $GLOBALS['phpgw']->session->appsession('insert_record_entity','property');

				for ($j=0;$j<count($insert_record_entity);$j++)
				{
					$insert_record['extra'][$insert_record_entity[$j]]	= $insert_record_entity[$j];
				}

				$values = $this->bocommon->collect_locationdata($values,$insert_record);
			}
			else
			{
				$location_code 		= phpgw::get_var('location_code');
				$p_entity_id		= phpgw::get_var('p_entity_id', 'int');
				$p_cat_id			= phpgw::get_var('p_cat_id', 'int');
				$values['p'][$p_entity_id]['p_entity_id']	= $p_entity_id;
				$values['p'][$p_entity_id]['p_cat_id']		= $p_cat_id;
				$values['p'][$p_entity_id]['p_num']		= phpgw::get_var('p_num');
				$values['p_entity_id']=$p_entity_id;
				$values['p_cat_id']=$p_cat_id;

				if($p_entity_id && $p_cat_id)
				{
					$entity_category = $this->boadmin_entity->read_single_category($p_entity_id,$p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if(phpgw::get_var('p_num'))
				{
					$_values	= execMethod('property.soentity.read_single',array('entity_id'=>$p_entity_id,'cat_id'=>$p_cat_id, 'num' => phpgw::get_var('p_num')));
	
					$location = $this->bo->read_location_data($_values['location_code']);
					$location_code = $_values['location_code'];
					unset($_values);
				}

				if($location_code)
				{
					$values['location_data'] = $this->bolocation->read_single($location_code,array('view' => true));
				}
			}

			if($values[extra]['p_entity_id'])
			{
				$this->entity_id=$values['extra']['p_entity_id'];
				$this->cat_id=$values['extra']['p_cat_id'];
				$p_num=$values['extra']['p_num'];
			}

/*
			if($this->cat_id)
			{
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$values['entity_name']=$entity['name'];
				$values['category_name']=$category['name'];
			}
 */
			if ($values['save'])
			{
				$values['vendor_id']		= phpgw::get_var('vendor_id', 'int', 'POST');

				if(!$values['link'])
				{
					$values['document_name']=str_replace (' ','_',$_FILES['document_file']['name']);
				}

				if((!$values['document_name'] && !$values['document_name_orig']) && !$values['link'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a file to upload !'));
				}

				if(!$values['doc_type'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					$error_id=true;
				}

				if(!$values['status'])
				{
					//					$receipt['error'][]=array('msg'=>lang('Please select a status !'));
				}
				if(!$values['location'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location !'));
				}

				$values['location_code'] = isset($values['location_code']) && $values['location_code'] ? $values['location_code'] : implode('-',$values['location']);

				$document_dir = "document/{$values['location_code']}";

				if($values['extra']['p_num'])
				{
					$document_dir = "document/entity_{$this->entity_id}_{$this->cat_id}/{$values['extra']['p_num']}";
				}

				$document_dir .= "/{$values['doc_type']}";

				$to_file	= "{$this->bofiles->fakebase}/{$document_dir}/{$values['document_name']}";

				if((!isset($values['document_name_orig']) || !$values['document_name_orig']) && $this->bofiles->vfs->file_exists(array
					(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$receipt['error'][]=array('msg'=>lang('This file already exists !'));
				}

				if(!$receipt['error'])
				{
					if(isset($_FILES['document_file']['tmp_name']) && $_FILES['document_file']['tmp_name'])
					{
						$receipt = $this->bofiles->create_document_dir($document_dir);
						if(isset($values['document_name_orig']) && $values['document_name_orig'] && (!isset($values['document_name']) || !$values['document_name']))
						{
							$old_file 	= $this->bo->get_file($document_id);

							$to_file .= $values['document_name_orig'];

							if($old_file != $to_file)
							{
								$this->bofiles->vfs->override_acl = 1;
								if(!$this->bofiles->vfs->mv (array (
									'from'		=> $old_file,
									'to'		=> $to_file,
									'relatives'	=> array (RELATIVE_ALL, RELATIVE_ALL))))
								{
									$receipt['error'][]=array('msg'=>lang('Failed to move file !'));
								}
								$this->bofiles->vfs->override_acl = 0;
							}
						}
					}

					$values['document_id'] = $document_id;

					if(!$receipt['error'])
					{
						if($values['document_name'] && !$values['link'])
						{
							$this->bofiles->vfs->override_acl = 1;

							if(!$this->bofiles->vfs->cp (array (
								'from'		=> $_FILES['document_file']['tmp_name'],
								'to'		=> $to_file,
								'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
							{
								$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
							}
							$this->bofiles->vfs->override_acl = 0;
						}
					}
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save($values);
					//					$document_id=$receipt['document_id'];
					$GLOBALS['phpgw']->session->appsession('session_data','document_receipt',$receipt);
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'property.uidocument.list_doc', 'location_code'=> implode("-", $values['location']), 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $values['extra']['p_num']));
				}
				else
				{
					$values['document_name']='';
					if($values['location'])
					{
						//				$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $this->bolocation->read_single($values['location_code'],$values['extra']);
					}
					if($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num']=$values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id']=$values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id']=$values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name']=$_POST['entity_cat_name_'.$values['extra']['p_entity_id']];
					}
				}
			}

			if ($document_id ||(!$receipt['error'] && $values['document_id']))
			{
				$values = $this->bo->read_single($document_id);
				$record_history = $this->bo->read_record_history($document_id);
				$function_msg = lang('Edit document');
			}
			else
			{
				$function_msg = lang('Add document');
			}

			$table_header_history[] = array
				(
					'lang_date'		=> lang('Date'),
					'lang_user'		=> lang('User'),
					'lang_action'		=> lang('Action'),
					'lang_new_value'	=> lang('New value')
				);

			if ($values['doc_type'])
			{
				$this->doc_type = $values['doc_type'];
			}
			if ($values['location_code'])
			{
				$location_code = $values['location_code'];
			}
/*			if ($values['p_num'])
			{
				$p_num = $values['p_num'];
			}
 */
			$location_data=$this->bolocation->initiate_ui_location(array
				(
					'values'		=> $values['location_data'],
					'type_id'		=> -1, // calculated from location_types
					'no_link'		=> false, // disable lookup links for location type less than type_id
					'tenant'		=> false,
					'lookup_type'		=> 'form',
					'lookup_entity'		=> $this->bocommon->get_lookup_entity('document'),
					'entity_data'		=> $values['p']
				));


			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array
				(
					'vendor_id'	=> $values['vendor_id'],
					'vendor_name'	=> $values['vendor_name']));


			$link_data = array
				(
					'menuaction'	=> 'property.uidocument.edit',
					'document_id'	=> $document_id,
					'from'		=> $from,
					'location_code' => $values['location_code'],
					'entity_id'	=> $this->entity_id,
					'cat_id'	=> $this->cat_id,
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$GLOBALS['phpgw']->jqcal->add_listener('values_document_date');

			//data test    	$record_history = array(array(value_date=>"1111",value_user=>"22222",value_action=>"33333",value_new_value=>"44444444"));

			//----datatable settings------------------------------------

			$datavalues[0] = array
				(
					'name'					=> "0",
					'values' 				=> json_encode($record_history),
					'total_records'			=> count($record_history),
					'is_paginator'			=> 0,
					'footer'				=> 0
				);	

			$myColumnDefs[0] = array
				(
					'name'		=> "0",
					'values'	=>	json_encode(array(	array('key' => 'value_date',	'label'=>lang('Date'),	'sortable'=>true,'resizeable'=>true),
					array('key' => 'value_user',	'label'=>lang('User'),	'sortable'=>true,'resizeable'=>true),
					array('key' => 'value_action',	'label'=>lang('Action'),	'sortable'=>true,'resizeable'=>true),
					array('key' => 'value_new_value','label'=>lang('New value'),'sortable'=>true,'resizeable'=>true)))
				);	

			//-----------------------------------------datatable settings-----

			$data = array
				(
					'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
					'datatable'						=> $datavalues,
					'myColumnDefs'					=> $myColumnDefs,	

					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'vendor_data'					=> $vendor_data,
					'record_history'				=> $record_history,
					'table_header_history'			=> $table_header_history,
					'lang_history'					=> lang('History'),
					'lang_no_history'				=> lang('No history'),

					'lang_document_date_statustext'	=> lang('Select date the document was created'),
					'lang_document_date'			=> lang('document date'),
					'value_document_date'			=> $values['document_date'],

					'vendor_data'					=> $vendor_data,
					'location_data'					=> $location_data,
					'location_type'					=> 'form',
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_year'						=> lang('Year'),
					'lang_category'					=> lang('category'),
					'lang_save'						=> lang('save'),
					'lang_save_statustext'			=> lang('Save the document'),

					'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.' .$from, 'location_code'=> $location_code, 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $p_num, 'preserve'=> 1)),
					'lang_done'						=> lang('done'),
					'lang_done_statustext'			=> lang('Back to the list'),

					'lang_update_file'				=> lang('Update file'),

					'lang_document_id'				=> lang('document ID'),
					'value_document_id'				=> $document_id,

					'lang_document_name'			=> lang('document name'),
					'value_document_name'			=> $values['document_name'],
					'lang_document_name_statustext'	=> lang('Enter document Name'),

					'lang_floor_id'					=> lang('Floor ID'),
					'value_floor_id'				=> $values['floor_id'],
					'lang_floor_statustext'			=> lang('Enter the floor ID'),

					'lang_title'					=> lang('title'),
					'value_title'					=> $values['title'],
					'lang_title_statustext'			=> lang('Enter document title'),

					'lang_version'					=> lang('Version'),
					'value_version'					=> $values['version'],
					'lang_version_statustext'		=> lang('Enter document version'),

					'lang_link'						=> lang('Link'),
					'value_link'					=> $values['link'],
					'lang_link_statustext'			=> lang('Alternative - link instead of uploading a file'),

					'lang_descr_statustext'			=> lang('Enter a description of the document'),
					'lang_descr'					=> lang('Description'),
					'value_descr'					=> $values['descr'],
					'lang_no_cat'					=> lang('Select category'),
					'lang_cat_statustext'			=> lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
					'value_cat_id'					=> $values['doc_type'],
					'cat_select'					=> $this->cats->formatted_xslt_list(array('select_name' => 'values[doc_type]','selected' => $values['doc_type']?$values['doc_type']:$this->doc_type)),
					'lang_coordinator'				=> lang('Coordinator'),
					'lang_user_statustext'			=> lang('Select the coordinator the document belongs to. To do not use a category select NO USER'),
					'select_user_name'				=> 'values[coordinator]',
					'lang_no_user'					=> lang('Select coordinator'),
					'user_list'						=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator']?$values['coordinator']:$this->account,$this->acl_location),

					'status_list'					=> $this->bo->select_status_list('select',$values['status']),
					'status_name'					=> 'values[status]',
					'lang_no_status'				=> lang('Select status'),
					'lang_status'					=> lang('Status'),
					'lang_status_statustext'		=> lang('What is the current status of this document ?'),

					'value_location_code'			=> $values['location_code'],

					'branch_list'					=> $this->bo->select_branch_list($values['branch_id']),
					'lang_no_branch'				=> lang('No branch'),
					'lang_branch'					=> lang('branch'),
					'lang_branch_statustext'		=> lang('Select the branch for this document')
				);
			//----datatable settings------------------------------------
			phpgwapi_yui::load_widget('dragdrop');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('menu');
			phpgwapi_yui::load_widget('connection');
			phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

			$GLOBALS['phpgw']->css->validate_file('datatable');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/datatable/assets/skins/sam/datatable.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/paginator/assets/skins/sam/paginator.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/yahoo/container/assets/skins/sam/container.css');			
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'document.view', 'property' );
			//--------------------------------datatable settings--------

			$appname		= lang('document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$location_code = phpgw::get_var('location_code');
			$p_num = phpgw::get_var('p_num');
			$document_id = phpgw::get_var('document_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' 	=> 'property.uidocument.list_doc',
					'location_code'	=> $location_code,
					'p_num'		=> $p_num
				);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($document_id);
				return "document_id ".$document_id." ".lang("has been deleted");
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.delete', 'document_id'=> $document_id, 'location_code'=> $location_code, 'p_num'=> $p_num)),
					'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
					'lang_yes'		=> lang('yes'),
					'lang_yes_statustext'	=> lang('Delete the entry'),
					'lang_no_statustext'	=> lang('Back to the list'),
					'lang_no'		=> lang('no')
				);

			$appname	= lang('document');
			$function_msg	= lang('delete document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
			//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function view()
		{

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$from 		= phpgw::get_var('from');
			$document_id 	= phpgw::get_var('document_id', 'int');

			if(!$from)
			{
				$from='index';
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('document'));

			$values = $this->bo->read_single($document_id);
			$function_msg = lang('view document');
			$record_history = $this->bo->read_record_history($document_id);

			$table_header_history[] = array
				(
					'lang_date'		=> lang('Date'),
					'lang_user'		=> lang('User'),
					'lang_action'		=> lang('Action'),
					'lang_new_value'	=> lang('New value')
				);

			if ($values['doc_type'])
			{
				$this->cat_id = $values['doc_type'];
			}

			$location_data=$this->bolocation->initiate_ui_location(array
				(
					'values'	=> $values['location_data'],
					'type_id'	=> count(explode('-',$values['location_data']['location_code'])),
					'no_link'	=> false, // disable lookup links for location type less than type_id
					'tenant'	=> false,
					'lookup_type'	=> 'view',
					'lookup_entity'	=> $this->bocommon->get_lookup_entity('document'),
					'entity_data'	=> $values['p']
				));


			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
				'type'		=> 'view',
				'vendor_id'	=> $values['vendor_id'],
				'vendor_name'	=> $values['vendor_name']));


			$link_data = array
				(
					'menuaction'	=> 'property.uidocument.edit',
					'document_id'	=> $document_id
				);

			$categories = $this->cats->formatted_xslt_list(array('selected' => $values['doc_type']));

			$data = array
				(
					'vendor_data'						=> $vendor_data,
					'record_history'					=> $record_history,
					'table_header_history'				=> $table_header_history,
					'lang_history'					=> lang('History'),
					'lang_no_history'				=> lang('No history'),

					'lang_document_date'				=> lang('document date'),
					'value_document_date'				=> $values['document_date'],

					'vendor_data'					=> $vendor_data,
					'location_data'					=> $location_data,
					'location_type'					=> 'form',
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.' .$from, 'location_code'=> $values['location_code'], 'entity_id'=> $values['p_entity_id'], 'cat_id'=> $values['p_cat_id'], 'preserve'=> 1)),
					'lang_year'					=> lang('Year'),
					'lang_category'					=> lang('category'),
					'lang_save'					=> lang('save'),
					'lang_done'					=> lang('done'),

					'lang_update_file'				=> lang('Update file'),

					'lang_document_id'				=> lang('document ID'),
					'value_document_id'				=> $document_id,

					'lang_document_name'				=> lang('document name'),
					'value_document_name'				=> $values['document_name'],
					'lang_document_name_statustext'			=> lang('Enter document Name'),

					'lang_floor_id'					=> lang('Floor ID'),
					'value_floor_id'				=> $values['floor_id'],
					'lang_floor_statustext'				=> lang('Enter the floor ID'),

					'lang_title'					=> lang('title'),
					'value_title'					=> $values['title'],
					'lang_title_statustext'				=> lang('Enter document title'),

					'lang_version'					=> lang('Version'),
					'value_version'					=> $values['version'],
					'lang_version_statustext'			=> lang('Enter document version'),

					'lang_descr_statustext'				=> lang('Enter a description of the document'),
					'lang_descr'					=> lang('Description'),
					'value_descr'					=> $values['descr'],
					'lang_done_statustext'				=> lang('Back to the list'),

					'cat_list'						=> $categories['cat_list'],

					'lang_coordinator'				=> lang('Coordinator'),
					'lang_user_statustext'				=> lang('Select the coordinator the document belongs to. To do not use a category select NO USER'),
					'select_user_name'				=> 'values[coordinator]',
					'lang_no_user'					=> lang('Select coordinator'),
					'user_list'					=> $this->bocommon->get_user_list('select',$values['coordinator'],$extra=false,$default=false,$start=-1,$sort='ASC',$order='account_lastname',$query='',$offset=-1),

					'status_list'					=> $this->bo->select_status_list('select',$values['status']),
					'status_name'					=> 'values[status]',
					'lang_no_status'				=> lang('Select status'),
					'lang_status'					=> lang('Status'),
					'lang_status_statustext'			=> lang('What is the current status of this document ?'),


					'branch_list'					=> $this->bo->select_branch_list($values['branch_id']),
					'lang_no_branch'				=> lang('No branch'),
					'lang_branch'					=> lang('branch'),
					'lang_branch_statustext'			=> lang('Select the branch for this document'),

					'edit_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.edit', 'document_id'=> $document_id, 'from'=> $from)),
					'lang_edit_statustext'				=> lang('Edit this entry'),
					'lang_edit'					=> lang('Edit')
				);

			$appname = lang('document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		}
	}

