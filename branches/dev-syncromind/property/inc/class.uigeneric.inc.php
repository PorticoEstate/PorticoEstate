<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007,2008,2009,2010,2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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

	class property_uigeneric
	{
		protected $appname = 'property';
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
				'index'  			=> true,
				'edit'   			=> true,
				'delete'			=> true,
				'download'			=> true,
				'columns'			=> true,
				'attrib_history'	=> true
			);

		function __construct()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$this->account				= $GLOBALS['phpgw_info']['user']['account_id'];
			$this->bo					= CreateObject('property.bogeneric',true);
			$this->bo->get_location_info();
			$this->bocommon				= & $this->bo->bocommon;
			$this->custom				= & $this->bo->custom;

			$this->location_info		= $this->bo->location_info;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = $this->location_info['menu_selection'];
			$this->acl 					= & $GLOBALS['phpgw']->acl;
			$this->acl_location			= $this->location_info['acl_location'];
			$this->acl_read 			= $this->acl->check($this->acl_location, PHPGW_ACL_READ, $this->location_info['acl_app']);
			$this->acl_add 				= $this->acl->check($this->acl_location, PHPGW_ACL_ADD, $this->location_info['acl_app']);
			$this->acl_edit 			= $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, $this->location_info['acl_app']);
			$this->acl_delete 			= $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, $this->location_info['acl_app']);
			$this->acl_manage 			= $this->acl->check($this->acl_location, 16, $this->location_info['acl_app']);

			$this->start				= $this->bo->start;
			$this->query				= $this->bo->query;
			$this->sort					= $this->bo->sort;
			$this->order				= $this->bo->order;
			$this->allrows				= $this->bo->allrows;

			$this->type 		= $this->bo->type;
			$this->type_id 		= $this->bo->type_id;

			if($appname = $this->bo->appname)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] = str_replace('property', $appname, $GLOBALS['phpgw_info']['flags']['menu_selection']);
				$this->appname = $appname;
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
					'allrows'	=> $this->allrows,
					'type'		=> $this->type
				);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$list = $this->bo->read();
			$uicols	= $this->bo->uicols;
			$this->bocommon->download($list,$uicols['name'],$uicols['descr'],$uicols['input_type']);
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
				$GLOBALS['phpgw']->preferences->account_id = $this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add($this->location_info['acl_app'],"generic_columns_{$this->type}_{$this->type_id}",$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			$function_msg   = lang('Select Column');

			$link_data = array
				(
					'menuaction'	=> 'property.uigeneric.columns',
					'type'			=> $this->type,
					'type_id'		=> $this->type_id

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
		}

		function index()
		{
			if(!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}

			$receipt = $GLOBALS['phpgw']->session->appsession('session_data', "general_receipt_{$this->type}_{$this->type_id}");
			$this->save_sessiondata();

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = "general.index.{$this->type}";

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uigeneric.index',
						'appname'		=> $this->appname,
						'type'			=> $this->type,
						'type_id'		=> $this->type_id
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uigeneric.index',"
					."appname:'{$this->appname}',"
					."type:'{$this->type}',"
					."type_id:'{$this->type_id}'";

				$link_data = array
					(
						'menuaction'	=> 'property.uigeneric.index',
						'appname'		=> $this->appname,
						'type'			=> $this->type,
						'type_id'		=> $this->type_id
					);


				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'property.uigeneric.index',
								'appname'		=> $this->appname,
								'type'			=> $this->type,
								'type_id'		=> $this->type_id
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								array
								(
									'type'	=> 'button',
									'id'	=> 'btn_export',
									'value'	=> lang('download'),
									'tab_index' => 10
								),
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


				if($GLOBALS['phpgw']->locations->get_attrib_table($this->location_info['acl_app'], $this->location_info['acl_location']))
				{
					$datatable['actions']['form'][0]['fields']['field'][] =  array
						(
							'type'=> 'link',
							'id'  => 'btn_columns',
							'url' => "Javascript:window.open('".$GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction' => 'property.uigeneric.columns',
								'appname'		=> $this->appname,
								'type'			=> $this->type,
								'type_id'		=> $this->type_id
							)
						)."','','width=350,height=370')",
						'value' => lang('columns'),
						'tab_index' => 7
					);
				}

				$values_combo_box = array();
				$i = 0;
				$button_def = array();
				$code_inner = array();
				foreach ( $this->location_info['fields'] as $field )
				{
					if (isset($field['filter']) && $field['filter'])
					{
						$datatable['actions']['form'][0]['fields']['field'][] = array
							(
								'id' => "btn_{$field['name']}",
								'name' => $field['name'],
								'value'	=> $field['descr'],
								'type' => 'button',
								'style' => 'filter',
								'tab_index' => $i
							);

						$button_def[] = "oMenuButton_{$i}"; 
						$code_inner[] = "{order:{$i}, var_URL:'{$field['name']}',name:'btn_{$field['name']}',style:'genericbutton',dependiente:[]}";

						if($field['values_def']['valueset'])
						{
							$values_combo_box[] = $field['values_def']['valueset'];
							// TODO find selected value
						}
						else if(isset($field['values_def']['method']))
						{
							foreach($field['values_def']['method_input'] as $_argument => $_argument_value)
							{
								if(preg_match('/^##/', $_argument_value))
								{
									$_argument_value_name = trim($_argument_value,'#');
									$_argument_value = $values[$_argument_value_name];
								}
								if(preg_match('/^\$this->/', $_argument_value))
								{
									$_argument_value_name = ltrim($_argument_value,'$this->');
									$_argument_value = $this->$_argument_value_name;
								}								
								$method_input[$_argument] = $_argument_value;
							}
							$values_combo_box[] = execMethod($field['values_def']['method'],$method_input);
						}
						$default_value = array ('id'=>'','name'=> lang('select') . ' ' . $field['descr']);
						array_unshift ($values_combo_box[$i],$default_value);
						$i++;
					}
				}

				if($button_def)
				{
					$code = 'var ' . implode(',', $button_def)  . ";\n";
					$code .= 'var selectsButtons = [' . "\n" . implode(",\n",$code_inner) . "\n];";
				}
				else
				{
					$code = 'var selectsButtons = [];';
				}

				$GLOBALS['phpgw']->js->add_code('', $code);

				if($values_combo_box)
				{
					$i = 0;
					foreach ( $values_combo_box as $combo )
					{
						$datatable['actions']['form'][0]['fields']['hidden_value'][] = array
							(
								'id' 	=> "values_combo_box_{$i}",
								'value'	=> $this->bocommon->select2String($combo)						
							);
						$i++;
					}
				}

				//				$dry_run = true;
			}

			$values = $this->bo->read();
			$uicols = $this->bo->uicols;

			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($values) AND is_array($values))
			{
				foreach($values as $entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						if($uicols['input_type'][$k]!='hidden')
						{
							$datatable['rows']['row'][$j]['column'][$k]['name'] 		= $uicols['name'][$k];
							$datatable['rows']['row'][$j]['column'][$k]['value']		= $entry[$uicols['name'][$k]];
							$datatable['rows']['row'][$j]['column'][$k]['format']		= $uicols['datatype'][$k];
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
							'name'		=> $this->location_info['id']['name'],
							'source'	=>  $this->location_info['id']['name']
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
							'menuaction'		=> isset($this->location_info['edit_action']) &&  $this->location_info['edit_action'] ?  $this->location_info['edit_action'] : 'property.uigeneric.edit',
							'appname'			=> $this->appname,
							'type'				=> $this->type,
							'type_id'			=> $this->type_id
						)),
						'parameters'	=> $parameters
					);
				$datatable['rowactions']['action'][] = array
					(
						'my_name'		=> 'edit',
						'text' 			=> lang('open edit in new window'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> isset($this->location_info['edit_action']) &&  $this->location_info['edit_action'] ?  $this->location_info['edit_action'] : 'property.uigeneric.edit',
							'appname'		=> $this->appname,
							'type'				=> $this->type,
							'type_id'			=> $this->type_id,
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
							'menuaction'	=> 'property.uigeneric.delete',
							'appname'		=> $this->appname,
							'type'			=> $this->type,
							'type_id'		=> $this->type_id
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
							'menuaction'	=> isset($this->location_info['edit_action']) &&  $this->location_info['edit_action'] ?  $this->location_info['edit_action'] : 'property.uigeneric.edit',
							'appname'		=> $this->appname,
							'type'			=> $this->type,
							'type_id'		=> $this->type_id
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
					$datatable['headers']['header'][$i]['sortable']			= $uicols['sortable'][$i];
					$datatable['headers']['header'][$i]['sort_field']   	= $uicols['name'][$i];
				}
			}

			//path for property.js
			$property_js = "/property/js/yahoo/property.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js;

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($values);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname			=  $this->location_info['name'];
			$function_msg		= lang('list %1', $appname);

			if ( ($this->start == 0) && (!$this->order))
			{
				$datatable['sorting']['order'] 			=  $this->location_info['id']['name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= $this->order; // name of column of Database
				$datatable['sorting']['sort'] 			= $this->sort; // ASC / DESC
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
						if(isset($column['format']) && $column['format']== 'link' && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='{$column['link']}' onclick='javascript:filter_data(this.id);'>{$column['value']}</a>";
						}
						else if(isset($column['format']) && $column['format']== 'link')
						{
							$json_row[$column['name']] = "<a href='{$column['value']}' target='_blank'>" .lang('link') . '</a>';
						}
						else if(isset($column['format']) && $column['format']== 'text')
						{
							$json_row[$column['name']] = nl2br($column['value']);
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
				$json['rights'] = $datatable['rowactions']['action'];
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

			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw']->translation->translate($this->location_info['acl_app'], array(), false, $this->location_info['acl_app']) . "::{$appname}::{$function_msg}";

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'generic.index', 'property' );
		}

		function edit()
		{
			if(!$this->acl_add)
			{
				$this->bocommon->no_access();
				return;
			}

			$id			= phpgw::get_var($this->location_info['id']['name']);
			$values		= phpgw::get_var('values');

			$values_attribute  = phpgw::get_var('values_attribute');

			$GLOBALS['phpgw_info']['apps']['manual']['section'] = 'general.edit.' . $this->type;

			$GLOBALS['phpgw']->xslttpl->add_file(array('generic','attributes_form'));
			$receipt = array();

			if (is_array($values))
			{
				$insert_record_attributes = $GLOBALS['phpgw']->session->appsession("insert_record_values{$this->acl_location}",$this->location_info['acl_app']);

				if(is_array($insert_record_attributes))
				{
					foreach ($insert_record_attributes as $attribute)
					{
						foreach ($values_attribute as &$attr)
						{
							if($attr['name'] ==  $attribute)
							{
								$attr['value'] = phpgw::get_var($attribute, 'string', 'POST');
							}
						}
					}
				}

//				$values = $this->bocommon->collect_locationdata($values,$insert_record_values);
				if ((isset($values['save']) && $values['save']) || (isset($values['apply']) && $values['apply']))
				{
					if($GLOBALS['phpgw']->session->is_repost())
					{
						$receipt['error'][]=array('msg'=>lang('Hmm... looks like a repost!'));
					}

					if(!$id && !$values[$this->location_info['id']['name']] && $this->location_info['id']['type'] !='auto')
					{
						$receipt['error'][]=array('msg'=>lang('missing value for %1', lang('id')));									
					}

					foreach ( $this->location_info['fields'] as $field_info )
					{
						if (isset($field_info['nullable']) && $field_info['nullable'] != true)
						{
							if( !$values[$field_info['name']] )
							{
								$receipt['error'][]=array('msg'=>lang('missing value for %1', $field_info['descr']));									
							}
						}

						if ($field_info['type'] == 'int')
						{
							if( $values[$field_info['name']] && !ctype_digit($values[$field_info['name']]) )
							{
								$receipt['error'][]=array('msg'=> "{$field_info['descr']}: " . lang('Please enter an integer !'));
							}
						}
						else if ($field_info['type'] == 'numeric')
						{
							$values[$field_info['name']] = str_replace(',', '.', $values[$field_info['name']]);
							if( $values[$field_info['name']] && ! is_numeric($values[$field_info['name']]) )
							{
								$receipt['error'][]=array('msg'=> "{$field_info['descr']}: " . lang('Please enter a numeric value !'));
							}
						}
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

							if($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
							{
								$receipt['error'][]=array('msg'=>lang('Please enter value for attribute %1', $attribute['input_text']));
							}
	
							if(isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && ! ctype_digit($attribute['value']))
							{
								$receipt['error'][]=array('msg'=>lang('Please enter integer for attribute %1', $attribute['input_text']));						
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
							$GLOBALS['phpgw']->session->appsession('session_data', "general_receipt_{$this->type}_{$this->type_id}", $receipt);
							$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uigeneric.index',
													'appname'		=> $this->appname,
													'type'			=> $this->type,
													'type_id'		=> $this->type_id));
						}
						$id = $receipt['id'];
					}

				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uigeneric.index', 
														'appname'		=> $this->appname,
														'type'			=> $this->type,
														'type_id' 		=> $this->type_id));
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
			if(isset($receipt['error']))
			{
				foreach ( $this->location_info['fields'] as $field )
				{
					$values[$field['name']] = phpgw::clean_value($_POST['values'][$field['name']]);
				}

				if(isset( $values_attribute) && is_array( $values_attribute))
				{
					$values = $this->custom->preserve_attribute_values($values,$values_attribute);
				}
			}

			$link_data = array
				(
					'menuaction'	=> 'property.uigeneric.edit',
					'id'			=> $id,
					'appname'		=> $this->appname,
					'type'			=> $this->type,
					'type_id'		=> $this->type_id
				);

			$tabs = array();

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if($attribute['history'] == true)
					{
						$link_history_data = array
							(
								'menuaction'	=> 'property.uigeneric.attrib_history',
								'appname'		=> $this->appname,
								'attrib_id'	=> $attribute['id'],
								'actor_id'	=> $actor_id,
								'role'		=> $this->role,
								'acl_location'	=> $this->acl_location,
								'edit'		=> true
							);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php',$link_history_data);
					}
				}

				phpgwapi_yui::tabview_setup('general_edit_tabview');

				$attributes_groups = $this->custom->get_attribute_groups($this->location_info['acl_app'], $this->acl_location, $values['attributes']);
