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

	class property_uistandard_3
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

		function property_uistandard_3()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo				= CreateObject('property.bostandard_3',true);
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
			$type	= phpgw::get_var('type');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::property::{$type}";
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
			);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$type	= phpgw::get_var('type');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'	=> 'property.uistandard_3.index',
					'type'		=> $type
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uistandard_3.index',"
	    												."type:'{$type}'";

				$link_data = array
				(
					'menuaction'	=> 'property.uistandard_3.index',
					'type'		=> $type
				);

				$datatable['config']['allow_allrows'] = false;

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uistandard_3.index',
								'type'		=> $type
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

			$standard_list = $this->bo->read($type);
			$uicols['name'][0]	= 'id';
			$uicols['descr'][0]	= lang('standard id');
			$uicols['name'][1]	= 'num';
			$uicols['descr'][1]	= lang('Name');
			$uicols['name'][2]	= 'descr';
			$uicols['descr'][2]	= lang('Description');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($standard_list) AND is_array($standard_list))
			{
				foreach($standard_list as $list_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']				= $list_entry[$uicols['name'][$k]];
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
										'menuaction'		=> 'property.uistandard_3.edit',
										'type'				=> $type,
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
										'menuaction'	=> 'property.uistandard_3.delete',
										'type'				=> $type,
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
										'menuaction'	=> 'property.uistandard_3.edit',
										'type'				=> $type,
										'menu_selection' 	=> $GLOBALS['phpgw_info']['flags']['menu_selection']
									))
						);
			}

			$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'done',
							'statustext' 	=> lang('done'),
							'text'			=> lang('done'),
							'action'		=> $GLOBALS['phpgw']->link('/admin/index.php')
						);

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

					if($uicols['name'][$i]=='num')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'num';
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

			$appname					= lang('standard');
			$function_msg	= lang('list '.$type.' attribute');

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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'uistandard_3.index', 'property' );

			$this->save_sessiondata();
			/*if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$type	= phpgw::get_var('type');

			$GLOBALS['phpgw']->xslttpl->add_file(array('standard_3','nextmatchs',
										'search_field'));

			$standard_list = $this->bo->read($type);

			while (is_array($standard_list) && list(,$standard) = each($standard_list))
			{

				$content[] = array
				(
					'id'				=> $standard['id'],
					'num'				=> $standard['num'],
					'first'				=> $standard['descr'],
					'link_edit'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_3.edit', 'id'=> $standard['id'], 'type'=> $type)),
					'link_delete'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_3.delete', 'id'=> $standard['id'], 'type'=> $type)),
					'lang_view_standardtext'	=> lang('view the standard'),
					'lang_edit_standardtext'	=> lang('edit the standard'),
					'lang_delete_standardtext'	=> lang('delete the standard'),
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
											'extra'	=> array('menuaction'	=> 'property.uistandard_3.index',
																	'type'	=>$type)
										)),
				'lang_id'		=> lang('standard id'),
				'sort_num'		=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'num',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uistandard_3.index',
																	'type'	=>$type)
										)),
				'lang_num'		=> lang('Name'),
			);

			$table_add[] = array
			(
				'lang_add'			=> lang('add'),
				'lang_add_standardtext'		=> lang('add a standard'),
				'add_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_3.edit', 'type'=> $type)),
				'lang_done'			=> lang('done'),
				'lang_done_standardtext'	=> lang('back to admin'),
				'done_action'			=> $GLOBALS['phpgw']->link('/admin/index.php')
			);


			$data = array
			(
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($standard_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_3.index', 'type'=> $type)),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'lang_searchfield_standardtext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_standardtext'		=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header'					=> $table_header,
				'values'					=> $content,
				'table_add'					=> $table_add
			);

			$appname	= lang('Standard');
			$function_msg	= lang('list '.$type.' attribute');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();*/
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$type	= phpgw::get_var('type');
			$id	= phpgw::get_var('id', 'int');
			$values	= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('standard_3'));

			if ($values['save'])
			{
				if($id)
				{
					$values['id']=$id;
					$action='edit';
				}

				$receipt = $this->bo->save($values,$action,$type);

				$id = $receipt['id'];
			}

			if ($id)
			{
				$standard = $this->bo->read_single($id,$type);
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
				'menuaction'	=> 'property.uistandard_3.edit',
				'id'		=> $id,
				'type'		=> $type
			);
//_debug_array($link_data);
			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_3.index', 'type'=> $type)),
				'lang_id'					=> lang('standard ID'),
				'lang_num'					=> lang('Name'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'					=> lang('save'),
				'lang_done'					=> lang('done'),
				'value_id'					=> $id,
				'value_num'					=> $standard['num'],
				'value_generaladdress'				=> $standard['general_address'],
				'lang_id_standardtext'				=> lang('Enter the standard ID'),
				'lang_descr_standardtext'			=> lang('Enter a description the standard'),
				'lang_done_standardtext'			=> lang('Back to the list'),
				'lang_save_standardtext'			=> lang('Save the standard'),
				'type_id'					=> $standard['type_id'],
				'location_code'					=> $standard['location_code'],
				'value_descr'					=> $standard['descr']
			);

			$appname	= lang($type);

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 8, 'acl_location'=> $this->acl_location));
			}

			$type		= phpgw::get_var('type');
			$id		= phpgw::get_var('id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uistandard_3.index',
				'type' => $type
			);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id,$type);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uistandard_3.delete', 'id'=> $id, 'type'=> $type)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_standardtext'		=> lang('Delete the entry'),
				'lang_no_standardtext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname	= lang($type);
			$function_msg	= lang('delete '.$type.' standard');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

	}

