<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009 Free Software Foundation, Inc. http://www.fsf.org/
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
		var $location_info;

		var $public_functions = array
		(
			'index'  => true,
			'view'   => true,
			'edit'   => true,
			'delete' => true
		);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bocategory',true);
			$this->bocommon				= CreateObject('property.bocommon');
			$this->custom				= & $this->bo->custom;

			$this->location_info		= $this->bo->location_info;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $this->location_info['menu_selection'];
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->location_info['acl_location'];
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
			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', "general_receipt_{$type}_{$type_id}");
			$this->save_sessiondata();

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = "general.index.{$type}";

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

				$datatable['actions']['form'] = array
				(
					array
					(
					'action'	=> $GLOBALS['phpgw']->link('/index.php',
								array
								(
									'menuaction'	=> 'property.uicategory.index',
									'type'			=> $type,
									'type_id'		=> $type_id
								)
							),
					'fields'	=> array
					(
	                		'field' => array
	                		(
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_done',
									'value'	=> lang('done'),
									'tab_index' => 9
								),
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_new',
									'value'	=> lang('add'),
									'tab_index' => 8
								),
								array
								( //button     SEARCH
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
									'value'    => $this->query,
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

			$values = $this->bo->read();
			$uicols = $this->bo->uicols;

/*			$uicols['name'][0]	= 'id';
			$uicols['descr'][0]	= lang('category ID');
			$uicols['name'][1]	= 'descr';
			$uicols['descr'][1]	= lang('Descr');
*/
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($values) AND is_array($values))
			{
				foreach($values as $category_entry)
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
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'edit',
					'statustext' 	=> lang('edit the actor'),
					'text'			=> lang('edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'		=> 'property.uicategory.edit',
											'type'				=> $type,
											'type_id'			=> $type_id
										)),
					'parameters'	=> $parameters
				);
				$datatable['rowactions']['action'][] = array
				(
					'my_name'		=> 'edit',
					'text' 			=> lang('open edit in new window'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'		=> 'property.uicategory.edit',
											'type'				=> $type,
											'type_id'			=> $type_id,
											'target'			=> '_blank'
										)),
					'parameters'	=> $parameters
				);
			}

			if($this->acl_delete)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'delete',
					'statustext' 	=> lang('delete the actor'),
					'text'			=> lang('delete'),
					'confirm_msg'	=> lang('do you really want to delete this entry'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uicategory.delete',
											'type'			=> $type,
											'type_id'		=> $type_id
										)),
					'parameters'	=> $parameters
				);
			}
			unset($parameters);

			if($this->acl_add)
			{
				$datatable['rowactions']['action'][] = array
				(
					'my_name' 			=> 'add',
					'statustext' 	=> lang('add'),
					'text'			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction'	=> 'property.uicategory.edit',
											'type'			=> $type,
											'type_id'		=> $type_id
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
			$datatable['pagination']['records_returned']= count($values);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname			=  $this->location_info['name'];
			$function_msg		= lang('list %1', $appname);

			if ( ($this->start == 0) && (!$this->order))
			{
				$datatable['sorting']['order'] 			= 'id'; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= $this->order; // name of column of Database
				$datatable['sorting']['sort'] 			= $this->sort; // ASC / DESC
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

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::{$function_msg}";

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'category.index', 'property' );
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=> 2, 'acl_location'=> $this->acl_location));
			}

			$type		= phpgw::get_var('type');
			$type_id	= phpgw::get_var('type_id', 'int');
			$id			= phpgw::get_var('id');
			$values		= phpgw::get_var('values');

			$values_attribute  = phpgw::get_var('values_attribute');
		
			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'general.edit.' . $type;

			$GLOBALS['phpgw']->xslttpl->add_file(array('category','attributes_form'));
			$receipt = array();

			if (is_array($values))
			{
				$insert_record_values = $GLOBALS['phpgw']->session->appsession("insert_record_values{$this->acl_location}",'property');
				
				if(is_array($insert_record_values))
				{
					foreach($insert_record_values as $field)
					{
						$values['extra'][$field] = 	phpgw::get_var($field);
					}
				}

				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					if(!$id && !$values['id'] && $this->location_info['id']['type'] !='auto')
					{
						$receipt['error'][]=array('msg'=>lang('Please enter an id!'));									
					}

					if($values['id'] && $this->location_info['id']['type'] == 'int' && !ctype_digit($values['id']))
					{
						$receipt['error'][]=array('msg'=>lang('Please enter an integer !'));
						unset($values['id']);
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
						$receipt = $this->bo->save($values,$action,$values_attribute);

						if (isset($values['save']) && $values['save'])
						{
							$GLOBALS['phpgw']->session->appsession('session_data', "general_receipt_{$type}_{$type_id}", $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uicategory.index', 'type'=> $type,	'type_id' => $type_id));
						}
						$id = $receipt['id'];
					}
					else
					{
						unset($values['id']);
						$id = '';
					}
					
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uicategory.index', 'type'=> $type,	'type_id' => $type_id));
				}
			}

			if ($id)
			{
				$values = $this->bo->read_single(array('id' => $id));
				$function_msg = $this->location_info['edit_msg'];
				$action='edit';
			}
			else
			{
				$values = $this->bo->read_single();
				$function_msg = $this->location_info['add_msg'];
				$action='add';
			}

			/* Preserve attribute values from post */
			if(isset($receipt['error']) && (isset( $values_attribute) && is_array( $values_attribute)))
			{
				$values = $this->custom->preserve_attribute_values($values,$values_attribute);
			}


			$link_data = array
			(
				'menuaction'	=> 'property.uicategory.edit',
				'id'			=> $id,
				'type'			=> $type,
				'type_id'		=> $type_id
			);
//_debug_array($link_data);

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

				phpgwapi_yui::tabview_setup('general_edit_tabview');
				$tabs['general']	= array('label' => lang('general'), 'link' => '#general');

				$attributes_groups = $this->custom->get_attribute_groups('property', $this->acl_location, $values['attributes']);

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

			foreach ($this->location_info['fields'] as & $field)
			{
				$field['value'] = 	isset($values[$field['name']]) ? $values[$field['name']] : '';
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
			(
				'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uicategory.index', 'type'=> $type, 'type_id'=> $type_id)),
				'lang_id'						=> lang('ID'),
				'lang_descr'					=> lang('Descr'),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),
				'lang_apply'					=> lang('apply'),
				'value_id'						=> isset($values['id']) ? $values['id'] : '',
				'value_descr'					=> $values['descr'],
				'lang_id_text'					=> lang('Enter the ID'),
				'lang_descr_text'				=> lang('Enter a description of the record'),
				'lang_done_text'				=> lang('Back to the list'),
				'lang_save_text'				=> lang('Save the record'),
				'lang_apply_statustext'			=> lang('Apply the values'),
				'lang_cancel_statustext'		=> lang('Leave the actor untouched and return back to the list'),
				'lang_save_statustext'			=> lang('Save the actor and return back to the list'),

				'attributes_group'				=> $attributes,
				'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
				'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 60,
				'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 10,
				'tabs'							=> phpgwapi_yui::tabview_generate($tabs, 'general'),
				'id_type'						=> $this->location_info['id']['type'],
				'fields'						=> $this->location_info['fields']
			);
//_debug_array($this->location_info['fields']);die();
			$appname	=  $this->location_info['name'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . "::{$appname}::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				return lang('no access');
			}

			$id	= phpgw::get_var('id');

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return lang('id %1 has been deleted', $id);
			}
		}
	}

