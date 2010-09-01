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

	class property_uijasper
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
			'edit'   => true,
			'delete' => true,
			'download'	=> true
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::jasper';

			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.bojasper',true);
			$this->bocommon			= CreateObject('property.bocommon');

			$this->acl 				= & $GLOBALS['phpgw']->acl;
			$this->acl_location		= '.jasper';
			$this->acl_read 		= $this->acl->check('.jasper', PHPGW_ACL_READ, 'property');
			$this->acl_add 			= $this->acl->check('.jasper', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 		= $this->acl->check('.jasper', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 		= $this->acl->check('.jasper', PHPGW_ACL_DELETE, 'property');

			$this->start			= $this->bo->start;
			$this->query			= $this->bo->query;
			$this->sort				= $this->bo->sort;
			$this->order			= $this->bo->order;
			$this->allrows			= $this->bo->allrows;
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

		function download()
		{
			$list = $this->bo->read();
			$uicols['name'][0]	= 'id';
			$uicols['descr'][0]	= lang('Budget account');
			$uicols['name'][1]	= 'descr';
			$uicols['descr'][1]	= lang('Description');

			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>1, 'acl_location'=> $this->acl_location));
			}

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
	    		(
	    			'menuaction'			=> 'property.uijasper.index'//,
   				));

   				$datatable['config']['base_java_url'] = "menuaction:'property.uijasper.index'";

				$link_data = array
				(
					'menuaction'	=> 'property.uijasper.index'//,
					//'id'		=> $id
				);

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array(
				array(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array(
								'menuaction'	=> 'property.uijasper.index'//,
							)
						),
					'fields'	=> array(
	                                    'field' => array
	                                    			(
														array
														(
															'type'	=> 'button',
															'id'	=> 'btn_export',
															'value'	=> lang('download'),
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
			}

			$jasper_list = $this->bo->read($type);
			$uicols = array();
			$uicols['name'][]	= 'id';
			$uicols['descr'][]	= lang('id');
			$uicols['name'][]	= 'title';
			$uicols['descr'][]	= lang('title');
			$uicols['name'][]	= 'descr';
			$uicols['descr'][]	= lang('Description');
			$uicols['name'][]	= 'file_name';
			$uicols['descr'][]	= lang('filename');
			$uicols['name'][]	= 'location';
			$uicols['descr'][]	= lang('location');
			$uicols['name'][]	= 'user';
			$uicols['descr'][]	= lang('user');
			$uicols['name'][]	= 'entry_date';
			$uicols['descr'][]	= lang('entry date');
			$uicols['name'][]	= 'access';
			$uicols['descr'][]	= lang('access');

			$j = 0;
			$count_uicols_name = count($uicols['name']);

			foreach($jasper_list as $account_entry)
			{
				for ($k=0;$k<$count_uicols_name;$k++)
				{
					$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols['name'][$k];
					$datatable['rows']['row'][$j]['column'][$k]['value']			= $account_entry[$uicols['name'][$k]];
				}
				$j++;
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

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'edit',
							'statustext' 	=> lang('edit the jasper entry'),
							'text'			=> lang('edit'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uijasper.edit'
									)),
						'parameters'	=> $parameters
						);
			}

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array(
							'my_name' 			=> 'delete',
							'statustext' 	=> lang('delete the jasper entry'),
							'text'			=> lang('delete'),
							'confirm_msg'	=> lang('do you really want to delete this entry'),
							'action'		=> $GLOBALS['phpgw']->link('/index.php',array
									(
										'menuaction'	=> 'property.uijasper.delete'
									)),
						'parameters'	=> $parameters
						);
			}

			$datatable['rowactions']['action'][] = array(
					'my_name' 		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uijasper.edit'
					)));

			for ($i=0;$i<$count_uicols_name;$i++)
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
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($jasper_list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname					= 'JasperReport';
			$function_msg				= lang('list report definitions');

			if ( !phpgw::get_var('start') && !phpgw::get_var('order','string'))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'desc'; // ASC / DESC
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

			phpgwapi_yui::load_widget('dragdrop');
		  	phpgwapi_yui::load_widget('datatable');
		  	phpgwapi_yui::load_widget('menu');
		  	phpgwapi_yui::load_widget('connection');
		  	phpgwapi_yui::load_widget('loader');
			phpgwapi_yui::load_widget('tabview');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('animation');

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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'jasper.index', 'property' );

		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$id	= phpgw::get_var('id', 'int');
			$values			= phpgw::get_var('values');

			$GLOBALS['phpgw']->xslttpl->add_file(array('jasper'));

			if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
			{
				if($GLOBALS['phpgw']->session->is_repost())
				{
	//				$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
				}


				if(!isset($values['location']) || !$values['location'])
				{
					$receipt['error'][]=array('msg'=>lang('Please select a location!'));
				}

				if(!isset($values['title']) || !$values['title'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a title!'));
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
					$receipt = $this->bo->save($values);

//-------------start files
					$bofiles	= CreateObject('property.bofiles');
					$files = array();
					if(isset($_FILES['file']['name']) && $_FILES['file']['name'])
					{
_debug_array($_FILES['file']['name']);die();
//FIXME
						$file_name = str_replace (' ','_',$_FILES['file']['name']);
						$to_file	= "{$bofiles->fakebase}/{$this->category_dir}/{$loc1}/{$id}/{$file_name}";

						if ($bofiles->vfs->file_exists(array(
								'string' => $to_file,
								'relatives' => Array(RELATIVE_NONE)
							)))
						{
							$receipt['error'][]=array('msg'=>lang('This file already exists !'));
						}
						else
						{
							$files[] = array
							(
								'from_file'	=> $_FILES['file']['tmp_name'],
								'to_file'	=> $to_file
							);
						}

						unset($to_file);
						unset($file_name);
					}
					foreach ($files as $file)
					{
						$bofiles->create_document_dir("{$this->category_dir}/{$loc1}/{$id}");
						$bofiles->vfs->override_acl = 1;

						if(!$bofiles->vfs->cp (array (
							'from'	=> $file['from_file'],
							'to'	=> $file['to_file'],
							'relatives'	=> array (RELATIVE_NONE|VFS_REAL, RELATIVE_ALL))))
						{
							$receipt['error'][]=array('msg'=>lang('Failed to upload file !'));
						}
						$bofiles->vfs->override_acl = 0;
					}
					unset($loc1);
					unset($files);
					unset($file);					
//-------------end files



					if (isset($values['save']) && $values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','jasper_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uijasper.index'));
					}
				}
			}

			if (isset($values['cancel']) && $values['cancel'])
			{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uijasper.index'));
			}

			if ($id)
			{
				$values = $this->bo->read_single($id);
				$function_msg = lang('edit budget account');
				$action='edit';
			}
			else
			{
				$function_msg = lang('add budget account');
				$action='add';
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uijasper.edit',
				'id'		=> $id
			);
//_debug_array($jasper);

			$locations = $GLOBALS['phpgw']->locations->get_locations();
			$selected_location = isset($values['location']) ? $values['location'] : '';
			if(isset($values['location_id']) && $values['location_id'])
			{
				$locations_info = $GLOBALS['phpgw']->locations->get_name($values['location_id']);
				$selected_location = $locations_info['location'];
			}

			$location_list = array();
			foreach ( $locations as $location => $descr )
			{
				$location_list[] = array
				(
					'id'		=> $location,
					'name'		=> "{$location} [{$descr}]",
					'selected'	=> $location == $selected_location
				);
			}

			$type_def = array
			(
				array('key' => 'count',	'label'=>'#','sortable'=>true,'resizeable'=>true),
       			array('key' => 'type_name',	'label'=>lang('type'),'sortable'=>true,'resizeable'=>true),
      			array('key' => 'input_name','label'=>lang('name'),'sortable'=>true,'resizeable'=>true),
		//		array('key' => 'value',	'label'=>lang('value'),'sortable'=>true,'resizeable'=>true)
			);

			$inputs = isset($values['input']) && $values['input'] ? $values['input'] : array();

			if($this->acl_edit)
			{
				$type_def[] = array('key' => 'delete_input','label'=>lang('delete'),'sortable'=>false,'resizeable'=>true,'formatter'=>'FormatterCenter');
				foreach($inputs as &$input)
				{
					$input['delete_input'] = '<input type="checkbox" name="values[delete_input][]" value="'.$input['id'].'" title="'.lang('Check to delete input').'">';
				}
			}

			//---datatable settings--------------------------
			$datavalues[0] = array
			(
					'name'					=> "0",
					'values' 				=> json_encode($inputs),
					'total_records'			=> count($inputs),
					'is_paginator'			=> 0,
					'footer'				=> 0
			);					
       		$myColumnDefs[0] = array
       		(
       			'name'		=> "0",
				'values'	=>	json_encode($type_def)
			);		
			//-----------------------------------------------


			$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'value_id'						=> $id,
				'value_title'					=> $values['title'],
				'value_descr'					=> $values['descr'],
				'value_access'					=> $values['access'],
				'input_type_list'				=> $this->bo->get_input_type_list(),
				'location_list'					=> $location_list,
				'td_count'						=> '""',
				'base_java_url'					=> "{menuaction:'property.uijasper.edit'}",
				'property_js'					=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property2.js"),
				'datatable'						=> $datavalues,
				'myColumnDefs'					=> $myColumnDefs,
			);

			//---datatable settings--------------------
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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'jasper.edit', 'property' );
			//-----------------------datatable settings---

			$appname						= 'JasperReports';

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>8, 'acl_location'=> $this->acl_location));
			}

			$id		= phpgw::get_var('id'); // string
			//$confirm		= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
			(
				'menuaction' => 'property.uijasper.index'
			);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return "id ".$id." ".lang("has been deleted");
				//$GLOBALS['phpgw']->redirect_link('/index.php',$link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
			(
				'done_action'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'delete_action'			=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uijasper.delete', 'id'=> $id)),
				'lang_confirm_msg'		=> lang('do you really want to delete this entry'),
				'lang_yes'			=> lang('yes'),
				'lang_yes_jaspertext'	=> lang('Delete the entry'),
				'lang_no_jaspertext'		=> lang('Back to the list'),
				'lang_no'			=> lang('no')
			);

			$appname		= lang('budget account');
			$function_msg		= lang('delete budget account');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		//	$GLOBALS['phpgw']->xslttpl->pp();
		}

	}

