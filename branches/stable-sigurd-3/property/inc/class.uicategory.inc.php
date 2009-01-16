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

	class property_uicategory
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
			'index'  => true,
			'view'   => true,
			'edit'   => true,
			'delete' => true
		);

		function property_uicategory()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = phpgw::get_var('menu_selection');
		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('property.bocategory',true);
			$this->bocommon				= CreateObject('property.bocommon');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.admin';
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, 'property');

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort				= $this->bo->sort;
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
				'allrows'	=> $this->allrows
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$type		= phpgw::get_var('type');
			$type_id	= phpgw::get_var('type_id', 'int');

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = "category.index.{$type}";

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uicategory.index',
					'type'		=> $type,
					'type_id'		=> $type_id
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uicategory.index',"
	    												."type:'{$type}',"
	    												."type_id:'{$type_id}'";

				$link_data = array
				(
					'menuaction'	=> 'property.uicategory.index',
					'type'		=> $type,
					'type_id'		=> $type_id
				);

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uicategory.index',
								'type'		=> $type,
					'type_id'		=> $type_id
							)
						),
					'fields'	=> array(
	                                    'field' => array
	                                    			(
														array(
							                                'type'	=> 'button',
							                            	'id'	=> 'btn_done',
							                                'value'	=> lang('done'),
							                                'tab_index' => 9
							                            ),
														array(
							                                'type'	=> 'button',
							                            	'id'	=> 'btn_new',
							                                'value'	=> lang('add'),
							                                'tab_index' => 8
							                            ),
				                                        array( //boton     SEARCH
				                                            'id' => 'btn_search',
				                                            'name' => 'search',
				                                            'value'    => lang('search'),
				                                            'type' => 'button',
				                                            'tab_index' => 7
				                                        ),
				   										array( // TEXT INPUT
				                                            'name'     => 'query',
				                                            'id'     => 'txt_query',
				                                            'value'    => '',//$query,
				                                            'type' => 'text',
				                                            'onkeypress' => 'return pulsar(event)',
				                                            'size'    => 28,
				                                            'tab_index' => 6
				                                        )
			                           				)
										)
					 )
				);
				$dry_run = true;
			}

			$category_list = $this->bo->read($type,$type_id);
			$uicols['name'][0]	= 'id';
			$uicols['descr'][0]	= lang('category ID');
			$uicols['name'][1]	= 'descr';
			$uicols['descr'][1]	= lang('Descr');
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
							$datatable['rows']['row'][$j]['column'][$k]['value']				= $category_entry[$uicols['name'][$k]];
						}
					}
					$j++;
				}
			}

			//_debug_array($datatable);die;
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

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'edit',
							'statustext' 	=> lang('edit the actor'),
							'text'			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'		=> 'property.uicategory.edit',
										'type'				=> $type,
										'type_id'				=> $type_id,
										'menu_selection' 	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
									)),
						'parameters'	=> $parameters
						);
			}

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'delete',
							'statustext' 	=> lang('delete the actor'),
							'text'			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uicategory.delete',
										'type'				=> $type,
										'type_id'				=> $type_id,
										'menu_selection' 	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
									)),
						'parameters'	=> $parameters
						);
			}
			unset($parameters);

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'add',
							'statustext' 	=> lang('add'),
							'text'			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uicategory.edit',
										'type'				=> $type,
										'type_id'				=> $type_id,
										'menu_selection' 	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
									))
						);
			}

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
						$datatable['headers']['header'][$i]['sortable']			= true;
						$datatable['headers']['header'][$i]['sort_field']   	= $uicols['name'][$i];
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

			$appname					= lang('document');
			$function_msg				= lang('list document category');

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

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ' : ' . $function_msg;

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'category.index', 'property' );

			$this->save_sessiondata();
			/*if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$type		= phpgw::get_var('type');
			$type_id	= phpgw::get_var('type_id', 'int');

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = "category.index.{$type}";

			$GLOBALS['phpgw']->xslttpl->add_file(array('category','nextmatchs',
										'search_field'));

			$category_list = $this->bo->read($type,$type_id);

			while (is_array($category_list) && list(,$category) = each($category_list))
			{
				$content[] = array
				(
					'id'				=> $category['id'],
					'first'				=> $category['descr'],
					'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.edit', 'id'=> $category['id'], 'type'=> $type, 'type_id'=> $type_id, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
					'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.delete', 'id'=> $category['id'], 'type'=> $type, 'type_id'=> $type_id, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
					'lang_view_categorytext'	=> lang('view the category'),
					'lang_edit_categorytext'	=> lang('edit the category'),
					'lang_delete_categorytext'	=> lang('delete the category'),
					'text_view'			=> lang('view'),
					'text_edit'			=> lang('edit'),
					'text_delete'			=> lang('delete')
				);
			}

//_debug_array($content);

			$table_header[] = array
			(

				'lang_descr'		=> lang('Descr'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				'sort_id'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'id',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uicategory.index',
																	'type'	=> $type,
																	'type_id' => $type_id,
																	'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
															)
										)),
				'lang_id'		=> lang('category id'),
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_categorytext'	=> lang('add a category'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.edit', 'type'=> $type,  'type_id'=> $type_id, 'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_done'		=> lang('done'),
				'lang_done_categorytext'=> lang('back to admin'),
				'done_action'		=> $GLOBALS['phpgw']->link('/admin/index.php')
			);

			if(!$this->allrows)
			{
				$record_limit	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$record_limit	= $this->bo->total_records;
			}


			$data = array
			(
				'allow_allrows'					=> true,
				'allrows'					=> $this->allrows,
				'start_record'					=> $this->start,
				'record_limit'					=> $record_limit,
				'num_records'					=> count($category_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.index', 'type'=> $type, 'type_id'=> $type_id,'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_categorytext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_categorytext'		=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'table_add'					=> $table_add
			);

			$appname	= lang($type). ' ' . $type_id;
;
			$function_msg	= lang('list %1 category',$type);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
			$this->save_sessiondata();*/
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$type	= phpgw::get_var('type');
			$type_id	= phpgw::get_var('type_id', 'int');
			$id	= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'category.edit.' . $type;

			$GLOBALS['phpgw']->xslttpl->add_file(array('category'));

			if ($values['save'])
			{
				if(!$id && !ctype_digit($values['id']))
				{
					$receipt['error'][]=array('msg'=>lang('Please enter an integer !'));
					unset($values['id']);
				}

				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}
				else
				{
					$id =	$values['id'];
				}

				if(!$receipt['error'])
				{
					$receipt = $this->bo->save($values,$action,$type,$type_id);
				}
			}

			if ($id)
			{
				$category = $this->bo->read_single($id,$type,$type_id);
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
				'menuaction'	=> 'property.uicategory.edit',
				'id'		=> $id,
				'type'		=> $type,
				'type_id'	=> $type_id,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);
//_debug_array($link_data);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.index', 'type'=> $type, 'type_id'=> $type_id,'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_id'					=> lang('category ID'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'value_id'					=> $id,
				'lang_id_categorytext'				=> lang('Enter the category ID'),
				'lang_descr_categorytext'			=> lang('Enter a description the category'),
				'lang_done_categorytext'			=> lang('Back to the list'),
				'lang_save_categorytext'			=> lang('Save the category'),
				'type_id'					=> $category['type_id'],
				'value_descr'					=> $category['descr']
			);

			$appname	= lang($type). ' ' . $type_id;

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$type		= phpgw::get_var('type');
			$type_id	= phpgw::get_var('type_id', 'int');
			$id		= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'property.uicategory.index',
				'type'		=> $type,
				'type_id'	=> $type_id,
				'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection']
			);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id,$type,$type_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.delete', 'id'=> $id, 'type'=> $type, 'type_id'=> $type_id,'menu_selection' => $GLOBALS['phpgw_info']['flags']['menu_selection'])),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_categorytext'		=> lang('Delete the entry'),
				'lang_no_categorytext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang($type). ' ' . $type_id;
			$function_msg	= lang('delete '.$type.' category');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}
	}