//_debug_array($attributes_groups);die();
				if((isset($attributes_groups[0]['id']) && $attributes_groups[0]['id'] > 0 ) || count($attributes_groups) > 1 )
				{
//					$tabs['general']	= array('label' => lang('general'), 'link' => '#general');
				}

				$attributes = array();
				foreach ($attributes_groups as $group)
				{
					if(isset($group['attributes']) && isset($tabs['general']))
					{
//						$tabs[str_replace(' ', '_', $group['name'])] = array('label' => $group['name'], 'link' => '#' . str_replace(' ', '_', $group['name']));
//						$group['link'] = str_replace(' ', '_', $group['name']);
					}
					$attributes[] = $group;
				}
				unset($attributes_groups);
				unset($values['attributes']);
			}

			foreach ($this->location_info['fields'] as & $field)
			{
				$field['value'] = 	isset($values[$field['name']]) ? $values[$field['name']] : '';
				if(isset($field['values_def']))
				{
					if($field['values_def']['valueset'] && is_array($field['values_def']['valueset']))
					{
						$field['valueset'] = $field['values_def']['valueset'];
						foreach($field['valueset'] as &$_entry)
						{
							$_entry['selected'] = $_entry['id'] == $field['value'] ? 1 : 0;
						}
					}
					else if(isset($field['values_def']['method']))
					{

						foreach($field['values_def']['method_input'] as $_argument => $_argument_value)
						{
							if(preg_match('/^##/', $_argument_value))
							{
								$_argument_value_name = trim($_argument_value,'#');
								$_argument_value = $values[$_argument_value_name];
							}
							if(preg_match('/^\$this->/', $_argument_value))
							{
								$_argument_value_name = ltrim($_argument_value,'$this->');
								$_argument_value = $this->$_argument_value_name;
							}

							$method_input[$_argument] = $_argument_value;
						}

						$field['valueset'] = execMethod($field['values_def']['method'],$method_input);
					}

					if(isset($values['id']) && $values['id'] && isset($field['role']) && $field['role'] == 'parent')
					{
						// can not select it self as parent.
						$exclude = array($values['id']);
						$children = $this->bo->get_children2($values['id'], 0,true);

						foreach($children as $child)
						{
							$exclude[] = $child['id']; 
						}

						$k = count($field['valueset']);
						for ($i=0; $i<$k; $i++)
						{
							if (in_array($field['valueset'][$i]['id'],$exclude))
							{
								unset($field['valueset'][$i]);
							}
						}
					}
				}
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'done_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'property.uigeneric.index', 'type'=> $this->type, 'type_id'=> $this->type_id)),
					'lang_descr'					=> lang('Descr'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'value_id'						=> isset($values['id']) ? $values['id'] : '',
					'value_descr'					=> $values['descr'],

					'attributes_group'				=> $attributes,
					'lookup_functions'				=> isset($values['lookup_functions'])?$values['lookup_functions']:'',
					'textareacols'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 60,
					'textarearows'					=> isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 10,
					'tabs'							=> phpgwapi_yui::tabview_generate($tabs, 'general'),
					'id_name'						=> $this->location_info['id']['name'],
					'id_type'						=> $this->location_info['id']['type'],
					'fields'						=> $this->location_info['fields']
				);

			$appname	=  $this->location_info['name'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw']->translation->translate($this->location_info['acl_app'], array(), false, $this->location_info['acl_app']) . "::{$appname}::{$function_msg}";
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}

		function attrib_history()
		{
			$GLOBALS['phpgw']->xslttpl->add_file(array('attrib_history','nextmatchs'));
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$acl_location 	= phpgw::get_var('acl_location', 'string');
			$id				= phpgw::get_var('id', 'int');
			$attrib_id 		= phpgw::get_var('attrib_id', 'int');
			$detail_id 		= phpgw::get_var('detail_id', 'int');

			$data_lookup= array
			(
				'app'			=> 'property',
				'acl_location'	=> $acl_location,
				'id'			=> $id,
				'attrib_id' 	=> $attrib_id,
				'detail_id' 	=> $detail_id,
			);

			$delete = phpgw::get_var('delete', 'bool');
			$edit = phpgw::get_var('edit', 'bool');

			if ($delete)
			{
				$data_lookup['history_id'] = phpgw::get_var('history_id', 'int');
		//		$this->bo->delete_history_item($data_lookup);
			}

			$values = $this->bo->read_attrib_history($data_lookup);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			while (is_array($values) && list(,$entry) = each($values))
			{
				$link_delete_history_data = array
					(
						'menuaction'	=> 'property.uientity.attrib_history',
						'acl_location'	=> $acl_location,
						'id'			=> $data_lookup['id'],
						'attrib_id'		=> $data_lookup['attrib_id'],
						'detail_id' 	=> $data_lookup['detail_id'],
						'history_id'	=> $entry['id'],
						'delete'		=> true,
						'edit'			=> true,
						'type'			=> $this->type
					);
				if($edit)
				{
					$text_delete	= lang('delete');
					$link_delete	= $GLOBALS['phpgw']->link('/index.php',$link_delete_history_data);
				}

				$content[] = array
					(
						'id'				=> $entry['id'],
						'value'				=> $entry['new_value'],
						'user'				=> $entry['owner'],
						'time_created'			=> $GLOBALS['phpgw']->common->show_date($entry['datetime'],$dateformat),
						'link_delete'			=> $link_delete,
						'lang_delete_statustext'	=> lang('delete the item'),
						'text_delete'			=> $text_delete,
					);
			}


			$table_header = array
				(
					'lang_value'		=> lang('value'),
					'lang_user'			=> lang('user'),
					'lang_time_created'	=> lang('time created'),
					'lang_delete'		=> lang('delete')
				);

			$link_data = array
				(
					'menuaction'	=> 'property.uientity.attrib_history',
					'acl_location'	=> $acl_location,
					'id'			=> $id,
					'detail_id' 	=> $data_lookup['detail_id'],
					'edit'			=> $edit,
					'type'			=> $this->type
				);


			//--- asynchronous response --------------------------------------------				

			if( phpgw::get_var('phpgw_return_as') == 'json')
			{
				if(count($content))
				{
					return json_encode($content);
				}
				else
				{
					return "";
				}
			}		
			//---datatable settings---------------------------------------------------				
			$parameters['delete'] = array
				(
					'parameter' => array
					(
						array
						(
							'name'  => 'acl_location',
							'source' => $data_lookup['acl_location'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'id',
							'source' => $data_lookup['id'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'attrib_id',
							'source' => $data_lookup['attrib_id'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'detail_id',
							'source' => $data_lookup['detail_id'],
							'ready'  => 1
						),
						array
						(
							'name'  => 'history_id',
							'source' => 'id',
						),
						array
						(
							'name'  => 'delete',
							'source' => true,
							'ready'  => 1
						),
						array
						(
							'name'  => 'edit',
							'source' => true,
							'ready'  => 1
						),
						array
						(
							'name'  => 'type',
							'source' => $this->type,
							'ready'  => 1
						)				
					)
				);

			if($edit && $this->acl->check($acl_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]))
			{
				$permissions['rowactions'][] = array
					(
						'text'    	=> lang('delete'),
						'action'  	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'property.uigeneric.attrib_history' )),
						'confirm_msg'=> lang('do you really want to delete this entry'),
						'parameters'=> $parameters['delete']
					);
			}

			$datavalues[0] = array
				(
					'name'			=> "0",
					'values' 		=> json_encode($content),
					'total_records'	=> count($content),
					'permission'   	=> json_encode($permissions['rowactions']),
					'is_paginator'	=> 1,
					'footer'		=> 0
				);			   

			$myColumnDefs[0] = array
				(
					'name'			=> "0",
					'values'		=>	json_encode(array(	array('key' => 'id',			'hidden'=>true),
													array('key' => 'value',			'label'=>lang('value'),		'sortable'=>true,'resizeable'=>true),
													array('key' => 'time_created',	'label'=>lang('time created'),'sortable'=>true,'resizeable'=>true),
													array('key' => 'user',			'label'=>lang('user'),		'sortable'=>true,'resizeable'=>true)
				))
			);				

			//----------------------------------------------datatable settings--------			
			$property_js = "/property/js/yahoo/property2.js";

			if (!isset($GLOBALS['phpgw_info']['server']['no_jscombine']) || !$GLOBALS['phpgw_info']['server']['no_jscombine'])
			{
				$cachedir = urlencode($GLOBALS['phpgw_info']['server']['temp_dir']);
				$property_js = "/phpgwapi/inc/combine.php?cachedir={$cachedir}&type=javascript&files=" . str_replace('/', '--', ltrim($property_js,'/'));
			}

			$data = array
			(
				'property_js'		=> json_encode($GLOBALS['phpgw_info']['server']['webserver_url'] . $property_js),
				'base_java_url'		=> json_encode(array(menuaction => "property.uigeneric.attrib_history")),
				'datatable'			=> $datavalues,
				'myColumnDefs'		=> $myColumnDefs,
				'allow_allrows'		=> false,
				'start_record'		=> $this->start,
				'record_limit'		=> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'],
				'num_records'		=> count($values),
				'all_records'		=> $this->bo->total_records,
				'link_url'			=> $GLOBALS['phpgw']->link('/index.php',$link_data),
				'img_path'			=> $GLOBALS['phpgw']->common->get_image_path('phpgwapi','default'),
				'values' 			=> $content,
				'table_header'		=> $table_header,
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
			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'entity.attrib_history', 'property' );
			//-----------------------datatable settings---	

			//_debug_array($data);die();
			$custom			= createObject('phpgwapi.custom_fields');
			$attrib_data 	= $custom->get('property', $acl_location, $attrib_id);
			$appname		= $attrib_data['input_text'];
			$function_msg	= lang('history');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('attrib_history' => $data));
		}

		function delete()
		{
			if(!$this->acl_delete)
			{
				return lang('no access');
			}

			$id	= phpgw::get_var($this->location_info['id']['name']);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($id);
				return lang('id %1 has been deleted', $id);
			}
		}
	}
