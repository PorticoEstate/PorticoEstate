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
	* @subpackage custom
 	* @version $Id$
	*/
	phpgw::import_class('phpgwapi.yui');

	/**
	 * Description
	 * @package property
	 */

	class property_uicustom
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
				'download'	=> true,
				'delete'	=> true
			);

		function property_uicustom()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = 'property::custom';

			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo			= CreateObject('property.bocustom',true);
			$this->bocommon		= CreateObject('property.bocommon');
			$this->start		= $this->bo->start;
			$this->query		= $this->bo->query;
			$this->sort			= $this->bo->sort;
			$this->order		= $this->bo->order;
			$this->filter		= $this->bo->filter;
			$this->cat_id		= $this->bo->cat_id;
			$this->allrows		= $this->bo->allrows;

			$this->acl 			= & $GLOBALS['phpgw']->acl;
			$this->acl_location	= '.custom';
			$this->acl_read 	= $this->acl->check('.custom', PHPGW_ACL_READ, 'property');
			$this->acl_add 		= $this->acl->check('.custom', PHPGW_ACL_ADD, 'property');
			$this->acl_edit 	= $this->acl->check('.custom', PHPGW_ACL_EDIT, 'property');
			$this->acl_delete 	= $this->acl->check('.custom', PHPGW_ACL_DELETE, 'property');

		}

		function save_sessiondata()
		{
			$data = array
				(
					'start'				=> $this->start,
					'query'				=> $this->query,
					'sort'				=> $this->sort,
					'order'				=> $this->order,
					'filter'			=> $this->filter,
					'cat_id'			=> $this->cat_id,
					'this->allrows'		=> $this->allrows
				);
			$this->bo->save_sessiondata($data);
		}

		function index()
		{
			$receipt = $GLOBALS['phpgw']->session->appsession('session_data','custom_receipt');
			$GLOBALS['phpgw']->session->appsession('session_data','custom_receipt','');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']	= $this->bocommon->get_menu();

				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'			=> 'property.uicustom.index',
						'sort'            		=> $this->sort,
						'order'     		   	=> $this->order,
						'cat_id'        		=> $this->cat_id,
						'filter'        		=> $this->filter,
						'query'   	     		=> $this->query
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uicustom.index',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."cat_id:'{$this->cat_id}',"
					."filter:'{$this->filter}',"
					."query:'{$this->query}'";

				$link_data = array
					(
						'menuaction'	=> 'property.uicustom.index',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'cat_id'	=> $this->cat_id,
						'filter'	=> $this->filter,
						'query'		=> $this->query
					);

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'property.uicustom.index',
								'sort'		=> $this->sort,
								'order'		=> $this->order,
								'cat_id'	=> $this->cat_id,
								'filter'	=> $this->filter,
								'query'		=> $this->query
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
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
							)
						)
					)
				);

			}

			$list = $this->bo->read();

			$uicols['name'][0]	= 'custom_id';
			$uicols['descr'][0]	= lang('ID');
			$uicols['name'][1]	= 'name';
			$uicols['descr'][1]	= lang('Name');
			$uicols['name'][2]	= 'entry_date';
			$uicols['descr'][2]	= lang('date');
			$uicols['name'][3]	= 'user';
			$uicols['descr'][3]	= lang('User');
			$j = 0;
			$count_uicols_name = count($uicols['name']);

			if (isset($list) AND is_array($list))
			{
				foreach($list as $list_entry)
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

			$datatable['rowactions']['action'] = array();

			$parameters = array
				(
					'parameter' => array
					(
						array
						(
							'name'		=> 'custom_id',
							'source'	=> 'custom_id'
						),
					)
				);

			if($this->acl_read)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 		=> 'view',
						'statustext' 	=> lang('view the entity'),
						'text'			=> lang('view'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicustom.view'
						)),
						'parameters'	=> $parameters
					);
			}

			if($this->acl_edit)
			{
				$datatable['rowactions']['action'][] = array
					(
						'my_name' 		=> 'edit',
						'statustext' 	=> lang('edit the actor'),
						'text'			=> lang('edit'),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'	=> 'property.uicustom.edit'
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
							'menuaction'	=> 'property.uicustom.delete'
						)),
						'parameters'	=> $parameters
					);
			}

			$datatable['rowactions']['action'][] = array
				(
					'my_name' 		=> 'add',
					'text' 			=> lang('add'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'property.uicustom.edit'
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
					if($uicols['name'][$i]=='custom_id')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'id';
					}

					if($uicols['name'][$i]=='entry_date')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= 'name';
					}

					if($uicols['name'][$i]=='name' || $uicols['name'][$i]=='user')
					{
						$datatable['headers']['header'][$i]['sortable']		= true;
						$datatable['headers']['header'][$i]['sort_field']	= $uicols['name'][$i];
					}
				}
			}

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname					= lang('custom');
			$function_msg				= lang('list custom');

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= 'custom_id'; // name key Column in myColumnDef
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'custom.index', 'property' );
		}

		function edit()
		{
			if(!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php',array('menuaction'=> 'property.uilocation.stop', 'perm'=>2, 'acl_location'=> $this->acl_location));
			}

			$custom_id	= phpgw::get_var('custom_id', 'int');
			$cols_id	= phpgw::get_var('cols_id', 'int');
			$resort		= phpgw::get_var('resort');
			$values		= phpgw::get_var('values');
			$values['sql_text'] = $_POST['values']['sql_text'];
			if($cols_id)
			{
				$this->bo->resort(array('custom_id'=>$custom_id,'id'=>$cols_id,'resort'=>$resort));
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('custom'));

			if ($values['save'] || $values['apply'])
			{
				if(!$values['name'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a name !'));
				}

				if(!$values['sql_text'])
				{
					$receipt['error'][]=array('msg'=>lang('Please enter a sql query !'));
				}

				if(!$receipt['error'])
				{
					$values['custom_id']	= $custom_id;
					$receipt = $this->bo->save($values);
					$custom_id = $receipt['custom_id'];
					$this->cat_id = ($values['cat_id']?$values['cat_id']:$this->cat_id);

					if ($values['save'])
					{
						$GLOBALS['phpgw']->session->appsession('session_data','custom_receipt',$receipt);
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'property.uicustom.index'));
					}
				}
			}

			if ($values['cancel'])
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction'=> 'property.uicustom.index'));
			}


			if ($custom_id)
			{
				$custom = $this->bo->read_single($custom_id);
				$this->cat_id = ($custom['cat_id']?$custom['cat_id']:$this->cat_id);
			}


			$link_data = array
				(
					'menuaction'	=> 'property.uicustom.edit',
					'custom_id'	=> $custom_id
				);

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			while (is_array($custom['cols']) && list(,$entry) = each($custom['cols']))
			{
				$cols[] = array(
					'id'		=> $entry['id'],
					'name'		=> $entry['name'],
					'descr'		=> $entry['descr'],
					'sorting'	=> $entry['sorting'],
					'text_up'	=> lang('Up'),
					'text_down'	=> lang('Down'),
					'link_up'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uicustom.edit', 'resort'=> 'up', 'cols_id'=> $entry['id'], 'custom_id'=> $custom_id)),
					'link_down'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uicustom.edit', 'resort'=> 'down', 'cols_id'=> $entry['id'], 'custom_id'=> $custom_id)),
				);
			}


			$data = array
				(
					'msgbox_data'					=> $GLOBALS['phpgw']->common->msgbox($msgbox_data),
					'edit_url'						=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'lang_custom_id'				=> lang('ID'),
					'value_custom_id'				=> $custom_id,
					'lang_sql_text'					=> lang('sql'),
					'lang_name'						=> lang('name'),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
					'lang_apply'					=> lang('apply'),
					'value_sql_text'				=> $custom['sql_text'],
					'value_name'					=> $custom['name'],
					'lang_name_statustext'			=> lang('Enter a name for the query'),
					'lang_sql_statustext'			=> lang('Enter a sql query'),
					'lang_apply_statustext'			=> lang('Apply the values'),
					'lang_cancel_statustext'		=> lang('Leave the custom untouched and return back to the list'),
					'lang_save_statustext'			=> lang('Save the custom and return back to the list'),
					'lang_no_cat'					=> lang('no category'),
					'lang_cat_statustext'			=> lang('Select the category the custom belongs to. To do not use a category select NO CATEGORY'),
					'lang_descr'					=> lang('descr'),
					'lang_new_name_statustext'		=> lang('name'),
					'lang_new_descr_statustext'		=> lang('descr'),
					'cols'							=> $cols,
					'lang_col_name'					=> lang('Column name'),
					'lang_col_descr'				=> lang('Column description'),
					'lang_delete_column'			=> lang('Delete column'),
					'lang_delete_cols_statustext'	=> lang('Delete this column from the output'),
					'lang_up_text'					=> lang('Up'),
					'lang_down_text'				=> lang('Down'),
					'lang_sorting'					=> lang('Sorting'),

				);
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('custom') . ': ' . ($custom_id?lang('edit custom'):lang('add custom'));

			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('edit' => $data));
		}


		function delete()
		{
			$custom_id	= phpgw::get_var('custom_id', 'int');
			$confirm	= phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
					'menuaction' => 'property.uicustom.index'
				);

			if( phpgw::get_var('phpgw_return_as') == 'json' )
			{
				$this->bo->delete($custom_id);
				return "custom_id ".$custom_id." ".lang("has been deleted");
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
					'done_action'				=> $GLOBALS['phpgw']->link('/index.php',$link_data),
					'delete_action'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'property.uicustom.delete', 'custom_id'=> $custom_id)),
					'lang_confirm_msg'			=> lang('do you really want to delete this entry'),
					'lang_yes'					=> lang('yes'),
					'lang_yes_statustext'		=> lang('Delete the entry'),
					'lang_no_statustext'		=> lang('Back to the list'),
					'lang_no'					=> lang('no')
				);

			$appname	= lang('custom');
			$function_msg	= lang('delete custom');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('delete' => $data));
		}


		function view()
		{
			$custom_id	= phpgw::get_var('custom_id', 'int','GET');

			$datatable = array();

			if( phpgw::get_var('phpgw_return_as') != 'json' )
			{
				$datatable['menu']	= $this->bocommon->get_menu();

				$datatable['config']['base_url'] = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction'	=> 'property.uicustom.view',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'custom_id'	=> $custom_id,
						'filter'	=> $this->filter,
						'query'		=> $this->query
					));

				$datatable['config']['base_java_url'] = "menuaction:'property.uicustom.view',"
					."sort:'{$this->sort}',"
					."order:'{$this->order}',"
					."custom_id:'{$custom_id}',"
					."filter:'{$this->filter}',"
					."query:'{$this->query}'";

				$link_data = array
					(
						'menuaction'	=> 'property.uicustom.view',
						'sort'		=> $this->sort,
						'order'		=> $this->order,
						'custom_id'	=> $custom_id,
						'filter'	=> $this->filter,
						'query'		=> $this->query
					);

				$datatable['config']['allow_allrows'] = true;

				$datatable['actions']['form'] = array
					(
						array
						(
							'action'	=> $GLOBALS['phpgw']->link('/index.php',
							array
							(
								'menuaction'	=> 'property.uicustom.view',
								'sort'		=> $this->sort,
								'order'		=> $this->order,
								'custom_id'	=> $custom_id,
								'filter'	=> $this->filter,
								'query'		=> $this->query
							)
						),
						'fields'	=> array
						(
							'field' => array
							(
								'type'	=> 'button',
								'id'	=> 'btn_export',
								'value'	=> lang('download'),
								'tab_index' => 1
							)
						)
					)
				);
			}
			//_debug_array($custom_id);die;
			$list = $this->bo->read_custom($custom_id);
			$uicols	= $this->bo->uicols;
			$j = 0;
			$count_uicols_name = count($uicols);

			if (isset($list) AND is_array($list))
			{
				foreach($list as $list_entry)
				{
					for ($k=0;$k<$count_uicols_name;$k++)
					{
						$datatable['rows']['row'][$j]['column'][$k]['name'] 			= $uicols[$k]['name'];
						$datatable['rows']['row'][$j]['column'][$k]['value']			= $list_entry[$uicols[$k]['name']];
					}
					$j++;
				}

				$datatable['rowactions']['action'] = array();

			}
			//_debug_array($datatable);die;

			for ($i=0;$i<$count_uicols_name;$i++)
			{
				if($uicols['input_type'][$i]!='hidden')
				{
					$datatable['headers']['header'][$i]['formatter'] 		= ($uicols[$i]['formatter']==''?  '""' : $uicols[$i]['formatter']);
					$datatable['headers']['header'][$i]['name'] 			= $uicols[$i]['name'];
					$datatable['headers']['header'][$i]['text'] 			= $uicols[$i]['descr'];
					$datatable['headers']['header'][$i]['visible'] 			= true;
					$datatable['headers']['header'][$i]['sortable']			= false;
				}
			}

			$link_download = array
				(
					'menuaction'	=> 'property.uicustom.download',
					'sort'		=> $this->sort,
					'order'		=> $this->order,
					'filter'	=> $this->filter,
					'query'		=> $this->query,
					'custom_id'	=> $custom_id,
					'allrows'	=> $this->allrows
				);

			//path for property.js
			$datatable['property_js'] = $GLOBALS['phpgw_info']['server']['webserver_url']."/property/js/yahoo/property.js";

			// Pagination and sort values
			$datatable['pagination']['records_start'] 	= (int)$this->bo->start;
			$datatable['pagination']['records_limit'] 	= $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$datatable['pagination']['records_returned']= count($list);
			$datatable['pagination']['records_total'] 	= $this->bo->total_records;

			$appname					= lang('documents');
			$function_msg				= lang('list documents');

			//_debug_array($datatable['headers']['header']);die;

			if ( (phpgw::get_var("start")== "") && (phpgw::get_var("order",'string')== ""))
			{
				$datatable['sorting']['order'] 			= $datatable['headers']['header'][0]['name']; // name key Column in myColumnDef
				$datatable['sorting']['sort'] 			= 'asc'; // ASC / DESC
			}
			else
			{
				$datatable['sorting']['order']			= null; // name of column of Database
				$datatable['sorting']['sort'] 			= null; // ASC / DESC
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
						if(isset($column['format']) && $column['format']== "link" && $column['java_link']==true)
						{
							$json_row[$column['name']] = "<a href='#' id='".$column['link']."' onclick='javascript:filter_data(this.id);'>" .$column['value']."</a>";
						}
						else if(isset($column['format']) && $column['format']== "link")
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

			$GLOBALS['phpgw']->js->validate_file( 'yahoo', 'custom.view', 'property' );
			//$this->save_sessiondata();

		}

		function download()
		{
			$custom_id = phpgw::get_var('custom_id', 'int');
			$list= $this->bo->read_custom($custom_id,$allrows=true);
			$uicols	= $this->bo->uicols;
			foreach($uicols as $col)
			{
				$names[] = $col['name'];
				$descr[] = $col['descr'];
			}
			$this->bocommon->download($list,$names,$descr);
		}
	}
