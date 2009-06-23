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
	 * uiactor class
	 *
	 * uiactor is the ui-class for three set of actors, separarated by their roles:
	 * - Tenant
	 * - Vendor
	 * - Owner
	 * @package property
	 */

	class property_uiactor
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
			'index'  	=> true,
			'view'   	=> true,
			'edit'   	=> true,
			'delete' 	=> true,
			'columns'	=> true
		);

		function property_uiactor()
		{
			$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo				= CreateObject('property.boactor',true);
			$this->bocommon 		= & $this->bo->bocommon;

			$this->role				= $this->bo->role;

			$this->cats				= CreateObject('phpgwapi.categories');
			$this->cats->app_name	= 'fm_' . $this->role;

			$this->acl				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.' . $this->role;

			$this->acl_read 		= $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'property');
			$this->acl_add			= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'property');
			$this->acl_edit			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'property');
			$this->acl_delete		= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'property');
			$this->acl_manage		= $this->acl->check($this->acl_location, 16, 'property');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->filter			= $this->bo->filter;
			$this->cat_id			= $this->bo->cat_id;
			$this->allrows			= $this->bo->allrows;
			$this->member_id		= $this->bo->member_id;

			$valid_role = array(
				'tenant'=>true,
				'owner'	=>true,
				'vendor'=>true
				);
			if(!$valid_role[$this->role])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.index'));
			}

			if (phpgw::get_var('admin', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "admin::property::{$this->role}";
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::invoice::{$this->role}";
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
				'allrows'	=> $this->allrows,
				'member_id'	=> $this->member_id
			);
			$this->bo->save_sessiondata($data);
		}

		function columns()
		{

			//cramirez: necesary for windows.open . Avoid error JS
   			phpgwapi_yui::load_widget('tabview');

			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$values	= phpgw::get_var('values');

			if ($values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id=$this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('property','actor_columns_' .$this->role,$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
			(
				'menuaction'	=> 'property.uiactor.columns',
				'role'		=> $this->role
			);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data' 	=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list'	=> $this->bo->column_list($values['columns'],$allrows=true),
				'function_msg'	=> $function_msg,
				'form_action'	=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_columns'	=> lang('columns'),
				'lang_none'		=> lang('None'),
				'lang_save'		=> lang('save'),
				'select_name'	=> 'period'
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('columns' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}



		function index()
		{
			$menu_sub = array(
				'tenant'=>'invoice',
				'owner'	=>'admin',
				'vendor'=>'invoice'
				);

			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$dry_run=false;
			$lookup = ''; //Fix this

			$datatable = array();
			$values_combo_box = array();

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role);
			$GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role,'');


			if( phpgw::get_var('phpgw_return_as') != 'json' )
			 {

				if(!$lookup)
				{
					$datatable['menu']	= $this->bocommon->get_menu();
				}

	    		$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    				(
	    							'menuaction'=> 'property.uiactor.index',
									'lookup'    => $lookup,
									'cat_id'	=>$this->cat_id,
									'query'		=>$this->query,
									'role'		=> $this->role,
									'member_id'	=> $this->member_id

	    				));
	    		$datatable['config']['allow_allrows'] = true;

				$datatable['config']['base_java_url'] = "menuaction:'property.uiactor.index',"

	    											."lookup:'{$lookup}',"
	    											."query:'{$this->query}',"
													."cat_id:'{$this->cat_id}',"
						 	                        ."role:'{$this->role}',"
						 	                        ."member_id:'{$this->member_id}'";
                //die(_debug_array($datatable));

				$values_combo_box[0]  = $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true));
				$default_value = array ('cat_id'=>'','name'=>lang('no member'));
				array_unshift ($values_combo_box[0]['cat_list'],$default_value);

				$values_combo_box[1] = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->cat_id,'type' => $this->role,'order'=>'descr'));
				$default_value = array ('id'=>'','name'=> lang('no category'));
				array_unshift ($values_combo_box[1],$default_value);

				$datatable['actions']['form'] = array(
					array(
						'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array(
									'menuaction' 		=> 'property.uiactor.index',
									'lookup'        		=> $lookup,
									'cat_id'	=> $this->cat_id,
									'query'		=> $this->query,
									'role'		=> $this->role,
									'member_id'	=> $this->member_id
								)
							),
						'fields'	=> array(
                                    	'field' => array(
			                                        array(
			                                            'id' => 'btn_member_id',
			                                            'name' => 'member_id',
			                                            'value'	=> lang('Member'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 1
			                                        ),
			                                        array(
			                                            'id' => 'btn_cat_id',
			                                            'name' => 'cat_id',
			                                            'value'	=> lang('Category'),
			                                            'type' => 'button',
			                                            'style' => 'filter',
			                                            'tab_index' => 2
			                                        ),
													array(
										                'type'=> 'link',
										                'id'  => 'btn_columns',
										                'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
										                       array(
										                           'menuaction' => 'property.uiactor.columns',
										                           'role'		=> $this->role
										                           ))."','','width=350,height=370')",
										                 'value' => lang('columns'),
										                 'tab_index' => 6
										            ),
													array(
						                                'type'	=> 'button',
						                            	'id'	=> 'btn_new',
						                                'value'	=> lang('add'),
						                                'tab_index' => 5
						                            ),
			                                        array( //boton     SEARCH
			                                            'id' => 'btn_search',
			                                            'name' => 'search',
			                                            'value'    => lang('search'),
			                                            'type' => 'button',
			                                            'tab_index' => 4
			                                        ),
			   										array( // TEXT IMPUT
			                                            'name'     => 'query',
			                                            'id'     => 'txt_query',
			                                            'value'    => '',//$query,
			                                            'type' => 'text',
			                                            'onkeypress' => 'return pulsar(event)',
			                                            'size'    => 28,
			                                            'tab_index' => 3
			                                        )
		                           				),
		                       		'hidden_value' => array(
					                                        array( //div values  combo_box_0
							                                            'id' => 'values_combo_box_0',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[0]['cat_list'], 'cat_id') //i.e.  id,value/id,vale/
							                                      ),
							                                array( //div values  combo_box_1
							                                            'id' => 'values_combo_box_1',
							                                            'value'	=> $this->bocommon->select2String($values_combo_box[1])
							                                      )
		                       								)
												)
										  )
				);

				if($this->role == 'tenant')
				{
					unset($datatable['actions']['form'][0]['fields']['field'][0]);
				}

				if(!$this->acl_add)
				{
					unset($datatable['actions']['form'][0]['fields']['field'][3]);
				}
				$dry_run=true;
			}

			$actor_list = array();
			$actor_list = $this->bo->read($dry_run);

			//echo $dry_run; count($actor_list); die(_debug_array($actor_list));

			$uicols	= $this->bo->uicols;

			$j=0;
			if (isset($actor_list) && is_array($actor_list))
			{
				foreach($actor_list as $actor)
				{
					for ($i=0;$i<count($uicols['name']);$i++)
					{
						if($uicols['input_type'][$i]!='hidden')
						{
							if(isset($actor['query_location'][$uicols['name'][$i]]))
							{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['statustext']		= lang('search');
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $actor[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['format'] 			= 'link';
								$datatable['rows']['row'][$j]['column'][$i]['java_link']		= true;
								$datatable['rows']['row'][$j]['column'][$i]['link']				= $actor['query_location'][$uicols['name'][$i]];
							}
							else
							{
								$datatable['rows']['row'][$j]['column'][$i]['value'] 			= $actor[$uicols['name'][$i]];
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['lookup'] 			= $lookup;
								$datatable['rows']['row'][$j]['column'][$i]['align'] 			= (isset($uicols['align'][$i])?$uicols['align'][$i]:'center');

								if(isset($uicols['datatype']) && isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]=='link' && $actor[$uicols['name'][$i]])
								{
									$datatable['rows']['row'][$j]['column'][$i]['value']		= lang('link');
									$datatable['rows']['row'][$j]['column'][$i]['link']		= $actor[$uicols['name'][$i]];
									$datatable['rows']['row'][$j]['column'][$i]['target']	= '_blank';
								}
							}
						}
						else
						{
								$datatable['rows']['row'][$j]['column'][$i]['name'] 			= $uicols['name'][$i];
								$datatable['rows']['row'][$j]['column'][$i]['value']			= $actor[$uicols['name'][$i]];
						}

						$datatable['rows']['row'][$j]['hidden'][$i]['value'] 			= $actor[$uicols['name'][$i]];
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
							'name'		=> 'actor_id',
							'source'	=> 'id'
						)
					)
				);

				if($this->acl_read)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'view',
						'text' 			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiactor.view',
											'role'	=> $this->role
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'view',
						'text' 			=> lang('open view in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiactor.view',
											'role'			=> $this->role,
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_edit)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'edit',
						'text' 			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiactor.edit',
											'role'	=> $this->role
										)),
						'parameters'	=> $parameters
					);
					$datatable['rowactions']['action'][] = array(
						'my_name' 		=> 'edit',
						'text' 			=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiactor.edit',
											'role'			=> $this->role,
											'target'		=> '_blank'
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_delete)
				{
					$datatable['rowactions']['action'][] = array(
						'my_name' 			=> 'delete',
						'text' 			=> lang('delete'),
						'confirm_msg'	=> lang('do you really want to delete this entry'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uiactor.delete',
											'role'	=> $this->role
										)),
						'parameters'	=> $parameters
					);
				}
				if($this->acl_add)
				{
					$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'add',
							'text' 			=> lang('add'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
											(
												'menuaction'	=> 'property.uiactor.edit',
												'role'	=> $this->role
											))
					);
				}
				unset($parameters);
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
					$datatable['headers']['header'][$i]['format'] 			= $this->bocommon->translate_datatype_format($uicols['datatype'][$i]);
					$datatable['headers']['header'][$i]['sortable']			= false;

					if(isset($uicols['datatype'][$i]) && $uicols['datatype'][$i]!='T' && $uicols['datatype'][$i]!='CH')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
				}
				else
				{
					$datatable['headers']['header'][$i]['name'] 			= 'id2';
					$datatable['headers']['header'][$i]['text'] 			= $uicols['descr'][$i];
					$datatable['headers']['header'][$i]['visible'] 			= false;
					$datatable['headers']['header'][$i]['sortable']			= false;
					$datatable['headers']['header'][$i]['format'] 			= 'hidden';
				}
			}

			// path for property.js
			$datatable['property_js'] =  $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned'] = count($actor_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			//$datatable['sorting']['order'] 	= phpgw::get_var('order', 'string'); // Column
			//$datatable['sorting']['sort'] 	= phpgw::get_var('sort', 'string'); // ASC / DESC

			if($this->role == 'tenant')
			{
				if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
				{
					$datatable['sorting']['order'] 			= 'first_name'; // name key Column in myColumnDef
					$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
				}
				else
				{
					$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
					$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
				}
			}
			else
			{
				if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
				{
					$datatable['sorting']['order'] 			= 'org_name'; // name key Column in myColumnDef
					$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
				}
				else
				{
					$datatable['sorting']['order']			= phpgw::get_var('order', 'string'); // name of column of Database
					$datatable['sorting']['sort'] 			= phpgw::get_var('sort', 'string'); // ASC / DESC
				}
			}

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
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('list ' . $this->role);

	  		// Prepare YUI Library
  			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'actor.index', 'property' );

			//$this->save_sessiondata();
		}

		function edit()
		{

			if(!$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$actor_id	= phpgw::get_var('actor_id', 'int');
			$values		= phpgw::get_var('values');
			$values_attribute  = phpgw::get_var('values_attribute');

			$insert_record_actor = $GLOBALS['phpgw']->session->appsession('insert_record_values.' . $this->role,'property');

//_debug_array($insert_record_actor);
//_debug_array($values_attribute);
			for ($j=0;$j<count($insert_record_actor);$j++)
			{
				$insert_record['extra'][$insert_record_actor[$j]]	= $insert_record_actor[$j];
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('actor','attributes_form'));
			$receipt = array();

			if (is_array($values))
			{
				if(isset($insert_record) && is_array($insert_record))
				{
					foreach ($insert_record['extra'] as $key => $column)
					{
						if($_POST[$key])
						{
							$values['extra'][$column]	= phpgw::get_var($key, 'string', 'POST');
						}
					}
				}

//_debug_array($values);

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{

					if(!isset($values['cat_id']) || !$values['cat_id'])
					{
						$receipt['error'][]=array('msg'=>lang('Please select a category !'));
					}

					if(!$values['last_name'])
					{
//						$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
					}

					if(isset($values_attribute) && is_array($values_attribute))
					{
						foreach ($values_attribute as $attribute )
						{
							if($attribute['nullable'] != 1 && !$attribute['value'])
							{
								$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
							}
						}
					}

					if(!isset($receipt['error']) || !$receipt['error'])
					{
						$values['actor_id']	= $actor_id;
						$receipt = $this->bo->save($values,$values_attribute);
						$actor_id = $receipt['actor_id'];
						$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data','actor_receipt_' . $this->role,$receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> $this->role));
						}
					}
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> $this->role));
				}
			}


			$values = $this->bo->read_single(array('actor_id'=>$actor_id));

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->bo->preserve_attribute_values($values,$values_attribute);
			}

			if ($actor_id)
			{
				$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);
				$this->member_id = ($values['member_of']?$values['member_of']:$this->member_id);
			}

			$link_data = array
			(
				'menuaction'	=> 'property.uiactor.edit',
				'actor_id'	=> $actor_id,
				'role'		=> $this->role
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $this->member_id,'globals' => true, 'link_data' =>array()));

			$tabs = array();

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
						(
							'menuaction'	=> 'property.uiactor.attrib_history',
							'attrib_id'	=> $attribute['id'],
							'actor_id'	=> $actor_id,
							'role'		=> $this->role,
							'edit'		=> true
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				phpgwapi_yui::tabview_setup('actor_edit_tabview');
				$tabs['general']	= array('label' => lang('general'), 'link' => '#general');

				$location = $this->acl_location;
				$attributes_groups = $this->bo->get_attribute_groups($location, $values['attributes']);

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']))
					{
						$tabs[str_replace(' ', '_', $group['name'])] = array('label' => $group['name'], 'link' => '#' . str_replace(' ', '_', $group['name']));
						$group['link'] = str_replace(' ', '_', $group['name']);
						$attributes[] = $group;
					}
				}
				unset($attributes_groups);
				unset($values['attributes']);
			}

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_actor_id'					=> lang($this->role) . ' ID',
				'value_actor_id'				=> $actor_id,
				'lang_category'					=> lang('category'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
	//			'value_cat'						=> $values['cat'],
				'lang_id_statustext'			=> lang('Choose an ID'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the actor untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the actor and return back to the list'),
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'			=> lang('Select the category the actor belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[cat_id]',
				'cat_list'						=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $this->cat_id,'type' => $this->role,'order'=>'descr')),

				'lang_member_of'				=> lang('member of'),
				'member_of_name'				=> 'member_id',
				'member_of_list'				=> $member_of_data['cat_list'],

				'lang_attributes'				=> lang('Attributes'),
				'attributes_group'				=> $attributes,
				'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'dateformat'					=> $dateformat,
				'lang_edit'						=> lang('edit'),
				'lang_add'						=> lang('add'),
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs'							=> phpgwapi_yui::tabview_generate($tabs, 'general')
			);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . ($actor_id?lang('edit') . ' ' . lang($this->role):lang('add') . ' ' . lang($this->role));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		function delete()
		{

			$actor_id	= phpgw::get_var('actor_id', 'int');

			//cramirez add JsonCod for Delete
			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($actor_id);
				return "actor_id ".$actor_id." ".lang("has been deleted");
			}

			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction'	=> 'property.uiactor.index',
				'role'		=> $this->role
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($actor_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'		=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.delete', 'actor_id'=> $actor_id, 'role'=> $this->role)),
				'lang_confirm_msg'	=> lang('do you really want to delete this entry'),
				'lang_yes'		=> lang('yes'),
				'lang_yes_statustext'	=> lang('Delete the entry'),
				'lang_no_statustext'	=> lang('Back to the list'),
				'lang_no'		=> lang('no')
			);

			$appname	= lang('actor');
			$function_msg	= lang('delete') . ' ' . lang($this->role);

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

			$actor_id	= phpgw::get_var('actor_id', 'int');
			$action		= phpgw::get_var('action');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('actor') . ': ' . lang('view') . ' ' . lang($this->role);

			$GLOBALS['phpgw']->xslttpl->add_file(array('actor','attributes_view'));

			$actor = $this->bo->read_single(array('actor_id'=>$actor_id, 'view'=>true));

			$attributes_values=$actor['attributes'];

			$dateformat = strtolower($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$sep = '/';
			$dlarr[strpos($dateformat,'y')] = 'yyyy';
			$dlarr[strpos($dateformat,'m')] = 'MM';
			$dlarr[strpos($dateformat,'d')] = 'DD';
			ksort($dlarr);

			$dateformat= (implode($sep,$dlarr));

			$member_of_data	= $this->cats->formatted_xslt_list(array('selected' => $actor['member_of'],'globals' => true, 'link_data' =>array()));

			$data = array
			(
				'lang_actor_id'				=> lang($this->role) . ' ID',
				'value_actor_id'			=> $actor_id,
				'done_action'				=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uiactor.index', 'role'=> $this->role)),
				'lang_category'				=> lang('category'),
				'lang_time_created'			=> lang('time created'),
				'lang_done'				=> lang('done'),
				'cat_list'				=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $actor['cat_id'],'type' => $this->role,'order'=>'descr')),

				'lang_member_of'			=> lang('member of'),
				'member_of_list'			=> $member_of_data['cat_list'],

				'value_date'				=> $GLOBALS['phpgw']->common->show_date($actor['entry_date']),
				'lang_dateformat' 			=> lang(strtolower($dateformat)),
				'lang_attributes'			=> lang('Attributes'),
				'attributes_view'			=> $attributes_values,
				'dateformat'				=> $dateformat,
				'textareacols'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows'				=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6
			);

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('view' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}

