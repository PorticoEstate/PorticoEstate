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

	class property_uiadmin_entity
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
			'category' 				=> true,
			'edit'   				=> true,
			'edit_category'			=> true,
			'view'   				=> true,
			'delete' 				=> true,
			'list_attribute_group'	=> true,
			'list_attribute'		=> true,
			'edit_attrib_group'		=> true,
			'edit_attrib' 			=> true,
			'list_custom_function'	=> true,
			'edit_custom_function'	=> true
		);

		function property_uiadmin_entity()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.boadmin_entity',true);
			$this->bocommon				= & $this->bo->bocommon;

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->entity_id			= $this->bo->entity_id;
			$this->cat_id				= $this->bo->cat_id;
			$this->allrows				= $this->bo->allrows;
			$this->type					= $this->bo->type;
			$this->type_app				= $this->bo->type_app;
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.admin.entity';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->type_app[$this->type]);
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->type_app[$this->type]);
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->type_app[$this->type]);
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]);
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, $this->type_app[$this->type]);

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::{$this->type_app[$this->type]}::entity";
		}

		function save_sessiondata()
		{
			$data = array
			(
				'start'		=> $this->start,
				'query'		=> $this->query,
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'allrows'	=> $this->allrows,
				'entity_id'	=> $this->entity_id,
				'cat_id'	=> $this->cat_id
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$this->bocommon->reset_fm_cache();

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uiadmin_entity.index',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'query'		=>$this->query,
					'type'		=> $this->type
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_entity.index',"
	    											."sort:'{$this->sort}',"
	    											."order:'{$this->order}',"
						 	                        ."query:'{$this->query}',"
						 	                        ."type:'{$this->type}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_entity.index',
					'sort'		=>$this->sort,
					'order'		=>$this->order,
					'query'		=>$this->query,
					'type'		=> $this->type
				);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uiadmin_entity.index',
								'sort'		=>$this->sort,
								'order'		=>$this->order,
								'query'		=>$this->query,
								'type'		=> $this->type
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

			$entity_list = $this->bo->read();
			$uicols['name'][0]	= 'id';
			$uicols['descr'][0]	= lang('Entity ID');
			$uicols['name'][1]	= 'name';
			$uicols['descr'][1]	= lang('Name');
			$uicols['name'][2]	= 'descr';
			$uicols['descr'][2]	= lang('Descr');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($entity_list) AND is_array($entity_list))
			{
				foreach($entity_list as $entity_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']				= $entity_entry[$uicols['name'][$k]];
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
						'name'		=> 'entity_id',
						'source'	=> 'id'
					),
				)
			);

			$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'categories',
						'statustext' 	=> lang('categories'),
						'text'			=> lang('Categories'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uiadmin_entity.category'
								)),
					'parameters'	=> $parameters2
					);

			$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'edit',
						'statustext' 	=> lang('edit'),
						'text'			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uiadmin_entity.edit'
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
									'menuaction'	=> 'property.uiadmin_entity.delete'
								)),
					'parameters'	=> $parameters2
					);


			$datatable['rowactions']['action'][] = array(
					'my_name' 		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uiadmin_entity.edit'
					)));

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
			$datatable['pagination']['records_returned']= count($entity_list);
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
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
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

	    		return $json;
			}
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_entity.index', 'property' );
		}

		function category()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}";

			$entity = $this->bo->read_single($entity_id);

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uiadmin_entity.category',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'type'		=> $this->type
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_entity.category',"
	    											."sort:'{$this->sort}',"
	    											."order:'{$this->order}',"
						 	                        ."query:'{$this->query}',"
						 	                        ."entity_id:'{$this->entity_id}',"
						 	                        ."type:'{$this->type}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_entity.category',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'type'		=> $this->type
				);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uiadmin_entity.category',
								'sort'		=> $this->sort,
								'order'		=> $this->order,
								'query'		=> $this->query,
								'entity_id'	=> $entity_id,
								'type'		=> $this->type
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
				                                            'value'    => 'Entity: '.$entity['name'],
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

			$category_list = $this->bo->read_category($entity_id);
			$uicols['name'][0]	= 'id';
			$uicols['descr'][0]	= lang('category ID');
			$uicols['name'][1]	= 'name';
			$uicols['descr'][1]	= lang('Name');
			$uicols['name'][2]	= 'descr';
			$uicols['descr'][2]	= lang('Descr');
			$uicols['name'][3]	= 'prefix';
			$uicols['descr'][3]	= lang('Prefix');
			$uicols['name'][4]	= 'entity_id';
			$uicols['descr'][4]	= lang('id');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($category_list) AND is_array($category_list))
			{
				foreach($category_list as $category_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $category_entry[$uicols['name'][$k]];
							if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'entity_id')
							{
								$datatable['rows']['row'][$j]['column'][$k]['value']		= $entity_id;
							}
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
					array
					(
						'name'		=> 'entity_id',
						'source'	=> 'entity_id'
					)
				)
			);

			$parameters2 = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'cat_id',
						'source'	=> 'id'
					),
					array
					(
						'name'		=> 'entity_id',
						'source'	=> 'entity_id'
					)
				)
			);

			$parameters3 = array
			(
				'parameter' => array
				(
					array
					(
						'name'		=> 'entity_id',
						'source'	=> 'entity_id'
					)
				)
			);

			//_debug_array($parameters2);die;

			$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'attributes',
						'statustext' 	=> lang('attributes'),
						'text'			=> lang('Attributes'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uiadmin_entity.list_attribute'
								)),
					'parameters'	=> $parameters2
					);

			$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'custom functions',
						'statustext' 	=> lang('custom functions'),
						'text'			=> lang('Custom functions'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uiadmin_entity.list_custom_function'
								)),
					'parameters'	=> $parameters2
					);

			$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'edit',
						'statustext' 	=> lang('edit'),
						'text'			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
								(
									'menuaction'	=> 'property.uiadmin_entity.edit_category'
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
									'menuaction'	=> 'property.uiadmin_entity.delete'
								)),
					'parameters'	=> $parameters2
					);


			$datatable['rowactions']['action'][] = array(
					'my_name' 		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uiadmin_entity.edit_category'
						)),
					'parameters'	=> $parameters3
					);

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
					if($uicols['name'][$i]=='entity_id')
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
			$datatable['pagination']['records_returned']= count($category_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('entity');
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
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  $json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
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

	    		return $json;
			}
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_entity.category', 'property' );
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');
			$config = CreateObject('phpgwapi.config', $this->type_app[$this->type]);

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($values['save'])
			{
				if (!$values['name'])
				{
					$receipt['error'][] = array('msg'=>lang('Name not entered!'));
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if (!$receipt['error'])
				{

					$receipt = $this->bo->save($values,$action);
					if(!$id)
					{
						$id=$receipt['id'];
					}
					$config->read();

					if(!is_array($config->config_data['location_form']))
					{
						$config->config_data['location_form'] = array();
					}

					if($values['location_form'])
					{

						$config->config_data['location_form']['entity_' . $id] = 'entity_' . $id;

					}
					else
					{
						unset($config->config_data['location_form']['entity_' . $id]);
					}

					$config->save_repository();
				}
				else
				{
					$receipt['error'][] = array('msg'=> lang('Entity has NOT been saved'));
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
				$function_msg = lang('add entity');
				$action='add';
			}

			$include_list	= $this->bo->get_entity_list($values['lookup_entity']);
			$include_list_2	= $this->bo->get_entity_list_2($values['include_entity_for']);
			$include_list_3	= $this->bo->get_entity_list_3($values['start_entity_from']);

			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit',
				'id'		=> $id,
				'type'		=> $this->type
			);
//_debug_array($include_list);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_name_standardtext'			=> lang('Enter a name of the standard'),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.index', 'type' => $this->type)),
				'lang_id'					=> lang('standard ID'),
				'lang_name'					=> lang('Name'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'value_id'					=> $id,
				'value_name'					=> $values['name'],
				'lang_id_standardtext'				=> lang('Enter the standard ID'),
				'lang_descr_standardtext'			=> lang('Enter a description of the standard'),
				'lang_done_standardtext'			=> lang('Back to the list'),
				'lang_save_standardtext'			=> lang('Save the standard'),
				'type_id'					=> $values['type_id'],
				'value_descr'					=> $values['descr'],
				'lang_location_form'				=> lang('location form'),
				'value_location_form'				=> $values['location_form'],
				'lang_location_form_statustext'			=> lang('If this entity type is to be linked to a location'),
				'lang_include_in_location_form'			=> lang('include in location form'),
				'include_list'					=> $include_list,
				'lang_include_statustext'			=> lang('Which entity type is to show up in location forms'),
				'lang_include_this_entity'			=> lang('include this entity'),
				'include_list_2'				=> $include_list_2,
				'lang_include_2_statustext'			=> lang('Let this entity show up in location form'),
				'lang_start_this_entity'			=> lang('start this entity'),
				'include_list_3'				=> $include_list_3,
				'lang_include_3_statustext'			=> lang('Start this entity from'),
				'lang_select'					=> lang('select'),
				'lang_documentation'				=> lang('documentation'),
				'value_documentation'				=> $values['documentation'],
				'lang_documentation_statustext'			=> lang('If this entity type is to be linked to documents'),
			);

			$appname	= lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_category()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($values['save'])
			{
				$values['entity_id']	= $entity_id;

				if (!$values['name'])
				{
					$receipt['error'][] = array('msg'=>lang('Name not entered!'));
				}
				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('Entity not chosen'));
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				if (!$receipt['error'])
				{
					$receipt = $this->bo->save_category($values,$action);
					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg'=> lang('Category has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_category($entity_id,$id);
				$function_msg = lang('edit category');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add category');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit_category',
				'entity_id'		=> $entity_id,
				'id'			=> $id,
				'type'			=> $this->type
			);
//_debug_array($link_data);

			$entity = $this->bo->read_single($entity_id,false);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'lang_prefix_standardtext'			=> lang('Enter a standard prefix for the id'),
				'lang_name_standardtext'			=> lang('Enter a name of the standard'),

				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entity_id,'type' => $this->type)),
				'lang_id'							=> lang('Category'),
				'lang_name'							=> lang('Name'),
				'lang_descr'						=> lang('Descr'),
				'lang_prefix'						=> lang('Prefix'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,
				'value_name'						=> $values['name'],
				'value_prefix'						=> $values['prefix'],
				'lang_id_standardtext'				=> lang('Enter the standard ID'),
				'lang_descr_standardtext'			=> lang('Enter a description of the standard'),
				'lang_done_standardtext'			=> lang('Back to the list'),
				'lang_save_standardtext'			=> lang('Save the standard'),
				'type_id'							=> $values['type_id'],
				'value_descr'						=> $values['descr'],
				'lang_lookup_tenant'				=> lang('lookup tenant'),
				'value_lookup_tenant'				=> $values['lookup_tenant'],
				'lang_lookup_tenant_statustext'		=> lang('If this entity type is to look up tenants'),
				'lang_location_level'				=> lang('location level'),
				'location_level_list'				=> $this->bo->get_location_level_list($values['location_level']),
				'lang_location_level_statustext'	=> lang('select location level'),
				'lang_no_location_level'			=> lang('None'),
				'lang_tracking'						=> lang('tracking'),
				'value_tracking'					=> $values['tracking'],
				'lang_tracking_statustext'			=> lang('If this entity type is to be tracket in ticket list'),
				'lang_fileupload'					=> lang('Enable file upload'),
				'value_fileupload'					=> $values['fileupload'],
				'lang_fileupload_statustext'		=> lang('If files can be uploaded for this category'),
				'lang_loc_link'						=> lang('Link from location'),
				'value_loc_link'					=> $values['loc_link'],
				'lang_loc_link_statustext'			=> lang('Enable link from location detail'),
				'lang_start_project'				=> lang('Start project'),
				'value_start_project'				=> $values['start_project'],
				'lang_start_project_statustext'		=> lang('Enable start project from this category'),
				'lang_start_ticket'					=> lang('Start ticket'),
				'value_start_ticket'				=> $values['start_ticket'],
				'lang_start_ticket_statustext'		=> lang('Enable start ticket from this category')
			);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$entity_id		= phpgw::get_var('entity_id', 'int');
			$cat_id			= phpgw::get_var('cat_id', 'int');
			$attrib_id		= phpgw::get_var('attrib_id', 'int');
			$group_id		= phpgw::get_var('group_id', 'int');
			$acl_location		= phpgw::get_var('acl_location');
			$custom_function_id	= phpgw::get_var('custom_function_id', 'int');
			$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			if($group_id)
			{
				$function='list_attribute_group';
			}
			else if($attrib_id)
			{
				$function='list_attribute';
			}
			else if($custom_function_id)
			{
				$function='list_custom_function';
			}

			if (!$acl_location && $entity_id && $cat_id)
			{
				$acl_location = ".{$this->type}.{$entity_id}.{$cat_id}";
			}

			if(!$function)
			{
				if($cat_id)
				{
					$function='category';
				}
				else
				{
					$function='index';
				}
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.'.$function,
				'cat_id' 	=> $cat_id,
				'entity_id'	=> $entity_id,
				'attrib_id'	=> $attrib_id,
				'type'		=> $this->type
			);

			$delete_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.delete',
				'cat_id'	=> $cat_id,
				'entity_id'	=> $entity_id,
				'group_id'	=> $group_id,
				'attrib_id'	=> $attrib_id,
				'acl_location'	=> $acl_location,
				'custom_function_id' => $custom_function_id,
				'type'				=> $this->type
			);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($cat_id,$entity_id,$attrib_id,$acl_location,$custom_function_id,$group_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',$delete_data),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_standardtext'		=> lang('Delete the entry'),
				'lang_no_standardtext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('entity');
			$function_msg		= lang('delete entity type');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function list_attribute_group()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id	= $this->cat_id;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			if($resort)
			{
				$this->bo->resort_attrib_group($id,$resort);
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uiadmin_entity.list_attribute_group',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id,
					'type'		=> $this->type
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_entity.list_attribute_group',"
	    											."sort:'{$this->sort}',"
	    											."order:'{$this->order}',"
						 	                        ."query:'{$this->query}',"
						 	                        ."entity_id:'{$this->entity_id}',"
						 	                        ."cat_id:'{$this->cat_id}',"
						 	                        ."type:'{$this->type}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_entity.list_attribute',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id,
					'type'		=> $this->type
				);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uiadmin_entity.list_attribute_group',
								'sort'		=> $this->sort,
								'order'		=> $this->order,
								'query'		=> $this->query,
								'entity_id'	=> $entity_id,
								'cat_id'	=> $cat_id,
								'type'		=> $this->type
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

			$attrib_list = $this->bo->read_attrib_group($entity_id,$cat_id);
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
							$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'up';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.')';
						}

						if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'down')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'down';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)).'"';
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
								'menuaction'		=> 'property.uiadmin_entity.edit_attrib_group',
								'entity_id'			=> $entity_id,
								'cat_id'			=> $cat_id,
								'type' 				=> $this->type
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
								'menuaction'		=> 'property.uiadmin_entity.delete',
								'entity_id'			=> $entity_id,
								'cat_id'			=> $cat_id,
								'type' 				=> $this->type
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
										'menuaction'	=> 'property.uiadmin_entity.edit_attrib_group',
										'entity_id'			=> $entity_id,
										'cat_id'			=> $cat_id,
										'type' 				=> $this->type
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
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
		    					//$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  //$json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
		    				  $json_row[$column['name']] = "<a href='#' onclick='".$column['link']."'>" .$column['value']."</a>";
		    				  //$json_row[$column['name']] = '<a href="#" onclick="delete_record("/index.php?menuaction=property.uiasync.delete")">' .$column['value'].'</a>';
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

	    		return $json;
			}
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_entity.attribute_group', 'property' );

			/*if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id	= $this->cat_id;

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			$id	= phpgw::get_var('id', 'int');
			$resort	= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_entity',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_attrib_group($id,$resort);
			}
			$attrib_list = $this->bo->read_attrib_group($entity_id,$cat_id);

			if (isset($attrib_list) AND is_array($attrib_list))
			{
				foreach($attrib_list as $entry)
				{

					$content[] = array
					(
						'name'					=> $entry['name'],
						'descr'					=> $entry['descr'],
						'sorting'				=> $entry['group_sort'],
						'link_up'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)),
						'link_down'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)),
						'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_attrib_group', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'type' => $this->type)),
						'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.delete', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'group_id'=> $entry['id'], 'type' => $this->type)),
						'lang_up_text'			=> lang('shift up'),
						'lang_down_text'		=> lang('shift down'),
						'lang_edit_text'		=> lang('edit the attrib'),
						'lang_delete_text'		=> lang('delete the attrib'),
						'text_attribute'		=> lang('Attributes'),
						'text_up'				=> lang('up'),
						'text_down'				=> lang('down'),
						'text_edit'				=> lang('edit'),
						'text_delete'			=> lang('delete')
					);
				}
			}

//_debug_array($content);

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_sorting'		=> lang('sorting'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_attribute',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'=>$this->allrows,
																'type'	=> $this->type)
										)),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'attrib_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_attribute',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'=>$this->allrows,
																'type'	=> $this->type)
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_attribtext'	=> lang('add an attrib'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_attrib_group', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id,'type' => $this->type)),
				'lang_done'		=> lang('done'),
				'lang_done_attribtext'	=> lang('back to admin'),
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entity_id, 'type' => $this->type)),
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
				'menuaction'	=> 'property.uiadmin_entity.list_attribute',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'query'		=> $this->query,
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'type'		=> $this->type
			);

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'lang_category'						=> lang('category'),
				'category_name'						=> $category['name'],
				'allow_allrows'						=> true,
				'allrows'							=> $this->allrows,
				'start_record'						=> $this->start,
				'record_limit'						=> $record_limit,
				'start_record'						=> $this->start,
				'num_records'						=> count($attrib_list),
				'all_records'						=> $this->bo->total_records,
				'link_url'							=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'							=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_attribtext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_attribtext'		=> lang('Submit the search string'),
				'query'								=> $this->query,
				'lang_search'						=> lang('search'),
				'table_header_attrib_group'			=> $table_header,
				'values_attrib_group'				=> $content,
				'table_add'							=> $table_add
			);

			$appname	= lang('attribute');
			$function_msg	= lang('list entity attribute group');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_attribute_group' => $data));
			$this->save_sessiondata();*/
		}

		function list_attribute()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id	= $this->cat_id;

			$entity = $this->bo->read_single($entity_id);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

			$id	= phpgw::get_var('id');
			$resort	= phpgw::get_var('resort');

			if($resort && phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->resort_attrib($id,$resort);
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uiadmin_entity.list_attribute',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id,
					'type'		=> $this->type
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_entity.list_attribute',"
	    											."sort:'{$this->sort}',"
	    											."order:'{$this->order}',"
						 	                        ."query:'{$this->query}',"
						 	                        ."entity_id:'{$this->entity_id}',"
						 	                        ."cat_id:'{$this->cat_id}',"
						 	                        ."type:'{$this->type}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_entity.list_attribute',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id,
					'type'		=> $this->type
				);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uiadmin_entity.list_attribute',
								'sort'		=> $this->sort,
								'order'		=> $this->order,
								'query'		=> $this->query,
								'entity_id'	=> $entity_id,
								'cat_id'	=> $cat_id,
								'type'		=> $this->type
							)
						),
					'fields'	=> array(
	                                    'field' => array(
				                                        array( // mensaje
															'type'	=> 'label',
															'id'	=> 'msg_header',
															'value'	=> '',
															'style' => 'filter'
														),
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
														array( //container of  control's Form
															'type'	=> 'label',
															'id'	=> 'controlsForm_container',
															'value'	=> ''
														)
			                           				),
			                       		'hidden_value' => array(

			                       								)
										)
					 )
				);

				$dry_run = true;
			}

			$attrib_list = $this->bo->read_attrib($entity_id,$cat_id);
			$uicols['name'][0]	= 'column_name';
			$uicols['descr'][0]	= lang('Name');
			$uicols['name'][1]	= 'name';
			$uicols['descr'][1]	= lang('Descr');
			$uicols['name'][2]	= 'trans_datatype';
			$uicols['descr'][2]	= lang('Datatype');
			$uicols['name'][3]	= 'attrib_sort';
			$uicols['descr'][3]	= lang('sorting');
			$uicols['name'][4]	= 'up';
			$uicols['descr'][4]	= lang('up');
			$uicols['name'][5]	= 'down';
			$uicols['descr'][5]	= lang('down');
			$uicols['name'][6]	= 'search';
			$uicols['descr'][6]	= lang('Search');
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
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $attrib_entry[$uicols['name'][$k]];
						}

						if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'up')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'up';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'resort'=> 'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.')';
						}

						if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'down')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'down';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'resort'=> 'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $attrib_entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.')';
						}
					}
					$j++;
				}
			}

			$current_Consult = array ();
			for($i=0;$i<2;$i++)
			{
				if($i==0)
				{
					$current_Consult[] = array('entity',$entity['name']);
				}
				if($i==1)
				{
					$current_Consult[] = array('Category',$category['name']);
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
						'name'		=> 'attrib_id',
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
								'menuaction'		=> 'property.uiadmin_entity.edit_attrib',
								'entity_id'			=> $entity_id,
								'cat_id'			=> $cat_id,
								'type' 				=> $this->type
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
								'menuaction'		=> 'property.uiadmin_entity.delete',
								'entity_id'			=> $entity_id,
								'cat_id'			=> $cat_id,
								'type' 				=> $this->type
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
										'menuaction'	=> 'property.uiadmin_entity.edit_attrib',
										'entity_id'			=> $entity_id,
										'cat_id'			=> $cat_id,
										'type' 				=> $this->type
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
					if($uicols['name'][$i]=='column_name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'column_name';
					}
					if($uicols['name'][$i]=='id')
					{
						$datatable['headers']['header'][$i]['visible'] 			= false;
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
				$datatable['sorting']['order'] 			= 'column_name'; // name key Column in myColumnDef
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
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
		    					//$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
		    				{
		    				  //$json_row[$column['name']] = "<a href='".$column['link']."'>" .$column['value']."</a>";
		    				  $json_row[$column['name']] = "<a href='#' onclick='".$column['link']."'>" .$column['value']."</a>";
		    				  //$json_row[$column['name']] = '<a href="#" onclick="delete_record("/index.php?menuaction=property.uiasync.delete")">' .$column['value'].'</a>';
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

				if(isset($current_Consult) && is_array($current_Consult))
				{
					$json ['current_consult'] = $current_Consult;
				}

	    		return $json;
			}
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_entity.attribute', 'property' );
		}

		function edit_attrib_group()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$id			= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

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

				$values['entity_id']=$entity_id;
				$values['cat_id']=$cat_id;

				if (!$values['group_name'])
				{
					$receipt['error'][] = array('msg'=>lang('group name not entered!'));
				}

				if (!$values['descr'])
				{
					$receipt['error'][] = array('msg'=>lang('description not entered!'));
				}

				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('entity type not chosen!'));
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
				$values = $this->bo->read_single_attrib_group($entity_id,$cat_id,$id);
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
				'menuaction'	=> 'property.uiadmin_entity.edit_attrib_group',
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'id'		=> $id,
				'type'		=> $this->type
			);


			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'lang_category'						=> lang('category'),
				'category_name'						=> $category['name'],

				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'type' => $this->type)),
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
//_debug_array($values);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib_group' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function edit_attrib()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::entity_{$entity_id}::entity_{$entity_id}_{$cat_id}";

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

				$values['entity_id']=$entity_id;
				$values['cat_id']=$cat_id;

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

				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('entity type not chosen!'));
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


				if (!isset($receipt['error']))
				{
					$receipt = $this->bo->save_attrib($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Attribute has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_attrib($entity_id,$cat_id,$id);
				$type_name=$values['type_name'];
				$function_msg = lang('edit attribute'). ' ' . lang($type_name);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add attribute');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit_attrib',
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'id'		=> $id,
				'type'		=> $this->type
			);

			if($values['column_info']['type']=='R' || $values['column_info']['type']=='CH' || $values['column_info']['type']=='LB')
			{
				$multiple_choice= true;
			}

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$msgbox_data = (isset($receipt)?$this->bocommon->msgbox_data($receipt):'');

			$data = array
			(
				'lang_entity'						=> lang('entity'),
				'entity_name'						=> $entity['name'],
				'lang_category'						=> lang('category'),
				'category_name'						=> $category['name'],

				'lang_choice'						=> lang('Choice'),
				'lang_new_value'					=> lang('New value'),
				'lang_new_value_statustext'			=> lang('New value for multiple choice'),
				'multiple_choice'					=> (isset($multiple_choice)?$multiple_choice:''),
				'value_choice'						=> (isset($values['choice'])?$values['choice']:''),
				'lang_delete_value'					=> lang('Delete value'),
				'lang_value'						=> lang('value'),
				'lang_delete_choice_statustext'		=> lang('Delete this value from the list of multiple choice'),

				'msgbox_data'						=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'						=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'type' => $this->type)),
				'lang_id'							=> lang('Attribute ID'),
				'lang_entity_type'					=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'							=> lang('save'),
				'lang_done'							=> lang('done'),
				'value_id'							=> $id,

				'lang_column_name'					=> lang('Column name'),
				'value_column_name'					=> $values['column_name'],
				'lang_column_name_statustext'		=> lang('enter the name for the column'),

				'lang_input_text'					=> lang('input text'),
				'value_input_text'					=> $values['input_text'],
				'lang_input_name_statustext'		=> lang('enter the input text for records'),

				'lang_id_attribtext'				=> lang('Enter the attribute ID'),
				'lang_entity_statustext'			=> lang('Select a entity type'),

				'lang_statustext'					=> lang('Statustext'),
				'lang_statustext_attribtext'		=> lang('Enter a statustext for the inputfield in forms'),
				'value_statustext'					=> $values['statustext'],

				'lang_done_attribtext'				=> lang('Back to the list'),
				'lang_save_attribtext'				=> lang('Save the attribute'),

				'lang_datatype'						=> lang('Datatype'),
				'lang_datatype_statustext'			=> lang('Select a datatype'),
				'lang_no_datatype'					=> lang('No datatype'),
				'datatype_list'						=> $this->bocommon->select_datatype($values['column_info']['type']),

				'lang_group'						=> lang('group'),
				'lang_group_statustext'				=> lang('Select a group'),
				'lang_no_group'					=> lang('no group'),
				'attrib_group_list'					=> $this->bo->get_attrib_group_list($entity_id,$cat_id, $values['group_id']),

				'lang_precision'					=> lang('Precision'),
				'lang_precision_statustext'			=> lang('enter the record length'),
				'value_precision'					=> $values['column_info']['precision'],

				'lang_scale'						=> lang('scale'),
				'lang_scale_statustext'				=> lang('enter the scale if type is decimal'),
				'value_scale'						=> $values['column_info']['scale'],

				'lang_default'						=> lang('default'),
				'lang_default_statustext'			=> lang('enter the default value'),
				'value_default'						=> $values['column_info']['default'],

				'lang_nullable'						=> lang('Nullable'),
				'lang_nullable_statustext'			=> lang('Chose if this column is nullable'),
				'lang_select_nullable'				=> lang('Select nullable'),
				'nullable_list'						=> $this->bocommon->select_nullable($values['column_info']['nullable']),
				'value_lookup_form'					=> $values['lookup_form'],
				'lang_lookup_form'					=> lang('show in lookup forms'),
				'lang_lookup_form_statustext'		=> lang('check to show this attribue in lookup forms'),
				'value_list'						=> $values['list'],
				'lang_list'							=> lang('show in list'),
				'lang_list_statustext'				=> lang('check to show this attribute in entity list'),
				'value_search'						=> $values['search'],
				'lang_include_search'				=> lang('Include in search'),
				'lang_include_search_statustext'	=> lang('check to show this attribute in location list'),

				'value_history'						=> $values['history'],
				'lang_history'						=> lang('history'),
				'lang_history_statustext'			=> lang('Enable history for this attribute'),

				'value_disabled'					=> $values['disabled'],
				'lang_disabled'						=> lang('disabled'),
				'lang_disabled_statustext'			=> lang('This attribute turn up as disabled in the form'),

				'value_helpmsg'						=> $values['helpmsg'],
				'lang_helpmsg'						=> lang('help message'),
				'lang_helpmsg_statustext'			=> lang('Enables help message for this attribute'),
			);
//_debug_array($values);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_attrib' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function list_custom_function()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id		= $this->cat_id;
			$id		= phpgw::get_var('id', 'int');
			$resort		= phpgw::get_var('resort');

			if($resort)
			{
				$this->bo->resort_custom_function($id,$resort);
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']					= $this->bocommon->get_menu();
	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uiadmin_entity.list_custom_function',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id,
					'type'		=> $this->type
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uiadmin_entity.list_custom_function',"
	    											."sort:'{$this->sort}',"
	    											."order:'{$this->order}',"
						 	                        ."query:'{$this->query}',"
						 	                        ."entity_id:'{$this->entity_id}',"
						 	                        ."cat_id:'{$this->cat_id}',"
						 	                        ."type:'{$this->type}'";

				$datatable['config']['allow_allrows'] = true;

				$link_data = array
				(
					'menuaction'	=> 'property.uiadmin_entity.list_custom_function',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'query'		=> $this->query,
					'entity_id'	=> $entity_id,
					'cat_id'	=> $cat_id,
					'type'		=> $this->type
				);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uiadmin_entity.list_custom_function',
								'sort'		=> $this->sort,
								'order'		=> $this->order,
								'query'		=> $this->query,
								'entity_id'	=> $entity_id,
								'cat_id'	=> $cat_id,
								'type'		=> $this->type
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

			$custom_function_list = $this->bo->read_custom_function($entity_id,$cat_id);
			$uicols['name'][0]	= 'name';
			$uicols['descr'][0]	= lang('Name');
			$uicols['name'][1]	= 'descr';
			$uicols['descr'][1]	= lang('Descr');
			$uicols['name'][2]	= 'active';
			$uicols['descr'][2]	= lang('Active');
			$uicols['name'][3]	= 'group_sort';
			$uicols['descr'][3]	= lang('sorting');
			$uicols['name'][4]	= 'up';
			$uicols['descr'][4]	= lang('up');
			$uicols['name'][5]	= 'down';
			$uicols['descr'][5]	= lang('down');
			$uicols['name'][6]	= 'id';
			$uicols['descr'][6]	= lang('id');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($custom_function_list) AND is_array($custom_function_list))
			{
				foreach($custom_function_list as $custom_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']			= $custom_entry[$uicols['name'][$k]];
						}

						/*if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'up')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'up';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $custom_entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.')';
						}

						if($datatable['rows']['row'][$j]['column'][$k]['name'] == 'down')
						{
							$datatable['rows']['row'][$j]['column'][$k]['format'] 			= 'link';
							$datatable['rows']['row'][$j]['column'][$k]['value']		= 'down';//$uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['target']	= '_blank';
							$url = '"'.$GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_attribute_group', 'resort'=> 'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $custom_entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)).'"';
							$datatable['rows']['row'][$j]['column'][$k]['link']			= 'move_record('.$url.')';
						}*/
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
								'menuaction'		=> 'property.uiadmin_entity.edit_attrib_group',
								'entity_id'			=> $entity_id,
								'cat_id'			=> $cat_id,
								'type' 				=> $this->type
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
								'menuaction'		=> 'property.uiadmin_entity.delete',
								'entity_id'			=> $entity_id,
								'cat_id'			=> $cat_id,
								'type' 				=> $this->type
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
										'menuaction'	=> 'property.uiadmin_entity.edit_custom_function',
										'entity_id'			=> $entity_id,
										'cat_id'			=> $cat_id,
										'type' 				=> $this->type
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
					/*if($uicols['name'][$i]=='id')
					{
						$datatable['headers']['header'][$i]['visible'] 			= false;
					}

					if($uicols['name'][$i]=='name')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'name';
					}*/
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($custom_function_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname	= lang('custom function');
			$function_msg	= lang('list entity custom function');

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
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
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
		    					//$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
		    				}
		    				elseif(isset($column['format']) && $column['format']== "link")
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

	    		return $json;
			}
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'admin_entity.function_group', 'property' );

			/*if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 1, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= $this->entity_id;
			$cat_id		= $this->cat_id;
			$id		= phpgw::get_var('id', 'int');
			$resort		= phpgw::get_var('resort');

			$GLOBALS['phpgw']->xslttpl->add_file(array(
								'admin_entity',
								'nextmatchs',
								'search_field'));

			if($resort)
			{
				$this->bo->resort_custom_function($id,$resort);
			}
			$custom_function_list = $this->bo->read_custom_function($entity_id,$cat_id);

			if (isset($custom_function_list) AND is_array($custom_function_list))
			{
				foreach($custom_function_list as $entry)
				{

					$content[] = array
					(
						'file_name'				=> $entry['file_name'],
						'descr'					=> $entry['descr'],
						'sorting'				=> $entry['sorting'],
						'active'				=> $entry['active']?'X':'',
						'link_up'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_custom_function', 'resort'=>'up', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)),
						'link_down'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_custom_function', 'resort'=>'down', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'allrows'=> $this->allrows, 'type' => $this->type)),
						'link_edit'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_custom_function', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'id'=> $entry['id'], 'type' => $this->type)),
						'link_delete'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.delete', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'custom_function_id'=> $entry['id'], 'type' => $this->type)),
						'lang_up_text'				=> lang('shift up'),
						'lang_down_text'			=> lang('shift down'),
						'lang_edit_text'			=> lang('edit the custom_function'),
						'lang_delete_text'			=> lang('delete the custom_function'),
						'text_custom_function'			=> lang('custom functions'),
						'text_up'				=> lang('up'),
						'text_down'				=> lang('down'),
						'text_edit'				=> lang('edit'),
						'text_delete'				=> lang('delete')
					);
				}
			}

			$table_header[] = array
			(
				'lang_descr'		=> lang('Descr'),
				'lang_active'		=> lang('Active'),
				'lang_sorting'		=> lang('sorting'),
				'lang_search'		=> lang('search'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_name'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'column_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_custom_function',
																'entity_id'	=> $entity_id,
																'cat_id'	=> $cat_id,
																'allrows'	=> $this->allrows,
																'type'		=> $this->type)
										)),
				'sort_sorting'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'custom_function_sort',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uiadmin_entity.list_custom_function',
																'entity_id'	=>$entity_id,
																'cat_id'	=>$cat_id,
																'allrows'	=>$this->allrows,
																'type'		=> $this->type)
										)),
				'lang_name'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_custom_functiontext'	=> lang('add a custom_function'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.edit_custom_function', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id,'type' => $this->type)),
				'lang_done'			=> lang('done'),
				'lang_done_custom_functiontext'	=> lang('back to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.category', 'entity_id'=> $entity_id,'type' => $this->type)),
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
				'menuaction'	=> 'property.uiadmin_entity.list_custom_function',
				'sort'		=> $this->sort,
				'order'		=> $this->order,
				'query'		=> $this->query,
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'type'		=> $this->type
			);

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$data = array
			(
				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'lang_category'					=> lang('category'),
				'category_name'					=> $category['name'],
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'start_record'					=> $this->start,
				'num_records'					=> count($custom_function_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_custom_functiontext'		=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_custom_functiontext'		=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_custom_function'			=> $table_header,
				'values_custom_function'			=> $content,
				'table_add'					=> $table_add
			);

			$appname	= lang('custom function');
			$function_msg	= lang('list entity custom function');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_custom_function' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();*/
		}

		function edit_custom_function()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$entity_id	= phpgw::get_var('entity_id', 'int');
			$cat_id		= phpgw::get_var('cat_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$values		= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('admin_entity'));

			if ($values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$values['entity_id']=$entity_id;
				$values['cat_id']=$cat_id;


				if (!$values['entity_id'])
				{
					$receipt['error'][] = array('msg'=>lang('entity type not chosen!'));
				}

				if (!$values['custom_function_file'])
				{
					$receipt['error'][] = array('msg'=>lang('custom function file not chosen!'));
				}


				if (!$receipt['error'])
				{

					$receipt = $this->bo->save_custom_function($values,$action);

					if(!$id)
					{
						$id=$receipt['id'];
					}
				}
				else
				{
					$receipt['error'][] = array('msg' => lang('Custom function has NOT been saved'));
				}

			}

			if ($id)
			{
				$values = $this->bo->read_single_custom_function($entity_id,$cat_id,$id);
				$type_name=$values['type_name'];
				$function_msg = lang('edit custom function'). ' ' . lang($type_name);
				$action='edit';
			}
			else
			{
				$function_msg = lang('add custom function');
				$action='add';
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiadmin_entity.edit_custom_function',
				'entity_id'	=> $entity_id,
				'cat_id'	=> $cat_id,
				'id'		=> $id,
				'type'		=> $this->type
			);


//_debug_array($values);

			$entity = $this->bo->read_single($entity_id,false);
			$category = $this->bo->read_single_category($entity_id,$cat_id);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'lang_entity'					=> lang('entity'),
				'entity_name'					=> $entity['name'],
				'lang_category'					=> lang('category'),
				'category_name'					=> $category['name'],

				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiadmin_entity.list_custom_function', 'entity_id'=> $entity_id, 'cat_id'=> $cat_id, 'type' => $this->type)),
				'lang_id'					=> lang('Custom function ID'),
				'lang_entity_type'				=> lang('Entity type'),
				'lang_no_entity_type'				=> lang('No entity type'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'value_id'					=> $id,

				'lang_descr'					=> lang('descr'),
				'lang_descr_custom_functiontext'		=> lang('Enter a descr for the custom function'),
				'value_descr'					=> $values['descr'],

				'lang_done_custom_functiontext'			=> lang('Back to the list'),
				'lang_save_custom_functiontext'			=> lang('Save the custom function'),

				'lang_custom_function'				=> lang('custom function'),
				'lang_custom_function_statustext'		=> lang('Select a custom function'),
				'lang_no_custom_function'			=> lang('No custom function'),
				'custom_function_list'				=> $this->bo->select_custom_function($values['custom_function_file']),

				'value_active'					=> $values['active'],
				'lang_active'					=> lang('Active'),
				'lang_active_statustext'			=> lang('check to activate custom function'),
			);

			$appname = lang('entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit_custom_function' => $data));
		}
	}

