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

		//	$this->currentapp			= $GLOBALS['phpgw_info']['flags']['currentapp'];
			$this->nextmatchs			= CreateObject('phpgwapi.nextmatchs');
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bodocument',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->bolocation			= CreateObject('property.bolocation');
			$this->config				= CreateObject('phpgwapi.config','property');
			$this->boadmin_entity		= CreateObject('property.boadmin_entity');

			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= '.document';
			$this->acl_read 			= $this->acl->check('.document', PHPGW_ACL_READ, 'property');
			$this->acl_add 				= $this->acl->check('.document', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 			= $this->acl->check('.document', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 			= $this->acl->check('.document', PHPGW_ACL_DELETE, 'property');

			$this->rootdir 				= $this->bo->rootdir;
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

			    $values_combo_box[0] = $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->doc_type,'type' =>'document','order'=>'descr'));
				$default_value = array ('id'=>'','name'=> lang('no document type'));
				array_unshift ($values_combo_box[0],$default_value);

				$values_combo_box[1]  = $this->bocommon->get_user_list_right2('filter',4,$this->filter,$this->acl_location,array('all'),$default=$this->account);
				$default_value = array ('id'=>'','name'=>lang('no status'));
				array_unshift ($values_combo_box[1],$default_value);

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
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
					'fields'	=> array(
	                                    'field' => array(
				                                        array( //boton 	CATEGORY
				                                            'id' => 'btn_type_id',
				                                            'name' => 'type_id',
				                                            'value'	=> lang('Type'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 1
				                                        ),
				                                        array( //boton 	STATUS
				                                            'id' => 'btn_user_id',
				                                            'name' => 'user_id',
				                                            'value'	=> lang('User'),
				                                            'type' => 'button',
				                                            'style' => 'filter',
				                                            'tab_index' => 2
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
			                           				),
			                       		'hidden_value' => array(
						                                        array( //div values  combo_box_0
								                                            'id' => 'values_combo_box_0',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[0]) //i.e.  id,value/id,vale/
								                                      ),
								                                array( //div values  combo_box_1
								                                            'id' => 'values_combo_box_1',
								                                            'value'	=> $this->bocommon->select2String($values_combo_box[1])
								                                      )
			                       								)
										)
					 )
				);

				$dry_run = true;
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
					$datatable['rowactions']['action'][] = array(
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
				$this->sort				= $this->bo->sort;
				$this->order				= $this->bo->order;
				$this->filter				= $this->bo->filter;
				$this->entity_id			= $this->bo->entity_id;
				$this->cat_id				= $this->bo->cat_id;
				$this->status_id			= $this->bo->status_id;
			}


			/*if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$preserve = phpgw::get_var('preserve', 'bool');

			if($preserve)
			{
				$this->bo->read_sessiondata();

				$this->start				= $this->bo->start;
				$this->query				= $this->bo->query;
				$this->sort				= $this->bo->sort;
				$this->order				= $this->bo->order;
				$this->filter				= $this->bo->filter;
				$this->entity_id			= $this->bo->entity_id;
				$this->cat_id				= $this->bo->cat_id;
				$this->status_id			= $this->bo->status_id;
			}
//_debug_array($this->cat_id);

			$GLOBALS['phpgw']->xslttpl->add_file(array('document',
										'receipt',
										'nextmatchs',
										'search_field'));

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','document_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','document_receipt','');

			$location_code = phpgw::get_var('location_code');
			if($this->query_location)
			{
				$location_code = $this->query_location;
			}

			$p_num = phpgw::get_var('p_num');

			$location=$this->bo->read_location_data($location_code);

			if($this->cat_id)
			{
				$entity_data[$this->entity_id]['p_num']=$p_num;
				$entity_data[$this->entity_id]['p_entity_id']=$this->entity_id;
				$entity_data[$this->entity_id]['p_cat_id']=$this->cat_id;
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$lookup_entity	= $this->bocommon->get_lookup_entity('document');
				$appname_sub	= $entity['name'];
			}
			else
			{
				$appname_sub	= lang('location');
			}

			if($category['name'])
			{
				$entity_data[$this->entity_id]['p_cat_name']=$category['name'];
			}

			$this->config->read();
			$files_url = $this->config->config_data['files_url'];

			$document_list = $this->bo->read_at_location($location_code);

//_debug_array($document_list);

			if($this->cat_id)
			{
				$directory = $this->fakebase. '/' . 'document' . '/' . $location['loc1'] . '/' . $entity['name'] . '/' . $category['name'] . '/' . $p_num;
			}
			else
			{
				$directory = $this->fakebase. '/' . 'document' . '/' . $location['loc1'];
			}

			while (is_array($document_list) && list(,$document) = each($document_list))
			{
				if($document['link'])
				{
					$link_view_file=$document['link'];
					$document['document_name']='link';
					unset($link_to_files);
				}
				else
				{
					if(!$link_to_files)
					{
						$link_view_file = $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.view_file', 'id'=> $document['document_id'], 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $p_num));
						$link_to_files = $files_url;
					}
				}

				$content[] = array
				(
					'directory'				=> $directory,
					'document_id'				=> $document['document_id'],
					'document_name'				=> $document['document_name'],
					'title'					=> $document['title'],
					'user'					=> $document['user'],
					'doc_type'				=> $document['doc_type'],
					'link_view_file'			=> $link_view_file,
					'link_to_files'				=> $link_to_files,
					'link_view'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.view', 'document_id'=> $document['document_id'], 'from'=> 'list_doc')),
					'link_edit'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.edit', 'document_id'=> $document['document_id'], 'from'=> 'list_doc')),
					'link_delete'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.delete', 'document_id'=> $document['document_id'], 'location_code'=> $location_code, 'p_num'=> $p_num)),
					'lang_view_file_statustext'		=> lang('view the document'),
					'lang_view_statustext'			=> lang('view information about the document'),
					'lang_edit_statustext'			=> lang('edit information about the document'),
					'lang_delete_statustext'		=> lang('delete this document'),
					'text_view'				=> lang('view'),
					'text_edit'				=> lang('edit'),
					'text_delete'				=> lang('delete')
				);
			}


			$table_header[] = array
			(
				'sort_document_name'	=> $this->nextmatchs->show_sort_order(array
										(
											'sort'	=> $this->sort,
											'var'	=> 'document_name',
											'order'	=> $this->order,
											'extra'	=> array('menuaction'	=> 'property.uidocument.list_doc',
														'entity_id'	=> $this->entity_id,
														'cat_id'	=> $this->cat_id,
														'doc_type'	=> $this->doc_type,
														'p_num'		=> $p_num,
														'location_code'	=> $location_code,
														'filter'	=> $this->filter,
														'query'		=> $this->query,
														'query_location' => $this->query_location
														)
										)),
				'lang_document_name'	=> lang('Document name'),
				'lang_doc_type'		=> lang('Doc type'),
				'lang_user'		=> lang('user'),
				'lang_title'		=> lang('Title'),
				'lang_view'		=> lang('view'),
				'lang_edit'		=> lang('edit'),
				'lang_delete'		=> lang('delete'),
				);


			$link_data_add = array
			(
				'menuaction'		=> 'property.uidocument.edit',
				'location_code'		=> $location_code,
				'p_entity_id'		=> $this->entity_id,
				'entity_id'		=> $this->entity_id,
				'p_cat_id'		=> $this->cat_id,
				'cat_id'		=> $this->cat_id,
				'p_num'			=> $p_num,
				'from'			=> 'list_doc',
				'bypass'		=> true
			);

			$table_add[] = array
			(
				'lang_add'		=> lang('add'),
				'lang_add_statustext'	=> lang('add a document'),
				'add_action'		=> $GLOBALS['phpgw']->link('/index.php',$link_data_add)
			);

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
				'query_location'=> $this->query_location
			);


			$location_data=$this->bolocation->initiate_ui_location(array(
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

//_debug_array($location_data);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'menu'							=> $this->bocommon->get_menu(),
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data'					=> $location_data,
				'link_history'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.list_doc', 'cat_id'=> $this->cat_id)),
				'lang_history_statustext'			=> lang('search for history at this location'),
				'lang_select'					=> lang('select'),
				'lookup_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uiworkorder.edit')),
				'lookup'					=> $lookup,
				'allow_allrows'					=> false,
				'start_record'					=> $this->start,
				'record_limit'					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'					=> count($document_list),
				'all_records'					=> $this->bo->total_records,
				'link_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'					=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'type'						=> $this->doc_type,
				'lang_no_cat'					=> lang('no category'),
				'lang_cat_statustext'				=> lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'doc_type',
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'filter','selected' => $this->doc_type,'type' =>'document','order'=>'descr')),

				'select_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),

				'lang_status_statustext'			=> lang('Select the status the document belongs to. To do not use a category select NO STATUS'),
				'status_name'					=> 'status_id',
				'lang_no_status'				=> lang('No status'),
				'status_list'					=> $this->bo->select_status_list('filter',$this->status_id),

				'lang_user_statustext'				=> lang('Select the user the document belongs to. To do not use a category select NO USER'),
				'select_user_name'				=> 'filter',
				'lang_no_user'					=> lang('No user'),
				'user_list'					=> $this->bocommon->get_user_list_right2('filter',4,$this->filter,$this->acl_location,array('all'),$default=$this->account),

				'lang_searchfield_statustext'			=> lang('Enter the search string. To show all entries, empty this field and press the SUBMIT button again'),
				'lang_searchbutton_statustext'			=> lang('Submit the search string'),
				'query'						=> $this->query,
				'lang_search'					=> lang('search'),
				'table_header_document'				=> $table_header,
				'values_document'				=> $content,
				'table_add'					=> $table_add,
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.index', 'preserve'=> 1)),
				'lang_done'					=> lang('done'),
				'lang_done_statustext'				=> lang('Back to the list')
			);

			$appname	= lang('document');
			$function_msg	= lang('list document');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg . ' - ' . $appname_sub;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('list_document' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
			$this->save_sessiondata();*/
		}

		function view_file()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$document_id 		= phpgw::get_var('id', 'int');
			$p_num = phpgw::get_var('p_num');

			$values = $this->bo->read_single($document_id);

			$bofiles	= CreateObject('property.bofiles');
			if($this->cat_id)
			{
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$file	= "{$bofiles->fakebase}/document/{$values['location_data']['loc1']}/entity_{$this->entity_id}_{$this->cat_id}/{$p_num}/{$values['document_name']}";
			}
			else
			{
				$file	= "{$bofiles->fakebase}/document/{$values['location_data']['loc1']}/{$values['document_name']}";
			}

			$bofiles->view_file('', $file);
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

				if($location_code)
				{
					$values['location_data'] = $this->bolocation->read_single($location_code,array());
				}
			}

//_debug_array($values);
			if($values[extra]['p_entity_id'])
			{
				$this->entity_id=$values[extra]['p_entity_id'];
				$this->cat_id=$values[extra]['p_cat_id'];
				$p_num=$values['extra']['p_num'];
			}

			if($this->cat_id)
			{
				$entity = $this->boadmin_entity->read_single($this->entity_id,false);
				$category = $this->boadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$values['entity_name']=$entity['name'];
				$values['category_name']=$category['name'];
			}

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

				$bofiles	= CreateObject('property.bofiles');

				$document_dir = 'document/' . $values['location']['loc1'];

				if($values['extra']['p_num'])
				{
					$document_dir .= "/entity_{$this->entity_id}_{$this->cat_id}/{$values['extra']['p_num']}";
				}

				$to_file	= "{$bofiles->fakebase}/{$document_dir}/{$values['document_name']}";

				if((!isset($values['document_name_orig']) || !$values['document_name_orig']) && $bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$receipt['error'][]=array('msg'=>lang('This file already exists !'));
				}

				$receipt2 = $bofiles->create_document_dir($document_dir);
				$receipt = array_merge($receipt, $receipt2);
				unset($receipt2);

				$values['document_id'] = $document_id;

				if(!$receipt['error'])
				{
					if($values['document_name'] && !$values['link'])
					{
						$bofiles->vfs->override_acl = 1;

						if(!$bofiles->vfs->cp (array (
							'from'		=> $_FILES['document_file']['tmp_name'],
							'to'		=> $to_file,
							'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
						{
							$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
						}
						$bofiles->vfs->override_acl = 0;
					}

					if(!$receipt['error'])
					{
						$receipt = $this->bo->save($values);
	//					$document_id=$receipt['document_id'];
						$GLOBALS['phpgw']->session->appsession('session_data','document_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'property.uidocument.list_doc', 'location_code'=> implode("-", $values['location']), 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $values['extra']['p_num']));
					}
				}
				else
				{
					$values['document_name']='';
					if($values['location'])
					{
						$location_code=implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $this->bolocation->read_single($location_code,$values['extra']);
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
			$location_data=$this->bolocation->initiate_ui_location(array(
						'values'		=> $values['location_data'],
						'type_id'		=> -1, // calculated from location_types
						'no_link'		=> false, // disable lookup links for location type less than type_id
						'tenant'		=> false,
						'lookup_type'		=> 'form',
						'lookup_entity'		=> $this->bocommon->get_lookup_entity('document'),
						'entity_data'		=> $values['p']
						));


			$vendor_data=$this->bocommon->initiate_ui_vendorlookup(array(
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

			$jscal = CreateObject('phpgwapi.jscalendar');
			$jscal->add_listener('values_document_date');

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'vendor_data'					=> $vendor_data,
				'record_history'				=> $record_history,
				'table_header_history'				=> $table_header_history,
				'lang_history'					=> lang('History'),
				'lang_no_history'				=> lang('No history'),

				'img_cal'					=> $GLOBALS['phpgw']->common->image('phpgwapi','cal'),
				'lang_datetitle'				=> lang('Select date'),

				'lang_document_date_statustext'			=> lang('Select date the document was created'),
				'lang_document_date'				=> lang('document date'),
				'value_document_date'				=> $values['document_date'],

				'vendor_data'					=> $vendor_data,
				'location_data'					=> $location_data,
				'location_type'					=> 'form',
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'lang_year'					=> lang('Year'),
				'lang_category'					=> lang('category'),
				'lang_save'					=> lang('save'),
				'lang_save_statustext'				=> lang('Save the document'),

				'done_action'					=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uidocument.' .$from, 'location_code'=> $location_code, 'entity_id'=> $this->entity_id, 'cat_id'=> $this->cat_id, 'p_num'=> $p_num, 'preserve'=> 1)),
				'lang_done'					=> lang('done'),
				'lang_done_statustext'				=> lang('Back to the list'),

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

				'lang_link'					=> lang('Link'),
				'value_link'					=> $values['link'],
				'lang_link_statustext'				=> lang('Alternative - link instead of uploading a file'),

				'lang_descr_statustext'				=> lang('Enter a description of the document'),
				'lang_descr'					=> lang('Description'),
				'value_descr'					=> $values['descr'],
				'lang_no_cat'					=> lang('Select category'),
				'lang_cat_statustext'				=> lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[doc_type]',
				'value_cat_id'					=> $values['doc_type'],
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['doc_type'],'type' =>'document','order'=>'descr')),

				'lang_coordinator'				=> lang('Coordinator'),
				'lang_user_statustext'				=> lang('Select the coordinator the document belongs to. To do not use a category select NO USER'),
				'select_user_name'				=> 'values[coordinator]',
				'lang_no_user'					=> lang('Select coordinator'),
				'user_list'					=> $this->bocommon->get_user_list_right2('select',4,$values['coordinator'],$this->acl_location),

				'status_list'					=> $this->bo->select_status_list('select',$values['status']),
				'status_name'					=> 'values[status]',
				'lang_no_status'				=> lang('Select status'),
				'lang_status'					=> lang('Status'),
				'lang_status_statustext'			=> lang('What is the current status of this document ?'),

				'value_location_code'				=> $values['location_code'],

				'branch_list'					=> $this->bo->select_branch_list($values['branch_id']),
				'lang_no_branch'				=> lang('No branch'),
				'lang_branch'					=> lang('branch'),
				'lang_branch_statustext'			=> lang('Select the branch for this document')
			);

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

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($document_id);
				$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
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

			$location_data=$this->bolocation->initiate_ui_location(array(
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

			$data = array
			(
				'vendor_data'					=> $vendor_data,
				'record_history'				=> $record_history,
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
				'lang_save_statustext'				=> lang('Save the document'),
				'lang_no_cat'					=> lang('Select category'),
				'lang_cat_statustext'				=> lang('Select the category the document belongs to. To do not use a category select NO CATEGORY'),
				'select_name'					=> 'values[doc_type]',
				'value_cat_id'					=> $values['doc_type'],
				'cat_list'					=> $this->bocommon->select_category_list(array('format'=>'select','selected' => $values['doc_type'],'type' =>'document','order'=>'descr')),

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
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}
	}

